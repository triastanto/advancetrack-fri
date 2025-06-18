<?php

namespace Database\Factories;

use App\Models\CourseResponsibility;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseResponsibilityFactory extends Factory
{
    protected $model = CourseResponsibility::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'course_name' => $this->faker->randomElement([
                'Kalkulus I',
                'Kalkulus II',
                'Aljabar Linear',
                'Analisis Real',
                'Analisis Kompleks',
                'Fisika Dasar I',
                'Fisika Dasar II',
                'Mekanika Kuantum',
                'Kimia Dasar',
                'Kimia Organik',
                'Biologi Umum',
                'Biokimia',
                'Statistika',
                'Geometri Analitik',
                'Persamaan Diferensial'
            ]),
            'semester' => $this->faker->optional(0.8)->numberBetween(1, 8),
            'academic_year' => $this->faker->optional(0.9)->randomElement([
                '2023/2024',
                '2024/2025',
                '2025/2026'
            ]),
        ];
    }

    /**
     * Create course responsibility for current academic year
     */
    public function currentYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'academic_year' => '2024/2025',
        ]);
    }

    /**
     * Create course responsibility for specific semester
     */
    public function semester(int $semester): static
    {
        return $this->state(fn (array $attributes) => [
            'semester' => $semester,
        ]);
    }
}
