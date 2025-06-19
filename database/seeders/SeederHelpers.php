<?php

namespace Database\Seeders;

trait SeederHelpers
{
    /**
     * Generate random birth place
     */
    protected function randomBirthPlace(): string
    {
        $places = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
            'Makassar', 'Palembang', 'Tangerang', 'Bekasi', 'Depok',
            'Yogyakarta', 'Malang', 'Solo', 'Balikpapan', 'Pontianak'
        ];
        return $places[array_rand($places)];
    }

    /**
     * Generate random birth date
     */
    protected function randomBirthDate(int $startYear, int $endYear): string
    {
        $year = rand($startYear, $endYear);
        $month = rand(1, 12);
        $day = rand(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Determine gender from name
     */
    protected function determineGender(string $name): string
    {
        $femaleNames = [
            'Siti', 'Dewi', 'Sri', 'Rina', 'Maya', 'Ani', 'Indah', 'Ratna', 
            'Lestari', 'Anisa', 'Putri', 'Sinta', 'Winda', 'Fitri', 'Nanda', 
            'Mira', 'Lia', 'Vera', 'Rini', 'Sari'
        ];
        
        $maleNames = [
            'Budi', 'Agus', 'Andi', 'Ahmad', 'Sutrisno', 'Bambang', 'Heri', 
            'Fajar', 'Hendi', 'Bayu', 'Rendra', 'Indra', 'Rizki', 'Teguh', 
            'Arif', 'Yoga', 'Dimas', 'Eko', 'Farid'
        ];
        
        foreach ($femaleNames as $femaleName) {
            if (stripos($name, $femaleName) !== false) {
                return 'female';
            }
        }
        
        foreach ($maleNames as $maleName) {
            if (stripos($name, $maleName) !== false) {
                return 'male';
            }
        }
        
        // Default based on common patterns
        return 'male';
    }

    /**
     * Generate random address
     */
    protected function randomAddress(): string
    {
        $streets = [
            'Jl. Merdeka No. 123',
            'Jl. Sudirman No. 456',
            'Jl. Asia Afrika No. 789',
            'Jl. Malioboro No. 321',
            'Jl. Pemuda No. 654',
            'Jl. Diponegoro No. 987',
            'Jl. Ahmad Yani No. 147',
            'Jl. Veteran No. 258'
        ];
        
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Semarang', 'Yogyakarta', 'Malang'];
        
        return $streets[array_rand($streets)] . ', ' . $cities[array_rand($cities)];
    }

    /**
     * Generate random phone number
     */
    protected function randomPhone(): string
    {
        return '0812345678' . rand(10, 99);
    }

    /**
     * Generate unique NIDN (Nomor Induk Dosen Nasional)
     * Format: 10 digits starting with 0987654
     */
    protected function generateUniqueNIDN(): string
    {
        static $usedNIDNs = [];
        
        do {
            $nidn = '0987654' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        } while (in_array($nidn, $usedNIDNs));
        
        $usedNIDNs[] = $nidn;
        return $nidn;
    }

    /**
     * Determine gender from name with more comprehensive patterns
     */
    protected function determineGenderFromName(string $name): string
    {
        return $this->determineGender($name);
    }

    /**
     * Generate random birth place (alias for consistency)
     */
    protected function randomBirthPlace2(): string
    {
        return $this->randomBirthPlace();
    }

    /**
     * Generate random birth date with default range
     */
    protected function randomBirthDateDefault(): string
    {
        return $this->randomBirthDate(1970, 1990);
    }
}
