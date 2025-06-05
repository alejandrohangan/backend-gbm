<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Models\Conversation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateConversationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketAssigned $event): void
    {
        $existingConversation = Conversation::where('ticket_id', $event->ticket->id)->first();

        // Solo crear si no existe
        if (!$existingConversation) {
            Log::info('Ticket ID en CreateConversationListener:', ['ticket_id' => $event->ticket->id]);
            Conversation::create([
                'user_id1' => $event->ticket->requester_id,
                'user_id2' => $event->agentId,
                'ticket_id' => $event->ticket->id,
            ]);
        }
    }
}