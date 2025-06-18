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
        // 1. Wakil Dekan II FRI (one employee)
        $wakilDekanUser = User::create([
            'name' => 'Prof. Dr. Ir. Bambang Setiawan, M.T.',
            'email' => 'wakildekan2@fri.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $wakilDekanUser->id,
            'nidn' => '0202500001',
            'position' => 'Wakil Dekan II FRI',
            'role' => 'fri_vice_dean',
            'birth_place' => 'Jakarta',
            'birth_date' => '1970-05-15',
            'gender' => 'male',
            'functional_position' => 'Lektor Kepala',
            'origin_address' => 'Jl. Merdeka No. 123, Jakarta',
            'contact_phone' => '081234567890',
            'contact_email' => 'wakildekan2@fri.ac.id',
        ]);

        // 2. Kepala Urusan SDM & Keuangan (one employee)
        $kepalaUrusanUser = User::create([
            'name' => 'Dra. Siti Nurhasanah, M.M.',
            'email' => 'kaur.sdm@fri.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $kepalaUrusanUser->id,
            'nidn' => '0202500002',
            'position' => 'Kepala Urusan SDM & Keuangan',
            'role' => 'head_of_hr_finance',
            'birth_place' => 'Surabaya',
            'birth_date' => '1975-03-20',
            'gender' => 'female',
            'functional_position' => 'Tenaga Kependidikan',
            'origin_address' => 'Jl. Sudirman No. 456, Surabaya',
            'contact_phone' => '081234567891',
            'contact_email' => 'kaur.sdm@fri.ac.id',
        ]);

        // 3. Staf SDM & Keuangan (multiple employees)
        $staffData = [
            ['name' => 'Ahmad Rifai, S.E.', 'email' => 'ahmad.rifai@fri.ac.id', 'emp_no' => '202500003'],
            ['name' => 'Dewi Kartika, S.Sos.', 'email' => 'dewi.kartika@fri.ac.id', 'emp_no' => '202500004'],
            ['name' => 'Budi Santoso, A.Md.', 'email' => 'budi.santoso@fri.ac.id', 'emp_no' => '202500005'],
        ];

        foreach ($staffData as $staff) {
            $user = User::create([
                'name' => $staff['name'],
                'email' => $staff['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            Employee::create([
                'user_id' => $user->id,
                'nidn' => '0' . $staff['emp_no'],
                'position' => 'Staf SDM & Keuangan',
                'role' => 'hr_finance_staff',
                'birth_place' => 'Surabaya',
                'birth_date' => '1980-01-01',
                'gender' => str_contains($staff['name'], 'Dewi') ? 'female' : 'male',
                'functional_position' => 'Tenaga Kependidikan',
                'origin_address' => 'Jl. Veteran No. 100, Surabaya',
                'contact_phone' => '0812345678' . substr($staff['emp_no'], -2),
                'contact_email' => $staff['email'],
            ]);
        }

        // 4. Ketua Program Studi (three employees)
        $kaprodiData = [
            ['name' => 'Dr. Ir. Agus Widodo, M.T.', 'email' => 'kaprodi.ti@fri.ac.id', 'program' => 'S1 Teknik Industri', 'emp_no' => '202500006'],
            ['name' => 'Dr. Rina Fitriana, S.T., M.T.', 'email' => 'kaprodi.tl@fri.ac.id', 'program' => 'S1 Teknik Logistik', 'emp_no' => '202500007'],
            ['name' => 'Dr. Hendra Kurniawan, S.Kom., M.T.', 'email' => 'kaprodi.si@fri.ac.id', 'program' => 'S1 Sistem Informasi', 'emp_no' => '202500008'],
        ];

        foreach ($kaprodiData as $kaprodi) {
            $user = User::create([
                'name' => $kaprodi['name'],
                'email' => $kaprodi['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            Employee::create([
                'user_id' => $user->id,
                'nidn' => '0' . $kaprodi['emp_no'],
                'position' => 'Ketua Program Studi ' . $kaprodi['program'],
                'role' => 'head_of_study_program',
                'birth_place' => 'Surabaya',
                'birth_date' => '1970-06-15',
                'gender' => str_contains($kaprodi['name'], 'Rina') ? 'female' : 'male',
                'functional_position' => 'Lektor Kepala',
                'origin_address' => 'Jl. Raya ITS No. 25, Surabaya',
                'contact_phone' => '0812345678' . substr($kaprodi['emp_no'], -2),
                'contact_email' => $kaprodi['email'],
            ]);
        }

        // 5. Ketua Kelompok Keilmuan (three employees) - corrected role name
        $kkelData = [
            ['name' => 'Prof. Dr. Ir. Sutrisno, M.Eng.', 'email' => 'kkel.mpe@fri.ac.id', 'group' => 'Manufacturing and Process Engineering', 'emp_no' => '202500009'],
            ['name' => 'Dr. Ir. Maya Sari, M.M.', 'email' => 'kkel.eims@fri.ac.id', 'group' => 'Enterprise and Industrial Management System', 'emp_no' => '202500010'],
            ['name' => 'Dr. Andi Prasetyo, S.T., M.T.', 'email' => 'kkel.dest@fri.ac.id', 'group' => 'Digital Enterprise System and Technology', 'emp_no' => '202500011'],
        ];

        foreach ($kkelData as $kkel) {
            $user = User::create([
                'name' => $kkel['name'],
                'email' => $kkel['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            Employee::create([
                'user_id' => $user->id,
                'nidn' => '0' . $kkel['emp_no'],
                'position' => 'Ketua Kelompok Keilmuan ' . $kkel['group'],
                'role' => 'head_of_research_group',
                'birth_place' => 'Surabaya',
                'birth_date' => '1965-08-10',
                'gender' => str_contains($kkel['name'], 'Maya') ? 'female' : 'male',
                'functional_position' => str_contains($kkel['name'], 'Prof.') ? 'Profesor' : 'Lektor Kepala',
                'origin_address' => 'Jl. Keputran No. 15, Surabaya',
                'contact_phone' => '0812345678' . substr($kkel['emp_no'], -2),
                'contact_email' => $kkel['email'],
            ]);
        }

        // 6. Dosen (multiple employees)
        $dosenData = [
            ['name' => 'Dr. Ir. Bambang Supriadi, M.T.', 'email' => 'bambang.supriadi@fri.ac.id', 'emp_no' => '202500012', 'dept' => 'Teknik Industri'],
            ['name' => 'Dr. Siti Rahayu, S.T., M.T.', 'email' => 'siti.rahayu@fri.ac.id', 'emp_no' => '202500013', 'dept' => 'Teknik Logistik'],
            ['name' => 'Ir. Dedi Setiawan, M.T.', 'email' => 'dedi.setiawan@fri.ac.id', 'emp_no' => '202500014', 'dept' => 'Teknik Industri'],
            ['name' => 'Dr. Lina Kusumawati, S.Kom., M.T.', 'email' => 'lina.kusumawati@fri.ac.id', 'emp_no' => '202500015', 'dept' => 'Sistem Informasi'],
            ['name' => 'Dr. Rahmat Budiman, S.T., M.T.', 'email' => 'rahmat.budiman@fri.ac.id', 'emp_no' => '202500016', 'dept' => 'Teknik Logistik'],
            ['name' => 'Dra. Ani Wijayanti, M.Si.', 'email' => 'ani.wijayanti@fri.ac.id', 'emp_no' => '202500017', 'dept' => 'Matematika'],
            ['name' => 'Dr. Benny Kurniawan, S.T., M.T.', 'email' => 'benny.kurniawan@fri.ac.id', 'emp_no' => '202500018', 'dept' => 'Teknik Industri'],
            ['name' => 'Dr. Citra Maharani, S.Kom., M.T.', 'email' => 'citra.maharani@fri.ac.id', 'emp_no' => '202500019', 'dept' => 'Sistem Informasi'],
            ['name' => 'Prof. Dr. Ahmad Fauzi, M.Eng.', 'email' => 'ahmad.fauzi@fri.ac.id', 'emp_no' => '202500020', 'dept' => 'Teknik Industri'],
            ['name' => 'Dr. Rina Sari, S.T., M.T.', 'email' => 'rina.sari@fri.ac.id', 'emp_no' => '202500021', 'dept' => 'Teknik Logistik'],
            ['name' => 'Dr. Indra Gunawan, S.Kom., M.T.', 'email' => 'indra.gunawan@fri.ac.id', 'emp_no' => '202500022', 'dept' => 'Sistem Informasi'],
            ['name' => 'Ir. Wulan Sari, M.T.', 'email' => 'wulan.sari@fri.ac.id', 'emp_no' => '202500023', 'dept' => 'Teknik Industri'],
            ['name' => 'Dr. Rudi Hermawan, S.T., M.T.', 'email' => 'rudi.hermawan@fri.ac.id', 'emp_no' => '202500024', 'dept' => 'Teknik Logistik'],
            ['name' => 'Dr. Maya Indrawati, S.Kom., M.T.', 'email' => 'maya.indrawati@fri.ac.id', 'emp_no' => '202500025', 'dept' => 'Sistem Informasi'],
            ['name' => 'Dr. Hadi Santoso, S.T., M.T.', 'email' => 'hadi.santoso@fri.ac.id', 'emp_no' => '202500026', 'dept' => 'Teknik Industri'],
            ['name' => 'Dra. Evi Susanti, M.Si.', 'email' => 'evi.susanti@fri.ac.id', 'emp_no' => '202500027', 'dept' => 'Matematika'],
            ['name' => 'Dr. Yudi Prasetyo, S.T., M.T.', 'email' => 'yudi.prasetyo@fri.ac.id', 'emp_no' => '202500028', 'dept' => 'Teknik Logistik'],
            ['name' => 'Dr. Diah Anggraini, S.Kom., M.T.', 'email' => 'diah.anggraini@fri.ac.id', 'emp_no' => '202500029', 'dept' => 'Sistem Informasi'],
        ];

        foreach ($dosenData as $dosen) {
            $user = User::create([
                'name' => $dosen['name'],
                'email' => $dosen['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            Employee::create([
                'user_id' => $user->id,
                'nidn' => '0' . $dosen['emp_no'],
                'position' => 'Dosen ' . $dosen['dept'],
                'role' => 'lecturer',
                'birth_place' => 'Surabaya',
                'birth_date' => '1975-01-01',
                'gender' => $this->determineGender($dosen['name']),
                'functional_position' => $this->determineFunctionalPosition($dosen['name']),
                'origin_address' => 'Jl. ITS Raya No. 50, Surabaya',
                'contact_phone' => '0812345678' . substr($dosen['emp_no'], -2),
                'contact_email' => $dosen['email'],
            ]);
        }

        // Remove the factory-generated users since we now have specific lecturers
        // $additionalUsers = User::factory(10)->create();
    }

    /**
     * Determine gender based on name patterns
     */
    private function determineGender(string $name): string
    {
        $name = strtolower($name);
        
        // Check for female name patterns
        $femalePatterns = ['siti', 'dewi', 'sri', 'rina', 'maya', 'ani', 'indah', 'ratna', 'citra', 'diah', 'evi', 'wulan'];
        foreach ($femalePatterns as $pattern) {
            if (str_contains($name, $pattern)) {
                return 'female';
            }
        }
        
        return 'male'; // Default for Indonesian academic names
    }

    /**
     * Determine functional position based on academic title
     */
    private function determineFunctionalPosition(string $name): string
    {
        if (str_contains($name, 'Prof.')) {
            return 'Profesor';
        } elseif (str_contains($name, 'Dr.')) {
            return 'Lektor Kepala';
        } elseif (str_contains($name, 'Ir.') || str_contains($name, 'Dra.')) {
            return 'Lektor';
        } else {
            return 'Asisten Ahli';
        }
    }
}
