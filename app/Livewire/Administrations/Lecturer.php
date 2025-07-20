<?php

namespace App\Livewire\Administrations;

use App\Models\Employee;
use App\Models\ResearchGroup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\LecturerApprovedMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;

class Lecturer extends Component
{
    use WithPagination;

    public $researchGroup = null;
    public $searchTerm = '';
    public $showPendingOnly = false;
    public $isHrFinanceStaff = false;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'researchGroup' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->isHrFinanceStaff = Auth::check() && in_array(optional(Auth::user()->employee)->role, ['hr_finance_staff', 'head_of_hr_finance']);
    }

    #[On('approveLecturer')]
    public function approveLecturer($lecturerId)
    {
        if (!$this->isHrFinanceStaff) return;
        $lecturer = Employee::with('user')->find($lecturerId);
        if ($lecturer && !$lecturer->is_approved) {
            $lecturer->approve(Auth::id());
            // Notify the lecturer by email
            if ($lecturer->user && $lecturer->user->email) {
                Mail::to($lecturer->user->email)->send(new LecturerApprovedMail($lecturer->user->name));
            }
            session()->flash('success', 'Dosen berhasil divalidasi.');
            $this->resetPage();
            $this->dispatch('closeLecturerModal');
        }
    }

    public function rejectLecturer($lecturerId)
    {
        // Optionally implement rejection logic
    }

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

        if ($this->showPendingOnly) {
            $query->where('is_approved', false);
        }

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
            'isHrFinanceStaff' => $this->isHrFinanceStaff,
            'showPendingOnly' => $this->showPendingOnly,
        ]);
    }
}
