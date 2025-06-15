<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studyPrograms = [
            'Matematika',
            'Fisika',
            'Kimia',
            'Biologi',
            'Statistika',
            'Ilmu Komputer',
            'Sistem Informasi',
            'Teknik Informatika',
            'Sains Data',
            'Bioinformatika',
            'Farmasi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Aktuaria',
        ];

        foreach ($studyPrograms as $programName) {
            StudyProgram::create([
                'name' => $programName,
            ]);
        }
    }
}
