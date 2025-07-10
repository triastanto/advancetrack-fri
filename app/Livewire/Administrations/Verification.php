<?php

namespace App\Livewire\Administrations;

use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use App\Traits\HasModal;
use Livewire\WithPagination;
use App\Models\AcademicDocument;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Constants\DocumentTypeConstants;
use App\Services\Workflow\WorkflowDefinition;

class Verification extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation, HasModal;

    // Search and Filter Properties
    public $search = '';
    public $documentType = '';
    public $statusFilter = '';

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
    protected $queryString = ['search', 'documentType', 'statusFilter'];

    // Add a property to track if we should open the modal
    public $shouldOpenDocumentModal = false;
    public $documentIdToOpen = null;

    public $workflowStates = [];

    protected $listeners = [
        'document-verified' => 'handleDocumentVerified',
        'document-rejected' => 'handleDocumentRejected',
        'workflow-transition-applied' => 'handleTransitionApplied',
        'verification-list:refresh' => 'refreshData',
        'open-verification-modal' => 'openVerificationModal'
    ];

    public function render()
    {
        try {
            $documents = $this->getFilteredDocuments();
            // Only show document types from allowed categories
            $allowedTypeNames = array_merge(
                DocumentTypeConstants::getStudyRequirementNames(),
                DocumentTypeConstants::getSemesterDocumentNames(),
                DocumentTypeConstants::getFinalDocumentNames(),
                ['additional']
            );
            $documentTypes = DocumentType::whereIn('name', $allowedTypeNames)
                ->orderBy('display_name')->get();

            // Group document types by category for the select
            $categoryLabels = [
                'study_requirements' => 'Persyaratan Studi Lanjut',
                'semester_documents' => 'Laporan Per Semester',
                'final_documents' => 'Laporan Akhir & Kelulusan',
                'additional_documents' => 'Dokumen Tambahan',
            ];
            $groupedDocumentTypes = [];
            foreach ($categoryLabels as $catKey => $catLabel) {
                $names = [];
                if ($catKey === 'study_requirements') $names = DocumentTypeConstants::getStudyRequirementNames();
                if ($catKey === 'semester_documents') $names = DocumentTypeConstants::getSemesterDocumentNames();
                if ($catKey === 'final_documents') $names = DocumentTypeConstants::getFinalDocumentNames();
                if ($catKey === 'additional_documents') $names = ['additional'];
                $grouped = $documentTypes->whereIn('name', $names);
                if ($grouped->isNotEmpty()) {
                    $groupedDocumentTypes[$catLabel] = $grouped;
                }
            }

            // Check if we should open the modal after rendering
            if ($this->shouldOpenDocumentModal && $this->documentIdToOpen) {
                // Reset flags to prevent reopening on refresh
                $this->shouldOpenDocumentModal = false;
                $openDocumentId = $this->documentIdToOpen;
                $this->documentIdToOpen = null;

                // Use dispatch to ensure component is fully rendered before opening modal
                $this->dispatch('open-verification-modal', ['documentId' => $openDocumentId]);
            }

            return view('livewire.administrations.verification', [
                'documents' => $documents,
                'documentTypes' => $documentTypes,
                'groupedDocumentTypes' => $groupedDocumentTypes,
                'canManageWorkflow' => $this->canUserManageWorkflow(),
                'userRoles' => Auth::user()->roles,
                'workflowStates' => $this->workflowStates,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Verification render: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memuat data verifikasi.');

            return view('livewire.administrations.verification', [
                'documents' => collect(),
                'documentTypes' => collect(),
                'groupedDocumentTypes' => [],
                'canManageWorkflow' => false,
                'workflowStates' => $this->workflowStates,
            ]);
        }
    }

    /**
     * Get filtered documents using optimized queries
     */
    protected function getFilteredDocuments()
    {
        $query = AcademicDocument::with(['employee.user', 'employee.studyPrograms', 'workflowHistory.user', 'documentType']);

        // Apply search filters using trait validation
        if ($this->search && strlen($this->search) >= 2) {
            $query->whereHas('employee.user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->documentType) {
            $query->where('document_type_id', $this->documentType);
        }

        if ($this->statusFilter) {
            $query->where('workflow_state', $this->statusFilter);
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
        // Handle both direct calls and calls from event
        if (is_array($documentId) && isset($documentId['documentId'])) {
            $documentId = $documentId['documentId'];
        }

        $this->selectedDocument = AcademicDocument::with(['employee.user', 'documentType'])->find($documentId);
        $this->verificationNote = '';
        $this->isModalOpen = true;
    }

    public function closeVerificationModal()
    {
        $this->isModalOpen = false;
        $this->selectedDocument = null;
        $this->verificationNote = '';
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->workflowDocument = AcademicDocument::with(['employee.user', 'documentType'])->find($documentId);
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

    /**
     * Verify the currently selected document
     */
    public function verifyDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        try {
            // Find the verify transition ID
            $transitions = $this->selectedDocument->getFormattedTransitions();
            $verifyTransitionId = null;

            foreach ($transitions as $transition) {
                if (strtoupper($transition['name']) == 'VERIFY') {
                    $verifyTransitionId = $transition['id'];
                    break;
                }
            }

            if ($verifyTransitionId) {
                // Apply the transition using the document's own method
                try {
                    // Build context for transition
                    $context = [
                        'user_id' => Auth::id(),
                        'comment' => $this->verificationNote,
                        'timestamp' => now(),
                        'user_name' => Auth::user()->name,
                    ];

                    // Apply the transition directly on the document
                    $this->selectedDocument->applyTransition($verifyTransitionId, $context);

                    $this->closeVerificationModal();
                    $this->dispatch('document-verified', ['message' => 'Dokumen berhasil diverifikasi.']);
                } catch (\Exception $e) {
                    session()->flash('error', 'Error saat memverifikasi dokumen: ' . $e->getMessage());
                }
            } else {
                session()->flash('error', 'Transisi verifikasi tidak ditemukan.');
                $this->closeVerificationModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error saat memverifikasi dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Reject the currently selected document
     */
    public function rejectDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        try {
            // Find the reject transition ID
            $transitions = $this->selectedDocument->getFormattedTransitions();
            $rejectTransitionId = null;

            foreach ($transitions as $transition) {
                if (                    strtoupper($transition['label']) == 'REJECT') {
                    $rejectTransitionId = $transition['id'];
                    break;
                }
            }

            if ($rejectTransitionId) {
                // Apply the transition using the document's own method
                try {
                    // Build context for transition
                    $context = [
                        'user_id' => Auth::id(),
                        'comment' => $this->verificationNote,
                        'timestamp' => now(),
                        'user_name' => Auth::user()->name,
                    ];

                    // Apply the transition directly on the document
                    $this->selectedDocument->applyTransition($rejectTransitionId, $context);

                    $this->closeVerificationModal();
                    $this->dispatch('document-rejected', ['message' => 'Dokumen berhasil ditolak.']);
                } catch (\Exception $e) {
                    session()->flash('error', 'Error saat menolak dokumen: ' . $e->getMessage());
                }
            } else {
                session()->flash('error', 'Transisi penolakan tidak ditemukan.');
                $this->closeVerificationModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error saat menolak dokumen: ' . $e->getMessage());
        }
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

    /**
     * Extends the parent mount method to also handle opening documents from email links
     */
    public function mount(...$parameters)
    {
        // Call the parent mount method first
        parent::mount(...$parameters);

        // Check if document_id is in the request and select the document if present
        if (request()->has('document_id')) {
            $documentId = request()->get('document_id');
            $document = AcademicDocument::find($documentId);

            if ($document) {
                // Schedule the modal to be opened after the component is fully rendered
                $this->shouldOpenDocumentModal = true;
                $this->documentIdToOpen = $document->id;

                // Add a flash message to indicate document was automatically selected
                session()->flash('success', 'Dokumen dari notifikasi email telah dipilih untuk verifikasi.');
            }
        }
        $this->workflowStates = WorkflowDefinition::getAllStates('verification_by_staff');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'documentType', 'statusFilter']);
        $this->resetPage();
    }
}
