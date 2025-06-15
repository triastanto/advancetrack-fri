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

        if (str_contains($email, 'wakildekan') || str_contains($name, 'wakil dekan')) {
            return 'vice_dean';
        }

        if (str_contains($email, 'kaur') || str_contains($name, 'kepala urusan')) {
            return 'head_of_affairs';
        }

        if (str_contains($email, 'staf') || str_contains($name, 'staf')) {
            return 'fsdp_staff';
        }

        if (str_contains($email, 'admin') || str_contains($name, 'administrator')) {
            return 'fsdp_staff';
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
            case 'vice_dean':
                return 'Wakil Dekan Bidang Akademik';
            case 'head_of_affairs':
                return 'Kepala Urusan Akademik';
            case 'fsdp_staff':
                if (str_contains(strtolower($name), 'administrator')) {
                    return 'Administrator Sistem';
                }
                return 'Staf Administrasi FSDP';
            case 'lecturer':
            default:
                // Extract academic title if present
                if (str_contains($name, 'Prof.')) {
                    return 'Profesor';
                } elseif (str_contains($name, 'Dr.')) {
                    return 'Dosen/Doktor';
                } else {
                    return 'Dosen';
                }
        }
    }
}
