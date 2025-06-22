<?php


use App\Traits\HasCommonValidation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

uses(HasCommonValidation::class);

beforeEach(function () {
    // Create test data
    $user = createUser();
    Auth::login($user); // Authenticate the user for workflow engine
    $this->employee = createEmployee(['user_id' => $user->id, 'role' => 'lecturer']);
    $this->documentType = createDocumentType();

    $this->document = createAcademicDocument([
        'employee_id' => $this->employee->id,
        'document_type_id' => $this->documentType->id,
        'file_name' => 'test-document.pdf',
        'file_path' => 'documents/test-document.pdf',
        'workflow_state' => 1 // Draft
    ]);
});

test('it gets document validation rules with configuration', function () {
    $rules = $this->getDocumentValidationRules();

    expect($rules)->toHaveKey('documentFile');
    expect($rules)->toHaveKey('fileName');
    expect($rules)->toHaveKey('selectedDocumentTypeId');

    expect($rules['documentFile'])->toContain('max:' . self::MAX_FILE_SIZE);
    expect($rules['fileName'])->toContain('max:' . self::MAX_FILE_NAME_LENGTH);
});

test('it gets document validation messages with enhanced descriptions', function () {
    $messages = $this->getDocumentValidationMessages();

    expect($messages)->toHaveKey('documentFile.max');
    expect($messages['documentFile.max'])->toContain((self::MAX_FILE_SIZE / 1024) . 'MB');

    expect($messages)->toHaveKey('fileName.max');
    expect($messages['fileName.max'])->toContain(self::MAX_FILE_NAME_LENGTH . ' karakter');
});

test('it gets semester validation rules with configuration', function () {
    $rules = $this->getSemesterValidationRules();

    expect($rules)->toHaveKey('selectedSemester');
    expect($rules['selectedSemester'])->toContain('min:' . self::MIN_SEMESTER);
    expect($rules['selectedSemester'])->toContain('max:' . self::MAX_SEMESTER);
});

test('it gets workflow transition validation rules', function () {
    $rules = $this->getWorkflowTransitionValidationRules();

    expect($rules)->toHaveKey('transitionId');
    expect($rules)->toHaveKey('comment');
    expect($rules['comment'])->toContain('max:' . self::MAX_COMMENT_LENGTH);
});

test('it validates document operation successfully', function () {
    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'view');

    expect($errors)->toBeEmpty();
});

test('it validates document operation with missing document', function () {
    $errors = $this->validateDocumentOperationRules(null, $this->employee, 'view');

    expect($errors)->toContain('Dokumen tidak ditemukan.');
});

test('it validates document operation with missing employee', function () {
    $errors = $this->validateDocumentOperationRules($this->document, null, 'view');

    expect($errors)->toContain('Data karyawan tidak valid.');
});

test('it validates document operation with ownership violation', function () {
    $otherEmployee = createEmployee();
    $errors = $this->validateDocumentOperationRules($this->document, $otherEmployee, 'view');

    expect($errors)->toContain('Anda tidak memiliki akses untuk operasi ini.');
});

test('it validates delete operation with invalid state', function () {
    // Set document to non-draft state
    $this->document->update(['workflow_state' => 2]); // Pending

    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'delete');

    expect($errors)->toContain('Dokumen hanya dapat dihapus dalam status Draft.');
});

test('it validates submit operation with invalid state', function () {
    // Set document to non-draft state
    $this->document->update(['workflow_state' => 2]); // Pending

    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'submit');

    expect($errors)->toContain('Dokumen hanya dapat dikirim dalam status Draft.');
});

test('it validates download operation with invalid state', function () {
    // Document in draft state should not be downloadable
    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'download');

    expect($errors)->toContain('Dokumen hanya dapat diunduh setelah diverifikasi.');
});

test('it validates workflow transition operation', function () {
    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'workflow_transition', [
        'transitionId' => 999 // Invalid transition
    ]);

    expect($errors)->toContain('ID transisi tidak valid.');
});

test('it validates unknown operation', function () {
    $errors = $this->validateDocumentOperationRules($this->document, $this->employee, 'unknown_operation');

    expect($errors)->toBeEmpty(); // Unknown operations are allowed
});

test('it validates employee ownership successfully', function () {
    $result = $this->validateEmployeeOwnership($this->document, $this->employee);
    expect($result)->toBeTrue();
});

test('it validates employee ownership with invalid data', function () {
    $result = $this->validateEmployeeOwnership(null, $this->employee);
    expect($result)->toBeFalse();

    $result = $this->validateEmployeeOwnership($this->document, null);
    expect($result)->toBeFalse();
});

test('it validates employee ownership with different employee', function () {
    $otherEmployee = createEmployee();
    $result = $this->validateEmployeeOwnership($this->document, $otherEmployee);
    expect($result)->toBeFalse();
});

test('it validates document state successfully', function () {
    $result = $this->validateDocumentState($this->document, 'Draft');
    expect($result)->toBeTrue();
});

test('it validates document state with invalid state', function () {
    $result = $this->validateDocumentState($this->document, 'InvalidState');
    expect($result)->toBeFalse();
});

test('it validates document state with invalid document', function () {
    $result = $this->validateDocumentState(null, 'Draft');
    expect($result)->toBeFalse();
});

test('it gets state id by name', function () {
    $stateId = $this->getStateIdByName('Draft');
    expect($stateId)->toBe(1);

    $stateId = $this->getStateIdByName('Pending');
    expect($stateId)->toBe(2);
});

