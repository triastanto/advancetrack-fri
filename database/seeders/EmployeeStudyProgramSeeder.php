<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\StudyProgram;
use App\Models\StudyCalendar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeStudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all lecturers (employees with lecturer role)
        $lecturers = Employee::where('role', 'lecturer')->get();
        
        // Get all study programs
        $studyPrograms = StudyProgram::all();

        if ($lecturers->isEmpty() || $studyPrograms->isEmpty()) {
            $this->command->info('No lecturers or study programs found. Skipping employee-study program associations.');
            return;
        }

        foreach ($lecturers as $lecturer) {
            // Use transaction to ensure data consistency
            DB::transaction(function () use ($lecturer, $studyPrograms) {
                // Assign each lecturer to 1-3 study programs based on their specialization
                $assignedPrograms = $this->getStudyProgramsForLecturer($lecturer, $studyPrograms);
                
                // Attach the study programs to the lecturer
                $lecturer->studyPrograms()->attach($assignedPrograms->pluck('id'));
                
                // Create study calendar for this lecturer
                $this->createStudyCalendarForLecturer($lecturer);
            });
        }
    }

    /**
     * Get study programs for a lecturer based on their specialization
     */
    private function getStudyProgramsForLecturer(Employee $lecturer, $studyPrograms)
    {
        $lecturerName = strtolower($lecturer->user->name);
        $lecturerPosition = strtolower($lecturer->position);
        
        // Determine specialization based on name or position
        $assignedPrograms = collect();
        
        if (str_contains($lecturerName, 'matematika') || str_contains($lecturerPosition, 'matematika')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Matematika', 'Statistika', 'Aktuaria']);
        } elseif (str_contains($lecturerName, 'fisika') || str_contains($lecturerPosition, 'fisika')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Fisika', 'Teknik Elektro']);
        } elseif (str_contains($lecturerName, 'kimia') || str_contains($lecturerPosition, 'kimia')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Kimia', 'Farmasi']);
        } elseif (str_contains($lecturerName, 'biologi') || str_contains($lecturerPosition, 'biologi')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Biologi', 'Bioinformatika']);
        } elseif (str_contains($lecturerName, 'komputer') || str_contains($lecturerPosition, 'komputer') || 
                  str_contains($lecturerName, 'informatika') || str_contains($lecturerPosition, 'informatika')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Ilmu Komputer', 'Teknik Informatika', 'Sistem Informasi', 'Sains Data']);
        } elseif (str_contains($lecturerName, 'teknik') || str_contains($lecturerPosition, 'teknik')) {
            $assignedPrograms = $studyPrograms->whereIn('name', ['Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil']);
        } else {
            // For generic lecturers or those with unclear specialization
            // Assign them to a random selection of 1-2 programs
            $assignedPrograms = $studyPrograms->random(rand(1, 2));
        }

        // If no specific match found or collection is empty, assign random programs
        if ($assignedPrograms->isEmpty()) {
            $assignedPrograms = $studyPrograms->random(rand(1, 2));
        }

        // Limit to maximum 3 programs per lecturer
        return $assignedPrograms->take(3);
    }

    /**
     * Create study calendar for a lecturer
     */
    private function createStudyCalendarForLecturer(Employee $lecturer): void
    {
        // Skip if lecturer already has a study calendar
        if ($lecturer->studyCalendars()->exists()) {
            return;
        }

        $studyCalendarData = $this->generateStudyCalendarData($lecturer);
        
        StudyCalendar::create(array_merge([
            'employee_id' => $lecturer->id,
        ], $studyCalendarData));
    }

    /**
     * Generate study calendar data based on lecturer profile
     */
    private function generateStudyCalendarData(Employee $lecturer): array
    {
        $lecturerName = strtolower($lecturer->user->name);
        $position = strtolower($lecturer->position);
        
        // Determine study level and duration based on current title
        $studyLevel = $this->determineStudyLevel($lecturerName, $position);
        $studyDurationYears = $this->getStudyDuration($studyLevel);
        
        // Generate realistic dates
        $studyStart = $this->generateStudyStartDate();
        $estimatedEnd = $studyStart->copy()->addYears($studyDurationYears);
        
        // Determine current status based on timeline
        $status = $this->determineStudyStatus($studyStart, $estimatedEnd);
        
        $data = [
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'study_status' => $status,
        ];

        // Add graduation date if finished
        if ($status === 'finished') {
            $data['graduation_date'] = $estimatedEnd->copy()->subMonths(rand(0, 6));
        }

        return $data;
    }

    /**
     * Determine study level based on current academic title
     */
    private function determineStudyLevel(string $name, string $position): string
    {
        // Those with Prof. are likely pursuing post-doctoral or sabbatical
        if (str_contains($name, 'prof.') || str_contains($position, 'prof')) {
            return 'postdoc'; // Post-doctoral research
        }
        
        // Those with Dr. might be pursuing additional specialization or higher degree
        if (str_contains($name, 'dr.') || str_contains($position, 'doktor')) {
            return rand(0, 1) ? 'postdoc' : 'specialist'; // Post-doc or specialization
        }
        
        // Others are likely pursuing doctoral studies
        return 'doctoral';
    }

    /**
     * Get study duration in years based on level
     */
    private function getStudyDuration(string $level): int
    {
        return match($level) {
            'doctoral' => rand(3, 5), // 3-5 years for doctoral
            'postdoc' => rand(1, 2),  // 1-2 years for post-doc
            'specialist' => rand(1, 3), // 1-3 years for specialization
            default => 4
        };
    }

    /**
     * Generate realistic study start date
     */
    private function generateStudyStartDate(): Carbon
    {
        // Generate dates between 1-4 years ago for realistic timeline
        $yearsAgo = rand(1, 4);
        $monthsAgo = rand(0, 11);
        
        // Prefer academic calendar starts (September, January)
        $preferredMonths = [1, 9]; // January, September
        $month = $preferredMonths[array_rand($preferredMonths)];
        
        return Carbon::create(
            year: date('Y') - $yearsAgo,
            month: $month,
            day: 1
        );
    }

    /**
     * Determine study status based on timeline
     */
    private function determineStudyStatus(Carbon $startDate, Carbon $estimatedEnd): string
    {
        $now = Carbon::now();
        
        // If estimated end has passed, 70% chance finished, 30% still active (extended)
        if ($estimatedEnd->isPast()) {
            return rand(1, 10) <= 7 ? 'finished' : 'active';
        }
        
        // If more than 80% through the program, might be on leave or still active
        $totalDuration = $startDate->diffInDays($estimatedEnd);
        $elapsed = $startDate->diffInDays($now);
        $progress = $elapsed / $totalDuration;
        
        if ($progress > 0.8) {
            // Near completion: 80% active, 15% leave, 5% drop_out
            $rand = rand(1, 100);
            if ($rand <= 80) return 'active';
            if ($rand <= 95) return 'leave';
            return 'drop_out';
        }
        
        // Earlier in program: 85% active, 10% leave, 5% drop_out
        $rand = rand(1, 100);
        if ($rand <= 85) return 'active';
        if ($rand <= 95) return 'leave';
        return 'drop_out';
    }
}
