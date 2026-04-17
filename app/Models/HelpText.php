<?php

namespace App\Models;

use App\Enums\HelpTextLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpText extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['key', 'level', 'model_id', 'content'];

    protected function casts(): array
    {
        return [
            'level' => HelpTextLevel::class,
        ];
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(DecisionModel::class, 'model_id');
    }
}
