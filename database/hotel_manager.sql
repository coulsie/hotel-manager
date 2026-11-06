-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 06 nov. 2026 à 12:08
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billings`
--

INSERT INTO `billings` (`id`, `receipt_number`, `room_id`, `order_id`, `event_ticket_id`, `service_type`, `amount_paid`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(7, 'REC-20261104-0003', 1, 1, NULL, 'restaurant', 18500.00, 'cash', 'completed', '2026-11-04 15:59:48', '2026-11-04 15:59:48'),
(6, 'REC-20261104-0002', 2, NULL, NULL, 'room_booking', 125000.00, 'cash', 'completed', '2026-11-04 15:05:01', '2026-11-04 15:05:01'),
(5, 'REC-20261104-0001', 1, NULL, NULL, 'room_booking', 30000.00, 'cash', 'completed', '2026-11-04 14:52:48', '2026-11-04 14:52:48'),
(8, 'REC-20261104-0004', 1, NULL, NULL, 'room_booking', 78500.00, 'cash', 'completed', '2026-11-04 16:07:49', '2026-11-04 16:07:49');

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
  `id_card_number` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `phone`, `id_card_number`, `created_at`, `updated_at`) VALUES
(1, 'COULIBALY YOUSSEF', '0143677424', 'CNI 123333', '2026-11-04 10:48:04', '2026-11-04 10:48:04'),
(2, 'KONE YAYA', '0405021326', 'cni21022', '2026-11-04 11:26:29', '2026-11-04 11:26:29'),
(3, 'KONE NAFATA', '0102034568', 'CNI 24513578925', '2026-11-04 14:08:50', '2026-11-04 14:08:50'),
(4, 'COULIBALY YOUSSEF', '0143677424', 'CNI 123333', '2026-11-04 14:52:48', '2026-11-04 14:52:48'),
(5, 'KONE YAYA', '0405021326', 'cni21022', '2026-11-04 15:05:01', '2026-11-04 15:05:01');

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
(3, 1, 'concert Ismael Isaac', 'cvbbg', '2026-11-28 16:40:00', '2026-11-02 11:40:54', '2026-11-02 11:40:54');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_tickets`
--

INSERT INTO `event_tickets` (`id`, `event_id`, `ticket_type`, `total_capacity`, `available_seats`, `price`, `created_at`, `updated_at`) VALUES
(3, 2, 'populaire', 3000, 3000, 5000.00, '2026-11-02 14:06:49', '2026-11-02 14:06:49'),
(2, 3, 'populaire', 1000, 1000, 5000.00, '2026-11-02 11:40:54', '2026-11-02 11:40:54'),
(4, 2, 'vip', 1000, 1000, 10000.00, '2026-11-02 14:06:49', '2026-11-02 14:06:49');

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `magnetic_cards`
--

INSERT INTO `magnetic_cards` (`id`, `card_uid`, `security_token`, `room_id`, `duration_days`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'RFID_0248D9D1', '5alAJLosCoKfCHgxpaVaVpjLKoCwQH7P', 1, 2, '2026-11-06 12:00:00', 1, '2026-11-04 14:52:48', '2026-11-04 14:52:48'),
(8, 'RFID_BEE58BDA', 'G1ll7iBr0OMLx0cd6qarOA9Qe4aKVjty', 2, 5, '2026-11-09 12:00:00', 1, '2026-11-04 15:05:01', '2026-11-04 15:05:01');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `location_type`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Garba de Luxe au Thon', 'Attiéké local avec darne de thon frit, piments et oignons frais.', 3500.00, 'restaurant', 1, '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 'Cocktail l\'Abidjanaise', 'Mélange rafraîchissant à base de fruits exotiques et de liqueur locale.', 5000.00, 'bar_piscine', 1, '2026-10-31 09:07:47', '2026-10-31 09:07:47');

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(20, '2026_11_04_142545_add_duration_days_to_magnetic_cards_table', 5);

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `restaurant_table_id`, `room_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 18500.00, 'paid', '2026-11-04 15:56:37', '2026-11-04 15:59:48');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price_at_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 3500.00, '2026-11-04 15:56:37', '2026-11-04 15:56:37'),
(2, 1, 2, 3, 5000.00, '2026-11-04 15:56:37', '2026-11-04 15:56:37');

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rooms`
--

