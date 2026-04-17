<?php

namespace App\Enums;

enum InvitationRole: string
{
    case MEMBER = 'member';
    case ANIMATOR = 'animator';
    case OBSERVER = 'observer';
    case PARTICIPANT = 'participant';
}
