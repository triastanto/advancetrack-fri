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
        $statuses = ['active', 'finished', 'leave', 'drop_out'];
        $start = $this->faker->dateTimeBetween('-5 years', 'now');
        $estimated_end = (clone $start)->modify('+4 years');
        $graduation = $this->faker->boolean(70) ? (clone $estimated_end)->modify('+'.rand(0, 6).' months') : null;
        return [
            'employee_id' => Employee::factory(),
            'study_start' => $start,
            'estimated_study_end' => $estimated_end,
            'graduation_date' => $graduation,
            'study_status' => $this->faker->randomElement($statuses),
        ];
    }
}
