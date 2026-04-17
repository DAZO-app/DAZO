<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionAnimatorLog extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false; // We use assigned_at and removed_at

    protected $fillable = ['decision_id', 'animator_id', 'assigned_by', 'assigned_at', 'removed_at'];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function animator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'animator_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
