# Lecturer Detail Modal

## Overview

The Lecturer Detail Modal is a comprehensive view component that displays detailed personal and education information for lecturers in the administration system. It provides a complete overview of a lecturer's profile including personal information, education history, study calendars, course responsibilities, and research affiliations.

## Features

### Personal Information Section
- **Basic Details**: Name, NIDN, email, position, and role
- **Personal Data**: Birth place, birth date, gender, functional position
- **Contact Information**: Phone, contact email, and origin address
- **Profile Photo**: Displays lecturer's photo or initials if no photo is available

### Research Lab Information
- **Laboratory**: Shows the research laboratory the lecturer belongs to
- **Research Group**: Displays the research group associated with the laboratory
- **Leadership**: Indicates if the lecturer is a lab head (Ketua Laboratorium)

### Education History
- **Academic Records**: Complete list of degrees, majors, and institutions
- **Graduation Details**: Graduation years and GPA information
- **Chronological Order**: Education records sorted by graduation year (descending)

### Study Calendar Information
- **Study Programs**: Current or past study programs and universities
- **Timeline**: Study start dates, estimated completion, and graduation dates
- **Status Tracking**: Workflow state of study calendars (Draft, Pending, Approved, etc.)
- **Promotors**: Lists of primary and secondary promotors/supervisors

### Course Responsibilities
- **Teaching Assignments**: Courses the lecturer is responsible for
- **Semester Information**: Semester and academic year details
- **Workload Overview**: Complete teaching portfolio

### Study Programs
- **Program Affiliations**: Study programs the lecturer is associated with
- **Role Indicators**: Visual badges showing program memberships

## Technical Implementation

### Components

1. **LecturerDetailModal** (`app/Livewire/Components/LecturerDetailModal.php`)
   - Main modal component handling data loading and display
   - Manages modal state and data relationships
   - Provides helper methods for data formatting

2. **Lecturer** (`app/Livewire/Administrations/Lecturer.php`)
   - Updated to include modal trigger functionality
   - Dispatches events to open the detail modal

### Views

1. **lecturer-detail-modal.blade.php** (`resources/views/livewire/components/lecturer-detail-modal.blade.php`)
   - Comprehensive modal layout with multiple sections
   - Responsive design with proper spacing and typography
   - Uses Tailwind CSS for styling

2. **lecturer.blade.php** (`resources/views/livewire/administrations/lecturer.blade.php`)
   - Updated to include "Lihat Detail" button on each lecturer card
   - Includes the modal component

### Data Relationships

The modal loads comprehensive data through the following relationships:

```php
Employee::with([
    'user',
    'researchLab.researchGroup',
    'studyPrograms',
    'educations',
    'studyCalendars.studyDetail.studyProgram',
    'studyCalendars.studyDetail.promotors',
    'courseResponsibilities'
])->find($lecturerId);
```

### Usage

1. **Access**: Navigate to the lecturer administration page
2. **Trigger**: Click "Lihat Detail" button on any lecturer card
3. **View**: Modal opens with comprehensive lecturer information
4. **Close**: Click the close button or press Escape key

### Event System

The modal uses Livewire's event system for communication:

- **Open Modal**: `$this->dispatch('lecturer-detail-modal:open', $lecturerId)`
- **Close Modal**: `$this->dispatch('lecturer-detail-modal:close')`

### Styling

- **Color Scheme**: Uses the application's primary green color (`#009444`)
- **Responsive Design**: Adapts to different screen sizes
- **Accessibility**: Proper ARIA labels and keyboard navigation
- **Animations**: Smooth transitions for modal open/close

## Data Display Logic

### Conditional Sections
- Research Lab section only shows if lecturer has a lab assignment
- Education section only displays if education records exist
- Study Calendar section shows only if study calendars are present
- Course Responsibilities section displays only if teaching assignments exist
- Study Programs section shows only if program affiliations exist

### Status Indicators
- Workflow states are color-coded for easy identification
- Lab head status is prominently displayed with crown icon
- Primary promotors are distinguished from secondary ones

### Data Formatting
- Dates are formatted in Indonesian locale
- GPA values are displayed with 2 decimal places
- Academic years are shown in standard format (YYYY/YYYY)
- Gender and role labels are translated to Indonesian

## Security Considerations

- Modal only loads data for authenticated users
- All data is properly escaped to prevent XSS attacks
- No sensitive information is exposed in the modal
- Access control is handled at the route level

## Performance Optimizations

- Eager loading of relationships to prevent N+1 queries
- Efficient data loading with specific relationship chains
- Modal content is only loaded when needed
- Proper indexing on database relationships

## Future Enhancements

Potential improvements for the modal:

1. **Export Functionality**: Add ability to export lecturer details to PDF
2. **Edit Capabilities**: Allow editing of certain information directly in modal
3. **Document Links**: Show related documents and their status
4. **Timeline View**: Visual timeline of academic progression
5. **Contact Actions**: Direct contact options (email, phone)
6. **Photo Upload**: Allow photo updates within the modal
7. **Print View**: Optimized layout for printing lecturer details 