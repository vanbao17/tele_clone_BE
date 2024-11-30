<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json(['message' => 'Email đã tồn tại trong hệ thống.'], 409); // Trả về mã lỗi 409 nếu email trùng
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'User registered successfully. Please verify your email before logging in.',
            'user' => $user,
        ], 201);
    }
    public function register_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json(['message' => 'Email đã tồn tại trong hệ thống.'], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
        ], 201);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json([
                'message' => 'Email đã tồn tại trong hệ thống.',
                "status"=> 201,
            ], 201);
        }
        return response()->json([
            'message' => 'Email hợp lệ và chưa được sử dụng.',
            "status"=> 200,
        ], 200);
    }
}

