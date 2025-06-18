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
        $workflowName = $event->workflowName;
        
        if (!$workflowName) {
            Log::error('Workflow name is required for notifications', [
                'model' => get_class($event->model),
                'model_id' => $event->model->id,
            ]);
            return;
        }

        // Check if auto-notification is enabled for this workflow
        if (!NotificationConfig::isAutoNotifyEnabled($workflowName)) {
            Log::info('Auto-notification disabled for workflow', [
                'workflow' => $workflowName,
                'model' => get_class($event->model),
                'model_id' => $event->model->id,
            ]);
            return;
        }

        $this->logTransitionEvent($event);
        $this->handleWorkflowNotifications($event, $workflowName);
    }

    /**
     * Handle workflow-specific notifications
     */
    private function handleWorkflowNotifications(WorkflowTransitionApplied $event, string $workflowName): void
    {
        $notificationData = $this->buildNotificationData($event, $workflowName);
        $settings = NotificationConfig::getWorkflowSettings($workflowName);

        // Send in-app notifications
        if (in_array('in_app', $settings['notification_types'])) {
            $this->sendInAppNotifications($event->model, $event->transition, $notificationData, $workflowName);
        }

        // Send email notifications
        if (in_array('email', $settings['notification_types'])) {
            $this->sendEmailNotifications($event->model, $event->transition, $notificationData, $workflowName);
        }
    }

    /**
     * Log the workflow transition event
     */
    private function logTransitionEvent(WorkflowTransitionApplied $event): void
    {
        Log::info('Processing workflow notifications', [
            'workflow' => $event->workflowName,
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState, $event->workflowName),
            'to' => WorkflowDefinition::getStateLabel($event->toState, $event->workflowName),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition, $event->workflowName),
            'user' => $event->context['user_name'] ?? 'System',
        ]);
    }

    /**
     * Build notification data array from event
     */
    private function buildNotificationData(WorkflowTransitionApplied $event, string $workflowName): array
    {
        $baseData = [
            'workflow_name' => $workflowName,
            'model_id' => $event->model->id,
            'model_type' => get_class($event->model),
            'from_state' => WorkflowDefinition::getStateLabel($event->fromState, $workflowName),
            'to_state' => WorkflowDefinition::getStateLabel($event->toState, $workflowName),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition, $workflowName),
            'comment' => $event->context['comment'] ?? null,
            'user_name' => $event->context['user_name'] ?? 'System',
            'user_id' => $event->context['user_id'] ?? null,
            'timestamp' => $event->context['timestamp'] ?? now(),
        ];

        // Add model-specific data
        if ($event->model instanceof Document) {
            $baseData['document_id'] = $event->model->id;
            $baseData['document_name'] = $event->model->file_name;
            $baseData['document_type'] = $event->model->documentType->display_name ?? 'Unknown';
        }

        return $baseData;
    }

    /**
     * Send in-app notifications to relevant users
     */
    private function sendInAppNotifications($model, int $transition, array $notificationData, string $workflowName): void
    {
        // Notify model owner
        if (NotificationConfig::shouldNotifyModelOwner($transition, $workflowName)) {
            $this->notifyModelOwner($model, $notificationData);
        }

        // Notify staff/reviewers
        if (NotificationConfig::shouldNotifyStaff($transition, $workflowName)) {
            $this->notifyStaff($notificationData, $workflowName);
        }
    }

    /**
     * Send email notifications for critical transitions
     */
    private function sendEmailNotifications($model, int $transition, array $notificationData, string $workflowName): void
    {
        if (!NotificationConfig::shouldSendEmail($transition, $workflowName)) {
            return;
        }

        try {
            // Handle document-specific emails
            if ($model instanceof Document) {
                $this->handleDocumentEmails($model, $transition, $notificationData);
            } else {
                // Handle generic workflow emails
                $this->handleGenericWorkflowEmails($model, $transition, $notificationData, $workflowName);
            }
        } catch (\Exception $e) {
            $this->logEmailFailure($model->id, $transition, $e);
        }
    }

    /**
     * Handle document-specific email notifications
     */
    private function handleDocumentEmails(Document $document, int $transition, array $notificationData): void
    {
        // Map transitions to email classes (this could be made configurable)
        $emailMap = [
            1 => DocumentSubmittedMail::class,    // SUBMIT
            2 => DocumentVerifiedMail::class,     // VERIFY
            3 => DocumentRejectedMail::class,     // REJECT
            4 => DocumentResubmittedMail::class,  // RESUBMIT
        ];

        $emailClass = $emailMap[$transition] ?? null;
        
        if ($emailClass && class_exists($emailClass)) {
            $this->sendEmail($document, $emailClass, $notificationData);
        } else {
            $this->logUnhandledTransition($transition, $notificationData);
        }
    }

    /**
     * Handle generic workflow email notifications
     */
    private function handleGenericWorkflowEmails($model, int $transition, array $notificationData, string $workflowName): void
    {
        $emailTemplate = NotificationConfig::getEmailTemplate($transition, $workflowName);
        
        if ($emailTemplate) {
            // Send using custom email template
            $this->sendCustomEmail($model, $emailTemplate, $notificationData);
        } else {
            // Log generic notification
            Log::info('Generic workflow email notification', [
                'workflow' => $workflowName,
                'transition' => $transition,
                'model' => get_class($model),
                'model_id' => $model->id,
            ]);
        }
    }

    /**
     * Send email using specified mail class
     */
    private function sendEmail($model, string $mailClass, array $notificationData): void
    {
        $recipients = $this->getEmailRecipients($model, $notificationData);
        
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new $mailClass($model, $notificationData));
        }
    }

    /**
     * Send custom email using template
     */
    private function sendCustomEmail($model, string $template, array $notificationData): void
    {
        $recipients = $this->getEmailRecipients($model, $notificationData);
        
        foreach ($recipients as $recipient) {
            Mail::send($template, $notificationData, function ($message) use ($recipient, $notificationData) {
                $message->to($recipient)
                        ->subject("Workflow Update: {$notificationData['transition']}");
            });
        }
    }

    /**
     * Get email recipients for the model
     */
    private function getEmailRecipients($model, array $notificationData): array
    {
        $recipients = [];

        // Add model owner
        $owner = $this->getModelOwner($model);
        if ($owner && $owner->email) {
            $recipients[] = $owner->email;
        }

        // Add staff emails
        $staffUsers = $this->getStaffUsers($notificationData['workflow_name']);
        foreach ($staffUsers as $staffUser) {
            if ($staffUser->email) {
                $recipients[] = $staffUser->email;
            }
        }

        return array_unique($recipients);
    }

    /**
     * Notify model owner
     */
    private function notifyModelOwner($model, array $notificationData): void
    {
        $owner = $this->getModelOwner($model);
        
        if ($owner) {
            $this->sendNotificationToUser(
                $owner,
                'workflow_updated',
                $notificationData
            );
        }
    }

    /**
     * Get model owner (supports different model types)
     */
    private function getModelOwner($model): ?User
    {
        // Handle Document model
        if ($model instanceof Document && $model->employee && $model->employee->user) {
            return $model->employee->user;
        }

        // Handle other models with user relationship
        if (method_exists($model, 'user') && $model->user) {
            return $model->user;
        }

        // Handle models with owner relationship
        if (method_exists($model, 'owner') && $model->owner) {
            return $model->owner;
        }

        return null;
    }

    /**
     * Notify staff/reviewers
     */
    private function notifyStaff(array $notificationData, string $workflowName): void
    {
        $staffUsers = $this->getStaffUsers($workflowName);
        
        foreach ($staffUsers as $staffUser) {
            $this->sendNotificationToUser(
                $staffUser,
                'workflow_action_required',
                $notificationData
            );
        }
    }

    /**
     * Get staff users for a specific workflow
     */
    private function getStaffUsers(string $workflowName): Collection
    {
        $staffRoles = NotificationConfig::getStaffRoles($workflowName);
        
        if (empty($staffRoles)) {
            return collect();
        }

        return User::whereHas('employee', function ($query) use ($staffRoles) {
            $query->whereIn('role', $staffRoles);
        })->get();
    }

    /**
     * Send notification to a specific user
     */
    private function sendNotificationToUser(User $user, string $type, array $data): void
    {
        try {
            // Here you would implement your notification system
            // For example, using Laravel's notification system:
            // $user->notify(new WorkflowNotification($type, $data));

            Log::info('In-app notification sent', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'type' => $type,
                'workflow' => $data['workflow_name'] ?? 'unknown',
                'model_id' => $data['model_id'] ?? null,
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
            'workflow' => $notificationData['workflow_name'] ?? 'unknown',
            'model_id' => $notificationData['model_id'],
        ]);
    }

    /**
     * Log email sending failure
     */
    private function logEmailFailure(int $modelId, int $transition, \Exception $e): void
    {
        Log::error('Failed to send email notification', [
            'model_id' => $modelId,
            'transition' => $transition,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
