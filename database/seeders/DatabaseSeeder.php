<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $environment = app()->environment();

        // Always seed master data first
        $this->seedMasterData();

        // Add development data only in non-production environments
        if ($environment !== 'production') {
            $this->seedDevelopmentData();
        }
    }

    /**
     * Seed master data required in all environments
     */
    private function seedMasterData(): void
    {
        $this->command->info('📋 Seeding Master Data...');

        // Core master data - required for system operation
        $this->call(DocumentTypeSeeder::class);
        $this->call(StudyProgramSeeder::class);
        $this->call(ResearchStructureSeeder::class); // Creates research groups and labs (production-safe)

        $this->command->info('✅ Master data seeded successfully');
    }

    /**
     * Seed development/test data (non-production only)
     */
    private function seedDevelopmentData(): void
    {
        $this->command->info('👥 Seeding Development Data...');

        // Create users and employees for research structure (development only)
        $this->call(PersonnelSeeder::class);

        // Create complete study calendar data for one lecturer (includes all related data)
        $this->call(StudyCalendarSeeder::class);

        // Create verified study requirement documents for one lecturer
        $this->call(StudyRequirementDocumentSeeder::class);

        // Add education records for all employees
        $this->call(EducationSeeder::class);

        // Additional development data can be added here as needed

        $this->command->info('✅ Development data seeded successfully');
    }
}
