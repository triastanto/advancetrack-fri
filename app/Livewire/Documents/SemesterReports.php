<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use App\Livewire\Base\WorkflowComponent;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class SemesterReports extends WorkflowComponent
{
    use WithFileUploads, WithPagination;

    public $documentFile;
    public $fileName;
    public $selectedDocumentTypeId;
    public $selectedSemester = '';
    public $uploadModalOpen = false;
    public $viewModalOpen = false;
    public $currentDocument;
    public $availableDocumentTypes;
    public $selectedTransition;
    public $transitionComment = '';
    public $showWorkflowHistory = false;

    protected $rules = [
        'documentFile' => 'required|file|mimes:pdf|max:10240',
        'fileName' => 'required|string|max:255',
        'selectedDocumentTypeId' => 'required|exists:document_types,id',
        'selectedSemester' => 'required|integer|min:1|max:20',
        'transitionComment' => 'nullable|string|max:1000'
    ];

    protected $messages = [
        'documentFile.required' => 'File dokumen wajib dipilih.',
        'documentFile.file' => 'File yang dipilih tidak valid.',
        'documentFile.mimes' => 'File harus berformat PDF.',
        'documentFile.max' => 'Ukuran file maksimal 10MB.',
        'fileName.required' => 'Nama dokumen wajib diisi.',
        'fileName.max' => 'Nama dokumen maksimal 255 karakter.',
        'selectedDocumentTypeId.required' => 'Jenis dokumen laporan semester wajib dipilih.',
        'selectedDocumentTypeId.exists' => 'Jenis dokumen yang dipilih tidak valid.',
        'selectedSemester.required' => 'Semester wajib dipilih.',
        'selectedSemester.integer' => 'Semester harus berupa angka.',
        'selectedSemester.min' => 'Semester minimal 1.',
        'selectedSemester.max' => 'Semester maksimal 20.',
        'transitionComment.max' => 'Komentar maksimal 1000 karakter.',
    ];

    protected function getSemesterReportDocumentTypes()
    {
        $semesterDocumentNames = array_column(
            DocumentTypeConstants::getByCategory('semester_documents'),
            'name'
        );

        return DocumentType::whereIn('name', $semesterDocumentNames)
            ->orderBy('display_name')
            ->get();
    }

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->selectedDocumentTypeId = '';
        $this->availableDocumentTypes = $this->getSemesterReportDocumentTypes();
        $this->selectedSemester = $this->getCurrentSemester();
    }

    // Reuse modal methods from FinalReports pattern
    public function openUploadModal()
    {
        if (!$this->selectedDocumentTypeId) {
            return;
        }

        $this->uploadModalOpen = true;
        $this->reset(['fileName', 'documentFile']);
        
        // Auto-suggest filename
        if ($this->selectedDocumentTypeId && $this->selectedSemester) {
            $documentType = DocumentType::find($this->selectedDocumentTypeId);
            $this->fileName = "Semester {$this->selectedSemester} - {$documentType->display_name}";
        }
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
        $this->showWorkflowHistory = false;
    }

    public function openWorkflowModal($documentId, $transitionId)
    {
        $this->currentDocument = Document::with(['employee.user', 'employee.studyPrograms', 'documentType'])->findOrFail($documentId);
        $this->selectedTransition = $transitionId;
        $this->transitionComment = '';
        $this->workflowModalOpen = true;
    }

    public function closeWorkflowModal()
    {
        $this->workflowModalOpen = false;
        $this->currentDocument = null;
        $this->selectedTransition = null;
        $this->transitionComment = '';
    }

    public function toggleWorkflowHistory()
    {
        $this->showWorkflowHistory = !$this->showWorkflowHistory;
    }

    // Reuse document management methods from FinalReports pattern
    public function submitDocument($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);
            
            if (!$document->isInDraftState()) {
                session()->flash('error', 'Dokumen tidak dalam status draft.');
                return;
            }

            $employee = $this->getEmployee();
            if ($document->employee_id !== $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengirim dokumen ini.');
                return;
            }

            if (method_exists($document, 'canTransition') && !$document->canTransition(1)) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengirim dokumen ini.');
                return;
            }

            $context = [
                'user_id' => Auth::id(),
                'comment' => 'Laporan semester dikirim untuk verifikasi',
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            $document->applyTransition(1, $context);
            session()->flash('message', 'Laporan semester berhasil dikirim untuk verifikasi.');
        } catch (\Exception $e) {
            Log::error('Error in submitDocument: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengirim dokumen: ' . $e->getMessage());
        }
    }

    public function uploadDocument()
    {
        try {
            $this->validate();
            $employee = $this->getEmployee();

            if (!$this->documentFile) {
                throw new \Exception('No file was uploaded.');
            }

            $filePath = $this->documentFile->store('semester-reports/' . $employee->id, 'public');
            $documentType = DocumentType::findOrFail($this->selectedDocumentTypeId);

            Document::create([
                'employee_id' => $employee->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->fileName,
                'file_path' => $filePath,
                'state_id' => 1,
                'semester' => $this->selectedSemester,
            ]);

            $this->closeUploadModal();
            session()->flash('message', 'Laporan semester berhasil diunggah sebagai draft. Klik "Kirim untuk Verifikasi" untuk mengirimkan dokumen.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage());
        }
    }

    public function deleteDocument($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);

            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();
            session()->flash('message', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage());
        }
    }

    // Enhanced completion status for semester reports with per-semester tracking
    public function getCompletionStatus()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getSemesterReportDocumentTypes();
            $currentSemester = $this->getCurrentSemester();
            
            $completionStatus = [];
            $overallCompleted = true;

            // For semester reports, we need to track completion per semester per document type
            foreach ($documentTypes as $docType) {
                $semesterData = [];
                
                // Check each semester up to current semester
                for ($semester = 1; $semester <= $currentSemester; $semester++) {
                    $document = Document::where('employee_id', $employee->id)
                        ->where('document_type_id', $docType->id)
                        ->where('semester', $semester)
                        ->first();

                    if ($document) {
                        $semesterData[$semester] = [
                            'uploaded' => true,
                            'state_info' => [
                                'id' => $document->state_id,
                                'label' => $this->getStateLabel($document->state_id)
                            ]
                        ];
                    } else {
                        $semesterData[$semester] = [
                            'uploaded' => false,
                            'state_info' => null
                        ];
                        $overallCompleted = false;
                    }
                }

                $completionStatus[$docType->name] = [
                    'semesters' => $semesterData,
                    // For backward compatibility, also include current semester status
                    'uploaded' => isset($semesterData[$currentSemester]) ? $semesterData[$currentSemester]['uploaded'] : false,
                    'state_info' => isset($semesterData[$currentSemester]) ? $semesterData[$currentSemester]['state_info'] : null
                ];
            }

            return [
                'status' => $overallCompleted ? 'Lengkap' : 'Belum Lengkap',
                'details' => $completionStatus
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCompletionStatus: ' . $e->getMessage());
            return [
                'status' => 'Error',
                'details' => []
            ];
        }
    }

    public function getCurrentSemester()
    {
        try {
            $employee = $this->getEmployee();
            
            $activeStudy = $employee->studyCalendars()
                ->where('study_status', 'active')
                ->latest()
                ->first();

            if (!$activeStudy) {
                return 1;
            }

            $startDate = Carbon::parse($activeStudy->study_start);
            $now = Carbon::now();
            $monthsDiff = $startDate->diffInMonths($now);
            
            return max(1, floor($monthsDiff / 6) + 1);
        } catch (\Exception $e) {
            return 1;
        }
    }

    // Reuse active study info from FinalReports
    public function getActiveStudyInfo()
    {
        try {
            $employee = $this->getEmployee();

            $activeStudy = $employee->studyCalendars()
                ->where('study_status', 'active')
                ->latest()
                ->first();

            if (!$activeStudy) {
                $activeStudy = $employee->studyCalendars()
                    ->latest()
                    ->first();

                if (!$activeStudy) {
                    return null;
                }
            }

            $studyProgram = $employee->studyPrograms()->first();
            $programName = $studyProgram ? $studyProgram->name : 'Tidak tersedia';

            $startDate = Carbon::parse($activeStudy->study_start);
            $currentSemester = $this->getCurrentSemester();

            return [
                'program' => $programName,
                'status' => $activeStudy->study_status,
                'start_date' => $startDate->format('F Y'),
                'estimated_end' => Carbon::parse($activeStudy->estimated_study_end)->format('F Y'),
                'current_semester' => $currentSemester,
                'has_multiple_studies' => $employee->studyCalendars()->count() > 1,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    // Update methods
    public function updatedSelectedDocumentTypeId()
    {
        if ($this->selectedDocumentTypeId && $this->selectedSemester) {
            $documentType = DocumentType::find($this->selectedDocumentTypeId);
            $this->fileName = "Semester {$this->selectedSemester} - {$documentType->display_name}";
        }
    }

    public function updatedSelectedSemester()
    {
        if ($this->selectedDocumentTypeId && $this->selectedSemester) {
            $documentType = DocumentType::find($this->selectedDocumentTypeId);
            $this->fileName = "Semester {$this->selectedSemester} - {$documentType->display_name}";
        }
    }

    public function updatedDocumentFile()
    {
        $this->validateOnly('documentFile');
    }

    public function updatedFileName()
    {
        $this->validateOnly('fileName');
    }

    protected function getSuccessMessage(): string
    {
        return 'Status laporan semester berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getSemesterReportDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $semesterReports = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('semester', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.semester-reports', [
                'documents' => $semesterReports,
                'completionStatus' => $this->getCompletionStatus(),
                'activeStudyInfo' => $this->getActiveStudyInfo(),
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            $emptyPaginator = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url()]
            );

            return view('livewire.documents.semester-reports', [
                'documents' => $emptyPaginator,
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'activeStudyInfo' => null,
                'canManageWorkflow' => false
            ]);
        }
    }

    // Required abstract method implementations
    protected function getWorkflowModelClass(): string
    {
        return Document::class;
    }

    protected function getWorkflowDocumentPropertyName(): string
    {
        return 'currentDocument';
    }

    protected function getWorkflowCommentPropertyName(): string
    {
        return 'transitionComment';
    }

    protected function getWorkflowTransitionPropertyName(): string
    {
        return 'selectedTransition';
    }

    // Helper method to get state labels
    private function getStateLabel($stateId)
    {
        $stateLabels = [
            1 => 'Draft',
            2 => 'Menunggu Verifikasi',
            3 => 'Terverifikasi',
            4 => 'Ditolak'
        ];
        
        return $stateLabels[$stateId] ?? 'Unknown';
    }
}
