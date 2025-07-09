<?php

use App\Livewire\StudyCalendar\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user with employee
    $this->user = createUser();
    $this->employee = createEmployee([
        'user_id' => $this->user->id,
        'role' => 'lecturer'
    ]);
});

describe('StudyCalendar Create Validation', function () {

    test('validation errors on step 1 redirect to step 1', function () {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('submit')
            ->assertHasErrors(['start_date', 'end_date'])
            ->assertSet('step', 1); // Should redirect to step 1
    });

    test('validation errors on step 2 redirect to step 2', function () {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '2024-01-01') // Valid date
            ->set('end_date', '2024-12-31') // Valid date
            ->set('university_name', '') // Invalid - required
            ->set('university_address', '') // Invalid - required
            ->set('study_program_name', '') // Invalid - required
            ->set('study_level', '') // Invalid - required
            ->set('funding_source', '') // Invalid - required
            ->set('study_address', '') // Invalid - required
            ->call('submit')
            ->assertHasErrors(['university_name', 'university_address', 'study_program_name', 'study_level', 'funding_source', 'study_address'])
            ->assertSet('step', 2); // Should redirect to step 2
    });

    test('validation errors on step 3 stay on step 3', function () {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '2024-01-01') // Valid date
            ->set('end_date', '2024-12-31') // Valid date
            ->set('university_name', 'Test University') // Valid
            ->set('university_address', 'Test Address') // Valid
            ->set('study_program_name', 'Test Program') // Valid
            ->set('study_level', 'S3') // Valid
            ->set('funding_source', 'LPDP') // Valid
            ->set('study_address', 'Test Study Address') // Valid
            ->set('agreed', false) // Invalid - must be true
            ->call('submit')
            ->assertHasErrors(['agreed'])
            ->assertSet('step', 3); // Should stay on step 3
    });

    test('progress bar shows error state for step with errors', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('submit');

        // Access the method directly on the component instance
        $progressData = $component->instance()->getProgressBarData();
        
        // Step 1 should have error
        expect($progressData['phases'][0]['has_error'])->toBe(true);
        // Step 2 should not have error
        expect($progressData['phases'][1]['has_error'])->toBe(false);
        // Step 3 should not have error
        expect($progressData['phases'][2]['has_error'])->toBe(false);
    });

    test('goToErrorStep method navigates to step with errors', function () {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('submit')
            ->assertSet('step', 1) // Should be redirected to step 1
            ->set('step', 3) // Manually go back to step 3
            ->call('goToErrorStep')
            ->assertSet('step', 1); // Should go back to step 1
    });

    test('hasErrorsOnOtherSteps returns true when there are errors on other steps', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('submit');

        expect($component->instance()->hasErrorsOnOtherSteps())->toBe(true);
    });

    test('hasErrorsOnOtherSteps returns false when there are no errors', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        expect($component->instance()->hasErrorsOnOtherSteps())->toBe(false);
    });

    test('getErrorStep returns correct step number', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('step', 3) // Move to step 3
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('submit');

        expect($component->instance()->getErrorStep())->toBe(1);
    });

    test('getErrorStep returns null when there are no errors', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        expect($component->instance()->getErrorStep())->toBe(null);
    });

    test('validation errors are cleared when navigating between steps', function () {
        $component = Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('start_date', '') // Invalid date
            ->set('end_date', '') // Invalid date
            ->call('nextStep')
            ->assertHasErrors(['start_date', 'end_date'])
            ->call('previousStep')
            ->assertHasNoErrors(); // Errors should be cleared
    });

    test('form submission with valid data succeeds', function () {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('start_date', '2024-01-01')
            ->set('end_date', '2024-12-31')
            ->set('university_name', 'Test University')
            ->set('university_address', 'Test Address')
            ->set('study_program_name', 'Test Program')
            ->set('study_level', 'S3')
            ->set('funding_source', 'LPDP')
            ->set('study_address', 'Test Study Address')
            ->set('agreed', true)
            ->call('submit')
            ->assertHasNoErrors();
    });
}); 