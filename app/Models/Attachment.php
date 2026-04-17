<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['decision_version_id', 'uploader_id', 'filename', 's3_path', 'mime_type', 'size_bytes'];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(DecisionVersion::class, 'decision_version_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
