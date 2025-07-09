<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Education;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        // For each employee, add 1-2 education records
        Employee::all()->each(function ($employee) {
            Education::create([
                'employee_id' => $employee->id,
                'degree' => 'S1',
                'major' => 'Teknik Informatika',
                'institution' => 'Institut Teknologi Bandung',
                'graduation_year' => 2010,
                'gpa' => 3.75,
            ]);
            Education::create([
                'employee_id' => $employee->id,
                'degree' => 'S2',
                'major' => 'Ilmu Komputer',
                'institution' => 'Universitas Indonesia',
                'graduation_year' => 2014,
                'gpa' => 3.85,
            ]);
        });
    }
} 