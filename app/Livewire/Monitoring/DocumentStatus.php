<?php

namespace App\Livewire\Monitoring;

use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\ResearchGroup;
use App\Models\ResearchLab;
use App\Models\Workflow\WorkflowHistory;
use App\Constants\DocumentTypeConstants;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentStatus extends Component
{
    use WithPagination;

    // Filter properties
    public $selectedResearchGroup = '';
    public $selectedResearchLab = '';
    public $selectedDocumentType = '';
    public $selectedStatus = '';
    public $dateRange = '30'; // days

    // Document data
    public $documentStats = [];
    public $statusDistribution = [];
    public $documentTypeStats = [];
    public $recentDocuments = [];
    public $processingTimeStats = [];

    protected $queryString = [
        'selectedResearchGroup' => ['except' => ''],
        'selectedResearchLab' => ['except' => ''],
        'selectedDocumentType' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'dateRange' => ['except' => '30'],
    ];

    public function mount()
    {
        $this->loadDocumentData();
    }

    public function updatedSelectedResearchGroup()
    {
        $this->selectedResearchLab = '';
        $this->loadDocumentData();
    }

    public function updatedSelectedResearchLab()
    {
        $this->loadDocumentData();
    }

    public function updatedSelectedDocumentType()
    {
        $this->loadDocumentData();
    }

    public function updatedSelectedStatus()
    {
        $this->loadDocumentData();
    }

    public function updatedDateRange()
    {
        $this->loadDocumentData();
    }

    public function loadDocumentData()
    {
        $this->documentStats = $this->getDocumentStats();
        $this->statusDistribution = $this->getStatusDistribution();
        $this->documentTypeStats = $this->getDocumentTypeStats();
        $this->recentDocuments = $this->getRecentDocuments();
        $this->processingTimeStats = $this->getProcessingTimeStats();
    }

    public function getDocumentStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $employeeIds = $query->pluck('id');

        // Get academic documents
        $academicQuery = AcademicDocument::with(['employee.user', 'documentType'])
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate);

        // Get approval documents
        $approvalQuery = ApprovalDocument::with(['employee.user', 'documentType'])
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate);

        // Apply document type filter
        if ($this->selectedDocumentType) {
            $academicQuery->where('document_type_id', $this->selectedDocumentType);
            $approvalQuery->where('document_type_id', $this->selectedDocumentType);
        }

        // Apply status filter
        if ($this->selectedStatus) {
            $academicQuery->where('workflow_state', $this->selectedStatus);
            $approvalQuery->where('workflow_state', $this->selectedStatus);
        }

        $academicTotal = $academicQuery->count();
        $approvalTotal = $approvalQuery->count();
        $total = $academicTotal + $approvalTotal;

        $stats = [
            'total_documents' => $total,
            'academic_documents' => $academicTotal,
            'approval_documents' => $approvalTotal,
            'verified_documents' => $academicQuery->clone()->where('workflow_state', 3)->count() + 
                                  $approvalQuery->clone()->where('workflow_state', 3)->count(),
            'pending_documents' => $academicQuery->clone()->where('workflow_state', 2)->count() + 
                                 $approvalQuery->clone()->where('workflow_state', 2)->count(),
            'rejected_documents' => $academicQuery->clone()->where('workflow_state', 4)->count() + 
                                  $approvalQuery->clone()->where('workflow_state', 4)->count(),
            'draft_documents' => $academicQuery->clone()->where('workflow_state', 1)->count() + 
                               $approvalQuery->clone()->where('workflow_state', 1)->count(),
            'verification_rate' => $total > 0 ? round((($academicQuery->clone()->where('workflow_state', 3)->count() + 
                                                       $approvalQuery->clone()->where('workflow_state', 3)->count()) / $total) * 100, 1) : 0,
            'avg_processing_time' => $this->getAverageProcessingTime($academicQuery, $approvalQuery),
        ];

        return $stats;
    }

    public function getStatusDistribution()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $employeeIds = $query->pluck('id');

        // Get academic documents by status
        $academicByStatus = AcademicDocument::selectRaw('workflow_state, COUNT(*) as count')
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->groupBy('workflow_state')
            ->pluck('count', 'workflow_state')
            ->toArray();

        // Get approval documents by status
        $approvalByStatus = ApprovalDocument::selectRaw('workflow_state, COUNT(*) as count')
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->groupBy('workflow_state')
            ->pluck('count', 'workflow_state')
            ->toArray();

        $distribution = [
            'draft' => ($academicByStatus[1] ?? 0) + ($approvalByStatus[1] ?? 0),
            'pending' => ($academicByStatus[2] ?? 0) + ($approvalByStatus[2] ?? 0),
            'verified' => ($academicByStatus[3] ?? 0) + ($approvalByStatus[3] ?? 0),
            'rejected' => ($academicByStatus[4] ?? 0) + ($approvalByStatus[4] ?? 0),
        ];

        return $distribution;
    }

    public function getDocumentTypeStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $employeeIds = $query->pluck('id');

        // Get academic documents by type
        $academicByType = AcademicDocument::selectRaw('document_type_id, COUNT(*) as count')
            ->with('documentType')
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->groupBy('document_type_id')
            ->get()
            ->keyBy('document_type_id')
            ->toArray();

        // Get approval documents by type
        $approvalByType = ApprovalDocument::selectRaw('document_type_id, COUNT(*) as count')
            ->with('documentType')
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->groupBy('document_type_id')
            ->get()
            ->keyBy('document_type_id')
            ->toArray();

        // Get all document types
        $documentTypes = DocumentType::all();

        $stats = [];
        foreach ($documentTypes as $type) {
            $academicCount = $academicByType[$type->id]['count'] ?? 0;
            $approvalCount = $approvalByType[$type->id]['count'] ?? 0;
            $totalCount = $academicCount + $approvalCount;

            if ($totalCount > 0) {
                $stats[] = [
                    'type' => $type,
                    'total_count' => $totalCount,
                    'academic_count' => $academicCount,
                    'approval_count' => $approvalCount,
                    'verified_count' => $this->getVerifiedCountByType($type->id, $employeeIds, $startDate),
                    'pending_count' => $this->getPendingCountByType($type->id, $employeeIds, $startDate),
                    'rejected_count' => $this->getRejectedCountByType($type->id, $employeeIds, $startDate),
                ];
            }
        }

        return collect($stats)->sortByDesc('total_count')->values();
    }

    public function getRecentDocuments()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $employeeIds = $query->pluck('id');

        // Get recent academic documents
        $academicDocs = AcademicDocument::with(['employee.user', 'employee.researchLab.researchGroup', 'documentType'])
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get recent approval documents
        $approvalDocs = ApprovalDocument::with(['employee.user', 'employee.researchLab.researchGroup', 'documentType'])
            ->whereIn('employee_id', $employeeIds)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Apply document type filter
        if ($this->selectedDocumentType) {
            $academicDocs = $academicDocs->where('document_type_id', $this->selectedDocumentType);
            $approvalDocs = $approvalDocs->where('document_type_id', $this->selectedDocumentType);
        }

        // Apply status filter
        if ($this->selectedStatus) {
            $academicDocs = $academicDocs->where('workflow_state', $this->selectedStatus);
            $approvalDocs = $approvalDocs->where('workflow_state', $this->selectedStatus);
        }

        // Convert to arrays and combine
        $allDocs = collect()
            ->concat($academicDocs->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'employee_id' => $doc->employee_id,
                    'document_type_id' => $doc->document_type_id,
                    'file_name' => $doc->file_name,
                    'workflow_state' => $doc->workflow_state,
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at,
                    'employee' => [
                        'id' => $doc->employee->id,
                        'nidn' => $doc->employee->nidn,
                        'user' => [
                            'id' => $doc->employee->user->id,
                            'name' => $doc->employee->user->name,
                            'email' => $doc->employee->user->email,
                        ],
                        'research_lab' => $doc->employee->researchLab ? [
                            'id' => $doc->employee->researchLab->id,
                            'name' => $doc->employee->researchLab->name,
                            'research_group' => $doc->employee->researchLab->researchGroup ? [
                                'id' => $doc->employee->researchLab->researchGroup->id,
                                'name' => $doc->employee->researchLab->researchGroup->name,
                            ] : null,
                        ] : null,
                    ],
                    'document_type' => [
                        'id' => $doc->documentType->id,
                        'name' => $doc->documentType->name,
                        'display_name' => $doc->documentType->display_name,
                    ],
                ];
            }))
            ->concat($approvalDocs->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'employee_id' => $doc->employee_id,
                    'document_type_id' => $doc->document_type_id,
                    'file_name' => $doc->file_name,
                    'workflow_state' => $doc->workflow_state,
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at,
                    'employee' => [
                        'id' => $doc->employee->id,
                        'nidn' => $doc->employee->nidn,
                        'user' => [
                            'id' => $doc->employee->user->id,
                            'name' => $doc->employee->user->name,
                            'email' => $doc->employee->user->email,
                        ],
                        'research_lab' => $doc->employee->researchLab ? [
                            'id' => $doc->employee->researchLab->id,
                            'name' => $doc->employee->researchLab->name,
                            'research_group' => $doc->employee->researchLab->researchGroup ? [
                                'id' => $doc->employee->researchLab->researchGroup->id,
                                'name' => $doc->employee->researchLab->researchGroup->name,
                            ] : null,
                        ] : null,
                    ],
                    'document_type' => [
                        'id' => $doc->documentType->id,
                        'name' => $doc->documentType->name,
                        'display_name' => $doc->documentType->display_name,
                    ],
                ];
            }))
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return $allDocs;
    }

    public function getProcessingTimeStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        $query = Employee::with(['user', 'researchLab.researchGroup'])
            ->where('role', 'lecturer');

        $this->applyFilters($query);

        $employeeIds = $query->pluck('id');

        // Get documents with workflow history
        $academicDocuments = AcademicDocument::with(['workflowHistory' => function($q) {
            $q->where('to_state', 3)->orderBy('created_at', 'desc');
        }])
        ->whereIn('employee_id', $employeeIds)
        ->where('workflow_state', 3)
        ->where('created_at', '>=', $startDate)
        ->get();

        $approvalDocuments = ApprovalDocument::with(['workflowHistory' => function($q) {
            $q->where('to_state', 3)->orderBy('created_at', 'desc');
        }])
        ->whereIn('employee_id', $employeeIds)
        ->where('workflow_state', 3)
        ->where('created_at', '>=', $startDate)
        ->get();

        $documents = collect()
            ->concat($academicDocuments->toArray())
            ->concat($approvalDocuments->toArray());

        $processingTimes = [];
        foreach ($documents as $document) {
            // Since we're now working with arrays, we need to get the workflow history differently
            $documentId = $document['id'];
            $verificationHistory = WorkflowHistory::where('workflowable_id', $documentId)
                ->where('to_state', 3)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($verificationHistory) {
                $processingTime = Carbon::parse($document['created_at'])->diffInDays($verificationHistory->created_at);
                $processingTimes[] = $processingTime;
            }
        }

        if (empty($processingTimes)) {
            return [
                'avg_days' => 0,
                'min_days' => 0,
                'max_days' => 0,
                'total_verified' => 0,
            ];
        }

        return [
            'avg_days' => round(array_sum($processingTimes) / count($processingTimes), 1),
            'min_days' => min($processingTimes),
            'max_days' => max($processingTimes),
            'total_verified' => count($processingTimes),
        ];
    }

    protected function applyFilters($query)
    {
        if ($this->selectedResearchGroup) {
            $query->whereHas('researchLab.researchGroup', function($q) {
                $q->where('id', $this->selectedResearchGroup);
            });
        }

        if ($this->selectedResearchLab) {
            $query->where('research_lab_id', $this->selectedResearchLab);
        }
    }

    protected function getVerifiedCountByType($typeId, $employeeIds, $startDate)
    {
        return AcademicDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 3)
            ->where('created_at', '>=', $startDate)
            ->count() +
            ApprovalDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 3)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    protected function getPendingCountByType($typeId, $employeeIds, $startDate)
    {
        return AcademicDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 2)
            ->where('created_at', '>=', $startDate)
            ->count() +
            ApprovalDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 2)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    protected function getRejectedCountByType($typeId, $employeeIds, $startDate)
    {
        return AcademicDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 4)
            ->where('created_at', '>=', $startDate)
            ->count() +
            ApprovalDocument::where('document_type_id', $typeId)
            ->whereIn('employee_id', $employeeIds)
            ->where('workflow_state', 4)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    protected function getAverageProcessingTime($academicQuery, $approvalQuery)
    {
        // This is a simplified calculation - in a real implementation, you'd want to track actual processing times
        $totalDocuments = $academicQuery->count() + $approvalQuery->count();
        $verifiedDocuments = $academicQuery->clone()->where('workflow_state', 3)->count() + 
                           $approvalQuery->clone()->where('workflow_state', 3)->count();
        
        if ($totalDocuments === 0) {
            return 0;
        }

        // Estimate average processing time based on verification rate
        return round(($verifiedDocuments / $totalDocuments) * 7, 1); // Days
    }

    public function getResearchGroups()
    {
        return ResearchGroup::orderBy('name')->get();
    }

    public function getResearchLabs()
    {
        $query = ResearchLab::with('researchGroup');
        
        if ($this->selectedResearchGroup) {
            $query->where('research_group_id', $this->selectedResearchGroup);
        }
        
        return $query->orderBy('name')->get();
    }

    public function getDocumentTypes()
    {
        return DocumentType::orderBy('display_name')->get();
    }

    public function getStatusOptions()
    {
        return [
            1 => 'Draft',
            2 => 'Pending',
            3 => 'Verified',
            4 => 'Rejected',
        ];
    }

    public function render()
    {
        return view('livewire.monitoring.document-status', [
            'researchGroups' => $this->getResearchGroups(),
            'researchLabs' => $this->getResearchLabs(),
            'documentTypes' => $this->getDocumentTypes(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
