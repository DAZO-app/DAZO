<?php

namespace Database\Seeders;

use App\Enums\ConsentSignal;
use App\Enums\DecisionNotificationLevel;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\DecisionVisibility;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Circle;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionAnimatorLog;
use App\Models\DecisionModel;
use App\Models\DecisionParticipant;
use App\Models\DecisionRelation;
use App\Models\DecisionUserSetting;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\FeedbackJoin;
use App\Models\FeedbackMessage;
use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DecisionSeeder extends Seeder
{
    private array $users;
    private array $circles;
    private array $categories;
    private array $labels;
    private array $models;
    private array $createdDecisions = [];

    public function run(): void
    {
        $this->loadReferences();

        foreach (DecisionDataProvider::decisions() as $idx => $data) {
            $this->createDecision($data, $idx);
        }

        $this->createRelations();
        $this->createUserSettings();
    }

    private function loadReferences(): void
    {
        $this->users = User::all()->keyBy('email')->toArray();
        // Index by name for easy lookup
        $this->circles = Circle::all()->keyBy('name')->toArray();
        $this->categories = Category::all()->keyBy('name')->toArray();
        $this->labels = Label::all()->keyBy('name')->toArray();
        $this->models = DecisionModel::all()->keyBy('name')->toArray();

        // Convert to id-keyed for convenience
        foreach ($this->users as $email => $u) {
            $this->users[$email] = $u;
        }
    }

    private function userId(string $email): ?string
    {
        return $this->users[$email]['id'] ?? null;
    }

    private function createDecision(array $data, int $idx): void
    {
        $circleId = $this->circles[$data['circle']]['id'] ?? null;
        $modelId = $this->models[$data['model']]['id'] ?? null;

        if (!$circleId) {
            $this->command->warn("Circle '{$data['circle']}' not found, skipping: {$data['title']}");
            return;
        }

        $createdAt = isset($data['created_ago'])
            ? now()->modify($data['created_ago'])
            : now()->subDays(rand(1, 10))->subHours(rand(0, 23));

        $deadline = isset($data['deadline'])
            ? now()->modify($data['deadline'])
            : null;

        $decision = Decision::create([
            'circle_id' => $circleId,
            'status' => $data['status'],
            'title' => $data['title'],
            'visibility' => $data['visibility'],
            'priority' => $data['priority'],
            'emergency_mode' => $data['emergency'],
            'model_id' => $modelId,
            'current_deadline' => $deadline,
            'revision_content' => $data['revision_content'] ?? null,
            'created_at' => $createdAt,
            'updated_at' => now(),
        ]);

        // Handle status_before_suspension
        if (isset($data['status_before'])) {
            \DB::table('decisions')->where('id', $decision->id)
                ->update(['status_before_suspension' => $data['status_before']]);
        }

        $this->createdDecisions[$idx] = $decision;

        // Assign categories
        $catIds = [];
        foreach ($data['categories'] as $catName) {
            if (isset($this->categories[$catName])) {
                $catIds[] = $this->categories[$catName]['id'];
            }
        }
        if ($catIds) $decision->categories()->sync($catIds);

        // Assign labels
        $labelIds = [];
        foreach ($data['labels'] as $labelName) {
            if (isset($this->labels[$labelName])) {
                $labelIds[] = $this->labels[$labelName]['id'];
            }
        }
        if ($labelIds) $decision->labels()->sync($labelIds);

        // Pick author and animator from circle members
        $authorEmail = $this->pickAuthor($data, $idx);
        $animatorEmail = $this->pickAnimator($data, $idx, $authorEmail);

        $authorId = $this->userId($authorEmail);
        $animatorId = $this->userId($animatorEmail);

        // Create participants
        if ($authorId) {
            DecisionParticipant::create([
                'decision_id' => $decision->id,
                'user_id' => $authorId,
                'role' => DecisionParticipantRole::AUTHOR,
                'added_at' => $createdAt,
            ]);
        }
        if ($animatorId && $animatorId !== $authorId) {
            DecisionParticipant::create([
                'decision_id' => $decision->id,
                'user_id' => $animatorId,
                'role' => DecisionParticipantRole::ANIMATOR,
                'added_at' => $createdAt,
            ]);

            DecisionAnimatorLog::create([
                'decision_id' => $decision->id,
                'animator_id' => $animatorId,
                'assigned_by' => $authorId,
                'assigned_at' => $createdAt,
            ]);
        }

        // Create versions
        $this->createVersions($decision, $data, $authorId, $createdAt);
    }

    private function pickAuthor(array $data, int $idx): string
    {
        $authors = ['admin@dazo.test', 'user@dazo.test', 'claire@dazo.test',
                     'david@dazo.test', 'emma@dazo.test', 'franck@dazo.test', 'hugo@dazo.test'];
        return $authors[$idx % count($authors)];
    }

    private function pickAnimator(array $data, int $idx, string $authorEmail): string
    {
        $animators = ['admin@dazo.test', 'claire@dazo.test', 'hugo@dazo.test'];
        $anim = $animators[$idx % count($animators)];
        if ($anim === $authorEmail) {
            $anim = $animators[($idx + 1) % count($animators)];
        }
        return $anim;
    }

    private function createVersions(Decision $decision, array $data, ?string $authorId, $createdAt): void
    {
        $versionCount = $data['versions'] ?? 1;
        $previousVersionId = null;

        for ($v = 1; $v <= $versionCount; $v++) {
            $isCurrent = ($v === $versionCount);

            if ($v === 1 && $versionCount > 1 && isset($data['v1_content'])) {
                $content = $data['v1_content'];
            } elseif ($v === 2 && $versionCount > 2 && isset($data['v2_content'])) {
                $content = $data['v2_content'];
            } elseif ($isCurrent) {
                $content = $data['content'];
            } else {
                $content = $data['content'];
            }

            $changeReason = null;
            if ($v === 2) $changeReason = $data['change_reason'] ?? 'Révision suite aux retours';
            if ($v === 3) $changeReason = $data['change_reason_v2'] ?? 'Seconde révision';

            $versionCreatedAt = (clone $createdAt)->addDays($v - 1);

            $version = DecisionVersion::create([
                'decision_id' => $decision->id,
                'author_id' => $authorId,
                'previous_version_id' => $previousVersionId,
                'version_number' => $v,
                'is_current' => $isCurrent,
                'content' => $content,
                'change_reason' => $changeReason,
                'created_at' => $versionCreatedAt,
                'updated_at' => $versionCreatedAt,
            ]);

            // Feedbacks on this version
            if ($isCurrent || $v === 1) {
                $targetVersion = isset($data['feedbacks']) ? $v : 0;
                foreach ($data['feedbacks'] as $fb) {
                    $fbVersion = $fb['version'] ?? $versionCount;
                    if ($fbVersion !== $v) continue;
                    $this->createFeedback($version, $fb);
                }
            }

            // Consents on this version
            if ($isCurrent || $v === 1) {
                foreach ($data['consents'] as $consent) {
                    $cVersion = $consent['version'] ?? $versionCount;
                    if ($cVersion !== $v) continue;
                    $this->createConsents($version, $consent);
                }
            }

            // Attachments on current version
            if ($isCurrent && ($data['attachments'] ?? 0) > 0) {
                $this->createAttachments($version, $data['attachments'], $authorId);
            }

            $previousVersionId = $version->id;
        }
    }

    private function createFeedback(DecisionVersion $version, array $fb): void
    {
        $authorId = $this->userId($fb['author']);
        if (!$authorId) return;

        $feedback = Feedback::create([
            'decision_version_id' => $version->id,
            'author_id' => $authorId,
            'type' => $fb['type'],
            'status' => $fb['status'],
            'content' => $fb['content'],
            'created_at' => $version->created_at->addHours(rand(1, 48)),
        ]);

        // Add messages if any (thread)
        if (isset($fb['messages'])) {
            foreach ($fb['messages'] as $msg) {
                FeedbackMessage::create([
                    'feedback_id' => $feedback->id,
                    'author_id' => $version->author_id, // Author responds
                    'content' => $msg,
                    'created_at' => $feedback->created_at->addHours(rand(1, 12)),
                ]);
            }
        }

        // Some feedbacks get joins (users who agree)
        if (in_array($fb['type'], ['objection', 'reaction']) && rand(0, 1)) {
            $otherUsers = collect($this->users)
                ->where('id', '!=', $authorId)
                ->take(rand(1, 2));
            foreach ($otherUsers as $u) {
                FeedbackJoin::create([
                    'feedback_id' => $feedback->id,
                    'user_id' => $u['id'],
                ]);
            }
        }
    }

    private function createConsents(DecisionVersion $version, array $consent): void
    {
        foreach ($consent['users'] as $email) {
            $userId = $this->userId($email);
            if (!$userId) continue;

            // Check for unique constraint
            $exists = Consent::where('decision_version_id', $version->id)
                ->where('user_id', $userId)
                ->where('phase', $consent['phase'])
                ->exists();
            if ($exists) continue;

            Consent::create([
                'decision_version_id' => $version->id,
                'user_id' => $userId,
                'signal' => $consent['signal'],
                'phase' => $consent['phase'],
                'created_at' => $version->created_at->addHours(rand(1, 72)),
            ]);
        }
    }

    private function createAttachments(DecisionVersion $version, int $count, ?string $uploaderId): void
    {
        $fakeFiles = [
            ['filename' => 'analyse-impact.pdf', 'mime' => 'application/pdf', 'size' => 245_000],
            ['filename' => 'budget-previsionnel.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => 89_000],
            ['filename' => 'maquette-v2.png', 'mime' => 'image/png', 'size' => 1_200_000],
            ['filename' => 'compte-rendu-reunion.pdf', 'mime' => 'application/pdf', 'size' => 156_000],
            ['filename' => 'planning-migration.pdf', 'mime' => 'application/pdf', 'size' => 310_000],
            ['filename' => 'benchmark-outils.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => 67_000],
            ['filename' => 'logo-proposition-A.svg', 'mime' => 'image/svg+xml', 'size' => 12_000],
            ['filename' => 'logo-proposition-B.svg', 'mime' => 'image/svg+xml', 'size' => 15_000],
            ['filename' => 'photo-bureaux-candidats.jpg', 'mime' => 'image/jpeg', 'size' => 2_400_000],
            ['filename' => 'devis-prestataire.pdf', 'mime' => 'application/pdf', 'size' => 98_000],
        ];

        $selected = array_slice($fakeFiles, $version->version_number % count($fakeFiles), $count);
        if (count($selected) < $count) {
            $selected = array_merge($selected, array_slice($fakeFiles, 0, $count - count($selected)));
        }

        foreach ($selected as $file) {
            Attachment::create([
                'decision_version_id' => $version->id,
                'uploader_id' => $uploaderId,
                'filename' => $file['filename'],
                's3_path' => 'seed-attachments/' . Str::uuid() . '/' . $file['filename'],
                'mime_type' => $file['mime'],
                'size_bytes' => $file['size'],
                'created_at' => $version->created_at->addMinutes(rand(5, 120)),
            ]);
        }
    }

    private function createRelations(): void
    {
        // Link decision 9 (Politique remboursement) derives_from decision 11 (Budget formation)
        if (isset($this->createdDecisions[8], $this->createdDecisions[10])) {
            DecisionRelation::create([
                'source_decision_id' => $this->createdDecisions[8]->id,
                'target_decision_id' => $this->createdDecisions[10]->id,
                'relation_type' => 'derives_from',
            ]);
        }

        // Decision 6 (patch sécurité) blocks decision 3 (migration PG)
        if (isset($this->createdDecisions[5], $this->createdDecisions[2])) {
            DecisionRelation::create([
                'source_decision_id' => $this->createdDecisions[5]->id,
                'target_decision_id' => $this->createdDecisions[2]->id,
                'relation_type' => 'blocks',
            ]);
        }
    }

    private function createUserSettings(): void
    {
        $settings = [
            // Alice a mis en favoris les décisions stratégiques
            ['admin@dazo.test', 7, true, DecisionNotificationLevel::ALL],
            ['admin@dazo.test', 13, true, DecisionNotificationLevel::PHASE_CHANGE],
            // Bob suit la convention Git avec notifs minimales
            ['user@dazo.test', 9, true, DecisionNotificationLevel::RELEVANT],
            ['user@dazo.test', 4, false, DecisionNotificationLevel::NONE],
            // Claire suit activement les décisions tech
            ['claire@dazo.test', 2, true, DecisionNotificationLevel::ALL],
            ['claire@dazo.test', 5, true, DecisionNotificationLevel::ALL],
            ['claire@dazo.test', 16, true, DecisionNotificationLevel::ALL],
            // David a coupé les notifs sur certaines
            ['david@dazo.test', 6, false, DecisionNotificationLevel::NONE],
            ['david@dazo.test', 17, true, DecisionNotificationLevel::PHASE_CHANGE],
        ];

        foreach ($settings as [$email, $decisionIdx, $fav, $level]) {
            $userId = $this->userId($email);
            $decision = $this->createdDecisions[$decisionIdx] ?? null;
            if (!$userId || !$decision) continue;

            DecisionUserSetting::updateOrCreate(
                ['user_id' => $userId, 'decision_id' => $decision->id],
                ['is_favorite' => $fav, 'notification_level' => $level]
            );
        }
    }
}
