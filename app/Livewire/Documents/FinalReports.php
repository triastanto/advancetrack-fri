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

class FinalReports extends WorkflowComponent
{
    use WithFileUploads, WithPagination;

    public $documentFile;
    public $fileName;
    public $selectedDocumentTypeId;
    public $uploadModalOpen = false;
    public $viewModalOpen = false;
    public $currentDocument;
    public $availableDocumentTypes;
    public $selectedTransition;
    public $transitionComment = '';
    public $showWorkflowHistory = false;

    protected $rules = [
        'documentFile' => 'required|file|mimes:pdf|max:10240', // 10MB limit, PDF only
        'fileName' => 'required|string|max:255',
        'selectedDocumentTypeId' => 'required|exists:document_types,id',
        'transitionComment' => 'nullable|string|max:1000'
    ];

    protected $messages = [
        'documentFile.required' => 'File dokumen wajib dipilih.',
        'documentFile.file' => 'File yang dipilih tidak valid.',
        'documentFile.mimes' => 'File harus berformat PDF.',
        'documentFile.max' => 'Ukuran file maksimal 10MB.',
        'fileName.required' => 'Nama dokumen wajib diisi.',
        'fileName.max' => 'Nama dokumen maksimal 255 karakter.',
        'selectedDocumentTypeId.required' => 'Jenis dokumen laporan akhir wajib dipilih.',
        'selectedDocumentTypeId.exists' => 'Jenis dokumen yang dipilih tidak valid.',
        'transitionComment.max' => 'Komentar maksimal 1000 karakter.',
    ];

    // Helper method to get final document types
    protected function getFinalDocumentTypes()
    {
        $finalDocumentNames = array_column(
            DocumentTypeConstants::getByCategory('final_documents'),
            'name'
        );

        return DocumentType::whereIn('name', $finalDocumentNames)
            ->orderBy('display_name')
            ->get();
    }

    public function mount(...$parameters)
    {
        parent::mount(...$parameters);
        $this->selectedDocumentTypeId = '';
        $this->availableDocumentTypes = $this->getFinalDocumentTypes();
    }

    // Modal Methods
    public function openUploadModal()
    {
        if (!$this->selectedDocumentTypeId) {
            return;
        }

        $this->uploadModalOpen = true;
        $this->reset(['fileName', 'documentFile']);
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

    public function submitDocument($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);
            
            // Check if document is in draft state
            if ($document->state_id !== 1) {
                session()->flash('error', 'Dokumen tidak dalam status draft.');
                return;
            }

            // Check if user owns this document
            $employee = $this->getEmployee();
            if ($document->employee_id !== $employee->id) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengirim dokumen ini.');
                return;
            }

            // Check if user can perform this transition
            if (method_exists($document, 'canTransition') && !$document->canTransition(1)) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengirim dokumen ini.');
                return;
            }

            // Submit the document (transition from DRAFT to PENDING)
            $context = [
                'user_id' => Auth::id(),
                'comment' => 'Dokumen dikirim untuk verifikasi',
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            // Apply SUBMIT transition (transition ID 1)
            $document->applyTransition(1, $context);

            session()->flash('message', 'Dokumen berhasil dikirim untuk verifikasi.');
        } catch (\Exception $e) {
            Log::error('Error in submitDocument: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Terjadi kesalahan saat mengirim dokumen: ' . $e->getMessage());
        }
    }

    // Document Management Methods
    public function uploadDocument()
    {
        try {
            $this->validate();
            $employee = $this->getEmployee();

            if (!$this->documentFile) {
                throw new \Exception('No file was uploaded.');
            }

            $filePath = $this->documentFile->store('final-reports/' . $employee->id, 'public');
            $documentType = DocumentType::findOrFail($this->selectedDocumentTypeId);

            Document::create([
                'employee_id' => $employee->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->fileName,
                'file_path' => $filePath,
                'state_id' => 1, // DRAFT - workflow will handle this automatically
            ]);

            $this->closeUploadModal();
            session()->flash('message', 'Dokumen berhasil diunggah sebagai draft. Klik "Kirim untuk Verifikasi" untuk mengirimkan dokumen.');

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

    // Status and Info Methods
    public function getCompletionStatus()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getFinalDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $uploadedDocuments = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with('documentType')
                ->get();

            $completionStatus = [];
            $allCompleted = true;

            foreach ($this->availableDocumentTypes as $docType) {
                $document = $uploadedDocuments->where('document_type_id', $docType->id)->first();
                
                if ($document) {
                    $stateInfo = $document->getWorkflowStateInfo();
                    $completionStatus[$docType->name] = [
                        'uploaded' => true,
                        'state_info' => $stateInfo
                    ];
                    
                    // Consider document complete only if it's verified
                    if ($document->state_id !== 3) { // 3 = VERIFIED
                        $allCompleted = false;
                    }
                } else {
                    $completionStatus[$docType->name] = [
                        'uploaded' => false,
                        'state_info' => null
                    ];
                    $allCompleted = false;
                }
            }

            return [
                'status' => $allCompleted ? 'Lengkap' : 'Belum Lengkap',
                'details' => $completionStatus
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Error',
                'details' => []
            ];
        }
    }

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
            $now = Carbon::now();
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
        } catch (\Exception $e) {
            return null;
        }
    }

    // Livewire Update Methods
    public function updatedSelectedDocumentTypeId()
    {
        // Reserved for future enhancements
    }

    public function updatedDocumentFile()
    {
        $this->validateOnly('documentFile');
    }

    public function updatedFileName()
    {
        $this->validateOnly('fileName');
    }

    // Override trait methods for custom behavior
    protected function getSuccessMessage(): string
    {
        return 'Status dokumen berhasil diperbarui.';
    }

    protected function getSuccessFlashKey(): string
    {
        return 'message';
    }

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getFinalDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $finalReports = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with(['documentType', 'workflowHistory.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.final-reports', [
                'documents' => $finalReports,
                'completionStatus' => $this->getCompletionStatus(),
                'activeStudyInfo' => $this->getActiveStudyInfo(),
                'canManageWorkflow' => $this->canUserManageWorkflow()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());

            // Return empty paginated result to maintain consistency
            $emptyPaginator = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url()]
            );

            return view('livewire.documents.final-reports', [
                'documents' => $emptyPaginator,
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'activeStudyInfo' => null,
                'canManageWorkflow' => false
            ]);
        }
    }

    // Implementation of abstract methods from WorkflowComponent
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
}
