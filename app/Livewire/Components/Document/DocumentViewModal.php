<?php

namespace App\Livewire\Components\Document;

use App\Models\Document;
use Livewire\Component;

class DocumentViewModal extends Component
{
    public $isOpen = false;
    public $document = null;
    public $showWorkflowHistory = false;

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
            $this->document = Document::with([
                'employee.user', 
                'employee.studyPrograms', 
                'documentType',
                'workflowHistory.user'
            ])->findOrFail($documentId);
            
            \Illuminate\Support\Facades\Log::info('DocumentViewModal::open - Document loaded:', ['document_id' => $this->document->id, 'file_name' => $this->document->file_name]);
            
            $this->isOpen = true;
            $this->showWorkflowHistory = false;
            
            \Illuminate\Support\Facades\Log::info('DocumentViewModal::open - Modal opened successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DocumentViewModal::open - Error loading document:', ['error' => $e->getMessage()]);
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->document = null;
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

    public function render()
    {
        return view('livewire.components.document.document-view-modal');
    }
}
