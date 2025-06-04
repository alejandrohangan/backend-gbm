<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Models\Conversation;

class CreateTicketConversation
{
    public function handle(TicketAssigned $event): void
    {
        Conversation::firstOrCreate([
            'user_id1' => $event->ticket->requester_id,
            'user_id2' => $event->agentId,
        ]);
    }
}