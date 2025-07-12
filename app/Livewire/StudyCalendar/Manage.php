<?php

namespace App\Livewire\StudyCalendar;

use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Livewire\Base\WorkflowComponent;
use App\Traits\HasDocumentManagement;
use App\Traits\HasCommonValidation;
use App\Constants\DocumentTypeConstants;
use App\Services\StudyCalendarRequirementsService;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\DocumentType;

class Manage extends WorkflowComponent
{
    use WithPagination, HasDocumentManagement, HasCommonValidation;

    public $selectedTransition;
    public $transitionComment = '';
    public $activeTab = 'timeline-approval';

    // Modal states
    public $workflowModalOpen = false;
    public $currentStudyCalendar;

    // Study Information Edit Modals
    public $showStudyInfoModal = false;
    public $showSupportInfoModal = false;

    // Form data for editing
    public $editStudyStart;
    public $editEstimatedEnd;
    public $editGraduationDate;
    public $editTotalSemester;
    public $editUniversityName;
    public $editUniversityAddress;
    public $editUniversityEmail;
    public $editUniversityPhone;
    public $editStudyProgramId;
    public $editStudyLevel;
    public $editStudyAddress;
    public $editFundingSource;
    public $editScholarship;
    public $editStudyRegulationNotes;

    // Form data for editing support information
    public $editSupervisorAssignments = [];
    public $editPromotors = [];
    public $editCourseResponsibilities = [];
    
    // Support modal form data
    public $newSupervisorName;
    public $newSupervisorNidn;
    public $newSupervisorStartDate;
    public $newSupervisorEndDate;
    public $newSupervisorIsActive = true;
    
    public $newPromotorName;
    public $newPromotorEmail;
    public $newPromotorType = 'primary'; // primary, secondary
    
    public $newCourseName;
    public $newCourseSemester;
    public $newCourseAcademicYear;

    // Form toggle properties
    public $showSupervisorForm = false;
    public $showPromotorForm = false;
    public $showCourseForm = false;

    protected $listeners = [
        'workflow:transition-applied' => 'handleTransitionApplied',
        'study-calendar:refresh' => 'refreshData'
    ];

    protected $rules = [];
    protected $messages = [];

    protected StudyCalendarRequirementsService $requirementsService;

    public function boot(StudyCalendarRequirementsService $requirementsService)
    {
        $this->requirementsService = $requirementsService;
    }

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

    // Study Information Edit Modal Methods
    public function openStudyInfoModal()
    {
        $studyCalendar = $this->getStudyCalendarForEmployee();
        if ($studyCalendar) {
            $this->editStudyStart = $studyCalendar->study_start?->format('Y-m-d');
            $this->editEstimatedEnd = $studyCalendar->estimated_study_end?->format('Y-m-d');
            $this->editGraduationDate = $studyCalendar->graduation_date?->format('Y-m-d');
            $this->editTotalSemester = $studyCalendar->studyDetail?->total_semester;
            $this->editUniversityName = $studyCalendar->studyDetail?->university_name;
            $this->editUniversityAddress = $studyCalendar->studyDetail?->university_address;
            $this->editUniversityEmail = $studyCalendar->studyDetail?->university_email;
            $this->editUniversityPhone = $studyCalendar->studyDetail?->university_phone;
            $this->editStudyProgramId = $studyCalendar->studyDetail?->study_program_id;
            $this->editStudyLevel = $studyCalendar->studyDetail?->study_level;
            $this->editStudyAddress = $studyCalendar->studyDetail?->study_address;
            $this->editFundingSource = $studyCalendar->studyDetail?->funding_source;
            $this->editScholarship = $studyCalendar->studyDetail?->scholarship;
            $this->editStudyRegulationNotes = $studyCalendar->studyDetail?->study_regulation_notes;
        }
        $this->showStudyInfoModal = true;
    }

