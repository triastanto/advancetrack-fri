<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $role = $user?->employee?->role ?? null;
        $studyInfo = null;
        if ($user && $user->employee) {
            // Use trait or method to get active study info for the employee
            $employee = $user->employee;
            if (method_exists($employee, 'studyCalendars')) {
                $activeStudy = $employee->studyCalendars()
                    ->where('workflow_state', 5) // ACTIVE
                    ->latest()
                    ->first();
                if (!$activeStudy) {
                    $activeStudy = $employee->studyCalendars()->latest()->first();
                }
                if ($activeStudy) {
                    $studyProgram = $employee->studyPrograms()->first();
                    $programName = $studyProgram ? $studyProgram->name : 'Tidak tersedia';
                    $startDate = $activeStudy->study_start ? \Carbon\Carbon::parse($activeStudy->study_start) : null;
                    $estimatedEnd = $activeStudy->estimated_study_end ? \Carbon\Carbon::parse($activeStudy->estimated_study_end) : null;
                    $now = now();
                    $monthsDiff = $startDate ? $startDate->diffInMonths($now) : 0;
                    $currentSemester = $startDate ? max(1, floor($monthsDiff / 6) + 1) : null;
                    $studyInfo = [
                        'program' => $programName,
                        'status' => $activeStudy->workflow_state,
                        'start_date' => $startDate ? $startDate->format('d M Y') : '-',
                        'estimated_end' => $estimatedEnd ? $estimatedEnd->format('d M Y') : '-',
                        'current_semester' => $currentSemester,
                        'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
                    ];
                }
            }
        }
        // Always pass studyInfo to the view, even if null
        return view('livewire.dashboard.dashboard', [
            'role' => $role,
            'studyInfo' => $studyInfo ?? [],
        ]);
    }
}