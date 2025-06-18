<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $role = $this->faker->randomElement(['lecturer', 'hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean', 'head_of_study_program', 'head_of_research_group']);

        return [
            'user_id' => User::factory(),
            'nidn' => $this->generateNIDN(),
            'position' => $this->generatePosition($role),
            'role' => $role,
            'birth_place' => $this->faker->optional(0.9)->city(),
            'birth_date' => $this->faker->optional(0.9)->dateTimeBetween('-60 years', '-25 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'functional_position' => $this->faker->optional(0.7)->randomElement([
                'Asisten Ahli',
                'Lektor',
                'Lektor Kepala',
                'Profesor',
                'Tenaga Pendidik',
                'Tenaga Kependidikan'
            ]),
            'origin_address' => $this->faker->optional(0.8)->address(),
            'contact_phone' => $this->faker->optional(0.9)->phoneNumber(),
            'contact_email' => $this->faker->optional(0.8)->email,
        ];
    }

    /**
     * Generate a realistic NIDN (Nomor Induk Dosen Nasional)
     */
    private function generateNIDN(): string
    {
        // NIDN format: 0 + 10 digits
        return '0' . $this->faker->unique()->numerify('##########');
    }

    /**
     * Generate position based on role
     */
    private function generatePosition(string $role): string
    {
        return match($role) {
            'fri_vice_dean' => $this->faker->randomElement([
                'Wakil Dekan Bidang Akademik',
                'Wakil Dekan Bidang Kemahasiswaan',
                'Wakil Dekan Bidang Keuangan'
            ]),
            'head_of_hr_finance' => $this->faker->randomElement([
                'Kepala Urusan Akademik',
                'Kepala Urusan Kemahasiswaan',
                'Kepala Urusan Keuangan'
            ]),
            'hr_finance_staff' => $this->faker->randomElement([
                'Staf Administrasi FSDP',
                'Staf Akademik',
                'Administrator Sistem',
                'Staf Keuangan'
            ]),
            'lecturer' => $this->faker->randomElement([
                'Dosen Matematika',
                'Dosen Fisika',
                'Dosen Kimia',
                'Dosen Biologi',
                'Profesor Matematika',
                'Profesor Fisika',
                'Lektor Matematika',
                'Lektor Fisika'
            ]),
            default => 'Staff'
        };
    }

    /**
     * Create employee for specific role
     */
    public function role(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
            'position' => $this->generatePosition($role),
        ]);
    }

    /**
     * Create lecturer employee
     */
    public function lecturer(): static
    {
        return $this->role('lecturer');
    }

    /**
     * Create staff employee
     */
    public function staff(): static
    {
        return $this->role('hr_finance_staff');
    }

    /**
     * Create head of hr finances employee
     */
    public function headOfHrFinances(): static
    {
        return $this->role('head_of_hr_finance');
    }

    /**
     * Create vice dean employee
     */
    public function viceDean(): static
    {
        return $this->role('fri_vice_dean');
    }
}
