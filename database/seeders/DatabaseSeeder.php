<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed document types first (required for other seeders)
        $this->call(DocumentTypeSeeder::class);

        // Seed study programs
        $this->call(StudyProgramSeeder::class);

        // Seed users with their corresponding employee records
        $this->call(UserSeeder::class);
        
        // If there are users without employee records, create them
        $this->call(EmployeeSeeder::class);

        // Create many-to-many relationships between employees and study programs
        // This will also create study calendars for lecturers
        $this->call(EmployeeStudyProgramSeeder::class);

        // Create study calendars for any lecturers who might not have them
        // (This acts as a safety net)
        $this->call(StudyCalendarSeeder::class);

        // Seed new tables based on updated ERD
        $this->call(StudyDetailSeeder::class);
        $this->call(StudyPromotorSeeder::class);
        $this->call(SupervisorAssignmentSeeder::class);
        $this->call(CourseResponsibilitySeeder::class);
    }
}
