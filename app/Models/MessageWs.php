<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageWs extends Model
{
    use HasFactory;

    protected $table = 'message_ws';  // Tên bảng trong CSDL

    protected $fillable = [
        'id_conversation_ws',
        'sender_id',
        'content_type',
        'content',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    // Mối quan hệ với bảng ConversationWs
    public function conversationWs()
    {
        return $this->belongsTo(ConversationWs::class, 'id_conversation_ws');
    }

    // Mối quan hệ với bảng User
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
