<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLayout extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'view_name',
        'layout_data',
    ];

    protected $casts = [
        'layout_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
