<?php

namespace App\Traits;

trait HasFormValidation
{
    /**
     * Validate a specific field
     */
    public function validateField($field): void
    {
        $this->validateOnly($field);
    }
    
    /**
     * Clear all validation errors
     */
    public function clearErrors(): void
    {
        $this->resetValidation();
    }
    
    /**
     * Add a custom error message
     */
    public function addCustomError($field, $message): void
    {
        $this->addError($field, $message);
    }
    
    /**
     * Check if a field has errors
     */
    public function hasError($field): bool
    {
        return $this->getErrorBag()->has($field);
    }
    
    /**
     * Get error message for a field
     */
    public function getErrorMessage($field): ?string
    {
        return $this->getErrorBag()->first($field);
    }
    
    /**
     * Reset form and validation
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->clearErrors();
    }
} 