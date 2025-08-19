-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: wm_mysql:3306
-- Generation Time: Aug 09, 2025 at 11:41 PM
-- Server version: 5.7.44
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wm_slot_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_reference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slot_id` bigint(20) UNSIGNED NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `booking_type_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `vehicle_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gate_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bay_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manifest_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hazmat` tinyint(1) NOT NULL DEFAULT '0',
  `temperature_requirements` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_arrival` timestamp NULL DEFAULT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `actual_cases` int(11) DEFAULT NULL,
  `expected_cases` int(11) DEFAULT NULL,
  `case_variance` int(11) DEFAULT NULL,
  `arrived_at` timestamp NULL DEFAULT NULL,
  `departed_at` timestamp NULL DEFAULT NULL,
  `container_size` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expected_pallets` int(11) DEFAULT NULL,
  `actual_pallets` int(11) DEFAULT NULL,
  `pallet_variance` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `original_booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rebook_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_rebooked` tinyint(1) NOT NULL DEFAULT '0',
  `rebook_count` int(11) NOT NULL DEFAULT '0',
  `tipping_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipping_bay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipping_status` enum('not_started','trailer_dropped','moved_to_bay','tipping_in_progress','tipping_completed','trailer_departed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `trailer_dropped_at` timestamp NULL DEFAULT NULL,
  `moved_to_bay_at` timestamp NULL DEFAULT NULL,
  `tipping_started_at` timestamp NULL DEFAULT NULL,
  `tipping_completed_at` timestamp NULL DEFAULT NULL,
  `trailer_departed_at` timestamp NULL DEFAULT NULL,
  `tipping_notes` text COLLATE utf8mb4_unicode_ci,
  `actual_tipping_duration` int(11) DEFAULT NULL,
  `tipping_issues` json DEFAULT NULL,
  `tipping_operator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bay_assigned_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_reference`, `slot_id`, `end_time`, `booking_type_id`, `user_id`, `reference`, `notes`, `vehicle_registration`, `container_number`, `driver_name`, `driver_phone`, `carrier_company`, `carrier_contact`, `gate_number`, `bay_number`, `manifest_number`, `load_type`, `hazmat`, `temperature_requirements`, `estimated_arrival`, `special_instructions`, `actual_cases`, `expected_cases`, `case_variance`, `arrived_at`, `departed_at`, `container_size`, `created_at`, `updated_at`, `customer_id`, `expected_pallets`, `actual_pallets`, `pallet_variance`, `status`, `deleted_at`, `original_booking_id`, `rebook_reason`, `cancelled_at`, `cancellation_reason`, `cancelled_by`, `is_rebooked`, `rebook_count`, `tipping_location_id`, `tipping_bay_id`, `tipping_status`, `trailer_dropped_at`, `moved_to_bay_at`, `tipping_started_at`, `tipping_completed_at`, `trailer_departed_at`, `tipping_notes`, `actual_tipping_duration`, `tipping_issues`, `tipping_operator_id`, `bay_assigned_by`) VALUES
