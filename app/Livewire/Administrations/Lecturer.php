<?php

namespace App\Livewire\Administrations;

use App\Models\Employee;
use App\Models\ResearchGroup;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Lecturer extends Component
{
    use WithPagination;

    public $researchGroup = null;
    public $searchTerm = '';

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'researchGroup' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function updatingResearchGroup()
    {
        $this->resetPage();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function openLecturerDetail($lecturerId)
    {
        $this->dispatch('lecturer-detail-modal:open', $lecturerId);
    }

    public function render()
    {
        Log::debug('Livewire render', [
            'filter_researchGroup' => $this->researchGroup,
            'searchTerm' => $this->searchTerm,
            'request' => request()->all(),
            'component' => static::class,
        ]);
        $query = Employee::with(['user', 'researchLab.researchGroup', 'studyCalendars' => function($q) {
            $q->latest();
        }]);

        if ($this->researchGroup) {
            $query->whereHas('researchLab.researchGroup', function ($q) {
                $q->where('id', $this->researchGroup);
            });
        }

        if ($this->searchTerm) {
            $search = $this->searchTerm;
            $query->where(function ($q) use ($search) {
                $q->where('nidn', 'like', "%$search%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%") ;
                  })
                  ->orWhereHas('researchLab', function ($labQuery) use ($search) {
                      $labQuery->where('name', 'like', "%$search%") ;
                  });
            });
        }

        Log::debug('Lecturer query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'filter_researchGroup' => $this->researchGroup,
            'component' => static::class,
            'request' => request()->all(),
        ]);

        $lecturers = $query->paginate(9);
        Log::debug('Lecturer search results', [
            'searchTerm' => $this->searchTerm,
            'count' => $lecturers->total(),
            'current_page' => $lecturers->currentPage(),
            'per_page' => $lecturers->perPage(),
        ]);
        $researchGroups = ResearchGroup::orderBy('name')->get();

        return view('livewire.administrations.lecturer', [
            'lecturers' => $lecturers,
            'researchGroups' => $researchGroups,
            'researchGroup' => $this->researchGroup,
        ]);
    }
}
