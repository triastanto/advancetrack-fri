<?php

use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\StudyCalendar;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = createUser();
    Auth::login($this->user); // Authenticate the user for workflow engine
    $this->employee = createEmployee(['user_id' => $this->user->id, 'role' => 'lecturer']);
    $this->documentType = createDocumentType();
});

test('academic document with null workflow state handles correctly', function () {
    $document = new AcademicDocument();

    expectDocumentState($document, 1);
    expectDocumentDraft($document);
    expect($document->isInPendingState())->toBeFalse();
    expect($document->isInVerifiedState())->toBeFalse();
    expect($document->isInRejectedState())->toBeFalse();
});

test('study calendar with null workflow state handles correctly', function () {
    $studyCalendar = new \App\Models\StudyCalendar();

    expectDocumentState($studyCalendar, 1);
    expectDocumentDraft($studyCalendar);
    expect($studyCalendar->isInPendingState())->toBeFalse();
});

test('academic document with explicit workflow state', function () {
    $document = new AcademicDocument();
    $document->workflow_state = 2; // PENDING

    expectDocumentState($document, 2);
    expect($document->isInDraftState())->toBeFalse();
    expectDocumentPending($document);
});

test('workflow state persistence', function () {
    $document = AcademicDocument::create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'file_name' => 'test-document.pdf',
        'file_path' => '/uploads/test-document.pdf',
    ]);

    expectDocumentState($document, 1);
    expect($document->workflow_state)->toBe(1);
    expectDocumentDraft($document);

    $document->workflow_state = 2;
    $document->save();

    expectDocumentState($document, 2);
    expect($document->workflow_state)->toBe(2);
    expectDocumentPending($document);
});

test('academic document can transition from draft to pending', function () {
    $document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    expectDocumentDraft($document);
    expect($document->canTransition(1))->toBeTrue();

    $document->applyTransition(1);
    expectDocumentPending($document);
});

test('academic document cannot transition to invalid state', function () {
    $document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    expectDocumentDraft($document);
    expect($document->canTransition(2))->toBeFalse(); // Transition 2: VERIFY requires PENDING state
});

test('workflow transition creates history record', function () {
    $document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    $document->applyTransition(1, ['comment' => 'Moving to pending review']); // Transition 1: SUBMIT

    $finalHistory = $document->workflowHistory()->get();
    expect($finalHistory)->toHaveCount(1); // Only the transition creates a history record
    $latestHistory = $document->workflowHistory()->latest()->first();
    expect($latestHistory)->not->toBeNull();
    expect($latestHistory->to_state)->toBe(2); // Check to_state, not workflow_state
});

test('academic document state info returns correct data', function () {
    $document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    $stateInfo = $document->getWorkflowStateInfo();

    expect($stateInfo)->toHaveKeys(['label', 'color', 'icon']);
    expect($stateInfo['label'])->toBe('Draft');
    expect($stateInfo['color'])->toBe('secondary');
});

test('academic document available transitions are correct', function () {
    $document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    $transitions = $document->getAvailableTransitions();

    expect($transitions)->toBeArray();
    expect(array_keys($transitions))->toContain(1); // Should be able to transition to pending (SUBMIT)
});

test('email notifications command handles both document models', function () {
    // Create test models for both document types
    $academicDocument = AcademicDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'workflow_state' => 1, // DRAFT
    ]);

    $approvalDocument = ApprovalDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'workflow_state' => 1, // DRAFT
    ]);

    // Test verification_by_staff workflow with AcademicDocument
    $this->artisan('test:email-notifications', [
        'workflow' => 'verification_by_staff',
        '--simulate' => true,
    ])
    ->assertExitCode(0);

    // Test verification_by_management workflow with ApprovalDocument
    $this->artisan('test:email-notifications', [
        'workflow' => 'verification_by_management',
        '--simulate' => true,
    ])
    ->assertExitCode(0);

    // Test with specific model IDs
    $this->artisan('test:email-notifications', [
        'workflow' => 'verification_by_staff',
        '--model_id' => $academicDocument->id,
        '--simulate' => true,
    ])
    ->assertExitCode(0);

    $this->artisan('test:email-notifications', [
        'workflow' => 'verification_by_management',
        '--model_id' => $approvalDocument->id,
        '--simulate' => true,
    ])
    ->assertExitCode(0);
});

test('email notifications command handles all workflows', function () {
    // Create test models
    AcademicDocument::factory()->create(['employee_id' => $this->employee->id]);
    ApprovalDocument::factory()->create(['employee_id' => $this->employee->id]);
    StudyCalendar::factory()->create(['employee_id' => $this->employee->id]);

    // Test all workflows
    $this->artisan('test:email-notifications', [
        'workflow' => 'all',
        '--simulate' => true,
    ])
    ->assertExitCode(0)
    ->expectsOutput('Testing workflow: verification_by_staff')
    ->expectsOutput('Testing workflow: verification_by_management')
    ->expectsOutput('Testing workflow: study_calendar_approval');
});

test('email notifications command validates workflow names', function () {
    $this->artisan('test:email-notifications', [
        'workflow' => 'invalid_workflow',
    ])
    ->assertExitCode(1)
    ->expectsOutput("Workflow 'invalid_workflow' not found!");
});

test('email notifications command requires email for direct testing', function () {
    $this->artisan('test:email-notifications', [
        'workflow' => 'verification_by_staff',
        '--transition' => 1,
    ])
    ->assertExitCode(1)
    ->expectsOutput('Email address is required for direct email testing. Use --email option or --simulate flag.');
});