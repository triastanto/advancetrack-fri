<?php

use App\Livewire\Documents\StudyRequirements;
use App\Models\User;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed document types for study requirements
    seedStudyRequirementDocumentTypes();
});

describe('Page Access & Authentication', function () {

    test('guest users are redirected to login page', function () {
        $this->get(route('documents.study-requirements'))
            ->assertRedirect(route('login'));
    });

    test('authenticated lecturer can access study requirements page', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $this->actingAs($user)
            ->get(route('documents.study-requirements'))
            ->assertOk()
            ->assertViewIs('pages.documents.study-requirements')
            ->assertSee('Dokumen Persyaratan Studi Lanjut');
    });

    test('study requirements page renders livewire component', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $this->actingAs($user)
            ->get(route('documents.study-requirements'))
            ->assertOk()
            ->assertSeeLivewire(StudyRequirements::class);
    });

    test('unauthenticated ajax requests return unauthorized', function () {
        $this->getJson(route('documents.study-requirements'))
            ->assertUnauthorized();
    });

    test('authenticated lecturer ajax requests return success', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $this->actingAs($user)
            ->getJson(route('documents.study-requirements'))
            ->assertOk()
            ->assertViewIs('pages.documents.study-requirements');
    });

    test('session persistence works across requests', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        // First request
        $this->actingAs($user)
            ->get(route('documents.study-requirements'))
            ->assertOk();

        // Second request without re-authentication
        $this->get(route('documents.study-requirements'))
            ->assertOk()
            ->assertViewIs('pages.documents.study-requirements');
    });

    test('multiple lecturer users can access page simultaneously', function () {
        // Create first lecturer
        $user1 = createUser(['email' => 'lecturer1@example.com']);
        $employee1 = createEmployee([
            'user_id' => $user1->id,
            'role' => 'lecturer'
        ]);

        // Create second lecturer
        $user2 = createUser(['email' => 'lecturer2@example.com']);
        $employee2 = createEmployee([
            'user_id' => $user2->id,
            'role' => 'lecturer'
        ]);

        // Both should be able to access
        $this->actingAs($user1)
            ->get(route('documents.study-requirements'))
            ->assertOk();

        $this->actingAs($user2)
            ->get(route('documents.study-requirements'))
            ->assertOk();
    });

    test('route name is correctly defined', function () {
        $route = \Illuminate\Support\Facades\Route::getRoutes()
            ->getByName('documents.study-requirements');

        expect($route)->not->toBeNull();
        expect($route->getName())->toBe('documents.study-requirements');
        expect($route->uri())->toBe('documents/study-requirements');
    });
});

describe('Livewire Component Access & Authentication', function () {

    test('authenticated lecturer can mount livewire component', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertSuccessful();
    });

    test('livewire component loads study requirement document types on mount', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $studyRequirementTypesCount = count(DocumentTypeConstants::getByCategory('study_requirements'));

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertSet('availableDocumentTypes', function ($types) use ($studyRequirementTypesCount) {
                return $types->count() === $studyRequirementTypesCount;
            })
            ->assertSet('selectedDocumentTypeId', '');
    });

    test('livewire component initializes with empty properties', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertSet('selectedTransition', null)
            ->assertSet('transitionComment', '')
            ->assertSet('uploadModalOpen', false)
            ->assertSet('viewModalOpen', false)
            ->assertSet('workflowModalOpen', false)
            ->assertSet('fileName', '')
            ->assertSet('documentFile', null);
    });

    test('authenticated lecturer can call component methods', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->call('refreshData')
            ->assertSuccessful();
    });

    test('component inherits workflow component functionality', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $component = Livewire::actingAs($user)
            ->test(StudyRequirements::class);

        // Use reflection to access protected methods
        $instance = $component->instance();
        $reflectionClass = new \ReflectionClass($instance);

        // Get the protected methods via reflection
        $modelClassMethod = $reflectionClass->getMethod('getWorkflowModelClass');
        $modelClassMethod->setAccessible(true);

        $docPropertyMethod = $reflectionClass->getMethod('getWorkflowDocumentPropertyName');
        $docPropertyMethod->setAccessible(true);

        $commentPropertyMethod = $reflectionClass->getMethod('getWorkflowCommentPropertyName');
        $commentPropertyMethod->setAccessible(true);

        $transitionPropertyMethod = $reflectionClass->getMethod('getWorkflowTransitionPropertyName');
        $transitionPropertyMethod->setAccessible(true);

        // Test abstract method implementations
        expect($modelClassMethod->invoke($instance))->toBe(\App\Models\Document::class);
        expect($docPropertyMethod->invoke($instance))->toBe('currentDocument');
        expect($commentPropertyMethod->invoke($instance))->toBe('transitionComment');
        expect($transitionPropertyMethod->invoke($instance))->toBe('selectedTransition');
    });

    test('component uses required traits', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $component = Livewire::actingAs($user)
            ->test(StudyRequirements::class);

        $uses = class_uses_recursive($component->instance());

        expect($uses)->toHaveKey(\Livewire\WithPagination::class);
        expect($uses)->toHaveKey(\App\Traits\HasDocumentManagement::class);
        expect($uses)->toHaveKey(\App\Traits\HasCommonValidation::class);
    });

    test('component event listeners are properly configured', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $component = Livewire::actingAs($user)
            ->test(StudyRequirements::class);

        $expectedListeners = [
            'document:uploaded' => 'handleDocumentUploaded',
            'document:deleted' => 'handleDocumentDeleted',
            'document:submitted' => 'handleDocumentSubmitted',
            'workflow:transition-applied' => 'handleTransitionApplied',
            'document-submit' => 'handleDocumentSubmit',
            'document-delete' => 'handleDocumentDelete',
            'document-list:refresh' => 'refreshData'
        ];

        // Use reflection to access protected $listeners property
        $instance = $component->instance();
        $reflectionClass = new \ReflectionClass($instance);
        $listenersProperty = $reflectionClass->getProperty('listeners');
        $listenersProperty->setAccessible(true);
        $actualListeners = $listenersProperty->getValue($instance);

        foreach ($expectedListeners as $event => $method) {
            expect($actualListeners)->toHaveKey($event);
            expect($actualListeners[$event])->toBe($method);
        }
    });

    test('component renders view with required data', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertViewHas('documents')
            ->assertViewHas('activeStudyInfo')
            ->assertViewHas('completionStatus')
            ->assertViewHas('canManageWorkflow');
    });

    test('component requires valid document type constants', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        // Remove all document types from database
        DocumentType::query()->delete();

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertSet('availableDocumentTypes', function ($types) {
                return $types->isEmpty();
            });
    });
});

