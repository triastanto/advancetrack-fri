<?php

namespace App\Services\Workflow;

class NotificationConfig
{
    // Workflow transition constants
    public const TRANSITION_SUBMIT = 1;
    public const TRANSITION_VERIFY = 2;
    public const TRANSITION_REJECT = 3;
    public const TRANSITION_RESUBMIT = 4;

    // Staff roles that can perform verification
    public const VERIFICATION_STAFF_ROLES = ['fsdp_staff', 'head_of_affairs', 'vice_dean'];

    // Email notification configuration
    public const EMAIL_ENABLED_TRANSITIONS = [
        self::TRANSITION_SUBMIT,
        self::TRANSITION_VERIFY,
        self::TRANSITION_REJECT,
        self::TRANSITION_RESUBMIT,
    ];

    // In-app notification configuration
    public const NOTIFY_DOCUMENT_OWNER_TRANSITIONS = [
        self::TRANSITION_VERIFY,
        self::TRANSITION_REJECT,
    ];

    public const NOTIFY_VERIFICATION_STAFF_TRANSITIONS = [
        self::TRANSITION_SUBMIT,
        self::TRANSITION_RESUBMIT,
    ];

    /**
     * Get staff roles from configuration with fallback
     */
    public static function getStaffRoles(): array
    {
        return config(
            'workflows.workflows.document_verification.notifications.staff_roles',
            self::VERIFICATION_STAFF_ROLES
        );
    }

    /**
     * Check if email should be sent for transition
     */
    public static function shouldSendEmail(int $transition): bool
    {
        return in_array($transition, self::EMAIL_ENABLED_TRANSITIONS);
    }

    /**
     * Check if document owner should be notified for transition
     */
    public static function shouldNotifyDocumentOwner(int $transition): bool
    {
        return in_array($transition, self::NOTIFY_DOCUMENT_OWNER_TRANSITIONS);
    }

    /**
     * Check if verification staff should be notified for transition
     */
    public static function shouldNotifyVerificationStaff(int $transition): bool
    {
        return in_array($transition, self::NOTIFY_VERIFICATION_STAFF_TRANSITIONS);
    }

    /**
     * Get transition name for logging
     */
    public static function getTransitionName(int $transition): string
    {
        return match ($transition) {
            self::TRANSITION_SUBMIT => 'SUBMIT',
            self::TRANSITION_VERIFY => 'VERIFY',
            self::TRANSITION_REJECT => 'REJECT',
            self::TRANSITION_RESUBMIT => 'RESUBMIT',
            default => 'UNKNOWN',
        };
    }

    /**
     * Get email queue name for transition (for future queue implementation)
     */
    public static function getEmailQueue(int $transition): string
    {
        return match ($transition) {
            self::TRANSITION_SUBMIT, self::TRANSITION_RESUBMIT => 'notifications-staff',
            self::TRANSITION_VERIFY, self::TRANSITION_REJECT => 'notifications-users',
            default => 'notifications-default',
        };
    }
}
