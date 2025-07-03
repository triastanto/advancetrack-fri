<?php

use App\Models\User;
use App\Models\Employee;

test('lecturer can access personal routes', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => 'lecturer'
    ]);

    $this->actingAs($user)
         ->get(route('personal-data'))
         ->assertStatus(200);

    $this->actingAs($user)
         ->get(route('documents.study-requirements'))
         ->assertStatus(200);
});

test('lecturer cannot access administrative routes', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => 'lecturer'
    ]);

    $this->actingAs($user)
         ->get(route('administrations.upload'))
         ->assertStatus(403);

    $this->actingAs($user)
         ->get(route('monitoring.analytics'))
         ->assertStatus(403);
});

test('hr staff can access administrative routes', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => 'hr_finance_staff'
    ]);

    $this->actingAs($user)
         ->get(route('administrations.upload'))
         ->assertStatus(200);

    $this->actingAs($user)
         ->get(route('monitoring.analytics'))
         ->assertStatus(200);
});

test('hr staff cannot access lecturer routes', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => 'hr_finance_staff'
    ]);

    $this->actingAs($user)
         ->get(route('personal-data'))
         ->assertStatus(403);

    $this->actingAs($user)
         ->get(route('documents.study-requirements'))
         ->assertStatus(403);
});

test('unauthenticated user redirected to login', function () {
    $this->get(route('personal-data'))
         ->assertRedirect(route('login'));

    $this->get(route('administrations.upload'))
         ->assertRedirect(route('login'));
});

test('user without employee data gets 403', function () {
    /** @var User $user */
    $user = User::factory()->create();
    // No employee record created

    $this->actingAs($user)
         ->get(route('personal-data'))
         ->assertStatus(403);

    $this->actingAs($user)
         ->get(route('administrations.upload'))
         ->assertStatus(403);
});

test('vice dean has full access', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => 'fri_vice_dean'
    ]);

    // Can access administrative routes
    $this->actingAs($user)
         ->get(route('administrations.upload'))
         ->assertStatus(200);

    $this->actingAs($user)
         ->get(route('monitoring.analytics'))
         ->assertStatus(200);

    // Cannot access lecturer-only routes
    $this->actingAs($user)
         ->get(route('personal-data'))
         ->assertStatus(403);
});
