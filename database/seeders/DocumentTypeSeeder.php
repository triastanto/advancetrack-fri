<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DocumentTypeConstants::DOCUMENT_TYPES as $documentType) {
            DocumentType::updateOrCreate(
                ['name' => $documentType['name']],
                $documentType
            );
        }
    }
}
