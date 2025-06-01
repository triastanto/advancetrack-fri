<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\StudyProgram;
use App\Models\Document;
use App\Models\SemesterReport;
use App\Models\ServiceBondAgreement;
use App\Models\StudyCalendar;

class AdvancedTrackSeeder extends Seeder
{
    public function run(): void
    {
        // Study Programs
        $studyPrograms = StudyProgram::factory(3)->create();

        // Users & Employees
        $users = User::factory(30)->create();
        $employees = collect();
        foreach ($users as $user) {
            $role = fake()->randomElement(['lecturer', 'fsdp_staff', 'head_of_affairs', 'vice_dean']);
            $employee = Employee::factory()->create([
                'user_id' => $user->id,
                'role' => $role,
            ]);
            $employees->push($employee);
        }

        // Attach study programs to employees (many-to-many, only for lecturers)
        foreach ($employees as $employee) {
            if ($employee->role === 'lecturer') {
                $programIds = $studyPrograms->random(rand(1, 3))->pluck('id')->toArray();
                $employee->studyPrograms()->attach($programIds);
            }
        }

        // Documents, Semester Reports, Service Bond Agreements, Study Calendars
        foreach ($employees as $employee) {
            Document::factory(rand(2, 6))->create(['employee_id' => $employee->id]);
            SemesterReport::factory(rand(1, 4))->create(['employee_id' => $employee->id]);
            ServiceBondAgreement::factory(rand(0, 2))->create(['employee_id' => $employee->id]);
            StudyCalendar::factory()->create(['employee_id' => $employee->id]);
        }
    }
}
