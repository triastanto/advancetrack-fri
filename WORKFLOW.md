# Workflow Management System

## Overview

The Workflow Management System provides a flexible, multi-workflow framework for managing any business process that requires state transitions and approvals. The system is designed to be completely workflow-agnostic, allowing multiple workflows to be configured and managed through a centralized configuration system.

**Examples of workflows this system can handle:**
- Document approval processes (contracts, reports, proposals)
- Request management (leave requests, expense claims, purchase orders)
- Content moderation (posts, comments, user-generated content)
- Order processing (e-commerce, manufacturing, fulfillment)
- Project management (task approval, milestone tracking)
- Quality assurance (testing phases, review cycles)

## Key Features

- **Multi-Workflow Support**: Configure and manage multiple workflows simultaneously
- **Role-Based Access Control**: Restrict transitions based on user roles and permissions
- **Event-Driven Architecture**: Automatic notifications and audit logging
- **Flexible State Management**: Support for any combination of states (draft, pending, approved, rejected, etc.)
- **Guard System**: Time-based, role-based, and condition-based transition guards
- **Notification System**: Configurable email and in-app notifications
- **Audit Trail**: Complete history of all workflow transitions
- **Generic Implementation**: Works with any model and business process

## Architecture

### Core Components

1. **WorkflowDefinition**: Central configuration access and validation
2. **WorkflowEngine**: Workflow execution engine with guard and validation logic
3. **WorkflowManager**: Workflow instance management and caching
4. **WorkflowConfiguration**: Individual workflow configuration management
5. **HasWorkflow Trait**: Model integration for workflow functionality
6. **HasWorkflowManagement Trait**: Livewire component workflow management

### Configuration Structure

All workflows are defined in `config/workflows.php`:

```php
'workflows' => [
    'approval_process' => [
        'name' => 'Generic Approval Workflow',
        'description' => 'Standard approval process with review and approval stages',
        'initial_state' => 1, // DRAFT
        'settings' => [
            'track_history' => true,
            'auto_save' => true,
            'strict_mode' => true,
            'auto_notify' => true,
        ],
        'states' => [...],
        'transitions' => [...],
        'guards' => [...],
        'notifications' => [...],
    ],
    'request_management' => [
        'name' => 'Request Management Workflow',
        'description' => 'Generic request processing workflow',
        'initial_state' => 1,
        // ... configuration
    ],
]
```

**Common workflow examples:**
- `approval_process`: For any approval-based workflow
- `request_management`: For handling requests and applications
- `content_moderation`: For content review and publication
- `order_processing`: For order fulfillment workflows
- `project_lifecycle`: For project management workflows

### Model Integration

Models integrate with the workflow system using the `HasWorkflow` trait:

```php
use App\Traits\HasWorkflow;

// Example: Order processing workflow
class Order extends Model
{
    use HasFactory, HasWorkflow;

    // Define which workflow this model uses
    protected string $workflowName = 'order_processing';

    // Cast workflow_state to integer
    protected $casts = [
        'workflow_state' => 'integer',
    ];

    // The trait provides all workflow functionality
    // Including getWorkflowName(), canTransition(), applyTransition(), etc.
}

// Example: Content moderation workflow
class Post extends Model
{
    use HasFactory, HasWorkflow;

    protected string $workflowName = 'content_moderation';

    protected $casts = [
        'workflow_state' => 'integer',
    ];
}

// Example: Request management workflow
class LeaveRequest extends Model
{
    use HasFactory, HasWorkflow;

    protected string $workflowName = 'request_management';

    protected $casts = [
        'workflow_state' => 'integer',
    ];
}
```

## State Management

### State Configuration

Each workflow defines its states with the following properties:

```php
'states' => [
    1 => [
        'name' => 'DRAFT',
        'type' => 'draft',
        'label' => 'Draft',
        'color' => 'secondary',
        'icon' => 'edit',
        'is_terminal' => false,
        'is_initial' => true,
    ],
    2 => [
        'name' => 'PENDING',
        'type' => 'pending',
        'label' => 'Under Review',
        'color' => 'warning',
        'icon' => 'clock',
        'is_terminal' => false,
        'is_initial' => false,
    ],
    3 => [
        'name' => 'APPROVED',
        'type' => 'approved',
        'label' => 'Approved',
        'color' => 'success',
        'icon' => 'check-circle',
        'is_terminal' => true,
        'is_initial' => false,
    ],
    4 => [
        'name' => 'REJECTED',
        'type' => 'rejected',
        'label' => 'Rejected',
        'color' => 'danger',
        'icon' => 'x-circle',
        'is_terminal' => false,
        'is_initial' => false,
    ],
]
```

