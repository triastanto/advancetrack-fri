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
| uploaded_at            | waktu unggah            |
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
- role (lecturer, fsdp_staff, head_of_affairs, vice_dean)
- study_program_id (FK, nullable)
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
- document_type (personal_data, requirement, semester_report, final_report, graduation, additional, minutes, nde, pid)
- file_name
- file_path
- verification_status (draft, pending, verified, rejected)
- verification_note
- uploaded_at

### 6. semester_reports
- id (PK)
- employee_id (FK)
- semester
- year
- file_name
- file_path
- verification_status (draft, pending, verified, rejected)
- verification_note
- uploaded_at

### 7. service_bond_agreements
- id (PK)
- employee_id (FK)
- file_name
- file_path
- upload_date
- verification_status (draft, pending, verified, rejected)
- verification_note

### 8. study_calendars
- id (PK)
- employee_id (FK)
- study_start
- estimated_study_end
- graduation_date
- study_status (active, finished, leave, drop_out)

---

## Main Relationships

- **users** has one **employee** (if the user is an employee)
- **employees** has a role (lecturer, fsdp_staff, head_of_affairs, vice_dean)
- **employees** (lecturer role) has many **study_programs** via **employee_study_programs** (many-to-many)
- **employees** (non-lecturer roles) may have one **study_program** (study_program_id)
- **employee_study_programs** is a pivot table for many-to-many between **employees** (lecturer role) and **study_programs**
- **users** has many **documents**, **semester_reports**, **service_bond_agreements**, and **study_calendars**
- **documents**, **semester_reports**, **service_bond_agreements**, and **study_calendars** each have a FK to **employees**

---

## Diagram (Text)

```
users (1) --- (0..1) employees (N) ---< employee_study_programs >--- (N) study_programs
      |             |
      |             +--- (N) documents
      |             +--- (N) semester_reports
      |             +--- (N) service_bond_agreements
      |             +--- (N) study_calendars
```

---

## Notes
- The `users` table only stores authentication and shared user info. Organizational/HR info and role are in the `employees` table.
- The `role` field in employees distinguishes between lecturer, fsdp_staff, head_of_affairs, and vice_dean.
- Study program relationships for lecturers are managed via the `employee_study_programs` pivot table.
- Study program relationships for other employees are direct (FK in employees).
- The documents table can be used for various document types, including reports, minutes, NDE, PID, and additional documents.
- Verification status and verification notes exist in documents, reports, and agreements.
- Allowed values for `verification_status` are: `draft`, `pending`, `verified`, `rejected` (see `document-verification-state-machine.md`).
- Allowed values for `study_status` are: `active`, `leave`, `finished`, `drop_out` (see `study-calendar-state-machine.md`).
- Notifications will be handled by Laravel's built-in notification system
