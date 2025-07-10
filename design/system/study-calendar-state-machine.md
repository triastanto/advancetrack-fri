# Study Calendar State Machine

This state machine applies to:
- Kalender Studi Lanjut Dosen (`study_calendars`)

## States

| State ID | State Name       | Type      | Description                                 | Color     | Icon         | Terminal |
|----------|------------------|-----------|---------------------------------------------|-----------|--------------|----------|
| 1        | DRAFT            | draft     | Draft Calendar                             | secondary | edit         | No       |
| 2        | PENDING_APPROVAL | pending   | Pending Approval                           | warning   | clock        | No       |
| 3        | APPROVED         | approved  | Calendar Approved                          | info      | check-circle | No       |
| 4        | REJECTED         | rejected  | Calendar Rejected                          | danger    | x-circle     | No       |
| 5        | ACTIVE           | active    | Currently Studying                         | success   | book-open    | No       |
| 6        | LEAVE            | leave     | On Official Leave                          | warning   | pause-circle | No       |
| 7        | FINISHED         | finished  | Study Completed                            | success   | award        | Yes      |
| 8        | DROP_OUT         | drop_out  | Study Discontinued                         | danger    | x-circle     | Yes      |

## Transitions

| Transition ID | Name                | Label                               | From State         | To State           | Required Roles                                          | Requires Comment |
|---------------|---------------------|-------------------------------------|--------------------|--------------------|--------------------------------------------------------|------------------|
| 1             | SUBMIT_STUDY        | Submit Study Calendar for Approval  | DRAFT (1)          | PENDING_APPROVAL (2) | lecturer                                               | No               |
| 2             | APPROVE_STUDY       | Approve Study Calendar              | PENDING_APPROVAL (2) | APPROVED (3)       | head_of_study_program, fri_vice_dean                   | Yes              |
| 3             | REJECT_STUDY        | Reject Study Calendar               | PENDING_APPROVAL (2) | REJECTED (4)       | head_of_study_program, fri_vice_dean                   | Yes              |
| 4             | RESUBMIT_STUDY      | Revise and Resubmit Study Calendar  | REJECTED (4)       | PENDING_APPROVAL (2) | lecturer                                               | No               |
| 5             | START_STUDY         | Begin Study Program                 | APPROVED (3)       | ACTIVE (5)         | lecturer, head_of_study_program, fri_vice_dean         | Yes              |
| 6             | TAKE_LEAVE          | Take Official Leave                 | ACTIVE (5)         | LEAVE (6)          | lecturer, head_of_study_program, fri_vice_dean         | Yes              |
| 7             | RETURN_FROM_LEAVE   | Return from Leave                   | LEAVE (6)          | ACTIVE (5)         | lecturer, head_of_study_program, fri_vice_dean         | Yes              |
| 8             | COMPLETE_STUDY      | Complete Study                      | ACTIVE (5)         | FINISHED (7)       | head_of_study_program, fri_vice_dean                   | Yes              |
| 9             | DROP_OUT_ACTIVE     | Discontinue Study                   | ACTIVE (5)         | DROP_OUT (8)       | lecturer, head_of_study_program, fri_vice_dean         | Yes              |
| 10            | DROP_OUT_LEAVE      | Discontinue Study (from Leave)      | LEAVE (6)          | DROP_OUT (8)       | head_of_study_program, fri_vice_dean                   | Yes              |

## State Flow Diagram

```
[DRAFT] --SUBMIT_STUDY--> [PENDING_APPROVAL]
                               |         |
                               |         +--APPROVE_STUDY--> [APPROVED]
                               |         |                      |
                               |         +--REJECT_STUDY--> [REJECTED]
                               |                                |
                               +--RESUBMIT_STUDY---------------+

[APPROVED] --START_STUDY--> [ACTIVE] --TAKE_LEAVE--> [LEAVE]
                             |            |             |
                             |            +--RETURN_FROM_LEAVE--> (back to ACTIVE)
                             |                          |
                             +--COMPLETE_STUDY--> [FINISHED] (terminal)
                             |                          |
                             +--DROP_OUT_ACTIVE--> [DROP_OUT] (terminal)
                                                        |
                                                        +--DROP_OUT_LEAVE-- (from LEAVE)
```

