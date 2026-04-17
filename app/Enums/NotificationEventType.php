<?php

namespace App\Enums;

enum NotificationEventType: string
{
    case NEW_DECISION = 'new_decision';
    case NEW_VERSION = 'new_version';
    case DECISION_ADOPTED = 'decision_adopted';
    case DECISION_ADOPTED_OVERRIDE = 'decision_adopted_override';
    case DECISION_ABANDONED = 'decision_abandoned';
    case DECISION_LAPSED = 'decision_lapsed';
    case DECISION_DESERTED = 'decision_deserted';
    case OBJECTION_SUBMITTED = 'objection_submitted';
    case SUGGESTION_SUBMITTED = 'suggestion_submitted';
    case FEEDBACK_REPLIED = 'feedback_replied';
    case FEEDBACK_JOINED = 'feedback_joined';
    case PARTICIPANT_REMINDER = 'participation_reminder';
    case ANIMATOR_INVOKED = 'animator_invoked';
    case EMAIL_VALIDATION = 'email_validation';
    case INVITATION = 'invitation';
}
