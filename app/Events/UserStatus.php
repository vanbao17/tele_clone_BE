<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $sender_id;
    public $id_conversation;
    public $isTyping;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($sender_id , $id_conversation, $isTyping)
    {
        $this->sender_id = $sender_id;
        $this->id_conversation = $id_conversation;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn() : array
    {
        return [ new PrivateChannel('user-status.' . $this->id_conversation) ];
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
