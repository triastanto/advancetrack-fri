<?php

namespace Database\Factories;

use App\Models\StudyPromotor;
use App\Models\StudyDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPromotorFactory extends Factory
{
    protected $model = StudyPromotor::class;

    public function definition(): array
    {
        return [
            'study_detail_id' => StudyDetail::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'is_primary' => $this->faker->boolean(30), // 30% chance of being primary
        ];
    }

    /**
     * Create primary promotor
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Create secondary promotor
     */
    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
        ]);
    }
}
