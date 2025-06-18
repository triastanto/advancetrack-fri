# Entity Relationship Diagram (ERD) Design

## English - Bahasa Indonesia Terminology Table

| English Term              | Bahasa Indonesia              |
|---------------------------|-------------------------------|
| user                      | pengguna                      |
| users                     | pengguna                      |
| study program             | program studi                 |
| study_programs            | program studi                 |
| employee_number           | NIP                           |
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
| study_status              | status studi                  |
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
- user_id (FK, cascade)
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
- created_at
- updated_at

### 3. employee_study_program
- employee_id (FK, cascade)
- study_program_id (FK, cascade)
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
- employee_id (FK, cascade)
- document_type_id (FK, restrict)
- file_name
- file_path
- workflow_state (default 1)
- semester (nullable)
- year (nullable)
- upload_date (nullable)
- created_at
- updated_at

### 7. workflow_histories
- id (PK)
- workflowable_type
- workflowable_id
- workflow_name
- from_state (nullable)
- to_state
- transition (nullable)
- context (json, nullable)
- user_id (FK, nullable)
- created_at
- updated_at
- INDEX: workflowable_type + workflowable_id

### 8. study_calendars
- id (PK)
- employee_id (FK, cascade)
- study_start
- estimated_study_end
- graduation_date (nullable)
- study_status (enum: active, finished, leave, drop_out; default 'active')
- workflow_state (default 1)
- created_at
- updated_at

### 9. study_details
- id (PK)
- study_calendar_id (FK → study_calendars.id)
- university_name
- university_address
- university_email
- university_phone
- study_program_name
- study_address
- study_level
- scholarship (nullable)
- funding_source (nullable)
- study_regulation_notes (nullable)
- created_at
- updated_at

### 10. study_promotors
- id (PK)
- study_detail_id (FK)
- name
- email
- is_primary (default: false)
- created_at
- updated_at

### 11. supervisor_assignments
- id (PK)
- employee_id (FK)
- supervisor_id (FK, self-reference)
- start_date
- end_date (nullable)
- created_at
- updated_at

### 12. course_responsibilities
- id (PK)
- employee_id (FK)
- course_name
- semester (nullable)
- academic_year (nullable)
- created_at
- updated_at

## Relationships

- users (1) --- (0..1) employees (user_id)
- employees (1) --- (N) employee_study_program (employee_id)
- study_programs (1) --- (N) employee_study_program (study_program_id)
- employees (1) --- (N) documents (employee_id)
- document_types (1) --- (N) documents (document_type_id)
- employees (1) --- (N) study_calendars (employee_id)
- study_calendars (1) --- (1) study_details (study_calendar_id)
- study_details (1) --- (N) study_promotors (study_detail_id)
- documents (1) --- (N) workflow_histories (workflowable_type/id)
- study_calendars (1) --- (N) workflow_histories (workflowable_type/id)
- users (1) --- (N) workflow_histories (user_id)
- employees (1) --- (N) supervisor_assignments (as supervisee)
- employees (1) --- (N) supervisor_assignments (as supervisor)
- employees (1) --- (N) course_responsibilities

## Diagram (Text)

```
                    users
                      |
                     (1)
                      |
                   employees -----(N)-----> course_responsibilities
                      |
                     (N)
                      |
          ┌-----------+----------┐
          |           |          |
         (N)         (N)        (N)
          |           |          |
employee_study_    documents  study_calendars
   program           |          |
      |             (N)        (1)
     (N)             |          |
      |        workflow_     study_details
                    |           |
                   (1)           |
                  users          |
                               study_promotors
                                |
                               (N)
                                |
                          supervisor_assignments
                                |
                         (self-reference to employees)

Legend:
- (1) = One-to-one relationship
- (N) = One-to-many relationship
- workflow_histories connects to both documents and study_calendars via polymorphic relationship
```

## Notes

- The `study_calendars` table manages time-based workflow lifecycle of the study (e.g. DRAFT → APPROVED → ACTIVE → FINISHED).
- The `study_details` table links to `study_calendars`, allowing multiple study attempts per employee to be uniquely documented.
- The `active_status` field was removed from `study_details` to avoid duplication with `study_calendars.workflow_state`.
- Workflow states now solely determine the study lifecycle.
- Promotors and supervisors are normalized for better traceability.
- Course responsibilities are separated to support multi-semester teaching tracking.
- `workflow_histories` uses polymorphic relationships to track state changes for both `documents` and `study_calendars`.

