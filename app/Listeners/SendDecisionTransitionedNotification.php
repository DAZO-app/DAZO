<?php

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\DecisionTransitioned;
use App\Services\NotificationService;

class SendDecisionTransitionedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(DecisionTransitioned $event): void
    {
        $decision = $event->decision;
        $decision->load('participants.user', 'circle.members.user');

        // Notify participants (author, animator, active participants)
        $participants = $decision->participants->pluck('user_id')->toArray();
        
        // Also get anyone who has ever given feedback/consent on this decision
        $activeUserIds = \App\Models\Feedback::whereHas('decisionVersion', function($q) use ($decision) {
            $q->where('decision_id', $decision->id);
        })->pluck('author_id')->toArray();

        $consentUserIds = \App\Models\Consent::whereHas('decisionVersion', function($q) use ($decision) {
            $q->where('decision_id', $decision->id);
        })->pluck('user_id')->toArray();

        $allRelevantUserIds = array_unique(array_merge($participants, $activeUserIds, $consentUserIds));

        foreach ($allRelevantUserIds as $userId) {
            $user = \App\Models\User::find($userId);
            if (!$user) continue;

            $this->notificationService->notify(
                $user,
                NotificationEventType::PHASE_CHANGE,
                [
                    'decision_id' => $decision->id, 
                    'title' => $decision->title, 
                    'status' => $decision->status->value
                ]
            );
        }
    }
}
