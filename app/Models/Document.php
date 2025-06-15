<?php

namespace App\Models;

use App\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, HasWorkflow;

    protected string $workflowName = 'document_verification';

    protected $guarded = [];

    protected $casts = [
        'state_id' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get verification status based on workflow state (replaces verification_status field)
     */
    public function getVerificationStatusAttribute(): string
    {
        return match($this->state_id) {
            1 => 'draft',      // DRAFT
            2 => 'pending',    // PENDING
            3 => 'verified',   // VERIFIED
            4 => 'rejected',   // REJECTED
            default => 'draft'
        };
    }

    /**
     * Get latest verification note from workflow history (replaces verification_note field)
     */
    public function getVerificationNoteAttribute(): ?string
    {
        $latestHistory = $this->workflowHistory()
            ->whereNotNull('context')
            ->latest()
            ->first();

        if (!$latestHistory || !$latestHistory->context) {
            return null;
        }

        $context = is_string($latestHistory->context) 
            ? json_decode($latestHistory->context, true) 
            : $latestHistory->context;

        return $context['comment'] ?? null;
    }

    /**
     * Get the user who performed the latest workflow action
     */
    public function getLatestWorkflowUser()
    {
        return $this->workflowHistory()
            ->with('user')
            ->latest()
            ->first()?->user;
    }

    /**
     * Get formatted workflow status for display
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->state_id) {
            1 => 'Draft',
            2 => 'Menunggu Verifikasi',
            3 => 'Terverifikasi',
            4 => 'Ditolak',
            default => 'Draft'
        };
    }
}
