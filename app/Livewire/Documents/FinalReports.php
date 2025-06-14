<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class FinalReports extends Component
{
    use WithFileUploads, WithPagination;

    public $documentFile;
    public $fileName;
    public $documentSubtype;
    public $uploadModalOpen = false;
    public $viewModalOpen = false;
    public $currentDocument;

    // Document subtypes for final reports
    public $documentSubtypes = [
        'thesis' => 'Disertasi/Tesis',
        'graduation_letter' => 'Surat Kelulusan',
        'diploma' => 'Ijazah Akhir',
        'transcript' => 'Transkrip Akhir'
    ];

    protected $rules = [
        'documentFile' => 'required|file|max:10240',
        'fileName' => 'required|string|max:255',
        'documentSubtype' => 'required|string|in:thesis,graduation_letter,diploma,transcript'
    ];

    public function openUploadModal()
    {
        $this->uploadModalOpen = true;
        $this->reset(['fileName', 'documentFile', 'documentSubtype']);
    }

    public function closeUploadModal()
    {
        $this->uploadModalOpen = false;
    }

    public function openViewModal($documentId)
    {
        $this->currentDocument = Document::findOrFail($documentId);
        $this->viewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->viewModalOpen = false;
        $this->currentDocument = null;
    }

    public function uploadDocument()
    {
        $this->validate();

        $user = Auth::user();
        $employee = $user->employee;

        $filePath = $this->documentFile->store('final-reports/' . $employee->id, 'public');

        // Add metadata to the file name to identify the subtype
        $metaFileName = '[' . $this->documentSubtypes[$this->documentSubtype] . '] ' . $this->fileName;

        Document::create([
            'employee_id' => $employee->id,
            'document_type' => $this->documentSubtype === 'thesis' ? 'final_report' : 'graduation',
            'file_name' => $metaFileName,
            'file_path' => $filePath,
            'verification_status' => 'pending',
            'uploaded_at' => now()
        ]);

        $this->closeUploadModal();
        session()->flash('message', 'Dokumen laporan akhir berhasil diunggah.');
    }

    public function deleteDocument($documentId)
    {
        $document = Document::find($documentId);
        if ($document) {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
            session()->flash('message', 'Dokumen laporan akhir berhasil dihapus.');
        }
    }

    // Helper method to get document completion status
    public function getCompletionStatus()
    {
        $employee = Auth::user()->employee;;

        // Check if all required document types have been uploaded
        $uploadedTypes = Document::where('employee_id', $employee->id)
            ->whereIn('document_type', ['final_report', 'graduation'])
            ->get()
            ->pluck('file_name')
            ->toArray();

        // Check for each required document subtype
        $completionStatus = [];
        $allCompleted = true;

        foreach ($this->documentSubtypes as $key => $label) {
            $isUploaded = false;
            // Check if any document name contains the label (which was prefixed to the filename)
            foreach ($uploadedTypes as $uploadedDoc) {
                if (strpos($uploadedDoc, '[' . $label . ']') !== false) {
                    $isUploaded = true;
                    break;
                }
            }

            $completionStatus[$key] = $isUploaded;
            if (!$isUploaded) {
                $allCompleted = false;
            }
        }

        return [
            'status' => $allCompleted ? 'Lengkap' : 'Belum Lengkap',
            'details' => $completionStatus
        ];
    }

    public function getActiveStudyInfo()
    {
        $employee = Auth::user()->employee;;

        // First try to get an active study
        $activeStudy = $employee->studyCalendars()
            ->where('study_status', 'active')
            ->latest()
            ->first();

        // If no active study, get the most recent one of any status
        if (!$activeStudy) {
            $activeStudy = $employee->studyCalendars()
                ->latest()
                ->first();

            // If still no study calendar found
            if (!$activeStudy) {
                return null;
            }
        }

        // Get the most recent study program
        $studyProgram = $employee->studyPrograms()->first();
        $programName = $studyProgram ? $studyProgram->name : 'Tidak tersedia';

        // Calculate current semester based on study start date
        $startDate = Carbon::parse($activeStudy->study_start);
        $now = Carbon::now();

        // Assume 6 months per semester, starting from study_start
        $monthsDiff = $startDate->diffInMonths($now);
        $currentSemester = floor($monthsDiff / 6) + 1;

        return [
            'program' => $programName,
            'status' => $activeStudy->study_status,
            'start_date' => $startDate->format('F Y'),
            'estimated_end' => Carbon::parse($activeStudy->estimated_study_end)->format('F Y'),
            'current_semester' => $currentSemester,
            'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
        ];
    }

    public function render()
    {
        $employee = Auth::user()->employee;;

        $finalReports = Document::where('employee_id', $employee->id)
            ->whereIn('document_type', ['final_report', 'graduation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $completionStatus = $this->getCompletionStatus();
        $activeStudyInfo = $this->getActiveStudyInfo();

        return view('livewire.documents.final-reports', [
            'documents' => $finalReports,
            'completionStatus' => $completionStatus,
            'activeStudyInfo' => $activeStudyInfo
        ]);
    }
}
