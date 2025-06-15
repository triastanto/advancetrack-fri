<?php

namespace App\Listeners;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Models\User;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Support\Facades\Log;

class NotifyStakeholders
{
    /**
     * Handle the event.
     */
    public function handle(WorkflowTransitionApplied $event): void
    {
        $model = $event->model;
        $fromState = $event->fromState;
        $toState = $event->toState;
        $transition = $event->transition;
        $context = $event->context;

        Log::info('Notifying stakeholders after workflow transition', [
            'model' => get_class($model),
            'id' => $model->id,
            'from' => WorkflowDefinition::getStateLabel($fromState),
            'to' => WorkflowDefinition::getStateLabel($toState),
            'transition' => WorkflowDefinition::getTransitionLabel($transition),
        ]);

        // Handle Document-specific notifications
        if ($model instanceof \App\Models\Document) {
            $this->handleDocumentNotifications($model, $fromState, $toState, $transition, $context);
        }
    }

    /**
     * Handle document-specific notifications
     */
    protected function handleDocumentNotifications($document, $fromState, $toState, $transition, $context): void
    {
        $notificationData = [
            'document_id' => $document->id,
            'document_name' => $document->file_name,
            'document_type' => $document->documentType->display_name ?? 'Unknown',
            'from_state' => WorkflowDefinition::getStateLabel($fromState),
            'to_state' => WorkflowDefinition::getStateLabel($toState),
            'transition' => WorkflowDefinition::getTransitionLabel($transition),
            'comment' => $context['comment'] ?? null,
            'user_name' => $context['user_name'] ?? 'System',
            'timestamp' => now(),
        ];

        // Notify document owner (employee/lecturer)
        if ($document->employee && $document->employee->user) {
            $this->sendNotificationToUser(
                $document->employee->user,
                'document_workflow_updated',
                $notificationData
            );
        }

        // Notify managers/supervisors based on transition
        $this->notifyManagers($transition, $notificationData);

        // Send email notifications for critical transitions
        if (in_array($transition, [1, 2, 5])) { // APPROVE, REJECT, RESUBMIT_REJECTED
            $this->sendEmailNotification($document, $notificationData);
        }
    }

    /**
     * Notify managers based on transition type
     */
    protected function notifyManagers($transition, $notificationData): void
    {
        $transitionConfig = WorkflowDefinition::getTransition($transition);
        $requiredRoles = $transitionConfig['required_roles'] ?? [];

        // Use the actual employee roles from the workflow configuration
        $employeeRoles = array_intersect($requiredRoles, ['fsdp_staff', 'head_of_affairs', 'vice_dean']);

        if (!empty($employeeRoles)) {
            $managers = User::whereHas('employee', function ($query) use ($employeeRoles) {
                $query->whereIn('role', $employeeRoles);
            })->get();

            foreach ($managers as $manager) {
                $this->sendNotificationToUser(
                    $manager,
                    'document_workflow_action_required',
                    $notificationData
                );
            }
        }
    }

    /**
     * Send notification to a specific user
     */
    protected function sendNotificationToUser(User $user, string $type, array $data): void
    {
        try {
            // Here you would implement your notification system
            // For example, using Laravel's notification system:
            // $user->notify(new DocumentWorkflowNotification($type, $data));

            Log::info('Notification sent', [
                'user_id' => $user->id,
                'type' => $type,
                'document_id' => $data['document_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email notification for critical transitions
     */
    protected function sendEmailNotification($document, $notificationData): void
    {
        try {
            // Implement email notification logic here
            // Example: Mail::to($document->employee->user->email)->send(new DocumentStatusMail($notificationData));

            Log::info('Email notification would be sent', [
                'document_id' => $document->id,
                'recipient' => $document->employee->user->email ?? 'unknown',
                'transition' => $notificationData['transition'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
