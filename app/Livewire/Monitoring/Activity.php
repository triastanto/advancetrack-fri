<?php

namespace App\Livewire\Monitoring;

use App\Models\Employee;
use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\Workflow\WorkflowHistory;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Activity extends Component
{
    use WithPagination;

    // Filter properties
    public $dateRange = '30'; // days
    public $selectedResearchGroup = '';
    public $selectedResearchLab = '';
    public $selectedActivityType = '';
    public $searchTerm = '';

    // Activity data
    public $activityStats = [];
    public $recentActivities = [];
    public $topActiveLecturers = [];

    protected $queryString = [
        'dateRange' => ['except' => '30'],
        'selectedResearchGroup' => ['except' => ''],
        'selectedResearchLab' => ['except' => ''],
        'selectedActivityType' => ['except' => ''],
        'searchTerm' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadActivityData();
    }

    public function updatedDateRange()
    {
        $this->loadActivityData();
    }

    public function updatedSelectedResearchGroup()
    {
        $this->selectedResearchLab = '';
        $this->loadActivityData();
    }

    public function updatedSelectedResearchLab()
    {
        $this->loadActivityData();
    }

    public function updatedSelectedActivityType()
    {
        $this->loadActivityData();
    }

    public function updatedSearchTerm()
    {
        $this->loadActivityData();
    }

    public function loadActivityData()
    {
        $this->activityStats = $this->getActivityStats();
        $this->recentActivities = $this->getRecentActivities();
        $this->topActiveLecturers = $this->getTopActiveLecturers();
    }

    public function getActivityStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $totalLecturers = $query->count();
        
        // Get study calendar activities
        $studyCalendarQuery = StudyCalendar::whereHas('employee', function($q) use ($query) {
            $q->whereIn('id', $query->pluck('id'));
        })->where('created_at', '>=', $startDate);

        // Get document activities
        $academicDocQuery = AcademicDocument::whereHas('employee', function($q) use ($query) {
            $q->whereIn('id', $query->pluck('id'));
        })->where('created_at', '>=', $startDate);

        $approvalDocQuery = ApprovalDocument::whereHas('employee', function($q) use ($query) {
            $q->whereIn('id', $query->pluck('id'));
        })->where('created_at', '>=', $startDate);

        // Get workflow transitions
        $workflowQuery = WorkflowHistory::whereHas('workflowable.employee', function($q) use ($query) {
            $q->whereIn('id', $query->pluck('id'));
        })->where('created_at', '>=', $startDate);

        $stats = [
            'total_lecturers' => $totalLecturers,
            'active_lecturers' => $this->getActiveLecturersCount($query, $startDate),
            'study_calendars_created' => $studyCalendarQuery->count(),
            'documents_submitted' => $academicDocQuery->count() + $approvalDocQuery->count(),
            'workflow_transitions' => $workflowQuery->count(),
            'avg_activity_per_lecturer' => $totalLecturers > 0 ? round(($studyCalendarQuery->count() + $academicDocQuery->count() + $approvalDocQuery->count()) / $totalLecturers, 1) : 0,
            'most_active_day' => $this->getMostActiveDay($startDate),
        ];

        return $stats;
    }

    public function getRecentActivities()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $activities = collect();

        // Get study calendar activities
        $studyCalendars = StudyCalendar::with(['employee.user', 'employee.researchLab.researchGroup'])
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(function($calendar) {
                return [
                    'type' => 'study_calendar',
                    'action' => 'created',
                    'employee' => $calendar->employee,
                    'description' => 'Membuat Masa Studi',
                    'date' => $calendar->created_at,
                    'details' => [
                        'study_start' => $calendar->study_start,
                        'estimated_end' => $calendar->estimated_study_end,
                    ]
                ];
            });

        // Get document activities
        $academicDocs = AcademicDocument::with(['employee.user', 'employee.researchLab.researchGroup', 'documentType'])
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(function($doc) {
                return [
                    'type' => 'document',
                    'action' => 'submitted',
                    'employee' => $doc->employee,
                    'description' => 'Mengunggah dokumen: ' . $doc->documentType->display_name,
                    'date' => $doc->created_at,
                    'details' => [
                        'document_type' => $doc->documentType->display_name,
                        'file_name' => $doc->file_name,
                    ]
                ];
            });

        $approvalDocs = ApprovalDocument::with(['employee.user', 'employee.researchLab.researchGroup', 'documentType'])
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(function($doc) {
                return [
                    'type' => 'document',
                    'action' => 'submitted',
                    'employee' => $doc->employee,
                    'description' => 'Mengunggah dokumen: ' . $doc->documentType->display_name,
                    'date' => $doc->created_at,
                    'details' => [
                        'document_type' => $doc->documentType->display_name,
                        'file_name' => $doc->file_name,
                    ]
                ];
            });

        // Get workflow transitions
        $workflowHistory = WorkflowHistory::with(['user', 'workflowable.employee.user', 'workflowable.employee.researchLab.researchGroup'])
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(function($history) {
                $workflowable = $history->workflowable;
                if (!$workflowable || !$workflowable->employee) {
                    return null;
                }

                $transitionName = $history->transition_name ?? 'State Change';
                
                return [
                    'type' => 'workflow',
                    'action' => 'transition',
                    'employee' => $workflowable->employee,
                    'description' => 'Transisi workflow: ' . $transitionName,
                    'date' => $history->created_at,
                    'details' => [
                        'from_state' => $history->from_state,
                        'to_state' => $history->to_state,
                        'transition' => $transitionName,
                        'workflow_type' => class_basename($workflowable),
                    ]
                ];
            })
            ->filter();

        // Combine and sort all activities
        $activities = $studyCalendars->concat($academicDocs)->concat($approvalDocs)->concat($workflowHistory);
        
        // Apply search filter
        if ($this->searchTerm) {
            $activities = $activities->filter(function($activity) {
                return str_contains(strtolower($activity['employee']['user']['name']), strtolower($this->searchTerm)) ||
                       str_contains(strtolower($activity['employee']['nidn']), strtolower($this->searchTerm));
            });
        }

        // Apply activity type filter
        if ($this->selectedActivityType) {
            $activities = $activities->filter(function($activity) {
                return $activity['type'] === $this->selectedActivityType;
            });
        }

        return $activities->sortByDesc('date')->take(50);
    }

    public function getTopActiveLecturers()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $lecturers = $query->get()->map(function($lecturer) use ($startDate) {
            $studyCalendarCount = StudyCalendar::where('employee_id', $lecturer->id)
                ->where('created_at', '>=', $startDate)
                ->count();

            $documentCount = AcademicDocument::where('employee_id', $lecturer->id)
                ->where('created_at', '>=', $startDate)
                ->count() + 
                ApprovalDocument::where('employee_id', $lecturer->id)
                ->where('created_at', '>=', $startDate)
                ->count();

            $workflowCount = WorkflowHistory::whereHas('workflowable', function($q) use ($lecturer) {
                $q->where('employee_id', $lecturer->id);
            })->where('created_at', '>=', $startDate)->count();

            $totalActivity = $studyCalendarCount + $documentCount + $workflowCount;

            return [
                'employee' => $lecturer,
                'study_calendar_count' => $studyCalendarCount,
                'document_count' => $documentCount,
                'workflow_count' => $workflowCount,
                'total_activity' => $totalActivity,
            ];
        });

        return $lecturers->sortByDesc('total_activity')->take(10);
    }

    protected function applyFilters($query)
    {
        if ($this->selectedResearchGroup) {
            $query->whereHas('researchLab.researchGroup', function($q) {
                $q->where('id', $this->selectedResearchGroup);
            });
        }

        if ($this->selectedResearchLab) {
            $query->where('research_lab_id', $this->selectedResearchLab);
        }
    }

    protected function getActiveLecturersCount($query, $startDate)
    {
        $lecturerIds = $query->pluck('id');

        $activeLecturers = Employee::whereIn('id', $lecturerIds)
            ->where(function($q) use ($startDate) {
                $q->whereHas('studyCalendars', function($sc) use ($startDate) {
                    $sc->where('created_at', '>=', $startDate);
                })
                ->orWhereHas('academicDocuments', function($d) use ($startDate) {
                    $d->where('created_at', '>=', $startDate);
                })
                ->orWhereHas('approvalDocuments', function($d) use ($startDate) {
                    $d->where('created_at', '>=', $startDate);
                });
            })
            ->count();

        return $activeLecturers;
    }

    protected function getMostActiveDay($startDate)
    {
        $mostActiveDay = WorkflowHistory::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->first();

        return $mostActiveDay ? $mostActiveDay->date : null;
    }

    public function getResearchGroups()
    {
        return ResearchGroup::orderBy('name')->get();
    }

    public function getResearchLabs()
    {
        $query = ResearchLab::with('researchGroup');
        
        if ($this->selectedResearchGroup) {
            $query->where('research_group_id', $this->selectedResearchGroup);
        }
        
        return $query->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.monitoring.activity', [
            'researchGroups' => $this->getResearchGroups(),
            'researchLabs' => $this->getResearchLabs(),
        ]);
    }
}
