# 📱 In-App Notification System

## Overview

The in-app notification system provides personalized, real-time notifications to users based on workflow transitions and system events. Notifications are displayed personally to each user and include rich content with actionable links.

## 🏗️ System Architecture

### Core Components

1. **WorkflowNotification Class** (`app/Notifications/WorkflowNotification.php`)
   - Handles notification content generation
   - Provides rich data structure for UI display
   - Supports multiple notification types

2. **NotifyStakeholders Listener** (`app/Listeners/NotifyStakeholders.php`)
   - Processes workflow transition events
   - Determines notification recipients
   - Sends personalized notifications

3. **Notification Livewire Component** (`app/Livewire/Notifications/Notification.php`)
   - Manages notification display and interaction
   - Provides filtering and search functionality
   - Handles bulk actions

4. **NotificationBell Component** (`app/Livewire/Components/NotificationBell.php`)
   - Real-time unread count display
   - Sidebar integration

## 📋 Notification Types

### Document Workflow Notifications

| Type | Trigger | Recipients | Content |
|------|---------|------------|---------|
| `document_submitted` | Document submitted for verification | Staff/Reviewers | "Dokumen Dikirim untuk Verifikasi" |
| `document_approved` | Document approved | Document Owner | "Dokumen Disetujui" |
| `document_rejected` | Document rejected | Document Owner | "Dokumen Ditolak" |

### Study Calendar Notifications

| Type | Trigger | Recipients | Content |
|------|---------|------------|---------|
| `study_calendar_approved` | Study calendar approved | Student | "Kalender Studi Disetujui" |
| `study_calendar_rejected` | Study calendar rejected | Student | "Kalender Studi Ditolak" |
| `study_started` | Study program started | Student, Supervisors, Admin | "Studi Dimulai" |
| `study_completed` | Study completed | Student, Supervisors, Admin | "Studi Selesai" |

### General Workflow Notifications

| Type | Trigger | Recipients | Content |
|------|---------|------------|---------|
| `workflow_updated` | General workflow state change | Relevant users | Status update message |
| `workflow_action_required` | Action required from user | Specific users | Action request message |

## 🎨 Notification Data Structure

Each notification contains:

```php
[
    'type' => 'document_approved',
    'data' => [
        'workflow_name' => 'verification_by_staff',
        'model_id' => 123,
        'model_type' => 'App\\Models\\AcademicDocument',
        'transition_id' => 2,
        'from_state' => 'Pending',
        'to_state' => 'Verified',
        'transition' => 'Verify',
        'comment' => 'Document looks good',
        'user_name' => 'John Doe',
        'user_id' => 456,
        'timestamp' => '2024-01-15T10:30:00Z',
    ],
    'title' => 'Dokumen Disetujui',
    'message' => 'Dokumen Anda telah disetujui oleh John Doe',
    'icon' => 'heroicon-o-check-circle',
    'color' => 'success',
    'action_url' => '/administrations/verification?document_id=123',
    'action_text' => 'Lihat Dokumen',
    'priority' => 'normal'
]
```

## 🔧 Configuration

### Workflow Configuration

Notifications are configured per workflow in `config/workflows.php`:

```php
'notifications' => [
    'channels' => ['mail', 'database'],
    'auto_notify' => true,
    'notification_types' => ['in_app', 'email'],
    'events' => [
        1 => ['staff'], // SUBMIT - notify staff
        2 => ['document_owner'], // VERIFY - notify document owner
        3 => ['document_owner'], // REJECT - notify document owner
    ],
    'staff_roles' => ['hr_finance_staff'],
    'email_templates' => [
        1 => 'emails.document-submitted',
        2 => 'emails.document-verified',
        3 => 'emails.document-rejected',
    ],
],
```

### Notification Settings

- **Channels**: `database` for in-app, `mail` for email
- **Auto Notify**: Enable/disable automatic notifications
- **Notification Types**: `in_app` and/or `email`
- **Events**: Map transitions to recipient groups
- **Roles**: Define role-based recipient groups

## 🎯 Usage Examples

### Sending a Notification

```php
// Automatic via workflow transition
$event = new WorkflowTransitionApplied($model, $fromState, $toState, $transition, $context, $workflowName);
event($event);

// Manual notification
$user->notify(new WorkflowNotification('document_approved', $data));
```

### Displaying Notifications

```blade
<!-- In sidebar -->
<livewire:components.notification-bell />

<!-- In notification page -->
<livewire:notifications.notification />
```

### Custom Notification Item

```blade
<x-ui.notification-item :notification="$notification" />
```

