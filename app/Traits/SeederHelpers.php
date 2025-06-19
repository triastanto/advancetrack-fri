<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SeederHelpers
{
    // Gender determination
    public function determineGenderFromName(string $name): string
    {
        $name = strtolower($name);
        $femalePatterns = ['siti', 'dewi', 'sri', 'rina', 'maya', 'ani', 'indah', 'ratna', 'citra', 'diah', 'evi', 'wulan'];
        foreach ($femalePatterns as $pattern) {
            if (str_contains($name, $pattern)) {
                return 'female';
            }
        }
        return 'male';
    }

    // Functional position determination
    public function determineFunctionalPositionFromName(string $name): string
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

    public function determineFunctionalPositionFromRole(string $role): ?string
    {
        return match($role) {
            'lecturer' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'][rand(0, 3)],
            'fri_vice_dean', 'head_of_study_program', 'head_of_research_group' => 'Lektor Kepala',
            'head_of_hr_finance', 'hr_finance_staff' => 'Tenaga Kependidikan',
            default => null
        };
    }

    // Birth place and date
    public function randomBirthPlace(): string
    {
        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Semarang', 'Yogyakarta',
            'Malang', 'Solo', 'Denpasar', 'Medan', 'Palembang',
            'Makassar', 'Balikpapan', 'Banjarmasin', 'Pontianak',
            'Padang', 'Pekanbaru', 'Jambi', 'Lampung', 'Bogor',
            'Tangerang', 'Bekasi', 'Depok', 'Sidoarjo', 'Gresik'
        ];
        return $cities[array_rand($cities)];
    }

    public function randomBirthDate(int $minYear = 1960, int $maxYear = 1990): string
    {
        $year = rand($minYear, $maxYear);
        $month = rand(1, 12);
        $day = rand(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    // Position/title extraction
    public function extractStudyProgramPosition(string $name): string
    {
        $name = strtolower($name);
        if (str_contains($name, 'industri')) {
            return 'Ketua Program Studi S1 Teknik Industri';
        } elseif (str_contains($name, 'logistik')) {
            return 'Ketua Program Studi S1 Teknik Logistik';
        } elseif (str_contains($name, 'sistem') || str_contains($name, 'informasi')) {
            return 'Ketua Program Studi S1 Sistem Informasi';
        }
        return 'Ketua Program Studi';
    }

    public function extractResearchGroupPosition(): string
    {
        return 'Ketua Kelompok Keilmuan';
    }

    public function extractLecturerPosition(string $name): string
    {
        if (str_contains($name, 'Prof.')) {
            return 'Profesor';
        } elseif (str_contains($name, 'Dr.')) {
            return 'Dosen/Doktor';
        }
        return 'Dosen';
    }

    // Study calendar logic
    public function generateStudyCalendarData($lecturer): array
    {
        $lecturerName = strtolower($lecturer->user->name);
        $position = strtolower($lecturer->position);
        $studyLevel = $this->determineStudyLevel($lecturerName, $position);
        $studyDurationYears = $this->getStudyDuration($studyLevel);
        $studyStart = $this->generateStudyStartDate();
        $estimatedEnd = $studyStart->copy()->addYears($studyDurationYears);
        $status = $this->determineStudyStatus($studyStart, $estimatedEnd);
        $data = [
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'study_status' => $status,
        ];
        if ($status === 'finished') {
            $data['graduation_date'] = $estimatedEnd->copy()->subMonths(rand(0, 6));
        }
        return $data;
    }

    public function determineStudyLevel(string $name, string $position): string
    {
        if (str_contains($name, 'prof.') || str_contains($position, 'prof')) {
            return 'postdoc';
        }
        if (str_contains($name, 'dr.') || str_contains($position, 'doktor')) {
            return rand(0, 1) ? 'postdoc' : 'specialist';
        }
        return 'doctoral';
    }

    public function getStudyDuration(string $level): int
    {
        return match($level) {
            'doctoral' => rand(3, 5),
            'postdoc' => rand(1, 2),
            'specialist' => rand(1, 3),
            default => 4
        };
    }

    public function generateStudyStartDate(): \Carbon\Carbon
    {
        $yearsAgo = rand(1, 4);
        $preferredMonths = [1, 9];
        $month = $preferredMonths[array_rand($preferredMonths)];
        return \Carbon\Carbon::create(
            year: date('Y') - $yearsAgo,
            month: $month,
            day: 1
        );
    }

    public function determineStudyStatus($studyStart, $estimatedEnd): string
    {
        $now = \Carbon\Carbon::now();
        if ($estimatedEnd->isPast()) {
            return rand(1, 10) <= 7 ? 'finished' : 'active';
        }
        $totalDuration = $studyStart->diffInDays($estimatedEnd);
        $elapsed = $studyStart->diffInDays($now);
        $progress = $elapsed / $totalDuration;
        if ($progress > 0.8) {
            $rand = rand(1, 100);
            if ($rand <= 80) return 'active';
            if ($rand <= 95) return 'leave';
            return 'drop_out';
        }
        $rand = rand(1, 100);
        if ($rand <= 85) return 'active';
        if ($rand <= 95) return 'leave';
        return 'drop_out';
    }
} 