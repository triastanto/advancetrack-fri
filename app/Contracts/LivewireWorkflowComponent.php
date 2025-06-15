<?php

namespace App\Contracts;

interface LivewireWorkflowComponent
{
    /**
     * Add validation error to a property
     */
    public function addError(string $key, string $message): void;

    /**
     * Reset component properties
     */
    public function reset(...$properties): void;

    /**
     * Validate only specific properties
     */
    public function validateOnly(string $field): void;
}
