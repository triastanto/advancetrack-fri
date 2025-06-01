<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $role = $this->faker->randomElement(['lecturer', 'fsdp_staff', 'head_of_affairs', 'vice_dean']);
        return [
            'user_id' => User::factory(),
            'employee_number' => $this->faker->unique()->numerify('1970######'),
            'position' => $this->faker->jobTitle(),
            'role' => $role,
        ];
    }
}
