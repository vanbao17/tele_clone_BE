<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversation';

    protected $fillable = [
        'id_account1', 'id_account2'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_member', 'id_conversation', 'id_user')
                    ->where('is_deleted', 0);
    }
}
