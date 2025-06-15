# Reusable Document Management Components

This document outlines the reusable components extracted from the final-reports.blade.php file that can be used across different views in the application.

## Components Created

### 1. Alert Message Component (`x-alert-message`)
**File:** `resources/views/components/alert-message.blade.php`

**Purpose:** Display success, error, warning, or info messages

**Props:**
- `message` (optional): Custom message text
- `type` (optional): Message type - 'success', 'error', 'warning', 'info' (default: 'success')

**Usage:**
```blade
<x-alert-message />
<x-alert-message message="Custom message" type="error" />
```

### 2. Study Info Card Component (`x-study-info-card`)
**File:** `resources/views/components/study-info-card.blade.php`

**Purpose:** Display active study information in a card format

**Props:**
- `studyInfo` (required): Array containing study information

**Usage:**
```blade
<x-study-info-card :study-info="$activeStudyInfo" />
```

### 3. Document Upload Section Component (`x-document-upload-section`)
**File:** `resources/views/components/document-upload-section.blade.php`

**Purpose:** Provides document type selection, upload button, and completion status

**Props:**
- `availableDocumentTypes` (required): Collection of document types
- `selectedDocumentTypeId` (required): Currently selected document type ID
- `uploadButtonText` (optional): Custom button text (default: 'Unggah Dokumen')
- `completionStatus` (optional): Document completion status array
- `title` (optional): Section title (default: '📄 Unggah Dokumen')

**Usage:**
```blade
<x-document-upload-section
    :available-document-types="$availableDocumentTypes"
    :selected-document-type-id="$selectedDocumentTypeId"
    :completion-status="$completionStatus"
    title="📄 Custom Title"
    upload-button-text="Custom Button Text" />
```

### 4. Document Status Badge Component (`x-document-status-badge`)
**File:** `resources/views/components/document-status-badge.blade.php`

**Purpose:** Display document verification status with appropriate colors

**Props:**
- `status` (required): Document status ('pending', 'verified', 'rejected', or other)
- `note` (optional): Verification note for rejected documents

**Usage:**
```blade
<x-document-status-badge :status="$document->verification_status" :note="$document->verification_note" />
```

### 5. Documents Table Component (`x-documents-table`)
**File:** `resources/views/components/documents-table.blade.php`

**Purpose:** Display documents in a table format with actions

**Props:**
- `documents` (required): Collection of documents
- `emptyMessage` (optional): Message when no documents (default: 'Tidak ada dokumen yang telah diunggah.')
- `showActions` (optional): Whether to show action buttons (default: true)

**Usage:**
```blade
<x-documents-table
    :documents="$documents"
    empty-message="Custom empty message"
    :show-actions="false" />
```

### 6. Document Upload Modal Component (`x-document-upload-modal`)
**File:** `resources/views/components/document-upload-modal.blade.php`

**Purpose:** Modal for uploading new documents

**Props:**
- `modalOpen` (required): Boolean to control modal visibility
- `availableDocumentTypes` (required): Collection of document types
- `selectedDocumentTypeId` (required): Currently selected document type ID
- `fileName` (required): Document file name
- `documentFile` (required): Document file

**Usage:**
```blade
<x-document-upload-modal
    :modal-open="$uploadModalOpen"
    :available-document-types="$availableDocumentTypes"
    :selected-document-type-id="$selectedDocumentTypeId"
    :file-name="$fileName"
    :document-file="$documentFile" />
```

### 7. Document View Modal Component (`x-document-view-modal`)
**File:** `resources/views/components/document-view-modal.blade.php`

**Purpose:** Modal for viewing and downloading documents

**Props:**
- `modalOpen` (required): Boolean to control modal visibility
- `document` (required): Document object to display

**Usage:**
```blade
<x-document-view-modal
    :modal-open="$viewModalOpen"
    :document="$currentDocument" />
```

## Example Usage in Another View

Here's how you can use these components in another document management view:

```blade
<div>
    {{-- Success message --}}
    <x-alert-message />

    {{-- Study information (if needed) --}}
    <x-study-info-card :study-info="$studyInfo" />

    {{-- Upload section --}}
    <x-document-upload-section
        :available-document-types="$documentTypes"
        :selected-document-type-id="$selectedTypeId"
        title="📋 Upload Academic Documents"
        upload-button-text="Upload File" />

    {{-- Documents table --}}
    <x-documents-table
        :documents="$userDocuments"
        empty-message="No academic documents uploaded yet." />

    {{-- Modals --}}
    <x-document-upload-modal
        :modal-open="$uploadModalOpen"
        :available-document-types="$documentTypes"
        :selected-document-type-id="$selectedTypeId"
        :file-name="$fileName"
        :document-file="$documentFile" />

    <x-document-view-modal
        :modal-open="$viewModalOpen"
        :document="$currentDocument" />
</div>
```

## Benefits

1. **Reusability**: Components can be used across multiple views
2. **Consistency**: Ensures consistent UI/UX across the application
3. **Maintainability**: Changes to component logic only need to be made in one place
4. **Flexibility**: Components accept props for customization
5. **Clean Code**: Original views become much cleaner and more readable

## Requirements

- Laravel 8+ (for component props support)
- Livewire (for wire: directives)
- Alpine.js (for tooltip functionality in status badges)
- Tailwind CSS (for styling)
