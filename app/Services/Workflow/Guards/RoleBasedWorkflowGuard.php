<?php

namespace App\Services\Workflow\Guards;

use App\Contracts\Workflow\WorkflowGuardInterface;
use App\Services\Workflow\WorkflowConfigService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoleBasedWorkflowGuard implements WorkflowGuardInterface
{
    protected array $rolePermissions = [];

    public function __construct(array $rolePermissions = [])
    {
        $this->rolePermissions = $rolePermissions;
    }

    public function canTransition(Model $model, int $from, int $to, int $transition): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Check if specific role permissions are set for this instance
        if (!empty($this->rolePermissions) && isset($this->rolePermissions[$transition])) {
            $allowedRoles = $this->rolePermissions[$transition];
        } else {
            // Fall back to config-based role permissions
            $allowedRoles = WorkflowConfigService::getRequiredRoles($transition);
        }

        if (empty($allowedRoles)) {
            return true; // If no specific role requirements, allow
        }

        // Check if user has any of the allowed roles
        if (method_exists($user, 'hasRole')) {
            foreach ($allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }
        }

        // Fallback to checking roles relationship if it exists
        if ($user->relationLoaded('roles') || method_exists($user, 'roles')) {
            $userRoles = $user->roles->pluck('name')->toArray();
            return !empty(array_intersect($allowedRoles, $userRoles));
        }

        return false;
    }

    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string
    {
        // Check if specific role permissions are set for this instance
        if (!empty($this->rolePermissions) && isset($this->rolePermissions[$transition])) {
            $allowedRoles = $this->rolePermissions[$transition];
        } else {
            // Fall back to config-based role permissions
            $allowedRoles = WorkflowConfigService::getRequiredRoles($transition);
        }

        if (!empty($allowedRoles)) {
            $rolesList = implode(', ', $allowedRoles);
            return "User must have one of these roles: {$rolesList}";
        }

        return 'Insufficient permissions for this transition';
    }

    public function setRolePermissions(array $rolePermissions): self
    {
        $this->rolePermissions = $rolePermissions;
        return $this;
    }

    public function addRolePermission(string|int $transition, array $roles): self
    {
        $this->rolePermissions[$transition] = $roles;
        return $this;
    }
}
