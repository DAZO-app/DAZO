<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case LIFECYCLE = 'lifecycle';
    case FEEDBACK = 'feedback';
    case VOTE = 'vote';
    case ADMIN = 'admin';
    case SYSTEM = 'system';
}
