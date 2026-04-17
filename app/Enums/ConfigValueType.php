<?php

namespace App\Enums;

enum ConfigValueType: string
{
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case STRING = 'string';
    case JSON = 'json';
}
