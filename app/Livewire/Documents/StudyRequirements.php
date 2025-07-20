<?php

namespace App\Livewire\Documents;

use App\Models\AcademicDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class StudyRequirements extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $availableDocumentTypes;
    public $selectedDocumentTypeId;
    public $selectedTransition;
    public $transitionComment = '';

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

    // Helper method to get study requirements document types
    protected function getStudyRequirementDocumentTypes()
    {
        $studyRequirementNames = array_column(
            DocumentTypeConstants::getByCategory('study_requirements'),
            'name'
        );

        // Ensure 'additional_study_requirement' is included
        if (!in_array('additional_study_requirement', $studyRequirementNames)) {
            $studyRequirementNames[] = 'additional_study_requirement';
        }

        return DocumentType::whereIn('name', $studyRequirementNames)
            ->orderBy('display_name')
            ->get();
    }

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->availableDocumentTypes = $this->getStudyRequirementDocumentTypes();
        $this->selectedDocumentTypeId = '';
    }

    // Event Handlers for Modular Components
    public function handleDocumentUploaded($data)
    {
        Log::info('handleDocumentUploaded called with data:', $data); // Debug log
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

            $document = AcademicDocument::findOrFail($documentId);
            $employee = $this->getEmployee();

            // Enhanced debug logging
            Log::info('Document submit attempt', [
                'document_id' => $document->id,
                'document_employee_id' => $document->employee_id,
                'current_employee_id' => $employee->id,
                'current_user_id' => Auth::id(),
                'current_user_name' => Auth::user()->name,
                'current_user_email' => Auth::user()->email,
                'document_file_name' => $document->file_name,
                'document_state' => $document->workflow_state,
                'document_owner_user_id' => $document->employee->user_id ?? null,
                'document_owner_name' => $document->employee->user->name ?? 'Unknown',
                'current_employee_user_id' => $employee->user_id ?? null,
                'current_employee_nidn' => $employee->nidn ?? null,
                'document_employee_nidn' => $document->employee->nidn ?? null,
            ]);

            // Check document ownership
            if ($document->employee_id !== $employee->id) {
                Log::warning('Document access denied - ownership mismatch', [
                    'document_id' => $document->id,
                    'document_employee_id' => $document->employee_id,
                    'current_employee_id' => $employee->id,
                    'document_owner_user_id' => $document->employee->user_id ?? null,
                    'current_user_id' => Auth::id(),
                    'document_owner_name' => $document->employee->user->name ?? 'Unknown',
                    'current_user_name' => $employee->user->name ?? 'Unknown',
                    'document_owner_nidn' => $document->employee->nidn ?? 'Unknown',
                    'current_employee_nidn' => $employee->nidn ?? 'Unknown',
                ]);
                session()->flash('error', 'Anda tidak memiliki akses untuk dokumen ini.');
                return;
            }

            if ($document->workflow_state !== 1) { // DRAFT
                session()->flash('error', 'Dokumen tidak dapat dikirim karena bukan dalam status draft.');
                return;
            }

            // Get available transitions and find submit transition
            $availableTransitions = $document->getAvailableTransitions();
            Log::info('Available transitions for document:', [
                'document_id' => $document->id,
                'current_state' => $document->workflow_state,
                'transitions' => $availableTransitions
            ]);

            $submitTransitionId = null;
            foreach ($availableTransitions as $transitionId => $transition) {
                if ($transition['name'] === 'SUBMIT') {
                    $submitTransitionId = $transitionId;
                    break;
                }
            }

            if (!$submitTransitionId) {
                Log::warning('Submit transition not found', [
                    'document_id' => $document->id,
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

            $document->applyTransition($submitTransitionId, $context);

            session()->flash('message', 'Dokumen berhasil dikirim untuk verifikasi.');
            $this->refreshData();

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

            $document = AcademicDocument::findOrFail($documentId);
            $employee = $this->getEmployee();

            // Check document ownership
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
                'category' => 'study-requirements'
            ]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openViewModal($documentId)
    {
        $this->dispatch('document-view-modal:open', [
            'documentId' => $documentId,
            'documentModel' => 'AcademicDocument',
            'category' => 'study-requirements'
        ]);
    }

    public function showDocument($documentId)
    {
        $this->openViewModal($documentId);
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->dispatch('workflow-transition-modal:open', [
            'documentId' => $documentId,
            'transitionId' => $transitionId,
            'modelType' => 'academic_document'
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

    public function downloadDocument($documentId)
    {
        try {
            $document = AcademicDocument::findOrFail($documentId);

            if (!$document->isInVerifiedState()) {
                session()->flash('error', 'Dokumen belum diverifikasi.');
                return;
            }

            $employee = $this->getEmployee();
            if ((int) $document->employee_id !== (int) $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
                return;
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                session()->flash('error', 'File dokumen tidak ditemukan.');
                return;
            }

            $filePath = Storage::disk('public')->path($document->file_path);
            return response()->download($filePath, $document->file_name);
        } catch (\Exception $e) {
            Log::error('Error in downloadDocument: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengunduh dokumen: ' . $e->getMessage());
        }
    }

    // Status and Info Methods - Using traits
    public function getCompletionStatus()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypeIds = $this->availableDocumentTypes->pluck('id')->toArray();

            return $this->getDocumentCompletionStatus($employee, $documentTypeIds, true, 'AcademicDocument');
        } catch (\Exception $e) {
            return [
                'status' => 'Error',
                'details' => [],
                'completed_count' => 0,
                'total_count' => 0
            ];
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

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status dokumen persyaratan studi lanjut berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    /**
     * Debug method to check user and employee relationship integrity
     */
    public function debugUserEmployeeRelationship()
    {
        try {
            $user = Auth::user();
            $employee = $this->getEmployee();

            Log::info('User-Employee relationship debug', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'employee_id' => $employee->id,
                'employee_user_id' => $employee->user_id,
                'employee_nidn' => $employee->nidn,
                'employee_role' => $employee->role,
                'relationship_valid' => $employee->user_id === $user->id,
            ]);

            return [
                'user' => $user,
                'employee' => $employee,
                'relationship_valid' => $employee->user_id === $user->id,
            ];
        } catch (\Exception $e) {
            Log::error('Error debugging user-employee relationship: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getStudyRequirementDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $studyRequirements = AcademicDocument::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.study-requirements', [
                'documents' => $studyRequirements,
                'activeStudyInfo' => $this->getActiveStudyInfoForEmployee(),
                'completionStatus' => $this->getCompletionStatus(),
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            // Return empty paginated result to maintain consistency
            $emptyPaginator = new LengthAwarePaginator(
                [],
                0,
                10,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );

            return view('livewire.documents.study-requirements', [
                'documents' => $emptyPaginator,
                'activeStudyInfo' => null,
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'canManageWorkflow' => false
            ]);
        }
    }

    // Implementation of abstract methods from WorkflowComponent
    protected function getWorkflowModelClass(): string
    {
        return AcademicDocument::class;
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
