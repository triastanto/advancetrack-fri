<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\StudyCalendar;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudyCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all lecturers who have study program assignments
        $lecturersWithPrograms = Employee::where('role', 'lecturer')
            ->whereHas('studyPrograms')
            ->get();

        if ($lecturersWithPrograms->isEmpty()) {
            $this->command->info('No lecturers with study program assignments found. Run EmployeeStudyProgramSeeder first.');
            return;
        }

        // Only seed one lecturer with a draft study calendar
        $lecturer = $lecturersWithPrograms->first();
        if (!$lecturer) {
            $this->command->warn('No eligible lecturer found.');
            return;
        }
        // Remove all existing study calendars for this lecturer
        $lecturer->studyCalendars()->delete();

        $studyCalendarData = $this->generateStudyCalendarDataForState($lecturer, 1); // 1 = DRAFT

        StudyCalendar::create(array_merge([
            'employee_id' => $lecturer->id,
        ], $studyCalendarData));

        $this->command->info("Seeded 1 study calendar in draft state for lecturer: {$lecturer->user->name}");
    }

    /**
     * Generate study calendar data for a specific workflow_state
     */
    function generateStudyCalendarDataForState(Employee $lecturer, int $workflowState): array
    {
        $lecturerName = strtolower($lecturer->user->name);
        $position = strtolower($lecturer->position);

        // Determine study level and duration based on current title
        $studyLevel = $this->determineStudyLevel($lecturerName, $position);
        $studyDurationYears = $this->getStudyDuration($studyLevel);

        // Generate realistic dates
        $studyStart = $this->generateStudyStartDate();
        $estimatedEnd = $studyStart->copy()->addYears($studyDurationYears);

        $data = [
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'workflow_state' => $workflowState,
        ];

        // Add graduation date if finished
        if ($workflowState === 7) { // FINISHED state
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
}