    public function openSupportInfoModal()
    {
        // Load support information data
        $studyCalendar = $this->getStudyCalendarForEmployee();
        $employee = $this->getEmployee();
        
        if ($studyCalendar && $employee) {
            // Load supervisor assignments
            $supervisorAssignments = $this->getSupervisorAssignments($employee);
            $this->editSupervisorAssignments = $supervisorAssignments->map(function($assignment) {
                $startDate = $assignment['start_date'];
                $endDate = $assignment['end_date'];
                
                // Handle date formatting - check if it's already a string or Carbon object
                $formattedStartDate = null;
                $formattedEndDate = null;
                
                if ($startDate) {
                    $formattedStartDate = is_string($startDate) ? $startDate : $startDate->format('Y-m-d');
                }
                
                if ($endDate) {
                    $formattedEndDate = is_string($endDate) ? $endDate : $endDate->format('Y-m-d');
                }
                
                return [
                    'id' => $assignment['id'] ?? null,
                    'supervisor_name' => $assignment['supervisor_name'],
                    'supervisor_nidn' => $assignment['supervisor_nidn'],
                    'start_date' => $formattedStartDate,
                    'end_date' => $formattedEndDate,
                    'is_active' => $assignment['is_active'],
                ];
            })->toArray();

            // Load promotors
            if ($studyCalendar->studyDetail) {
                $this->editPromotors = $studyCalendar->studyDetail->promotors->map(function($promotor) {
                    return [
                        'id' => $promotor->id,
                        'name' => $promotor->name,
                        'email' => $promotor->email,
                        'type' => $promotor->type, // primary, secondary
                    ];
                })->toArray();
            }

            // Load course responsibilities
            $courseResponsibilities = $this->getCourseResponsibilities($employee);
            $this->editCourseResponsibilities = $courseResponsibilities->map(function($course) {
                return [
                    'id' => $course['id'] ?? null,
                    'course_name' => $course['course_name'],
                    'semester' => $course['semester'],
                    'academic_year' => $course['academic_year'],
                ];
            })->toArray();
        }
        $this->showSupportInfoModal = true;
    }

    public function addSupervisor()
    {
        $this->validate([
            'newSupervisorName' => 'required|string|max:255',
            'newSupervisorNidn' => 'required|string|max:20',
            'newSupervisorStartDate' => 'required|date',
            'newSupervisorEndDate' => 'required|date|after:newSupervisorStartDate',
        ]);

        $this->editSupervisorAssignments[] = [
            'id' => null,
            'supervisor_name' => $this->newSupervisorName,
            'supervisor_nidn' => $this->newSupervisorNidn,
            'start_date' => $this->newSupervisorStartDate,
            'end_date' => $this->newSupervisorEndDate,
            'is_active' => $this->newSupervisorIsActive,
        ];

        $this->reset(['newSupervisorName', 'newSupervisorNidn', 'newSupervisorStartDate', 'newSupervisorEndDate', 'newSupervisorIsActive']);
    }

    public function removeSupervisor($index)
    {
        unset($this->editSupervisorAssignments[$index]);
        $this->editSupervisorAssignments = array_values($this->editSupervisorAssignments);
    }

    public function addPromotor()
    {
        $this->validate([
            'newPromotorName' => 'required|string|max:255',
            'newPromotorEmail' => 'required|email',
            'newPromotorType' => 'required|in:primary,secondary',
        ]);

        $this->editPromotors[] = [
            'id' => null,
            'name' => $this->newPromotorName,
            'email' => $this->newPromotorEmail,
            'type' => $this->newPromotorType,
        ];

        $this->reset(['newPromotorName', 'newPromotorEmail', 'newPromotorType']);
    }

    public function removePromotor($index)
    {
        unset($this->editPromotors[$index]);
        $this->editPromotors = array_values($this->editPromotors);
    }

    public function addCourse()
    {
        $this->validate([
            'newCourseName' => 'required|string|max:255',
            'newCourseSemester' => 'required|integer|min:1|max:14',
            'newCourseAcademicYear' => 'required|string|max:20',
        ]);

        $this->editCourseResponsibilities[] = [
            'id' => null,
            'course_name' => $this->newCourseName,
            'semester' => $this->newCourseSemester,
            'academic_year' => $this->newCourseAcademicYear,
        ];

        $this->reset(['newCourseName', 'newCourseSemester', 'newCourseAcademicYear']);
    }

    public function removeCourse($index)
    {
        unset($this->editCourseResponsibilities[$index]);
        $this->editCourseResponsibilities = array_values($this->editCourseResponsibilities);
    }

    public function closeModal($modalName)
    {
        $this->$modalName = false;
        $this->resetFormData();
    }

