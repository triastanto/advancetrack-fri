# Document & Report Verification State Machine

This state machine applies to:
- documents
- semester_reports
- service_bond_agreements

## States

| State ID | State Name | Type      | Description                                      | Color     | Icon         | Terminal |
|----------|------------|-----------|--------------------------------------------------|-----------|--------------|----------|
| 1        | DRAFT      | draft     | Document not yet submitted/uploaded              | secondary | edit         | No       |
| 2        | PENDING    | pending   | Awaiting verification by staff                   | warning   | clock        | No       |
| 3        | VERIFIED   | verified  | Approved/verified by staff (final state)        | success   | check-circle | Yes      |
| 4        | REJECTED   | rejected  | Not approved, needs revision                     | danger    | x-circle     | No       |

## Transitions

| Transition ID | Name      | Label                   | From State | To State | Required Roles                                                                                      | Requires Comment |
|---------------|-----------|-------------------------|------------|----------|-----------------------------------------------------------------------------------------------------|------------------|
| 1             | SUBMIT    | Submit for Verification | DRAFT (1)  | PENDING (2) | lecturer                                                                                            | No               |
| 2             | VERIFY    | Verify Document         | PENDING (2)| VERIFIED (3)| hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group | Yes              |
| 3             | REJECT    | Reject Document         | PENDING (2)| REJECTED (4)| hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group | Yes              |
| 4             | RESUBMIT  | Revise and Resubmit     | REJECTED (4)| PENDING (2)| lecturer                                                                                            | No               |

## State Flow Diagram

```
[DRAFT] --SUBMIT--> [PENDING]
           |             |
           |             +--VERIFY--> [VERIFIED] (terminal)
           |             |
           |             +--REJECT--> [REJECTED]
           |                              |
           +--RESUBMIT---------------------+
```

## Notification Rules

| Transition | Notified Parties |
|------------|------------------|
| SUBMIT     | Verification staff (hr_finance_staff, head_of_hr_finance, fri_vice_dean, head_of_study_program, head_of_research_group) |
| VERIFY     | Document owner |
| REJECT     | Document owner |
| RESUBMIT   | Verification staff |

## Email Templates

| Transition | Template |
|------------|----------|
| SUBMIT     | emails.document-submitted |
| VERIFY     | emails.document-verified |
| REJECT     | emails.document-rejected |
| RESUBMIT   | emails.document-resubmitted |

## Configuration Details

- **Initial State**: DRAFT (ID: 1)
- **Workflow Name**: `document_verification`
- **Settings**: History tracking enabled, auto-save enabled, strict mode enabled, auto-notify enabled
- **Notification Channels**: Mail, Database
- **Guards**: Role-based permissions enabled

## Notes

- **DRAFT** is the initial state where documents are created
- **VERIFIED** is a terminal state; no further transitions allowed
- **REJECT** and **VERIFY** transitions require comments from staff
- Only lecturers can submit/resubmit documents
- Only authorized staff can verify or reject documents
- Each transition triggers automatic notifications to relevant parties
