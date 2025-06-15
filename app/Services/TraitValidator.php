<?php

namespace App\Services;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class TraitValidator
{
    /**
     * Validate that a class can use HasWorkflowManagement trait
     */
    public static function validateWorkflowManagement(object $instance): void
    {
        if (!$instance instanceof Component) {
            throw new \InvalidArgumentException(
                'HasWorkflowManagement trait can only be used in Livewire Components. ' .
                'Class ' . static::class . ' must extend ' . Component::class
            );
        }

        $requiredMethods = ['addError', 'reset', 'validateOnly'];
        static::validateMethods($instance, $requiredMethods, 'HasWorkflowManagement');
    }

    /**
     * Validate that a class can use HasDocumentOperations trait
     */
    public static function validateDocumentOperations(object $instance): void
    {
        // This trait can be used in any class, but we should validate
        // that it's used in a context where it makes sense
        if (!$instance instanceof Component && !$instance instanceof Model) {
            throw new \InvalidArgumentException(
                'HasDocumentOperations trait should be used in Livewire Components or Eloquent Models. ' .
                'Consider if this is the right place for this trait.'
            );
        }
    }

    /**
     * Validate that a class can use HasEmployeeAuthentication trait
     */
    public static function validateEmployeeAuthentication(object $instance): void
    {
        // This trait can be used anywhere, but warn about context
        if (!$instance instanceof Component && !method_exists($instance, 'middleware')) {
            trigger_error(
                'HasEmployeeAuthentication trait is designed for use in Controllers or Livewire Components. ' .
                'Using it in ' . get_class($instance) . ' may not work as expected.',
                E_USER_WARNING
            );
        }
    }

    /**
     * Validate that an object has required methods
     */
    private static function validateMethods(object $instance, array $methods, string $traitName): void
    {
        $class = get_class($instance);
        
        foreach ($methods as $method) {
            if (!method_exists($instance, $method)) {
                throw new \BadMethodCallException(
                    "Method '{$method}' is required for {$traitName} trait. " .
                    "Ensure {$class} has this method available."
                );
            }
        }
    }

    /**
     * Validate that an object has required properties
     */
    private static function validateProperties(object $instance, array $properties, string $traitName): void
    {
        $class = get_class($instance);
        
        foreach ($properties as $property) {
            if (!property_exists($instance, $property)) {
                throw new \InvalidArgumentException(
                    "Property '{$property}' is required for {$traitName} trait. " .
                    "Ensure {$class} has this property defined."
                );
            }
        }
    }
}
