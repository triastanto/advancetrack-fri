# 🔄 Complete Workflow Integration & User Journey Flow

## 📊 Overall System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           STUDY ADVANCEMENT SYSTEM                            │
│                              Complete User Journey                            │
└─────────────────────────────────────────────────────────────────────────────────┘

PHASE 1: INITIAL SETUP & DOCUMENT SUBMISSION
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🎓 LECTURER JOURNEY                                                          │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Week 1-2: Account Setup                                                      │
│ ├── Dashboard (View notifications)                                            │
│ ├── Data Pribadi (Complete profile)                                          │
│ └── Riwayat Pendidikan (Upload background)                                   │
│                                                                               │
│ Week 3-4: Study Calendar Creation                                            │
│ ├── Masa Studi → Kelola Masa Studi Lanjut                           │
│ ├── Create study calendar (DRAFT state)                                      │
│ ├── Define timeline and milestones                                           │
│ └── Select study program from available options                              │
│                                                                               │
│ Week 5-8: Document Collection                                                │
│ ├── Dokumen Akademik → Persyaratan Studi Lanjut                             │
│ ├── Upload required documents (10 documents)                                 │
│ ├── Submit each for verification (DRAFT → PENDING)                          │
│ └── Track verification status                                                │
│                                                                               │
│ Week 9-10: Calendar Submission                                               │
│ ├── Finalize study calendar with study program details                       │
│ ├── Submit for approval (DRAFT → PENDING_APPROVAL)                          │
│ └── Wait for supervisor approval                                             │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 👨‍💼 STAFF JOURNEY (Parallel)                                                │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Daily: Document Verification                                                  │
│ ├── Administrasi Dokumen → Cari & Pilih Dosen                               │
│ ├── Search for lecturers                                                     │
│ ├── Verify/reject documents (PENDING → VERIFIED/REJECTED)                   │
│ └── Add verification comments                                                │
│                                                                               │
│ Weekly: Approval Document Management                                          │
│ ├── Administrasi Dokumen → Unggah Persetujuan Studi Lanjut                  │
│ ├── Upload approval documents (4 documents)                                  │
│ ├── Submit for management approval (DRAFT → PENDING_L1)                     │
│ └── Track approval status                                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
PHASE 2: APPROVAL & STUDY PREPARATION
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🎯 HEAD OF STUDY PROGRAM JOURNEY                                             │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Weekly: Study Calendar Review                                                │
│ ├── Masa Studi → Persetujuan Masa Studi Lanjut                      │
│ ├── Review submitted calendars with study program details                     │
│ ├── Approve/reject (PENDING_APPROVAL → APPROVED/REJECTED)                   │
│ └── Provide academic guidance                                                │
│                                                                               │
│ As Needed: Level 1 Approval                                                  │
│ ├── Administrasi Dokumen → Persetujuan Manajemen                            │
│ ├── Review approval documents                                                │
│ ├── Approve/reject (PENDING_L1 → PENDING_L2/REJECTED)                      │
│ └── Add detailed comments                                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🔬 HEAD OF RESEARCH GROUP JOURNEY                                            │
├─────────────────────────────────────────────────────────────────────────────────┤
│ As Needed: Level 2 Approval                                                  │
│ ├── Administrasi Dokumen → Persetujuan Manajemen                            │
│ ├── Review Level 1 approved documents                                        │
│ ├── Final approval (PENDING_L2 → APPROVED)                                  │
│ └── Or reject with comments (PENDING_L2 → REJECTED)                         │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🏛️ VICE DEAN JOURNEY (Alternative Path)                                     │
├─────────────────────────────────────────────────────────────────────────────────┤
│ As Needed: Executive Decisions                                               │
│ ├── Masa Studi → Persetujuan Masa Studi Lanjut                      │
│ ├── Administrasi Dokumen → Persetujuan Manajemen                            │
│ ├── Approve/reject study calendars (Level 1)                                │
│ ├── Provide final approval (Level 2)                                        │
│ └── Make executive decisions on complex cases                                │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
PHASE 3: STUDY COMMENCEMENT
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🎓 LECTURER JOURNEY (Continued)                                             │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Week 15-16: Study Start                                                      │
│ ├── Masa Studi → Kelola Masa Studi Lanjut                           │
│ ├── Request study start (APPROVED → ACTIVE)                                 │
│ ├── Begin actual study program                                               │
│ └── Start regular semester reporting                                         │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
PHASE 4: ACTIVE STUDY PERIOD
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🎓 LECTURER JOURNEY (Ongoing)                                               │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Monthly: Semester Reports                                                    │
│ ├── Dokumen Akademik → Laporan Per Semester (LKS)                          │
│ ├── Upload semester reports (6 documents)                                   │
│ ├── Submit for verification (DRAFT → PENDING)                               │
│ └── Track verification status                                                │
│                                                                               │
│ As Needed: Leave Management                                                  │
│ ├── Masa Studi → Kelola Masa Studi Lanjut                           │
│ ├── Request leave (ACTIVE → LEAVE)                                          │
│ ├── Return from leave (LEAVE → ACTIVE)                                      │
│ └── Track leave status                                                       │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 👨‍💼 STAFF JOURNEY (Ongoing)                                                │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Daily: Ongoing Verification                                                  │
│ ├── Verify semester reports                                                  │
│ ├── Process leave requests                                                   │
│ ├── Monitor study progress                                                   │
│ └── Generate progress reports                                                │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
PHASE 5: STUDY COMPLETION
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🎓 LECTURER JOURNEY (Final Phase)                                           │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Final Semester: Final Reports                                                │
│ ├── Dokumen Akademik → Laporan Akhir & Kelulusan                           │
│ ├── Upload final documents (4 documents)                                    │
│ ├── Submit for verification (DRAFT → PENDING)                               │
│ └── Track verification status                                                │
│                                                                               │
│ Completion: Study Finalization                                               │
│ ├── Masa Studi → Kelola Masa Studi Lanjut                           │
│ ├── Request completion (ACTIVE → FINISHED)                                  │
│ ├── Or request discontinuation (ACTIVE → DROP_OUT)                          │
│ └── Complete study program                                                   │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
PHASE 6: MONITORING & REPORTING (Continuous)
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🏢 HEAD OF HR FINANCE JOURNEY                                               │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Weekly: Strategic Monitoring                                                 │
│ ├── Monitoring & Laporan → Dasbor Analitik                                  │
│ ├── Review analytics dashboard                                               │
│ ├── Analyze trends in study programs                                         │
│ └── Monitor completion rates                                                 │
│                                                                               │
│ Monthly: Policy Review                                                       │
│ ├── Monitoring & Laporan → Audit & Log                                      │
│ ├── Review audit logs                                                        │
│ ├── Monitor system usage                                                     │
│ └── Ensure policy compliance                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 🏛️ VICE DEAN JOURNEY (Strategic Oversight)                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│ Quarterly: Strategic Planning                                                │
│ ├── Monitoring & Laporan → Laporan Dosen                                    │
│ ├── Review institutional performance                                         │
│ ├── Plan strategic initiatives                                               │
│ └── Make policy recommendations                                              │
└─────────────────────────────────────────────────────────────────────────────────┘
```

## 🔄 Detailed Workflow State Transitions

### 📋 Document Verification Workflow (`verification_by_staff`)
```
DRAFT → PENDING → VERIFIED/REJECTED
  │        │           │
  │        │           └── RESUBMIT → PENDING
  │        └── VERIFY/REJECT
  └── SUBMIT
