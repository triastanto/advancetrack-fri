# Entity Relationship Diagram (ERD) Design

## English - Bahasa Indonesia Terminology Table

| English Term              | Bahasa Indonesia              |
|---------------------------|-------------------------------|
| user                      | pengguna                      |
| users                     | pengguna                      |
| study program             | program studi                 |
| study_programs            | program studi                 |
| nidn                      | NIP                           |
| NIDN                      | NIDN (Nomor Induk Dosen Nasional) |
| document                  | dokumen                       |
| documents                 | dokumen-dokumen               |
| document_type             | jenis dokumen                 |
| personal_data             | data pribadi                  |
| requirement               | persyaratan                   |
| semester_report           | laporan semester              |
| final_report              | laporan akhir                 |
| graduation                | kelulusan                     |
| additional                | tambahan                      |
| minutes                   | berita acara                  |
| nde                       | NDE (nota dinas elektronik)   |
| pid                       | PID (perjanjian ikatan dinas) |
| file_name                 | nama berkas                   |
| file_path                 | lokasi berkas                 |
| verification_status       | status verifikasi             |
| verification_note         | catatan verifikasi            |
| semester_reports          | laporan semester              |
| semester                  | semester                      |
| year                      | tahun                         |
| service_bond_agreements   | perjanjian ikatan dinas       |
| upload_date               | tanggal unggah                |
| study_calendars           | kalender studi                |
| study_start               | awal studi                    |
| estimated_study_end       | estimasi akhir studi          |
| graduation_date           | tanggal kelulusan             |
| workflow_state            | status alur kerja             |
| study_details             | detail studi lanjut           |
| study_address             | alamat selama studi           |
| university_name           | nama universitas              |
| university_address        | alamat universitas            |
| university_email          | email universitas             |
| university_phone          | telepon universitas           |
| study_program_name        | nama program studi lanjut     |
| study_level               | jenjang studi                 |
| scholarship               | beasiswa                      |
| funding_source            | sumber pendanaan              |
| study_regulation_notes    | peraturan universitas         |
| study_promotors           | promotor akademik             |
| supervisor_assignments    | dosen pendamping              |
| course_responsibilities   | pengampu mata kuliah          |
| draft                     | draf                          |
| pending                   | menunggu                      |
| verified                  | terverifikasi                 |
| rejected                  | ditolak                       |
| active                    | aktif                         |
| finished                  | selesai                       |
| leave                     | cuti                          |
| drop_out                  | drop out                      |
| research_groups           | kelompok keilmuan             |
| research_group            | kelompok keilmuan             |
| research_labs             | laboratorium riset            |
| research_lab              | laboratorium riset            |
| head_employee_id          | ketua kelompok keilmuan       |
| is_lab_head               | adalah ketua laboratorium     |
| lab_head                  | ketua laboratorium riset      |
| alias_name                | nama alias                    |


## Main Tables & Relationships

### 1. users
- id (PK)
- name
- email (unique)
- email_verified_at (nullable)
- password
- remember_token
- created_at
- updated_at

### 2. employees
- id (PK)
- user_id (FK → users.id, cascade)
- nidn (unique, indexed) // NIDN (Nomor Induk Dosen Nasional)
- position
- role (enum: lecturer, hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group; indexed)
- birth_place (nullable)
- birth_date (nullable)
- gender (enum: male, female, other)
- functional_position (nullable)
- origin_address (nullable)
- contact_phone (nullable)
- contact_email (nullable)
- research_lab_id (FK → research_labs.id, nullable, set null)
- is_lab_head (boolean, default: false, indexed)
- created_at
- updated_at
- INDEX: role, nidn, research_lab_id, is_lab_head

### 3. employee_study_program
- employee_id (FK → employees.id, cascade)
- study_program_id (FK → study_programs.id, cascade)
- created_at
- updated_at
- PRIMARY: [employee_id, study_program_id]

### 4. study_programs
- id (PK)
- name
- created_at
- updated_at

### 5. document_types
- id (PK)
- name (unique)
- display_name
- description (nullable)
- created_at
- updated_at

