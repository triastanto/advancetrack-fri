<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudyRequirementDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get study requirement document types
        $studyRequirementTypes = DocumentType::whereIn('name', DocumentTypeConstants::getStudyRequirementNames())->get();

        if ($studyRequirementTypes->isEmpty()) {
            $this->command->warn('No study requirement document types found. Run DocumentTypeSeeder first.');
            return;
        }

        // Get the same lecturer seeded by StudyCalendarSeeder (first lecturer with study program assignments)
        $lecturer = Employee::where('role', 'lecturer')
            ->whereHas('studyPrograms')
            ->first();

        if (!$lecturer) {
            $this->command->warn('No eligible lecturer found.');
            return;
        }

        $this->command->info("Creating verified study requirement documents for lecturer: {$lecturer->user->name}");

        $createdCount = 0;

        foreach ($studyRequirementTypes as $documentType) {
            // Check if document already exists for this lecturer and type
            $existingDocument = AcademicDocument::where('employee_id', $lecturer->id)
                ->where('document_type_id', $documentType->id)
                ->first();

            if ($existingDocument) {
                $this->command->info("Document {$documentType->display_name} already exists for {$lecturer->user->name}, skipping...");
                continue;
            }

            // Create verified document
            $document = AcademicDocument::create([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->generateFileName($documentType->name),
                'file_path' => $this->generateFilePath($lecturer->id, $documentType->name),
                'workflow_state' => 3, // VERIFIED state
                'upload_date' => Carbon::now()->subDays(rand(1, 30)), // Random upload date within last 30 days
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $createdCount++;
            $this->command->info("Created verified document: {$documentType->display_name}");
        }

        $this->command->info("✅ Successfully created {$createdCount} verified study requirement documents for {$lecturer->user->name}");
    }

    /**
     * Generate a realistic file name for the document
     */
    private function generateFileName(string $documentTypeName): string
    {
        $fileNameMap = [
            'letter_of_acceptance' => 'Surat_Penerimaan_Resmi.pdf',
            'scholarship_cover_letter' => 'Surat_Pengantar_Beasiswa.pdf',
            'rector_permit_letter' => 'Surat_Izin_Rektor.pdf',
            'permanent_lecturer_decree' => 'SK_Dosen_Tetap_Yayasan.pdf',
            'diploma_certificate' => 'Ijazah_Terakhir.pdf',
            's1_transcript' => 'Transkrip_Nilai_S1.pdf',
            's2_transcript' => 'Transkrip_Nilai_S2.pdf',
            'inpassing_decree' => 'SK_Inpassing.pdf',
            'jad_decree' => 'SK_JAD.pdf',
            'graduation_report_statement' => 'Surat_Pernyataan_Laporan_Kelulusan.pdf',
            'lldikti_assignment_statement' => 'Pernyataan_Penugasan_LLDIKTI.pdf',
            'willing_to_be_relieved_letter' => 'Surat_Bersedia_Dibebastugaskan.pdf',
            'management_permit_letter' => 'Surat_Izin_Pimpinan.pdf',
            'work_period_certificate' => 'Surat_Keterangan_Masa_Kerja.pdf',
            'academic_recommendation_letter' => 'Surat_Rekomendasi_Akademisi.pdf',
            'management_recommendation_letter' => 'Surat_Rekomendasi_Pimpinan.pdf',
        ];

        return $fileNameMap[$documentTypeName] ?? 'Dokumen_Persyaratan_Studi.pdf';
    }

    /**
     * Generate a realistic file path for the document
     */
    private function generateFilePath(int $employeeId, string $documentTypeName): string
    {
        $timestamp = Carbon::now()->format('Y/m/d');
        return "documents/{$employeeId}/study_requirements/{$timestamp}/{$this->generateFileName($documentTypeName)}";
    }
}