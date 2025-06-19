<?php

namespace App\Traits;

trait HasModal
{
    /**
     * Modal state
     */
    public $isModalOpen = false;
    
    /**
     * Modal data
     */
    public $modalData = [];
    
    /**
     * Open modal with optional data
     */
    public function openModal($data = []): void
    {
        $this->modalData = $data;
        $this->isModalOpen = true;
    }
    
    /**
     * Close modal and reset data
     */
    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->modalData = [];
    }
    
    /**
     * Check if modal is open
     */
    public function isModalOpen(): bool
    {
        return $this->isModalOpen;
    }
    
    /**
     * Get modal data
     */
    public function getModalData($key = null)
    {
        if ($key === null) {
            return $this->modalData;
        }
        
        return $this->modalData[$key] ?? null;
    }
    
    /**
     * Set modal data
     */
    public function setModalData($key, $value): void
    {
        $this->modalData[$key] = $value;
    }
} 