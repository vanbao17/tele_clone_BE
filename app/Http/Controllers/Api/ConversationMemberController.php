<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationMember;
use App\Models\ConversationWs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversationMemberController extends Controller
{
    /**
     * Thêm dữ liệu vào bảng conversation_member.
     */
    public function index(Request $request)
    {
        // Nhận `user_id` từ request
        $userId = $request->input('user_id');

        // Kiểm tra đầu vào
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        // Truy vấn dữ liệu
        $conversations = DB::table('conversation_ws')
            ->join('conversation_member', 'conversation_ws.id', '=', 'conversation_member.id_conversation')
            ->where('conversation_member.id_user', $userId) // Lọc theo user_id
            ->where('conversation_member.is_deleted', false) // Lọc các bản ghi chưa bị xóa
            ->where('conversation_ws.is_deleted', false) // Lọc các cuộc trò chuyện chưa bị xóa
            ->select('conversation_ws.*') // Chỉ lấy thông tin từ bảng conversation_ws
            ->get();

        // Trả về danh sách cuộc trò chuyện
        return response()->json($conversations);
    }

    public function addMember(Request $request)
    {
        // Xác thực chỉ có id_conversation và id_user
        $request->validate([
            'id_conversation' => 'required|exists:conversation_ws,id',
            'id_user' => 'required|exists:users,id',
        ]);

        // Tạo mới ConversationMember
        $member = new ConversationMember();
        $member->id_conversation = $request->id_conversation;
        $member->id_user = $request->id_user;
        $member->role = 'member'; // Mặc định là 'member' (không cần đưa vào request)
        $member->save();

        // Trả về phản hồi thành công với thông tin thành viên
        return response()->json([
            'message' => 'Thành viên đã được thêm thành công.',
            'data' => $member,
            'status' => 200
        ], 201);
    }


    public function addMultipleMembers(Request $request)
{
    // Xác thực yêu cầu
    $request->validate([
        'id_conversation' => 'required|exists:conversation_ws,id',
        'id_users' => 'required|array', // Đảm bảo 'id_users' là một mảng
        'id_users.*' => 'required|exists:users,id', // Mỗi phần tử trong mảng phải tồn tại trong bảng users
    ]);

    // Lấy danh sách các id_user từ request
    $idUsers = $request->id_users;
    $idConversation = $request->id_conversation;

    // Mảng để lưu các thành viên mới
    $newMembers = [];

    // Thêm từng user vào conversation
    foreach ($idUsers as $idUser) {
        // Kiểm tra nếu user đã là thành viên để tránh thêm trùng lặp
        $existingMember = ConversationMember::where('id_conversation', $idConversation)
            ->where('id_user', $idUser)
            ->first();

        if (!$existingMember) {
            // Tạo mới ConversationMember
            $member = new ConversationMember();
            $member->id_conversation = $idConversation;
            $member->id_user = $idUser;
            $member->role = 'member'; // Mặc định là 'member'
            $member->save();

            // Thêm vào danh sách thành viên mới
            $newMembers[] = $member;
        }
    }

    // Trả về phản hồi thành công với danh sách thành viên đã thêm
    return response()->json([
        'message' => 'Thành viên đã được thêm thành công.',
        'data' => $newMembers, // Danh sách các thành viên được thêm
        'status' => 200
    ], 201);
}


    public function removeMember(Request $request)
    {
        $idConversation = $request->query('id_conversation');
        $idUser = $request->query('id_user');
        
        if (!$idConversation || !$idUser) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ.'], 400);
            Log::info('Dữ liệu không hợp lệ.:');
        }
        
        $member = ConversationMember::where('id_conversation', $idConversation)
            ->where('id_user', $idUser)
            ->first();
        $member->delete();

        return response()->json(['message' => 'Thành viên đã được xóa thành công.', 'status' => 200], 200);
    }

    public function removeMultipleMembers(Request $request)
{
    // Xác thực dữ liệu đầu vào
    $request->validate([
        'id_conversation' => 'required|exists:conversation_ws,id',
        'id_users' => 'required|array',
        'id_users.*' => 'exists:users,id', // Đảm bảo từng id_user tồn tại trong bảng users
    ]);

    $idConversation = $request->id_conversation;
    $idUsers = $request->id_users;

    // Lấy danh sách thành viên cần xóa
    $members = ConversationMember::where('id_conversation', $idConversation)
        ->whereIn('id_user', $idUsers)
        ->get();

    if ($members->isEmpty()) {
        return response()->json([
            'message' => 'Không tìm thấy thành viên nào phù hợp để xóa.',
            'status' => 404
        ], 404);
    }

    // Xóa các thành viên được tìm thấy
    ConversationMember::where('id_conversation', $idConversation)
        ->whereIn('id_user', $idUsers)
        ->delete();

    return response()->json([
        'message' => 'Các thành viên đã được xóa thành công.',
        'removed_users' => $idUsers,
        'status' => 200
    ], 200);
}


    public function getMembers(Request $request)
{
    // Validate yêu cầu
    $request->validate([
        'id_conversation' => 'required|exists:conversation_ws,id',
    ]);

    // Lấy thông tin của conversation
    $conversation = ConversationWs::find($request->id_conversation);

    // Lấy danh sách thành viên và role, thay đổi cách lấy thông tin user
    $members = ConversationMember::where('id_conversation', $request->id_conversation)
        ->with('user:id,name,email') // Lấy thông tin người dùng
        ->get()
        ->map(function ($member) {
            // Thêm thông tin user vào cùng level với member
            $user = $member->user;  // Lấy thông tin user
            // Gỡ bỏ trường user và merge trực tiếp thông tin user vào member
            unset($member->user);
            $member->id_user = $user->id;
            $member->name = $user->name;
            $member->email = $user->email;
            return $member;
        });

    // Trả về thông tin conversation và danh sách thành viên
    return response()->json([
        'message' => 'Danh sách thành viên và thông tin cuộc trò chuyện đã được lấy thành công.',
        'conversation' => $conversation,  // Thông tin của conversation
        'members' => $members,  // Danh sách thành viên với role và thông tin user ở cấp cao hơn
        'status' => 200
    ], 200);
}

}
