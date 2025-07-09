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

        foreach ($lecturersWithPrograms as $lecturer) {
            // Skip if already has study calendar
            if ($lecturer->studyCalendars()->exists()) {
                continue;
            }

            $studyCalendarData = $this->generateStudyCalendarData($lecturer);

            StudyCalendar::create(array_merge([
                'employee_id' => $lecturer->id,
            ], $studyCalendarData));
        }

        $this->command->info('Study calendars created for ' . $lecturersWithPrograms->count() . ' lecturers.');
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

        // Determine current workflow state based on timeline
        $workflowState = $this->determineWorkflowState($studyStart, $estimatedEnd);

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

    /**
     * Determine workflow state based on timeline
     * Workflow states: 1=DRAFT, 2=PENDING_APPROVAL, 3=APPROVED, 4=REJECTED, 5=ACTIVE, 6=LEAVE, 7=FINISHED, 8=DROP_OUT
     */
    private function determineWorkflowState(Carbon $startDate, Carbon $estimatedEnd): int
    {
        $now = Carbon::now();

        // If estimated end has passed, 70% chance finished, 30% still active (extended)
        if ($estimatedEnd->isPast()) {
            return rand(1, 10) <= 7 ? 7 : 5; // FINISHED or ACTIVE
        }

        // If more than 80% through the program, might be on leave or still active
        $totalDuration = $startDate->diffInDays($estimatedEnd);
        $elapsed = $startDate->diffInDays($now);
        $progress = $elapsed / $totalDuration;

        if ($progress > 0.8) {
            // Near completion: 80% active, 15% leave, 5% drop_out
            $rand = rand(1, 100);
            if ($rand <= 80) return 5; // ACTIVE
            if ($rand <= 95) return 6; // LEAVE
            return 8; // DROP_OUT
        }

        // Earlier in program: 85% active, 10% leave, 5% drop_out
        $rand = rand(1, 100);
        if ($rand <= 85) return 5; // ACTIVE
        if ($rand <= 95) return 6; // LEAVE
        return 8; // DROP_OUT
    }
}
