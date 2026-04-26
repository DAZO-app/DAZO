<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case NEW_DECISION = 'new_decision';
    case PHASE_CHANGE = 'phase_change';
    case REVISION = 'revision';
    case DEADLINE = 'deadline';
    case FEEDBACK = 'feedback';
    case MENTION = 'mention';
    case SYSTEM = 'system';
}
