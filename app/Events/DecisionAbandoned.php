<?php

namespace App\Events;

use App\Models\Decision;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DecisionAbandoned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Decision $decision) {}
}
