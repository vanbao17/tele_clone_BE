<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageWs extends Model
{
    use HasFactory;

    protected $table = 'message_ws';

    protected $fillable = [
        'id_conversation_ws', 'sender_id', 'content_type', 'content'
    ];

    public function conversationWs()
    {
        return $this->belongsTo(ConversationWs::class, 'id_conversation_ws');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
