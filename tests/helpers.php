<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\StudyCalendar;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Create a user for testing
 */
function createUser($attributes = []): User
{
    return User::factory()->create($attributes);
}

/**
 * Create an employee for testing
 */
function createEmployee($attributes = []): Employee
{
    return Employee::factory()->create($attributes);
}

/**
 * Create a document type for testing
 */
function createDocumentType($attributes = []): DocumentType
{
    return DocumentType::factory()->create($attributes);
}

/**
 * Create a document for testing
 */
function createDocument($attributes = []): Document
{
    return Document::factory()->create($attributes);
}

/**
 * Create a study calendar for testing
 */
function createStudyCalendar($attributes = []): StudyCalendar
{
    return StudyCalendar::factory()->create($attributes);
}

/**
 * Assert document workflow state
 */
function expectDocumentState($document, $expectedState): void
{
    expect($document->getCurrentState())->toBe($expectedState);
}

/**
 * Assert document is in draft state
 */
function expectDocumentDraft($document): void
{
    expect($document->isInDraftState())->toBeTrue();
}

/**
 * Assert document is in pending state
 */
function expectDocumentPending($document): void
{
    expect($document->isInPendingState())->toBeTrue();
}

/**
 * Assert document is in rejected state
 */
function expectDocumentRejected($document): void
{
    expect($document->isInRejectedState())->toBeTrue();
} 