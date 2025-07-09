<?php

namespace App\Livewire\Administrations;

use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class Upload extends WorkflowComponent
{
    use WithFileUploads, WithPagination, HasDocumentManagement, HasCommonValidation;

    // Remove modal state properties since they're now handled by modular components
    public $selectedDocumentTypeId;
    public $availableDocumentTypes;
    public $selectedTransition;
    public $transitionComment = '';
    public $showWorkflowHistory = false;

    // Employee selection properties
    public $selectedEmployeeId;
    public $selectedEmployee;
    public $isNonLecturerRole = false;

    protected $listeners = [
        'employeeSelected' => 'handleEmployeeSelected',
        'employeeCleared' => 'handleEmployeeCleared',
        'document:uploaded' => 'handleDocumentUploaded',
        'document:deleted' => 'handleDocumentDeleted',
        'document:submitted' => 'handleDocumentSubmitted',
        'workflow:transition-applied' => 'handleTransitionApplied',
        'document-list:refresh' => 'refreshData',
        'document-submit' => 'handleDocumentSubmit'
    ];

    protected $rules = [];
    protected $messages = [];

    // Helper method to get approval document types
    protected function getapprovalDocumentTypes()
    {
        $approvalDocumentNames = array_column(
            DocumentTypeConstants::getByCategory('approval_documents'),
            'name'
        );

        return DocumentType::whereIn('name', $approvalDocumentNames)
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Check if current user has non-lecturer role
     */
    protected function checkIsNonLecturerRole(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->employee) {
            return false;
        }

        $nonLecturerRoles = [
            'hr_finance_staff',
            'head_of_hr_finance',
            'fri_vice_dean',
            'head_of_study_program',
            'head_of_research_group'
        ];

        return in_array($user->employee->role, $nonLecturerRoles);
    }

    /**
     * Get employee for document operations
     * For lecturers: returns their own employee record
     * For non-lecturers: returns selected employee
     */
    protected function getEmployeeForDocuments()
    {
        if ($this->isNonLecturerRole) {
            if (!$this->selectedEmployeeId) {
                throw new \Exception('Silakan pilih dosen terlebih dahulu.');
            }

            $employee = Employee::with(['user', 'studyPrograms'])->find($this->selectedEmployeeId);
            if (!$employee) {
                throw new \Exception('Data dosen tidak ditemukan.');
            }

            return $employee;
        }

        // For lecturers, use the standard getEmployee method
        return $this->getEmployee();
    }

    /**
     * Handle employee selection from employee finder
     */
    public function handleEmployeeSelected($data)
    {
        $this->selectedEmployeeId = $data['employeeId'];
        $this->selectedEmployee = Employee::with(['user', 'studyPrograms'])->find($data['employeeId']);

        // Force refresh of the component data
        $this->refreshData();
    }

    /**
     * Handle employee selection clearing
     */
    public function handleEmployeeCleared()
    {
        $this->selectedEmployeeId = null;
        $this->selectedEmployee = null;

        // Force refresh of the component data
        $this->refreshData();
    }

    /**
     * Refresh component data after employee selection or document operations
     */
    public function refreshData()
    {
        // Reset pagination to first page
        $this->resetPage();

        // Clear any cached data
        $this->dispatch('$refresh');
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

    /**
     * Handle document submission directly
     */
    public function handleDocumentSubmit($data)
    {
        try {
            $documentId = $data['documentId'] ?? null;
            if (!$documentId) {
                session()->flash('error', 'ID dokumen tidak valid.');
                return;
            }

            $document = ApprovalDocument::findOrFail($documentId);
            $employee = $this->getEmployeeForDocuments();

            if ((int) $document->employee_id !== (int) $employee->id) {
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

    public function handleTransitionApplied($data)
    {
        session()->flash('success', $data['message'] ?? 'Status dokumen berhasil diperbarui.');
        $this->refreshData();
    }

    // Modal Methods - Dispatch to modular components
    public function openUploadModal()
    {
        if (!$this->selectedDocumentTypeId) {
            session()->flash('error', 'Silakan pilih jenis dokumen terlebih dahulu.');
            return;
        }

        // Check if employee is selected for non-lecturer roles
        if ($this->isNonLecturerRole && !$this->selectedEmployeeId) {
            session()->flash('error', 'Silakan pilih dosen terlebih dahulu.');
            return;
        }

        try {
            $employee = $this->getEmployeeForDocuments();
            $this->dispatch('document-upload-modal:open', [
                'documentTypeId' => $this->selectedDocumentTypeId,
                'employeeId' => $employee->id
            ]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openViewModal($documentId)
    {
        $this->dispatch('document-view-modal:open', ['documentId' => $documentId]);
    }

    /**
     * Show document in modal view
     * This method is called from the documents table component
     */
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

    // Document Operations - Delegated to modular components via events
    public function submitDocument($documentId)
    {
        $this->handleDocumentSubmit(['documentId' => $documentId]);
    }

    public function deleteDocument($documentId)
    {
        $this->dispatch('document-delete', ['documentId' => $documentId]);
    }

    public function downloadDocument($documentId)
    {
        try {
            $document = ApprovalDocument::findOrFail($documentId);

            if (!$document->isInVerifiedState()) {
                session()->flash('error', 'Dokumen belum diverifikasi.');
                return;
            }

            $employee = $this->getEmployeeForDocuments();
            if ((int) $document->employee_id !== (int) $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
                return;
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                session()->flash('error', 'File dokumen tidak ditemukan.');
                return;
            }

            $filePath = Storage::disk('public')->path($document->file_path);
            if (!file_exists($filePath)) {
                session()->flash('error', 'File dokumen tidak ditemukan.');
                return;
            }
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
            $employee = $this->getEmployeeForDocuments();
            $documentTypeIds = $this->availableDocumentTypes->pluck('id')->toArray();

            return $this->getDocumentCompletionStatus($employee, $documentTypeIds, true, 'ApprovalDocument');
        } catch (\Exception $e) {
            return [
                'status' => 'Error',
                'details' => [],
                'completed_count' => 0,
                'total_count' => 0
            ];
        }
    }

    public function getActiveStudyInfo()
    {
        try {
            $employee = $this->getEmployeeForDocuments();
            return parent::getActiveStudyInfo($employee);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Livewire Update Methods
    public function updatedSelectedDocumentTypeId()
    {
        // Reserved for future enhancements - could trigger filtering
    }

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status dokumen berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->selectedDocumentTypeId = '';

        // Ensure availableDocumentTypes is always initialized
        try {
            $this->availableDocumentTypes = $this->getapprovalDocumentTypes();
        } catch (\Exception $e) {
            $this->availableDocumentTypes = collect();
        }

        // Check if current user is non-lecturer role
        $this->isNonLecturerRole = $this->checkIsNonLecturerRole();

        // If user is lecturer, auto-select their employee record
        if (!$this->isNonLecturerRole) {
            try {
                $employee = $this->getEmployee();
                $this->selectedEmployeeId = $employee->id;
                $this->selectedEmployee = $employee;
            } catch (\Exception $e) {
                // Handle case where lecturer doesn't have employee record
                Log::warning('Lecturer without employee record: ' . Auth::id());
            }
        }
    }

    /**
     * Implementation of abstract methods from WorkflowComponent
     */
    protected function getWorkflowModelClass(): string
    {
        return ApprovalDocument::class;
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

    public function render()
    {
        try {
            // Ensure availableDocumentTypes is always available
            if (!$this->availableDocumentTypes) {
                $this->availableDocumentTypes = $this->getapprovalDocumentTypes();
            }

            // Return empty state if non-lecturer hasn't selected an employee
            if ($this->isNonLecturerRole && !$this->selectedEmployeeId) {
                // Return empty paginated result
                $emptyPaginator = new LengthAwarePaginator(
                    collect(),
                    0,
                    10,
                    1,
                    ['path' => request()->url()]
                );

                return view('livewire.administrations.upload', [
                    'documents' => $emptyPaginator,
                    'completionStatus' => ['status' => 'Pilih Dosen', 'details' => []],
                    'activeStudyInfo' => null,
                    'canManageWorkflow' => false
                ]);
            }

            $employee = $this->getEmployeeForDocuments();
            $documentTypes = $this->getapprovalDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $ApprovalDocuments = ApprovalDocument::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.administrations.upload', [
                'documents' => $ApprovalDocuments,
                'completionStatus' => $this->getCompletionStatus(),
                'activeStudyInfo' => $this->getActiveStudyInfo(),
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            // Ensure availableDocumentTypes is available even in error case
            if (!$this->availableDocumentTypes) {
                $this->availableDocumentTypes = collect();
            }

            // Return empty paginated result to maintain consistency
            $emptyPaginator = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url()]
            );

            return view('livewire.administrations.upload', [
                'documents' => $emptyPaginator,
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'activeStudyInfo' => null,
                'canManageWorkflow' => false
            ]);
        }
    }
}
