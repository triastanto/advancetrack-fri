<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and create corresponding employee records
        $users = User::all();

        foreach ($users as $user) {
            // Skip if employee already exists
            if ($user->employee) {
                continue;
            }

            // Determine role based on email or name
            $role = $this->determineRole($user);

            Employee::create([
                'user_id' => $user->id,
                'employee_number' => $this->generateEmployeeNumber($user),
                'position' => $this->determinePosition($user, $role),
                'role' => $role,
            ]);
        }
    }

    /**
     * Determine employee role based on user information
     */
    private function determineRole(User $user): string
    {
        $email = strtolower($user->email);
        $name = strtolower($user->name);

        // Vice Dean
        if (str_contains($email, 'wakildekan') || str_contains($name, 'wakil dekan')) {
            return 'fri_vice_dean';
        }

        // Head of HR Finance
        if (str_contains($email, 'kaur') || str_contains($name, 'kepala urusan')) {
            return 'head_of_hr_finance';
        }

        // Study Program Head
        if (str_contains($email, 'kaprodi') || str_contains($name, 'ketua prodi')) {
            return 'head_of_study_program';
        }

        // Research Group Head (Kelompok Keilmuan)
        if (str_contains($email, 'kkel') || str_contains($name, 'ketua kk') || str_contains($name, 'ketua kelompok')) {
            return 'head_of_research_group';
        }

        // HR Finance Staff
        if (str_contains($email, 'staf') || str_contains($name, 'staf')) {
            return 'hr_finance_staff';
        }

        if (str_contains($email, 'admin') || str_contains($name, 'administrator')) {
            return 'hr_finance_staff';
        }

        // Default to lecturer for dosen or other users
        return 'lecturer';
    }

    /**
     * Generate employee number
     */
    private function generateEmployeeNumber(User $user): string
    {
        // Use a combination of year and incremental number
        $year = date('Y');
        $sequence = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        return $year . $sequence;
    }

    /**
     * Determine position based on user and role
     */
    private function determinePosition(User $user, string $role): string
    {
        $name = $user->name;

        switch ($role) {
            case 'fri_vice_dean':
                return 'Wakil Dekan II FRI';
            case 'head_of_hr_finance':
                return 'Kepala Urusan SDM & Keuangan';
            case 'head_of_study_program':
                return $this->extractStudyProgramPosition($name);
            case 'head_of_research_group':
                return $this->extractResearchGroupPosition($name);
            case 'hr_finance_staff':
                if (str_contains(strtolower($name), 'administrator')) {
                    return 'Administrator Sistem';
                }
                return 'Staf SDM & Keuangan';
            case 'lecturer':
            default:
                return $this->extractLecturerPosition($name);
        }
    }

    private function extractStudyProgramPosition(string $name): string
    {
        if (str_contains(strtolower($name), 'industri')) {
            return 'Ketua Program Studi S1 Teknik Industri';
        } elseif (str_contains(strtolower($name), 'logistik')) {
            return 'Ketua Program Studi S1 Teknik Logistik';
        } elseif (str_contains(strtolower($name), 'sistem') || str_contains(strtolower($name), 'informasi')) {
            return 'Ketua Program Studi S1 Sistem Informasi';
        }
        return 'Ketua Program Studi';
    }

    private function extractResearchGroupPosition(string $name): string
    {
        if (str_contains(strtolower($name), 'sutrisno') || str_contains(strtolower($name), 'mpe')) {
            return 'Ketua Kelompok Keilmuan Manufacturing and Process Engineering';
        } elseif (str_contains(strtolower($name), 'maya') || str_contains(strtolower($name), 'eims')) {
            return 'Ketua Kelompok Keilmuan Enterprise and Industrial Management System';
        } elseif (str_contains(strtolower($name), 'andi') || str_contains(strtolower($name), 'dest')) {
            return 'Ketua Kelompok Keilmuan Digital Enterprise System and Technology';
        }
        return 'Ketua Kelompok Keilmuan';
    }

    private function extractLecturerPosition(string $name): string
    {
        if (str_contains($name, 'Prof.')) {
            return 'Profesor';
        } elseif (str_contains($name, 'Dr.')) {
            return 'Dosen/Doktor';
        }
        return 'Dosen';
    }
}