### 6. documents
- id (PK)
- employee_id (FK → employees.id, cascade)
- document_type_id (FK → document_types.id, restrict)
- file_name
- file_path
- workflow_state (unsignedBigInteger, default 1)
- semester (integer, nullable)
- year (year, nullable)
- upload_date (date, nullable)
- created_at
- updated_at

### 7. workflow_histories
- id (PK)
- workflowable_type
- workflowable_id
- workflow_name
- from_state (unsignedBigInteger, nullable)
- to_state (unsignedBigInteger)
- transition (unsignedBigInteger, nullable)
- context (json, nullable)
- user_id (FK → users.id, nullable, set null)
- created_at
- updated_at
- INDEX: workflowable_type + workflowable_id

### 8. study_calendars
- id (PK)
- employee_id (FK → employees.id, cascade)
- study_start (date)
- estimated_study_end (date)
- graduation_date (date, nullable)
- workflow_state (unsignedBigInteger, default 1)
- created_at
- updated_at

### 9. study_details
- id (PK)
- study_calendar_id (FK → study_calendars.id, cascade)
- university_name
- university_address (text)
- university_email
- university_phone
- study_program_id (FK → study_programs.id, cascade)
- study_address (text)
- study_level
- total_semester
- scholarship (nullable)
- funding_source (nullable)
- study_regulation_notes (text, nullable)
- created_at
- updated_at

### 10. study_promotors
- id (PK)
- study_detail_id (FK → study_details.id, cascade)
- name
- email
- is_primary (boolean, default: false)
- created_at
- updated_at

### 11. supervisor_assignments
- id (PK)
- employee_id (FK → employees.id, cascade)
- supervisor_id (FK → employees.id, cascade, self-reference)
- start_date (date)
- end_date (date, nullable)
- created_at
- updated_at

### 12. course_responsibilities
- id (PK)
- employee_id (FK → employees.id, cascade)
- course_name
- semester (integer, nullable)
- academic_year (string, nullable)
- created_at
- updated_at

### 13. research_groups
- id (PK)
- name (indexed)
- description (nullable)
- head_employee_id (FK → employees.id, nullable, set null, indexed) // Ketua Kelompok Keilmuan
- created_at
- updated_at

### 14. research_labs
- id (PK)
- name (indexed)
- alias_name (nullable)
- description (nullable)
- research_group_id (FK → research_groups.id, cascade, indexed)
- created_at
- updated_at

### 15. educations
- id (PK)
- employee_id (FK → employees.id, cascade)
- degree
- major
- institution
- graduation_year (nullable)
- gpa (nullable)
- created_at
- updated_at

## Relationships

- users (1) --- (0..1) employees (user_id)
- employees (1) --- (N) employee_study_program (employee_id)
- study_programs (1) --- (N) employee_study_program (study_program_id)
- research_groups (1) --- (N) research_labs (research_group_id)
- research_groups (1) --- (0..1) employees (head_employee_id) // Ketua Kelompok Keilmuan
- research_labs (1) --- (N) employees (research_lab_id)
- employees (1) --- (N) documents (employee_id)
- document_types (1) --- (N) documents (document_type_id)
- employees (1) --- (N) study_calendars (employee_id)
- study_calendars (1) --- (1) study_details (study_calendar_id)
- study_details (1) --- (N) study_promotors (study_detail_id)
- study_details (N) --- (1) study_programs (study_program_id)
- documents (1) --- (N) workflow_histories (workflowable_type/id)
- study_calendars (1) --- (N) workflow_histories (workflowable_type/id)
- users (1) --- (N) workflow_histories (user_id)
- employees (1) --- (N) supervisor_assignments (as supervisee)
- employees (1) --- (N) supervisor_assignments (as supervisor)
- employees (1) --- (N) course_responsibilities
- employees (1) --- (N) educations (employee_id)

## Diagram (Text)

