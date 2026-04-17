<?php

namespace App\Models;

use App\Enums\InvitationRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'circle_id', 'inviter_id', 'email', 'role', 
        'token', 'expires_at', 'used_by'
    ];

    protected function casts(): array
    {
        return [
            'role' => InvitationRole::class,
            'expires_at' => 'datetime',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
