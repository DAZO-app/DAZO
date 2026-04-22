<?php

namespace App\Services;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use App\Models\Attachment;
use App\Models\Circle;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\DecisionNotificationMail;
use App\Services\ConfigService;

class DecisionService
{
    public function __construct(private ConfigService $configService)
    {
    }

    /**
     * Initialise une nouvelle Décision (DRAFT).
     */
    public function createDecision(array $data, User $author, Circle $circle): Decision
    {
        return DB::transaction(function () use ($data, $author, $circle) {
            
            $decision = Decision::create([
                'circle_id' => $circle->id,
                'category_id' => $data['category_id'] ?? null,
                'model_id' => $data['model_id'] ?? null,
                'title' => $data['title'],
                'visibility' => $data['visibility'] ?? \App\Enums\DecisionVisibility::PUBLIC->value,
                'priority' => $data['priority'] ?? 0,
                'emergency_mode' => $data['emergency_mode'] ?? false,
                'status' => DecisionStatus::DRAFT->value,
            ]);

            // Création de la Version 1 (DRAFT)
            $decision->versions()->create([
                'author_id' => $author->id,
                'version_number' => 1,
                'is_current' => true,
                'content' => $data['content'],
            ]);

            // Auteur = Participant
            $decision->participants()->create([
                'user_id' => $author->id,
                'role' => DecisionParticipantRole::AUTHOR->value,
            ]);

            // Optionnel: Assigné à un animateur distinct
            // Si $data['animator_id'] est fourni et valide :
            if (!empty($data['animator_id'])) {
                $decision->participants()->create([
                    'user_id' => $data['animator_id'],
                    'role' => DecisionParticipantRole::ANIMATOR->value,
                ]);
            }

            event(new \App\Events\DecisionCreated($decision));

            return $decision;
        });
    }

    /**
     * Machine à États simplifiée (sans Spatie pour compat Laravel 13).
     */
    public function transition(Decision $decision, string $toStatus, User $actor, bool $isSystem = false): Decision
    {
        $fromStatus = $decision->status->value;

        // Transitions sans contrainte de départ (globales)
        $globalTransitions = [
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value,
        ];

        // Transitions spécifiques
        $allowedTransitions = [
            DecisionStatus::DRAFT->value => [DecisionStatus::CLARIFICATION->value],
            DecisionStatus::CLARIFICATION->value => [
                DecisionStatus::REACTION->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value
            ],
            DecisionStatus::REACTION->value => [
                DecisionStatus::OBJECTION->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value
            ],
            DecisionStatus::OBJECTION->value => [
                DecisionStatus::ADOPTED->value,
                DecisionStatus::ADOPTED_OVERRIDE->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value
            ],
            DecisionStatus::SUSPENDED->value => [
                DecisionStatus::CLARIFICATION->value,
                DecisionStatus::REACTION->value,
                DecisionStatus::OBJECTION->value,
                DecisionStatus::REVISION->value,
            ],
            DecisionStatus::REVISION->value => [
                DecisionStatus::CLARIFICATION->value,
                DecisionStatus::SUSPENDED->value
            ],
        ];

        if (!in_array($toStatus, $globalTransitions)) {
            $allowedFromCurrent = $allowedTransitions[$fromStatus] ?? [];
            if (!in_array($toStatus, $allowedFromCurrent)) {
                throw ValidationException::withMessages([
                    'status' => ["Transition non autorisée de '{$fromStatus}' vers '{$toStatus}'."],
                ]);
            }
        }

        // Vérifications de droits supplémentaires si non-système
        if (!$isSystem) {
            $isAuthorOrAnimator = $decision->participants()->where('user_id', $actor->id)
                ->whereIn('role', [
                    DecisionParticipantRole::AUTHOR->value,
                    DecisionParticipantRole::ANIMATOR->value
                ])->exists();

            if (!$isAuthorOrAnimator && !$actor->is_global_animator) {
                 throw ValidationException::withMessages([
                    'status' => ["Vous n'avez pas les droits pour changer le statut de cette décision."],
                ]);
            }

            // Gestion de la suspension
            if ($toStatus === DecisionStatus::SUSPENDED->value) {
                $decision->status_before_suspension = $fromStatus;
            }

            // Gestion de la reprise
            if ($fromStatus === DecisionStatus::SUSPENDED->value) {
                if ($toStatus !== $decision->status_before_suspension) {
                   // Optionnel: on pourrait forcer la reprise vers l'état exact d'avant,
                   // mais laisser le choix à l'animateur est plus souple.
                }
                $decision->status_before_suspension = null;
            }
        }

        $decision->status = $toStatus;
        
        // Reset reminder and set deadline
        $decision->reminder_sent = false;
        
        // Clear deadline if suspended, otherwise recalculate
        if ($toStatus === DecisionStatus::SUSPENDED->value) {
            $decision->current_deadline = null;
        } else {
            $decision->current_deadline = $this->calculateDeadline($toStatus);
        }
        
        $decision->save();

        $this->dispatchEventForTransition($decision, $toStatus);

        return $decision;
    }

    /**
     * Calculate the deadline based on the new status and configuration.
     */
    private function calculateDeadline(string $status): ?\Illuminate\Support\Carbon
    {
        $days = match ($status) {
            DecisionStatus::REACTION->value => (int) $this->configService->get('decision_reaction_days', 3),
            DecisionStatus::OBJECTION->value => (int) $this->configService->get('decision_objection_days', 3),
            default => null,
        };

        return $days ? now()->addDays($days) : null;
    }

