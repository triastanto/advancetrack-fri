<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
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
    public $selectedDocumentTypeId;
    public $uploadModalOpen = false;
    public $viewModalOpen = false;
    public $currentDocument;
    public $availableDocumentTypes;

    protected $rules = [
        'documentFile' => 'required|file|mimes:pdf|max:10240', // 10MB limit, PDF only
        'fileName' => 'required|string|max:255',
        'selectedDocumentTypeId' => 'required|exists:document_types,id'
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
    ];

    // Helper method to get employee
    protected function getEmployee()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            throw new \Exception('Employee data not found.');
        }
        return $employee;
    }

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

    public function mount()
    {
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
                'verification_status' => 'pending',
            ]);

            $this->closeUploadModal();
            session()->flash('message', 'Dokumen berhasil diunggah dan menunggu verifikasi.');

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
                $isUploaded = $uploadedDocuments->contains('document_type_id', $docType->id);
                $completionStatus[$docType->name] = $isUploaded;

                if (!$isUploaded) {
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

    public function render()
    {
        try {
            $employee = $this->getEmployee();
            $documentTypes = $this->getFinalDocumentTypes();
            $documentTypeIds = $documentTypes->pluck('id')->toArray();

            $finalReports = Document::where('employee_id', $employee->id)
                ->whereIn('document_type_id', $documentTypeIds)
                ->with('documentType')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('livewire.documents.final-reports', [
                'documents' => $finalReports,
                'completionStatus' => $this->getCompletionStatus(),
                'activeStudyInfo' => $this->getActiveStudyInfo()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return view('livewire.documents.final-reports', [
                'documents' => collect(),
                'completionStatus' => ['status' => 'Error', 'details' => []],
                'activeStudyInfo' => null
            ]);
        }
    }
}
