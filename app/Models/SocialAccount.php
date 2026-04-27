<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'provider_data',
    ];

    protected $casts = [
        'provider_data' => 'array',
    ];

    protected $hidden = [
        'provider_token',
        'provider_refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
