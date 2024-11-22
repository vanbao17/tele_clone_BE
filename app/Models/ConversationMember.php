<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationMember extends Model
{
    use HasFactory;

    protected $table = 'conversation_member';

    protected $fillable = [
        'id_conversation', 'id_user', 'role'
    ];

    public function conversation()
    {
        return $this->belongsTo(ConversationWs::class, 'id_conversation');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
