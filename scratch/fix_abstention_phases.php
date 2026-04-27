<?php
use App\Models\Consent;
use App\Enums\DecisionStatus;

$consents = Consent::where('signal', 'abstention')->whereNull('phase')->get();
foreach ($consents as $c) {
    $decision = $c->version->decision;
    $status = $decision->status->value;
    
    $inferredPhase = match($status) {
        'reaction' => 'clarification',
        'objection' => 'reaction',
        'adopted', 'adopted_override' => 'objection',
        'revision' => 'objection', // After objection phase
        default => 'clarification'
    };
    
    echo "Updating abstention for user {$c->user_id} in decision {$decision->id}: phase = {$inferredPhase} (current status: {$status})\n";
    $c->phase = $inferredPhase;
    $c->save();
}