**Common state types and their typical usage:**
- `draft`: Initial creation state (orders, documents, posts)
- `pending`: Awaiting review/processing (requests, applications)
- `approved`: Successfully approved (contracts, expenses)
- `rejected`: Denied or declined (applications, content)
- `published`: Live/active state (content, announcements)
- `archived`: Completed/historical (projects, campaigns)
- `cancelled`: User or system cancelled (orders, requests)

### State Checking Methods

The `HasWorkflow` trait provides convenient state checking methods:

```php
// Check specific state types (works with any workflow)
if ($model->isInDraftState()) {
    // Handle draft state - item is being created/edited
}

if ($model->isInPendingState()) {
    // Handle pending state - awaiting review or processing
}

if ($model->isInApprovedState()) {
    // Handle approved state - successfully approved
}

if ($model->isInRejectedState()) {
    // Handle rejected state - denied or needs revision
}

if ($model->isInPublishedState()) {
    // Handle published state - live/active content
}

// Check if in any terminal state
if ($model->isInTerminalState()) {
    // Handle final states - no further transitions possible
}

// Get current state information
$stateInfo = $model->getWorkflowStateInfo();
$stateLabel = $stateInfo['label'];  // "Under Review", "Approved", etc.
$stateColor = $stateInfo['color'];  // "warning", "success", etc.
$stateIcon = $stateInfo['icon'];    // "clock", "check-circle", etc.
```

**Examples by workflow type:**
```php
// Order processing
if ($order->isInPendingState()) {
    // Order is being processed
}

// Content moderation
if ($post->isInPublishedState()) {
    // Post is live and visible
}

// Request management
if ($request->isInApprovedState()) {
    // Request has been approved
}
```

## Transition Management

### Transition Configuration

Transitions define how models move between states:

```php
'transitions' => [
    1 => [
        'name' => 'SUBMIT',
        'label' => 'Submit for Review',
        'from_state' => 1, // DRAFT
        'to_state' => 2,   // PENDING
        'icon' => 'upload',
        'color' => 'primary',
        'required_roles' => ['user', 'author'],
        'requires_comment' => false,
    ],
    2 => [
        'name' => 'APPROVE',
        'label' => 'Approve',
        'from_state' => 2, // PENDING
        'to_state' => 3,   // APPROVED
        'icon' => 'check-circle',
        'color' => 'success',
        'required_roles' => ['manager', 'supervisor', 'admin'],
        'requires_comment' => true,
    ],
    3 => [
        'name' => 'REJECT',
        'label' => 'Reject',
        'from_state' => 2, // PENDING
        'to_state' => 4,   // REJECTED
        'icon' => 'x-circle',
        'color' => 'danger',
        'required_roles' => ['manager', 'supervisor', 'admin'],
        'requires_comment' => true,
    ],
    4 => [
        'name' => 'REVISE',
        'label' => 'Revise and Resubmit',
        'from_state' => 4, // REJECTED
        'to_state' => 2,   // PENDING
        'icon' => 'refresh-cw',
        'color' => 'primary',
        'required_roles' => ['user', 'author'],
        'requires_comment' => false,
    ],
]
```

**Common transition patterns:**
- `SUBMIT`: Move from draft to review (orders, documents, requests)
- `APPROVE`: Accept and approve (applications, expenses, content)
- `REJECT`: Deny or decline (reviews, applications, content)
- `PUBLISH`: Make live/public (content, announcements, products)
- `ARCHIVE`: Move to archived state (completed projects, old campaigns)
- `CANCEL`: User or system cancellation (orders, subscriptions)

### Transition Operations

```php
// Check if transition is available
if ($model->canTransition(1)) {
    // Apply transition with optional context
    $model->applyTransition(1, [
        'comment' => 'Ready for review',
        'additional_data' => ['reviewer_id' => auth()->id()]
    ]);
}

// Get all available transitions for current user
$transitions = $model->getAvailableTransitions();

// Get specific transition information
$transition = $model->getTransition(1);
$transitionLabel = $transition['label'];
$requiresComment = $transition['requires_comment'];
```

