# Entity Relationship Diagram (ERD) Design

## English - Bahasa Indonesia Terminology Table

| English Term            | Bahasa Indonesia         |
|------------------------|-------------------------|
| user                   | pengguna                |
| users                  | pengguna                |
| study program          | program studi           |
| study_programs         | program studi           |
| employee_number        | NIP                     |
| document               | dokumen                 |
| documents              | dokumen-dokumen         |
| document_type          | jenis dokumen           |
| personal_data          | data pribadi            |
| requirement            | persyaratan             |
| semester_report        | laporan semester        |
| final_report           | laporan akhir           |
| graduation             | kelulusan               |
| additional             | tambahan                |
| minutes                | berita acara            |
| nde                    | NDE (nota dinas elektronik) |
| pid                    | PID (perjanjian ikatan dinas) |
| file_name              | nama berkas             |
| file_path              | lokasi berkas           |
| verification_status    | status verifikasi       |
| verification_note      | catatan verifikasi      |
| semester_reports       | laporan semester        |
| semester               | semester                |
| year                   | tahun                   |
| service_bond_agreements| perjanjian ikatan dinas |
| upload_date            | tanggal unggah          |
| study_calendars        | kalender studi          |
| study_start            | awal studi              |
| estimated_study_end    | estimasi akhir studi    |
| graduation_date        | tanggal kelulusan       |
| study_status           | status studi            |
| draft                  | draf                    |
| pending                | menunggu                |
| verified               | terverifikasi           |
| rejected               | ditolak                 |
| active                 | aktif                   |
| finished               | selesai                 |
| leave                  | cuti                    |
| drop_out               | drop out                |

## Main Tables & Relationships

### 1. users
- id (PK)
- name
- email
- password
- created_at
- updated_at

### 2. employees
- id (PK)
- user_id (FK)
- employee_number
- position
- role (lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group)
- created_at
- updated_at

### 3. study_programs
- id (PK)
- name

### 4. employee_study_programs
- id (PK)
- employee_id (FK)
- study_program_id (FK)
- created_at
- updated_at

### 5. documents
- id (PK)
- employee_id (FK)
- document_type_id (FK) - references document_types table
- title
- file_name
- file_path
- state_id (workflow state: 1=DRAFT, 2=PENDING, 3=VERIFIED, 4=REJECTED)
- semester (nullable, used for semester_report type)
- year (nullable, used for semester_report type)
- upload_date (nullable, used for service_bond_agreement type)
- created_at
- updated_at

### 6. document_types
- id (PK)
- name (personal_data, requirement, semester_report, final_report, graduation, additional, minutes, nde, pid, service_bond_agreement)
- description
- created_at
- updated_at

### 7. workflow_histories
- id (PK)
- workflowable_type (Document class)
- workflowable_id (document.id)
- from_state_id
- to_state_id
- user_id (FK to users - who performed action)
- context (JSON: comments, metadata, user info)
- created_at
- updated_at

### 8. study_calendars
- id (PK)
- employee_id (FK)
- study_start
- estimated_study_end
- graduation_date
- study_status (active, finished, leave, drop_out)
- created_at
- updated_at

---

## Main Relationships

- **users** has one **employee** (if the user is an employee)
- **employees** has a role (lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean)
- **employees** (lecturer role) has many **study_programs** via **employee_study_programs** (many-to-many)
- **employees** (non-lecturer roles) may have one **study_program** (study_program_id)
- **employee_study_programs** is a pivot table for many-to-many between **employees** (lecturer role) and **study_programs**
- **users** has many **documents** and **study_calendars**
- **documents** belongs to **document_types** and **employees**
- **documents** has many **workflow_histories** (polymorphic relationship)
- **workflow_histories** belongs to **users** (who performed the action)
- **study_calendars** belongs to **employees**

---

## Diagram (Text)

```
users (1) --- (0..1) employees (N) ---< employee_study_programs >--- (N) study_programs
      |             |
      |             +--- (N) documents --- (1) document_types
      |             |           |
      |             |           +--- (N) workflow_histories --- (1) users
      |             |
      |             +--- (N) study_calendars
```

---

## Notes

- The `users` table only stores authentication and shared user info. Organizational/HR info and role are in the `employees` table.
- The `role` field in employees distinguishes between lecturer, hr_finance_staff, head_of_hr_finance, and fri_vice_dean.
- Study program relationships for lecturers are managed via the `employee_study_programs` pivot table.
- Study program relationships for other employees are direct (FK in employees).
- The documents table can be used for various document types, with specific types managed by `document_types` table.
- Document types include: personal_data, requirement, semester_report, final_report, graduation, additional, minutes, nde, pid, service_bond_agreement.
- Semester reports have additional fields (semester, year), service bond agreements have upload_date.
- Document status is managed by workflow system with `state_id` and `workflow_histories` table.
- Allowed values for `state_id` are: `1` (DRAFT), `2` (PENDING), `3` (VERIFIED), `4` (REJECTED)
- Allowed values for `study_status` are: `active`, `leave`, `finished`, `drop_out` (see `study-calendar-state-machine.md`).
- Notifications will be handled by Laravel's built-in notification system

## Workflow System

### Document Status Management:
Document status is managed through a workflow system using the `state_id` field and `workflow_histories` table for complete audit trail.

### Workflow States:
- `1` = DRAFT (initial state)
- `2` = PENDING (submitted for review)  
- `3` = VERIFIED (approved/accepted)
- `4` = REJECTED (needs revision)

### Data Access Patterns:
- **Status**: `document.verification_status` (accessor method maps state_id to string)
- **Notes**: `document.verification_note` (accessor gets latest comment from workflow_histories)
- **History**: `document.workflowHistory` (complete audit trail with user context)

### Benefits:
1. **Single Source of Truth**: All status information from workflow system
2. **Rich History**: Complete audit trail with user context and timestamps
3. **No Sync Issues**: Eliminates data inconsistency between redundant fields
4. **Extensible**: Easy to add new workflow states and transitions
5. **Better Audit**: Full traceability of who changed what and when
