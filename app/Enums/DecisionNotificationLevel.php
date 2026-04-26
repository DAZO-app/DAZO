<?php

namespace App\Enums;

enum DecisionNotificationLevel: string
{
    case ALL = 'all';               // Chaque mise à jour
    case RELEVANT = 'relevant';     // Mise à jour me concernant
    case PHASE_CHANGE = 'phase_change'; // Changement de phase
    case NONE = 'none';             // Aucune notification
}
