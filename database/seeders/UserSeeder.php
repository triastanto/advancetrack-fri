<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat pengguna spesifik untuk setiap peran berdasarkan spesifikasi

        // 1. Wakil Dekan 2 - Pengambil keputusan strategis
        $wakilDekanUser = User::create([
            'name' => 'Prof. Dr. Wakil Dekan Bidang Akademik',
            'email' => 'wakildekan@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create corresponding employee record
        Employee::create([
            'user_id' => $wakilDekanUser->id,
            'employee_number' => '202500001',
            'position' => 'Wakil Dekan Bidang Akademik',
            'role' => 'vice_dean',
        ]);

        // 2. Kepala Urusan - Mengelola PID (Perjanjian Ikatan Dinas)
        $kepalaUrusanUser = User::create([
            'name' => 'Drs. Kepala Urusan Akademik',
            'email' => 'kaur@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $kepalaUrusanUser->id,
            'employee_number' => '202500002',
            'position' => 'Kepala Urusan Akademik',
            'role' => 'head_of_affairs',
        ]);

        // 3. Staf FSDP - Verifikasi dokumen dan administrasi
        $stafFSDPUser = User::create([
            'name' => 'Staf Administrasi FSDP',
            'email' => 'staf@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $stafFSDPUser->id,
            'employee_number' => '202500003',
            'position' => 'Staf Administrasi FSDP',
            'role' => 'fsdp_staff',
        ]);

        // 4. Dosen sampel - Upload dan kelola dokumen studi lanjut
        $dosen1User = User::create([
            'name' => 'Dr. Ahmad Dosen Matematika',
            'email' => 'dosen1@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $dosen1User->id,
            'employee_number' => '202500004',
            'position' => 'Dosen Matematika',
            'role' => 'lecturer',
        ]);

        $dosen2User = User::create([
            'name' => 'Prof. Dr. Siti Dosen Fisika',
            'email' => 'dosen2@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $dosen2User->id,
            'employee_number' => '202500005',
            'position' => 'Profesor Fisika',
            'role' => 'lecturer',
        ]);

        // Additional specialized lecturers
        $dosen3User = User::create([
            'name' => 'Dr. Budi Dosen Kimia',
            'email' => 'dosen3@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $dosen3User->id,
            'employee_number' => '202500007',
            'position' => 'Dosen Kimia',
            'role' => 'lecturer',
        ]);

        $dosen4User = User::create([
            'name' => 'Dr. Ani Dosen Informatika',
            'email' => 'dosen4@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $dosen4User->id,
            'employee_number' => '202500008',
            'position' => 'Dosen Teknik Informatika',
            'role' => 'lecturer',
        ]);

        // 5. Administrator sistem
        $adminUser = User::create([
            'name' => 'Administrator Sistem',
            'email' => 'admin@fsdp.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $adminUser->id,
            'employee_number' => '202500006',
            'position' => 'Administrator Sistem',
            'role' => 'fsdp_staff',
        ]);

        // Buat pengguna tambahan untuk testing dengan employee records
        $additionalUsers = User::factory(5)->create();
        
        foreach ($additionalUsers as $index => $user) {
            Employee::create([
                'user_id' => $user->id,
                'employee_number' => '20250' . str_pad(9 + $index, 4, '0', STR_PAD_LEFT),
                'position' => 'Dosen',
                'role' => 'lecturer',
            ]);
        }
    }
}
