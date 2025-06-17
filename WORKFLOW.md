# Laravel Workflow System

A comprehensive, extensible workflow management system for Laravel applications that provides state management, transitions, guards, events, and history tracking.

## Features

- 🔄 **State Management**: Manage complex state transitions with ease
- 🛡️ **Guards**: Implement business logic to control when transitions can occur
- 📊 **History Tracking**: Track all workflow transitions with context
- 🎯 **Events**: Listen to workflow transitions for side effects
- ⚙️ **Configurable**: Multiple workflows with different configurations
- 🕒 **Time-based Rules**: Guards with time constraints and business hours
- 👤 **Role-based Access**: Control who can perform transitions
- 🖥️ **Console Commands**: Manage workflows from the command line
- 🎭 **Facades**: Easy access via Laravel facades

## Installation

The workflow system is already integrated into your Laravel application. To set it up:

1. Run the migrations:
```bash
php artisan migrate
```

2. The system will automatically register default workflows via the `WorkflowServiceProvider`.

## Architecture

The workflow system consists of several key components:

- **WorkflowEngine** (`App\Services\Workflow\WorkflowEngine`) - The core engine that handles state transitions
- **WorkflowManager** (`App\Services\Workflow\WorkflowManager`) - Manages multiple workflow configurations
- **WorkflowConfiguration** (`App\Services\Workflow\WorkflowConfiguration`) - Configuration class for individual workflows
- **Guards** (`App\Services\Workflow\Guards\*`) - Business logic validators for transitions
- **Events** (`App\Events\Workflow\*`) - Events dispatched during transitions
- **HasWorkflow** (`App\Traits\HasWorkflow`) - Trait to add workflow functionality to models
- **Facades** (`App\Facades\*`) - Convenient facades for easy access

## Basic Usage

### Adding Workflow to Models

Add the `HasWorkflow` trait to any model that needs workflow functionality:

```php
<?php

namespace App\Models;

use App\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasWorkflow;

    protected string $workflowName = 'document_approval';

    // ... rest of your model
}
```

### Applying Transitions

```php
$document = Document::find(1);

// Check if transition is allowed
if ($document->canTransition(1)) { // 1 = APPROVE
    // Apply the transition with context
    $document->applyTransition(1, [
        'approved_by' => auth()->id(),
        'comments' => 'Document looks good',
        'approval_date' => now(),
    ]);
}
```

### Checking States

```php
$document = Document::find(1);

// Get current state
$currentState = $document->getCurrentState();

// Check if in specific state
if ($document->isInState(3)) { // 3 = VERIFIED
    // Document is verified
}

// Check if has ever reached a state
if ($document->hasReachedState(3)) { // 3 = VERIFIED
    // Document was verified at some point
}

// Get available transitions
$availableTransitions = $document->getAvailableTransitions();
```

## Configuration

### Workflow Configuration

Define workflows in `config/workflows.php`:

```php
return [
    'states' => [
        1 => [
            'name' => 'DRAFT',
            'label' => 'Draft',
            'color' => 'secondary',
            'icon' => 'edit',
            'is_terminal' => false,
            'is_initial' => true,
        ],
        2 => [
            'name' => 'PENDING',
            'label' => 'Awaiting Verification',
            'color' => 'warning',
            'icon' => 'clock',
            'is_terminal' => false,
            'is_initial' => false,
        ],
        3 => [
            'name' => 'VERIFIED',
            'label' => 'Verified',
            'color' => 'success',
            'icon' => 'check-circle',
            'is_terminal' => true,
            'is_initial' => false,
        ],
        // ... more states
    ],
    'transitions' => [
        1 => [
            'name' => 'APPROVE',
            'label' => 'Verify',
            'from_state' => 2,
            'to_state' => 3,
            'icon' => 'check-circle',
            'color' => 'success',
            'required_roles' => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean'],
            'requires_comment' => true,
        ],
        // ... more transitions
    ],
];
```

### Custom Guards

Create custom guards by implementing `WorkflowGuardInterface`:

```php
<?php

namespace App\Services\Workflow\Guards;

use App\Contracts\Workflow\WorkflowGuardInterface;
use Illuminate\Database\Eloquent\Model;

class CustomBusinessRuleGuard implements WorkflowGuardInterface
{
    public function canTransition(Model $model, int $from, int $to, int $transition): bool
    {
        // Your custom business logic here
        return true;
    }

    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string
    {
        return 'Custom business rule failed';
    }
}
```

## Available Guards

### DefaultWorkflowGuard
Basic guard that allows all transitions (good for testing).

### RoleBasedWorkflowGuard
Controls transitions based on user roles:

```php
$guard = new RoleBasedWorkflowGuard([
    1 => ['lecturer'],                                    // 1 = SUBMIT
    2 => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean'], // 2 = VERIFY
    3 => ['hr_finance_staff', 'head_of_hr_finance', 'fri_vice_dean'], // 3 = REJECT
    4 => ['lecturer'],                                    // 4 = RESUBMIT
]);
```

### TimeBasedWorkflowGuard
Controls transitions based on time constraints:

```php
$guard = new TimeBasedWorkflowGuard();
$guard->setMinTimeInState(1, 1, 2, 30); // 30 minutes for APPROVE transition
$guard->requireBusinessHours(1, 1, 2);  // Require business hours for APPROVE
$guard->setCooldown(2, 60);            // 1 hour cooldown for REJECT
```

## Events

Listen to workflow events for side effects:

