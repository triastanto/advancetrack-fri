<?php

namespace App\Livewire\Administrations;

use App\Livewire\Base\WorkflowComponent;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Verification extends WorkflowComponent
{
    use WithPagination;

    public $search = '';
    public $documentType = '';
    public $studyProgram = '';
    public $selectedDocument = null;
    public $selectedTransition = null;
    public $showModal = false;
    public $verificationNote = '';

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'documentType', 'studyProgram'];

    public function render()
    {
        $query = Document::with(['employee.user', 'employee.studyPrograms', 'workflowHistory.user', 'documentType'])
            ->where(function($q) {
                $q->where('state_id', 2) // PENDING
                  ->orWhere('state_id', 4); // REJECTED (for resubmission)
            });

        if ($this->search) {
            $query->whereHas('employee.user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }
        if ($this->documentType) {
            $query->whereHas('documentType', function ($q) {
                $q->where('id', $this->documentType);
            });
        }
        if ($this->studyProgram) {
            $query->whereHas('employee.studyPrograms', function ($q) {
                $q->where('study_programs.id', $this->studyProgram);
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        $documentTypes = DocumentType::orderBy('display_name')->get();
        $studyPrograms = StudyProgram::all();

        // Debug information
        $currentUser = Auth::user();
        $userRoles = $currentUser ? $currentUser->employee?->role : 'No role';
        $canManageWorkflow = $this->canUserManageWorkflow();

        return view('livewire.administrations.verification', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'studyPrograms' => $studyPrograms,
            'canManageWorkflow' => $canManageWorkflow,
            'userRoles' => $userRoles,
        ]);
    }

    public function showDocument($id)
    {
        $this->selectedDocument = Document::with(['employee.user', 'employee.studyPrograms', 'documentType'])->findOrFail($id);
        $this->verificationNote = $this->selectedDocument->verification_note; // This now uses the accessor method
        $this->showModal = true;
    }

    public function verifyDocument()
    {
        if (!$this->selectedDocument) return;

        // Check if user is authenticated and has employee data
        $user = Auth::user();
        if (!$user || !$user->employee) {
            $errorMsg = 'Akses tidak diizinkan. User tidak memiliki data employee.';
            Log::warning('Verification attempt failed: No employee data', [
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'document_id' => $this->selectedDocument->id,
            ]);
            session()->flash('error', $errorMsg);
            return;
        }

        try {
            // Check if user can perform the transition
            if (!$this->selectedDocument->canTransition(2)) {
                $blockingReason = $this->selectedDocument->getTransitionBlockingReason(2);
                $errorMsg = 'Tidak dapat melakukan verifikasi: ' . $blockingReason;
                
                Log::warning('Verification attempt blocked', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->employee->role,
                    'document_id' => $this->selectedDocument->id,
                    'blocking_reason' => $blockingReason,
                ]);
                
                session()->flash('error', $errorMsg);
                return;
            }

            // Use workflow transition instead of direct status update
            $context = [
                'user_id' => Auth::id(),
                'comment' => $this->verificationNote,
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            // Apply VERIFY transition (transition ID 2)
            $this->selectedDocument->applyTransition(2, $context);

            $this->showModal = false;
            session()->flash('success', 'Dokumen berhasil diverifikasi.');
        } catch (\Exception $e) {
            $errorMsg = 'Terjadi kesalahan: ' . $e->getMessage();
            
            Log::error('Verification attempt failed with exception', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->employee->role,
                'document_id' => $this->selectedDocument->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', $errorMsg);
        }
    }

    public function rejectDocument()
    {
        if (!$this->selectedDocument) return;

        $this->validate([
            'verificationNote' => 'required|string|min:5',
        ]);

        // Check if user is authenticated and has employee data
        $user = Auth::user();
        if (!$user || !$user->employee) {
            $errorMsg = 'Akses tidak diizinkan. User tidak memiliki data employee.';
            Log::warning('Rejection attempt failed: No employee data', [
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'document_id' => $this->selectedDocument->id,
            ]);
            session()->flash('error', $errorMsg);
            return;
        }

        try {
            // Check if user can perform the transition
            if (!$this->selectedDocument->canTransition(3)) {
                $blockingReason = $this->selectedDocument->getTransitionBlockingReason(3);
                $errorMsg = 'Tidak dapat melakukan penolakan: ' . $blockingReason;
                
                Log::warning('Rejection attempt blocked', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->employee->role,
                    'document_id' => $this->selectedDocument->id,
                    'blocking_reason' => $blockingReason,
                ]);
                
                session()->flash('error', $errorMsg);
                return;
            }

            // Use workflow transition instead of direct status update
            $context = [
                'user_id' => Auth::id(),
                'comment' => $this->verificationNote,
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            // Apply REJECT transition (transition ID 3)
            $this->selectedDocument->applyTransition(3, $context);

            $this->showModal = false;
            session()->flash('error', 'Dokumen ditolak.');
        } catch (\Exception $e) {
            $errorMsg = 'Terjadi kesalahan: ' . $e->getMessage();
            
            Log::error('Rejection attempt failed with exception', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->employee->role,
                'document_id' => $this->selectedDocument->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', $errorMsg);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDocument = null;
        $this->verificationNote = '';
    }

    // Implementation of abstract methods from WorkflowComponent
    protected function getWorkflowModelClass(): string
    {
        return Document::class;
    }

    protected function getWorkflowDocumentPropertyName(): string
    {
        return 'selectedDocument';
    }

    protected function getWorkflowCommentPropertyName(): string
    {
        return 'verificationNote';
    }

    protected function getWorkflowTransitionPropertyName(): string  
    {
        return 'selectedTransition'; // Note: this property might need to be added to Verification class
    }
}
