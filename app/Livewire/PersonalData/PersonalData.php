<?php

namespace App\Livewire\PersonalData;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class PersonalData extends Component
{
    use WithFileUploads;

    public $name;
    public $nidn;
    public $position;
    public $role;
    public $birth_place;
    public $birth_date;
    public $gender;
    public $functional_position;
    public $origin_address;
    public $contact_phone;
    public $employee;
    public $photo;
    public $photo_preview;

    public function mount()
    {
        $user = Auth::user();
        $this->employee = $user->employee;
        if ($this->employee) {
            $this->name = $user->name;
            $this->nidn = $this->employee->nidn;
            $this->position = $this->employee->position;
            $this->role = $this->employee->role;
            $this->birth_place = $this->employee->birth_place;
            $this->birth_date = $this->employee->birth_date;
            $this->gender = $this->employee->gender;
            $this->functional_position = $this->employee->functional_position;
            $this->origin_address = $this->employee->origin_address;
            $this->contact_phone = $this->employee->contact_phone;
            $this->photo_preview = $this->employee->photo ?? null;
        }
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);
        $this->photo_preview = $this->photo->temporaryUrl();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nidn' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'role' => 'required|string',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'functional_position' => 'nullable|string|max:255',
            'origin_address' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->save();

        if ($this->employee) {
            $this->employee->nidn = $this->nidn;
            $this->employee->position = $this->position;
            $this->employee->role = $this->role;
            $this->employee->birth_place = $this->birth_place;
            $this->employee->birth_date = $this->birth_date;
            $this->employee->gender = $this->gender;
            $this->employee->functional_position = $this->functional_position;
            $this->employee->origin_address = $this->origin_address;
            $this->employee->contact_phone = $this->contact_phone;
            if ($this->photo) {
                $path = $this->photo->store('photos', 'public');
                $this->employee->photo = $path;
            }
            $this->employee->save();
        }

        session()->flash('success', 'Personal data updated successfully.');
    }

    public function render()
    {
        return view('livewire.personal-data.personal-data');
    }
}
