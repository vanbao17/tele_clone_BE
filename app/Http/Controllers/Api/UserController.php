<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    
    public function search(Request $request)
    {
        // Log request data
    Log::info('Search request data:', $request->all());

    // Validate email input
    $request->validate([
        'email' => 'required|string',
    ]);

    // Get the email from the request
    $email = $request->input('email');
    Log::info('Email received:', ['email' => $email]);

    // Tách tên từ email (phần trước @gmail.com)
    $namePart = explode('@', $email)[0];

    // Search users by the name part (before the @gmail.com)
    $users = User::where('email', 'LIKE', "%{$namePart}%")->get();

    Log::info('Search results:', $users->toArray());

    return response()->json([
        'status' => 200,
        'message' => 'Users found',
        'users' => $users,
    ]);
    }
}
