<?php

namespace App\Traits;

use App\Models\Document;
use App\Models\DocumentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait HasDocumentManagement
{
    // Configuration constants
    protected const MAX_FILE_SIZE = 10240; // 10MB
    protected const ALLOWED_MIME_TYPES = ['pdf'];
    protected const CACHE_TTL_MINUTES = 15;
    protected const PAGINATION_PER_PAGE = 10;
    protected const COMPLETION_STATUS_CACHE_PREFIX = 'completion_status';
    protected const ACTIVE_STUDY_CACHE_PREFIX = 'active_study_info';

    /**
     * Get completion status for document types with caching and optimization
     *
     * @param Employee $employee The employee whose documents to check
     * @param array $documentTypeIds Array of document type IDs to check
     * @param bool $useCache Whether to use cached results (default: true)
     * @param string|null $documentClass Specific document class to query (AcademicDocument, ApprovalDocument, or null for all)
     * @return array Completion status with detailed information
     *
     * @throws \Exception When employee data is invalid
     */
    public function getDocumentCompletionStatus($employee, $documentTypeIds, bool $useCache = true, $documentClass): array
    {
        if (!$employee || !$employee->id) {
            throw new \InvalidArgumentException('Valid employee is required');
        }

        if ($useCache) {
            return $this->getCachedCompletionStatus($employee, $documentTypeIds, $documentClass);
        }

        return $this->calculateCompletionStatus($employee, $documentTypeIds, $documentClass);
    }

    /**
     * Get cached completion status
     *
     * @param Employee $employee The employee whose documents to check
     * @param array $documentTypeIds Array of document type IDs to check
     * @param string|null $documentClass Specific document class to query
     * @return array Cached completion status
     */
    protected function getCachedCompletionStatus($employee, $documentTypeIds, $documentClass): array
    {
        $cacheKey = $this->generateCompletionStatusCacheKey($employee, $documentTypeIds, $documentClass);

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($employee, $documentTypeIds, $documentClass) {
            return $this->calculateCompletionStatus($employee, $documentTypeIds, $documentClass);
        });
    }

    /**
     * Generate cache key for completion status
     *
     * @param Employee $employee The employee
     * @param array $documentTypeIds Document type IDs
     * @param string|null $documentClass Document class name if specific
     * @return string The cache key
     */
    protected function generateCompletionStatusCacheKey($employee, $documentTypeIds, $documentClass): string
    {
        $sortedIds = $documentTypeIds;
        sort($sortedIds);
        $classPrefix = $documentClass ? strtolower(substr($documentClass, strrpos($documentClass, '\\') + 1)) . '_' : '';
        return self::COMPLETION_STATUS_CACHE_PREFIX . "_{$employee->id}_{$classPrefix}" . md5(implode(',', $sortedIds));
    }

    /**
     * Calculate completion status without caching
     *
     * @param Employee $employee The employee whose documents to check
     * @param array $documentTypeIds Array of document type IDs to check
     * @param string|null $documentClass Specific document class to query (AcademicDocument, ApprovalDocument, or null for all)
     * @return array Completion status with detailed information
     */
    protected function calculateCompletionStatus($employee, $documentTypeIds, $documentClass): array
    {
        try {
            $uploadedDocuments = $this->getDocumentsWithOptimizedQueries($employee, $documentTypeIds, [], $documentClass);
            $documentTypes = DocumentType::whereIn('id', $documentTypeIds)->get();

            $completionDetails = [];
            $allCompleted = true;
            $completedCount = 0;

            foreach ($documentTypes as $docType) {
                $document = $uploadedDocuments->where('document_type_id', $docType->id)->first();

                if ($document) {
                    $stateInfo = $document->getWorkflowStateInfo();
                    $isCompleted = $document->isInVerifiedState();

                    $completionDetails[$docType->name] = [
                        'uploaded' => true,
                        'state_info' => $stateInfo,
                        'document' => $document,
                        'uploaded_at' => $document->created_at->format('d M Y'),
                        'completed' => $isCompleted
                    ];

                    if ($isCompleted) {
                        $completedCount++;
                    } else {
                        $allCompleted = false;
                    }
                } else {
                    $completionDetails[$docType->name] = [
                        'uploaded' => false,
                        'state_info' => null,
                        'document' => null,
                        'uploaded_at' => null,
                        'completed' => false
                    ];
                    $allCompleted = false;
                }
            }

            $result = [
                'status' => $allCompleted ? 'Lengkap' : 'Belum Lengkap',
                'details' => $completionDetails,
                'completed_count' => $completedCount,
                'total_count' => count($documentTypes)
            ];

            $this->logDocumentOperation('completion_status_calculated', null, $employee, [
                'document_type_ids' => $documentTypeIds,
                'result' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error getting completion status: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'document_type_ids' => $documentTypeIds,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'Error',
                'details' => [],
                'completed_count' => 0,
                'total_count' => 0
            ];
        }
    }

    /**
     * Get documents with optimized queries to prevent N+1 problems
     *
     * @param Employee $employee The employee whose documents to retrieve
     * @param array $documentTypeIds Array of document type IDs to filter by
     * @param array $withRelations Additional relations to load
     * @param string|null $documentClass Specific document class to query (AcademicDocument, ApprovalDocument, or null for all)
     * @return \Illuminate\Database\Eloquent\Collection Collection of documents
     */
    protected function getDocumentsWithOptimizedQueries($employee, $documentTypeIds, $withRelations = [], $documentClass)
    {
        $defaultRelations = ['documentType', 'workflowHistory.user'];
        $relations = array_merge($defaultRelations, $withRelations);

        // Start with the base query
        if ($documentClass === 'AcademicDocument') {
            $query = \App\Models\AcademicDocument::where('employee_id', $employee->id);
        } elseif ($documentClass === 'ApprovalDocument') {
            $query = \App\Models\ApprovalDocument::where('employee_id', $employee->id);
        } else {
            $query = Document::where('employee_id', $employee->id);
        }

        // Apply the common filters and relations
        return $query->whereIn('document_type_id', $documentTypeIds)
            ->with($relations)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active study information for an employee with caching
     */
    public function getActiveStudyInfo($employee, bool $useCache = true): ?array
    {
        if (!$employee || !$employee->id) {
            return null;
        }

        if ($useCache) {
            return $this->getCachedActiveStudyInfo($employee);
        }

        return $this->calculateActiveStudyInfo($employee);

    }

    /**
     * Get cached active study info
     */
    protected function getCachedActiveStudyInfo($employee): ?array
    {
        $cacheKey = self::ACTIVE_STUDY_CACHE_PREFIX . "_{$employee->id}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($employee) {
            return $this->calculateActiveStudyInfo($employee);
        });
    }

    /**
     * Calculate active study info without caching
     */
    protected function calculateActiveStudyInfo($employee): ?array
    {
        try {
            $activeStudy = $employee->studyCalendars()
                ->where('workflow_state', 5) // ACTIVE state
                ->latest()
                ->first();

            if (!$activeStudy) {
                $activeStudy = $employee->studyCalendars()
                    ->latest()
                    ->first();

                if (!$activeStudy) {
                    return null;
                }
            }

            $studyProgram = $employee->studyPrograms()->first();
            $programName = $studyProgram ? $studyProgram->name : 'Tidak tersedia';

            $startDate = Carbon::parse($activeStudy->study_start);
            $now = Carbon::now();
            $monthsDiff = $startDate->diffInMonths($now);
            $currentSemester = max(1, floor($monthsDiff / 6) + 1);

            return [
                'program' => $programName,
                'status' => $this->getStudyStatusFromWorkflowState($activeStudy->workflow_state),
                'start_date' => $startDate->format('F Y'),
                'estimated_end' => Carbon::parse($activeStudy->estimated_study_end)->format('F Y'),
                'current_semester' => $currentSemester,
                'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
                'months_elapsed' => $monthsDiff
            ];
        } catch (\Exception $e) {
            Log::error('Error getting active study info: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Enhanced document operation validation with detailed error messages
     */
    protected function validateDocumentOperationAccess($document, $employee, string $operation): array
    {
        $errors = [];

        if (!$document) {
            $errors[] = 'Dokumen tidak ditemukan.';
            return $errors;
        }

        if (!$this->validateDocumentAccess($document, $employee, $operation)) {
            $errors[] = 'Anda tidak memiliki akses untuk operasi ini.';
        }

        // Operation-specific validation
        switch ($operation) {
            case 'delete':
                if (!$document->canBeDeleted()) {
                    $errors[] = 'Dokumen tidak dapat dihapus dalam status saat ini.';
                }
                break;
            case 'submit':
                if (!$document->canBeSubmitted()) {
                    $errors[] = 'Dokumen tidak dapat dikirim dalam status saat ini.';
                }
                break;
            case 'download':
                if (!$this->canDownloadDocument($document, $employee)) {
                    $errors[] = 'Dokumen tidak dapat diunduh dalam status saat ini.';
                }
                break;
            case 'view':
                if (!$this->validateDocumentAccess($document, $employee, 'view')) {
                    $errors[] = 'Anda tidak memiliki akses untuk melihat dokumen ini.';
                }
                break;
        }

        return $errors;
    }

    /**
     * Enhanced security validation for document access
     */
    protected function validateDocumentAccess($document, $employee, string $permission = 'view'): bool
    {
        // Check ownership
        if (!$this->validateDocumentOwnership($document, $employee)) {
            return false;
        }

        // Check role-based permissions
        $userRole = $employee->role ?? 'employee';
        $allowedRoles = config("permissions.document.{$permission}", []);

        if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
            return false;
        }

        return true;
    }

    /**
     * Enhanced document ownership validation
     */
    protected function validateDocumentOwnership($document, $employee): bool
    {
        if (!$document || !$employee) {
            return false;
        }

        return $document->employee_id === $employee->id;
    }

    /**
     * Enhanced document deletion with validation and logging
     */
    protected function deleteDocumentWithFile($document, $employee = null): bool
    {
        try {
            // Validate operation
            if ($employee) {
                $errors = $this->validateDocumentOperationAccess($document, $employee, 'delete');
                if (!empty($errors)) {
                    Log::warning('Document deletion blocked', [
                        'document_id' => $document->id,
                        'employee_id' => $employee->id,
                        'errors' => $errors
                    ]);
                    return false;
                }
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete database record
            $document->delete();

            // Log successful deletion
            $this->logDocumentOperation('deleted', $document, $employee);

            // Clear related caches
            $this->clearDocumentRelatedCaches($document, $employee);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'employee_id' => $employee?->id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Enhanced document download validation
     */
    protected function canDownloadDocument($document, $employee = null): bool
    {
        // Check if document is in verified state
        if (!$document->isInVerifiedState()) {
            return false;
        }

        // Check ownership if employee is provided
        if ($employee && !$this->validateDocumentAccess($document, $employee, 'download')) {
            return false;
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            return false;
        }

        return true;
    }

    /**
     * Enhanced document download with logging
     */
    protected function generateDocumentDownload($document, $employee = null)
    {
        try {
            if ($employee) {
                $errors = $this->validateDocumentOperationAccess($document, $employee, 'download');
                if (!empty($errors)) {
                    throw new \Exception(implode(', ', $errors));
                }
            }

            $filePath = Storage::disk('public')->path($document->file_path);

            // Log download
            $this->logDocumentOperation('downloaded', $document, $employee);

            return response()->download($filePath, $document->file_name);
        } catch (\Exception $e) {
            Log::error('Error generating document download: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'employee_id' => $employee?->id
            ]);
            throw $e;
        }
    }

    /**
     * Enhanced logging for document operations
     */
    protected function logDocumentOperation($operation, $document, $employee, $context = [])
    {
        $logData = [
            'operation' => $operation,
            'employee_id' => $employee?->id,
            'employee_name' => $employee?->user?->name,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        if ($document) {
            $logData['document_id'] = $document->id;
            $logData['document_type'] = $document->documentType?->name;
            $logData['workflow_state'] = $document->getCurrentState();
            $logData['file_name'] = $document->file_name;
        }

        Log::info('Document operation performed', $logData);
    }

    /**
     * Clear caches related to document operations
     */
    protected function clearDocumentRelatedCaches($document, $employee)
    {
        if ($employee) {
            // Clear completion status cache
            $cacheKey = self::COMPLETION_STATUS_CACHE_PREFIX . "_{$employee->id}_*";
            Cache::forget($cacheKey);

            // Clear active study info cache
            $studyCacheKey = self::ACTIVE_STUDY_CACHE_PREFIX . "_{$employee->id}";
            Cache::forget($studyCacheKey);
        }
    }

    /**
     * Get document data for API responses
     */
    public function getDocumentApiData($document, $employee = null): array
    {
        return [
            'id' => $document->id,
            'file_name' => $document->file_name,
            'document_type' => $document->documentType->display_name,
            'status' => $document->getWorkflowStateInfo(),
            'uploaded_at' => $document->created_at->toISOString(),
            'can_download' => $this->canDownloadDocument($document, $employee),
            'can_delete' => $document->canBeDeleted(),
            'can_submit' => $document->canBeSubmitted(),
            'file_size' => $this->getDocumentFileSize($document),
            'file_url' => $this->getDocumentUrl($document),
        ];
    }

    /**
     * Get document file size
     */
    protected function getDocumentFileSize($document): ?string
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return null;
        }

        $bytes = Storage::disk('public')->size($document->file_path);
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get document URL
     */
    protected function getDocumentUrl($document): ?string
    {
        if (!$document->file_path) {
            return null;
        }

        return asset('storage/' . $document->file_path);
    }

    /**
     * Get document status label with color
     */
    protected function getDocumentStatusLabel($document): string
    {
        $stateInfo = $document->getWorkflowStateInfo();
        return $stateInfo['label'];
    }

    /**
     * Get current semester for an employee
     */
    protected function getCurrentSemester($employee): int
    {
        try {
            $activeStudy = $employee->studyCalendars()
                ->where('workflow_state', 5) // ACTIVE state
                ->latest()
                ->first();

            if (!$activeStudy) {
                $activeStudy = $employee->studyCalendars()
                    ->latest()
                    ->first();

                if (!$activeStudy) {
                    return 1;
                }
            }

            $startDate = Carbon::parse($activeStudy->study_start);
            $now = Carbon::now();
            $monthsDiff = $startDate->diffInMonths($now);

            return max(1, floor($monthsDiff / 6) + 1);
        } catch (\Exception $e) {
            Log::error('Error calculating current semester: ' . $e->getMessage(), [
                'employee_id' => $employee->id
            ]);
            return 1;
        }
    }

    /**
     * Convert workflow state to readable study status
     */
    protected function getStudyStatusFromWorkflowState(int $workflowState): string
    {
        return match($workflowState) {
            1 => 'draft',
            2 => 'pending',
            3 => 'approved',
            4 => 'rejected',
            5 => 'active',
            6 => 'leave',
            7 => 'finished',
            8 => 'drop_out',
            default => 'unknown'
        };
    }
}

