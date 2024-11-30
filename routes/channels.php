<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;


/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::channel('chat.{receiver_id}.{sender_id}', function ($user, $receiver_id) {
    return (int) $user->id === (int) $receiver_id;
});
Broadcast::channel('group-chat.{id_conversation_ws}', function ($user, $id_conversation_ws) {
    return DB::table('conversation_member')
        ->where('id_user', $user->id)
        ->where('id_conversation', $id_conversation_ws)
        ->exists();
});
Broadcast::channel('group-status.{id_conversation_ws}', function ($user, $id_conversation_ws) {
    return DB::table('conversation_member')
        ->where('id_user', $user->id)
        ->where('id_conversation', $id_conversation_ws)
        ->exists();
});
Broadcast::channel('user-status.{id_conversation}', function ($user, $id_conversation) {
    return DB::table('conversation')
            ->where('id', $id_conversation)
            ->where(fn($q) => $q->where('id_account1', $user->id)
                                ->orWhere('id_account2', $user->id))
            ->exists();
});




