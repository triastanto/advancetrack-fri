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

class Manage extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $selectedTransition;
    public $transitionComment = '';

    // Modal states
    public $workflowModalOpen = false;
    public $currentStudyCalendar;

    protected $listeners = [
        'workflow:transition-applied' => 'handleTransitionApplied',
        'study-calendar:refresh' => 'refreshData'
    ];

    protected $rules = [];
    protected $messages = [];

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
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
            Log::info("Opening workflow modal", [
                'study_calendar_id' => $studyCalendarId,
                'transition_id' => $transitionId,
                'user_id' => Auth::id()
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

    // Study Calendar Operations
    public function submitForApproval($studyCalendarId)
    {
        Log::info("Submitting study calendar {$studyCalendarId} for approval.");

        // Validate requirements before submission
        $requirements = $this->getRequirementsStatus();

        if (!$requirements['academic_documents']['complete']) {
            $missingCount = $requirements['academic_documents']['total'] - $requirements['academic_documents']['verified'];
            session()->flash('error', "Tidak dapat mengajukan kalender studi. Masih ada {$missingCount} dokumen persyaratan yang belum diverifikasi.");
            return;
        }

        if (!$requirements['approval_document']['approved']) {
            session()->flash('error', 'Tidak dapat mengajukan kalender studi. Dokumen persetujuan harus disetujui terlebih dahulu.');
            return;
        }

        $this->openWorkflowModal($studyCalendarId, 1); // SUBMIT_STUDY transition
    }

    public function resubmitStudy($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 4); // RESUBMIT_STUDY transition
    }

    public function startStudy($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 5); // START_STUDY transition
    }

    public function takeLeave($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 6); // TAKE_LEAVE transition
    }

    public function returnFromLeave($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 7); // RETURN_FROM_LEAVE transition
    }

    public function completeStudy($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 8); // COMPLETE_STUDY transition
    }

    public function dropOut($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 9); // DROP_OUT_ACTIVE transition
    }

    // Requirements Check Methods
    public function getRequirementsStatus()
    {
        try {
            $employee = $this->getEmployee();

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

            // Check Approval Document
            $approvalDocument = ApprovalDocument::where('employee_id', $employee->id)->first();
            $approvalDocumentApproved = $approvalDocument && $approvalDocument->workflow_state === 4; // APPROVED state

            return [
                'academic_documents' => [
                    'verified' => $verifiedAcademicDocs,
                    'total' => $totalAcademicDocs,
                    'complete' => $verifiedAcademicDocs === $totalAcademicDocs && $totalAcademicDocs > 0
                ],
                'approval_document' => [
                    'exists' => $approvalDocument !== null,
                    'approved' => $approvalDocumentApproved
                ],
                'all_requirements_met' => $verifiedAcademicDocs === $totalAcademicDocs &&
                                        $totalAcademicDocs > 0 &&
                                        $approvalDocumentApproved
            ];
        } catch (\Exception $e) {
            Log::error('Error getting requirements status: ' . $e->getMessage());
            return [
                'academic_documents' => ['verified' => 0, 'total' => 0, 'complete' => false],
                'approval_document' => ['exists' => false, 'approved' => false],
                'all_requirements_met' => false
            ];
        }
    }

    public function canStartStudy($studyCalendar)
    {
        if ($studyCalendar->workflow_state !== 3) { // Not APPROVED
            return false;
        }

        $requirements = $this->getRequirementsStatus();
        return $requirements['all_requirements_met'];
    }

    public function getStudyCalendarForEmployee()
    {
        try {
            $employee = $this->getEmployee();
            return StudyCalendar::where('employee_id', $employee->id)
                ->with(['employee.user', 'studyDetail'])
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting study calendar: ' . $e->getMessage());
            return null;
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

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status kalender studi lanjut berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'success';
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $studyCalendar = $this->getStudyCalendarForEmployee();
            $requirementsStatus = $this->getRequirementsStatus();
            $workflowProgress = $this->getWorkflowProgress($studyCalendar);
            $workflowTimeline = $studyCalendar ? $this->getWorkflowTimeline($studyCalendar) : collect();

            return view('livewire.study-calendar.manage', [
                'studyCalendar' => $studyCalendar,
                'requirementsStatus' => $requirementsStatus,
                'workflowProgress' => $workflowProgress,
                'workflowTimeline' => $workflowTimeline,
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            return view('livewire.study-calendar.manage', [
                'studyCalendar' => null,
                'requirementsStatus' => [
                    'academic_documents' => ['verified' => 0, 'total' => 0, 'complete' => false],
                    'approval_document' => ['exists' => false, 'approved' => false],
                    'all_requirements_met' => false
                ],
                'workflowProgress' => [
                    'current_phase' => 1,
                    'total_phases' => 4,
                    'phases' => [
                        ['id' => 1, 'name' => 'Draft', 'status' => 'completed', 'has_error' => false],
                        ['id' => 2, 'name' => 'Pending Approval', 'status' => 'current', 'has_error' => false],
                        ['id' => 3, 'name' => 'Approved', 'status' => 'pending', 'has_error' => false],
                        ['id' => 4, 'name' => 'Active', 'status' => 'pending', 'has_error' => false]
                    ]
                ],
                'workflowTimeline' => collect(),
                'canManageWorkflow' => false
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