<?php

namespace App\Models;

use App\Enums\CircleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Circle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'description', 'type', 'parent_id', 'archived_at'];

    protected function casts(): array
    {
        return [
            'type' => CircleType::class,
            'archived_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Circle::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Circle::class, 'parent_id')->orderByDesc('created_at');
    }

    public function activeChildren(): HasMany
    {
        return $this->hasMany(Circle::class, 'parent_id')
            ->whereNull('archived_at')
            ->orderByDesc('created_at');
    }

    public function archivedChildren(): HasMany
    {
        return $this->hasMany(Circle::class, 'parent_id')
            ->whereNotNull('archived_at')
            ->orderByDesc('created_at');
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

    public function inviteLink(): HasOne
    {
        return $this->hasOne(CircleInviteLink::class)->latest();
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // Helpers

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function isSubCircle(): bool
    {
        return $this->parent_id !== null;
    }
}
