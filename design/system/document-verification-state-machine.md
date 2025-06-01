# Document & Report Verification State Machine

This state machine applies to:
- documents
- semester_reports
- service_bond_agreements

## States

| State    | Description                                      |
|----------|--------------------------------------------------|
| draft    | (optional) Document not yet submitted/uploaded   |
| pending  | Awaiting verification by staff                   |
| verified | Approved/verified by staff (final state)         |
| rejected | Not approved, needs revision                     |

## Transitions

| From     | To        | Trigger/Action                |
|----------|-----------|------------------------------|
| draft    | pending   | User submits/uploads         |
| pending  | verified  | Staff verifies/approves      |
| pending  | rejected  | Staff rejects (with note)    |
| rejected | pending   | User revises and resubmits   |
| verified |           | (Final state, no transition) |

## Diagram (Text)

```
[draft] --submit/upload--> [pending]
[pending] --approve--> [verified]
[pending] --reject--> [rejected]
[rejected] --revise/resubmit--> [pending]
[verified] (final)
```

## Notes
- "draft" is optional and may be skipped if documents are always submitted directly.
- "verified" is a terminal state; no further transitions allowed.
- Each transition may include metadata (e.g., verification note, timestamp).
