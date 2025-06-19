<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResearchGroup>
 */
class ResearchGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groupNames = [
            'Manufacturing dan Process Engineering',
            'Enterprise and Industrial Management System',
            'Digital Enterprise System and Technology',
            'Artificial Intelligence and Machine Learning',
            'Cyber Security and Network Systems',
            'Software Engineering and Development'
        ];

        return [
            'name' => fake()->randomElement($groupNames),
            'description' => fake()->paragraph(2),
            'head_employee_id' => null, // Will be set after employees are created
        ];
    }
}
