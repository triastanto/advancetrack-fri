<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\StudyProgram;
use App\Models\Document;
use App\Models\StudyCalendar;

class AdvancedTrackSeeder extends Seeder
{
    public function run(): void
    {
        // Study Programs
        $studyPrograms = StudyProgram::factory(3)->create();

        // Create employees with their associated users
        $employees = collect();
        $roles = ['lecturer', 'fsdp_staff', 'head_of_affairs', 'vice_dean'];

        // Create 30 employees with their respective users
        for ($i = 0; $i < 30; $i++) {
            $role = fake()->randomElement($roles);
            // Create a user first
            $user = User::factory()->create();

            // Then create an employee related to this user
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

        // Documents and Study Calendars
        foreach ($employees as $employee) {
            // Regular documents
            Document::factory(rand(2, 6))->create(['employee_id' => $employee->id]);

            // Semester reports (now as documents)
            for ($i = 0; $i < rand(1, 4); $i++) {
                Document::factory()->create([
                    'employee_id' => $employee->id,
                    'document_type' => 'semester_report',
                    'semester' => rand(1, 8),
                    'year' => rand(2023, 2025),
                ]);
            }

            // Service bond agreements (now as documents)
            for ($i = 0; $i < rand(0, 2); $i++) {
                Document::factory()->create([
                    'employee_id' => $employee->id,
                    'document_type' => 'service_bond_agreement',
                    'upload_date' => now()->subDays(rand(1, 365)),
                ]);
            }

            StudyCalendar::factory()->create(['employee_id' => $employee->id]);
        }
    }
}
