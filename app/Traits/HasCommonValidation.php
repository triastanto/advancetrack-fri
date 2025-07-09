<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait HasCommonValidation
{
    // Configuration constants
    protected const MAX_FILE_SIZE = 10240; // 10MB
    protected const MAX_FILE_NAME_LENGTH = 255;
    protected const MAX_COMMENT_LENGTH = 1000;
    protected const MAX_SEARCH_LENGTH = 255;
    protected const MIN_SEMESTER = 1;
    protected const MAX_SEMESTER = 19;
    protected const CACHE_TTL_MINUTES = 15;
    protected const VALIDATION_CACHE_PREFIX = 'validation_rules';
    protected const ALLOWED_MIME_TYPES = ['pdf'];

    /**
     * Get common document validation rules with configuration
     */
    public function getDocumentValidationRules(): array
    {
        return [
            'documentFile' => "required|file|mimes:pdf|max:" . self::MAX_FILE_SIZE,
            'fileName' => "required|string|max:" . self::MAX_FILE_NAME_LENGTH,
            'selectedDocumentTypeId' => 'required|exists:document_types,id',
        ];
    }

    /**
     * Get common document validation messages with enhanced error descriptions
     */
    public function getDocumentValidationMessages(): array
    {
        return [
            'documentFile.required' => 'File dokumen wajib dipilih.',
            'documentFile.file' => 'File yang dipilih tidak valid.',
            'documentFile.mimes' => 'File harus berformat PDF.',
            'documentFile.max' => "Ukuran file maksimal " . (self::MAX_FILE_SIZE / 1024) . "MB.",
            'fileName.required' => 'Nama dokumen wajib diisi.',
            'fileName.max' => "Nama dokumen maksimal " . self::MAX_FILE_NAME_LENGTH . " karakter.",
            'selectedDocumentTypeId.required' => 'Jenis dokumen wajib dipilih.',
            'selectedDocumentTypeId.exists' => 'Jenis dokumen yang dipilih tidak valid.',
        ];
    }

    /**
     * Get semester validation rules with configuration
     */
    public function getSemesterValidationRules(): array
    {
        return [
            'selectedSemester' => "required|integer|min:" . self::MIN_SEMESTER . "|max:" . self::MAX_SEMESTER,
        ];
    }

    /**
     * Get semester validation messages with enhanced error descriptions
     */
    public function getSemesterValidationMessages(): array
    {
        return [
            'selectedSemester.required' => 'Semester wajib dipilih.',
            'selectedSemester.integer' => 'Semester harus berupa angka.',
            'selectedSemester.min' => "Semester minimal " . self::MIN_SEMESTER . ".",
            'selectedSemester.max' => "Semester maksimal " . self::MAX_SEMESTER . ".",
        ];
    }

    /**
     * Get comment validation rules with configuration
     */
    public function getCommentValidationRules(): array
    {
        return [
            'comment' => "nullable|string|max:" . self::MAX_COMMENT_LENGTH,
        ];
    }

    /**
     * Get comment validation messages with enhanced error descriptions
     */
    public function getCommentValidationMessages(): array
    {
        return [
            'comment.max' => "Komentar maksimal " . self::MAX_COMMENT_LENGTH . " karakter.",
        ];
    }

    /**
     * Get search validation rules with configuration
     */
    public function getSearchValidationRules(): array
    {
        return [
            'search' => "nullable|string|max:" . self::MAX_SEARCH_LENGTH,
        ];
    }

    /**
     * Get search validation messages with enhanced error descriptions
     */
    public function getSearchValidationMessages(): array
    {
        return [
            'search.max' => "Pencarian maksimal " . self::MAX_SEARCH_LENGTH . " karakter.",
        ];
    }

    /**
     * Get workflow transition validation rules
     */
    public function getWorkflowTransitionValidationRules(): array
    {
        return [
            'transitionId' => 'required|integer|min:1',
            'comment' => "nullable|string|max:" . self::MAX_COMMENT_LENGTH,
        ];
    }

    /**
     * Get workflow transition validation messages
     */
    public function getWorkflowTransitionValidationMessages(): array
    {
        return [
            'transitionId.required' => 'ID transisi wajib diisi.',
            'transitionId.integer' => 'ID transisi harus berupa angka.',
            'transitionId.min' => 'ID transisi tidak valid.',
            'comment.max' => "Komentar maksimal " . self::MAX_COMMENT_LENGTH . " karakter.",
        ];
    }

    /**
     * Enhanced validation for document operations with detailed error messages
     */
    public function validateDocumentOperationRules($document, $employee, string $operation, array $context = []): array
    {
        $errors = [];

        // Basic validation
        if (!$document) {
            $errors[] = 'Dokumen tidak ditemukan.';
            return $errors;
        }

        if (!$employee) {
            $errors[] = 'Data karyawan tidak valid.';
            return $errors;
        }

        // Ownership validation
        if (!$this->validateEmployeeOwnership($document, $employee)) {
            $errors[] = 'Anda tidak memiliki akses untuk operasi ini.';
        }

        // Operation-specific validation
        $operationErrors = $this->validateOperationSpecificRules($document, $operation, $context);
        $errors = array_merge($errors, $operationErrors);

        // Log validation attempt
        $this->logValidationAttempt($operation, $document, $employee, $errors, $context);

        return $errors;
    }

    /**
     * Validate operation-specific rules
     */
    protected function validateOperationSpecificRules($document, string $operation, array $context = []): array
    {
        $errors = [];

        switch ($operation) {
            case 'delete':
                if (!$this->validateDocumentState($document, 'Draft')) {
                    $errors[] = 'Dokumen hanya dapat dihapus dalam status Draft.';
                }
                break;

            case 'submit':
                if (!$this->validateDocumentState($document, 'Draft')) {
                    $errors[] = 'Dokumen hanya dapat dikirim dalam status Draft.';
                }
                break;

            case 'download':
                if (!$this->validateDocumentState($document, 'Verified')) {
                    $errors[] = 'Dokumen hanya dapat diunduh setelah diverifikasi.';
                }
                break;

            case 'view':
                // Basic view validation - ownership is already checked
                break;

            case 'workflow_transition':
                $transitionId = $context['transitionId'] ?? null;
                if (!$transitionId) {
                    $errors[] = 'ID transisi tidak valid.';
                } else {
                    // Check if transition exists
                    $workflowName = $document->getWorkflowName();
                    $transition = \App\Services\Workflow\WorkflowDefinition::getTransition($transitionId, $workflowName);

                    if (empty($transition)) {
                        $errors[] = 'ID transisi tidak valid.';
                    } elseif (!$document->canTransition($transitionId)) {
                        $errors[] = 'Transisi tidak dapat dilakukan dalam status saat ini.';
                    }
                }
                break;

            default:
                // Unknown operations are allowed - no validation errors
                break;
        }

        return $errors;
    }

    /**
     * Enhanced employee ownership validation with security checks
     */
    public function validateEmployeeOwnership($model, $employee): bool
    {
        if (!$model || !$employee) {
            return false;
        }

        // Basic ownership check
        $isOwner = $model->employee_id === $employee->id;

        // Additional security checks
        if ($isOwner) {
            // Check if employee is active
            if (method_exists($employee, 'isActive') && !$employee->isActive()) {
                return false;
            }

            // Check if user account is active
            if ($employee->user && method_exists($employee->user, 'isActive') && !$employee->user->isActive()) {
                return false;
            }
        }

        return $isOwner;
    }

    /**
     * Enhanced document state validation with workflow integration
     */
    public function validateDocumentState($document, string $requiredState): bool
    {
        if (!$document) {
            return false;
        }

        // Check if document has workflow capabilities
        if (!method_exists($document, 'getCurrentState')) {
            return false;
        }

        $method = "isIn{$requiredState}State";

        if (method_exists($document, $method)) {
            return $document->$method();
        }

        // Fallback to direct state comparison
        $currentState = $document->getCurrentState();
        $requiredStateId = $this->getStateIdByName($requiredState);

        // If the required state is invalid (not found in state map), return false
        if ($requiredStateId === null) {
            return false;
        }

        return $currentState === $requiredStateId;
    }

    /**
     * Get state ID by name (workflow-agnostic)
     */
    protected function getStateIdByName(string $stateName): ?int
    {
        $stateMap = [
            'Draft' => 1,
            'Pending' => 2,
            'Verified' => 3,
            'Rejected' => 4,
        ];

        return $stateMap[$stateName] ?? null;
    }

    /**
     * Enhanced file validation with security checks
     */
    public function validateFileUpload($file, array $options = []): array
    {
        $errors = [];

        if (!$file) {
            $errors[] = 'File wajib dipilih.';
            return $errors;
        }

        // File type validation
        $allowedMimes = $options['mimes'] ?? self::ALLOWED_MIME_TYPES;
        $fileExtension = strtolower($file->getClientOriginalExtension());

        if (!in_array($fileExtension, $allowedMimes)) {
            $mimesList = implode(', ', array_map('strtoupper', $allowedMimes));
            $errors[] = "File harus berformat {$mimesList}.";
        }

        // File size validation
        $maxSize = $options['max_size'] ?? self::MAX_FILE_SIZE;
        if ($file->getSize() / 1024 > $maxSize) {
            $maxSizeMB = $maxSize / 1024;
            $errors[] = "Ukuran file maksimal {$maxSizeMB}MB.";
        }

        // Security validation
        $securityErrors = $this->validateFileSecurity($file);
        $errors = array_merge($errors, $securityErrors);

        return $errors;
    }

    /**
     * Validate file security
     */
    protected function validateFileSecurity($file): array
    {
        $errors = [];

        // Check file content type
        $mimeType = $file->getMimeType();
        if ($mimeType && !in_array($mimeType, ['application/pdf'])) {
            $errors[] = 'Tipe file tidak valid.';
        }

        return $errors;
    }

    /**
     * Enhanced validation for workflow transitions
     */
    public function validateWorkflowTransition($document, $employee, int $transitionId, ?string $comment = null): array
    {
        $errors = [];

        // Basic validation
        if (!$document || !$employee) {
            $errors[] = 'Data tidak valid.';
            return $errors;
        }

        // Check if transition is available
        if (method_exists($document, 'canTransition') && !$document->canTransition($transitionId)) {
            $errors[] = 'Transisi tidak dapat dilakukan dalam status saat ini.';
        }

        // Check if comment is required
        if (method_exists($document, 'transitionRequiresComment') &&
            $document->transitionRequiresComment($transitionId) &&
            empty($comment)) {
            $errors[] = 'Komentar wajib diisi untuk transisi ini.';
        }

        // Validate comment length if provided
        if ($comment && strlen($comment) > self::MAX_COMMENT_LENGTH) {
            $errors[] = "Komentar maksimal " . self::MAX_COMMENT_LENGTH . " karakter.";
        }

        return $errors;
    }

    /**
     * Combine validation rules with caching
     */
    public function mergeValidationRules(array ...$ruleSets): array
    {
        $cacheKey = $this->generateValidationRulesCacheKey($ruleSets);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($ruleSets) {
            return array_merge(...$ruleSets);
        });
    }

    /**
     * Generate cache key for validation rules
     */
    protected function generateValidationRulesCacheKey(array $ruleSets): string
    {
        $ruleHash = md5(serialize($ruleSets));
        return self::VALIDATION_CACHE_PREFIX . "_{$ruleHash}";
    }

    /**
     * Combine validation messages with caching
     */
    public function mergeValidationMessages(array ...$messageSets): array
    {
        $cacheKey = $this->generateValidationMessagesCacheKey($messageSets);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($messageSets) {
            return array_merge(...$messageSets);
        });
    }

    /**
     * Generate cache key for validation messages
     */
    protected function generateValidationMessagesCacheKey(array $messageSets): string
    {
        $messageHash = md5(serialize($messageSets));
        return self::VALIDATION_CACHE_PREFIX . "_messages_{$messageHash}";
    }

    /**
     * Enhanced logging for validation attempts
     */
    protected function logValidationAttempt(string $operation, $document, $employee, array $errors, array $context = [])
    {
        $logData = [
            'operation' => $operation,
            'document_id' => $document?->id,
            'employee_id' => $employee?->id,
            'employee_name' => $employee?->user?->name,
            'has_errors' => !empty($errors),
            'error_count' => count($errors),
            'errors' => $errors,
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ];

        if (!empty($errors)) {
            Log::warning('Document validation failed', $logData);
        } else {
            Log::info('Document validation passed', $logData);
        }
    }

    /**
     * Get validation configuration for different document types
     */
    public function getValidationConfig(string $documentType): array
    {
        $configs = [
            'approval_documents' => [
                'max_size' => self::MAX_FILE_SIZE,
                'mimes' => self::ALLOWED_MIME_TYPES,
                'requires_semester' => false,
            ],
            'semester_reports' => [
                'max_size' => self::MAX_FILE_SIZE,
                'mimes' => self::ALLOWED_MIME_TYPES,
                'requires_semester' => true,
            ],
            'final_reports' => [
                'max_size' => self::MAX_FILE_SIZE,
                'mimes' => self::ALLOWED_MIME_TYPES,
                'requires_semester' => false,
            ],
            'study_requirements' => [
                'max_size' => self::MAX_FILE_SIZE,
                'mimes' => self::ALLOWED_MIME_TYPES,
                'requires_semester' => false,
            ],
        ];

        return $configs[$documentType] ?? $configs['approval_documents'];
    }

    /**
     * Validate semester selection based on study progress
     */
    public function validateSemesterSelection($employee, int $semester): array
    {
        $errors = [];

        if ($semester < self::MIN_SEMESTER || $semester > self::MAX_SEMESTER) {
            if ($semester < self::MIN_SEMESTER) {
                $errors[] = "Semester minimal " . self::MIN_SEMESTER . ".";
            } else {
                $errors[] = "Semester maksimal " . self::MAX_SEMESTER . ".";
            }
            return $errors;
        }

        // Check if semester is reasonable based on study progress
        $currentSemester = $this->calculateCurrentSemester($employee);
        if ($semester > $currentSemester + 2) {
            $errors[] = "Semester yang dipilih terlalu jauh di masa depan.";
        }

        return $errors;
    }

    /**
     * Calculate current semester based on study start date
     */
    protected function calculateCurrentSemester($employee): int
    {
        try {
            $activeStudy = $employee->studyCalendars()
                ->where('workflow_state', 5) // ACTIVE state
                ->latest()
                ->first();

            if (!$activeStudy) {
                return 1;
            }

            $startDate = \Carbon\Carbon::parse($activeStudy->study_start);
            $now = \Carbon\Carbon::now();
            $monthsDiff = $startDate->diffInMonths($now);

            return max(1, floor($monthsDiff / 6) + 1);
        } catch (\Exception $e) {
            Log::error('Error calculating current semester: ' . $e->getMessage(), [
                'employee_id' => $employee->id
            ]);
            return 1;
        }
    }
}
