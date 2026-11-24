-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mar. 24 nov. 2026 à 09:34
-- Version du serveur : 11.4.9-MariaDB
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hotel_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity_bookings`
--

DROP TABLE IF EXISTS `activity_bookings`;
CREATE TABLE IF NOT EXISTS `activity_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hotel_activity_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_bookings_hotel_activity_id_foreign` (`hotel_activity_id`),
  KEY `activity_bookings_room_id_foreign` (`room_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `billings`
--

DROP TABLE IF EXISTS `billings`;
CREATE TABLE IF NOT EXISTS `billings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(191) NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_ticket_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_type` varchar(191) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billings_receipt_number_unique` (`receipt_number`),
  KEY `billings_order_id_foreign` (`order_id`),
  KEY `billings_event_ticket_id_foreign` (`event_ticket_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billings`
--

INSERT INTO `billings` (`id`, `receipt_number`, `room_id`, `order_id`, `event_ticket_id`, `service_type`, `amount_paid`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 'REC-20261123-0001', 1, NULL, NULL, 'room_booking', 11000.00, 'wave', 'completed', '2026-11-23 16:15:30', '2026-11-23 16:29:38'),
(2, 'REC-20261123-0002', 4, NULL, NULL, 'room_booking', 125000.00, 'orange_money', 'completed', '2026-11-23 16:32:02', '2026-11-23 16:32:02'),
(3, 'REC-20261123-0003', 10, NULL, NULL, 'room_booking', 200000.00, 'cash', 'completed', '2026-11-23 16:43:42', '2026-11-23 16:43:42'),
(4, 'REC-20261123-0004', 10, NULL, NULL, 'car_wash', 2500.00, 'cash', 'completed', '2026-11-23 16:45:49', '2026-11-23 16:45:49');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:18:{i:0;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:17:\"configurer_salles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"voir_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:2;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"gerer_checkin\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:3;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:8:\"voir_iot\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:4;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"voir_chambres\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:5;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"gerer_menage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:18:\"gerer_restauration\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"gerer_services\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:13:\"gerer_encours\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:9;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:16:\"voir_grand_livre\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"voir_rapport_z\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:11;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:17:\"gerer_billetterie\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:9:\"voir_rack\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"gerer_activites\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"gerer_depenses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:15;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:11:\"gerer_stock\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:9:\"gerer_crm\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:17;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:8:\"gerer_rh\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"receptionniste\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"restauration\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"gouvernante\";s:1:\"c\";s:3:\"web\";}}}', 1795529670);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `negotiated_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `full_name` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) NOT NULL DEFAULT 'Ivoirienne',
  `vip_level` enum('standard','regular','vip') NOT NULL DEFAULT 'standard',
  `preferences` text DEFAULT NULL,
  `id_type` varchar(255) NOT NULL DEFAULT 'CNI',
  `id_card_number` varchar(191) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_company_id_foreign` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`id`, `company_id`, `full_name`, `phone`, `email`, `nationality`, `vip_level`, `preferences`, `id_type`, `id_card_number`, `address`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-04 10:48:04', '2026-11-04 10:48:04'),
(2, NULL, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-04 11:26:29', '2026-11-04 11:26:29'),
(3, NULL, 'KONE NAFATA', '0102034568', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-04 14:08:50', '2026-11-04 14:08:50'),
(4, NULL, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-04 14:52:48', '2026-11-04 14:52:48'),
(5, NULL, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-04 15:05:01', '2026-11-04 15:05:01'),
(6, NULL, 'KONE NAFATA', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 11:27:57', '2026-11-11 11:27:57'),
(7, NULL, 'Yoro Sylvanus', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI21245', NULL, NULL, '2026-11-11 11:44:29', '2026-11-11 11:44:29'),
(8, NULL, 'Kouadio Seraphin', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-11 12:00:54', '2026-11-11 12:00:54'),
(9, NULL, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 12:50:38', '2026-11-11 12:50:38'),
(10, NULL, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:11:52', '2026-11-11 13:11:52'),
(11, NULL, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 13:31:24', '2026-11-11 13:31:24'),
(12, NULL, 'KONE NAFATA', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:33:21', '2026-11-11 13:33:21'),
(13, NULL, 'Kouadio Seraphin', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 13:34:33', '2026-11-11 13:34:33'),
(14, NULL, 'COULIBALY YOUSSEF', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 13:40:01', '2026-11-11 13:40:01'),
(15, NULL, 'KONE YAYA', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:41:05', '2026-11-11 13:41:05'),
(16, NULL, 'Koffi Samption', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 13:57:48', '2026-11-11 13:57:48'),
(17, NULL, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 14:22:09', '2026-11-11 14:22:09'),
(18, NULL, 'KONE NAFATA', '0102034568', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-11 14:22:59', '2026-11-11 14:22:59'),
(19, NULL, 'Kouadio Seraphin', '0102034568', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI21245', NULL, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(20, NULL, 'Amara Kanté', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-12 14:32:48', '2026-11-12 14:32:48'),
(21, NULL, 'Abade Luc Romuald', '0725435689', NULL, 'Ivoirienne', 'standard', NULL, 'Passeport', 'CI001248', NULL, NULL, '2026-11-13 10:40:18', '2026-11-13 10:40:18'),
(22, NULL, 'Kouamé Jean-Marc', '07 89 45 12 36', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CI-00984521', NULL, NULL, '2026-11-17 08:27:28', '2026-11-17 08:27:28'),
(23, NULL, 'Ablasse Ouédraogo', '0407050602', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CI0012345', NULL, NULL, '2026-11-23 16:15:30', '2026-11-23 16:15:30'),
(24, NULL, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'cni21022', NULL, NULL, '2026-11-23 16:32:02', '2026-11-23 16:32:02'),
(25, NULL, 'Kouadio Seraphin', '0143677424', NULL, 'Ivoirienne', 'standard', NULL, 'CNI', 'CNI 123333', NULL, NULL, '2026-11-23 16:43:42', '2026-11-23 16:43:42');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `show_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `performance_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_show_id_foreign` (`show_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `show_id`, `title`, `description`, `performance_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Grand Concert Concert d\'Ouverture', NULL, '2026-10-31 20:00:00', '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 1, 'Concert gadji celi', 'cfvg', '2026-11-05 20:30:00', '2026-11-02 11:35:44', '2026-11-02 14:06:49'),
(3, 1, 'concert Ismael Isaac', 'cvbbg', '2026-11-28 16:40:00', '2026-11-02 11:40:54', '2026-11-09 15:04:00');

-- --------------------------------------------------------

--
-- Structure de la table `event_tickets`
--

DROP TABLE IF EXISTS `event_tickets`;
CREATE TABLE IF NOT EXISTS `event_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_type` varchar(191) NOT NULL,
  `total_capacity` int(11) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_tickets_event_id_foreign` (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_tickets`
--

INSERT INTO `event_tickets` (`id`, `event_id`, `ticket_type`, `total_capacity`, `available_seats`, `price`, `created_at`, `updated_at`) VALUES
(10, 3, 'populaire', 3000, 3000, 10000.00, '2026-11-09 15:04:00', '2026-11-09 15:04:00'),
(9, 3, 'honneurs', 50, 50, 100000.00, '2026-11-09 15:04:00', '2026-11-12 09:40:13'),
(8, 3, 'vip', 150, 150, 50000.00, '2026-11-09 15:04:00', '2026-11-09 15:22:25');

-- --------------------------------------------------------

--
-- Structure de la table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_number` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Espèces',
  `beneficiary` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_voucher_number_unique` (`voucher_number`) USING HASH,
  KEY `expenses_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `extra_services`
--

DROP TABLE IF EXISTS `extra_services`;
CREATE TABLE IF NOT EXISTS `extra_services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` enum('pressing','car_wash') NOT NULL,
  `service_name` varchar(191) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `work_status` enum('pending','ready') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extra_services_room_id_foreign` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `extra_services`
--

INSERT INTO `extra_services` (`id`, `category`, `service_name`, `quantity`, `unit_price`, `total_amount`, `room_id`, `customer_name`, `work_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 'car_wash', 'SUV', 1, 2500.00, 2500.00, 10, NULL, 'pending', 'paid', '2026-11-23 16:45:29', '2026-11-23 16:45:49'),
(2, 'car_wash', 'SUV', 2, 2500.00, 5000.00, 4, NULL, 'pending', 'unpaid', '2026-11-23 16:46:39', '2026-11-23 16:46:39');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_failed_at_index` (`failed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hotel_activities`
--

DROP TABLE IF EXISTS `hotel_activities`;
CREATE TABLE IF NOT EXISTS `hotel_activities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'forfait',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hotel_activities`
--

INSERT INTO `hotel_activities` (`id`, `category`, `name`, `price`, `unit`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'piscine', 'Pass Piscine Journée + Transat', 10000.00, 'personne', 1, '2026-11-16 09:55:11', '2026-11-16 09:55:11'),
(2, 'spa', 'Massage Relaxant (1h)', 25000.00, 'séance', 1, '2026-11-16 09:55:11', '2026-11-16 09:55:11'),
(3, 'coworking', 'Espace Coworking + Wi-Fi + Café', 5000.00, 'journée', 1, '2026-11-16 09:55:11', '2026-11-16 09:55:11'),
(4, 'seminaire', 'Location Salle de Séminaire Équipée', 150000.00, 'journée', 1, '2026-11-16 09:55:11', '2026-11-16 09:55:11'),
(5, 'navette', 'Navette Aéroport Félix Houphouët-Boigny', 15000.00, 'trajet', 1, '2026-11-16 09:55:11', '2026-11-16 09:55:11');

-- --------------------------------------------------------

--
-- Structure de la table `housekeepers`
--

DROP TABLE IF EXISTS `housekeepers`;
CREATE TABLE IF NOT EXISTS `housekeepers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `housekeepers`
--

INSERT INTO `housekeepers` (`id`, `full_name`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Koné Mariam', '047581233', 1, '2026-11-13 14:32:25', '2026-11-13 14:32:25'),
(2, 'Assetou Sawadogo', '0124536966', 1, '2026-11-13 14:32:58', '2026-11-13 14:32:58'),
(3, 'Agnero gertrude', '012345678', 1, '2026-11-13 14:33:16', '2026-11-13 14:33:16'),
(4, 'Kouassi Aya Brigitte', '07 08 11 22 33', 1, '2026-11-13 15:16:58', '2026-11-13 15:16:58'),
(5, 'Traoré Aminata', '05 44 55 66 77', 1, '2026-11-13 15:16:58', '2026-11-13 15:16:58'),
(6, 'Yao Adjoua Clarisse', '01 02 03 04 05', 1, '2026-11-13 15:16:58', '2026-11-13 15:16:58'),
(7, 'Diabaté Fatoumata', '07 58 96 32 14', 1, '2026-11-13 15:16:58', '2026-11-13 15:16:58'),
(8, 'Bamba Souleymane', '05 88 77 66 55', 1, '2026-11-13 15:16:58', '2026-11-13 15:16:58');

-- --------------------------------------------------------

--
-- Structure de la table `housekeeping_logs`
--

DROP TABLE IF EXISTS `housekeeping_logs`;
CREATE TABLE IF NOT EXISTS `housekeeping_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `housekeeper_name` varchar(255) DEFAULT NULL,
  `action_type` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `housekeeping_logs_room_id_foreign` (`room_id`),
  KEY `housekeeping_logs_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `housekeeping_schedules`
--

DROP TABLE IF EXISTS `housekeeping_schedules`;
CREATE TABLE IF NOT EXISTS `housekeeping_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `work_date` date NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `housekeeper_id` bigint(20) UNSIGNED NOT NULL,
  `task_type` varchar(255) NOT NULL DEFAULT 'daily',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `housekeeping_schedules_work_date_room_id_unique` (`work_date`,`room_id`),
  KEY `housekeeping_schedules_room_id_foreign` (`room_id`),
  KEY `housekeeping_schedules_housekeeper_id_foreign` (`housekeeper_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `magnetic_cards`
--

DROP TABLE IF EXISTS `magnetic_cards`;
CREATE TABLE IF NOT EXISTS `magnetic_cards` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `card_uid` varchar(50) NOT NULL,
  `security_token` varchar(100) NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `duration_days` int(11) NOT NULL DEFAULT 1,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `magnetic_cards_card_uid_unique` (`card_uid`),
  KEY `magnetic_cards_room_id_foreign` (`room_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `magnetic_cards`
--

INSERT INTO `magnetic_cards` (`id`, `card_uid`, `security_token`, `room_id`, `duration_days`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'RFID_P9GPBY', 'XTUVEVeTGrSeJCb73iKdr2Vm85elmLBu', 4, 3, '2026-11-26 12:00:00', 1, '2026-11-23 16:32:02', '2026-11-23 16:32:02'),
(2, 'RFID_WCV75S', 'SwF3cipDk3tvTxhCiECeonr1vSTr7gP0', 10, 5, '2026-11-28 12:00:00', 1, '2026-11-23 16:43:42', '2026-11-23 16:43:42');

-- --------------------------------------------------------

--
-- Structure de la table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `location_type` varchar(191) NOT NULL DEFAULT 'restaurant',
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `location_type`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Garba de Luxe au Thon', 'Attiéké local avec darne de thon frit, piments et oignons frais.', 3500.00, 'restaurant', 1, '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 'Cocktail l\'Abidjanaise', 'Mélange rafraîchissant à base de fruits exotiques et de liqueur locale.', 6000.00, 'bar_piscine', 1, '2026-10-31 09:07:47', '2026-11-13 09:17:01'),
(3, 'Poulet Braisé', 'Attiéké ou du Riz', 7500.00, 'restaurant', 1, '2026-11-13 09:00:47', '2026-11-13 09:00:47'),
(4, 'Vin Valpierre', NULL, 5000.00, 'restaurant', 1, '2026-11-13 09:01:19', '2026-11-13 09:01:19'),
(5, 'Poisson Braisé', 'Attiéké', 5000.00, 'restaurant', 1, '2026-11-13 09:21:13', '2026-11-13 09:21:13'),
(6, 'Coca Cola 33cl', NULL, 1500.00, 'restaurant', 1, '2026-11-13 09:23:17', '2026-11-13 09:23:17');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_29_094312_create_rooms_table', 1),
(5, '2026_10_29_094313_create_magnetic_cards_table', 1),
(6, '2026_10_29_094314_create_room_access_logs_table', 1),
(7, '2026_10_29_101725_create_personal_access_tokens_table', 1),
(8, '2026_10_29_102632_create_menu_items_table', 1),
(9, '2026_10_29_102633_create_restaurant_tables_table', 1),
(10, '2026_10_29_102634_create_orders_table', 1),
(11, '2026_10_29_102635_create_order_items_table', 1),
(12, '2026_10_29_103025_create_shows_table', 1),
(13, '2026_10_29_103026_create_show_bookings_table', 1),
(14, '2026_10_30_113332_create_events_table', 1),
(15, '2026_10_30_114045_alter_shows_table_to_venues', 1),
(16, '2026_10_30_124424_add_ticket_details_to_show_bookings_table', 1),
(17, '2026_11_02_103309_alter_events_and_create_event_tickets_table', 2),
(18, '2026_11_03_120322_create_billings_table', 3),
(19, '2026_11_04_103210_create_customers_table', 4),
(20, '2026_11_04_142545_add_duration_days_to_magnetic_cards_table', 5),
(21, '2026_11_09_094800_create_permission_tables', 6),
(22, '2026_11_09_142840_add_event_id_to_show_bookings_table', 7),
(23, '2026_11_09_143246_make_user_id_nullable_in_show_bookings_table', 8),
(24, '2026_11_12_115657_create_service_catalogs_table', 9),
(25, '2026_11_12_115658_create_extra_services_table', 9),
(26, '2026_11_13_102611_add_police_fields_to_customers_table', 10),
(27, '2026_11_13_102612_create_reservations_table', 10),
(28, '2026_11_13_141936_create_housekeeping_logs_table', 11),
(29, '2026_11_13_142436_create_housekeeping_planning_tables', 12),
(30, '2026_11_16_095138_create_hotel_activities_tables', 13),
(31, '2026_11_16_101739_create_expenses_table', 14),
(32, '2026_11_17_120614_create_stock_management_tables', 15),
(33, '2026_11_17_122336_create_room_requests_table', 16),
(34, '2026_11_17_124315_add_crm_and_companies_tables', 17),
(35, '2026_11_17_125718_create_hr_management_tables', 18);

-- --------------------------------------------------------

--
-- Structure de la table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_restaurant_table_id_foreign` (`restaurant_table_id`),
  KEY `orders_room_id_foreign` (`room_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `restaurant_table_id`, `room_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 24500.00, 'charged_to_room', '2026-11-23 16:44:40', '2026-11-23 16:44:40');

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_order` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_menu_item_id_foreign` (`menu_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price_at_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 3500.00, '2026-11-23 16:44:40', '2026-11-23 16:44:40'),
(2, 1, 2, 1, 6000.00, '2026-11-23 16:44:40', '2026-11-23 16:44:40'),
(3, 1, 4, 2, 5000.00, '2026-11-23 16:44:40', '2026-11-23 16:44:40'),
(4, 1, 5, 1, 5000.00, '2026-11-23 16:44:40', '2026-11-23 16:44:40');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(5, 'configurer_salles', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(7, 'voir_dashboard', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(8, 'gerer_checkin', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(9, 'voir_iot', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(10, 'voir_chambres', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(11, 'gerer_menage', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(12, 'gerer_restauration', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(13, 'gerer_services', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(14, 'gerer_encours', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(15, 'voir_grand_livre', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(16, 'voir_rapport_z', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(17, 'gerer_billetterie', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06'),
(18, 'voir_rack', 'web', '2026-11-17 09:03:46', '2026-11-17 09:03:46'),
(19, 'gerer_activites', 'web', '2026-11-17 09:03:46', '2026-11-17 09:03:46'),
(20, 'gerer_depenses', 'web', '2026-11-17 09:03:46', '2026-11-17 09:03:46'),
(21, 'gerer_stock', 'web', '2026-11-17 12:07:40', '2026-11-17 12:07:40'),
(22, 'gerer_crm', 'web', '2026-11-17 12:45:48', '2026-11-17 12:45:48'),
(23, 'gerer_rh', 'web', '2026-11-17 12:58:19', '2026-11-17 12:58:19');

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `nights` int(11) NOT NULL DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `deposit_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('confirmed','checked_in','checked_out','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  `provenance` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_customer_id_foreign` (`customer_id`),
  KEY `reservations_room_id_foreign` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `customer_id`, `room_id`, `check_in_date`, `check_out_date`, `nights`, `total_amount`, `deposit_paid`, `status`, `provenance`, `destination`, `special_requests`, `created_at`, `updated_at`) VALUES
(1, 23, 1, '2026-11-24', '2026-11-26', 2, 90000.00, 11000.00, 'confirmed', NULL, 'Abidjan', NULL, '2026-11-23 16:15:30', '2026-11-23 16:29:38');

-- --------------------------------------------------------

--
-- Structure de la table `restaurant_tables`
--

DROP TABLE IF EXISTS `restaurant_tables`;
CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` varchar(20) NOT NULL,
  `location_type` varchar(191) NOT NULL DEFAULT 'restaurant',
  `status` varchar(191) NOT NULL DEFAULT 'free',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `number`, `location_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Table 5', 'restaurant', 'free', '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 'Transat B2', 'bar_piscine', 'occupied', '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(3, 'Table 6', 'restaurant', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(4, 'Table 7', 'restaurant', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(5, 'Table 8', 'restaurant', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(6, 'Table 9', 'terrasse', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(7, 'Table 10', 'terrasse', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(8, 'Table VIP', 'restaurant', 'free', '2026-11-16 09:18:35', '2026-11-16 09:18:35'),
(9, 'Table 1', 'restaurant', 'free', '2026-11-16 09:19:58', '2026-11-16 09:19:58'),
(10, 'Table 2', 'restaurant', 'free', '2026-11-16 09:19:58', '2026-11-16 09:19:58'),
(11, 'Table 3', 'restaurant', 'free', '2026-11-16 09:19:58', '2026-11-16 09:19:58'),
(12, 'Table 4', 'restaurant', 'free', '2026-11-16 09:19:58', '2026-11-16 09:19:58'),
(13, 'Salon VIP', 'restaurant', 'free', '2026-11-16 09:19:58', '2026-11-16 09:19:58'),
(14, 'Transat B1', 'bar_piscine', 'free', '2026-11-16 09:21:18', '2026-11-16 09:21:18'),
(15, 'Transat B3', 'bar_piscine', 'free', '2026-11-16 09:21:18', '2026-11-16 09:21:18'),
(16, 'Transat B4', 'bar_piscine', 'free', '2026-11-16 09:21:18', '2026-11-16 09:21:18'),
(17, 'Transat B5', 'bar_piscine', 'free', '2026-11-16 09:21:18', '2026-11-16 09:21:18');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(2, 'receptionniste', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(3, 'restauration', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(4, 'gouvernante', 'web', '2026-11-17 09:00:24', '2026-11-17 09:00:24');

-- --------------------------------------------------------

--
-- Structure de la table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(5, 1),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(8, 1),
(8, 2),
(8, 4),
(9, 1),
(9, 2),
(9, 4),
(10, 1),
(10, 2),
(10, 4),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(12, 3),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(14, 3),
(15, 1),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(20, 3),
(21, 1),
(22, 1),
(22, 2),
(23, 1);

-- --------------------------------------------------------

--
-- Structure de la table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` varchar(50) NOT NULL,
  `type` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'available',
  `price_per_night` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_number_unique` (`number`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rooms`
--

INSERT INTO `rooms` (`id`, `number`, `type`, `status`, `price_per_night`, `created_at`, `updated_at`) VALUES
(1, 'Chambre 101', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-19 08:40:44'),
(2, 'Chambre 102', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-19 08:40:47'),
(3, 'Chambre 103', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-23 16:08:55'),
(4, 'Chambre 104', 'Luxe', 'occupied', 75000.00, '2026-10-31 09:07:47', '2026-11-23 16:32:02'),
(5, 'Chambre 105', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-19 08:40:49'),
(6, 'Chambre 106', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-19 08:40:51'),
(7, 'Chambre 107', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(8, 'Chambre 108', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-12 16:14:41'),
(9, 'Chambre 109', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(10, 'Chambre 110', 'Luxe', 'occupied', 75000.00, '2026-10-31 09:07:47', '2026-11-23 16:43:42'),
(11, 'Chambre 111', 'Suite Junior', 'available', 25000.00, '2026-11-13 08:56:51', '2026-11-23 16:08:58'),
(12, 'Chambre 112', 'Standard', 'available', 45000.00, '2026-11-13 08:57:16', '2026-11-13 08:57:16'),
(13, 'Chambre 113', 'Standard', 'available', 50000.00, '2026-11-13 09:16:41', '2026-11-23 16:09:00'),
(14, 'Chambre 114', 'Double', 'available', 55000.00, '2026-11-13 09:19:18', '2026-11-13 09:19:33'),
(15, 'Chambre 115', 'Suite VIP', 'available', 100000.00, '2026-11-13 09:19:53', '2026-11-13 09:19:53');

-- --------------------------------------------------------

--
-- Structure de la table `room_access_logs`
--

DROP TABLE IF EXISTS `room_access_logs`;
CREATE TABLE IF NOT EXISTS `room_access_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `magnetic_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(191) NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_access_logs_room_id_foreign` (`room_id`),
  KEY `room_access_logs_magnetic_card_id_foreign` (`magnetic_card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `room_requests`
--

DROP TABLE IF EXISTS `room_requests`;
CREATE TABLE IF NOT EXISTS `room_requests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_requests_room_id_foreign` (`room_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `service_catalogs`
--

DROP TABLE IF EXISTS `service_catalogs`;
CREATE TABLE IF NOT EXISTS `service_catalogs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` enum('pressing','car_wash') NOT NULL,
  `name` varchar(191) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `service_catalogs`
--

INSERT INTO `service_catalogs` (`id`, `category`, `name`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'pressing', 'Pantalon', 1000.00, 1, '2026-11-12 12:37:25', '2026-11-12 12:37:25'),
(2, 'pressing', 'Chemise', 1000.00, 1, '2026-11-12 12:37:41', '2026-11-12 12:37:41'),
(3, 'pressing', 'Ensemble costume', 2000.00, 1, '2026-11-12 12:38:09', '2026-11-12 12:38:09'),
(4, 'pressing', 'Boubou long', 2000.00, 1, '2026-11-12 12:38:27', '2026-11-12 12:38:27'),
(5, 'pressing', 'jean', 1500.00, 1, '2026-11-12 12:38:39', '2026-11-12 12:38:39'),
(6, 'pressing', 'Serviette', 1000.00, 1, '2026-11-12 12:39:05', '2026-11-12 12:39:05'),
(7, 'pressing', 'Chaussure', 1000.00, 1, '2026-11-12 12:39:21', '2026-11-12 12:39:21'),
(8, 'pressing', 'kepi', 500.00, 1, '2026-11-12 12:39:33', '2026-11-12 12:39:33'),
(9, 'car_wash', 'SUV', 2500.00, 1, '2026-11-12 12:39:57', '2026-11-12 12:39:57'),
(10, 'car_wash', 'Camion', 5000.00, 1, '2026-11-12 12:40:13', '2026-11-12 12:40:13'),
(11, 'car_wash', 'voiture ordinaire', 2000.00, 1, '2026-11-12 12:40:37', '2026-11-12 12:40:37'),
(12, 'pressing', 'Tapis petit', 2000.00, 1, '2026-11-12 12:44:55', '2026-11-12 12:44:55'),
(13, 'pressing', 'Tapis Grand', 5000.00, 1, '2026-11-12 12:45:35', '2026-11-12 12:45:35');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vlLqlSDBsjQG7cMZqCWknWw9DQQCDusspPeduAfs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJaazg2N0ZWc21URXFRbFVuR2F6THhSeHFOTVk1T2UxaFFsdVBXdWI4IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443791),
('t4ERgHXw1zkMEBnmxYmCsIQLmlrhtdzEhbgdHgKV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI4UzdGRG1EUjdBMHBad2RFWUFNRDhZUnB1V1IzbEJHN081Tm5FUVE1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443792),
('ri4hPsxoOWChKmrG2EvKIHkyfaEzShpNZMRibrI5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmNjhaemVQeWtQdFVnZ3lLbkNteE9YMUZ2c295OEw3UE9Cdm1FelVvIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443791),
('xI7dCctz6GkZtdIdNHj89EpwwGavGQgf67YAZmQQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI0QmxkZGIzbVdxd0ozOWkyWjN3R252OXN3RDVsZk1lYWxCbWhwRTh5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443791),
('tquWWaGg9G86aAXPTJ8nClxpouek2lFZgR2KMSzq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI5R01uMXZya0VhWkdVNzJ0S3BuckdGUHZkZGVaMHpvdTB5NkpxSDE0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443791),
('kKLlLbPLai2PuOrlifP6ct2jTnxsMWCdYhNt7Fgp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJITmdSanNUR3dwaVpzTzh2RnpaMEMxN1lWT1ptRGxpNXliSHVHYlZBIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443791),
('VCNv0eq5GWSFLPJvSj32swIimnDxz5OuRH3ZyLbD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJVbUR6Mk1TSERRb2hZdUZ4cmJxWW5VNFVIZWN1SGh1a09jYXplOXU1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443790),
('1lrWWbjw19zU972RaDnCfZfZr8YQByWUj7hbynK2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHQkZXV3gxQlo1aGFNNEltb2tPUmQ4TUN6WDZjUkxoSlY2MER0c0QyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443790),
('W2KpQipicfxyOzf85XQduUuHqROKQF15abqfnFJC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJDMk11TlI1am9BNDZLS1RXZEpwTGhZNjJrckhUamJCQUlWU2Q1SHBtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443790),
('tFtgKjqXu3i33Q79Gh4PAEQyF5ozaIg0ihCpEEbk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTaEMySXNYMklWNHFVQ21QNVl1NkJySHJJNkJrSjZycHZOZlMyaHluIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443790),
('yXOkrLmJX7ug3qAF46vGOro9ApUaXjB3BuDeiaKo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJJZ3JKOWhXendKWEl4MmNhZVdrc3d5UUJFUnpOVWMyOFNzMWl2Y01jIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443790),
('9noRd7kkFMif2lUko7i8Pgc6YcW5natWF2b5te2p', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJMVVlFWkVEaU1WY3kyMW9wQ21DUlRTUHE2Q204aUlWYWRzaG9BaGd0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('sk7efonJben85sSbFdWsBUF79l1noQ9ZcVidnAxM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJvRVRwQmJGNTRFc2R3ZnhRM095SXJ4MGgwUnV6dVFqNVJITGdOZ0ZoIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('YYsvoMJbAHyn74AkFSmSwq18e1xo4ROdQm28Srwg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkYTh1VkR0OUNlM1VaN0lSODJ3c3kwT1JsQktjamtvTHZNTEZHVEF3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('Pc2VMCkI4U2P6DKQZxuBu1lwXXKXt8Hdlb1l673u', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxc1FpS1d3ZWNzeGI0aHdCdmFrQVg4S1VSZzk2cUxoVG5abVRLT3l5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('WzkLgXYxnUYIuXpQYHAJUgfhQ6ANWCwTHh1rJ3TY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKallEMk9kM0lNRm1DQUdzMWpJc091ZlNqOFFSUkdIRGFwYmpRcXd4IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('gNEqzYmXryazzRgqJArO8evXL5pUlhhahY4Tj1kp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpMEZNakJDbDZsRmdBcHQ3cURUdTNuSE1XUzF5cm1zUmQ2cXptems3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443789),
('mF0DNGwZOsvd9PsOBmgIg9dTWc8uJi9livTTQs3H', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJyM3hlbmJZZHpqVGhBd2JHbjRFdzgxa2FZT1VlVDBkS3kxQW9DVU1XIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('QTl1HpZbF8gqW5a5AniQaw3GIixx0guRJmvU8ylW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJLQktLWHptb2NhQ1RNU25tallSSzNUd0d1OWtkRmswSTNvZUlUWk9SIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('bfJT9iQWmztmK1PF6AXNs0mYOWm2BSRO5rbGTjJq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI3RU5OYzBqY28wVmN3UVBsdkVGSjZKNGdhRmtkQlgxTTJQQUdGZ0QxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('Nu4Ruh4CJjUDE27odxO9WmYDUU5bQh6Rcizc3YjH', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJQcExnWjM5Q2ZGTmpuaHdYSnVyeHlwUnJ3RXhrTHJXbnRPbkVMYlRtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('bAuhUbE0dMKnNzXR17eUtaNohafIHifujCgyebof', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmZnoydkY2ZkhSU0liMklsZEJZSmo0WHYwVEdtNFNZMWoxaEQxckJaIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('THzscRYvfFaQmtFdRBVl6sXBRjPFC6il1bBsBkUw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNZUJqME1iSGYzTU80SDNrTnlwbk0xaWo5aldqQXBuMmxrY3NNZVV2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('64iy47Amz9lkB0ePUHjix86rTGzRuRrGBrnTL4zn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJUNlppMVMwZWtENXBNWFB4STdIcUJ4WmJSRTlJcEpuTXdsTTVpdWl0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('3JbGH8TorvILU0QcTdPZRQxqAXYmrVZpt9EZYLtx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkR2NMOXlvUWRNbE9hTVd4cTE3djFaYmRPVERTNExNakVCZWV3MGFrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('NJ7Qf0dWCfOcRFXchPW2pzT4wKuIzEz78b2FWQtN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI3WXNWTEptYkh5SmVRYTA5NFIwV0VGWEU4V0d5MjI1V1hMMWNMMkU0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443788),
('Ly8OAIeCPXGY6VyGxNBCpKlmqXrCDInXouq8eMBG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIydktMWmxzVWV5WVc5UUQyOUZFbVpreGJVV2pZczVFR0JxNEZVZUoxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('b9zCD9tKQVraMMMNyn7HqOuhOMoXY96EC5N1CzYi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJVcWNNcXIyVFRTRDJOVTNCWjhwa1BvaTdoSWhrbHpnaGZaNDFGM0VXIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('2xIN2azAeqbQTEiK4FDq2gvybKRpmU2w07nDZKi4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJzWUwwQzhIMzB5S2JBMGM2MTRxQlFtYUdjcFQ4N1F2Z016VjdlT2ZxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('IWfv4fdkh2fsSOErqJpVvBst7IDr9jzMmeAKLz0q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkbXpkRUpxQ2kyemhpTjlzdHdsRFNMQzAydVY0Tk1XUUt4MGhES0t6IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('li1eFwPVOmJqQd1eV7SVBn5A77qJ8uLJf1diSZTu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNWXBiUk1rVDduOE5vMzh5b2tacVhyTVJ3cURCV0FTU3VHS2Jvc3hpIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443787),
('vkyfNJco4N2JUZBne2SzUFzGuMoEznqtUYWNPDwd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJiWXBIOVROU0JLRXA5clU4enVZc21McGdURjNPQ2R2dGNyMHd2dGxuIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('ia7YMkGznaOVmpOse9w2eyxE0oCdqTbTGSKlW6YW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJMWHpET2RLQ0RBckVLRGxHNjg5MkFpS3g2eVhiQVVkaEdZMEpWeUY2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('pOWmmBHpTtiE6ygKLiLX1JvpJEByDdAxYP0WwBqJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzeTVrSXZITFVxdmlvRkdmdUpDam1ZbWRlWXBFZ1B2c3BhWFpLTHZOIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('thOFHnRQmMKvopMBhxNnXWdlUTprTpcu6qeAKRkr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJyVXlaTlU5cFVBMnpZbFRsT0NxSlE1SGM0QURlUGZRdjhmSTI2dzk3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('pHhVmfNENgerPRhjTwVFeE93HiUqmC8DO94MtM19', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtcnpyY2xzd0NqUXR4TEk0OXBPWENDamxSZjV3WjhDOUxmUk1XUXFuIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('n9VlglBTUQNmahbJPw8RtM6tFaKOj65M2FsKZgnU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJsSTJidWY2OVJwZVcybTc5RTZVUjAwZ3pxTVpibXo1UnVXMVFMaW45IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('uvCTHloYc0tYRSzL88yNQrfI2NcAb66jcJ1F7GmA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJsMmdaTXFiYTNHSGJiWldsTFhUNXVTQ3pzSVgyT0NhV1l2TzJaR1FQIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443786),
('XpPQnHdEBETJgF2Gtyc8j1v6U5MTgtoovY1nvl7B', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGQjBDS3E1a0JvOHpjdmxZMUZPcHltbVpNWmtXaGxXMjdTSVkxQmNmIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('iC0k97UIBRwvYnLJ9irCH82Y3pLpCBie71Aobnx2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkU0FONnNNT0tQb25vbjhWaVpEa2JDN2lXV2UwNDdPbE1ZMGs2d0ZuIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('YOPxKoPv5POFV4lQXLF6XRmpL0b8eeKEGVTVf9as', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ3VGx5cmJtY0lEWW03N1Z6cVZNTXZNemtnbmkyWGdWbTB5dmEyem4xIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('ovTWStHviissvruygPaL5IjXACuY1zJGywFili8g', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGRFpNakFLSDI2MlpVNmtsWkJWQXMyazVCNUhqaHF5YmFhVFhVUjRBIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('YiAKC5dgw1Lj4pF80Xe4O7rtyilHv22iDrtvh8Ka', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFdndiRUZ3S3lvWHhNTVl0NUlhU2NITXZkd3RFRGJiWWxzNmxVZUxZIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('wD0e2B5nphvnETBivyS9wPS4tybVJKXbn9YMmxac', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzdzJ5STBNQk9KOUtiQjVadDB4eVRLVTdGblVCU0N6U2VnQ2g1MnZkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443785),
('2L9hfnTCYXTqhHIuopRGxUGaMzOZyBitaVWBIKBz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKNmd5Rk12T0RyTHVYSWdueEhERFlwQjlUdDFRV3I1emdycjFwN2x0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('aZyH1YSt615icDP3ZqMb3ZimIWghtt5WkEhKSpjV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJBdGdFclNFbU9Hc0tZRXMybjZIV294REFQWmVHVjI0cUE2SGpBV0RMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('wYAr9dyCVK52eqHXYV3XK8uuWMhMMaMIbuFh7xz9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNVmNWZENoZUc0QUlLWGZoUWJXN2NGUkpKc2V3SW9Tek84NWZSN0pUIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('Bj6zqIMfjrhTeYiEcR5C8PPG81JT87ztVrKt6TD9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKYjhTUkxxNG1SMVZFTGNHTEF4Uk9XM1hDZzRycm9mWWQ3WVRZU2lJIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('8BAnu1pQfJMsLOMiTcZj4knzJQzk9xgK1qqTATXT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIxSTBQMDJqUWpHejViZlZiYzJaeHZyMXpsVFFhdXBpUDJ4RGdpN1FzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('btxqupjMHXvXkuKiBBJ6McPOrMxclBYmbgDqZJ3Q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJLNjhhNXNYS0tBTEJZcDdubUpzNDFoRlA2bjJsUVdZa3R6MkNvWVdaIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('RCbMeeHs2JnhqLSCuBHp2nZSuz2r2vQhpYIFHte8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJuYXJrQ3Fvdk1Odm1SY0tvTU1yS0xYQng5UkJJZjRlTUxHcTFoN1plIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('aQkUnBPjjq2cpoE94hSJAQ8ED1q1T5F2p4bchif5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ5SzEzdExPVHl0dXNIQ3cwbUx1SlFmYVdCN090UlZ5Unh1cUkwbnBWIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443784),
('U2FYJab6Q1wp2ZR3pxF7o1nyYL3Jn6mPlZ4y1K8H', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxc0djYVJGVFdUdkJNZlAxMDFFQ2prclo1bUZ6eGNDWmcxYkJvUWdhIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('1AGJsq2c5tbg3FjI2V1OBWFxWo4r8aBMJT1h59dJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJJTWdQS0xPZmJGMHc5b2FVWWJkWVUyRVVQVWUzektPVDdZb2dRbG1yIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('U5el9JS7P0XKNkVF8HSn3sDqcsRpJNvL1w8hlJ7h', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJQejExdHdkbnBCWTJLMDd0dlUxMHN1MFA4V201Y0JrUHhHMzNWckdYIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('QayEBdefXFY7WSktnP5yhYjBjnq8QNDYJOixxrjK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFYk5NbXZTWk9PZ3g3cUJwVG5SVkhXV2JVZjFnVldRTU5rUjRjdjlwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('4Ob2AlIT0Vj0wugqV0SVsIaExHk28m45GmqILOsv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmMEVKeXA4d1NGVVhOV1JHUU9OSGFGN2dWUUpqRDFDcThwUE5OMXBMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('rDhOk4QNJnxSYBiyqpRoI2OJ2BX67olZA7uA3pAw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJYVDVCWURJREw2a3ZPVDM2TnB1Sjh1aGE4NFNFU2dHTG5LYm1ZT3lyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443783),
('xIuSskeCY2lQVTGQ9Zqc9V7Id3KRcKPnAWKsrPG6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJXSUVzaVU3VE10dTF0NFZoa1VTT0p3ZlVaNXNoNjNpRmNBY2JNYUxDIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('EHArR4SL2Z16raP2YKmA14Ybnmr7C90OOcP6Cuch', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJXVGkyN3B6bWhZeTlFdlFQV09QbWxiaU05ZVM4NjBPUFJ1NjRqc2RRIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('b2tHSKmvOFEcfdGhhU86kCk1xsxW0xkUeD3eSZ9w', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSdXRkbE53amY2MzJqUVhpR0ZNR1RFQmN1dzhvUGY1eVhMclJINUxhIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('6YM6HvmhptvVyxhQyyGeyu9jym5UjfnMSnXImPR1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ4OERaSVdzUUZWaVIzV2lBNUdvRzg2cWNWMkpVcG9JRE9xbXdEV09CIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('w07ZUOvMow1Vd7q6ChOAr205IWdmj83OZDycfqsT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoZTduRU14bzNCU3FVRVUyUWdWYUFNNWlXSzFCQUhucXBJR1Q4M1UyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('QXoaeGS7Tz44on57eCe686Cn8l955pzsBr6hhLSh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJXcDRIcGhwazR4cFVpcHQwQTNPVkJWNkRib3NCZHIzdGVvZWp3QkF0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('n8wGipBspL8oS1i3h37geug7L5nbzlsFuDOjl7cc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJEZko2NlJaWlJSdDlFTFV6OWE4UDlVeGxQS0tDSHZqVmVKN3pmMjZ6IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('oJIwtZoocNqf5nghB1TMd5Le4GaOuDG64BNYRisY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJIdmRhMHgxOVlqRDBIVmZ1T3NlMmN3aUtEOGVmYjZHQ1RRS0VtVVFhIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443782),
('iyLHMxVIQU99mSXLyM8lFqbZYTck2BSl15fkUOtd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI5QmRMZmlkSEx5NUtCQzZvbVpSVVBwUk9VOWRybE85dXhKMU9OMEVlIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('ZRT9Xf1z5HbGXuOucD9CTzrrbvW2kMbLeCzO6aeq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI4d28zdWJNTktadW82MnVPajBUTTVDckVXVmpCZWYwTHpYZFFPSUJCIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('NidE7LpQrvngEe834UQnLjbdliqD9ywuimHjoCBB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2cVFjN05uNDdQZG9qaVVnUFowbHF0MmEwOFFhZkFmZHlWeUNsOFlDIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('buvNYOg9o3QGNODmQ5ZTgtQQu09UNmBEM0rnjtcx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJEbGVDbTk1bGlUYlcwdjY0Z0FITzR1elg1Skl4bE1WM0cwRkttcWpaIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('gASoOzNHRL3vF1GjmGC1ps3cUgugbWD1Gp2FqZQl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJWbThITFFWcnpKUVRiRFMzQmVUSWlGQXRGbnBiTlpWMDYxckxGR2tmIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('bzoMAQsoWyTSbr5LUZjArlZSZ9Eu26cHxS5TVIkP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGOEJVQnVLSm1pT3BHWGpLODNxMHVwVGpsc3E0S3VqaTlBMjVtNXFKIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443781),
('GBcDI10HzdOf6GIXSkGTyyZkVy8JtJX3JVlW1cdy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJwQnRkckNtMktPWE41cVVCRHIza0wyaWRqSFpzZmZPdFFBUzFnR2pLIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('MVHJbFp3LintlYx73JEaXlHonvQV2296Eezgi36q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2eERHellKelhIeTZXTEY2YUJ4Q3pjeWZCZDNqUHh2eTVqd3hCQW85IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('se2dDedJFftzxxRfWbVfQYiQ6QkHoWYx1jwME8Sb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ0Y0wwR0FCQUtnamU2emFOVTZib29aRkxtNllEM0Y1R3UyaTdBRkFyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('NURZJ1KLnyn3iL21N9E30HC1ukMYLY8V8xKO2xLg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmR2kzR1N4ZHJzTEZjZDRiUmJQdjFBY2dHbHdCNHdleTk2ZmVtaFcyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('VpqkO7b2q5N2DMxMwQ2qJQtNOaCkajcXfPckxfF5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJlU2xGS1ZVWTlJeTBNSnlvc1g0ZEhHaXdoMGpJT1VPREJQbGRkMWExIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('QMb7g8XwTWSL0ArcRXkQXbeR3362CZAVAJFQCm8C', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkdGRCN3lobEhQRVYyR3hWVmY1TEI1OVRta0J4QWNQVUNzdEh0WXA4IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443780),
('kv0lPCF4hfQ2kf2kGz594t9m2pkye5E76tHCU4aA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxN2lkNXI4bGEwcW5zejFtbDBYdnRvMVNmMXliRXgxbXIwT0JqRTh5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443779),
('b1sofw7uERXjWP4JvOkpMioxWmdhq0ScqVpyuDq9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJjSnQ4MHFXaVVsYzRBVTJNd3RtMzNUQWlWUkpmRFpocXpXNG1VdU1EIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443779),
('u9dsaZUkeZVfzNajgCNCpX1IP5rzyfggPtlv9kp1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmMGxHWllha29ZcFA4anJZSUZzVFNuVUkwSk1lMGpsZnp6MFJ5NnRpIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443779),
('AcAVwrYDGdepGR4sCrwD0NJ2CnNzTWGLkbAOrzw4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJJdkoySW00Q05ES1psZWFYQ2dvdW1CMml2cWZ5Rkg0dk4wUThydFpJIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443779),
('rLLRJuosHdlD5zay5dYToJWDCdPstPCc6Mckd651', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJPNlVXRFdBcGNSeUlZVXdPZVRKWVM4VFFmMUlLZHd5YmFBOVFyNzRLIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443779),
('UyNuaJERSiNY9BdGW5PYZUc3EEVuwzna0dRAuwh9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIyV3RreUREV0Z6VW5ZcXFRYzFBSDkzaUV5TnMzMUdlSWhQMFAxSm83IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443778),
('S1BwRUohvYFSbnBfTDVQ4TXVyGuj5fEcXxRs82GP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJadENwZ3dJS1R3dkpBUmNNWXdralQzazZZSVJzSmRCNzlxcVpzVkJkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443778),
('H5RdZMdhVJZkm8xx0Qsmaxd9UXvN9fUI684dG8L1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhWWRQeDBHa2dGQkhlellHQWxGZG51MEpHV1dGYXlaU3lhTFpwVlFiIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443777),
('ZSiief4fKHUcWnW0sUAes50qR3ypAf9jNqjhFfJc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpNHltTWl5eDBKRjlQWDltd25qd1UyQ01iSGRucnAzNmVpNUdmak5DIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443777),
('9JNJ8UibblHOmvnFrfx7kSmrkkqZNXCqxmBJYmLf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOQmszQUdrZUtNNHVwNXJmbjFwQ0E4TkNZZW4xR2JqSUpnZTJ0MGVxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443776),
('pwFV6CRjIn5QXbaNlpiH0UhekawmaEB0vYXTbZWG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ4N24xTWk4SVh2Q2RwalNONHRTS29NRDc2elVybHk5dmJ6VnkxajZZIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443776),
('GXrPTYh0tEA0Q7jjWY6Tyi2PI3uPYol0LZ7c9NJ5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJyc0x5TjJlWGRvU3Z6RGtJdFlJN21raXJ6cnYwbkk0bGp4WEI1UnF3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443776),
('nemMCCeEdJI6mnuVHClCQoxbJWdLJILI0V7uguhx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI0YkRBQ3ZHQnlMWm40cHpISlB4QUZ2bDdZMlVteWZadG53cjFpUjR6IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443774),
('PKjOmDorXaAIKKjNJLoBe1qs8SohCA8ZupW8Tdmo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJicGU2YnY2Q0Z5NlM4V0FYMm54QVROWU5Kd1dPeHZSaFo2TW5Cbks1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443773),
('wOcRYJ5xlVsPKk0goq7MTxOvaGvxIEeHkvJMELEU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpOFNxMjk4Umd1eVFNdHQxU011WmFCSHV0YXBYNmRVMVkxMXgwRnlPIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443773),
('2SIsWzPaDKviSnPNIIYump56KAvsoxcQmLFFM7Bx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI4R2pNUlFCdXM4RzNSRVRGYlRQVVAyV204dGlSeE5neW9jREF1RkZUIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443773),
('UIwHZX8eHeY8GMXMPAN3MuXCt7p3EWcMRZw2bydk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJRZElkc1BXWGxxd0hJUnhtYmgxdDMwZnJRZHZWV0NKeEkzSEs1S1BtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443770),
('Qqiq1aUbZ7KdXSXWXvcNFWgW2NpPix4xo8nxNRfp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI3eEdxbnNPSjlmVHNmT1RFRlpOVEs2cXBnck1OeGlFcWJoUEVqVnNvIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443770),
('66TA9louQyCF8mHx9kWpF6NbDVNXnl9IAsYVH8AG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJac081SUFyS2NoWWZTc2gyT3U2RkNqbm5LUDVTR1c1R1ZaUVZ1MjJGIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2NoZWNraW4ifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iaWxsaW5nXC9yb29tXC80Iiwicm91dGUiOiJiaWxsaW5nLnJvb20ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiI2YWNmNTkyY2I2NTg5NTA2ZGU3YThjOGJjMDQxOGQwMzUyYTFlOTU4ODY2MDdkNWE5Nzg5NjAyN2U0OWU0N2VlIn0=', 1795452590),
('J8vwnaoLa0rANqfDEpDvVl651fMEWId495lfW158', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzQ0NNV2Z3MlRNUjJuSnpiSGE2R3RwcGVOcEVkNTkxSUpGdklaeWtpIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443769),
('Nes9TqnStIyePYCVf8Taw8ohUpzBNHBwPGfLNXDa', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJqeko4SHNMRmQxSWdodkRONmpPVHlHSWpjODRkMUdWR0VIM1ZOa3B3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795443769);

-- --------------------------------------------------------

--
-- Structure de la table `shows`
--

DROP TABLE IF EXISTS `shows`;
CREATE TABLE IF NOT EXISTS `shows` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `total_capacity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `shows`
--

INSERT INTO `shows` (`id`, `title`, `description`, `total_capacity`, `created_at`, `updated_at`) VALUES
(1, 'Lougah François', 'Spectacle musical en direct dans la grande salle de l\'hôtel.', 200, '2026-10-31 09:07:47', '2026-11-11 10:01:10'),
(2, 'Salle Ernesto Djédjé', 'En hommage au grand Artiste Ivoirien des années 80, père du Ziglibity', 5000, '2026-11-02 13:44:43', '2026-11-02 13:44:43'),
(3, 'Paul Wassaba', 'Grande salle de spectacle', 7500, '2026-11-11 10:05:11', '2026-11-11 10:05:11');

-- --------------------------------------------------------

--
-- Structure de la table `show_bookings`
--

DROP TABLE IF EXISTS `show_bookings`;
CREATE TABLE IF NOT EXISTS `show_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `show_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seat_number` varchar(10) DEFAULT NULL,
  `ticket_type` varchar(191) NOT NULL DEFAULT 'populaire',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `show_bookings_show_id_foreign` (`show_id`),
  KEY `show_bookings_user_id_foreign` (`user_id`),
  KEY `show_bookings_room_id_foreign` (`room_id`),
  KEY `show_bookings_event_id_foreign` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `staff_advances`
--

DROP TABLE IF EXISTS `staff_advances`;
CREATE TABLE IF NOT EXISTS `staff_advances` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `advance_date` date NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Espèces',
  `reason` varchar(255) DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_advances_user_id_foreign` (`user_id`),
  KEY `staff_advances_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `staff_attendances`
--

DROP TABLE IF EXISTS `staff_attendances`;
CREATE TABLE IF NOT EXISTS `staff_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `work_date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `status` enum('present','late','absent','off') NOT NULL DEFAULT 'present',
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_attendances_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `staff_shifts`
--

DROP TABLE IF EXISTS `staff_shifts`;
CREATE TABLE IF NOT EXISTS `staff_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(255) NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('matin','soir','nuit','journee') NOT NULL DEFAULT 'journee',
  `hours_label` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_shifts_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock_items`
--

DROP TABLE IF EXISTS `stock_items`;
CREATE TABLE IF NOT EXISTS `stock_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'unité',
  `current_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `alert_threshold` decimal(10,2) NOT NULL DEFAULT 5.00,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock_items`
--

INSERT INTO `stock_items` (`id`, `category`, `name`, `unit`, `current_stock`, `alert_threshold`, `unit_cost`, `created_at`, `updated_at`) VALUES
(1, 'bar', 'Pack Eau Minérale Céleste (6x1.5L)', 'pack', 25.00, 5.00, 2500.00, '2026-11-17 12:21:05', '2026-11-17 12:21:05'),
(2, 'bar', 'Casier Coca-Cola / Sucrerie (24)', 'casier', 12.00, 4.00, 8500.00, '2026-11-17 12:21:05', '2026-11-17 12:21:05'),
(3, 'cuisine', 'Bouteille de Gaz B12 (12.5 kg)', 'bouteille', 4.00, 2.00, 5500.00, '2026-11-17 12:21:05', '2026-11-17 12:21:05'),
(4, 'entretien', 'Bidon Eau de Javel 5L', 'bidon', 8.00, 3.00, 3000.00, '2026-11-17 12:21:05', '2026-11-17 12:21:05'),
(5, 'entretien', 'Papier Toilette (Paquet de 12)', 'paquet', 15.00, 5.00, 3500.00, '2026-11-17 12:21:05', '2026-11-17 12:21:05'),
(6, 'linge', 'Serviette de Bain Blanche', 'pièce', 42.00, 10.00, 4500.00, '2026-11-17 12:21:05', '2026-11-19 11:18:40');

-- --------------------------------------------------------

--
-- Structure de la table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_item_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out','loss') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_stock_item_id_foreign` (`stock_item_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@hotel.com', NULL, '$2y$12$YcLsejkb2dqmwyqT6WSiz.lNXIr2bJ7g.T1jQKjOhnJhBvSHgvADO', NULL, '2026-10-31 09:07:47', '2026-11-11 09:03:14'),
(2, 'Koné Nafata', 'nafie@gmail.com', NULL, '$2y$12$XM05cWyuEEjPsHuUVGrS9ucrDRHDAP3GuoH2MkreRMsAgcZoe2z/W', NULL, '2026-11-09 11:18:06', '2026-11-17 10:32:02'),
(3, 'Coulibaly Youssef', 'youssef@yahoo.fr', NULL, '$2y$12$O5ng2kcBQvkdgGkdaYdKAeIQtUxduugJva2GLNnv7SL1Msd9ofAbW', NULL, '2026-11-17 09:23:23', '2026-11-17 09:23:23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
