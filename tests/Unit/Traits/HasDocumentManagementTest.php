<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\User;
use App\Models\StudyCalendar;
use App\Models\StudyProgram;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

uses(HasDocumentManagement::class, HasCommonValidation::class);

beforeEach(function () {
    // Create test data
    $user = createUser();
    Auth::login($user); // Authenticate the user for workflow engine
    $this->employee = createEmployee(['user_id' => $user->id]);
    $this->documentType = createDocumentType();
    
    $this->document = createDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'file_name' => 'test-document.pdf',
        'file_path' => 'documents/test-document.pdf',
        'workflow_state' => 1 // Draft
    ]);
});

test('it gets document completion status with caching', function () {
    // Create additional document types
    $docType2 = createDocumentType();
    $docType3 = createDocumentType();
    
    $documentTypeIds = [$this->documentType->id, $docType2->id, $docType3->id];

    // First call should calculate and cache
    $result1 = $this->getDocumentCompletionStatus($this->employee, $documentTypeIds);
    
    // Second call should use cache
    $result2 = $this->getDocumentCompletionStatus($this->employee, $documentTypeIds);
    
    expect($result1)->toBe($result2);
    expect($result1)->toHaveKey('status');
    expect($result1)->toHaveKey('details');
    expect($result1)->toHaveKey('completed_count');
    expect($result1)->toHaveKey('total_count');
});

test('it gets document completion status without caching', function () {
    $documentTypeIds = [$this->documentType->id];
    
    $result = $this->getDocumentCompletionStatus($this->employee, $documentTypeIds, false);
    
    expect($result)->toHaveKey('status');
    expect($result)->toHaveKey('details');
    expect($result['total_count'])->toBe(1);
});

test('it throws exception for invalid employee', function () {
    expect(fn() => $this->getDocumentCompletionStatus(null, [1]))
        ->toThrow(\InvalidArgumentException::class, 'Valid employee is required');
});

test('it gets active study info with caching', function () {
    // Create study program and calendar
    $studyProgram = \App\Models\StudyProgram::factory()->create();
    $this->employee->studyPrograms()->attach($studyProgram->id);
    
    $studyCalendar = createStudyCalendar([
        'employee_id' => $this->employee->id,
        'study_status' => 'active',
        'study_start' => Carbon::now()->subMonths(6),
        'estimated_study_end' => Carbon::now()->addMonths(18)
    ]);

    // First call should calculate and cache
    $result1 = $this->getActiveStudyInfo($this->employee);
    
    // Second call should use cache
    $result2 = $this->getActiveStudyInfo($this->employee);
    
    expect($result1)->toBe($result2);
    expect($result1)->toHaveKey('program');
    expect($result1)->toHaveKey('status');
    expect($result1)->toHaveKey('current_semester');
});

test('it returns null for employee without study calendar', function () {
    $result = $this->getActiveStudyInfo($this->employee);
    expect($result)->toBeNull();
});

test('it validates document operation successfully', function () {
    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'view');
    
    expect($errors)->toBeEmpty();
});

test('it validates document operation with errors', function () {
    // Test with non-existent document
    $errors = $this->validateDocumentOperationRules(null, $this->employee, 'view');
    
    expect($errors)->toContain('Dokumen tidak ditemukan.');
});

test('it validates document access with ownership', function () {
    $result = $this->validateDocumentAccess($this->document, $this->employee, 'view');
    expect($result)->toBeTrue();
});

test('it validates document access without ownership', function () {
    $otherEmployee = createEmployee();
    $result = $this->validateDocumentAccess($this->document, $otherEmployee, 'view');
    expect($result)->toBeFalse();
});

test('it validates document ownership', function () {
    $result = $this->validateDocumentOwnership($this->document, $this->employee);
    expect($result)->toBeTrue();
});

test('it validates document ownership with invalid data', function () {
    $result = $this->validateDocumentOwnership(null, $this->employee);
    expect($result)->toBeFalse();
    
    $result = $this->validateDocumentOwnership($this->document, null);
    expect($result)->toBeFalse();
});

