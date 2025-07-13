<?php

namespace App\Services;

use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Support\Facades\Log;

class StudyCalendarRequirementsService
{
    /**
     * Get requirements status for an employee
     */
    public function getRequirementsStatus(int $employeeId): array
    {
        return $this->calculateRequirementsStatus($employeeId);
    }

    /**
     * Calculate requirements status without caching
     */
    private function calculateRequirementsStatus(int $employeeId): array
    {
        try {
            // Get study requirement document type names
            $studyRequirementNames = DocumentTypeConstants::getStudyRequirementNames();

            // Check Academic Documents (Study Requirements)
            $academicDocuments = AcademicDocument::where('employee_id', $employeeId)
                ->whereHas('documentType', function($query) use ($studyRequirementNames) {
                    $query->whereIn('name', $studyRequirementNames);
                })
                ->with(['documentType'])
                ->get();

            $verifiedAcademicDocs = $academicDocuments->where('workflow_state', 3)->count();
            $totalAcademicDocs = $academicDocuments->count();

            // Get approval document type names and IDs
            $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $approvalTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

            // Check Approval Documents
            $approvalDocuments = ApprovalDocument::where('employee_id', $employeeId)
                ->whereIn('document_type_id', $approvalTypeIds)
                ->with(['documentType'])
                ->get();
                
            $approvalTotal = count($approvalTypeIds);
            $approvalApproved = $approvalDocuments->where('workflow_state', 4)->count();
            $approvalDocument = $approvalDocuments->first();
            $allApprovalDocumentsApproved = $approvalApproved === $approvalTotal && $approvalTotal > 0;

            return [
                'academic_documents' => [
                    'verified' => $verifiedAcademicDocs,
                    'total' => $totalAcademicDocs,
                    'complete' => $verifiedAcademicDocs === $totalAcademicDocs && $totalAcademicDocs > 0
                ],
                'approval_document' => [
                    'exists' => $approvalDocument !== null,
                    'approved' => $allApprovalDocumentsApproved,
                    'approved_count' => $approvalApproved,
                    'total' => $approvalTotal
                ],
                'all_requirements_met' => $verifiedAcademicDocs === $totalAcademicDocs &&
                                        $totalAcademicDocs > 0 &&
                                        $approvalApproved === $approvalTotal && $approvalTotal > 0
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating requirements status: ' . $e->getMessage(), [
                'employee_id' => $employeeId
            ]);
            
            return [
                'academic_documents' => ['verified' => 0, 'total' => 0, 'complete' => false],
                'approval_document' => ['exists' => false, 'approved' => false, 'approved_count' => 0, 'total' => 0],
                'all_requirements_met' => false
            ];
        }
    }

    /**
     * Check if study can be started
     */
    public function canStartStudy(array $requirementsStatus): bool
    {
        return $requirementsStatus['all_requirements_met'];
    }

    /**
     * Get missing requirements summary
     */
    public function getMissingRequirements(array $requirementsStatus): array
    {
        $missing = [];

        if (!$requirementsStatus['academic_documents']['complete']) {
            $missingCount = $requirementsStatus['academic_documents']['total'] - $requirementsStatus['academic_documents']['verified'];
            $missing[] = "{$missingCount} dokumen persyaratan belum diverifikasi";
        }

        if (!$requirementsStatus['approval_document']['approved']) {
            $approvedCount = $requirementsStatus['approval_document']['approved_count'] ?? 0;
            $totalCount = $requirementsStatus['approval_document']['total'] ?? 0;
            $missingCount = $totalCount - $approvedCount;
            $missing[] = "{$missingCount} dari {$totalCount} dokumen persetujuan belum disetujui (semua {$totalCount} dokumen harus disetujui)";
        }

        return $missing;
    }

    /**
     * Get academic documents with workflow states
     */
    public function getAcademicDocumentsWithStates(int $employeeId): array
    {
        try {
            $studyRequirementNames = DocumentTypeConstants::getStudyRequirementNames();
            
            $academicDocuments = AcademicDocument::where('employee_id', $employeeId)
                ->whereHas('documentType', function($query) use ($studyRequirementNames) {
                    $query->whereIn('name', $studyRequirementNames);
                })
                ->with(['documentType'])
                ->get();

            return $academicDocuments->map(function($document) {
                $documentType = DocumentTypeConstants::getByName($document->documentType->name ?? '');
                return [
                    'id' => $document->id,
                    'name' => $documentType['display_name'] ?? $document->documentType->name ?? 'Unknown Document',
                    'workflow_state' => $document->workflow_state,
                    'workflow_state_label' => $this->getWorkflowStateLabel($document->workflow_state),
                    'workflow_state_color' => $this->getWorkflowStateColor($document->workflow_state),
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting academic documents with states: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get approval documents with workflow states
     */
    public function getApprovalDocumentsWithStates(int $employeeId): array
    {
        try {
            $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $approvalTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

            $approvalDocuments = ApprovalDocument::where('employee_id', $employeeId)
                ->whereIn('document_type_id', $approvalTypeIds)
                ->with(['documentType'])
                ->get();

            return $approvalDocuments->map(function($document) {
                $documentType = DocumentTypeConstants::getByName($document->documentType->name ?? '');
                return [
                    'id' => $document->id,
                    'name' => $documentType['display_name'] ?? $document->documentType->name ?? 'Unknown Document',
                    'workflow_state' => $document->workflow_state,
                    'workflow_state_label' => $this->getWorkflowStateLabel($document->workflow_state),
                    'workflow_state_color' => $this->getWorkflowStateColor($document->workflow_state),
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting approval documents with states: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get complete study requirement documents with status
     */
    public function getCompleteStudyRequirementDocuments(int $employeeId): array
    {
        try {
            $studyRequirementNames = DocumentTypeConstants::getStudyRequirementNames();
            $documentTypeIds = DocumentType::whereIn('name', $studyRequirementNames)->pluck('id', 'name');
            
            // Get uploaded documents for this employee
            $uploadedDocuments = AcademicDocument::where('employee_id', $employeeId)
                ->whereIn('document_type_id', $documentTypeIds->values())
                ->with(['documentType'])
                ->get()
                ->keyBy('documentType.name');

            $completeDocuments = [];
            
            foreach ($studyRequirementNames as $documentName) {
                $documentType = DocumentTypeConstants::getByName($documentName);
                $uploadedDocument = $uploadedDocuments->get($documentName);
                
                if ($uploadedDocument) {
                    // Document is uploaded
                    $completeDocuments[] = [
                        'name' => $documentType['display_name'] ?? $documentName,
                        'type' => $documentName,
                        'status' => 'uploaded',
                        'workflow_state' => $uploadedDocument->workflow_state,
                        'workflow_state_label' => $this->getWorkflowStateLabel($uploadedDocument->workflow_state),
                        'workflow_state_color' => $this->getWorkflowStateColor($uploadedDocument->workflow_state),
                        'uploaded_at' => $uploadedDocument->created_at,
                        'document_id' => $uploadedDocument->id,
                        'is_verified' => $uploadedDocument->workflow_state === 3,
                        'is_approved' => $uploadedDocument->workflow_state === 4,
                    ];
                } else {
                    // Document is not uploaded
                    $completeDocuments[] = [
                        'name' => $documentType['display_name'] ?? $documentName,
                        'type' => $documentName,
                        'status' => 'not_uploaded',
                        'workflow_state' => null,
                        'workflow_state_label' => 'Belum Diunggah',
                        'workflow_state_color' => 'gray',
                        'uploaded_at' => null,
                        'document_id' => null,
                        'is_verified' => false,
                        'is_approved' => false,
                    ];
                }
            }

            return $completeDocuments;
        } catch (\Exception $e) {
            Log::error('Error getting complete study requirement documents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get complete approval documents with status
     */
    public function getCompleteApprovalDocuments(int $employeeId): array
    {
        try {
            $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $documentTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id', 'name');
            
            // Get uploaded documents for this employee
            $uploadedDocuments = ApprovalDocument::where('employee_id', $employeeId)
                ->whereIn('document_type_id', $documentTypeIds->values())
                ->with(['documentType'])
                ->get()
                ->keyBy('documentType.name');

            $completeDocuments = [];
            
            foreach ($approvalTypeNames as $documentName) {
                $documentType = DocumentTypeConstants::getByName($documentName);
                $uploadedDocument = $uploadedDocuments->get($documentName);
                
                if ($uploadedDocument) {
                    // Document is uploaded
                    $completeDocuments[] = [
                        'name' => $documentType['display_name'] ?? $documentName,
                        'type' => $documentName,
                        'status' => 'uploaded',
                        'workflow_state' => $uploadedDocument->workflow_state,
                        'workflow_state_label' => $this->getWorkflowStateLabel($uploadedDocument->workflow_state),
                        'workflow_state_color' => $this->getWorkflowStateColor($uploadedDocument->workflow_state),
                        'uploaded_at' => $uploadedDocument->created_at,
                        'document_id' => $uploadedDocument->id,
                        'is_verified' => $uploadedDocument->workflow_state === 3,
                        'is_approved' => $uploadedDocument->workflow_state === 4,
                    ];
                } else {
                    // Document is not uploaded
                    $completeDocuments[] = [
                        'name' => $documentType['display_name'] ?? $documentName,
                        'type' => $documentName,
                        'status' => 'not_uploaded',
                        'workflow_state' => null,
                        'workflow_state_label' => 'Belum Diunggah',
                        'workflow_state_color' => 'gray',
                        'uploaded_at' => null,
                        'document_id' => null,
                        'is_verified' => false,
                        'is_approved' => false,
                    ];
                }
            }

            return $completeDocuments;
        } catch (\Exception $e) {
            Log::error('Error getting complete approval documents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get workflow state label
     */
    private function getWorkflowStateLabel(int $state): string
    {
        return match($state) {
            1 => 'Draft',
            2 => 'Pending',
            3 => 'Verified',
            4 => 'Approved',
            5 => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Get workflow state color
     */
    private function getWorkflowStateColor(int $state): string
    {
        return match($state) {
            1 => 'gray',
            2 => 'yellow',
            3 => 'green',
            4 => 'green',
            5 => 'red',
            default => 'gray'
        };
    }
} 