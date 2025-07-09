<?php

namespace Database\Factories;

use App\Models\StudyCalendar;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyCalendarFactory extends Factory
{
    protected $model = StudyCalendar::class;

    public function definition(): array
    {
        // Workflow states: 1=DRAFT, 2=PENDING_APPROVAL, 3=APPROVED, 4=REJECTED, 5=ACTIVE, 6=LEAVE, 7=FINISHED, 8=DROP_OUT
        $workflowStates = [1, 2, 3, 4, 5, 6, 7, 8];
        $start = $this->faker->dateTimeBetween('-5 years', 'now');
        $estimated_end = (clone $start)->modify('+4 years');
        $graduation = $this->faker->boolean(70) ? (clone $estimated_end)->modify('+'.rand(0, 6).' months') : null;
        return [
            'employee_id' => Employee::factory(),
            'study_start' => $start,
            'estimated_study_end' => $estimated_end,
            'graduation_date' => $graduation,
            'workflow_state' => $this->faker->randomElement($workflowStates),
        ];
    }
}
