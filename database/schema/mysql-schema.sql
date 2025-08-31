/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_reference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  UNIQUE KEY `bookings_slot_id_user_id_unique` (`slot_id`,`user_id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
  `current_status` enum('scheduled','en_route','arrived','in_waiting','in_location','at_bay','unloading','empty','loading','loaded','ready_to_depart','departed','trailer_dropped','trailer_collected') COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_case_count` int DEFAULT NULL,
  `default_pallets` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `slot_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `day_of_week` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_minutes` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slot_templates_depot_id_foreign` (`depot_id`),
  CONSTRAINT `slot_templates_depot_id_foreign` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipping_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipping_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `depot_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location_type` enum('drop_zone','collection_zone','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'drop_zone',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2025_01_01_000001_create_depots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2025_01_01_000002_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2025_01_01_000003_create_customers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_01_01_000004_create_booking_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_01_02_000001_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_01_02_000002_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_01_02_000003_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_01_03_000001_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_01_04_000001_create_slots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_01_04_000002_create_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_01_05_000001_create_carriers_system',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_01_06_000001_create_depot_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_01_06_000002_create_customer_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_01_07_000001_add_deleted_at_to_depots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_01_07_000002_add_deleted_at_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_01_07_000003_add_deleted_at_to_customers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_01_07_000004_add_deleted_at_to_booking_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_01_07_000005_add_deleted_at_to_slots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_01_07_000006_add_deleted_at_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_01_08_000001_add_customer_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_01_09_000001_create_products_and_booking_product_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_01_09_000002_create_depot_product_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_01_09_000003_create_customer_depot_product_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_01_09_000004_create_depot_case_ranges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_01_10_000001_add_case_and_size_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_01_10_000002_add_details_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_01_10_000003_add_arrival_departure_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_01_10_000004_add_customer_id_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_01_10_000005_add_expected_actual_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_01_10_000006_edit_expected_actual_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_01_10_000007_add_status_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_01_10_000008_add_end_time_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_01_11_000001_create_slot_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_01_11_000002_add_capacity_to_slots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_01_11_000003_add_duration_minutes_to_booking_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_01_11_000004_create_booking_type_depot_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_01_11_000005_create_slot_generation_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_01_11_000006_add_cut_off_time_to_depots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_01_12_000001_create_slot_release_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_01_12_000002_create_slot_release_rule_customer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_01_12_000003_add_release_and_cutoff_to_slots',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_01_12_000004_create_slot_customer_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_01_13_000001_add_transportation_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_01_13_000002_rename_trailer_number_to_container_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_01_13_000003_update_container_number_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_01_14_000001_create_trailer_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_01_15_000001_create_booking_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_01_15_000002_add_rebooking_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_01_15_000003_create_customer_behavior_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_01_16_000001_create_arrival_time_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_01_17_000001_create_tipping_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_01_17_000002_create_tipping_bays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_01_17_000003_add_tipping_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_01_17_000004_add_tipping_workflow_enabled_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_01_17_000005_add_waiting_area_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_01_17_000006_add_trailer_collection_fields_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_01_18_000001_create_booking_po_numbers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_01_18_000002_create_pallet_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_01_18_000003_create_po_lines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_01_18_000004_create_po_line_actual_pallets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_01_19_000001_create_vehicles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_01_19_000002_create_trailers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_01_19_000003_create_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_01_19_000004_create_consignments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_01_19_000005_create_consignment_references_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_01_19_000006_create_consignment_loads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_01_19_000007_create_movement_loads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_01_20_000001_add_vehicle_details_json_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_01_21_000001_remove_driver_fields_from_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_01_21_000002_make_container_size_nullable_in_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_01_21_000003_remove_quantity_columns_from_booking_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_01_21_000004_remove_reference_and_gate_fields_from_bookings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_01_21_000005_remove_vehicle_fields_from_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_01_22_000001_add_foreign_key_constraints',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_01_23_000001_add_trailer_type_id_to_bookings_manual',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_08_14_165255_add_vehicle_details_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_08_15_073303_add_missing_tipping_bay_id_to_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2025_08_16_191613_update_booking_history_action_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2025_08_16_200000_add_timing_fields_to_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2025_08_16_220000_add_in_location_status_to_movements',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2025_08_16_230000_add_unit_departure_and_collection_times',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2025_08_16_230500_add_trailer_collected_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2025_08_17_161316_create_factory_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2025_08_17_162048_add_factory_booking_support_to_movements_and_po_numbers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2025_08_18_124536_add_priority_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2025_08_18_124546_add_collection_scheduling_to_bookings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2025_08_18_131017_add_tipping_type_to_bookings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2025_08_18_134007_simplify_tipping_type_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2025_08_19_063310_add_location_type_to_tipping_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2025_08_20_122945_add_coordinates_to_tipping_bays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2025_08_20_123024_add_map_file_to_depots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2025_08_20_194548_add_visual_properties_to_tipping_bays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2025_08_20_194645_add_text_color_to_tipping_bays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2025_08_20_200937_add_visual_properties_to_tipping_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2025_08_21_064815_add_tipping_type_to_factory_bookings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2025_08_21_065827_create_user_functions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2025_08_21_082115_add_customer_id_to_users_table_fix',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2025_08_21_110859_create_custom_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2025_08_21_110918_create_user_custom_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2025_08_22_073614_add_factory_delivery_to_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2025_08_23_152130_add_is_active_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2025_08_24_000001_create_outbound_loads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2025_08_24_000002_create_customer_addresses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2025_08_24_000003_create_outbound_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2025_08_24_000004_create_load_collections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2025_08_24_000005_create_wms_staging_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2025_08_24_000006_create_import_configuration_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2025_08_26_115902_add_map_fields_to_tipping_locations_table',1);
