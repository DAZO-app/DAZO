<?php

namespace App\Models;

use App\Enums\DecisionParticipantRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionParticipant extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = ['decision_id', 'user_id', 'role', 'added_at'];

    protected function casts(): array
    {
        return [
            'role' => DecisionParticipantRole::class,
            'added_at' => 'datetime',
        ];
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
