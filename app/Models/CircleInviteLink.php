<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircleInviteLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'circle_id', 'token', 'created_by', 'role',
        'expires_at', 'max_uses', 'use_count',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'max_uses' => 'integer',
            'use_count' => 'integer',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        if ($this->isExpired()) return false;
        if ($this->max_uses !== null && $this->use_count >= $this->max_uses) return false;
        return true;
    }

    public function generateUrl(): string
    {
        return url("/invite/{$this->token}");
    }
}
