<?php

use Illuminate\Support\Facades\Schedule;

// Academic Document Reminders
Schedule::command('reminders:academic-documents --type=semester')
    ->weekly()
    ->mondays()
    ->at('09:00')
    ->description('Send semester report reminders to lecturers');

Schedule::command('reminders:academic-documents --type=final')
    ->weekly()
    ->mondays()
    ->at('10:00')
    ->description('Send final report reminders to lecturers approaching completion');

// Overdue reminders (more frequent)
Schedule::command('reminders:academic-documents --type=semester')
    ->daily()
    ->at('14:00')
    ->when(function () {
        // Only send overdue reminders on weekdays
        return now()->isWeekday();
    })
    ->description('Send overdue semester report reminders');

