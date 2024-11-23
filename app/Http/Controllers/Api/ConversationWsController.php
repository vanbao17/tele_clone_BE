<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConversationWs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationWsController extends Controller
{
    // 1. Get all conversations
    public function index()
    {
        $conversations = ConversationWs::where('is_deleted', false)->get();
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

        // Log thông tin sau khi validate
        Log::info('Data validated', [
            'validated_data' => $validated  // Log dữ liệu đã qua validate
        ]);

        // Tạo conversation
        $conversation = ConversationWs::create($validated);

        // Log thông tin conversation đã được tạo
        Log::info('Conversation created successfully', [
            'conversation_id' => $conversation->id,  // Log ID của conversation mới tạo
            'conversation_name' => $conversation->name,  // Log tên của conversation
        ]);

        // Trả về response
        return response()->json([
            'message' => 'Conversation created successfully',
            'conversation' => $conversation,
            'status' => 200
        ], 201);
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
}
