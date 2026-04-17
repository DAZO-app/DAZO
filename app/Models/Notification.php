<?php

namespace App\Models;

use App\Enums\NotificationCategory;
use App\Enums\NotificationEventType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'category', 'event_type', 'payload', 'read_at'];

    protected function casts(): array
    {
        return [
            'category' => NotificationCategory::class,
            'event_type' => NotificationEventType::class,
            'payload' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
