<?php

namespace Database\Seeders;

use App\Models\CourseResponsibility;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class CourseResponsibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = Employee::whereIn('role', [
            'lecturer',
            'head_of_study_program',
            'head_of_research_group'
        ])->get();

        $courses = $this->getCourseList();

        foreach ($lecturers as $lecturer) {
            // Each lecturer teaches 2-4 courses
            $numberOfCourses = rand(2, 4);
            $assignedCourses = collect($courses)->random($numberOfCourses);

            foreach ($assignedCourses as $course) {
                CourseResponsibility::create([
                    'employee_id' => $lecturer->id,
                    'course_name' => $course,
                    'semester' => $this->getRandomSemester(),
                    'academic_year' => $this->getCurrentAcademicYear(),
                ]);
            }
        }
    }

    private function getCourseList(): array
    {
        return [
            // Mathematics Courses
            'Kalkulus I',
            'Kalkulus II',
            'Kalkulus III',
            'Aljabar Linear',
            'Analisis Real',
            'Analisis Kompleks',
            'Statistika',
            'Geometri Analitik',
            'Persamaan Diferensial',
            'Matematika Diskrit',
            
            // Physics Courses
            'Fisika Dasar I',
            'Fisika Dasar II',
            'Mekanika Klasik',
            'Termodinamika',
            'Elektromagnetisme',
            'Mekanika Kuantum',
            'Fisika Modern',
            'Optik',
            
            // Chemistry Courses
            'Kimia Dasar',
            'Kimia Organik I',
            'Kimia Organik II',
            'Kimia Anorganik',
            'Kimia Fisik',
            'Biokimia',
            'Kimia Analitik',
            
            // Biology Courses
            'Biologi Umum',
            'Biologi Sel',
            'Genetika',
            'Ekologi',
            'Mikrobiologi',
            'Fisiologi',
            'Anatomi',
            
            // General Courses
            'Metodologi Penelitian',
            'Seminar',
            'Skripsi',
            'Bahasa Inggris',
            'Pancasila',
            'Kewarganegaraan'
        ];
    }

    private function getRandomSemester(): int
    {
        return rand(1, 8);
    }

    private function getCurrentAcademicYear(): string
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        return $currentYear . '/' . $nextYear;
    }
}
