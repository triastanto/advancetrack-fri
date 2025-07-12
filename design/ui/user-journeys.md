# 👥 User Journeys by Role

## 🎓 Dosen (Lecturer) - Primary User Journey

### 📋 Phase 1: Initial Setup & Document Submission

#### **Week 1-2: Account Setup & Profile**
- **Navigation Access**: Dashboard, Data Pribadi
- **Actions**:
  - Complete profile information in "Data Pribadi"
  - Upload educational background in "Riwayat Pendidikan"
  - Review dashboard notifications and reminders
- **Permissions**: Read/Write own profile data

#### **Week 3-4: Study Calendar Creation**
- **Navigation Access**: Kalender Studi Lanjut → Kelola Kalender Studi Lanjut
- **Actions**:
  - Create initial study calendar (DRAFT state)
  - Define study timeline and milestones
  - Save draft for later completion
- **Permissions**: Create/edit own study calendar

#### **Week 5-8: Document Collection & Upload**
- **Navigation Access**: Dokumen Akademik → Persyaratan Studi Lanjut
- **Actions**:
  - Upload required documents:
    - Letter of Acceptance
    - Surat Pengantar Beasiswa
    - Surat Izin Rektor
    - SK Dosen Tetap Yayasan
    - Ijazah
    - Transkrip Nilai S1/S2
    - SK Inpassing
    - SK JAD
    - Surat Pernyataan Melaporkan Kelulusan
  - Submit each document for verification (DRAFT → PENDING)
  - Track verification status
- **Permissions**: Upload documents, submit for verification, view own document status

#### **Week 9-10: Study Calendar Submission**
- **Navigation Access**: Kalender Studi Lanjut → Kelola Kalender Studi Lanjut
- **Actions**:
  - Finalize study calendar
  - Submit for approval (DRAFT → PENDING_APPROVAL)
  - Wait for supervisor approval
- **Permissions**: Submit study calendar for approval

### 📋 Phase 2: Approval & Study Preparation

#### **Week 11-14: Wait for Approvals**
- **Navigation Access**: Dashboard, Kalender Studi Lanjut → Linimasa Kalender Studi Lanjut
- **Actions**:
  - Monitor approval status
  - Respond to any rejection comments
  - Revise and resubmit if needed (REJECTED → PENDING_APPROVAL)
- **Permissions**: View approval status, revise and resubmit

#### **Week 15-16: Study Commencement**
- **Navigation Access**: Kalender Studi Lanjut → Kelola Kalender Studi Lanjut
- **Actions**:
  - Once all approvals received, request to start study
  - Transition to ACTIVE state
  - Begin actual study program
- **Permissions**: Request study start (requires all approvals)

### 📋 Phase 3: Active Study Period

#### **Monthly: Semester Reports**
- **Navigation Access**: Dokumen Akademik → Laporan Per Semester (LKS)
- **Actions**:
  - Upload semester reports:
    - Surat Pengantar dari Dosen
    - Transkrip Nilai
    - Surat Keterangan Aktif
    - Bukti Unggah Publikasi di Igracias
    - Bukti Pembayaran Biaya Pendidikan
    - Surat Keterangan Progres Studi
  - Submit for verification
  - Track verification status
- **Permissions**: Upload semester reports, submit for verification

#### **As Needed: Leave Management**
- **Navigation Access**: Kalender Studi Lanjut → Kelola Kalender Studi Lanjut
- **Actions**:
  - Request official leave (ACTIVE → LEAVE)
  - Return from leave (LEAVE → ACTIVE)
- **Permissions**: Request leave, return from leave

### 📋 Phase 4: Study Completion

#### **Final Semester: Final Reports**
- **Navigation Access**: Dokumen Akademik → Laporan Akhir & Kelulusan
- **Actions**:
  - Upload final documents:
    - Ijazah
    - Transkrip Nilai Akhir
    - Surat Keterangan Lulus
    - Surat Pernyataan Telah Menyelesaikan Studi
  - Submit for verification
- **Permissions**: Upload final reports, submit for verification

#### **Completion: Study Finalization**
- **Navigation Access**: Kalender Studi Lanjut → Kelola Kalender Studi Lanjut
- **Actions**:
  - Request study completion (requires all final reports verified)
  - Or request study discontinuation if needed
- **Permissions**: Request completion or discontinuation

---

## 👨‍💼 Staf SDM & Keuangan (HR Finance Staff) - Administrative Journey

### 📋 Phase 1: Document Verification

#### **Daily: Document Verification**
- **Navigation Access**: Administrasi Dokumen → Cari & Pilih Dosen
- **Actions**:
  - Search for lecturers by name/ID
  - View submitted documents for verification
  - Verify or reject documents with comments
  - Process verification workflow (PENDING → VERIFIED/REJECTED)
