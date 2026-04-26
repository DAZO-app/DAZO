<?php

namespace App\Models;

use App\Enums\DecisionNotificationLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionUserSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'decision_id',
        'is_favorite',
        'notification_level'
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'notification_level' => DecisionNotificationLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }
}
