<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class EmployeeStudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = Employee::where('role', 'lecturer')->get();
        $studyPrograms = StudyProgram::all();

        if ($lecturers->isEmpty() || $studyPrograms->isEmpty()) {
            $this->command->info('No lecturers or study programs found.');
            return;
        }

        foreach ($lecturers as $lecturer) {
            // Randomly assign 1-2 study programs to each lecturer
            $programsToAssign = $studyPrograms->random(rand(1, min(2, $studyPrograms->count())));
            $lecturer->studyPrograms()->syncWithoutDetaching($programsToAssign->pluck('id')->toArray());
        }

        $this->command->info('Study programs assigned to lecturers.');
    }
}
