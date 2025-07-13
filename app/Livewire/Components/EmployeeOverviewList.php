<?php

namespace App\Livewire\Components;

use App\Models\Employee;
use App\Models\StudyCalendar;
use App\Models\ApprovalDocument;
use App\Constants\DocumentTypeConstants;
use App\Models\DocumentType;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class EmployeeOverviewList extends Component
{
    use WithPagination;

    public $selectedEmployeeId;
    public $searchTerm = '';
    public $statusFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $listeners = [
        'document:uploaded' => 'refreshData',
        'document:deleted' => 'refreshData',
        'document:submitted' => 'refreshData',
        'workflow:transition-applied' => 'refreshData',
    ];

    public function mount()
    {
        $this->selectedEmployeeId = null;
    }

    public function refreshData()
    {
        $this->resetPage();
    }

    public function selectEmployee($employeeId)
    {
        $this->dispatch('employeeSelected', ['employeeId' => $employeeId]);
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getEmployeesWithStudyCalendars()
    {
        try {
            $query = Employee::with(['user', 'studyCalendar'])
                ->whereHas('studyCalendar')
                ->whereHas('user');

            // Apply search filter
            if ($this->searchTerm) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            }

            // Apply status filter
            if ($this->statusFilter) {
                $query->whereHas('studyCalendar', function($q) {
                    $q->where('workflow_state', $this->statusFilter);
                });
            }

            // Apply sorting
            switch ($this->sortBy) {
                case 'name':
                    $query->join('users', 'employees.user_id', '=', 'users.id')
                          ->orderBy('users.name', $this->sortDirection);
                    break;
                case 'status':
                    $query->join('study_calendars', 'employees.id', '=', 'study_calendars.employee_id')
                          ->orderBy('study_calendars.workflow_state', $this->sortDirection);
                    break;
                case 'last_updated':
                    $query->join('study_calendars', 'employees.id', '=', 'study_calendars.employee_id')
                          ->orderBy('study_calendars.updated_at', $this->sortDirection);
                    break;
                default:
                    $query->join('users', 'employees.user_id', '=', 'users.id')
                          ->orderBy('users.name', 'asc');
            }

            return $query->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error getting employees with study calendars: ' . $e->getMessage());
            return collect();
        }
    }

    public function getApprovalDocumentStatus($employeeId)
    {
        try {
            $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
            $approvalTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

            $uploadedDocuments = ApprovalDocument::where('employee_id', $employeeId)
                ->whereIn('document_type_id', $approvalTypeIds)
                ->get();

            $totalRequired = count($approvalTypeIds);
            $uploadedCount = $uploadedDocuments->count();
            $approvedCount = $uploadedDocuments->where('workflow_state', 4)->count();

            return [
                'total' => $totalRequired,
                'uploaded' => $uploadedCount,
                'approved' => $approvedCount,
                'pending' => $uploadedCount - $approvedCount,
                'missing' => $totalRequired - $uploadedCount,
                'completion_percentage' => $totalRequired > 0 ? ($uploadedCount / $totalRequired) * 100 : 0,
                'approval_percentage' => $totalRequired > 0 ? ($approvedCount / $totalRequired) * 100 : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting approval document status: ' . $e->getMessage());
            return [
                'total' => 0,
                'uploaded' => 0,
                'approved' => 0,
                'pending' => 0,
                'missing' => 0,
                'completion_percentage' => 0,
                'approval_percentage' => 0,
            ];
        }
    }

    public function getWorkflowStateLabel($state)
    {
        return match($state) {
            1 => 'Draft',
            2 => 'Pending Approval',
            3 => 'Approved',
            4 => 'Active',
            5 => 'On Leave',
            6 => 'Completed',
            7 => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getWorkflowStateColor($state)
    {
        return match($state) {
            1 => 'gray',
            2 => 'yellow',
            3 => 'green',
            4 => 'blue',
            5 => 'orange',
            6 => 'purple',
            7 => 'red',
            default => 'gray'
        };
    }

    public function render()
    {
        $employees = $this->getEmployeesWithStudyCalendars();
        
        return view('livewire.components.employee-overview-list', [
            'employees' => $employees
        ]);
    }
} 