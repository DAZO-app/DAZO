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
                'circle_id'      => $circle->id,
                'model_id'       => $data['model_id'] ?? null,
                'title'          => $data['title'],
                'visibility'     => $data['visibility'] ?? \App\Enums\DecisionVisibility::PUBLIC->value,
                'priority'       => $data['priority'] ?? 0,
                'emergency_mode' => $data['emergency_mode'] ?? false,
                'status'         => DecisionStatus::DRAFT->value,
            ]);

            if (!empty($data['category_ids'])) {
                $decision->categories()->sync($data['category_ids']);
            }

            $decision->versions()->create([
                'author_id'      => $author->id,
                'version_number' => 1,
                'is_current'     => true,
                'content'        => $data['content'],
            ]);

            $decision->participants()->create([
                'user_id' => $author->id,
                'role'    => DecisionParticipantRole::AUTHOR->value,
            ]);

            if (!empty($data['animator_id'])) {
                $decision->participants()->create([
                    'user_id' => $data['animator_id'],
                    'role'    => DecisionParticipantRole::ANIMATOR->value,
                ]);
            }

            event(new \App\Events\DecisionCreated($decision));

            return $decision;
        });
    }

    /**
     * Machine à États simplifiée.
     */
    public function transition(Decision $decision, string $toStatus, User $actor, bool $isSystem = false, bool $notify = true, bool $isMeeting = false): Decision
    {
        $fromStatus = $decision->status->value;

        $globalTransitions = [
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value,
        ];

        $allowedTransitions = [
            DecisionStatus::DRAFT->value => [DecisionStatus::CLARIFICATION->value],
            DecisionStatus::CLARIFICATION->value => [
                DecisionStatus::REACTION->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value,
            ],
            DecisionStatus::REACTION->value => [
                DecisionStatus::OBJECTION->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value,
            ],
            DecisionStatus::OBJECTION->value => [
                DecisionStatus::ADOPTED->value,
                DecisionStatus::ADOPTED_OVERRIDE->value,
                DecisionStatus::REVISION->value,
                DecisionStatus::SUSPENDED->value,
            ],
            DecisionStatus::SUSPENDED->value => [
                DecisionStatus::CLARIFICATION->value,
                DecisionStatus::REACTION->value,
                DecisionStatus::OBJECTION->value,
                DecisionStatus::REVISION->value,
            ],
            DecisionStatus::REVISION->value => [
                DecisionStatus::CLARIFICATION->value,
                DecisionStatus::OBJECTION->value,
                DecisionStatus::SUSPENDED->value,
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

        if (!$isSystem) {
            $isAuthorOrAnimator = $decision->participants()->where('user_id', $actor->id)
                ->whereIn('role', [
                    DecisionParticipantRole::AUTHOR->value,
                    DecisionParticipantRole::ANIMATOR->value,
                ])->exists();

            $isCircleMember = $decision->circle->members()
                ->where('user_id', $actor->id)
                ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value)
                ->exists();

            $isCircleAnimator = $decision->circle->members()
                ->where('user_id', $actor->id)
                ->where('role', \App\Enums\CircleMemberRole::ANIMATOR->value)
                ->exists();

            // Si c'est en mode meeting, on autorise tous les membres (non-observateurs)
            // Sinon, on reste sur le porteur, l'animateur, l'admin global / système ou l'animateur du cercle
            $hasRights = $isAuthorOrAnimator 
                || $actor->is_global_animator 
                || $isCircleAnimator
                || in_array($actor->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPERADMIN])
                || ($isMeeting && $isCircleMember);

            if (!$hasRights) {
                throw ValidationException::withMessages([
                    'status' => ["Vous n'avez pas les droits pour changer le statut de cette décision."],
                ]);
            }

            if ($toStatus === DecisionStatus::SUSPENDED->value) {
                $decision->status_before_suspension = $fromStatus;
            }

            if ($fromStatus === DecisionStatus::SUSPENDED->value) {
                $decision->status_before_suspension = null;
            }
        }

        $decision->status = $toStatus;
        $decision->reminder_sent = false;

        if ($toStatus === DecisionStatus::SUSPENDED->value) {
            $decision->current_deadline = null;
        } else {
            $decision->current_deadline = $this->calculateDeadline($toStatus);
        }

        $decision->save();

        // ── Auto-abstention : enregistrer les participants silencieux ──
        // Quand on quitte une phase active, on marque d'abstention tous les
        // membres éligibles qui n'ont pas encore exprimé de signal/feedback.
        $fromPhaseConfig = DecisionStatus::tryFrom($fromStatus)?->getPhaseConfig();
        if ($fromPhaseConfig && !in_array($toStatus, $globalTransitions)) {
            $this->recordAbstentionsForPhase($decision, $fromPhaseConfig, $fromStatus);
        }

        if ($notify) {
            $this->dispatchEventForTransition($decision, $toStatus);
        }

        return $decision;
    }

    /**
     * Enregistre un signal d'abstention pour les participants éligibles
     * qui ne se sont pas exprimés durant la phase qui se termine.
     */
    public function recordAbstentionsForPhase(Decision $decision, array $phaseConfig, string $phase): void
    {
        $version = $decision->currentVersion;
        if (!$version) return;

        $circle = $decision->circle;
        if (!$circle) return;

        // Membres éligibles (non observateurs, non exclus/porteur/animateur)
        $allMemberIds = $circle->members()
            ->where('role', '!=', CircleMemberRole::OBSERVER->value)
            ->pluck('user_id')->toArray();

        $managingIds = $decision->participants()
            ->whereIn('role', [
                DecisionParticipantRole::EXCLUDED->value,
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->pluck('user_id')->toArray();

        $eligibleIds = array_diff($allMemberIds, $managingIds);

        // Participants ayant déjà donné un signal ou un feedback sur cette version
        $hasFeedback = Feedback::where('decision_version_id', $version->id)
            ->whereIn('type', $phaseConfig['feedback_types'])
            ->pluck('author_id')->toArray();

        $hasConsent = Consent::where('decision_version_id', $version->id)
            ->where('phase', $phase)
            ->pluck('user_id')->toArray();

        $alreadyParticipated = array_unique(array_merge($hasFeedback, $hasConsent));
        $silentIds = array_diff($eligibleIds, $alreadyParticipated);

        foreach ($silentIds as $uid) {
            Consent::create([
                'decision_version_id' => $version->id,
                'user_id' => $uid,
                'signal' => ConsentSignal::ABSTENTION,
                'phase' => $phase,
            ]);
        }
    }

    /**
     * Calculate the deadline based on the new status and configuration.
     */
    private function calculateDeadline(string $status): ?\Illuminate\Support\Carbon
    {
        $days = match ($status) {
            DecisionStatus::REACTION->value  => (int) $this->configService->get('decision_reaction_days', 3),
            DecisionStatus::OBJECTION->value => (int) $this->configService->get('decision_objection_days', 3),
            default => null,
        };

        return $days ? now()->addDays($days) : null;
    }

    private function dispatchEventForTransition(Decision $decision, string $toStatus): void
    {
        match ($toStatus) {
            DecisionStatus::CLARIFICATION->value,
            DecisionStatus::REACTION->value       => event(new \App\Events\DecisionTransitioned($decision)),
            DecisionStatus::ADOPTED->value        => event(new \App\Events\DecisionAdopted($decision)),
            DecisionStatus::ADOPTED_OVERRIDE->value => event(new \App\Events\DecisionAdoptedWithOverride($decision)),
            DecisionStatus::REVISION->value       => event(new \App\Events\DecisionRevisionStarted($decision)),
            DecisionStatus::ABANDONED->value      => event(new \App\Events\DecisionAbandoned($decision)),
            DecisionStatus::LAPSED->value         => event(new \App\Events\DecisionLapsed($decision)),
            DecisionStatus::DESERTED->value       => event(new \App\Events\DecisionDeserted($decision)),
            default => null,
        };
    }

    /**
     * Crée une nouvelle version et effectue la transition de révision vers le statut cible.
     */
    public function createNewVersion(Decision $decision, string $content, User $author, array $attachmentIds = [], ?string $targetStatus = null): DecisionVersion
    {
        return DB::transaction(function () use ($decision, $content, $author, $attachmentIds, $targetStatus) {
            $oldVersion = $decision->currentVersion;

            if ($oldVersion) {
                $oldVersion->is_current = false;
                $oldVersion->save();
            }

            $currentMax = $decision->versions()->max('version_number') ?? 0;

            $newVersion = $decision->versions()->create([
                'author_id'           => $author->id,
                'previous_version_id' => $oldVersion?->id,
                'version_number'      => $currentMax + 1,
                'is_current'          => true,
                'content'             => $content,
            ]);

            if (!empty($attachmentIds)) {
                Attachment::whereIn('id', $attachmentIds)
                    ->update(['decision_version_id' => $newVersion->id]);
            }

            $decision->update([
                'revision_content'        => null,
                'revision_attachment_ids' => null,
            ]);

            $status = $targetStatus ?? DecisionStatus::CLARIFICATION->value;
            $this->transition($decision, $status, $author, true);

            return $newVersion;
        });
    }

    /**
     * Calcule les statistiques de participation pour une version spécifique.
     * Utilise le helper getPhaseConfig() pour éviter la duplication.
     */
    public function getParticipationStats(Decision $decision, DecisionVersion $version, ?string $statusOverride = null): array
    {
        $circle      = $decision->circle;
        $totalMembers = $circle->members()->where('role', '!=', CircleMemberRole::OBSERVER->value)->pluck('user_id')->toArray();
        $excludedOrManaging = $decision->participants()
            ->whereIn('role', [
                DecisionParticipantRole::EXCLUDED->value,
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->pluck('user_id')->toArray();

        $eligible   = array_diff($totalMembers, $excludedOrManaging);
        $statusEnum = DecisionStatus::tryFrom($statusOverride ?? $decision->status->value);
        $phaseConfig = $statusEnum?->getPhaseConfig();

        $participated = 0;
        if ($phaseConfig) {
            $feedbackAuthors = Feedback::where('decision_version_id', $version->id)
                ->whereIn('type', $phaseConfig['feedback_types'])
                ->pluck('author_id')->toArray();

            $consentAuthors = Consent::where('decision_version_id', $version->id)
                ->whereIn('signal', $phaseConfig['consent_signals'])
                ->pluck('user_id')->toArray();

            $allParticipants = array_unique(array_merge($feedbackAuthors, $consentAuthors));
            $participated    = count(array_intersect($eligible, $allParticipants));
        }

        return [
            'eligible'    => count($eligible),
            'participated' => $participated,
            'pending'     => max(0, count($eligible) - $participated),
        ];
    }

    /**
     * Construit la map de participation par phase pour une version donnée.
     * Centralisé ici pour éviter la duplication entre show() et autres méthodes.
     */
    public function getPhaseParticipationMap(Decision $decision, DecisionVersion $version): array
    {
        $map = ['clarification' => [], 'reaction' => [], 'objection' => []];

        $versionFeedbacks = $version->feedbacks()->get(['author_id', 'type']);
        $versionConsents  = $version->consents()->get(['user_id', 'signal', 'phase']);

        foreach ($versionFeedbacks as $fb) {
            $typeVal = is_object($fb->type) ? $fb->type->value : $fb->type;
            match ($typeVal) {
                FeedbackType::CLARIFICATION->value => $map['clarification'][$fb->author_id] = $typeVal,
                FeedbackType::REACTION->value      => $map['reaction'][$fb->author_id] = $typeVal,
                FeedbackType::OBJECTION->value,
                FeedbackType::SUGGESTION->value    => $map['objection'][$fb->author_id] = $typeVal,
                default => null,
            };
        }

        foreach ($versionConsents as $cs) {
            $signalVal = is_object($cs->signal) ? $cs->signal->value : $cs->signal;
            $phaseVal  = is_object($cs->phase) ? $cs->phase->value : $cs->phase;
            
            // Map terminal statuses back to their logical phase for the participation map
            if (in_array($phaseVal, [DecisionStatus::ADOPTED->value, DecisionStatus::ADOPTED_OVERRIDE->value])) {
                $phaseVal = 'objection';
            }

            if (isset($map[$phaseVal])) {
                $map[$phaseVal][(string)$cs->user_id] = $signalVal;
            }
        }

        return $map;
    }

    private function mapAbstention(array &$map, string $userId, Decision $decision): void
    {
        // Cette méthode devient obsolète car on utilise maintenant la colonne 'phase' en base,
        // mais on la garde pour compatibilité si besoin ou on la supprime.
        // On la laisse vide pour l'instant pour éviter les erreurs d'appel.
    }

    /**
     * Retourne les utilisateurs éligibles n'ayant pas encore participé à la phase active.
     * Utilisé pour la fonctionnalité de relance manuelle.
     */
    public function getPendingUsers(Decision $decision): \Illuminate\Support\Collection
    {
        $circle = $decision->circle;
        if (!$circle) return collect([]);

        $statusEnum = $decision->status;
        $phaseConfig = $statusEnum->getPhaseConfig();
        if (!$phaseConfig) return collect([]);

        $v = $decision->currentVersion;
        if (!$v) return collect([]);

        $eligibleUserIds = $circle->members()
            ->where('role', '!=', CircleMemberRole::OBSERVER->value)
            ->pluck('user_id')->toArray();

        $excludedOrManaging = $decision->participants()
            ->whereIn('role', [
                DecisionParticipantRole::EXCLUDED->value,
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->pluck('user_id')->toArray();

        $targetUserIds = array_diff($eligibleUserIds, $excludedOrManaging);

        $participatedUserIds = array_unique(array_merge(
            Feedback::where('decision_version_id', $v->id)
                ->whereIn('type', $phaseConfig['feedback_types'])
                ->pluck('author_id')->toArray(),
            Consent::where('decision_version_id', $v->id)
                ->whereIn('signal', $phaseConfig['consent_signals'])
                ->pluck('user_id')->toArray()
        ));

        $pendingUserIds = array_diff($targetUserIds, $participatedUserIds);

        return User::whereIn('id', $pendingUserIds)
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);
    }
}
