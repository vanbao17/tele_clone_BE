<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    // Tên bảng
    protected $table = 'conversation';

    // Chỉ định các cột có thể được thêm hoặc sửa đổi
    protected $fillable = [
        'id_account1',
        'id_account2',
        'is_deleted',
    ];

    // Định nghĩa các kiểu dữ liệu
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    // Ẩn các trường không cần thiết khi trả về JSON
    protected $hidden = [
        'created_date',
        'updated_date',
    ];
}
