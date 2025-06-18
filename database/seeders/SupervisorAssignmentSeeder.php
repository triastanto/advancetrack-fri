<?php

namespace Database\Seeders;

use App\Models\SupervisorAssignment;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class SupervisorAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = Employee::where('role', 'lecturer')->get();
        $supervisors = Employee::whereIn('role', [
            'head_of_study_program',
            'head_of_research_group',
            'fri_vice_dean',
            'lecturer'
        ])->get();

        foreach ($lecturers as $lecturer) {
            // 60% chance to have a supervisor assignment
            if (rand(1, 100) <= 60) {
                $supervisor = $supervisors->where('id', '!=', $lecturer->id)->random();
                
                $startDate = $this->getRandomStartDate();
                $endDate = $this->getRandomEndDate($startDate);

                SupervisorAssignment::create([
                    'employee_id' => $lecturer->id,
                    'supervisor_id' => $supervisor->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
    }

    private function getRandomStartDate(): \DateTime
    {
        // Start date between 2 years ago and now
        $start = strtotime('-2 years');
        $end = time();
        $randomTimestamp = rand($start, $end);
        
        return new \DateTime(date('Y-m-d', $randomTimestamp));
    }

    private function getRandomEndDate(\DateTime $startDate): ?\DateTime
    {
        // 30% chance to have an end date (completed supervision)
        if (rand(1, 100) <= 30) {
            $startTimestamp = $startDate->getTimestamp();
            $maxEndTimestamp = min(time(), $startTimestamp + (365 * 24 * 60 * 60)); // Max 1 year or now
            $randomEndTimestamp = rand($startTimestamp + (30 * 24 * 60 * 60), $maxEndTimestamp); // Min 30 days after start
            
            return new \DateTime(date('Y-m-d', $randomEndTimestamp));
        }
        
        return null; // Active supervision
    }
}
