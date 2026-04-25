<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification ini sudah digantikan oleh App\Mail\OTPMail.
 * Dipertahankan untuk kompatibilitas dengan MustVerifyEmail interface
 * jika diperlukan di masa mendatang.
 */
class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email - IBIK Event')
            ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
            ->line('Klik tombol di bawah untuk memverifikasi email Anda.')
            ->action('Verifikasi Email', url('/'))
            ->line('Jika Anda tidak mendaftar, abaikan email ini.')
            ->salutation('Salam, Tim IBIK Event');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
