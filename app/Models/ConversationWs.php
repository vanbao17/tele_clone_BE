<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationWs extends Model
{
    use HasFactory;

    protected $table = 'conversation_ws';

    protected $fillable = [
        'id_owner', 'name', 'thumb'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'id_owner');
    }

    public function members()
    {
        return $this->hasMany(ConversationMember::class, 'id_conversation');
    }
}
