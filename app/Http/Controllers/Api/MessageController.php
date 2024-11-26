<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class MessageController extends Controller
{
    // Lấy tất cả các tin nhắn của một cuộc trò chuyện
    public function index($conversationId)
    {
        $messages = Message::with('sender') // Tải thông tin người gửi
                            ->where('id_conversation', $conversationId)
                            ->where('is_deleted', false) // Chỉ lấy tin nhắn chưa bị xóa
                            ->orderBy('created_at', 'asc')
                            ->get();

        return response()->json($messages);
    }



    // Tạo tin nhắn mới
    public function store(Request $request)
    {
        $request->validate([
            'id_conversation' => 'required|exists:conversation,id',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content_type' => 'required|in:text,image,video,file',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'id_conversation' => $request->id_conversation,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content_type' => $request->content_type,
            'content' => $request->content,
        ]);
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    // Cập nhật trạng thái đã đọc của tin nhắn
    public function markAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->update(['is_read' => true]);

        return response()->json($message);
    }

    // Xóa tin nhắn (cập nhật trường is_deleted)
    public function delete($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->update(['is_deleted' => true]);

        return response()->json(['message' => 'Message deleted successfully']);
    }

    // Xóa hoàn toàn tin nhắn (không thể phục hồi)
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->delete();

        return response()->json(['message' => 'Message permanently deleted']);
    }
}