## 🔄 Real-time Updates

The notification system supports real-time updates through Livewire events:

- `notifications-updated`: Refresh notification count
- `notification-read`: Mark specific notification as read
- `notification-deleted`: Remove notification from list

## 🎨 UI Features

### Notification Display

- **Rich Content**: Title, message, icon, color
- **Action Links**: Direct navigation to relevant pages
- **Priority Indicators**: High, medium, normal priority levels
- **Read/Unread States**: Visual distinction
- **Timestamp**: Relative time display

### Management Features

- **Search**: Full-text search across notifications
- **Filtering**: All, unread, read filters
- **Bulk Actions**: Mark multiple as read, delete selected
- **Pagination**: Efficient loading of large notification lists
- **Real-time Count**: Live unread count in sidebar

## 🧪 Testing

Run notification tests:

```bash
php artisan test tests/Feature/Notifications/InAppNotificationTest.php
```

### Test Coverage

- Document workflow notifications
- Study calendar notifications
- Notification data structure validation
- Recipient targeting verification

## 📊 Monitoring

### Logging

Notifications are logged with detailed information:

```php
Log::info('In-app notification sent', [
    'user_id' => $user->id,
    'user_name' => $user->name,
    'type' => $type,
    'workflow' => $workflowName,
    'model_id' => $modelId,
]);
```

### Metrics

Track notification effectiveness:

- Delivery success rate
- Read rates by notification type
- User engagement with action links
- Notification volume by workflow

## 🔧 Customization

### Adding New Notification Types

1. Update `WorkflowNotification::getNotificationTitle()`
2. Update `WorkflowNotification::getNotificationMessage()`
3. Update `WorkflowNotification::getNotificationIcon()`
4. Update `WorkflowNotification::getNotificationColor()`
5. Update `NotifyStakeholders::determineNotificationType()`

### Custom Notification Content

```php
// In WorkflowNotification class
private function getCustomNotificationTitle(): string
{
    return match($this->type) {
        'custom_type' => 'Custom Notification Title',
        default => parent::getNotificationTitle()
    };
}
```

### Custom Action URLs

```php
// In WorkflowNotification class
private function getCustomActionUrl(): ?string
{
    return match($this->data['workflow_name']) {
        'custom_workflow' => route('custom.route', $this->data['model_id']),
        default => parent::getActionUrl()
    };
}
```

## 🚀 Performance Considerations

### Database Optimization

- Index on `notifications.notifiable_type` and `notifications.notifiable_id`
- Index on `notifications.read_at` for filtering
- Consider archiving old notifications

### Caching

- Cache unread counts for sidebar
- Cache notification lists for frequent access
- Use Redis for real-time features

### Queue Management

- Use queues for notification processing
- Implement retry logic for failed notifications
- Monitor queue performance

## 🔒 Security

### Access Control

- Users can only see their own notifications
- Notification data is sanitized
- Action URLs are validated

### Data Protection

- Sensitive information is not included in notifications
- Personal data is handled according to privacy policies
- Notification history is managed appropriately

## 📈 Future Enhancements

### Planned Features

- **Push Notifications**: Browser push notifications
- **Email Integration**: Enhanced email templates
- **Notification Preferences**: User-configurable settings
- **Advanced Filtering**: Date ranges, notification types
- **Notification Analytics**: Usage statistics and insights

### Integration Opportunities

- **Slack Integration**: Send notifications to Slack channels
- **SMS Notifications**: Critical notifications via SMS
- **Webhook Support**: External system integration
- **API Endpoints**: RESTful notification management

## 🐛 Troubleshooting

### Common Issues

1. **Notifications not appearing**
   - Check database connection
   - Verify notification configuration
   - Check user permissions

2. **Real-time updates not working**
   - Verify Livewire is properly configured
   - Check JavaScript console for errors
   - Ensure event listeners are registered

3. **Incorrect recipients**
   - Verify role assignments
   - Check workflow configuration
   - Review notification event mapping

### Debug Commands

```bash
# Check notification configuration
php artisan tinker
config('workflows.workflows.verification_by_staff.notifications');

# Test notification sending
php artisan tinker
$user = User::first();
$user->notify(new \App\Notifications\WorkflowNotification('test', ['message' => 'Test']));

# Clear notification cache
php artisan cache:clear
```

## 📚 Related Documentation

- [Workflow System Documentation](workflow-system.md)
- [Livewire Components](livewire-components.md)
- [Database Schema](database-schema.md)
- [Testing Guide](testing-guide.md) 