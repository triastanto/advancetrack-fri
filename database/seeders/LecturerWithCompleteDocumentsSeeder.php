<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LecturerWithCompleteDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get study requirement document types
        $studyRequirementTypes = DocumentType::whereIn('name', DocumentTypeConstants::getStudyRequirementNames())->get();

        // Get approval document types
        $approvalTypes = DocumentType::whereIn('name', DocumentTypeConstants::getApprovalDocumentNames())->get();

        if ($studyRequirementTypes->isEmpty()) {
            $this->command->warn('No study requirement document types found. Run DocumentTypeSeeder first.');
            return;
        }

        if ($approvalTypes->isEmpty()) {
            $this->command->warn('No approval document types found. Run DocumentTypeSeeder first.');
            return;
        }

        // Get the second lecturer (skip the first one)
        $lecturer = Employee::where('role', 'lecturer')->skip(1)->first();

        if (!$lecturer) {
            $this->command->warn('No eligible lecturer found.');
            return;
        }

        $this->command->info("Creating complete document set for lecturer: {$lecturer->user->name}");

        $studyRequirementCount = 0;
        $approvalCount = 0;

        // Create verified study requirement documents
        foreach ($studyRequirementTypes as $documentType) {
            // Check if document already exists for this lecturer and type
            $existingDocument = AcademicDocument::where('employee_id', $lecturer->id)
                ->where('document_type_id', $documentType->id)
                ->first();

            if ($existingDocument) {
                $this->command->info("Study requirement document {$documentType->display_name} already exists for {$lecturer->user->name}, skipping...");
                continue;
            }

            // Create verified study requirement document
            $document = AcademicDocument::create([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->generateStudyRequirementFileName($documentType->name),
                'file_path' => $this->generateStudyRequirementFilePath($lecturer->id, $documentType->name),
                'workflow_state' => 3, // VERIFIED state
                'upload_date' => Carbon::now()->subDays(rand(1, 30)), // Random upload date within last 30 days
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $studyRequirementCount++;
            $this->command->info("Created verified study requirement document: {$documentType->display_name}");
        }

        // Create approved approval documents
        foreach ($approvalTypes as $documentType) {
            // Check if document already exists for this lecturer and type
            $existingDocument = ApprovalDocument::where('employee_id', $lecturer->id)
                ->where('document_type_id', $documentType->id)
                ->first();

            if ($existingDocument) {
                $this->command->info("Approval document {$documentType->display_name} already exists for {$lecturer->user->name}, skipping...");
                continue;
            }

            // Create approved approval document
            $document = ApprovalDocument::create([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->generateApprovalFileName($documentType->name),
                'file_path' => $this->generateApprovalFilePath($lecturer->id, $documentType->name),
                'workflow_state' => 4, // APPROVED state (based on verification-by-management state machine)
                'upload_date' => Carbon::now()->subDays(rand(1, 30)), // Random upload date within last 30 days
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $approvalCount++;
            $this->command->info("Created approved approval document: {$documentType->display_name}");
        }

        $this->command->info("✅ Successfully created {$studyRequirementCount} verified study requirement documents and {$approvalCount} approved approval documents for {$lecturer->user->name}");

        // Create study calendar in draft state
        $this->createStudyCalendar($lecturer);

        $this->command->info("🎓 Lecturer {$lecturer->user->name} now has complete document set and can start a study calendar!");
    }

    /**
     * Generate a realistic file name for study requirement documents
     */
    private function generateStudyRequirementFileName(string $documentTypeName): string
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
     * Generate a realistic file name for approval documents
     */
    private function generateApprovalFileName(string $documentTypeName): string
    {
        $fileNameMap = [
            'study_compatibility' => 'Dokumen_Kesesuaian_Studi_Lanjut.pdf',
            'application_minutes' => 'Berita_Acara_Pengajuan_Studi_Lanjut.pdf',
            'approval_minutes' => 'Berita_Acara_Persetujuan_Studi_Lanjut.pdf',
            'nde' => 'NDE_Studi_Lanjut.pdf',
            'pid' => 'Perjanjian_Ikatan_Dinas_PID.pdf',
        ];

        return $fileNameMap[$documentTypeName] ?? 'Dokumen_Persetujuan_Studi.pdf';
    }

    /**
     * Generate a realistic file path for study requirement documents
     */
    private function generateStudyRequirementFilePath(int $employeeId, string $documentTypeName): string
    {
        $timestamp = Carbon::now()->format('Y/m/d');
        return "documents/{$employeeId}/study_requirements/{$timestamp}/{$this->generateStudyRequirementFileName($documentTypeName)}";
    }

    /**
     * Generate a realistic file path for approval documents
     */
    private function generateApprovalFilePath(int $employeeId, string $documentTypeName): string
    {
        $timestamp = Carbon::now()->format('Y/m/d');
        return "documents/{$employeeId}/approvals/{$timestamp}/{$this->generateApprovalFileName($documentTypeName)}";
    }

    /**
     * Create study calendar in draft state for the lecturer
     */
    private function createStudyCalendar(Employee $lecturer): void
    {
        // Check if study calendar already exists
        $existingCalendar = \App\Models\StudyCalendar::where('employee_id', $lecturer->id)->first();
        
        if ($existingCalendar) {
            $this->command->info("Study calendar already exists for {$lecturer->user->name}, skipping...");
            return;
        }

        // Get a random study program
        $studyProgram = \App\Models\StudyProgram::inRandomOrder()->first();
        
        if (!$studyProgram) {
            $this->command->warn('No study program found. Skipping study calendar creation.');
            return;
        }

        // Generate realistic study dates
        $studyStart = Carbon::now()->addMonths(rand(1, 6));
        $estimatedEnd = $studyStart->copy()->addYears(rand(3, 4));

        // Create study calendar in draft state
        $studyCalendar = \App\Models\StudyCalendar::create([
            'employee_id' => $lecturer->id,
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'workflow_state' => 1, // DRAFT state
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);

        // Create study detail
        \App\Models\StudyDetail::create([
            'study_calendar_id' => $studyCalendar->id,
            'university_name' => $this->getRandomUniversity(),
            'university_address' => $this->getUniversityAddress(),
            'university_email' => $this->getUniversityEmail(),
            'university_phone' => $this->getUniversityPhone(),
            'study_program_id' => $studyProgram->id,
            'study_address' => $this->getRandomStudyAddress(),
            'study_level' => $this->getRandomStudyLevel(),
            'total_semester' => rand(6, 8),
            'scholarship' => $this->getRandomScholarship(),
            'funding_source' => $this->getRandomFundingSource(),
            'study_regulation_notes' => $this->getRandomRegulationNotes(),
        ]);

        $this->command->info("📅 Created study calendar in draft state for {$lecturer->user->name}");
    }

    /**
     * Get random university name
     */
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

    /**
     * Get university address
     */
    private function getUniversityAddress(): string
    {
        $addresses = [
            'Jl. Salemba Raya No. 4, Jakarta Pusat',
            'Jl. Ganesha No. 10, Bandung',
            'Jl. Bulaksumur, Yogyakarta',
            'Jl. Raya ITS, Surabaya',
            'Jl. Airlangga No. 4-6, Surabaya',
            'Jl. Dipati Ukur No. 35, Bandung',
            'Jl. Veteran, Malang',
            'Jl. Prof. Soedarto, Semarang',
            'Jl. Meranti, Bogor',
            'Jl. Perintis Kemerdekaan KM. 10, Makassar'
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * Get university email
     */
    private function getUniversityEmail(): string
    {
        $emails = [
            'info@ui.ac.id',
            'info@itb.ac.id',
            'info@ugm.ac.id',
            'info@its.ac.id',
            'info@unair.ac.id',
            'info@unpad.ac.id',
            'info@ub.ac.id',
            'info@undip.ac.id',
            'info@ipb.ac.id',
            'info@unhas.ac.id'
        ];

        return $emails[array_rand($emails)];
    }

    /**
     * Get university phone
     */
    private function getUniversityPhone(): string
    {
        $phones = [
            '+62-21-314-0000',
            '+62-22-250-0000',
            '+62-27-451-0000',
            '+62-31-599-0000',
            '+62-31-503-0000',
            '+62-22-842-0000',
            '+62-34-551-0000',
            '+62-24-746-0000',
            '+62-25-186-0000',
            '+62-41-585-0000'
        ];

        return $phones[array_rand($phones)];
    }

    /**
     * Get random study address
     */
    private function getRandomStudyAddress(): string
    {
        $addresses = [
            'Jl. Sudirman No. 123, Jakarta Pusat',
            'Jl. Asia Afrika No. 45, Bandung',
            'Jl. Malioboro No. 67, Yogyakarta',
            'Jl. Tunjungan No. 89, Surabaya',
            'Jl. Ahmad Yani No. 12, Malang',
            'Jl. Pandanaran No. 34, Semarang',
            'Jl. Pajajaran No. 56, Bogor',
            'Jl. Pengayoman No. 78, Makassar'
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * Get random study level
     */
    private function getRandomStudyLevel(): string
    {
        $levels = ['S3', 'Postdoc', 'Specialist'];
        return $levels[array_rand($levels)];
    }

    /**
     * Get random scholarship
     */
    private function getRandomScholarship(): ?string
    {
        $scholarships = [
            'LPDP',
            'Beasiswa Unggulan',
            'Beasiswa BUDI',
            'Beasiswa PPA',
            null
        ];

        return $scholarships[array_rand($scholarships)];
    }

    /**
     * Get random funding source
     */
    private function getRandomFundingSource(): string
    {
        $sources = ['LPDP', 'Pribadi', 'Instansi', 'Perusahaan', 'Yayasan'];
        return $sources[array_rand($sources)];
    }

    /**
     * Get random regulation notes
     */
    private function getRandomRegulationNotes(): ?string
    {
        $notes = [
            'Mengikuti regulasi studi lanjut sesuai SK Rektor',
            'Sesuai dengan ketentuan akademik universitas',
            'Mengikuti panduan studi lanjut yang berlaku',
            null
        ];

        return $notes[array_rand($notes)];
    }
} 