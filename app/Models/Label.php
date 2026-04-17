<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'color_hex'];

    public function decisions(): BelongsToMany
    {
        return $this->belongsToMany(Decision::class, 'decision_labels');
    }
}
