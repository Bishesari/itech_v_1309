-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2026 at 06:04 AM
-- Server version: 13.0.1-MariaDB-log
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cgynaeet_1309`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `short_name` varchar(30) NOT NULL,
  `code` varchar(7) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `province_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(150) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `short_name`, `code`, `is_main`, `province_id`, `city_id`, `address`, `postal_code`, `phone`, `mobile`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'مرکزی بوشهر', 'BU001', 1, 7, 11, 'بوشهر، سنگی، اول گلخونه، ساختمان سیراف', '7514977863', '07733103350', '09350568163', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 'یک بوشهر', 'BU002', 0, 7, 11, 'بوشهر، میدان برج، پشت بیمارستان مادر و کودک', NULL, '07733103350', '09034336111', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `province_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `province_id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 8, 'تهران', 'tehran', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 8, 'ری', 'rey', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(3, 8, 'شمیرانات', 'shemiranat', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(4, 4, 'اصفهان', 'isfahan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(5, 4, 'کاشان', 'kashan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(6, 4, 'خمینی شهر', 'khomeyni-shahr', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(7, 17, 'شیراز', 'shiraz', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(8, 17, 'مرودشت', 'marvdasht', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(9, 25, 'رشت', 'rasht', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(10, 25, 'لاهیجان', 'lahijan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(11, 7, 'بوشهر', 'bushehr', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(12, 7, 'برازجان', 'borazjan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(13, 7, 'دشتستان', 'dashtestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(14, 7, 'کنگان', 'kangaan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(15, 7, 'دیر', 'dayyer', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(16, 7, 'گناوه', 'genaveh', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(17, 7, 'دیلم', 'deylam', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(18, 7, 'جم', 'jam', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(19, 7, 'عسلویه', 'asaluyeh', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(20, 7, 'دشتی', 'dashti', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(21, 7, 'تنگستان', 'tangestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(22, 7, 'اهرم', 'ahram', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(23, 7, 'خورموج', 'khormoj', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_00_00_000001_create_provinces_table', 1),
(2, '0000_00_00_000002_create_cities_table', 1),
(3, '0001_01_00_000000_create_people_table', 1),
(4, '0001_01_01_000000_create_users_table', 1),
(5, '0001_01_01_000001_create_cache_table', 1),
(6, '0001_01_01_000002_create_jobs_table', 1),
(7, '2024_01_01_000000_create_passkeys_table', 1),
(8, '2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
(9, '2026_08_30_104737_create_verification_challenges_table', 1),
(10, '2026_09_03_081137_create_mobiles_table', 1),
(11, '2026_09_03_081417_create_mobile_person_table', 1),
(12, '2026_09_04_063010_create_roles_table', 1),
(13, '2026_09_04_063023_create_branches_table', 1),
(14, '2026_09_04_063737_create_memberships_table', 1),
(15, '2026_09_04_064650_create_role_assignments_table', 1),
(16, '2026_10_12_091044_create_payments_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mobiles`
--

CREATE TABLE `mobiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mobile` char(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mobiles`
--

INSERT INTO `mobiles` (`id`, `mobile`, `created_at`, `updated_at`) VALUES
(1, '09177755924', '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, '09034336111', '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(3, '09350568163', '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `mobile_person`
--

CREATE TABLE `mobile_person` (
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `mobile_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mobile_person`
--

INSERT INTO `mobile_person` (`person_id`, `mobile_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(1, 2, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 3, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `passkeys`
--

CREATE TABLE `passkeys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credential`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `gateway` varchar(20) NOT NULL DEFAULT 'sep',
  `res_num` varchar(50) NOT NULL,
  `gateway_token` varchar(255) DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nationality_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `identity` varchar(20) NOT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `first_name_fa` varchar(30) DEFAULT NULL,
  `last_name_fa` varchar(40) DEFAULT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `nationality_type`, `identity`, `gender`, `first_name_fa`, `last_name_fa`, `nickname`, `created_at`, `updated_at`) VALUES
(1, 1, '2063531218', NULL, 'یاسر', 'بیشه سری', NULL, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 1, '3500984886', NULL, 'ندا', 'بخشی زاده', NULL, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'آذربایجان شرقی', 'east-azerbaijan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 'آذربایجان غربی', 'west-azerbaijan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(3, 'اردبیل', 'ardabil', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(4, 'اصفهان', 'isfahan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(5, 'البرز', 'alborz', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(6, 'ایلام', 'ilam', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(7, 'بوشهر', 'bushehr', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(8, 'تهران', 'tehran', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(9, 'چهارمحال و بختیاری', 'chaharmahal-bakhtiari', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(10, 'خراسان جنوبی', 'south-khorasan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(11, 'خراسان رضوی', 'razavi-khorasan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(12, 'خراسان شمالی', 'north-khorasan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(13, 'خوزستان', 'khuzestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(14, 'زنجان', 'zanjan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(15, 'سمنان', 'semnan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(16, 'سیستان و بلوچستان', 'sistan-baluchestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(17, 'فارس', 'fars', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(18, 'قزوین', 'qazvin', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(19, 'قم', 'qom', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(20, 'کردستان', 'kurdistan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(21, 'کرمان', 'kerman', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(22, 'کرمانشاه', 'kermanshah', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(23, 'کهگیلویه و بویراحمد', 'kohgiluyeh-boyerahmad', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(24, 'گلستان', 'golestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(25, 'گیلان', 'gilan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(26, 'لرستان', 'lorestan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(27, 'مازندران', 'mazandaran', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(28, 'مرکزی', 'markazi', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(29, 'هرمزگان', 'hormozgan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(30, 'همدان', 'hamedan', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(31, 'یزد', 'yazd', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `scope`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'site_admin', 'مدیر سامانه', 'system', 'مدیر فنی سامانه با دسترسی کامل به نرم‌افزار.', 'red', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 'founder', 'مؤسس', 'institute', 'مالک و مدیر کل آموزشگاه که می‌تواند در کنار آن نقش‌های عملیاتی نیز داشته باشد.', 'teal', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(3, 'administrative', 'مسئول اداری', 'branch', 'مسئول امور اجرایی و اداری یک شعبه.', 'fuchsia', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(4, 'instructor', 'مربی', 'branch', 'مدرس یا مربی فعال در یک یا چند شعبه.', 'amber', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(5, 'student', 'هنرجو', 'branch', 'فردی که در یک یا چند دوره آموزشی شرکت می‌کند.', 'lime', 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `role_assignments`
--

CREATE TABLE `role_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `membership_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_assignments`
--

INSERT INTO `role_assignments` (`id`, `person_id`, `role_id`, `membership_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 2, 2, NULL, 1, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `person_id`, `username`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Yasser', '$2y$12$Jw7Kj262bndspBVFAWBYDO5/WspzxxLdf1e73GvUOPApJynbxq5wK', NULL, NULL, NULL, NULL, '2026-10-12 06:01:06', '2026-10-12 06:01:06'),
(2, 2, 'Neda', '$2y$12$ndnuecZpS3MieTZfzan7DOIN5GwL2IceE1OarEQa7xU/rCCxCZk3K', NULL, NULL, NULL, NULL, '2026-10-12 06:01:06', '2026-10-12 06:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `verification_challenges`
--

CREATE TABLE `verification_challenges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name_fa` varchar(30) NOT NULL,
  `last_name_fa` varchar(40) NOT NULL,
  `nationality_type` tinyint(3) UNSIGNED NOT NULL,
  `identity` varchar(20) NOT NULL,
  `mobile` char(11) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `fingerprint` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL,
  `sms_sent_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`),
  ADD KEY `branches_province_id_foreign` (`province_id`),
  ADD KEY `branches_city_id_foreign` (`city_id`),
  ADD KEY `branches_is_main_index` (`is_main`),
  ADD KEY `branches_postal_code_index` (`postal_code`),
  ADD KEY `branches_is_active_index` (`is_active`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cities_province_id_name_unique` (`province_id`,`name`),
  ADD UNIQUE KEY `cities_province_id_slug_unique` (`province_id`,`slug`),
  ADD KEY `cities_is_active_index` (`is_active`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `memberships_person_id_branch_id_unique` (`person_id`,`branch_id`),
  ADD KEY `memberships_branch_id_foreign` (`branch_id`),
  ADD KEY `memberships_is_active_index` (`is_active`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobiles`
--
ALTER TABLE `mobiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobiles_mobile_unique` (`mobile`);

--
-- Indexes for table `mobile_person`
--
ALTER TABLE `mobile_person`
  ADD PRIMARY KEY (`person_id`,`mobile_id`),
  ADD KEY `mobile_person_mobile_id_foreign` (`mobile_id`);

--
-- Indexes for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  ADD KEY `passkeys_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_res_num_unique` (`res_num`),
  ADD UNIQUE KEY `payments_gateway_token_unique` (`gateway_token`),
  ADD UNIQUE KEY `payments_gateway_reference_unique` (`gateway_reference`),
  ADD KEY `payments_user_id_foreign` (`user_id`),
  ADD KEY `payments_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `people_identity_unique` (`identity`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provinces_name_unique` (`name`),
  ADD UNIQUE KEY `provinces_slug_unique` (`slug`),
  ADD KEY `provinces_is_active_index` (`is_active`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_code_unique` (`code`);

--
-- Indexes for table `role_assignments`
--
ALTER TABLE `role_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_assignments_person_id_role_id_membership_id_unique` (`person_id`,`role_id`,`membership_id`),
  ADD KEY `role_assignments_role_id_foreign` (`role_id`),
  ADD KEY `role_assignments_membership_id_foreign` (`membership_id`),
  ADD KEY `role_assignments_is_active_index` (`is_active`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_person_id_unique` (`person_id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `verification_challenges`
--
ALTER TABLE `verification_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verification_challenges_purpose_mobile_index` (`purpose`,`mobile`),
  ADD KEY `verification_challenges_expires_at_index` (`expires_at`),
  ADD KEY `verification_challenges_verified_at_index` (`verified_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `mobiles`
--
ALTER TABLE `mobiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `passkeys`
--
ALTER TABLE `passkeys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_assignments`
--
ALTER TABLE `role_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `verification_challenges`
--
ALTER TABLE `verification_challenges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `memberships_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mobile_person`
--
ALTER TABLE `mobile_person`
  ADD CONSTRAINT `mobile_person_mobile_id_foreign` FOREIGN KEY (`mobile_id`) REFERENCES `mobiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mobile_person_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_assignments`
--
ALTER TABLE `role_assignments`
  ADD CONSTRAINT `role_assignments_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_assignments_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_assignments_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
