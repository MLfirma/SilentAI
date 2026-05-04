-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 03:42 AM
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
-- Database: `concern_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `concern_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `actor` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `concerns`
--

CREATE TABLE `concerns` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Submitted',
  `department` varchar(100) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `assigned_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concerns`
--

INSERT INTO `concerns` (`id`, `student_name`, `email`, `category`, `description`, `attachment`, `is_anonymous`, `status`, `department`, `assigned_to`, `created_at`, `updated_at`, `assigned_by`) VALUES
(1, 'marylloyd', 'marylloyd@gmai.com', 'Academic', 'yayayayy', '', 0, 'Escalated', 'Registrar', NULL, '2026-04-30 02:40:16', '2026-05-03 02:40:33', NULL),
(2, 'marylloyd', 'marylloyd@gmai.com', 'Academic', 'yayayayy', '', 0, 'Submitted', 'Registrar', NULL, '2026-05-03 02:43:16', NULL, NULL),
(3, 'marylloyd', 'marylloyd@gmai.com', 'Academic', 'yayayayy', '', 0, 'Resolved', 'Registrar', 'IT', '2026-05-03 03:06:54', '2026-05-03 08:27:25', NULL),
(4, 'merimeri', 'merimeri@gmail.com', 'Financial', 'unable to pay tuition', '', 0, 'Submitted', 'Accounting', 'Accounting', '2026-05-03 03:08:15', '2026-05-03 10:37:23', NULL),
(8, 'daniel kwankwo', 'daniel@gmail.com', 'Welfare', 'osa concern', '', 0, 'Escalated', '', 'OSA', '2026-05-03 08:51:17', '2026-05-03 10:11:23', NULL),
(9, 'nadine lustre', 'nadine@gmail.com', 'Academic', 'need to talk with dean', '', 0, 'Escalated', 'Registrar', 'Dean\'s Office', '2026-05-03 08:53:35', '2026-05-03 08:56:48', NULL),
(10, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Guidance', 'sobrang depress po ako ilang araw na ako nag aayos ng para sa amc huhu', '', 0, 'Submitted', 'Guidance', NULL, '2026-05-03 10:49:38', NULL, NULL),
(11, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Guidance', 'im so depressed sa pag gawa ng amc huhu pang ilang gawa nato sana ok na', '', 0, 'Submitted', 'Guidance', NULL, '2026-05-03 11:18:07', NULL, NULL),
(12, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'payment issue', '', 0, 'Submitted', 'Accounting', NULL, '2026-05-03 11:32:22', NULL, NULL),
(13, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'khhdlkalkdaldad', '', 0, 'Submitted', 'Accounting', NULL, '2026-05-03 11:36:11', NULL, NULL),
(14, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Medical', 'need gamot', '', 0, 'Submitted', 'Clinic', NULL, '2026-05-03 12:02:17', NULL, NULL),
(15, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Medical', 'maryryyyyyyyyyyyyyy', '', 0, 'Submitted', 'Clinic', NULL, '2026-05-03 12:04:12', NULL, NULL),
(16, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Guidance', 'bagoooooooo', '', 0, 'Submitted', 'Guidance', NULL, '2026-05-03 12:11:32', NULL, NULL),
(17, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Academic', 'newwwwwwwwwwwwwwwww', '', 0, 'Submitted', 'Registrar', NULL, '2026-05-03 12:13:02', NULL, NULL),
(18, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'etooooooooooooooooooooo', '', 0, 'Submitted', 'Accounting', NULL, '2026-05-03 12:13:55', NULL, NULL),
(19, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Welfare', 'sana gumanaaaa', '', 0, 'Submitted', 'OSA', NULL, '2026-05-03 12:18:21', NULL, NULL),
(20, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'arayyyyyyyyyyyyyy', '', 0, 'Submitted', 'Accounting', 'Cashier', '2026-05-03 12:19:10', '2026-05-03 12:19:56', NULL),
(21, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Welfare', 'para to sa clinic', '', 0, 'Resolved', 'OSA', 'Clinic', '2026-05-03 12:33:00', '2026-05-03 12:33:51', NULL),
(22, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Academic', 'haah', '', 0, 'Submitted', 'Registrar', NULL, '2026-05-03 14:51:46', NULL, NULL),
(23, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'wala na kong pera ', '', 0, 'Resolved', 'Accounting', NULL, '2026-05-03 15:06:26', '2026-05-03 15:09:43', NULL),
(24, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Welfare', 'Community Service Concern', '', 0, 'Submitted', 'OSA', NULL, '2026-05-03 15:16:41', NULL, NULL),
(25, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Welfare', 'Need Medical Kit', '', 0, 'Submitted', 'OSA', NULL, '2026-05-03 15:25:27', NULL, NULL),
(26, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Financial', 'Promissory Note', '', 0, 'Submitted', 'Accounting', NULL, '2026-05-03 16:54:31', NULL, NULL),
(27, 'Mary Lloyd Firma', 'firmamarylloyd@gmail.com', 'Welfare', 'Osa', '', 0, 'Resolved', 'OSA', 'OSA', '2026-05-03 19:36:47', '2026-05-03 19:39:59', NULL),
(28, 'Marj justhel Argonsula', 'jevel04@gmail.com', 'Academic', 'grade issuee', '', 0, 'Submitted', 'Registrar', NULL, '2026-05-04 00:19:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_units`
--

CREATE TABLE `department_units` (
  `id` int(11) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `office` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `concern_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `concern_id`, `department`, `message`, `created_at`) VALUES
(1, 5, 'Welfare (OSA)', 'jahdjhsuhdkjabs', '2026-05-03 04:40:46'),
(2, 6, 'Academic', 'ydiusyfys', '2026-05-03 07:08:20'),
(3, 6, 'Academic', 'knkfwqdkjbn', '2026-05-03 07:13:53'),
(4, 6, 'Academic', 'hayssss', '2026-05-03 08:20:01'),
(5, 3, 'Academic', 'okay nato', '2026-05-03 08:27:36'),
(6, 23, 'Financial', 'Tapos kana Eugene.', '2026-05-03 15:09:57'),
(7, 27, 'Welfare', 'this is resolved.', '2026-05-03 19:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'cachocacho', 'cachocaho@test.com', 'admin123', 'admin'),
(2, 'admin', 'admin@test.com', 'admin123', 'admin'),
(3, 'Registrar User', 'reg@test.com', '123456', 'Academic'),
(4, 'Cashier User', 'cash@test.com', '123456', 'Financial'),
(5, 'OSA User', 'osa@test.com', '123456', 'Welfare'),
(6, 'michael macinas', 'macinasguidance@test.com', '123456', 'Welfare');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concerns`
--
ALTER TABLE `concerns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_units`
--
ALTER TABLE `department_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `concerns`
--
ALTER TABLE `concerns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `department_units`
--
ALTER TABLE `department_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
