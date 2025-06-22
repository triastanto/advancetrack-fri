<?php

namespace App\Livewire\Components\Document;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class DocumentUploadModal extends Component
{
    use WithFileUploads;

    // Public properties
    public $isOpen = false;
    public $documentFile = null;
    public $fileName = '';
    public $selectedDocumentTypeId = '';
    public $selectedSemester = null;
    public $availableDocumentTypes = [];
    public $employee;
    public $storageBasePath = 'documents';
    public $documentClass = Document::class; // Default document model
    public $documentCategory = ''; // Track the current document category

    // Component configuration
    public $requiresSemester = false;
    public $autoGenerateFileName = true;

    protected $listeners = [
        'document-upload-modal:open' => 'open',
        'document-upload-modal:close' => 'close'
    ];

    protected $rules = [
        'fileName' => 'required|string|max:255',
        'selectedDocumentTypeId' => 'required|exists:document_types,id',
        'selectedSemester' => 'nullable|integer|min:1|max:20'
    ];

    protected $messages = [
        'fileName.required' => 'Nama dokumen wajib diisi.',
        'fileName.max' => 'Nama dokumen maksimal 255 karakter.',
        'selectedDocumentTypeId.required' => 'Jenis dokumen wajib dipilih.',
        'selectedDocumentTypeId.exists' => 'Jenis dokumen yang dipilih tidak valid.',
        'selectedSemester.integer' => 'Semester harus berupa angka.',
        'selectedSemester.min' => 'Semester minimal 1.',
        'selectedSemester.max' => 'Semester maksimal 20.'
    ];

    public function mount($employee = null, $documentTypes = [], $storageBasePath = 'documents', $requiresSemester = false, $documentClass = Document::class)
    {
        $this->employee = $employee;
        $this->availableDocumentTypes = $documentTypes;
        $this->storageBasePath = $storageBasePath;
        $this->requiresSemester = $requiresSemester;
        $this->documentClass = $documentClass;

        if ($this->requiresSemester) {
            $this->rules['selectedSemester'] = 'required|integer|min:1|max:20';
            $this->messages['selectedSemester.required'] = 'Semester wajib dipilih.';
        }
    }

    public function open($data = [])
    {
        // Set employee if provided
        if (isset($data['employeeId'])) {
            $this->employee = Employee::find($data['employeeId']);
        }

        // Set document category
        $this->documentCategory = $data['category'] ?? '';

        // Set available document types based on category
        if (isset($data['category'])) {
            $this->setDocumentTypesForCategory($data['category']);
        }

        // Set document class based on category
        if (isset($data['category'])) {
            $this->setDocumentClass($data['category']);
        }

        // Set document type ID
        $this->selectedDocumentTypeId = $data['documentTypeId'] ?? $this->selectedDocumentTypeId;

        // Set semester if provided
        $this->selectedSemester = $data['semester'] ?? $this->selectedSemester;

        // Determine if semester is required based on category
        $this->requiresSemester = in_array($data['category'] ?? '', ['semester-reports']);

        // Update validation rules based on semester requirement
        if ($this->requiresSemester) {
            $this->rules['selectedSemester'] = 'required|integer|min:1|max:20';
            $this->messages['selectedSemester.required'] = 'Semester wajib dipilih.';
        } else {
            $this->rules['selectedSemester'] = 'nullable|integer|min:1|max:20';
        }

        // Reset and generate filename
        $this->fileName = '';
        $this->documentFile = null;
        $this->resetValidation();
        $this->generateFileName();
        $this->isOpen = true;
    }

