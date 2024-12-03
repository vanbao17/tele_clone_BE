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


    public function showAll()
{
    $users = User::all(); // Lấy tất cả người dùng

    if ($users->isEmpty()) {
        return response()->json(['message' => 'No users found'], 404);
    }

    return response()->json([
        'message' => 'Users retrieved successfully',
        'users' => $users
    ]);
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
    // Nếu không có mật khẩu trong request, mặc định là '12345678'
    $password = $request->password ?: '12345678';  // mật khẩu mặc định

    // Kiểm tra và validate dữ liệu nhập
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'string|min:8',
    ]);

    // Tạo người dùng mới
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($password),
        'phoneNumber' => $request->phoneNumber,
        'email_verified_at' => now(), // Xác nhận email ngay lập tức
    ]);

    // Gửi thông báo hoặc thông tin người dùng đã được tạo thành công
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



// Upload ảnh và lưu vào imgUrl
    public function uploadFile(Request $request, $id)
{
    // Kiểm tra xem người dùng có tồn tại hay không
    $user = User::find($id);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Kiểm tra xem có file trong request không
    if ($request->hasFile('file')) {
        // Lấy file từ request
        $file = $request->file('file');
        
        // Kiểm tra định dạng file
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $allowedExtensions)) {
            return response()->json(['error' => 'Invalid file format'], 400);
        }

        // Lưu file vào thư mục public/uploads
        $path = $file->storeAs('public/uploads', $file->getClientOriginalName());

        // Tạo URL của file theo localhost:3000
        $url = env('APP_SERVER') . '/storage/uploads/' . $file->getClientOriginalName();

        // Cập nhật để khi truy cập vào url trả về ảnh trên trang web
        // $url = str_replace('public/', '', $url);


        // Cập nhật trường imgUrl của người dùng
        $user->thumb = $url;
        $user->imgUrl = $url;
        $user->save();

        // Trả về thông báo thành công và URL của file đã upload
        return response()->json(['message' => 'File uploaded successfully', 'imgUrl' => $url], 200);
    } else {
        return response()->json(['error' => 'No file provided'], 400);
    }
}



   // Cập nhật thông tin người dùng (tên, email, số điện thoại)
   public function updateUserInfo(Request $request, $id)
   {
       $request->validate([
           'name' => 'required|string|max:255',
           'phoneNumber' => 'required|string|max:15',
       ]);
   
       // Lấy người dùng theo ID
       $user = User::findOrFail($id); // Nếu không tìm thấy, trả về lỗi 404
       
       Log::info('Updating phone number: ' . $request->phoneNumber);
       // Cập nhật thông tin người dùng
       $user->update([
           'name' => $request->name,
           'phoneNumber' => $request->phoneNumber,
       ]);
   
       return response()->json([
           'message' => 'User information updated successfully',
           'user' => $user,
       ], 200);
   }


}
