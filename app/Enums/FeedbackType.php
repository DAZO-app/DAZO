<?php

namespace App\Enums;

enum FeedbackType: string
{
    case CLARIFICATION = 'clarification';
    case REACTION = 'reaction';
    case OBJECTION = 'objection';
    case SUGGESTION = 'suggestion';
}