**Examples by use case:**
```php
// Order processing
if ($order->canTransition(5)) { // SHIP transition
    $order->applyTransition(5, [
        'comment' => 'Shipped via FedEx',
        'tracking_number' => 'FX123456789'
    ]);
}

// Content management
if ($post->canTransition(2)) { // PUBLISH transition
    $post->applyTransition(2, [
        'comment' => 'Content approved for publication',
        'published_by' => auth()->id()
    ]);
}

// Request management
if ($request->canTransition(3)) { // APPROVE transition
    $request->applyTransition(3, [
        'comment' => 'Request approved by manager',
        'approved_amount' => 1500.00
    ]);
}
```

## Event System

### Workflow Events

The system fires events for all workflow operations:

```php
// Events fired during workflow operations
WorkflowTransitionAttempted::class  // Before transition validation
WorkflowTransitionApplied::class    // After successful transition
```

### Event Listeners

Events are handled by specialized listeners:

```php
// app/Listeners/NotifyStakeholders.php
class NotifyStakeholders
{
    public function handle(WorkflowTransitionApplied $event)
    {
        $workflowName = $event->workflowName;
        $model = $event->model;
        $transitionId = $event->transitionId;
        
        // Send notifications based on workflow configuration
        $this->sendNotifications($workflowName, $model, $transitionId);
    }
}

// app/Listeners/LogTransitionAttempt.php
class LogTransitionAttempt
{
    public function handle(WorkflowTransitionAttempted $event)
    {
        // Log all transition attempts for audit trail
        Log::info('Workflow transition attempted', [
            'workflow' => $event->workflowName,
            'model_type' => class_basename($event->model),
            'transition_id' => $event->transitionId,
            'user_id' => auth()->id(),
        ]);
    }
}

// app/Listeners/UpdateRelatedModels.php
class UpdateRelatedModels
{
    public function handle(WorkflowTransitionApplied $event)
    {
        // Update related models based on workflow state changes
        // Example: Update inventory when order is shipped
        // Example: Update user permissions when request is approved
    }
}
```

## Notification System

### Notification Configuration

Notifications are configured per workflow:

```php
'notifications' => [
    'channels' => ['mail', 'database'],
    'auto_notify' => true,
    'notification_types' => ['in_app', 'email'],
    'events' => [
        1 => ['reviewers'], // SUBMIT - notify reviewers
        2 => ['authors'], // APPROVE - notify authors
        3 => ['authors'], // REJECT - notify authors
        4 => ['reviewers'], // REVISE - notify reviewers
    ],
    'email_templates' => [
        1 => 'emails.item-submitted',
        2 => 'emails.item-approved',
        3 => 'emails.item-rejected',
        4 => 'emails.item-revised',
    ],
]
```

### Notification Recipients

The system supports flexible recipient groups:

**Generic recipient roles:**
- `authors`: Item creators/owners
- `reviewers`: All review staff
- `managers`: Management level users
- `admins`: System administrators
- `stakeholders`: All interested parties

**Domain-specific examples:**
```php
// E-commerce
'events' => [
    1 => ['fulfillment_team'], // Order placed
    2 => ['customer'], // Order shipped
    3 => ['customer', 'support'], // Order cancelled
]

// Content management
'events' => [
    1 => ['editors'], // Content submitted
    2 => ['authors'], // Content published
    3 => ['authors', 'editors'], // Content rejected
]

// HR processes
'events' => [
    1 => ['hr_staff'], // Request submitted
    2 => ['employee'], // Request approved
    3 => ['employee', 'hr_staff'], // Request denied
]
```

## Guard System

### Role-Based Guards

All transitions are protected by role-based guards:

```php
'guards' => [
    'role_based' => ['enabled' => true],
    'time_based' => [
        'enabled' => false,
        'business_hours_only' => [2, 3], // Only during business hours
        'minimum_time_in_state' => [
            2 => 15, // Must be in PENDING for at least 15 minutes
        ],
        'cooldown_periods' => [
            3 => 60, // 60-minute cooldown after APPROVE
        ],
    ],
]
```

