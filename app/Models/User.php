<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    public function markEmailAsVerified()
    {
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();

        return true;
    }
}
