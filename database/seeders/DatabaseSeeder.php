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
        // Seed document types first (required for other seeders)
        $this->call(DocumentTypeSeeder::class);

        // Call the main app seeder
        $this->call(AdvancedTrackSeeder::class);
    }
}
