# Employee Selection Component for Approval Documents

## Overview
This component allows non-lecturer roles (HR staff, administrators, etc.) to select and manage approval documents for lecturers. The system automatically determines the user's role and presents the appropriate interface.

## Components Created

### 1. EmployeeFinder Component
**File:** `app/Livewire/Components/EmployeeFinder.php`
**View:** `resources/views/livewire/components/employee-finder.blade.php`

**Features:**
- Employee search functionality with pagination
- Real-time search across employee number, name, email, and position
- Modal-based employee selection interface
- Displays selected employee information
- Emits events for parent component integration

**Usage:**
```blade
<livewire:components.employee-finder :selectedEmployeeId="$selectedEmployeeId" />
```

### 2. Enhanced Upload Component
**File:** `app/Livewire/Administrations/Upload.php`
**View:** `resources/views/livewire/administrations/upload.blade.php`

**Enhanced Features:**
- Role-based interface switching
- Employee selection for non-lecturer roles
- Automatic employee detection for lecturers
- Conditional document display based on employee selection

## User Roles and Behavior

### Lecturer Roles
- **Role:** `lecturer`
- **Behavior:** Automatically uses their own employee record
- **Interface:** Standard document upload interface (no employee selection)

### Non-Lecturer Roles
- **Roles:** 
  - `hr_finance_staff`
  - `head_of_hr_finance`
  - `fri_vice_dean`
  - `head_of_study_program`
  - `head_of_research_group`
- **Behavior:** Must select a lecturer before managing documents
- **Interface:** Employee finder + document management interface

## Workflow

### For Non-Lecturer Users:
1. User accesses the upload page
2. System detects non-lecturer role
3. Employee finder component is displayed
4. User searches and selects a lecturer
5. Document management interface becomes available
6. User can upload/manage approval documents for selected lecturer

### For Lecturer Users:
1. User accesses the upload page
2. System detects lecturer role
3. Automatically uses their employee record
4. Document management interface is immediately available

## API/Events

### Events Emitted by EmployeeFinder:
- `employeeSelected`: Fired when employee is selected
  - Data: `{employeeId: number, employee: object}`
- `employeeCleared`: Fired when employee selection is cleared

### Events Listened by Upload Component:
- `employeeSelected`: Updates selected employee
- `employeeCleared`: Clears selected employee

## Security Considerations

1. **Role Verification:** System checks user roles before allowing employee selection
2. **Document Access:** Only allows document management for appropriate employee records
3. **Validation:** Validates employee selection before allowing document operations
4. **Error Handling:** Graceful handling of missing employee data

## File Structure
```
app/
├── Livewire/
│   ├── Components/
│   │   └── EmployeeFinder.php
│   └── Administrations/
│       └── Upload.php (enhanced)
resources/
└── views/
    ├── livewire/
    │   ├── components/
    │   │   └── employee-finder.blade.php
    │   └── administrations/
    │       └── upload.blade.php (enhanced)
    └── pages/
        └── administrations/
            └── upload.blade.php (title updated)
```

## Testing Scenarios

1. **Non-lecturer user accesses page without selecting employee**
   - Should show employee finder interface
   - Should not show document management interface

2. **Non-lecturer user selects employee**
   - Should show selected employee information
   - Should enable document management interface
   - Should allow document upload for selected employee

3. **Lecturer user accesses page**
   - Should skip employee selection
   - Should show their own documents immediately

4. **Employee search functionality**
   - Should search across multiple fields
   - Should paginate results
   - Should handle empty results gracefully

## Future Enhancements

1. **Bulk Operations:** Allow multiple employee selection for batch operations
2. **Favorites:** Allow users to mark frequently managed employees as favorites
3. **Recent Selections:** Show recently selected employees for quick access
4. **Advanced Filters:** Add department, role, or status filters to employee search