```
                    users
                      |
                     (1)
                      |
                   employees -----(N)-----> course_responsibilities
                      |            |
                 ┌----+----┐      (N)
                (N)       (N)       |
                 |         |   research_labs
    employee_study_    documents    |
       program           |         (1)
          |             (N)         |
         (N)             |       research_groups
          |              |           |
     study_programs     (1)         (1)
                    |   |
                    | educations
                    |
                users

Legend:
- (1) = One-to-one relationship
- (N) = One-to-many relationship
- workflow_histories connects to both documents and study_calendars via polymorphic relationship
```

## Notes

### Workflow Management
- The `study_calendars` table manages time-based workflow lifecycle of the study (e.g. DRAFT → APPROVED → ACTIVE → FINISHED).
- The `study_details` table links to `study_calendars`, allowing multiple study attempts per employee to be uniquely documented.
- The `active_status` field was removed from `study_details` to avoid duplication with `study_calendars.workflow_state`.
- Workflow states determine the study lifecycle and serve as the single source of truth for study status.
- `workflow_histories` uses polymorphic relationships to track state changes for both `documents` and `study_calendars`, enabling complete audit trails.

### Document Management
- Documents are categorized by `document_types` to standardize document handling across the system.
- Each document belongs to an employee and has verification status tracking through workflow states.
- File storage paths are maintained in the `file_path` field for retrieval and management.

### Academic Structure
- Course responsibilities are separated to support multi-semester teaching tracking and workload distribution.
- Promotors and supervisors are normalized for better traceability and to handle changing supervision relationships over time.
- The `supervisor_assignments` table uses start_date and end_date to track supervision periods, allowing for supervisor changes during study periods.

### Research Organization Hierarchy
- **Research Groups (Kelompok Keilmuan)**: Top-level academic groupings with 3 main groups:
  1. Manufacturing dan Process Engineering
  2. Enterprise and Industrial Management System
  3. Digital Enterprise System and Technology
- **Research Labs (Laboratorium Riset)**: Individual laboratories within research groups (14 total laboratories)
- **Group Leadership**: Each research group can have a head employee (Ketua Kelompok Keilmuan) via `head_employee_id`
- **Lab Leadership**: Each research lab can have lab heads (Ketua Laboratorium) via `is_lab_head` flag on employees
- **Employee Assignment**: Employees are assigned to research labs, which automatically places them in the corresponding research group

### Research Laboratory Organization
- Added hierarchical structure: `research_groups` → `research_labs` → `employees`
- Employees belong to research labs with optional foreign key `research_lab_id` - allows for unassigned employees
- Boolean `is_lab_head` on employees indicates Ketua Lab (Head of Research Laboratory)
- Research groups can have designated heads via `head_employee_id` for Ketua Kelompok Keilmuan
- Each research laboratory is supervised by exactly one Ketua Laboratorium Riset, but employees can exist without lab assignments
- Multiple employees can be lab heads within the same research group (across different labs)

### Data Integrity Considerations
- Foreign key constraints with appropriate cascade/set null actions ensure referential integrity
- Unique constraints on critical fields (email, nidn) prevent duplicate entries
- Nullable fields are explicitly marked to distinguish between required and optional data
- Polymorphic relationships in `workflow_histories` allow tracking of different entity types while maintaining consistency
- Research group heads and lab heads are independent - one employee can be both

### Business Rules Implications
- An employee can belong to only one research lab at a time (single lab assignment)
- A research lab can have multiple lab heads and multiple regular members
- A research group can have one designated head employee
- An employee can be both a research group head and a lab head simultaneously
- Study calendars represent the official timeline and status, while study details contain the academic specifics
- Document workflows and study calendar workflows operate independently but may be related through business processes
- Employee roles (lecturer, hr_finance_staff, etc.) determine system permissions and available actions

### Scalability and Performance
- Indexes are strategically placed on frequently queried fields (role, nidn, research_lab_id, research_group_id, workflow states)
- Polymorphic relationships reduce table proliferation while maintaining flexibility
- Hierarchical research organization allows for efficient querying and reporting
- Separation of concerns between workflow management, document storage, and academic data allows for independent scaling