<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }
        $token = $user->createToken('YourAppName')->plainTextToken;

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }    

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token
        ], 200);
    }
}
