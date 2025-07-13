<?php

namespace App\Livewire\Monitoring;

use App\Models\StudyCalendar;
use App\Models\Employee;
use App\Models\Document;
use App\Models\Workflow\WorkflowHistory;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use App\Models\StudyProgram;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Analytics extends Component
{
    use WithPagination;

    // Filter properties
    public $dateRange = '7'; // days
    public $selectedResearchGroup = '';
    public $selectedResearchLab = '';

    // Chart data properties
    public $studyCalendarStats = [];
    public $documentStats = [];
    public $workflowPerformance = [];
    public $researchGroupStats = [];
    public $studyProgramStats = [];
    public $timelineData = [];

    protected $queryString = [
        'dateRange' => ['except' => '7'],
        'selectedResearchGroup' => ['except' => ''],
        'selectedResearchLab' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedDateRange()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedResearchGroup()
    {
        $this->selectedResearchLab = '';
        $this->loadAnalytics();
    }

    public function updatedSelectedResearchLab()
    {
        $this->loadAnalytics();
    }





    public function loadAnalytics()
    {
        $this->studyCalendarStats = $this->getStudyCalendarStats();
        $this->documentStats = $this->getDocumentStats();
        $this->workflowPerformance = $this->getWorkflowPerformance();
        $this->researchGroupStats = $this->getResearchGroupStats();
        $this->studyProgramStats = $this->getStudyProgramStats();
        $this->timelineData = $this->getTimelineData();
    }

    public function getStudyCalendarStats()
    {
        $query = StudyCalendar::query()
            ->with(['employee.user', 'studyDetail.studyProgram']);

        // Apply filters
        $this->applyFilters($query);

        $total = $query->count();
        
        $stats = [
            'total' => $total,
            'by_state' => [
                'draft' => $query->clone()->where('workflow_state', 1)->count(),
                'pending_approval' => $query->clone()->where('workflow_state', 2)->count(),
                'approved' => $query->clone()->where('workflow_state', 3)->count(),
                'rejected' => $query->clone()->where('workflow_state', 4)->count(),
                'active' => $query->clone()->where('workflow_state', 5)->count(),
                'leave' => $query->clone()->where('workflow_state', 6)->count(),
                'finished' => $query->clone()->where('workflow_state', 7)->count(),
                'drop_out' => $query->clone()->where('workflow_state', 8)->count(),
            ],
            'completion_rate' => $total > 0 ? round(($query->clone()->whereIn('workflow_state', [7, 8])->count() / $total) * 100, 1) : 0,
            'active_studies' => $query->clone()->whereIn('workflow_state', [5, 6])->count(),
            'avg_duration_days' => $this->getAverageStudyDuration($query),
        ];

        return $stats;
    }

    public function getDocumentStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        // Get Academic Documents
        $academicQuery = \App\Models\AcademicDocument::query()
            ->with(['employee.user', 'documentType']);
        $this->applyFilters($academicQuery);
        
        // Get Approval Documents
        $approvalQuery = \App\Models\ApprovalDocument::query()
            ->with(['employee.user', 'documentType']);
        $this->applyFilters($approvalQuery);

        $academicTotal = $academicQuery->count();
        $approvalTotal = $approvalQuery->count();
        $total = $academicTotal + $approvalTotal;
        
        $stats = [
            'total' => $total,
            'academic_total' => $academicTotal,
            'approval_total' => $approvalTotal,
            'by_state' => [
                'draft' => $academicQuery->clone()->where('workflow_state', 1)->count() + 
                          $approvalQuery->clone()->where('workflow_state', 1)->count(),
                'pending' => $academicQuery->clone()->where('workflow_state', 2)->count() + 
                            $approvalQuery->clone()->where('workflow_state', 2)->count(),
                'verified' => $academicQuery->clone()->where('workflow_state', 3)->count() + 
                             $approvalQuery->clone()->where('workflow_state', 3)->count(),
                'rejected' => $academicQuery->clone()->where('workflow_state', 4)->count() + 
                             $approvalQuery->clone()->where('workflow_state', 4)->count(),
            ],
            'by_type' => $this->getDocumentTypeStats(),
            'verification_rate' => $total > 0 ? round((($academicQuery->clone()->where('workflow_state', 3)->count() + 
                                                       $approvalQuery->clone()->where('workflow_state', 3)->count()) / $total) * 100, 1) : 0,
            'avg_processing_time' => $this->getAverageDocumentProcessingTime(),
        ];

        return $stats;
    }

    public function getWorkflowPerformance()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = WorkflowHistory::query()
            ->with(['user', 'workflowable'])
            ->where('workflow_histories.created_at', '>=', $startDate);

        $totalTransitions = $query->count();
        
        $stats = [
            'total_transitions' => $totalTransitions,
            'by_workflow' => [
                'study_calendar' => $query->clone()->where('workflow_name', 'study_calendar')->count(),
                'verification_by_staff' => $query->clone()->where('workflow_name', 'verification_by_staff')->count(),
                'verification_by_management' => $query->clone()->where('workflow_name', 'verification_by_management')->count(),
            ],
            'by_user' => $this->getUserWorkflowStats($query),
            'avg_transitions_per_day' => $this->dateRange > 0 ? round($totalTransitions / $this->dateRange, 1) : 0,
            'peak_activity_day' => $this->getPeakActivityDay($query),
        ];

        return $stats;
    }

    public function getResearchGroupStats()
    {
        $query = ResearchGroup::query()
            ->withCount(['researchLabs', 'researchLabs as total_employees' => function($q) {
                $q->withCount('employees');
            }]);

        $stats = [];
        
        foreach ($query->get() as $group) {
            $employeeCount = $group->researchLabs->sum(function($lab) {
                return $lab->employees_count ?? 0;
            });
            
            $studyCalendarCount = StudyCalendar::whereHas('employee.researchLab.researchGroup', function($q) use ($group) {
                $q->where('id', $group->id);
            })->count();

            $stats[] = [
                'name' => $group->name,
                'lab_count' => $group->research_labs_count,
                'employee_count' => $employeeCount,
                'study_calendar_count' => $studyCalendarCount,
                'completion_rate' => $employeeCount > 0 ? round(($studyCalendarCount / $employeeCount) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    public function getStudyProgramStats()
    {
        $query = StudyProgram::query()
            ->withCount(['studyDetails as study_count' => function($q) {
                $q->whereHas('studyCalendar');
            }]);

        $stats = [];
        
        foreach ($query->get() as $program) {
            $activeStudies = $program->studyDetails()
                ->whereHas('studyCalendar', function($q) {
                    $q->whereIn('workflow_state', [5, 6]); // ACTIVE, LEAVE
                })->count();

            $completedStudies = $program->studyDetails()
                ->whereHas('studyCalendar', function($q) {
                    $q->where('workflow_state', 7); // FINISHED
                })->count();

            $stats[] = [
                'name' => $program->name,
                'total_studies' => $program->study_count,
                'active_studies' => $activeStudies,
                'completed_studies' => $completedStudies,
                'completion_rate' => $program->study_count > 0 ? round(($completedStudies / $program->study_count) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    public function getTimelineData()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $data = WorkflowHistory::selectRaw('
                DATE(workflow_histories.created_at) as date,
                workflow_name,
                COUNT(*) as transition_count
            ')
            ->where('workflow_histories.created_at', '>=', $startDate)
            ->groupBy('date', 'workflow_name')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $timeline = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= Carbon::now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayData = $data->get($dateStr, collect());
            
            $studyCalendarCount = $dayData->where('workflow_name', 'study_calendar')->sum('transition_count');
            $verificationByStaffCount = $dayData->where('workflow_name', 'verification_by_staff')->sum('transition_count');
            $verificationByManagementCount = $dayData->where('workflow_name', 'verification_by_management')->sum('transition_count');
            $totalCount = $studyCalendarCount + $verificationByStaffCount + $verificationByManagementCount;
            
            $timeline[] = [
                'date' => $currentDate->format('M d'),
                'count' => $totalCount,
                'study_calendar' => $studyCalendarCount,
                'verification_by_staff' => $verificationByStaffCount,
                'verification_by_management' => $verificationByManagementCount,
            ];
            
            $currentDate->addDay();
        }

        return $timeline;
    }

    protected function applyFilters($query)
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        if ($this->selectedResearchGroup) {
            $query->whereHas('employee.researchLab.researchGroup', function($q) {
                $q->where('id', $this->selectedResearchGroup);
            });
        }

        if ($this->selectedResearchLab) {
            $query->whereHas('employee.researchLab', function($q) {
                $q->where('id', $this->selectedResearchLab);
            });
        }





        // Use the table name to avoid ambiguity
        $tableName = $query->getModel()->getTable();
        $query->where($tableName . '.created_at', '>=', $startDate);
    }

    protected function getAverageStudyDuration($query)
    {
        $completedStudies = $query->clone()
            ->whereIn('workflow_state', [7, 8]) // FINISHED, DROP_OUT
            ->whereNotNull('graduation_date')
            ->get();

        if ($completedStudies->isEmpty()) {
            return 0;
        }

        $totalDays = $completedStudies->sum(function($study) {
            return Carbon::parse($study->study_start)->diffInDays($study->graduation_date);
        });

        return round($totalDays / $completedStudies->count());
    }

    protected function getDocumentTypeStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        // Get academic document types with explicit table references
        $academicTypes = \App\Models\AcademicDocument::query()
            ->where('documents.created_at', '>=', $startDate)
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->selectRaw('document_types.name, COUNT(*) as count')
            ->groupBy('document_types.id', 'document_types.name')
            ->get();

        // Get approval document types with explicit table references
        $approvalTypes = \App\Models\ApprovalDocument::query()
            ->where('documents.created_at', '>=', $startDate)
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->selectRaw('document_types.name, COUNT(*) as count')
            ->groupBy('document_types.id', 'document_types.name')
            ->get();

        // Combine and sort by count
        $combined = $academicTypes->concat($approvalTypes)
            ->groupBy('name')
            ->map(function($group) {
                return $group->sum('count');
            })
            ->sortByDesc(function($count) {
                return $count;
            })
            ->take(10)
            ->toArray();

        return $combined;
    }

    protected function getAverageDocumentProcessingTime()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        // Get verified academic documents
        $verifiedAcademicDocs = \App\Models\AcademicDocument::query()
            ->where('documents.created_at', '>=', $startDate)
            ->where('workflow_state', 3) // VERIFIED
            ->with('workflowHistory')
            ->get();

        // Get verified approval documents
        $verifiedApprovalDocs = \App\Models\ApprovalDocument::query()
            ->where('documents.created_at', '>=', $startDate)
            ->where('workflow_state', 3) // VERIFIED (or APPROVED for approval docs)
            ->with('workflowHistory')
            ->get();

        $allVerifiedDocs = $verifiedAcademicDocs->concat($verifiedApprovalDocs);

        if ($allVerifiedDocs->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($allVerifiedDocs as $doc) {
            $submitHistory = $doc->workflowHistory()
                ->where('to_state', 2) // PENDING
                ->first();
            
            $verifyHistory = $doc->workflowHistory()
                ->where('to_state', 3) // VERIFIED/APPROVED
                ->first();

            if ($submitHistory && $verifyHistory) {
                $totalDays += Carbon::parse($submitHistory->created_at)
                    ->diffInDays($verifyHistory->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count) : 0;
    }

    protected function getUserWorkflowStats($query)
    {
        return $query->clone()
            ->join('users', 'workflow_histories.user_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(*) as transition_count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('transition_count')
            ->limit(10)
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->name => $item->transition_count];
            })
            ->toArray();
    }

    protected function getPeakActivityDay($query)
    {
        $peakDay = $query->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderByDesc('count')
            ->first();

        return $peakDay ? [
            'date' => Carbon::parse($peakDay->date)->format('M d, Y'),
            'count' => $peakDay->count
        ] : null;
    }

    public function getResearchGroups()
    {
        return ResearchGroup::orderBy('name')->get();
    }

    public function getResearchLabs()
    {
        $query = ResearchLab::orderBy('name');
        
        if ($this->selectedResearchGroup) {
            $query->where('research_group_id', $this->selectedResearchGroup);
        }
        
        return $query->get();
    }





    public function render()
    {
        return view('livewire.monitoring.analytics', [
            'researchGroups' => $this->getResearchGroups(),
            'researchLabs' => $this->getResearchLabs(),
        ]);
    }
}
