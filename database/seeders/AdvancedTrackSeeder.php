<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\StudyProgram;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\StudyCalendar;
use App\Constants\DocumentTypeConstants;

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
            // Generate at least one document for every predefined document type
            foreach (DocumentTypeConstants::getAllNames() as $documentTypeName) {
                // Create 1-3 documents for each type to add variety
                $documentsToCreate = rand(1, 3);

                for ($i = 0; $i < $documentsToCreate; $i++) {
                    $documentType = DocumentType::where('name', $documentTypeName)->first();

                    if ($documentType) {
                        $documentData = ['employee_id' => $employee->id];

                        // Add specific fields based on document type
                        if ($documentTypeName === 'semester_report') {
                            $documentData['semester'] = rand(1, 8);
                            $documentData['year'] = rand(2023, 2025);
                        } elseif ($documentTypeName === 'pid') {
                            $documentData['upload_date'] = now()->subDays(rand(1, 365));
                        }

                        Document::factory()->withDocumentType($documentTypeName)->create($documentData);
                    }
                }
            }

            // Create additional random documents to simulate realistic variety
            Document::factory(rand(2, 5))->withDocumentType()->create(['employee_id' => $employee->id]);

            StudyCalendar::factory()->create(['employee_id' => $employee->id]);
        }
    }
}