**Role-based guard examples:**
```php
// E-commerce workflow
'required_roles' => ['warehouse_staff', 'fulfillment_manager']

// Content moderation workflow
'required_roles' => ['editor', 'content_moderator', 'admin']

// Financial approval workflow
'required_roles' => ['finance_manager', 'cfo', 'accounting_supervisor']

// HR request workflow
'required_roles' => ['hr_generalist', 'hr_manager', 'department_head']
```

### Custom Guards

Custom guards can be implemented for specific business logic:

```php
// Custom guard implementation examples
class OrderValueGuard
{
    public function canTransition(Model $model, array $transition, array $context): bool
    {
        // Only allow high-value orders to skip certain approvals
        return $model->total_amount < 10000 || auth()->user()->hasRole('senior_manager');
    }
}

class ContentComplianceGuard
{
    public function canTransition(Model $model, array $transition, array $context): bool
    {
        // Ensure content meets compliance requirements before publishing
        return $model->hasCompletedComplianceCheck() && $model->isContentAppropriate();
    }
}

class BusinessHoursGuard
{
    public function canTransition(Model $model, array $transition, array $context): bool
    {
        // Only allow certain transitions during business hours
        $now = now();
        return $now->isWeekday() && $now->hour >= 9 && $now->hour <= 17;
    }
}
```

## Livewire Integration

### HasWorkflowManagement Trait

Livewire components can use the `HasWorkflowManagement` trait:

```php
use App\Traits\HasWorkflowManagement;

// Example: Order management component
class OrderManagement extends Component
{
    use HasWorkflowManagement;

    public Order $order;

    public function applyTransition($transitionId, $comment = null)
    {
        try {
            $this->performTransition($this->order, $transitionId, [
                'comment' => $comment,
                'user_id' => auth()->id()
            ]);
            
            $this->emit('transitionApplied');
            session()->flash('success', 'Order status updated successfully');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}

// Example: Content moderation component
class ContentModerator extends Component
{
    use HasWorkflowManagement;

    public Post $post;

    public function approveContent($comment)
    {
        $this->performTransition($this->post, 2, [ // APPROVE transition
            'comment' => $comment,
            'moderator_id' => auth()->id()
        ]);
    }

    public function rejectContent($reason)
    {
        $this->performTransition($this->post, 3, [ // REJECT transition
            'comment' => $reason,
            'moderator_id' => auth()->id()
        ]);
    }
}
```

### Workflow Components

The system provides reusable Blade components:

```blade
<!-- Workflow status display -->
<x-workflow.workflow-status :model="$order" />
<x-workflow.workflow-status :model="$post" />
<x-workflow.workflow-status :model="$request" />

<!-- Workflow transition buttons -->
<x-workflow.transition-buttons :model="$order" />
<x-workflow.transition-buttons :model="$post" />

<!-- Workflow history -->
<x-workflow.workflow-history :model="$order" />
<x-workflow.workflow-history :model="$request" />

<!-- Workflow transition modal -->
<x-workflow.workflow-transition-modal :model="$order" :transition-id="$transitionId" />
```

**Component usage examples:**
```blade
<!-- E-commerce order management -->
<div class="order-status">
    <x-workflow.workflow-status :model="$order" />
    @if($order->canTransition())
        <x-workflow.transition-buttons :model="$order" />
    @endif
</div>

<!-- Content management system -->
<div class="post-moderation">
    <x-workflow.workflow-status :model="$post" />
    <x-workflow.workflow-history :model="$post" />
</div>

<!-- Request management -->
<div class="request-processing">
    <h3>Request Status</h3>
    <x-workflow.workflow-status :model="$leaveRequest" />
    
    @can('manage-requests')
        <x-workflow.transition-buttons :model="$leaveRequest" />
    @endcan
</div>
```

## Database Schema

### Workflow State Storage

Models store their workflow state in a `workflow_state` column:

```php
// Migration example - works for any model
Schema::table('orders', function (Blueprint $table) {
    $table->integer('workflow_state')->default(1); // Default to initial state
    $table->index('workflow_state');
});

Schema::table('posts', function (Blueprint $table) {
    $table->integer('workflow_state')->default(1);
    $table->index('workflow_state');
});

Schema::table('requests', function (Blueprint $table) {
    $table->integer('workflow_state')->default(1);
    $table->index('workflow_state');
});
```