(3, NULL, 99925, '2025-07-06 02:00:00', 2, 1, 'ACME123', 'Roadrunner Supplies', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 100, NULL, '2025-07-09 22:54:04', '2025-07-09 22:54:54', 40, '2025-07-03 22:56:14', '2025-07-24 15:05:57', 5, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, NULL, 99921, '2025-07-06 02:00:00', 1, 1, '1584', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2512, 0, '2025-07-05 20:59:18', '2025-07-09 22:46:16', 40, '2025-07-03 22:57:51', '2025-07-24 15:06:34', 4, 12, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, 99926, NULL, 1, 4, 'Book Test 1', 'Customer Booking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1400, 1500, -100, '2025-07-04 12:42:16', '2025-07-05 14:27:36', 40, '2025-07-04 00:31:30', '2025-07-24 15:06:21', 5, 20, 25, 5, 'early', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, NULL, 99927, '2025-07-06 03:00:00', 2, 1, 'sun0200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 20, NULL, '2025-07-06 19:16:41', '2025-07-09 22:46:10', 20, '2025-07-06 01:20:54', '2025-07-24 15:06:10', 4, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, NULL, 100215, NULL, 2, 7, 'PASTA', 'Pasta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, NULL, '2025-07-24 19:59:51', '2025-07-24 22:14:01', 40, '2025-07-24 19:33:28', '2025-07-24 22:14:01', 4, 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, NULL, 100207, NULL, 2, 4, 'dsf', 'dsf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 20, 10, NULL, '2025-07-24 19:59:57', '2025-07-24 22:14:08', 40, '2025-07-24 19:44:34', '2025-07-24 22:14:08', 1, 200, 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, NULL, 100208, NULL, 2, 7, 'sdsd', 'asd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 23, NULL, '2025-07-24 22:14:25', '2025-08-06 10:44:26', 40, '2025-07-24 19:45:24', '2025-08-06 10:44:26', 4, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, NULL, 100228, NULL, 1, 7, 'Pgssands', 'Hurry No way man!!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 2490, 2500, -10, '2025-08-01 10:34:51', '2025-08-08 09:15:56', 40, '2025-08-01 10:29:55', '2025-08-08 09:15:56', 4, 20, 19, -1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, NULL, 100282, NULL, 1, 7, 'Lookup', 'Get it done', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1500, NULL, NULL, NULL, 40, '2025-08-01 10:43:18', '2025-08-01 10:43:18', 4, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, NULL, 100321, NULL, 3, 7, 'New', 'Late in', 'L146 VVL', 'kt416', NULL, NULL, NULL, NULL, 'Phaase 1', '3', NULL, NULL, 0, NULL, NULL, NULL, 1490, 1500, -10, '2025-08-06 22:06:08', '2025-08-08 09:16:06', 40, '2025-08-01 10:44:38', '2025-08-08 09:16:06', 4, 20, 22, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'WM-20250806-012A', 100288, NULL, 2, 8, 'Test', 'Test', 'AB24 PAL', 'KT741', NULL, NULL, NULL, NULL, 'Tunnel', 'Bay 3', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1500, 0, '2025-08-07 13:20:25', NULL, 40, '2025-08-06 22:38:37', '2025-08-07 13:20:25', NULL, 25, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'WM-20250806-9E40', 100318, NULL, 2, 4, '145215484568', NULL, NULL, 'MSDU784515', NULL, NULL, 'Knowles', NULL, NULL, NULL, NULL, 'General', 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-06 23:11:59', '2025-08-06 23:11:59', NULL, 26, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'WM-20250807-E419', 100314, NULL, 2, 1, 'MSDU8943206', 'Test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-07 11:30:55', '2025-08-09 14:07:03', 4, 25, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'WM-20250807-E069', 100290, NULL, 2, 2, '12151', 'csd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-07 12:35:36', '2025-08-07 12:35:36', NULL, 25, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'WM-20250807-7FED', 100292, NULL, 2, 2, 'MSDU7859786', 'sdsd', 'AF09 MJY', 'KT416', NULL, NULL, 'KNOWLES', NULL, 'T SHED', '15', NULL, NULL, 0, NULL, NULL, 'URGENT', 2400, 2500, -100, '2025-08-07 12:59:43', NULL, 40, '2025-08-07 12:55:23', '2025-08-08 08:12:12', NULL, 14, 13, -1, NULL, '2025-08-08 08:12:12', NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'WM-20250808-4810', 100294, NULL, 2, 1, 'MSMU6841114', NULL, 'Test', 'MSMU6841114', NULL, NULL, 'BUCCI', NULL, 'Phase 2', 'Bay 1', NULL, NULL, 0, NULL, NULL, NULL, 3392, 3392, 0, '2025-08-08 06:58:25', '2025-08-08 12:09:35', 40, '2025-08-08 08:10:51', '2025-08-08 12:09:35', 4, 42, 42, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'WM-20250808-BFFF', 100495, NULL, 2, 1, 'MSBU6727453', NULL, 'AV71 UJJ', 'MSBU6727453', NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 3092, 3092, 0, '2025-08-08 09:37:49', '2025-08-08 14:22:25', 40, '2025-08-08 08:11:50', '2025-08-08 14:25:24', 4, 37, 37, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'WM-20250808-F809', 100293, NULL, 2, 1, 'MSCU5241603', NULL, 'Unknown', 'MSCU5241603', NULL, NULL, 'BUCCI', NULL, 'Phase 2', 'Bay 2', NULL, NULL, 0, NULL, NULL, NULL, NULL, 3710, 0, '2025-08-08 14:21:31', '2025-08-09 14:15:42', 40, '2025-08-08 08:20:06', '2025-08-09 14:15:42', 4, 41, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'WM-20250808-B7E3', 100296, NULL, 2, 1, 'MSMU7838590', NULL, NULL, NULL, NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-08-09 15:00:00', NULL, NULL, 3376, 0, NULL, NULL, 40, '2025-08-08 08:21:02', '2025-08-08 22:31:41', 4, 40, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-08 22:31:41', 'Rebooked to 2025-08-22 07:00', 4, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'WM-20250808-07E3', 100297, NULL, 2, 1, 'MSMU8036027', NULL, 'Unknown', 'MSMU8036027', NULL, NULL, 'BUCCI', NULL, 'Phase 2', 'Bay 3', NULL, NULL, 0, NULL, NULL, NULL, NULL, 3676, 0, '2025-08-08 14:20:54', NULL, 40, '2025-08-08 08:21:44', '2025-08-09 18:20:42', 4, 41, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'WM-20250808-B2CE', 100496, NULL, 1, 1, 'MSBU4085380', 'Pesto Palletised', NULL, NULL, NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 40, '2025-08-08 08:22:28', '2025-08-08 22:31:03', 4, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-08 22:31:03', 'Rebooked to 2025-08-25 07:00', 4, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'WM-20250808-E4DE', 100372, NULL, 2, 4, 'TEST', NULL, NULL, NULL, NULL, NULL, 'KNOWLES', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1450, 0, NULL, NULL, 40, '2025-08-08 17:28:40', '2025-08-08 20:42:36', 4, 1500, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-08 20:42:36', 'Rebooked to 2025-08-12 12:30', 4, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'WM-20250808-39A2', 100481, NULL, 2, 4, 'TEST', NULL, NULL, NULL, NULL, NULL, 'KNOWLES', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1450, 0, NULL, NULL, 40, '2025-08-08 20:42:36', '2025-08-08 20:59:36', 4, 1500, NULL, 0, NULL, NULL, 46, 'Incorrect booking', '2025-08-08 20:59:36', 'Rebooked to 2025-08-25 09:30', 4, 1, 1, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'WM-20250808-9FAB', 100405, NULL, 2, 4, 'REBOOK1', NULL, NULL, NULL, NULL, NULL, 'KNOWLES', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1450, 0, NULL, NULL, 40, '2025-08-08 20:59:36', '2025-08-08 21:10:08', 4, 1500, NULL, 0, NULL, NULL, 46, 'Test2', '2025-08-08 21:09:13', 'Rebooked to 2025-08-22 12:30', 4, 1, 2, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'WM-20250808-D7A2', 100391, NULL, 2, 4, 'TEST', NULL, 'Skip bay 3', 'Skip bay 3', NULL, NULL, 'KNOWLES', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1450, 0, '2025-08-09 18:18:34', NULL, 40, '2025-08-08 21:09:13', '2025-08-09 18:47:37', 4, 1500, NULL, 0, NULL, NULL, 46, 'Rebook', NULL, NULL, NULL, 1, 3, 1, NULL, 'moved_to_bay', NULL, '2025-08-09 18:18:34', NULL, NULL, NULL, 'Moved directly to bay during arrival process', NULL, NULL, NULL, 1),
(50, 'WM-20250808-B592', 100403, NULL, 1, 1, 'MSBU4085380', 'Pesto Palletised', NULL, NULL, NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 40, '2025-08-08 22:31:03', '2025-08-08 22:32:40', 4, NULL, NULL, 0, NULL, NULL, 45, 'Test', '2025-08-08 22:32:40', 'Cancel', NULL, 1, 1, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'WM-20250808-4CB7', 100388, NULL, 2, 1, 'MSMU7838590', NULL, NULL, NULL, NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-08-09 15:00:00', NULL, NULL, 3376, 0, NULL, NULL, 40, '2025-08-08 22:31:41', '2025-08-09 08:45:32', 4, 40, NULL, 0, NULL, NULL, 43, 'Test', '2025-08-09 08:45:32', 'Rebooked to 2025-08-22 06:30', 4, 1, 1, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'WM-20250809-6740', 100387, NULL, 2, 1, 'MSMU7838590', NULL, 'Skip to bay', 'Skip to Bay', NULL, NULL, 'BUCCI', NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-08-09 15:00:00', NULL, NULL, 3376, 0, '2025-08-09 18:12:53', NULL, 40, '2025-08-09 08:45:32', '2025-08-09 18:47:24', 4, 40, NULL, 0, NULL, NULL, 43, 'Test Rebook', NULL, NULL, NULL, 1, 2, 1, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'WM-20250809-9B4C', 100308, NULL, 2, 4, 'Rebook Test', 'Rebook Test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-09 11:01:17', '2025-08-09 11:02:34', 4, 26, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-09 11:02:34', 'Rebooked to 2025-08-11 07:00', 4, 0, 0, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'WM-20250809-23CD', 100309, NULL, 2, 4, 'Rebook Test', 'Rebook Test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-09 11:02:34', '2025-08-09 11:03:15', 4, 26, NULL, 0, NULL, NULL, 53, 'Incorrect Time', '2025-08-09 11:03:15', 'Rebooked to 2025-08-11 09:30', 4, 1, 1, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'WM-20250809-B2F6', 100311, NULL, 2, 4, 'Rebook Test', 'Rebook Test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-09 11:03:14', '2025-08-09 11:04:01', 4, 26, NULL, 0, NULL, NULL, 53, 'Time Chnaged', '2025-08-09 11:04:01', 'Rebooked to 2025-08-11 12:30', 4, 1, 2, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'WM-20250809-0E74', 100313, NULL, 2, 4, 'Rebook Test', 'Rebook Test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, NULL, NULL, 40, '2025-08-09 11:04:01', '2025-08-09 16:40:16', 4, 26, NULL, 0, NULL, NULL, 53, 'Wrong Time', '2025-08-09 16:40:16', 'Rebooked to 2025-08-13 07:00', 1, 1, 3, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'WM-20250809-35A9', 100326, NULL, 2, 4, 'Rebook Test', 'Rebook Test', 'Skip Bay', 'Skip to Bay', NULL, NULL, 'KNOWLES', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 2500, 0, '2025-08-09 18:06:38', NULL, 40, '2025-08-09 16:40:16', '2025-08-09 18:06:38', 4, 26, NULL, 0, NULL, NULL, 53, 'Testing', NULL, NULL, NULL, 1, 4, NULL, 1, 'not_started', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_history`
--

CREATE TABLE `booking_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `original_slot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `original_start_time` datetime DEFAULT NULL,
  `original_end_time` datetime DEFAULT NULL,
  `new_slot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `new_start_time` datetime DEFAULT NULL,
  `new_end_time` datetime DEFAULT NULL,
  `action` enum('created','cancelled','rebooked','modified','completed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `hours_before_slot` int(11) DEFAULT NULL,
  `is_last_minute` tinyint(1) NOT NULL DEFAULT '0',
  `customer_rebook_count_30days` int(11) NOT NULL DEFAULT '0',
  `customer_cancel_count_30days` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_history`
--

INSERT INTO `booking_history` (`id`, `booking_id`, `customer_id`, `user_id`, `original_slot_id`, `original_start_time`, `original_end_time`, `new_slot_id`, `new_start_time`, `new_end_time`, `action`, `reason`, `changes`, `hours_before_slot`, `is_last_minute`, `customer_rebook_count_30days`, `customer_cancel_count_30days`, `created_at`, `updated_at`) VALUES
(2, 46, 4, 4, 100372, '2025-08-20 06:30:00', '2025-08-20 09:30:00', 100481, '2025-08-12 12:30:00', '2025-08-12 15:30:00', 'rebooked', 'Incorrect booking', NULL, 88, 0, 0, 0, '2025-08-08 20:42:36', '2025-08-08 20:42:36'),
(3, 47, 4, 4, 100372, '2025-08-20 06:30:00', '2025-08-20 09:30:00', 100481, '2025-08-12 12:30:00', '2025-08-12 15:30:00', 'created', 'Rebooked from slot 2025-08-20 06:30', NULL, 88, 0, 1, 0, '2025-08-08 20:42:36', '2025-08-08 20:42:36'),
(4, 47, 4, 4, 100481, '2025-08-12 12:30:00', '2025-08-12 15:30:00', 100405, '2025-08-25 09:30:00', '2025-08-25 12:30:00', 'rebooked', 'Test2', NULL, 397, 0, 1, 0, '2025-08-08 20:59:36', '2025-08-08 20:59:36'),
(5, 48, 4, 4, 100481, '2025-08-12 12:30:00', '2025-08-12 15:30:00', 100405, '2025-08-25 09:30:00', '2025-08-25 12:30:00', 'created', 'Rebooked from slot 2025-08-12 12:30', NULL, 397, 0, 2, 0, '2025-08-08 20:59:36', '2025-08-08 20:59:36'),
(6, 48, 4, 4, 100405, '2025-08-25 09:30:00', '2025-08-25 12:30:00', 100391, '2025-08-22 12:30:00', '2025-08-22 15:30:00', 'rebooked', 'Rebook', NULL, 327, 0, 2, 0, '2025-08-08 21:09:13', '2025-08-08 21:09:13'),
(7, 49, 4, 4, 100405, '2025-08-25 09:30:00', '2025-08-25 12:30:00', 100391, '2025-08-22 12:30:00', '2025-08-22 15:30:00', 'created', 'Rebooked from slot 2025-08-25 09:30', NULL, 327, 0, 3, 0, '2025-08-08 21:09:13', '2025-08-08 21:09:13'),
(8, 45, 4, 4, 100496, '2025-08-08 16:00:00', '2025-08-08 17:00:00', 100403, '2025-08-25 07:00:00', '2025-08-25 10:00:00', 'rebooked', 'Test', NULL, 392, 0, 3, 0, '2025-08-08 22:31:03', '2025-08-08 22:31:03'),
(9, 50, 4, 4, 100496, '2025-08-08 16:00:00', '2025-08-08 17:00:00', 100403, '2025-08-25 07:00:00', '2025-08-25 10:00:00', 'created', 'Rebooked from slot 2025-08-08 16:00', NULL, 392, 0, 4, 0, '2025-08-08 22:31:03', '2025-08-08 22:31:03'),
(10, 43, 4, 4, 100296, '2025-08-08 13:00:00', '2025-08-08 16:00:00', 100388, '2025-08-22 07:00:00', '2025-08-22 10:00:00', 'rebooked', 'Test', NULL, 320, 0, 4, 0, '2025-08-08 22:31:41', '2025-08-08 22:31:41'),
(11, 51, 4, 4, 100296, '2025-08-08 13:00:00', '2025-08-08 16:00:00', 100388, '2025-08-22 07:00:00', '2025-08-22 10:00:00', 'created', 'Rebooked from slot 2025-08-08 13:00', NULL, 320, 0, 5, 0, '2025-08-08 22:31:41', '2025-08-08 22:31:41'),
(12, 50, 4, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'cancelled', 'Cancel', NULL, 392, 0, 5, 0, '2025-08-08 22:32:40', '2025-08-08 22:32:40'),
(13, 51, 4, 4, 100388, '2025-08-22 07:00:00', '2025-08-22 10:00:00', 100387, '2025-08-22 06:30:00', '2025-08-22 09:30:00', 'rebooked', 'Test Rebook', NULL, 310, 0, 5, 1, '2025-08-09 08:45:32', '2025-08-09 08:45:32'),
(14, 52, 4, 4, 100388, '2025-08-22 07:00:00', '2025-08-22 10:00:00', 100387, '2025-08-22 06:30:00', '2025-08-22 09:30:00', 'created', 'Rebooked from slot 2025-08-22 07:00', NULL, 310, 0, 6, 1, '2025-08-09 08:45:32', '2025-08-09 08:45:32'),
(15, 53, 4, 4, NULL, NULL, NULL, 100308, '2025-08-11 06:30:00', '2025-08-11 09:30:00', 'created', NULL, NULL, 43, 0, 6, 1, '2025-08-09 11:01:17', '2025-08-09 11:01:17'),
(16, 53, 4, 4, 100308, '2025-08-11 06:30:00', '2025-08-11 09:30:00', 100309, '2025-08-11 07:00:00', '2025-08-11 10:00:00', 'rebooked', 'Incorrect Time', NULL, 44, 0, 6, 1, '2025-08-09 11:02:34', '2025-08-09 11:02:34'),
(18, 54, 4, 4, 100308, '2025-08-11 06:30:00', '2025-08-11 09:30:00', 100309, '2025-08-11 07:00:00', '2025-08-11 10:00:00', 'created', 'Rebooked from slot 2025-08-11 06:30', NULL, 44, 0, 7, 1, '2025-08-09 11:02:34', '2025-08-09 11:02:34'),
(19, 54, 4, 4, 100309, '2025-08-11 07:00:00', '2025-08-11 10:00:00', 100311, '2025-08-11 09:30:00', '2025-08-11 12:30:00', 'rebooked', 'Time Chnaged', NULL, 46, 0, 7, 1, '2025-08-09 11:03:14', '2025-08-09 11:03:14'),
(20, 56, 4, 4, 100309, '2025-08-11 07:00:00', '2025-08-11 10:00:00', 100311, '2025-08-11 09:30:00', '2025-08-11 12:30:00', 'created', 'Rebooked from slot 2025-08-11 07:00', NULL, 46, 0, 8, 1, '2025-08-09 11:03:15', '2025-08-09 11:03:15'),
(21, 56, 4, 4, 100311, '2025-08-11 09:30:00', '2025-08-11 12:30:00', 100313, '2025-08-11 12:30:00', '2025-08-11 15:30:00', 'rebooked', 'Wrong Time', NULL, 49, 0, 8, 1, '2025-08-09 11:04:01', '2025-08-09 11:04:01'),
(22, 57, 4, 4, 100311, '2025-08-11 09:30:00', '2025-08-11 12:30:00', 100313, '2025-08-11 12:30:00', '2025-08-11 15:30:00', 'created', 'Rebooked from slot 2025-08-11 09:30', NULL, 49, 0, 9, 1, '2025-08-09 11:04:01', '2025-08-09 11:04:01'),
(23, 57, 4, 1, 100313, '2025-08-11 12:30:00', '2025-08-11 15:30:00', 100326, '2025-08-13 07:00:00', '2025-08-13 10:00:00', 'rebooked', 'Testing', NULL, 86, 0, 9, 1, '2025-08-09 16:40:16', '2025-08-09 16:40:16'),
(24, 58, 4, 1, 100313, '2025-08-11 12:30:00', '2025-08-11 15:30:00', 100326, '2025-08-13 07:00:00', '2025-08-13 10:00:00', 'created', 'Rebooked from slot 2025-08-11 12:30', NULL, 86, 0, 10, 1, '2025-08-09 16:40:16', '2025-08-09 16:40:16'),
(25, 49, 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'modified', 'Moved to tipping bay: Bay 1', '{\"bay_id\": 1, \"bay_name\": \"Bay 1\", \"action_type\": \"moved_to_bay\"}', 306, 0, 10, 1, '2025-08-09 18:18:34', '2025-08-09 18:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `booking_product`
--

CREATE TABLE `booking_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `po_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cases` int(11) DEFAULT NULL,
  `pallets` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_types`
--

CREATE TABLE `booking_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slots_required` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_types`
--

INSERT INTO `booking_types` (`id`, `name`, `description`, `slots_required`, `created_at`, `updated_at`, `duration_minutes`, `deleted_at`) VALUES
(1, 'Palletised', NULL, 1, '2025-06-29 19:16:08', '2025-06-29 19:16:08', NULL, NULL),
(2, 'Handball', NULL, 2, '2025-06-29 19:16:08', '2025-06-29 19:16:08', NULL, NULL),
(3, 'Tonne Bags', NULL, 1, '2025-06-29 19:16:08', '2025-06-29 22:41:07', NULL, NULL),
(4, 'Other', NULL, 1, '2025-07-08 23:45:40', '2025-07-08 23:46:21', NULL, '2025-07-08 23:46:21');

-- --------------------------------------------------------

--
-- Table structure for table `booking_type_depot`
--

CREATE TABLE `booking_type_depot` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `booking_type_id` bigint(20) UNSIGNED NOT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('booking_system_cache_admin@exanple.com|172.24.0.1', 'i:1;', 1754074620),
('booking_system_cache_admin@exanple.com|172.24.0.1:timer', 'i:1754074620;', 1754074620),
('booking_system_cache_atantie@example.com|172.24.0.1', 'i:1;', 1753854998),
('booking_system_cache_atantie@example.com|172.24.0.1:timer', 'i:1753854998;', 1753854998),
('booking_system_cache_atlante@exanple.com|172.24.0.1', 'i:1;', 1753854961),
('booking_system_cache_atlante@exanple.com|172.24.0.1:timer', 'i:1753854961;', 1753854961),
('booking_system_cache_danupton1@aol.com|172.24.0.1', 'i:1;', 1754561412),
('booking_system_cache_danupton1@aol.com|172.24.0.1:timer', 'i:1754561412;', 1754561412),
('booking_system_cache_hugolehmann92@outlook.com|172.24.0.1', 'i:1;', 1754561417),
('booking_system_cache_hugolehmann92@outlook.com|172.24.0.1:timer', 'i:1754561417;', 1754561417),
('booking_system_cache_keyshawn74@moneysquad.org|172.24.0.1', 'i:3;', 1754561407),
('booking_system_cache_keyshawn74@moneysquad.org|172.24.0.1:timer', 'i:1754561407;', 1754561407),
('booking_system_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}', 1754604757);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cut_off_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `cut_off_time`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ACME Logistics', NULL, '2025-06-30 21:55:03', '2025-06-30 21:55:03', NULL),
(2, 'Bugs Bunny', NULL, '2025-06-30 21:55:03', '2025-06-30 21:55:03', NULL),
(3, 'Paul_deleted', NULL, '2025-06-30 21:55:03', '2025-07-07 18:45:41', '2025-07-07 18:45:41'),
(4, 'Atlante', NULL, '2025-07-08 11:26:48', '2025-07-08 11:26:48', NULL),
(5, 'Silver Spoon', NULL, '2025-07-08 23:25:29', '2025-07-08 23:25:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_behavior_settings`
--

CREATE TABLE `customer_behavior_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'integer',
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_behavior_settings`
--

INSERT INTO `customer_behavior_settings` (`id`, `customer_id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4, 'max_rebooks_per_booking', '10', 'integer', 'Maximum number of times a single booking can be rebooked', 1, '2025-08-09 16:38:39', '2025-08-09 16:38:39'),
(2, 4, 'max_total_rebooks_30days', '30', 'integer', 'Maximum total rebooks allowed in 30 days', 1, '2025-08-09 16:38:39', '2025-08-09 16:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `customer_depot_product`
--

CREATE TABLE `customer_depot_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `min_cases` int(10) UNSIGNED DEFAULT NULL,
  `max_cases` int(10) UNSIGNED DEFAULT NULL,
  `override_duration_minutes` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_depot_product`
--

INSERT INTO `customer_depot_product` (`id`, `customer_id`, `depot_id`, `product_id`, `min_cases`, `max_cases`, `override_duration_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 12, 12, 120, '2025-07-01 21:29:17', '2025-07-01 21:29:17'),
(3, 1, 2, 1, 12, 12, 120, '2025-07-01 21:32:18', '2025-07-01 21:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `customer_user`
--

CREATE TABLE `customer_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_user`
--

INSERT INTO `customer_user` (`id`, `customer_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 4, 4, NULL, NULL),
(3, 4, 1, NULL, NULL),
(4, 4, 7, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `depots`
--

CREATE TABLE `depots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cut_off_time` time NOT NULL DEFAULT '16:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `depots`
--

INSERT INTO `depots` (`id`, `name`, `location`, `created_at`, `updated_at`, `cut_off_time`, `deleted_at`) VALUES
(1, 'Main Depot', 'Default Location', '2025-06-29 19:16:06', '2025-07-07 18:47:54', '16:00:00', NULL),
(2, 'Wimblington', 'March', '2025-06-29 19:16:07', '2025-06-29 19:16:07', '16:00:00', NULL),
(3, 'Cromwell Road', 'Wisbech', '2025-06-29 19:16:07', '2025-06-29 19:16:07', '16:00:00', NULL),
(4, 'Salters Yard', 'Wisbech', '2025-06-29 19:16:07', '2025-06-29 19:16:07', '16:00:00', NULL),
(5, 'Lynn Road', 'Wisbech', '2025-06-29 19:16:07', '2025-06-29 19:16:07', '16:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `depot_case_ranges`
--

CREATE TABLE `depot_case_ranges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `min_cases` int(10) UNSIGNED DEFAULT NULL,
  `max_cases` int(10) UNSIGNED DEFAULT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `depot_product`
--

CREATE TABLE `depot_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `expected_case_count` int(10) UNSIGNED DEFAULT NULL,
  `min_cases` int(11) DEFAULT NULL,
  `max_cases` int(11) DEFAULT NULL,
  `override_duration_minutes` int(10) UNSIGNED DEFAULT NULL,
  `duration_override_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `depot_product`
--

INSERT INTO `depot_product` (`id`, `depot_id`, `product_id`, `expected_case_count`, `min_cases`, `max_cases`, `override_duration_minutes`, `duration_override_minutes`, `created_at`, `updated_at`) VALUES
(1, 5, 1, NULL, 10, 20, NULL, 100, '2025-07-01 22:30:16', '2025-07-01 22:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `depot_user`
--

CREATE TABLE `depot_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `depot_user`
--

INSERT INTO `depot_user` (`id`, `user_id`, `depot_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, NULL),
(22, 5, 1, NULL, NULL),
(30, 6, 5, NULL, NULL),
(31, 2, 2, NULL, NULL),
(32, 7, 5, NULL, NULL),
(41, 8, 1, NULL, NULL),
(42, 8, 2, NULL, NULL),
(43, 8, 3, NULL, NULL),
(44, 8, 4, NULL, NULL),
(45, 8, 5, NULL, NULL),
(58, 4, 5, NULL, NULL),
(62, 1, 5, NULL, NULL),
(64, 3, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_01_01_000000_create_depots_table', 1),
(2, '2025_01_01_000001_create_users_table', 1),
(3, '2025_01_01_000002_create_booking_types_table', 1),
(4, '2025_01_01_000003_create_slots_table', 1),
(5, '2025_01_01_000004_create_bookings_table', 1),
(6, '2025_06_24_143904_create_sessions_table', 1),
(7, '2025_06_24_144236_create_cache_table', 1),
(8, '2025_06_25_000000_add_case_and_size_to_bookings_table', 1),
(9, '2025_06_25_225157_create_slot_templates_table', 1),
(10, '2025_06_26_225152_add_details_to_bookings_table', 1),
(11, '2025_06_29_080210_add_capacity_to_slots_table', 1),
(12, '2025_06_29_081807_add_duration_minutes_to_booking_types_table', 1),
(13, '2025_06_29_082546_create_booking_type_depot_table', 1),
(14, '2025_06_29_094218_create_slot_generation_settings_table', 1),
(15, '2025_06_29_094425_add_arrival_departure_to_bookings_table', 1),
(16, '2025_06_29_172838_create_permission_tables', 1),
(17, '2025_06_30_211619_add_cut_off_time_to_depots_table', 2),
(18, '2025_06_30_214246_create_customers_table', 3),
(29, '2025_06_30_214449_add_customer_id_to_users_table', 4),
(30, '2025_07_01_170627_create_products_and_booking_product_tables', 4),
(31, '2025_07_01_171942_create_depot_product_table', 4),
(32, '2025_07_01_185252_create_customer_depot_product_table', 5),
(33, '2025_07_01_213934_create_depot_case_ranges_table', 6),
(36, '2025_07_03_175958_add_status_fields_to_bookings_table', 7),
(37, '2025_07_03_184941_add_customer_id_to_bookings_table', 8),
(38, '2025_07_03_193030_add_expected_actual_fields_to_bookings_table', 8),
(39, '2025_07_03_210203_edit_expected_actual_fields_to_bookings_table', 9),
(40, '2025_07_03_222151_add_status_to_bookings_table', 10),
(41, '2025_07_03_225327_add_end_time_to_bookings_table', 11),
(42, '2025_07_04_182151_create_depot_user_table', 12),
(43, '2025_07_06_001009_add_deleted_at_to_slots_table', 13),
(44, '2025_07_06_120039_create_password_reset_tokens_table', 14),
(49, '2025_07_07_181732_add_deleted_at_to_depots_table', 15),
(50, '2025_07_07_181735_add_deleted_at_to_bookings_table', 15),
(51, '2025_07_07_181919_add_deleted_at_to_users_table', 15),
(52, '2025_07_07_182421_add_deleted_at_to_booking_types_table', 15),
(56, '2025_07_07_183641_add_deleted_at_to_customers_table', 16),
(57, '2025_07_10_180021_create_slot_release_rules_table', 17),
(58, '2025_07_10_222631_create_slot_release_rule_customer', 18),
(59, '2025_07_18_114348_add_release_and_cutoff_to_slots', 19),
(60, '2025_07_18_130836_create_slot_customer_table', 20),
(61, '2025_08_02_000001_add_transportation_fields_to_bookings_table', 21),
(62, '2025_08_02_000002_rename_trailer_number_to_container_number', 21),
(63, '2025_08_02_000003_update_container_number_index', 21),
(64, '2025_08_04_120000_create_customer_user_table', 21),
(65, '2025_08_08_164430_create_booking_history_table', 22),
(66, '2025_08_08_164623_add_rebooking_fields_to_bookings_table', 22),
(67, '2025_08_09_000001_create_customer_behavior_settings_table', 23),
(68, '2025_08_09_000002_create_tipping_locations_table', 24),
(69, '2025_08_09_000003_create_tipping_bays_table', 24),
(70, '2025_08_09_000004_add_tipping_fields_to_bookings_table', 24);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(1, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('paul.carr@knowleslogistics.com', '$2y$12$KaVBbxA3UhVqL9Su94XsNeHU/kKaZkBfTH7EsY0s5fy5z2jFfw4sK', '2025-07-09 23:55:54');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_case_count` int(11) DEFAULT NULL,
  `default_pallets` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `description`, `default_case_count`, `default_pallets`, `created_at`, `updated_at`) VALUES
(1, 'PAUL123', '123', 12, 12, '2025-07-01 21:28:34', '2025-07-01 21:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-06-29 19:16:05', '2025-06-29 19:16:05'),
(2, 'depot-admin', 'web', '2025-06-29 19:16:05', '2025-06-29 19:16:05'),
(3, 'site-admin', 'web', '2025-06-29 19:16:05', '2025-06-29 19:16:05'),
(4, 'customer', 'web', '2025-06-29 19:16:05', '2025-06-29 19:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0nAqHqwXbAwuAEScRmxe8qWH9pMgdCtLJVGEV9L3', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjM3R0NkSzlzNnJVTjJGWE53VGZISmg5MWZpU1lkNkxlNlU2QjhtVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754780274),
('8r28FcPACiatBwgF7EH0a4U7q1BVgFfeBOXlY6lU', 1, '172.24.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZmxibDJkSWRkYzRxTVNWZ3d0MHcxTGh5OUFXVmRJN090OEl5azFQSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvZGVwb3QtYWRtaW4vYm9va2luZ3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjQwOiJodHRwOi8vYm9va2luZy5mdXJ5Lm1lLnVrL2ltYWdlcy9rdGwuc3ZnIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1754757512),
('9A7AlU2Y0rwVibTW1qSWYEcwHcETf1XSHcbED35G', NULL, '172.24.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMmhrdGNncmdVY0tEMklxREpLbktkVmY3ZW9vTGhkSEJuV1UyOWVTVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjQwOiJodHRwOi8vYm9va2luZy5mdXJ5Lm1lLnVrL2ltYWdlcy9rdGwuc3ZnIjt9fQ==', 1754760563),
('aF5irsh4vcqa63HSHM2LkvFr9teYeixIKDkfkKnv', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUTY1czVTV0N2RzJMVFhSU3p1Tk1ha21JZkpUUE5ZZG9zM3RaYVRwQiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hZG1pbi90aXBwaW5nLWJheXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754761688),
('CIblkRu789M8L3BOpmZ2tUe4HWmq0inrSQILNSQp', 1, '172.24.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRkROTnl2Rk9FcDlnYjR4eEpmd2VNRWlZUmdtQVI5WkhCMGpGMldBbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvYWRtaW4vYm9va2luZ3MvNTgvZWRpdCI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDA6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvaW1hZ2VzL2t0bC5zdmciO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTU6ImJvb2tpbmdfZmlsdGVycyI7YToxOntzOjc6ImFycml2YWwiO3M6Njoib25zaXRlIjt9fQ==', 1754761784),
('d3uY4trhzK6Zi4dnaLwz5jIlUHQbOXhw40OHKnpl', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Safari', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidk1QVmdEeWYxbHdmTEhlYWNXaEU5R1BIa2NOV2lCUm5xUHVXSmVIYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hcHBsZS10b3VjaC1pY29uLnBuZyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYm9va2luZy5mdXJ5Lm1lLnVrL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754755486),
('DZvoEIL6CyPm6GIQZlxnwum0A0lGSvNcexkvgJxG', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNDdwV0I5WWhzd0ptMFNkN2tFdHRmYm9DbFBlMERHb01XcGlnOHQyRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hZG1pbi90aXBwaW5nLWJheXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754761626),
('eH32wCoKIWVEX51XCUfjT1AQlCGmpLvNZx7ZlXqr', 1, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiYndCQUZDNGNUMjJ5TlBzUGE1ZkhQeXFNQWJJUGhBN05uR0dBenM3QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvYWRtaW4vdGlwcGluZy13b3JrZmxvdy9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjQwOiJodHRwOi8vYm9va2luZy5mdXJ5Lm1lLnVrL2ltYWdlcy9rdGwuc3ZnIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE1OiJib29raW5nX2ZpbHRlcnMiO2E6MTp7czo2OiJmaWx0ZXIiO3M6NToidG9kYXkiO319', 1754781109),
('JNhV1fOcAnvyyW4gUe5qor5v7jV4DhQqSVdQt6Bq', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiamRHRDRXbEZod1p6dXhLRXh1MTY1R3Voa1ZtQUtGZGQ4enkxZ2VySiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hZG1pbi90aXBwaW5nLWJheXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754761687),
('SakXmtAocdSsnz5MmhlFT1GSxmnolwKbKcisUN4S', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNVF3TjJBZHNlWUx0MWM0ZFBuQVcyQVVQb0RJUzl3MDBjallBdTNTbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hZG1pbi90aXBwaW5nLWJheXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754761574),
('wHw0DO2UYV8INnyXednL5TOYWU9vPXDui4YAam0V', 6, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibHNWZm1Ea0Q1Y2Zrd3VhODVSVjliOWpQZUh1blVjVjQyV3JJV2JnVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9ib29raW5nLmZ1cnkubWUudWsvZGVwb3QtYWRtaW4vYm9va2luZ3MiO31zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjQ3OiJodHRwOi8vYm9va2luZy5mdXJ5Lm1lLnVrL2RlcG90LWFkbWluL2Rhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==', 1754755668),
('XKmVgOxND1TkyPWV7CCQnbQud9HaNVgcSYzEiNGD', NULL, '172.24.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRG4yWDk3NkM5NnRzSnR2cVFyblduVm82Ym1sT05sSFRCd2hXZUlnNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9hZG1pbi90aXBwaW5nLWJheXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL2Jvb2tpbmcuZnVyeS5tZS51ay9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754761574);

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `booking_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`id`, `depot_id`, `booking_type_id`, `start_at`, `end_at`, `is_blocked`, `created_at`, `updated_at`, `capacity`, `deleted_at`, `released_at`, `locked_at`) VALUES
(99921, 4, NULL, '2025-07-06 01:00:00', '2025-07-06 02:00:00', 0, '2025-07-02 19:59:56', '2025-07-02 19:59:56', 1, NULL, '2025-07-22 18:56:58', '2025-07-05 17:00:00'),
(99922, 4, NULL, '2025-07-13 01:00:00', '2025-07-13 02:00:00', 0, '2025-07-02 19:59:56', '2025-07-02 19:59:56', 1, NULL, '2025-07-22 18:56:58', '2025-07-12 17:00:00'),
(99923, 4, NULL, '2025-07-06 03:00:00', '2025-07-06 04:00:00', 0, '2025-07-02 19:59:56', '2025-07-02 19:59:56', 1, NULL, '2025-07-21 22:10:16', '2025-07-21 23:30:00'),
(99924, 4, NULL, '2025-07-13 03:00:00', '2025-07-13 04:00:00', 0, '2025-07-02 19:59:57', '2025-07-02 19:59:57', 1, NULL, '2025-07-22 18:56:58', '2025-07-12 19:00:00'),
(99925, 2, NULL, '2025-07-06 01:00:00', '2025-07-06 04:00:00', 0, '2025-07-02 20:00:06', '2025-07-24 15:05:57', 1, NULL, '2025-07-22 18:56:58', '2025-07-05 17:00:00'),
(99926, 2, NULL, '2025-07-13 01:00:00', '2025-07-13 02:00:00', 0, '2025-07-02 20:00:06', '2025-07-02 20:00:06', 1, NULL, '2025-07-22 18:56:58', '2025-07-12 17:00:00'),
(99927, 2, NULL, '2025-07-06 02:00:00', '2025-07-06 05:00:00', 0, '2025-07-02 20:00:06', '2025-07-24 15:06:10', 1, NULL, '2025-07-22 18:56:58', '2025-07-05 18:00:00'),
(99928, 2, NULL, '2025-07-13 02:00:00', '2025-07-13 03:00:00', 0, '2025-07-02 20:00:06', '2025-07-02 20:00:06', 1, NULL, '2025-07-22 18:56:58', '2025-07-12 18:00:00'),
(99952, 1, NULL, '2025-08-04 06:00:00', '2025-08-04 08:00:00', 0, '2025-07-07 14:33:29', '2025-07-07 14:33:29', 1, NULL, '2025-07-21 22:10:16', NULL),
(99955, 1, NULL, '2025-07-22 08:00:00', '2025-07-22 09:00:00', 0, '2025-07-07 14:33:30', '2025-07-21 22:10:16', 1, NULL, '2025-07-21 22:10:16', '2025-07-21 16:00:00'),
(99960, 1, NULL, '2025-07-22 06:00:00', '2025-07-22 07:00:00', 0, '2025-07-07 14:33:30', '2025-07-21 22:10:16', 1, NULL, '2025-07-21 22:10:16', '2025-07-21 16:00:00'),
(99971, 2, NULL, '2025-07-20 01:00:00', '2025-07-20 02:00:00', 0, '2025-07-07 17:52:50', '2025-07-07 17:52:50', 1, NULL, '2025-07-22 18:56:58', '2025-07-19 17:00:00'),
(99972, 2, NULL, '2025-07-20 02:00:00', '2025-07-20 03:00:00', 0, '2025-07-07 17:52:51', '2025-07-07 17:52:51', 1, NULL, '2025-07-22 18:56:58', '2025-07-19 18:00:00'),
(100051, 5, NULL, '2025-07-23 06:30:00', '2025-07-23 09:30:00', 0, '2025-07-21 22:04:33', '2025-07-22 19:39:56', 1, NULL, '2025-07-22 19:39:56', '2025-07-22 16:00:00'),
(100053, 5, NULL, '2025-07-23 07:00:00', '2025-07-23 10:00:00', 0, '2025-07-21 22:04:33', '2025-07-22 19:39:56', 1, NULL, '2025-07-22 19:39:56', '2025-07-22 16:00:00'),
(100055, 5, NULL, '2025-07-23 09:30:00', '2025-07-23 12:30:00', 0, '2025-07-21 22:04:33', '2025-07-22 19:39:56', 1, NULL, '2025-07-22 19:39:56', '2025-07-22 16:00:00'),
(100057, 5, NULL, '2025-07-23 10:00:00', '2025-07-23 13:00:00', 0, '2025-07-21 22:04:33', '2025-07-22 19:39:56', 1, NULL, '2025-07-22 19:39:56', '2025-07-22 16:00:00'),
(100061, 5, NULL, '2025-07-23 12:30:00', '2025-07-23 15:30:00', 0, '2025-07-21 22:04:33', '2025-07-22 19:39:56', 1, NULL, '2025-07-22 19:39:56', '2025-07-22 16:00:00'),
(100081, 4, NULL, '2025-07-27 01:00:00', '2025-07-27 02:00:00', 0, '2025-07-21 22:52:32', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', '2025-07-26 16:00:00'),
(100082, 4, NULL, '2025-08-03 01:00:00', '2025-08-03 02:00:00', 0, '2025-07-21 22:52:32', '2025-07-22 17:54:48', 1, NULL, '2025-07-22 17:54:48', '2025-08-02 16:00:00'),
(100083, 4, NULL, '2025-07-27 03:00:00', '2025-07-27 04:00:00', 0, '2025-07-21 22:52:32', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', '2025-07-26 16:00:00'),
(100084, 4, NULL, '2025-08-03 03:00:00', '2025-08-03 04:00:00', 0, '2025-07-21 22:52:32', '2025-07-22 17:54:48', 1, NULL, '2025-07-22 17:54:48', '2025-08-02 16:00:00'),
(100205, 5, NULL, '2025-07-28 06:30:00', '2025-07-28 09:30:00', 0, '2025-07-24 19:31:51', '2025-07-27 18:22:48', 1, NULL, '2025-07-27 18:22:48', '2025-07-27 16:00:00'),
(100206, 5, NULL, '2025-07-28 07:00:00', '2025-07-28 10:00:00', 0, '2025-07-24 19:31:52', '2025-07-27 18:22:48', 1, NULL, '2025-07-27 18:22:48', '2025-07-27 16:00:00'),
(100207, 5, NULL, '2025-07-28 09:30:00', '2025-07-28 12:30:00', 0, '2025-07-24 19:31:52', '2025-07-24 19:31:52', 1, NULL, '2025-07-23 19:42:20', NULL),
(100208, 5, NULL, '2025-07-28 10:00:00', '2025-07-28 13:00:00', 0, '2025-07-24 19:31:52', '2025-07-27 18:22:49', 1, NULL, '2025-07-27 18:22:48', '2025-07-27 16:00:00'),
(100209, 5, NULL, '2025-07-28 12:30:00', '2025-07-28 15:30:00', 0, '2025-07-24 19:31:52', '2025-07-24 19:31:52', 1, NULL, '2025-07-23 22:28:19', NULL),
(100210, 5, NULL, '2025-07-29 06:30:00', '2025-07-29 09:30:00', 0, '2025-07-24 19:31:52', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-28 16:00:00'),
(100211, 5, NULL, '2025-07-29 07:00:00', '2025-07-29 10:00:00', 0, '2025-07-24 19:31:52', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-28 16:00:00'),
(100212, 5, NULL, '2025-07-29 10:00:00', '2025-07-29 13:00:00', 0, '2025-07-24 19:31:52', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-28 16:00:00'),
(100213, 5, NULL, '2025-07-29 09:30:00', '2025-07-29 12:30:00', 0, '2025-07-24 19:31:52', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-28 16:00:00'),
(100214, 5, NULL, '2025-07-29 12:30:00', '2025-07-29 15:30:00', 0, '2025-07-24 19:31:52', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-28 16:00:00'),
(100215, 5, NULL, '2025-07-25 06:30:00', '2025-07-25 09:30:00', 0, '2025-07-24 19:31:52', '2025-07-24 22:20:21', 1, NULL, '2025-07-24 22:20:21', '2025-07-24 16:00:00'),
(100216, 5, NULL, '2025-07-25 07:00:00', '2025-07-25 10:00:00', 0, '2025-07-24 19:31:53', '2025-07-24 22:20:21', 1, NULL, '2025-07-24 22:20:21', '2025-07-24 16:00:00'),
(100217, 5, NULL, '2025-07-25 10:00:00', '2025-07-25 13:00:00', 0, '2025-07-24 19:31:53', '2025-07-24 22:20:22', 1, NULL, '2025-07-24 22:20:21', '2025-07-24 16:00:00'),
(100218, 5, NULL, '2025-07-25 09:30:00', '2025-07-25 12:30:00', 0, '2025-07-24 19:31:53', '2025-07-24 22:20:21', 1, NULL, '2025-07-24 22:20:21', '2025-07-24 16:00:00'),
(100219, 5, NULL, '2025-07-25 12:30:00', '2025-07-25 15:30:00', 0, '2025-07-24 19:31:53', '2025-07-24 19:31:53', 1, NULL, '2025-07-21 22:10:16', NULL),
(100220, 5, NULL, '2025-08-04 06:30:00', '2025-08-04 09:30:00', 0, '2025-07-24 22:23:27', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-08-03 16:00:00'),
(100221, 5, NULL, '2025-08-04 07:00:00', '2025-08-04 10:00:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-08-03 16:00:00'),
(100222, 5, NULL, '2025-08-04 09:30:00', '2025-08-04 12:30:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-08-03 16:00:00'),
(100223, 5, NULL, '2025-08-04 10:00:00', '2025-08-04 13:00:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-08-03 16:00:00'),
(100224, 5, NULL, '2025-08-04 12:30:00', '2025-08-04 15:30:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:45', 1, NULL, '2025-07-30 17:31:42', '2025-08-03 16:00:00'),
(100225, 5, NULL, '2025-08-05 06:30:00', '2025-08-05 09:30:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:45', 1, NULL, '2025-07-30 17:31:42', '2025-08-04 16:00:00'),
(100226, 5, NULL, '2025-08-05 07:00:00', '2025-08-05 10:00:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:45', 1, NULL, '2025-07-30 17:31:42', '2025-08-04 16:00:00'),
(100227, 5, NULL, '2025-08-05 10:00:00', '2025-08-05 13:00:00', 0, '2025-07-24 22:23:28', '2025-07-30 17:31:45', 1, NULL, '2025-07-30 17:31:42', '2025-08-04 16:00:00'),
(100228, 5, NULL, '2025-08-05 09:30:00', '2025-08-05 10:30:00', 0, '2025-07-24 22:23:28', '2025-08-01 10:33:06', 1, NULL, '2025-07-30 17:31:42', '2025-08-04 16:00:00'),
(100229, 5, NULL, '2025-08-05 12:30:00', '2025-08-05 15:30:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:45', 1, NULL, '2025-07-30 17:31:42', '2025-08-04 16:00:00'),
(100230, 5, NULL, '2025-07-30 06:30:00', '2025-07-30 09:30:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-29 16:00:00'),
(100231, 5, NULL, '2025-08-06 06:30:00', '2025-08-06 09:30:00', 0, '2025-07-24 22:23:29', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-05 16:00:00'),
(100232, 5, NULL, '2025-07-30 07:00:00', '2025-07-30 10:00:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-29 16:00:00'),
(100233, 5, NULL, '2025-08-06 07:00:00', '2025-08-06 10:00:00', 0, '2025-07-24 22:23:29', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-05 16:00:00'),
(100234, 5, NULL, '2025-07-30 09:30:00', '2025-07-30 12:30:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-29 16:00:00'),
(100235, 5, NULL, '2025-08-06 09:30:00', '2025-08-06 12:30:00', 0, '2025-07-24 22:23:29', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-05 16:00:00'),
(100236, 5, NULL, '2025-07-30 10:00:00', '2025-07-30 13:00:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-29 16:00:00'),
(100237, 5, NULL, '2025-08-06 10:00:00', '2025-08-06 13:00:00', 0, '2025-07-24 22:23:29', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-05 16:00:00'),
(100238, 5, NULL, '2025-07-31 06:30:00', '2025-07-31 09:30:00', 0, '2025-07-24 22:23:29', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-30 16:00:00'),
(100239, 5, NULL, '2025-08-07 06:30:00', '2025-08-07 09:30:00', 0, '2025-07-24 22:23:30', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-06 16:00:00'),
(100240, 5, NULL, '2025-07-30 12:30:00', '2025-07-30 15:30:00', 0, '2025-07-24 22:23:30', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-29 16:00:00'),
(100241, 5, NULL, '2025-08-06 12:30:00', '2025-08-06 15:30:00', 0, '2025-07-24 22:23:30', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-05 16:00:00'),
(100242, 5, NULL, '2025-07-31 07:00:00', '2025-07-31 10:00:00', 0, '2025-07-24 22:23:30', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-30 16:00:00'),
(100243, 5, NULL, '2025-08-07 07:00:00', '2025-08-07 10:00:00', 0, '2025-07-24 22:23:30', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-06 16:00:00'),
(100244, 5, NULL, '2025-07-31 09:30:00', '2025-07-31 12:30:00', 0, '2025-07-24 22:23:30', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-30 16:00:00'),
(100245, 5, NULL, '2025-08-07 09:30:00', '2025-08-07 12:30:00', 0, '2025-07-24 22:23:30', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-06 16:00:00'),
(100246, 5, NULL, '2025-07-31 10:00:00', '2025-07-31 13:00:00', 0, '2025-07-24 22:23:30', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-30 16:00:00'),
(100247, 5, NULL, '2025-08-07 10:00:00', '2025-08-07 13:00:00', 0, '2025-07-24 22:23:30', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-06 16:00:00'),
(100248, 5, NULL, '2025-07-31 12:30:00', '2025-07-31 15:30:00', 0, '2025-07-24 22:23:30', '2025-07-30 17:31:43', 1, NULL, '2025-07-30 17:31:42', '2025-07-30 16:00:00'),
(100249, 5, NULL, '2025-08-07 12:30:00', '2025-08-07 15:30:00', 0, '2025-07-24 22:23:31', '2025-08-06 22:00:05', 1, NULL, '2025-08-06 22:00:05', '2025-08-06 16:00:00'),
(100250, 5, NULL, '2025-08-01 06:30:00', '2025-08-01 09:30:00', 0, '2025-07-24 22:23:31', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-07-31 16:00:00'),
(100251, 5, NULL, '2025-08-01 07:00:00', '2025-08-01 10:00:00', 0, '2025-07-24 22:23:31', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-07-31 16:00:00'),
(100252, 5, NULL, '2025-08-01 10:00:00', '2025-08-01 13:00:00', 0, '2025-07-24 22:23:31', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-07-31 16:00:00'),
(100253, 5, NULL, '2025-08-01 09:30:00', '2025-08-01 12:30:00', 0, '2025-07-24 22:23:31', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-07-31 16:00:00'),
(100254, 5, NULL, '2025-08-01 12:30:00', '2025-08-01 15:30:00', 0, '2025-07-24 22:23:31', '2025-07-30 17:31:44', 1, NULL, '2025-07-30 17:31:42', '2025-07-31 16:00:00'),
(100255, 4, NULL, '2025-07-28 06:30:00', '2025-07-28 09:30:00', 0, '2025-07-25 10:57:10', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', NULL),
(100256, 4, NULL, '2025-07-29 06:00:00', '2025-07-29 09:00:00', 0, '2025-07-25 10:57:10', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', NULL),
(100257, 4, NULL, '2025-07-30 06:00:00', '2025-07-30 09:00:00', 0, '2025-07-25 10:57:10', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', NULL),
(100258, 4, NULL, '2025-07-31 06:00:00', '2025-07-31 09:00:00', 0, '2025-07-25 10:57:10', '2025-07-25 10:57:10', 1, NULL, '2025-07-25 10:57:10', NULL),
(100259, 4, NULL, '2025-08-01 06:00:00', '2025-08-01 09:00:00', 0, '2025-07-25 10:57:11', '2025-07-25 10:57:11', 1, NULL, '2025-07-25 10:57:11', NULL),
(100260, 2, NULL, '2025-07-27 01:00:00', '2025-07-27 02:00:00', 0, '2025-07-27 17:34:16', '2025-07-27 17:34:16', 1, NULL, '2025-07-27 17:34:16', NULL),
(100261, 2, NULL, '2025-07-27 02:00:00', '2025-07-27 03:00:00', 0, '2025-07-27 17:34:16', '2025-07-27 17:34:16', 1, NULL, '2025-07-27 17:34:16', NULL),
(100262, 3, NULL, '2025-07-27 01:00:00', '2025-07-27 02:00:00', 0, '2025-07-27 17:34:16', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-12 16:00:00'),
(100263, 3, NULL, '2025-07-27 02:00:00', '2025-07-27 03:00:00', 0, '2025-07-27 17:34:17', '2025-07-27 19:04:45', 1, NULL, '2025-07-27 19:04:44', '2025-07-12 16:00:00'),
(100264, 1, NULL, '2025-07-27 07:00:00', '2025-07-27 08:00:00', 0, '2025-07-27 17:34:17', '2025-07-27 19:04:44', 1, NULL, '2025-07-27 19:04:44', '2025-07-26 16:00:00'),
(100265, 1, NULL, '2025-07-27 10:00:00', '2025-07-27 11:00:00', 0, '2025-07-27 17:34:17', '2025-07-27 19:04:44', 1, NULL, '2025-07-27 19:04:44', '2025-07-26 16:00:00'),
(100266, 1, NULL, '2025-07-28 06:00:00', '2025-07-28 08:00:00', 0, '2025-07-27 17:34:17', '2025-07-27 19:04:44', 1, NULL, '2025-07-27 19:04:44', '2025-07-27 16:00:00'),
(100267, 1, NULL, '2025-07-28 15:00:00', '2025-07-28 16:00:00', 0, '2025-07-27 17:34:17', '2025-07-27 19:04:44', 1, NULL, '2025-07-27 19:04:44', '2025-07-27 16:00:00'),
(100268, 1, NULL, '2025-07-29 08:00:00', '2025-07-29 09:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-28 16:00:00'),
(100269, 1, NULL, '2025-07-29 06:00:00', '2025-07-29 07:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-28 16:00:00'),
(100270, 1, NULL, '2025-07-30 09:00:00', '2025-07-30 10:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-29 16:00:00'),
(100271, 1, NULL, '2025-07-30 11:00:00', '2025-07-30 14:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-29 16:00:00'),
(100272, 2, NULL, '2025-07-30 06:00:00', '2025-07-30 09:00:00', 0, '2025-07-27 17:34:18', '2025-07-27 17:34:18', 1, NULL, '2025-07-27 17:34:18', NULL),
(100273, 1, NULL, '2025-07-31 14:00:00', '2025-07-31 18:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-30 16:00:00'),
(100274, 1, NULL, '2025-08-01 08:00:00', '2025-08-01 12:00:00', 0, '2025-07-27 17:34:18', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-07-31 16:00:00'),
(100275, 2, NULL, '2025-08-03 01:00:00', '2025-08-03 02:00:00', 0, '2025-07-27 17:34:19', '2025-07-27 17:34:19', 1, NULL, '2025-07-27 17:34:19', NULL),
(100276, 2, NULL, '2025-08-03 02:00:00', '2025-08-03 03:00:00', 0, '2025-07-27 17:34:19', '2025-07-27 17:34:19', 1, NULL, '2025-07-27 17:34:19', NULL),
(100277, 3, NULL, '2025-08-03 01:00:00', '2025-08-03 02:00:00', 0, '2025-07-27 17:34:19', '2025-07-30 00:15:07', 1, NULL, '2025-07-30 00:15:06', '2025-07-19 16:00:00'),
(100278, 3, NULL, '2025-08-03 02:00:00', '2025-08-03 03:00:00', 0, '2025-07-27 17:34:19', '2025-07-30 00:15:07', 1, NULL, '2025-07-30 00:15:06', '2025-07-19 16:00:00'),
(100279, 1, NULL, '2025-08-03 07:00:00', '2025-08-03 08:00:00', 0, '2025-07-27 17:34:19', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-08-02 16:00:00'),
(100280, 1, NULL, '2025-08-03 10:00:00', '2025-08-03 11:00:00', 0, '2025-07-27 17:34:19', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', '2025-08-02 16:00:00'),
(100281, 1, NULL, '2025-08-04 15:00:00', '2025-08-04 16:00:00', 0, '2025-07-27 17:34:19', '2025-07-30 00:15:07', 1, NULL, '2025-07-30 00:15:06', '2025-08-03 16:00:00'),
(100282, 4, NULL, '2025-08-04 06:30:00', '2025-08-04 09:30:00', 0, '2025-07-27 17:34:19', '2025-07-27 17:34:20', 1, NULL, '2025-07-27 17:34:20', NULL),
(100283, 1, NULL, '2025-08-05 08:00:00', '2025-08-05 09:00:00', 0, '2025-07-27 17:34:20', '2025-08-06 00:15:06', 1, NULL, '2025-08-06 00:15:06', '2025-08-04 16:00:00'),
(100284, 1, NULL, '2025-08-05 06:00:00', '2025-08-05 07:00:00', 0, '2025-07-27 17:34:20', '2025-08-06 00:15:06', 1, NULL, '2025-08-06 00:15:06', '2025-08-04 16:00:00'),
(100285, 4, NULL, '2025-08-05 06:00:00', '2025-08-05 09:00:00', 0, '2025-07-27 17:34:20', '2025-07-27 17:34:20', 1, NULL, '2025-07-27 17:34:20', NULL),
(100286, 1, NULL, '2025-08-06 09:00:00', '2025-08-06 10:00:00', 0, '2025-07-27 17:34:20', '2025-08-06 00:15:06', 1, NULL, '2025-08-06 00:15:06', '2025-08-05 16:00:00'),
(100287, 1, NULL, '2025-08-06 11:00:00', '2025-08-06 14:00:00', 0, '2025-07-27 17:34:20', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-08-05 16:00:00'),
(100288, 2, NULL, '2025-08-06 06:00:00', '2025-08-06 09:00:00', 0, '2025-07-27 17:34:20', '2025-07-27 17:34:20', 1, NULL, '2025-07-27 17:34:20', NULL),
(100289, 4, NULL, '2025-08-06 06:00:00', '2025-08-06 09:00:00', 0, '2025-07-27 17:34:20', '2025-07-27 17:34:20', 1, NULL, '2025-07-27 17:34:20', NULL),
(100290, 1, NULL, '2025-08-07 14:00:00', '2025-08-07 17:00:00', 0, '2025-07-27 17:34:21', '2025-08-07 12:35:36', 1, NULL, '2025-08-06 00:15:06', '2025-08-06 16:00:00'),
(100291, 4, NULL, '2025-08-07 06:00:00', '2025-08-07 09:00:00', 0, '2025-07-27 17:34:21', '2025-07-27 17:34:21', 1, NULL, '2025-07-27 17:34:21', NULL),
(100292, 1, NULL, '2025-08-08 08:00:00', '2025-08-08 11:00:00', 0, '2025-07-27 17:34:21', '2025-08-07 12:55:23', 1, NULL, '2025-08-06 00:15:06', '2025-08-07 16:00:00'),
(100293, 5, NULL, '2025-08-08 13:30:00', '2025-08-08 16:30:00', 0, '2025-07-27 17:34:21', '2025-08-08 08:13:33', 1, NULL, '2025-08-06 22:00:05', '2025-08-07 16:00:00'),
(100294, 5, NULL, '2025-08-08 07:00:00', '2025-08-08 10:00:00', 0, '2025-07-27 17:34:21', '2025-08-06 22:00:06', 1, NULL, '2025-08-06 22:00:05', '2025-08-07 16:00:00'),
(100295, 5, NULL, '2025-08-08 10:00:00', '2025-08-08 13:00:00', 0, '2025-07-27 17:34:21', '2025-08-08 08:16:38', 1, '2025-08-08 08:16:38', '2025-08-06 22:00:05', '2025-08-07 16:00:00'),
(100296, 5, NULL, '2025-08-08 13:00:00', '2025-08-08 16:00:00', 0, '2025-07-27 17:34:21', '2025-08-08 08:17:24', 1, NULL, '2025-08-06 22:00:05', '2025-08-07 16:00:00'),
(100297, 5, NULL, '2025-08-08 13:30:00', '2025-08-08 16:30:00', 0, '2025-07-27 17:34:21', '2025-08-08 08:14:20', 1, NULL, '2025-08-06 22:00:05', '2025-08-07 16:00:00'),
(100298, 4, NULL, '2025-08-08 06:00:00', '2025-08-08 09:00:00', 0, '2025-07-27 17:34:21', '2025-07-27 17:34:21', 1, NULL, '2025-07-27 17:34:21', NULL),
(100299, 2, NULL, '2025-08-10 01:00:00', '2025-08-10 02:00:00', 0, '2025-07-28 00:15:05', '2025-08-07 11:26:52', 1, NULL, '2025-08-07 11:26:52', NULL),
(100300, 2, NULL, '2025-08-10 02:00:00', '2025-08-10 03:00:00', 0, '2025-07-28 00:15:05', '2025-08-07 11:26:53', 1, NULL, '2025-08-07 11:26:53', NULL),
(100301, 3, NULL, '2025-08-10 01:00:00', '2025-08-10 02:00:00', 0, '2025-07-28 00:15:05', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-07-26 16:00:00'),
(100302, 3, NULL, '2025-08-10 02:00:00', '2025-08-10 03:00:00', 0, '2025-07-28 00:15:05', '2025-08-06 00:15:08', 1, NULL, '2025-08-06 00:15:06', '2025-07-26 16:00:00'),
(100303, 4, NULL, '2025-08-10 01:00:00', '2025-08-10 02:00:00', 0, '2025-07-28 00:15:05', '2025-07-28 00:15:05', 1, NULL, '2025-07-28 00:15:05', NULL),
(100304, 4, NULL, '2025-08-10 03:00:00', '2025-08-10 04:00:00', 0, '2025-07-28 00:15:06', '2025-07-28 00:15:06', 1, NULL, '2025-07-28 00:15:06', NULL),
(100305, 1, NULL, '2025-08-10 07:00:00', '2025-08-10 08:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-08-09 16:00:00'),
(100306, 1, NULL, '2025-08-10 10:00:00', '2025-08-10 11:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-08-09 16:00:00'),
(100307, 1, NULL, '2025-08-11 06:00:00', '2025-08-11 08:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-08-10 16:00:00'),
(100308, 5, NULL, '2025-08-11 06:30:00', '2025-08-11 09:30:00', 0, '2025-07-28 00:15:06', '2025-08-06 22:00:06', 1, NULL, '2025-08-06 22:00:05', '2025-08-10 16:00:00'),
(100309, 5, NULL, '2025-08-11 07:00:00', '2025-08-11 10:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 22:00:06', 1, NULL, '2025-08-06 22:00:05', '2025-08-10 16:00:00'),
(100310, 1, NULL, '2025-08-11 15:00:00', '2025-08-11 16:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 00:15:07', 1, NULL, '2025-08-06 00:15:06', '2025-08-10 16:00:00'),
(100311, 5, NULL, '2025-08-11 09:30:00', '2025-08-11 12:30:00', 0, '2025-07-28 00:15:06', '2025-08-06 22:00:06', 1, NULL, '2025-08-06 22:00:05', '2025-08-10 16:00:00'),
(100312, 5, NULL, '2025-08-11 10:00:00', '2025-08-11 13:00:00', 0, '2025-07-28 00:15:06', '2025-08-06 22:00:06', 1, NULL, '2025-08-06 22:00:05', '2025-08-10 16:00:00'),
(100313, 5, NULL, '2025-08-11 12:30:00', '2025-08-11 15:30:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:00:07', 1, NULL, '2025-08-06 22:00:05', '2025-08-10 16:00:00'),
(100314, 4, NULL, '2025-08-11 06:30:00', '2025-08-11 09:30:00', 0, '2025-07-28 00:15:07', '2025-07-28 00:15:07', 1, NULL, '2025-07-28 00:15:07', NULL),
(100315, 1, NULL, '2025-08-12 08:00:00', '2025-08-12 09:00:00', 0, '2025-07-28 00:15:07', '2025-07-28 00:15:07', 1, NULL, NULL, NULL),
(100316, 1, NULL, '2025-08-12 06:00:00', '2025-08-12 07:00:00', 0, '2025-07-28 00:15:07', '2025-07-28 00:15:07', 1, NULL, NULL, NULL),
(100317, 5, NULL, '2025-08-12 06:30:00', '2025-08-12 09:30:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:00:07', 1, NULL, '2025-08-06 22:00:05', '2025-08-11 16:00:00'),
(100318, 5, NULL, '2025-08-12 07:00:00', '2025-08-12 10:00:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:00:07', 1, NULL, '2025-08-06 22:00:05', '2025-08-11 16:00:00'),
(100319, 5, NULL, '2025-08-12 10:00:00', '2025-08-12 13:00:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:00:07', 1, NULL, '2025-08-06 22:00:05', '2025-08-11 16:00:00'),
(100320, 5, NULL, '2025-08-12 09:30:00', '2025-08-12 12:30:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:00:07', 1, NULL, '2025-08-06 22:00:05', '2025-08-11 16:00:00'),
(100321, 5, NULL, '2025-08-12 12:30:00', '2025-08-12 13:30:00', 0, '2025-07-28 00:15:07', '2025-08-06 22:06:50', 1, NULL, '2025-08-06 22:00:05', '2025-08-11 16:00:00'),
(100322, 4, NULL, '2025-08-12 06:00:00', '2025-08-12 09:00:00', 0, '2025-07-28 00:15:07', '2025-07-28 00:15:07', 1, NULL, '2025-07-28 00:15:07', NULL),
(100323, 1, NULL, '2025-08-13 09:00:00', '2025-08-13 10:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100324, 1, NULL, '2025-08-13 11:00:00', '2025-08-13 14:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100325, 5, NULL, '2025-08-13 06:30:00', '2025-08-13 09:30:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100326, 5, NULL, '2025-08-13 07:00:00', '2025-08-13 10:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100327, 5, NULL, '2025-08-13 09:30:00', '2025-08-13 12:30:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100328, 5, NULL, '2025-08-13 10:00:00', '2025-08-13 13:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100329, 5, NULL, '2025-08-13 12:30:00', '2025-08-13 15:30:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100330, 2, NULL, '2025-08-13 06:00:00', '2025-08-13 09:00:00', 0, '2025-07-28 00:15:08', '2025-08-07 11:26:53', 1, NULL, '2025-08-07 11:26:53', NULL),
(100331, 4, NULL, '2025-08-13 06:00:00', '2025-08-13 09:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, '2025-07-28 00:15:08', NULL),
(100332, 1, NULL, '2025-08-14 14:00:00', '2025-08-14 18:00:00', 0, '2025-07-28 00:15:08', '2025-07-28 00:15:08', 1, NULL, NULL, NULL),
(100333, 5, NULL, '2025-08-14 06:30:00', '2025-08-14 09:30:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100334, 5, NULL, '2025-08-14 07:00:00', '2025-08-14 10:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100335, 5, NULL, '2025-08-14 09:30:00', '2025-08-14 12:30:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100336, 5, NULL, '2025-08-14 10:00:00', '2025-08-14 13:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100337, 5, NULL, '2025-08-14 12:30:00', '2025-08-14 15:30:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100338, 4, NULL, '2025-08-14 06:00:00', '2025-08-14 09:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, '2025-07-28 00:15:09', NULL),
(100339, 1, NULL, '2025-08-15 08:00:00', '2025-08-15 12:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100340, 5, NULL, '2025-08-15 06:30:00', '2025-08-15 09:30:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100341, 5, NULL, '2025-08-15 07:00:00', '2025-08-15 10:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100342, 5, NULL, '2025-08-15 10:00:00', '2025-08-15 13:00:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100343, 5, NULL, '2025-08-15 09:30:00', '2025-08-15 12:30:00', 0, '2025-07-28 00:15:09', '2025-07-28 00:15:09', 1, NULL, NULL, NULL),
(100344, 5, NULL, '2025-08-15 12:30:00', '2025-08-15 15:30:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, NULL, NULL),
(100345, 4, NULL, '2025-08-15 06:00:00', '2025-08-15 09:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, '2025-07-28 00:15:10', NULL),
(100346, 2, NULL, '2025-08-17 01:00:00', '2025-08-17 02:00:00', 0, '2025-07-28 00:15:10', '2025-08-07 11:26:53', 1, NULL, '2025-08-07 11:26:53', NULL),
(100347, 2, NULL, '2025-08-17 02:00:00', '2025-08-17 03:00:00', 0, '2025-07-28 00:15:10', '2025-08-07 11:26:53', 1, NULL, '2025-08-07 11:26:53', NULL),
(100348, 3, NULL, '2025-08-17 01:00:00', '2025-08-17 02:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, NULL, NULL),
(100349, 3, NULL, '2025-08-17 02:00:00', '2025-08-17 03:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, NULL, NULL),
(100350, 4, NULL, '2025-08-17 01:00:00', '2025-08-17 02:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, '2025-07-28 00:15:10', NULL),
(100351, 4, NULL, '2025-08-17 03:00:00', '2025-08-17 04:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, '2025-07-28 00:15:10', NULL),
(100352, 1, NULL, '2025-08-17 07:00:00', '2025-08-17 08:00:00', 0, '2025-07-28 00:15:10', '2025-07-28 00:15:10', 1, NULL, NULL, NULL),
(100353, 1, NULL, '2025-08-17 10:00:00', '2025-08-17 11:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100354, 1, NULL, '2025-08-18 06:00:00', '2025-08-18 08:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100355, 5, NULL, '2025-08-18 06:30:00', '2025-08-18 09:30:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100356, 5, NULL, '2025-08-18 07:00:00', '2025-08-18 10:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100357, 1, NULL, '2025-08-18 15:00:00', '2025-08-18 16:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100358, 5, NULL, '2025-08-18 09:30:00', '2025-08-18 12:30:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100359, 5, NULL, '2025-08-18 10:00:00', '2025-08-18 13:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100360, 5, NULL, '2025-08-18 12:30:00', '2025-08-18 15:30:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100361, 4, NULL, '2025-08-18 06:30:00', '2025-08-18 09:30:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, '2025-07-28 00:15:11', NULL),
(100362, 1, NULL, '2025-08-19 08:00:00', '2025-08-19 09:00:00', 0, '2025-07-28 00:15:11', '2025-07-28 00:15:11', 1, NULL, NULL, NULL),
(100363, 1, NULL, '2025-08-19 06:00:00', '2025-08-19 07:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100364, 5, NULL, '2025-08-19 06:30:00', '2025-08-19 09:30:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100365, 5, NULL, '2025-08-19 07:00:00', '2025-08-19 10:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100366, 5, NULL, '2025-08-19 10:00:00', '2025-08-19 13:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100367, 5, NULL, '2025-08-19 09:30:00', '2025-08-19 12:30:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100368, 5, NULL, '2025-08-19 12:30:00', '2025-08-19 15:30:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100369, 4, NULL, '2025-08-19 06:00:00', '2025-08-19 09:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, '2025-07-28 00:15:12', NULL),
(100370, 1, NULL, '2025-08-20 09:00:00', '2025-08-20 10:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100371, 1, NULL, '2025-08-20 11:00:00', '2025-08-20 14:00:00', 0, '2025-07-28 00:15:12', '2025-07-28 00:15:12', 1, NULL, NULL, NULL),
(100372, 5, NULL, '2025-08-20 06:30:00', '2025-08-20 09:30:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100373, 5, NULL, '2025-08-20 07:00:00', '2025-08-20 10:00:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100374, 5, NULL, '2025-08-20 09:30:00', '2025-08-20 12:30:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100375, 5, NULL, '2025-08-20 10:00:00', '2025-08-20 13:00:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100376, 5, NULL, '2025-08-20 12:30:00', '2025-08-20 15:30:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100377, 2, NULL, '2025-08-20 06:00:00', '2025-08-20 09:00:00', 0, '2025-07-28 00:15:13', '2025-08-07 11:26:53', 1, NULL, '2025-08-07 11:26:53', NULL),
(100378, 4, NULL, '2025-08-20 06:00:00', '2025-08-20 09:00:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, '2025-07-28 00:15:13', NULL),
(100379, 1, NULL, '2025-08-21 14:00:00', '2025-08-21 18:00:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100380, 5, NULL, '2025-08-21 06:30:00', '2025-08-21 09:30:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100381, 5, NULL, '2025-08-21 07:00:00', '2025-08-21 10:00:00', 0, '2025-07-28 00:15:13', '2025-07-28 00:15:13', 1, NULL, NULL, NULL),
(100382, 5, NULL, '2025-08-21 09:30:00', '2025-08-21 12:30:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100383, 5, NULL, '2025-08-21 10:00:00', '2025-08-21 13:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100384, 5, NULL, '2025-08-21 12:30:00', '2025-08-21 15:30:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100385, 4, NULL, '2025-08-21 06:00:00', '2025-08-21 09:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, '2025-07-28 00:15:14', NULL),
(100386, 1, NULL, '2025-08-22 08:00:00', '2025-08-22 12:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100387, 5, NULL, '2025-08-22 06:30:00', '2025-08-22 09:30:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100388, 5, NULL, '2025-08-22 07:00:00', '2025-08-22 10:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100389, 5, NULL, '2025-08-22 10:00:00', '2025-08-22 13:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100390, 5, NULL, '2025-08-22 09:30:00', '2025-08-22 12:30:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100391, 5, NULL, '2025-08-22 12:30:00', '2025-08-22 15:30:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, NULL, NULL),
(100392, 4, NULL, '2025-08-22 06:00:00', '2025-08-22 09:00:00', 0, '2025-07-28 00:15:14', '2025-07-28 00:15:14', 1, NULL, '2025-07-28 00:15:14', NULL),
(100393, 2, NULL, '2025-08-24 01:00:00', '2025-08-24 02:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, '2025-07-28 00:15:15', NULL),
(100394, 2, NULL, '2025-08-24 02:00:00', '2025-08-24 03:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, '2025-07-28 00:15:15', NULL),
(100395, 3, NULL, '2025-08-24 01:00:00', '2025-08-24 02:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, NULL, NULL),
(100396, 3, NULL, '2025-08-24 02:00:00', '2025-08-24 03:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, NULL, NULL),
(100397, 4, NULL, '2025-08-24 01:00:00', '2025-08-24 02:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, '2025-07-28 00:15:15', NULL),
(100398, 4, NULL, '2025-08-24 03:00:00', '2025-08-24 04:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, '2025-07-28 00:15:15', NULL),
(100399, 1, NULL, '2025-08-24 07:00:00', '2025-08-24 08:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, NULL, NULL),
(100400, 1, NULL, '2025-08-24 10:00:00', '2025-08-24 11:00:00', 0, '2025-07-28 00:15:15', '2025-07-28 00:15:15', 1, NULL, NULL, NULL),
(100401, 1, NULL, '2025-08-25 06:00:00', '2025-08-25 08:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100402, 5, NULL, '2025-08-25 06:30:00', '2025-08-25 09:30:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100403, 5, NULL, '2025-08-25 07:00:00', '2025-08-25 10:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100404, 1, NULL, '2025-08-25 15:00:00', '2025-08-25 16:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100405, 5, NULL, '2025-08-25 09:30:00', '2025-08-25 12:30:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100406, 5, NULL, '2025-08-25 10:00:00', '2025-08-25 13:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100407, 5, NULL, '2025-08-25 12:30:00', '2025-08-25 15:30:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100408, 4, NULL, '2025-08-25 06:30:00', '2025-08-25 09:30:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, '2025-07-28 00:15:16', NULL),
(100409, 1, NULL, '2025-08-26 08:00:00', '2025-08-26 09:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100410, 1, NULL, '2025-08-26 06:00:00', '2025-08-26 07:00:00', 0, '2025-07-28 00:15:16', '2025-07-28 00:15:16', 1, NULL, NULL, NULL),
(100411, 5, NULL, '2025-08-26 06:30:00', '2025-08-26 09:30:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, NULL, NULL),
(100412, 5, NULL, '2025-08-26 07:00:00', '2025-08-26 10:00:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, NULL, NULL),
(100413, 5, NULL, '2025-08-26 10:00:00', '2025-08-26 13:00:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, NULL, NULL),
(100414, 5, NULL, '2025-08-26 09:30:00', '2025-08-26 12:30:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, NULL, NULL),
(100415, 5, NULL, '2025-08-26 12:30:00', '2025-08-26 15:30:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, NULL, NULL),
(100416, 4, NULL, '2025-08-26 06:00:00', '2025-08-26 09:00:00', 0, '2025-07-28 00:15:17', '2025-07-28 00:15:17', 1, NULL, '2025-07-28 00:15:17', NULL),
(100417, 1, NULL, '2025-08-27 09:00:00', '2025-08-27 10:00:00', 0, '2025-07-29 00:15:05', '2025-07-29 00:15:05', 1, NULL, NULL, NULL),
(100418, 1, NULL, '2025-08-27 11:00:00', '2025-08-27 14:00:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100419, 5, NULL, '2025-08-27 06:30:00', '2025-08-27 09:30:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100420, 5, NULL, '2025-08-27 07:00:00', '2025-08-27 10:00:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100421, 5, NULL, '2025-08-27 09:30:00', '2025-08-27 12:30:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100422, 5, NULL, '2025-08-27 10:00:00', '2025-08-27 13:00:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100423, 5, NULL, '2025-08-27 12:30:00', '2025-08-27 15:30:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, NULL, NULL),
(100424, 2, NULL, '2025-08-27 06:00:00', '2025-08-27 09:00:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, '2025-07-29 00:15:06', NULL),
(100425, 4, NULL, '2025-08-27 06:00:00', '2025-08-27 09:00:00', 0, '2025-07-29 00:15:06', '2025-07-29 00:15:06', 1, NULL, '2025-07-29 00:15:06', NULL),
(100426, 1, NULL, '2025-08-28 14:00:00', '2025-08-28 18:00:00', 0, '2025-07-30 00:15:05', '2025-07-30 00:15:05', 1, NULL, NULL, NULL),
(100427, 5, NULL, '2025-08-28 06:30:00', '2025-08-28 09:30:00', 0, '2025-07-30 00:15:05', '2025-07-30 00:15:05', 1, NULL, NULL, NULL),
(100428, 5, NULL, '2025-08-28 07:00:00', '2025-08-28 10:00:00', 0, '2025-07-30 00:15:05', '2025-07-30 00:15:05', 1, NULL, NULL, NULL),
(100429, 5, NULL, '2025-08-28 09:30:00', '2025-08-28 12:30:00', 0, '2025-07-30 00:15:05', '2025-07-30 00:15:05', 1, NULL, NULL, NULL),
(100430, 5, NULL, '2025-08-28 10:00:00', '2025-08-28 13:00:00', 0, '2025-07-30 00:15:05', '2025-07-30 00:15:05', 1, NULL, NULL, NULL),
(100431, 5, NULL, '2025-08-28 12:30:00', '2025-08-28 15:30:00', 0, '2025-07-30 00:15:06', '2025-07-30 00:15:06', 1, NULL, NULL, NULL),
(100432, 4, NULL, '2025-08-28 06:00:00', '2025-08-28 09:00:00', 0, '2025-07-30 00:15:06', '2025-07-30 00:15:06', 1, NULL, '2025-07-30 00:15:06', NULL),
(100433, 1, NULL, '2025-08-29 08:00:00', '2025-08-29 12:00:00', 0, '2025-07-31 00:15:05', '2025-07-31 00:15:05', 1, NULL, NULL, NULL),
(100434, 5, NULL, '2025-08-29 06:30:00', '2025-08-29 09:30:00', 0, '2025-07-31 00:15:05', '2025-07-31 00:15:05', 1, NULL, NULL, NULL),
(100435, 5, NULL, '2025-08-29 07:00:00', '2025-08-29 10:00:00', 0, '2025-07-31 00:15:05', '2025-07-31 00:15:05', 1, NULL, NULL, NULL),
(100436, 5, NULL, '2025-08-29 10:00:00', '2025-08-29 13:00:00', 0, '2025-07-31 00:15:06', '2025-07-31 00:15:06', 1, NULL, NULL, NULL),
(100437, 5, NULL, '2025-08-29 09:30:00', '2025-08-29 12:30:00', 0, '2025-07-31 00:15:06', '2025-07-31 00:15:06', 1, NULL, NULL, NULL),
(100438, 5, NULL, '2025-08-29 12:30:00', '2025-08-29 15:30:00', 0, '2025-07-31 00:15:06', '2025-07-31 00:15:06', 1, NULL, NULL, NULL),
(100439, 4, NULL, '2025-08-29 06:00:00', '2025-08-29 09:00:00', 0, '2025-07-31 00:15:06', '2025-07-31 00:15:06', 1, NULL, '2025-07-31 00:15:06', NULL),
(100440, 5, NULL, '2025-08-05 09:30:00', '2025-08-05 12:30:00', 0, '2025-08-02 00:15:05', '2025-08-02 00:15:06', 1, NULL, '2025-08-02 00:15:06', '2025-08-04 16:00:00'),
(100441, 2, NULL, '2025-08-31 01:00:00', '2025-08-31 02:00:00', 0, '2025-08-02 00:15:05', '2025-08-02 00:15:05', 1, NULL, '2025-08-02 00:15:05', NULL),
(100442, 2, NULL, '2025-08-31 02:00:00', '2025-08-31 03:00:00', 0, '2025-08-02 00:15:05', '2025-08-02 00:15:05', 1, NULL, '2025-08-02 00:15:05', NULL),
(100443, 3, NULL, '2025-08-31 01:00:00', '2025-08-31 02:00:00', 0, '2025-08-02 00:15:05', '2025-08-02 00:15:05', 1, NULL, NULL, NULL),
(100444, 3, NULL, '2025-08-31 02:00:00', '2025-08-31 03:00:00', 0, '2025-08-02 00:15:05', '2025-08-02 00:15:05', 1, NULL, NULL, NULL),
(100445, 4, NULL, '2025-08-31 01:00:00', '2025-08-31 02:00:00', 0, '2025-08-02 00:15:06', '2025-08-02 00:15:06', 1, NULL, '2025-08-02 00:15:06', NULL),
(100446, 4, NULL, '2025-08-31 03:00:00', '2025-08-31 04:00:00', 0, '2025-08-02 00:15:06', '2025-08-02 00:15:06', 1, NULL, '2025-08-02 00:15:06', NULL),
(100447, 1, NULL, '2025-08-31 07:00:00', '2025-08-31 08:00:00', 0, '2025-08-02 00:15:06', '2025-08-02 00:15:06', 1, NULL, NULL, NULL),
(100448, 1, NULL, '2025-08-31 10:00:00', '2025-08-31 11:00:00', 0, '2025-08-02 00:15:06', '2025-08-02 00:15:06', 1, NULL, NULL, NULL),
(100449, 1, NULL, '2025-09-01 06:00:00', '2025-09-01 08:00:00', 0, '2025-08-03 00:15:06', '2025-08-03 00:15:06', 1, NULL, NULL, NULL),
(100450, 5, NULL, '2025-09-01 06:30:00', '2025-09-01 09:30:00', 0, '2025-08-03 00:15:06', '2025-08-03 00:15:06', 1, NULL, NULL, NULL),
(100451, 5, NULL, '2025-09-01 07:00:00', '2025-09-01 10:00:00', 0, '2025-08-03 00:15:06', '2025-08-03 00:15:06', 1, NULL, NULL, NULL),
(100452, 1, NULL, '2025-09-01 15:00:00', '2025-09-01 16:00:00', 0, '2025-08-03 00:15:06', '2025-08-03 00:15:06', 1, NULL, NULL, NULL),
(100453, 5, NULL, '2025-09-01 09:30:00', '2025-09-01 12:30:00', 0, '2025-08-03 00:15:07', '2025-08-03 00:15:07', 1, NULL, NULL, NULL),
(100454, 5, NULL, '2025-09-01 10:00:00', '2025-09-01 13:00:00', 0, '2025-08-03 00:15:07', '2025-08-03 00:15:07', 1, NULL, NULL, NULL),
(100455, 5, NULL, '2025-09-01 12:30:00', '2025-09-01 15:30:00', 0, '2025-08-03 00:15:07', '2025-08-03 00:15:07', 1, NULL, NULL, NULL),
(100456, 4, NULL, '2025-09-01 06:30:00', '2025-09-01 09:30:00', 0, '2025-08-03 00:15:07', '2025-08-03 00:15:07', 1, NULL, '2025-08-03 00:15:07', NULL),
(100457, 1, NULL, '2025-09-02 08:00:00', '2025-09-02 09:00:00', 0, '2025-08-04 00:15:05', '2025-08-04 00:15:05', 1, NULL, NULL, NULL),
(100458, 1, NULL, '2025-09-02 06:00:00', '2025-09-02 07:00:00', 0, '2025-08-04 00:15:05', '2025-08-04 00:15:05', 1, NULL, NULL, NULL),
(100459, 5, NULL, '2025-09-02 06:30:00', '2025-09-02 09:30:00', 0, '2025-08-04 00:15:05', '2025-08-04 00:15:05', 1, NULL, NULL, NULL),
(100460, 5, NULL, '2025-09-02 07:00:00', '2025-09-02 10:00:00', 0, '2025-08-04 00:15:06', '2025-08-04 00:15:06', 1, NULL, NULL, NULL),
(100461, 5, NULL, '2025-09-02 10:00:00', '2025-09-02 13:00:00', 0, '2025-08-04 00:15:06', '2025-08-04 00:15:06', 1, NULL, NULL, NULL),
(100462, 5, NULL, '2025-09-02 09:30:00', '2025-09-02 12:30:00', 0, '2025-08-04 00:15:06', '2025-08-04 00:15:06', 1, NULL, NULL, NULL),
(100463, 5, NULL, '2025-09-02 12:30:00', '2025-09-02 15:30:00', 0, '2025-08-04 00:15:06', '2025-08-04 00:15:06', 1, NULL, NULL, NULL),
(100464, 4, NULL, '2025-09-02 06:00:00', '2025-09-02 09:00:00', 0, '2025-08-04 00:15:06', '2025-08-04 00:15:06', 1, NULL, '2025-08-04 00:15:06', NULL),
(100465, 1, NULL, '2025-09-03 09:00:00', '2025-09-03 10:00:00', 0, '2025-08-05 00:15:05', '2025-08-05 00:15:05', 1, NULL, NULL, NULL),
(100466, 1, NULL, '2025-09-03 11:00:00', '2025-09-03 14:00:00', 0, '2025-08-05 00:15:05', '2025-08-05 00:15:05', 1, NULL, NULL, NULL),
(100467, 5, NULL, '2025-09-03 06:30:00', '2025-09-03 09:30:00', 0, '2025-08-05 00:15:05', '2025-08-05 00:15:05', 1, NULL, NULL, NULL),
(100468, 5, NULL, '2025-09-03 07:00:00', '2025-09-03 10:00:00', 0, '2025-08-05 00:15:05', '2025-08-05 00:15:05', 1, NULL, NULL, NULL),
(100469, 5, NULL, '2025-09-03 09:30:00', '2025-09-03 12:30:00', 0, '2025-08-05 00:15:06', '2025-08-05 00:15:06', 1, NULL, NULL, NULL),
(100470, 5, NULL, '2025-09-03 10:00:00', '2025-09-03 13:00:00', 0, '2025-08-05 00:15:06', '2025-08-05 00:15:06', 1, NULL, NULL, NULL),
(100471, 5, NULL, '2025-09-03 12:30:00', '2025-09-03 15:30:00', 0, '2025-08-05 00:15:06', '2025-08-05 00:15:06', 1, NULL, NULL, NULL),
(100472, 2, NULL, '2025-09-03 06:00:00', '2025-09-03 09:00:00', 0, '2025-08-05 00:15:06', '2025-08-05 00:15:06', 1, NULL, '2025-08-05 00:15:06', NULL),
(100473, 4, NULL, '2025-09-03 06:00:00', '2025-09-03 09:00:00', 0, '2025-08-05 00:15:06', '2025-08-05 00:15:06', 1, NULL, '2025-08-05 00:15:06', NULL),
(100474, 1, NULL, '2025-09-04 14:00:00', '2025-09-04 18:00:00', 0, '2025-08-06 00:15:05', '2025-08-06 00:15:05', 1, NULL, NULL, NULL),
(100475, 5, NULL, '2025-09-04 06:30:00', '2025-09-04 09:30:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, NULL, NULL),
(100476, 5, NULL, '2025-09-04 07:00:00', '2025-09-04 10:00:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, NULL, NULL),
(100477, 5, NULL, '2025-09-04 09:30:00', '2025-09-04 12:30:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, NULL, NULL),
(100478, 5, NULL, '2025-09-04 10:00:00', '2025-09-04 13:00:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, NULL, NULL),
(100479, 5, NULL, '2025-09-04 12:30:00', '2025-09-04 15:30:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, NULL, NULL),
(100480, 4, NULL, '2025-09-04 06:00:00', '2025-09-04 09:00:00', 0, '2025-08-06 00:15:06', '2025-08-06 00:15:06', 1, NULL, '2025-08-06 00:15:06', NULL),
(100481, 5, NULL, '2025-08-12 12:30:00', '2025-08-12 15:30:00', 0, '2025-08-07 00:15:05', '2025-08-07 00:15:07', 1, NULL, '2025-08-07 00:15:07', '2025-08-11 16:00:00'),
(100482, 1, NULL, '2025-09-05 08:00:00', '2025-09-05 12:00:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100483, 5, NULL, '2025-09-05 06:30:00', '2025-09-05 09:30:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100484, 5, NULL, '2025-09-05 07:00:00', '2025-09-05 10:00:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100485, 5, NULL, '2025-09-05 10:00:00', '2025-09-05 13:00:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100486, 5, NULL, '2025-09-05 09:30:00', '2025-09-05 12:30:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100487, 5, NULL, '2025-09-05 12:30:00', '2025-09-05 15:30:00', 0, '2025-08-07 00:15:06', '2025-08-07 00:15:06', 1, NULL, NULL, NULL),
(100488, 4, NULL, '2025-09-05 06:00:00', '2025-09-05 09:00:00', 0, '2025-08-07 00:15:07', '2025-08-07 00:15:07', 1, NULL, '2025-08-07 00:15:07', NULL),
(100489, 1, NULL, '2025-08-08 08:00:00', '2025-08-08 12:00:00', 0, '2025-08-08 00:15:05', '2025-08-08 00:15:06', 1, NULL, '2025-08-08 00:15:06', '2025-08-07 16:00:00'),
(100490, 3, NULL, '2025-08-08 08:00:00', '2025-08-08 12:00:00', 0, '2025-08-08 00:15:05', '2025-08-08 00:15:06', 1, NULL, '2025-08-08 00:15:06', '2025-07-24 16:00:00'),
(100491, 3, NULL, '2025-08-15 08:00:00', '2025-08-15 12:00:00', 0, '2025-08-08 00:15:05', '2025-08-08 00:15:05', 1, NULL, NULL, NULL),
(100492, 3, NULL, '2025-08-22 08:00:00', '2025-08-22 12:00:00', 0, '2025-08-08 00:15:05', '2025-08-08 00:15:05', 1, NULL, NULL, NULL),
(100493, 3, NULL, '2025-08-29 08:00:00', '2025-08-29 12:00:00', 0, '2025-08-08 00:15:05', '2025-08-08 00:15:05', 1, NULL, NULL, NULL),
(100494, 3, NULL, '2025-09-05 08:00:00', '2025-09-05 12:00:00', 0, '2025-08-08 00:15:06', '2025-08-08 00:15:06', 1, NULL, NULL, NULL),
(100495, 5, NULL, '2025-08-08 10:00:00', '2025-08-08 13:00:00', 0, '2025-08-08 08:09:29', '2025-08-08 08:16:08', 1, NULL, '2025-08-08 08:15:05', '2025-08-07 16:00:00'),
(100496, 5, NULL, '2025-08-08 16:00:00', '2025-08-08 17:00:00', 0, '2025-08-08 08:18:35', '2025-08-08 08:30:04', 1, NULL, '2025-08-08 08:30:04', '2025-08-07 16:00:00'),
(100497, 2, NULL, '2025-09-07 01:00:00', '2025-09-07 02:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, '2025-08-09 00:15:06', NULL),
(100498, 2, NULL, '2025-09-07 02:00:00', '2025-09-07 03:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, '2025-08-09 00:15:06', NULL),
(100499, 3, NULL, '2025-09-07 01:00:00', '2025-09-07 02:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, NULL, NULL),
(100500, 3, NULL, '2025-09-07 02:00:00', '2025-09-07 03:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, NULL, NULL),
(100501, 4, NULL, '2025-09-07 01:00:00', '2025-09-07 02:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, '2025-08-09 00:15:06', NULL),
(100502, 4, NULL, '2025-09-07 03:00:00', '2025-09-07 04:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, '2025-08-09 00:15:06', NULL),
(100503, 1, NULL, '2025-09-07 07:00:00', '2025-09-07 08:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, NULL, NULL),
(100504, 1, NULL, '2025-09-07 10:00:00', '2025-09-07 11:00:00', 0, '2025-08-09 00:15:06', '2025-08-09 00:15:06', 1, NULL, NULL, NULL),
(100505, 1, NULL, '2025-09-08 06:00:00', '2025-09-08 08:00:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100506, 5, NULL, '2025-09-08 06:30:00', '2025-09-08 09:30:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100507, 5, NULL, '2025-09-08 07:00:00', '2025-09-08 10:00:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100508, 1, NULL, '2025-09-08 15:00:00', '2025-09-08 16:00:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100509, 5, NULL, '2025-09-08 09:30:00', '2025-09-08 12:30:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100510, 5, NULL, '2025-09-08 10:00:00', '2025-09-08 13:00:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100511, 5, NULL, '2025-09-08 12:30:00', '2025-09-08 15:30:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, NULL, NULL),
(100512, 4, NULL, '2025-09-08 06:30:00', '2025-09-08 09:30:00', 0, '2025-08-10 00:15:05', '2025-08-10 00:15:05', 1, NULL, '2025-08-10 00:15:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slot_customer`
--

CREATE TABLE `slot_customer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slot_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_customer`
--

INSERT INTO `slot_customer` (`id`, `slot_id`, `customer_id`, `created_at`, `updated_at`) VALUES
(1, 99960, 4, NULL, NULL),
(2, 99955, 4, NULL, NULL),
(3, 100051, 4, NULL, NULL),
(4, 100053, 4, NULL, NULL),
(5, 100055, 4, NULL, NULL),
(6, 100057, 4, NULL, NULL),
(7, 100061, 4, NULL, NULL),
(137, 100207, 4, NULL, NULL),
(145, 100215, 4, NULL, NULL),
(146, 100216, 4, NULL, NULL),
(147, 100217, 4, NULL, NULL),
(148, 100218, 4, NULL, NULL),
(149, 100219, 4, NULL, NULL),
(276, 100315, 1, NULL, NULL),
(277, 100315, 2, NULL, NULL),
(278, 100315, 4, NULL, NULL),
(279, 100316, 1, NULL, NULL),
(280, 100316, 2, NULL, NULL),
(281, 100316, 4, NULL, NULL),
(287, 100323, 1, NULL, NULL),
(288, 100323, 2, NULL, NULL),
(289, 100323, 4, NULL, NULL),
(290, 100324, 1, NULL, NULL),
(291, 100324, 2, NULL, NULL),
(292, 100324, 4, NULL, NULL),
(293, 100325, 4, NULL, NULL),
(294, 100326, 4, NULL, NULL),
(295, 100327, 4, NULL, NULL),
(296, 100328, 4, NULL, NULL),
(297, 100329, 4, NULL, NULL),
(298, 100332, 1, NULL, NULL),
(299, 100332, 2, NULL, NULL),
(300, 100332, 4, NULL, NULL),
(301, 100333, 4, NULL, NULL),
(302, 100334, 4, NULL, NULL),
(303, 100335, 4, NULL, NULL),
(304, 100336, 4, NULL, NULL),
(305, 100337, 4, NULL, NULL),
(306, 100339, 1, NULL, NULL),
(307, 100339, 2, NULL, NULL),
(308, 100339, 4, NULL, NULL),
(309, 100340, 4, NULL, NULL),
(310, 100341, 4, NULL, NULL),
(311, 100342, 4, NULL, NULL),
(312, 100343, 4, NULL, NULL),
(313, 100344, 4, NULL, NULL),
(314, 100348, 4, NULL, NULL),
(315, 100348, 5, NULL, NULL),
(316, 100349, 4, NULL, NULL),
(317, 100349, 5, NULL, NULL),
(318, 100352, 1, NULL, NULL),
(319, 100352, 2, NULL, NULL),
(320, 100352, 4, NULL, NULL),
(321, 100353, 1, NULL, NULL),
(322, 100353, 2, NULL, NULL),
(323, 100353, 4, NULL, NULL),
(324, 100354, 1, NULL, NULL),
(325, 100354, 2, NULL, NULL),
(326, 100354, 4, NULL, NULL),
(327, 100355, 4, NULL, NULL),
(328, 100356, 4, NULL, NULL),
(329, 100357, 1, NULL, NULL),
(330, 100357, 2, NULL, NULL),
(331, 100357, 4, NULL, NULL),
(332, 100358, 4, NULL, NULL),
(333, 100359, 4, NULL, NULL),
(334, 100360, 4, NULL, NULL),
(335, 100362, 1, NULL, NULL),
(336, 100362, 2, NULL, NULL),
(337, 100362, 4, NULL, NULL),
(338, 100363, 1, NULL, NULL),
(339, 100363, 2, NULL, NULL),
(340, 100363, 4, NULL, NULL),
(341, 100364, 4, NULL, NULL),
(342, 100365, 4, NULL, NULL),
(343, 100366, 4, NULL, NULL),
(344, 100367, 4, NULL, NULL),
(345, 100368, 4, NULL, NULL),
(346, 100370, 1, NULL, NULL),
(347, 100370, 2, NULL, NULL),
(348, 100370, 4, NULL, NULL),
(349, 100371, 1, NULL, NULL),
(350, 100371, 2, NULL, NULL),
(351, 100371, 4, NULL, NULL),
(352, 100372, 4, NULL, NULL),
(353, 100373, 4, NULL, NULL),
(354, 100374, 4, NULL, NULL),
(355, 100375, 4, NULL, NULL),
(356, 100376, 4, NULL, NULL),
(357, 100379, 1, NULL, NULL),
(358, 100379, 2, NULL, NULL),
(359, 100379, 4, NULL, NULL),
(360, 100380, 4, NULL, NULL),
(361, 100381, 4, NULL, NULL),
(362, 100382, 4, NULL, NULL),
(363, 100383, 4, NULL, NULL),
(364, 100384, 4, NULL, NULL),
(365, 100386, 1, NULL, NULL),
(366, 100386, 2, NULL, NULL),
(367, 100386, 4, NULL, NULL),
(368, 100387, 4, NULL, NULL),
(369, 100388, 4, NULL, NULL),
(370, 100389, 4, NULL, NULL),
(371, 100390, 4, NULL, NULL),
(372, 100391, 4, NULL, NULL),
(373, 100395, 4, NULL, NULL),
(374, 100395, 5, NULL, NULL),
(375, 100396, 4, NULL, NULL),
(376, 100396, 5, NULL, NULL),
(377, 100399, 1, NULL, NULL),
(378, 100399, 2, NULL, NULL),
(379, 100399, 4, NULL, NULL),
(380, 100400, 1, NULL, NULL),
(381, 100400, 2, NULL, NULL),
(382, 100400, 4, NULL, NULL),
(383, 100401, 1, NULL, NULL),
(384, 100401, 2, NULL, NULL),
(385, 100401, 4, NULL, NULL),
(386, 100402, 4, NULL, NULL),
(387, 100403, 4, NULL, NULL),
(388, 100404, 1, NULL, NULL),
(389, 100404, 2, NULL, NULL),
(390, 100404, 4, NULL, NULL),
(391, 100405, 4, NULL, NULL),
(392, 100406, 4, NULL, NULL),
(393, 100407, 4, NULL, NULL),
(394, 100409, 1, NULL, NULL),
(395, 100409, 2, NULL, NULL),
(396, 100409, 4, NULL, NULL),
(397, 100410, 1, NULL, NULL),
(398, 100410, 2, NULL, NULL),
(399, 100410, 4, NULL, NULL),
(400, 100411, 4, NULL, NULL),
(401, 100412, 4, NULL, NULL),
(402, 100413, 4, NULL, NULL),
(403, 100414, 4, NULL, NULL),
(404, 100415, 4, NULL, NULL),
(405, 100417, 1, NULL, NULL),
(406, 100417, 2, NULL, NULL),
(407, 100417, 4, NULL, NULL),
(408, 100418, 1, NULL, NULL),
(409, 100418, 2, NULL, NULL),
(410, 100418, 4, NULL, NULL),
(411, 100419, 4, NULL, NULL),
(412, 100420, 4, NULL, NULL),
(413, 100421, 4, NULL, NULL),
(414, 100422, 4, NULL, NULL),
(415, 100423, 4, NULL, NULL),
(416, 100426, 1, NULL, NULL),
(417, 100426, 2, NULL, NULL),
(418, 100426, 4, NULL, NULL),
(419, 100427, 4, NULL, NULL),
(420, 100428, 4, NULL, NULL),
(421, 100429, 4, NULL, NULL),
(422, 100430, 4, NULL, NULL),
(423, 100431, 4, NULL, NULL),
(424, 100433, 1, NULL, NULL),
(425, 100433, 2, NULL, NULL),
(426, 100433, 4, NULL, NULL),
(427, 100434, 4, NULL, NULL),
(428, 100435, 4, NULL, NULL),
(429, 100436, 4, NULL, NULL),
(430, 100437, 4, NULL, NULL),
(431, 100438, 4, NULL, NULL),
(433, 100443, 4, NULL, NULL),
(434, 100443, 5, NULL, NULL),
(435, 100444, 4, NULL, NULL),
(436, 100444, 5, NULL, NULL),
(437, 100447, 1, NULL, NULL),
(438, 100447, 2, NULL, NULL),
(439, 100447, 4, NULL, NULL),
(440, 100448, 1, NULL, NULL),
(441, 100448, 2, NULL, NULL),
(442, 100448, 4, NULL, NULL),
(443, 100449, 1, NULL, NULL),
(444, 100449, 2, NULL, NULL),
(445, 100449, 4, NULL, NULL),
(446, 100450, 4, NULL, NULL),
(447, 100451, 4, NULL, NULL),
(448, 100452, 1, NULL, NULL),
(449, 100452, 2, NULL, NULL),
(450, 100452, 4, NULL, NULL),
(451, 100453, 4, NULL, NULL),
(452, 100454, 4, NULL, NULL),
(453, 100455, 4, NULL, NULL),
(454, 100457, 1, NULL, NULL),
(455, 100457, 2, NULL, NULL),
(456, 100457, 4, NULL, NULL),
(457, 100458, 1, NULL, NULL),
(458, 100458, 2, NULL, NULL),
(459, 100458, 4, NULL, NULL),
(460, 100459, 4, NULL, NULL),
(461, 100460, 4, NULL, NULL),
(462, 100461, 4, NULL, NULL),
(463, 100462, 4, NULL, NULL),
(464, 100463, 4, NULL, NULL),
(465, 100465, 1, NULL, NULL),
(466, 100465, 2, NULL, NULL),
(467, 100465, 4, NULL, NULL),
(468, 100466, 1, NULL, NULL),
(469, 100466, 2, NULL, NULL),
(470, 100466, 4, NULL, NULL),
(471, 100467, 4, NULL, NULL),
(472, 100468, 4, NULL, NULL),
(473, 100469, 4, NULL, NULL),
(474, 100470, 4, NULL, NULL),
(475, 100471, 4, NULL, NULL),
(476, 100474, 1, NULL, NULL),
(477, 100474, 2, NULL, NULL),
(478, 100474, 4, NULL, NULL),
(479, 100475, 4, NULL, NULL),
(480, 100476, 4, NULL, NULL),
(481, 100477, 4, NULL, NULL),
(482, 100478, 4, NULL, NULL),
(483, 100479, 4, NULL, NULL),
(485, 100482, 1, NULL, NULL),
(486, 100482, 2, NULL, NULL),
(487, 100482, 4, NULL, NULL),
(488, 100483, 4, NULL, NULL),
(489, 100484, 4, NULL, NULL),
(490, 100485, 4, NULL, NULL),
(491, 100486, 4, NULL, NULL),
(492, 100487, 4, NULL, NULL),
(498, 100491, 4, NULL, NULL),
(499, 100491, 5, NULL, NULL),
(500, 100492, 4, NULL, NULL),
(501, 100492, 5, NULL, NULL),
(502, 100493, 4, NULL, NULL),
(503, 100493, 5, NULL, NULL),
(504, 100494, 4, NULL, NULL),
(505, 100494, 5, NULL, NULL),
(506, 100499, 4, NULL, NULL),
(507, 100499, 5, NULL, NULL),
(508, 100500, 4, NULL, NULL),
(509, 100500, 5, NULL, NULL),
(510, 100503, 1, NULL, NULL),
(511, 100503, 2, NULL, NULL),
(512, 100504, 1, NULL, NULL),
(513, 100504, 2, NULL, NULL),
(514, 100505, 1, NULL, NULL),
(515, 100505, 2, NULL, NULL),
(516, 100506, 4, NULL, NULL),
(517, 100507, 4, NULL, NULL),
(518, 100508, 1, NULL, NULL),
(519, 100508, 2, NULL, NULL),
(520, 100509, 4, NULL, NULL),
(521, 100510, 4, NULL, NULL),
(522, 100511, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slot_generation_settings`
--

CREATE TABLE `slot_generation_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `interval_minutes` int(10) UNSIGNED NOT NULL DEFAULT '60',
  `slots_per_block` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `default_capacity` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `days_active` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_generation_settings`
--

INSERT INTO `slot_generation_settings` (`id`, `depot_id`, `start_time`, `end_time`, `interval_minutes`, `slots_per_block`, `default_capacity`, `days_active`, `created_at`, `updated_at`) VALUES
(1, 1, '06:00:00', '08:00:00', 18, 1, 5, '[]', '2025-06-29 20:23:23', '2025-07-01 22:55:23'),
(2, 2, '06:00:00', '08:00:00', 60, 2, 1, '[\"mon\"]', '2025-06-29 20:23:23', '2025-07-01 22:55:23'),
(3, 3, '06:00:00', '18:00:00', 60, 1, 1, '[]', '2025-06-29 20:23:23', '2025-06-29 20:23:23'),
(4, 4, '06:00:00', '18:00:00', 60, 1, 1, '[]', '2025-06-29 20:23:23', '2025-06-29 20:23:23'),
(5, 5, '06:00:00', '18:00:00', 180, 2, 1, '[]', '2025-06-29 20:23:23', '2025-07-01 22:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `slot_release_rules`
--

CREATE TABLE `slot_release_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `release_day` tinyint(4) NOT NULL COMMENT 'ISO day of week: 1=Mon … 7=Sun',
  `release_time` time NOT NULL,
  `lock_cutoff_days` int(11) NOT NULL DEFAULT '1',
  `lock_cutoff_time` time NOT NULL DEFAULT '16:00:00',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_release_rules`
--

INSERT INTO `slot_release_rules` (`id`, `depot_id`, `customer_id`, `release_day`, `release_time`, `lock_cutoff_days`, `lock_cutoff_time`, `priority`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 2, '08:00:00', 1, '16:00:00', 50, '2025-07-10 19:54:06', '2025-08-08 18:47:44'),
(4, 3, NULL, 2, '16:00:00', 15, '16:00:00', 50, '2025-07-10 22:36:58', '2025-07-11 11:34:44'),
(5, 5, NULL, 3, '16:00:00', 1, '16:00:00', 50, '2025-07-21 22:12:46', '2025-08-08 18:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `slot_release_rule_customer`
--

CREATE TABLE `slot_release_rule_customer` (
  `slot_release_rule_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_release_rule_customer`
--

INSERT INTO `slot_release_rule_customer` (`slot_release_rule_id`, `customer_id`) VALUES
(1, 1),
(1, 2),
(4, 4),
(5, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `slot_templates`
--

CREATE TABLE `slot_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_templates`
--

INSERT INTO `slot_templates` (`id`, `depot_id`, `day_of_week`, `start_time`, `end_time`, `duration_minutes`, `created_at`, `updated_at`) VALUES
(16, 1, '1', '06:00:00', '08:00:00', -120, '2025-06-30 19:27:11', '2025-06-30 19:27:11'),
(17, 1, '2', '08:00:00', '09:00:00', -60, '2025-07-02 19:38:47', '2025-07-06 23:28:16'),
(18, 2, '0', '01:00:00', '02:00:00', -60, '2025-07-02 19:57:46', '2025-07-02 19:57:46'),
(19, 2, '0', '02:00:00', '03:00:00', -60, '2025-07-02 19:57:58', '2025-07-02 19:57:58'),
(20, 3, '0', '01:00:00', '02:00:00', -60, '2025-07-02 19:58:10', '2025-07-02 19:58:10'),
(21, 3, '0', '02:00:00', '03:00:00', -60, '2025-07-02 19:58:22', '2025-07-02 19:58:22'),
(22, 4, '0', '01:00:00', '02:00:00', -60, '2025-07-02 19:58:33', '2025-07-02 19:58:33'),
(23, 4, '0', '03:00:00', '04:00:00', -60, '2025-07-02 19:58:43', '2025-07-02 19:58:43'),
(24, 5, '1', '06:30:00', '09:30:00', -180, '2025-07-02 19:58:56', '2025-07-08 10:50:42'),
(25, 5, '1', '07:00:00', '10:00:00', -180, '2025-07-02 19:59:15', '2025-07-08 10:52:40'),
(26, 1, '2', '06:00:00', '07:00:00', -60, '2025-07-06 23:27:42', '2025-07-06 23:27:42'),
(27, 1, '0', '07:00:00', '08:00:00', -60, '2025-07-06 23:28:00', '2025-07-06 23:28:00'),
(28, 1, '1', '15:00:00', '16:00:00', -60, '2025-07-06 23:28:47', '2025-07-06 23:28:47'),
(29, 1, '3', '09:00:00', '10:00:00', -60, '2025-07-07 18:59:14', '2025-07-07 18:59:14'),
(30, 1, '0', '10:00:00', '11:00:00', -60, '2025-07-07 18:59:43', '2025-07-07 18:59:43'),
(31, 1, '3', '11:00:00', '14:00:00', -180, '2025-07-07 19:00:08', '2025-07-07 19:00:08'),
(32, 1, '4', '14:00:00', '18:00:00', -240, '2025-07-07 19:00:46', '2025-07-07 19:00:46'),
(33, 1, '5', '08:00:00', '12:00:00', -240, '2025-07-07 19:01:06', '2025-07-07 19:01:06'),
(34, 5, '1', '09:30:00', '12:30:00', -180, '2025-07-08 10:51:49', '2025-07-08 10:51:49'),
(35, 5, '1', '10:00:00', '13:00:00', -180, '2025-07-08 10:52:22', '2025-07-08 10:52:22'),
(36, 5, '1', '12:30:00', '15:30:00', -180, '2025-07-08 10:54:00', '2025-07-08 10:54:00'),
(37, 5, '2', '06:30:00', '09:30:00', -180, '2025-07-08 10:56:48', '2025-07-08 10:56:48'),
(38, 5, '2', '07:00:00', '10:00:00', -180, '2025-07-08 10:57:06', '2025-07-08 10:59:18'),
(39, 5, '2', '10:00:00', '13:00:00', -180, '2025-07-08 10:57:34', '2025-07-08 10:57:34'),
(40, 5, '2', '09:30:00', '12:30:00', -180, '2025-07-08 10:58:18', '2025-07-08 10:58:18'),
(41, 5, '2', '12:30:00', '15:30:00', -180, '2025-07-08 10:58:52', '2025-07-08 10:58:52'),
(42, 5, '3', '06:30:00', '09:30:00', -180, '2025-07-08 10:59:41', '2025-07-08 10:59:41'),
(43, 5, '3', '07:00:00', '10:00:00', -180, '2025-07-08 10:59:59', '2025-07-08 10:59:59'),
(44, 5, '3', '09:30:00', '12:30:00', -180, '2025-07-08 11:00:48', '2025-07-08 11:00:48'),
(45, 5, '3', '10:00:00', '13:00:00', -180, '2025-07-08 11:02:47', '2025-07-08 11:02:47'),
(46, 5, '4', '06:30:00', '09:30:00', -180, '2025-07-08 11:12:04', '2025-07-08 11:13:41'),
(47, 5, '3', '12:30:00', '15:30:00', -180, '2025-07-08 11:12:34', '2025-07-08 11:12:34'),
(48, 5, '4', '07:00:00', '10:00:00', -180, '2025-07-08 11:14:08', '2025-07-08 11:14:08'),
(49, 5, '4', '09:30:00', '12:30:00', -180, '2025-07-08 11:14:25', '2025-07-08 11:14:25'),
(50, 5, '4', '10:00:00', '13:00:00', -180, '2025-07-08 11:15:41', '2025-07-08 11:15:41'),
(51, 5, '4', '12:30:00', '15:30:00', -180, '2025-07-08 11:16:14', '2025-07-08 11:16:14'),
(52, 5, '5', '06:30:00', '09:30:00', -180, '2025-07-08 11:16:43', '2025-07-08 11:16:43'),
(53, 5, '5', '07:00:00', '10:00:00', -180, '2025-07-08 11:16:59', '2025-07-08 11:18:37'),
(54, 5, '5', '10:00:00', '13:00:00', -180, '2025-07-08 11:17:35', '2025-07-08 11:17:35'),
(55, 5, '5', '09:30:00', '12:30:00', -180, '2025-07-08 11:17:55', '2025-07-08 11:17:55'),
(56, 5, '5', '12:30:00', '15:30:00', -180, '2025-07-08 11:18:17', '2025-07-08 11:18:17'),
(57, 2, '3', '06:00:00', '09:00:00', -180, '2025-07-23 11:45:20', '2025-07-23 11:45:20'),
(58, 4, '1', '06:30:00', '09:30:00', -180, '2025-07-25 10:55:15', '2025-07-25 10:55:15'),
(59, 4, '2', '06:00:00', '09:00:00', -180, '2025-07-25 10:55:31', '2025-07-25 10:55:31'),
(60, 4, '3', '06:00:00', '09:00:00', -180, '2025-07-25 10:56:00', '2025-07-25 10:56:00'),
(61, 4, '4', '06:00:00', '09:00:00', -180, '2025-07-25 10:56:20', '2025-07-25 10:56:20'),
(62, 4, '5', '06:00:00', '09:00:00', -180, '2025-07-25 10:56:42', '2025-07-25 10:56:42'),
(63, 3, '5', '08:00:00', '12:00:00', -240, '2025-08-07 10:55:30', '2025-08-07 10:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `tipping_bays`
--

CREATE TABLE `tipping_bays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_occupied` tinyint(1) NOT NULL DEFAULT '0',
  `equipment` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipping_bays`
--

INSERT INTO `tipping_bays` (`id`, `depot_id`, `name`, `code`, `description`, `is_active`, `is_occupied`, `equipment`, `created_at`, `updated_at`) VALUES
(1, 5, 'Bay 1', 'ATL-BAY-1', 'Atlante Bay 1', 1, 1, '[\"Forklift\"]', '2025-08-09 13:58:18', '2025-08-09 18:18:34'),
(2, 5, 'Bay 2', 'ATL-BAY-2', 'Atlante Bay 2', 1, 0, '[]', '2025-08-09 18:43:53', '2025-08-09 18:43:53'),
(3, 5, 'Bay 3', 'ATL-BAY-3', 'Atlante Bay 3', 1, 0, '[]', '2025-08-09 18:44:28', '2025-08-09 18:44:28'),
(4, 5, 'Bay 4', 'ATL-BAY-4', 'Atlante Bay 4', 1, 0, '[]', '2025-08-09 18:44:58', '2025-08-09 18:44:58'),
(5, 5, 'Bay 5', 'ATL-BAY-5', 'Atlante Bay 5', 1, 0, '[]', '2025-08-09 18:45:24', '2025-08-09 18:45:24'),
(6, 5, 'Bay 6', 'ATL-BAY-6', 'Atlante Bay 5', 1, 0, '[]', '2025-08-09 18:45:39', '2025-08-09 18:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `tipping_locations`
--

CREATE TABLE `tipping_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `capacity` int(11) NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `coordinates` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipping_locations`
--

INSERT INTO `tipping_locations` (`id`, `depot_id`, `name`, `code`, `description`, `capacity`, `is_active`, `coordinates`, `created_at`, `updated_at`) VALUES
(1, 5, 'Atlante', 'ATL', 'Atlante Drop Zone', 5, 1, NULL, '2025-08-09 13:56:29', '2025-08-09 13:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `depot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `depot_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `customer_id`, `deleted_at`) VALUES
(1, 1, 'Admin User', 'admin@example.com', NULL, '$2y$12$5d.f3is4wE1eqqtQ3Z.C4uSVj4ybr7Pozh2JYjW957Sl1nWzBHFhi', NULL, '2025-06-29 19:16:06', '2025-07-05 20:11:38', 1, NULL),
(2, 1, 'Depot Admin', 'depotadmin@example.com', NULL, '$2y$12$oDU7xgdh8uFDAkvdtZLF8e7v.ArSOO4EdGjdQQlx6e207FbZFrTyO', NULL, '2025-06-29 19:16:06', '2025-08-07 22:55:16', 1, NULL),
(3, 1, 'Site Admin', 'siteadmin@example.com', NULL, '$2y$12$ABK4c3Paxh64boEarjLv0erytIuVQjasAaLfPGxkYraRG4jClJcwi', NULL, '2025-06-29 19:16:07', '2025-08-07 22:56:47', 1, NULL),
(4, 1, 'Customer One', 'customer@example.com', NULL, '$2y$12$UPh8B50ShIl69KWFKsRBBeyVRFWQD1ZxH5mwFdXbJoGsbPTM4tJUO', NULL, '2025-06-29 19:16:07', '2025-07-09 09:12:25', 1, NULL),
(5, 1, 'Customer Two', 'customer2@example.com', NULL, '$2y$12$8QjaO.mCKVAi/XOdL8xlkekXLIg1FpA.1mCcqWYv7ay7/d75MucMW', NULL, '2025-06-29 19:16:07', '2025-08-01 20:07:08', 2, NULL),
(6, NULL, 'Paul Carr', 'paul.carr@knowleslogistics.com', NULL, '$2y$12$0F3HVpJkyeMXQURWAA2PXuI7bOMlpgdZHhCAW1zc3esvfbLxugjXq', 'cCJMj8TiOrIzEdoo9ot4CXsE2Kn4DzMQxNisZxdCMkoSERDZBS5A0hQ7VtO8', '2025-07-05 01:31:05', '2025-08-07 17:10:12', 1, NULL),
(7, 5, 'Atlante', 'atlante@example.com', NULL, '$2y$12$4SHNUMXJJzGr7Um6KH6QuOug0l8LmSyAW3NxYgd09RMFOoXgH/m7a', NULL, '2025-07-08 11:52:55', '2025-07-08 11:52:55', 4, NULL),
(8, NULL, 'Ray', 'ray@example.com', NULL, '$2y$12$oM/uTIAO.ug9MnObXu42iOjLLolYWv/EeD9GDUA07qyrrhw1VQTFi', NULL, '2025-07-09 13:59:23', '2025-07-09 13:59:23', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_slot_id_user_id_unique` (`slot_id`,`user_id`),
  ADD UNIQUE KEY `bookings_booking_reference_unique` (`booking_reference`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_booking_type_id_foreign` (`booking_type_id`),
  ADD KEY `bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `bookings_booking_reference_index` (`booking_reference`),
  ADD KEY `bookings_vehicle_registration_container_number_index` (`vehicle_registration`,`container_number`),
  ADD KEY `bookings_original_booking_id_foreign` (`original_booking_id`),
  ADD KEY `bookings_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `bookings_tipping_operator_id_foreign` (`tipping_operator_id`),
  ADD KEY `bookings_bay_assigned_by_foreign` (`bay_assigned_by`),
  ADD KEY `bookings_tipping_status_index` (`tipping_status`),
  ADD KEY `bookings_tipping_location_id_tipping_status_index` (`tipping_location_id`,`tipping_status`),
  ADD KEY `bookings_tipping_bay_id_tipping_status_index` (`tipping_bay_id`,`tipping_status`);

--
-- Indexes for table `booking_history`
--
ALTER TABLE `booking_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_history_user_id_foreign` (`user_id`),
  ADD KEY `booking_history_original_slot_id_foreign` (`original_slot_id`),
  ADD KEY `booking_history_new_slot_id_foreign` (`new_slot_id`),
  ADD KEY `booking_history_customer_id_action_created_at_index` (`customer_id`,`action`,`created_at`),
  ADD KEY `booking_history_booking_id_action_index` (`booking_id`,`action`),
  ADD KEY `booking_history_is_last_minute_action_index` (`is_last_minute`,`action`);

--
-- Indexes for table `booking_product`
--
ALTER TABLE `booking_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_product_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `booking_types`
--
ALTER TABLE `booking_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_type_depot`
--
ALTER TABLE `booking_type_depot`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_type_depot_depot_id_booking_type_id_unique` (`depot_id`,`booking_type_id`),
  ADD KEY `booking_type_depot_booking_type_id_foreign` (`booking_type_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_behavior_settings`
--
ALTER TABLE `customer_behavior_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_behavior_settings_customer_id_setting_key_unique` (`customer_id`,`setting_key`),
  ADD KEY `customer_behavior_settings_updated_by_foreign` (`updated_by`),
  ADD KEY `customer_behavior_settings_customer_id_index` (`customer_id`);

--
-- Indexes for table `customer_depot_product`
--
ALTER TABLE `customer_depot_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cdp_unique` (`customer_id`,`depot_id`,`product_id`),
  ADD KEY `customer_depot_product_depot_id_foreign` (`depot_id`),
  ADD KEY `customer_depot_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `customer_user`
--
ALTER TABLE `customer_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_user_customer_id_user_id_unique` (`customer_id`,`user_id`),
  ADD KEY `customer_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `depots`
--
ALTER TABLE `depots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `depot_case_ranges`
--
ALTER TABLE `depot_case_ranges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `depot_case_ranges_depot_id_foreign` (`depot_id`);

--
-- Indexes for table `depot_product`
--
ALTER TABLE `depot_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `depot_product_depot_id_product_id_unique` (`depot_id`,`product_id`),
  ADD KEY `depot_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `depot_user`
--
ALTER TABLE `depot_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `depot_user_user_id_foreign` (`user_id`),
  ADD KEY `depot_user_depot_id_foreign` (`depot_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD KEY `password_reset_tokens_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slots_booking_type_id_foreign` (`booking_type_id`),
  ADD KEY `slots_depot_id_start_at_index` (`depot_id`,`start_at`);

--
-- Indexes for table `slot_customer`
--
ALTER TABLE `slot_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_customer_slot_id_foreign` (`slot_id`),
  ADD KEY `slot_customer_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `slot_generation_settings`
--
ALTER TABLE `slot_generation_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_generation_settings_depot_id_foreign` (`depot_id`);

--
-- Indexes for table `slot_release_rules`
--
ALTER TABLE `slot_release_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slot_release_unique` (`depot_id`,`customer_id`,`release_day`,`release_time`),
  ADD KEY `slot_release_rules_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `slot_release_rule_customer`
--
ALTER TABLE `slot_release_rule_customer`
  ADD PRIMARY KEY (`slot_release_rule_id`,`customer_id`),
  ADD KEY `slot_release_rule_customer_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `slot_templates`
--
ALTER TABLE `slot_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_templates_depot_id_foreign` (`depot_id`);

--
-- Indexes for table `tipping_bays`
--
ALTER TABLE `tipping_bays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipping_bays_depot_id_code_unique` (`depot_id`,`code`),
  ADD KEY `tipping_bays_depot_id_is_active_is_occupied_index` (`depot_id`,`is_active`,`is_occupied`);

--
-- Indexes for table `tipping_locations`
--
ALTER TABLE `tipping_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipping_locations_depot_id_code_unique` (`depot_id`,`code`),
  ADD KEY `tipping_locations_depot_id_is_active_index` (`depot_id`,`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_depot_id_foreign` (`depot_id`),
  ADD KEY `users_customer_id_foreign` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `booking_history`
--
ALTER TABLE `booking_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `booking_product`
--
ALTER TABLE `booking_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_types`
--
ALTER TABLE `booking_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `booking_type_depot`
--
ALTER TABLE `booking_type_depot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_behavior_settings`
--
ALTER TABLE `customer_behavior_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_depot_product`
--
ALTER TABLE `customer_depot_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_user`
--
ALTER TABLE `customer_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `depots`
--
ALTER TABLE `depots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `depot_case_ranges`
--
ALTER TABLE `depot_case_ranges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `depot_product`
--
ALTER TABLE `depot_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `depot_user`
--
ALTER TABLE `depot_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100513;

--
-- AUTO_INCREMENT for table `slot_customer`
--
ALTER TABLE `slot_customer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=523;

--
-- AUTO_INCREMENT for table `slot_generation_settings`
--
ALTER TABLE `slot_generation_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `slot_release_rules`
--
ALTER TABLE `slot_release_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `slot_templates`
--
ALTER TABLE `slot_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tipping_bays`
--
ALTER TABLE `tipping_bays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tipping_locations`
--
ALTER TABLE `tipping_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_bay_assigned_by_foreign` FOREIGN KEY (`bay_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_original_booking_id_foreign` FOREIGN KEY (`original_booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `bookings_slot_id_foreign` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_tipping_bay_id_foreign` FOREIGN KEY (`tipping_bay_id`) REFERENCES `tipping_bays` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_tipping_location_id_foreign` FOREIGN KEY (`tipping_location_id`) REFERENCES `tipping_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_tipping_operator_id_foreign` FOREIGN KEY (`tipping_operator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_history`
--
ALTER TABLE `booking_history`
  ADD CONSTRAINT `booking_history_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_history_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `booking_history_new_slot_id_foreign` FOREIGN KEY (`new_slot_id`) REFERENCES `slots` (`id`),
  ADD CONSTRAINT `booking_history_original_slot_id_foreign` FOREIGN KEY (`original_slot_id`) REFERENCES `slots` (`id`),
  ADD CONSTRAINT `booking_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_product`
--
ALTER TABLE `booking_product`
  ADD CONSTRAINT `booking_product_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_type_depot`
--
ALTER TABLE `booking_type_depot`
  ADD CONSTRAINT `booking_type_depot_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_type_depot_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_behavior_settings`
--
ALTER TABLE `customer_behavior_settings`
  ADD CONSTRAINT `customer_behavior_settings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_behavior_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `customer_depot_product`
--
ALTER TABLE `customer_depot_product`
  ADD CONSTRAINT `customer_depot_product_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_depot_product_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_depot_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_user`
--
ALTER TABLE `customer_user`
  ADD CONSTRAINT `customer_user_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `depot_case_ranges`
--
ALTER TABLE `depot_case_ranges`
  ADD CONSTRAINT `depot_case_ranges_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `depot_product`
--
ALTER TABLE `depot_product`
  ADD CONSTRAINT `depot_product_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `depot_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `depot_user`
--
ALTER TABLE `depot_user`
  ADD CONSTRAINT `depot_user_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `depot_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slots`
--
ALTER TABLE `slots`
  ADD CONSTRAINT `slots_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `slots_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slot_customer`
--
ALTER TABLE `slot_customer`
  ADD CONSTRAINT `slot_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `slot_customer_slot_id_foreign` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slot_generation_settings`
--
ALTER TABLE `slot_generation_settings`
  ADD CONSTRAINT `slot_generation_settings_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slot_release_rules`
--
ALTER TABLE `slot_release_rules`
  ADD CONSTRAINT `slot_release_rules_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `slot_release_rules_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slot_release_rule_customer`
--
ALTER TABLE `slot_release_rule_customer`
  ADD CONSTRAINT `slot_release_rule_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `slot_release_rule_customer_slot_release_rule_id_foreign` FOREIGN KEY (`slot_release_rule_id`) REFERENCES `slot_release_rules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slot_templates`
--
ALTER TABLE `slot_templates`
  ADD CONSTRAINT `slot_templates_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tipping_bays`
--
ALTER TABLE `tipping_bays`
  ADD CONSTRAINT `tipping_bays_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tipping_locations`
--
ALTER TABLE `tipping_locations`
  ADD CONSTRAINT `tipping_locations_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
