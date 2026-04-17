<?php

namespace App\Services;

use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Models\Circle;
use App\Models\Decision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DecisionService
{
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
            DecisionStatus::CLARIFICATION->value => [DecisionStatus::REACTION->value],
            DecisionStatus::REACTION->value => [DecisionStatus::OBJECTION->value],
            DecisionStatus::OBJECTION->value => [
                DecisionStatus::ADOPTED->value,
                DecisionStatus::ADOPTED_OVERRIDE->value,
                DecisionStatus::REVISION->value,
            ],
            DecisionStatus::REVISION->value => [DecisionStatus::CLARIFICATION->value],
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

            // Ex: "objection -> adopted" seulement possible si pas d'objection bloquante ?
            // Laissez la liberté à l'animateur si manual, ou throw si strict.
        }

        $decision->status = $toStatus;
        $decision->save();

        $this->dispatchEventForTransition($decision, $toStatus);

        return $decision;
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
    public function createNewVersion(Decision $decision, string $content, User $author): \App\Models\DecisionVersion
    {
        return DB::transaction(function () use ($decision, $content, $author) {
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

            // Forcer la transition auto de REVISION à CLARIFICATION (le nouveau cycle)
            $this->transition($decision, DecisionStatus::CLARIFICATION->value, $author, true);

            return $newVersion;
        });
    }
}
