# Changelog

## Final Polish

- Rewrote the README as a portfolio-focused GitHub landing page
- Added `.env.example` for database configuration reference
- Added `PROJECT_SUMMARY.md` for interview preparation
- Added this phase-based changelog
- Improved setup guidance and project documentation consistency

## Connection Request Lifecycle

- Added student-to-alumni mentorship / connection requests
- Added alumni-side request review and accept / reject actions
- Added student sent-requests history
- Added pending request cancellation
- Improved request status display across discovery and profile pages

## Public Profiles And Events

- Added public view-only alumni and student profiles
- Connected discovery results to view-only profile pages
- Added event delete flow for admins
- Improved event status messages and dashboard event presentation

## Discovery And Messaging

- Expanded alumni discovery filters for students
- Expanded student discovery filters for alumni
- Improved result cards with richer details
- Added message prefill from search results and public profiles
- Improved inbox validation and conversation flow

## CSRF And Upload Validation

- Added CSRF protection to sensitive POST forms
- Reworked student CV upload validation for PDF-only uploads
- Added file size limits and randomized upload filenames
- Added basic input validation for email, IDs, and event dates

## Authentication And Security

- Replaced plaintext password storage with password hashing
- Reworked login to use prepared statements and password verification
- Added session regeneration after login
- Added reusable role-based access helpers
- Added real logout support
- Restricted admin-only actions and protected role-specific pages

## Initial Setup And Database Bootstrap

- Added `README.md`, `.gitignore`, `sql/schema.sql`, and `sql/seed.sql`
- Reconstructed the relational schema from the existing PHP codebase
- Added demo student, alumni, and admin accounts
- Added seeded events and sample messages
- Added `uploads/.gitkeep` and baseline DB bootstrap guidance
