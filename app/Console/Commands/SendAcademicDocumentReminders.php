<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\StudyCalendar;
use App\Constants\DocumentTypeConstants;
use App\Notifications\WorkflowNotification;
use App\Services\NotificationCountService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAcademicDocumentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:academic-documents 
                            {--type=semester : Type of reminder (semester|final)}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to lecturers for academic document submissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info("Sending {$type} document reminders...");

        if ($type === 'semester') {
            $this->sendSemesterReportReminders($dryRun);
        } elseif ($type === 'final') {
            $this->sendFinalReportReminders($dryRun);
        } else {
            $this->error("Invalid type. Use 'semester' or 'final'");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Send semester report reminders
     */
    private function sendSemesterReportReminders(bool $dryRun): void
    {
        $activeLecturers = $this->getActiveLecturers();
        $currentSemester = $this->getCurrentSemester();
        
        $this->info("Found " . $activeLecturers->count() . " active lecturers");
        $this->info("Current semester: {$currentSemester}");

        foreach ($activeLecturers as $lecturer) {
            $missingDocuments = $this->getMissingSemesterDocuments($lecturer, $currentSemester);
            
            if ($missingDocuments->isNotEmpty()) {
                $this->sendSemesterReminder($lecturer, $missingDocuments, $currentSemester, $dryRun);
            }
        }
    }

    /**
     * Send final report reminders
     */
    private function sendFinalReportReminders(bool $dryRun): void
    {
        $lecturersNearCompletion = $this->getLecturersNearCompletion();
        
        $this->info("Found " . $lecturersNearCompletion->count() . " lecturers near completion");

        foreach ($lecturersNearCompletion as $lecturer) {
            $missingDocuments = $this->getMissingFinalDocuments($lecturer);
            
            if ($missingDocuments->isNotEmpty()) {
                $this->sendFinalReminder($lecturer, $missingDocuments, $dryRun);
            }
        }
    }

    /**
     * Get active lecturers with study calendars
     */
    private function getActiveLecturers()
    {
        $this->info("🔍 Debug: getActiveLecturers()");
        
        // Get all lecturers first
        $allLecturers = Employee::where('role', 'lecturer')->get();
        $this->info("   - Total lecturers found: {$allLecturers->count()}");
        
        // Check each lecturer's study calendars
        $activeLecturers = collect();
        foreach ($allLecturers as $lecturer) {
            $studyCalendars = $lecturer->studyCalendars()->get();
            $this->info("   - Lecturer {$lecturer->user->name} (ID: {$lecturer->id}): {$studyCalendars->count()} study calendars");
            
            foreach ($studyCalendars as $calendar) {
                $this->info("     - Study Calendar ID: {$calendar->id}");
                $this->info("       - Workflow State: {$calendar->workflow_state}");
                
                if ($calendar->workflow_state == 5) {
                    $activeLecturers->push($lecturer);
                    $this->info("       ✅ ACTIVE STUDY");
                } else {
                    $this->info("       ❌ Not active (state: {$calendar->workflow_state})");
                }
            }
        }
        
        $this->info("   - Final result: {$activeLecturers->count()} active lecturers");
        
        // Get the unique lecturer IDs and load the relationships
        $uniqueLecturerIds = $activeLecturers->unique('id')->pluck('id');
        return Employee::whereIn('id', $uniqueLecturerIds)
            ->with(['studyCalendars.studyDetail', 'user'])
            ->get();
    }

    /**
     * Get lecturers approaching study completion
     */
    private function getLecturersNearCompletion()
    {
        $threeMonthsFromNow = Carbon::now()->addMonths(3);
        
        $this->info("🔍 Debug: getLecturersNearCompletion()");
        $this->info("   - Three months from now: {$threeMonthsFromNow->format('Y-m-d')}");
        
        // Get all lecturers first
        $allLecturers = Employee::where('role', 'lecturer')->get();
        $this->info("   - Total lecturers found: {$allLecturers->count()}");
        
        // Check each lecturer's study calendars
        $lecturersWithStudies = collect();
        foreach ($allLecturers as $lecturer) {
            $studyCalendars = $lecturer->studyCalendars()->get();
            $this->info("   - Lecturer {$lecturer->user->name} (ID: {$lecturer->id}): {$studyCalendars->count()} study calendars");
            
            foreach ($studyCalendars as $calendar) {
                $this->info("     - Study Calendar ID: {$calendar->id}");
                $this->info("       - Workflow State: {$calendar->workflow_state}");
                $this->info("       - Estimated End: {$calendar->estimated_study_end}");
                $this->info("       - Days until completion: " . Carbon::now()->diffInDays($calendar->estimated_study_end));
                
                if ($calendar->workflow_state == 5 && $calendar->estimated_study_end <= $threeMonthsFromNow) {
                    $lecturersWithStudies->push($lecturer);
                    $this->info("       ✅ MATCHES CRITERIA");
                } else {
                    $this->info("       ❌ Does not match criteria");
                }
            }
        }
        
        $this->info("   - Final result: {$lecturersWithStudies->count()} lecturers near completion");
        
        return $lecturersWithStudies->unique('id');
    }

    /**
     * Get current academic semester
     */
    private function getCurrentSemester(): int
    {
        $month = Carbon::now()->month;
        
        // Academic year typically starts in August/September
        if ($month >= 8) {
            return 1; // First semester
        } elseif ($month >= 1 && $month <= 6) {
            return 2; // Second semester
        } else {
            return 3; // Summer semester (if applicable)
        }
    }

    /**
     * Get missing semester documents for a lecturer
     */
    private function getMissingSemesterDocuments(Employee $lecturer, int $semester)
    {
        $requiredDocumentTypes = DocumentTypeConstants::getSemesterDocumentNames();
        $documentTypeIds = \App\Models\DocumentType::whereIn('name', $requiredDocumentTypes)->pluck('id');
        
        $existingDocuments = AcademicDocument::where('employee_id', $lecturer->id)
            ->whereIn('document_type_id', $documentTypeIds)
            ->where('semester', $semester)
            ->where('year', Carbon::now()->year)
            ->pluck('document_type_id');
        
        return $documentTypeIds->diff($existingDocuments);
    }

    /**
     * Get missing final documents for a lecturer
     */
    private function getMissingFinalDocuments(Employee $lecturer)
    {
        $requiredDocumentTypes = DocumentTypeConstants::getFinalDocumentNames();
        $documentTypeIds = \App\Models\DocumentType::whereIn('name', $requiredDocumentTypes)->pluck('id');
        
        $existingDocuments = AcademicDocument::where('employee_id', $lecturer->id)
            ->whereIn('document_type_id', $documentTypeIds)
            ->pluck('document_type_id');
        
        return $documentTypeIds->diff($existingDocuments);
    }

    /**
     * Send semester reminder to lecturer
     */
    private function sendSemesterReminder(Employee $lecturer, $missingDocuments, int $semester, bool $dryRun): void
    {
        $documentTypes = \App\Models\DocumentType::whereIn('id', $missingDocuments)->get();
        $documentList = $documentTypes->pluck('display_name')->implode(', ');
        
        $notificationData = [
            'type' => 'semester_report_reminder',
            'lecturer_name' => $lecturer->user->name,
            'semester' => $semester,
            'missing_documents' => $documentList,
            'action_url' => route('documents.semester-reports'),
            'reminder_date' => Carbon::now()->format('d M Y'),
        ];

        if (!$dryRun) {
            $lecturer->user->notify(new WorkflowNotification('semester_report_reminder', $notificationData));
            
            Log::info('Semester report reminder sent', [
                'lecturer_id' => $lecturer->id,
                'lecturer_name' => $lecturer->user->name,
                'semester' => $semester,
                'missing_documents_count' => $missingDocuments->count(),
            ]);
        }

        $this->line("Reminder for {$lecturer->user->name}: {$missingDocuments->count()} missing documents for semester {$semester}");
    }

    /**
     * Send final report reminder to lecturer
     */
    private function sendFinalReminder(Employee $lecturer, $missingDocuments, bool $dryRun): void
    {
        $documentTypes = \App\Models\DocumentType::whereIn('id', $missingDocuments)->get();
        $documentList = $documentTypes->pluck('display_name')->implode(', ');
        
        $studyCalendar = $lecturer->studyCalendars()->where('workflow_state', 5)->first();
        $daysUntilCompletion = Carbon::now()->diffInDays($studyCalendar->estimated_study_end);
        
        $notificationData = [
            'type' => 'final_report_reminder',
            'lecturer_name' => $lecturer->user->name,
            'missing_documents' => $documentList,
            'days_until_completion' => $daysUntilCompletion,
            'action_url' => route('documents.final-reports'),
            'reminder_date' => Carbon::now()->format('d M Y'),
        ];

        if (!$dryRun) {
            $lecturer->user->notify(new WorkflowNotification('final_report_reminder', $notificationData));
            
            Log::info('Final report reminder sent', [
                'lecturer_id' => $lecturer->id,
                'lecturer_name' => $lecturer->user->name,
                'days_until_completion' => $daysUntilCompletion,
                'missing_documents_count' => $missingDocuments->count(),
            ]);
        }

        $this->line("Final reminder for {$lecturer->user->name}: {$missingDocuments->count()} missing documents, {$daysUntilCompletion} days until completion");
    }
} 