test('it deletes document with file successfully', function () {
    // Mock storage
    Storage::fake('public');
    Storage::disk('public')->put('documents/test-document.pdf', 'test content');
    
    $result = $this->deleteDocumentWithFile($this->document, $this->employee);
    
    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('documents', ['id' => $this->document->id]);
    expect(Storage::disk('public')->exists('documents/test-document.pdf'))->toBeFalse();
});

test('it blocks document deletion with invalid state', function () {
    // Set document to non-draft state
    $this->document->update(['workflow_state' => 2]); // Pending
    
    $result = $this->deleteDocumentWithFile($this->document, $this->employee);
    
    expect($result)->toBeFalse();
    $this->assertDatabaseHas('documents', ['id' => $this->document->id]);
});

test('it validates document download permissions', function () {
    // Document in draft state should not be downloadable
    $result = $this->canDownloadDocument($this->document, $this->employee);
    expect($result)->toBeFalse();
    
    // Set to verified state and create file in storage
    $this->document->update(['workflow_state' => 3]); // Verified
    Storage::fake('public');
    Storage::disk('public')->put('documents/test-document.pdf', 'test content');
    
    $result = $this->canDownloadDocument($this->document, $this->employee);
    expect($result)->toBeTrue();
});

test('it gets document api data', function () {
    $data = $this->getDocumentApiData($this->document);
    
    expect($data)->toHaveKey('id');
    expect($data)->toHaveKey('file_name');
    expect($data)->toHaveKey('uploaded_at');
    expect($data)->toHaveKey('document_type');
    expect($data)->toHaveKey('status');
});

test('it gets document file size', function () {
    // Mock storage
    Storage::fake('public');
    Storage::disk('public')->put('documents/test-document.pdf', 'test content');
    
    $size = $this->getDocumentFileSize($this->document);
    
    expect($size)->toBeGreaterThan(0);
});

test('it returns null file size for nonexistent file', function () {
    $size = $this->getDocumentFileSize($this->document);
    
    expect($size)->toBeNull();
});

test('it gets document url', function () {
    $url = $this->getDocumentUrl($this->document);
    
    expect($url)->toContain('documents/test-document.pdf');
});

test('it returns null url for document without file path', function () {
    $this->document->update(['file_path' => '']);
    
    $url = $this->getDocumentUrl($this->document);
    
    expect($url)->toBeNull();
});

test('it calculates current semester correctly', function () {
    // Create study calendar with 6 months ago start
    $studyProgram = \App\Models\StudyProgram::factory()->create();
    $this->employee->studyPrograms()->attach($studyProgram->id);
    
    $studyCalendar = createStudyCalendar([
        'employee_id' => $this->employee->id,
        'study_status' => 'active',
        'study_start' => Carbon::now()->subMonths(6),
        'estimated_study_end' => Carbon::now()->addMonths(18)
    ]);

    $semester = $this->calculateCurrentSemester($this->employee);
    expect($semester)->toBe(2); // 6 months = 1 semester, so current semester is 2
});

test('it returns semester one for employee without study calendar', function () {
    $semester = $this->calculateCurrentSemester($this->employee);
    expect($semester)->toBe(1);
});

test('it handles exceptions gracefully in completion status calculation', function () {
    // Mock employee to throw exception
    $this->mock(Employee::class, function ($mock) {
        $mock->shouldReceive('documents->whereIn->get')
            ->andThrow(new \Exception('Database error'));
    });
    
    $result = $this->getDocumentCompletionStatus($this->employee, [1]);
    expect($result['status'])->toBe('Belum Lengkap');
});

test('it handles exceptions gracefully in active study info calculation', function () {
    // Mock employee to throw exception
    $this->mock(Employee::class, function ($mock) {
        $mock->shouldReceive('studyCalendars->where->latest->first')
            ->andThrow(new \Exception('Database error'));
    });
    
    $result = $this->getActiveStudyInfo($this->employee);
    expect($result)->toBeNull();
}); 