<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Academic Document Reminder Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for academic document reminder system
    |
    */

    'academic_documents' => [
        /*
        |--------------------------------------------------------------------------
        | Semester Report Reminders
        |--------------------------------------------------------------------------
        */
        'semester_reports' => [
            'enabled' => env('SEMESTER_REMINDERS_ENABLED', true),
            'schedule' => [
                'first_reminder' => 7, // days before semester end
                'second_reminder' => 3, // days before semester end
                'final_reminder' => 1, // day before semester end
                'overdue_alert' => 7, // days after semester end
            ],
            'semester_dates' => [
                1 => [ // First semester
                    'start' => '08-01', // August 1st
                    'end' => '12-31',   // December 31st
                ],
                2 => [ // Second semester
                    'start' => '01-01', // January 1st
                    'end' => '06-30',   // June 30th
                ],
                3 => [ // Summer semester (if applicable)
                    'start' => '07-01', // July 1st
                    'end' => '07-31',   // July 31st
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Final Report Reminders
        |--------------------------------------------------------------------------
        */
        'final_reports' => [
            'enabled' => env('FINAL_REMINDERS_ENABLED', true),
            'schedule' => [
                'initial_reminder' => 90, // days before completion
                'progress_reminder' => 30, // days before completion
                'final_reminder' => 14, // days before completion
                'overdue_alert' => 7, // days after completion
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Notification Settings
        |--------------------------------------------------------------------------
        */
        'notifications' => [
            'channels' => ['database', 'mail'],
            'email_templates' => [
                'semester_reminder' => 'emails.academic-document-reminder',
                'final_reminder' => 'emails.final-report-reminder',
            ],
            'in_app' => [
                'enabled' => true,
                'priority_colors' => [
                    'low' => 'info',
                    'medium' => 'warning',
                    'high' => 'danger',
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Dashboard Integration
        |--------------------------------------------------------------------------
        */
        'dashboard' => [
            'show_reminders' => true,
            'max_reminders' => 5,
            'refresh_interval' => 300, // 5 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reminder Frequency Settings
    |--------------------------------------------------------------------------
    */
    'frequency' => [
        'semester_reminders' => [
            'weekly' => true,
            'daily_overdue' => true,
        ],
        'final_reminders' => [
            'weekly' => true,
            'daily_overdue' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours Settings
    |--------------------------------------------------------------------------
    */
    'business_hours' => [
        'timezone' => 'Asia/Jakarta',
        'start_time' => '08:00',
        'end_time' => '17:00',
        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
    ],
]; 