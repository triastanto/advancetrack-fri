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
        $role = $this->faker->randomElement(['lecturer', 'fsdp_staff', 'head_of_affairs', 'vice_dean']);

        return [
            'user_id' => User::factory(),
            'employee_number' => $this->generateEmployeeNumber(),
            'position' => $this->generatePosition($role),
            'role' => $role,
        ];
    }

    /**
     * Generate a realistic employee number
     */
    private function generateEmployeeNumber(): string
    {
        $year = date('Y');
        $sequence = $this->faker->unique()->numberBetween(1000, 9999);
        return $year . $sequence;
    }

    /**
     * Generate position based on role
     */
    private function generatePosition(string $role): string
    {
        return match($role) {
            'vice_dean' => $this->faker->randomElement([
                'Wakil Dekan Bidang Akademik',
                'Wakil Dekan Bidang Kemahasiswaan',
                'Wakil Dekan Bidang Keuangan'
            ]),
            'head_of_affairs' => $this->faker->randomElement([
                'Kepala Urusan Akademik',
                'Kepala Urusan Kemahasiswaan',
                'Kepala Urusan Keuangan'
            ]),
            'fsdp_staff' => $this->faker->randomElement([
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
        return $this->role('fsdp_staff');
    }

    /**
     * Create head of affairs employee
     */
    public function headOfAffairs(): static
    {
        return $this->role('head_of_affairs');
    }

    /**
     * Create vice dean employee
     */
    public function viceDean(): static
    {
        return $this->role('vice_dean');
    }
}
