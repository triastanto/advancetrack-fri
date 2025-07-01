# Management Multi-Level Approval State Machine

This state machine applies to:
- management-level documents (e.g., service bond agreements, study approval, etc.)

## States

| State ID | State Name    | Type         | Description                  | Color     | Icon         | Terminal |
|----------|---------------|--------------|------------------------------|-----------|--------------|----------|
| 1        | DRAFT         | draft        | Initial document draft       | secondary | edit         | No       |
| 2        | PENDING_L1    | pending_l1   | Pending first level approval | info      | user-check   | No       |
| 3        | PENDING_L2    | pending_l2   | Pending second level approval| primary   | shield       | No       |
| 4        | APPROVED      | approved     | Final approved state         | success   | check-circle | Yes      |
| 5        | REJECTED      | rejected     | Rejected document            | danger    | x-circle     | No       |

## Transitions

| Transition ID | Name        | Label                        | From State   | To State     | Required Roles           | Requires Comment |
|---------------|-------------|------------------------------|--------------|--------------|--------------------------|------------------|
| 1             | SUBMIT      | Submit for Level 1 Approval  | DRAFT (1)    | PENDING_L1 (2) | hr_finance_staff         | No               |
| 2             | APPROVE_L1  | Approve (Level 1)            | PENDING_L1 (2)| PENDING_L2 (3)| head_of_study_program    | Yes              |
| 3             | APPROVE_L2  | Approve (Level 2)            | PENDING_L2 (3)| APPROVED (4)  | head_of_research_group   | Yes              |
| 4             | REJECT_L1   | Reject Document (Level 1)    | PENDING_L1 (2)| REJECTED (5)  | head_of_study_program    | Yes              |
| 5             | REJECT_L2   | Reject Document (Level 2)    | PENDING_L2 (3)| REJECTED (5)  | head_of_research_group   | Yes              |
| 6             | REVISE      | Revise Document              | REJECTED (5) | DRAFT (1)     | hr_finance_staff         | No               |

## State Flow Diagram

```
[DRAFT] --SUBMIT--> [PENDING_L1]
           |             |
           |             +--APPROVE_L1--> [PENDING_L2]
           |             |                    |
           |             |                    +--APPROVE_L2--> [APPROVED] (terminal)
           |             |
           |             +--REJECT_L1--> [REJECTED]
           |                                   |
           +--REVISE---------------------------+
[PENDING_L2] +--REJECT_L2--> [REJECTED]
```

## Notification Rules

| Transition   | Notified Parties |
|--------------|------------------|
| SUBMIT       | Level 1 Approvers (head_of_study_program) |
| APPROVE_L1   | Level 2 Approvers (head_of_research_group) |
| APPROVE_L2   | Document owner |
| REJECT_L1    | Document owner |
| REJECT_L2    | Document owner |
| REVISE       | Reviewers (hr_finance_staff) |

## Email Templates

| Transition   | Template |
|--------------|----------|
| SUBMIT       | emails.document-sent-for-level-one-review |
| APPROVE_L1   | emails.document-sent-for-level-two-review |
| APPROVE_L2   | emails.document-fully-approved |
| REJECT_L1    | emails.document-rejected |
| REJECT_L2    | emails.document-rejected |
| REVISE       | emails.document-resubmitted |

## Configuration Details

- **Initial State**: DRAFT (ID: 1)
- **Workflow Name**: `verification_by_management`
- **Settings**: History tracking enabled, auto-save enabled, strict mode enabled, auto-notify enabled
- **Notification Channels**: Mail, Database
- **Guards**: Role-based permissions enabled

## Notes

- **DRAFT** is the initial state where documents are created
- **APPROVED** is a terminal state; no further transitions allowed
- **APPROVE_L1**, **APPROVE_L2**, **REJECT_L1**, and **REJECT_L2** transitions require comments from approvers
- Only hr_finance_staff can submit/revise documents
- Only authorized approvers can approve or reject documents at each level
- Each transition triggers automatic notifications to relevant parties
