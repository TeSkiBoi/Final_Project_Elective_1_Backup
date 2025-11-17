CREATE DATABASE IF NOT EXISTS student_information_system;
USE student_information_system;

-- =======================
--  ROLES TABLE
-- =======================
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);

INSERT INTO roles (role_name) VALUES 
('Admin'),
('Staff');

-- =======================
--  USERS TABLE (Custom ID example: U001)
--  REMOVED supplement field
-- =======================
CREATE TABLE users (
    user_id VARCHAR(10) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

INSERT INTO users (user_id, username, password_hash, fullname, email, role_id)
VALUES
('U001', 'admin', '0192023a7bbd73250516f069df18b500', 'System Administrator', 'admin@sis.com', 1),
('U002', 'staff1', '4f0c68cf74f6a4f508f5d5fb4bc4f29d', 'Enrollment Staff', 'staff@sis.com', 2);

-- =======================
--  USER LOGS TABLE
--  NEW TABLE
-- =======================
CREATE TABLE user_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(10) NOT NULL,
    action VARCHAR(255) NOT NULL,
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- SAMPLE: Insert logs (optional)
-- INSERT INTO user_logs (user_id, action, ip_address)
-- VALUES ('U001', 'Logged in', '192.168.1.15');

-- =======================
--  STUDENTS TABLE
-- =======================
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    birthdate DATE,
    gender ENUM('M','F') NOT NULL,
    address VARCHAR(255),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =======================
--  DEPARTMENTS TABLE (Custom ID: D001)
-- =======================
CREATE TABLE departments (
    department_id VARCHAR(10) PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL
);

INSERT INTO departments (department_id, department_name)
VALUES 
('D001', 'College of Information and Computing Sciences'),
('D002', 'College of Engineering'),
('D003', 'College of Education');

-- =======================
--  COURSES TABLE (Custom ID: C001)
-- =======================
CREATE TABLE courses (
    course_id VARCHAR(10) PRIMARY KEY,
    course_name VARCHAR(150) NOT NULL,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    units INT NOT NULL,
    department_id VARCHAR(10),
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

INSERT INTO courses (course_id, course_name, course_code, units, department_id)
VALUES
('C001', 'Introduction to Computing', 'ITC101', 3, 'D001'),
('C002', 'Data Structures', 'DS102', 4, 'D001'),
('C003', 'Database Management Systems', 'DBMS201', 3, 'D001');

-- =======================
--  ENROLLMENTS TABLE (Custom ID: E001)
-- =======================
CREATE TABLE enrollments (
    enrollment_id VARCHAR(10) PRIMARY KEY,
    student_id INT NOT NULL,
    course_id VARCHAR(10) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    year_level INT NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);