### Workflow History

All transitions are logged in the `workflow_histories` table:

```php
Schema::create('workflow_histories', function (Blueprint $table) {
    $table->id();
    $table->morphs('workflowable'); // Polymorphic relation
    $table->string('workflow_name');
    $table->integer('from_state');
    $table->integer('to_state');
    $table->integer('transition_id');
    $table->text('comment')->nullable();
    $table->json('context')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->timestamps();
});
```

## Console Commands

### Workflow Management Commands

The system provides several Artisan commands:

```bash
# List all configured workflows
php artisan workflow:list

# Show workflow states for any workflow
php artisan workflow:states approval_process
php artisan workflow:states order_processing
php artisan workflow:states content_moderation

# Show workflow transitions for any workflow
php artisan workflow:transitions approval_process
php artisan workflow:transitions request_management

# Test email notifications for any workflow
php artisan workflow:test-notifications approval_process
php artisan workflow:test-notifications order_processing
```

### Example Command Output

```bash
$ php artisan workflow:list

Configured Workflows:
+------------------------+--------------------------------+---------------+
| Workflow Name          | Description                    | Initial State |
+------------------------+--------------------------------+---------------+
| approval_process       | Generic Approval Workflow     | 1 (DRAFT)     |
| order_processing       | Order Processing Workflow     | 1 (PENDING)   |
| content_moderation     | Content Moderation Workflow   | 1 (DRAFT)     |
| request_management     | Request Management Workflow   | 1 (SUBMITTED) |
+------------------------+--------------------------------+---------------+

$ php artisan workflow:states approval_process

Approval Process States:
+----+----------+---------+-------+-----------+
| ID | Name     | Label   | Color | Terminal  |
+----+----------+---------+-------+-----------+
| 1  | DRAFT    | Draft   | gray  | No        |
| 2  | PENDING  | Review  | yellow| No        |
| 3  | APPROVED | Approved| green | Yes       |
| 4  | REJECTED | Rejected| red   | No        |
+----+----------+---------+-------+-----------+
```

## Testing

### Unit Tests

```php
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    public function test_model_initialization()
    {
        // Test with any model type
        $order = Order::factory()->create();
        $post = Post::factory()->create();
        $request = LeaveRequest::factory()->create();
        
        // Test workflow initialization
        $this->assertEquals('order_processing', $order->getWorkflowName());
        $this->assertEquals('content_moderation', $post->getWorkflowName());
        $this->assertEquals('request_management', $request->getWorkflowName());
        
        $this->assertEquals(1, $order->getCurrentState());
        $this->assertTrue($order->isInDraftState());
    }

    public function test_workflow_transitions()
    {
        $model = Order::factory()->create(); // Or any model with workflow
        
        // Test available transitions
        $transitions = $model->getAvailableTransitions();
        $this->assertArrayHasKey(1, $transitions); // SUBMIT transition
        
        // Test transition execution
        $this->assertTrue($model->canTransition(1));
        $model->applyTransition(1, ['comment' => 'Submitting for processing']);
        
        $this->assertEquals(2, $model->getCurrentState());
        $this->assertTrue($model->isInPendingState());
    }

    public function test_role_based_guards()
    {
        $user = User::factory()->create(['role' => 'user']);
        $manager = User::factory()->create(['role' => 'manager']);
        $model = Order::factory()->create();
        
        // Test user can submit
        $this->actingAs($user);
        $this->assertTrue($model->canTransition(1)); // SUBMIT
        
        // Test manager can approve
        $this->actingAs($manager);
        $model->workflow_state = 2; // Set to PENDING
        $this->assertTrue($model->canTransition(2)); // APPROVE
        
        // Test user cannot approve
        $this->actingAs($user);
        $this->assertFalse($model->canTransition(2)); // APPROVE
    }
}
```

### Integration Tests

