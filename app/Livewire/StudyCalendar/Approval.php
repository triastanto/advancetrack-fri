<?php

namespace App\Livewire\StudyCalendar;

use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use App\Constants\DocumentTypeConstants;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\Workflow\WorkflowDefinition;

class Approval extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $selectedTransition;
    public $transitionComment = '';

    // Modal states
    public $workflowModalOpen = false;
    public $currentStudyCalendar;

    // Filter properties
    public $searchTerm = '';
    public $statusFilter = '';
    protected $queryString = ['searchTerm', 'statusFilter'];

    public $workflowStates = [];

    protected $listeners = [
        'workflow:transition-applied' => 'handleTransitionApplied',
        'study-calendar:refresh' => 'refreshData'
    ];

    protected $rules = [];
    protected $messages = [];

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->workflowStates = WorkflowDefinition::getAllStates('study_calendar');
    }

    // Event Handlers
    public function handleTransitionApplied($data)
    {
        session()->flash('success', $data['message'] ?? 'Status kalender studi berhasil diperbarui.');
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->resetPage();
    }

    // Modal Methods
    public function openWorkflowModal($studyCalendarId, $transitionId)
    {
        try {
            Log::info("Opening workflow modal for approval", [
                'study_calendar_id' => $studyCalendarId,
                'transition_id' => $transitionId,
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->employee->role ?? 'unknown'
            ]);

            // Dispatch with model type to ensure correct model is loaded
            $this->dispatch('workflow-transition-modal:open', [
                'documentId' => $studyCalendarId,
                'transitionId' => $transitionId,
                'modelType' => 'study_calendar'
            ]);

            Log::info("Workflow modal event dispatched successfully");
        } catch (\Exception $e) {
            Log::error("Error opening workflow modal", [
                'study_calendar_id' => $studyCalendarId,
                'transition_id' => $transitionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    // Approval Operations
    public function approveStudy($studyCalendarId)
    {
        Log::info("Approving study calendar {$studyCalendarId}");

        // Validate that the study calendar is in PENDING_APPROVAL state
        $studyCalendar = StudyCalendar::find($studyCalendarId);
        if (!$studyCalendar || $studyCalendar->workflow_state !== 2) { // 2 = PENDING_APPROVAL
            session()->flash('error', 'Hanya kalender studi dengan status PENDING_APPROVAL yang dapat disetujui.');
            return;
        }

        // Check requirements before approval
        $requirements = $this->getRequirementsStatusForStudyCalendar($studyCalendar);
        if (!$requirements['academic_documents']['complete']) {
            $missingCount = $requirements['academic_documents']['total'] - $requirements['academic_documents']['verified'];
            session()->flash('error', "Tidak dapat menyetujui kalender studi. Masih ada {$missingCount} dokumen persyaratan yang belum diverifikasi.");
            return;
        }

        $this->openWorkflowModal($studyCalendarId, 2); // APPROVE_STUDY transition
    }

    public function rejectStudy($studyCalendarId)
    {
        Log::info("Rejecting study calendar {$studyCalendarId}");

        // Validate that the study calendar is in PENDING_APPROVAL state
        $studyCalendar = StudyCalendar::find($studyCalendarId);
        if (!$studyCalendar || $studyCalendar->workflow_state !== 2) { // 2 = PENDING_APPROVAL
            session()->flash('error', 'Hanya kalender studi dengan status PENDING_APPROVAL yang dapat ditolak.');
            return;
        }

        $this->openWorkflowModal($studyCalendarId, 3); // REJECT_STUDY transition
    }

    // Requirements Check Methods for specific study calendar
    public function getRequirementsStatusForStudyCalendar($studyCalendar)
    {
        try {
            $employee = $studyCalendar->employee;

            // Get study requirement document type names from constants
            $studyRequirementNames = DocumentTypeConstants::getStudyRequirementNames();

            // Check Academic Documents (Study Requirements)
            $academicDocuments = AcademicDocument::where('employee_id', $employee->id)
                ->whereHas('documentType', function($query) use ($studyRequirementNames) {
                    $query->whereIn('name', $studyRequirementNames);
                })
                ->get();

            $verifiedAcademicDocs = $academicDocuments->where('workflow_state', 3)->count(); // VERIFIED state
            $totalAcademicDocs = $academicDocuments->count();

            // Get approval document type names and IDs
            $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $approvalTypeIds = \App\Models\DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

            // Check Approval Documents (all types for this employee)
            $approvalDocuments = ApprovalDocument::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $approvalTypeIds)
                ->get();
            $approvalTotal = count($approvalTypeIds); // Should be 5
            $approvalApproved = $approvalDocuments->where('workflow_state', 4)->count(); // APPROVED state

            return [
                'academic_documents' => [
                    'verified' => $verifiedAcademicDocs,
                    'total' => $totalAcademicDocs,
                    'complete' => $verifiedAcademicDocs === $totalAcademicDocs && $totalAcademicDocs > 0
                ],
                'approval_document' => [
                    'exists' => $approvalDocuments->count() > 0,
                    'approved' => $approvalApproved === $approvalTotal && $approvalTotal > 0,
                    'approved_count' => $approvalApproved,
                    'total' => $approvalTotal
                ],
                'all_requirements_met' => $verifiedAcademicDocs === $totalAcademicDocs &&
                                        $totalAcademicDocs > 0 &&
                                        $approvalApproved === $approvalTotal &&
                                        $approvalTotal > 0
            ];
        } catch (\Exception $e) {
            Log::error('Error getting requirements status for study calendar: ' . $e->getMessage());
            return [
                'academic_documents' => ['verified' => 0, 'total' => 0, 'complete' => false],
                'approval_document' => ['exists' => false, 'approved' => false, 'approved_count' => 0, 'total' => 0],
                'all_requirements_met' => false
            ];
        }
    }

    // Data Retrieval Methods
    public function getPendingStudyCalendars()
    {
        try {
            $query = StudyCalendar::with(['employee.user', 'studyDetail'])
                ->where('workflow_state', 2); // PENDING_APPROVAL

            // Apply filters
            if (!empty($this->searchTerm)) {
                $query->whereHas('employee.user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            }

            if (!empty($this->statusFilter)) {
                $query->where('workflow_state', $this->statusFilter);
            }

            return $query->orderBy('created_at', 'desc')->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error getting pending study calendars: ' . $e->getMessage());
            return new LengthAwarePaginator([], 0, 10);
        }
    }

    public function getAllStudyCalendars()
    {
        try {
            $query = StudyCalendar::with(['employee.user', 'studyDetail']);

            // Apply filters
            if (!empty($this->searchTerm)) {
                $query->whereHas('employee.user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            }

            if (!empty($this->statusFilter)) {
                $query->where('workflow_state', $this->statusFilter);
            }

            return $query->orderBy('created_at', 'desc')->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error getting all study calendars: ' . $e->getMessage());
            return new LengthAwarePaginator([], 0, 10);
        }
    }

    public function getWorkflowTimeline($studyCalendar)
    {
        if (!$studyCalendar) {
            return collect();
        }

        try {
            return $studyCalendar->workflowHistory()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($history) {
                    return [
                        'id' => $history->id,
                        'transition_name' => $history->transition_name,
                        'from_state' => $history->from_state,
                        'to_state' => $history->to_state,
                        'comment' => $history->comment,
                        'user_name' => $history->user->name ?? 'System',
                        'created_at' => $history->created_at,
                        'formatted_date' => $history->created_at->format('d M Y H:i')
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting workflow timeline: ' . $e->getMessage());
            return collect();
        }
    }

    public function getWorkflowProgress($studyCalendar)
    {
        if (!$studyCalendar) {
            return [
                'current_phase' => 1,
                'total_phases' => 4,
                'phases' => [
                    ['id' => 1, 'name' => 'Draft', 'status' => 'completed', 'has_error' => false],
                    ['id' => 2, 'name' => 'Pending Approval', 'status' => 'current', 'has_error' => false],
                    ['id' => 3, 'name' => 'Approved', 'status' => 'pending', 'has_error' => false],
                    ['id' => 4, 'name' => 'Active', 'status' => 'pending', 'has_error' => false]
                ]
            ];
        }

        $currentState = $studyCalendar->workflow_state;
        $phases = [
            ['id' => 1, 'name' => 'Draft', 'status' => 'completed', 'has_error' => false],
            ['id' => 2, 'name' => 'Pending Approval', 'status' => 'pending', 'has_error' => false],
            ['id' => 3, 'name' => 'Approved', 'status' => 'pending', 'has_error' => false],
            ['id' => 4, 'name' => 'Active', 'status' => 'pending', 'has_error' => false]
        ];

        // Update phase status based on current state
        if ($currentState >= 2) {
            $phases[1]['status'] = 'completed';
            $phases[2]['status'] = $currentState === 2 ? 'current' : 'completed';
        }
        if ($currentState >= 3) {
            $phases[3]['status'] = $currentState === 3 ? 'current' : 'completed';
        }
        if ($currentState >= 5) {
            $phases[4]['status'] = 'current';
        }

        return [
            'current_phase' => $currentState,
            'total_phases' => 4,
            'phases' => $phases
        ];
    }

    // Filter Methods
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'statusFilter']);
        $this->resetPage();
    }

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status kalender studi berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'success';
    }

    public function render()
    {
        try {
            $studyCalendars = $this->getAllStudyCalendars();

            return view('livewire.study-calendar.approval', [
                'studyCalendars' => $studyCalendars,
                'canManageWorkflow' => $this->canUserManageWorkflow(),
                'workflowStates' => $this->workflowStates,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            return view('livewire.study-calendar.approval', [
                'studyCalendars' => new LengthAwarePaginator([], 0, 10),
                'canManageWorkflow' => false,
                'workflowStates' => $this->workflowStates,
            ]);
        }
    }

    // Implementation of abstract methods from WorkflowComponent
    protected function getWorkflowModelClass(): string
    {
        return StudyCalendar::class;
    }

    protected function getWorkflowDocumentPropertyName(): string
    {
        return 'currentStudyCalendar';
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
