<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WikiPage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'wiki_category_id',
        'slug',
        'title',
        'content',
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(WikiCategory::class, 'wiki_category_id');
    }
}
