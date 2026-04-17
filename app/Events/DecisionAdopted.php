<?php

namespace App\Events;

use App\Models\Decision;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DecisionAdopted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Decision $decision) {}
}
