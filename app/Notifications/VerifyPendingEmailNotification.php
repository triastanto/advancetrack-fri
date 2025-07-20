<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerifyPendingEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your New Email Address')
            ->line('Please click the button below to verify your new email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not request this change, please ignore this email.');
    }

    protected function verificationUrl($notifiable)
    {
        $expiration = Carbon::now()->addMinutes(config('auth.verification.expire', 60));
        return URL::temporarySignedRoute(
            'pending-email.verify',
            $expiration,
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->pending_email),
            ]
        );
    }
} 