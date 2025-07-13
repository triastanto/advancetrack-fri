<?php

namespace App\Livewire\Monitoring;

use App\Models\Workflow\WorkflowHistory;
use App\Models\User;
use App\Models\Employee;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLog extends Component
{
    use WithPagination;

    // Filter properties
    public $selectedUser = '';
    public $selectedWorkflowType = '';
    public $selectedResearchGroup = '';
    public $selectedResearchLab = '';
    public $dateRange = '30'; // days
    public $searchTerm = '';

    // Audit data
    public $auditStats = [];
    public $userActivityStats = [];
    public $workflowTypeStats = [];

    protected $queryString = [
        'selectedUser' => ['except' => ''],
        'selectedWorkflowType' => ['except' => ''],
        'selectedResearchGroup' => ['except' => ''],
        'selectedResearchLab' => ['except' => ''],
        'dateRange' => ['except' => '30'],
        'searchTerm' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadAuditData();
    }

    public function updatedSelectedUser()
    {
        $this->loadAuditData();
    }

    public function updatedSelectedWorkflowType()
    {
        $this->loadAuditData();
    }

    public function updatedSelectedResearchGroup()
    {
        $this->selectedResearchLab = '';
        $this->loadAuditData();
    }

    public function updatedSelectedResearchLab()
    {
        $this->loadAuditData();
    }

    public function updatedDateRange()
    {
        $this->loadAuditData();
    }

    public function updatedSearchTerm()
    {
        $this->loadAuditData();
    }

    public function loadAuditData()
    {
        $this->auditStats = $this->getAuditStats();
        $this->userActivityStats = $this->getUserActivityStats();
        $this->workflowTypeStats = $this->getWorkflowTypeStats();
    }

    public function getAuditStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = WorkflowHistory::with(['user', 'workflowable'])
            ->where('created_at', '>=', $startDate);

        // Apply filters
        $this->applyFilters($query);

        $totalTransitions = $query->count();
        
        $stats = [
            'total_transitions' => $totalTransitions,
            'unique_users' => $query->clone()->distinct('user_id')->count(),
            'unique_workflows' => $query->clone()->distinct('workflow_name')->count(),
            'avg_transitions_per_day' => $this->dateRange > 0 ? round($totalTransitions / $this->dateRange, 1) : 0,
            'most_active_day' => $this->getMostActiveDay($query),
            'most_active_user' => $this->getMostActiveUser($query),
            'most_common_workflow' => $this->getMostCommonWorkflow($query),
        ];

        return $stats;
    }

    public function getWorkflowHistory()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = WorkflowHistory::with([
            'user.employee.researchLab.researchGroup',
            'workflowable.employee.user',
            'workflowable.employee.researchLab.researchGroup'
        ])
        ->where('created_at', '>=', $startDate)
        ->orderBy('created_at', 'desc');

        // Apply filters
        $this->applyFilters($query);

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                             ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                })
                ->orWhereHas('workflowable.employee.user', function($employeeQuery) {
                    $employeeQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                                 ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            });
        }

        return $query->paginate(20);
    }

    public function getUserActivityStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = WorkflowHistory::with(['user.employee'])
            ->where('created_at', '>=', $startDate);

        // Apply filters
        $this->applyFilters($query);

        $userStats = $query->selectRaw('user_id, COUNT(*) as transition_count')
            ->groupBy('user_id')
            ->orderBy('transition_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($stat) use ($startDate) {
                $user = User::with('employee.researchLab.researchGroup')->find($stat->user_id);
                return [
                    'user' => $user,
                    'transition_count' => $stat->transition_count,
                    'last_activity' => WorkflowHistory::where('user_id', $stat->user_id)
                        ->where('created_at', '>=', $startDate)
                        ->latest()
                        ->first()?->created_at,
                ];
            });

        return $userStats;
    }

    public function getWorkflowTypeStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = WorkflowHistory::where('created_at', '>=', $startDate);

        // Apply filters
        $this->applyFilters($query);

        $workflowStats = $query->selectRaw('workflow_name, COUNT(*) as transition_count')
            ->groupBy('workflow_name')
            ->orderBy('transition_count', 'desc')
            ->get()
            ->map(function($stat) use ($startDate) {
                return [
                    'workflow_name' => $stat->workflow_name,
                    'display_name' => $this->getWorkflowDisplayName($stat->workflow_name),
                    'transition_count' => $stat->transition_count,
                    'unique_users' => WorkflowHistory::where('workflow_name', $stat->workflow_name)
                        ->where('created_at', '>=', $startDate)
                        ->distinct('user_id')
                        ->count(),
                    'last_activity' => WorkflowHistory::where('workflow_name', $stat->workflow_name)
                        ->where('created_at', '>=', $startDate)
                        ->latest()
                        ->first()?->created_at,
                ];
            });

        return $workflowStats;
    }

    protected function applyFilters($query)
    {
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        if ($this->selectedWorkflowType) {
            $query->where('workflow_name', $this->selectedWorkflowType);
        }

        if ($this->selectedResearchGroup) {
            $query->whereHas('workflowable.employee.researchLab.researchGroup', function($q) {
                $q->where('id', $this->selectedResearchGroup);
            });
        }

        if ($this->selectedResearchLab) {
            $query->whereHas('workflowable.employee', function($q) {
                $q->where('research_lab_id', $this->selectedResearchLab);
            });
        }
    }

    protected function getMostActiveDay($query)
    {
        $mostActiveDay = $query->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->first();

        return $mostActiveDay ? $mostActiveDay->date : null;
    }

    protected function getMostActiveUser($query)
    {
        $mostActiveUser = $query->clone()
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->first();

        if ($mostActiveUser) {
            $user = User::find($mostActiveUser->user_id);
            return $user ? $user->name : 'Unknown User';
        }

        return null;
    }

    protected function getMostCommonWorkflow($query)
    {
        $mostCommonWorkflow = $query->clone()
            ->selectRaw('workflow_name, COUNT(*) as count')
            ->groupBy('workflow_name')
            ->orderBy('count', 'desc')
            ->first();

        return $mostCommonWorkflow ? $this->getWorkflowDisplayName($mostCommonWorkflow->workflow_name) : null;
    }

    protected function getWorkflowDisplayName($workflowName)
    {
        return match($workflowName) {
            'study_calendar' => 'Kalender Studi',
            'verification_by_staff' => 'Verifikasi oleh Staff',
            'verification_by_management' => 'Verifikasi oleh Manajemen',
            default => ucwords(str_replace('_', ' ', $workflowName))
        };
    }

    public function getUsers()
    {
        return User::with('employee')
            ->whereIn('id', function($query) {
                $query->select('user_id')
                    ->from('workflow_histories')
                    ->distinct();
            })
            ->orderBy('name')
            ->get();
    }

    public function getWorkflowTypes()
    {
        return WorkflowHistory::select('workflow_name')
            ->distinct()
            ->orderBy('workflow_name')
            ->pluck('workflow_name');
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

    public function getTransitionDisplayName($history)
    {
        $transitionName = $history->transition_name ?? 'State Change';
        
        return match($history->workflow_name) {
            'study_calendar' => $this->getStudyCalendarTransitionName($history),
            'verification_by_staff' => $this->getVerificationTransitionName($history),
            'verification_by_management' => $this->getVerificationTransitionName($history),
            default => $transitionName
        };
    }

    protected function getStudyCalendarTransitionName($history)
    {
        return match($history->transition) {
            1 => 'Submit untuk Approval',
            2 => 'Approve',
            3 => 'Reject',
            4 => 'Aktifkan Studi',
            5 => 'Selesaikan Studi',
            6 => 'Drop Out',
            default => 'State Change'
        };
    }

    protected function getVerificationTransitionName($history)
    {
        return match($history->transition) {
            1 => 'Submit untuk Verifikasi',
            2 => 'Verifikasi',
            3 => 'Reject',
            4 => 'Approve',
            default => 'State Change'
        };
    }

    public function render()
    {
        return view('livewire.monitoring.audit-log', [
            'users' => $this->getUsers(),
            'workflowTypes' => $this->getWorkflowTypes(),
            'researchGroups' => $this->getResearchGroups(),
            'researchLabs' => $this->getResearchLabs(),
            'workflowHistory' => $this->getWorkflowHistory(),
        ]);
    }
}
