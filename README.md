 NAME: FRANK NTARO JOSEHAT    REGISTRATION NUMBER:23100533350007  CA NUMBER 18265
 NAME : AGHATA JONAS AUGUSTINO  RGISTRATION NUMBER:23100533350051   CA NUMBER 19350


 DATA MODELING

 Here's  below is a complete Data Model for the MUST Student Registration System, designed to illustrate how the system components relate and function within the MySQL database.

 1.Entity Relationship Diagram overview
 [accounts]
    ├── id (Primary Key(PK))
    ├── username
    ├── password_hash
    ├── role ('student', 'admin')
    ├── email
    └── created_at

    |
    └── [1:M] relationship with → student_profiles.user_id
    └── [1:M] relationship with → student_documents.user_id
    └── [1:M] relationship with → student_payments.user_id
    └── [1:M] relationship with → hostel_requests.user_id


[student_profiles]
    ├── id (Primary Key (PK))
    ├── user_id (Foreign key (FK) → accounts.id)
    ├── full_name
    ├── gender
    ├── date_of_birth
    ├── email
    ├── phone_number
    ├── nationality
    ├── district
    ├── disease
    ├── hospital_report (file name)
    ├── program
    ├── intake_year

[student_documents]
    ├── id (Primary Key(PK))
    ├── user_id (FK → accounts.id)
    ├── doc_type (e.g., 'form_iv', 'birth_cert')
    ├── file_url

[student_payments]
    ├── id (Primary Key(PK))
    ├── user_id (FK → accounts.id)
    ├── payment_type ('tuition', 'direct', 'nhif')
    ├── control_number
    ├── amount
    ├── created_at

[hostel_requests]
    ├── id (Primary Key(PK))
    ├── user_id (FK → accounts.id)
    ├── room_type ('single', 'double')
    ├── created_at


 TABLE DETAILS 
1. accounts
Holds authentication credentials and user roles.

| Column         | Type         | Notes                         |
| -------------- | ------------ | ----------------------------- |
| id             | INT (PK)     | Auto increment                |
| username       | VARCHAR(50)  | Unique login name             |
| password\_hash | VARCHAR(255) | Hashed with `password_hash()` |
| role           | ENUM         | 'admin' or 'student'          |
| email          | VARCHAR(100) | Optional                      |
| created\_at    | TIMESTAMP    | Auto-generated                |



2. student_profiles
Stores full student biodata.

| Column           | Type         | Notes                          |
| ---------------- | ------------ | ------------------------------ |
| user\_id         | INT (FK)     | Linked to `accounts.id`        |
| full\_name       | VARCHAR(100) |                                |
| gender           | ENUM         | male/female/other              |
| date\_of\_birth  | DATE         |                                |
| email            | VARCHAR(100) | Optional                       |
| phone\_number    | VARCHAR(20)  | Intl format w/ code            |
| nationality      | VARCHAR(100) |                                |
| district         | VARCHAR(100) |                                |
| disease          | TEXT         | "None" or actual disease       |
| hospital\_report | VARCHAR(255) | Filename of uploaded PDF       |
| program          | VARCHAR(100) | e.g., "BSc IT" or "Diploma..." |
| intake\_year     | INT          | e.g., 2025                     |

3. student_documents
Student uploads scanned PDF files.

| Column    | Type         | Notes                                  |
| --------- | ------------ | -------------------------------------- |
| user\_id  | INT (FK)     | Linked to `accounts.id`                |
| doc\_type | ENUM         | 'form\_iv', 'form\_vi', 'diploma', etc |
| file\_url | VARCHAR(255) | Filename path                          |


4. student_payments
Contains payment records and control numbers.

| Column          | Type         | Notes                       |
| --------------- | ------------ | --------------------------- |
| user\_id        | INT (FK)     | Linked to `accounts.id`     |
| payment\_type   | ENUM         | 'tuition', 'direct', 'nhif' |
| control\_number | VARCHAR(100) | Generated by system         |
| amount          | DECIMAL      | Payment amount              |
| created\_at     | TIMESTAMP    | When generated              |


5. hostel_requests
Stores room selection per student.

| Column      | Type      | Notes                   |
| ----------- | --------- | ----------------------- |
| user\_id    | INT (FK)  | Linked to `accounts.id` |
| room\_type  | ENUM      | 'single', 'double'      |
| created\_at | TIMESTAMP | Timestamp of request    |

6.admin_logs
  For  auditing and tracking admin actions

| Column      | Type      | Description                            |
| ----------- | --------- | -------------------------------------- |
| `id`        | INT (PK)  | Unique log entry                       |
| `admin_id`  | INT (FK)  | References `accounts.id` of the admin  |
| `action`    | TEXT      | Description of the action taken        |
| `timestamp` | TIMESTAMP | Time the action occurred (auto-filled) |




 RELATIONSHIPS SUMMARY  DIAGRAM

One account → One student_profile

One account → Many student_documents

One account → Many student_payments

One account → One hostel_request
