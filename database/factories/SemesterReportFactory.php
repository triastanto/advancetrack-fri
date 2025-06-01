<?php

namespace Database\Factories;

use App\Models\SemesterReport;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterReportFactory extends Factory
{
    protected $model = SemesterReport::class;

    public function definition(): array
    {
        $statuses = ['draft', 'pending', 'verified', 'rejected'];
        return [
            'employee_id' => Employee::factory(),
            'semester' => $this->faker->numberBetween(1, 8),
            'year' => $this->faker->year(),
            'file_name' => $this->faker->lexify('semester_report_????.pdf'),
            'file_path' => 'uploads/' . $this->faker->uuid . '.pdf',
            'verification_status' => $this->faker->randomElement($statuses),
            'verification_note' => $this->faker->optional()->sentence(),
            'uploaded_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