INSERT INTO `rooms` (`id`, `number`, `type`, `status`, `price_per_night`, `created_at`, `updated_at`) VALUES
(1, 'Chambre 101', 'Standard', 'occupied', 45000.00, '2026-10-31 09:07:47', '2026-11-04 14:52:48'),
(2, 'Chambre 102', 'Luxe', 'occupied', 75000.00, '2026-10-31 09:07:47', '2026-11-04 15:05:01'),
(3, 'Chambre 103', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(4, 'Chambre 104', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(5, 'Chambre 105', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(6, 'Chambre 106', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(7, 'Chambre 107', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(8, 'Chambre 108', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(9, 'Chambre 109', 'Standard', 'available', 45000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57'),
(10, 'Chambre 110', 'Luxe', 'available', 75000.00, '2026-10-31 09:07:47', '2026-11-04 14:49:57');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `room_access_logs`
--

INSERT INTO `room_access_logs` (`id`, `room_id`, `magnetic_card_id`, `action`, `scanned_at`) VALUES
(1, 4, 1, 'access_granted', '2026-10-31 08:52:47'),
(2, 2, NULL, 'access_denied', '2026-10-31 08:37:47');

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
('7sisc1DK8gDstzBhP6H9FNoUuzPewmwZ2MmBL2nb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI0UVZvdVNmeGF0Wk9HUVdDUWtMVE0wbGo4VDJNd1VVNjdjNkRmZGxHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957553),
('5biq7XYRBsemGOXRvTVHVZyw7oyfbabYO24Exoq4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpNVNZRXdCYWh4dFcwOTlqTmpJZGZsQUFMRm96MDZ2VW9xNjBUMHR2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552),
('XFf0WPBObqMzSYZstKNPIhAZAh6PQJmWqK2aW0FA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJwbVRpUUo1anNFWjBtVXJlZGFzQlNvM0FBcng1cUlSall2d0ExQ0tsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('EEL2hgsuquhmcPa0sUZjg2WL5iLqwzyrmJF0frli', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhWlZrVnk5dzRZNDlFT0dSaXVONW94TUlNb21MeHJON3A0Q1p4blV1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('NDmCW4nZBFKdgAsL4vZwbUN3A7so7DphpfSFMGA0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI4OEtHbWNIYWdiZ3RnVFJBUjFnS09FSHlheDBLenpvb0pXNG0zZGtIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('vk9Xb6ICMM04Ry9BXfLD7eN9TpCEmoSvb4iVFDGg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFdnhrMTVPamhka3FtS3Z1djVxMnpJbXdUUGJQYld3QVdBZDJISFg3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('l73gYZCVM2LeBmnwPdOiKaqrEOcInsyi2G9VBcEd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhVGhreWxrUnlKZ0ZPMEUwUG0za1V1SllHZm1aVVVWbndKZGRmVUtxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957526),
('qbF7KdTZrocbeUAB6GB7zoRjDd3qz0oyI08qzAe2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJBbHBIcnFZVGZXb3Vyc0I1OE01cHZQZ0lZOGR3ZTAxVHFORWJvSHVXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957527),
('GXkxqG3kLtelijm4qd3D0AUgP3ZEsJrSbpGeLHuY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxTzNpMm5oV2Fra1RRMWh5RUFrWHhXMmRocTN5REFXVHYyN3VjOTlyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957527),
('0KooLZKZEylhwPdqIQWn7URftYWM9SjaVDMYLmyw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIwT25Ud0lnOUQ0RGhaYTJhV2laU1oyTmxGNTZmbFpGWEVvZE9VS3U2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957527),
('wtKqdaSSo8qaHGIeRbSNFHye0wOpS4a3wsPmcOaw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI5UkJjOTRUSEtBcXVtQnlEMmtFcUR3VUJReFZRbVN6RTBPd2pQNnBhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957530),
('Fqov4wsqo47g7JkKOJAxMikaLwULkRBJZXy2MGnv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJYVnJzRktjVHRvcUl3VVh4QkJhU2dNVWh4M2JyMzNuc2dxeDRBWFhpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957530),
('bDa4MFZQ7TNW7P0GALGOPKqq5KUIqS3iTcZTf2LC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJUT2FPeUVEUW05alVLNkk1THBnekFFdHE3b1c3OTdOcjFsS2gyZjRsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957530),
('iLAt9hJwjTuCg34UyQFJvKbD0JMH2qLwcHohRdHu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ4UmluRURBcWVyVmdsQnphWW91dzRFWkpSUjdxd084ZElOeTQ2alprIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957531),
('gwaATqArrz6G6QhWPsgfiv9sXrNmyUeYqblllRD1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIycG0yZEF0dE5Gckk1d2RaTkxTOW9pMTdLUU8wM2RBb1R1UHN3V0tZIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957533),
('QfipFxaYXsKtdNRzVxoB1E6DduDSOgovSXS4If0V', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIxYWZSMG5VY2tMeGxwTFZ0bWQzRXRWVHhoeklNSjVxdVRWSDE3eGRvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957533),
('EULwQClZ0csNMZPy3mu9zzjNqfg5JG3duNMdjpkr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJIbXlPdG9WemtMMDhQMVcyb2RQZ0FoSTN5TTlFMUVqVURuTk13RThvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957533),
('h477w0A4q9SpnqpcnkRcxsqD7kcnnJ87j3LSWGbL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJzMlFWdW5mM2t0WTVJOThUa2NDNFJ4aDBKN1BZcFhMWlJSUE9wQlB5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957534),
('3bmbWyyafjJxXiUZUMJtbteNyXMKsatuN8Ud55Um', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoZmR3U0I5SVNvSjBXU25uRHdQNGNHS3BYWWhxdFRPUUFtdDRjbWpjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957535),
('Z0Q6mk8WWeeeTnrXFWDNgrH7pPP1KQCIzwf6tm0v', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ6NWIydlV0c2hUZzhucExTaUF0WEo3T3pXazY1RDNZcjNDanMzaUd0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957535),
('jE2AcW3riaeCRbD1Y821A7J5OKhkYMJNv7s5p8cL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJBM1RVNVloWFd1cnVyRkc5Z0FtcnRnMlRrMkV5Znc3ZWwwb2R5OWNpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957536),
('8MdiHOjIPbKEROZ6o0qf8UA6VoeSmYs0QqWhnRKC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGRW9PN3NFakcwNnVGZ045MWZIV1M1cVlwbnVtNzkya1V2a1R0NjZVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957536),
('W0jM2TmTVhoclLOZKYBSemGzQ9pl6LLFLzvXeREV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJscXRjUkFUQ1lBSXZlczdBQloyM1RObHpBcmtOSFdrRktuUUxXek9WIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957536),
('W90P9uIeUaRIg6dLBJoISFuDpfhRh4cDrJuxuavX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJOR2M3ak03blBtMFA5Z1VFcDRRNU1Db0ExcEVrckE5clkxMk13ZmljIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957536),
('cGISQ1r63obq9RbHvsHFpsiLhsVWRQl81N9AdLEt', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJhelZ1cG1KeThTR0RrRjQxYTFUekt5a1diWFdwb3d3bkxRaFZlTk16IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957537),
('fWhKFuwVOxZFzj6zvh052ue9rB6KbkrtOEyxuqZe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJaNTFUNVJib1hBMk9jQ2pna2x5RERwcVlTeGx0b0ZOSXZPYTNyVHM1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957537),
('tj7JlQn6KBJsalsWQMCBZhhceQIMtPmYc1yiN7DF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJUMWhhcEFaTVloQjI4RlQ2T3FLNm1ia3VmcThPQzNldDI3c2plQVJBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('RB4Ts3SkNMA7RJImi5iKGIgV4HNnnBOPq8Ke3I4h', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHZWVCMFpVazUza1lJUFpTa1NuMFFiMDZlRG5TZWZmVGRqaXZoSk9RIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('jNYoXDLdSQWA1Z0wsErFsd3byWumNozw1wuh0sDI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIycndpOElHN2V6TkRtMVJudkwycDh0MFlOSHRjOXlsMWcxcTlWYW1BIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('wNJtnEd9ENVTdIop5G9eQwecV79gH7oPEEDx3Ial', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIwY203U0NxanlDckVEa3BmVWdtY3pEdElsS2duSnZ2eTZySzlBeFRaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('AUa1QP6c4CFu0phEMIG5oyGV70VgX8cY6P1VITKq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSZVFqMHQzY1pPZ0kxTXRVZmxqeUh3UXV3N2I5TWFLejV3VEkyRWxWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('7368YxglzGxkdSAW44dz4VBfjSrJtl1NkXWBFXYv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIwZWIzM2J4dEMwcE5DV1FLZHk1cUhWUjN2YWhQSmk0M0haUkltcGZ1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('pNyrxKeA8Yor1fQOp2127ZmUoq7Jwh3vm9oY5sYT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJzZDJ4YjZEdFBEMlBzclV1Q1BPbnNTRk5uZkJCZ2F2RXlXSVNPNGs5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('JpEDwMPvifkqUA3BnAeoOh1oHl2HLoSoPI6wSf4z', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoc2NoOHJiYjdlUlp6Z1dKNjY2bXgzV3d1Tzc4c0Z1aDMwSEV6Q3lHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957538),
('pteZnbNzvnrY0DEqWR31mypWw8z0HiwyutXr1W4I', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJicWpxUmZmQ0ZVQkN1aEJYYmlJeXRZUGk3UUtlVmFDekZzS21FWWdXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('f5Vndz2mqHgoIos5Diia32xDvMEhHFxeawoNjeCp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmZ0ZiVGw3ZUlEejRnclg4N3JMeVZJeEZXQ2VSRjZRVWdiUDNSRFQyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('sMZ1hJyonIRpC0RBFXz444vacpXTPa7TjZVRBIwJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJwT3lBblVzZHBpc1VJVTBBWTRpZzdXbTVSNkt5WVNKbDlyV0EyV1ZxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('P7MmQ8B7vtV6lmjlJBV15UKM50npPXDOZ7AHTSJK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ6NXRyRzFIMXRyR1VZNE83ZEZqUGQzZlNZbnlkUWVaN3dMWFNxQm9rIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('iPTczqBkVfd3M1Ia9L44UdeOUdydKDyGBJ9DXHo8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI5MjFjVmZaTm5TN3NoN2JGR2hZVFZhOEF3YXdkeHRVbk55NDBDcktDIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('CLdmMPVPjvuwdyoA20Z2Zui4s3ta2tTgJzy2GMXw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJvakJBNWJkVXA5OHZoTVY5UW1QWnhPbjNkZU51NXRHS3hoRlp1UXpJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('RgAWh1lCvwJiGlsc4jYqbyWUqoejwtavhkLu4caw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJneGF6Q1pBQ3h2WTAxelNGeG9tTmdvZ2xQS213UHhCU25oQVJPOGg3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957539),
('wp83b7bWff40EhIk2tke31sCCe0GNQ2efJd1bi17', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoTFRVNTYxSkJ5ZE9yZFlrVVdIcEJWdGNXZG1jUW9hOFIwcGp2bUJ3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('vlWafPFthApL1lj4zetycuo3FJ9pRhgkYLtM8cR8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI1aUpzREhyWGlhd1hxRkVzaUJIaERIMDhQZExtU2lUUnd2dFhyVXpMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('gqSiJxSR2d6dB3WNhU1caKA2Q7CNi74q0IR0L1my', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIzcHRSRkZ5SmRrSWh2S1J2WWNCSzR2clJycEU2QW9nR3lhT3A3ejh3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('3trEeZzaGNY1uOZT7u9JLaXtf5Y7SGWMlWfk4HFG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxaHF5S2dXaTZYSmFIa0JqeVptQldGT2kzRm5UNjVpc255UFBaUkFjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('M4HJKD2ZvxY7M4rgYqD030mFMxcLYfWVxvxF1X7I', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJBMmhOWnFWOGN6TldYbVhxRGI3bjZsNFh3YzVxWWd3QlVuVlV4ZUhYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('BUCRg0wzlurhzTlLXeqFGfjTK8YrEsdd6srt9Sjt', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ3eW5PNkNKUXlIRmRNWnR2WTk2RHRVYUtrTXBWTnJOakJLWE1xS3A1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957540),
('zPlivCcc9gj10r2AR5vRKZCykkNlOXJNnpec9IR2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJnSVVmdDVkMnp0UlFqWGIxVkVpNmpJZTFYeW01Q1VObnZ5VWZwblY4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('hcSzhyETfWvp326SUyFSNAAGOkjCTcBH0Dyzd3B9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ0Zk5JNjhMZzl1VEhlcThyMlRYdlZ3TzB1QVZZQ2lxSGJoNmVxbk9KIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('0XGz9OkIt5TBzAzpldhvJRGTiLRMP2RwvvNY9Iq1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNRUM0dnNadWVOMkVMSkw3SkRld0N3MDJYQzJnUElxWW81M1M3T1pDIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('A4uy1fYv32U7DXLYdZc3Op34meyYvoxwYahwMo8W', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJtZHFBb2ZMRHdBeFpLd0Fla3JQWU5obWZSaHdHdDhnRThNREpGVlVTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('1UqylVoGv4ivXaIiVS1e8Ysiich521QpkkVBn7Mc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2TFVFZ3FHREFONFFaVHJ1WTlmeG5EMkphWHpxVXBZOGlURUVEZkNhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('8oKkDl21tgEk56klswsYwqBUZTxlsnXMg7rBZNN2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI4TkNLNzBKN1REbVhVR1RxcGRvd3JtYXBJZE5adnJRS2JzRGswY3ZJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('pO1idr6Qon3JJ08eU3tylIdDWQSEIZ8UzyQNk4Ne', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJGaEdtRUYyeGlEWHUxOFFYZ2FRYWRaNTEzSGVWbjZoZlB4TU9Tam45IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957541),
('4jaPSicm0bTyGWk1JbSnhn3i4NOWQnwpHdqxSv3I', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJYRDhROWQxVWl4NTNJalhScjV0OTBOcUM2Z2NsQldNUHF5aGtjblJWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('exr4X3TMHPvbOcjMMFr9gflEsO3gMRXt8k5zqKU2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJsRHhrVFp2ODVvcXFQU0I3djhDbnFBcFg3ZWZkNWExdzZ4clBTWExmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('V4G6J7KmVCgOebdGfppX6EARllVKplnknDQby1Ar', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJNUUg4U1psSlRYc21qUHJYNFZocXNLQ3NUVFJMczBiZ0FCelYwWWlXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('QWeF9opJKrmdHlZwd5ArKn3S7iSq2kBAm5DSWIbr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ6OUxETmJUbjdpeW1JclptNEFiVWdRWDI2cmk4YzJpREwzckpGQ0dlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('kmRhUR0Tb5UbFwoNZyst9qtVAF20ptiir1up4tOr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiIxQ3AyR0cwTmRqaUxIWE41YjV3cFZqQk5qU3VlQURHV05HQUMxYTZnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('4qtG01U3NK60ISaWyyGeAC1ACjhTE5aL8LFthX2q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTN3BWS0prZWlOenhhazZRcU41TG1EeHpibllnVkYyWmViWFhoOVhHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('v3rlzuEfcp2Mm0LlzMPqnImwsqb7cDsRTNV50YKU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJHRklYWFFJZGVUWTh0cWFiRXNTRzZha2l0aWdETElycHprOUhlZ2tsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957542),
('FYKWEv4foE9tubH3e35G0q9HXEUz4eBPDvt2fs6f', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ3TG9NcGFaS1pyVzFHUDR5SVFsRFFhYnFKT2tNaW80OVhna0ZHa2oxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('nGxuTPbMnApFpdzvNOAmeX11ZpLXF10VbKb2LCrk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJiYWF6VW9iMDRsVFY5UlRHZnhNcGdBblV0a0FzUmJ2d25CMFBPVGpaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('XxnxA0QrldyPt6Fzoccy4uVSmbV8aVNachlM3MFO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJvcEc5MnhsNkJWbG5zSEZpa3RVbmJheG5nQUJydnhuRGsycGtZTXhIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('3u89FVDSpQwEoXrLyGAxSbwFKj24IRBRQ9c2rFIv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJzU1BRYVdIOVpkN3JkTnZlMFdOWlZNdXlMcm05a2liSFB1OU1Gd3ZUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('xQCXKF9owmY5FEevsXQD3eN90eY05GS4CenVe6jz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ3bzV4T3lIMWtiRGJhR1FFTUNsM1hwNTJFYXRVVWM3UXp6NmtuQzd0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('sKCgF6aPTqIOKDWyRJmCGxkGiOCmXGbvyDGs1ii4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxbnVHaDRYS0hRWlZQV2dsb3d6SDVzTlJGaVVoWURqb1padHJBZmM0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('wv8XnjiKT6KC2YcT4rd3hhfTe1vXYym0DkfPSbZl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJxbzVsSXZaTXU0TFhVcGVpYzF0dGt0bWV0RjBaNGd6YlpOZFlOcklPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957543),
('pK2lrkQ6AkJoVhMzot965fmZyfL7htzwGCq0CgMF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJZYTZyZnVIUU1EdzFJYWx3RDduNHhkTnU5cjhtRzhaZmZuV2Z3Y2ZsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('rHwr6B8actI85N5va9KchENVgnHaFfpm4ooik6z9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJWVWYxYnlvTDdZZ3MwT1JlVWs2dG12a1BHNFhpYlhZSzl0OFQ0aGFvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('I21dYdhNPaLupHC2ZDpRDRlHTWoJRMsYxfWeAFCq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJUWkJVOEE1V0JYeGhSVUxod1RlT2R5T2tucVpUVHRMMzhpVnBJbEwwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957544),
('t9z57WoIbnYhe4uY640vUL2CHDwPzJ7ifu4Gp6Ix', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJyakVMQnlRZ3Zsb3p0TzZnYXd5ZHV5bkttcllESm9vM2ZEUHIwWmphIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('gXcx3TEcRtyU83GnHH4Qm0Ice8klzRTNMiN8ndYs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJzVnFOUHNVeWN4cVF2d2dKMDBjZHkyT0JoNm5PSXNoOHRrR0tFRWNBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('GeVgvTBH567G8kujqXRHVzYJBMAehWZz1o1Kmltk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpdGFCcjM0b2pqOXNpYVdSSjN4Ukt1RlB1WE5qazQ1QzlpamhjR1c3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('aznTSUnmruOKnIuH0pCI9tStiXhgVURI8MqbeVxx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJuUEhUOTFGbEpnQmY1alU4M0hnRmh1elpDNWVxVWlCU0g3YUF4MW8zIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('H5xXxPVBC8Y2mNv4Nx96X0N8XbhMDh5XIY9rvlNr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJ4V0hpcW1YbWc3dUVvMHVVZzdnVUNoREVUcFdSZEtCWXc3NVBUakt2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('EtlkLCH8ZjXraoZ2cli1pqu1lEIJLL7vlTqQmpDF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2ZjlSYkFDNGYxU1Z4MlVTMWN4dDZCVnNwUGtKeGYwREN6V3hrbDhSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957545),
('Q3nMcg91YlliJLx6mSCScv2IotEUaMo40t8Oh9z2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmdllkcWczSFRPdDVsNW1XeHpBd1pNVEY2ZnNtd3YwSnk3ZDVpblRmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957546),
('Ya0TffSguZq3wuo9hXDxauEKMIryAKH8s127ccGQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJqQ2c2QmZGQzBFbERMS0xRemhRcVZha0R3a2RSc082ZFdQeENtZW0xIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957549),
('fR9U78b3Bxrm1aTr9XkRKP9nyDxXEEenD4VPInRs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJJVjd4cGwxNlkwM2pYVjVaZnhNMVJwT3VuQm5neHVSQ0F0Rzd3eXhFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('tuOevGnpBnIhCL9qXFcqg6JeVuo3qTlpg8cCDyda', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJoSnNGRFZsRWs5clBiZnp2TjdVUHNWbWNKWEVOU3JSNUZGaWZHNGVmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('tUtLlqOAhp6PGBdk6RJiUiAeHclfgbwn5nUIx7MB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJFNDQ0c1VGeU0yS3NHWkpIRVNRdXFzenFndHd4SDhjNHBvY3BoN0JuIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('fusCpqPvl6C3XEknSgETL4gVA9nygEB02eb2vaxe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI1clpsUVNQNG1GQndsU3lPR1hiYlh5dFpIWHRlbmJQeUpuc3VRZ3BkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('OAxVCTamAl2G2tjckZ1WOveyVvYx0fxqTCdo2agw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJSQWlVSnpya1ozNlZnbTdvelNVRTJXbjBFdktPcGZPekk2UHZNRHU4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('kvg6ITh4b22n3ZUC8mZ3Kr8kIPH8v9GHnPPDmOeB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJITW9JQ0RXclRETGtmdTlhM3dGcTN3QjB2V3AyOWZ2VkF4cTNPWnVKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957550),
('tSfR3PRj5qRQGJB2NFuO30GB7K11QSiNNgFMAmvG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJTOEF5eXM2eEE4anRTejhKaTlscGdKZ0NlSllvcThDTTBMWlNtdWxJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('kAEokqKv89q2QV40PEKV2Emg3LgAbiym4fa7aiSP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJVT3ZPQjlJZG5uanR0SjVlb0tvRUg1a09pUTB3RWdpa01pNHZlZVlLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('JpcaljqgPlWPFtFpbs4Ui7nCzI7Zp9Jr02b9UD5V', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJENFFBbmRUNjdCZ1dLUXJPQUJCT1dURzFDUFM1bFZTSFBmakJnZkZjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('ehzyqHcuMxEudV74RmifAKMfLynn5anz1KjZcNj1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI2N1JDVnkxOFFBbm96TnJ6NFVuRlpNc3NSRklNWlZuY3MwbFFaWTFJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('uyR205emUfV5PTzYMu3xhjZY2fKjq5XMiD41Eyfw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJqV1BSRVZRZEVZcmJ1SFRoSjlKOHNGazMxTW56VmRmTmp6WkJuQWtoIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('pInoA2TVxOypbacFUsCzqYcEyP8mzwRjqjBzAdh0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJrOHFVUDVpOHNyVWpYU2ZhQTlKZG1KdHBrUG9Ba0FWQ2doMzNZbzlhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957551),
('RXnreZpe8L4hqmmn0mpxbtonFWHMPVo4JW9HM5gO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiI0T3FJTG1jVThSeEVvSFBmeUxXcnRxc3dJbTZiN0xMN2VFUVV0R3Q4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552),
('5kwVI0B6JEbkK4HTbAbpfVL5UPDVpZsD9aKubdt9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJpWjE5OGk3a0laOUQ2V01TNUFBUnVhS3djWmVRdFRjWmZiVDVCQUJ6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552),
('YcQC94qEF8PpfV8MILavbEqfNEpEwFXkbACAjVh5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJWcDJMaUVPSVNNTHJGNXgzU0ZnazJWYTF4SHhFT0NzU3dYdnA5U2tRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552),
('abuKSKTe88e54zn26BWhxOpNtmnhwlakiT5bub8G', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJmTWtOWlpzY0V3ZnlFU0ZwbXlBMm9UQm1JNmtCTXFSZGRYS2NhVVo3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552),
('M6EXngW5vE18YntSJVRrdPP3yWRgt2b9ax8GzTzv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36', 'eyJfdG9rZW4iOiJKV0VyWVJPc1pCTFZ3WjBnNUZVRGRSNEJ4VEtaNFpHbGVsUzRKemt1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1793957552);

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `shows`
--

INSERT INTO `shows` (`id`, `title`, `description`, `total_capacity`, `created_at`, `updated_at`) VALUES
(1, 'Grand Concert Concert d\'Ouverture', 'Spectacle musical en direct dans la grande salle de l\'hôtel.', 200, '2026-10-31 09:07:47', '2026-10-31 09:07:47'),
(2, 'Salle Ernesto Djédjé', 'En hommage au grand Artiste Ivoirien des années 80, père du Ziglibity', 5000, '2026-11-02 13:44:43', '2026-11-02 13:44:43');

-- --------------------------------------------------------

--
-- Structure de la table `show_bookings`
--

DROP TABLE IF EXISTS `show_bookings`;
CREATE TABLE IF NOT EXISTS `show_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `show_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
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
  KEY `show_bookings_room_id_foreign` (`room_id`)
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@hotel.com', NULL, '$2y$12$rK1JkPRz1gf/9oGJok2vY.EjWzQI2tCWBtz70x8sn0N8EGZV5J.hS', NULL, '2026-10-31 09:07:47', '2026-10-31 09:07:47');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
