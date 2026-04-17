<?php

namespace App\Enums;

enum DecisionParticipantRole: string
{
    case AUTHOR = 'author';
    case ANIMATOR = 'animator';
    case PARTICIPANT = 'participant';
}
