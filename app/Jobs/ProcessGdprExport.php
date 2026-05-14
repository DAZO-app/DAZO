<?php

namespace App\Jobs;

use App\Mail\GdprExportMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessGdprExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $data = [
            'profile' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => (string) $this->user->role?->value,
                'is_active' => $this->user->is_active,
                'created_at' => $this->user->created_at,
                'avatar_url' => $this->user->avatar_url,
            ],
            'circles' => $this->user->circles()->with('circle:id,name')->get()->map(fn($m) => [
                'circle_id' => $m->circle_id,
                'circle_name' => $m->circle?->name,
                'role' => $m->role,
                'joined_at' => $m->created_at,
            ]),
            'feedbacks' => \App\Models\Feedback::where('author_id', $this->user->id)->get()->map(fn($f) => [
                'decision_version_id' => $f->decision_version_id,
                'type' => $f->type?->value,
                'status' => $f->status?->value,
                'content' => $f->content,
                'created_at' => $f->created_at,
            ]),
            'participations' => \App\Models\DecisionParticipant::where('user_id', $this->user->id)->with('decision:id,title')->get()->map(fn($p) => [
                'decision_id' => $p->decision_id,
                'decision_title' => $p->decision?->title,
                'role' => $p->role,
                'created_at' => $p->created_at,
            ]),
            'social_accounts' => $this->user->socialAccounts()->get()->map(fn($s) => [
                'provider' => $s->provider,
                'provider_user_id' => $s->provider_user_id,
                'created_at' => $s->created_at,
            ]),
            'decision_settings' => \App\Models\DecisionUserSetting::where('user_id', $this->user->id)->get()->map(fn($s) => [
                'decision_id' => $s->decision_id,
                'is_favorite' => $s->is_favorite,
                'notification_level' => $s->notification_level?->value,
            ]),
            'notification_preferences' => \App\Models\NotificationPreference::where('user_id', $this->user->id)->get()->map(fn($p) => [
                'category' => $p->category,
                'email_enabled' => $p->email_enabled,
                'web_enabled' => $p->web_enabled,
            ]),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Ensure directory exists
        if (!Storage::disk('local')->exists('gdpr')) {
            Storage::disk('local')->makeDirectory('gdpr');
        }

        $fileName = 'gdpr/export-' . $this->user->id . '-' . time() . '.json';
        Storage::disk('local')->put($fileName, $json);
        $filePath = Storage::disk('local')->path($fileName);

        try {
            Mail::to($this->user->email)->send(new GdprExportMail($this->user, $filePath));

            // Create notification
            \App\Models\Notification::create([
                'user_id' => $this->user->id,
                'category' => \App\Enums\NotificationCategory::SYSTEM,
                'event_type' => \App\Enums\NotificationEventType::GDPR_EXPORT_READY,
                'payload' => [
                    'message' => 'Votre archive de données personnelles est prête et vous a été envoyée par e-mail.',
                ],
            ]);
        } finally {
            // Delete the file after sending or if it fails
            if (Storage::disk('local')->exists($fileName)) {
                Storage::disk('local')->delete($fileName);
            }
        }
    }
}