    private function dispatchEventForTransition(Decision $decision, string $toStatus)
    {
        switch ($toStatus) {
            case DecisionStatus::CLARIFICATION->value:
            case DecisionStatus::REACTION->value:
                event(new \App\Events\DecisionTransitioned($decision));
                break;
            case DecisionStatus::ADOPTED->value:
                event(new \App\Events\DecisionAdopted($decision));
                break;
            case DecisionStatus::ADOPTED_OVERRIDE->value:
                event(new \App\Events\DecisionAdoptedWithOverride($decision));
                break;
            case DecisionStatus::REVISION->value:
                event(new \App\Events\DecisionRevisionStarted($decision));
                break;
            case DecisionStatus::ABANDONED->value:
                event(new \App\Events\DecisionAbandoned($decision));
                break;
            case DecisionStatus::LAPSED->value:
                event(new \App\Events\DecisionLapsed($decision));
                break;
            case DecisionStatus::DESERTED->value:
                event(new \App\Events\DecisionDeserted($decision));
                break;
        }
    }

    /**
     * Crée une nouvelle version et effectue la transition de révision vers clarification.
     */
    public function createNewVersion(Decision $decision, string $content, User $author, array $attachmentIds = []): DecisionVersion
    {
        return DB::transaction(function () use ($decision, $content, $author, $attachmentIds) {
            $oldVersion = $decision->currentVersion;

            // Désactive l'ancienne version courante
            if ($oldVersion) {
                $oldVersion->is_current = false;
                $oldVersion->save();
            }

            $currentMax = $decision->versions()->max('version_number') ?? 0;

            // Insère la nouvelle
            $newVersion = $decision->versions()->create([
                'author_id' => $author->id,
                'previous_version_id' => $oldVersion?->id,
                'version_number' => $currentMax + 1,
                'is_current' => true,
                'content' => $content,
            ]);

            // Lier les pièces jointes explicitement reçues
            if (!empty($attachmentIds)) {
                Attachment::whereIn('id', $attachmentIds)
                    ->update(['decision_version_id' => $newVersion->id]);
            }

            // Nettoyage des champs de brouillon
            $decision->update([
                'revision_content' => null,
                'revision_attachment_ids' => null,
            ]);

            // Forcer la transition auto de REVISION à CLARIFICATION (le nouveau cycle)
            $this->transition($decision, DecisionStatus::CLARIFICATION->value, $author, true);

            return $newVersion;
        });
    }

    /**
     * Calcule les statistiques de participation pour une version spécifique.
     */
    public function getParticipationStats(Decision $decision, DecisionVersion $version, ?string $statusOverride = null): array
    {
        $circle = $decision->circle;
        $totalMembers = $circle->members()->where('role', '!=', CircleMemberRole::OBSERVER->value)->pluck('user_id')->toArray();
        $excludedOrManaging = $decision->participants()
            ->whereIn('role', [
                \App\Enums\DecisionParticipantRole::EXCLUDED->value,
                \App\Enums\DecisionParticipantRole::AUTHOR->value,
                \App\Enums\DecisionParticipantRole::ANIMATOR->value
            ])->pluck('user_id')->toArray();
        
        $eligible = array_diff($totalMembers, $excludedOrManaging);
        $status = $statusOverride ?? $decision->status->value;
        $participated = 0;

        $phaseFeedbackTypes = [];
        $phaseConsentSignals = [];

        if (in_array($status, [DecisionStatus::CLARIFICATION->value, DecisionStatus::REACTION->value, DecisionStatus::OBJECTION->value], true)) {
            
            if ($status === DecisionStatus::CLARIFICATION->value) {
                $phaseFeedbackTypes = [FeedbackType::CLARIFICATION->value];
                $phaseConsentSignals = [ConsentSignal::NO_QUESTIONS->value];
            } elseif ($status === DecisionStatus::REACTION->value) {
                $phaseFeedbackTypes = [FeedbackType::REACTION->value];
                $phaseConsentSignals = [ConsentSignal::NO_REACTION->value];
            } elseif ($status === DecisionStatus::OBJECTION->value) {
                $phaseFeedbackTypes = [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value];
                $phaseConsentSignals = [ConsentSignal::NO_OBJECTION->value, ConsentSignal::ABSTENTION->value];
            }

            $feedbackAuthors = Feedback::where('decision_version_id', $version->id)
                ->whereIn('type', $phaseFeedbackTypes)
                ->pluck('author_id')->toArray();
                
            $consentAuthors = Consent::where('decision_version_id', $version->id)
                ->whereIn('signal', $phaseConsentSignals)
                ->pluck('user_id')->toArray();
            
            $allParticipants = array_unique(array_merge($feedbackAuthors, $consentAuthors));
            $participated = count(array_intersect($eligible, $allParticipants));
        }

        return [
            'eligible'     => count($eligible),
            'participated' => $participated,
            'pending'      => max(0, count($eligible) - $participated)
        ];
    }

    /**
     * Notifie les participants du cercle par email.
     */
    public function notifyParticipants(Decision $decision, User $sender): void
    {
        // On recharge les relations nécessaires pour l'email
        $decision->load(['circle.members.user', 'currentVersion.attachments']);
        
        $circle = $decision->circle;
        if (!$circle) return;

        $excludedIds = $decision->participants()
            ->where('role', \App\Enums\DecisionParticipantRole::EXCLUDED->value)
            ->pluck('user_id')
            ->toArray();

        foreach ($circle->members as $member) {
            $user = $member->user;
            
            // Skip observer, excluded, sender, or inactive users
            if ($member->role === CircleMemberRole::OBSERVER->value) continue;
            if (in_array($user->id, $excludedIds)) continue;
            if ($user->id === $sender->id) continue;
            if (!$user->is_active || !$user->email) continue;

            Mail::to($user->email)
                ->queue(new DecisionNotificationMail($decision, $decision->currentVersion, $user));
        }
    }
}
