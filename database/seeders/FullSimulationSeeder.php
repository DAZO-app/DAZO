<?php

namespace Database\Seeders;

use App\Enums\NotificationCategory;
use App\Enums\CircleMemberRole;
use App\Enums\CircleType;
use App\Enums\DecisionStatus;
use App\Enums\DecisionVisibility;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionRelationType;
use App\Enums\DecisionNotificationLevel;
use App\Enums\ConsentSignal;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Enums\UserRole;
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
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FullSimulationSeeder extends Seeder
{
    private const ATTACHMENTS_DEFERRED = false;
    private const ATTACHMENT_POOL_PATH = 'attachments/seed-pool';
    private const RANDOM_SEED = 20260513;

    private const CONFIG = [
        'user_count' => 50,
        'circle_count' => 10,
        'decision_count' => 200,
        'attachments_per_decision_min' => 0,
        'attachments_per_decision_max' => 5,
        'public_decision_ratio' => 0.90,
        'public_attachment_ratio' => 0.90,
    ];

    private bool $attachmentPoolWarningShown = false;

    /**
     * Run the full simulation seed.
     */
    public function run(): void
    {
        $this->seedRandomGenerators();

        DB::transaction(function (): void {
            $this->command?->info('Starting DAZO full simulation seed...');

            $this->seedFullSimulation();
        });

        $this->printAndValidateSummary();
    }

    private function seedFullSimulation(): void
    {
        $this->seedTaxonomies();
        $users = $this->seedUsers();
        $circles = $this->seedCircles($users);
        $decisions = $this->seedDecisions($circles, $users);
        $this->seedDecisionRelations($decisions);
        $this->seedDecisionUserSettings($decisions, $users);

        if (self::ATTACHMENTS_DEFERRED) {
            $this->command?->warn('Attachment generation is deferred until test files are provided.');
        }
    }

    private function seedDecisions(Collection $circles, Collection $users): Collection
    {
        $categories = Category::all()->keyBy('name');
        $labels = Label::all()->keyBy('name');
        $models = DecisionModel::all()->keyBy('name');
        $statusPlan = $this->decisionStatusPlan();
        $privateSlots = collect($statusPlan)
            ->keys()
            ->filter(fn (int $index): bool => $index < 12 || in_array($index, [31, 47, 68, 89, 111, 133, 155, 177], true))
            ->flip();
        $decisions = collect();

        foreach ($statusPlan as $index => $status) {
            $number = $index + 1;
            $categoryNames = $this->categoryNamesForDecision($number, $status);
            $primaryCategory = $categoryNames[0];
            $labelNames = $this->labelNamesForDecision($number, $status);
            $isPrivate = $privateSlots->has($index);
            $emergencyMode = in_array($number, [6, 42, 88, 134, 176], true);
            $priority = $emergencyMode ? 2 : (int) $this->weightedPick([0 => 70, 1 => 25, 2 => 5]);
            $circle = $this->circleForDecision($circles, $primaryCategory, $number);
            $model = $emergencyMode
                ? $models->get('Décision Urgente')
                : ($number % 5 === 0 ? $models->get('Avis Sollicité') : $models->get('Consentement (Standard)'));

            $decision = Decision::updateOrCreate(
                ['title' => $this->decisionTitle($number, $primaryCategory, $status)],
                [
                    'circle_id' => $circle->id,
                    'status' => $status,
                    'visibility' => $isPrivate ? DecisionVisibility::PRIVATE : DecisionVisibility::PUBLIC,
                    'priority' => $priority,
                    'emergency_mode' => $emergencyMode,
                    'model_id' => $model?->id,
                    'current_deadline' => $this->deadlineForStatus($status, $emergencyMode),
                    'revision_content' => $status === DecisionStatus::REVISION->value
                        ? $this->revisionContent($number, $primaryCategory)
                        : null,
                    'revision_attachment_ids' => null,
                    'reminder_sent' => $status === DecisionStatus::LAPSED->value,
                    'status_before_suspension' => $status === DecisionStatus::SUSPENDED->value
                        ? $this->statusBeforeSuspension($number)
                        : null,
                    'share_count' => $status === DecisionStatus::DRAFT->value ? 0 : mt_rand(0, 18),
                    'created_at' => now()->subDays(($number % 45) + 1)->subHours($number % 11),
                    'updated_at' => now()->subDays($number % 7),
                ]
            );

            $decision->categories()->sync(
                collect($categoryNames)
                    ->map(fn (string $name): ?string => $categories->get($name)?->id)
                    ->filter()
                    ->values()
                    ->all()
            );
            $decision->labels()->sync(
                collect($labelNames)
                    ->map(fn (string $name): ?string => $labels->get($name)?->id)
                    ->filter()
                    ->values()
                    ->all()
            );

            $this->seedDecisionVersions($decision, $number, $primaryCategory, $status, $users);
            $this->seedDecisionParticipants($decision->fresh(['circle.members', 'decisionModel', 'currentVersion']), $users, $number);
            $this->seedInteractionsForDecision($decision->fresh(['participants.user', 'currentVersion']), $number);
            $this->seedConsentsForDecision($decision->fresh(['participants.user', 'currentVersion']), $number);
            $this->seedAttachmentsForDecisionVersion($decision->fresh(['participants.user', 'currentVersion']), $number);
            $decisions->push($decision);
        }

        $this->command?->info("Seeded {$decisions->count()} simulation decisions.");

        return $decisions;
    }

    private function seedAttachmentsForDecisionVersion(Decision $decision, int $number): void
    {
        $version = $decision->currentVersion;

        if (!$version) {
            return;
        }

        Attachment::whereIn(
            'decision_version_id',
            $decision->versions()->pluck('id')
        )->delete();

        $poolFiles = $this->attachmentPoolFiles();

        if ($poolFiles->isEmpty()) {
            if (!$this->attachmentPoolWarningShown) {
                $this->command?->warn(
                    'No fake attachment pool found. Run ./dazo-generate-fake-attachments.sh --clean before seeding attachments.'
                );
                $this->attachmentPoolWarningShown = true;
            }
            return;
        }

        $participants = $decision->participants
            ->filter(fn (DecisionParticipant $participant): bool => $participant->role !== DecisionParticipantRole::EXCLUDED)
            ->pluck('user')
            ->filter()
            ->values();

        if ($participants->isEmpty()) {
            return;
        }

        $attachmentCount = mt_rand(
            self::CONFIG['attachments_per_decision_min'],
            self::CONFIG['attachments_per_decision_max']
        );

        if ($attachmentCount === 0) {
            return;
        }

        $selectedFiles = $poolFiles->shuffle()->take($attachmentCount)->values();

        foreach ($selectedFiles as $index => $path) {
            $uploader = $participants[($number + $index) % $participants->count()];

            Attachment::create([
                'decision_version_id' => $version->id,
                'uploader_id' => $uploader->id,
                'filename' => basename($path),
                's3_path' => $path,
                'mime_type' => $this->mimeTypeForAttachmentPath($path),
                'size_bytes' => Storage::disk('local')->size($path) ?: 0,
                'is_private' => (($number + $index) % 10) === 0,
                'created_at' => ($decision->created_at ?? now())->addHours(10 + $index),
                'updated_at' => ($decision->created_at ?? now())->addHours(10 + $index),
            ]);
        }
    }

    private function attachmentPoolFiles(): Collection
    {
        return collect(Storage::disk('local')->files(self::ATTACHMENT_POOL_PATH))
            ->filter(fn (string $path): bool => preg_match('/\.(txt|md|csv|json)$/i', $path) === 1)
            ->values();
    }

    private function mimeTypeForAttachmentPath(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'md', 'txt' => 'text/plain',
            'csv' => 'text/csv',
            'json' => 'application/json',
            default => Storage::disk('local')->mimeType($path) ?: 'application/octet-stream',
        };
    }

    private function seedDecisionRelations(Collection $decisions): void
    {
        $decisionsByNumber = $decisions
            ->values()
            ->mapWithKeys(fn (Decision $decision, int $index): array => [$index + 1 => $decision]);

        DecisionRelation::whereIn('source_decision_id', $decisions->pluck('id'))
            ->orWhereIn('target_decision_id', $decisions->pluck('id'))
            ->delete();

        $relations = [
            [10, 2, DecisionRelationType::DERIVES_FROM],
            [20, 7, DecisionRelationType::DERIVES_FROM],
            [30, 12, DecisionRelationType::DERIVES_FROM],
            [40, 18, DecisionRelationType::BLOCKS],
            [50, 22, DecisionRelationType::DERIVES_FROM],
            [60, 33, DecisionRelationType::BLOCKS],
            [75, 41, DecisionRelationType::DERIVES_FROM],
            [88, 45, DecisionRelationType::BLOCKS],
            [96, 52, DecisionRelationType::DERIVES_FROM],
            [110, 67, DecisionRelationType::BLOCKS],
            [125, 72, DecisionRelationType::DERIVES_FROM],
            [134, 84, DecisionRelationType::BLOCKS],
            [150, 93, DecisionRelationType::DERIVES_FROM],
            [165, 101, DecisionRelationType::BLOCKS],
            [176, 118, DecisionRelationType::BLOCKS],
            [190, 137, DecisionRelationType::DERIVES_FROM],
        ];

        foreach ($relations as [$sourceNumber, $targetNumber, $type]) {
            $source = $decisionsByNumber->get($sourceNumber);
            $target = $decisionsByNumber->get($targetNumber);

            if (!$source || !$target || $source->id === $target->id) {
                continue;
            }

            DecisionRelation::updateOrCreate(
                [
                    'source_decision_id' => $source->id,
                    'target_decision_id' => $target->id,
                    'relation_type' => $type->value,
                ],
                []
            );
        }

        $this->command?->info('Seeded simulation decision relations.');
    }

    private function seedDecisionUserSettings(Collection $decisions, Collection $users): void
    {
        DecisionUserSetting::whereIn('decision_id', $decisions->pluck('id'))->delete();

        $inactiveUsers = $users->where('is_active', false)->values();

        foreach ($decisions->values() as $index => $decision) {
            $number = $index + 1;
            $participants = $decision->participants()->with('user')->get()
                ->pluck('user')
                ->filter()
                ->values();

            if ($participants->isEmpty()) {
                continue;
            }

            $settingUsers = $participants->shuffle()->take($decision->emergency_mode ? 8 : mt_rand(2, 5));

            foreach ($settingUsers as $userIndex => $user) {
                DecisionUserSetting::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'decision_id' => $decision->id,
                    ],
                    [
                        'is_favorite' => $decision->emergency_mode || ($number + $userIndex) % 4 === 0,
                        'notification_level' => $this->notificationLevelForDecisionSetting($decision, $number, $userIndex)->value,
                    ]
                );
            }

            if ($number % 9 === 0 && $inactiveUsers->isNotEmpty()) {
                $inactiveUser = $inactiveUsers[$number % $inactiveUsers->count()];
                DecisionUserSetting::updateOrCreate(
                    [
                        'user_id' => $inactiveUser->id,
                        'decision_id' => $decision->id,
                    ],
                    [
                        'is_favorite' => false,
                        'notification_level' => DecisionNotificationLevel::NONE->value,
                    ]
                );
            }
        }

        $this->command?->info('Seeded simulation decision user settings.');
    }

    private function notificationLevelForDecisionSetting(
        Decision $decision,
        int $number,
        int $userIndex
    ): DecisionNotificationLevel {
        if ($decision->emergency_mode) {
            return $userIndex % 3 === 0
                ? DecisionNotificationLevel::ALL
                : DecisionNotificationLevel::RELEVANT;
        }

        return match (($number + $userIndex) % 4) {
            0 => DecisionNotificationLevel::ALL,
            1 => DecisionNotificationLevel::RELEVANT,
            2 => DecisionNotificationLevel::PHASE_CHANGE,
            default => DecisionNotificationLevel::NONE,
        };
    }

    private function seedConsentsForDecision(Decision $decision, int $number): void
    {
        $version = $decision->currentVersion;

        if (!$version) {
            return;
        }

        Consent::whereIn(
            'decision_version_id',
            $decision->versions()->pluck('id')
        )->delete();

        $status = $decision->status instanceof DecisionStatus ? $decision->status->value : $decision->status;
        $phase = $this->consentPhaseForStatus($status);

        if (!$phase) {
            return;
        }

        $actors = $decision->participants
            ->filter(fn (DecisionParticipant $participant): bool => $participant->role !== DecisionParticipantRole::EXCLUDED)
            ->pluck('user')
            ->filter()
            ->values();

        if ($actors->isEmpty()) {
            return;
        }

        $consentingActors = $this->consentingActorsForDecision($actors, $status, $number);
        $defaultSignal = $this->defaultConsentSignalForPhase($phase);

        foreach ($consentingActors as $index => $actor) {
            $signal = $index % 5 === 0 ? ConsentSignal::ABSTENTION : $defaultSignal;

            Consent::updateOrCreate(
                [
                    'decision_version_id' => $version->id,
                    'user_id' => $actor->id,
                    'phase' => $phase,
                ],
                [
                    'signal' => $signal->value,
                    'created_at' => ($decision->created_at ?? now())->addHours(8 + $index),
                    'updated_at' => ($decision->created_at ?? now())->addHours(8 + $index),
                ]
            );
        }
    }

    private function consentPhaseForStatus(string $status): ?string
    {
        return match ($status) {
            DecisionStatus::CLARIFICATION->value => DecisionStatus::CLARIFICATION->value,
            DecisionStatus::REACTION->value => DecisionStatus::REACTION->value,
            DecisionStatus::OBJECTION->value,
            DecisionStatus::REVISION->value,
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value => DecisionStatus::OBJECTION->value,
            default => null,
        };
    }

    private function defaultConsentSignalForPhase(string $phase): ConsentSignal
    {
        return match ($phase) {
            DecisionStatus::CLARIFICATION->value => ConsentSignal::NO_QUESTIONS,
            DecisionStatus::REACTION->value => ConsentSignal::NO_REACTION,
            DecisionStatus::OBJECTION->value => ConsentSignal::NO_OBJECTION,
            default => ConsentSignal::ABSTENTION,
        };
    }

    private function consentingActorsForDecision(Collection $actors, string $status, int $number): Collection
    {
        $actorCount = $actors->count();

        if ($actorCount === 0) {
            return collect();
        }

        $targetCount = match ($status) {
            DecisionStatus::CLARIFICATION->value => mt_rand(1, max(1, (int) floor($actorCount * 0.55))),
            DecisionStatus::REACTION->value => mt_rand(1, max(1, (int) floor($actorCount * 0.5))),
            DecisionStatus::OBJECTION->value => mt_rand(1, max(1, (int) floor($actorCount * 0.45))),
            DecisionStatus::REVISION->value => mt_rand(1, max(1, (int) floor($actorCount * 0.35))),
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value => max(1, (int) ceil($actorCount * 0.8)),
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value => max(1, (int) floor($actorCount * 0.25)),
            default => 0,
        };

        if ($number % 11 === 0 && !in_array($status, [
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
        ], true)) {
            $targetCount = max(1, $targetCount - 1);
        }

        return $actors->shuffle()->take(min($targetCount, $actorCount));
    }

    private function seedInteractionsForDecision(Decision $decision, int $number): void
    {
        $version = $decision->currentVersion;

        if (!$version) {
            return;
        }

        Feedback::whereIn(
            'decision_version_id',
            $decision->versions()->pluck('id')
        )->delete();

        $actors = $decision->participants
            ->filter(fn (DecisionParticipant $participant): bool => $participant->role !== DecisionParticipantRole::EXCLUDED)
            ->pluck('user')
            ->filter()
            ->values();

        if ($actors->isEmpty()) {
            return;
        }

        foreach ($this->feedbackPlanForDecision($decision, $number) as $index => $feedbackData) {
            $author = $actors[($number + $index) % $actors->count()];
            $feedback = Feedback::create([
                'decision_version_id' => $version->id,
                'author_id' => $author->id,
                'type' => $feedbackData['type'],
                'status' => $feedbackData['status'],
                'content' => $feedbackData['content'],
            ]);
            $feedback->forceFill([
                'created_at' => ($decision->created_at ?? now())->addHours(2 + $index),
                'updated_at' => ($decision->created_at ?? now())->addHours(3 + $index),
            ])->save();

            $this->seedFeedbackMessages($feedback, $actors, $index);
            $this->seedFeedbackJoins($feedback, $actors, $author, $index);
        }
    }

    private function feedbackPlanForDecision(Decision $decision, int $number): array
    {
        $status = $decision->status instanceof DecisionStatus ? $decision->status->value : $decision->status;
        $count = match ($status) {
            DecisionStatus::DRAFT->value => $number % 6 === 0 ? 1 : 0,
            DecisionStatus::CLARIFICATION->value => mt_rand(2, 7),
            DecisionStatus::REACTION->value => mt_rand(3, 10),
            DecisionStatus::OBJECTION->value => mt_rand(4, 12),
            DecisionStatus::REVISION->value => mt_rand(3, 8),
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value => mt_rand(2, 7),
            DecisionStatus::SUSPENDED->value => mt_rand(1, 4),
            default => 0,
        };
        $plan = [];

        for ($index = 0; $index < $count; $index++) {
            $type = $this->feedbackTypeForStatus($status, $index);
            $plan[] = [
                'type' => $type->value,
                'status' => $this->feedbackStatusForIndex($status, $index)->value,
                'content' => $this->feedbackContent($type, $status, $number, $index),
            ];
        }

        return $plan;
    }

    private function feedbackTypeForStatus(string $status, int $index): FeedbackType
    {
        return match ($status) {
            DecisionStatus::CLARIFICATION->value => FeedbackType::CLARIFICATION,
            DecisionStatus::REACTION->value => FeedbackType::REACTION,
            DecisionStatus::OBJECTION->value => $index % 3 === 0 ? FeedbackType::SUGGESTION : FeedbackType::OBJECTION,
            DecisionStatus::REVISION->value => $index % 2 === 0 ? FeedbackType::OBJECTION : FeedbackType::SUGGESTION,
            DecisionStatus::DRAFT->value => FeedbackType::CLARIFICATION,
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value => $index % 2 === 0 ? FeedbackType::OBJECTION : FeedbackType::REACTION,
            default => $index % 3 === 0 ? FeedbackType::CLARIFICATION : FeedbackType::REACTION,
        };
    }

    private function feedbackStatusForIndex(string $status, int $index): FeedbackStatus
    {
        if (in_array($status, [
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
        ], true)) {
            return FeedbackStatus::TREATED;
        }

        if (in_array($status, [
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value,
        ], true) && $index % 3 === 0) {
            return FeedbackStatus::ACKNOWLEDGED;
        }

        $statuses = [
            FeedbackStatus::SUBMITTED,
            FeedbackStatus::TREATED,
            FeedbackStatus::REJECTED,
            FeedbackStatus::ACKNOWLEDGED,
            FeedbackStatus::WITHDRAWN,
            FeedbackStatus::IGNORED,
        ];

        return $statuses[$index % count($statuses)];
    }

    private function feedbackContent(FeedbackType $type, string $status, int $number, int $index): string
    {
        return match ($type) {
            FeedbackType::CLARIFICATION => [
                "Pouvez-vous préciser le périmètre exact de la proposition {$number} ?",
                "Quel indicateur permettra de savoir si cette décision est réussie ?",
                "Le cercle concerné dispose-t-il déjà des ressources nécessaires ?",
            ][$index % 3],
            FeedbackType::REACTION => [
                "Je suis favorable à cette orientation, elle clarifie un vrai point de tension.",
                "Réaction réservée : l'intention est bonne, mais le calendrier me semble ambitieux.",
                "Cette proposition me paraît utile si l'on garde un bilan court après expérimentation.",
            ][$index % 3],
            FeedbackType::OBJECTION => [
                "Objection : le risque opérationnel est trop élevé sans étape pilote.",
                "Objection : le budget et la charge humaine ne sont pas encore suffisamment explicités.",
                "Objection : la décision pourrait exclure des membres directement concernés.",
            ][$index % 3],
            FeedbackType::SUGGESTION => [
                "Suggestion : réduire le périmètre à un seul cercle pour commencer.",
                "Suggestion : ajouter une revue à mi-parcours avant adoption définitive.",
                "Suggestion : documenter les impacts attendus dans une annexe courte.",
            ][$index % 3],
        };
    }

    private function seedFeedbackMessages(Feedback $feedback, Collection $actors, int $index): void
    {
        if ($index % 2 !== 0 || $actors->count() < 2) {
            return;
        }

        $responder = $actors[($index + 1) % $actors->count()];
        FeedbackMessage::create([
            'feedback_id' => $feedback->id,
            'author_id' => $responder->id,
            'content' => match ($feedback->type) {
                FeedbackType::CLARIFICATION => 'Merci, je complète la proposition avec cette précision.',
                FeedbackType::OBJECTION => 'Objection reçue, je propose de la traiter dans une version révisée.',
                FeedbackType::SUGGESTION => 'Bonne piste, je l’ajoute aux options de compromis.',
                default => 'Retour pris en compte pour la suite du processus.',
            },
        ]);
    }

    private function seedFeedbackJoins(Feedback $feedback, Collection $actors, User $author, int $index): void
    {
        if (!in_array($feedback->type, [FeedbackType::REACTION, FeedbackType::OBJECTION], true) || $index % 2 !== 0) {
            return;
        }

        $supporters = $actors
            ->reject(fn (User $user): bool => $user->id === $author->id)
            ->shuffle()
            ->take(min(2, max(0, $actors->count() - 1)));

        foreach ($supporters as $supporter) {
            FeedbackJoin::updateOrCreate([
                'feedback_id' => $feedback->id,
                'user_id' => $supporter->id,
            ]);
        }
    }

    private function seedDecisionParticipants(Decision $decision, Collection $users, int $number): void
    {
        $currentVersion = $decision->currentVersion;
        $activeUsers = $users->where('is_active', true)->values();
        $circleMembers = $decision->circle?->members()->with('user')->get() ?? collect();
        $circleUsers = $circleMembers
            ->pluck('user')
            ->filter()
            ->where('is_active', true)
            ->values();
        $author = $currentVersion?->author ?? $circleUsers->first() ?? $activeUsers->first();
        $animator = $this->animatorForDecision($decision, $author, $circleMembers, $activeUsers, $number);
        $participantCount = $decision->visibility === DecisionVisibility::PRIVATE
            ? mt_rand(5, 9)
            : mt_rand(3, 12);

        $participantPool = $circleUsers->isNotEmpty() ? $circleUsers : $activeUsers;
        $participants = $participantPool
            ->reject(fn (User $user): bool => in_array($user->id, [$author?->id, $animator?->id], true))
            ->shuffle()
            ->take($participantCount);

        DecisionParticipant::where('decision_id', $decision->id)->delete();
        DecisionAnimatorLog::where('decision_id', $decision->id)->delete();

        if ($author) {
            $this->createDecisionParticipant($decision, $author, DecisionParticipantRole::AUTHOR);
        }

        if ($animator && $animator->id !== $author?->id) {
            $this->createDecisionParticipant($decision, $animator, DecisionParticipantRole::ANIMATOR);
            DecisionAnimatorLog::create([
                'decision_id' => $decision->id,
                'animator_id' => $animator->id,
                'assigned_by' => $author?->id,
                'assigned_at' => ($decision->created_at ?? now())->addHours(1),
            ]);
        }

        foreach ($participants as $participant) {
            $this->createDecisionParticipant($decision, $participant, DecisionParticipantRole::PARTICIPANT);
        }

        if ($number % 13 === 0) {
            $alreadyParticipantIds = collect([$author?->id, $animator?->id])
                ->merge($participants->pluck('id'))
                ->filter()
                ->all();
            $excluded = $activeUsers
                ->reject(fn (User $user): bool => in_array($user->id, $alreadyParticipantIds, true))
                ->shuffle()
                ->first();

            if ($excluded) {
                $this->createDecisionParticipant($decision, $excluded, DecisionParticipantRole::EXCLUDED);
            }
        }
    }

    private function animatorForDecision(
        Decision $decision,
        ?User $author,
        Collection $circleMembers,
        Collection $activeUsers,
        int $number
    ): ?User {
        if ($decision->status === DecisionStatus::DRAFT) {
            return $number % 4 === 0 ? $this->fallbackAnimator($activeUsers, $author) : null;
        }

        $animators = $circleMembers
            ->filter(fn ($member): bool => $member->role === CircleMemberRole::ANIMATOR)
            ->pluck('user')
            ->filter()
            ->where('is_active', true)
            ->values();

        if ($decision->decisionModel?->requires_distinct_animator) {
            $animators = $animators->reject(fn (User $user): bool => $user->id === $author?->id)->values();
        }

        return $animators->first() ?? $this->fallbackAnimator($activeUsers, $author);
    }

    private function fallbackAnimator(Collection $activeUsers, ?User $author): ?User
    {
        return $activeUsers
            ->where('is_global_animator', true)
            ->reject(fn (User $user): bool => $user->id === $author?->id)
            ->first();
    }

    private function createDecisionParticipant(Decision $decision, User $user, DecisionParticipantRole $role): void
    {
        DecisionParticipant::create([
            'decision_id' => $decision->id,
            'user_id' => $user->id,
            'role' => $role,
            'added_at' => $decision->created_at ?? now(),
        ]);
    }

    private function seedDecisionVersions(
        Decision $decision,
        int $number,
        string $category,
        string $status,
        Collection $users
    ): void {
        $versionCount = $this->versionCountForStatus($status, $number);
        $author = $this->versionAuthorForDecision($users, $number);
        $previousVersionId = null;
        $baseCreatedAt = $decision->created_at ?? now()->subDays(10);

        DecisionVersion::where('decision_id', $decision->id)
            ->where('version_number', '>', $versionCount)
            ->delete();
        DecisionVersion::where('decision_id', $decision->id)->update(['is_current' => false]);

        for ($versionNumber = 1; $versionNumber <= $versionCount; $versionNumber++) {
            $isCurrent = $versionNumber === $versionCount;
            $createdAt = (clone $baseCreatedAt)->addDays($versionNumber - 1)->addHours($versionNumber);
            $content = $this->decisionContentForVersion($number, $category, $status, $versionNumber, $versionCount);

            $version = DecisionVersion::updateOrCreate(
                [
                    'decision_id' => $decision->id,
                    'version_number' => $versionNumber,
                ],
                [
                    'author_id' => $author->id,
                    'previous_version_id' => $previousVersionId,
                    'is_current' => $isCurrent,
                    'content' => $content,
                    'change_reason' => $versionNumber === 1 ? null : $this->changeReasonForVersion($versionNumber, $status),
                    'created_at' => $createdAt,
                    'updated_at' => $isCurrent ? now()->subDays($number % 5) : $createdAt,
                ]
            );

            $previousVersionId = $version->id;
        }
    }

    private function versionCountForStatus(string $status, int $number): int
    {
        if (in_array($status, [
            DecisionStatus::REVISION->value,
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
            DecisionStatus::ABANDONED->value,
            DecisionStatus::LAPSED->value,
            DecisionStatus::DESERTED->value,
        ], true)) {
            return 2 + ($number % 3);
        }

        if ($status === DecisionStatus::SUSPENDED->value) {
            return 2;
        }

        return $number % 10 === 0 ? 2 : 1;
    }

    private function versionAuthorForDecision(Collection $users, int $number): User
    {
        $activeUsers = $users->where('is_active', true)->values();

        return $activeUsers[($number - 1) % $activeUsers->count()];
    }

    private function decisionContentForVersion(
        int $number,
        string $category,
        string $status,
        int $versionNumber,
        int $versionCount
    ): string {
        $content = $this->decisionContent($number, $category, $status);

        if ($versionNumber === 1 && $versionCount > 1) {
            return $content
                . "\n\n## Note de version\n"
                . "Version initiale volontairement large, destinée à ouvrir la discussion.";
        }

        if ($versionNumber < $versionCount) {
            return $content
                . "\n\n## Note de version\n"
                . "Version intermédiaire intégrant une partie des retours déjà reçus.";
        }

        if ($versionCount > 1) {
            return $content
                . "\n\n## Note de version\n"
                . "Version courante avec périmètre stabilisé, risques clarifiés et prochaines étapes resserrées.";
        }

        return $content;
    }

    private function changeReasonForVersion(int $versionNumber, string $status): string
    {
        $reasons = [
            'Clarification intégrée suite aux questions du cercle',
            'Objection traitée par réduction du périmètre',
            'Budget ajusté après analyse complémentaire',
            'Délai modifié pour réduire le risque opérationnel',
            'Critères de succès reformulés avant décision finale',
        ];

        if ($status === DecisionStatus::REVISION->value) {
            return 'Révision suite aux objections et suggestions reçues';
        }

        return $reasons[($versionNumber - 2) % count($reasons)];
    }

    private function decisionStatusPlan(): array
    {
        return array_merge(
            array_fill(0, 30, DecisionStatus::DRAFT->value),
            array_fill(0, 40, DecisionStatus::CLARIFICATION->value),
            array_fill(0, 60, DecisionStatus::REACTION->value),
            array_fill(0, 40, DecisionStatus::OBJECTION->value),
            array_fill(0, 5, DecisionStatus::REVISION->value),
            array_fill(0, 3, DecisionStatus::SUSPENDED->value),
            array_fill(0, 10, DecisionStatus::ADOPTED->value),
            array_fill(0, 5, DecisionStatus::ABANDONED->value),
            array_fill(0, 3, DecisionStatus::LAPSED->value),
            array_fill(0, 2, DecisionStatus::DESERTED->value),
            array_fill(0, 2, DecisionStatus::ADOPTED_OVERRIDE->value),
        );
    }

    private function categoryNamesForDecision(int $number, string $status): array
    {
        $primaryByModulo = [
            'Stratégie',
            'RH',
            'Finance',
            'Tech',
            'Juridique',
            'Produit',
            'Opérations',
            'Partenariats',
            'Communication',
            'Sécurité',
        ];
        $categories = [$primaryByModulo[$number % count($primaryByModulo)]];

        if ($number % 7 === 0) {
            $categories[] = 'Stratégie';
        }

        if (in_array($status, [DecisionStatus::REVISION->value, DecisionStatus::SUSPENDED->value], true)) {
            $categories[] = 'Juridique';
        }

        return array_values(array_unique($categories));
    }

    private function labelNamesForDecision(int $number, string $status): array
    {
        $labels = [];

        if ($number % 3 === 0) {
            $labels[] = 'Quick-win';
        }

        if ($number % 4 === 0) {
            $labels[] = 'Long terme';
        }

        if ($number % 5 === 0) {
            $labels[] = 'Expérimental';
        }

        if ($number % 8 === 0) {
            $labels[] = 'Dépendance externe';
        }

        if ($number % 9 === 0) {
            $labels[] = 'À communiquer';
        }

        if (in_array($status, [DecisionStatus::DRAFT->value, DecisionStatus::SUSPENDED->value], true) && $number % 2 === 0) {
            $labels[] = 'Confidentiel';
        }

        if (in_array($status, [DecisionStatus::OBJECTION->value, DecisionStatus::REVISION->value], true)) {
            $labels[] = 'À arbitrer';
        }

        if ($number % 17 === 0) {
            $labels[] = 'Bloquant';
            $labels[] = 'Risque élevé';
        }

        if (in_array($number, [6, 42, 88, 134, 176], true)) {
            $labels[] = 'Urgent';
        }

        if ($labels === []) {
            $labels[] = 'Récurrent';
        }

        return array_values(array_unique($labels));
    }

    private function circleForDecision(Collection $circles, string $category, int $number): Circle
    {
        $preferredCircle = match ($category) {
            'Produit' => 'Simulation - Produit & UX',
            'Tech', 'Sécurité' => 'Simulation - Tech & Sécurité',
            'Finance' => 'Simulation - Finance & Risques',
            'Juridique' => 'Simulation - Juridique & Conformité',
            'Opérations' => 'Simulation - Support & Opérations',
            'Partenariats', 'Communication' => 'Simulation - Veille & Partenariats',
            'RH' => 'Simulation - Communauté & Onboarding',
            default => $number % 2 === 0 ? 'Simulation - Organisation Partagée' : 'Simulation - Pilotage Stratégique',
        };

        return $circles->firstWhere('name', $preferredCircle) ?? $circles->first();
    }

    private function decisionTitle(int $number, string $category, string $status): string
    {
        $subjects = [
            'Stratégie' => 'orientation annuelle',
            'RH' => 'politique de travail hybride',
            'Finance' => 'cadre budgétaire trimestriel',
            'Tech' => 'évolution de l’architecture applicative',
            'Juridique' => 'mise à jour de conformité',
            'Produit' => 'priorisation roadmap',
            'Opérations' => 'amélioration du support interne',
            'Partenariats' => 'nouveau partenariat terrain',
            'Communication' => 'campagne de communication publique',
            'Sécurité' => 'renforcement sécurité',
        ];

        return sprintf(
            'Simulation %03d - %s - %s',
            $number,
            ucfirst($subjects[$category] ?? 'proposition transverse'),
            str_replace('_', ' ', $status)
        );
    }

    private function decisionContent(int $number, string $category, string $status): string
    {
        return "# Proposition\n\n"
            . "## Contexte\n"
            . "Cette décision de simulation {$number} couvre un sujet {$category} actuellement en phase `{$status}`.\n\n"
            . "## Proposition\n"
            . "Valider une expérimentation encadrée avec un périmètre clair, un responsable identifié et un suivi dans le cercle concerné.\n\n"
            . "## Impacts attendus\n"
            . "- Améliorer la lisibilité des arbitrages\n"
            . "- Réduire les dépendances implicites\n"
            . "- Donner aux membres un point d'entrée concret pour réagir\n\n"
            . "## Risques\n"
            . "- Charge de coordination supplémentaire\n"
            . "- Besoin d'une communication précise aux parties prenantes\n\n"
            . "## Budget\n"
            . "Budget estimatif : " . (1000 + ($number * 37 % 9000)) . " EUR.\n\n"
            . "## Prochaines étapes\n"
            . "Collecter les retours, traiter les objections éventuelles, puis formaliser la décision finale.";
    }

    private function deadlineForStatus(string $status, bool $emergencyMode): ?\Carbon\CarbonInterface
    {
        if ($status === DecisionStatus::DRAFT->value) {
            return null;
        }

        if ($emergencyMode) {
            return now()->addDay();
        }

        return match ($status) {
            DecisionStatus::CLARIFICATION->value => now()->addDays(mt_rand(2, 8)),
            DecisionStatus::REACTION->value => now()->addDays(mt_rand(2, 10)),
            DecisionStatus::OBJECTION->value => now()->addDays(mt_rand(3, 12)),
            DecisionStatus::REVISION->value => now()->addDays(mt_rand(1, 5)),
            DecisionStatus::SUSPENDED->value => now()->addDays(mt_rand(5, 15)),
            DecisionStatus::LAPSED->value, DecisionStatus::DESERTED->value => now()->subDays(mt_rand(1, 12)),
            DecisionStatus::ADOPTED->value,
            DecisionStatus::ADOPTED_OVERRIDE->value,
            DecisionStatus::ABANDONED->value => now()->subDays(mt_rand(3, 30)),
            default => null,
        };
    }

    private function revisionContent(int $number, string $category): string
    {
        return "# Révision proposée\n\n"
            . "La proposition {$number} est révisée après retours du cercle {$category}.\n\n"
            . "## Ajustements\n"
            . "- Périmètre réduit\n"
            . "- Critères de succès clarifiés\n"
            . "- Délai de mise en œuvre ajusté";
    }

    private function statusBeforeSuspension(int $number): string
    {
        $statuses = [
            DecisionStatus::CLARIFICATION->value,
            DecisionStatus::REACTION->value,
            DecisionStatus::OBJECTION->value,
        ];

        return $statuses[$number % count($statuses)];
    }

    private function seedTaxonomies(): void
    {
        $this->call([
            CategorySeeder::class,
            LabelSeeder::class,
            DecisionModelSeeder::class,
        ]);

        $extraCategories = [
            ['name' => 'Opérations', 'description' => 'Processus internes, support, incidents et amélioration continue', 'color_hex' => '#0f766e', 'icon' => 'settings'],
            ['name' => 'Partenariats', 'description' => 'Relations externes, alliances, écosystème et conventions', 'color_hex' => '#7c3aed', 'icon' => 'handshake'],
            ['name' => 'Communication', 'description' => 'Messages publics, identité, éditorial et événements', 'color_hex' => '#be123c', 'icon' => 'megaphone'],
            ['name' => 'Sécurité', 'description' => 'Protection des données, continuité, incidents et conformité sécurité', 'color_hex' => '#b91c1c', 'icon' => 'lock'],
        ];

        foreach ($extraCategories as $categoryData) {
            $category = Category::withTrashed()->updateOrCreate(
                ['name' => $categoryData['name']],
                array_merge($categoryData, ['is_active' => true])
            );

            if ($category->trashed()) {
                $category->restore();
            }
        }

        $extraLabels = [
            ['name' => 'Confidentiel', 'color_hex' => '#4b5563'],
            ['name' => 'À arbitrer', 'color_hex' => '#f97316'],
            ['name' => 'Risque élevé', 'color_hex' => '#991b1b'],
            ['name' => 'Dépendance externe', 'color_hex' => '#0e7490'],
            ['name' => 'À communiquer', 'color_hex' => '#db2777'],
        ];

        foreach ($extraLabels as $labelData) {
            Label::updateOrCreate(['name' => $labelData['name']], $labelData);
        }

        DecisionModel::withTrashed()->updateOrCreate(
            ['name' => 'Décision Urgente'],
            [
                'description' => 'Circuit raccourci pour les décisions à forte contrainte temporelle.',
                'template_content' => "# Proposition urgente\n\n## Contexte\n...\n\n## Risque si inaction\n...\n\n## Décision attendue\n...",
                'requires_distinct_animator' => true,
                'default_objection_days' => 1,
                'is_active' => true,
            ]
        )?->restore();

        $this->command?->info('Seeded simulation taxonomies.');
    }

    private function seedCircles(Collection $users): Collection
    {
        $definitions = collect([
            [
                'name' => 'Simulation - Organisation Partagée',
                'description' => 'Cercle racine ouvert pour les sujets transverses et les décisions visibles par défaut.',
                'type' => CircleType::OPEN,
                'parent' => null,
                'member_min' => 18,
                'member_max' => 24,
                'animators' => 3,
            ],
            [
                'name' => 'Simulation - Pilotage Stratégique',
                'description' => 'Cercle fermé de coordination, arbitrage et priorisation.',
                'type' => CircleType::CLOSED,
                'parent' => null,
                'member_min' => 10,
                'member_max' => 14,
                'animators' => 2,
            ],
            [
                'name' => 'Simulation - Communauté & Onboarding',
                'description' => 'Cercle observer open pour accueillir, observer et accompagner les nouveaux membres.',
                'type' => CircleType::OBSERVER_OPEN,
                'parent' => null,
                'member_min' => 14,
                'member_max' => 20,
                'animators' => 2,
                'observers' => 4,
            ],
            [
                'name' => 'Simulation - Produit & UX',
                'description' => 'Sous-cercle ouvert pour la roadmap, les parcours et les usages.',
                'type' => CircleType::OPEN,
                'parent' => 'Simulation - Pilotage Stratégique',
                'member_min' => 12,
                'member_max' => 18,
                'animators' => 2,
            ],
            [
                'name' => 'Simulation - Tech & Sécurité',
                'description' => 'Sous-cercle ouvert pour architecture, exploitation et sécurité applicative.',
                'type' => CircleType::OPEN,
                'parent' => 'Simulation - Pilotage Stratégique',
                'member_min' => 10,
                'member_max' => 16,
                'animators' => 2,
            ],
            [
                'name' => 'Simulation - Finance & Risques',
                'description' => 'Sous-cercle fermé pour budgets, achats, trésorerie et risques.',
                'type' => CircleType::CLOSED,
                'parent' => 'Simulation - Pilotage Stratégique',
                'member_min' => 8,
                'member_max' => 12,
                'animators' => 1,
            ],
            [
                'name' => 'Simulation - Juridique & Conformité',
                'description' => 'Sous-cercle fermé de niveau 2 pour contrats, RGPD et conformité.',
                'type' => CircleType::CLOSED,
                'parent' => 'Simulation - Lab Innovation',
                'member_min' => 6,
                'member_max' => 9,
                'animators' => 1,
            ],
            [
                'name' => 'Simulation - Lab Innovation',
                'description' => 'Sous-cercle ouvert pour expérimentations, prototypes et signaux faibles.',
                'type' => CircleType::OPEN,
                'parent' => 'Simulation - Organisation Partagée',
                'member_min' => 12,
                'member_max' => 18,
                'animators' => 2,
            ],
            [
                'name' => 'Simulation - Support & Opérations',
                'description' => 'Sous-cercle ouvert pour support, incidents et amélioration continue.',
                'type' => CircleType::OPEN,
                'parent' => 'Simulation - Organisation Partagée',
                'member_min' => 8,
                'member_max' => 12,
                'animators' => 1,
            ],
            [
                'name' => 'Simulation - Veille & Partenariats',
                'description' => 'Sous-cercle observer open pour partenariats, veille et écosystème.',
                'type' => CircleType::OBSERVER_OPEN,
                'parent' => 'Simulation - Communauté & Onboarding',
                'member_min' => 10,
                'member_max' => 14,
                'animators' => 1,
                'observers' => 3,
            ],
        ]);

        $circlesByName = collect();

        foreach ($definitions as $definition) {
            $circle = Circle::withTrashed()->updateOrCreate(
                ['name' => $definition['name']],
                [
                    'description' => $definition['description'],
                    'type' => $definition['type'],
                    'parent_id' => null,
                    'archived_at' => null,
                ]
            );

            if ($circle->trashed()) {
                $circle->restore();
            }

            $circlesByName->put($definition['name'], $circle);
        }

        foreach ($definitions as $definition) {
            if ($definition['parent'] === null) {
                continue;
            }

            $circle = $circlesByName->get($definition['name']);
            $parent = $circlesByName->get($definition['parent']);

            if ($circle && $parent) {
                $circle->update(['parent_id' => $parent->id]);
            }
        }

        foreach ($definitions as $definition) {
            $circle = $circlesByName->get($definition['name']);

            if (!$circle) {
                continue;
            }

            $parent = $definition['parent'] ? $circlesByName->get($definition['parent']) : null;
            $this->seedCircleMemberships($circle->fresh(), $users, $definition, $parent);
        }

        $this->command?->info("Seeded {$circlesByName->count()} simulation circles.");

        return $circlesByName->values();
    }

    private function seedCircleMemberships(
        Circle $circle,
        Collection $users,
        array $definition,
        ?Circle $parent = null
    ): void {
        $eligibleUsers = $users->where('is_active', true)->values();
        $memberCount = mt_rand($definition['member_min'], $definition['member_max']);

        $members = collect();

        if ($parent) {
            $parentMembers = $parent->members()->with('user')->get()
                ->pluck('user')
                ->filter()
                ->where('is_active', true)
                ->values();

            $inheritedCount = min(
                $parentMembers->count(),
                max(1, (int) floor($memberCount * $this->randomFloat(0.4, 0.7)))
            );

            $members = $members->merge($parentMembers->shuffle()->take($inheritedCount));
        }

        $remainingCount = max(0, $memberCount - $members->count());
        $additionalUsers = $eligibleUsers
            ->reject(fn (User $user): bool => $members->contains('id', $user->id))
            ->shuffle()
            ->take($remainingCount);

        $members = $members->merge($additionalUsers)->unique('id')->values();

        $globalAnimators = $eligibleUsers
            ->where('is_global_animator', true)
            ->reject(fn (User $user): bool => $members->contains('id', $user->id))
            ->take(max(0, ($definition['animators'] ?? 1) - $members->count()));

        $members = $members->merge($globalAnimators)->unique('id')->values();

        $animatorCount = min($definition['animators'] ?? 1, $members->count());
        $observerCount = min($definition['observers'] ?? 0, max(0, $members->count() - $animatorCount));

        $animatorIds = $members->take($animatorCount)->pluck('id')->all();
        $observerIds = $circle->type === CircleType::OBSERVER_OPEN
            ? $members->slice($animatorCount)->take($observerCount)->pluck('id')->all()
            : [];

        foreach ($members as $member) {
            $role = match (true) {
                in_array($member->id, $animatorIds, true) => CircleMemberRole::ANIMATOR,
                in_array($member->id, $observerIds, true) => CircleMemberRole::OBSERVER,
                default => CircleMemberRole::MEMBER,
            };

            $circle->members()->updateOrCreate(
                ['user_id' => $member->id],
                ['role' => $role]
            );
        }
    }

    private function seedUsers(): Collection
    {
        $names = [
            'Alice Durand',
            'Hugo Bernard',
            'Claire Lefèvre',
            'Samir Benali',
            'Nadia Morel',
            'Bob Martin',
            'Emma Petit',
            'David Nguyen',
            'Inès Caron',
            'Thomas Leroy',
            'Gaëlle Rousseau',
            'Franck Moreau',
            'Lina Garnier',
            'Mathieu Simon',
            'Camille Roux',
            'Yanis Perrin',
            'Sofia Lambert',
            'Julien Mercier',
            'Manon Faure',
            'Karim Vidal',
            'Chloé Renard',
            'Antoine Marchand',
            'Sarah Colin',
            'Nicolas Aubert',
            'Leïla Fontaine',
            'Maxime Chevalier',
            'Océane Giraud',
            'Romain Blanc',
            'Myriam Leclerc',
            'Bastien Roche',
            'Élodie Henry',
            'Mehdi Masson',
            'Pauline Picard',
            'Adrien Denis',
            'Maya Schmitt',
            'Quentin Roy',
            'Lucie Brun',
            'Ibrahim Garnier',
            'Anaïs Lambert',
            'Victor Moulin',
            'Mélissa Carpentier',
            'Jules Robin',
            'Aïcha Bourgeois',
            'Noémie Adam',
            'Émile Garcia',
            'Morgane Paris',
            'Olivier Renaud',
            'Salomé Perrier',
            'Cédric Barbier',
            'Fatou Diop',
        ];

        $users = collect();

        foreach (array_slice($names, 0, self::CONFIG['user_count']) as $index => $name) {
            $number = $index + 1;
            $role = match (true) {
                $number === 1 => UserRole::SUPERADMIN,
                $number <= 5 => UserRole::ADMIN,
                default => UserRole::USER,
            };

            $user = User::updateOrCreate(
                ['email' => sprintf('simulation%02d@dazo.test', $number)],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => $role,
                    'is_global_animator' => $number <= 8,
                    'is_active' => $number <= 46,
                    'custom_views' => $this->customViewsForUser($number),
                    'dashboard_widgets' => $this->dashboardWidgetsForUser($number),
                ]
            );

            $this->seedNotificationPreferences($user, $number);
            $users->push($user);
        }

        $this->command?->info("Seeded {$users->count()} simulation users.");

        return $users;
    }

    private function customViewsForUser(int $number): ?array
    {
        return match ($number % 6) {
            0 => [
                ['name' => 'Mes urgences', 'filters' => ['priority' => 2, 'needs_my_action' => true]],
                ['name' => 'Décisions privées', 'filters' => ['visibility' => 'private']],
            ],
            1 => [
                ['name' => 'À clarifier', 'filters' => ['status' => ['clarification']]],
            ],
            2 => [
                ['name' => 'Objections ouvertes', 'filters' => ['status' => ['objection'], 'has_objections' => true]],
            ],
            3 => [
                ['name' => 'Suivi stratégie', 'filters' => ['category' => 'Stratégie']],
            ],
            default => null,
        };
    }

    private function dashboardWidgetsForUser(int $number): array
    {
        $baseWidgets = ['my_actions', 'recent_decisions', 'favorite_decisions'];

        if ($number <= 5) {
            return array_merge($baseWidgets, ['admin_health', 'activity_feed']);
        }

        if ($number % 4 === 0) {
            return ['my_actions', 'circle_activity', 'deadlines'];
        }

        return $baseWidgets;
    }

    private function seedNotificationPreferences(User $user, int $number): void
    {
        $profile = match ($number % 5) {
            0 => 'high_signal',
            1 => 'web_only',
            2 => 'email_only',
            3 => 'quiet',
            default => 'deadlines_only',
        };

        foreach (NotificationCategory::cases() as $category) {
            [$emailEnabled, $webEnabled] = $this->notificationSettingsForProfile($profile, $category);

            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'category' => $category->value,
                ],
                [
                    'email_enabled' => $emailEnabled,
                    'web_enabled' => $webEnabled,
                ]
            );
        }
    }

    private function notificationSettingsForProfile(string $profile, NotificationCategory $category): array
    {
        return match ($profile) {
            'high_signal' => [true, true],
            'web_only' => [false, true],
            'email_only' => [true, false],
            'quiet' => [
                in_array($category, [NotificationCategory::DEADLINE, NotificationCategory::SYSTEM], true),
                $category === NotificationCategory::SYSTEM,
            ],
            'deadlines_only' => [
                $category === NotificationCategory::DEADLINE,
                in_array($category, [NotificationCategory::DEADLINE, NotificationCategory::PHASE_CHANGE], true),
            ],
            default => [true, true],
        };
    }

    private function seedRandomGenerators(): void
    {
        mt_srand(self::RANDOM_SEED);
        \fake()->seed(self::RANDOM_SEED);
    }

    /**
     * Pick a value from an associative array of value => weight.
     *
     * @param array<string|int, int|float> $weights
     */
    private function weightedPick(array $weights): string|int
    {
        $totalWeight = array_sum($weights);

        if ($totalWeight <= 0) {
            throw new \InvalidArgumentException('Weighted pick requires a positive total weight.');
        }

        $random = $this->randomFloat(0, $totalWeight);
        $cumulative = 0.0;

        foreach ($weights as $value => $weight) {
            if ($weight <= 0) {
                continue;
            }

            $cumulative += $weight;

            if ($random <= $cumulative) {
                return $value;
            }
        }

        return array_key_last($weights);
    }

    private function chance(float $ratio): bool
    {
        if ($ratio <= 0) {
            return false;
        }

        if ($ratio >= 1) {
            return true;
        }

        return $this->randomFloat() < $ratio;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param Collection<TKey, TValue> $items
     * @return Collection<TKey, TValue>
     */
    private function pickMany(Collection $items, int $min, int $max): Collection
    {
        if ($items->isEmpty()) {
            return collect();
        }

        $min = max(0, min($min, $items->count()));
        $max = max($min, min($max, $items->count()));
        $count = mt_rand($min, $max);

        return $items->shuffle()->take($count);
    }

    private function randomFloat(float $min = 0.0, float $max = 1.0): float
    {
        return $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
    }

    private function printAndValidateSummary(): void
    {
        $simulationDecisions = Decision::where('title', 'like', 'Simulation %');
        $decisionCount = (clone $simulationDecisions)->count();
        $publicDecisionCount = (clone $simulationDecisions)->where('visibility', 'public')->count();
        $privateDecisionCount = (clone $simulationDecisions)->where('visibility', 'private')->count();
        $publicDecisionRatio = $decisionCount > 0
            ? round(($publicDecisionCount / $decisionCount) * 100, 1)
            : 0.0;
        $simulationDecisionIds = (clone $simulationDecisions)->pluck('id');
        $simulationVersionIds = DecisionVersion::whereIn('decision_id', $simulationDecisionIds)->pluck('id');
        $simulationCircleIds = Circle::where('name', 'like', 'Simulation - %')->pluck('id');

        $this->command?->table(
            ['Metric', 'Value'],
            [
                ['Simulation users', User::where('email', 'like', 'simulation%@dazo.test')->count()],
                ['Simulation circles', $simulationCircleIds->count()],
                ['Decisions', $decisionCount],
                ['Public decisions', "{$publicDecisionCount} ({$publicDecisionRatio}%)"],
                ['Private decisions', $privateDecisionCount],
                ['Decision versions', $simulationVersionIds->count()],
                ['Participants', DecisionParticipant::whereIn('decision_id', $simulationDecisionIds)->count()],
                ['Feedbacks', Feedback::whereIn('decision_version_id', $simulationVersionIds)->count()],
                ['Consents', Consent::whereIn('decision_version_id', $simulationVersionIds)->count()],
                ['Relations', DecisionRelation::whereIn('source_decision_id', $simulationDecisionIds)->count()],
                ['User settings', DecisionUserSetting::whereIn('decision_id', $simulationDecisionIds)->count()],
                ['Attachments', Attachment::whereIn('decision_version_id', $simulationVersionIds)->count()],
                ['Random seed', self::RANDOM_SEED],
                ['Target users', self::CONFIG['user_count']],
                ['Target circles', self::CONFIG['circle_count']],
                ['Target decisions', self::CONFIG['decision_count']],
            ]
        );

        $warnings = $this->validationWarnings($simulationDecisionIds, $simulationVersionIds, $simulationCircleIds, $decisionCount, $publicDecisionRatio);

        if ($warnings === []) {
            $this->command?->info('Full simulation validation passed.');
            return;
        }

        foreach ($warnings as $warning) {
            $this->command?->warn($warning);
        }
    }

    private function validationWarnings(
        Collection $simulationDecisionIds,
        Collection $simulationVersionIds,
        Collection $simulationCircleIds,
        int $decisionCount,
        float $publicDecisionRatio
    ): array {
        $warnings = [];
        $simulationUserCount = User::where('email', 'like', 'simulation%@dazo.test')->count();
        $simulationCircleCount = $simulationCircleIds->count();
        $currentVersionMissingCount = Decision::whereIn('id', $simulationDecisionIds)
            ->whereDoesntHave('currentVersion')
            ->count();
        $authorMissingCount = Decision::whereIn('id', $simulationDecisionIds)
            ->whereDoesntHave('participants', fn ($query) => $query->where('role', DecisionParticipantRole::AUTHOR->value))
            ->count();
        $animatorMissingCount = Decision::whereIn('id', $simulationDecisionIds)
            ->where('status', '!=', DecisionStatus::DRAFT->value)
            ->whereDoesntHave('participants', fn ($query) => $query->where('role', DecisionParticipantRole::ANIMATOR->value))
            ->count();
        $selfRelationCount = DecisionRelation::whereColumn('source_decision_id', 'target_decision_id')->count();
        $selfParentCircleCount = Circle::whereIn('id', $simulationCircleIds)
            ->whereColumn('id', 'parent_id')
            ->count();
        $feedbackWithoutAuthorCount = Feedback::whereIn('decision_version_id', $simulationVersionIds)
            ->whereDoesntHave('author')
            ->count();
        $invalidConsentPhaseCount = Consent::whereIn('decision_version_id', $simulationVersionIds)
            ->whereNotIn('phase', [
                DecisionStatus::CLARIFICATION->value,
                DecisionStatus::REACTION->value,
                DecisionStatus::OBJECTION->value,
            ])
            ->count();

        if ($simulationUserCount !== self::CONFIG['user_count']) {
            $warnings[] = "Expected " . self::CONFIG['user_count'] . " simulation users, got {$simulationUserCount}.";
        }

        if ($simulationCircleCount !== self::CONFIG['circle_count']) {
            $warnings[] = "Expected " . self::CONFIG['circle_count'] . " simulation circles, got {$simulationCircleCount}.";
        }

        if ($decisionCount < self::CONFIG['decision_count']) {
            $warnings[] = "Expected at least " . self::CONFIG['decision_count'] . " simulation decisions, got {$decisionCount}.";
        }

        if ($decisionCount > 0 && abs($publicDecisionRatio - 90.0) > 2.0) {
            $warnings[] = "Expected public decision ratio near 90%, got {$publicDecisionRatio}%.";
        }

        if ($currentVersionMissingCount > 0) {
            $warnings[] = "{$currentVersionMissingCount} simulation decisions have no current version.";
        }

        if ($authorMissingCount > 0) {
            $warnings[] = "{$authorMissingCount} simulation decisions have no author participant.";
        }

        if ($animatorMissingCount > 0) {
            $warnings[] = "{$animatorMissingCount} non-draft simulation decisions have no animator participant.";
        }

        if ($selfRelationCount > 0) {
            $warnings[] = "{$selfRelationCount} decision relations point to the same decision.";
        }

        if ($selfParentCircleCount > 0) {
            $warnings[] = "{$selfParentCircleCount} simulation circles use themselves as parent.";
        }

        if ($feedbackWithoutAuthorCount > 0) {
            $warnings[] = "{$feedbackWithoutAuthorCount} simulation feedbacks have no author.";
        }

        if ($invalidConsentPhaseCount > 0) {
            $warnings[] = "{$invalidConsentPhaseCount} simulation consents have an invalid phase.";
        }

        $attachmentCount = Attachment::whereIn('decision_version_id', $simulationVersionIds)->count();
        $publicAttachmentCount = Attachment::whereIn('decision_version_id', $simulationVersionIds)
            ->where('is_private', false)
            ->count();
        $publicAttachmentRatio = $attachmentCount > 0
            ? round(($publicAttachmentCount / $attachmentCount) * 100, 1)
            : 0.0;
        $invalidAttachmentCount = Attachment::whereIn('decision_version_id', $simulationVersionIds)
            ->where(function ($query) {
                $query->whereNull('decision_version_id')->orWhereNull('uploader_id');
            })
            ->count();

        if ($attachmentCount > 0 && abs($publicAttachmentRatio - 90.0) > 2.0) {
            $warnings[] = "Expected public attachment ratio near 90%, got {$publicAttachmentRatio}%.";
        }

        if ($attachmentCount === 0) {
            $warnings[] = 'No simulation attachments were generated. Run ./dazo-generate-fake-attachments.sh --clean before the full seeder.';
        }

        if ($invalidAttachmentCount > 0) {
            $warnings[] = "{$invalidAttachmentCount} simulation attachments have no uploader or version.";
        }

        return $warnings;
    }
}
