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
        $uniqueId = uniqid('notify_', true);
        Log::info('Processing workflow notifications', [
            'unique_id' => $uniqueId,
            'workflow' => $event->workflowName,
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState, $event->workflowName),
            'to' => WorkflowDefinition::getStateLabel($event->toState, $event->workflowName),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition, $event->workflowName),
            'user' => $event->context['user_name'] ?? 'System',
            'timestamp' => now()->toISOString(),
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
            'transition_id' => $event->transition,
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
        // Get all notification recipients for this transition
        $recipients = NotificationConfig::getNotificationRecipients($transition, $workflowName);

        // Notify model owner if configured
        if (NotificationConfig::shouldNotifyModelOwner($transition, $workflowName)) {
            $this->notifyModelOwner($model, $notificationData);
        }

        // Notify staff/reviewers using legacy method (for backward compatibility)
        if (NotificationConfig::shouldNotifyStaff($transition, $workflowName)) {
            $this->notifyStaff($notificationData, $workflowName);
        }

        // Process all other role type notifications
        foreach ($recipients as $recipient) {
            // Skip standard recipients that are handled separately
            if (in_array($recipient, ['staff', 'reviewers', 'document_owner', 'model_owner'])) {
                continue;
            }

            // Handle any role-based recipient dynamically
            if (NotificationConfig::shouldNotifyRoleType($recipient, $transition, $workflowName)) {
                $this->notifyRoleType($recipient, $notificationData, $workflowName);
            }
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
        // Map transitions to email classes based on transition type
        $workflowName = $notificationData['workflow_name'] ?? null;

        // Get a better email mapping based on the workflow config and transition purpose
        $emailClass = $this->determineEmailClassForTransition($transition, $workflowName);

        if ($emailClass && class_exists($emailClass)) {
            $this->sendEmail($document, $emailClass, $notificationData);
        } else {
            $this->logUnhandledTransition($transition, $notificationData);
        }
    }

    /**
     * Determine which email class to use based on transition characteristics
     */
    private function determineEmailClassForTransition(int $transition, ?string $workflowName): ?string
    {
        // First check for a configured email class mapping
        $configuredClass = NotificationConfig::getEmailClassForTransition($transition, $workflowName);
        if ($configuredClass && class_exists($configuredClass)) {
            return $configuredClass;
        }

        // If we're using a workflow that doesn't have email_class_mapping configuration
        if ($workflowName === 'verification_by_management') {
            $recipientTypes = NotificationConfig::getNotificationRecipients($transition, $workflowName);

            // For transitions notifying approvers at any level, use DocumentSubmittedMail
            if (in_array('reviewers', $recipientTypes) ||
                in_array('level_one_approvers', $recipientTypes) ||
                in_array('level_two_approvers', $recipientTypes)) {

                // Special case for resubmission
                if ($transition === 8) {
                    return DocumentResubmittedMail::class;
                }

                return DocumentSubmittedMail::class;
            }

            // For transitions to document owner indicating approval
            if (in_array('document_owner', $recipientTypes) && in_array($transition, [4])) {
                return DocumentVerifiedMail::class;
            }

            // For transitions to document owner indicating rejection
            if (in_array('document_owner', $recipientTypes) && in_array($transition, [5, 6, 7])) {
                return DocumentRejectedMail::class;
            }
        } else {
            // Legacy behavior for other workflows
            $legacyMap = [
                1 => DocumentSubmittedMail::class,    // SUBMIT
                2 => DocumentVerifiedMail::class,     // VERIFY
                3 => DocumentRejectedMail::class,     // REJECT
                4 => DocumentResubmittedMail::class,  // RESUBMIT
            ];

            return $legacyMap[$transition] ?? null;
        }

        return null;
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
        $uniqueId = uniqid('email_', true);

        Log::info('Sending workflow emails', [
            'unique_id' => $uniqueId,
            'mail_class' => $mailClass,
            'recipients_count' => count($recipients),
            'recipients' => $recipients,
            'workflow' => $notificationData['workflow_name'],
            'transition' => $notificationData['transition_id'],
            'model_id' => $notificationData['model_id'],
        ]);

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new $mailClass($model, $notificationData));

            Log::info('Email sent successfully', [
                'unique_id' => $uniqueId,
                'recipient' => $recipient,
                'mail_class' => $mailClass,
            ]);
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
        $workflowName = $notificationData['workflow_name'];
        $transition = $notificationData['transition_id'] ?? null;

        // Get all notification recipients for this transition
        $notifyRecipients = NotificationConfig::getNotificationRecipients($transition, $workflowName);

        // Add model owner if transition should notify document owner
        if (NotificationConfig::shouldNotifyModelOwner($transition, $workflowName)) {
            $owner = $this->getModelOwner($model);
            if ($owner && $owner->email) {
                $recipients[] = $owner->email;
            }
        }

        // Add staff emails only if transition should notify staff (backward compatibility)
        if (NotificationConfig::shouldNotifyStaff($transition, $workflowName)) {
            $staffUsers = $this->getStaffUsers($workflowName);
            foreach ($staffUsers as $staffUser) {
                if ($staffUser->email) {
                    $recipients[] = $staffUser->email;
                }
            }
        }

        // Process all other role type notifications
        foreach ($notifyRecipients as $recipient) {
            // Skip standard recipients that are handled separately
            if (in_array($recipient, ['staff', 'reviewers', 'document_owner', 'model_owner'])) {
                continue;
            }

            // Get users for this role type
            $roleUsers = $this->getUsersByRoleType($recipient, $workflowName);
            foreach ($roleUsers as $roleUser) {
                if ($roleUser->email) {
                    $recipients[] = $roleUser->email;
                }
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
            // Determine specific notification type based on workflow and transition
            $specificType = $this->determineNotificationType($data);
            
            // Use Laravel's notification system
            $user->notify(new \App\Notifications\WorkflowNotification($specificType, $data));

            Log::info('In-app notification sent', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'type' => $specificType,
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
     * Determine specific notification type based on workflow and transition
     */
    private function determineNotificationType(array $data): string
    {
        $workflowName = $data['workflow_name'] ?? '';
        $transition = $data['transition_id'] ?? null;
        
        // Document workflow notifications
        if (in_array($workflowName, ['verification_by_staff', 'verification_by_management'])) {
            return match($transition) {
                1 => 'document_submitted',
                2 => 'document_approved',
                3 => 'document_rejected',
                4 => 'document_submitted', // resubmit
                default => 'workflow_updated'
            };
        }
        
        // Study calendar workflow notifications
        if ($workflowName === 'study_calendar') {
            return match($transition) {
                1 => 'workflow_action_required', // SUBMIT_STUDY
                2 => 'study_calendar_approved',
                3 => 'study_calendar_rejected',
                4 => 'workflow_action_required', // RESUBMIT_STUDY
                5 => 'study_started',
                6 => 'workflow_updated', // TAKE_LEAVE
                7 => 'workflow_updated', // RETURN_FROM_LEAVE
                8 => 'study_completed',
                9, 10 => 'workflow_updated', // DROP_OUT
                default => 'workflow_updated'
            };
        }
        
        return 'workflow_updated';
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

    /**
     * Get users by role type
     */
    private function getUsersByRoleType(string $roleType, string $workflowName): Collection
    {
        // Handle direct role names (for backward compatibility and direct role mapping)
        $roles = NotificationConfig::getRolesByType($roleType, $workflowName);

        if (empty($roles)) {
            // If no configured roles, treat the roleType as a direct role name
            $roles = [$roleType];
        }

        return User::whereHas('employee', function ($query) use ($roles) {
            $query->whereIn('role', $roles);
        })->get();
    }

    /**
     * Notify users with a specific role type
     */
    private function notifyRoleType(string $roleType, array $notificationData, string $workflowName): void
    {
        $users = $this->getUsersByRoleType($roleType, $workflowName);
        $uniqueId = uniqid('notify_role_', true);

        Log::info('Notifying role type', [
            'unique_id' => $uniqueId,
            'role_type' => $roleType,
            'workflow' => $workflowName,
            'users_count' => $users->count(),
        ]);

        foreach ($users as $user) {
            $this->sendNotificationToUser(
                $user,
                'workflow_action_required',
                $notificationData
            );
        }
    }
}
