<?php

use App\Livewire\Components\Document\DocumentUploadModal;
use App\Livewire\Components\Document\DocumentViewModal;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test user and employee
    $user = User::factory()->create();
    $this->employee = Employee::factory()->create(['user_id' => $user->id]);

    // Create document types for testing
    $this->academicDocumentType = DocumentType::factory()->create([
        'name' => 'transcript',
        'display_name' => 'Transkrip Nilai'
    ]);

    $this->approvalDocumentType = DocumentType::factory()->create([
        'name' => 'study_compatibility',
        'display_name' => 'Surat Kesesuaian Studi'
    ]);
});

test('document upload modal supports academic documents', function () {
    Livewire::test(DocumentUploadModal::class, [
        'employee' => $this->employee
    ])
    ->call('open', [
        'category' => 'semester-reports',
        'documentTypeId' => $this->academicDocumentType->id,
        'semester' => 1
    ])
    ->assertSet('documentClass', AcademicDocument::class)
    ->assertSet('requiresSemester', true)
    ->assertSet('selectedSemester', 1)
    ->assertSet('selectedDocumentTypeId', $this->academicDocumentType->id);
});

test('document upload modal supports approval documents', function () {
    Livewire::test(DocumentUploadModal::class, [
        'employee' => $this->employee
    ])
    ->call('open', [
        'category' => 'approvals',
        'documentTypeId' => $this->approvalDocumentType->id
    ])
    ->assertSet('documentClass', ApprovalDocument::class)
    ->assertSet('requiresSemester', false)
    ->assertSet('selectedDocumentTypeId', $this->approvalDocumentType->id);
});

test('document upload modal validates document type compatibility', function () {
    // Test that academic document type is rejected for approval documents
    Livewire::test(DocumentUploadModal::class, [
        'employee' => $this->employee
    ])
    ->call('open', [
        'category' => 'approvals',
        'documentTypeId' => $this->academicDocumentType->id
    ])
    ->set('fileName', 'Test Document')
    ->set('documentFile', \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 100))
    ->call('uploadDocument')
    ->assertHasErrors(['upload']);
});

test('document view modal loads academic documents', function () {
    // Create an academic document
    $document = AcademicDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->academicDocumentType->id,
        'file_name' => 'Test Academic Document',
        'file_path' => 'test/path.pdf'
    ]);

    Livewire::test(DocumentViewModal::class)
    ->call('open', [
        'documentId' => $document->id,
        'category' => 'semester-reports'
    ])
    ->assertSet('documentModel', AcademicDocument::class)
    ->assertSet('document.id', $document->id)
    ->assertSet('document.file_name', 'Test Academic Document');
});

test('document view modal loads approval documents', function () {
    // Create an approval document
    $document = ApprovalDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->approvalDocumentType->id,
        'file_name' => 'Test Approval Document',
        'file_path' => 'test/path.pdf'
    ]);

    Livewire::test(DocumentViewModal::class)
    ->call('open', [
        'documentId' => $document->id,
        'category' => 'approvals'
    ])
    ->assertSet('documentModel', ApprovalDocument::class)
    ->assertSet('document.id', $document->id)
    ->assertSet('document.file_name', 'Test Approval Document');
});

test('document view modal falls back to base model', function () {
    // Create a document using AcademicDocument model (since Document is abstract)
    $document = AcademicDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->academicDocumentType->id,
        'file_name' => 'Test Base Document',
        'file_path' => 'test/path.pdf'
    ]);

    Livewire::test(DocumentViewModal::class)
    ->call('open', [
        'documentId' => $document->id
    ])
    ->assertSet('documentModel', AcademicDocument::class)
    ->assertSet('document.id', $document->id)
    ->assertSet('document.file_name', 'Test Base Document');
});

test('document view modal determines model from document model parameter', function () {
    $document = AcademicDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->academicDocumentType->id,
        'file_name' => 'Test Document',
        'file_path' => 'test/path.pdf'
    ]);

    Livewire::test(DocumentViewModal::class)
    ->call('open', [
        'documentId' => $document->id,
        'documentModel' => 'AcademicDocument'
    ])
    ->assertSet('documentModel', AcademicDocument::class);
});

test('document upload modal emits correct event data', function () {
    // Mock file upload
    $file = \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 100);

    Livewire::test(DocumentUploadModal::class, [
        'employee' => $this->employee
    ])
    ->call('open', [
        'category' => 'approvals',
        'documentTypeId' => $this->approvalDocumentType->id
    ])
    ->set('fileName', 'Test Approval Document')
    ->set('documentFile', $file)
    ->call('uploadDocument')
    ->assertDispatched('document:uploaded');
});

test('document view modal returns correct category', function () {
    $document = AcademicDocument::factory()->create([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->academicDocumentType->id,
        'file_name' => 'Test Document',
        'file_path' => 'test/path.pdf'
    ]);

    $component = Livewire::test(DocumentViewModal::class)
    ->call('open', [
        'documentId' => $document->id,
        'category' => 'semester-reports'
    ]);

    // Get the category from the component instance
    $category = $component->instance()->getDocumentCategory();
    expect($category)->toBe('semester-reports');
});
