<?php

namespace Database\Factories;

use App\Models\SupervisorAssignment;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupervisorAssignmentFactory extends Factory
{
    protected $model = SupervisorAssignment::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        
        return [
            'employee_id' => Employee::factory(),
            'supervisor_id' => Employee::factory(),
            'start_date' => $startDate,
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween($startDate, '+1 year'),
        ];
    }

    /**
     * Create active supervision (no end date)
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
        ]);
    }

    /**
     * Create completed supervision (with end date)
     */
    public function completed(): static
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', '-6 months');
        
        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, 'now'),
        ]);
    }
}
