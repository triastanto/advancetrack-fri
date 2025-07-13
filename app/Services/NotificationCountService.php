<?php

namespace App\Services;

use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Constants\DocumentTypeConstants;
use Illuminate\Support\Facades\Auth;

class NotificationCountService
{
    /**
     * Get pending approval count for the current user's role
     */
    public static function getPendingApprovalCount(): int
    {
        $user = Auth::user();
        if (!$user || !$user->employee) {
            return 0;
        }

        $role = $user->employee->role;

        // Get approval document type IDs
        $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
        $approvalTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

        $query = ApprovalDocument::whereIn('document_type_id', $approvalTypeIds);

        // Filter by role-specific pending states
        switch ($role) {
            case 'head_of_study_program':
                // Pending Level 1 approval (state 2)
                return $query->where('workflow_state', 2)->count();

            case 'head_of_research_group':
                // Pending Level 2 approval (state 3)
                return $query->where('workflow_state', 3)->count();

            case 'fri_vice_dean':
                // Pending Level 1 or Level 2 approval (states 2 and 3)
                return $query->whereIn('workflow_state', [2, 3])->count();

            default:
                return 0;
        }
    }

    /**
     * Get pending approval count for a specific role
     */
    public static function getPendingApprovalCountForRole(string $role): int
    {
        // Get approval document type IDs
        $approvalTypeNames = DocumentTypeConstants::getApprovalDocumentNames();
        $approvalTypeIds = DocumentType::whereIn('name', $approvalTypeNames)->pluck('id');

        $query = ApprovalDocument::whereIn('document_type_id', $approvalTypeIds);

        // Filter by role-specific pending states
        switch ($role) {
            case 'head_of_study_program':
                // Pending Level 1 approval (state 2)
                return $query->where('workflow_state', 2)->count();

            case 'head_of_research_group':
                // Pending Level 2 approval (state 3)
                return $query->where('workflow_state', 3)->count();

            case 'fri_vice_dean':
                // Pending Level 1 or Level 2 approval (states 2 and 3)
                return $query->whereIn('workflow_state', [2, 3])->count();

            default:
                return 0;
        }
    }
} 