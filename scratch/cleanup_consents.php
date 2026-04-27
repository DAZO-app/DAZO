<?php
use App\Models\Consent;
use App\Models\Feedback;
use App\Enums\DecisionStatus;

$consents = Consent::all();
foreach ($consents as $c) {
    $phase = $c->phase;
    if (!$phase) continue;
    
    $phaseVal = $phase instanceof DecisionStatus ? $phase->value : $phase;
    $types = match($phaseVal) {
        'clarification' => ['clarification'],
        'reaction' => ['reaction'],
        'objection' => ['objection', 'suggestion'],
        default => []
    };
    
    if (empty($types)) continue;
    
    $hasFeedback = Feedback::where('decision_version_id', $c->decision_version_id)
        ->where('author_id', $c->user_id)
        ->whereIn('type', $types)
        ->exists();
        
    if ($hasFeedback) {
        echo "Deleting duplicate consent for user {$c->user_id} in phase {$phaseVal}\n";
        $c->delete();
    }
}
