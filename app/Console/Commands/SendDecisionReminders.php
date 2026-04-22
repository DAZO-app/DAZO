<?php

namespace App\Console\Commands;

use App\Models\Decision;
use App\Models\User;
use App\Services\ConfigService;
use App\Services\DecisionService;
use App\Mail\DecisionReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDecisionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dazo:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for decisions approaching their deadline';

    /**
     * Execute the console command.
     */
    public function handle(DecisionService $decisionService, ConfigService $configService)
    {
        $reminderHours = (int) $configService->get('reminder_hours_before', 24);
        
        $decisions = Decision::whereNotNull('current_deadline')
            ->where('reminder_sent', false)
            ->where('current_deadline', '<=', now()->addHours($reminderHours))
            ->where('current_deadline', '>', now()) // Not yet expired
            ->with(['circle.members.user', 'participants', 'currentVersion'])
            ->get();

        $this->info("Found {$decisions->count()} decisions needing reminders.");

        foreach ($decisions as $decision) {
            $this->processDecision($decision, $decisionService);
            $decision->update(['reminder_sent' => true]);
        }

        return Command::SUCCESS;
    }

    private function processDecision(Decision $decision, DecisionService $decisionService)
    {
        $stats = $decisionService->getParticipationStats($decision, $decision->currentVersion);
        
        // Find users who haven't participated yet
        // Logic similar to getParticipationStats but we need the actual user models
        $eligibleUserIds = $decision->circle->members()
            ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value)
            ->pluck('user_id')
            ->toArray();

        $excludedOrManaging = $decision->participants()
            ->whereIn('role', [
                \App\Enums\DecisionParticipantRole::EXCLUDED->value,
                \App\Enums\DecisionParticipantRole::AUTHOR->value,
                \App\Enums\DecisionParticipantRole::ANIMATOR->value
            ])->pluck('user_id')->toArray();

        $targetUserIds = array_diff($eligibleUserIds, $excludedOrManaging);

        // Get those who ALREADY participated (Feedback or Consent)
        $participatedUserIds = $this->getParticipatedUserIds($decision);

        $pendingUserIds = array_diff($targetUserIds, $participatedUserIds);

        $users = User::whereIn('id', $pendingUserIds)->where('is_active', true)->get();

        foreach ($users as $user) {
            $this->line("Sending reminder to {$user->email} for decision: {$decision->title}");
            Mail::to($user->email)->queue(new DecisionReminderMail($decision, $user));
        }
    }

    private function getParticipatedUserIds(Decision $decision): array
    {
        $version = $decision->currentVersion;
        $status = $decision->status->value;

        $feedbackAuthors = \App\Models\Feedback::where('decision_version_id', $version->id)
            ->pluck('author_id')->toArray();
            
        $consentAuthors = \App\Models\Consent::where('decision_version_id', $version->id)
            ->pluck('user_id')->toArray();
        
        return array_unique(array_merge($feedbackAuthors, $consentAuthors));
    }
}
