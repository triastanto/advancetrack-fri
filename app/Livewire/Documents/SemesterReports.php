<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class SemesterReports extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $availableDocumentTypes;
    public $selectedDocumentTypeId;
    public $selectedTransition;
    public $transitionComment = '';
    public $selectedSemester = '';
    
    // Modal states for backward compatibility with Blade views
    public $uploadModalOpen = false;
    public $viewModalOpen = false;
    public $workflowModalOpen = false;
    public $currentDocument;
    public $fileName = '';
    public $documentFile;

    protected $listeners = [
        'document:uploaded' => 'handleDocumentUploaded',
        'document:deleted' => 'handleDocumentDeleted', 
        'document:submitted' => 'handleDocumentSubmitted',
        'workflow:transition-applied' => 'handleTransitionApplied',
        'document-submit' => 'handleDocumentSubmit',
        'document-delete' => 'handleDocumentDelete',
        'document-list:refresh' => 'refreshData'
    ];

    protected $rules = [];
    protected $messages = [];

    protected function getSemesterReportDocumentTypes()
    {
        $semesterDocumentNames = array_column(
            DocumentTypeConstants::getByCategory('semester_documents'),
            'name'
        );

        return DocumentType::whereIn('name', $semesterDocumentNames)
            ->orderBy('display_name')
            ->get();
    }

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->availableDocumentTypes = $this->getSemesterReportDocumentTypes();
        $this->selectedDocumentTypeId = '';
        $this->selectedSemester = $this->getCurrentSemester();
    }

    // Event Handlers for Modular Components
    public function handleDocumentUploaded($data)
    {
        session()->flash('message', $data['message'] ?? 'Dokumen berhasil diunggah.');
        $this->refreshData();
    }

    public function handleDocumentDeleted($data)
    {
        session()->flash('message', $data['message'] ?? 'Dokumen berhasil dihapus.');
        $this->refreshData();
    }

    public function handleDocumentSubmitted($data)
    {
        session()->flash('message', $data['message'] ?? 'Dokumen berhasil dikirim.');
        $this->refreshData();
    }

    public function handleTransitionApplied($data)
    {
        session()->flash('success', $data['message'] ?? 'Status dokumen berhasil diperbarui.');
        $this->refreshData();
    }

    // Event Handlers for Dispatched Events
    public function handleDocumentSubmit($data)
    {
        try {
            $documentId = $data['documentId'] ?? null;
            if (!$documentId) {
                session()->flash('error', 'ID dokumen tidak valid.');
                return;
            }

            $document = Document::findOrFail($documentId);
            $employee = $this->getEmployee();
            
            if ($document->employee_id !== $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk dokumen ini.');
                return;
            }

            if ($document->workflow_state !== 1) { // DRAFT
                session()->flash('error', 'Dokumen tidak dapat dikirim karena bukan dalam status draft.');
                return;
            }

            // Get available transitions and find submit transition
            $availableTransitions = $document->getAvailableTransitions();
            
            // Find the SUBMIT transition ID and data
            $submitTransitionId = null;
            $submitTransition = null;
            
            foreach ($availableTransitions as $transitionId => $transitionData) {
                if ($transitionData['name'] === 'SUBMIT') {
                    $submitTransitionId = $transitionId;
                    $submitTransition = $transitionData;
                    break;
                }
            }
            
            if (!$submitTransitionId || !$submitTransition) {
                Log::error('Submit transition not found for document', [
                    'document_id' => $documentId,
                    'available_transitions' => $availableTransitions
                ]);
                session()->flash('error', 'Transisi submit tidak tersedia.');
                return;
            }

            // Apply the transition
            $context = [
                'user_id' => Auth::id(),
                'comment' => 'Dokumen dikirim untuk verifikasi',
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            try {
                $document->applyTransition($submitTransitionId, $context);
                session()->flash('message', 'Dokumen berhasil dikirim untuk verifikasi.');
                $this->refreshData();
            } catch (\Exception $transitionError) {
                Log::error('Error applying transition', [
                    'document_id' => $documentId,
                    'transition_id' => $submitTransitionId,
                    'context' => $context,
                    'error' => $transitionError->getMessage(),
                    'trace' => $transitionError->getTraceAsString()
                ]);
                session()->flash('error', 'Gagal mengirim dokumen: ' . $transitionError->getMessage());
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function handleDocumentDelete($data)
    {
        try {
            $documentId = $data['documentId'] ?? null;
            if (!$documentId) {
                session()->flash('error', 'ID dokumen tidak valid.');
                return;
            }

            $document = Document::findOrFail($documentId);
            $employee = $this->getEmployee();
            
            if ($document->employee_id !== $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk dokumen ini.');
                return;
            }

            if ($document->workflow_state !== 1) { // DRAFT
                session()->flash('error', 'Hanya dokumen dengan status draft yang dapat dihapus.');
                return;
            }

            // Delete file from storage
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();
            
            session()->flash('message', 'Dokumen berhasil dihapus.');
            $this->refreshData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->resetPage();
        // Livewire will automatically re-render the component
    }

    // Modal Methods - Dispatch to modular components
    public function openUploadModal($documentTypeId = null)
    {
        try {
            $employee = $this->getEmployee();
            $this->dispatch('document-upload-modal:open', [
                'documentTypeId' => $documentTypeId ?: $this->selectedDocumentTypeId,
                'employeeId' => $employee->id,
                'category' => 'semester-reports',
                'semester' => $this->selectedSemester
            ]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openViewModal($documentId)
    {
        $this->dispatch('document-view-modal:open', ['documentId' => $documentId]);
    }

    public function showDocument($documentId)
    {
        $this->openViewModal($documentId);
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->dispatch('workflow-transition-modal:open', [
            'documentId' => $documentId,
            'transitionId' => $transitionId
        ]);
    }

    // Document Operations - Handle directly instead of dispatching to self
    public function submitDocument($documentId)
    {
        $this->handleDocumentSubmit(['documentId' => $documentId]);
    }

    public function deleteDocument($documentId)
    {
        $this->handleDocumentDelete(['documentId' => $documentId]);
    }

    // Status and Info Methods - Using traits  
    public function getCompletionStatus()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypeIds = $this->availableDocumentTypes->pluck('id')->toArray();
            
            // For semester reports, get semester-aware completion status
            return $this->getSemesterAwareCompletionStatus($employee, $documentTypeIds);
        } catch (\Exception $e) {
            Log::error('Error in SemesterReports getCompletionStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
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
     * Get completion status that includes all semester documents
     */
    protected function getSemesterAwareCompletionStatus($employee, $documentTypeIds): array
    {
        try {
            // Get all documents for the employee and document types (including all semesters)
            $allDocuments = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->get();
            
            $documentTypes = DocumentType::whereIn('id', $documentTypeIds)->get();
            
            $completionDetails = [];
            $allCompleted = true;
            $completedCount = 0;

            foreach ($documentTypes as $docType) {
                // Get all documents for this document type across all semesters
                $documentsForType = $allDocuments->where('document_type_id', $docType->id);
                
                // For display purposes, we'll use the latest document as the primary one
                // but we'll also store all documents for semester-specific filtering
                $latestDocument = $documentsForType->sortByDesc('created_at')->first();
                
                if ($latestDocument) {
                    $stateInfo = $latestDocument->getWorkflowStateInfo();
                    $isCompleted = $latestDocument->isInVerifiedState();
                    
                    $completionDetails[$docType->name] = [
                        'uploaded' => true,
                        'state_info' => $stateInfo,
                        'document' => $latestDocument,
                        'uploaded_at' => $latestDocument->created_at->format('d M Y'),
                        'completed' => $isCompleted,
                        'all_documents' => $documentsForType // Keep as collection for proper filtering
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
                        'completed' => false,
                        'all_documents' => collect() // Empty collection for consistency
                    ];
                    $allCompleted = false;
                }
            }

            return [
                'status' => $allCompleted ? 'Lengkap' : 'Belum Lengkap',
                'details' => $completionDetails,
                'completed_count' => $completedCount,
                'total_count' => count($documentTypes)
            ];

        } catch (\Exception $e) {
            Log::error('Error getting semester-aware completion status: ' . $e->getMessage(), [
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

    public function getCurrentSemester()
    {
        try {
            $employee = $this->getEmployee();
            
            $activeStudy = $employee->studyCalendars()
                ->where('study_status', 'active')
                ->latest()
                ->first();

            if (!$activeStudy) {
                return 1;
            }

            $startDate = Carbon::parse($activeStudy->study_start);
            $now = Carbon::now();
            $monthsDiff = $startDate->diffInMonths($now);
            
            return max(1, floor($monthsDiff / 6) + 1);
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function getActiveStudyInfoForEmployee()
    {
        try {
            $employee = $this->getEmployee();
            return $this->getActiveStudyInfo($employee);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getSuccessMessage(): string
    {
        return 'Status laporan semester berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getSemesterReportDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $semesterReports = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('semester', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.semester-reports', [
                'documents' => $semesterReports,
                'completionStatus' => $this->getCompletionStatus(),
                'activeStudyInfo' => $this->getActiveStudyInfoForEmployee(),
                'currentSemester' => $this->getCurrentSemester(),
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            $emptyPaginator = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url()]
            );

            return view('livewire.documents.semester-reports', [
                'documents' => $emptyPaginator,
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'activeStudyInfo' => null,
                'currentSemester' => 1,
                'canManageWorkflow' => false
            ]);
        }
    }

    // Required abstract method implementations
    protected function getWorkflowModelClass(): string
    {
        return Document::class;
    }

    protected function getWorkflowDocumentPropertyName(): string
    {
        return 'currentDocument';
    }

    protected function getWorkflowCommentPropertyName(): string
    {
        return 'transitionComment';
    }

    protected function getWorkflowTransitionPropertyName(): string
    {
        return 'selectedTransition';
    }
}
