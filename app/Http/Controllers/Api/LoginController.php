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
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ', 'status' => 401], 401);
        }
        $token = $user->createToken('YourAppName')->plainTextToken;

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email before logging in.', 'status' => 403], 403);
        }    

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user'=>$user
        ], 200);
    }

    // Hàm xác thực token và trả về thông tin người dùng
    public function checkTokenAndReturnUser(Request $request)
    {
        // Lấy token từ header Authorization
        $token = $request->bearerToken();

        // Nếu không có token trong header
        if (!$token) {
            return response()->json(['message' => 'Token không được cung cấp.'], 401);
        }

        // Xác thực token qua Sanctum và lấy user
        try {
            // Lấy thông tin người dùng từ token
            $user = Auth::guard('sanctum')->user();

            // Kiểm tra nếu token không hợp lệ hoặc hết hạn
            if (!$user) {
                return response()->json(['message' => 'Token không hợp lệ hoặc hết hạn.'], 401);
            }

            // Trả về người dùng nếu token hợp lệ
            return response()->json(['user' => $user]);

        } catch (\Exception $e) {
            // Xử lý lỗi nếu có vấn đề khi xác thực
            return response()->json(['message' => 'Đã xảy ra lỗi trong quá trình xác thực.'], 500);
        }
    }
    
}
