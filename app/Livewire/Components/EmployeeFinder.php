<?php

namespace App\Livewire\Components;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeFinder extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedEmployeeId = null;
    public $selectedEmployee = null;
    public $showEmployeeModal = false;
    public $perPage = 10;
    public $page = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'employeeSelected' => 'handleEmployeeSelected',
        'clearEmployeeSelection' => 'clearSelection'
    ];

    public function mount($selectedEmployeeId = null)
    {
        if ($selectedEmployeeId) {
            $this->selectedEmployeeId = $selectedEmployeeId;
            $this->selectedEmployee = Employee::with(['user', 'studyPrograms'])->find($selectedEmployeeId);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openEmployeeModal()
    {
        $this->showEmployeeModal = true;
    }

    public function closeEmployeeModal()
    {
        $this->showEmployeeModal = false;
    }

    public function selectEmployee($employeeId)
    {
        $employee = Employee::with(['user', 'studyPrograms'])->find($employeeId);

        if ($employee) {
            $this->selectedEmployeeId = $employeeId;
            $this->selectedEmployee = $employee;
            $this->closeEmployeeModal();

            // Emit event to parent component
            $this->dispatch('employeeSelected', [
                'employeeId' => $employeeId,
                'employee' => $employee->toArray()
            ]);
        }
    }

    public function clearSelection()
    {
        $this->selectedEmployeeId = null;
        $this->selectedEmployee = null;
        $this->search = '';

        // Emit event to parent component
        $this->dispatch('employeeCleared');
    }

    public function handleEmployeeSelected($data)
    {
        $this->selectedEmployeeId = $data['employeeId'];
        $this->selectedEmployee = Employee::with(['user', 'studyPrograms'])->find($data['employeeId']);
    }

    public function getEmployeesProperty()
    {
        return Employee::with(['user', 'studyPrograms'])
            ->where('role', 'lecturer') // Only show lecturers for approval documents
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nidn', 'like', '%' . $this->search . '%')
                      ->orWhere('position', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('nidn')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.components.employee-finder', [
            'employees' => $this->employees,
        ]);
    }
}
