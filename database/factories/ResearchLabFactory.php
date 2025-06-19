<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResearchLab>
 */
class ResearchLabFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $labTypes = [
            'Quality System Engineering' => 'QSE',
            'Product Development & Ergonomics' => 'PDE',
            'Manufacturing System' => 'MS',
            'Business Modelling & Simulation' => 'BMS',
            'Project Management & Digital Talent' => 'PMDT',
            'Enterprise System and Solution' => 'ESS',
            'E-Logistic and Supply Chain' => 'ELSC',
            'Digital Marketing and Intelligence' => 'DMI',
            'Enterprise Resource Planning' => 'ERP',
            'Enterprise Data Management' => 'EDM',
            'Enterprise Infrastructure Management' => 'EIM',
            'Enterprise Intelligent System Development' => 'EISD',
            'System Architecture and Governance' => 'SAG',
            'Information System Development (TUKJ)' => 'ISD-TUKJ'
        ];

        $labName = fake()->randomElement(array_keys($labTypes));
        $aliasName = $labTypes[$labName];

        return [
            'name' => 'Laboratorium Riset ' . $labName,
            'alias_name' => 'Lab ' . $aliasName,
            'description' => fake()->paragraph(2),
            'research_group_id' => \App\Models\ResearchGroup::factory(),
        ];
    }
}
