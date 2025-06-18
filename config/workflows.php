<?php

return [
    /*
    |--------------------------------------------------------------------------
    | States Definition
    |--------------------------------------------------------------------------
    */
    'states' => [
        1 => [
            'name' => 'DRAFT',
            'label' => 'Draft',
            'color' => 'secondary',
            'icon' => 'edit',
            'is_terminal' => false,
            'is_initial' => true,
        ],
        2 => [
            'name' => 'PENDING',
            'label' => 'Awaiting Verification',
            'color' => 'warning',
            'icon' => 'clock',
            'is_terminal' => false,
            'is_initial' => false,
        ],
        3 => [
            'name' => 'VERIFIED',
            'label' => 'Verified',
            'color' => 'success',
            'icon' => 'check-circle',
            'is_terminal' => true,
            'is_initial' => false,
        ],
        4 => [
            'name' => 'REJECTED',
            'label' => 'Rejected',
            'color' => 'danger',
            'icon' => 'x-circle',
            'is_terminal' => false,
            'is_initial' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transitions Definition
    |--------------------------------------------------------------------------
    */
    'transitions' => [
        1 => [
            'name' => 'SUBMIT',
            'label' => 'Submit for Verification',
            'from_state' => 1, // DRAFT
            'to_state' => 2,   // PENDING
            'icon' => 'upload',
            'color' => 'primary',
            'required_roles' => ['lecturer'], // Only lecturers submit documents
            'requires_comment' => false,
        ],
        2 => [
            'name' => 'VERIFY',
            'label' => 'Verify Document',
            'from_state' => 2, // PENDING
            'to_state' => 3,   // VERIFIED
            'icon' => 'check-circle',
            'color' => 'success',
            'required_roles' => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean', 'head_of_study_program', 'head_of_research_group'], // Staff can verify
            'requires_comment' => true,
        ],
        3 => [
            'name' => 'REJECT',
            'label' => 'Reject Document',
            'from_state' => 2, // PENDING
            'to_state' => 4,   // REJECTED
            'icon' => 'x-circle',
            'color' => 'danger',
            'required_roles' => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean', 'head_of_study_program', 'head_of_research_group'], // Staff can reject
            'requires_comment' => true,
        ],
        4 => [
            'name' => 'RESUBMIT',
            'label' => 'Revise and Resubmit',
            'from_state' => 4, // REJECTED
            'to_state' => 2,   // PENDING
            'icon' => 'refresh-cw',
            'color' => 'primary',
            'required_roles' => ['lecturer'], // Only lecturers can resubmit
            'requires_comment' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Workflow Definitions
    |--------------------------------------------------------------------------
    */
    'workflows' => [
        'document_verification' => [
            'name' => 'Document Verification Workflow',
            'description' => 'Document verification process with draft, pending, verified, and rejected states',
            'initial_state' => 1, // DRAFT
            'settings' => [
                'track_history' => true,
                'auto_save' => true,
                'strict_mode' => true,
                'auto_notify' => true,
            ],
            'guards' => [
                'role_based' => ['enabled' => true], // Use RoleBasedWorkflowGuard (recommended)
                'employee_role' => ['enabled' => false], // Legacy employee-based roles
                'time_based' => [
                    'enabled' => false,
                    'business_hours_only' => [2, 3], // VERIFY, REJECT
                    'minimum_time_in_state' => [
                        2 => 15, // 15 minutes in PENDING
                    ],
                    'cooldown_periods' => [
                        3 => 60, // 1 hour after REJECT
                    ],
                ],
            ],
            'notifications' => [
                'channels' => ['mail', 'database'],
                'events' => [
                    1 => ['staff'], // SUBMIT - notify verification staff
                    2 => ['document_owner'], // VERIFY - notify document owner
                    3 => ['document_owner'], // REJECT - notify document owner
                    4 => ['staff'], // RESUBMIT - notify verification staff
                ],
                'staff_roles' => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean', 'head_of_study_program', 'head_of_research_group'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours Configuration
    |--------------------------------------------------------------------------
    */
    'business_hours' => [
        'timezone' => 'America/New_York',
        'days' => [
            'monday' => ['09:00', '17:00'],
            'tuesday' => ['09:00', '17:00'],
            'wednesday' => ['09:00', '17:00'],
            'thursday' => ['09:00', '17:00'],
            'friday' => ['09:00', '17:00'],
            'saturday' => null,
            'sunday' => null,
        ],
        'holidays' => ['2024-01-01', '2024-07-04', '2024-12-25'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Settings
    |--------------------------------------------------------------------------
    */
    'global' => [
        'default_timeout_hours' => 24,
        'enable_notifications' => true,
        'log_all_transitions' => true,
        'cache_workflow_instances' => true,
        'max_history_entries' => 1000,
    ],
];
