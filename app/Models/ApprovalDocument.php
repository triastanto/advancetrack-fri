<?php

namespace App\Models;

use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalDocument extends Document
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ApprovalDocumentFactory::new();
    }
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * Boot the model
     */
    protected static function booted()
    {
        // Add global scope to only include approval document types
        static::addGlobalScope('approval_documents', function (Builder $query) {
            $query->whereHas('documentType', function ($q) {
                $q->whereIn('name', self::getAllowedTypes());
            });
        });

        // Validate document type on save
        static::saving(function ($document) {
            if (!$document->documentType) {
                return;
            }

            if (!self::isAllowedType($document->documentType->name)) {
                throw new \InvalidArgumentException('Invalid document type for approval document: ' . $document->documentType->name);
            }
        });
    }

    /**
     * Get allowed document types for ApprovalDocument
     */
    public static function getAllowedTypes(): array
    {
        return array_merge(
            \App\Constants\DocumentTypeConstants::getApprovalDocumentNames(),
            \App\Constants\DocumentTypeConstants::getExtensionApprovalDocumentNames()
        );
    }

    /**
     * Get the workflow name for this model
     */
    public function getWorkflowName(): string
    {
        return 'verification_by_management';
    }

    /**
     * Create a new ApprovalDocument with a document type by name
     */
    public static function createWithDocumentType(string $documentTypeName, array $attributes = []): self
    {
        $documentType = DocumentType::where('name', $documentTypeName)->first();

        if (!$documentType) {
            throw new \InvalidArgumentException("Document type not found: {$documentTypeName}");
        }

        if (!self::isAllowedType($documentTypeName)) {
            throw new \InvalidArgumentException("Document type not allowed for approval documents: {$documentTypeName}");
        }

        $attributes['document_type_id'] = $documentType->id;
        return self::create($attributes);
    }

    /**
     * Scope query for documents by type name
     */
    public function scopeByDocumentTypeName(Builder $query, string $typeName): Builder
    {
        return $query->whereHas('documentType', function ($q) use ($typeName) {
            $q->where('name', $typeName);
        });
    }

    /**
     * Get pending approval documents for an employee
     */
    public static function getPendingApprovalDocuments(int $employeeId): Builder
    {
        return self::where('employee_id', $employeeId)
            ->where('workflow_state', 2) // PENDING state
            ->with(['documentType', 'workflowHistory.user']);
    }

    /**
     * Check if document is in verified state
     */
    public function isInVerifiedState(): bool
    {
        return $this->workflow_state == 3; // VERIFIED state
    }

    /**
     * Check if document is in approval process
     */
    public function isInApprovalProcess(): bool
    {
        return $this->workflow_state == 2; // PENDING state
    }

    /**
     * Check if document has been approved
     */
    public function isApproved(): bool
    {
        return $this->workflow_state == 3; // APPROVED state
    }

    /**
     * Check if document requires department head approval
     */
    public function requiresDepartmentHeadApproval(): bool
    {
        // Logic to determine if document needs department head approval
        return in_array($this->documentType->name ?? '', [
            'study_compatibility',
            'application_minutes'
        ]);
    }

    /**
     * Check if document requires vice dean approval
     */
    public function requiresViceDeanApproval(): bool
    {
        // Logic to determine if document needs vice dean approval
        return in_array($this->documentType->name ?? '', [
            'approval_minutes',
            'nde',
            'pid'
        ]);
    }
}