- **Permissions**: Verify/reject academic documents, add comments

#### **Weekly: Approval Document Management**
- **Navigation Access**: Administrasi Dokumen → Unggah Persetujuan Studi Lanjut
- **Actions**:
  - Upload approval documents for lecturers:
    - Dokumen Kesesuaian Studi Lanjut
    - Berita Acara Studi Lanjut
    - NDE Studi Lanjut
    - Perjanjian Ikatan Dinas (PID)
  - Submit for management approval (DRAFT → PENDING_L1)
- **Permissions**: Upload approval documents, submit for management review

### 📋 Phase 2: Management Approval Support

#### **As Needed: Document Revision**
- **Navigation Access**: Administrasi Dokumen → Unggah Persetujuan Studi Lanjut
- **Actions**:
  - Revise rejected approval documents (REJECTED → DRAFT)
  - Resubmit for approval
- **Permissions**: Revise and resubmit approval documents

### 📋 Phase 3: Monitoring & Reporting

#### **Weekly: Status Monitoring**
- **Navigation Access**: Monitoring & Laporan → Status Dokumen
- **Actions**:
  - Monitor document verification status
  - Track incomplete documents
  - Review pending verifications
- **Permissions**: View all document statuses, generate reports

#### **Monthly: Report Generation**
- **Navigation Access**: Monitoring & Laporan → Laporan Dosen
- **Actions**:
  - Generate dosen reports per semester
  - Track dosen by study program
  - Monitor approaching study deadlines
- **Permissions**: Generate and view reports

---

## 🏢 Kepala Urusan SDM & Keuangan (Head of HR Finance) - Management Journey

### 📋 Phase 1: Document Verification Oversight

#### **Daily: Verification Oversight**
- **Navigation Access**: Administrasi Dokumen → Verifikasi Persyaratan
- **Actions**:
  - Oversee document verification process
  - Review verification decisions
  - Ensure compliance with policies
- **Permissions**: Oversee verification process, override decisions if needed

### 📋 Phase 2: Management Approval

#### **As Needed: Level 1 Approval**
- **Navigation Access**: Administrasi Dokumen → Persetujuan Manajemen
- **Actions**:
  - Review approval documents submitted by staff
  - Approve or reject at Level 1 (PENDING_L1 → PENDING_L2/REJECTED)
  - Provide detailed comments for decisions
- **Permissions**: Level 1 approval authority, add comments

### 📋 Phase 3: Strategic Monitoring

#### **Weekly: Strategic Reports**
- **Navigation Access**: Monitoring & Laporan → Dasbor Analitik
- **Actions**:
  - Review analytics dashboard
  - Analyze trends in study programs
  - Monitor completion rates
- **Permissions**: Access to all analytics and reports

#### **Monthly: Policy Review**
- **Navigation Access**: Monitoring & Laporan → Audit & Log
- **Actions**:
  - Review audit logs
  - Monitor system usage
  - Ensure policy compliance
- **Permissions**: Access to audit logs and system monitoring

---

## 🎯 Ketua Program Studi (Head of Study Program) - Academic Journey

### 📋 Phase 1: Study Calendar Approval

#### **Weekly: Calendar Review**
- **Navigation Access**: Kalender Studi Lanjut → Persetujuan Kalender Studi Lanjut
- **Actions**:
  - Review submitted study calendars
  - Approve or reject study plans (PENDING_APPROVAL → APPROVED/REJECTED)
  - Provide academic guidance and comments
- **Permissions**: Approve/reject study calendars, add comments

### 📋 Phase 2: Study Progress Monitoring

#### **Monthly: Progress Tracking**
- **Navigation Access**: Monitoring & Laporan → Laporan Dosen
- **Actions**:
  - Monitor dosen progress in study programs
  - Review semester reports
  - Track completion rates
- **Permissions**: View dosen progress, generate academic reports

### 📋 Phase 3: Study Management

#### **As Needed: Study Status Management**
- **Navigation Access**: Kalender Studi Lanjut → Persetujuan Kalender Studi Lanjut
- **Actions**:
  - Approve study start requests (APPROVED → ACTIVE)
  - Approve leave requests (ACTIVE → LEAVE)
  - Approve study completion (ACTIVE → FINISHED)
  - Approve study discontinuation (ACTIVE → DROP_OUT)
- **Permissions**: Manage study status transitions, add comments

### 📋 Phase 4: Academic Oversight

#### **Quarterly: Academic Review**
- **Navigation Access**: Monitoring & Laporan → Dasbor Analitik
- **Actions**:
  - Review academic performance metrics
  - Analyze study program effectiveness
  - Monitor graduation rates
- **Permissions**: Access to academic analytics and reports

---

