<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Role-based gates
        Gate::define('lecturer-access', function (User $user) {
            return $user->employee && $user->employee->role === 'lecturer';
        });

        Gate::define('administrative-access', function (User $user) {
            if (!$user->employee) {
                return false;
            }
            
            $administrativeRoles = [
                'hr_finance_staff',
                'head_of_hr_finance',
                'fri_vice_dean',
                'head_of_study_program',
                'head_of_research_group'
            ];
            
            return in_array($user->employee->role, $administrativeRoles);
        });

        // Specific role gates
        Gate::define('hr-access', function (User $user) {
            return $user->employee && in_array($user->employee->role, [
                'hr_finance_staff',
                'head_of_hr_finance'
            ]);
        });

        Gate::define('leadership-access', function (User $user) {
            return $user->employee && in_array($user->employee->role, [
                'fri_vice_dean',
                'head_of_study_program',
                'head_of_research_group'
            ]);
        });

        Gate::define('high-level-access', function (User $user) {
            return $user->employee && in_array($user->employee->role, [
                'fri_vice_dean',
                'head_of_hr_finance'
            ]);
        });

        // Document management gates
        Gate::define('manage-documents', function (User $user) {
            return $user->employee && in_array($user->employee->role, [
                'hr_finance_staff',
                'head_of_hr_finance',
                'fri_vice_dean'
            ]);
        });

        Gate::define('view-reports', function (User $user) {
            return $user->employee && in_array($user->employee->role, [
                'hr_finance_staff',
                'head_of_hr_finance',
                'fri_vice_dean',
                'head_of_study_program',
                'head_of_research_group'
            ]);
        });

        // Personal data access
        Gate::define('access-personal-data', function (User $user) {
            return $user->employee && $user->employee->role === 'lecturer';
        });
    }
}
