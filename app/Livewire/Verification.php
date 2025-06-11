<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\Employee;
use App\Models\StudyProgram;

class Verification extends Component
{
    use WithPagination;

    public $search = '';
    public $documentType = '';
    public $studyProgram = '';
    public $selectedDocument = null;
    public $showModal = false;
    public $verificationNote = '';

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'documentType', 'studyProgram'];

    public function render()
    {
        $query = Document::with(['employee.user', 'employee.studyPrograms'])
            ->whereIn('verification_status', ['pending', 'rejected']);

        if ($this->search) {
            $query->whereHas('employee.user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }
        if ($this->documentType) {
            $query->where('document_type', $this->documentType);
        }
        if ($this->studyProgram) {
            $query->whereHas('employee.studyPrograms', function ($q) {
                $q->where('study_programs.id', $this->studyProgram);
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        $documentTypes = Document::select('document_type')->distinct()->pluck('document_type');
        $studyPrograms = StudyProgram::all();

        return view('livewire.verification', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'studyPrograms' => $studyPrograms,
        ]);
    }

    public function showDocument($id)
    {
        $this->selectedDocument = Document::with(['employee.user', 'employee.studyPrograms'])->findOrFail($id);
        $this->verificationNote = $this->selectedDocument->verification_note;
        $this->showModal = true;
    }

    public function verifyDocument()
    {
        if (!$this->selectedDocument) return;
        $this->selectedDocument->update([
            'verification_status' => 'verified',
            'verification_note' => $this->verificationNote,
        ]);
        $this->showModal = false;
        session()->flash('success', 'Dokumen berhasil diverifikasi.');
    }

    public function rejectDocument()
    {
        if (!$this->selectedDocument) return;
        $this->validate([
            'verificationNote' => 'required|string|min:5',
        ]);
        $this->selectedDocument->update([
            'verification_status' => 'rejected',
            'verification_note' => $this->verificationNote,
        ]);
        $this->showModal = false;
        session()->flash('error', 'Dokumen ditolak.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDocument = null;
        $this->verificationNote = '';
    }
}
