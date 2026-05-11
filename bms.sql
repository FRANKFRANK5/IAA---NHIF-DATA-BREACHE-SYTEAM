-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 08:32 AM
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
-- Database: `bms`
--

-- --------------------------------------------------------

--
-- Table structure for table `breaches`
--

CREATE TABLE `breaches` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_type` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholders`
--

CREATE TABLE `stakeholders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholders`
--
-- Error reading structure for table bms.stakeholders: #1932 - Table &#039;bms.stakeholders&#039; doesn&#039;t exist in engine
-- Error reading data for table bms.stakeholders: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `bms`.`stakeholders`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--
-- START OF DATABASE RESTRUCTURING AND SECURITY HARDENING
START TRANSACTION;

-- 1. HARDENING THE USERS TABLE
-- Purpose: Setting the username as a unique identifier and preparing for secure password hashing.
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`), -- Defines a unique identity for each admin/user.
  MODIFY `password` VARCHAR(255) NOT NULL, -- Expands storage to accommodate secure Bcrypt/Argon2 hashes.
  ADD COLUMN `last_login` TIMESTAMP NULL DEFAULT NULL; -- Audit trail to track user activity.

-- 2. ENFORCING IDENTITY IN STAKEHOLDERS
-- Purpose: Ensuring every stakeholder has a unique, auto-incrementing ID.
ALTER TABLE `stakeholders`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT; -- Automates ID generation for new stakeholders.

-- 3. ORGANIZING THE REPORTS TABLE
-- Purpose: Creating a structured index for system-generated reports.
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;

-- 4. LINKING BREACHES TO SYSTEM USERS (Accountability)
-- Purpose: Establishing a relationship to track which admin reported which security breach.
ALTER TABLE `breaches`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT,
  ADD COLUMN `reported_by` VARCHAR(50); -- The username of the admin who filed the report.

-- Adding Foreign Key for Breaches
ALTER TABLE `breaches`
  ADD CONSTRAINT `fk_reported`
  FOREIGN KEY (`reported_by`) REFERENCES `users` (`username`)
  ON DELETE SET NULL ON UPDATE CASCADE; -- Maintains data integrity if a user account is modified.

-- 5. LINKING NOTIFICATIONS TO STAKEHOLDERS (Communication Flow)
-- Purpose: Ensuring notifications are accurately mapped to the correct stakeholders.
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT,
  ADD CONSTRAINT `fk_stakeholder`
  FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE; -- Automatically cleans up notifications if a stakeholder is deleted.
--

--
-- AUTHORIZED SYSTEM UPDATE
START TRANSACTION;

-- 1. Enhancing User Management
ALTER TABLE `users`
  ADD COLUMN `status` ENUM('active', 'suspended', 'pending') DEFAULT 'active',
  ADD COLUMN `role` ENUM('admin', 'auditor', 'analyst') DEFAULT 'analyst';

-- 2. Enhancing Breach Accountability
ALTER TABLE `breaches`
  ADD COLUMN `affected_systems` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `resolution_details` TEXT DEFAULT NULL,
  MODIFY `status` ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open';

-- 3. Enhancing Notification Tracking
ALTER TABLE `notifications`
  ADD COLUMN `read_at` TIMESTAMP NULL DEFAULT NULL;

-- 
ALTER TABLE `users` ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- 
-- Kuingiza Admin mwenye password ya 'admin@group33'
INSERT INTO `users` (`username`, `password`, `role`, `status`) 
VALUES ('adm1n1strat0r', '$2y$10$EixZA5VK16SR3XwP8rO2LuyG8pP9p9S9Yv8.O6vP7v/lS7.T.6Yy.', 'admin', 'active')
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`), `role`='admin';
COMMIT;
-- END OF SCRIPT

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
