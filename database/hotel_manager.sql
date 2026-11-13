-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 13 nov. 2026 à 15:19
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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billings`
--

INSERT INTO `billings` (`id`, `receipt_number`, `room_id`, `order_id`, `event_ticket_id`, `service_type`, `amount_paid`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 'REC-20261111-0001', 2, NULL, NULL, 'room_booking', 150000.00, 'cash', 'completed', '2026-11-11 14:22:59', '2026-11-11 14:22:59'),
(2, 'REC-20261111-0002', 1, 2, NULL, 'restaurant', 45000.00, 'cash', 'completed', '2026-11-11 14:26:20', '2026-11-11 14:26:20'),
(3, 'REC-20261111-0003', 1, NULL, NULL, 'room_booking', 355000.00, 'cash', 'completed', '2026-11-11 14:27:38', '2026-11-11 14:27:38'),
(4, 'REC-20261111-0004', 2, NULL, NULL, 'room_booking', 75000.00, 'cash', 'completed', '2026-11-11 14:31:48', '2026-11-11 14:31:48'),
(5, 'REC-20261111-0005', 5, NULL, NULL, 'room_booking', 55000.00, 'cash', 'completed', '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(6, 'REC-20261111-0006', 5, 3, NULL, 'restaurant', 22000.00, 'cash', 'completed', '2026-11-11 14:43:13', '2026-11-11 14:43:13'),
(7, 'REC-20261111-0007', 5, 4, NULL, 'restaurant', 8500.00, 'cash', 'completed', '2026-11-11 15:06:41', '2026-11-11 15:06:41'),
(8, 'REC-20261111-0008', 5, 5, NULL, 'restaurant', 27500.00, 'cash', 'completed', '2026-11-11 15:15:09', '2026-11-11 15:15:09'),
(9, 'REC-20261111-0009', 5, NULL, NULL, 'room_booking', 170000.00, 'cash', 'completed', '2026-11-11 15:19:30', '2026-11-11 15:19:30'),
(10, 'REC-20261112-0001', 2, 6, NULL, 'restaurant', 27500.00, 'cash', 'completed', '2026-11-12 08:57:21', '2026-11-12 08:57:21'),
(11, 'REC-20261112-0002', NULL, 7, NULL, 'restaurant', 22000.00, 'cash', 'completed', '2026-11-12 08:58:25', '2026-11-12 08:58:25'),
(12, 'REC-20261112-0003', 1, NULL, NULL, 'room_booking', 0.00, 'cash', 'completed', '2026-11-12 10:06:06', '2026-11-12 10:06:06'),
(13, 'REC-20261112-0004', 1, NULL, NULL, 'pressing', 6000.00, 'cash', 'completed', '2026-11-12 13:14:42', '2026-11-12 13:14:42'),
(14, 'REC-20261112-0005', NULL, NULL, NULL, 'car_wash', 25000.00, 'cash', 'completed', '2026-11-12 13:19:55', '2026-11-12 13:19:55'),
(15, 'REC-20261112-0006', NULL, NULL, NULL, 'pressing', 15000.00, 'cash', 'completed', '2026-11-12 13:20:42', '2026-11-12 13:20:42'),
(16, 'REC-20261112-0007', 2, NULL, NULL, 'pressing', 3000.00, 'cash', 'completed', '2026-11-12 13:21:46', '2026-11-12 13:21:46'),
(17, 'REC-20261112-0008', 6, NULL, NULL, 'room_booking', 52000.00, 'cash', 'completed', '2026-11-12 14:32:48', '2026-11-12 14:32:48'),
(18, 'REC-20261113-0001', 5, NULL, NULL, 'room_booking', 65000.00, 'Espèces', 'completed', '2026-11-13 11:26:12', '2026-11-13 11:26:12'),
(19, 'REC-20261113-0002', 11, NULL, NULL, 'reservation_deposit', 25000.00, 'Espèces', 'completed', '2026-11-13 12:27:17', '2026-11-13 12:27:17');

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
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:17:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"gerer_chambres\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:10:\"gerer_menu\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:18:\"consulter_iot_logs\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:15:\"emettre_billets\";s:1:\"c\";s:3:\"web\";}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:17:\"configurer_salles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:17:\"voir_comptabilite\";s:1:\"c\";s:3:\"web\";}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"voir_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"gerer_checkin\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:8:\"voir_iot\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"voir_chambres\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"gerer_menage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:18:\"gerer_restauration\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"gerer_services\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:13:\"gerer_encours\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:16:\"voir_grand_livre\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"voir_rapport_z\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:17:\"gerer_billetterie\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"receptionniste\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"restauration\";s:1:\"c\";s:3:\"web\";}}}', 1794586262);

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
-- Structure de la table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) NOT NULL DEFAULT 'Ivoirienne',
  `id_type` varchar(255) NOT NULL DEFAULT 'CNI',
  `id_card_number` varchar(191) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `phone`, `email`, `nationality`, `id_type`, `id_card_number`, `address`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-04 10:48:04', '2026-11-04 10:48:04'),
(2, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-04 11:26:29', '2026-11-04 11:26:29'),
(3, 'KONE NAFATA', '0102034568', NULL, 'Ivoirienne', 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-04 14:08:50', '2026-11-04 14:08:50'),
(4, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-04 14:52:48', '2026-11-04 14:52:48'),
(5, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-04 15:05:01', '2026-11-04 15:05:01'),
(6, 'KONE NAFATA', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 11:27:57', '2026-11-11 11:27:57'),
(7, 'Yoro Sylvanus', '0405021326', NULL, 'Ivoirienne', 'CNI', 'CNI21245', NULL, NULL, '2026-11-11 11:44:29', '2026-11-11 11:44:29'),
(8, 'Kouadio Seraphin', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-11 12:00:54', '2026-11-11 12:00:54'),
(9, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 12:50:38', '2026-11-11 12:50:38'),
(10, 'KONE YAYA', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:11:52', '2026-11-11 13:11:52'),
(11, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 13:31:24', '2026-11-11 13:31:24'),
(12, 'KONE NAFATA', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:33:21', '2026-11-11 13:33:21'),
(13, 'Kouadio Seraphin', '0405021326', NULL, 'Ivoirienne', 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 13:34:33', '2026-11-11 13:34:33'),
(14, 'COULIBALY YOUSSEF', '0405021326', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 13:40:01', '2026-11-11 13:40:01'),
(15, 'KONE YAYA', '0143677424', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-11 13:41:05', '2026-11-11 13:41:05'),
(16, 'Koffi Samption', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 24513578925', NULL, NULL, '2026-11-11 13:57:48', '2026-11-11 13:57:48'),
(17, 'COULIBALY YOUSSEF', '0143677424', NULL, 'Ivoirienne', 'CNI', 'CNI 123333', NULL, NULL, '2026-11-11 14:22:09', '2026-11-11 14:22:09'),
(18, 'KONE NAFATA', '0102034568', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-11 14:22:59', '2026-11-11 14:22:59'),
(19, 'Kouadio Seraphin', '0102034568', NULL, 'Ivoirienne', 'CNI', 'CNI21245', NULL, NULL, '2026-11-11 14:41:10', '2026-11-11 14:41:10'),
(20, 'Amara Kanté', '0405021326', NULL, 'Ivoirienne', 'CNI', 'cni21022', NULL, NULL, '2026-11-12 14:32:48', '2026-11-12 14:32:48'),
(21, 'Abade Luc Romuald', '0725435689', NULL, 'Ivoirienne', 'Passeport', 'CI001248', NULL, NULL, '2026-11-13 10:40:18', '2026-11-13 10:40:18');

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
(9, 3, 'honneurs', 50, 48, 100000.00, '2026-11-09 15:04:00', '2026-11-12 09:40:13'),
(8, 3, 'vip', 150, 148, 50000.00, '2026-11-09 15:04:00', '2026-11-09 15:22:25');

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `extra_services`
--

INSERT INTO `extra_services` (`id`, `category`, `service_name`, `quantity`, `unit_price`, `total_amount`, `room_id`, `customer_name`, `work_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 'pressing', 'Boubou long', 3, 2000.00, 6000.00, 1, NULL, 'ready', 'paid', '2026-11-12 12:46:43', '2026-11-12 13:14:42'),
(2, 'car_wash', 'Camion', 5, 5000.00, 25000.00, NULL, 'KONE YAYA', 'ready', 'paid', '2026-11-12 13:19:50', '2026-11-12 13:19:58'),
(3, 'pressing', 'jean', 10, 1500.00, 15000.00, NULL, 'KONE YAYA', 'ready', 'paid', '2026-11-12 13:20:40', '2026-11-12 13:20:43'),
(4, 'pressing', 'Pantalon', 3, 1000.00, 3000.00, 2, NULL, 'pending', 'paid', '2026-11-12 13:21:22', '2026-11-12 13:21:46'),
(5, 'pressing', 'Boubou long', 2, 2000.00, 4000.00, 6, NULL, 'pending', 'unpaid', '2026-11-12 14:34:01', '2026-11-12 14:34:01'),
(6, 'pressing', 'Chemise', 5, 1000.00, 5000.00, 6, NULL, 'pending', 'unpaid', '2026-11-12 14:34:17', '2026-11-12 14:34:17'),
(7, 'pressing', 'Pantalon', 5, 1000.00, 5000.00, 6, NULL, 'pending', 'unpaid', '2026-11-12 14:34:34', '2026-11-12 14:34:34');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `housekeeping_logs`
--

