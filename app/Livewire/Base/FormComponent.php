<?php

namespace App\Livewire\Base;

use App\Traits\HasFormValidation;
use Livewire\Component;

abstract class FormComponent extends Component
{
    use HasFormValidation;

    /**
     * Form data
     */
    public $formData = [];

    /**
     * Form validation rules
     */
    protected $rules = [];

    /**
     * Custom validation messages
     */
    protected $messages = [];

    /**
     * Form submission status
     */
    public $isSubmitting = false;

    /**
     * Success message
     */
    public $successMessage = '';

    /**
     * Initialize form component
     */
    public function mount($initialData = [])
    {
        $this->formData = $initialData;
        $this->initializeForm();
    }

    /**
     * Initialize form - override in child classes
     */
    protected function initializeForm(): void
    {
        // Override in child classes
    }

    /**
     * Submit form
     */
    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->clearErrors();

        try {
            $this->validate();
            $this->processForm();
            $this->showSuccessMessage();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Livewire
        } catch (\Exception $e) {
            $this->addCustomError('form', 'An error occurred: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Process form data - override in child classes
     */
    abstract protected function processForm(): void;

    /**
     * Show success message
     */
    protected function showSuccessMessage(): void
    {
        $this->successMessage = $this->getSuccessMessage();
        $this->dispatch('formSubmitted', $this->formData);
    }

    /**
     * Get success message - override in child classes
     */
    protected function getSuccessMessage(): string
    {
        return 'Form submitted successfully!';
    }

    /**
     * Reset form
     */
    public function resetForm(): void
    {
        parent::resetForm();
        $this->successMessage = '';
        $this->isSubmitting = false;
    }

    /**
     * Check if form is valid
     */
    public function isFormValid(): bool
    {
        return !$this->getErrorBag()->any();
    }

    /**
     * Get form data
     */
    public function getFormData($key = null)
    {
        if ($key === null) {
            return $this->formData;
        }

        return $this->formData[$key] ?? null;
    }

    /**
     * Set form data
     */
    public function setFormData($key, $value): void
    {
        $this->formData[$key] = $value;
    }
} 