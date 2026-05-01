<?php

namespace App\Models;

use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\DecisionVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Decision extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'circle_id', 'status', 'title', 'visibility',
        'priority', 'emergency_mode', 'objection_round_deadline', 'model_id',
        'revision_content', 'revision_attachment_ids',
        'current_deadline', 'reminder_sent', 'status_before_suspension'
    ];

    protected $appends = ['author_id', 'animator_id'];

    protected function casts(): array
    {
        return [
            'status' => DecisionStatus::class,
            'visibility' => DecisionVisibility::class,
            'emergency_mode' => 'boolean',
            'priority' => 'integer',
            'objection_round_deadline' => 'datetime',
            'current_deadline' => 'datetime',
            'reminder_sent' => 'boolean',
            'revision_attachment_ids' => 'array',
        ];
    }

    public function getAuthorIdAttribute(): ?string
    {
        // Si la relation participants est déjà chargée, utiliser la collection en mémoire
        if ($this->relationLoaded('participants')) {
            return $this->participants
                ->firstWhere('role', \App\Enums\DecisionParticipantRole::AUTHOR->value)
                ?->user_id;
        }
        return $this->author()->value('user_id');
    }

    public function getAnimatorIdAttribute(): ?string
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants
                ->firstWhere('role', \App\Enums\DecisionParticipantRole::ANIMATOR->value)
                ?->user_id;
        }
        return $this->currentAnimator()->value('user_id');
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'decision_categories');
    }

    public function decisionModel(): BelongsTo
    {
        return $this->belongsTo(DecisionModel::class, 'model_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DecisionVersion::class);
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(DecisionVersion::class)->where('is_current', true);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(DecisionParticipant::class);
    }

    public function author(): HasOne
    {
        return $this->hasOne(DecisionParticipant::class)->where('role', DecisionParticipantRole::AUTHOR->value);
    }

    public function currentAnimator(): HasOne
    {
        return $this->hasOne(DecisionParticipant::class)->where('role', DecisionParticipantRole::ANIMATOR->value);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'decision_labels');
    }

    public function animatorLogs(): HasMany
    {
        return $this->hasMany(DecisionAnimatorLog::class);
    }

    public function userSettings(): HasMany
    {
        return $this->hasMany(DecisionUserSetting::class);
    }
}
