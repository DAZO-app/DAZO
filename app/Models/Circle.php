<?php

namespace App\Models;

use App\Enums\CircleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Circle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'description', 'type', 'parent_id'];

    protected function casts(): array
    {
        return [
            'type' => CircleType::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Circle::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Circle::class, 'parent_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