## 🔬 Ketua Kelompok Keilmuan (Head of Research Group) - Research Journey

### 📋 Phase 1: Level 2 Approval

#### **As Needed: Final Approval**
- **Navigation Access**: Administrasi Dokumen → Persetujuan Manajemen
- **Actions**:
  - Review Level 1 approved documents
  - Provide final approval (PENDING_L2 → APPROVED)
  - Or reject with detailed comments (PENDING_L2 → REJECTED)
- **Permissions**: Level 2 approval authority, add comments

### 📋 Phase 2: Research Oversight

#### **Monthly: Research Monitoring**
- **Navigation Access**: Monitoring & Laporan → Laporan Dosen
- **Actions**:
  - Monitor research progress of dosen
  - Review publication submissions
  - Track research milestones
- **Permissions**: View research progress, generate research reports

### 📋 Phase 3: Strategic Research Planning

#### **Quarterly: Research Strategy**
- **Navigation Access**: Monitoring & Laporan → Dasbor Analitik
- **Actions**:
  - Analyze research trends
  - Review publication metrics
  - Plan research group development
- **Permissions**: Access to research analytics and strategic reports

---

## 🏛️ Wakil Dekan II FRI (FRI Vice Dean) - Executive Journey

### 📋 Phase 1: Executive Approval

#### **As Needed: Executive Decisions**
- **Navigation Access**: 
  - Kalender Studi Lanjut → Persetujuan Kalender Studi Lanjut
  - Administrasi Dokumen → Persetujuan Manajemen
- **Actions**:
  - Approve/reject study calendars (Level 1 authority)
  - Provide final approval for critical documents (Level 2 authority)
  - Make executive decisions on complex cases
- **Permissions**: Both Level 1 and Level 2 approval authority

### 📋 Phase 2: Strategic Oversight

#### **Weekly: Strategic Monitoring**
- **Navigation Access**: Monitoring & Laporan → Dasbor Analitik
- **Actions**:
  - Review overall faculty performance
  - Monitor strategic initiatives
  - Analyze institutional trends
- **Permissions**: Access to all analytics and strategic reports

### 📋 Phase 3: Policy & Compliance

#### **Monthly: Policy Review**
- **Navigation Access**: Monitoring & Laporan → Audit & Log
- **Actions**:
  - Review compliance reports
  - Monitor policy effectiveness
  - Ensure institutional standards
- **Permissions**: Access to all audit logs and compliance reports

### 📋 Phase 4: Institutional Leadership

#### **Quarterly: Strategic Planning**
- **Navigation Access**: Monitoring & Laporan → Laporan Dosen
- **Actions**:
  - Review institutional performance
  - Plan strategic initiatives
  - Make policy recommendations
- **Permissions**: Access to all institutional reports and analytics

---

## 🔄 Cross-Role Interactions & Workflow Integration

### 📋 Document Verification Flow
1. **Lecturer** submits document → **Staff** verifies → **Head of HR** oversees
2. **Staff** uploads approval document → **Head of Study Program** approves L1 → **Head of Research Group** approves L2
3. **Lecturer** submits study calendar → **Head of Study Program** or **Vice Dean** approves

### 📋 Study Management Flow
1. **Lecturer** requests study start → **Head of Study Program** or **Vice Dean** approves
2. **Lecturer** requests leave → **Head of Study Program** or **Vice Dean** approves
3. **Lecturer** requests completion → **Head of Study Program** or **Vice Dean** approves

### 📋 Monitoring & Reporting Flow
- **All Management Roles** have access to monitoring dashboards
- **Staff** focuses on operational reports
- **Academic Leaders** focus on academic metrics
- **Executives** focus on strategic analytics

---

## 📱 Navigation Menu Access Matrix

| Menu Section | Lecturer | Staff | Head HR | Head Prodi | Head KK | Vice Dean |
|--------------|----------|-------|---------|------------|---------|-----------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Data Pribadi | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Kalender Studi (Kelola) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Kalender Studi (Persetujuan) | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Dokumen Akademik | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Persetujuan Studi Lanjut | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Administrasi Dokumen | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Monitoring & Laporan | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Notifikasi | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Pengaturan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 🎯 Key Success Metrics by Role

### 📊 Lecturer Success Metrics
- Document submission completion rate
- Study calendar approval rate
- Semester report submission timeliness
- Study completion rate

### 📊 Staff Success Metrics
- Document verification turnaround time
- Approval document processing accuracy
- Report generation completeness
- System compliance rate

### 📊 Management Success Metrics
- Approval decision quality
- Policy compliance rate
- Strategic goal achievement
- Team performance metrics

### 📊 Executive Success Metrics
- Institutional performance indicators
- Strategic initiative success
- Policy effectiveness
- Stakeholder satisfaction 