<?php

namespace App\Livewire\Components;

use App\Models\Employee;
use App\Models\Education;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class LecturerDetailModal extends Component
{
    public $isOpen = false;
    public $lecturer = null;
    public $educations = [];
    public $studyCalendars = [];
    public $courseResponsibilities = [];

    protected $listeners = [
        'lecturer-detail-modal:open' => 'open',
        'lecturer-detail-modal:close' => 'close'
    ];

    public function open($lecturerId)
    {
        try {
            $this->lecturer = Employee::with([
                'user',
                'researchLab.researchGroup',
                'studyPrograms',
                'educations',
                'studyCalendars.studyDetail.studyProgram',
                'studyCalendars.studyDetail.promotors',
                'courseResponsibilities'
            ])->find($lecturerId);

            if (!$this->lecturer) {
                session()->flash('error', 'Dosen tidak ditemukan.');
                return;
            }

            $this->educations = $this->lecturer->educations()->orderBy('graduation_year', 'desc')->get();
            $this->studyCalendars = $this->lecturer->studyCalendars()->with(['studyDetail.studyProgram', 'studyDetail.promotors'])->get();
            $this->courseResponsibilities = $this->lecturer->courseResponsibilities()->orderBy('academic_year', 'desc')->orderBy('semester')->get();
            
            $this->isOpen = true;

            Log::info('Lecturer detail modal opened', [
                'lecturer_id' => $lecturerId,
                'lecturer_name' => $this->lecturer->user->name ?? 'Unknown'
            ]);
        } catch (\Exception $e) {
            Log::error('Error opening lecturer detail modal', [
                'lecturer_id' => $lecturerId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Terjadi kesalahan saat membuka detail dosen.');
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->lecturer = null;
        $this->educations = [];
        $this->studyCalendars = [];
        $this->courseResponsibilities = [];
    }

    public function getGenderLabel($gender)
    {
        return match($gender) {
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            'other' => 'Lainnya',
            default => 'Tidak diketahui'
        };
    }

    public function getRoleLabel($role)
    {
        return match($role) {
            'lecturer' => 'Dosen',
            'hr_finance_staff' => 'Staff HR/Keuangan',
            'head_of_hr_finance' => 'Kepala HR/Keuangan',
            'fri_vice_dean' => 'Wakil Dekan FRI',
            'head_of_study_program' => 'Kepala Program Studi',
            'head_of_research_group' => 'Kepala Kelompok Riset',
            default => 'Tidak diketahui'
        };
    }

    public function getWorkflowStateLabel($state)
    {
        $stateMap = [
            'draft' => ['label' => 'Draft', 'color' => 'bg-gray-400'],
            'pending' => ['label' => 'Menunggu Persetujuan', 'color' => 'bg-yellow-400'],
            'approved' => ['label' => 'Disetujui', 'color' => 'bg-blue-400'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-400'],
            'active' => ['label' => 'Aktif', 'color' => 'bg-green-500'],
            'leave' => ['label' => 'Cuti', 'color' => 'bg-yellow-500'],
            'finished' => ['label' => 'Selesai', 'color' => 'bg-green-700'],
            'drop_out' => ['label' => 'Drop Out', 'color' => 'bg-red-600'],
        ];

        return $stateMap[$state] ?? ['label' => 'Tidak diketahui', 'color' => 'bg-gray-300'];
    }

    public function render()
    {
        return view('livewire.components.lecturer-detail-modal');
    }
} 