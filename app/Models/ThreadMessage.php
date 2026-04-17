<?php

namespace App\Models;

use App\Enums\ThreadTour;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['decision_id', 'author_id', 'tour', 'content', 'is_moderator_note'];

    protected function casts(): array
    {
        return [
            'tour' => ThreadTour::class,
            'is_moderator_note' => 'boolean',
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
}
