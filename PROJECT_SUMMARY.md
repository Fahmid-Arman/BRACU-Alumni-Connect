# Project Summary

## Problem Solved

BRACU Alumni Connect solves a simple but real campus problem: students often know alumni exist, but they do not have an organized platform for discovering them, reaching out safely, and requesting mentorship. This project turns that problem into a database-driven platform with role-based access, profile browsing, messaging, events, and connection request workflows.

## User Roles

- `Student`: searches alumni, views alumni profiles, messages alumni, sends connection requests, tracks sent requests
- `Alumni`: searches students, views student profiles, messages students, manages received connection requests
- `Admin`: manages users and alumni events

## Main Modules

- Authentication and session management
- Student and alumni profile management
- Alumni discovery and student discovery
- Public view-only profile pages
- Direct messaging
- Mentorship / connection request lifecycle
- Admin event management

## Database Tables

- `users`
- `students`
- `alumni`
- `events`
- `messages`
- `connection_requests`

## Security Improvements Implemented

- Hashed passwords with `password_hash()`
- Login verification with `password_verify()`
- Session regeneration after login
- Role-based access control
- Prepared statements for high-risk queries
- CSRF protection for state-changing POST requests
- Output escaping helper for rendered data
- PDF-only CV upload validation with size limit and safe filenames
- Basic validation for user IDs, email fields, and event date input

## What I Learned

- How to split shared user identity data from role-specific profile tables
- How to turn a database class project into a more complete application flow
- How prepared statements and CSRF protection improve basic PHP security
- How to model workflow state with a table like `connection_requests`
- How product features such as discovery, messaging, and events map back to relational schema design

## How To Explain The Project In 60 Seconds

BRACU Alumni Connect is a PHP and MySQL alumni networking platform I built to explore database design and secure web app fundamentals. The system supports three roles: students, alumni, and admins. Students can search alumni, view public profiles, message them, and send mentorship requests. Alumni can search students, respond to requests, and stay engaged through events. Admins manage users and alumni events. From a technical perspective, the project highlights normalized relational schema design, role-based workflows, prepared statements, password hashing, CSRF protection, and practical CRUD features built without a framework.

## Possible Interview Questions

### 1. Why did you separate `users`, `students`, and `alumni`?

I used a shared `users` table for authentication and identity fields, then moved role-specific fields into `students` and `alumni`. That keeps the schema cleaner and avoids many nullable columns in one large table.

### 2. How did you secure authentication in plain PHP?

I replaced plaintext passwords with `password_hash()` and `password_verify()`, used prepared statements for login, regenerated the session ID after successful login, and added role checks for protected pages.

### 3. How does the connection request workflow work?

Students create rows in `connection_requests`, alumni view requests addressed to them, and alumni can accept or reject pending requests. Students can also cancel their own pending requests. The status field tracks the lifecycle.

### 4. What part of the database design matters most here?

The key design decision is mapping product features directly to relational tables: profiles, messages, events, and requests all have their own entities and relationships. That made the app easier to query and extend.

### 5. If you had more time, what would you improve?

I would add pagination, notifications, deployment, automated tests, and richer networking features such as accepted-connection lists, RSVP flows, and better mobile responsiveness.