INSERT INTO `housekeeping_logs` (`id`, `room_id`, `user_id`, `housekeeper_name`, `action_type`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Agnero gertrude', 'daily_stay_clean', 'Recouche quotidienne effectuée (Agent : Agnero gertrude).', '2026-11-13 14:34:03', '2026-11-13 14:34:03'),
(2, 2, 1, 'Non assigné', 'daily_stay_clean', 'Recouche quotidienne effectuée (Agent : Non assigné).', '2026-11-13 14:34:20', '2026-11-13 14:34:20'),
(3, 5, 1, 'Non assigné', 'daily_stay_clean', 'Recouche quotidienne effectuée (Agent : Non assigné).', '2026-11-13 14:38:07', '2026-11-13 14:38:07'),
(4, 11, 1, 'Non assigné', 'daily_stay_clean', 'Recouche quotidienne effectuée (Agent : Non assigné).', '2026-11-13 14:38:09', '2026-11-13 14:38:09');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `housekeeping_schedules`
--

INSERT INTO `housekeeping_schedules` (`id`, `work_date`, `room_id`, `housekeeper_id`, `task_type`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, '2026-11-13', 1, 3, 'daily', 'done', '2026-11-13 14:34:03', '2026-11-13 14:33:51', '2026-11-13 14:34:03');

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `magnetic_cards`
--

INSERT INTO `magnetic_cards` (`id`, `card_uid`, `security_token`, `room_id`, `duration_days`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'RFID_QHMTYA', 'eF2kV4iIh2HpPOlQlbUJmuacrxw18ZjF', 1, 6, '2026-11-17 12:00:00', 1, '2026-11-11 14:22:09', '2026-11-11 14:22:09'),
(2, 'RFID_ZMNZYO', 'kFW51R8nP40740HOWTuFEpeqAf6i1CJs', 2, 5, '2026-11-16 12:00:00', 1, '2026-11-11 14:22:59', '2026-11-12 11:29:07'),
(3, 'RFID_547NQO', 'gTr2lKBuMFTVPhNghHt3dxqIACv08BEY', 5, 6, '2026-11-17 12:00:00', 1, '2026-11-11 14:41:10', '2026-11-12 11:41:33'),
(4, 'RFID_L65ESH', '4XQtlOEzbvE1Q9bUZkj2ieRNe5H6m414', 6, 4, '2026-11-16 12:00:00', 1, '2026-11-12 14:32:48', '2026-11-12 14:38:06'),
(5, 'RFID_RES001', 'token_reservation_7_nuits_123456', 11, 7, '2026-11-20 12:00:00', 1, '2026-11-13 12:00:00', '2026-11-13 12:00:00');

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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(29, '2026_11_13_142436_create_housekeeping_planning_tables', 12);

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
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_table_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_restaurant_table_id_foreign` (`restaurant_table_id`),
  KEY `orders_room_id_foreign` (`room_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `restaurant_table_id`, `room_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 85000.00, 'paid', '2026-11-11 14:23:39', '2026-11-11 14:27:38'),
(2, 1, 1, 45000.00, 'paid', '2026-11-11 14:26:03', '2026-11-11 14:26:20'),
(3, 2, 5, 22000.00, 'paid', '2026-11-11 14:41:34', '2026-11-11 14:43:13'),
(4, 1, 5, 8500.00, 'paid', '2026-11-11 14:43:07', '2026-11-11 15:06:41'),
(5, 2, 5, 27500.00, 'paid', '2026-11-11 15:05:59', '2026-11-11 15:15:09'),
(6, 1, 2, 27500.00, 'paid', '2026-11-11 15:21:34', '2026-11-12 08:57:21'),
(7, 1, NULL, 22000.00, 'paid', '2026-11-12 08:57:01', '2026-11-12 08:58:25'),
(8, 1, 6, 42500.00, 'charged_to_room', '2026-11-12 14:33:39', '2026-11-12 14:33:39');

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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price_at_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10, 3500.00, '2026-11-11 14:23:39', '2026-11-11 14:23:39'),
(2, 1, 2, 10, 5000.00, '2026-11-11 14:23:39', '2026-11-11 14:23:39'),
(3, 2, 1, 10, 3500.00, '2026-11-11 14:26:03', '2026-11-11 14:26:03'),
(4, 2, 2, 2, 5000.00, '2026-11-11 14:26:03', '2026-11-11 14:26:03'),
(5, 3, 1, 2, 3500.00, '2026-11-11 14:41:34', '2026-11-11 14:41:34'),
(6, 3, 2, 3, 5000.00, '2026-11-11 14:41:34', '2026-11-11 14:41:34'),
(7, 4, 1, 1, 3500.00, '2026-11-11 14:43:07', '2026-11-11 14:43:07'),
(8, 4, 2, 1, 5000.00, '2026-11-11 14:43:07', '2026-11-11 14:43:07'),
(9, 5, 1, 5, 3500.00, '2026-11-11 15:05:59', '2026-11-11 15:05:59'),
(10, 5, 2, 2, 5000.00, '2026-11-11 15:05:59', '2026-11-11 15:05:59'),
(11, 6, 1, 5, 3500.00, '2026-11-11 15:21:34', '2026-11-11 15:21:34'),
(12, 6, 2, 2, 5000.00, '2026-11-11 15:21:34', '2026-11-11 15:21:34'),
(13, 7, 1, 2, 3500.00, '2026-11-12 08:57:01', '2026-11-12 08:57:01'),
(14, 7, 2, 3, 5000.00, '2026-11-12 08:57:01', '2026-11-12 08:57:01'),
(15, 8, 1, 5, 3500.00, '2026-11-12 14:33:39', '2026-11-12 14:33:39'),
(16, 8, 2, 5, 5000.00, '2026-11-12 14:33:39', '2026-11-12 14:33:39');

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'gerer_chambres', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(2, 'gerer_menu', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(3, 'consulter_iot_logs', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(4, 'emettre_billets', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(5, 'configurer_salles', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(6, 'voir_comptabilite', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
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
(17, 'gerer_billetterie', 'web', '2026-11-12 15:41:06', '2026-11-12 15:41:06');

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
  `status` enum('confirmed','checked_in','cancelled','no_show') NOT NULL DEFAULT 'confirmed',
  `provenance` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_customer_id_foreign` (`customer_id`),
  KEY `reservations_room_id_foreign` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `customer_id`, `room_id`, `check_in_date`, `check_out_date`, `nights`, `total_amount`, `deposit_paid`, `status`, `provenance`, `destination`, `special_requests`, `created_at`, `updated_at`) VALUES
