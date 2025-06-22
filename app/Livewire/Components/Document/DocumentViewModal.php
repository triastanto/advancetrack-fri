<?php

namespace App\Livewire\Components\Document;

use App\Models\Document;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use Livewire\Component;

class DocumentViewModal extends Component
{
    public $isOpen = false;
    public $document = null;
    public $showWorkflowHistory = false;
    public $documentModel = null; // Track which model was used to load the document

    protected $listeners = [
        'document-view-modal:open' => 'open',
        'document-view-modal:close' => 'close',
        'toggleWorkflowHistory' => 'toggleWorkflowHistory'
    ];

    public function open($data)
    {
        \Illuminate\Support\Facades\Log::info('DocumentViewModal::open called with data:', $data);
        
        $documentId = $data['documentId'] ?? null;
        if (!$documentId) {
            \Illuminate\Support\Facades\Log::warning('DocumentViewModal::open - No documentId provided');
            return;
        }

        try {
            // Determine which model to use based on the data or try to detect automatically
            $this->documentModel = $this->determineDocumentModel($data);
            
            // Load the document using the appropriate model
            $this->document = $this->loadDocument($documentId, $this->documentModel);
            
            if (!$this->document) {
                \Illuminate\Support\Facades\Log::warning('DocumentViewModal::open - Document not found');
                return;
            }
            
            \Illuminate\Support\Facades\Log::info('DocumentViewModal::open - Document loaded:', [
                'document_id' => $this->document->id, 
                'file_name' => $this->document->file_name,
                'model_used' => $this->documentModel
            ]);
            
            $this->isOpen = true;
            $this->showWorkflowHistory = false;
            
            \Illuminate\Support\Facades\Log::info('DocumentViewModal::open - Modal opened successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DocumentViewModal::open - Error loading document:', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Determine which document model to use based on the provided data
     *
     * @param array $data
     * @return string
     */
    protected function determineDocumentModel($data)
    {
        // If model is explicitly specified in the data
        if (isset($data['documentModel'])) {
            switch ($data['documentModel']) {
                case 'AcademicDocument':
                    return AcademicDocument::class;
                case 'ApprovalDocument':
                    return ApprovalDocument::class;
                default:
                    return AcademicDocument::class; // Use AcademicDocument as default instead of abstract Document
            }
        }

        // If category is specified, determine model based on category
        if (isset($data['category'])) {
            switch ($data['category']) {
                case 'study-requirements':
                case 'semester-reports':
                case 'final-reports':
                    return AcademicDocument::class;
                case 'approvals':
                    return ApprovalDocument::class;
                default:
                    return AcademicDocument::class; // Use AcademicDocument as default instead of abstract Document
            }
        }

        // Default to AcademicDocument model instead of abstract Document
        return AcademicDocument::class;
    }

    /**
     * Load document using the specified model
     *
     * @param int $documentId
     * @param string $modelClass
     * @return Document|null
     */
    protected function loadDocument($documentId, $modelClass)
    {
        try {
            // If the model class is the abstract Document class, use AcademicDocument as fallback
            if ($modelClass === Document::class) {
                $modelClass = AcademicDocument::class;
            }
            
            return $modelClass::with([
                'employee.user', 
                'employee.studyPrograms', 
                'documentType',
                'workflowHistory.user'
            ])->findOrFail($documentId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // If the specified model doesn't find the document, try AcademicDocument as fallback
            if ($modelClass !== AcademicDocument::class) {
                \Illuminate\Support\Facades\Log::info('DocumentViewModal::loadDocument - Document not found in specified model, trying AcademicDocument as fallback');
                return $this->loadDocument($documentId, AcademicDocument::class);
            }
            return null;
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->document = null;
        $this->documentModel = null;
        $this->showWorkflowHistory = false;
    }

    public function toggleWorkflowHistory()
    {
        $this->showWorkflowHistory = !$this->showWorkflowHistory;
    }

    public function downloadDocument()
    {
        if (!$this->document) {
            return;
        }

        try {
            if (!$this->document->isInVerifiedState()) {
                $this->addError('download', 'Dokumen hanya dapat diunduh setelah terverifikasi.');
                return;
            }

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($this->document->file_path)) {
                $this->addError('download', 'File tidak ditemukan.');
                return;
            }

            $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($this->document->file_path);
            
            $this->dispatch('downloadReady', [
                'path' => $filePath,
                'filename' => $this->document->file_name
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error downloading document: ' . $e->getMessage());
            $this->addError('download', 'Terjadi kesalahan saat mengunduh dokumen.');
        }
    }

    /**
     * Get the UI document category for display/routing purposes
     *
     * @return string
     */
    public function getDocumentCategory()
    {
        if (!$this->document) {
            return '';
        }

        $documentTypeName = $this->document->documentType->name ?? '';
        $dtc = \App\Constants\DocumentTypeConstants::class;

        if (in_array($documentTypeName, $dtc::getStudyRequirementNames())) {
            return 'study-requirements';
        } elseif (in_array($documentTypeName, $dtc::getSemesterDocumentNames())) {
            return 'semester-reports';
        } elseif (in_array($documentTypeName, $dtc::getFinalDocumentNames())) {
            return 'final-reports';
        } elseif (in_array($documentTypeName, $dtc::getApprovalDocumentNames())) {
            return 'approvals';
        }

        return '';
    }

    public function render()
    {
        return view('livewire.components.document.document-view-modal');
    }
}
