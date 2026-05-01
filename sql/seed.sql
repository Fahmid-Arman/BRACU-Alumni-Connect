USE university;

INSERT INTO users (user_id, first_name, last_name, username, password, role)
VALUES
    (1, 'Admin', 'Demo', 'admin_demo', '$2y$10$fy5ccK8xafRCQUyPlz530./RTK2CDFEGRT4z2QcwGZQyakh5kdM9e', 'admin'),
    (2, 'Nafisa', 'Rahman', 'student_demo', '$2y$10$XPAuk.nMjmhgkfslKNEUa.HEzgXSIEvAGprtR/4ixs.uV9jklB2Pi', 'student'),
    (3, 'Tanvir', 'Ahmed', 'alumni_demo', '$2y$10$RESr5AZ5dEcEniTi8DiW1OmyAxM9i/oJZSfD/UqpGJGmNGJ7vexuq', 'alumni')
ON DUPLICATE KEY UPDATE
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    username = VALUES(username),
    password = VALUES(password),
    role = VALUES(role);

INSERT INTO students (
    user_id,
    programme,
    expertise,
    cv,
    cgpa,
    email,
    github,
    linkedin,
    sex,
    city,
    country,
    zip_code
)
VALUES
    (
        2,
        'Computer Science',
        'Web Development, SQL, UI Prototyping',
        'Not Provided',
        3.78,
        'student_demo@bracu.ac.bd',
        'https://github.com/student-demo',
        'https://www.linkedin.com/in/student-demo',
        'female',
        'Dhaka',
        'Bangladesh',
        '1212'
    )
ON DUPLICATE KEY UPDATE
    programme = VALUES(programme),
    expertise = VALUES(expertise),
    cv = VALUES(cv),
    cgpa = VALUES(cgpa),
    email = VALUES(email),
    github = VALUES(github),
    linkedin = VALUES(linkedin),
    sex = VALUES(sex),
    city = VALUES(city),
    country = VALUES(country),
    zip_code = VALUES(zip_code);

INSERT INTO alumni (
    user_id,
    github,
    linkedin,
    sex,
    city,
    country,
    zip_code,
    type,
    thesis,
    university,
    current_country,
    degree_programme,
    field_of_study,
    company_name,
    role_title,
    employment_start_date,
    location,
    business_name,
    business_theme
)
VALUES
    (
        3,
        'https://github.com/alumni-demo',
        'https://www.linkedin.com/in/alumni-demo',
        'male',
        'Dhaka',
        'Bangladesh',
        '1207',
        'corporate',
        'Scalable Community Platforms for University Networks',
        'BRAC University',
        'Bangladesh',
        'Computer Science',
        'Software Engineering',
        'Optimizely',
        'Software Engineer',
        '2023-01-15',
        'Dhaka, Bangladesh',
        'Not Set',
        'Not Set'
    )
ON DUPLICATE KEY UPDATE
    github = VALUES(github),
    linkedin = VALUES(linkedin),
    sex = VALUES(sex),
    city = VALUES(city),
    country = VALUES(country),
    zip_code = VALUES(zip_code),
    type = VALUES(type),
    thesis = VALUES(thesis),
    university = VALUES(university),
    current_country = VALUES(current_country),
    degree_programme = VALUES(degree_programme),
    field_of_study = VALUES(field_of_study),
    company_name = VALUES(company_name),
    role_title = VALUES(role_title),
    employment_start_date = VALUES(employment_start_date),
    location = VALUES(location),
    business_name = VALUES(business_name),
    business_theme = VALUES(business_theme);

INSERT INTO events (
    event_id,
    event_name,
    event_description,
    event_date,
    event_location
)
VALUES
    (
        1,
        'Alumni Mentorship Meetup',
        'A networking session for students to connect with BRACU alumni mentors from industry and graduate programs.',
        '2030-06-15 18:00:00',
        'BRAC University Auditorium'
    ),
    (
        2,
        'Career Pathways Panel',
        'An alumni panel on career growth, internships, and transitioning from campus to the workplace.',
        '2030-07-20 16:30:00',
        'Online via Zoom'
    )
ON DUPLICATE KEY UPDATE
    event_name = VALUES(event_name),
    event_description = VALUES(event_description),
    event_date = VALUES(event_date),
    event_location = VALUES(event_location);

INSERT INTO messages (
    message_id,
    sender_id,
    receiver_id,
    message_content,
    sent_at
)
VALUES
    (
        1,
        3,
        2,
        'Hello! I saw your profile and would be happy to connect regarding software engineering and internship preparation.',
        '2030-05-01 10:00:00'
    )
ON DUPLICATE KEY UPDATE
    sender_id = VALUES(sender_id),
    receiver_id = VALUES(receiver_id),
    message_content = VALUES(message_content),
    sent_at = VALUES(sent_at);

INSERT INTO connection_requests (
    request_id,
    student_id,
    alumni_id,
    message,
    status,
    created_at,
    updated_at
)
VALUES
    (
        1,
        2,
        3,
        'Hello! I would love to learn more about your transition from BRACU to software engineering roles and get mentorship advice.',
        'pending',
        '2030-05-02 09:00:00',
        '2030-05-02 09:00:00'
    )
ON DUPLICATE KEY UPDATE
    student_id = VALUES(student_id),
    alumni_id = VALUES(alumni_id),
    message = VALUES(message),
    status = VALUES(status),
    created_at = VALUES(created_at),
    updated_at = VALUES(updated_at);
