## Multiple Workflow Integartion Design

This approach separates the process into distinct, modular workflows for each major phase or document type. Each workflow is orchestrated as part of a unified process, with strict guards and integration points.

### Workflows Overview

#### Academic Document Workflow
- **Purpose:** Verification of study requirements, semester reports, and final reports.
- **States:** DRAFT → SUBMIT → PENDING → VERIFY/REJECT → VERIFIED/RESUBMIT
- **Transitions:** SUBMIT, VERIFY, REJECT, RESUBMIT
- **Actors:** Lecturer (submit), Staff (verify/reject)
- **Usage:**
  - Pre-Approval: Study requirements (e.g., LoA, Ijazah)
  - During Study: Semester reports
  - Completion: Final reports

#### Approval Document Workflow
- **Purpose:** Multi-level management approval for the study (e.g., Persetujuan Studi Lanjut).
- **States:** DRAFT → SUBMIT → PENDING_L1 → APPROVE_L1/REJECT_L1 → PENDING_L2 → APPROVE_L2/REJECT_L2 → APPROVED/REJECTED
- **Transitions:** SUBMIT, APPROVE_L1, APPROVE_L2, REJECT_L1, REJECT_L2, REVISE
- **Actors:** Staff (submit/revise), Head of Study Program (approve/reject L1), Head of Research Group (approve/reject L2)
- **Usage:** Gatekeeper before study can start.

#### Study Calendar Workflow
- **Purpose:** Governs the lecturer's study journey.
- **States:** DRAFT → PENDING_APPROVAL → APPROVED → ACTIVE → LEAVE → FINISHED/DROP_OUT/REJECTED
- **Transitions:** SUBMIT_STUDY, APPROVE_STUDY, REJECT_STUDY, RESUBMIT_STUDY, START_STUDY, TAKE_LEAVE, RETURN_FROM_LEAVE, COMPLETE_STUDY, DROP_OUT
- **Actors:** Lecturer, Supervisors

### Integration & Guards

- Study Calendar cannot move to ACTIVE until all required AcademicDocuments are VERIFIED and ApprovalDocument is APPROVED.
- ApprovalDocument cannot be submitted until all required AcademicDocuments are VERIFIED.
- Completion (FINISHED) requires all final reports to be VERIFIED.
- Each workflow has its own history and permissions.
- All transitions (e.g., START_STUDY, COMPLETE_STUDY) are guarded by checks on the status of required documents.

### Lifecycle Phases

#### Pre-Approval Phase
1. Lecturer creates a Study Calendar (DRAFT).
2. Lecturer uploads AcademicDocuments (study requirements).
3. Each AcademicDocument goes through verification_by_staff workflow.
4. All required AcademicDocuments must reach VERIFIED before proceeding.
5. Lecturer submits Study Calendar for approval (SUBMIT_STUDY → PENDING_APPROVAL).
6. Supervisors review: APPROVE_STUDY → APPROVED (with comment), REJECT_STUDY → REJECTED (with comment, can RESUBMIT)

#### ApprovalDocument Phase
1. Lecturer (or staff) uploads ApprovalDocument.
2. ApprovalDocument goes through verification_by_management workflow:
   - DRAFT → SUBMIT (by staff) → PENDING_L1 (head_of_study_program) → APPROVE_L1/REJECT_L1
   - If APPROVE_L1: PENDING_L2 (head_of_research_group) → APPROVE_L2/REJECT_L2
   - APPROVED is terminal; REJECTED can be revised.
3. All required AcademicDocuments must be VERIFIED before ApprovalDocument can be submitted.
4. ApprovalDocument must reach APPROVED before the study can start.

#### Study Execution Phase
1. Lecturer requests to start study (START_STUDY).
2. System checks:
   - Study Calendar is APPROVED.
   - All AcademicDocuments are VERIFIED.
   - ApprovalDocument is APPROVED.
   - If all conditions met: Study Calendar transitions to ACTIVE.
3. During ACTIVE:
   - Lecturer uploads Semester Reports (AcademicDocument, type: semester report).
   - Each goes through verification_by_staff workflow.
   - Lecturer can TAKE_LEAVE, RETURN_FROM_LEAVE, or DROP_OUT (with appropriate transitions and comments).

