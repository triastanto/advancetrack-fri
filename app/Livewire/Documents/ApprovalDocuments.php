<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ApprovalDocuments extends WorkflowComponent
{
    use WithPagination;

    public $viewModalOpen = false;
    public $currentDocument;
    public $availableDocumentTypes;
    public $selectedTransition;
    public $transitionComment = '';
    public $showWorkflowHistory = false;

    protected $rules = [
        'transitionComment' => 'nullable|string|max:1000'
    ];

    protected $messages = [
        'transitionComment.max' => 'Komentar maksimal 1000 karakter.',
    ];

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

    // Modal Methods
    public function openViewModal($documentId)
    {
        $this->currentDocument = Document::findOrFail($documentId);
        $this->viewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->viewModalOpen = false;
        $this->currentDocument = null;
        $this->showWorkflowHistory = false;
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->currentDocument = Document::with(['employee.user', 'employee.studyPrograms', 'documentType'])->findOrFail($documentId);
        $this->selectedTransition = $transitionId;
        $this->transitionComment = '';
        $this->workflowModalOpen = true;
    }

    public function closeWorkflowModal()
    {
        $this->workflowModalOpen = false;
        $this->currentDocument = null;
        $this->selectedTransition = null;
        $this->transitionComment = '';
    }

    public function toggleWorkflowHistory()
    {
        $this->showWorkflowHistory = !$this->showWorkflowHistory;
    }

    public function getActiveStudyInfo()
    {
        try {
            $employee = $this->getEmployee();

            $activeStudy = $employee->studyCalendars()
                ->where('study_status', 'active')
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
            $currentSemester = floor($monthsDiff / 6) + 1;

            return [
                'program' => $programName,
                'status' => $activeStudy->study_status,
                'start_date' => $startDate->format('F Y'),
                'estimated_end' => Carbon::parse($activeStudy->estimated_study_end)->format('F Y'),
                'current_semester' => $currentSemester,
                'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
            ];
        } catch (\Exception $e) {
            return null;
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

            $ApprovalDocuments = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.approval-documents', [
                'documents' => $ApprovalDocuments,
                'activeStudyInfo' => $this->getActiveStudyInfo(),
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

            return view('livewire.documents.approval-documents', [
                'documents' => $emptyPaginator,
                'activeStudyInfo' => null,
                'canManageWorkflow' => false
            ]);
        }
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
