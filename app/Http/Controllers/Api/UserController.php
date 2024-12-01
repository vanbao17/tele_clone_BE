<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

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


     // CRUD User

public function index(Request $request)
{
    $query = User::query();

    // Kiểm tra nếu có tham số 'search'
    if ($request->has('search')) {
        $search = $request->get('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    // Thực thi query và trả kết quả
    $users = $query->get();

    return response()->json($users);
}

public function show($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
        'message' => 'User retrieved successfully',
        'user' => $user
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    // Tạo người dùng mới
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'User created successfully',
        'user' => $user
    ], 201);
}

public function update(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->update($request->only(['name', 'email', 'password']));

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}

public function destroy($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();
    return response()->json(['message' => 'User deleted successfully']);
}

public function getTrashed()
{
    // Lấy tất cả người dùng đã bị xóa mềm
    $trashedUsers = User::onlyTrashed()->get();

    return response()->json([
        'message' => 'Trashed users retrieved successfully',
        'data' => $trashedUsers,
    ]);
}

public function restore($id)
{
    // Tìm người dùng bị xóa mềm theo ID
    $user = User::withTrashed()->find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

    // Phục hồi người dùng
    $user->restore();

    return response()->json([
        'message' => 'User restored successfully',
        'data' => $user,
    ]);
}
}