## Workflow Phases

### Phase 1: Study Calendar Submission & Approval
1. **DRAFT** → **PENDING_APPROVAL**: Student submits study calendar
2. **PENDING_APPROVAL** → **APPROVED**: Supervisor approves study calendar
3. **PENDING_APPROVAL** → **REJECTED**: Supervisor rejects study calendar
4. **REJECTED** → **PENDING_APPROVAL**: Student revises and resubmits study calendar

### Phase 2: Study Execution
5. **APPROVED** → **ACTIVE**: Student begins study program
6. **ACTIVE** → **LEAVE**: Student takes official leave
7. **LEAVE** → **ACTIVE**: Student returns from leave

### Phase 3: Study Completion
8. **ACTIVE** → **FINISHED**: Study successfully completed
9. **ACTIVE** → **DROP_OUT**: Study discontinued
10. **LEAVE** → **DROP_OUT**: Study discontinued while on leave

## Notification Rules

| Transition          | Notified Parties |
|---------------------|------------------|
| SUBMIT_STUDY        | Approvers (head_of_study_program, fri_vice_dean) |
| APPROVE_STUDY       | Student |
| REJECT_STUDY        | Student |
| RESUBMIT_STUDY      | Approvers (head_of_study_program, fri_vice_dean) |
| START_STUDY         | Student, Supervisors, Admin (head_of_hr_finance, hr_finance_staff) |
| TAKE_LEAVE          | Student, Supervisors (head_of_study_program, fri_vice_dean) |
| RETURN_FROM_LEAVE   | Student, Supervisors (head_of_study_program, fri_vice_dean) |
| COMPLETE_STUDY      | Student, Supervisors, Admin (head_of_hr_finance, hr_finance_staff) |
| DROP_OUT_ACTIVE     | Student, Supervisors, Admin (head_of_hr_finance, hr_finance_staff) |
| DROP_OUT_LEAVE      | Student, Supervisors, Admin (head_of_hr_finance, hr_finance_staff) |

## Email Templates

| Transition          | Template |
|---------------------|----------|
| SUBMIT_STUDY        | emails.study-submitted |
| APPROVE_STUDY       | emails.study-approved |
| REJECT_STUDY        | emails.study-rejected |
| RESUBMIT_STUDY      | emails.study-resubmitted |
| START_STUDY         | emails.study-started |
| TAKE_LEAVE          | emails.study-leave-started |
| RETURN_FROM_LEAVE   | emails.study-leave-ended |
| COMPLETE_STUDY      | emails.study-completed |
| DROP_OUT_ACTIVE     | emails.study-discontinued |
| DROP_OUT_LEAVE      | emails.study-discontinued |

## Configuration Details

- **Initial State**: DRAFT (ID: 1)
- **Workflow Name**: `study_calendar`
- **Settings**: History tracking enabled, auto-save enabled, strict mode enabled, auto-notify enabled
- **Notification Channels**: Mail, Database
- **Guards**: Role-based permissions enabled

## Notes

- **DRAFT** is the initial state where students prepare their study calendar
- **FINISHED** and **DROP_OUT** are terminal states; no further transitions allowed
- Students must get their calendar approved before they can start studying
- The workflow has three distinct phases: submission/approval, study execution, and completion
- All transitions (except submissions and resubmissions) require comments for proper documentation and audit trail
- Students (lecturers) can submit and resubmit calendars, and participate in study status changes
- Only authorized supervisors can approve/reject calendars and mark studies as completed or discontinued
- Study discontinuation can happen from both ACTIVE and LEAVE states
- Each transition triggers automatic notifications to relevant parties including the student, supervisors, and administrative staff when appropriate
