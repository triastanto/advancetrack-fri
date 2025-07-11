<?php

namespace App\Livewire\Documents;

use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class StudyApprovals extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $availableDocumentTypes;
    public $selectedTransition;
    public $transitionComment = '';
    public $showWorkflowHistory = false;

    protected $listeners = [
        'document:uploaded' => 'handleDocumentUploaded',
        'document:deleted' => 'handleDocumentDeleted',
        'document:submitted' => 'handleDocumentSubmitted',
        'workflow:transition-applied' => 'handleTransitionApplied',
        'document-list:refresh' => 'refreshData'
    ];

    protected $rules = [];
    protected $messages = [];

    // Helper method to get Approval document types
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

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->availableDocumentTypes = $this->getapprovalDocumentTypes();
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

    public function refreshData()
    {
        $this->resetPage();
        // Livewire will automatically re-render the component
    }

    // Modal Methods - Dispatch to modular components
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

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->dispatch('workflow-transition-modal:open', [
            'documentId' => $documentId,
            'transitionId' => $transitionId,
            'modelType' => 'approval_document'
        ]);
    }

    // Status and Info Methods - Using traits
    public function getActiveStudyInfoForEmployee()
    {
        try {
            $employee = $this->getEmployee();
            return $this->getActiveStudyInfo($employee);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCompletionStatus()
    {
        try {
            $employee = $this->getEmployee();
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

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status Persetujuan Studi Lanjut berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getapprovalDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $approvalDocuments = ApprovalDocument::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.study-approvals', [
                'documents' => $approvalDocuments,
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

            return view('livewire.documents.study-approvals', [
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
}
