/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE `chambers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sys_service_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gps_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_temperature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16971 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `latest_telemetry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sys_service_id` bigint unsigned NOT NULL,
  `sys_msg_type` int unsigned DEFAULT '1',
  `sys_proc_time` datetime NOT NULL,
  `sys_proc_host` varchar(45) NOT NULL,
  `sys_asset_id` varchar(45) DEFAULT NULL,
  `sys_geofence_id` int unsigned DEFAULT NULL,
  `sys_device_id` bigint unsigned NOT NULL,
  `gps_time` datetime NOT NULL,
  `gps_latitude` float(12,9) NOT NULL DEFAULT '0.000000000',
  `gps_longitude` float(12,9) NOT NULL DEFAULT '0.000000000',
  `gps_orientation` float(12,9) NOT NULL DEFAULT '0.000000000',
  `gps_speed` float(12,9) NOT NULL DEFAULT '0.000000000',
  `gps_fix` int unsigned NOT NULL DEFAULT '0',
  `geo_street` text,
  `geo_town` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `geo_country` varchar(100) DEFAULT NULL,
  `geo_postcode` varchar(100) DEFAULT NULL,
  `jny_distance` varchar(100) DEFAULT NULL,
  `jny_duration` int unsigned DEFAULT NULL,
  `jny_idle_time` int unsigned DEFAULT NULL,
  `jny_status` varchar(10) DEFAULT '0',
  `jny_leg_code` int DEFAULT NULL,
  `jny_device_jny_id` int DEFAULT NULL,
  `des_movement_id` int DEFAULT NULL,
  `des_vehicle_id` int DEFAULT NULL,
  `tel_state` int DEFAULT NULL,
  `tel_ignition` bit(1) DEFAULT NULL,
  `tel_alarm` bit(1) DEFAULT NULL,
  `tel_panic` bit(1) DEFAULT NULL,
  `tel_shield` bit(1) DEFAULT NULL,
  `tel_theft_attempt` bit(1) DEFAULT NULL,
  `tel_tamper` bit(1) DEFAULT NULL,
  `tel_ext_alarm` bit(1) DEFAULT NULL,
  `tel_journey` bit(1) DEFAULT NULL,
  `tel_journey_status` bit(1) DEFAULT NULL,
  `tel_idle` bit(1) DEFAULT NULL,
  `tel_ex_idle` bit(1) DEFAULT NULL,
  `tel_hours` int unsigned DEFAULT NULL,
  `tel_input_0` bit(1) DEFAULT NULL,
  `tel_input_1` bit(1) DEFAULT NULL,
  `tel_input_2` bit(1) DEFAULT NULL,
  `tel_input_3` bit(1) DEFAULT NULL,
  `tel_temperature` float(12,9) DEFAULT NULL,
  `tel_voltage` float(12,9) DEFAULT NULL,
  `main_powervoltage` float(12,9) DEFAULT NULL,
  `tel_odometer` bigint unsigned DEFAULT NULL,
  `tel_poweralert` bit(1) DEFAULT NULL,
  `tel_speedalert` bit(1) DEFAULT NULL,
  `tel_boxalert` bit(1) DEFAULT NULL,
  `tel_fuel` float(12,9) DEFAULT NULL,
  `tel_rfid` varchar(50) DEFAULT NULL,
  `tel_rawlog` text,
  PRIMARY KEY (`sys_service_id`),
  UNIQUE KEY `id` (`id`),
  KEY `vehid` (`sys_service_id`),
  KEY `sys_service_id_gps_time` (`sys_service_id`,`gps_time`),
  KEY `geo_country` (`geo_country`)
) ENGINE=InnoDB AUTO_INCREMENT=187356 DEFAULT CHARSET=latin1;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_service_unique_ids` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=313 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `telemetry_new_chamber` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sys_service_id` int unsigned NOT NULL,
  `sys_msg_type` int unsigned NOT NULL DEFAULT '1',
  `sys_proc_time` datetime NOT NULL,
  `sys_proc_host` varchar(45) NOT NULL,
  `sys_asset_id` varchar(45) DEFAULT NULL,
  `sys_geofence_id` int unsigned DEFAULT NULL,
  `sys_device_id` bigint unsigned NOT NULL DEFAULT '0',
  `gps_date` date NOT NULL,
  `gps_time` time NOT NULL,
  `gps_latitude` float(12,9) NOT NULL,
  `gps_longitude` float(12,9) NOT NULL,
  `gps_orientation` float(12,9) NOT NULL,
  `gps_speed` float(12,9) NOT NULL,
  `gps_fix` int unsigned NOT NULL DEFAULT '6',
  `geo_street` varchar(500) DEFAULT NULL,
  `geo_town` varchar(100) DEFAULT NULL,
  `geo_country` varchar(100) DEFAULT NULL,
  `geo_postcode` varchar(100) DEFAULT NULL,
  `jny_distance` float(12,2) DEFAULT NULL,
  `jny_duration` int unsigned DEFAULT NULL,
  `jny_idle_time` int unsigned DEFAULT NULL,
  `jny_status` varchar(10) NOT NULL DEFAULT '0',
  `jny_leg_code` int DEFAULT NULL,
  `jny_device_jny_id` int unsigned DEFAULT NULL,
  `des_movement_id` int DEFAULT NULL,
  `des_vehicle_id` int unsigned DEFAULT NULL,
  `tel_state` int NOT NULL DEFAULT '0',
  `tel_ignition` bit(1) DEFAULT NULL,
  `tel_alarm` bit(1) DEFAULT NULL,
  `tel_panic` bit(1) DEFAULT NULL,
  `tel_shield` bit(1) DEFAULT NULL,
  `tel_theft_attempt` bit(1) DEFAULT NULL,
  `tel_tamper` bit(1) DEFAULT NULL,
  `tel_ext_alarm` bit(1) DEFAULT NULL,
  `tel_journey` bit(1) DEFAULT NULL,
  `tel_journey_status` bit(1) DEFAULT NULL,
  `tel_idle` bit(1) DEFAULT NULL,
  `tel_ex_idle` bit(1) DEFAULT NULL,
  `tel_hours` int unsigned DEFAULT NULL,
  `tel_input_0` bit(1) DEFAULT NULL,
  `tel_input_1` bit(1) DEFAULT NULL,
  `tel_input_2` bit(1) DEFAULT NULL,
  `tel_input_3` bit(1) DEFAULT NULL,
  `tel_temperature` float(12,9) DEFAULT NULL,
  `tel_voltage` float(12,9) DEFAULT NULL,
  `main_powervoltage` float(12,9) DEFAULT NULL,
  `tel_odometer` bigint unsigned DEFAULT NULL,
  `tel_poweralert` bit(1) DEFAULT NULL,
  `tel_speedalert` bit(1) DEFAULT NULL,
  `tel_boxalert` bit(1) DEFAULT NULL,
  `tel_fuel` float(12,9) DEFAULT '0.000000000',
  `tel_rfid` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`,`sys_service_id`,`gps_date`),
  UNIQUE KEY `id` (`id`),
  KEY `sys_service_id_3` (`sys_service_id`,`gps_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `temperature_summary` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sys_service_id` bigint unsigned NOT NULL,
  `min_temp` double(12,9) DEFAULT NULL,
  `max_temp` double(12,9) DEFAULT NULL,
  `avg_temp` double(12,9) DEFAULT NULL,
  `temp_date` date DEFAULT NULL,
  `updatetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_service_id_temp_date` (`sys_service_id`,`temp_date`)
) ENGINE=InnoDB AUTO_INCREMENT=7126451 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;