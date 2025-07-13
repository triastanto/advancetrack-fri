<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Models\StudyCalendar;
use App\Models\StudyDetail;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LecturerWithActiveStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get study requirement document types
        $studyRequirementTypes = DocumentType::whereIn('name', DocumentTypeConstants::getStudyRequirementNames())->get();
        $approvalTypes = DocumentType::whereIn('name', DocumentTypeConstants::getApprovalDocumentNames())->get();

        if ($studyRequirementTypes->isEmpty() || $approvalTypes->isEmpty()) {
            $this->command->warn('Document types missing. Run DocumentTypeSeeder first.');
            return;
        }

        // Get the third lecturer (skip first two)
        $lecturer = Employee::where('role', 'lecturer')->skip(2)->first();
        if (!$lecturer) {
            $this->command->warn('No eligible lecturer found.');
            return;
        }

        $this->command->info("Creating complete document set and ACTIVE study calendar for lecturer: {$lecturer->user->name}");

        // Create verified study requirement documents
        foreach ($studyRequirementTypes as $documentType) {
            AcademicDocument::firstOrCreate([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
            ], [
                'file_name' => $documentType->display_name . '.pdf',
                'file_path' => "documents/{$lecturer->id}/study_requirements/" . $documentType->display_name . '.pdf',
                'workflow_state' => 3, // VERIFIED
                'upload_date' => Carbon::now()->subDays(rand(1, 30)),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        // Create approved approval documents
        foreach ($approvalTypes as $documentType) {
            ApprovalDocument::firstOrCreate([
                'employee_id' => $lecturer->id,
                'document_type_id' => $documentType->id,
            ], [
                'file_name' => $documentType->display_name . '.pdf',
                'file_path' => "documents/{$lecturer->id}/approvals/" . $documentType->display_name . '.pdf',
                'workflow_state' => 4, // APPROVED
                'upload_date' => Carbon::now()->subDays(rand(1, 30)),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        // Create study calendar in ACTIVE state
        $existingCalendar = StudyCalendar::where('employee_id', $lecturer->id)->where('workflow_state', 5)->first();
        if ($existingCalendar) {
            $this->command->info("Active study calendar already exists for {$lecturer->user->name}, skipping...");
            return;
        }

        $studyProgram = \App\Models\StudyProgram::inRandomOrder()->first();
        if (!$studyProgram) {
            $this->command->warn('No study program found. Skipping study calendar creation.');
            return;
        }

        // Set study start in the past, estimated end in the future
        $studyStart = Carbon::now()->subYears(2)->startOfMonth();
        $estimatedEnd = Carbon::now()->addYear()->endOfMonth();

        $studyCalendar = StudyCalendar::create([
            'employee_id' => $lecturer->id,
            'study_start' => $studyStart,
            'estimated_study_end' => $estimatedEnd,
            'workflow_state' => 5, // ACTIVE
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);

        // Create study detail
        StudyDetail::create([
            'study_calendar_id' => $studyCalendar->id,
            'university_name' => 'Universitas Indonesia',
            'university_address' => 'Jl. Salemba Raya No. 4, Jakarta Pusat',
            'university_email' => 'info@ui.ac.id',
            'university_phone' => '+62-21-314-0000',
            'study_program_id' => $studyProgram->id,
            'study_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'study_level' => 'S3',
            'total_semester' => 8,
            'scholarship' => 'LPDP',
            'funding_source' => 'LPDP',
            'study_regulation_notes' => 'Mengikuti regulasi studi lanjut sesuai SK Rektor',
        ]);

        $this->command->info("✅ Lecturer {$lecturer->user->name} now has complete documents and an ACTIVE study calendar!");
    }
} 