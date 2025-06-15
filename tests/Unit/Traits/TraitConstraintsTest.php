<?php

namespace Tests\Unit\Traits;

use App\Traits\HasWorkflowManagement;
use App\Traits\HasDocumentOperations;
use App\Services\TraitValidator;
use Tests\TestCase;
use Livewire\Component;

class TraitConstraintsTest extends TestCase
{
    /** @test */
    public function workflow_management_trait_requires_livewire_component()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('HasWorkflowManagement trait can only be used in Livewire Components');
        
        // Create a class that uses the trait but is not a Livewire component
        $invalidClass = new class {
            use HasWorkflowManagement;
        };
        
        // This should throw an exception
        $invalidClass->initializeHasWorkflowManagement();
    }

    /** @test */
    public function document_operations_validates_model_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Document parameter must be an Eloquent Model instance');
        
        $classWithTrait = new class {
            use HasDocumentOperations;
            
            public function testMethod() {
                // This should fail because we're passing an array instead of a model
                $this->deleteDocumentWithFile(['invalid' => 'data']);
            }
        };
        
        $classWithTrait->testMethod();
    }

    /** @test */
    public function trait_validator_correctly_identifies_valid_component()
    {
        // Create a valid Livewire component
        $validComponent = new class extends Component {
            use HasWorkflowManagement;
        };
        
        // This should not throw an exception
        TraitValidator::validateWorkflowManagement($validComponent);
        
        // If we reach here, validation passed
        $this->assertTrue(true);
    }

    /** @test */
    public function trait_validator_rejects_invalid_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $invalidClass = new class {
            // Not a Livewire component
        };
        
        TraitValidator::validateWorkflowManagement($invalidClass);
    }
}
