<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RequiredTraitConstraint
{
    public function __construct(
        public string $requiredParentClass,
        public array $requiredMethods = [],
        public array $requiredProperties = [],
        public string $message = ''
    ) {}

    public function validate(object $instance): void
    {
        $class = get_class($instance);

        // Check parent class constraint
        if (!$instance instanceof $this->requiredParentClass) {
            throw new \InvalidArgumentException(
                $this->message ?: 
                "Class {$class} must extend {$this->requiredParentClass} to use this trait"
            );
        }

        // Check required methods
        foreach ($this->requiredMethods as $method) {
            if (!method_exists($instance, $method)) {
                throw new \BadMethodCallException(
                    "Class {$class} must implement method '{$method}'"
                );
            }
        }

        // Check required properties
        foreach ($this->requiredProperties as $property) {
            if (!property_exists($instance, $property)) {
                throw new \InvalidArgumentException(
                    "Class {$class} must have property '{$property}'"
                );
            }
        }
    }
}
