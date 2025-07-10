<?php

namespace Tests\Feature\StudyCalendar;

use App\Models\User;
use App\Models\Employee;
use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create document types for testing
        DocumentType::factory()->create(['name' => 'Letter of Acceptance']);
        DocumentType::factory()->create(['name' => 'Ijazah']);
    }

    public function test_hr_finance_staff_can_access_approval_page()
    {
        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'role' => 'hr_finance_staff'
        ]);

        $this->actingAs($user)
             ->get(route('study-calendar.approval'))
             ->assertStatus(200);
    }

    public function test_lecturer_cannot_access_approval_page()
    {
        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $this->actingAs($user)
             ->get(route('study-calendar.approval'))
             ->assertStatus(403);
    }

    public function test_approval_page_shows_pending_study_calendars()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer and study calendar
        $lecturerUser = User::factory()->create();
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        $studyCalendar = StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        $this->actingAs($hrUser)
             ->get(route('study-calendar.approval'))
             ->assertStatus(200)
             ->assertSee($lecturerUser->name)
             ->assertSee('Menunggu Persetujuan');
    }

    public function test_hr_staff_can_approve_study_calendar_with_complete_requirements()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer and study calendar
        $lecturerUser = User::factory()->create();
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        $studyCalendar = StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        // Create verified academic documents
        $documentType = DocumentType::where('name', 'Letter of Acceptance')->first();
        AcademicDocument::factory()->create([
            'employee_id' => $lecturer->id,
            'document_type_id' => $documentType->id,
            'workflow_state' => 3 // VERIFIED
        ]);

        $this->actingAs($hrUser)
             ->post(route('workflow.transition'), [
                 'model_type' => 'study_calendar',
                 'model_id' => $studyCalendar->id,
                 'transition_id' => 2, // APPROVE_STUDY
                 'comment' => 'Study calendar approved'
             ])
             ->assertStatus(200);

        $studyCalendar->refresh();
        $this->assertEquals(3, $studyCalendar->workflow_state); // APPROVED
    }

    public function test_hr_staff_cannot_approve_study_calendar_without_complete_requirements()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer and study calendar
        $lecturerUser = User::factory()->create();
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        $studyCalendar = StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        // No academic documents created - requirements incomplete

        $this->actingAs($hrUser)
             ->post(route('workflow.transition'), [
                 'model_type' => 'study_calendar',
                 'model_id' => $studyCalendar->id,
                 'transition_id' => 2, // APPROVE_STUDY
                 'comment' => 'Study calendar approved'
             ])
             ->assertStatus(422); // Should fail validation

        $studyCalendar->refresh();
        $this->assertEquals(2, $studyCalendar->workflow_state); // Still PENDING_APPROVAL
    }

    public function test_hr_staff_can_reject_study_calendar()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer and study calendar
        $lecturerUser = User::factory()->create();
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        $studyCalendar = StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        $this->actingAs($hrUser)
             ->post(route('workflow.transition'), [
                 'model_type' => 'study_calendar',
                 'model_id' => $studyCalendar->id,
                 'transition_id' => 3, // REJECT_STUDY
                 'comment' => 'Study calendar rejected due to incomplete requirements'
             ])
             ->assertStatus(200);

        $studyCalendar->refresh();
        $this->assertEquals(4, $studyCalendar->workflow_state); // REJECTED
    }

    public function test_approval_page_shows_correct_statistics()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer
        $lecturerUser = User::factory()->create();
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        // Create study calendars in different states
        StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 3 // APPROVED
        ]);

        StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 4 // REJECTED
        ]);

        $this->actingAs($hrUser)
             ->get(route('study-calendar.approval'))
             ->assertStatus(200)
             ->assertSee('1') // Pending count
             ->assertSee('1') // Approved count
             ->assertSee('1'); // Rejected count
    }

    public function test_approval_page_filters_work_correctly()
    {
        // Create HR staff user
        $hrUser = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $hrUser->id,
            'role' => 'hr_finance_staff'
        ]);

        // Create lecturer
        $lecturerUser = User::factory()->create(['name' => 'John Doe']);
        $lecturer = Employee::factory()->create([
            'user_id' => $lecturerUser->id,
            'role' => 'lecturer'
        ]);

        $studyCalendar = StudyCalendar::factory()->create([
            'employee_id' => $lecturer->id,
            'workflow_state' => 2 // PENDING_APPROVAL
        ]);

        $this->actingAs($hrUser)
             ->get(route('study-calendar.approval'))
             ->assertStatus(200)
             ->assertSee('John Doe');

        // Test search filter
        $this->actingAs($hrUser)
             ->get(route('study-calendar.approval') . '?searchTerm=Jane')
             ->assertStatus(200)
             ->assertDontSee('John Doe');
    }
} 