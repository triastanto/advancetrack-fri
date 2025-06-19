<?php

namespace App\Livewire\Administrations;

use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use App\Traits\HasModal;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Verification extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation, HasModal;

    // Search and Filter Properties
    public $search = '';
    public $documentType = '';
    public $studyProgram = '';
    
    // Verification Modal Properties
    public $selectedDocument = null;
    public $verificationNote = '';
    
    // Workflow Modal Properties
    public $workflowModalOpen = false;
    public $workflowDocument = null;
    public $workflowTransitionId = null;
    public $workflowComment = '';
    
    // Workflow Properties
    public $selectedTransition = null;
    public $transitionComment = '';

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'documentType', 'studyProgram'];

    protected $listeners = [
        'document-verified' => 'handleDocumentVerified',
        'document-rejected' => 'handleDocumentRejected',
        'workflow-transition-applied' => 'handleTransitionApplied',
        'verification-list:refresh' => 'refreshData'
    ];

    public function render()
    {
        try {
            $documents = $this->getFilteredDocuments();
            $documentTypes = DocumentType::orderBy('display_name')->get();
            $studyPrograms = StudyProgram::all();

            return view('livewire.administrations.verification', [
                'documents' => $documents,
                'documentTypes' => $documentTypes,
                'studyPrograms' => $studyPrograms,
                'canManageWorkflow' => $this->canUserManageWorkflow(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Verification render: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memuat data verifikasi.');
            
            return view('livewire.administrations.verification', [
                'documents' => collect(),
                'documentTypes' => collect(),
                'studyPrograms' => collect(),
                'canManageWorkflow' => false,
            ]);
        }
    }

    /**
     * Get filtered documents using optimized queries
     */
    protected function getFilteredDocuments()
    {
        $query = Document::with(['employee.user', 'employee.studyPrograms', 'workflowHistory.user', 'documentType'])
            ->where(function($q) {
                $q->where('workflow_state', 2) // PENDING
                  ->orWhere('workflow_state', 4); // REJECTED (for resubmission)
            });

        // Apply search filters using trait validation
        if ($this->search && strlen($this->search) >= 2) {
            $query->whereHas('employee.user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->documentType) {
            $query->where('document_type_id', $this->documentType);
        }

        if ($this->studyProgram) {
            $query->whereHas('employee.studyPrograms', function ($q) {
                $q->where('study_programs.id', $this->studyProgram);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    // Event Handlers for Modular Components
    public function handleDocumentVerified($data)
    {
        session()->flash('success', $data['message'] ?? 'Dokumen berhasil diverifikasi.');
        $this->refreshData();
    }

    public function handleDocumentRejected($data)
    {
        session()->flash('error', $data['message'] ?? 'Dokumen berhasil ditolak.');
        $this->refreshData();
    }

    public function handleTransitionApplied($data)
    {
        session()->flash('success', $data['message'] ?? 'Status dokumen berhasil diperbarui.');
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    // Modal Operations - Delegate to modular components
    public function openViewModal($documentId)
    {
        $this->dispatch('document-view-modal:open', ['documentId' => $documentId]);
    }

    public function showDocument($documentId)
    {
        $this->openViewModal($documentId);
    }

    public function openVerificationModal($documentId)
    {
        $this->selectedDocument = Document::with(['employee.user', 'documentType'])->find($documentId);
        $this->verificationNote = '';
        $this->openModal(['document' => $this->selectedDocument]);
    }

    public function closeVerificationModal()
    {
        $this->closeModal();
        $this->selectedDocument = null;
        $this->verificationNote = '';
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->workflowDocument = Document::with(['employee.user', 'documentType'])->find($documentId);
        $this->workflowTransitionId = $transitionId;
        $this->workflowComment = '';
        $this->workflowModalOpen = true;
    }

    public function closeWorkflowModal()
    {
        $this->workflowModalOpen = false;
        $this->workflowDocument = null;
        $this->workflowTransitionId = null;
        $this->workflowComment = '';
    }

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status verifikasi berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'success';
    }

    // Implementation of abstract methods from WorkflowComponent
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
