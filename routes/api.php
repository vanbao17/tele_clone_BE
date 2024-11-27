<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ResetPasswordController;
use Illuminate\Auth\Events\Verified;

// Auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
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

    return response()->json(['message' => 'Email verified successfully.']);
})->name('verification.verify');

// Protected routes requiring verified email
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
