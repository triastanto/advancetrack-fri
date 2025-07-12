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
            'title' => $this->getNotificationTitle(),
            'message' => $this->getNotificationMessage(),
            'icon' => $this->getNotificationIcon(),
            'color' => $this->getNotificationColor(),
            'action_url' => $this->getActionUrl(),
            'action_text' => $this->getActionText(),
            'priority' => $this->getNotificationPriority(),
        ];
    }

    /**
     * Get notification title based on type and data
     */
    private function getNotificationTitle(): string
    {
        $workflowName = $this->data['workflow_name'] ?? '';
        $transition = $this->data['transition'] ?? '';
        $documentType = $this->data['document_type'] ?? '';
        
        return match($this->type) {
            'workflow_updated' => "Status {$workflowName} Diperbarui",
            'workflow_action_required' => "Aksi Diperlukan - {$workflowName}",
            'document_approved' => "{$documentType} Disetujui",
            'document_rejected' => "{$documentType} Ditolak",
            'document_submitted' => "{$documentType} Dikirim untuk Verifikasi",
            'study_calendar_approved' => "Kalender Studi Disetujui",
            'study_calendar_rejected' => "Kalender Studi Ditolak",
            'study_started' => "Studi Dimulai",
            'study_completed' => "Studi Selesai",
            default => "Notifikasi {$workflowName}"
        };
    }

    /**
     * Get notification message based on type and data
     */
    private function getNotificationMessage(): string
    {
        $workflowName = $this->data['workflow_name'] ?? '';
        $transition = $this->data['transition'] ?? '';
        $fromState = $this->data['from_state'] ?? '';
        $toState = $this->data['to_state'] ?? '';
        $userName = $this->data['user_name'] ?? 'System';
        $comment = $this->data['comment'] ?? '';
        $documentName = $this->data['document_name'] ?? '';
        $documentType = $this->data['document_type'] ?? '';
        
        $baseMessage = match($this->type) {
            'workflow_updated' => "{$userName} mengubah status dari '{$fromState}' ke '{$toState}'",
            'workflow_action_required' => "{$userName} memerlukan aksi Anda untuk {$workflowName}",
            'document_approved' => "{$userName} menyetujui {$documentType} '{$documentName}'",
            'document_rejected' => "{$userName} menolak {$documentType} '{$documentName}'",
            'document_submitted' => "{$userName} mengirim {$documentType} '{$documentName}' untuk verifikasi",
            'study_calendar_approved' => "{$userName} menyetujui kalender studi Anda",
            'study_calendar_rejected' => "{$userName} menolak kalender studi Anda",
            'study_started' => "{$userName} memulai studi Anda",
            'study_completed' => "{$userName} menyelesaikan studi Anda",
            default => "{$userName} memperbarui {$workflowName}"
        };

        if ($comment) {
            $baseMessage .= ". Catatan: {$comment}";
        }

        return $baseMessage;
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon(): string
    {
        return match($this->type) {
            'workflow_updated' => 'heroicon-o-check-circle',
            'workflow_action_required' => 'heroicon-o-exclamation-triangle',
            'document_approved' => 'heroicon-o-check-circle',
            'document_rejected' => 'heroicon-o-x-circle',
            'document_submitted' => 'heroicon-o-arrow-up-tray',
            'study_calendar_approved' => 'heroicon-o-academic-cap',
            'study_calendar_rejected' => 'heroicon-o-x-circle',
            'study_started' => 'heroicon-o-play-circle',
            'study_completed' => 'heroicon-o-award',
            default => 'heroicon-o-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor(): string
    {
        return match($this->type) {
            'workflow_updated', 'document_approved', 'study_calendar_approved', 'study_completed' => 'success',
            'workflow_action_required', 'document_submitted' => 'warning',
            'document_rejected', 'study_calendar_rejected' => 'danger',
            'study_started' => 'info',
            default => 'info'
        };
    }

    /**
     * Get action URL for the notification
     */
    private function getActionUrl(): ?string
    {
        $workflowName = $this->data['workflow_name'] ?? '';
        $modelId = $this->data['model_id'] ?? null;
        
        if (!$modelId) {
            return null;
        }

        return match($workflowName) {
            'verification_by_staff' => route('administrations.verification') . "?document_id={$modelId}",
            'verification_by_management' => route('administrations.verification') . "?document_id={$modelId}",
            'study_calendar' => route('study-calendar.manage'),
            default => null
        };
    }

    /**
     * Get action text for the notification
     */
    private function getActionText(): string
    {
        $workflowName = $this->data['workflow_name'] ?? '';
        
        return match($workflowName) {
            'verification_by_staff', 'verification_by_management' => 'Lihat Dokumen',
            'study_calendar' => 'Lihat Kalender',
            default => 'Lihat Detail'
        };
    }

    /**
     * Get notification priority
     */
    private function getNotificationPriority(): string
    {
        return match($this->type) {
            'workflow_action_required' => 'high',
            'document_rejected', 'study_calendar_rejected' => 'medium',
            default => 'normal'
        };
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