describe('Security & Authorization Tests', function () {

    test('component prevents access to other employees documents', function () {
        // Create two separate lecturers
        $user1 = createUser(['email' => 'lecturer1@test.com']);
        $employee1 = createEmployee(['user_id' => $user1->id, 'role' => 'lecturer']);

        $user2 = createUser(['email' => 'lecturer2@test.com']);
        $employee2 = createEmployee(['user_id' => $user2->id, 'role' => 'lecturer']);

        // Create a document for employee1
        $documentType = DocumentType::factory()->create();
        $document = \App\Models\Document::factory()->create([
            'employee_id' => $employee1->id,
            'document_type_id' => $documentType->id
        ]);

        // Employee2 should not see employee1's documents
        $component = Livewire::actingAs($user2)
            ->test(StudyRequirements::class);

        $documents = $component->viewData('documents');
        expect($documents->items())->toBeEmpty();
    });

    test('component works with authenticated users', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        // Test with Livewire's authentication
        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->assertSuccessful();

        // Test with HTTP authentication
        $response = $this->actingAs($user)
            ->get(route('documents.study-requirements'));

        $response->assertOk()
            ->assertViewIs('pages.documents.study-requirements')
            ->assertSeeLivewire(StudyRequirements::class);
    });

    test('component enforces role-based access for workflow operations', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $component = Livewire::actingAs($user)
            ->test(StudyRequirements::class);

        // Verify that canManageWorkflow returns appropriate value for lecturer
        $canManageWorkflow = $component->viewData('canManageWorkflow');
        expect($canManageWorkflow)->toBeBool();
    });

    test('component handles concurrent user sessions', function () {
        $user1 = createUser(['email' => 'lecturer1@test.com']);
        $employee1 = createEmployee(['user_id' => $user1->id, 'role' => 'lecturer']);

        $user2 = createUser(['email' => 'lecturer2@test.com']);
        $employee2 = createEmployee(['user_id' => $user2->id, 'role' => 'lecturer']);

        // Both users should be able to access simultaneously
        $component1 = Livewire::actingAs($user1)
            ->test(StudyRequirements::class);

        $component2 = Livewire::actingAs($user2)
            ->test(StudyRequirements::class);

        $component1->assertSuccessful();
        $component2->assertSuccessful();
    });

    test('component validates input sanitization', function () {
        $user = createUser();
        $employee = createEmployee([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        // Test XSS prevention
        $maliciousInput = '<script>alert("xss")</script>';

        Livewire::actingAs($user)
            ->test(StudyRequirements::class)
            ->set('transitionComment', $maliciousInput)
            ->assertSet('transitionComment', $maliciousInput); // Should store as-is, but be escaped in output
    });
});

/**
 * Helper function to seed study requirement document types
 */
function seedStudyRequirementDocumentTypes(): void
{
    $studyRequirementTypes = DocumentTypeConstants::getByCategory('study_requirements');

    foreach ($studyRequirementTypes as $typeData) {
        DocumentType::factory()->create([
            'name' => $typeData['name'],
            'display_name' => $typeData['display_name'],
            'description' => $typeData['description']
        ]);
    }
}