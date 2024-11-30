<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmailNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Custom Subject for Email Verification') // Tiêu đề email
            ->line('Please click the button below to verify your email address.') // Dòng mô tả
            ->action('Verify Email Address', $this->verificationUrl($notifiable)) // Nút xác minh
            ->line('Thank you for using our application!'); // Dòng kết thúc
    }
}