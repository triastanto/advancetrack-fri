<?php

namespace App\Services\Workflow;

class NotificationConfig
{
    /**
     * Get notification configuration for a specific workflow
     */
    public static function getWorkflowNotificationConfig(string $workflowName): array
    {
        return config("workflows.workflows.{$workflowName}.notifications", []);
    }

    /**
     * Get staff roles from configuration for a specific workflow
     */
    public static function getStaffRoles(string $workflowName): array
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return $config['staff_roles'] ?? $config['reviewer_roles'] ?? [];
    }

    /**
     * Check if email should be sent for transition in a specific workflow
     */
    public static function shouldSendEmail(int $transition, string $workflowName): bool
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        $channels = $config['channels'] ?? [];
        return in_array('mail', $channels);
    }

    /**
     * Check if a specific event should trigger notifications
     */
    public static function shouldNotifyForEvent(int $transition, string $eventType, string $workflowName): bool
    {
        $events = self::getNotificationEvents($workflowName);
        return in_array($eventType, $events[$transition] ?? []);
    }

    /**
     * Get notification channels for a workflow
     */
    public static function getNotificationChannels(string $workflowName): array
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return $config['channels'] ?? ['database'];
    }

    /**
     * Get notification events configuration for a workflow
     */
    public static function getNotificationEvents(string $workflowName): array
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return $config['events'] ?? [];
    }

    /**
     * Get all notification recipients for a transition
     */
    public static function getNotificationRecipients(int $transition, string $workflowName): array
    {
        $events = self::getNotificationEvents($workflowName);
        return $events[$transition] ?? [];
    }

    /**
     * Check if model owner should be notified for transition
     */
    public static function shouldNotifyModelOwner(int $transition, string $workflowName): bool
    {
        return self::shouldNotifyForEvent($transition, 'document_owner', $workflowName) ||
               self::shouldNotifyForEvent($transition, 'model_owner', $workflowName);
    }

    /**
     * Check if staff/reviewers should be notified for transition
     */
    public static function shouldNotifyStaff(int $transition, string $workflowName): bool
    {
        return self::shouldNotifyForEvent($transition, 'staff', $workflowName) ||
               self::shouldNotifyForEvent($transition, 'reviewers', $workflowName);
    }

    /**
     * Get workflow-specific notification settings
     */
    public static function getWorkflowSettings(string $workflowName): array
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return [
            'channels' => $config['channels'] ?? ['database'],
            'auto_notify' => $config['auto_notify'] ?? true,
            'email_templates' => $config['email_templates'] ?? [],
            'notification_types' => $config['notification_types'] ?? ['in_app', 'email'],
        ];
    }

    /**
     * Check if workflow has auto-notification enabled
     */
    public static function isAutoNotifyEnabled(string $workflowName): bool
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return $config['auto_notify'] ?? true;
    }

    /**
     * Get email template for a specific transition
     */
    public static function getEmailTemplate(int $transition, string $workflowName): ?string
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        $templates = $config['email_templates'] ?? [];
        return $templates[$transition] ?? null;
    }

    /**
     * Get roles for a specific role type from workflow configuration
     */
    public static function getRolesByType(string $roleType, string $workflowName): array
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        $key = "{$roleType}_roles";
        return $config[$key] ?? [];
    }

    /**
     * Check if a specific role type should be notified for a transition
     */
    public static function shouldNotifyRoleType(string $roleType, int $transition, string $workflowName): bool
    {
        return self::shouldNotifyForEvent($transition, $roleType, $workflowName);
    }

    /**
     * Get the email class mapping for a transition
     *
     * @param int $transition The transition ID
     * @param string $workflowName The workflow name
     * @return string|null The email class name
     */
    public static function getEmailClassForTransition(int $transition, string $workflowName): ?string
    {
        $config = self::getWorkflowNotificationConfig($workflowName);
        return $config['email_class_mapping'][$transition] ?? null;
    }
}
