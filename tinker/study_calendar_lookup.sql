-- All employees showing study calendar status
SELECT
    u.id AS user_id,
    u.name AS employee_name,
    u.email AS user_email,
    e.id AS employee_id,
    e.nidn,
    e.position,
    e.role,
    e.functional_position,
    -- Study Calendar Status
    CASE
        WHEN sc.id IS NOT NULL THEN 'Has Study Calendar'
        ELSE 'No Study Calendar'
    END AS study_calendar_status,
    sc.id AS study_calendar_id,
    sc.study_start,
    sc.estimated_study_end,
    sc.graduation_date,
    sc.workflow_state,
    -- Study Details (if exists)
    sd.university_name,
    sd.study_program_id,
    sd.study_level,
    -- Research Organization
    rl.name AS research_lab_name,
    rg.name AS research_group_name,
    e.is_lab_head
FROM users u
INNER JOIN employees e ON u.id = e.user_id
LEFT JOIN study_calendars sc ON e.id = sc.employee_id
LEFT JOIN study_details sd ON sc.id = sd.study_calendar_id
LEFT JOIN research_labs rl ON e.research_lab_id = rl.id
LEFT JOIN research_groups rg ON rl.research_group_id = rg.id
ORDER BY
    CASE WHEN sc.id IS NOT NULL THEN 0 ELSE 1 END,
    u.name;