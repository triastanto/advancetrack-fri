# Academic Document Reminder System

## Overview

The Academic Document Reminder System is designed to automatically notify lecturers about missing academic documents for semester reports and final reports. This system integrates with the existing workflow and notification infrastructure to ensure timely submission of required documents.

## Features

### 1. **Smart Detection**
- Automatically identifies lecturers with active studies
- Detects missing semester and final documents
- Calculates appropriate reminder timing based on academic calendar

### 2. **Flexible Scheduling**
- **Semester Reports**: Monthly reminders based on academic semester
- **Final Reports**: Reminders based on study completion timeline
- Configurable reminder intervals and frequencies

### 3. **Multiple Notification Channels**
- In-app notifications with rich content
- Email reminders with detailed information
- Dashboard integration with real-time updates

### 4. **Priority System**
- **Medium Priority**: Semester report reminders
- **High Priority**: Final report reminders (approaching completion)
- Visual indicators for different urgency levels

## System Components

### Console Commands

#### `reminders:academic-documents`
```bash
# Send semester report reminders (dry run)
php artisan reminders:academic-documents --type=semester --dry-run

# Send final report reminders (dry run)
php artisan reminders:academic-documents --type=final --dry-run

# Send actual reminders
php artisan reminders:academic-documents --type=semester
php artisan reminders:academic-documents --type=final
```

### Scheduler Configuration

The system uses Laravel's task scheduler to automatically send reminders:

```php
// Weekly reminders (Mondays at 9:00 AM)
Schedule::command('reminders:academic-documents --type=semester')
    ->weekly()
    ->mondays()
    ->at('09:00');

// Daily overdue reminders (Weekdays at 2:00 PM)
Schedule::command('reminders:academic-documents --type=semester')
    ->daily()
    ->at('14:00')
    ->when(function () {
        return now()->isWeekday();
    });
```

### Dashboard Integration

The dashboard displays real-time reminders for lecturers:

- **Semester Report Reminders**: Shows missing semester documents
- **Final Report Reminders**: Shows missing final documents with completion countdown
- **Action Buttons**: Direct links to upload missing documents
- **Priority Indicators**: Visual cues for urgent reminders

## Configuration

### Reminder Settings (`config/reminders.php`)

```php
'academic_documents' => [
    'semester_reports' => [
        'enabled' => true,
        'schedule' => [
            'first_reminder' => 7,  // days before semester end
            'second_reminder' => 3, // days before semester end
            'final_reminder' => 1,  // day before semester end
            'overdue_alert' => 7,   // days after semester end
        ],
    ],
    'final_reports' => [
        'enabled' => true,
        'schedule' => [
            'initial_reminder' => 90,  // days before completion
            'progress_reminder' => 30, // days before completion
            'final_reminder' => 14,    // days before completion
            'overdue_alert' => 7,      // days after completion
        ],
    ],
],
```

## Reminder Types

### Semester Report Reminders

**Triggers:**
- Lecturer has active study calendar (workflow_state = 5)
- Current semester matches expected submission timeline
- Missing or incomplete semester documents

**Timing:**
- **First Reminder**: 1 week before semester end
- **Second Reminder**: 3 days before semester end
- **Final Reminder**: 1 day before semester end
- **Overdue Alert**: 1 week after semester end

**Required Documents:**
- Surat Pengantar dari Dosen
- Transkrip Nilai
- Surat Keterangan Aktif
- Bukti Unggah Publikasi di iGracias
- Bukti Pembayaran Biaya Pendidikan
- Surat Keterangan Progres Studi

### Final Report Reminders

**Triggers:**
- Lecturer approaching study end date (within 3 months)
- Missing final documents
- Study calendar in ACTIVE state nearing completion

**Timing:**
- **Initial Reminder**: 3 months before estimated completion
- **Progress Reminder**: 1 month before estimated completion
- **Final Reminder**: 2 weeks before estimated completion
- **Overdue Alert**: After estimated completion date

**Required Documents:**
- Ijazah
- Transkrip Nilai Akhir
- Surat Keterangan Lulus
- Surat Pernyataan Telah Menyelesaikan Studi

## Notification Content

### In-App Notifications

**Semester Report Reminder:**
```
Title: Pengingat Laporan Semester
Message: Halo [Name], Anda belum mengunggah dokumen laporan semester [Semester]. 
        Dokumen yang belum diunggah: [Document List]. 
        Silakan lengkapi dan unggah dokumen tersebut.
Icon: 📚
Color: warning
Action: Unggah Dokumen Semester
```

**Final Report Reminder:**
```
Title: Pengingat Laporan Akhir
Message: Halo [Name], studi Anda akan selesai dalam [X] hari. 
        Dokumen laporan akhir yang belum diunggah: [Document List]. 
        Silakan lengkapi dokumen tersebut untuk menyelesaikan studi.
Icon: 🎓
Color: danger
Action: Unggah Dokumen Akhir
```

## Usage Examples

### Testing the System

```bash
# Test semester reminders without sending
php artisan reminders:academic-documents --type=semester --dry-run

# Test final report reminders without sending
php artisan reminders:academic-documents --type=final --dry-run

# Send actual reminders
php artisan reminders:academic-documents --type=semester
php artisan reminders:academic-documents --type=final
```

### Setting Up Cron Jobs

Add to your server's crontab:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Environment Variables

```env
# Enable/disable reminder types
SEMESTER_REMINDERS_ENABLED=true
FINAL_REMINDERS_ENABLED=true
```

## Benefits

1. **Improved Compliance**: Ensures timely submission of academic documents
2. **Reduced Administrative Burden**: Automated reminders reduce manual follow-up
3. **Better User Experience**: Proactive notifications help lecturers stay on track
4. **Audit Trail**: Complete logging of all reminder activities
5. **Configurable**: Easy to adjust timing and frequency based on institutional needs

## Integration Points

- **Workflow System**: Integrates with existing document workflow states
- **Notification System**: Uses existing WorkflowNotification infrastructure
- **Dashboard**: Real-time reminders displayed on lecturer dashboard
- **Email System**: Leverages existing email templates and delivery system

## Monitoring and Logging

All reminder activities are logged for audit purposes:

```php
Log::info('Semester report reminder sent', [
    'lecturer_id' => $lecturer->id,
    'lecturer_name' => $lecturer->user->name,
    'semester' => $semester,
    'missing_documents_count' => $missingDocuments->count(),
]);
```

## Future Enhancements

1. **Push Notifications**: Browser push notifications for urgent reminders
2. **SMS Integration**: Critical reminders via SMS
3. **Customizable Templates**: User-configurable reminder messages
4. **Advanced Analytics**: Reminder effectiveness tracking
5. **Integration with External Systems**: Calendar integration, Slack notifications 