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
            'S3 Teknik Industri',
            'S3 Teknik Elektro',
            'S3 Ilmu Komputer',
            'S3 Manajemen',
            'S3 Ilmu Ekonomi',
            'S3 Pendidikan',
            'S3 Ilmu Hukum',
            'S3 Ilmu Lingkungan',
            'S3 Ilmu Administrasi Publik',
            'S3 Teknologi Informasi'
        ];

        foreach ($studyPrograms as $programName) {
            StudyProgram::create([
                'name' => $programName,
            ]);
        }
    }
}