```php
public function test_complete_workflow_process()
{
    // Example: Order processing workflow
    $customer = User::factory()->create(['role' => 'customer']);
    $manager = User::factory()->create(['role' => 'manager']);
    $order = Order::factory()->create(['customer_id' => $customer->id]);
    
    // Step 1: Customer places order
    $this->actingAs($customer);
    $order->applyTransition(1, ['comment' => 'Order placed']);
    $this->assertTrue($order->isInPendingState());
    
    // Step 2: Manager approves order
    $this->actingAs($manager);
    $this->assertTrue($order->canTransition(2)); // APPROVE
    $order->applyTransition(2, ['comment' => 'Order approved for fulfillment']);
    $this->assertTrue($order->isInApprovedState());
    
    // Step 3: Check workflow history
    $history = $order->workflowHistory;
    $this->assertCount(2, $history);
    $this->assertEquals('Order placed', $history[0]->comment);
    $this->assertEquals('Order approved for fulfillment', $history[1]->comment);
}

public function test_content_moderation_workflow()
{
    $author = User::factory()->create(['role' => 'author']);
    $editor = User::factory()->create(['role' => 'editor']);
    $post = Post::factory()->create(['author_id' => $author->id]);
    
    // Step 1: Author submits content
    $this->actingAs($author);
    $post->applyTransition(1, ['comment' => 'Ready for review']);
    $this->assertTrue($post->isInPendingState());
    
    // Step 2: Editor publishes content
    $this->actingAs($editor);
    $post->applyTransition(2, ['comment' => 'Content approved and published']);
    $this->assertTrue($post->isInPublishedState());
}

public function test_request_approval_workflow()
{
    $employee = User::factory()->create(['role' => 'employee']);
    $supervisor = User::factory()->create(['role' => 'supervisor']);
    $request = LeaveRequest::factory()->create(['employee_id' => $employee->id]);
    
    // Step 1: Employee submits request
    $this->actingAs($employee);
    $request->applyTransition(1, ['comment' => 'Vacation request']);
    $this->assertTrue($request->isInPendingState());
    
    // Step 2: Supervisor approves request
    $this->actingAs($supervisor);
    $request->applyTransition(2, ['comment' => 'Request approved']);
    $this->assertTrue($request->isInApprovedState());
}
```

## Error Handling

### Exception Types

The system defines specific exceptions for workflow errors:

```php
// app/Exceptions/Workflow/InvalidTransitionException.php
class InvalidTransitionException extends Exception
{
    public function __construct($message = 'Invalid workflow transition', $code = 422)
    {
        parent::__construct($message, $code);
    }
}

// app/Exceptions/Workflow/WorkflowNotFoundException.php
class WorkflowNotFoundException extends Exception
{
    public function __construct($workflowName)
    {
        parent::__construct("Workflow '{$workflowName}' not found", 404);
    }
}
```

### Error Logging

All workflow operations are logged with comprehensive context:

```php
// Success logging
Log::info('Workflow transition applied', [
    'workflow' => $workflowName,
    'model' => class_basename($model),
    'model_id' => $model->id,
    'transition_id' => $transitionId,
    'from_state' => $fromState,
    'to_state' => $toState,
    'user_id' => Auth::id(),
    'context' => $context,
]);

// Error logging examples
Log::warning('Workflow transition failed: Invalid permissions', [
    'workflow' => 'order_processing',
    'model' => 'Order',
    'model_id' => 123,
    'transition_id' => 2,
    'user_id' => 456,
    'user_roles' => ['customer'],
    'required_roles' => ['manager'],
]);

Log::error('Workflow transition failed: Business rule violation', [
    'workflow' => 'content_moderation',
    'model' => 'Post',
    'model_id' => 789,
    'transition_id' => 3,
    'error' => 'Content does not meet publication standards',
]);
```

## Performance Considerations

### Database Indexing

Proper indexing is crucial for performance:

```php
// Recommended indexes
$table->index('workflow_state');
$table->index(['workflowable_type', 'workflowable_id']);
$table->index('workflow_name');
$table->index(['workflow_name', 'from_state']);
```

## Security Considerations

### Role Validation

All transitions validate user roles before execution:

```php
// Only users with required roles can perform transitions
'required_roles' => ['hr_finance_staff', 'head_of_hr_finance']
```

### Audit Trail

Complete audit trail of all workflow operations:

- Who performed the transition
- When the transition occurred
- What context was provided
- What the previous and new states are

### Data Validation

All workflow operations validate:

- User authentication
- Role authorization
- State consistency
- Transition availability
- Required context (comments, etc.)