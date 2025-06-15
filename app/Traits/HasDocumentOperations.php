<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

trait HasDocumentOperations
{
    /**
     * Validate that the document parameter is a valid model
     */
    private function validateDocumentModel($document): void
    {
        if (!$document instanceof Model) {
            throw new \InvalidArgumentException(
                'Document parameter must be an Eloquent Model instance. ' .
                'Received: ' . gettype($document)
            );
        }

        // Check for required properties
        if (!property_exists($document, 'file_path') && !$document->hasAttribute('file_path')) {
            throw new \InvalidArgumentException(
                'Document model must have a file_path attribute. ' .
                'Model: ' . get_class($document)
            );
        }
    }

    /**
     * Delete a document and its file
     */
    protected function deleteDocumentWithFile($document): bool
    {
        $this->validateDocumentModel($document);

        try {
            // Delete the physical file if it exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the database record
            $document->delete();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get document file URL
     */
    protected function getDocumentUrl($document): ?string
    {
        $this->validateDocumentModel($document);

        if (!$document->file_path) {
            return null;
        }

        return asset('storage/' . $document->file_path);
    }

    /**
     * Check if document file exists
     */
    protected function documentFileExists($document): bool
    {
        $this->validateDocumentModel($document);
        
        return $document->file_path && Storage::disk('public')->exists($document->file_path);
    }

    /**
     * Get document file size in human readable format
     */
    protected function getDocumentSize($document): ?string
    {
        $this->validateDocumentModel($document);
        if (!$this->documentFileExists($document)) {
            return null;
        }

        $bytes = Storage::disk('public')->size($document->file_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Validate file upload
     */
    protected function validateDocumentFile($file, array $options = []): array
    {
        $maxSize = $options['max_size'] ?? 10240; // 10MB default
        $allowedMimes = $options['mimes'] ?? ['pdf'];
        $errors = [];

        if (!$file) {
            $errors[] = 'File dokumen wajib dipilih.';
            return $errors;
        }

        // Check file type
        if (!in_array($file->getClientOriginalExtension(), $allowedMimes)) {
            $mimesList = implode(', ', array_map('strtoupper', $allowedMimes));
            $errors[] = "File harus berformat: {$mimesList}.";
        }

        // Check file size (in KB)
        if ($file->getSize() / 1024 > $maxSize) {
            $maxSizeMB = $maxSize / 1024;
            $errors[] = "Ukuran file maksimal {$maxSizeMB}MB.";
        }

        return $errors;
    }

    /**
     * Generate unique file path for document storage
     */
    protected function generateDocumentPath($employeeId, $documentType = 'documents'): string
    {
        $timestamp = now()->format('Y/m/d');
        return "{$documentType}/{$employeeId}/{$timestamp}";
    }
}
