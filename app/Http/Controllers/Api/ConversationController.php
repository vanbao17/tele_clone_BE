<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\UserTyping;
use App\Events\UserGroupTyping;

class ConversationController extends Controller
{
    // Lấy danh sách tất cả các cuộc trò chuyện
    public function index($userId)
    {
        $conversations = DB::table('conversation as c')
            ->join('users as u1', 'c.id_account1', '=', 'u1.id')
            ->join('users as u2', 'c.id_account2', '=', 'u2.id')
            ->select(
                'c.id as id_conversation',
                DB::raw("CASE WHEN c.id_account1 = {$userId} THEN u2.name WHEN c.id_account2 = {$userId} THEN u1.name END as name"),
                DB::raw("CASE WHEN c.id_account1 = {$userId} THEN u2.thumb WHEN c.id_account2 = {$userId} THEN u1.thumb END as thumb"),
                DB::raw("CASE WHEN c.id_account1 = {$userId} THEN u2.id WHEN c.id_account2 = {$userId} THEN u1.id END as id_user"),
                DB::raw("CASE WHEN c.id_account1 = {$userId} THEN u1.id WHEN c.id_account2 = {$userId} THEN u2.id END as other_user_id")  // Trả về ID của người còn lại
            )
            ->where('c.id_account1', $userId)
            ->orWhere('c.id_account2', $userId)
            ->get();

        return response()->json($conversations);
    }






    

    // Tạo một cuộc trò chuyện mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_account1' => 'required|exists:users,id',
            'id_account2' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::create($validated);
        return response()->json($conversation, 201);
    }

    // Lấy thông tin chi tiết một cuộc trò chuyện
    public function show($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->is_deleted) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        return response()->json($conversation);
    }

    // Cập nhật một cuộc trò chuyện
    public function update(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->is_deleted) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $validated = $request->validate([
            'id_account1' => 'sometimes|exists:users,id',
            'id_account2' => 'sometimes|exists:users,id',
        ]);

        $conversation->update($validated);
        return response()->json($conversation);
    }

    // Xóa mềm một cuộc trò chuyện
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->is_deleted) {
            return response()->json(['error' => 'Conversation already deleted'], 404);
        }

        $conversation->update(['is_deleted' => true]);
        return response()->json(['message' => 'Conversation deleted']);
    }
    public function typingStatus(Request $request)
    {
        $validatedData = $request->validate([
            'sender_id' => 'required|integer|exists:users,id', 
            'id_conversation' => 'required|integer|exists:conversation,id',
            'isTyping' => 'required|boolean', 
        ]);

        $sender_id = $validatedData['sender_id'];
        $id_conversation = $validatedData['id_conversation'];
        $isTyping = $validatedData['isTyping'];

        broadcast(new UserTyping($sender_id, $id_conversation, $isTyping))->toOthers();

        return response()->json(['status' => 'Typing status broadcasted']);
    }

}