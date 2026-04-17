<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DecisionModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'description', 'template_content', 'requires_distinct_animator', 'default_objection_days', 'is_active'];

    protected function casts(): array
    {
        return [
            'requires_distinct_animator' => 'boolean',
            'is_active' => 'boolean',
            'default_objection_days' => 'integer',
        ];
    }

    public function helpTexts(): HasMany
    {
        return $this->hasMany(HelpText::class, 'model_id');
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class, 'model_id');
    }
}
