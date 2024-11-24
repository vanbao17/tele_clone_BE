<?php

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
use App\Http\Controllers\Api\UserController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/check-email', [RegisterController::class, 'checkEmail']);
Route::get('/conversations', [ConversationWsController::class, 'index']);
Route::post('/conversations', [ConversationWsController::class, 'store']);
Route::get('/conversations/{id}', [ConversationWsController::class, 'show']); 
Route::put('/conversations/{id}', [ConversationWsController::class, 'update']); 
Route::delete('/conversations/{id}', [ConversationWsController::class, 'destroy']); 
Route::patch('conversations/{id}/rename', [ConversationWsController::class, 'rename']);
Route::post('/conversation-member/add', [ConversationMemberController::class, 'addMember']);
Route::delete('/conversation-member/remove', [ConversationMemberController::class, 'removeMember']);
Route::get('/conversation-member/get-members', [ConversationMemberController::class, 'getMembers']);
Route::post('/conversations-ws', [ConversationMemberController::class, 'index']);
Route::get('/users/search', [UserController::class, 'search']);
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