#### Completion Phase
1. Lecturer uploads Final Reports (AcademicDocument, type: final report).
2. Each goes through verification_by_staff workflow.
3. All required Final Reports must be VERIFIED.
4. Supervisor reviews and, if all requirements are met, transitions Study Calendar to FINISHED (COMPLETE_STUDY).
5. If study is discontinued: Supervisor or lecturer transitions to DROP_OUT (terminal).

### Flow Diagram

```
+-------------------+
|   DRAFT           |<-----------------------------+
| (Create Study     |                              |
|  Calendar & Docs) |                              |
+--------+----------+                              |
         |                                         |
         | SUBMIT STUDY CALENDAR                   |
         v                                         |
+------------------------+                         |
| PENDING APPROVAL       |                         |
| (Supervisor Review)    |                         |
+-----+-----------+------+                         |
      |           |                                |
      |           | REJECTED                       |
      |           v                                |
      |     +-----------+                          |
      |     | REJECTED  |                          |
      |     +-----+-----+                          |
      |           | RESUBMIT                       |
      +-----------+                                |
         |                                         |
         | APPROVED                                |
         v                                         |
+------------------------+                         |
| APPROVED               |                         |
| (Wait for Approval     |                         |
|  Document Verified)    |                         |
+-----------+------------+                         |
            |                                      |
            | ApprovalDocument:                    |
            |  - SUBMIT FOR VERIFICATION           |
            |  - VERIFIED BY STAFF                 |
            |  - MANAGEMENT APPROVAL (L1, L2)      |
            |  - APPROVED                          |
            v                                      |
+------------------------+                         |
| READY TO START STUDY   |                         |
+-----------+------------+                         |
            |                                      |
            | START STUDY                          |
            v                                      |
+------------------------+                         |
| ACTIVE                 |<---+                    |
+-----+----------+-------+    |                    |
      |          |            |                    |
      |          | TAKE LEAVE |                    |
      |          v            |                    |
      |     +---------+       |                    |
      |     | LEAVE   |-------+                    |
      |     +----+----+                            |
      |          | RETURN FROM LEAVE               |
      +----------+                                 |
      |                                            |
      | COMPLETE STUDY                             |
      v                                            |
+------------------------+                         |
| FINISHED (Terminal)    |                         |
+------------------------+                         |
      |                                            |
      | DROP OUT
      v                                            |
+------------------------+
| DROP OUT (Terminal)    |
+------------------------+

Legend:
- ApprovalDocument must be VERIFIED & APPROVED before START STUDY is allowed.
- All document and approval sub-workflows are integrated as gates in the main process.
```

### Pros & Cons

**Pros:**
- Modularity, reusability, easier maintenance, clear separation of concerns.

**Cons:**
- More complex orchestration, fragmented user experience, cross-workflow dependencies.

---

## 3. Summary Table: States & Transitions

| Workflow                | States                                                                 | Transitions                                               |
|-------------------------|------------------------------------------------------------------------|-----------------------------------------------------------|
| AcademicDocument        | DRAFT, SUBMIT, PENDING, VERIFY/REJECT, VERIFIED, RESUBMIT              | SUBMIT, VERIFY, REJECT, RESUBMIT                          |
| ApprovalDocument        | DRAFT, SUBMIT, PENDING_L1, APPROVE_L1/REJECT_L1, PENDING_L2, APPROVE_L2/REJECT_L2, APPROVED, REJECTED | SUBMIT, APPROVE_L1, APPROVE_L2, REJECT_L1, REJECT_L2, REVISE |
| StudyCalendar           | DRAFT, PENDING_APPROVAL, APPROVED, ACTIVE, LEAVE, FINISHED, DROP_OUT, REJECTED | SUBMIT_STUDY, APPROVE_STUDY, REJECT_STUDY, RESUBMIT_STUDY, START_STUDY, TAKE_LEAVE, RETURN_FROM_LEAVE, COMPLETE_STUDY, DROP_OUT |

---

## 4. Conclusion

- **Unified Workflow**: Simpler for users and reporting, but less modular and harder to maintain.
- **Multiple Workflows**: More modular and maintainable, but requires careful orchestration and may be more complex for users.

Choose the approach that best fits your system's needs for modularity, maintainability, and user experience.
