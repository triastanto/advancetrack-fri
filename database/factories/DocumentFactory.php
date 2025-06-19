<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        // Workflow states: 1=DRAFT, 2=PENDING, 3=VERIFIED, 4=REJECTED
        $workflowStates = [1, 2, 3, 4];

        // Get a random document type from the database, preferring predefined types
        $documentType = DocumentType::inRandomOrder()->first() ?? DocumentType::factory()->predefined()->create();

        $data = [
            'employee_id' => Employee::factory(),
            'document_type_id' => $documentType->id,
            'file_name' => $this->faker->lexify('document_????.pdf'),
            'file_path' => 'uploads/' . $this->faker->uuid . '.pdf',
            'workflow_state' => $this->faker->randomElement($workflowStates), // Use workflow states instead
            'created_at' => $this->faker->dateTimeThisYear(),
        ];

        // Add specific fields based on document type
        if (in_array($documentType->name, DocumentTypeConstants::getSemesterDocumentNames())) {
            $data['semester'] = $this->faker->numberBetween(1, 8);
            $data['year'] = $this->faker->year();
        } elseif (in_array($documentType->name, ['pid'])) {
            $data['upload_date'] = $this->faker->date();
        }

        return $data;
    }

    /**
     * Create a document with a specific document type from constants
     */
    public function withDocumentType(string $documentTypeName = null): self
    {
        return $this->state(function (array $attributes) use ($documentTypeName) {
            $documentType = null;

            if ($documentTypeName) {
                // Get specific document type from constants
                $documentTypeData = DocumentTypeConstants::getByName($documentTypeName);
                if (!$documentTypeData) {
                    throw new \InvalidArgumentException("Document type '{$documentTypeName}' not found in constants.");
                }

                $documentType = DocumentType::where('name', $documentTypeName)->first()
                    ?? DocumentType::factory()->ofType($documentTypeName)->create();
            } else {
                // Get random document type from constants
                $randomTypeName = $this->faker->randomElement(DocumentTypeConstants::getAllNames());
                $documentType = DocumentType::where('name', $randomTypeName)->first()
                    ?? DocumentType::factory()->ofType($randomTypeName)->create();
            }

            $stateData = [
                'document_type_id' => $documentType->id,
            ];

            // Add type-specific attributes based on document type name
            if (in_array($documentType->name, DocumentTypeConstants::getSemesterDocumentNames())) {
                $stateData['semester'] = $attributes['semester'] ?? $this->faker->numberBetween(1, 8);
                $stateData['year'] = $attributes['year'] ?? $this->faker->year();
            } elseif ($documentType->name === 'pid') {
                $stateData['upload_date'] = $attributes['upload_date'] ?? $this->faker->date();
            }

            return $stateData;
        });
    }

    /**
     * Create a document for a specific predefined document type category
     */
    public function forCategory(string $category): self
    {
        $categoryTypes = DocumentTypeConstants::getByCategory($category);
        if (empty($categoryTypes)) {
            throw new \InvalidArgumentException("Category '{$category}' not found or empty.");
        }

        $randomType = $this->faker->randomElement($categoryTypes);
        return $this->withDocumentType($randomType['name']);
    }

    /**
     * Create a document in DRAFT state (workflow_state = 1)
     */
    public function draft(): self
    {
        return $this->state(['workflow_state' => 1]);
    }

    /**
     * Create a document in PENDING state (workflow_state = 2)
     */
    public function pending(): self
    {
        return $this->state(['workflow_state' => 2]);
    }

    /**
     * Create a document in VERIFIED state (workflow_state = 3)
     */
    public function verified(): self
    {
        return $this->state(['workflow_state' => 3]);
    }

    /**
     * Create a document in REJECTED state (workflow_state = 4)
     */
    public function rejected(): self
    {
        return $this->state(['workflow_state' => 4]);
    }

    /**
     * Create a document with specific workflow state
     */
    public function inState(int $stateId): self
    {
        return $this->state(['workflow_state' => $stateId]);
    }
}
