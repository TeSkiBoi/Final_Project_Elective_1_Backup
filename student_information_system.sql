-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 07:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_information_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` varchar(10) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `units` int(11) NOT NULL,
  `department_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_code`, `units`, `department_id`) VALUES
('C001', 'Introduction to Computing', 'ITC101', 3, 'D001'),
('C002', 'Data Structures', 'DS102', 4, 'D002'),
('C003', 'Computer Programming 2', 'DS103', 3, 'D001');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` varchar(10) NOT NULL,
  `department_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
('D001', 'College of Information and Computing Sciences'),
('D002', 'College of Engineering'),
('D003', 'College of Education'),
('D004', 'College of Agriculture'),
('D006', 'College of Allied and Health Sciences');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` varchar(10) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` varchar(10) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `year_level` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` varchar(10) NOT NULL,
  `faculty_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`) VALUES
('FC004', 'ARVIN PERNIA');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` varchar(10) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `status` enum('Single','Married','Widowed','Separated') NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Staff'),
(3, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('M','F') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` char(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `fullname`, `email`, `role_id`, `status`, `created_at`, `updated_at`, `profile_picture`) VALUES
('U002', 'administrator', '21232f297a57a5a743894a0e4a801fc3', 'JUAN DELA CRUZ', 'robles.jeremie@gmail.com', 1, 'active', '2025-11-15 10:33:41', '2025-11-17 01:37:34', 'U002_1763295438.jpeg'),
('U003', 'staff', '21232f297a57a5a743894a0e4a801fc3', 'STAFF INFORMATION', 'staff@gmail.com', 2, 'active', '2025-11-15 11:26:41', '2025-11-17 01:56:59', 'U003_1763207089.jpeg'),
('U004', 'TERRENCE', '42254836cd4ed98b563eb480f99c5886', 'TERRENCE BRIONES', 'terrence@gmail.com', 1, 'active', '2025-11-17 02:06:06', '2025-11-17 02:08:19', 'U004_1763345299.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `action` varchar(255) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`log_id`, `user_id`, `action`, `log_time`, `ip_address`) VALUES
(36, 'U002', 'User logged in', '2025-11-15 11:21:31', '::1'),
(37, 'U002', 'User uploaded profile picture', '2025-11-15 11:25:47', '::1'),
(38, 'U002', 'User logged out', '2025-11-15 11:26:53', '::1'),
(39, 'U003', 'User logged in', '2025-11-15 11:27:02', '::1'),
(40, 'U002', 'User logged in', '2025-11-15 11:40:16', '::1'),
(41, 'U003', 'User uploaded profile picture', '2025-11-15 11:44:49', '::1'),
(42, 'U003', 'User logged out', '2025-11-15 12:08:25', '::1'),
(43, 'U002', 'User logged in', '2025-11-15 12:08:47', '::1'),
(44, 'U002', 'User logged in', '2025-11-15 12:35:27', '::1'),
(45, 'U002', 'User logged in', '2025-11-15 12:45:58', '::1'),
(46, 'U002', 'User logged out', '2025-11-15 12:46:07', '::1'),
(47, 'U003', 'User logged in', '2025-11-15 12:46:44', '::1'),
(48, 'U003', 'User logged out', '2025-11-15 13:00:52', '::1'),
(49, 'U002', 'User logged in', '2025-11-15 13:00:55', '::1'),
(50, 'U002', 'User logged out', '2025-11-15 13:01:00', '::1'),
(51, 'U003', 'User logged in', '2025-11-15 13:01:09', '::1'),
(52, 'U003', 'User logged out', '2025-11-15 13:10:39', '::1'),
(53, 'U002', 'User logged in', '2025-11-16 11:19:20', '::1'),
(54, 'U002', 'User uploaded profile picture', '2025-11-16 12:17:18', '::1'),
(55, 'U002', 'User logged out', '2025-11-16 12:45:42', '::1'),
(56, 'U003', 'User logged in', '2025-11-16 12:45:55', '::1'),
(57, 'U003', 'User logged out', '2025-11-16 12:57:20', '::1'),
(58, 'U002', 'User logged in', '2025-11-16 12:57:37', '::1'),
(59, 'U002', 'User logged in', '2025-11-17 01:37:19', '::1'),
(60, 'U002', 'User logged out', '2025-11-17 01:39:07', '::1'),
(61, 'U003', 'User logged in', '2025-11-17 01:39:21', '::1'),
(62, 'U003', 'User logged out', '2025-11-17 01:54:24', '::1'),
(63, 'U002', 'User logged in', '2025-11-17 01:54:58', '::1'),
(64, 'U003', 'User logged in', '2025-11-17 01:57:03', '::1'),
(65, 'U002', 'User logged out', '2025-11-17 02:06:30', '::1'),
(66, 'U004', 'User logged in', '2025-11-17 02:06:43', '::1'),
(67, 'U004', 'User uploaded profile picture', '2025-11-17 02:08:19', '::1'),
(68, 'U002', 'User logged in', '2025-11-17 04:29:07', '::1'),
(69, 'U003', 'User logged in', '2025-11-17 04:29:24', '::1'),
(70, 'U002', 'User logged out', '2025-11-17 05:01:14', '::1'),
(71, 'U002', 'User logged in', '2025-11-17 05:01:28', '::1'),
(72, 'U003', 'User logged out', '2025-11-17 05:09:02', '::1'),
(73, 'U002', 'User logged in', '2025-11-17 05:09:20', '::1'),
(74, 'U002', 'User logged in', '2025-11-17 05:46:04', '::1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