test('it validates file upload successfully', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);
    $errors = $this->validateFileUpload($file);

    expect($errors)->toBeEmpty();
});

test('it validates file upload with missing file', function () {
    $errors = $this->validateFileUpload(null);

    expect($errors)->toContain('File wajib dipilih.');
});

test('it validates file upload with invalid type', function () {
    $file = UploadedFile::fake()->create('document.txt', 100);
    $errors = $this->validateFileUpload($file);

    expect($errors)->toContain('File harus berformat PDF.');
});

test('it validates file upload with oversized file', function () {
    $file = UploadedFile::fake()->create('document.pdf', self::MAX_FILE_SIZE + 1000);
    $errors = $this->validateFileUpload($file);

    expect($errors)->toContain('Ukuran file maksimal ' . (self::MAX_FILE_SIZE / 1024) . 'MB.');
});

test('it validates file security with invalid mime type', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'text/plain');
    $errors = $this->validateFileSecurity($file);

    expect($errors)->toContain('Tipe file tidak valid.');
});

test('it validates workflow transition successfully', function () {
    $errors = $this->validateWorkflowTransition($this->document, $this->employee, 1, 'Moving to pending');
    expect($errors)->toBeEmpty();
});

test('it validates workflow transition with pending document', function () {
    // Create a user and employee with a verifier role
    $verifierUser = createUser();
    Auth::login($verifierUser);
    $verifierEmployee = createEmployee(['user_id' => $verifierUser->id, 'role' => 'hr_finance_staff']);

    // Set document to pending state
    $this->document->update(['workflow_state' => 2]); // PENDING

    $errors = $this->validateWorkflowTransition($this->document, $verifierEmployee, 2, 'Verifying document');

    expect($errors)->toBeEmpty();
});

test('it validates workflow transition with invalid data', function () {
    $errors = $this->validateWorkflowTransition(null, $this->employee, 2);

    expect($errors)->toContain('Data tidak valid.');
});

test('it validates workflow transition with long comment', function () {
    $longComment = str_repeat('a', self::MAX_COMMENT_LENGTH + 10);
    $errors = $this->validateWorkflowTransition($this->document, $this->employee, 2, $longComment);

    expect($errors)->toContain('Komentar maksimal ' . self::MAX_COMMENT_LENGTH . ' karakter.');
});

test('it merges validation rules with caching', function () {
    $rules = $this->mergeValidationRules(['base' => 'rule'], ['extra' => 'rule']);

    expect($rules)->toHaveKey('base');
    expect($rules)->toHaveKey('extra');
});

test('it merges validation messages with caching', function () {
    $messages = $this->mergeValidationMessages(['base' => 'message'], ['extra' => 'message']);

    expect($messages)->toHaveKey('base');
    expect($messages)->toHaveKey('extra');
});

test('it gets validation config for different document types', function () {
    $config = $this->getValidationConfig('approval_documents');

    expect($config)->toBeArray();
    expect($config)->toHaveKey('max_size');
    expect($config)->toHaveKey('mimes');
});

test('it returns default config for unknown document type', function () {
    $config = $this->getValidationConfig('unknown_type');

    expect($config)->toBeArray();
    expect($config)->toHaveKey('max_size');
    expect($config)->toHaveKey('mimes');
});

test('it validates semester selection successfully', function () {
    $errors = $this->validateSemesterSelection($this->employee, 1);

    expect($errors)->toBeEmpty();
});

test('it validates semester selection with invalid range', function () {
    $errors = $this->validateSemesterSelection($this->employee, 0);

    expect($errors)->toContain('Semester minimal ' . self::MIN_SEMESTER . '.');
});

test('it validates semester selection with future semester', function () {
    $errors = $this->validateSemesterSelection($this->employee, 20);

    expect($errors)->toContain('Semester maksimal ' . self::MAX_SEMESTER . '.');
});

test('it calculates current semester correctly', function () {
    $semester = $this->calculateCurrentSemester($this->employee);

    expect($semester)->toBeGreaterThan(0);
    expect($semester)->toBeLessThanOrEqual(20);
});

test('it returns semester one for employee without study calendar', function () {
    $semester = $this->calculateCurrentSemester($this->employee);

    expect($semester)->toBe(1);
});

test('it handles exceptions gracefully in semester calculation', function () {
    // This should not throw an exception
    $semester = $this->calculateCurrentSemester($this->employee);

    expect($semester)->toBeGreaterThan(0);
});

test('it generates unique cache keys for validation rules', function () {
    $rules1 = ['field1' => 'required'];
    $rules2 = ['field2' => 'string'];

    $key1 = $this->generateValidationRulesCacheKey([$rules1, $rules2]);
    $key2 = $this->generateValidationRulesCacheKey([$rules1, $rules2]);

    expect($key1)->toBe($key2);

    $rules3 = ['field3' => 'email'];
    $key3 = $this->generateValidationRulesCacheKey([$rules1, $rules3]);

    expect($key1)->not->toBe($key3);
});

test('it generates unique cache keys for validation messages', function () {
    $messages1 = ['field1.required' => 'Required'];
    $messages2 = ['field2.string' => 'String'];

    $key1 = $this->generateValidationMessagesCacheKey([$messages1, $messages2]);
    $key2 = $this->generateValidationMessagesCacheKey([$messages1, $messages2]);

    expect($key1)->toBe($key2);

    $messages3 = ['field3.email' => 'Email'];
    $key3 = $this->generateValidationMessagesCacheKey([$messages1, $messages3]);

    expect($key1)->not->toBe($key3);
});