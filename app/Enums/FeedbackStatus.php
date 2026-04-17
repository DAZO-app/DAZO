<?php

namespace App\Enums;

enum FeedbackStatus: string
{
    case SUBMITTED = 'submitted';
    case CLARIFICATION_REQUESTED = 'clarification_requested';
    case IN_TREATMENT = 'in_treatment';
    case TREATED = 'treated';
    case REJECTED = 'rejected';
    case ACKNOWLEDGED = 'acknowledged';
    case WITHDRAWN = 'withdrawn';
    case IGNORED = 'ignored';
}
