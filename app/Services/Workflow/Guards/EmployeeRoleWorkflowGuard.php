<?php

namespace App\Services\Workflow\Guards;

use App\Contracts\Workflow\WorkflowGuardInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmployeeRoleWorkflowGuard implements WorkflowGuardInterface
{
    public function canTransition(Model $model, int $from, int $to, int $transition): bool
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return false;
        }

        $userRole = $user->employee->role;
        $requiredRoles = config("workflows.transitions.{$transition}.required_roles", []);

        if (empty($requiredRoles)) {
            return true;
        }

        // Check if user has the required employee role directly
        return in_array($userRole, $requiredRoles);
    }

    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return 'User tidak memiliki data karyawan.';
        }

        $userRole = $user->employee->role;
        $requiredRoles = config("workflows.transitions.{$transition}.required_roles", []);

        if (!empty($requiredRoles) && !in_array($userRole, $requiredRoles)) {
            return "Role '{$userRole}' tidak memiliki akses untuk melakukan transisi ini. Diperlukan salah satu role: " . implode(', ', $requiredRoles);
        }

        return null;
    }

    /**
     * Check if user owns the document (for ownership-based transitions)
     */
    protected function userOwnsDocument(Model $model): bool
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return false;
        }

        // Check if the document belongs to the current user's employee
        return $model->employee_id === $user->employee->id;
    }
}
