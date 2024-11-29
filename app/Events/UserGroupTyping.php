<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserGroupTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender_id;
    public $id_conversation_ws;
    public $isTyping;
    /**
     * Create a new event instance.
     *
     * 
     */
    public function __construct($sender_id , $id_conversation_ws, $isTyping)
    {
        $this->sender_id = $sender_id;
        $this->id_conversation_ws = $id_conversation_ws;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [ new PresenceChannel('user-group-typing.' . $this->id_conversation_ws) ];
    }
    /**
     * Broadcast data with action.
     */
    public function broadcastWith()
    {
        return [
            'isTyping' => $this->isTyping,
            'sender_id' => $this->sender_id,
        ];
    }
}
