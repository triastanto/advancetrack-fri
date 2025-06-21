<?php

use App\Models\Document;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = createUser();
    Auth::login($this->user); // Authenticate the user for workflow engine
    $this->employee = createEmployee(['user_id' => $this->user->id, 'role' => 'lecturer']);
    $this->documentType = createDocumentType();
});

test('document with null workflow state handles correctly', function () {
    $document = new Document();
    
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

test('document with explicit workflow state', function () {
    $document = new Document();
    $document->workflow_state = 2; // PENDING
    
    expectDocumentState($document, 2);
    expect($document->isInDraftState())->toBeFalse();
    expectDocumentPending($document);
});

test('workflow state persistence', function () {
    $document = Document::create([
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

test('document can transition from draft to pending', function () {
    $document = createDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    expectDocumentDraft($document);
    expect($document->canTransition(1))->toBeTrue();

    $document->applyTransition(1);
    expectDocumentPending($document);
});

test('document cannot transition to invalid state', function () {
    $document = createDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    expectDocumentDraft($document);
    expect($document->canTransition(2))->toBeFalse(); // Transition 2: VERIFY requires PENDING state
});

test('workflow transition creates history record', function () {
    $document = createDocument([
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

test('document state info returns correct data', function () {
    $document = createDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    $stateInfo = $document->getWorkflowStateInfo();
    
    expect($stateInfo)->toHaveKeys(['label', 'color', 'icon']);
    expect($stateInfo['label'])->toBe('Draft');
    expect($stateInfo['color'])->toBe('secondary');
});

test('document available transitions are correct', function () {
    $document = createDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'workflow_state' => 1
    ]);

    $transitions = $document->getAvailableTransitions();
    
    expect($transitions)->toBeArray();
    expect(array_keys($transitions))->toContain(1); // Should be able to transition to pending (SUBMIT)
}); 