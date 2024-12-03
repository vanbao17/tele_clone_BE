<?php
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ConversationMemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
 
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ConversationWsController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageWsController;
use App\Http\Controllers\Api\ResetPasswordController;
use Illuminate\Auth\Events\Verified;

// Auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register_verify', [RegisterController::class, 'register_verify']);
Route::post('/check-email', [RegisterController::class, 'checkEmail']);

// Password reset routes
Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

// Email verification routes
Route::middleware('auth:api')->group(function () {
    // Send email verification notification
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->middleware('throttle:6,1')->name('verification.send');
});

// Verify email route
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

     // Chuyển hướng đến trang verify-successful
     return redirect('http://localhost:3000/verify-successful');
})->name('verification.verify');


Route::middleware(['auth:api', 'verified'])->group(function () {
    // Lấy thông tin user đã đăng nhập
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
 
    // Một route bảo vệ khác
    Route::get('/protected-route', function () {
        return response()->json(['message' => 'This is a verified route']);
    });
});
 
Route::get('/conversations', [ConversationWsController::class, 'index']);
Route::post('/conversations/group-status', [ConversationWsController::class, 'groupStatus']);
Route::post('/conversations', [ConversationWsController::class, 'store']);
Route::get('/conversations/{id}', [ConversationWsController::class, 'show']); 
Route::put('/conversations/{id}', [ConversationWsController::class, 'update']); 
Route::delete('/conversations/{id}', [ConversationWsController::class, 'destroy']); 
Route::patch('conversations/{id}/rename', [ConversationWsController::class, 'rename']);
Route::post('/conversation-member/add', [ConversationMemberController::class, 'addMember']);
Route::delete('/conversation-member/remove', [ConversationMemberController::class, 'removeMember']);
Route::get('/conversation-member/get-members', [ConversationMemberController::class, 'getMembers']);
Route::post('/conversations-ws', [ConversationMemberController::class, 'index']);

Route::post('/conversationUser/user-status', [ConversationController::class, 'userStatus']);
Route::get('/conversationUser/{id}', [ConversationController::class, 'index']);
Route::post('/conversationUser', [ConversationController::class, 'store']);
Route::post('/conversationUser/typing-status', [ConversationController::class, 'typingStatus']);
Route::get('/conversationUser/detail/{id}', [ConversationController::class, 'show']);
Route::delete('/conversationUser/{id}', [ConversationController::class, 'destroy']);
Route::prefix('messages')->group(function () {
    Route::get('{conversationId}', [MessageController::class, 'index']);  // Lấy tất cả tin nhắn của một cuộc trò chuyện
    Route::post('', [MessageController::class, 'store']);  // Tạo tin nhắn mới
    Route::patch('{messageId}/read', [MessageController::class, 'markAsRead']);  // Đánh dấu là đã đọc
    Route::put('{messageId}/update', [MessageController::class, 'update']);
    Route::patch('{messageId}/delete', [MessageController::class, 'delete']);  // Xóa tin nhắn (đánh dấu là xóa)
    Route::delete('{messageId}', [MessageController::class, 'destroy']);  // Xóa hoàn toàn tin nhắn
});
Route::prefix('messagesws')->group(function () {
    Route::get('{conversationWsId}', [MessageWsController::class, 'index']);  
    Route::post('/', [MessageWsController::class, 'store']);  
    Route::put('{messageId}/update', [MessageWsController::class, 'update']);  
    Route::delete('{id}', [MessageWsController::class, 'destroy']);  
});
Route::get('/users/search', [UserController::class, 'search']);
Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// // CRUD routes for conversation_ws
// Route::prefix('conversations')->middleware('auth:sanctum')->group(function () {
//     Route::get('/', [ConversationWsController::class, 'index']);
//     Route::post('/', [ConversationWsController::class, 'store']);
//     Route::get('/{id}', [ConversationWsController::class, 'show']); 
//     Route::put('/{id}', [ConversationWsController::class, 'update']); 
//     Route::delete('/{id}', [ConversationWsController::class, 'destroy']); 
// });






Route::middleware('auth:sanctum')->group(function () {
    // Cập nhật thông tin người dùng theo ID
    Route::put('update-info/{id}', [UserController::class, 'updateUserInfo']);
    Route::post('upload-file/{id}', [UserController::class, 'uploadFile']);
    Route::get('/checkUser/user', [LoginController::class, 'checkTokenAndReturnUser']);
    Route::get('/checkAdmin/admin', [LoginController::class, 'checkTokenAndReturnAdmin']);
    
});

Route::get('/users', [UserController::class, 'showAll']);  // Chỉ dành cho admin

Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);  // Chỉ dành cho admin
    Route::put('/users/{id}', [UserController::class, 'update']);  // Chỉ dành cho admin
    Route::delete('/users/{id}', [UserController::class, 'destroy']);  // Chỉ dành cho admin
});