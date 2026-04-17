<?php

namespace App\Models;

use App\Enums\DecisionRelationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionRelation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['source_decision_id', 'target_decision_id', 'relation_type'];

    protected function casts(): array
    {
        return [
            'relation_type' => DecisionRelationType::class,
        ];
    }

    public function sourceDecision(): BelongsTo
    {
        return $this->belongsTo(Decision::class, 'source_decision_id');
    }

    public function targetDecision(): BelongsTo
    {
        return $this->belongsTo(Decision::class, 'target_decision_id');
    }
}
