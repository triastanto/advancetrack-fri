<?php

use App\Models\Document;
use App\Models\StudyCalendar;
use App\Models\User;
use App\Models\Employee;
use App\Models\DocumentType;

test('document with null workflow state handles correctly', function () {
    // Create a document without setting workflow_state (should be null)
    $document = new Document();
    
    // Should return initial state (1) when workflow_state is null
    expect($document->getCurrentState())->toBe(1);
    expect($document->isInDraftState())->toBeTrue();
    expect($document->isInPendingState())->toBeFalse();
    expect($document->isInVerifiedState())->toBeFalse();
    expect($document->isInRejectedState())->toBeFalse();
});

test('study calendar with null workflow state handles correctly', function () {
    // Create a study calendar without setting workflow_state (should be null)
    $studyCalendar = new StudyCalendar();
    
    // Should return initial state (1) when workflow_state is null
    expect($studyCalendar->getCurrentState())->toBe(1);
    expect($studyCalendar->isInDraftState())->toBeTrue();
    expect($studyCalendar->isInPendingState())->toBeFalse();
});

test('document with explicit workflow state', function () {
    // Create a document with explicit workflow_state
    $document = new Document();
    $document->workflow_state = 2; // PENDING
    
    expect($document->getCurrentState())->toBe(2);
    expect($document->isInDraftState())->toBeFalse();
    expect($document->isInPendingState())->toBeTrue();
});

test('workflow state persistence', function () {
    // Create required related models
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['user_id' => $user->id]);
    $documentType = DocumentType::factory()->create();

    // Create document without specifying workflow_state
    $document = Document::create([
        'employee_id' => $employee->id,
        'document_type_id' => $documentType->id,
        'file_name' => 'test-document.pdf',
        'file_path' => '/uploads/test-document.pdf',
    ]);

    // Should default to initial state
    expect($document->workflow_state)->toBe(1);
    expect($document->getCurrentState())->toBe(1);
    expect($document->isInDraftState())->toBeTrue();

    // Update workflow state
    $document->workflow_state = 2;
    $document->save();

    // Should reflect the new state
    expect($document->workflow_state)->toBe(2);
    expect($document->getCurrentState())->toBe(2);
    expect($document->isInPendingState())->toBeTrue();
}); 