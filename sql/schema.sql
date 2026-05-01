CREATE DATABASE IF NOT EXISTS university
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE university;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'alumni', 'admin') NOT NULL,
    PRIMARY KEY (user_id),
    UNIQUE KEY uq_users_username (username),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS students (
    user_id INT UNSIGNED NOT NULL,
    programme VARCHAR(255) NOT NULL,
    expertise VARCHAR(255) NOT NULL,
    cv VARCHAR(255) NOT NULL,
    cgpa DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    email VARCHAR(255) NOT NULL,
    github VARCHAR(255) NOT NULL,
    linkedin VARCHAR(255) NOT NULL,
    sex ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    PRIMARY KEY (user_id),
    KEY idx_students_programme (programme),
    CONSTRAINT fk_students_user
        FOREIGN KEY (user_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alumni (
    user_id INT UNSIGNED NOT NULL,
    github VARCHAR(255) NOT NULL,
    linkedin VARCHAR(255) NOT NULL,
    sex ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    type ENUM('higher studies', 'corporate', 'self employed') NOT NULL DEFAULT 'higher studies',
    thesis VARCHAR(255) NOT NULL,
    university VARCHAR(255) NOT NULL,
    current_country VARCHAR(100) NOT NULL,
    degree_programme VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    role_title VARCHAR(255) NOT NULL,
    employment_start_date DATE NULL,
    location VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_theme VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id),
    KEY idx_alumni_company_name (company_name),
    KEY idx_alumni_type (type),
    CONSTRAINT fk_alumni_user
        FOREIGN KEY (user_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
    event_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_name VARCHAR(255) NOT NULL,
    event_description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    event_location VARCHAR(255) NOT NULL,
    PRIMARY KEY (event_id),
    KEY idx_events_event_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    message_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    message_content TEXT NOT NULL,
    sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (message_id),
    KEY idx_messages_sender_id (sender_id),
    KEY idx_messages_receiver_id (receiver_id),
    KEY idx_messages_sent_at (sent_at),
    CONSTRAINT fk_messages_sender
        FOREIGN KEY (sender_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_messages_receiver
        FOREIGN KEY (receiver_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS connection_requests (
    request_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    student_id INT UNSIGNED NOT NULL,
    alumni_id INT UNSIGNED NOT NULL,
    message TEXT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (request_id),
    KEY idx_connection_requests_student_status (student_id, status),
    KEY idx_connection_requests_alumni_status (alumni_id, status),
    KEY idx_connection_requests_lookup (student_id, alumni_id, status),
    KEY idx_connection_requests_created_at (created_at),
    CONSTRAINT fk_connection_requests_student
        FOREIGN KEY (student_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_connection_requests_alumni
        FOREIGN KEY (alumni_id) REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
