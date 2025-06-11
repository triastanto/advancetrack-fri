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
        $types = ['personal_data', 'requirement', 'semester_report', 'final_report', 'graduation', 'additional', 'minutes', 'nde', 'pid', 'service_bond_agreement'];
        $statuses = ['draft', 'pending', 'verified', 'rejected'];
        $type = $this->faker->randomElement($types);
        $data = [
            'employee_id' => Employee::factory(),
            'document_type' => $type,
            'file_name' => $this->faker->lexify('document_????.pdf'),
            'file_path' => 'uploads/' . $this->faker->uuid . '.pdf',
            'verification_status' => $this->faker->randomElement($statuses),
            'verification_note' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];

        // Add specific fields based on document type
        if ($type === 'semester_report') {
            $data['semester'] = $this->faker->numberBetween(1, 8);
            $data['year'] = $this->faker->year();
        } elseif ($type === 'service_bond_agreement') {
            $data['upload_date'] = $this->faker->date();
        }

        return $data;
    }
}
