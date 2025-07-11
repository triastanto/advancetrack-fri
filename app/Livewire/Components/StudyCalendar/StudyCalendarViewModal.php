<?php

namespace App\Livewire\Components\StudyCalendar;

use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Constants\DocumentTypeConstants;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class StudyCalendarViewModal extends Component
{
    public $isOpen = false;
    public $studyCalendar = null;
    public $showWorkflowHistory = false;

    protected $listeners = [
        'openStudyCalendarModal' => 'openModal',
        'closeStudyCalendarModal' => 'closeModal'
    ];

    public function openModal($studyCalendarId = null)
    {
        try {
            $this->studyCalendar = StudyCalendar::with([
                'employee.user',
                'studyDetail.studyProgram',
                'studyDetail.promotors',
                'workflowHistory.user'
            ])->find($studyCalendarId);

            if (!$this->studyCalendar) {
                session()->flash('error', 'Kalender studi tidak ditemukan.');
                return;
            }

            $this->isOpen = true;
            $this->showWorkflowHistory = false;

            Log::info('Study calendar modal opened', [
                'study_calendar_id' => $studyCalendarId,
                'employee_name' => $this->studyCalendar->employee->user->name ?? 'Unknown'
            ]);
        } catch (\Exception $e) {
            Log::error('Error opening study calendar modal', [
                'study_calendar_id' => $studyCalendarId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Terjadi kesalahan saat membuka detail kalender studi.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->studyCalendar = null;
        $this->showWorkflowHistory = false;
    }

    public function toggleWorkflowHistory()
    {
        $this->showWorkflowHistory = !$this->showWorkflowHistory;
    }

    public function getWorkflowProgress()
    {
        if (!$this->studyCalendar) {
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

        $currentState = $this->studyCalendar->workflow_state;
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

    public function getRequirementsStatus()
    {
        if (!$this->studyCalendar) {
            return [
                'academic_documents' => ['verified' => 0, 'total' => 0, 'complete' => false],
                'approval_document' => ['exists' => false, 'approved' => false],
                'all_requirements_met' => false
            ];
        }

        try {
            $employee = $this->studyCalendar->employee;

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

    public function render()
    {
        return view('livewire.components.study-calendar.study-calendar-view-modal', [
            'workflowProgress' => $this->getWorkflowProgress(),
            'requirementsStatus' => $this->getRequirementsStatus()
        ]);
    }
} 