# Study Calendar State Machine

This state machine applies to:
- study_calendars

## States

| State     | Description                                 |
|-----------|---------------------------------------------|
| active    | Currently studying                          |
| leave     | On official leave                           |
| finished  | Study completed                             |
| drop_out  | Study discontinued (resigned/failed, etc.)  |

## Transitions

| From    | To        | Trigger/Action                                 |
|---------|-----------|------------------------------------------------|
| active  | leave     | User/staff updates status to leave             |
| leave   | active    | User/staff updates status to return            |
| active  | finished  | Study completed                                |
| active  | drop_out  | Study discontinued                             |
| leave   | drop_out  | Discontinued while on leave                    |

## Diagram (Text)

```
[active] --leave--> [leave]
[leave] --return--> [active]
[active] --complete--> [finished]
[active] --drop out--> [drop_out]
[leave] --drop out--> [drop_out]
[finished] (final)
[drop_out] (final)
```

## Notes
- "finished" and "drop_out" are terminal states.
- Only staff or authorized users can update the study status.
