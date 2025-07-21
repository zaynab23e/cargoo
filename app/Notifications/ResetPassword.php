<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رمز إعادة تعيين كلمة المرور')
            ->line('رمز التحقق الخاص بك هو:')
            ->line("🔐 {$this->code}")
            ->line('الرمز صالح لمدة 10 دقائق فقط.')
            ->line('إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.');
    }
}
