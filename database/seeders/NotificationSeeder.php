<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\WorkflowNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user to notify (adjust as needed)
        $user = User::first();
        if (!$user) {
            $this->command->warn('No users found to notify.');
            return;
        }

        // Example notification data
        $notificationData = [
            'workflow_name' => 'document_verification',
            'model_id' => 123,
            'model_type' => 'App\\Models\\Document',
            'transition_id' => 2,
            'from_state' => 'Submitted',
            'to_state' => 'Verified',
            'transition' => 'Verify',
            'comment' => 'Document looks good.',
            'user_name' => $user->name,
            'user_id' => $user->id,
            'timestamp' => now(),
        ];

        // Send a workflow notification
        $user->notify(new WorkflowNotification('workflow_updated', $notificationData));
    }
}
