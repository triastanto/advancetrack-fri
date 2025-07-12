<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global Settings
    |--------------------------------------------------------------------------
    */
    'global' => [
        'default_timeout_hours' => 24,
        'enable_notifications' => true,
        'log_all_transitions' => true,
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
    | Workflow Definitions
    |--------------------------------------------------------------------------
    */
    'workflows' => [
        'verification_by_staff' => [
            'name' => 'Document Verification Workflow',
            'description' => 'Document verification process with draft, pending, verified, and rejected states',
            'initial_state' => 1, // DRAFT
            'settings' => [
                'track_history' => true,
                'auto_save' => true,
                'strict_mode' => true,
                'auto_notify' => true,
            ],
            'states' => [
                1 => [
                    'name' => 'DRAFT',
                    'type' => 'draft',
                    'label' => 'Draft',
                    'color' => 'secondary',
                    'icon' => 'edit',
                    'is_terminal' => false,
                    'is_initial' => true,
                ],
                2 => [
                    'name' => 'PENDING',
                    'type' => 'pending',
                    'label' => 'Awaiting Verification',
                    'color' => 'warning',
                    'icon' => 'clock',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                3 => [
                    'name' => 'VERIFIED',
                    'type' => 'verified',
                    'label' => 'Verified',
                    'color' => 'success',
                    'icon' => 'check-circle',
                    'is_terminal' => true,
                    'is_initial' => false,
                ],
                4 => [
                    'name' => 'REJECTED',
                    'type' => 'rejected',
                    'label' => 'Rejected',
                    'color' => 'danger',
                    'icon' => 'x-circle',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
            ],
            'transitions' => [
                1 => [
                    'name' => 'SUBMIT',
                    'label' => 'Submit for Verification',
                    'from_state' => 1, // DRAFT
                    'to_state' => 2,   // PENDING
                    'icon' => 'upload',
                    'color' => 'primary',
                    'required_roles' => ['lecturer'],
                    'requires_comment' => false,
                ],
                2 => [
                    'name' => 'VERIFY',
                    'label' => 'Verify Document',
                    'from_state' => 2, // PENDING
                    'to_state' => 3,   // VERIFIED
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'required_roles' => ['hr_finance_staff'],
                    'requires_comment' => true,
                ],
                3 => [
                    'name' => 'REJECT',
                    'label' => 'Reject Document',
                    'from_state' => 2, // PENDING
                    'to_state' => 4,   // REJECTED
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['hr_finance_staff'],
                    'requires_comment' => true,
                ],
                4 => [
                    'name' => 'RESUBMIT',
                    'label' => 'Revise and Resubmit',
                    'from_state' => 4, // REJECTED
                    'to_state' => 2,   // PENDING
                    'icon' => 'refresh-cw',
                    'color' => 'primary',
                    'required_roles' => ['lecturer'],
                    'requires_comment' => false,
                ],
            ],
            'guards' => [
                'role_based' => ['enabled' => true],
                'time_based' => [
                    'enabled' => false,
                    'business_hours_only' => [2, 3],
                    'minimum_time_in_state' => [
                        2 => 15,
                    ],
                    'cooldown_periods' => [
                        3 => 60,
                    ],
                ],
            ],
            'notifications' => [
                'channels' => ['mail', 'database'],
                'auto_notify' => true,
                'notification_types' => ['in_app', 'email'],
                'events' => [
                    1 => ['staff'], // SUBMIT - notify verification staff
                    2 => ['document_owner'], // VERIFY - notify document owner
                    3 => ['document_owner'], // REJECT - notify document owner
                    4 => ['staff'], // RESUBMIT - notify verification staff
                ],
                'staff_roles' => ['hr_finance_staff'],
                'email_templates' => [
                    1 => 'emails.document-submitted',
                    2 => 'emails.document-verified',
                    3 => 'emails.document-rejected',
                    4 => 'emails.document-resubmitted',
                ],
            ],
        ],

        'verification_by_management' => [
            'name' => 'Management Multi-Level Approval Workflow',
            'description' => 'Document approval process with multiple approval levels',
            'initial_state' => 1, // DRAFT
            'settings' => [
                'track_history' => true,
                'auto_save' => true,
                'strict_mode' => true,
                'auto_notify' => true,
            ],
            'states' => [
                1 => [
                    'name' => 'DRAFT',
                    'type' => 'draft',
                    'label' => 'Initial document draft',
                    'color' => 'secondary',
                    'icon' => 'edit',
                    'is_terminal' => false,
                    'is_initial' => true,
                ],
                2 => [
                    'name' => 'PENDING_L1',
                    'type' => 'pending_l1',
                    'label' => 'Pending first level approval',
                    'color' => 'info',
                    'icon' => 'user-check',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                3 => [
                    'name' => 'PENDING_L2',
                    'type' => 'pending_l2',
                    'label' => 'Pending second level approval',
                    'color' => 'primary',
                    'icon' => 'shield',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                4 => [
                    'name' => 'APPROVED',
                    'type' => 'approved',
                    'label' => 'Final approved state',
                    'color' => 'success',
                    'icon' => 'check-circle',
                    'is_terminal' => true,
                    'is_initial' => false,
                ],
                5 => [
                    'name' => 'REJECTED',
                    'type' => 'rejected',
                    'label' => 'Rejected document',
                    'color' => 'danger',
                    'icon' => 'x-circle',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
            ],
            'transitions' => [
                1 => [
                    'name' => 'SUBMIT',
                    'label' => 'Submit for Level 1 Approval',
                    'from_state' => 1, // DRAFT
                    'to_state' => 2,   // PENDING_L1
                    'icon' => 'upload',
                    'color' => 'primary',
                    'required_roles' => ['hr_finance_staff'],
                    'requires_comment' => false,
                ],
                2 => [
                    'name' => 'APPROVE_L1',
                    'label' => 'Approve (Level 1)',
                    'from_state' => 2, // PENDING_L1
                    'to_state' => 3,   // PENDING_L2
                    'icon' => 'thumbs-up',
                    'color' => 'success',
                    'required_roles' => ['head_of_study_program'],
                    'requires_comment' => true,
                ],
                3 => [
                    'name' => 'APPROVE_L2',
                    'label' => 'Approve (Level 2)',
                    'from_state' => 3, // PENDING_L2
                    'to_state' => 4,   // APPROVED
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'required_roles' => ['head_of_research_group'],
                    'requires_comment' => true,
                ],
                4 => [
                    'name' => 'REJECT_L1',
                    'label' => 'Reject Document (Level 1)',
                    'from_state' => 2, // PENDING_L1
                    'to_state' => 5,   // REJECTED
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['head_of_study_program'],
                    'requires_comment' => true,
                ],
                5 => [
                    'name' => 'REJECT_L2',
                    'label' => 'Reject Document (Level 2)',
                    'from_state' => 3, // PENDING_L2
                    'to_state' => 5,   // REJECTED
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['head_of_research_group'],
                    'requires_comment' => true,
                ],
                6 => [
                    'name' => 'REVISE',
                    'label' => 'Revise Document',
                    'from_state' => 5, // REJECTED
                    'to_state' => 1,   // DRAFT
                    'icon' => 'refresh-cw',
                    'color' => 'primary',
                    'required_roles' => ['hr_finance_staff'],
                    'requires_comment' => false,
                ],
            ],
            'guards' => [
                'role_based' => ['enabled' => true],
                'time_based' => [
                    'enabled' => false,
                ],
            ],
            'notifications' => [
                'channels' => ['mail', 'database'],
                'auto_notify' => true,
                'notification_types' => ['in_app', 'email'],
                'events' => [
                    1 => ['level_one_approvers'], // SUBMIT - notify level one approvers
                    2 => ['level_two_approvers'], // APPROVE_L1 - notify level two approvers
                    3 => ['document_owner'],      // APPROVE_L2 - notify document owner of final approval
                    4 => ['document_owner'],      // REJECT_L1 - notify document owner of rejection
                    5 => ['document_owner'],      // REJECT_L2 - notify document owner of rejection
                    6 => ['reviewers'],           // REVISE - no email sent
                ],
                'reviewers_roles' => ['hr_finance_staff'],
                'level_one_approvers_roles' => ['head_of_study_program'],
                'level_two_approvers_roles' => ['head_of_research_group'],
                'email_templates' => [
                    1 => 'emails.document-sent-for-level-one-review',
                    2 => 'emails.document-sent-for-level-two-review',
                    3 => 'emails.document-fully-approved',
                    4 => 'emails.document-rejected',
                    5 => 'emails.document-rejected',
                    6 => 'emails.document-resubmitted',
                ],
                'email_class_mapping' => [
                    1 => 'App\\Mail\\DocumentSubmittedMail',     // SUBMIT - to level one approval
                    2 => 'App\\Mail\\DocumentSubmittedMail',     // APPROVE_L1 - to level two approval
                    3 => 'App\\Mail\\DocumentVerifiedMail',      // APPROVE_L2 - final approval
                    4 => 'App\\Mail\\DocumentRejectedMail',      // REJECT_L1 - document rejection
                    5 => 'App\\Mail\\DocumentRejectedMail',      // REJECT_L2 - document rejection
                    6 => 'App\\Mail\\DocumentResubmittedMail',   // REVISE - document revision
                ],
            ],
        ],

        'study_calendar' => [
            'name' => 'Study Calendar Workflow',
            'description' => 'Study calendar submission, approval, and status management for tracking student progress',
            'initial_state' => 1, // DRAFT
            'settings' => [
                'track_history' => true,
                'auto_save' => true,
                'strict_mode' => true,
                'auto_notify' => true,
            ],
            'states' => [
                1 => [
                    'name' => 'DRAFT',
                    'type' => 'draft',
                    'label' => 'Draft Calendar',
                    'color' => 'secondary',
                    'icon' => 'edit',
                    'is_terminal' => false,
                    'is_initial' => true,
                ],
                2 => [
                    'name' => 'PENDING_APPROVAL',
                    'type' => 'pending',
                    'label' => 'Pending Approval',
                    'color' => 'warning',
                    'icon' => 'clock',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                3 => [
                    'name' => 'APPROVED',
                    'type' => 'approved',
                    'label' => 'Calendar Approved',
                    'color' => 'info',
                    'icon' => 'check-circle',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                4 => [
                    'name' => 'REJECTED',
                    'type' => 'rejected',
                    'label' => 'Calendar Rejected',
                    'color' => 'danger',
                    'icon' => 'x-circle',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                5 => [
                    'name' => 'ACTIVE',
                    'type' => 'active',
                    'label' => 'Currently Studying',
                    'color' => 'success',
                    'icon' => 'book-open',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                6 => [
                    'name' => 'LEAVE',
                    'type' => 'leave',
                    'label' => 'On Official Leave',
                    'color' => 'warning',
                    'icon' => 'pause-circle',
                    'is_terminal' => false,
                    'is_initial' => false,
                ],
                7 => [
                    'name' => 'FINISHED',
                    'type' => 'finished',
                    'label' => 'Study Completed',
                    'color' => 'success',
                    'icon' => 'award',
                    'is_terminal' => true,
                    'is_initial' => false,
                ],
                8 => [
                    'name' => 'DROP_OUT',
                    'type' => 'drop_out',
                    'label' => 'Study Discontinued',
                    'color' => 'danger',
                    'icon' => 'x-circle',
                    'is_terminal' => true,
                    'is_initial' => false,
                ],
            ],
            'transitions' => [
                1 => [
                    'name' => 'SUBMIT_STUDY',
                    'label' => 'Submit Study Calendar for Approval',
                    'from_state' => 1, // DRAFT
                    'to_state' => 2,   // PENDING_APPROVAL
                    'icon' => 'upload',
                    'color' => 'primary',
                    'required_roles' => ['lecturer'],
                    'requires_comment' => false,
                ],
                2 => [
                    'name' => 'APPROVE_STUDY',
                    'label' => 'Approve Study Calendar',
                    'from_state' => 2, // PENDING_APPROVAL
                    'to_state' => 3,   // APPROVED
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'required_roles' => ['head_of_study_program', 'fri_vice_dean', 'hr_finance_staff'],
                    'requires_comment' => true,
                ],
                3 => [
                    'name' => 'REJECT_STUDY',
                    'label' => 'Reject Study Calendar',
                    'from_state' => 2, // PENDING_APPROVAL
                    'to_state' => 4,   // REJECTED
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['head_of_study_program', 'fri_vice_dean', 'hr_finance_staff'],
                    'requires_comment' => true,
                ],
                4 => [
                    'name' => 'RESUBMIT_STUDY',
                    'label' => 'Revise and Resubmit Study Calendar',
                    'from_state' => 4, // REJECTED
                    'to_state' => 2,   // PENDING_APPROVAL
                    'icon' => 'refresh-cw',
                    'color' => 'primary',
                    'required_roles' => ['lecturer'],
                    'requires_comment' => false,
                ],
                5 => [
                    'name' => 'START_STUDY',
                    'label' => 'Begin Study Program',
                    'from_state' => 3, // APPROVED
                    'to_state' => 5,   // ACTIVE
                    'icon' => 'play-circle',
                    'color' => 'success',
                    'required_roles' => ['lecturer', 'head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
                6 => [
                    'name' => 'TAKE_LEAVE',
                    'label' => 'Take Official Leave',
                    'from_state' => 5, // ACTIVE
                    'to_state' => 6,   // LEAVE
                    'icon' => 'pause-circle',
                    'color' => 'warning',
                    'required_roles' => ['lecturer', 'head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
                7 => [
                    'name' => 'RETURN_FROM_LEAVE',
                    'label' => 'Return from Leave',
                    'from_state' => 6, // LEAVE
                    'to_state' => 5,   // ACTIVE
                    'icon' => 'play-circle',
                    'color' => 'success',
                    'required_roles' => ['lecturer', 'head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
                8 => [
                    'name' => 'COMPLETE_STUDY',
                    'label' => 'Complete Study',
                    'from_state' => 5, // ACTIVE
                    'to_state' => 7,   // FINISHED
                    'icon' => 'award',
                    'color' => 'success',
                    'required_roles' => ['head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
                9 => [
                    'name' => 'DROP_OUT_ACTIVE',
                    'label' => 'Discontinue Study',
                    'from_state' => 5, // ACTIVE
                    'to_state' => 8,   // DROP_OUT
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['lecturer', 'head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
                10 => [
                    'name' => 'DROP_OUT_LEAVE',
                    'label' => 'Discontinue Study (from Leave)',
                    'from_state' => 6, // LEAVE
                    'to_state' => 8,   // DROP_OUT
                    'icon' => 'x-circle',
                    'color' => 'danger',
                    'required_roles' => ['head_of_study_program', 'fri_vice_dean'],
                    'requires_comment' => true,
                ],
            ],
            'guards' => [
                'role_based' => ['enabled' => true],
                'time_based' => [
                    'enabled' => false,
                    'business_hours_only' => [],
                    'minimum_time_in_state' => [],
                ],
            ],
            'notifications' => [
                'channels' => ['mail', 'database'],
                'auto_notify' => true,
                'notification_types' => ['in_app', 'email'],
                'events' => [
                    1 => ['head_of_study_program', 'fri_vice_dean', 'hr_finance_staff'], // SUBMIT_STUDY - notify approvers
                    2 => ['lecturer'], // APPROVE_STUDY - notify student
                    3 => ['lecturer'], // REJECT_STUDY - notify student
                    4 => ['head_of_study_program', 'fri_vice_dean', 'hr_finance_staff'], // RESUBMIT_STUDY - notify approvers
                    5 => ['lecturer', 'head_of_study_program', 'fri_vice_dean', 'head_of_hr_finance', 'hr_finance_staff'], // START_STUDY - notify student, supervisors, and admin
                    6 => ['lecturer', 'head_of_study_program', 'fri_vice_dean'], // TAKE_LEAVE - notify student and supervisors
                    7 => ['lecturer', 'head_of_study_program', 'fri_vice_dean'], // RETURN_FROM_LEAVE - notify student and supervisors
                    8 => ['lecturer', 'head_of_study_program', 'fri_vice_dean', 'head_of_hr_finance', 'hr_finance_staff'], // COMPLETE_STUDY - notify student, supervisors, and admin
                    9 => ['lecturer', 'head_of_study_program', 'fri_vice_dean', 'head_of_hr_finance', 'hr_finance_staff'], // DROP_OUT_ACTIVE - notify student, supervisors, and admin
                    10 => ['lecturer', 'head_of_study_program', 'fri_vice_dean', 'head_of_hr_finance', 'hr_finance_staff'], // DROP_OUT_LEAVE - notify student, supervisors, and admin
                ],
                'approver_roles' => ['head_of_study_program', 'fri_vice_dean', 'hr_finance_staff'],
                'supervisor_roles' => ['head_of_study_program', 'fri_vice_dean'],
                'admin_roles' => ['head_of_hr_finance', 'hr_finance_staff'],
                'email_templates' => [
                    1 => 'emails.study-submitted',
                    2 => 'emails.study-approved',
                    3 => 'emails.study-rejected',
                    4 => 'emails.study-resubmitted',
                    5 => 'emails.study-started',
                    6 => 'emails.study-leave-started',
                    7 => 'emails.study-leave-ended',
                    8 => 'emails.study-completed',
                    9 => 'emails.study-discontinued',
                    10 => 'emails.study-discontinued',
                ],
            ],
        ],
    ],
];
