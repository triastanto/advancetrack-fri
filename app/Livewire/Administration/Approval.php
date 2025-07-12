<?php

namespace App\Livewire\Administration;

use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use App\Traits\HasModal;
use Livewire\WithPagination;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Constants\DocumentTypeConstants;
use App\Services\Workflow\WorkflowDefinition;

class Approval extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation, HasModal;

    // Search and Filter Properties
    public $search = '';
    public $documentType = '';
    public $statusFilter = '';

    // Approval Modal Properties
    public $selectedDocument = null;
    public $approvalNote = '';

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
    public $userRole = '';

    protected $listeners = [
        'document-approved' => 'handleDocumentApproved',
        'document-rejected' => 'handleDocumentRejected',
        'workflow-transition-applied' => 'handleTransitionApplied',
        'approval-list:refresh' => 'refreshData',
        'open-approval-modal' => 'openApprovalModal'
    ];

    public function render()
    {
        try {
            $documents = $this->getFilteredDocuments();

            // Only show approval document types
            $allowedTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $documentTypes = DocumentType::whereIn('name', $allowedTypeNames)
                ->orderBy('display_name')->get();

            // Group document types by category for the select
            $categoryLabels = [
                'study_approval' => 'Persetujuan Studi Lanjut',
                'pid_documents' => 'Perjanjian Ikatan Dinas',
            ];
            $groupedDocumentTypes = [];
            foreach ($categoryLabels as $catKey => $catLabel) {
                $names = [];
                if ($catKey === 'study_approval') {
                    $names = ['study_compatibility', 'application_minutes', 'approval_minutes', 'nde'];
                }
                if ($catKey === 'pid_documents') {
                    $names = ['pid'];
                }
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
                $this->dispatch('open-approval-modal', ['documentId' => $openDocumentId]);
            }

            return view('livewire.administration.approval', [
                'documents' => $documents,
                'documentTypes' => $documentTypes,
                'groupedDocumentTypes' => $groupedDocumentTypes,
                'canManageWorkflow' => $this->canUserManageWorkflow(),
                'userRoles' => Auth::user()->roles,
                'workflowStates' => $this->workflowStates,
                'userRole' => $this->userRole,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Approval render: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memuat data persetujuan.');

            return view('livewire.administration.approval', [
                'documents' => collect(),
                'documentTypes' => collect(),
                'groupedDocumentTypes' => [],
                'canManageWorkflow' => false,
                'workflowStates' => $this->workflowStates,
                'userRole' => $this->userRole,
            ]);
        }
    }

    /**
     * Get filtered documents using optimized queries
     */
    protected function getFilteredDocuments()
    {
        $query = ApprovalDocument::with(['employee.user', 'workflowHistory.user', 'documentType']);

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
    public function handleDocumentApproved($data)
    {
        session()->flash('success', $data['message'] ?? 'Dokumen berhasil disetujui.');
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
        $this->dispatch('document-view-modal:open', [
            'documentId' => $documentId,
            'documentModel' => 'ApprovalDocument',
            'category' => 'approvals'
        ]);
    }

    public function showDocument($documentId)
    {
        $this->openViewModal($documentId);
    }

    public function openApprovalModal($documentId)
    {
        // Handle both direct calls and calls from event
        if (is_array($documentId) && isset($documentId['documentId'])) {
            $documentId = $documentId['documentId'];
        }

        $this->selectedDocument = ApprovalDocument::with(['employee.user', 'documentType'])->find($documentId);
        $this->approvalNote = '';
        $this->isModalOpen = true;
    }

    public function closeApprovalModal()
    {
        $this->isModalOpen = false;
        $this->selectedDocument = null;
        $this->approvalNote = '';
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->workflowDocument = ApprovalDocument::with(['employee.user', 'documentType'])->find($documentId);
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

    public function openDetailModal($documentId)
    {
        $this->dispatch('document-view-modal:open', [
            'documentId' => $documentId,
            'documentModel' => 'ApprovalDocument',
            'category' => 'approvals'
        ]);
    }

    /**
     * Approve the currently selected document
     */
    public function approveDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        try {
            // Find the appropriate approve transition ID based on current state
            $transitions = $this->selectedDocument->getFormattedTransitions();
            $approveTransitionId = null;

            foreach ($transitions as $transition) {
                if (in_array(strtoupper($transition['name']), ['APPROVE_L1', 'APPROVE_L2'])) {
                    $approveTransitionId = $transition['id'];
                    break;
                }
            }

            if ($approveTransitionId) {
                // Apply the transition using the document's own method
                try {
                    // Build context for transition
                    $context = [
                        'user_id' => Auth::id(),
                        'comment' => $this->approvalNote,
                        'timestamp' => now(),
                        'user_name' => Auth::user()->name,
                    ];

                    // Apply the transition directly on the document
                    $this->selectedDocument->applyTransition($approveTransitionId, $context);

                    $this->closeApprovalModal();
                    $this->dispatch('document-approved', ['message' => 'Dokumen berhasil disetujui.']);
                } catch (\Exception $e) {
                    session()->flash('error', 'Error saat menyetujui dokumen: ' . $e->getMessage());
                }
            } else {
                session()->flash('error', 'Transisi persetujuan tidak ditemukan.');
                $this->closeApprovalModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error saat menyetujui dokumen: ' . $e->getMessage());
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
            // Find the appropriate reject transition ID based on current state
            $transitions = $this->selectedDocument->getFormattedTransitions();
            $rejectTransitionId = null;

            foreach ($transitions as $transition) {
                if (in_array(strtoupper($transition['name']), ['REJECT_L1', 'REJECT_L2'])) {
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
                        'comment' => $this->approvalNote,
                        'timestamp' => now(),
                        'user_name' => Auth::user()->name,
                    ];

                    // Apply the transition directly on the document
                    $this->selectedDocument->applyTransition($rejectTransitionId, $context);

                    $this->closeApprovalModal();
                    $this->dispatch('document-rejected', ['message' => 'Dokumen berhasil ditolak.']);
                } catch (\Exception $e) {
                    session()->flash('error', 'Error saat menolak dokumen: ' . $e->getMessage());
                }
            } else {
                session()->flash('error', 'Transisi penolakan tidak ditemukan.');
                $this->closeApprovalModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error saat menolak dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Submit document for approval (HR Finance Staff)
     */
    public function submitDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        try {
            // Find the submit transition ID
            $transitions = $this->selectedDocument->getFormattedTransitions();
            $submitTransitionId = null;

            foreach ($transitions as $transition) {
                if (strtoupper($transition['name']) == 'SUBMIT') {
                    $submitTransitionId = $transition['id'];
                    break;
                }
            }

            if ($submitTransitionId) {
                // Apply the transition using the document's own method
                try {
                    // Build context for transition
                    $context = [
                        'user_id' => Auth::id(),
                        'comment' => $this->approvalNote,
                        'timestamp' => now(),
                        'user_name' => Auth::user()->name,
                    ];

                    // Apply the transition directly on the document
                    $this->selectedDocument->applyTransition($submitTransitionId, $context);

                    $this->closeApprovalModal();
                    $this->dispatch('document-approved', ['message' => 'Dokumen berhasil disubmit untuk persetujuan.']);
                } catch (\Exception $e) {
                    session()->flash('error', 'Error saat submit dokumen: ' . $e->getMessage());
                }
            } else {
                session()->flash('error', 'Transisi submit tidak ditemukan.');
                $this->closeApprovalModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error saat submit dokumen: ' . $e->getMessage());
        }
    }

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status persetujuan berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'success';
    }

    // Implementation of abstract methods from WorkflowComponent
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

    /**
     * Extends the parent mount method to also handle opening documents from email links
     */
    public function mount(...$parameters)
    {
        // Call the parent mount method first
        parent::mount(...$parameters);

        // Set user role
        $this->userRole = Auth::user()->employee->role ?? '';
        $allStates = WorkflowDefinition::getAllStates('verification_by_management');

        // Map roles to allowed state IDs
        $roleStateMap = [
            'hr_finance_staff' => [1, 5], // DRAFT, REJECTED
            'head_of_study_program' => [2, 5], // PENDING_L1, REJECTED
            'head_of_research_group' => [3, 5], // PENDING_L2, REJECTED
            'fri_vice_dean' => [2, 3, 5], // PENDING_L1, PENDING_L2, REJECTED
        ];

        $defaultStateMap = [
            'hr_finance_staff' => 1,
            'head_of_study_program' => 2,
            'head_of_research_group' => 3,
            'fri_vice_dean' => 2,
        ];

        $allowedStates = $roleStateMap[$this->userRole] ?? array_keys($allStates);
        $this->workflowStates = array_filter($allStates, fn($v, $k) => in_array($k, $allowedStates), ARRAY_FILTER_USE_BOTH);

        // Set default status filter if not set
        if (empty($this->statusFilter) && isset($defaultStateMap[$this->userRole])) {
            $this->statusFilter = $defaultStateMap[$this->userRole];
        }

        // Check if document_id is in the request and select the document if present
        if (request()->has('document_id')) {
            $documentId = request()->get('document_id');
            $document = ApprovalDocument::find($documentId);

            if ($document) {
                // Schedule the modal to be opened after the component is fully rendered
                $this->shouldOpenDocumentModal = true;
                $this->documentIdToOpen = $document->id;

                // Add a flash message to indicate document was automatically selected
                session()->flash('success', 'Dokumen dari notifikasi email telah dipilih untuk persetujuan.');
            }
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'documentType', 'statusFilter']);
        $this->resetPage();
    }
}