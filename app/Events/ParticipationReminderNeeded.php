<?php

namespace App\Events;

use App\Models\Decision;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipationReminderNeeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Decision $decision) {}
}
