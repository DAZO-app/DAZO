<?php

namespace App\Enums;

enum CircleMemberRole: string
{
    case MEMBER = 'member';
    case ANIMATOR = 'animator';
    case OBSERVER = 'observer';
}
