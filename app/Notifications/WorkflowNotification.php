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
        return ['database', 'mail'];
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
     * Get the notification title.
     */
    protected function getNotificationTitle(): string
    {
        return match($this->type) {
            'semester_report_reminder' => 'Pengingat Laporan Semester',
            'final_report_reminder' => 'Pengingat Laporan Akhir',
            'document_submitted' => 'Dokumen Dikirim untuk Verifikasi',
            'document_verified' => 'Dokumen Disetujui',
            'document_rejected' => 'Dokumen Ditolak',
            'document_resubmitted' => 'Dokumen Dikirim Ulang',
            'study_calendar_approved' => 'Kalender Studi Disetujui',
            'study_calendar_rejected' => 'Kalender Studi Ditolak',
            'study_started' => 'Studi Dimulai',
            'study_completed' => 'Studi Selesai',
            'workflow_action_required' => 'Aksi Diperlukan pada Workflow',
            'workflow_updated' => 'Workflow Diperbarui',
            default => 'Notifikasi Sistem'
        };
    }

    /**
     * Get the notification message.
     */
    protected function getNotificationMessage(): string
    {
        return match($this->type) {
            'semester_report_reminder' => $this->getSemesterReminderMessage(),
            'final_report_reminder' => $this->getFinalReminderMessage(),
            'document_submitted' => "Dokumen {$this->data['document_name']} telah dikirim untuk verifikasi.",
            'document_verified' => "Dokumen {$this->data['document_name']} telah disetujui.",
            'document_rejected' => "Dokumen {$this->data['document_name']} ditolak. Silakan perbaiki dan kirim ulang.",
            'document_resubmitted' => "Dokumen {$this->data['document_name']} telah dikirim ulang untuk verifikasi.",
            'study_calendar_approved' => 'Kalender studi Anda telah disetujui. Anda dapat memulai studi.',
            'study_calendar_rejected' => 'Kalender studi Anda ditolak. Silakan perbaiki dan kirim ulang.',
            'study_started' => 'Studi Anda telah dimulai. Selamat belajar!',
            'study_completed' => 'Selamat! Studi Anda telah selesai.',
            'workflow_action_required' => 'Ada aksi yang perlu Anda lakukan pada workflow.',
            'workflow_updated' => 'Status workflow telah diperbarui.',
            default => 'Anda memiliki notifikasi baru.'
        };
    }

    /**
     * Get semester reminder message
     */
    protected function getSemesterReminderMessage(): string
    {
        $lecturerName = $this->data['lecturer_name'] ?? 'Anda';
        $semester = $this->data['semester'] ?? '';
        $missingDocuments = $this->data['missing_documents'] ?? '';
        
        return "Halo {$lecturerName}, Anda belum mengunggah dokumen laporan semester {$semester}. " .
               "Dokumen yang belum diunggah: {$missingDocuments}. " .
               "Silakan lengkapi dan unggah dokumen tersebut.";
    }

    /**
     * Get final reminder message
     */
    protected function getFinalReminderMessage(): string
    {
        $lecturerName = $this->data['lecturer_name'] ?? 'Anda';
        $daysUntilCompletion = $this->data['days_until_completion'] ?? 0;
        $missingDocuments = $this->data['missing_documents'] ?? '';
        
        return "Halo {$lecturerName}, studi Anda akan selesai dalam {$daysUntilCompletion} hari. " .
               "Dokumen laporan akhir yang belum diunggah: {$missingDocuments}. " .
               "Silakan lengkapi dokumen tersebut untuk menyelesaikan studi.";
    }

    /**
     * Get the notification icon.
     */
    protected function getNotificationIcon(): string
    {
        return match($this->type) {
            'semester_report_reminder' => '📚',
            'final_report_reminder' => '🎓',
            'document_submitted' => '📤',
            'document_verified' => '✅',
            'document_rejected' => '❌',
            'document_resubmitted' => '🔄',
            'study_calendar_approved' => '📅',
            'study_calendar_rejected' => '⚠️',
            'study_started' => '🚀',
            'study_completed' => '🏆',
            default => '🔔'
        };
    }

    /**
     * Get the notification color.
     */
    protected function getNotificationColor(): string
    {
        return match($this->type) {
            'semester_report_reminder' => 'warning',
            'final_report_reminder' => 'danger',
            'document_submitted' => 'info',
            'document_verified' => 'success',
            'document_rejected' => 'danger',
            'document_resubmitted' => 'info',
            'study_calendar_approved' => 'success',
            'study_calendar_rejected' => 'danger',
            'study_started' => 'success',
            'study_completed' => 'success',
            default => 'info'
        };
    }

    /**
     * Get the action URL.
     */
    protected function getActionUrl(): string
    {
        // For study calendar notifications, link to approval page with studyCalendarId if available
        if (in_array($this->type, ['study_calendar_approved', 'study_calendar_rejected', 'study_started', 'study_completed', 'workflow_action_required', 'workflow_updated'])) {
            $studyCalendarId = $this->data['study_calendar_id'] ?? null;
            if ($studyCalendarId) {
                return url('/study-calendar/approval?studyCalendarId=' . $studyCalendarId);
            }
        }
        return match($this->type) {
            'semester_report_reminder' => $this->data['action_url'] ?? route('documents.semester-reports'),
            'final_report_reminder' => $this->data['action_url'] ?? route('documents.final-reports'),
            'document_submitted', 'document_verified', 'document_rejected', 'document_resubmitted' => 
                route('documents.study-requirements'),
            'study_calendar_approved', 'study_calendar_rejected' => 
                route('study-calendar.manage'),
            'study_started', 'study_completed' => 
                route('study-calendar.manage'),
            default => route('dashboard')
        };
    }

    /**
     * Get the action text.
     */
    protected function getActionText(): string
    {
        return match($this->type) {
            'semester_report_reminder' => 'Unggah Dokumen Semester',
            'final_report_reminder' => 'Unggah Dokumen Akhir',
            'document_submitted', 'document_verified', 'document_rejected', 'document_resubmitted' => 
                'Lihat Dokumen',
            'study_calendar_approved', 'study_calendar_rejected' => 
                'Kelola Kalender Studi',
            'study_started', 'study_completed' => 
                'Lihat Status Studi',
            default => 'Lihat Detail'
        };
    }

    /**
     * Get the notification priority.
     */
    protected function getNotificationPriority(): string
    {
        return match($this->type) {
            'semester_report_reminder' => 'medium',
            'final_report_reminder' => 'high',
            'document_submitted' => 'normal',
            'document_verified' => 'normal',
            'document_rejected' => 'high',
            'document_resubmitted' => 'normal',
            'study_calendar_approved' => 'normal',
            'study_calendar_rejected' => 'high',
            'study_started' => 'normal',
            'study_completed' => 'normal',
            default => 'normal'
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Use specific email templates for reminder notifications
        if (in_array($this->type, ['semester_report_reminder', 'final_report_reminder'])) {
            return $this->sendReminderEmail($notifiable);
        }
        
        // Use default mail format for other notifications
        $subject = $this->getNotificationTitle();
        $message = $this->getNotificationMessage();
        $actionUrl = $this->getActionUrl();
        $actionText = $this->getActionText();
        
        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Halo {$notifiable->name}!")
            ->line($message);
            
        // Add action button if URL is available
        if ($actionUrl) {
            $mailMessage->action($actionText, $actionUrl);
        }
        
        // Add additional context based on notification type
        switch ($this->type) {
            case 'document_submitted':
                $mailMessage->line("Dokumen: {$this->data['document_name']}")
                           ->line("Status: Menunggu Verifikasi");
                break;
                
            case 'document_verified':
                $mailMessage->line("Dokumen: {$this->data['document_name']}")
                           ->line("Status: Terverifikasi");
                break;
                
            case 'document_rejected':
                $rejectionReason = $this->data['rejection_reason'] ?? 'Tidak ada catatan';
                $mailMessage->line("Dokumen: {$this->data['document_name']}")
                           ->line("Status: Ditolak")
                           ->line("Catatan: {$rejectionReason}");
                break;
        }
        
        $mailMessage->line("Terima kasih telah menggunakan sistem AdvanceTrack.")
                   ->salutation("Salam,");
                   
        return $mailMessage;
    }
    
    /**
     * Send reminder email using custom template
     */
    private function sendReminderEmail($notifiable)
    {
        $template = $this->type === 'semester_report_reminder' 
            ? 'emails.academic-document-reminder' 
            : 'emails.final-report-reminder';
            
        return (new MailMessage)
            ->subject($this->getNotificationTitle())
            ->view($template, [
                'notificationData' => $this->data,
                'user' => $notifiable
            ]);
    }
}
