<?php

namespace App\Models;

use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'feedbacks';

    protected $fillable = ['decision_version_id', 'author_id', 'type', 'status', 'content'];

    protected function casts(): array
    {
        return [
            'type' => FeedbackType::class,
            'status' => FeedbackStatus::class,
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(DecisionVersion::class, 'decision_version_id');
    }

    public function decisionVersion(): BelongsTo
    {
        return $this->version();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function joins(): HasMany
    {
        return $this->hasMany(FeedbackJoin::class, 'feedback_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FeedbackMessage::class, 'feedback_id');
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FeedbackMessage::class, 'feedback_id')->where('id', function ($query) {
            $query->select('id')
                ->from('feedback_messages')
                ->whereColumn('feedback_id', 'feedbacks.id')
                ->latest()
                ->limit(1);
        });
    }
}
