<?php

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
use App\Http\Controllers\Api\ResetPasswordController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/check-email', [RegisterController::class, 'checkEmail']);
Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
