<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory,HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'thumb', 'code', 'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