```

### 📋 Management Approval Workflow (`verification_by_management`)
```
DRAFT → PENDING_L1 → PENDING_L2 → APPROVED
  │         │            │
  │         │            └── REJECT_L2 → REJECTED
  │         └── REJECT_L1 → REJECTED
  └── SUBMIT
```

### 📋 Study Calendar Workflow (`study_calendar`)
```
DRAFT → PENDING_APPROVAL → APPROVED → ACTIVE → FINISHED
  │            │              │         │
  │            └── REJECTED   │         └── DROP_OUT
  │                          │
  └── SUBMIT_STUDY           └── LEAVE
```

## 🎯 Decision Gates & Integration Points

### 📋 Gate 1: Document Verification Complete
- **Trigger**: All required academic documents reach VERIFIED state
- **Action**: Enable study calendar submission
- **Roles**: Lecturer, Staff, Head of HR

### 📋 Gate 2: Study Calendar Approved
- **Trigger**: Study calendar reaches APPROVED state
- **Action**: Enable approval document submission
- **Roles**: Lecturer, Head of Study Program, Vice Dean

### 📋 Gate 3: Approval Documents Complete
- **Trigger**: Approval documents reach APPROVED state
- **Action**: Enable study start request
- **Roles**: Staff, Head of Study Program, Head of Research Group

### 📋 Gate 4: Study Start Approved
- **Trigger**: Study calendar transitions to ACTIVE state
- **Action**: Enable semester reporting
- **Roles**: Lecturer, Head of Study Program, Vice Dean

### 📋 Gate 5: Final Reports Complete
- **Trigger**: All final reports reach VERIFIED state
- **Action**: Enable study completion request
- **Roles**: Lecturer, Staff, Head of Study Program

## 📊 Role Interaction Matrix

| Phase | Lecturer | Staff | Head HR | Head Prodi | Head KK | Vice Dean |
|-------|----------|-------|---------|------------|---------|-----------|
| **Setup** | Create profile, calendar | - | - | - | - | - |
| **Document Upload** | Upload documents | Verify documents | Oversee verification | - | - | - |
| **Calendar Approval** | Submit calendar | - | - | Approve/reject | - | Approve/reject |
| **Approval Documents** | - | Upload documents | Level 1 approval | Level 1 approval | Level 2 approval | Level 2 approval |
| **Study Start** | Request start | - | - | Approve start | - | Approve start |
| **Active Study** | Submit reports, manage leave | Verify reports | Monitor progress | Monitor progress | Monitor research | Strategic oversight |
| **Completion** | Submit final reports | Verify reports | - | Approve completion | - | Approve completion |
| **Monitoring** | View own status | Generate reports | Strategic monitoring | Academic monitoring | Research monitoring | Institutional oversight |

## 🔔 Notification Flow

### 📧 Email Notifications by Transition
1. **SUBMIT** → Notify verification staff
2. **VERIFY** → Notify document owner
3. **REJECT** → Notify document owner
4. **RESUBMIT** → Notify verification staff
5. **SUBMIT_STUDY** → Notify approvers
6. **APPROVE_STUDY** → Notify student
7. **START_STUDY** → Notify student, supervisors, admin
8. **COMPLETE_STUDY** → Notify student, supervisors, admin

### 📱 In-App Notifications
- Real-time status updates
- Pending action reminders
- Deadline notifications
- Approval request alerts

## 🎯 Success Metrics by Phase

### 📊 Phase 1 Success Metrics
- **Lecturer**: Profile completion rate, document submission rate
- **Staff**: Verification turnaround time, accuracy rate
- **Management**: Approval decision quality, policy compliance

### 📊 Phase 2 Success Metrics
- **Lecturer**: Calendar approval rate, document verification rate
- **Academic Leaders**: Approval decision quality, guidance effectiveness
- **Management**: Approval process efficiency, compliance rate

### 📊 Phase 3 Success Metrics
- **Lecturer**: Study start success rate, semester report timeliness
- **Staff**: Report verification efficiency, monitoring accuracy
- **Management**: Progress tracking effectiveness, intervention success

### 📊 Phase 4 Success Metrics
- **Lecturer**: Study completion rate, report submission rate
- **Academic Leaders**: Completion rate, academic quality
- **Management**: Strategic goal achievement, institutional performance

## 🔧 System Integration Points

### 📋 Database Integration
- User role management
- Document storage and retrieval
- Workflow state tracking
- Notification system
- Audit logging

### 📋 External System Integration
- Email service for notifications
- File storage for documents
- Authentication system
- Reporting and analytics

### 📋 API Endpoints
- Workflow state transitions
- Document upload/download
- User management
- Reporting and analytics
- Notification management 