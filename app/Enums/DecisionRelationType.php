<?php

namespace App\Enums;

enum DecisionRelationType: string
{
    case DERIVES_FROM = 'derives_from';
    case BLOCKS = 'blocks';
}
