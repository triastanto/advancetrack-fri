<?php

namespace Database\Factories;

use App\Models\ServiceBondAgreement;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBondAgreementFactory extends Factory
{
    protected $model = ServiceBondAgreement::class;

    public function definition(): array
    {
        $statuses = ['draft', 'pending', 'verified', 'rejected'];
        return [
            'employee_id' => Employee::factory(),
            'file_name' => $this->faker->lexify('agreement_????.pdf'),
            'file_path' => 'uploads/' . $this->faker->uuid . '.pdf',
            'upload_date' => $this->faker->dateTimeThisYear(),
            'verification_status' => $this->faker->randomElement($statuses),
            'verification_note' => $this->faker->optional()->sentence(),
        ];
    }
}
