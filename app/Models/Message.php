<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Các trường có thể mass-assign
    protected $fillable = [
        'id_conversation',
        'sender_id',
        'receiver_id',
        'content_type',
        'content',
        'is_read',
        'is_deleted',
    ];

    // Cấu hình các kiểu dữ liệu cho các trường boolean
    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    // Định nghĩa quan hệ với bảng Conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'id_conversation');
    }

    // Định nghĩa quan hệ với User (Người gửi)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Định nghĩa quan hệ với User (Người nhận)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Cải thiện quản lý thời gian (nếu cần thiết)
    public $timestamps = true; // Đảm bảo Eloquent tự động quản lý created_at và updated_at
}
