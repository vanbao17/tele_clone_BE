<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Tạo admin user
        User::updateOrCreate(
            ['email' => 'sbtc_admin@gmail.com'], // Điều kiện tìm user
            [
                'name' => 'Admin',
                'email' => 'sbtc_admin@gmail.com',
                'password' => Hash::make('sbtcgroup'), // Mật khẩu cố định
                'isAdmin' => true, // Đặt isAdmin thành true
                'imgUrl' => 'http://localhost:8000/storage/uploads/admin.webp',
                'email_verified_at' => now(),
                'thumb' => 'http://localhost:8000/storage/uploads/admin.webp',
            ]
        );
    }
}
