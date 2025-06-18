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
                'nidn' => $this->generateNIDN($user),
                'position' => $this->determinePosition($user, $role),
                'role' => $role,
                'birth_place' => $this->generateBirthPlace($user),
                'birth_date' => $this->generateBirthDate($user),
                'gender' => $this->determineGender($user),
                'functional_position' => $this->determineFunctionalPosition($role),
                'origin_address' => $this->generateOriginAddress(),
                'contact_phone' => $this->generateContactPhone(),
                'contact_email' => $user->email,
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
     * Generate NIDN (Nomor Induk Dosen Nasional)
     */
    private function generateNIDN(User $user): string
    {
        // NIDN format: 0 + 10 digits
        // Use user ID as part of the sequence to avoid duplicates
        $sequence = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $random = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        return '0' . $sequence . $random;
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

    /**
     * Generate birth place
     */
    private function generateBirthPlace(User $user): ?string
    {
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Semarang', 'Yogyakarta', 'Malang', 'Solo', 'Denpasar'];
        return $cities[array_rand($cities)];
    }

    /**
     * Generate birth date
     */
    private function generateBirthDate(User $user): \DateTime
    {
        // Generate birth date between 30-60 years ago
        $minAge = 30;
        $maxAge = 60;
        $birthYear = date('Y') - rand($minAge, $maxAge);
        $birthMonth = rand(1, 12);
        $birthDay = rand(1, 28);
        
        return new \DateTime("$birthYear-$birthMonth-$birthDay");
    }

    /**
     * Determine gender based on name patterns
     */
    private function determineGender(User $user): string
    {
        $name = strtolower($user->name);
        
        // Simple heuristic based on common Indonesian names
        $femalePatterns = ['siti', 'dewi', 'sri', 'rina', 'maya', 'ani', 'indah', 'ratna'];
        $malePatterns = ['budi', 'agus', 'andi', 'ahmad', 'sutrisno', 'bambang', 'heri'];
        
        foreach ($femalePatterns as $pattern) {
            if (str_contains($name, $pattern)) {
                return 'female';
            }
        }
        
        foreach ($malePatterns as $pattern) {
            if (str_contains($name, $pattern)) {
                return 'male';
            }
        }
        
        // Default random if can't determine
        return ['male', 'female'][rand(0, 1)];
    }

    /**
     * Determine functional position based on role
     */
    private function determineFunctionalPosition(string $role): ?string
    {
        return match($role) {
            'lecturer' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'][rand(0, 3)],
            'fri_vice_dean', 'head_of_study_program', 'head_of_research_group' => 'Lektor Kepala',
            'head_of_hr_finance' => 'Tenaga Kependidikan',
            'hr_finance_staff' => 'Tenaga Kependidikan',
            default => null
        };
    }

    /**
     * Generate origin address
     */
    private function generateOriginAddress(): ?string
    {
        $addresses = [
            'Jl. Merdeka No. 123, Jakarta Pusat',
            'Jl. Sudirman No. 456, Surabaya',
            'Jl. Asia Afrika No. 789, Bandung',
            'Jl. Malioboro No. 321, Yogyakarta',
            'Jl. Pemuda No. 654, Semarang'
        ];
        
        return $addresses[array_rand($addresses)];
    }

    /**
     * Generate contact phone
     */
    private function generateContactPhone(): ?string
    {
        return '08' . rand(10, 99) . rand(1000000, 9999999);
    }
}
