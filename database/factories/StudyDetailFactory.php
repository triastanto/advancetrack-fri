<?php

namespace Database\Factories;

use App\Models\StudyDetail;
use App\Models\StudyCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyDetailFactory extends Factory
{
    protected $model = StudyDetail::class;

    public function definition(): array
    {
        return [
            'study_calendar_id' => StudyCalendar::factory(),
            'university_name' => $this->faker->randomElement([
                'Universitas Indonesia',
                'Institut Teknologi Bandung',
                'Universitas Gadjah Mada',
                'Institut Teknologi Sepuluh Nopember',
                'Universitas Airlangga',
                'Universitas Padjadjaran',
                'Universitas Brawijaya',
                'Universitas Diponegoro'
            ]),
            'university_address' => $this->faker->address(),
            'university_email' => $this->faker->email(),
            'university_phone' => $this->faker->phoneNumber(),
            'study_program_id' => \App\Models\StudyProgram::inRandomOrder()->first()?->id ?? 1,
            'study_address' => $this->faker->address(),
            'study_level' => $this->faker->randomElement(['S2', 'S3']),
            'total_semester' => $this->faker->numberBetween(4, 12),
            'scholarship' => $this->faker->optional(0.6)->randomElement([
                'LPDP',
                'Beasiswa Unggulan',
                'Beasiswa DIKTI',
                'Beasiswa Pertamina',
                'Beasiswa Djarum',
                'Mandiri'
            ]),
            'funding_source' => $this->faker->optional(0.8)->randomElement([
                'LPDP',
                'Pribadi',
                'Instansi',
                'Perusahaan',
                'Yayasan'
            ]),
            'study_regulation_notes' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Create study detail for doctorate level
     */
    public function doctorate(): static
    {
        return $this->state(fn (array $attributes) => [
            'study_level' => 'S3',
            'total_semester' => $this->faker->numberBetween(8, 12),
            'study_program_id' => \App\Models\StudyProgram::whereIn('name', [
                'Doktor Matematika',
                'Doktor Fisika',
                'Doktor Kimia',
                'Doktor Biologi'
            ])->inRandomOrder()->first()?->id ?? 1,
        ]);
    }

    /**
     * Create study detail for master level
     */
    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'study_level' => 'S2',
            'total_semester' => $this->faker->numberBetween(4, 8),
            'study_program_id' => \App\Models\StudyProgram::whereIn('name', [
                'Magister Matematika',
                'Magister Fisika',
                'Magister Kimia',
                'Magister Biologi'
            ])->inRandomOrder()->first()?->id ?? 1,
        ]);
    }
}
