<?php

namespace Database\Seeders;

use App\Models\StudyDetail;
use App\Models\StudyCalendar;
use Illuminate\Database\Seeder;

class StudyDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studyCalendars = StudyCalendar::all();

        foreach ($studyCalendars as $calendar) {
            StudyDetail::create([
                'study_calendar_id' => $calendar->id,
                'university_name' => $this->getRandomUniversity(),
                'university_address' => $this->getUniversityAddress($calendar->id),
                'university_email' => $this->getUniversityEmail($calendar->id),
                'university_phone' => $this->getUniversityPhone(),
                'study_program_name' => $this->getRandomStudyProgram(),
                'study_address' => $this->getRandomStudyAddress(),
                'study_level' => $this->getRandomStudyLevel(),
                'scholarship' => $this->getRandomScholarship(),
                'funding_source' => $this->getRandomFundingSource(),
                'study_regulation_notes' => $this->getRandomRegulationNotes(),
            ]);
        }
    }

    private function getRandomUniversity(): string
    {
        $universities = [
            'Universitas Indonesia',
            'Institut Teknologi Bandung',
            'Universitas Gadjah Mada',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Airlangga',
            'Universitas Padjadjaran',
            'Universitas Brawijaya',
            'Universitas Diponegoro',
            'Institut Pertanian Bogor',
            'Universitas Hasanuddin'
        ];

        return $universities[array_rand($universities)];
    }

    private function getUniversityAddress(int $calendarId): string
    {
        $addresses = [
            'Jl. Margonda Raya, Depok, Jawa Barat 16424',
            'Jl. Ganesha 10, Bandung, Jawa Barat 40132',
            'Bulaksumur, Caturtunggal, Kec. Depok, Sleman, DIY 55281',
            'Jl. Arief Rahman Hakim, Keputih, Surabaya, Jawa Timur 60111',
            'Jl. Mulyorejo, Surabaya, Jawa Timur 60115',
            'Jl. Raya Bandung-Sumedang KM.21, Jatinangor, Sumedang, Jawa Barat 45363'
        ];

        return $addresses[array_rand($addresses)];
    }

    private function getUniversityEmail(int $calendarId): string
    {
        $emails = [
            'info@ui.ac.id',
            'info@itb.ac.id',
            'info@ugm.ac.id',
            'info@its.ac.id',
            'info@unair.ac.id',
            'info@unpad.ac.id'
        ];

        return $emails[array_rand($emails)];
    }

    private function getUniversityPhone(): string
    {
        return '021-' . rand(1000000, 9999999);
    }

    private function getRandomStudyProgram(): string
    {
        $programs = [
            'Doktor Matematika',
            'Doktor Fisika',
            'Doktor Kimia',
            'Doktor Biologi',
            'Doktor Teknik Industri',
            'Magister Matematika',
            'Magister Fisika',
            'Magister Kimia',
            'Magister Biologi',
            'Magister Teknik Industri'
        ];

        return $programs[array_rand($programs)];
    }

    private function getRandomStudyAddress(): string
    {
        $addresses = [
            'Jl. Keputran Gang II No. 15, Surabaya',
            'Jl. Gegerkalong Hilir No. 42, Bandung',
            'Jl. Kaliurang KM 5.5, Yogyakarta',
            'Jl. Veteran No. 123, Malang',
            'Jl. Dr. Wahidin No. 78, Solo'
        ];

        return $addresses[array_rand($addresses)];
    }

    private function getRandomStudyLevel(): string
    {
        return ['S2', 'S3'][array_rand(['S2', 'S3'])];
    }

    private function getRandomScholarship(): ?string
    {
        $scholarships = [
            'LPDP',
            'Beasiswa Unggulan',
            'Beasiswa DIKTI',
            'Beasiswa Pertamina',
            'Beasiswa Djarum',
            'Mandiri',
            null
        ];

        return $scholarships[array_rand($scholarships)];
    }

    private function getRandomFundingSource(): ?string
    {
        $sources = [
            'LPDP',
            'Pribadi',
            'Instansi',
            'Perusahaan',
            'Yayasan',
            null
        ];

        return $sources[array_rand($sources)];
    }

    private function getRandomRegulationNotes(): ?string
    {
        $notes = [
            'Mengikuti peraturan universitas terkait masa studi maksimal 4 tahun untuk program doktor.',
            'Wajib mengikuti seminar proposal dan seminar hasil penelitian.',
            'Publikasi jurnal internasional minimal 2 artikel untuk kelulusan program doktor.',
            null
        ];

        return $notes[array_rand($notes)];
    }
}
