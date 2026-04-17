<?php

namespace App\Enums;

enum CircleType: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case OBSERVER_OPEN = 'observer_open';
}
