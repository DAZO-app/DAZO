<?php

namespace App\Enums;

enum ConsentSignal: string
{
    case NO_QUESTIONS = 'no_questions';
    case NO_REACTION = 'no_reaction';
    case NO_OBJECTION = 'no_objection';
    case ABSTENTION = 'abstention';
}
