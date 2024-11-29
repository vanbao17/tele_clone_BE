<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $message;
    public $action; 

    /**
     * Create a new event instance.
     */
    public function __construct($message, $action = 'created')
    {
        $this->message = $message;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->receiver_id . '.' . $this->message->sender_id),
        ];
    }

    /**
     * Broadcast data with action.
     */
    public function broadcastWith()
    {
        return [
            'action' => $this->action, 
            'message' => $this->message,
        ];
    }
}