    protected function setDocumentTypesForCategory($category)
    {
        $documentTypeConstants = new \App\Constants\DocumentTypeConstants();

        switch ($category) {
            case 'study-requirements':
                $names = array_column($documentTypeConstants::getByCategory('study_requirements'), 'name');
                break;
            case 'semester-reports':
                $names = array_column($documentTypeConstants::getByCategory('semester_documents'), 'name');
                break;
            case 'final-reports':
                $names = array_column($documentTypeConstants::getByCategory('final_documents'), 'name');
                break;
            case 'approvals':
                $names = $documentTypeConstants::getApprovalDocumentNames();
                break;
            default:
                $names = [];
        }

        $this->availableDocumentTypes = DocumentType::whereIn('name', $names)
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Set the document class based on category
     *
     * @param string $category
     */
    protected function setDocumentClass($category)
    {
        switch ($category) {
            case 'study-requirements':
            case 'semester-reports':
            case 'final-reports':
                $this->documentClass = AcademicDocument::class;
                break;
            case 'approvals':
                $this->documentClass = ApprovalDocument::class;
                break;
            default:
                $this->documentClass = Document::class;
                break;
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->documentFile = null;
        $this->documentCategory = '';
        $this->reset(['fileName', 'selectedDocumentTypeId', 'selectedSemester']);
        $this->resetValidation();
    }

    public function uploadDocument()
    {
        try {
            // Check if file exists before validation
            if (!$this->documentFile) {
                $this->addError('documentFile', 'Silakan pilih file terlebih dahulu.');
                return;
            }

            // Validate with custom rules that make file required
            $this->validate([
                'documentFile' => 'required|file|mimes:pdf|max:10240',
                'fileName' => 'required|string|max:255',
                'selectedDocumentTypeId' => 'required|exists:document_types,id',
                'selectedSemester' => $this->requiresSemester ? 'required|integer|min:1|max:20' : 'nullable|integer|min:1|max:20'
            ]);

            if (!$this->employee) {
                throw new \Exception('Employee information is required.');
            }

            if (!$this->documentFile || !is_object($this->documentFile)) {
                throw new \Exception('No valid file was uploaded.');
            }

            // Additional file validation
            if (!method_exists($this->documentFile, 'store')) {
                throw new \Exception('Invalid file object.');
            }

            $filePath = $this->documentFile->store(
                $this->storageBasePath . '/' . $this->employee->id,
                'public'
            );

            $documentType = DocumentType::findOrFail($this->selectedDocumentTypeId);

            // Validate document type compatibility with the selected model
            $this->validateDocumentTypeCompatibility($documentType);

            $documentData = [
                'employee_id' => $this->employee->id,
                'document_type_id' => $documentType->id,
                'file_name' => $this->fileName,
                'file_path' => $filePath,
                'workflow_state' => 1, // DRAFT
            ];

            if ($this->requiresSemester && $this->selectedSemester) {
                $documentData['semester'] = $this->selectedSemester;
            }

            // Create document using the appropriate model class
            $document = $this->documentClass::create($documentData);

            $this->close();

            // Emit success event globally so parent components can catch it
            $this->dispatch('document:uploaded', [
                'document' => $document,
                'message' => 'Dokumen berhasil diunggah sebagai draft.',
                'documentModel' => class_basename($this->documentClass),
                'category' => $this->documentCategory
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage());
            $this->addError('upload', 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Validate that the document type is compatible with the selected document class
     *
     * @param DocumentType $documentType
     * @throws \Exception
     */
    protected function validateDocumentTypeCompatibility($documentType)
    {
        if ($this->documentClass === AcademicDocument::class) {
            if (!AcademicDocument::isAllowedType($documentType->name)) {
                throw new \Exception("Document type '{$documentType->display_name}' is not allowed for academic documents.");
            }
        } elseif ($this->documentClass === ApprovalDocument::class) {
            if (!ApprovalDocument::isAllowedType($documentType->name)) {
                throw new \Exception("Document type '{$documentType->display_name}' is not allowed for approval documents.");
            }
        }
    }

    public function updatedSelectedDocumentTypeId()
    {
        $this->generateFileName();
    }

    public function updatedSelectedSemester()
    {
        $this->generateFileName();
    }

    public function updatedDocumentFile()
    {
        // Only validate if a file is actually selected
        if ($this->documentFile && is_object($this->documentFile)) {
            try {
                $validator = \Illuminate\Support\Facades\Validator::make(
                    ['documentFile' => $this->documentFile],
                    ['documentFile' => 'file|mimes:pdf|max:10240']
                );

                if ($validator->fails()) {
                    $this->addError('documentFile', $validator->errors()->first('documentFile'));
                }
            } catch (\Exception $e) {
                // Handle validation error
            }
        }
    }

    public function updatedFileName()
    {
        $this->validateOnly('fileName');
    }

    protected function generateFileName()
    {
        if (!$this->autoGenerateFileName || !$this->selectedDocumentTypeId) {
            return;
        }

        $documentType = DocumentType::find($this->selectedDocumentTypeId);
        if (!$documentType) {
            return;
        }

        $filename = $documentType->display_name;

        if ($this->requiresSemester && $this->selectedSemester) {
            $filename = "Semester {$this->selectedSemester} - {$filename}";
        }

        $this->fileName = $filename;
    }

    public function render()
    {
        return view('livewire.components.document.document-upload-modal');
    }
}
