<?php

namespace App\Models;

use App\Enums\ConfigValueType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstanceConfig extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'instance_config';

    protected $fillable = ['key', 'type', 'value'];

    protected function casts(): array
    {
        return [
            'type' => ConfigValueType::class,
        ];
    }
}
