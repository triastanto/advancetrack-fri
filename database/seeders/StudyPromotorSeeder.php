<?php

namespace Database\Seeders;

use App\Models\StudyPromotor;
use App\Models\StudyDetail;
use Illuminate\Database\Seeder;

class StudyPromotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studyDetails = StudyDetail::all();

        foreach ($studyDetails as $studyDetail) {
            // Create primary promotor
            StudyPromotor::create([
                'study_detail_id' => $studyDetail->id,
                'name' => $this->getRandomPromotorName(),
                'email' => $this->generatePromotorEmail(),
                'is_primary' => true,
            ]);

            // 70% chance to create secondary promotor
            if (rand(1, 100) <= 70) {
                StudyPromotor::create([
                    'study_detail_id' => $studyDetail->id,
                    'name' => $this->getRandomPromotorName(),
                    'email' => $this->generatePromotorEmail(),
                    'is_primary' => false,
                ]);
            }
        }
    }

    private function getRandomPromotorName(): string
    {
        $promotorNames = [
            'Prof. Dr. Ahmad Sutrisno, M.Eng',
            'Prof. Dr. Maya Arlini Puspasari, M.T',
            'Prof. Dr. Andi Cakravastia, M.T',
            'Dr. Ir. Budi Hartono, M.T',
            'Dr. Dewi Hardiningtyas, S.T., M.T',
            'Dr. Eng. Ir. Erwin Widodo, M.Eng',
            'Dr. Ir. Herry Christian, M.T',
            'Dr. Nurhadi Siswanto, S.T., M.T',
            'Dr. Ir. Putu Dana Karningsih, M.T',
            'Dr. Ir. Stefanus Eko Wiratno, M.T'
        ];

        return $promotorNames[array_rand($promotorNames)];
    }

    private function generatePromotorEmail(): string
    {
        $domains = [
            'its.ac.id',
            'ui.ac.id',
            'itb.ac.id',
            'ugm.ac.id',
            'unair.ac.id'
        ];

        $username = 'promotor' . rand(100, 999);
        $domain = $domains[array_rand($domains)];

        return $username . '@' . $domain;
    }
}
