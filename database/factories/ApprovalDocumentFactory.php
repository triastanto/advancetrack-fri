<?php

namespace Database\Factories;

use App\Models\ApprovalDocument;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalDocumentFactory extends Factory
{
    protected $model = ApprovalDocument::class;

    public function definition(): array
    {
        // Workflow states: 1=DRAFT, 2=PENDING, 3=VERIFIED, 4=REJECTED
        $workflowStates = [1, 2, 3, 4];

        // Get valid approval document type names
        $documentTypeNames = DocumentTypeConstants::getApprovalDocumentNames();

        // Find a document type or create one with a valid name for approval documents
        $documentType = DocumentType::whereIn('name', $documentTypeNames)->inRandomOrder()->first();

        if (!$documentType) {
            $randomTypeName = $this->faker->randomElement($documentTypeNames);
            $documentType = DocumentType::factory()->create(['name' => $randomTypeName]);
        }

        $data = [
            'employee_id' => Employee::factory(),
            'document_type_id' => $documentType->id,
            'file_name' => $this->faker->lexify('approval_doc_????.pdf'),
            'file_path' => 'uploads/approval/' . $this->faker->uuid . '.pdf',
            'workflow_state' => $this->faker->randomElement($workflowStates),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];

        // Add specific fields based on document type
        if (in_array($documentType->name, ['pid'])) {
            $data['upload_date'] = $this->faker->date();
        }

        return $data;
    }

    /**
     * Create document in draft state
     */
    public function draft(): self
    {
        return $this->state(function () {
            return [
                'workflow_state' => 1, // DRAFT
            ];
        });
    }

    /**
     * Create document in pending state
     */
    public function pending(): self
    {
        return $this->state(function () {
            return [
                'workflow_state' => 2, // PENDING
            ];
        });
    }

    /**
     * Create document in verified state
     */
    public function verified(): self
    {
        return $this->state(function () {
            return [
                'workflow_state' => 3, // VERIFIED
            ];
        });
    }

    /**
     * Create document in rejected state
     */
    public function rejected(): self
    {
        return $this->state(function () {
            return [
                'workflow_state' => 4, // REJECTED
            ];
        });
    }

    /**
     * Create a document with a specific document type
     */
    public function withDocumentType(string $documentTypeName): self
    {
        return $this->state(function () use ($documentTypeName) {
            $documentType = DocumentType::where('name', $documentTypeName)->first();

            if (!$documentType) {
                $documentType = DocumentType::factory()->create(['name' => $documentTypeName]);
            }

            return [
                'document_type_id' => $documentType->id,
            ];
        });
    }
}
