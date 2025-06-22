<?php

namespace App\Models;

use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicDocument extends Document
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\AcademicDocumentFactory::new();
    }
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Apply the academic document type restriction
        static::addGlobalScope('academic_document', function ($query) {
            $validTypeIds = DocumentType::whereIn('name', array_merge(
                DocumentTypeConstants::getStudyRequirementNames(),
                DocumentTypeConstants::getSemesterDocumentNames(),
                DocumentTypeConstants::getFinalDocumentNames()
            ))->pluck('id')->toArray();

            $query->whereIn('document_type_id', $validTypeIds);
        });
    }

    /**
     * Get allowed document types for this document model
     */
    public static function getAllowedTypes(): array
    {
        return array_merge(
            DocumentTypeConstants::getStudyRequirementNames(),
            DocumentTypeConstants::getSemesterDocumentNames(),
            DocumentTypeConstants::getFinalDocumentNames()
        );
    }

    /**
     * Get the workflow name for this document.
     */
    public function getWorkflowName(): string
    {
        return 'verification_by_staff';
    }

    /**
     * Scope a query to only include documents of specific types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTypes($query, array $types)
    {
        $typeIds = DocumentType::whereIn('name', $types)->pluck('id')->toArray();
        return $query->whereIn('document_type_id', $typeIds);
    }

    /**
     * Query scope to get all study requirements documents
     */
    public function scopeStudyRequirements($query)
    {
        return $this->scopeOfTypes($query, DocumentTypeConstants::getStudyRequirementNames());
    }

    /**
     * Query scope to get all semester reports
     */
    public function scopeSemesterReports($query)
    {
        return $this->scopeOfTypes($query, DocumentTypeConstants::getSemesterDocumentNames());
    }

    /**
     * Query scope to get all final reports
     */
    public function scopeFinalReports($query)
    {
        return $this->scopeOfTypes($query, DocumentTypeConstants::getFinalDocumentNames());
    }
}