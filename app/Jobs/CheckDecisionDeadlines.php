<?php

namespace App\Jobs;

use App\Enums\DecisionStatus;
use App\Models\Decision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckDecisionDeadlines implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(\App\Services\DecisionService $decisionService): void
    {
        // Rechercher toutes les décisions actives avec une deadline dépassée
        $expiredDecisions = Decision::whereNotNull('objection_round_deadline')
            ->where('objection_round_deadline', '<', now())
            ->whereNotIn('status', [
                DecisionStatus::DRAFT->value,
                DecisionStatus::ADOPTED->value,
                DecisionStatus::ADOPTED_OVERRIDE->value,
                DecisionStatus::ABANDONED->value,
                DecisionStatus::LAPSED->value,
                DecisionStatus::DESERTED->value,
            ])
            ->get();

        // Système global utilisateur ? (Peut nécessiter un System User ou pas d'assertion de the actor).
        // On utilisera null pour le User et isSystem = true.
        // Wait, the signature requires User $actor. We can modify signature to accept null.
        $systemUser = \App\Models\User::orderBy('id')->first(); // Fallback to first user (Admin) or modify service.
        
        foreach ($expiredDecisions as $decision) {
            try {
                $decisionService->transition(
                    $decision,
                    DecisionStatus::LAPSED->value,
                    $systemUser, 
                    true
                );
            } catch (\Exception $e) {
                // Ignore exceptions internally
            }
        }
    }
}
