<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Constants\DocumentTypeConstants;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $role = $user?->employee?->role ?? null;
        $studyInfo = null;
        $documentStats = null;
        $recentNotifications = null;
        $recentDocuments = null;
        
        if ($user && $user->employee) {
            // Use trait or method to get active study info for the employee
            $employee = $user->employee;
            
            // Debug: Log employee data
            Log::info('Dashboard: Employee data', [
                'employee_id' => $employee->id,
                'role' => $employee->role,
                'name' => $employee->user->name ?? 'Unknown'
            ]);
            
            if (method_exists($employee, 'studyCalendars')) {
                $activeStudy = $employee->studyCalendars()
                    ->with('studyDetail.studyProgram')
                    ->where('workflow_state', 5) // ACTIVE
                    ->latest()
                    ->first();
                if (!$activeStudy) {
                    $activeStudy = $employee->studyCalendars()
                        ->with('studyDetail.studyProgram')
                        ->latest()
                        ->first();
                }
                
                // Debug: Log study calendar data
                Log::info('Dashboard: Study calendar data', [
                    'has_active_study' => $activeStudy ? true : false,
                    'study_id' => $activeStudy?->id,
                    'workflow_state' => $activeStudy?->workflow_state,
                    'study_program' => $activeStudy?->studyDetail?->studyProgram?->name ?? 'Unknown'
                ]);
                
                if ($activeStudy) {
                    $studyDetail = $activeStudy->studyDetail;
                    $programName = $studyDetail && $studyDetail->studyProgram ? $studyDetail->studyProgram->name : 'Tidak tersedia';
                    $startDate = $activeStudy->study_start ? \Carbon\Carbon::parse($activeStudy->study_start) : null;
                    $estimatedEnd = $activeStudy->estimated_study_end ? \Carbon\Carbon::parse($activeStudy->estimated_study_end) : null;
                    $now = now();
                    $monthsDiff = $startDate ? $startDate->diffInMonths($now) : 0;
                    $currentSemester = $startDate ? max(1, floor($monthsDiff / 6) + 1) : null;
                    // Get total semester from study details
                    $totalSemester = $activeStudy->studyDetail ? $activeStudy->studyDetail->total_semester : null;
                    
                    $studyInfo = [
                        'program' => $programName,
                        'study_program_name' => $programName, // Add this field for the component
                        'status' => $activeStudy->workflow_state,
                        'start_date' => $startDate ? $startDate->format('d M Y') : '-',
                        'estimated_end' => $estimatedEnd ? $estimatedEnd->format('d M Y') : '-',
                        'current_semester' => $currentSemester,
                        'total_semester' => $totalSemester, // Add total semester from study details
                        'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
                    ];
                }
            }
            
            // Get document statistics for lecturer
            if ($role === 'lecturer') {
                $documentStats = $this->getDocumentStats($employee);
                $recentNotifications = $this->getRecentNotifications($user);
                $recentDocuments = $this->getRecentDocuments($employee);
                
                // Debug: Log document stats
                Log::info('Dashboard: Document stats', [
                    'stats' => $documentStats,
                    'notifications_count' => $recentNotifications?->count() ?? 0,
                    'recent_documents_count' => $recentDocuments?->count() ?? 0
                ]);
            }
        }
        
        // Always pass studyInfo to the view, even if null
        return view('livewire.dashboard.dashboard', [
            'role' => $role,
            'studyInfo' => $studyInfo ?? [],
            'documentStats' => $documentStats,
            'recentNotifications' => $recentNotifications,
            'recentDocuments' => $recentDocuments,
        ]);
    }

    /**
     * Get the current user's employee record
     */
    private function getEmployee()
    {
        $user = Auth::user();
        return $user?->employee;
    }

    /**
     * Get document statistics for lecturer
     */
    private function getDocumentStats(Employee $employee): array
    {
        try {
            // Get academic documents
            $academicDocs = AcademicDocument::where('employee_id', $employee->id)->get();
            $pendingAcademic = $academicDocs->where('workflow_state', 2)->count();
            $verifiedAcademic = $academicDocs->where('workflow_state', 3)->count();
            $rejectedAcademic = $academicDocs->where('workflow_state', 4)->count();
            
            // Get approval documents
            $approvalDocs = ApprovalDocument::where('employee_id', $employee->id)->get();
            $pendingApproval = $approvalDocs->where('workflow_state', 2)->count();
            $verifiedApproval = $approvalDocs->where('workflow_state', 3)->count();
            $rejectedApproval = $approvalDocs->where('workflow_state', 4)->count();
            
            $stats = [
                'pending' => $pendingAcademic + $pendingApproval,
                'verified' => $verifiedAcademic + $verifiedApproval,
                'rejected' => $rejectedAcademic + $rejectedApproval,
                'total' => $academicDocs->count() + $approvalDocs->count(),
            ];
            
            // Debug: Log detailed document stats
            Log::info('Dashboard: Detailed document stats', [
                'employee_id' => $employee->id,
                'academic_docs_count' => $academicDocs->count(),
                'approval_docs_count' => $approvalDocs->count(),
                'academic_pending' => $pendingAcademic,
                'academic_verified' => $verifiedAcademic,
                'academic_rejected' => $rejectedAcademic,
                'approval_pending' => $pendingApproval,
                'approval_verified' => $verifiedApproval,
                'approval_rejected' => $rejectedApproval,
                'total_stats' => $stats
            ]);
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Dashboard: Error getting document stats', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
            return [
                'pending' => 0,
                'verified' => 0,
                'rejected' => 0,
                'total' => 0,
            ];
        }
    }

    /**
     * Get recent notifications for user
     */
    private function getRecentNotifications($user)
    {
        try {
            $notifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get();
                
            // Debug: Log notifications data
            Log::info('Dashboard: Recent notifications', [
                'user_id' => $user->id,
                'notifications_count' => $notifications->count(),
                'notification_ids' => $notifications->pluck('id')->toArray()
            ]);
            
            return $notifications;
        } catch (\Exception $e) {
            Log::error('Dashboard: Error getting notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Get recent documents for lecturer
     */
    private function getRecentDocuments(Employee $employee)
    {
        try {
            $academicDocs = AcademicDocument::where('employee_id', $employee->id)
                ->with(['documentType'])
                ->latest()
                ->take(3)
                ->get();
                
            $approvalDocs = ApprovalDocument::where('employee_id', $employee->id)
                ->with(['documentType'])
                ->latest()
                ->take(3)
                ->get();
                
            $recentDocs = $academicDocs->concat($approvalDocs)->sortByDesc('created_at')->take(5);
            
            // Debug: Log recent documents data
            Log::info('Dashboard: Recent documents', [
                'employee_id' => $employee->id,
                'academic_docs_count' => $academicDocs->count(),
                'approval_docs_count' => $approvalDocs->count(),
                'total_recent_docs' => $recentDocs->count(),
                'document_ids' => $recentDocs->pluck('id')->toArray()
            ]);
            
            return $recentDocs;
        } catch (\Exception $e) {
            Log::error('Dashboard: Error getting recent documents', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Get academic document reminders for the current user
     */
    public function getAcademicReminders()
    {
        $employee = $this->getEmployee();
        if (!$employee || $employee->role !== 'lecturer') {
            return collect();
        }

        $reminders = collect();

        // Check for semester report reminders
        $activeStudy = $employee->studyCalendars()
            ->where('workflow_state', 5) // ACTIVE state
            ->first();

        if ($activeStudy) {
            $currentSemester = $this->getCurrentSemester();
            $missingSemesterDocs = $this->getMissingSemesterDocuments($employee, $currentSemester);
            
            if ($missingSemesterDocs->isNotEmpty()) {
                $reminders->push([
                    'type' => 'semester_report',
                    'title' => 'Laporan Semester Belum Lengkap',
                    'message' => "Anda belum mengunggah {$missingSemesterDocs->count()} dokumen laporan semester {$currentSemester}",
                    'icon' => '📚',
                    'color' => 'warning',
                    'action_url' => route('documents.semester-reports'),
                    'action_text' => 'Unggah Dokumen',
                    'priority' => 'medium'
                ]);
            }

            // Check for final report reminders
            $daysUntilCompletion = Carbon::now()->diffInDays($activeStudy->estimated_study_end);
            if ($daysUntilCompletion <= 90) { // 3 months before completion
                $missingFinalDocs = $this->getMissingFinalDocuments($employee);
                
                if ($missingFinalDocs->isNotEmpty()) {
                    $reminders->push([
                        'type' => 'final_report',
                        'title' => 'Laporan Akhir Belum Lengkap',
                        'message' => "Studi akan selesai dalam {$daysUntilCompletion} hari. Anda belum mengunggah {$missingFinalDocs->count()} dokumen laporan akhir",
                        'icon' => '🎓',
                        'color' => 'danger',
                        'action_url' => route('documents.final-reports'),
                        'action_text' => 'Unggah Dokumen',
                        'priority' => 'high'
                    ]);
                }
            }
        }

        return $reminders;
    }

    /**
     * Get current academic semester
     */
    private function getCurrentSemester(): int
    {
        $month = Carbon::now()->month;
        
        if ($month >= 8) {
            return 1; // First semester
        } elseif ($month >= 1 && $month <= 6) {
            return 2; // Second semester
        } else {
            return 3; // Summer semester
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
}