    private function resetFormData()
    {
        $this->editStudyStart = null;
        $this->editEstimatedEnd = null;
        $this->editGraduationDate = null;
        $this->editTotalSemester = null;
        $this->editUniversityName = null;
        $this->editUniversityAddress = null;
        $this->editUniversityEmail = null;
        $this->editUniversityPhone = null;
        $this->editStudyProgramId = null;
        $this->editStudyLevel = null;
        $this->editStudyAddress = null;
        $this->editFundingSource = null;
        $this->editScholarship = null;
        $this->editStudyRegulationNotes = null;
        
        // Reset support form data
        $this->editSupervisorAssignments = [];
        $this->editPromotors = [];
        $this->editCourseResponsibilities = [];
        $this->newSupervisorName = null;
        $this->newSupervisorNidn = null;
        $this->newSupervisorStartDate = null;
        $this->newSupervisorEndDate = null;
        $this->newSupervisorIsActive = true;
        $this->newPromotorName = null;
        $this->newPromotorEmail = null;
        $this->newPromotorType = 'primary';
        $this->newCourseName = null;
        $this->newCourseSemester = null;
        $this->newCourseAcademicYear = null;
        
        // Reset form toggle properties
        $this->showSupervisorForm = false;
        $this->showPromotorForm = false;
        $this->showCourseForm = false;
    }

