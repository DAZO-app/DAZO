<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'decision_id', 'author_id', 'previous_version_id',
        'version_number', 'is_current', 'content', 'change_reason'
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'version_number' => 'integer',
        ];
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function previousVersion(): BelongsTo
    {
        return $this->belongsTo(DecisionVersion::class, 'previous_version_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
