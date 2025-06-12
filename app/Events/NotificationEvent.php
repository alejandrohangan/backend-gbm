<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    public $ticket;
    public $user;

    public function __construct($ticket, $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.tickets');
    }

    public function broadcastAs()
    {
        return 'ticket.created';
    }

    public function broadcastWith()
    {
        return [
            'message' => "{$this->user->name} creó un nuevo ticket, id {$this->ticket->id}",
            'user_name' => $this->user->name,
            'ticket_id' => $this->ticket->id,
            'type' => 'ticket_created'
        ];
    }
}
