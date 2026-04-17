<?php

namespace App\Enums;

enum DecisionStatus: string
{
    case DRAFT = 'draft';
    case CLARIFICATION = 'clarification';
    case REACTION = 'reaction';
    case OBJECTION = 'objection';
    case REVISION = 'revision';
    case ADOPTED = 'adopted';
    case ADOPTED_OVERRIDE = 'adopted_override';
    case ABANDONED = 'abandoned';
    case LAPSED = 'lapsed';
    case DESERTED = 'deserted';
}
