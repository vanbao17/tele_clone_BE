<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationWs extends Model
{
    use HasFactory;

    protected $table = 'conversation_ws';

    protected $fillable = [
        'id_owner',
        'name',
        'thumb',
        'is_deleted',
    ];

     // Quan hệ với bảng users để lấy email của người sở hữu
     public function owner()
     {
         return $this->belongsTo(User::class, 'id_owner');
     }
}