```php
// In EventServiceProvider
protected $listen = [
    \App\Events\Workflow\WorkflowTransitionAttempted::class => [
        \App\Listeners\LogTransitionAttempt::class,
    ],
    \App\Events\Workflow\WorkflowTransitionApplied::class => [
        \App\Listeners\NotifyStakeholders::class,
        \App\Listeners\UpdateRelatedModels::class,
    ],
];
```

## History Tracking

Access workflow history:

```php
$document = Document::find(1);

// Get all workflow history
$history = $document->getWorkflowHistory();

// Get latest workflow entry
$latest = $document->getLatestWorkflowEntry();

// Get transition history via relationship
$transitions = $document->workflowHistory()->with('user')->get();
```

## Console Commands

Manage workflows from the command line:

```bash
# List all workflows
php artisan workflow list

# Show specific workflow details
php artisan workflow show --workflow=document_verification

# List available states
php artisan workflow states

# List available transitions
php artisan workflow transitions
```

## States and Transitions

### Available States

- `DRAFT` (1) - Document not yet submitted
- `PENDING` (2) - Awaiting verification by staff
- `VERIFIED` (3) - Approved/verified by staff (final state)
- `REJECTED` (4) - Not approved, needs revision

### Available Transitions

- `SUBMIT` (1) - Draft → Pending (submit/upload)
- `VERIFY` (2) - Pending → Verified (approve)
- `REJECT` (3) - Pending → Rejected (reject with note)
- `RESUBMIT` (4) - Rejected → Pending (revise and resubmit)

The system supports transitions between states. Check `config/workflows.php` for complete transition configuration.

## Database Schema

The workflow system uses these tables:

- `workflow_histories` - Stores transition history
- Your model tables need a `state_id` column

## Advanced Usage

### Multiple Workflows

You can have different workflows for different models:

```php
class Document extends Model
{
    use HasWorkflow;
    protected string $workflowName = 'document_verification';
}

class UserApplication extends Model
{
    use HasWorkflow;
    protected string $workflowName = 'user_verification';
}
```

### Custom Workflow Manager

Access the workflow manager for advanced operations:

```php
use App\Services\Workflow\WorkflowManager;
// Or use the facade
use App\Facades\WorkflowManager;

$manager = app(WorkflowManager::class);
// Or via facade
$manager = WorkflowManager::getFacadeRoot();

// Get specific workflow engine
$workflowEngine = $manager->get('document_verification');

// Check configuration
$config = $manager->getConfiguration('document_verification');

// Register new workflow at runtime
$newConfig = $manager->define('custom_workflow', 1) // 1 = WAITING_APPROVAL
    ->enableHistoryTracking()
    ->setStrictMode(true);
```

### Using Facades

You can use the provided facades for cleaner code:

```php
use App\Facades\Workflow;
use App\Facades\WorkflowManager;

// Get workflow engine for specific workflow
$engine = Workflow::for('document_verification');

// Or get via manager
$engine = WorkflowManager::get('document_verification');

// Apply transition via facade
Workflow::applyTransition($model, 1, ['reason' => 'Approved']); // 1 = APPROVE
```

### Workflow Observer

The system includes a `WorkflowObserver` that automatically:
- Initializes workflow state on model creation
- Logs state changes
- Handles cleanup on model deletion

Register it in your `AppServiceProvider`:

```php
use App\Observers\WorkflowObserver;

public function boot()
{
    Document::observe(WorkflowObserver::class);
}
```

## Testing

Test your workflows using the provided factories:

```php
use App\Models\Workflow\WorkflowHistory;

// Create workflow history
$history = WorkflowHistory::factory()
    ->forModel(Document::class, $document->id)
    ->forWorkflow('document_verification')
    ->withUser($user->id)
    ->create();
```

## Configuration Options

### Workflow Settings

- `track_history` - Enable/disable history tracking
- `auto_save` - Automatically save model after transitions
- `strict_mode` - Strict validation of transitions
- `require_comments` - Require comments for transitions
- `auto_notify` - Automatically notify stakeholders

### Global Settings

Configure global settings in `config/workflows.php`:

```php
'global' => [
    'default_timeout_hours' => 24,
    'enable_notifications' => true,
    'log_all_transitions' => true,
    'cache_workflow_instances' => true,
    'max_history_entries' => 1000,
],
```

## Best Practices

1. **Always use guards** - Implement proper business logic validation
2. **Enable history tracking** - For audit trails and debugging
3. **Use events** - For side effects like notifications
4. **Test transitions** - Write comprehensive tests for your workflows
5. **Document your workflows** - Keep track of state meanings and transition rules
6. **Use descriptive names** - Make states and transitions self-documenting

## Troubleshooting

### Common Issues

1. **Transition not allowed**: Check your guards and business logic
2. **State not initialized**: Make sure to call `initializeWorkflow()` or use the observer
3. **History not tracking**: Ensure `track_history` is enabled in configuration
4. **Performance issues**: Consider caching workflow instances and limiting history entries

### Debugging

Enable detailed logging:

```php
// In your guard
Log::debug('Checking transition', [
    'model' => get_class($model),
    'from' => $from,
    'to' => $to,
    'transition' => $transition,
]);
```

## Contributing

When extending the workflow system:

1. Follow existing patterns and interfaces
2. Add appropriate tests
3. Update documentation
4. Consider backward compatibility
5. Use proper type hints and return types

## License

This workflow system is part of your Laravel application and follows the same license terms.
