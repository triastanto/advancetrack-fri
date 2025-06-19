<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lecturer_can_access_personal_routes()
    {
        /** @var User $user */
        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'role' => 'lecturer'
        ]);

        $this->actingAs($user)
             ->get(route('profile.index'))
             ->assertStatus(200);

        $this->actingAs($user)
             ->get(route('documents.study-requirements'))
             ->assertStatus(200);
    }

    /** @test */
    public function lecturer_cannot_access_administrative_routes()
    {
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
             ->get(route('reports.index'))
             ->assertStatus(403);
    }

    /** @test */
    public function hr_staff_can_access_administrative_routes()
    {
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
             ->get(route('reports.index'))
             ->assertStatus(200);
    }

    /** @test */
    public function hr_staff_cannot_access_lecturer_routes()
    {
        /** @var User $user */
        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'role' => 'hr_finance_staff'
        ]);

        $this->actingAs($user)
             ->get(route('profile.index'))
             ->assertStatus(403);

        $this->actingAs($user)
             ->get(route('documents.study-requirements'))
             ->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login()
    {
        $this->get(route('profile.index'))
             ->assertRedirect(route('login'));

        $this->get(route('administrations.upload'))
             ->assertRedirect(route('login'));
    }

    /** @test */
    public function user_without_employee_data_gets_403()
    {
        /** @var User $user */
        $user = User::factory()->create();
        // No employee record created

        $this->actingAs($user)
             ->get(route('profile.index'))
             ->assertStatus(403);

        $this->actingAs($user)
             ->get(route('administrations.upload'))
             ->assertStatus(403);
    }

    /** @test */
    public function vice_dean_has_full_access()
    {
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
             ->get(route('reports.index'))
             ->assertStatus(200);

        // Cannot access lecturer-only routes
        $this->actingAs($user)
             ->get(route('profile.index'))
             ->assertStatus(403);
    }
}
