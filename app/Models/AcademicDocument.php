<?php

namespace App\Models;

use App\Constants\DocumentTypeConstants;

class AcademicDocument extends Document
{

    protected static function booted()
    {
        static::addGlobalScope('academic_category', function ($query) {
            $query->whereHas('documentType', function ($subQuery) {
                $subQuery->whereIn('name', self::getAllowedTypes());
            });
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            // Check if the document type is valid for AcademicDocument
            if ($model->documentType && !in_array($model->documentType->name, self::getAllowedTypes())) {
                throw new \InvalidArgumentException('Invalid document type for AcademicDocument.');
            }
        });
    }

    public static function getAllowedTypes()
    {
        return array_merge(
            DocumentTypeConstants::getStudyRequirementNames(),
            DocumentTypeConstants::getSemesterDocumentNames(),
            DocumentTypeConstants::getFinalDocumentNames()
        );
    }

    public function getWorkflowName(): string
    {
        return 'verification_by_staff';
    }

    /**
     * Create a new AcademicDocument with a document type by name
     */
    public static function createWithDocumentType(string $documentTypeName, array $attributes = []): self
    {
        $documentType = DocumentType::where('name', $documentTypeName)->first();

        if (!$documentType) {
            throw new \InvalidArgumentException("Document type '{$documentTypeName}' not found.");
        }

        if (!in_array($documentTypeName, self::getAllowedTypes())) {
            throw new \InvalidArgumentException("Document type '{$documentTypeName}' is not allowed for AcademicDocument.");
        }

        $attributes['document_type_id'] = $documentType->id;

        return self::create($attributes);
    }

    /**
     * Scope to filter by document type name
     */
    public function scopeByDocumentTypeName($query, string $documentTypeName)
    {
        return $query->whereHas('documentType', function ($subQuery) use ($documentTypeName) {
            $subQuery->where('name', $documentTypeName);
        });
    }

    /**
     * Scope to filter by document category
     */
    public function scopeByCategory($query, string $category)
    {
        $typeNames = match($category) {
            'study_requirements' => DocumentTypeConstants::getStudyRequirementNames(),
            'semester_documents' => DocumentTypeConstants::getSemesterDocumentNames(),
            'final_documents' => DocumentTypeConstants::getFinalDocumentNames(),
            default => []
        };

        if (empty($typeNames)) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query->whereHas('documentType', function ($subQuery) use ($typeNames) {
            $subQuery->whereIn('name', $typeNames);
        });
    }

    /**
     * Get the category of this academic document
     */
    public function getCategory(): ?string
    {
        if (!$this->documentType) {
            return null;
        }

        $typeName = $this->documentType->name;

        if (in_array($typeName, DocumentTypeConstants::getStudyRequirementNames())) {
            return 'study_requirements';
        }

        if (in_array($typeName, DocumentTypeConstants::getSemesterDocumentNames())) {
            return 'semester_documents';
        }

        if (in_array($typeName, DocumentTypeConstants::getFinalDocumentNames())) {
            return 'final_documents';
        }

        return null;
    }
}