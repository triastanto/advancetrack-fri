<?php

namespace Database\Factories;

use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->slug(2),
            'display_name' => $this->faker->words(2, true),
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Define specific document types that match the DocumentTypeConstants
     */
    public function predefined(): self
    {
        return $this->state(function (array $attributes) {
            return $this->faker->randomElement(DocumentTypeConstants::DOCUMENT_TYPES);
        });
    }

    /**
     * Create a specific document type by name
     */
    public function ofType(string $typeName): self
    {
        return $this->state(function (array $attributes) use ($typeName) {
            $documentType = DocumentTypeConstants::getByName($typeName);

            if (!$documentType) {
                throw new \InvalidArgumentException("Document type '{$typeName}' not found in constants.");
            }

            return $documentType;
        });
    }

    /**
     * Create document types for a specific category
     */
    public function forCategory(string $category): self
    {
        return $this->state(function (array $attributes) use ($category) {
            $types = DocumentTypeConstants::getByCategory($category);

            if (empty($types)) {
                throw new \InvalidArgumentException("Category '{$category}' not found or empty.");
            }

            return $this->faker->randomElement($types);
        });
    }
}