(5, 8, 11, '2026-11-13', '2026-11-20', 7, 175000.00, 25000.00, 'checked_in', 'Aboisso', 'Abidjan', NULL, '2026-11-13 12:27:17', '2026-11-13 12:32:32'),
(4, 16, 5, '2026-11-26', '2026-11-28', 2, 90000.00, 65000.00, 'confirmed', 'Adiake', 'Abidjan', NULL, '2026-11-13 11:26:12', '2026-11-13 11:26:12');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `number`, `location_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Table 5', 'restaurant', 'free', '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 'Transat B2', 'bar_piscine', 'occupied', '2026-10-31 09:07:47', '2026-10-31 09:07:47');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(2, 'receptionniste', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37'),
(3, 'restauration', 'web', '2026-11-09 09:50:37', '2026-11-09 09:50:37');

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
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 3),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2);

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
(1, 'Chambre 101', 'Standard', 'occupied', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:22:09'),
(2, 'Chambre 102', 'Luxe', 'occupied', 75000.00, '2026-10-31 09:07:47', '2026-11-11 14:22:59'),
(3, 'Chambre 103', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-12 16:14:36'),
(4, 'Chambre 104', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(5, 'Chambre 105', 'Standard', 'occupied', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:41:10'),
(6, 'Chambre 106', 'Luxe', 'occupied', 75000.00, '2026-10-31 09:07:47', '2026-11-12 14:32:48'),
(7, 'Chambre 107', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(8, 'Chambre 108', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-12 16:14:41'),
(9, 'Chambre 109', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(10, 'Chambre 110', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-11 14:18:52'),
(11, 'Chambre 111', 'Suite Junior', 'occupied', 25000.00, '2026-11-13 08:56:51', '2026-11-13 12:32:32'),
(12, 'Chambre 112', 'Standard', 'available', 45000.00, '2026-11-13 08:57:16', '2026-11-13 08:57:16'),
(13, 'Chambre 113', 'Standard', 'available', 50000.00, '2026-11-13 09:16:41', '2026-11-13 09:18:54'),
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
('kMbaghFDdTyGmgpONgZmSROtfTEcRK6n762XfA9o', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmdDVnMEdKSHFOeVllTFZPSmVheEFVQmlBSDkwc2h3WFBQRUxRWjhBIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9ob3VzZWtlZXBpbmdcL3JlcG9ydCIsInJvdXRlIjoiaG91c2VrZWVwaW5nLnJlcG9ydCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZhY2Y1OTJjYjY1ODk1MDZkZTdhOGM4YmMwNDE4ZDAzNTJhMWU5NTg4NjYwN2Q1YTk3ODk2MDI3ZTQ5ZTQ3ZWUifQ==', 1794583110),
('VcI6oFF51RqHnJuvihmbADUC9BYFdPIEt1RhNAHm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSaDJPcEpHOGRiVVFCVzRlc0ZVVnBvUW03RVY1eUcxMElzTnI5VXZCIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578547),
('8DC1yKVCKy1USPNs7NtNGruHbvoWWbV8sMFhMfti', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ5UE4weFphbWZGY1VEV05WdDFOWTZXWWkzdGgweWtvNTlWMnJTalRNIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578547),
('5OlE2QsiSQO1qmdtoE00Qu0wl2JHZxCxtEl2xhON', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJPS1lBUXA3TlhqT0E5dm1kTWNzWExGSVl6QWd5NFB3VFdEbXBnS1F3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578547),
('jKUONRmo3KUJqoAzzhZoFnK8jBolMhIfLSBOrIvd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJZOGFKUFBLZDNxR2VaWkJ6WEwwRnhsYUZqaHpTb2trOFlOTjBZWTJiIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578547),
('DsPVEXjUYiiaMCgBay22zEGq7PLYTXTmWrssrMd0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpMml3ZTMxWm56c1hiUkM4Y1VhUkJUbkZrcWtRSGtNelUyQVBOWW1CIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578550),
('iU26XcJYYhHNUTeVmhiAQgUuboaCSutoK431kSeO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ1QWlUVEdpNmtEcnN5dWhwU0k5Y21FdUM1NWFyUU51RGZZcnlRNVFIIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578550),
('X3zDNdJRC1i8fvGyDUMw17EJ3QtmevwW4vG6cxIw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOVkJQaVQ1SllZM1NRSnRNaDVRZ1FiSzdyQWQ0U2cyUXJudkVWSlJuIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578550),
('Kv2Fa2HgmU8ttUzdrTOwfjJaI5MvwuoUMBhzL1pw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI0WDhpdGJ3RkNuU0ZmcUZDMkhrN2g0MnZHdDA2TVQ4UlM2Z0d0S2RtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578550),
('NxUCSuqFGhDqUvFOYTEIYC9kHRZYNv1vyTCvSSWR', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJObWM2WUdUUld1b3BXVGNFUUlFNGpqZkxmTGQxUU9KNTRCYVB4cTZRIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578551),
('LFd3ahI7ziPjDeMYbJzjbZrvqM3n2kMxo1gNWhVu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJaekQ3R0dsZjRBaElXZWlqT3NaR2dqRnhJaklGZEJwY3dHRDF1ZXMwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578551),
('BiONcztRq4OWwHHm2VrvQSKpicu5ag3ywk6MzGJx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJnbDJGaVJDMlVsZ1V1bG9JaGVQS1pMMFdwbnF3bkV0d0R0RHdGZUZkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578551),
('oeh1johOrr6IMEkPsvM05x7uNEqwytne1I7IpWQK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJMYkZydXAxSTA4endBMWZ3WFVldkhqbDlLRkJ3ZUJPeVdCWURmMkhoIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578551),
('8RzEBmpHr89GpfT2p4Azm6FKYgGuCZzmiHzUdSnL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJjdGdnUTNxWXkwcWVacWhOMXJLT2c3M2tLSHJ6czhmVGloakZackRoIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578553),
('yxNZS1xKdi7J7wqb5Fvm1SeCOPVmtJaRobez2Wd1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNNUxGbGcydWtjSzB2aE5MSlhRenBUUzFwcjZqWjF3S3h5WFVLbUEzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578553),
('bAx0f0ExelvCWJmDvnlKTSfDsoaSrpf8qogkAaoL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIwckZkVEtkMDI0Q0ZCUmNETThHdUhBRFdqZEV1a1QweElZM0tENHRiIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578554),
('m6VvGthQqd7oUlfAckc4vUVhPEKEr3QdJR8k1NIf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFU1dwR2x4SzNsbzhEdjRxNDNlejVMUFNocGZpekJyYmtLY2lldVZSIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578554),
('Chd8qch4Y5KSDWnmtnpompZNmsdpXHuq3nv9ynSp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhR00yMHVnOGFtSEZMdndZb1FNMUFRc0xzODJPenlDUVJISFlzUTQ4IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578554),
('6aVNBXAEjX0eZEc7C9sqzfTogBy1Vx52iFSnJeNF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2UDVSRGFhMWhYN2hROUZDTDBTaFNqczFWWXc0ZnY2Q21BalhVb0ZtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578554),
('qXadljREHiKxu901qoKdJoI5CghS61Y8IWU1NH0T', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtOFRoc0tGUzRvWlpPOWFJM3VIQWJnS21zYzF6QlJXVHZIMUo5cEF2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('bBnR7C14JIOa8V7DBqavyW8kUEfqyiUFw0XhjtIm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJwbVVTVkszWkpOc1VTd3Q2Z0lzSUI1ZUxVTnhyZlZyZTBtcjhwRW1ZIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('HgSS29aUPSnXCG646hElXg9wVVJ9gE75gJNiiMND', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ0RWFNb3NMaHNzSk1LQnYwNTlNTTFCN0xQNWdLNXowQ2M0RDgzUTdYIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('kSrx45fXT5gITphAnfgDLuv6aYSjUAvypH3Yfp7Q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSNW94MWVXWm9zcnkxZE9PZG1qdEFFYjcxVWZXZU9tUDVRYXoyaEZ5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('zqMgDO543NbNSh0DH1wtjFlHOfCmCbQPivXN3GPf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ5aVViQXoyenFaUFZWc2F4SlRxb2QyS0xZZU10Z3N4OW9saFNmS3UxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('Bk8U0N9Vz670wHySZY2v5oeK3AvJh09t7T6BoFTW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJLR1hVZHdtNmwyVVM2UjdOYWpzMVdtTzN2NE9Tb0FOV0NqYnNrZ3cyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578555),
('U1cneaEPToadF0FENIIc9dIE72rvX9z0AcL4vICM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJjZ0ZjT0htUklmZ0ZWRXp4c2l5ZElnd29RUlNBUDd3eGNyelFsTHNNIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578556),
('nsh8gR5QSUcVFSVLRDL2R4VcCndh4sJLnJYPLhuH', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtbGluZG96elJLZ2JKMjBFdFlRcWdtZjQzU0J5NmNZUklsMEdqV3pzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578556),
('Zn06pMXRWzhJKoDqXv1EJY54Bet9C36O2kKoGmnG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtWnRIRW9XVEhUc21PZVN1cEUybHgxYkFGRTF2dG82S0l4UUhFdVVlIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578556),
('qEGbNoovV6TVjVUVYOxeK3GmJLhWLUSlsgmgqrDP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJwQlp1SURtemlacGdYY2FlU2FuS05Pa0JhZWQwMXB6NnZXdjRoMVRIIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578556),
('xVbNam2O6ATfWZuUhyB4FSMBAfe3HSpKQieoXURm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtZ3VJUlhQZkt4REl2V2tKbHY1azJqTHZLeWk5emg3eTFHeDY2bWs3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578557),
('IJIeyFfV1deo3aV0wv2tZNHPu8S5BbtycDepRgpw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGRHA2eU9MTWVtVGhYbTRPTVplUVBLNll6T3ducWRqU09kN0pIQkt2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578557),
('jjL7iRAtavUMQzPSzyGfdpRnKyTEmQxoCmDNj2QN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJBcjB5akptMDk0SkRVQ3NrblpFOHc4OVIySm1ndGVuRWt1Sks3cjdGIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578557),
('j3ICwDaNcquK8s925SpDHq1O6Vw6bjuUrqJCAOIU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJPSWk5d2lWRFh6Z003Q0hPQ2VvdVYwUEpoRE5FaXlWTEdpNGp3Y2VEIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578557),
('AejrTvMqYcCiLDhpij4iWtldRuf0ElBGmnShgJ1M', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmc0F2dEhmZWJyUzdaUmtFY1Z2dXNrYW1oejJmdmF6c09VaDVXQ2w5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578557),
('n2njymW0lcPotJMejf1L4gBjWlNHJGL57zAVjScK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxNnd5TEZKdXBLT2pEVlYxYzNsQ095YmZiaE56bk52NU9ZMGVOeGtzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('xFG9gbqu0438phyKv9navWmwJ50vqzC458mkSVE1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHZVhsVHRqSHBQTTV1eFc0V0h4anl0c2pUNGRBWVAwU2NMcHppN3B1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('BNFQ0jGKdtn4EohPJ17k1dL8KPN8J6LGdIPj2otB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtNWxKeXdDSEk5T1pjMG5XbkMwVGZLUk5jbEpaYkNBbjczU2RSRVdkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('MOsDkTdGo8oL5jb0LjM7kTeMRfch3hW3UecEoAJh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkcHZScFRyMEJjcVlJNDNNcDdwRHYzYTJNNkhSOXJXMnhnVkJ2SEZrIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('KdHoCTfxErZJDwEvpII4DQ6u2ZLX0JoW71lJH2Zi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJWaVM3YnNndUF6UXViOG0zWnVYOVJoMkV6dnk4bzIyTVl0VERGVlNtIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('8O7JfFK90DMwWBaX2q9ZqC1VOABKMmva8eIIJybZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOVjREZ0V4NFB5S1JHaERqUUZjOEdvV3FUS2V3WjRYUWxUb1lXbElWIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('06C3I0isZiaFn8JaO8li35sgjCVswCZzsPvEfyMM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoVTlZVWtHRFpGOUtDTlpFam9LNWpUTTVSOG9zc01rS1V4NjhOOFdMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('fA9hic9LYaEvxeDDgV6GQsaBSH5KxttadQ45uvBn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTTFo0dTQ0ZU9HUW1yMXAwTzRvTnlTNHQ2MzkyVktiZ09pUUJPSXBRIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578558),
('LC7vQPRcfo5ZYzurHRtGLedtWneE4IYsCfRmw2DN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJqQW9Wbk0zYmtLazhxeTM4ZVNid2R1MGllM2dwT2dtMG9icUkzbUNEIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('8zMOF6qBtp1cLiqhGiPbSVKFdfmz1GruFt9WiOSE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIxY2h2aGt6TEVqVnRRU3BVeEZGWDJxWDQ3TnhhODJxYmY4UHFvZ3FJIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('NVjoI2968IFtAGrAJS0A3DGlWVpd2mQp7eFcMyXA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJCNTJIaUZDSFl3dmUwOUs4WjdtYlBCeWR6c0hoUUxueW1VdFlBWWY5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('N5Zf1arOeYjSBdQkfv8h3vuFuirZ3UUC8CZqC0dr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKWHhqOW05RWd2NWo5b3E1NDJ1OXVqM3RRZ0dJc2VZU05hdnJsVExMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('EgWt04VMr2SaEE5K5mI3H6uk84NoHnKicPoQMeAw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJuNmNoSlhxR0VVWXg2OW9WZ1lTZml1VFQ3RzlLdG1tRmFYZ0VsVUJkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('a7oOGokYnm0jkbKG7zrf1huU4EMgeJVsB2MRHR0U', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGOUdTeE9nRnBUb1ZFSXpiR2xxYXVHVmNJUWhZTENKblJYODBYWXdXIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578559),
('0qiB8Zbkx75u7DoMU0GL5rCnqM60tMEiLtOB5Dtj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzVmI0d2hVTElvRlc4RUhKSUFleEtQak9leGthY0Nsa21tUjFDZTNWIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('Tu8H1IJZyfE8Un55tGNPjmV9lSosH2euJFJqRCM7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJkSXVXNEt0ZzQ2Rkt4aW8wdHZpckg5SEcxWkZLd2VRNTczM0hVQkVBIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('JXyks3y3WZblUsyICbDr3D8aN1PbGKyO6Y9HLTZw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ2Z2lldFZ3REdIQ0lHYXNGczJNV0drQkY5U2M5dHJxVkJoNlIzNUN3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('Hppkn0ub5mQkpgdyPfrE4u3fG3xexxADkfK9warh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJRRGVabUJQV2xrUm1PVWE4ZldQSTdHQWVNVjhLSnlZM0hDOW0xMEFVIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('BzjzSs6Ewzlgk7iOb99STiZyHP8iIJhNGAXWR13t', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJya042TTdLckc5R2tJT2NGMUJMN0R0b2J0QVRFNE15b2VVSG9NMEgwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('UkwJZ0GXJgcxmJhLSI8eqqd0InxjRmXLedYVL51s', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpeTFjWUdKYXdDNGRBMnU2dllYOTBHY2JSeFJTVWpNQ3JMa3Jzb0xGIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('5S8ppbFTo2X6j1cBHWr8x5k4T2Wh7Qyh4vt5A6mn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOenhwdE41SmxxYW53b1R3YXA5cGh0eU1nYjNFU1lKeEFNbUJrVXpzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578560),
('7yWSRPQEBVbN3N97atp6zs1UFi2R0yobf8mSq2Ca', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJRNm5YdllkTmZZRDM3T2E5TkMzbTdwQlhJTExLNW9xMERlaFRUNDlSIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('tWfw07nydebWi7AHLxmNy5HKV53tALsBKmHnM47M', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOY05pcXYwbkN6a1FDV0RqZ2cyenJiTXdUd0F1d3ZTSG94TWtaVExKIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('4nYl37i6z3vQVlJ1ZqdyW2IguHM0Bs6S9SByRDs2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ1cm1uZE1Rd1RzaVptU2NqeWpvN1dlem5zZExBYTd3Sk1TTnRTelFUIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('EFO3xanyB3Vbo4FMhOz7uCE3j0o8TbOTO6pQqKie', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJrRXYycDl0MWd2QWJzdlZSam45djNDVXFTN2MzUDZBYW9LT1Npem9YIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('wzR8YeU6z6oaZGs2j5WPmsKtBHo7GoDk6Bd9xC3G', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTZFpJTUk3UENpZmNsN05DMlJHUU8xeXI5cGJwUnlSY1ZyT282UXRyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('ukMRTGzni8LozqussPu9eZRIpBVWGScieFGal3of', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJrMXlzQXF6Y050RXBsT2k5aDI5Y2pDVEtTOE5zdklqWlB4TGlab3BOIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('9XEM0eZIbdQCFt7SdTvciomk92myQh7ObosH5aFo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJXVDlXRzNPSjN2dlJVb0JUdnlISE5KcTBVSHppYjBMaVJLQXB3bWtvIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578561),
('z1S0o7XHKt1ehJfJ1PbNnrIB6tZyjBKZTg0uwDSs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJWS1NSU2JZU3lVajlMTGZ5SGsyaFdwdUtoS3hFM0gxc1BUMEZJeTFzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('RWl4aX8SIDDNUpswCecZgeLHCuaL1c9IyMiKSTmb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ6bXhLdDVaTmtwTDRoenkwQjJ3WVRvU2pMVFo1YTRzaExUMVhKaXF2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('AM7d3FfbkWyxXRNlGhbVhI41vHNKO8Hhtq39kOja', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJvSlVsUXYzV1lPOWlKWnRMT0w1QUpmZEo2ZWw0UHZpWU9FZVhRMFhnIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('2KUauwdKT10P63r5074tI3UDOM7jADWTzxbv76gI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHN1N4TzlmN0lWM2kyRGxFZ0J2SEdPS085aU93SkU0UWNVVllrWk9yIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('InB9uYadQdWhRFS2zEaCyLn8jjBP6bNIvOjwId8N', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJQY01SeHg0NFY0SXY0bkxWR2NMbFZwZEFMUGphNWdiek9RUGhhUkJDIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('eCZei127CUGWAMNdj7OlMLR2J8EgjVbPKBhe8wQx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJYeEpzRnVFMklPQXpONDEzWHZzSVJteW1sSmhvRWNOME8yODBxOFFyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('R4lEzJxwxaQ6PS5N3q2HenENuVeN7wH643Gqt0Wn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJQTVZLMWpBbktaS0MxTDRLZEh0ZXZsVFB5WXc0OTB1TWE3TEdJQ2t5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('YVPlsKZrEF6GbNmKaXs1j8loN40Yk5SrEAWVPGa6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJVV1lJTTlkWmpUaTFEM2dlQ3lCTEVUUU9ISHhYcXBlbkUzenc1YXAwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578562),
('VNiATtMIQkQBeX0JM23iRyEtfYDzw7RiVKzqulja', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJueXF6Q2dhTjJkNHdxdW9Ta2hwWGlpNjNORzlWa2pYOXA0M3FzUk50IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('CfYyfqNvgLal5QRUrxIDwVog8mD73FZzqZyoY2BZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIyNkdCcUdHZFBkWVRPYXhvZ1VsdzFCN2lrSWNUMFBjMzhlemFnVnBhIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('63yYl2LisnTRvvu6cGeGBqxWCPJMy9k6aRIYMZ5c', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJrNjR4cXN1dVJwNGE0YmNMa0FjVVlRQkZlQTVzR09UcXhSR1VuVWpWIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('ZcIjs980WTuYeCGgT1isCl86527w8zeC93enTufy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJySmhOdXVqQkVLRXBudzBTRGpFZlZiUjlYTjhwaFpZWDVwQ1l5OGd5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('0dXjQuM228XLGtvcRP7NTd9V0uJIRYHDhotxRr7N', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJiTWRyaUZsOXZsOVZWVENZMU9CS2czcUluUlR3M1NualMwdnpBSlN0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('vY7FbdAgOmghnUuaEwiBVHS7KQ9UssA2S72XvVg9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJma2VzZ0YxVWk2RU43dE1RZzkxY25tUTNjSkRjSDVobXkzaWZkYjBkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('H3DspV7t31UaWzNE1x8Om3m9zaFrf8lEfWzfeGWy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhR2V5MnUxVUlpa1FZNGNVdDdHZXhtOGJDZjliTVlXZWVCVkJ3U1ZZIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('ADLhMYmFAHjqHgnoHIpgqnsDItCO5vJji8Hee6KD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2d3VOT3Z6OUpsSnY0dTFUSnhZSENRM1FQR3psWkZicDJYZ3E4UXJQIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578563),
('YcXFhalN6u5SSfnDI90o8k3L47rsIjSJMgCoMOIz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOcEFyZXR6OFYwVkZmUG5jbUVYaGVJSE1Vd3RnSk5UN2F1Y2dpdTlsIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578564),
('jXTkyya7lSudCnrNhRCozPRxiBXGn5sYVgVEtuKy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJZQlU5T3hpVkxjc0g3N2hibXMxNjczbjhzQTJ2RnNNT3BRbU5WT2o5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578564),
('515XpD25Klg5NeVEPlWhmlDEXwshEwcLd870ge7b', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJLbEJ2V2JDNXg0cUVZZDhPRXNDSGx1MExNUnVvNmNuMmFUTFVXT3NZIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578564),
('rtIsIhS52T1j2WPgDotOzW21iya89cmPttb7K3Mi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGWUE4UXNHVVFVTk5ndVFaQW1waEZwUGcxdFJ2c2ZwcXAyOXFabm5OIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578564),
('CsaIvm6vxnp1upnpGmjysHcDJ5IsEV7MjQm8ZyI3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHY3k1RE0yZXFrRGNadGFPUmtyZVpJWU9BOHZ0SUFyUm0wdUpPcHptIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578564),
('dRRQfvg1c85KNDbEAP60H2XMkVOGiag2i8fuGo3w', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzMlJtaUNCN0R4Zm5Cb3lmVlkyaGM2N3BzdmpWZ25mMUZiZVR2VlAwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('5paoorxG0GyGFLeBLsste4wSJjfzJWD6A9hOqWxE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSMHdxRUYzTTdmbnMyaWZSOXpkdURHNDNBcHhWMGhuR2c3QW9MRW0zIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('2ZMHUMaFQv04B5RjgK8BV1Baq87fOlHejoOTrhhB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJrNnB0bEVEbVFIN0dOb2QxWUpiRFVkTjN4eWtKYVAyZ1owaWF6WkVCIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('nraYA33Ap8TKeESlPHa8clY13qS7Z7PyCVnBhYlq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKZ3h4NWxjR2hYM0kzbnRHa2hZcFdHS0dwdHpjUldjSEVobjdZT0FMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('HnlkGxU8usR0BO6vngEKV4lulpGzYqfyuBKd4Hci', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJIdUZTVjRXcTJIdURIWnp4WlMwWmpwNTF2WW9jZXZnUlhlWml2NTViIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('GxLuXmFfgFKzY9xp0Ib6YyjqCLJoBbKlZG0VcNji', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIyOWN4cWVUQzNOckFFSk15dzRRUkpNVGRPbDdBamd0V0VrT3JJeEx3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('PfnZa752O08p8azpEFfGDvUU91sWt7D5EUjtDxLZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJnOTZxT3NXZ3NnWlVoWGVTYUJNa3k2SGdXMHJrQ1VpYXRuUUplRDNMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('X1VP4lBlBUyMZqZUu9IkOf5GRjzRQ841HFJ0VMne', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJveGxHeklPakpBVG5uazNiZ25zSlI1RlJuQTl1bFYxekt2V2xFSFRlIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578565),
('R8yXYusYPbnjyC4FiGSYS57pG8N482bg426XeN5j', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzeEtnNEpYeGVOemRwcDlVUHl1TTdGVG9MbENPZ0R1NnVTTmUxU1huIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578566),
('pkUE2917KyTCa6HLGiiOxCwDZPDX2410xoI5q1wf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJnVjc5OFdnRjNPVWdpSVQzSFptcGFvSTVnMmFVb09FZ2Jjc2JVMWl0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578566),
('KJovyJQeYxXNAUevnrbR9kQKTVX5rZOvX0l9oQny', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI3UXNkbkVpQ3dQbFd2cWlDR0ROUGtqaW42M29ETWwwR0hOVGs5U3VwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578566),
('PUcFKBW8Q6fXp3lxpa8SRyihrfOpNYHG1zkOeYXd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI1REJERzIxVklnam9TVGlGMUMwc2NoWnQ4QU9MU0Fmd1M5SllZaXRsIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578566),
('rQ70svdHE1l2hmjdYUVZfFbQrUac5uxbZWH7hLQt', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI5d3BRNkRTSFNNVFpuMHo0cnNFZHY5ZkFMcXRmSzQ2V0dCR1BDM1FRIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578567),
('3ESvD1StTJ7yKqKUgJczxKsvdNy7YBSHq58oQ3UE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJZR3VycUEyeU4yalV6SnVnT3BHYzFzUzVkZ0Mwd2xXN0p5SmVxOHhYIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578567),
('8AD389QRDaCcfQ9WKbr5hVphsDukmScMeSUBZeXY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFS3BSbXhKTWdGVlVtRkR3MjBlSnRTa2Q1VHBJSktRN1J2ZlpxaVNUIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578567),
('XozklQRorSPgdToOD8O9nGuqG2fAUyxV16Y09Xt3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJaNHEzN0ZOTmRsdmRqTmdKYVJZU3A0OWhBNGxsekFKR0MySG0xQWJnIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578567),
('xxwcXpzyEB7gvcK64tVRJhAvAUFhjPx4WahkwVni', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTZlFEdmozV2xiZmpWckxJbFpvUmhJMkV4MWlmeXhMM2xuUEtaYWswIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794578567);

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `show_bookings`
--

INSERT INTO `show_bookings` (`id`, `show_id`, `event_id`, `user_id`, `customer_name`, `room_id`, `seat_number`, `ticket_type`, `amount_paid`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, 'Koffi Konan Alfred', NULL, 'S-2', 'honneurs', 100000.00, 'confirmed', '2026-11-12 09:40:13', '2026-11-12 09:40:13');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@hotel.com', NULL, '$2y$12$YcLsejkb2dqmwyqT6WSiz.lNXIr2bJ7g.T1jQKjOhnJhBvSHgvADO', NULL, '2026-10-31 09:07:47', '2026-11-11 09:03:14'),
(2, 'Koné Nafata', 'nafie@gmail.com', NULL, '$2y$12$4Myw/DtrcLpVVZgkD5H0E.g2S5prQBZcQXP3sQTD/kEovA8sGdT0i', NULL, '2026-11-09 11:18:06', '2026-11-09 11:18:06');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
