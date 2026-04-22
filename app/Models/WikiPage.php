<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WikiPage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'category',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
