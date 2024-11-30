<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MessageWs;
use Illuminate\Http\Request;
use App\Events\GroupMessageSent;

class MessageWsController extends Controller
{
    public function index($conversationWsId)
    {
        $messages = MessageWs::where('id_conversation_ws', $conversationWsId)
            ->where('is_deleted', false)
            ->with('sender') 
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Tạo tin nhắn mới
    public function store(Request $request)
    {
        $request->validate([
            'id_conversation_ws' => 'required|exists:conversation_ws,id',
            'sender_id' => 'required|exists:users,id',
            'content_type' => 'required|in:text,mention',
            'content' => 'required|string',
        ]);

        $message = MessageWs::create($request->all());

        broadcast(new GroupMessageSent($message, 'created'))->toOthers();
        return response()->json($message, 201); 
    }

    // Cập nhật tin nhắn (giả sử bạn chỉ cập nhật content)
    public function update(Request $request, $messageId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $message = MessageWs::findOrFail($messageId);
        $message->content = $request->content;
        $message->save();
        broadcast(new GroupMessageSent($message, 'updated'))->toOthers();

        return response()->json($message);
    }

    // Xóa tin nhắn
    public function destroy($id)
    {
        $message = MessageWs::findOrFail($id);
        broadcast(new GroupMessageSent($message, 'deleted'))->toOthers();
        $message->delete();

        return response()->json(null, 204);
    }
}