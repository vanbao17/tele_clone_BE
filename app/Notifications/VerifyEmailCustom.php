<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends Notification
{
    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Xác minh email của bạn')
            ->greeting('Chào bạn!')
            ->line('Vui lòng nhấp vào nút bên dưới để xác minh email của bạn.')
            ->action('Xác minh email', $url)
            ->line('Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.');
    }
}
