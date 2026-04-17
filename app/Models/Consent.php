<?php

namespace App\Models;

use App\Enums\ConsentSignal;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['decision_version_id', 'user_id', 'signal'];

    protected function casts(): array
    {
        return [
            'signal' => ConsentSignal::class,
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(DecisionVersion::class, 'decision_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
