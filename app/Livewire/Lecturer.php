<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\StudyProgram;
use Livewire\Component;
use Livewire\WithPagination;

class Lecturer extends Component
{
    use WithPagination;

    public $studyProgram = null;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStudyProgram()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::with('user', 'studyPrograms');

        if ($this->studyProgram) {
            $query->whereHas('studyPrograms', function ($q) {
                $q->where('id', $this->studyProgram);
            });
        }

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        $lecturers = $query->paginate(9);
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('livewire.lecturer', [
            'lecturers' => $lecturers,
            'studyPrograms' => $studyPrograms
        ]);
    }
}