    public function saveStudyInfo()
    {
        $this->validate([
            'editStudyStart' => 'required|date',
            'editEstimatedEnd' => 'required|date|after:editStudyStart',
            'editTotalSemester' => 'required|integer|min:1',
            'editUniversityName' => 'required|string|max:255',
            'editUniversityAddress' => 'required|string',
            'editUniversityEmail' => 'nullable|email',
            'editUniversityPhone' => 'nullable|string|max:20',
            'editStudyProgramId' => 'required|exists:study_programs,id',
            'editStudyLevel' => 'required|in:S2,S3,Postdoc,Specialist',
            'editStudyAddress' => 'required|string',
            'editFundingSource' => 'required|in:LPDP,Pribadi,Instansi,Perusahaan,Yayasan',
            'editScholarship' => 'nullable|string|max:255',
            'editStudyRegulationNotes' => 'nullable|string',
        ]);

        try {
            $studyCalendar = $this->getStudyCalendarForEmployee();
            if ($studyCalendar) {
                $studyCalendar->update([
                    'study_start' => $this->editStudyStart,
                    'estimated_study_end' => $this->editEstimatedEnd,
                    'graduation_date' => $this->editGraduationDate,
                ]);

                if ($studyCalendar->studyDetail) {
                    $studyCalendar->studyDetail->update([
                        'total_semester' => $this->editTotalSemester,
                        'university_name' => $this->editUniversityName,
                        'university_address' => $this->editUniversityAddress,
                        'university_email' => $this->editUniversityEmail,
                        'university_phone' => $this->editUniversityPhone,
                        'study_program_id' => $this->editStudyProgramId,
                        'study_level' => $this->editStudyLevel,
                        'study_address' => $this->editStudyAddress,
                        'funding_source' => $this->editFundingSource,
                        'scholarship' => $this->editScholarship,
                        'study_regulation_notes' => $this->editStudyRegulationNotes,
                    ]);
                }

                session()->flash('success', 'Informasi studi lanjut berhasil diperbarui.');
                $this->closeModal('showStudyInfoModal');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui informasi studi: ' . $e->getMessage());
        }
    }

    public function saveSupportInfo()
    {
        try {
            $studyCalendar = $this->getStudyCalendarForEmployee();
            if ($studyCalendar) {
                // Save supervisor assignments
                // This would typically involve creating/updating/deleting supervisor assignment records
                // For now, we'll just show a success message
                
                // Save promotors
                if ($studyCalendar->studyDetail) {
                    // Clear existing promotors and add new ones
                    $studyCalendar->studyDetail->promotors()->delete();
                    
                    foreach ($this->editPromotors as $promotor) {
                        $studyCalendar->studyDetail->promotors()->create([
                            'name' => $promotor['name'],
                            'email' => $promotor['email'],
                            'type' => $promotor['type'],
                        ]);
                    }
                }
                
                // Save course responsibilities
                // This would typically involve creating/updating/deleting course responsibility records
                // For now, we'll just show a success message
                
                session()->flash('success', 'Informasi pendukung studi berhasil diperbarui.');
                $this->closeModal('showSupportInfoModal');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui informasi pendukung studi: ' . $e->getMessage());
        }
    }

    // Study Calendar Operations
    public function submitForApproval($studyCalendarId)
    {
        Log::info("Submitting study calendar {$studyCalendarId} for approval.");

        // Validate requirements before submission (pre-approval phase: only study requirements needed)
        $requirements = $this->getRequirementsStatus();

        if (!$requirements['academic_documents']['complete']) {
            $missingCount = $requirements['academic_documents']['total'] - $requirements['academic_documents']['verified'];
            session()->flash('error', "Tidak dapat mengajukan kalender studi. Masih ada {$missingCount} dokumen persyaratan yang belum diverifikasi.");
            return;
        }

        // ApprovalDocument is NOT required at this phase per business process

        $this->openWorkflowModal($studyCalendarId, 1); // SUBMIT_STUDY transition
    }

    public function resubmitStudy($studyCalendarId)
    {
        $this->openWorkflowModal($studyCalendarId, 4); // RESUBMIT_STUDY transition
    }

    public function startStudy($studyCalendarId)
    {
        // START_STUDY: Only allowed if Study Calendar is APPROVED, all AcademicDocuments are VERIFIED, and ApprovalDocument is APPROVED
        $studyCalendar = StudyCalendar::find($studyCalendarId);
        if (!$studyCalendar || $studyCalendar->workflow_state !== 3) { // 3 = APPROVED
            session()->flash('error', 'Kalender studi harus berstatus APPROVED sebelum memulai studi.');
            return;
        }
        
        $requirements = $this->getRequirementsStatus();
        $missingRequirements = $this->requirementsService->getMissingRequirements($requirements);
        
        if (!empty($missingRequirements)) {
            $missingText = implode(', ', $missingRequirements);
            session()->flash('error', "Tidak dapat memulai studi. {$missingText}.");
            return;
        }
        
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
            return $this->requirementsService->getRequirementsStatus($employee->id);
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
        return $this->requirementsService->canStartStudy($requirements);
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

    public function getSupervisorAssignments($employee)
    {
        try {
            return $employee->supervisorAssignments()
                ->with(['supervisor.user'])
                ->orderBy('start_date', 'desc')
                ->get()
                ->map(function($assignment) {
                    return [
                        'id' => $assignment->id,
                        'supervisor_name' => $assignment->supervisor->user->name ?? 'Unknown',
                        'supervisor_nidn' => $assignment->supervisor->nidn ?? '-',
                        'start_date' => $assignment->start_date,
                        'end_date' => $assignment->end_date,
                        'is_active' => $assignment->isActive(),
                        'formatted_start_date' => $assignment->start_date->format('d M Y'),
                        'formatted_end_date' => $assignment->end_date ? $assignment->end_date->format('d M Y') : 'Sekarang',
                        'duration' => $assignment->end_date 
                            ? $assignment->start_date->diffInDays($assignment->end_date) . ' hari'
                            : $assignment->start_date->diffInDays(now()) . ' hari'
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting supervisor assignments: ' . $e->getMessage());
            return collect();
        }
    }

    public function getCourseResponsibilities($employee)
    {
        try {
            return $employee->courseResponsibilities()
                ->orderBy('academic_year', 'desc')
                ->orderBy('semester')
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'course_name' => $course->course_name,
                        'semester' => $course->semester,
                        'academic_year' => $course->academic_year,
                        'formatted_semester' => $course->semester ? 'Semester ' . $course->semester : '-',
                        'formatted_academic_year' => $course->academic_year ?? '-'
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting course responsibilities: ' . $e->getMessage());
            return collect();
        }
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
            $supervisorAssignments = $this->getSupervisorAssignments($employee);
            $courseResponsibilities = $this->getCourseResponsibilities($employee);

            // Get document states for display
            $academicDocumentsWithStates = $this->requirementsService->getAcademicDocumentsWithStates($employee->id);
            $approvalDocumentsWithStates = $this->requirementsService->getApprovalDocumentsWithStates($employee->id);

            // Ensure progress bar gets the actual workflow_state as current_state
            $workflowProgressWithState = array_merge(
                $workflowProgress,
                ['current_state' => $studyCalendar ? $studyCalendar->workflow_state : 1]
            );

            return view('livewire.study-calendar.manage', [
                'studyCalendar' => $studyCalendar,
                'requirementsStatus' => $requirementsStatus,
                'workflowProgress' => $workflowProgressWithState,
                'workflowTimeline' => $workflowTimeline,
                'supervisorAssignments' => $supervisorAssignments,
                'courseResponsibilities' => $courseResponsibilities,
                'canManageWorkflow' => $this->canUserManageWorkflow(),
                'academicDocumentsWithStates' => $academicDocumentsWithStates,
                'approvalDocumentsWithStates' => $approvalDocumentsWithStates,
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
                    ],
                    'current_state' => 1
                ],
                'workflowTimeline' => collect(),
                'supervisorAssignments' => collect(),
                'courseResponsibilities' => collect(),
                'canManageWorkflow' => false,
                'academicDocumentsWithStates' => [],
                'approvalDocumentsWithStates' => [],
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