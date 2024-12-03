<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConversationMember;
use App\Models\ConversationWs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\GroupStatus;

class ConversationWsController extends Controller
{
    // 1. Get all conversations
    public function index()
    {
        // Lấy tất cả các cuộc trò chuyện chưa bị xóa và kèm theo email của người sở hữu
        $conversations = ConversationWs::with('owner:id,email') // Tải thông tin email của người sở hữu
            ->where('is_deleted', false) // Lọc cuộc trò chuyện chưa bị xóa
            ->get();

        return response()->json($conversations);
    }

    // 2. Create a new conversation
    public function store(Request $request)
    {
    // Log thông tin ban đầu
    Log::info('Store conversation request received', [
        'request_data' => $request->all()  // Log toàn bộ dữ liệu request
    ]);

    // Validate dữ liệu
    $validated = $request->validate([
        'id_owner' => 'required|exists:users,id',
        'name' => 'required|string|max:100',
        'thumb' => 'nullable|string',
    ]);

    DB::beginTransaction(); // Bắt đầu transaction

    try {
        // Tạo conversation
        $conversation = ConversationWs::create($validated);

        // Log thông tin conversation đã được tạo
        Log::info('Conversation created successfully', [
            'conversation_id' => $conversation->id,
            'conversation_name' => $conversation->name,
        ]);

        // Thêm người tạo vào bảng conversation_member
        $conversationMember = [
            'id_conversation' => $conversation->id,
            'id_user' => $validated['id_owner'],
            'role' => 'admin', // Người tạo sẽ là admin
        ];

        ConversationMember::create($conversationMember);

        // Log thông tin thành viên đã được thêm
        Log::info('Conversation member added successfully', [
            'conversation_id' => $conversation->id,
            'user_id' => $validated['id_owner'],
            'role' => 'admin',
        ]);

        DB::commit(); // Hoàn tất transaction

        // Trả về response
        return response()->json([
            'message' => 'Conversation created successfully',
            'conversation' => $conversation,
            'status' => 200
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack(); // Hoàn tác transaction nếu lỗi xảy ra
        Log::error('Error creating conversation or adding member', [
            'error_message' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Failed to create conversation',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // 3. Show a specific conversation
    public function show($id)
    {
        $conversation = ConversationWs::find($id);

        if (!$conversation || $conversation->is_deleted) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        return response()->json($conversation);
    }

    // 4. Update a conversation
    public function update(Request $request, $id)
    {
        $conversation = ConversationWs::find($id);

        if (!$conversation || $conversation->is_deleted) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'thumb' => 'nullable|string',
        ]);

        $conversation->update($validated);

        return response()->json([
            'message' => 'Conversation updated successfully',
            'conversation' => $conversation
        ]);
    }

    // 5. Delete a conversation
    public function destroy($id)
    {
        $conversation = ConversationWs::find($id);

        if (!$conversation || $conversation->is_deleted) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->is_deleted = true;
        $conversation->save();

        return response()->json(['message' => 'Conversation deleted successfully']);
    }

    // 6. Rename a conversation (new method)
    public function rename(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Tìm cuộc trò chuyện theo ID
        $conversation = ConversationWs::find($id);

        if (!$conversation || $conversation->is_deleted) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        // Cập nhật tên cuộc trò chuyện
        $conversation->name = $validated['name'];
        $conversation->save();

        return response()->json([
            'status' => 200,
            'message' => 'Conversation renamed successfully',
            'conversation' => $conversation
        ]);
    }
    public function groupStatus(Request $request)
    {
        $validatedData = $request->validate([
            'sender_id' => 'required|integer|exists:users,id', 
            'id_conversation_ws' => 'required|integer|exists:conversation_ws,id', 
            'isTyping' => 'required|boolean', 
        ]);
        $sender_id = $validatedData['sender_id'];
        $id_conversation_ws = $validatedData['id_conversation_ws'];
        $isTyping = $validatedData['isTyping'];
        broadcast(new GroupStatus($sender_id, $id_conversation_ws, $isTyping))->toOthers();
        return response()->json(['status' => 'Typing status broadcasted']);
    }
}
