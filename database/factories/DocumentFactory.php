<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $types = ['personal_data', 'requirement', 'semester_report', 'final_report', 'graduation', 'additional', 'minutes', 'nde', 'pid'];
        $statuses = ['draft', 'pending', 'verified', 'rejected'];
        return [
            'employee_id' => Employee::factory(),
            'document_type' => $this->faker->randomElement($types),
            'file_name' => $this->faker->lexify('document_????.pdf'),
            'file_path' => 'uploads/' . $this->faker->uuid . '.pdf',
            'verification_status' => $this->faker->randomElement($statuses),
            'verification_note' => $this->faker->optional()->sentence(),
            'uploaded_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
