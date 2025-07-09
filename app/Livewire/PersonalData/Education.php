<?php

namespace App\Livewire\PersonalData;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Education as EducationModel;

class Education extends Component
{
    public $educations = [];
    public $degree;
    public $major;
    public $institution;
    public $graduation_year;
    public $gpa;
    public $editId = null;

    public function mount()
    {
        $user = Auth::user();
        $employee = $user->employee;
        if ($employee) {
            $this->educations = $employee->educations()->orderBy('graduation_year', 'desc')->get()->toArray();
        }
    }

    public function resetForm()
    {
        $this->degree = null;
        $this->major = null;
        $this->institution = null;
        $this->graduation_year = null;
        $this->gpa = null;
        $this->editId = null;
    }

    public function save()
    {
        $this->validate([
            'degree' => 'required|string|max:50',
            'major' => 'required|string|max:100',
            'institution' => 'required|string|max:150',
            'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'gpa' => 'nullable|numeric|min:0|max:4',
        ]);

        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee) return;

        if ($this->editId) {
            $education = EducationModel::find($this->editId);
            if ($education && $education->employee_id === $employee->id) {
                $education->update([
                    'degree' => $this->degree,
                    'major' => $this->major,
                    'institution' => $this->institution,
                    'graduation_year' => $this->graduation_year,
                    'gpa' => $this->gpa,
                ]);
            }
        } else {
            EducationModel::create([
                'employee_id' => $employee->id,
                'degree' => $this->degree,
                'major' => $this->major,
                'institution' => $this->institution,
                'graduation_year' => $this->graduation_year,
                'gpa' => $this->gpa,
            ]);
        }
        $this->mount();
        $this->resetForm();
        session()->flash('success', 'Education record saved successfully.');
    }

    public function edit($id)
    {
        $education = EducationModel::find($id);
        $user = Auth::user();
        $employee = $user->employee;
        if ($education && $education->employee_id === $employee->id) {
            $this->editId = $education->id;
            $this->degree = $education->degree;
            $this->major = $education->major;
            $this->institution = $education->institution;
            $this->graduation_year = $education->graduation_year;
            $this->gpa = $education->gpa;
        }
    }

    public function delete($id)
    {
        $education = EducationModel::find($id);
        $user = Auth::user();
        $employee = $user->employee;
        if ($education && $education->employee_id === $employee->id) {
            $education->delete();
            $this->mount();
            session()->flash('success', 'Education record deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.personal-data.education');
    }
} 