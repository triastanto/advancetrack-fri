<?php

namespace App\Console\Commands;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Listeners\NotifyStakeholders;
use App\Mail\DocumentSubmittedMail;
use App\Mail\DocumentVerifiedMail;
use App\Mail\DocumentRejectedMail;
use App\Mail\DocumentResubmittedMail;
use App\Models\Document;
use App\Models\User;
use App\Services\Workflow\NotificationConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications 
                            {type : The notification type (submission, verification, rejection, resubmission, all)}
                            {document_id : The ID of the document to test with}
                            {--email= : The email address to send test to (required for individual tests)}
                            {--staff_name=Test Staff : The name of the staff member}
                            {--employee_name=Test Employee : The name of the employee}
                            {--comment=Default test comment : The comment/reason text}
                            {--simulate : Use workflow simulation instead of direct mail sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unified test command for all document email notifications';

    /**
     * Available notification types
     */
    private const NOTIFICATION_TYPES = [
        'submission' => 'Document Submission',
        'verification' => 'Document Verification', 
        'rejection' => 'Document Rejection',
        'resubmission' => 'Document Resubmission',
        'all' => 'All Notification Types'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $type = strtolower($this->argument('type'));
            $documentId = $this->argument('document_id');
            $testEmail = $this->option('email');
            $simulate = $this->option('simulate');

            // Validate notification type
            if (!array_key_exists($type, self::NOTIFICATION_TYPES)) {
                $this->error("Invalid notification type: {$type}");
                $this->info("Available types: " . implode(', ', array_keys(self::NOTIFICATION_TYPES)));
                return 1;
            }

            // Find the document
            $document = Document::with(['employee.user', 'documentType'])->find($documentId);
            if (!$document) {
                $this->error("Document with ID {$documentId} not found!");
                return 1;
            }

            $this->displayHeader($type, $document);

            if ($type === 'all') {
                return $this->testAllNotifications($document, $testEmail, $simulate);
            } else {
                return $this->testSingleNotification($type, $document, $testEmail, $simulate);
            }

        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Display command header information
     */
    private function displayHeader(string $type, Document $document): void
    {
        $this->info("🧪 Testing Email Notifications");
        $this->line("Type: " . self::NOTIFICATION_TYPES[$type]);
        $this->line("Document: {$document->file_name}");
        $this->line("Owner: " . ($document->employee->user->name ?? 'Unknown'));
        $this->line("Type: " . ($document->documentType->display_name ?? 'Unknown'));
        $this->newLine();
    }

    /**
     * Test all notification types
     */
    private function testAllNotifications(Document $document, ?string $testEmail, bool $simulate): int
    {
        $this->info("🔄 Testing all notification types...");
        $this->newLine();

        $results = [];
        $types = ['submission', 'verification', 'rejection', 'resubmission'];

        foreach ($types as $type) {
            $this->info("Testing {$type} notification...");
            try {
                if ($simulate) {
                    $result = $this->testWithWorkflowSimulation($type, $document, false);
                } else {
                    if (!$testEmail) {
                        $this->error("Email address is required for direct email testing. Use --email option or --simulate flag.");
                        $results[$type] = '❌ Failed: No email provided';
                        continue;
                    }
                    $result = $this->testWithDirectEmail($type, $document, $testEmail, false);
                }
                $results[$type] = $result === 0 ? '✅ Success' : '❌ Failed';
            } catch (\Exception $e) {
                $results[$type] = '❌ Error: ' . $e->getMessage();
                Log::error("Email test error for {$type}: " . $e->getMessage());
            }
            $this->line("  Result: " . $results[$type]);
            $this->newLine();
        }

        // Display summary
        $this->info("📊 Test Results Summary:");
        $this->table(['Notification Type', 'Result'], 
            array_map(fn($type, $result) => [ucfirst($type), $result], array_keys($results), $results)
        );

        $hasFailures = collect($results)->contains(fn($result) => str_starts_with($result, '❌'));
        return $hasFailures ? 1 : 0;
    }

    /**
     * Test a single notification type
     */
    private function testSingleNotification(string $type, Document $document, ?string $testEmail, bool $simulate, bool $verbose = true): int
    {
        if ($simulate) {
            return $this->testWithWorkflowSimulation($type, $document, $verbose);
        } else {
            if (!$testEmail) {
                $this->error("Email address is required for direct email testing. Use --email option or --simulate flag.");
                return 1;
            }
            return $this->testWithDirectEmail($type, $document, $testEmail, $verbose);
        }
    }

    /**
     * Test using workflow simulation
     */
    private function testWithWorkflowSimulation(string $type, Document $document, bool $verbose = true): int
    {
        if ($verbose) {
            $this->info("🔄 Using workflow simulation...");
        }

        $config = $this->getWorkflowConfig($type);
        $notificationData = $this->buildNotificationData($type, $document, $config);

        // Check recipients
        if (in_array($type, ['submission', 'resubmission'])) {
            $recipients = $this->getStaffUsers();
            if ($recipients->isEmpty()) {
                $this->warn('No staff users found to receive notification emails.');
                return 1;
            }
            if ($verbose) {
                $this->info("Found {$recipients->count()} staff recipients:");
                foreach ($recipients as $user) {
                    $this->line("  - {$user->name} ({$user->email})");
                }
            }
        } else {
            $recipient = $this->getDocumentOwnerEmail($document);
            if (!$recipient) {
                $this->warn('No valid document owner email found.');
                return 1;
            }
            if ($verbose) {
                $this->info("Recipient: {$recipient['name']} ({$recipient['email']})");
            }
        }

        // Create workflow event
        $event = new WorkflowTransitionApplied(
            $document,
            $config['from_state'],
            $config['to_state'],
            $config['transition_id'],
            $notificationData['context']
        );

        // Handle the event
        $listener = new NotifyStakeholders();
        $listener->handle($event);

        if ($verbose) {
            $this->info('✅ Workflow simulation completed successfully!');
            $this->info('Check your mail logs or configured mail driver for email delivery.');
        }

        return 0;
    }

    /**
     * Test with direct email sending
     */
    private function testWithDirectEmail(string $type, Document $document, string $testEmail, bool $verbose = true): int
    {
        if ($verbose) {
            $this->info("📧 Sending direct email to: {$testEmail}");
        }

        $config = $this->getWorkflowConfig($type);
        $notificationData = $this->buildNotificationData($type, $document, $config);

        // Send appropriate email
        $mailClass = $this->getMailClass($type);
        Mail::to($testEmail)->send(new $mailClass($document, $notificationData['data']));

        if ($verbose) {
            $this->info("✅ {$config['name']} email sent successfully!");
            $this->displayEmailPreview($type, $document, $notificationData);
        }

        return 0;
    }

    /**
     * Get workflow configuration for notification type
     */
    private function getWorkflowConfig(string $type): array
    {
        return match ($type) {
            'submission' => [
                'name' => 'Document Submission',
                'transition_id' => NotificationConfig::TRANSITION_SUBMIT,
                'from_state' => 1, // DRAFT
                'to_state' => 2,   // PENDING
                'mail_class' => DocumentSubmittedMail::class,
            ],
            'verification' => [
                'name' => 'Document Verification',
                'transition_id' => NotificationConfig::TRANSITION_VERIFY,
                'from_state' => 2, // PENDING
                'to_state' => 3,   // VERIFIED
                'mail_class' => DocumentVerifiedMail::class,
            ],
            'rejection' => [
                'name' => 'Document Rejection',
                'transition_id' => NotificationConfig::TRANSITION_REJECT,
                'from_state' => 2, // PENDING
                'to_state' => 4,   // REJECTED
                'mail_class' => DocumentRejectedMail::class,
            ],
            'resubmission' => [
                'name' => 'Document Resubmission',
                'transition_id' => NotificationConfig::TRANSITION_RESUBMIT,
                'from_state' => 4, // REJECTED
                'to_state' => 2,   // PENDING
                'mail_class' => DocumentResubmittedMail::class,
            ],
        };
    }

    /**
     * Build notification data for the test
     */
    private function buildNotificationData(string $type, Document $document, array $config): array
    {
        $baseData = [
            'document_id' => $document->id,
            'document_name' => $document->file_name,
            'document_type' => $document->documentType->display_name ?? 'Unknown',
            'from_state' => $this->getStateName($config['from_state']),
            'to_state' => $this->getStateName($config['to_state']),
            'transition' => $config['name'],
            'timestamp' => now(),
        ];

        // Add type-specific data
        $specificData = match ($type) {
            'submission' => [
                'comment' => $this->option('comment') ?: 'Dokumen dikirim untuk verifikasi',
                'user_name' => $this->option('employee_name'),
            ],
            'verification' => [
                'comment' => $this->option('comment') ?: 'Document has been successfully verified and approved.',
                'user_name' => $this->option('staff_name'),
            ],
            'rejection' => [
                'comment' => $this->option('comment') ?: 'Document does not meet quality standards. Please revise and resubmit.',
                'user_name' => $this->option('staff_name'),
            ],
            'resubmission' => [
                'comment' => $this->option('comment') ?: 'Document has been revised based on previous feedback.',
                'user_name' => $this->option('employee_name'),
            ],
        };

        return [
            'data' => array_merge($baseData, $specificData),
            'context' => [
                'user_id' => 1,
                'comment' => $specificData['comment'],
                'user_name' => $specificData['user_name'],
                'timestamp' => now(),
            ]
        ];
    }

    /**
     * Get state name by ID
     */
    private function getStateName(int $stateId): string
    {
        return match ($stateId) {
            1 => 'Draft',
            2 => 'Awaiting Verification',
            3 => 'Verified',
            4 => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Get mail class for notification type
     */
    private function getMailClass(string $type): string
    {
        return $this->getWorkflowConfig($type)['mail_class'];
    }

    /**
     * Get staff users for notifications
     */
    private function getStaffUsers()
    {
        return User::whereHas('employee', function ($query) {
            $query->whereIn('role', NotificationConfig::getStaffRoles());
        })->get();
    }

    /**
     * Get document owner email
     */
    private function getDocumentOwnerEmail(Document $document): ?array
    {
        if (!$document->employee || !$document->employee->user || !$document->employee->user->email) {
            return null;
        }

        return [
            'email' => $document->employee->user->email,
            'name' => $document->employee->user->name,
        ];
    }

    /**
     * Display email preview information
     */
    private function displayEmailPreview(string $type, Document $document, array $notificationData): void
    {
        $this->newLine();
        $this->info("📋 Email Preview Data:");
        
        $previewData = [
            ['Document Name', $document->file_name],
            ['Document Type', $document->documentType->display_name ?? 'Unknown'],
            ['Owner', $document->employee->user->name ?? 'Unknown'],
            ['NIP', $document->employee->nip ?? 'N/A'],
        ];

        // Add type-specific preview data
        if (in_array($type, ['verification', 'rejection'])) {
            $previewData[] = ['Staff Member', $notificationData['data']['user_name']];
        } else {
            $previewData[] = ['Employee', $notificationData['data']['user_name']];
        }

        $previewData[] = ['Comment/Reason', $notificationData['data']['comment']];
        $previewData[] = ['Timestamp', $notificationData['data']['timestamp']->format('d M Y, H:i')];

        $this->table(['Field', 'Value'], $previewData);
        $this->newLine();
        $this->info("Test completed successfully! Check your email inbox.");
    }
}
