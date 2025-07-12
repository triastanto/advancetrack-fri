<?php

namespace Tests\Feature\Notifications;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Listeners\NotifyStakeholders;
use App\Models\AcademicDocument;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_document_submission_sends_notification_to_staff()
    {
        // Create users
        $staff = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $staff->id,
            'role' => 'hr_finance_staff'
        ]);

        $documentOwner = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $documentOwner->id,
            'role' => 'lecturer'
        ]);

        // Create document
        $documentType = DocumentType::factory()->create();
        $document = AcademicDocument::create([
            'employee_id' => $documentOwner->employee->id,
            'document_type_id' => $documentType->id,
            'file_name' => 'test-document.pdf',
            'file_path' => '/uploads/test-document.pdf',
        ]);

        // Create workflow event (SUBMIT - transition 1)
        $event = new WorkflowTransitionApplied(
            $document,
            1, // from state (DRAFT)
            2, // to state (PENDING)
            1, // transition (SUBMIT)
            [
                'user_id' => $documentOwner->id,
                'user_name' => $documentOwner->name,
                'timestamp' => now(),
            ],
            'verification_by_staff'
        );

        // Handle the event
        $listener = new NotifyStakeholders();
        $listener->handle($event);

        // Assert notification was sent to staff
        Notification::assertSentTo(
            $staff,
            \App\Notifications\WorkflowNotification::class,
            function ($notification) {
                return $notification->type === 'document_submitted';
            }
        );
    }

    public function test_document_approval_sends_notification_to_owner()
    {
        // Create users
        $staff = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $staff->id,
            'role' => 'hr_finance_staff'
        ]);

        $documentOwner = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $documentOwner->id,
            'role' => 'lecturer'
        ]);

        // Create document
        $documentType = DocumentType::factory()->create();
        $document = AcademicDocument::create([
            'employee_id' => $documentOwner->employee->id,
            'document_type_id' => $documentType->id,
            'file_name' => 'test-document.pdf',
            'file_path' => '/uploads/test-document.pdf',
            'workflow_state' => 2, // PENDING
        ]);

        // Create workflow event (VERIFY - transition 2)
        $event = new WorkflowTransitionApplied(
            $document,
            2, // from state (PENDING)
            3, // to state (VERIFIED)
            2, // transition (VERIFY)
            [
                'user_id' => $staff->id,
                'user_name' => $staff->name,
                'timestamp' => now(),
                'comment' => 'Document looks good',
            ],
            'verification_by_staff'
        );

        // Handle the event
        $listener = new NotifyStakeholders();
        $listener->handle($event);

        // Assert notification was sent to document owner
        Notification::assertSentTo(
            $documentOwner,
            \App\Notifications\WorkflowNotification::class,
            function ($notification) {
                return $notification->type === 'document_approved';
            }
        );
    }

    public function test_study_calendar_approval_sends_notification_to_student()
    {
        // Create users
        $approver = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $approver->id,
            'role' => 'head_of_study_program'
        ]);

        $student = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $student->id,
            'role' => 'lecturer'
        ]);

        // Create study calendar (you'll need to adjust based on your actual model)
        $studyCalendar = \App\Models\StudyCalendar::create([
            'employee_id' => $student->employee->id,
            'workflow_state' => 2, // PENDING_APPROVAL
        ]);

        // Create workflow event (APPROVE_STUDY - transition 2)
        $event = new WorkflowTransitionApplied(
            $studyCalendar,
            2, // from state (PENDING_APPROVAL)
            3, // to state (APPROVED)
            2, // transition (APPROVE_STUDY)
            [
                'user_id' => $approver->id,
                'user_name' => $approver->name,
                'timestamp' => now(),
                'comment' => 'Study calendar approved',
            ],
            'study_calendar'
        );

        // Handle the event
        $listener = new NotifyStakeholders();
        $listener->handle($event);

        // Assert notification was sent to student
        Notification::assertSentTo(
            $student,
            \App\Notifications\WorkflowNotification::class,
            function ($notification) {
                return $notification->type === 'study_calendar_approved';
            }
        );
    }

    public function test_notification_contains_correct_data()
    {
        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'role' => 'hr_finance_staff'
        ]);

        $documentType = DocumentType::factory()->create();
        $document = AcademicDocument::create([
            'employee_id' => $user->employee->id,
            'document_type_id' => $documentType->id,
            'file_name' => 'test-document.pdf',
            'file_path' => '/uploads/test-document.pdf',
        ]);

        $event = new WorkflowTransitionApplied(
            $document,
            1, // from state (DRAFT)
            2, // to state (PENDING)
            1, // transition (SUBMIT)
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now(),
            ],
            'verification_by_staff'
        );

        $listener = new NotifyStakeholders();
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            \App\Notifications\WorkflowNotification::class,
            function ($notification) {
                $data = $notification->toArray($notification);
                
                return isset($data['title']) &&
                       isset($data['message']) &&
                       isset($data['icon']) &&
                       isset($data['color']) &&
                       isset($data['action_url']) &&
                       isset($data['action_text']) &&
                       isset($data['priority']);
            }
        );
    }
} 