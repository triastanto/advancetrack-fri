<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WorkflowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // Add 'mail' if you want to send as email too
    }

    /**
     * Get the array representation of the notification (for database channel).
     */
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
        ];
    }

    /**
     * (Optional) Get the mail representation of the notification.
     * Uncomment and customize if you want to send as email too.
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Workflow Notification')
    //         ->line('You have a workflow update.')
    //         ->line('Type: ' . $this->type)
    //         ->line('Details: ' . json_encode($this->data));
    // }
}
