-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: ktl_booking
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `arrival_time_settings`
--

DROP TABLE IF EXISTS `arrival_time_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arrival_time_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `depot_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `early_threshold_minutes` int NOT NULL DEFAULT '0',
  `late_threshold_minutes` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_active_setting` (`level`,`depot_id`,`customer_id`),
  KEY `arrival_time_settings_level_is_active_index` (`level`,`is_active`),
  KEY `arrival_time_settings_depot_id_is_active_index` (`depot_id`,`is_active`),
  KEY `arrival_time_settings_customer_id_is_active_index` (`customer_id`,`is_active`),
  CONSTRAINT `arrival_time_settings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `arrival_time_settings_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arrival_time_settings`
--

LOCK TABLES `arrival_time_settings` WRITE;
/*!40000 ALTER TABLE `arrival_time_settings` DISABLE KEYS */;
INSERT INTO `arrival_time_settings` VALUES (1,'global',NULL,NULL,0,0,'Default global arrival time rules: exact time only (no tolerance)',1,'2025-08-31 16:47:53','2025-08-31 16:47:53');
/*!40000 ALTER TABLE `arrival_time_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_history`
--

DROP TABLE IF EXISTS `booking_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `original_slot_id` bigint unsigned DEFAULT NULL,
  `original_start_time` datetime DEFAULT NULL,
  `original_end_time` datetime DEFAULT NULL,
  `new_slot_id` bigint unsigned DEFAULT NULL,
  `new_start_time` datetime DEFAULT NULL,
  `new_end_time` datetime DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `hours_before_slot` int DEFAULT NULL,
  `is_last_minute` tinyint(1) NOT NULL DEFAULT '0',
  `customer_rebook_count_30days` int NOT NULL DEFAULT '0',
  `customer_cancel_count_30days` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_history_user_id_foreign` (`user_id`),
  KEY `booking_history_original_slot_id_foreign` (`original_slot_id`),
  KEY `booking_history_new_slot_id_foreign` (`new_slot_id`),
  KEY `booking_history_customer_id_action_created_at_index` (`customer_id`,`action`,`created_at`),
  KEY `booking_history_booking_id_action_index` (`booking_id`,`action`),
  KEY `booking_history_is_last_minute_action_index` (`is_last_minute`,`action`),
  CONSTRAINT `booking_history_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_history_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `booking_history_new_slot_id_foreign` FOREIGN KEY (`new_slot_id`) REFERENCES `slots` (`id`),
  CONSTRAINT `booking_history_original_slot_id_foreign` FOREIGN KEY (`original_slot_id`) REFERENCES `slots` (`id`),
  CONSTRAINT `booking_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history`
--

LOCK TABLES `booking_history` WRITE;
/*!40000 ALTER TABLE `booking_history` DISABLE KEYS */;
INSERT INTO `booking_history` VALUES (1,1,1,1,NULL,NULL,NULL,1,'2025-09-01 06:00:00','2025-09-01 07:00:00','created',NULL,NULL,11,1,0,0,'2025-08-31 18:30:21','2025-08-31 18:30:21'),(2,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'modified','Moved to tipping bay: Bay 1','{\"bay_id\": 1, \"bay_name\": \"Bay 1\", \"action_type\": \"moved_to_bay\"}',11,1,0,0,'2025-08-31 18:31:47','2025-08-31 18:31:47'),(3,1,1,2,NULL,NULL,NULL,NULL,NULL,NULL,'modified','Trailer dropped at location: Parking Area','{\"action_type\": \"trailer_dropped\", \"location_id\": 3, \"location_name\": \"Parking Area\"}',8,1,0,0,'2025-08-31 22:21:25','2025-08-31 22:21:25'),(4,2,2,1,NULL,NULL,NULL,19,'2025-10-07 08:00:00','2025-10-07 09:00:00','created',NULL,NULL,19,1,0,0,'2025-10-06 12:52:19','2025-10-06 12:52:19'),(5,5,2,1,NULL,NULL,NULL,19,'2025-10-07 08:00:00','2025-10-07 09:00:00','created',NULL,NULL,19,1,0,0,'2025-10-06 13:21:31','2025-10-06 13:21:31'),(6,6,2,1,NULL,NULL,NULL,27,'2025-10-07 09:00:00','2025-10-07 10:00:00','created',NULL,NULL,20,1,0,0,'2025-10-06 13:26:05','2025-10-06 13:26:05'),(7,7,2,1,NULL,NULL,NULL,35,'2025-10-07 10:00:00','2025-10-07 11:00:00','created',NULL,NULL,20,1,0,0,'2025-10-06 13:30:54','2025-10-06 13:30:54'),(8,8,2,1,NULL,NULL,NULL,19,'2025-10-07 08:00:00','2025-10-07 09:00:00','created',NULL,NULL,18,1,0,0,'2025-10-06 13:34:01','2025-10-06 13:34:01'),(9,9,2,1,NULL,NULL,NULL,19,'2025-10-07 08:00:00','2025-10-07 09:00:00','created',NULL,NULL,18,1,0,0,'2025-10-06 13:37:10','2025-10-06 13:37:10'),(10,10,2,1,NULL,NULL,NULL,27,'2025-10-07 09:00:00','2025-10-07 10:00:00','created',NULL,NULL,19,1,0,0,'2025-10-06 13:47:54','2025-10-06 13:47:54'),(11,11,1,1,NULL,NULL,NULL,53,'2025-10-08 12:00:00','2025-10-08 13:00:00','created',NULL,NULL,45,0,0,0,'2025-10-06 15:13:52','2025-10-06 15:13:52');
/*!40000 ALTER TABLE `booking_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_po_numbers`
--

DROP TABLE IF EXISTS `booking_po_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_po_numbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned DEFAULT NULL,
  `po_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `factory_booking_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_po_numbers_booking_id_po_number_unique` (`booking_id`,`po_number`),
  KEY `booking_po_numbers_factory_booking_id_foreign` (`factory_booking_id`),
  CONSTRAINT `booking_po_numbers_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_po_numbers_factory_booking_id_foreign` FOREIGN KEY (`factory_booking_id`) REFERENCES `factory_bookings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_po_numbers`
--

LOCK TABLES `booking_po_numbers` WRITE;
/*!40000 ALTER TABLE `booking_po_numbers` DISABLE KEYS */;
INSERT INTO `booking_po_numbers` VALUES (1,1,'REF2','2025-08-31 18:30:21','2025-08-31 18:30:21',NULL),(2,NULL,'REF2','2025-08-31 18:31:19','2025-08-31 18:31:19',1),(3,2,'PO353787-1','2025-10-06 12:52:19','2025-10-06 12:52:19',NULL),(4,5,'PO606558-1','2025-10-06 13:21:31','2025-10-06 13:21:31',NULL),(5,6,'PO615437-1','2025-10-06 13:26:08','2025-10-06 13:26:08',NULL),(9,9,'PO616221-1','2025-10-06 13:42:26','2025-10-06 13:42:26',NULL),(10,8,'PO616221-1','2025-10-06 13:43:39','2025-10-06 13:43:39',NULL),(11,10,'PO617706-1','2025-10-06 13:47:58','2025-10-06 13:47:58',NULL),(14,7,'PO617170-1','2025-10-06 15:10:37','2025-10-06 15:10:37',NULL),(15,11,'34','2025-10-06 15:13:54','2025-10-06 15:13:54',NULL);
/*!40000 ALTER TABLE `booking_po_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_product`
--

DROP TABLE IF EXISTS `booking_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `po_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cases` int DEFAULT NULL,
  `pallets` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_product_booking_id_foreign` (`booking_id`),
  KEY `booking_product_product_id_foreign` (`product_id`),
  CONSTRAINT `booking_product_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_product`
--

LOCK TABLES `booking_product` WRITE;
/*!40000 ALTER TABLE `booking_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_type_depot`
--

DROP TABLE IF EXISTS `booking_type_depot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_type_depot` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `booking_type_id` bigint unsigned NOT NULL,
  `duration_minutes` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_type_depot_depot_id_booking_type_id_unique` (`depot_id`,`booking_type_id`),
  KEY `booking_type_depot_booking_type_id_foreign` (`booking_type_id`),
  CONSTRAINT `booking_type_depot_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_type_depot_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_type_depot`
--

LOCK TABLES `booking_type_depot` WRITE;
/*!40000 ALTER TABLE `booking_type_depot` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_type_depot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_types`
--

DROP TABLE IF EXISTS `booking_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slots_required` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_types`
--

LOCK TABLES `booking_types` WRITE;
/*!40000 ALTER TABLE `booking_types` DISABLE KEYS */;
INSERT INTO `booking_types` VALUES (1,'Palletised',NULL,1,'2025-08-31 17:00:26','2025-08-31 17:00:26',NULL,NULL),(2,'Handball',NULL,2,'2025-08-31 17:00:26','2025-08-31 17:00:26',NULL,NULL),(3,'Ton Bags',NULL,1,'2025-08-31 17:00:26','2025-08-31 17:00:26',NULL,NULL);
/*!40000 ALTER TABLE `booking_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_reference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seal_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gate_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_arrival` timestamp NULL DEFAULT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `slot_id` bigint unsigned NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `booking_type_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `arrived_at` timestamp NULL DEFAULT NULL,
  `departed_at` timestamp NULL DEFAULT NULL,
  `container_size` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_booking_id` bigint unsigned DEFAULT NULL,
  `rebook_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_by` bigint unsigned DEFAULT NULL,
  `is_rebooked` tinyint(1) NOT NULL DEFAULT '0',
  `rebook_count` int NOT NULL DEFAULT '0',
  `vehicle_details` json DEFAULT NULL,
  `trailer_type_id` bigint unsigned DEFAULT NULL,
  `tipping_location_id` bigint unsigned DEFAULT NULL,
  `tipping_bay_id` bigint unsigned DEFAULT NULL,
  `tipping_status` enum('not_started','trailer_dropped','moved_to_bay','tipping_in_progress','tipping_completed','trailer_departed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `trailer_dropped_at` timestamp NULL DEFAULT NULL,
  `moved_to_bay_at` timestamp NULL DEFAULT NULL,
  `tipping_started_at` timestamp NULL DEFAULT NULL,
  `tipping_completed_at` timestamp NULL DEFAULT NULL,
  `collection_scheduled_at` datetime DEFAULT NULL COMMENT 'When trailer collection is scheduled',
  `manual_priority_boost` int NOT NULL DEFAULT '0' COMMENT 'Manual priority boost points (can be negative)',
  `priority_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about manual priority adjustments',
  `tipping_type` enum('live_tip','drop') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Simple tipping type: live_tip (unit stays) or drop (unit leaves)',
  `swap_trailer_id` bigint unsigned DEFAULT NULL,
  `trailer_departed_at` timestamp NULL DEFAULT NULL,
  `tipping_notes` text COLLATE utf8mb4_unicode_ci,
  `actual_tipping_duration` int DEFAULT NULL,
  `tipping_issues` json DEFAULT NULL,
  `tipping_operator_id` bigint unsigned DEFAULT NULL,
  `bay_assigned_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_booking_reference_unique` (`booking_reference`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_carrier_id_index` (`carrier_id`),
  KEY `bookings_booking_type_id_foreign` (`booking_type_id`),
  KEY `bookings_customer_id_foreign` (`customer_id`),
  KEY `bookings_booking_reference_index` (`booking_reference`),
  KEY `bookings_original_booking_id_foreign` (`original_booking_id`),
  KEY `bookings_cancelled_by_foreign` (`cancelled_by`),
  KEY `bookings_trailer_type_id_foreign` (`trailer_type_id`),
  KEY `bookings_tipping_operator_id_foreign` (`tipping_operator_id`),
  KEY `bookings_bay_assigned_by_foreign` (`bay_assigned_by`),
  KEY `bookings_tipping_status_index` (`tipping_status`),
  KEY `bookings_tipping_location_id_tipping_status_index` (`tipping_location_id`,`tipping_status`),
  KEY `bookings_tipping_bay_id_tipping_status_index` (`tipping_bay_id`,`tipping_status`),
  KEY `bookings_swap_trailer_id_foreign` (`swap_trailer_id`),
  KEY `bookings_slot_id_foreign` (`slot_id`),
  CONSTRAINT `bookings_bay_assigned_by_foreign` FOREIGN KEY (`bay_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`),
  CONSTRAINT `bookings_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_original_booking_id_foreign` FOREIGN KEY (`original_booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `bookings_slot_id_foreign` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_swap_trailer_id_foreign` FOREIGN KEY (`swap_trailer_id`) REFERENCES `trailers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_tipping_bay_id_foreign` FOREIGN KEY (`tipping_bay_id`) REFERENCES `tipping_bays` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_tipping_location_id_foreign` FOREIGN KEY (`tipping_location_id`) REFERENCES `tipping_locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_tipping_operator_id_foreign` FOREIGN KEY (`tipping_operator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_trailer_type_id_foreign` FOREIGN KEY (`trailer_type_id`) REFERENCES `trailer_types` (`id`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'WM-20250831-B120',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,2,1,NULL,NULL,NULL,NULL,'2025-08-31 18:30:21','2025-08-31 18:30:21',8,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"2\", \"container_number\": \"TEMU1234567\", \"vehicle_registration\": \"AV71 EVT\"}',NULL,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'WM-20251006-F8E5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,1,1,NULL,NULL,NULL,NULL,'2025-10-06 12:52:14','2025-10-06 12:52:14',9,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'WM-20251006-CDBF',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,1,1,'Store 7',NULL,NULL,NULL,'2025-10-06 13:21:29','2025-10-06 13:21:29',10,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"1\", \"container_number\": \"ONEU2430856\", \"special_instructions\": \"CASHEWS (HIGH RISK)\"}',1,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'WM-20251006-B9DF',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,27,NULL,1,1,'Store 7',NULL,NULL,NULL,'2025-10-06 13:26:05','2025-10-06 13:26:05',11,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"1\", \"container_number\": \"FTAU1335210\", \"special_instructions\": \"CASHEWS (HIGH RISK)\"}',1,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'WM-20251006-66CE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,35,NULL,2,1,NULL,NULL,NULL,NULL,'2025-10-06 13:30:51','2025-10-06 15:10:31',11,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"1\", \"container_number\": \"MRKU7007952\", \"special_instructions\": \"CASHEWS (HIGH RISK)\", \"vehicle_registration\": \"PO617170-1\"}',1,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'WM-20251006-9959',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,2,1,NULL,NULL,NULL,NULL,'2025-10-06 13:33:59','2025-10-06 13:43:36',10,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"6\"}',6,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'WM-20251006-718C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,2,1,NULL,NULL,NULL,NULL,'2025-10-06 13:37:04','2025-10-06 13:42:22',10,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"special_instructions\": \"FRUITSTRINGS\"}',NULL,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'WM-20251006-1245',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,27,NULL,1,1,NULL,NULL,NULL,NULL,'2025-10-06 13:47:53','2025-10-06 13:47:53',12,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'{\"trailer_type_id\": \"6\", \"special_instructions\": \"SQF\"}',6,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'WM-20251006-B4E7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,53,NULL,2,1,NULL,NULL,NULL,NULL,'2025-10-06 15:13:50','2025-10-06 15:13:50',8,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'not_started',NULL,NULL,NULL,NULL,NULL,0,NULL,'live_tip',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier_merges`
--

DROP TABLE IF EXISTS `carrier_merges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrier_merges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_carrier_id` bigint unsigned NOT NULL,
  `source_carrier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_carrier_id` bigint unsigned NOT NULL,
  `target_carrier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookings_moved` int NOT NULL,
  `depot_relationships_merged` json NOT NULL,
  `merged_by` bigint unsigned NOT NULL,
  `source_deleted` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_merges_source_carrier_id_foreign` (`source_carrier_id`),
  KEY `carrier_merges_target_carrier_id_foreign` (`target_carrier_id`),
  KEY `carrier_merges_merged_by_foreign` (`merged_by`),
  CONSTRAINT `carrier_merges_merged_by_foreign` FOREIGN KEY (`merged_by`) REFERENCES `users` (`id`),
  CONSTRAINT `carrier_merges_source_carrier_id_foreign` FOREIGN KEY (`source_carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `carrier_merges_target_carrier_id_foreign` FOREIGN KEY (`target_carrier_id`) REFERENCES `carriers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier_merges`
--

LOCK TABLES `carrier_merges` WRITE;
/*!40000 ALTER TABLE `carrier_merges` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrier_merges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carriers`
--

DROP TABLE IF EXISTS `carriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carriers_name_is_active_index` (`name`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carriers`
--

LOCK TABLES `carriers` WRITE;
/*!40000 ALTER TABLE `carriers` DISABLE KEYS */;
INSERT INTO `carriers` VALUES (1,'DHL',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(2,'FedEx',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(3,'UPS',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(4,'Royal Mail',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(5,'TNT',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(6,'Hermes',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(7,'Yodel',NULL,NULL,1,0,NULL,'2025-08-31 16:45:11','2025-08-31 16:45:11',NULL),(8,'Knowles Logistics',NULL,NULL,1,0,NULL,'2025-08-31 18:29:54','2025-08-31 18:29:54',NULL),(9,'Brentag',NULL,NULL,1,0,NULL,'2025-10-06 12:52:13','2025-10-06 12:52:13',NULL),(10,'Cargo Care',NULL,NULL,1,0,NULL,'2025-10-06 12:55:51','2025-10-06 12:55:51',NULL),(11,'Boast',NULL,NULL,1,0,NULL,'2025-10-06 13:26:03','2025-10-06 13:26:03',NULL),(12,'Sqf',NULL,NULL,1,0,NULL,'2025-10-06 13:47:53','2025-10-06 13:47:53',NULL);
/*!40000 ALTER TABLE `carriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignment_loads`
--

DROP TABLE IF EXISTS `consignment_loads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consignment_loads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `expected_cases` int NOT NULL DEFAULT '0',
  `expected_pallets` int NOT NULL DEFAULT '0',
  `expected_pallet_type_id` bigint unsigned DEFAULT NULL,
  `actual_cases` int DEFAULT NULL,
  `actual_pallets` int DEFAULT NULL,
  `actual_pallet_type_id` bigint unsigned DEFAULT NULL,
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `customer_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_notes` text COLLATE utf8mb4_unicode_ci,
  `load_status` enum('pending','loaded','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `loaded_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_loads_customer_id_foreign` (`customer_id`),
  KEY `consignment_loads_expected_pallet_type_id_foreign` (`expected_pallet_type_id`),
  KEY `consignment_loads_actual_pallet_type_id_foreign` (`actual_pallet_type_id`),
  KEY `consignment_loads_consignment_id_customer_id_index` (`consignment_id`,`customer_id`),
  KEY `consignment_loads_depot_id_load_status_index` (`depot_id`,`load_status`),
  KEY `consignment_loads_customer_reference_index` (`customer_reference`),
  CONSTRAINT `consignment_loads_actual_pallet_type_id_foreign` FOREIGN KEY (`actual_pallet_type_id`) REFERENCES `pallet_types` (`id`),
  CONSTRAINT `consignment_loads_consignment_id_foreign` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_loads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `consignment_loads_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `consignment_loads_expected_pallet_type_id_foreign` FOREIGN KEY (`expected_pallet_type_id`) REFERENCES `pallet_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_loads`
--

LOCK TABLES `consignment_loads` WRITE;
/*!40000 ALTER TABLE `consignment_loads` DISABLE KEYS */;
/*!40000 ALTER TABLE `consignment_loads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignment_references`
--

DROP TABLE IF EXISTS `consignment_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consignment_references` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint unsigned NOT NULL,
  `reference_type` enum('customer_ref','delivery_note','invoice','po_number','collection_note','manifest','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_references_consignment_id_reference_type_index` (`consignment_id`,`reference_type`),
  KEY `consignment_references_reference_value_index` (`reference_value`),
  CONSTRAINT `consignment_references_consignment_id_foreign` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_references`
--

LOCK TABLES `consignment_references` WRITE;
/*!40000 ALTER TABLE `consignment_references` DISABLE KEYS */;
/*!40000 ALTER TABLE `consignment_references` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignments`
--

DROP TABLE IF EXISTS `consignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `consignment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin_depot_id` bigint unsigned NOT NULL,
  `depot_route` json DEFAULT NULL,
  `collection_time` timestamp NULL DEFAULT NULL,
  `delivery_time` timestamp NULL DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `total_pallets` int NOT NULL DEFAULT '0',
  `total_cases` int NOT NULL DEFAULT '0',
  `total_weight_kg` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','loading','loaded','in_transit','delivered','cancelled','partial_delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `additional_data` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consignments_consignment_number_unique` (`consignment_number`),
  KEY `consignments_origin_depot_id_foreign` (`origin_depot_id`),
  KEY `consignments_consignment_number_index` (`consignment_number`),
  KEY `consignments_status_index` (`status`),
  KEY `consignments_collection_time_index` (`collection_time`),
  CONSTRAINT `consignments_origin_depot_id_foreign` FOREIGN KEY (`origin_depot_id`) REFERENCES `depots` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignments`
--

LOCK TABLES `consignments` WRITE;
/*!40000 ALTER TABLE `consignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `consignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_roles`
--

DROP TABLE IF EXISTS `custom_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `function_keys` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_roles`
--

LOCK TABLES `custom_roles` WRITE;
/*!40000 ALTER TABLE `custom_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `address_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_2` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `county` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GB',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `geocoded_at` timestamp NULL DEFAULT NULL,
  `delivery_instructions` text COLLATE utf8mb4_unicode_ci,
  `access_notes` text COLLATE utf8mb4_unicode_ci,
  `delivery_hours` json DEFAULT NULL,
  `requires_appointment` tinyint(1) NOT NULL DEFAULT '0',
  `requires_signature` tinyint(1) NOT NULL DEFAULT '1',
  `requires_photo_proof` tinyint(1) NOT NULL DEFAULT '0',
  `special_equipment` json DEFAULT NULL,
  `latest_delivery_time` time DEFAULT NULL,
  `delivery_buffer_minutes` int NOT NULL DEFAULT '15',
  `unloading_duration_minutes` int NOT NULL DEFAULT '30',
  `site_closure_time` time DEFAULT NULL,
  `lunch_break_start` time DEFAULT NULL,
  `lunch_break_end` time DEFAULT NULL,
  `no_delivery_periods` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_customer_id_is_active_index` (`customer_id`,`is_active`),
  KEY `customer_addresses_postcode_index` (`postcode`),
  KEY `customer_addresses_latitude_longitude_index` (`latitude`,`longitude`),
  KEY `customer_addresses_customer_id_is_default_index` (`customer_id`,`is_default`),
  CONSTRAINT `customer_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_addresses`
--

LOCK TABLES `customer_addresses` WRITE;
/*!40000 ALTER TABLE `customer_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_behavior_settings`
--

DROP TABLE IF EXISTS `customer_behavior_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_behavior_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'integer',
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_behavior_settings_customer_id_setting_key_unique` (`customer_id`,`setting_key`),
  KEY `customer_behavior_settings_updated_by_foreign` (`updated_by`),
  KEY `customer_behavior_settings_customer_id_index` (`customer_id`),
  CONSTRAINT `customer_behavior_settings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_behavior_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_behavior_settings`
--

LOCK TABLES `customer_behavior_settings` WRITE;
/*!40000 ALTER TABLE `customer_behavior_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_behavior_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_depot_product`
--

DROP TABLE IF EXISTS `customer_depot_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_depot_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `min_cases` int unsigned DEFAULT NULL,
  `max_cases` int unsigned DEFAULT NULL,
  `override_duration_minutes` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cdp_unique` (`customer_id`,`depot_id`,`product_id`),
  KEY `customer_depot_product_depot_id_foreign` (`depot_id`),
  KEY `customer_depot_product_product_id_foreign` (`product_id`),
  CONSTRAINT `customer_depot_product_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_depot_product_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_depot_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_depot_product`
--

LOCK TABLES `customer_depot_product` WRITE;
/*!40000 ALTER TABLE `customer_depot_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_depot_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_user`
--

DROP TABLE IF EXISTS `customer_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_user_customer_id_user_id_unique` (`customer_id`,`user_id`),
  KEY `customer_user_user_id_foreign` (`user_id`),
  CONSTRAINT `customer_user_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_user`
--

LOCK TABLES `customer_user` WRITE;
/*!40000 ALTER TABLE `customer_user` DISABLE KEYS */;
INSERT INTO `customer_user` VALUES (1,1,5,NULL,NULL),(2,1,6,NULL,NULL),(5,1,7,NULL,NULL);
/*!40000 ALTER TABLE `customer_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority_level` int NOT NULL DEFAULT '0' COMMENT 'Priority level for tipping queue (0=normal, 1-10=high priority)',
  `priority_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about why customer has priority',
  `cut_off_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Test Customer',0,NULL,NULL,'2025-08-31 18:24:02','2025-08-31 18:24:02',NULL),(2,'Zertus',0,NULL,NULL,'2025-10-06 12:48:25','2025-10-06 12:48:25',NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depot_carrier`
--

DROP TABLE IF EXISTS `depot_carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depot_carrier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `carrier_id` bigint unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `auto_disable_unused` tinyint(1) NOT NULL DEFAULT '1',
  `auto_disable_months` int NOT NULL DEFAULT '6',
  `allowed_customer_ids` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depot_carrier_depot_id_carrier_id_unique` (`depot_id`,`carrier_id`),
  KEY `depot_carrier_carrier_id_foreign` (`carrier_id`),
  CONSTRAINT `depot_carrier_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `depot_carrier_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depot_carrier`
--

LOCK TABLES `depot_carrier` WRITE;
/*!40000 ALTER TABLE `depot_carrier` DISABLE KEYS */;
/*!40000 ALTER TABLE `depot_carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depot_case_ranges`
--

DROP TABLE IF EXISTS `depot_case_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depot_case_ranges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `min_cases` int unsigned DEFAULT NULL,
  `max_cases` int unsigned DEFAULT NULL,
  `duration_minutes` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depot_case_ranges_depot_id_foreign` (`depot_id`),
  CONSTRAINT `depot_case_ranges_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depot_case_ranges`
--

LOCK TABLES `depot_case_ranges` WRITE;
/*!40000 ALTER TABLE `depot_case_ranges` DISABLE KEYS */;
/*!40000 ALTER TABLE `depot_case_ranges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depot_product`
--

DROP TABLE IF EXISTS `depot_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depot_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `expected_case_count` int unsigned DEFAULT NULL,
  `min_cases` int DEFAULT NULL,
  `max_cases` int DEFAULT NULL,
  `override_duration_minutes` int unsigned DEFAULT NULL,
  `duration_override_minutes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depot_product_depot_id_product_id_unique` (`depot_id`,`product_id`),
  KEY `depot_product_product_id_foreign` (`product_id`),
  CONSTRAINT `depot_product_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `depot_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depot_product`
--

LOCK TABLES `depot_product` WRITE;
/*!40000 ALTER TABLE `depot_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `depot_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depot_user`
--

DROP TABLE IF EXISTS `depot_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depot_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depot_user_user_id_foreign` (`user_id`),
  KEY `depot_user_depot_id_foreign` (`depot_id`),
  CONSTRAINT `depot_user_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `depot_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depot_user`
--

LOCK TABLES `depot_user` WRITE;
/*!40000 ALTER TABLE `depot_user` DISABLE KEYS */;
INSERT INTO `depot_user` VALUES (3,5,1,NULL,NULL),(4,6,1,NULL,NULL),(7,7,1,NULL,NULL),(9,28,1,NULL,NULL),(10,1,3,NULL,NULL);
/*!40000 ALTER TABLE `depot_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depots`
--

DROP TABLE IF EXISTS `depots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cut_off_time` time NOT NULL DEFAULT '16:00:00',
  `map_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Filename of the depot map (stored in public/images/depot-maps/)',
  `map_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about the depot map layout',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depots`
--

LOCK TABLES `depots` WRITE;
/*!40000 ALTER TABLE `depots` DISABLE KEYS */;
INSERT INTO `depots` VALUES (1,'Main Depot','Default Location','2025-08-31 17:00:23','2025-08-31 17:00:23',NULL,'16:00:00','Wimblington.svg',NULL),(2,'Test Depot','March','2025-09-04 08:33:39','2025-10-05 21:55:08',NULL,'16:00:00',NULL,NULL),(3,'Wimblington','March','2025-10-05 21:55:51','2025-10-05 21:55:51',NULL,'16:00:00',NULL,NULL);
/*!40000 ALTER TABLE `depots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factory_bookings`
--

DROP TABLE IF EXISTS `factory_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factory_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  `trailer_type_id` bigint unsigned DEFAULT NULL,
  `tipping_type` enum('live_tip','drop') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrived_at` timestamp NOT NULL,
  `vehicle_registration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trailer_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci,
  `vehicle_details` json DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '50',
  `status` enum('arrived','processing','completed','departed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'arrived',
  `processing_started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `departed_at` timestamp NULL DEFAULT NULL,
  `registered_by` bigint unsigned NOT NULL,
  `gate_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `factory_bookings_reference_unique` (`reference`),
  KEY `factory_bookings_customer_id_foreign` (`customer_id`),
  KEY `factory_bookings_carrier_id_foreign` (`carrier_id`),
  KEY `factory_bookings_trailer_type_id_foreign` (`trailer_type_id`),
  KEY `factory_bookings_registered_by_foreign` (`registered_by`),
  KEY `factory_bookings_depot_id_status_index` (`depot_id`,`status`),
  KEY `factory_bookings_arrived_at_index` (`arrived_at`),
  KEY `factory_bookings_priority_arrived_at_index` (`priority`,`arrived_at`),
  CONSTRAINT `factory_bookings_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  CONSTRAINT `factory_bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `factory_bookings_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `factory_bookings_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`),
  CONSTRAINT `factory_bookings_trailer_type_id_foreign` FOREIGN KEY (`trailer_type_id`) REFERENCES `trailer_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factory_bookings`
--

LOCK TABLES `factory_bookings` WRITE;
/*!40000 ALTER TABLE `factory_bookings` DISABLE KEYS */;
INSERT INTO `factory_bookings` VALUES (1,'FAC-2025-001',1,1,8,2,'live_tip','2025-08-31 18:31:19','FY21 MNO','KT412',NULL,NULL,NULL,NULL,50,'arrived',NULL,NULL,NULL,1,NULL,'2025-08-31 18:31:19','2025-08-31 18:31:19',NULL);
/*!40000 ALTER TABLE `factory_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_file_uploads`
--

DROP TABLE IF EXISTS `import_file_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_file_uploads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_template_id` bigint unsigned NOT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `uploaded_at` timestamp NOT NULL,
  `status` enum('uploaded','processing','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded',
  `total_rows` int DEFAULT NULL,
  `processed_rows` int NOT NULL DEFAULT '0',
  `successful_rows` int NOT NULL DEFAULT '0',
  `failed_rows` int NOT NULL DEFAULT '0',
  `duplicate_rows` int NOT NULL DEFAULT '0',
  `processing_summary` json DEFAULT NULL,
  `error_log` text COLLATE utf8mb4_unicode_ci,
  `processing_started_at` timestamp NULL DEFAULT NULL,
  `processing_completed_at` timestamp NULL DEFAULT NULL,
  `sample_data` json DEFAULT NULL,
  `detected_columns` json DEFAULT NULL,
  `requires_review` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_file_uploads_import_template_id_foreign` (`import_template_id`),
  KEY `import_file_uploads_file_hash_index` (`file_hash`),
  KEY `import_file_uploads_status_uploaded_at_index` (`status`,`uploaded_at`),
  KEY `import_file_uploads_uploaded_by_index` (`uploaded_by`),
  CONSTRAINT `import_file_uploads_import_template_id_foreign` FOREIGN KEY (`import_template_id`) REFERENCES `import_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `import_file_uploads_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_file_uploads`
--

LOCK TABLES `import_file_uploads` WRITE;
/*!40000 ALTER TABLE `import_file_uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_file_uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_row_results`
--

DROP TABLE IF EXISTS `import_row_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_row_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `import_file_upload_id` bigint unsigned NOT NULL,
  `row_number` int NOT NULL,
  `status` enum('success','failed','duplicate','skipped') COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `raw_data` json NOT NULL,
  `transformed_data` json DEFAULT NULL,
  `wms_staging_order_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_row_results_wms_staging_order_id_foreign` (`wms_staging_order_id`),
  KEY `import_row_results_import_file_upload_id_status_index` (`import_file_upload_id`,`status`),
  KEY `import_row_results_row_number_index` (`row_number`),
  CONSTRAINT `import_row_results_import_file_upload_id_foreign` FOREIGN KEY (`import_file_upload_id`) REFERENCES `import_file_uploads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `import_row_results_wms_staging_order_id_foreign` FOREIGN KEY (`wms_staging_order_id`) REFERENCES `wms_staging_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_row_results`
--

LOCK TABLES `import_row_results` WRITE;
/*!40000 ALTER TABLE `import_row_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_row_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_templates`
--

DROP TABLE IF EXISTS `import_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_system` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'csv',
  `description` text COLLATE utf8mb4_unicode_ci,
  `header_row` int NOT NULL DEFAULT '1',
  `data_start_row` int NOT NULL DEFAULT '2',
  `delimiter` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ',',
  `text_qualifier` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '"',
  `encoding` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTF-8',
  `column_mapping` json NOT NULL,
  `default_values` json DEFAULT NULL,
  `transformation_rules` json DEFAULT NULL,
  `required_columns` json DEFAULT NULL,
  `validation_rules` json DEFAULT NULL,
  `auto_process` tinyint(1) NOT NULL DEFAULT '1',
  `duplicate_handling` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'skip',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `files_processed` int NOT NULL DEFAULT '0',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_templates_source_system_is_active_index` (`source_system`,`is_active`),
  KEY `import_templates_file_type_index` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_templates`
--

LOCK TABLES `import_templates` WRITE;
/*!40000 ALTER TABLE `import_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `load_collections`
--

DROP TABLE IF EXISTS `load_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `load_collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outbound_load_id` bigint unsigned NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `planned_collection_time` timestamp NOT NULL,
  `actual_collection_time` timestamp NULL DEFAULT NULL,
  `estimated_duration_minutes` int NOT NULL DEFAULT '30',
  `actual_duration_minutes` int DEFAULT NULL,
  `collection_sequence` int DEFAULT NULL,
  `collection_notes` text COLLATE utf8mb4_unicode_ci,
  `depot_pallets` int NOT NULL DEFAULT '0',
  `depot_cases` int NOT NULL DEFAULT '0',
  `depot_units` int NOT NULL DEFAULT '0',
  `depot_weight_kg` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','ready','collecting','collected','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `load_collections_outbound_load_id_depot_id_unique` (`outbound_load_id`,`depot_id`),
  KEY `load_collections_depot_id_foreign` (`depot_id`),
  KEY `load_collections_outbound_load_id_depot_id_index` (`outbound_load_id`,`depot_id`),
  KEY `load_collections_planned_collection_time_index` (`planned_collection_time`),
  KEY `load_collections_outbound_load_id_collection_sequence_index` (`outbound_load_id`,`collection_sequence`),
  KEY `load_collections_status_index` (`status`),
  CONSTRAINT `load_collections_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `load_collections_outbound_load_id_foreign` FOREIGN KEY (`outbound_load_id`) REFERENCES `outbound_loads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `load_collections`
--

LOCK TABLES `load_collections` WRITE;
/*!40000 ALTER TABLE `load_collections` DISABLE KEYS */;
/*!40000 ALTER TABLE `load_collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_01_01_000001_create_depots_table',1),(2,'2025_01_01_000002_create_users_table',1),(3,'2025_01_01_000003_create_customers_table',1),(4,'2025_01_01_000004_create_booking_types_table',1),(5,'2025_01_02_000001_create_sessions_table',1),(6,'2025_01_02_000002_create_cache_table',1),(7,'2025_01_02_000003_create_password_reset_tokens_table',1),(8,'2025_01_03_000001_create_permission_tables',1),(9,'2025_01_04_000001_create_slots_table',1),(10,'2025_01_04_000002_create_bookings_table',1),(11,'2025_01_05_000001_create_carriers_system',1),(12,'2025_01_06_000001_create_depot_user_table',1),(13,'2025_01_06_000002_create_customer_user_table',1),(14,'2025_01_07_000001_add_deleted_at_to_depots_table',1),(15,'2025_01_07_000002_add_deleted_at_to_users_table',1),(16,'2025_01_07_000003_add_deleted_at_to_customers_table',1),(17,'2025_01_07_000004_add_deleted_at_to_booking_types_table',1),(18,'2025_01_07_000005_add_deleted_at_to_slots_table',1),(19,'2025_01_07_000006_add_deleted_at_to_bookings_table',1),(20,'2025_01_08_000001_add_customer_id_to_users_table',1),(21,'2025_01_09_000001_create_products_and_booking_product_tables',1),(22,'2025_01_09_000002_create_depot_product_table',1),(23,'2025_01_09_000003_create_customer_depot_product_table',1),(24,'2025_01_09_000004_create_depot_case_ranges_table',1),(25,'2025_01_10_000001_add_case_and_size_to_bookings_table',1),(26,'2025_01_10_000002_add_details_to_bookings_table',1),(27,'2025_01_10_000003_add_arrival_departure_to_bookings_table',1),(28,'2025_01_10_000004_add_customer_id_to_bookings_table',1),(29,'2025_01_10_000005_add_expected_actual_fields_to_bookings_table',1),(30,'2025_01_10_000006_edit_expected_actual_fields_to_bookings_table',1),(31,'2025_01_10_000007_add_status_to_bookings_table',1),(32,'2025_01_10_000008_add_end_time_to_bookings_table',1),(33,'2025_01_11_000001_create_slot_templates_table',1),(34,'2025_01_11_000002_add_capacity_to_slots_table',1),(35,'2025_01_11_000003_add_duration_minutes_to_booking_types_table',1),(36,'2025_01_11_000004_create_booking_type_depot_table',1),(37,'2025_01_11_000005_create_slot_generation_settings_table',1),(38,'2025_01_11_000006_add_cut_off_time_to_depots_table',1),(39,'2025_01_12_000001_create_slot_release_rules_table',1),(40,'2025_01_12_000002_create_slot_release_rule_customer',1),(41,'2025_01_12_000003_add_release_and_cutoff_to_slots',1),(42,'2025_01_12_000004_create_slot_customer_table',1),(43,'2025_01_13_000001_add_transportation_fields_to_bookings_table',1),(44,'2025_01_13_000002_rename_trailer_number_to_container_number',1),(45,'2025_01_13_000003_update_container_number_index',1),(46,'2025_01_14_000001_create_trailer_types_table',1),(47,'2025_01_15_000001_create_booking_history_table',1),(48,'2025_01_15_000002_add_rebooking_fields_to_bookings_table',1),(49,'2025_01_15_000003_create_customer_behavior_settings_table',1),(50,'2025_01_16_000001_create_arrival_time_settings_table',1),(51,'2025_01_17_000001_create_tipping_locations_table',1),(52,'2025_01_17_000002_create_tipping_bays_table',1),(53,'2025_01_17_000003_add_tipping_fields_to_bookings_table',1),(54,'2025_01_17_000004_add_tipping_workflow_enabled_to_settings',1),(55,'2025_01_17_000005_add_waiting_area_fields_to_bookings_table',1),(56,'2025_01_17_000006_add_trailer_collection_fields_to_bookings_table',1),(57,'2025_01_18_000001_create_booking_po_numbers_table',1),(58,'2025_01_18_000002_create_pallet_types_table',1),(59,'2025_01_18_000003_create_po_lines_table',1),(60,'2025_01_18_000004_create_po_line_actual_pallets_table',1),(61,'2025_01_19_000001_create_vehicles_table',1),(62,'2025_01_19_000002_create_trailers_table',1),(63,'2025_01_19_000003_create_movements_table',1),(64,'2025_01_19_000004_create_consignments_table',1),(65,'2025_01_19_000005_create_consignment_references_table',1),(66,'2025_01_19_000006_create_consignment_loads_table',1),(67,'2025_01_19_000007_create_movement_loads_table',1),(68,'2025_01_20_000001_add_vehicle_details_json_to_bookings_table',1),(69,'2025_01_21_000001_remove_driver_fields_from_bookings_table',1),(70,'2025_01_21_000002_make_container_size_nullable_in_bookings_table',1),(71,'2025_01_21_000003_remove_quantity_columns_from_booking_tables',1),(72,'2025_01_21_000004_remove_reference_and_gate_fields_from_bookings',1),(73,'2025_01_21_000005_remove_vehicle_fields_from_bookings_table',1),(74,'2025_01_22_000001_add_foreign_key_constraints',1),(75,'2025_01_23_000001_add_trailer_type_id_to_bookings_manual',1),(76,'2025_08_14_165255_add_vehicle_details_to_bookings_table',1),(77,'2025_08_15_073303_add_missing_tipping_bay_id_to_bookings_table',1),(78,'2025_08_16_191613_update_booking_history_action_enum',1),(79,'2025_08_16_200000_add_timing_fields_to_movements_table',1),(80,'2025_08_16_220000_add_in_location_status_to_movements',1),(81,'2025_08_16_230000_add_unit_departure_and_collection_times',1),(82,'2025_08_16_230500_add_trailer_collected_status',1),(83,'2025_08_17_161316_create_factory_bookings_table',1),(84,'2025_08_17_162048_add_factory_booking_support_to_movements_and_po_numbers',1),(85,'2025_08_18_124536_add_priority_fields',1),(86,'2025_08_18_124546_add_collection_scheduling_to_bookings',1),(87,'2025_08_18_131017_add_tipping_type_to_bookings',1),(88,'2025_08_18_134007_simplify_tipping_type_enum',1),(89,'2025_08_19_063310_add_location_type_to_tipping_locations_table',1),(90,'2025_08_20_122945_add_coordinates_to_tipping_bays_table',1),(91,'2025_08_20_123024_add_map_file_to_depots_table',1),(92,'2025_08_20_194548_add_visual_properties_to_tipping_bays_table',1),(93,'2025_08_20_194645_add_text_color_to_tipping_bays_table',1),(94,'2025_08_20_200937_add_visual_properties_to_tipping_locations_table',1),(95,'2025_08_21_064815_add_tipping_type_to_factory_bookings_table',1),(96,'2025_08_21_065827_create_user_functions_table',1),(97,'2025_08_21_082115_add_customer_id_to_users_table_fix',1),(98,'2025_08_21_110859_create_custom_roles_table',1),(99,'2025_08_21_110918_create_user_custom_roles_table',1),(100,'2025_08_22_073614_add_factory_delivery_to_movements_table',1),(101,'2025_08_23_152130_add_is_active_to_users_table',1),(102,'2025_08_24_000001_create_outbound_loads_table',1),(103,'2025_08_24_000002_create_customer_addresses_table',1),(104,'2025_08_24_000003_create_outbound_orders_table',1),(105,'2025_08_24_000004_create_load_collections_table',1),(106,'2025_08_24_000005_create_wms_staging_tables',1),(107,'2025_08_24_000006_create_import_configuration_tables',1),(108,'2025_08_26_115902_add_map_fields_to_tipping_locations_table',1),(109,'2025_08_27_114650_simplify_location_system_to_parking_and_bays',1),(110,'2025_08_28_132811_fix_movement_status_enum_transition',1),(111,'2025_10_06_100727_create_slot_bookings_table',2),(112,'2025_10_06_100732_add_booking_type_id_and_capacity_to_slot_templates',2),(113,'2025_10_06_120103_drop_unique_constraint_from_bookings_slot_id',3),(114,'2025_10_06_121117_add_seal_number_to_bookings_table',4),(115,'2025_10_06_121807_add_customer_id_to_products_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(4,'App\\Models\\User',5),(4,'App\\Models\\User',6),(4,'App\\Models\\User',7),(1,'App\\Models\\User',28);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movement_loads`
--

DROP TABLE IF EXISTS `movement_loads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movement_loads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `movement_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `operation_type` enum('inbound','outbound','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int NOT NULL DEFAULT '1',
  `expected_cases` int NOT NULL DEFAULT '0',
  `expected_pallets` int NOT NULL DEFAULT '0',
  `expected_pallet_type_id` bigint unsigned DEFAULT NULL,
  `actual_cases` int DEFAULT NULL,
  `actual_pallets` int DEFAULT NULL,
  `actual_pallet_type_id` bigint unsigned DEFAULT NULL,
  `customer_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_started_at` timestamp NULL DEFAULT NULL,
  `operation_completed_at` timestamp NULL DEFAULT NULL,
  `operation_notes` text COLLATE utf8mb4_unicode_ci,
  `booking_po_line_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movement_loads_expected_pallet_type_id_foreign` (`expected_pallet_type_id`),
  KEY `movement_loads_actual_pallet_type_id_foreign` (`actual_pallet_type_id`),
  KEY `movement_loads_movement_id_operation_type_index` (`movement_id`,`operation_type`),
  KEY `movement_loads_customer_id_operation_type_index` (`customer_id`,`operation_type`),
  KEY `movement_loads_po_number_index` (`po_number`),
  CONSTRAINT `movement_loads_actual_pallet_type_id_foreign` FOREIGN KEY (`actual_pallet_type_id`) REFERENCES `pallet_types` (`id`),
  CONSTRAINT `movement_loads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `movement_loads_expected_pallet_type_id_foreign` FOREIGN KEY (`expected_pallet_type_id`) REFERENCES `pallet_types` (`id`),
  CONSTRAINT `movement_loads_movement_id_foreign` FOREIGN KEY (`movement_id`) REFERENCES `movements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movement_loads`
--

LOCK TABLES `movement_loads` WRITE;
/*!40000 ALTER TABLE `movement_loads` DISABLE KEYS */;
/*!40000 ALTER TABLE `movement_loads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movements`
--

DROP TABLE IF EXISTS `movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `movement_type` enum('inbound_booked','inbound_unbooked','outbound','internal_transfer','factory_delivery') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `depot_id` bigint unsigned NOT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `trailer_id` bigint unsigned DEFAULT NULL,
  `carrier_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_arrival` timestamp NULL DEFAULT NULL,
  `actual_arrival` timestamp NULL DEFAULT NULL,
  `estimated_departure` timestamp NULL DEFAULT NULL,
  `actual_departure` timestamp NULL DEFAULT NULL,
  `unit_departed_at` timestamp NULL DEFAULT NULL,
  `collection_unit_arrived_at` timestamp NULL DEFAULT NULL,
  `collection_unit_departed_at` timestamp NULL DEFAULT NULL,
  `collection_unit_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_notes` text COLLATE utf8mb4_unicode_ci,
  `current_status` enum('scheduled','en_route','arrived','in_parking','at_bay','unloading','empty','back_to_parking','departed','trailer_collected') COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `gate_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipping_location_id` bigint unsigned DEFAULT NULL,
  `tipping_bay_id` bigint unsigned DEFAULT NULL,
  `current_location_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hazmat` tinyint(1) NOT NULL DEFAULT '0',
  `temperature_requirements` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `unloading_started_at` timestamp NULL DEFAULT NULL,
  `unloading_completed_at` timestamp NULL DEFAULT NULL,
  `moved_to_bay_at` timestamp NULL DEFAULT NULL,
  `moved_to_location_at` timestamp NULL DEFAULT NULL,
  `loading_started_at` timestamp NULL DEFAULT NULL,
  `loading_completed_at` timestamp NULL DEFAULT NULL,
  `operation_notes` text COLLATE utf8mb4_unicode_ci,
  `trailer_dropped_at` timestamp NULL DEFAULT NULL,
  `trailer_collected_at` timestamp NULL DEFAULT NULL,
  `collecting_vehicle_id` bigint unsigned DEFAULT NULL,
  `swap_notes` text COLLATE utf8mb4_unicode_ci,
  `booking_id` bigint unsigned DEFAULT NULL,
  `consignment_id` bigint unsigned DEFAULT NULL,
  `additional_data` json DEFAULT NULL,
  `custom_fields` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `factory_booking_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movements_trailer_id_foreign` (`trailer_id`),
  KEY `movements_tipping_location_id_foreign` (`tipping_location_id`),
  KEY `movements_tipping_bay_id_foreign` (`tipping_bay_id`),
  KEY `movements_collecting_vehicle_id_foreign` (`collecting_vehicle_id`),
  KEY `movements_booking_id_foreign` (`booking_id`),
  KEY `movements_reference_number_index` (`reference_number`),
  KEY `movements_movement_type_index` (`movement_type`),
  KEY `movements_current_status_index` (`current_status`),
  KEY `movements_actual_arrival_index` (`actual_arrival`),
  KEY `movements_depot_id_index` (`depot_id`),
  KEY `movements_vehicle_id_trailer_id_index` (`vehicle_id`,`trailer_id`),
  KEY `movements_consignment_id_foreign` (`consignment_id`),
  KEY `movements_factory_booking_id_foreign` (`factory_booking_id`),
  CONSTRAINT `movements_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `movements_collecting_vehicle_id_foreign` FOREIGN KEY (`collecting_vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `movements_consignment_id_foreign` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`),
  CONSTRAINT `movements_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `movements_factory_booking_id_foreign` FOREIGN KEY (`factory_booking_id`) REFERENCES `factory_bookings` (`id`),
  CONSTRAINT `movements_tipping_bay_id_foreign` FOREIGN KEY (`tipping_bay_id`) REFERENCES `tipping_bays` (`id`),
  CONSTRAINT `movements_tipping_location_id_foreign` FOREIGN KEY (`tipping_location_id`) REFERENCES `tipping_locations` (`id`),
  CONSTRAINT `movements_trailer_id_foreign` FOREIGN KEY (`trailer_id`) REFERENCES `trailers` (`id`),
  CONSTRAINT `movements_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movements`
--

LOCK TABLES `movements` WRITE;
/*!40000 ALTER TABLE `movements` DISABLE KEYS */;
INSERT INTO `movements` VALUES (1,'factory_delivery','FAC-2025-001',1,NULL,NULL,'Knowles Logistics',NULL,NULL,NULL,NULL,'2025-08-31 18:31:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'arrived',NULL,NULL,NULL,NULL,'factory_delivery',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 18:31:19','2025-08-31 18:31:19',1),(2,'inbound_booked','WM-20250831-B120',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'in_parking',NULL,3,1,NULL,NULL,0,NULL,NULL,NULL,NULL,'2025-08-31 18:31:46','2025-08-31 22:21:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2025-08-31 18:31:46','2025-08-31 22:21:24',NULL);
/*!40000 ALTER TABLE `movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outbound_loads`
--

DROP TABLE IF EXISTS `outbound_loads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outbound_loads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `load_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `load_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_from` enum('manual','booking_completion','factory_completion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `planned_vehicle_id` bigint unsigned DEFAULT NULL,
  `assigned_driver_id` bigint unsigned DEFAULT NULL,
  `total_orders` int NOT NULL DEFAULT '0',
  `total_customers` int NOT NULL DEFAULT '0',
  `total_collection_points` int NOT NULL DEFAULT '0',
  `total_delivery_points` int NOT NULL DEFAULT '0',
  `total_pallets` int NOT NULL DEFAULT '0',
  `total_cases` int NOT NULL DEFAULT '0',
  `total_units` int NOT NULL DEFAULT '0',
  `total_weight_kg` decimal(10,2) DEFAULT NULL,
  `status` enum('planning','ready_for_collection','collecting','in_transit','delivering','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planning',
  `optimized_distance_km` decimal(8,2) DEFAULT NULL,
  `estimated_duration_minutes` int DEFAULT NULL,
  `optimization_score` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outbound_loads_load_reference_unique` (`load_reference`),
  KEY `outbound_loads_assigned_driver_id_foreign` (`assigned_driver_id`),
  KEY `outbound_loads_created_by_foreign` (`created_by`),
  KEY `outbound_loads_load_reference_index` (`load_reference`),
  KEY `outbound_loads_status_index` (`status`),
  KEY `outbound_loads_planned_vehicle_id_assigned_driver_id_index` (`planned_vehicle_id`,`assigned_driver_id`),
  CONSTRAINT `outbound_loads_assigned_driver_id_foreign` FOREIGN KEY (`assigned_driver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `outbound_loads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `outbound_loads_planned_vehicle_id_foreign` FOREIGN KEY (`planned_vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outbound_loads`
--

LOCK TABLES `outbound_loads` WRITE;
/*!40000 ALTER TABLE `outbound_loads` DISABLE KEYS */;
/*!40000 ALTER TABLE `outbound_loads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outbound_orders`
--

DROP TABLE IF EXISTS `outbound_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outbound_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outbound_load_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_address_id` bigint unsigned NOT NULL,
  `order_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_order_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_depot_id` bigint unsigned NOT NULL,
  `collection_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `planned_delivery_date` date DEFAULT NULL,
  `planned_delivery_time_start` time DEFAULT NULL,
  `planned_delivery_time_end` time DEFAULT NULL,
  `estimated_delivery_time` timestamp NULL DEFAULT NULL,
  `actual_delivery_time` timestamp NULL DEFAULT NULL,
  `expected_pallets` int NOT NULL DEFAULT '0',
  `expected_cases` int NOT NULL DEFAULT '0',
  `expected_units` int NOT NULL DEFAULT '0',
  `expected_weight_kg` decimal(10,2) DEFAULT NULL,
  `actual_pallets` int DEFAULT NULL,
  `actual_cases` int DEFAULT NULL,
  `actual_units` int DEFAULT NULL,
  `actual_weight_kg` decimal(10,2) DEFAULT NULL,
  `temperature_controlled` tinyint(1) NOT NULL DEFAULT '0',
  `fragile` tinyint(1) NOT NULL DEFAULT '0',
  `hazardous` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','ready_for_collection','collected','in_transit','out_for_delivery','delivered','failed','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `latest_vehicle_arrival_time` timestamp NULL DEFAULT NULL,
  `delivery_window_end` timestamp NULL DEFAULT NULL,
  `travel_time_to_site_minutes` int DEFAULT NULL,
  `site_processing_time_minutes` int NOT NULL DEFAULT '30',
  `delivery_priority` enum('standard','priority','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `must_deliver_by` timestamp NULL DEFAULT NULL,
  `preferred_delivery_window_start` time DEFAULT NULL,
  `preferred_delivery_window_end` time DEFAULT NULL,
  `collection_notes` text COLLATE utf8mb4_unicode_ci,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci,
  `handling_instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outbound_orders_outbound_load_id_order_reference_unique` (`outbound_load_id`,`order_reference`),
  KEY `outbound_orders_customer_id_foreign` (`customer_id`),
  KEY `outbound_orders_customer_address_id_foreign` (`customer_address_id`),
  KEY `outbound_orders_outbound_load_id_customer_id_index` (`outbound_load_id`,`customer_id`),
  KEY `outbound_orders_order_reference_index` (`order_reference`),
  KEY `outbound_orders_collection_depot_id_index` (`collection_depot_id`),
  KEY `outbound_orders_estimated_delivery_time_index` (`estimated_delivery_time`),
  KEY `outbound_orders_status_index` (`status`),
  KEY `outbound_orders_delivery_priority_index` (`delivery_priority`),
  CONSTRAINT `outbound_orders_collection_depot_id_foreign` FOREIGN KEY (`collection_depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `outbound_orders_customer_address_id_foreign` FOREIGN KEY (`customer_address_id`) REFERENCES `customer_addresses` (`id`),
  CONSTRAINT `outbound_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `outbound_orders_outbound_load_id_foreign` FOREIGN KEY (`outbound_load_id`) REFERENCES `outbound_loads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outbound_orders`
--

LOCK TABLES `outbound_orders` WRITE;
/*!40000 ALTER TABLE `outbound_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `outbound_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pallet_types`
--

DROP TABLE IF EXISTS `pallet_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pallet_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pallet_types_name_unique` (`name`),
  UNIQUE KEY `pallet_types_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pallet_types`
--

LOCK TABLES `pallet_types` WRITE;
/*!40000 ALTER TABLE `pallet_types` DISABLE KEYS */;
INSERT INTO `pallet_types` VALUES (1,'Euro Pallet','EUR','Standard European pallet (1200x800mm)',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(2,'UK Pallet','UK','Standard UK pallet (1200x1000mm)',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(3,'CP Pallet','CP','Chep pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(4,'LPR Pallet','LPR','LPR pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(5,'Block Pallet','BLOCK','Block pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(6,'Red Pallet','RED','Red colored pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(7,'Blue Pallet','BLUE','Blue colored pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(8,'GKN Pallet','GKN','GKN pallet',1,'2025-08-31 16:49:40','2025-08-31 16:49:40'),(9,'Half Pallet','HALF','Half size pallet 600x800x144mm',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(10,'Quarter Pallet','QTR','Quarter size pallet 600x400x144mm',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(11,'Plastic Pallet - Standard','PLAS-STD','Plastic pallet 1200x800x150mm - hygienic',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(12,'Plastic Pallet - Heavy Duty','PLAS-HD','Heavy duty plastic pallet 1200x1000x160mm',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(13,'Metal Pallet','METAL','Steel pallet 1200x800x150mm - industrial use',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(14,'Display Pallet','DISP','Display pallet 800x600x144mm - retail',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(15,'Export Pallet - Heat Treated','EXP-HT','Heat treated export pallet 1200x800x144mm (ISPM 15)',1,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(16,'Custom Size Pallet','CUSTOM','Custom manufactured pallet - various sizes',1,'2025-08-31 17:00:27','2025-08-31 17:00:27');
/*!40000 ALTER TABLE `pallet_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_reset_tokens_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `physical_load_registrations`
--

DROP TABLE IF EXISTS `physical_load_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `physical_load_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `load_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_paperwork_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trailer_registration` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_company` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_license` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_time` timestamp NOT NULL,
  `arrival_depot_id` bigint unsigned NOT NULL,
  `arrival_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('arrived','orders_matched','ready_for_collection','collecting','departed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'arrived',
  `outbound_load_id` bigint unsigned DEFAULT NULL,
  `expected_orders` int NOT NULL DEFAULT '0',
  `matched_orders` int NOT NULL DEFAULT '0',
  `registered_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `physical_load_registrations_load_reference_unique` (`load_reference`),
  KEY `physical_load_registrations_arrival_depot_id_foreign` (`arrival_depot_id`),
  KEY `physical_load_registrations_outbound_load_id_foreign` (`outbound_load_id`),
  KEY `physical_load_registrations_registered_by_foreign` (`registered_by`),
  KEY `physical_load_registrations_load_reference_index` (`load_reference`),
  KEY `physical_load_registrations_vehicle_registration_index` (`vehicle_registration`),
  KEY `physical_load_registrations_status_index` (`status`),
  KEY `physical_load_registrations_arrival_time_index` (`arrival_time`),
  CONSTRAINT `physical_load_registrations_arrival_depot_id_foreign` FOREIGN KEY (`arrival_depot_id`) REFERENCES `depots` (`id`),
  CONSTRAINT `physical_load_registrations_outbound_load_id_foreign` FOREIGN KEY (`outbound_load_id`) REFERENCES `outbound_loads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `physical_load_registrations_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physical_load_registrations`
--

LOCK TABLES `physical_load_registrations` WRITE;
/*!40000 ALTER TABLE `physical_load_registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `physical_load_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_line_actual_pallets`
--

DROP TABLE IF EXISTS `po_line_actual_pallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_line_actual_pallets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_line_id` bigint unsigned NOT NULL,
  `pallet_type_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `po_line_actual_pallets_pallet_type_id_foreign` (`pallet_type_id`),
  KEY `po_line_actual_pallets_po_line_id_pallet_type_id_index` (`po_line_id`,`pallet_type_id`),
  CONSTRAINT `po_line_actual_pallets_pallet_type_id_foreign` FOREIGN KEY (`pallet_type_id`) REFERENCES `pallet_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `po_line_actual_pallets_po_line_id_foreign` FOREIGN KEY (`po_line_id`) REFERENCES `po_lines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_line_actual_pallets`
--

LOCK TABLES `po_line_actual_pallets` WRITE;
/*!40000 ALTER TABLE `po_line_actual_pallets` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_line_actual_pallets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_lines`
--

DROP TABLE IF EXISTS `po_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_po_number_id` bigint unsigned NOT NULL,
  `line_number` int NOT NULL DEFAULT '1',
  `expected_cases` int DEFAULT NULL,
  `expected_pallets` int DEFAULT NULL,
  `expected_pallet_type_id` bigint unsigned DEFAULT NULL,
  `actual_cases` int DEFAULT NULL,
  `actual_pallets` int DEFAULT NULL,
  `actual_pallet_type_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `bbe` date DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `batch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scc_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_lines_booking_po_number_id_line_number_unique` (`booking_po_number_id`,`line_number`),
  KEY `po_lines_expected_pallet_type_id_foreign` (`expected_pallet_type_id`),
  KEY `po_lines_actual_pallet_type_id_foreign` (`actual_pallet_type_id`),
  CONSTRAINT `po_lines_actual_pallet_type_id_foreign` FOREIGN KEY (`actual_pallet_type_id`) REFERENCES `pallet_types` (`id`),
  CONSTRAINT `po_lines_booking_po_number_id_foreign` FOREIGN KEY (`booking_po_number_id`) REFERENCES `booking_po_numbers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `po_lines_expected_pallet_type_id_foreign` FOREIGN KEY (`expected_pallet_type_id`) REFERENCES `pallet_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_lines`
--

LOCK TABLES `po_lines` WRITE;
/*!40000 ALTER TABLE `po_lines` DISABLE KEYS */;
INSERT INTO `po_lines` VALUES (1,1,1,2345,26,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 18:30:21','2025-08-31 18:30:21'),(2,2,1,4567,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 18:31:19','2025-08-31 18:31:19'),(3,3,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 12:52:21','2025-10-06 12:52:21'),(4,4,1,700,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 13:21:34','2025-10-06 13:21:34'),(5,5,1,700,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 13:26:11','2025-10-06 13:26:11'),(9,9,1,1,21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 13:42:26','2025-10-06 13:42:26'),(10,10,1,1,21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 13:43:39','2025-10-06 13:43:39'),(11,11,1,1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 13:48:00','2025-10-06 13:48:00'),(14,14,1,700,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 15:10:38','2025-10-06 15:10:38'),(15,15,1,12,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 15:13:54','2025-10-06 15:13:54');
/*!40000 ALTER TABLE `po_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_case_count` int DEFAULT NULL,
  `default_pallets` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_customer_id_sku_unique` (`customer_id`,`sku`),
  CONSTRAINT `products_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2025-08-31 17:00:23','2025-08-31 17:00:23'),(2,'depot-admin','web','2025-08-31 17:00:23','2025-08-31 17:00:23'),(3,'site-admin','web','2025-08-31 17:00:23','2025-08-31 17:00:23'),(4,'customer','web','2025-08-31 17:00:23','2025-08-31 17:00:23');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('Em43e3hubQOtpyc3VVY8UFDQZYvDkxkSi9LuzyN7',NULL,'172.17.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVWxpT3pHQ3JkdnluMG0xcVJIbjl5amx2OWdERk8xTVZyUTViRFhBMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvaW1hZ2VzL2t0bC5zdmciO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1759762657),('f7mTL6l3jbzXzh6Ru6KR2O3Gwjt8N5lllv4sWX3t',1,'172.17.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiR2FRNHVMR2QxalhTbHBSZ0x6ZDlnUElWVjkzd2ZEMmYybldzcEhjYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvaW1hZ2VzL2t0bC5zdmciO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo2OToiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvYXBwL2Jvb2tpbmdzP2ZpbHRlcj10b21vcnJvdyZzdGF0dXM9YWxsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE1OiJib29raW5nX2ZpbHRlcnMiO2E6Mjp7czo2OiJzdGF0dXMiO3M6MzoiYWxsIjtzOjY6ImZpbHRlciI7czo4OiJ0b21vcnJvdyI7fX0=',1759762010),('gnzplx9Khm1uSiVDC60R2cCsQ7Iuae7WsVcZKzVD',1,'172.17.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoidTllZUVSTTBLWTFFNjRpNFdSNGlBVHlwRHppQ1g1bnRoWDJreGZNQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvaW1hZ2VzL2t0bC5zdmciO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo2OToiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvYXBwL2Jvb2tpbmdzP2ZpbHRlcj10b21vcnJvdyZzdGF0dXM9YWxsIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE1OiJib29raW5nX2ZpbHRlcnMiO2E6Mjp7czo2OiJzdGF0dXMiO3M6MzoiYWxsIjtzOjY6ImZpbHRlciI7czo4OiJ0b21vcnJvdyI7fX0=',1759758301),('sQLWGckam0eErnzE7ROvT5gJMrt6LTbTdhygrDYY',NULL,'172.17.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibTZoZ2VGemdjM1VXanF0aGozT2RXUGdiZGlIUWZianZYMW5Dc0VpRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MDoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvYWRtaW4vc2xvdC10ZW1wbGF0ZXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1759756545),('wLkPmi0mPbKHu5HxkNDB7buXoyn0AuQ1QNHID8yO',1,'172.17.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoia2F2bDlKZ0RjQ1dqTWJzY1FoN3hnMFJobWtIRzBlQXhGMUU3cEJVRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvaW1hZ2VzL2t0bC5zdmciO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0NzoiaHR0cDovL2Jvb2tpbmdzdWF0LmZ1cnkubWUudWsvYXBwL2Jvb2tpbmctdHlwZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTU6ImJvb2tpbmdfZmlsdGVycyI7YToyOntzOjY6InN0YXR1cyI7czozOiJhbGwiO3M6NjoiZmlsdGVyIjtzOjg6InRvbW9ycm93Ijt9fQ==',1759757151);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'tipping_workflow_enabled','1','boolean','Enable or disable the tipping workflow system','2025-08-31 16:48:43','2025-08-31 16:48:43'),(2,'outbound_module_enabled','0','boolean',NULL,'2025-08-31 22:24:33','2025-08-31 22:27:23'),(3,'admin_approval_emails','paul.carr@knowleslogistics.com','string',NULL,'2025-09-03 09:39:57','2025-09-03 09:39:57');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_bookings`
--

DROP TABLE IF EXISTS `slot_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slot_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slot_bookings_slot_id_booking_id_unique` (`slot_id`,`booking_id`),
  KEY `slot_bookings_booking_id_index` (`booking_id`),
  CONSTRAINT `slot_bookings_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `slot_bookings_slot_id_foreign` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_bookings`
--

LOCK TABLES `slot_bookings` WRITE;
/*!40000 ALTER TABLE `slot_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `slot_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_customer`
--

DROP TABLE IF EXISTS `slot_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_customer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slot_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slot_customer_slot_id_foreign` (`slot_id`),
  KEY `slot_customer_customer_id_foreign` (`customer_id`),
  CONSTRAINT `slot_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `slot_customer_slot_id_foreign` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_customer`
--

LOCK TABLES `slot_customer` WRITE;
/*!40000 ALTER TABLE `slot_customer` DISABLE KEYS */;
INSERT INTO `slot_customer` VALUES (1,1,1,NULL,NULL),(2,2,1,NULL,NULL);
/*!40000 ALTER TABLE `slot_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_generation_settings`
--

DROP TABLE IF EXISTS `slot_generation_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_generation_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `interval_minutes` int unsigned NOT NULL DEFAULT '60',
  `slots_per_block` int unsigned NOT NULL DEFAULT '1',
  `default_capacity` int unsigned NOT NULL DEFAULT '1',
  `days_active` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slot_generation_settings_depot_id_foreign` (`depot_id`),
  CONSTRAINT `slot_generation_settings_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_generation_settings`
--

LOCK TABLES `slot_generation_settings` WRITE;
/*!40000 ALTER TABLE `slot_generation_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `slot_generation_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_release_rule_customer`
--

DROP TABLE IF EXISTS `slot_release_rule_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_release_rule_customer` (
  `slot_release_rule_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`slot_release_rule_id`,`customer_id`),
  KEY `slot_release_rule_customer_customer_id_foreign` (`customer_id`),
  CONSTRAINT `slot_release_rule_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `slot_release_rule_customer_slot_release_rule_id_foreign` FOREIGN KEY (`slot_release_rule_id`) REFERENCES `slot_release_rules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_release_rule_customer`
--

LOCK TABLES `slot_release_rule_customer` WRITE;
/*!40000 ALTER TABLE `slot_release_rule_customer` DISABLE KEYS */;
INSERT INTO `slot_release_rule_customer` VALUES (1,1);
/*!40000 ALTER TABLE `slot_release_rule_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_release_rules`
--

DROP TABLE IF EXISTS `slot_release_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_release_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `release_day` tinyint NOT NULL COMMENT 'ISO day of week: 1=Mon … 7=Sun',
  `release_time` time NOT NULL,
  `lock_cutoff_days` int NOT NULL DEFAULT '1',
  `lock_cutoff_time` time NOT NULL DEFAULT '16:00:00',
  `priority` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slot_release_unique` (`depot_id`,`customer_id`,`release_day`,`release_time`),
  KEY `slot_release_rules_customer_id_foreign` (`customer_id`),
  CONSTRAINT `slot_release_rules_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `slot_release_rules_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_release_rules`
--

LOCK TABLES `slot_release_rules` WRITE;
/*!40000 ALTER TABLE `slot_release_rules` DISABLE KEYS */;
INSERT INTO `slot_release_rules` VALUES (1,1,NULL,3,'16:00:00',1,'16:00:00',50,'2025-08-31 18:27:31','2025-08-31 18:27:31');
/*!40000 ALTER TABLE `slot_release_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_templates`
--

DROP TABLE IF EXISTS `slot_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `booking_type_id` bigint unsigned DEFAULT NULL,
  `day_of_week` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_minutes` int NOT NULL,
  `capacity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slot_templates_depot_id_foreign` (`depot_id`),
  KEY `slot_templates_booking_type_id_foreign` (`booking_type_id`),
  CONSTRAINT `slot_templates_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `slot_templates_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_templates`
--

LOCK TABLES `slot_templates` WRITE;
/*!40000 ALTER TABLE `slot_templates` DISABLE KEYS */;
INSERT INTO `slot_templates` VALUES (1,1,NULL,'1','06:00:00','07:00:00',-60,1,'2025-08-31 18:26:26','2025-08-31 18:26:26'),(2,3,NULL,'1','08:00:00','09:00:00',-60,4,'2025-10-06 10:11:25','2025-10-06 12:15:54'),(6,3,NULL,'1','09:00:00','10:00:00',-60,4,'2025-10-06 10:14:35','2025-10-06 12:15:54'),(10,3,NULL,'1','10:00:00','11:00:00',-60,4,'2025-10-06 10:19:15','2025-10-06 12:15:54'),(14,3,NULL,'1','11:00:00','12:00:00',-60,4,'2025-10-06 10:21:58','2025-10-06 12:15:54'),(21,3,NULL,'1','12:00:00','13:00:00',-60,4,'2025-10-06 10:26:50','2025-10-06 12:15:54'),(23,3,NULL,'1','15:00:00','16:00:00',-60,2,'2025-10-06 10:28:32','2025-10-06 12:16:29'),(27,3,NULL,'1','13:00:00','14:00:00',-60,4,'2025-10-06 10:30:38','2025-10-06 12:15:54'),(73,3,NULL,'1','14:00:00','15:00:00',-60,4,'2025-10-06 12:19:30','2025-10-06 12:19:30'),(74,3,NULL,'2','08:00:00','09:00:00',-60,4,'2025-10-06 12:29:04','2025-10-06 12:40:12'),(75,3,NULL,'3','08:00:00','09:00:00',-60,4,'2025-10-06 12:29:06','2025-10-06 12:40:12'),(76,3,NULL,'4','08:00:00','09:00:00',-60,4,'2025-10-06 12:29:07','2025-10-06 12:40:12'),(77,3,NULL,'5','08:00:00','09:00:00',-60,4,'2025-10-06 12:29:07','2025-10-06 12:40:12'),(78,3,NULL,'2','09:00:00','10:00:00',-60,4,'2025-10-06 12:29:07','2025-10-06 12:40:12'),(79,3,NULL,'3','09:00:00','10:00:00',-60,4,'2025-10-06 12:29:08','2025-10-06 12:40:12'),(80,3,NULL,'4','09:00:00','10:00:00',-60,4,'2025-10-06 12:29:09','2025-10-06 12:40:12'),(81,3,NULL,'5','09:00:00','10:00:00',-60,4,'2025-10-06 12:29:11','2025-10-06 12:40:12'),(82,3,NULL,'2','10:00:00','11:00:00',-60,4,'2025-10-06 12:29:12','2025-10-06 12:40:12'),(83,3,NULL,'3','10:00:00','11:00:00',-60,4,'2025-10-06 12:29:13','2025-10-06 12:40:12'),(84,3,NULL,'4','10:00:00','11:00:00',-60,4,'2025-10-06 12:29:13','2025-10-06 12:40:12'),(85,3,NULL,'5','10:00:00','11:00:00',-60,4,'2025-10-06 12:29:13','2025-10-06 12:40:12'),(86,3,NULL,'2','11:00:00','12:00:00',-60,4,'2025-10-06 12:29:13','2025-10-06 12:40:12'),(88,3,NULL,'3','11:00:00','12:00:00',-60,4,'2025-10-06 12:29:14','2025-10-06 12:40:12'),(89,3,NULL,'4','11:00:00','12:00:00',-60,4,'2025-10-06 12:29:15','2025-10-06 12:40:12'),(92,3,NULL,'5','11:00:00','12:00:00',-60,4,'2025-10-06 12:29:18','2025-10-06 12:40:12'),(94,3,NULL,'2','12:00:00','13:00:00',-60,4,'2025-10-06 12:29:20','2025-10-06 12:40:12'),(95,3,NULL,'3','12:00:00','13:00:00',-60,4,'2025-10-06 12:29:20','2025-10-06 12:40:12'),(97,3,NULL,'4','12:00:00','13:00:00',-60,4,'2025-10-06 12:29:20','2025-10-06 12:40:12'),(99,3,NULL,'5','12:00:00','13:00:00',-60,4,'2025-10-06 12:29:22','2025-10-06 12:40:12'),(100,3,NULL,'2','15:00:00','16:00:00',-60,2,'2025-10-06 12:29:23','2025-10-06 12:40:47'),(103,3,NULL,'3','15:00:00','16:00:00',-60,2,'2025-10-06 12:29:27','2025-10-06 12:40:47'),(104,3,NULL,'4','15:00:00','16:00:00',-60,2,'2025-10-06 12:29:28','2025-10-06 12:40:47'),(107,3,NULL,'5','15:00:00','16:00:00',-60,2,'2025-10-06 12:29:32','2025-10-06 12:40:47'),(111,3,NULL,'3','14:00:00','15:00:00',-60,4,'2025-10-06 12:29:34','2025-10-06 12:40:12'),(112,3,NULL,'4','13:00:00','14:00:00',-60,4,'2025-10-06 12:29:35','2025-10-06 12:40:12'),(114,3,NULL,'5','13:00:00','14:00:00',-60,4,'2025-10-06 12:29:37','2025-10-06 12:40:12'),(116,3,NULL,'2','13:00:00','14:00:00',-60,4,'2025-10-06 12:29:38','2025-10-06 12:40:12'),(117,3,NULL,'2','14:00:00','15:00:00',-60,4,'2025-10-06 12:29:38','2025-10-06 12:40:12'),(119,3,NULL,'3','13:00:00','14:00:00',-60,4,'2025-10-06 12:29:43','2025-10-06 12:40:12'),(121,3,NULL,'4','14:00:00','15:00:00',-60,4,'2025-10-06 12:29:45','2025-10-06 12:40:12'),(123,3,NULL,'5','14:00:00','15:00:00',-60,4,'2025-10-06 12:29:45','2025-10-06 12:40:12');
/*!40000 ALTER TABLE `slot_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slots`
--

DROP TABLE IF EXISTS `slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `booking_type_id` bigint unsigned DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `capacity` int unsigned NOT NULL DEFAULT '1',
  `released_at` timestamp NULL DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slots_booking_type_id_foreign` (`booking_type_id`),
  KEY `slots_depot_id_start_at_index` (`depot_id`,`start_at`),
  CONSTRAINT `slots_booking_type_id_foreign` FOREIGN KEY (`booking_type_id`) REFERENCES `booking_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `slots_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slots`
--

LOCK TABLES `slots` WRITE;
/*!40000 ALTER TABLE `slots` DISABLE KEYS */;
INSERT INTO `slots` VALUES (1,1,NULL,'2025-09-01 06:00:00','2025-09-01 09:00:00',0,'2025-08-31 18:29:08','2025-08-31 18:30:21',NULL,1,NULL,NULL),(2,1,NULL,'2025-09-08 06:00:00','2025-09-08 07:00:00',0,'2025-08-31 18:29:08','2025-08-31 18:29:08',NULL,1,NULL,NULL),(3,3,NULL,'2025-10-13 08:00:00','2025-10-13 09:00:00',0,'2025-10-06 12:41:46','2025-10-06 12:41:46',NULL,4,'2025-10-06 12:41:46',NULL),(4,3,NULL,'2025-10-20 08:00:00','2025-10-20 09:00:00',0,'2025-10-06 12:41:46','2025-10-06 12:41:52',NULL,4,'2025-10-06 12:41:52',NULL),(5,3,NULL,'2025-10-13 09:00:00','2025-10-13 10:00:00',0,'2025-10-06 12:41:52','2025-10-06 12:41:52',NULL,4,'2025-10-06 12:41:52',NULL),(6,3,NULL,'2025-10-20 09:00:00','2025-10-20 10:00:00',0,'2025-10-06 12:41:54','2025-10-06 12:41:57',NULL,4,'2025-10-06 12:41:57',NULL),(7,3,NULL,'2025-10-13 10:00:00','2025-10-13 11:00:00',0,'2025-10-06 12:41:57','2025-10-06 12:41:57',NULL,4,'2025-10-06 12:41:57',NULL),(8,3,NULL,'2025-10-20 10:00:00','2025-10-20 11:00:00',0,'2025-10-06 12:42:00','2025-10-06 12:42:03',NULL,4,'2025-10-06 12:42:03',NULL),(9,3,NULL,'2025-10-13 11:00:00','2025-10-13 12:00:00',0,'2025-10-06 12:42:04','2025-10-06 12:42:05',NULL,4,'2025-10-06 12:42:05',NULL),(10,3,NULL,'2025-10-20 11:00:00','2025-10-20 12:00:00',0,'2025-10-06 12:42:08','2025-10-06 12:42:09',NULL,4,'2025-10-06 12:42:09',NULL),(11,3,NULL,'2025-10-13 12:00:00','2025-10-13 13:00:00',0,'2025-10-06 12:42:10','2025-10-06 12:42:10',NULL,4,'2025-10-06 12:42:10',NULL),(12,3,NULL,'2025-10-20 12:00:00','2025-10-20 13:00:00',0,'2025-10-06 12:42:13','2025-10-06 12:42:16',NULL,4,'2025-10-06 12:42:16',NULL),(13,3,NULL,'2025-10-13 15:00:00','2025-10-13 16:00:00',0,'2025-10-06 12:42:17','2025-10-06 12:42:20',NULL,2,'2025-10-06 12:42:20',NULL),(14,3,NULL,'2025-10-20 15:00:00','2025-10-20 16:00:00',0,'2025-10-06 12:42:22','2025-10-06 12:42:25',NULL,2,'2025-10-06 12:42:25',NULL),(15,3,NULL,'2025-10-13 13:00:00','2025-10-13 14:00:00',0,'2025-10-06 12:42:30','2025-10-06 12:42:30',NULL,4,'2025-10-06 12:42:30',NULL),(16,3,NULL,'2025-10-20 13:00:00','2025-10-20 14:00:00',0,'2025-10-06 12:42:30','2025-10-06 12:42:33',NULL,4,'2025-10-06 12:42:33',NULL),(17,3,NULL,'2025-10-13 14:00:00','2025-10-13 15:00:00',0,'2025-10-06 12:42:35','2025-10-06 12:42:36',NULL,4,'2025-10-06 12:42:36',NULL),(18,3,NULL,'2025-10-20 14:00:00','2025-10-20 15:00:00',0,'2025-10-06 12:42:38','2025-10-06 12:42:41',NULL,4,'2025-10-06 12:42:41',NULL),(19,3,NULL,'2025-10-07 08:00:00','2025-10-07 11:00:00',0,'2025-10-06 12:42:45','2025-10-06 13:42:30',NULL,4,'2025-10-06 12:42:48',NULL),(20,3,NULL,'2025-10-14 08:00:00','2025-10-14 09:00:00',0,'2025-10-06 12:42:48','2025-10-06 12:42:48',NULL,4,'2025-10-06 12:42:48',NULL),(21,3,NULL,'2025-10-08 08:00:00','2025-10-08 09:00:00',0,'2025-10-06 12:42:50','2025-10-06 12:42:53',NULL,4,'2025-10-06 12:42:53',NULL),(22,3,NULL,'2025-10-15 08:00:00','2025-10-15 09:00:00',0,'2025-10-06 12:42:55','2025-10-06 12:42:55',NULL,4,'2025-10-06 12:42:55',NULL),(23,3,NULL,'2025-10-09 08:00:00','2025-10-09 09:00:00',0,'2025-10-06 12:43:00','2025-10-06 12:43:01',NULL,4,'2025-10-06 12:43:01',NULL),(24,3,NULL,'2025-10-16 08:00:00','2025-10-16 09:00:00',0,'2025-10-06 12:43:05','2025-10-06 12:43:07',NULL,4,'2025-10-06 12:43:07',NULL),(25,3,NULL,'2025-10-10 08:00:00','2025-10-10 09:00:00',0,'2025-10-06 12:43:07','2025-10-06 12:43:08',NULL,4,'2025-10-06 12:43:08',NULL),(26,3,NULL,'2025-10-17 08:00:00','2025-10-17 09:00:00',0,'2025-10-06 12:43:10','2025-10-06 12:43:12',NULL,4,'2025-10-06 12:43:12',NULL),(27,3,NULL,'2025-10-07 09:00:00','2025-10-07 10:00:00',0,'2025-10-06 12:43:13','2025-10-06 12:43:13',NULL,4,'2025-10-06 12:43:13',NULL),(28,3,NULL,'2025-10-14 09:00:00','2025-10-14 10:00:00',0,'2025-10-06 12:43:13','2025-10-06 12:43:13',NULL,4,'2025-10-06 12:43:13',NULL),(29,3,NULL,'2025-10-08 09:00:00','2025-10-08 10:00:00',0,'2025-10-06 12:43:14','2025-10-06 12:43:17',NULL,4,'2025-10-06 12:43:17',NULL),(30,3,NULL,'2025-10-15 09:00:00','2025-10-15 10:00:00',0,'2025-10-06 12:43:19','2025-10-06 12:43:22',NULL,4,'2025-10-06 12:43:22',NULL),(31,3,NULL,'2025-10-09 09:00:00','2025-10-09 10:00:00',0,'2025-10-06 12:43:26','2025-10-06 12:43:26',NULL,4,'2025-10-06 12:43:26',NULL),(32,3,NULL,'2025-10-16 09:00:00','2025-10-16 10:00:00',0,'2025-10-06 12:43:27','2025-10-06 12:43:29',NULL,4,'2025-10-06 12:43:29',NULL),(33,3,NULL,'2025-10-10 09:00:00','2025-10-10 10:00:00',0,'2025-10-06 12:43:31','2025-10-06 12:43:32',NULL,4,'2025-10-06 12:43:32',NULL),(34,3,NULL,'2025-10-17 09:00:00','2025-10-17 10:00:00',0,'2025-10-06 12:43:32','2025-10-06 12:43:32',NULL,4,'2025-10-06 12:43:32',NULL),(35,3,NULL,'2025-10-07 10:00:00','2025-10-07 13:00:00',0,'2025-10-06 12:43:32','2025-10-06 15:10:39',NULL,4,'2025-10-06 12:43:34',NULL),(36,3,NULL,'2025-10-14 10:00:00','2025-10-14 11:00:00',0,'2025-10-06 12:43:35','2025-10-06 12:43:37',NULL,4,'2025-10-06 12:43:37',NULL),(37,3,NULL,'2025-10-08 10:00:00','2025-10-08 11:00:00',0,'2025-10-06 12:43:40','2025-10-06 12:43:44',NULL,4,'2025-10-06 12:43:44',NULL),(38,3,NULL,'2025-10-15 10:00:00','2025-10-15 11:00:00',0,'2025-10-06 12:43:47','2025-10-06 12:43:49',NULL,4,'2025-10-06 12:43:49',NULL),(39,3,NULL,'2025-10-09 10:00:00','2025-10-09 11:00:00',0,'2025-10-06 12:43:50','2025-10-06 12:43:50',NULL,4,'2025-10-06 12:43:50',NULL),(40,3,NULL,'2025-10-16 10:00:00','2025-10-16 11:00:00',0,'2025-10-06 12:43:50','2025-10-06 12:43:52',NULL,4,'2025-10-06 12:43:52',NULL),(41,3,NULL,'2025-10-10 10:00:00','2025-10-10 11:00:00',0,'2025-10-06 12:43:54','2025-10-06 12:43:57',NULL,4,'2025-10-06 12:43:57',NULL),(42,3,NULL,'2025-10-17 10:00:00','2025-10-17 11:00:00',0,'2025-10-06 12:43:59','2025-10-06 12:44:00',NULL,4,'2025-10-06 12:44:00',NULL),(43,3,NULL,'2025-10-07 11:00:00','2025-10-07 12:00:00',0,'2025-10-06 12:44:02','2025-10-06 12:44:03',NULL,4,'2025-10-06 12:44:03',NULL),(44,3,NULL,'2025-10-14 11:00:00','2025-10-14 12:00:00',0,'2025-10-06 12:44:03','2025-10-06 12:44:05',NULL,4,'2025-10-06 12:44:05',NULL),(45,3,NULL,'2025-10-08 11:00:00','2025-10-08 12:00:00',0,'2025-10-06 12:44:08','2025-10-06 12:44:08',NULL,4,'2025-10-06 12:44:08',NULL),(46,3,NULL,'2025-10-15 11:00:00','2025-10-15 12:00:00',0,'2025-10-06 12:44:08','2025-10-06 12:44:09',NULL,4,'2025-10-06 12:44:09',NULL),(47,3,NULL,'2025-10-09 11:00:00','2025-10-09 12:00:00',0,'2025-10-06 12:44:10','2025-10-06 12:44:11',NULL,4,'2025-10-06 12:44:11',NULL),(48,3,NULL,'2025-10-16 11:00:00','2025-10-16 12:00:00',0,'2025-10-06 12:44:13','2025-10-06 12:44:14',NULL,4,'2025-10-06 12:44:14',NULL),(49,3,NULL,'2025-10-10 11:00:00','2025-10-10 12:00:00',0,'2025-10-06 12:44:15','2025-10-06 12:44:18',NULL,4,'2025-10-06 12:44:18',NULL),(50,3,NULL,'2025-10-17 11:00:00','2025-10-17 12:00:00',0,'2025-10-06 12:44:21','2025-10-06 12:44:22',NULL,4,'2025-10-06 12:44:22',NULL),(51,3,NULL,'2025-10-07 12:00:00','2025-10-07 13:00:00',0,'2025-10-06 12:44:22','2025-10-06 12:44:23',NULL,4,'2025-10-06 12:44:23',NULL),(52,3,NULL,'2025-10-14 12:00:00','2025-10-14 13:00:00',0,'2025-10-06 12:44:24','2025-10-06 12:44:26',NULL,4,'2025-10-06 12:44:26',NULL),(53,3,NULL,'2025-10-08 12:00:00','2025-10-08 15:00:00',0,'2025-10-06 12:44:28','2025-10-06 15:13:54',NULL,4,'2025-10-06 12:44:28',NULL),(54,3,NULL,'2025-10-15 12:00:00','2025-10-15 13:00:00',0,'2025-10-06 12:44:32','2025-10-06 12:44:34',NULL,4,'2025-10-06 12:44:34',NULL),(55,3,NULL,'2025-10-09 12:00:00','2025-10-09 13:00:00',0,'2025-10-06 12:44:34','2025-10-06 12:44:36',NULL,4,'2025-10-06 12:44:36',NULL),(56,3,NULL,'2025-10-16 12:00:00','2025-10-16 13:00:00',0,'2025-10-06 12:44:38','2025-10-06 12:44:40',NULL,4,'2025-10-06 12:44:40',NULL),(57,3,NULL,'2025-10-10 12:00:00','2025-10-10 13:00:00',0,'2025-10-06 12:44:40','2025-10-06 12:44:40',NULL,4,'2025-10-06 12:44:40',NULL),(58,3,NULL,'2025-10-17 12:00:00','2025-10-17 13:00:00',0,'2025-10-06 12:44:41','2025-10-06 12:44:45',NULL,4,'2025-10-06 12:44:45',NULL),(59,3,NULL,'2025-10-07 15:00:00','2025-10-07 16:00:00',0,'2025-10-06 12:44:48','2025-10-06 12:44:48',NULL,2,'2025-10-06 12:44:48',NULL),(60,3,NULL,'2025-10-14 15:00:00','2025-10-14 16:00:00',0,'2025-10-06 12:44:48','2025-10-06 12:44:48',NULL,2,'2025-10-06 12:44:48',NULL),(61,3,NULL,'2025-10-08 15:00:00','2025-10-08 16:00:00',0,'2025-10-06 12:44:52','2025-10-06 12:44:54',NULL,2,'2025-10-06 12:44:54',NULL),(62,3,NULL,'2025-10-15 15:00:00','2025-10-15 16:00:00',0,'2025-10-06 12:44:54','2025-10-06 12:45:01',NULL,2,'2025-10-06 12:45:01',NULL),(63,3,NULL,'2025-10-09 15:00:00','2025-10-09 16:00:00',0,'2025-10-06 12:45:01','2025-10-06 12:45:03',NULL,2,'2025-10-06 12:45:03',NULL),(64,3,NULL,'2025-10-16 15:00:00','2025-10-16 16:00:00',0,'2025-10-06 12:45:06','2025-10-06 12:45:07',NULL,2,'2025-10-06 12:45:07',NULL),(65,3,NULL,'2025-10-10 15:00:00','2025-10-10 16:00:00',0,'2025-10-06 12:45:07','2025-10-06 12:45:07',NULL,2,'2025-10-06 12:45:07',NULL),(66,3,NULL,'2025-10-17 15:00:00','2025-10-17 16:00:00',0,'2025-10-06 12:45:09','2025-10-06 12:45:11',NULL,2,'2025-10-06 12:45:11',NULL),(67,3,NULL,'2025-10-08 14:00:00','2025-10-08 15:00:00',0,'2025-10-06 12:45:13','2025-10-06 12:45:13',NULL,4,'2025-10-06 12:45:13',NULL),(68,3,NULL,'2025-10-15 14:00:00','2025-10-15 15:00:00',0,'2025-10-06 12:45:14','2025-10-06 12:45:17',NULL,4,'2025-10-06 12:45:17',NULL),(69,3,NULL,'2025-10-09 13:00:00','2025-10-09 14:00:00',0,'2025-10-06 12:45:19','2025-10-06 12:45:20',NULL,4,'2025-10-06 12:45:20',NULL),(70,3,NULL,'2025-10-16 13:00:00','2025-10-16 14:00:00',0,'2025-10-06 12:45:20','2025-10-06 12:45:20',NULL,4,'2025-10-06 12:45:20',NULL),(71,3,NULL,'2025-10-10 13:00:00','2025-10-10 14:00:00',0,'2025-10-06 12:45:22','2025-10-06 12:45:23',NULL,4,'2025-10-06 12:45:23',NULL),(72,3,NULL,'2025-10-17 13:00:00','2025-10-17 14:00:00',0,'2025-10-06 12:45:26','2025-10-06 12:45:26',NULL,4,'2025-10-06 12:45:26',NULL),(73,3,NULL,'2025-10-07 13:00:00','2025-10-07 14:00:00',0,'2025-10-06 12:45:27','2025-10-06 12:45:29',NULL,4,'2025-10-06 12:45:29',NULL),(74,3,NULL,'2025-10-14 13:00:00','2025-10-14 14:00:00',0,'2025-10-06 12:45:32','2025-10-06 12:45:36',NULL,4,'2025-10-06 12:45:36',NULL),(75,3,NULL,'2025-10-07 14:00:00','2025-10-07 15:00:00',0,'2025-10-06 12:45:38','2025-10-06 12:45:39',NULL,4,'2025-10-06 12:45:39',NULL),(76,3,NULL,'2025-10-14 14:00:00','2025-10-14 15:00:00',0,'2025-10-06 12:45:40','2025-10-06 12:45:41',NULL,4,'2025-10-06 12:45:41',NULL),(77,3,NULL,'2025-10-08 13:00:00','2025-10-08 14:00:00',0,'2025-10-06 12:45:42','2025-10-06 12:45:44',NULL,4,'2025-10-06 12:45:44',NULL),(78,3,NULL,'2025-10-15 13:00:00','2025-10-15 14:00:00',0,'2025-10-06 12:45:45','2025-10-06 12:45:45',NULL,4,'2025-10-06 12:45:45',NULL),(79,3,NULL,'2025-10-09 14:00:00','2025-10-09 15:00:00',0,'2025-10-06 12:45:45','2025-10-06 12:45:45',NULL,4,'2025-10-06 12:45:45',NULL),(80,3,NULL,'2025-10-16 14:00:00','2025-10-16 15:00:00',0,'2025-10-06 12:45:49','2025-10-06 12:45:52',NULL,4,'2025-10-06 12:45:52',NULL),(81,3,NULL,'2025-10-10 14:00:00','2025-10-10 15:00:00',0,'2025-10-06 12:45:52','2025-10-06 12:45:52',NULL,4,'2025-10-06 12:45:52',NULL),(82,3,NULL,'2025-10-17 14:00:00','2025-10-17 15:00:00',0,'2025-10-06 12:45:54','2025-10-06 12:45:56',NULL,4,'2025-10-06 12:45:56',NULL);
/*!40000 ALTER TABLE `slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipping_bays`
--

DROP TABLE IF EXISTS `tipping_bays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipping_bays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_occupied` tinyint(1) NOT NULL DEFAULT '0',
  `equipment` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `map_x` decimal(5,2) DEFAULT NULL COMMENT 'X coordinate on depot map (percentage)',
  `map_y` decimal(5,2) DEFAULT NULL COMMENT 'Y coordinate on depot map (percentage)',
  `show_on_map` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether to display this bay on the depot map',
  `map_rotation` decimal(5,2) DEFAULT '0.00' COMMENT 'Rotation angle in degrees (0-360)',
  `map_width` int DEFAULT '60' COMMENT 'Width in pixels',
  `map_height` int DEFAULT '40' COMMENT 'Height in pixels',
  `text_size` enum('xs','sm','md','lg') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'xs' COMMENT 'Text size for bay label',
  `text_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff' COMMENT 'Hex color for text (e.g., #ffffff)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipping_bays_depot_id_code_unique` (`depot_id`,`code`),
  KEY `tipping_bays_depot_id_is_active_is_occupied_index` (`depot_id`,`is_active`,`is_occupied`),
  CONSTRAINT `tipping_bays_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipping_bays`
--

LOCK TABLES `tipping_bays` WRITE;
/*!40000 ALTER TABLE `tipping_bays` DISABLE KEYS */;
INSERT INTO `tipping_bays` VALUES (1,1,'Bay 1','B1','Primary tipping bay - general use',1,1,'[\"forklift\", \"loading_dock\"]','2025-08-31 17:00:27','2025-09-02 11:46:56',44.77,9.49,1,0.00,60,40,'xs','#ffffff'),(2,1,'Bay 2','B2','Secondary tipping bay - general use',1,0,'[\"forklift\", \"loading_dock\"]','2025-08-31 17:00:28','2025-09-02 11:47:01',46.22,24.10,1,0.00,60,40,'xs','#ffffff'),(3,1,'Bay 3','B3','Express bay for priority loads',1,0,'[\"forklift\", \"loading_dock\", \"crane\"]','2025-08-31 17:00:28','2025-09-02 11:47:05',40.22,39.16,1,0.00,70,35,'xs','#ffffff'),(4,1,'Bay 4','B4','Bulk materials bay',1,0,'[\"conveyor\", \"bulk_loader\"]','2025-08-31 17:00:28','2025-09-02 11:47:09',23.06,42.02,1,0.00,80,45,'xs','#ffffff'),(5,1,'Bay 5','Bay_5',NULL,1,0,'[]','2025-09-02 10:11:23','2025-09-02 11:47:21',28.74,94.58,1,0.00,60,40,'xs','#ffffff'),(6,1,'Bay 6','Bay_6',NULL,1,0,'[]','2025-09-02 10:11:41','2025-09-02 11:47:16',37.12,94.43,1,0.00,60,40,'xs','#ffffff'),(7,1,'Bay 8','Bay_8',NULL,1,0,'[]','2025-09-02 10:12:01','2025-09-02 11:47:12',44.98,95.00,1,0.00,60,40,'xs','#ffffff'),(8,2,'Bay 1','B1',NULL,1,0,'[]','2025-09-04 08:36:34','2025-09-04 08:36:34',NULL,NULL,1,0.00,60,40,'xs','#ffffff'),(9,2,'Bay 2','B2',NULL,1,0,'[]','2025-09-04 08:36:56','2025-09-04 08:36:56',NULL,NULL,1,0.00,60,40,'xs','#ffffff'),(10,2,'BAY 3','B3',NULL,1,0,'[]','2025-09-04 08:37:09','2025-09-04 08:37:09',NULL,NULL,1,0.00,60,40,'xs','#ffffff'),(11,2,'BAY 4','B4',NULL,1,0,'[]','2025-09-04 08:37:24','2025-09-04 08:37:24',NULL,NULL,1,0.00,60,40,'xs','#ffffff'),(12,2,'BAY 5','B5',NULL,1,0,'[]','2025-09-04 08:37:45','2025-09-04 08:37:45',NULL,NULL,1,0.00,60,40,'xs','#ffffff');
/*!40000 ALTER TABLE `tipping_bays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipping_locations`
--

DROP TABLE IF EXISTS `tipping_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipping_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location_type` enum('parking') COLLATE utf8mb4_unicode_ci DEFAULT 'parking',
  `capacity` int NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `coordinates` json DEFAULT NULL,
  `map_x` decimal(8,4) DEFAULT NULL,
  `map_y` decimal(8,4) DEFAULT NULL,
  `show_on_map` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `map_width` int unsigned DEFAULT NULL COMMENT 'Width in pixels for map display',
  `map_height` int unsigned DEFAULT NULL COMMENT 'Height in pixels for map display',
  `map_rotation` decimal(5,2) DEFAULT '0.00' COMMENT 'Rotation angle in degrees',
  `text_size` enum('xs','sm','md','lg') COLLATE utf8mb4_unicode_ci DEFAULT 'xs' COMMENT 'Text size for location label',
  `text_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#ffffff' COMMENT 'Hex color code for text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipping_locations_depot_id_code_unique` (`depot_id`,`code`),
  KEY `tipping_locations_depot_id_is_active_index` (`depot_id`,`is_active`),
  CONSTRAINT `tipping_locations_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipping_locations`
--

LOCK TABLES `tipping_locations` WRITE;
/*!40000 ALTER TABLE `tipping_locations` DISABLE KEYS */;
INSERT INTO `tipping_locations` VALUES (1,1,'Warehouse A - General (Parking Area)','WH-A','Main warehouse for general cargo parking','parking',20,0,NULL,25.0000,30.0000,1,'2025-08-31 17:00:27','2025-08-31 18:19:24',120,80,0.00,'xs','#ffffff'),(2,1,'Loading Dock (Parking Area)','DOCK-1','Primary loading dock parking area','parking',15,0,NULL,65.0000,45.0000,1,'2025-08-31 17:00:27','2025-08-31 18:19:36',100,70,0.00,'xs','#ffffff'),(3,1,'Parking Area','Drop Zone 1','Area for trailer collection parking','parking',10,1,NULL,75.0000,20.0000,1,'2025-08-31 17:00:27','2025-08-31 18:20:28',90,60,0.00,'xs','#ffffff'),(4,2,'Parking Area','Park1',NULL,'parking',20,1,NULL,NULL,NULL,0,'2025-09-04 08:34:35','2025-09-04 08:34:35',NULL,NULL,0.00,'xs','#ffffff');
/*!40000 ALTER TABLE `tipping_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trailer_types`
--

DROP TABLE IF EXISTS `trailer_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trailer_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trailer_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trailer_types`
--

LOCK TABLES `trailer_types` WRITE;
/*!40000 ALTER TABLE `trailer_types` DISABLE KEYS */;
INSERT INTO `trailer_types` VALUES (1,'20ft Container','20-foot shipping container',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(2,'40ft Container','40-foot shipping container',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(3,'45ft Container','45-foot shipping container',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(4,'53ft Trailer','53-foot dry van trailer',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(5,'Box Trailer','Standard box trailer',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(6,'Curtain Side','Curtain side trailer',1,NULL,'2025-08-31 17:00:26','2025-08-31 17:00:26'),(7,'Flatbed','Flatbed trailer',1,NULL,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(8,'Articulated','Articulated lorry',1,NULL,'2025-08-31 17:00:27','2025-08-31 17:00:27'),(9,'Rigid','Rigid truck',1,NULL,'2025-08-31 17:00:27','2025-08-31 17:00:27');
/*!40000 ALTER TABLE `trailer_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trailers`
--

DROP TABLE IF EXISTS `trailers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trailers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trailer_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trailer_type` enum('container','curtain_sider','flatbed','box','tank','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'container',
  `size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity_pallets` int DEFAULT NULL,
  `capacity_weight_kg` int DEFAULT NULL,
  `temperature_controlled` tinyint(1) NOT NULL DEFAULT '0',
  `owner` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_data` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trailers_trailer_number_unique` (`trailer_number`),
  KEY `trailers_trailer_number_index` (`trailer_number`),
  KEY `trailers_trailer_type_index` (`trailer_type`),
  KEY `trailers_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trailers`
--

LOCK TABLES `trailers` WRITE;
/*!40000 ALTER TABLE `trailers` DISABLE KEYS */;
/*!40000 ALTER TABLE `trailers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_custom_roles`
--

DROP TABLE IF EXISTS `user_custom_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_custom_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `custom_role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_custom_roles_user_id_custom_role_id_unique` (`user_id`,`custom_role_id`),
  KEY `user_custom_roles_custom_role_id_foreign` (`custom_role_id`),
  CONSTRAINT `user_custom_roles_custom_role_id_foreign` FOREIGN KEY (`custom_role_id`) REFERENCES `custom_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_custom_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_custom_roles`
--

LOCK TABLES `user_custom_roles` WRITE;
/*!40000 ALTER TABLE `user_custom_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_custom_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_functions`
--

DROP TABLE IF EXISTS `user_functions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_functions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `function_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_functions_user_id_function_key_unique` (`user_id`,`function_key`),
  KEY `user_functions_function_key_index` (`function_key`),
  CONSTRAINT `user_functions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_functions`
--

LOCK TABLES `user_functions` WRITE;
/*!40000 ALTER TABLE `user_functions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_depot_id_foreign` (`depot_id`),
  KEY `users_customer_id_foreign` (`customer_id`),
  CONSTRAINT `users_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,3,'Paul Carr','paul.carr@knowleslogistics.com','2025-08-31 17:00:24',1,'$2y$12$aY8Kh9Wi3goaM9SN45wFW.gA32VYHvbhJoD0cgaZPlMV/UbI6/pRW','Otbve58050MNaYnqr9o9t1PLjNrYsPVkrWrmqbzTL179u4vSjeJb9PkEGPjJ','2025-08-31 17:00:24','2025-10-05 21:57:27',NULL,NULL),(2,NULL,'Admin User','admin@example.com',NULL,1,'$2y$12$BWQ4wXRWRvuVVXNklXYNzOAURi/ePKy622Glj/fBzgNOMUK1QWCuW',NULL,'2025-08-31 17:00:24','2025-09-03 23:22:31','2025-09-03 23:22:31',NULL),(3,NULL,'Depot Admin','depotadmin@example.com',NULL,1,'$2y$12$ehgsrMGb.r8tMG4OwLr23eYns/R/MQnAKDlEr8/2FXsS.MUm7d8Ty',NULL,'2025-08-31 17:00:25','2025-09-03 23:22:27','2025-09-03 23:22:27',NULL),(4,NULL,'Site Admin','siteadmin@example.com',NULL,1,'$2y$12$3gG8jXdo/9Lykoiwtde81Od8cNNUzPfta.303mnm5KVTXW3R6jNJW',NULL,'2025-08-31 17:00:25','2025-09-03 23:22:24','2025-09-03 23:22:24',NULL),(5,1,'Customer One','customer@example.com',NULL,1,'$2y$12$w3Prtok0LzbpSSU1E6cCtuuH1IYDKYdPJMvtMqCNr6SeE3.NpMRZG',NULL,'2025-08-31 17:00:26','2025-09-03 10:01:43','2025-09-03 10:01:43',1),(6,NULL,'Paul Carr','londo@fury.me.uk',NULL,1,'$2y$12$G.O6DBhaHAQNWW0xQiapieYACSCQ3QBouVaiKSdzLMs/bVxohnEOS',NULL,'2025-09-03 09:41:36','2025-09-03 10:01:24','2025-09-03 10:01:24',NULL),(7,1,'Paul Carr','londo320@gmail.com',NULL,1,'$2y$12$5XSbMneEnLNsTZ3/WoJHLOssOoupCRUoLRZ9r5FABMXsz9s/9sdJ2',NULL,'2025-09-03 10:33:15','2025-09-04 09:37:04',NULL,NULL),(18,NULL,'Paul Carr','londo1@fury.me.uk',NULL,1,'$2y$12$2q2SxkDX6alkG/rO6tRGqumbmhBX7LWNIVOyr8OrimrSTeKcnoLbu',NULL,'2025-09-03 18:32:57','2025-09-03 22:01:58','2025-09-03 22:01:58',NULL),(21,1,'Paul Carr','paul.carr@princes.co.uk',NULL,1,'$2y$12$WuNGOyBglDRZbxsw.ejfLOdIU/oLak14K4ge74CSaPvQ3ASiKAsM6',NULL,'2025-09-03 19:58:47','2025-09-03 22:02:11','2025-09-03 22:02:11',1),(24,NULL,'Paul Carr','sghsdgh@skdks.com',NULL,1,'$2y$12$2YbKP7fAHl81C2TjoVRCee9ha/o6OmQNoJkX88zH9F.opNIVXk4De',NULL,'2025-09-03 22:04:05','2025-09-03 23:22:13','2025-09-03 23:22:13',NULL),(28,1,'Patrick Sands','patrick.sands@knowleslogistics.com',NULL,1,'$2y$12$tf7vuk7MoylgAqS03Up4mehunr/xLw4m0.L2toObuCi5CCzDZ7F2W',NULL,'2025-09-04 09:20:04','2025-09-04 09:21:30',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` enum('tractor','rigid','van','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tractor',
  `carrier_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_data` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_registration_unique` (`registration`),
  KEY `vehicles_registration_index` (`registration`),
  KEY `vehicles_carrier_company_index` (`carrier_company`),
  KEY `vehicles_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wms_staging_orders`
--

DROP TABLE IF EXISTS `wms_staging_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wms_staging_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_system` enum('wms_1','wms_2','edi','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wms_1',
  `source_file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL,
  `load_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_load_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_depot_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address_raw` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_postcode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_delivery_date` date DEFAULT NULL,
  `delivery_time_start` time DEFAULT NULL,
  `delivery_time_end` time DEFAULT NULL,
  `pallets` int NOT NULL DEFAULT '0',
  `cases` int NOT NULL DEFAULT '0',
  `units` int NOT NULL DEFAULT '0',
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `temperature_controlled` tinyint(1) NOT NULL DEFAULT '0',
  `fragile` tinyint(1) NOT NULL DEFAULT '0',
  `hazardous` tinyint(1) NOT NULL DEFAULT '0',
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `processing_status` enum('pending','matched','failed','ignored') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `processing_notes` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `outbound_load_id` bigint unsigned DEFAULT NULL,
  `outbound_order_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wms_staging_orders_outbound_load_id_foreign` (`outbound_load_id`),
  KEY `wms_staging_orders_outbound_order_id_foreign` (`outbound_order_id`),
  KEY `wms_staging_orders_load_reference_index` (`load_reference`),
  KEY `wms_staging_orders_order_reference_index` (`order_reference`),
  KEY `wms_staging_orders_processing_status_index` (`processing_status`),
  KEY `wms_staging_orders_uploaded_at_index` (`uploaded_at`),
  KEY `wms_staging_orders_source_system_uploaded_at_index` (`source_system`,`uploaded_at`),
  CONSTRAINT `wms_staging_orders_outbound_load_id_foreign` FOREIGN KEY (`outbound_load_id`) REFERENCES `outbound_loads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wms_staging_orders_outbound_order_id_foreign` FOREIGN KEY (`outbound_order_id`) REFERENCES `outbound_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wms_staging_orders`
--

LOCK TABLES `wms_staging_orders` WRITE;
/*!40000 ALTER TABLE `wms_staging_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `wms_staging_orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-06 16:37:26
