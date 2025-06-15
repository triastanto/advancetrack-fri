<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasEmployeeAuthentication
{
    /**
     * Get the authenticated employee
     * 
     * @throws \Exception
     */
    protected function getEmployee()
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception('User not authenticated.');
        }
        
        $employee = $user->employee;
        if (!$employee) {
            throw new \Exception('Employee data not found.');
        }
        
        return $employee;
    }

    /**
     * Get the authenticated user's employee safely (returns null if not found)
     */
    protected function getEmployeeSafely()
    {
        try {
            return $this->getEmployee();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if the current user has employee data
     */
    protected function hasEmployee(): bool
    {
        $user = Auth::user();
        return $user && $user->employee;
    }

    /**
     * Get the current user's employee role
     */
    protected function getEmployeeRole(): ?string
    {
        $employee = $this->getEmployeeSafely();
        return $employee ? $employee->role : null;
    }

    /**
     * Check if the current user has a specific role
     */
    protected function hasRole(string $role): bool
    {
        return $this->getEmployeeRole() === $role;
    }

    /**
     * Check if the current user has any of the specified roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        $userRole = $this->getEmployeeRole();
        return $userRole && in_array($userRole, $roles);
    }
}
