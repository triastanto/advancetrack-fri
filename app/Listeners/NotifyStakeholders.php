<?php

namespace App\Listeners;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Mail\DocumentSubmittedMail;
use App\Mail\DocumentVerifiedMail;
use App\Mail\DocumentRejectedMail;
use App\Mail\DocumentResubmittedMail;
use App\Models\Document;
use App\Models\User;
use App\Services\Workflow\WorkflowDefinition;
use App\Services\Workflow\NotificationConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyStakeholders
{
    /**
     * Handle the event.
     */
    public function handle(WorkflowTransitionApplied $event): void
    {
        $this->logTransitionEvent($event);

        // Handle Document-specific notifications
        if ($event->model instanceof Document) {
            $this->handleDocumentNotifications($event);
        }
    }

    /**
     * Log the workflow transition event
     */
    private function logTransitionEvent(WorkflowTransitionApplied $event): void
    {
        Log::info('Notifying stakeholders after workflow transition', [
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState),
            'to' => WorkflowDefinition::getStateLabel($event->toState),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition),
            'user' => $event->context['user_name'] ?? 'System',
        ]);
    }

    /**
     * Handle document-specific notifications
     */
    private function handleDocumentNotifications(WorkflowTransitionApplied $event): void
    {
        $document = $event->model;
        $notificationData = $this->buildNotificationData($event);

        // Send in-app notifications
        $this->sendInAppNotifications($document, $event->transition, $notificationData);

        // Send email notifications for critical transitions
        $this->sendEmailNotifications($document, $event->transition, $notificationData);
    }

    /**
     * Build notification data array from event
     */
    private function buildNotificationData(WorkflowTransitionApplied $event): array
    {
        return [
            'document_id' => $event->model->id,
            'document_name' => $event->model->file_name,
            'document_type' => $event->model->documentType->display_name ?? 'Unknown',
            'from_state' => WorkflowDefinition::getStateLabel($event->fromState),
            'to_state' => WorkflowDefinition::getStateLabel($event->toState),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition),
            'comment' => $event->context['comment'] ?? null,
            'user_name' => $event->context['user_name'] ?? 'System',
            'user_id' => $event->context['user_id'] ?? null,
            'timestamp' => $event->context['timestamp'] ?? now(),
        ];
    }

    /**
     * Send in-app notifications to relevant users
     */
    private function sendInAppNotifications(Document $document, int $transition, array $notificationData): void
    {
        // Notify document owner
        if ($this->shouldNotifyDocumentOwner($transition)) {
            $this->notifyDocumentOwner($document, $notificationData);
        }

        // Notify verification staff
        if ($this->shouldNotifyVerificationStaff($transition)) {
            $this->notifyVerificationStaff($notificationData);
        }
    }

    /**
     * Send email notifications for critical transitions
     */
    private function sendEmailNotifications(Document $document, int $transition, array $notificationData): void
    {
        if (!NotificationConfig::shouldSendEmail($transition)) {
            return;
        }

        try {
            match ($transition) {
                NotificationConfig::TRANSITION_SUBMIT => $this->sendDocumentSubmittedEmail($document, $notificationData),
                NotificationConfig::TRANSITION_VERIFY => $this->sendDocumentVerifiedEmail($document, $notificationData),
                NotificationConfig::TRANSITION_REJECT => $this->sendDocumentRejectedEmail($document, $notificationData),
                NotificationConfig::TRANSITION_RESUBMIT => $this->sendDocumentResubmittedEmail($document, $notificationData),
                default => $this->logUnhandledTransition($transition, $notificationData),
            };
        } catch (\Exception $e) {
            $this->logEmailFailure($document->id, $transition, $e);
        }
    }

    /**
     * Determine if email should be sent for this transition
     */
    private function shouldSendEmail(int $transition): bool
    {
        return NotificationConfig::shouldSendEmail($transition);
    }

    /**
     * Determine if document owner should be notified
     */
    private function shouldNotifyDocumentOwner(int $transition): bool
    {
        return NotificationConfig::shouldNotifyDocumentOwner($transition);
    }

    /**
     * Determine if verification staff should be notified
     */
    private function shouldNotifyVerificationStaff(int $transition): bool
    {
        return NotificationConfig::shouldNotifyVerificationStaff($transition);
    }

    /**
     * Notify document owner
     */
    private function notifyDocumentOwner(Document $document, array $notificationData): void
    {
        if ($document->employee && $document->employee->user) {
            $this->sendNotificationToUser(
                $document->employee->user,
                'document_workflow_updated',
                $notificationData
            );
        }
    }

    /**
     * Notify verification staff
     */
    private function notifyVerificationStaff(array $notificationData): void
    {
        $staffUsers = $this->getVerificationStaff();
        
        foreach ($staffUsers as $staffUser) {
            $this->sendNotificationToUser(
                $staffUser,
                'document_workflow_action_required',
                $notificationData
            );
        }
    }

    /**
     * Get verification staff users
     */
    private function getVerificationStaff(): Collection
    {
        $staffRoles = $this->getStaffRolesFromConfig();
        
        return User::whereHas('employee', function ($query) use ($staffRoles) {
            $query->whereIn('role', $staffRoles);
        })->get();
    }

    /**
     * Get staff roles from configuration
     */
    private function getStaffRolesFromConfig(): array
    {
        return NotificationConfig::getStaffRoles();
    }

    /**
     * Send notification to a specific user
     */
    private function sendNotificationToUser(User $user, string $type, array $data): void
    {
        try {
            // Here you would implement your notification system
            // For example, using Laravel's notification system:
            // $user->notify(new DocumentWorkflowNotification($type, $data));

            Log::info('In-app notification sent', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'type' => $type,
                'document_id' => $data['document_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send in-app notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log unhandled transition
     */
    private function logUnhandledTransition(int $transition, array $notificationData): void
    {
        Log::info('Unhandled email notification transition', [
            'transition_id' => $transition,
            'transition_name' => $notificationData['transition'],
            'document_id' => $notificationData['document_id'],
        ]);
    }

    /**
     * Log email sending failure
     */
    private function logEmailFailure(int $documentId, int $transition, \Exception $e): void
    {
        Log::error('Failed to send email notification', [
            'document_id' => $documentId,
            'transition' => $transition,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Send email notification when document is submitted
     */
    private function sendDocumentSubmittedEmail(Document $document, array $notificationData): void
    {
        $staffUsers = $this->getVerificationStaff();
        
        if ($staffUsers->isEmpty()) {
            Log::warning('No verification staff found for document submission notification', [
                'document_id' => $document->id,
            ]);
            return;
        }

        foreach ($staffUsers as $staffUser) {
            if (!$staffUser->email) {
                Log::warning('Staff user has no email address', [
                    'user_id' => $staffUser->id,
                    'document_id' => $document->id,
                ]);
                continue;
            }

            try {
                Mail::to($staffUser->email)->send(new DocumentSubmittedMail($document, $notificationData));
                
                Log::info('Document submission email sent', [
                    'document_id' => $document->id,
                    'recipient' => $staffUser->email,
                    'recipient_name' => $staffUser->name,
                    'recipient_role' => $staffUser->employee->role ?? 'unknown',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send document submission email to individual recipient', [
                    'document_id' => $document->id,
                    'recipient' => $staffUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send email notification when document is verified/approved
     */
    private function sendDocumentVerifiedEmail(Document $document, array $notificationData): void
    {
        $recipient = $this->getDocumentOwnerEmail($document);
        
        if (!$recipient) {
            Log::warning('Cannot send document verification email - no valid recipient', [
                'document_id' => $document->id,
                'employee_exists' => $document->employee ? 'yes' : 'no',
                'user_exists' => $document->employee && $document->employee->user ? 'yes' : 'no',
                'email_exists' => $document->employee && $document->employee->user && $document->employee->user->email ? 'yes' : 'no',
            ]);
            return;
        }

        Mail::to($recipient['email'])->send(new DocumentVerifiedMail($document, $notificationData));
        
        Log::info('Document verification email sent', [
            'document_id' => $document->id,
            'recipient' => $recipient['email'],
            'recipient_name' => $recipient['name'],
            'verifying_staff' => $notificationData['user_name'] ?? 'Unknown',
        ]);
    }

    /**
     * Send email notification when document is rejected
     */
    private function sendDocumentRejectedEmail(Document $document, array $notificationData): void
    {
        $recipient = $this->getDocumentOwnerEmail($document);
        
        if (!$recipient) {
            Log::warning('Cannot send document rejection email - no valid recipient', [
                'document_id' => $document->id,
                'employee_exists' => $document->employee ? 'yes' : 'no',
                'user_exists' => $document->employee && $document->employee->user ? 'yes' : 'no',
                'email_exists' => $document->employee && $document->employee->user && $document->employee->user->email ? 'yes' : 'no',
            ]);
            return;
        }

        Mail::to($recipient['email'])->send(new DocumentRejectedMail($document, $notificationData));
        
        Log::info('Document rejection email sent', [
            'document_id' => $document->id,
            'recipient' => $recipient['email'],
            'recipient_name' => $recipient['name'],
            'rejecting_staff' => $notificationData['user_name'] ?? 'Unknown',
            'rejection_reason' => $notificationData['comment'] ?? 'No reason provided',
        ]);
    }

    /**
     * Send email notification when document is resubmitted
     */
    private function sendDocumentResubmittedEmail(Document $document, array $notificationData): void
    {
        $staffUsers = $this->getVerificationStaff();
        
        if ($staffUsers->isEmpty()) {
            Log::warning('No verification staff found for document resubmission notification', [
                'document_id' => $document->id,
            ]);
            return;
        }

        foreach ($staffUsers as $staffUser) {
            if (!$staffUser->email) {
                Log::warning('Staff user has no email address', [
                    'user_id' => $staffUser->id,
                    'document_id' => $document->id,
                ]);
                continue;
            }

            try {
                Mail::to($staffUser->email)->send(new DocumentResubmittedMail($document, $notificationData));
                
                Log::info('Document resubmission email sent', [
                    'document_id' => $document->id,
                    'recipient' => $staffUser->email,
                    'recipient_name' => $staffUser->name,
                    'recipient_role' => $staffUser->employee->role ?? 'unknown',
                    'resubmitting_employee' => $document->employee->user->name ?? 'Unknown',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send document resubmission email to individual recipient', [
                    'document_id' => $document->id,
                    'recipient' => $staffUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get document owner email information
     */
    private function getDocumentOwnerEmail(Document $document): ?array
    {
        if (!$document->employee || 
            !$document->employee->user || 
            !$document->employee->user->email) {
            return null;
        }

        return [
            'email' => $document->employee->user->email,
            'name' => $document->employee->user->name,
        ];
    }
}
