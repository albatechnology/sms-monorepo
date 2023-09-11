-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2023 at 05:30 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms_backend`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `follow_up_datetime` datetime NOT NULL,
  `feedback` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `follow_up_method` tinyint(3) UNSIGNED DEFAULT NULL,
  `status` tinyint(3) UNSIGNED DEFAULT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `latest_activity_comment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_comment_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `reminder_datetime` datetime DEFAULT NULL,
  `reminder_note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `estimated_value` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_brand_values`
--

CREATE TABLE `activity_brand_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `estimated_value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_discount` double(15,3) NOT NULL DEFAULT 0.000,
  `total_order_value` double(15,3) NOT NULL DEFAULT 0.000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_comments`
--

CREATE TABLE `activity_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `activity_comment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_product`
--

CREATE TABLE `activity_product` (
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_product_brand`
--

CREATE TABLE `activity_product_brand` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `estimated_value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_3` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `address_line_1`, `address_line_2`, `address_line_3`, `postcode`, `city`, `country`, `province`, `type`, `phone`, `customer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tangerang Raya', NULL, NULL, '15710', 'Tangerang', 'Indonesia', 'Banten', 1, '0987654321', 1, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(2, 'address customer dua', NULL, NULL, '15710', 'Tangerang', 'Indonesia', 'Banten', 1, '098709870987', 2, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(3, 'address customer tiga', NULL, NULL, '13848', 'Jakarta Utara', 'Indonesia', 'Jakarta', 1, '098765098765', 3, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(4, 'address customer empat', NULL, NULL, '22334', 'Bandung', 'Indonesia', 'Jawa Barat', 1, '098789098780', 4, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(5, 'address customer lima', NULL, NULL, '10902', 'Surabaya', 'Indonesia', 'Jawa Timur', 1, '082928347732', 5, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(6, 'address customer enam', NULL, NULL, '84930', 'Medan', 'Indonesia', 'Sumatera Utara', 1, '082127328283', 6, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `host` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brand_categories`
--

CREATE TABLE `brand_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand_categories`
--

INSERT INTO `brand_categories` (`id`, `subscribtion_user_id`, `name`, `code`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Brand Category Otomotif', 'BCOtomotif', 'brand-category-Otomotif', '2023-09-11 03:28:35', '2023-09-11 03:28:35', NULL),
(2, 2, 'Brand Category Properti', 'BCProperti', 'brand-category-Properti', '2023-09-11 03:28:44', '2023-09-11 03:28:44', NULL),
(3, 2, 'Brand Category Assurance', 'ACassurance', 'brand-category-Assurance', '2023-09-11 03:28:50', '2023-09-11 03:28:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_discount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `total_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `channels`
--

INSERT INTO `channels` (`id`, `subscribtion_user_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Channel Starter 1', '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(2, 3, 'Channel basic 1', '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `channel_user`
--

CREATE TABLE `channel_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `channel_user`
--

INSERT INTO `channel_user` (`user_id`, `channel_id`) VALUES
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `main_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `foreign_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `title` tinyint(4) DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `has_activity` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `subscribtion_user_id`, `title`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `description`, `default_address_id`, `has_activity`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'Customer satu', 'OKE', 'customer@gmail.com', '0987654321', '2023-09-11', 'description customer', NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(2, 2, 1, 'customer dua', 'sample', 'customerdua@gmail.com', '098709870987', NULL, NULL, NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(3, 2, 1, 'customer tiga', 'sample', 'customertiga@gmail.com', '098765098765', NULL, NULL, NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(4, 2, 1, 'customer empat', '', 'customerempat@gmail.com', '098789098780', NULL, NULL, NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(5, 2, 1, 'customer lima', '', 'customerlima@gmail.com', '082928347732', NULL, NULL, NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(6, 2, 1, 'customer enam', '', 'customerenam@gmail.com', '082127328283', NULL, NULL, NULL, 0, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_discount_uses`
--

CREATE TABLE `customer_discount_uses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `discount_id` bigint(20) UNSIGNED NOT NULL,
  `use_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`order_ids`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `promo_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `scope` tinyint(3) UNSIGNED NOT NULL,
  `activation_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` double(8,2) NOT NULL DEFAULT 0.00,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `max_discount_price_per_order` bigint(20) UNSIGNED DEFAULT NULL,
  `max_use_per_customer` int(10) UNSIGNED DEFAULT NULL,
  `min_order_price` bigint(20) UNSIGNED DEFAULT NULL,
  `product_ids` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exports`
--

CREATE TABLE `exports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `done_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_batches`
--

CREATE TABLE `import_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_summary` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `mode` tinyint(3) UNSIGNED DEFAULT NULL,
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`errors`)),
  `all_jobs_added_to_batch_at` datetime DEFAULT NULL,
  `job_batch_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_lines`
--

CREATE TABLE `import_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `preview_status` tinyint(3) UNSIGNED NOT NULL,
  `row` bigint(20) UNSIGNED NOT NULL,
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`errors`)),
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `exception_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `import_batch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_lead_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_new_customer` tinyint(1) DEFAULT 0,
  `is_unhandled` tinyint(1) NOT NULL DEFAULT 0,
  `group_id` bigint(20) UNSIGNED DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_referral_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` bigint(20) UNSIGNED NOT NULL,
  `status_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`status_history`)),
  `status_change_due_at` datetime DEFAULT NULL,
  `has_pending_status_change` tinyint(1) DEFAULT 0,
  `has_activity` tinyint(1) NOT NULL DEFAULT 0,
  `interest` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity_status` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `lead_category_id`, `sub_lead_category_id`, `parent_id`, `type`, `status`, `label`, `is_new_customer`, `is_unhandled`, `group_id`, `user_id`, `user_referral_id`, `customer_id`, `channel_id`, `status_history`, `status_change_due_at`, `has_pending_status_change`, `has_activity`, `interest`, `last_activity_status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, 3, 1, 'Customer satu OKE - 11-09-2023', 1, 0, 1, 7, NULL, 1, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.647419Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.653544Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(2, 1, 1, NULL, 3, 1, 'customer dua sample - 11-09-2023', 1, 0, 2, 7, NULL, 2, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.656397Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.660952Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(3, 1, 1, NULL, 3, 1, 'customer tiga sample - 11-09-2023', 1, 0, 3, 7, NULL, 3, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.663476Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.666778Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(4, 1, 1, NULL, 3, 1, 'customer empat  - 11-09-2023', 1, 0, 4, 7, NULL, 4, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.670025Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.673520Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(5, 1, 1, NULL, 3, 1, 'customer lima  - 11-09-2023', 1, 0, 5, 7, NULL, 5, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.676490Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.679492Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL),
(6, 1, 1, NULL, 3, 1, 'customer enam  - 11-09-2023', 1, 0, 6, 7, NULL, 6, 1, '[{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.681977Z\"},{\"status\":1,\"type\":3,\"updated_at\":\"2023-09-11T03:29:28.685198Z\"}]', NULL, 0, 0, NULL, NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_categories`
--

CREATE TABLE `lead_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_categories`
--

INSERT INTO `lead_categories` (`id`, `subscribtion_user_id`, `name`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'Lead Category 1', 'description Lead Category 1', NULL, '2023-09-11 03:29:28', '2023-09-11 03:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\ProductBrand', 1, 'ff595bc6-e366-4e23-a9a6-c1f7ace7be1d', 'photo', '64ed725a735e5_logo-astra-otoparts', '64ed725a735e5_logo-astra-otoparts.jpg', 'image/jpeg', 's3', 's3', 29090, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 1, '2023-09-11 03:28:35', '2023-09-11 03:28:42'),
(2, 'App\\Models\\ProductCategory', 1, 'aaf0c5f7-99d0-4db8-b670-6700b40572c9', 'photo', '64ed691f3d78e_Screen-Shot-2023-08-29-at-10.38.49', '64ed691f3d78e_Screen-Shot-2023-08-29-at-10.38.49.png', 'image/png', 's3', 's3', 251071, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 2, '2023-09-11 03:28:42', '2023-09-11 03:28:44'),
(3, 'App\\Models\\ProductBrand', 2, '20cc130a-80f3-46d6-8c27-1c693b5968a2', 'photo', '64ed7329ae0c8_pngtree-property-logo-png-image_6430110', '64ed7329ae0c8_pngtree-property-logo-png-image_6430110.png', 'image/png', 's3', 's3', 9190, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 3, '2023-09-11 03:28:44', '2023-09-11 03:28:45'),
(4, 'App\\Models\\ProductCategory', 2, 'ab24d85a-dd97-4de2-af03-f222f5978f1e', 'photo', '64ed6915ead8f_Screen-Shot-2023-08-29-at-10.36.35', '64ed6915ead8f_Screen-Shot-2023-08-29-at-10.36.35.png', 'image/png', 's3', 's3', 794099, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 4, '2023-09-11 03:28:47', '2023-09-11 03:28:50'),
(5, 'App\\Models\\ProductBrand', 3, '94540a38-e9df-43ba-bf25-e5a328623d39', 'photo', '64ed7448c8ccc_256x256bb', '64ed7448c8ccc_256x256bb.jpg', 'image/jpeg', 's3', 's3', 17261, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 5, '2023-09-11 03:28:50', '2023-09-11 03:28:51'),
(6, 'App\\Models\\ProductCategory', 3, '3aa2a874-2cea-465a-90e0-4610da325e02', 'photo', '64ed6904a42e3_logo-about-us', '64ed6904a42e3_logo-about-us.png', 'image/png', 's3', 's3', 50310, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 6, '2023-09-11 03:28:52', '2023-09-11 03:28:53'),
(7, 'App\\Models\\Product', 1, 'a3bb7a96-93e5-4b0b-8cdc-d002ac5d9a99', 'photo', '64ec4cfcac371_2023-tesla-model-x-101-1671475309', '64ec4cfcac371_2023-tesla-model-x-101-1671475309.jpeg', 'image/jpeg', 's3', 's3', 67473, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 7, '2023-09-11 03:28:55', '2023-09-11 03:28:56'),
(8, 'App\\Models\\Product', 2, '6f0189f4-b0ff-4f4b-a21e-a777b6e0d4bc', 'photo', 'mengenal-mesin-diesel-common-rail-tdi-dan-diesel-konvensional', 'mengenal-mesin-diesel-common-rail-tdi-dan-diesel-konvensional.png', 'image/png', 's3', 's3', 196887, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 8, '2023-09-11 03:28:56', '2023-09-11 03:28:58'),
(9, 'App\\Models\\Product', 3, '0d4b8773-15f4-412c-bba5-2b648d4d800b', 'photo', 'crawler-buldozer-illustration-transportation-illustration-generative-ai_710947-95', 'crawler-buldozer-illustration-transportation-illustration-generative-ai_710947-95.jpg', 'image/jpeg', 's3', 's3', 75965, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 9, '2023-09-11 03:28:58', '2023-09-11 03:28:59'),
(10, 'App\\Models\\Product', 4, 'a91d63f4-d78b-48c0-9cf1-410d1f463093', 'photo', '64ec4d60f0f7e_2021-nissan-gt-r-2457-3-1664901335', '64ec4d60f0f7e_2021-nissan-gt-r-2457-3-1664901335.jpeg', 'image/jpeg', 's3', 's3', 130127, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 10, '2023-09-11 03:29:00', '2023-09-11 03:29:01'),
(11, 'App\\Models\\Product', 5, '1815c7ff-e64b-4378-beb2-be703accb47b', 'photo', '64ec4cb1f20df_IMG-20200506-WA0004-e1588733192512', '64ec4cb1f20df_IMG-20200506-WA0004-e1588733192512.jpeg', 'image/jpeg', 's3', 's3', 62475, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 11, '2023-09-11 03:29:02', '2023-09-11 03:29:03'),
(12, 'App\\Models\\Product', 6, 'fe74dd93-d9ad-423b-a236-60460a1949c8', 'photo', '64ed5c2e310a8_maxresdefault', '64ed5c2e310a8_maxresdefault.jpeg', 'image/jpeg', 's3', 's3', 92548, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 12, '2023-09-11 03:29:03', '2023-09-11 03:29:04'),
(13, 'App\\Models\\Product', 7, 'ec775a7d-5825-4c32-b238-eff875662d7e', 'photo', '64ec4a9200e31_5fdebc94f4196', '64ec4a9200e31_5fdebc94f4196.jpeg', 'image/jpeg', 's3', 's3', 60368, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 13, '2023-09-11 03:29:05', '2023-09-11 03:29:06'),
(14, 'App\\Models\\Product', 8, 'ccd62e78-aaad-42e0-8bf0-92ca7db6cb9a', 'photo', '64ec4bb7ee80c_32127_24959', '64ec4bb7ee80c_32127_24959.jpeg', 'image/jpeg', 's3', 's3', 282156, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 14, '2023-09-11 03:29:07', '2023-09-11 03:29:09'),
(15, 'App\\Models\\Product', 9, '76e07569-4c73-4c0e-b4bd-36e7d145f5ee', 'photo', '64ec4a5a50cf3_lampukristal', '64ec4a5a50cf3_lampukristal.jpeg', 'image/jpeg', 's3', 's3', 57413, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 15, '2023-09-11 03:29:11', '2023-09-11 03:29:13'),
(16, 'App\\Models\\Product', 10, 'be29713e-ef6d-4244-acfc-69220ad0a748', 'photo', '64ec4b710dd2c_Screen-Shot-2023-08-28-at-14.23.16', '64ec4b710dd2c_Screen-Shot-2023-08-28-at-14.23.16.png', 'image/png', 's3', 's3', 394404, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 16, '2023-09-11 03:29:13', '2023-09-11 03:29:20'),
(17, 'App\\Models\\Product', 11, 'c5c3b5cb-725f-4844-b9d1-251a4ed5e99d', 'photo', '64ec4b4b857b9_nationwide-mutual-insurance-company4591', '64ec4b4b857b9_nationwide-mutual-insurance-company4591.jpeg', 'image/jpeg', 's3', 's3', 21025, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 17, '2023-09-11 03:29:21', '2023-09-11 03:29:22'),
(18, 'App\\Models\\Product', 12, '7cd3f432-a5b7-45e7-88c1-b7b15d0c164b', 'photo', '64ec4b031884a_insurance', '64ec4b031884a_insurance.jpeg', 'image/jpeg', 's3', 's3', 10848, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 18, '2023-09-11 03:29:23', '2023-09-11 03:29:25'),
(19, 'App\\Models\\Product', 13, '0c4ad3a6-25f9-409f-88fd-f5af3dc04fc8', 'photo', '64ec59cd9475f_HQT8RYW6SJSGMP2YJVJV-59523127', '64ec59cd9475f_HQT8RYW6SJSGMP2YJVJV-59523127.jpeg', 'image/jpeg', 's3', 's3', 50385, '[]', '[]', '{\"thumb\":true,\"preview\":true}', '[]', 19, '2023-09-11 03:29:26', '2023-09-11 03:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2018_08_08_100000_create_telescope_entries_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2020_05_09_185048_create_job_batches_table', 1),
(5, '2020_05_11_230754_create_failed_jobs_table', 1),
(6, '2021_03_05_000001_create_subscribtion_packages_table', 1),
(7, '2021_03_05_000002_create_subscribtion_users_table', 1),
(8, '2021_03_06_000001_create_companies_table', 1),
(9, '2021_03_06_000005_create_customers_table', 1),
(10, '2021_03_06_000006_create_addresses_table', 1),
(11, '2021_03_06_000010_create_payment_categories_table', 1),
(12, '2021_03_06_000024_create_supervisor_types_table', 1),
(13, '2021_03_06_000033_create_product_categories_table', 1),
(14, '2021_03_06_000034_create_product_tags_table', 1),
(15, '2021_03_06_000040_create_product_brands_table', 1),
(16, '2021_03_06_000041_create_channels_table', 1),
(17, '2021_03_07_000005_create_users_table', 1),
(18, '2021_03_07_000006_create_user_alerts_table', 1),
(19, '2021_03_07_000008_create_promos_table', 1),
(20, '2021_03_07_000009_create_discounts_table', 1),
(21, '2021_03_07_000011_create_payment_types_table', 1),
(22, '2021_03_07_000012_create_lead_categories_table', 1),
(23, '2021_03_07_000013_create_sub_lead_categories_table', 1),
(24, '2021_03_07_000015_create_leads_table', 1),
(25, '2021_03_07_000016_create_tax_invoices_table', 1),
(26, '2021_03_07_000017_create_product_category_codes_table', 1),
(27, '2021_03_07_000018_create_products_table', 1),
(28, '2021_03_07_000031_create_orders_table', 1),
(29, '2021_03_07_000032_create_activities_table', 1),
(30, '2021_03_07_000034_create_order_trackings_table', 1),
(31, '2021_03_07_000035_create_order_details_table', 1),
(32, '2021_03_07_000037_create_media_table', 1),
(33, '2021_03_07_000038_create_audit_logs_table', 1),
(34, '2021_03_07_000040_create_channel_user_pivot_table', 1),
(35, '2021_03_07_000041_create_company_user_pivot_table', 1),
(36, '2021_03_07_000043_create_product_product_category_pivot_table', 1),
(37, '2021_03_07_000045_create_product_product_tag_pivot_table', 1),
(38, '2021_03_07_000048_create_user_user_alert_pivot_table', 1),
(39, '2021_03_07_000052_create_stocks_table', 1),
(40, '2021_03_07_000053_create_stock_transfers_table', 1),
(41, '2021_03_07_000056_create_shipments_table', 1),
(42, '2021_03_07_000057_create_activity_product_pivot_table', 1),
(43, '2021_03_07_000058_create_payments_table', 1),
(44, '2021_03_07_000079_create_qa_table', 1),
(45, '2021_03_19_165256_create_notifications_table', 1),
(46, '2021_04_07_002131_create_carts_table', 1),
(47, '2021_04_10_234713_create_company_data_table', 1),
(48, '2021_04_14_215644_create_qa_topic_users_table', 1),
(49, '2021_04_14_221305_create_qa_message_users_table', 1),
(50, '2021_04_20_003044_customer_discount_use', 1),
(51, '2021_05_06_222516_create_import_batches_table', 1),
(52, '2021_05_06_223702_create_import_lines_table', 1),
(53, '2021_05_07_140028_create_seeders_table', 1),
(54, '2021_05_23_141546_create_stock_histories_table', 1),
(55, '2021_05_24_152823_create_company_accounts_table', 1),
(56, '2021_06_08_224714_add_shipment_order_details', 1),
(57, '2021_06_14_235703_create_notification_devices_table', 1),
(58, '2021_06_23_004512_create_report_target_table', 1),
(59, '2021_07_15_021437_create_target_type_priorities_table', 1),
(60, '2021_10_05_180000_create_activity_brand_table', 1),
(61, '2021_10_05_183045_add_estimated_value_to_activities_table', 1),
(62, '2021_10_21_034456_create_brand_categories_table', 1),
(63, '2021_10_21_044339_create_product_brand_categories_table', 1),
(64, '2021_12_13_093940_update_stock_transfers_table', 1),
(65, '2021_12_14_024416_recreate_stock_transfers_table', 1),
(66, '2021_12_14_041829_add_indent_to_stocks_table', 1),
(67, '2021_12_14_041830_create_religions_table', 1),
(68, '2021_12_23_030212_new_update_stock_transfers_table', 1),
(69, '2021_12_23_094505_add_order_id_to_stock_transfers_table', 1),
(70, '2021_12_27_035014_add_total_stock_to_stocks_table', 1),
(71, '2022_01_20_042257_create_order_detail_demands_table', 1),
(72, '2022_01_20_044502_create_cart_demands_table', 1),
(73, '2022_04_18_032112_create_exports_table', 1),
(74, '2022_04_22_095017_create_supervisor_discount_approval_limits_table', 1),
(75, '2022_04_27_071934_create_currencies_table', 1),
(76, '2022_05_11_094937_create_activity_brand_values_table', 1),
(77, '2022_06_22_230356_create_user_companies_table', 1),
(78, '2022_06_30_111625_create_order_discounts_table', 1),
(79, '2022_09_30_140816_create_new_targets_table', 1),
(80, '2022_10_04_153824_create_product_brand_users_table', 1),
(81, '2022_10_07_132636_create_product_brand_leads_table', 1),
(82, '2023_06_22_144407_create_permission_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`, `subscribtion_user_id`) VALUES
(1, 'user', 1, 1),
(2, 'user', 2, 1),
(2, 'user', 3, 2),
(3, 'user', 4, 2),
(3, 'user', 5, 2),
(3, 'user', 6, 2),
(3, 'user', 7, 2),
(2, 'user', 8, 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_devices`
--

CREATE TABLE `notification_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` bigint(20) UNSIGNED NOT NULL,
  `raw_source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_source`)),
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_fee` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `packing_fee` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `expected_shipping_datetime` datetime DEFAULT NULL,
  `tax_invoice_sent` tinyint(1) DEFAULT 0,
  `records` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_paid` bigint(20) NOT NULL DEFAULT 0,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `payment_status` tinyint(3) UNSIGNED NOT NULL,
  `stock_status` tinyint(3) UNSIGNED NOT NULL,
  `discount_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_discount` bigint(20) NOT NULL DEFAULT 0,
  `total_price` bigint(20) NOT NULL DEFAULT 0,
  `quotation_valid_until_datetime` datetime DEFAULT NULL,
  `deal_at` datetime DEFAULT NULL,
  `additional_discount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `additional_discount_ratio` int(10) UNSIGNED DEFAULT NULL,
  `approval_status` smallint(6) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_take_over_by` mediumint(8) UNSIGNED DEFAULT NULL,
  `approval_send_to` tinyint(3) UNSIGNED DEFAULT NULL,
  `approval_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_supervisor_type_id` smallint(5) UNSIGNED DEFAULT NULL,
  `is_direct_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `quantity_fulfilled` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `records` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` bigint(20) NOT NULL,
  `total_discount` bigint(20) NOT NULL DEFAULT 0,
  `total_price` bigint(20) NOT NULL DEFAULT 0,
  `total_cascaded_discount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` bigint(20) NOT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `payment_type_id` bigint(20) UNSIGNED NOT NULL,
  `approved_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `added_by_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_categories`
--

CREATE TABLE `payment_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_categories`
--

INSERT INTO `payment_categories` (`id`, `subscribtion_user_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Transfer', '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `require_approval` tinyint(1) DEFAULT 0,
  `payment_category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `subscribtion_user_id`, `name`, `require_approval`, `payment_category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'BCA Prioritas', 0, 1, '2023-09-11 03:29:28', '2023-09-11 03:29:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'dashboard_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(2, 'user_management_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(3, 'user_access', 'web', 2, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(4, 'user_create', 'web', 3, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(5, 'user_edit', 'web', 3, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(6, 'user_delete', 'web', 3, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(7, 'role_access', 'web', 2, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(8, 'role_create', 'web', 7, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(9, 'role_edit', 'web', 7, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(10, 'role_delete', 'web', 7, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(11, 'permission_access', 'web', 2, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(12, 'permission_create', 'web', 11, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(13, 'permission_edit', 'web', 11, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(14, 'permission_delete', 'web', 11, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(15, 'corporate_management_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(16, 'channel_access', 'web', 15, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(17, 'channel_create', 'web', 16, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(18, 'channel_edit', 'web', 16, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(19, 'channel_delete', 'web', 16, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(20, 'crm_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(21, 'lead_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(22, 'lead_create', 'web', 21, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(23, 'lead_edit', 'web', 21, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(24, 'lead_delete', 'web', 21, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(25, 'lead_category_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(26, 'lead_category_create', 'web', 25, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(27, 'lead_category_edit', 'web', 25, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(28, 'lead_category_delete', 'web', 25, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(29, 'sub_lead_category_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(30, 'sub_lead_category_create', 'web', 29, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(31, 'sub_lead_category_edit', 'web', 29, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(32, 'sub_lead_category_delete', 'web', 29, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(33, 'activity_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(34, 'activity_create', 'web', 33, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(35, 'activity_edit', 'web', 33, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(36, 'activity_delete', 'web', 33, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(37, 'customer_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(38, 'customer_create', 'web', 37, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(39, 'customer_edit', 'web', 37, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(40, 'customer_delete', 'web', 37, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(41, 'address_access', 'web', 20, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(42, 'address_create', 'web', 41, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(43, 'address_edit', 'web', 41, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(44, 'address_delete', 'web', 41, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(45, 'product_management_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(46, 'brand_category_access', 'web', 45, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(47, 'brand_category_create', 'web', 46, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(48, 'brand_category_edit', 'web', 46, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(49, 'brand_category_delete', 'web', 46, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(50, 'product_brand_access', 'web', 45, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(51, 'product_brand_create', 'web', 50, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(52, 'product_brand_edit', 'web', 50, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(53, 'product_brand_delete', 'web', 50, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(54, 'product_category_access', 'web', 45, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(55, 'product_category_create', 'web', 54, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(56, 'product_category_edit', 'web', 54, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(57, 'product_category_delete', 'web', 54, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(58, 'product_access', 'web', 45, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(59, 'product_create', 'web', 58, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(60, 'product_edit', 'web', 58, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(61, 'product_delete', 'web', 58, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(62, 'marketing_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(63, 'promo_access', 'web', 62, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(64, 'promo_create', 'web', 63, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(65, 'promo_edit', 'web', 63, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(66, 'promo_delete', 'web', 63, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(67, 'discount_access', 'web', 62, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(68, 'discount_create', 'web', 67, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(69, 'discount_edit', 'web', 67, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(70, 'discount_delete', 'web', 67, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(71, 'finance_access', 'web', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(72, 'order_access', 'web', 71, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(73, 'order_create', 'web', 72, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(74, 'order_edit', 'web', 72, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(75, 'order_delete', 'web', 72, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(76, 'payment_access', 'web', 71, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(77, 'payment_create', 'web', 76, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(78, 'payment_edit', 'web', 76, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(79, 'payment_delete', 'web', 76, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(80, 'payment_category_access', 'web', 71, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(81, 'payment_category_create', 'web', 80, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(82, 'payment_category_edit', 'web', 80, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(83, 'payment_category_delete', 'web', 80, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(84, 'payment_type_access', 'web', 71, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(85, 'payment_type_create', 'web', 84, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(86, 'payment_type_edit', 'web', 84, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(87, 'payment_type_delete', 'web', 84, '2023-09-11 03:28:33', '2023-09-11 03:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plain_text_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `product_category_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `brand_category_id` smallint(6) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` bigint(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `uom` int(11) NOT NULL DEFAULT 1,
  `production_cost` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `product_category` smallint(5) UNSIGNED DEFAULT NULL,
  `volume` double(8,2) DEFAULT NULL,
  `tags` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `subscribtion_user_id`, `product_category_id`, `product_brand_id`, `brand_category_id`, `name`, `description`, `sku`, `price`, `is_active`, `uom`, `production_cost`, `product_category`, `volume`, `tags`, `video_url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 1, 1, 'Motor Listrik M1', 'Motor Listrik M1', '10001', 1000000, 1, 1, 1000000, NULL, NULL, NULL, NULL, '2023-09-11 03:28:54', '2023-09-11 03:28:54', NULL),
(2, 2, 1, 1, 1, 'Mesin Diesel 100HP', 'Mesin Diesel 100HP', '10002', 2000000, 1, 1, 2000000, NULL, NULL, NULL, NULL, '2023-09-11 03:28:56', '2023-09-11 03:28:56', NULL),
(3, 2, 1, 1, 1, 'Bulldozer Metal Wheel', 'Bulldozer Metal Wheel', '10003', 3000000, 1, 1, 3000000, NULL, NULL, NULL, NULL, '2023-09-11 03:28:58', '2023-09-11 03:28:58', NULL),
(4, 2, 1, 1, 1, 'Nissan GT-R', 'Motor Listrik M1', '10004', 2000000, 1, 1, 2000000, NULL, NULL, NULL, NULL, '2023-09-11 03:28:59', '2023-09-11 03:28:59', NULL),
(5, 2, 1, 1, 1, 'Mazda RX-7 VeilSide', 'Mesin Diesel 100HP', '10005', 3000000, 1, 1, 3000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:01', '2023-09-11 03:29:01', NULL),
(6, 2, 1, 1, 1, 'GSX 1000rr', 'Bulldozer Metal Wheel', '10006', 1800000, 1, 1, 1800000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:03', '2023-09-11 03:29:03', NULL),
(7, 2, 2, 2, 2, 'Apartemen Garvyn 2 Kamar', 'Apartemen Garvyn 2 Kamar', '20001', 100000000, 1, 1, 100000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:04', '2023-09-11 03:29:04', NULL),
(8, 2, 2, 2, 2, 'Sofa Lazboy Melandas', 'Sofa Lazboy Melandas', '20002', 20000000, 1, 1, 20000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:06', '2023-09-11 03:29:06', NULL),
(9, 2, 2, 2, 2, 'Lampu Gantung Kristal', 'Lampu Gantung Kristal', '20003', 5000000, 1, 1, 5000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:09', '2023-09-11 03:29:09', NULL),
(10, 2, 3, 3, 3, 'Asuransi Kesehatan', 'Asuransi Kesehatan', '30001', 1000000, 1, 1, 1000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:13', '2023-09-11 03:29:13', NULL),
(11, 2, 3, 3, 3, 'Asuransi Kendaraan', 'Asuransi Kendaraan', '30002', 2000000, 1, 1, 2000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:20', '2023-09-11 03:29:20', NULL),
(12, 2, 3, 3, 3, 'Asuransi Proyek', 'Asuransi Proyek', '30003', 3000000, 1, 1, 3000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:22', '2023-09-11 03:29:22', NULL),
(13, 2, 3, 3, 3, 'FWD Soul Insurance', 'FWD Soul Insurance', '30004', 17000000, 1, 1, 17000000, NULL, NULL, NULL, NULL, '2023-09-11 03:29:25', '2023-09-11 03:29:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_brands`
--

CREATE TABLE `product_brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `brand_category_id` smallint(6) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hpp_calculation` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `currency_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_brands`
--

INSERT INTO `product_brands` (`id`, `subscribtion_user_id`, `brand_category_id`, `name`, `hpp_calculation`, `currency_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'Brand Otomotif', 0, NULL, '2023-09-11 03:28:35', '2023-09-11 03:28:35', NULL),
(2, 2, 2, 'Brand Properti', 0, NULL, '2023-09-11 03:28:44', '2023-09-11 03:28:44', NULL),
(3, 2, 3, 'Brand Assurance', 0, NULL, '2023-09-11 03:28:50', '2023-09-11 03:28:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_brand_categories`
--

CREATE TABLE `product_brand_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `brand_category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_brand_leads`
--

CREATE TABLE `product_brand_leads` (
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_brand_users`
--

CREATE TABLE `product_brand_users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `subscribtion_user_id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Category Otomotif', NULL, '2023-09-11 03:28:42', '2023-09-11 03:28:42', NULL),
(2, 2, 'Category Properti', NULL, '2023-09-11 03:28:45', '2023-09-11 03:28:45', NULL),
(3, 2, 'Category Assurance', NULL, '2023-09-11 03:28:51', '2023-09-11 03:28:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `religions`
--

CREATE TABLE `religions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `reportable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reportable_id` bigint(20) UNSIGNED NOT NULL,
  `reportable_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_diff` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `name`, `start_date`, `end_date`, `reportable_type`, `reportable_id`, `reportable_label`, `time_diff`, `created_at`, `updated_at`) VALUES
(1, 'User Starter - SALES - September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'user', 7, 'Sales', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(2, 'User Starter - BUM - September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'user', 5, 'BUM', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(3, 'User Starter - STORE LEADER - September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'user', 6, 'Store Leader', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(4, 'User Starter - Channel Starter 1', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'channel', 1, 'Channel Starter 1', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(5, 'User Basic - Channel basic 1', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'channel', 2, 'Channel basic 1', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(6, 'PT. Alba Digital Technology September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'subscribtion_user', 1, 'PT. Alba Digital Technology', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(7, 'User Starter September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'subscribtion_user', 2, 'User Starter', 2591999, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(8, 'User Basic September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'subscribtion_user', 3, 'User Basic', 2591999, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(9, 'User Advance September 2023', '2023-09-01 00:00:00', '2023-09-30 23:59:59', 'subscribtion_user', 4, 'User Advance', 2591999, '2023-09-11 03:29:52', '2023-09-11 03:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `subscribtion_user_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'super-admin', 'web', '2023-09-11 03:28:34', '2023-09-11 03:28:34'),
(2, NULL, 'admin', 'web', '2023-09-11 03:28:34', '2023-09-11 03:28:34'),
(3, NULL, 'user', 'web', '2023-09-11 03:28:34', '2023-09-11 03:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2);

-- --------------------------------------------------------

--
-- Table structure for table `seeders`
--

CREATE TABLE `seeders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seeders` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `channel_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_histories`
--

CREATE TABLE `stock_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` bigint(20) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `order_detail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_transfer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `from_channel_id` bigint(20) UNSIGNED NOT NULL,
  `to_channel_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribtion_packages`
--

CREATE TABLE `subscribtion_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_users` int(10) UNSIGNED DEFAULT NULL,
  `max_customers` int(10) UNSIGNED DEFAULT NULL,
  `max_activities` int(10) UNSIGNED DEFAULT NULL,
  `max_leads` int(10) UNSIGNED DEFAULT NULL,
  `max_orders` int(10) UNSIGNED DEFAULT NULL,
  `max_brands` int(10) UNSIGNED DEFAULT NULL,
  `max_categories` int(10) UNSIGNED DEFAULT NULL,
  `max_products` int(10) UNSIGNED DEFAULT NULL,
  `can_discount` tinyint(1) NOT NULL DEFAULT 0,
  `can_approval` tinyint(1) NOT NULL DEFAULT 0,
  `can_multi_companies` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribtion_packages`
--

INSERT INTO `subscribtion_packages` (`id`, `name`, `max_users`, `max_customers`, `max_activities`, `max_leads`, `max_orders`, `max_brands`, `max_categories`, `max_products`, `can_discount`, `can_approval`, `can_multi_companies`, `created_at`, `updated_at`) VALUES
(1, 'Starter', 10, 500, 500, 500, 1000, 1, 10, 50, 0, 0, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(2, 'Basic', 50, 3000, 2000, 3000, 10000, 10, 50, 500, 1, 1, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33'),
(3, 'Advance', 200, NULL, NULL, NULL, NULL, NULL, NULL, 5000, 1, 1, 1, '2023-09-11 03:28:33', '2023-09-11 03:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `subscribtion_users`
--

CREATE TABLE `subscribtion_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribtion_users`
--

INSERT INTO `subscribtion_users` (`id`, `subscribtion_package_id`, `name`, `email`, `phone`, `expiration_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'PT. Alba Digital Technology', 'alba@gmail.com', '080808080808', NULL, '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(2, 1, 'User Starter', 'user.starter@gmail.com', '09876543211', '2023-10-11', '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(3, 2, 'User Basic', 'user.basic@gmail.com', '09876789009', '2023-10-11', '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(4, 3, 'User Advance', 'user.advance@gmail.com', '098123456734', '2023-10-11', '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_lead_categories`
--

CREATE TABLE `sub_lead_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_lead_categories`
--

INSERT INTO `sub_lead_categories` (`id`, `lead_category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lead Category 1', 'description Lead Category 1', '2023-09-11 03:29:28', '2023-09-11 03:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_discount_approval_limits`
--

CREATE TABLE `supervisor_discount_approval_limits` (
  `subscribtion_user_id` bigint(20) UNSIGNED NOT NULL,
  `supervisor_type_id` bigint(20) UNSIGNED NOT NULL,
  `limit` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supervisor_discount_approval_limits`
--

INSERT INTO `supervisor_discount_approval_limits` (`subscribtion_user_id`, `supervisor_type_id`, `limit`) VALUES
(1, 1, 0),
(1, 2, 0),
(1, 3, 0),
(1, 4, 0),
(2, 1, 0),
(2, 2, 0),
(2, 3, 0),
(2, 4, 0),
(3, 1, 0),
(3, 2, 0),
(3, 3, 0),
(3, 4, 0),
(4, 1, 0),
(4, 2, 0),
(4, 3, 0),
(4, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_types`
--

CREATE TABLE `supervisor_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `can_assign_lead` tinyint(1) NOT NULL DEFAULT 0,
  `discount_approval_limit_percentage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supervisor_types`
--

INSERT INTO `supervisor_types` (`id`, `name`, `code`, `level`, `can_assign_lead`, `discount_approval_limit_percentage`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Store Leader', 'store-leader', 1, 0, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(2, 'Manager Area', 'manager-area', 2, 0, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(3, 'Head Sales', 'head-sales', 3, 0, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL),
(4, 'Director Sales Marketing', 'director-sales-marketing', 4, 0, 0, '2023-09-11 03:28:33', '2023-09-11 03:28:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `targets`
--

CREATE TABLE `targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value_percentage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `targets`
--

INSERT INTO `targets` (`id`, `model_type`, `model_id`, `report_id`, `type`, `target`, `value`, `value_percentage`, `context`, `created_at`, `updated_at`) VALUES
(1, 'user', 7, 1, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(2, 'user', 7, 1, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(3, 'user', 7, 1, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(4, 'user', 7, 1, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(5, 'user', 7, 1, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(6, 'user', 5, 2, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(7, 'user', 5, 2, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(8, 'user', 5, 2, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(9, 'user', 5, 2, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(10, 'user', 5, 2, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(11, 'user', 6, 3, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(12, 'user', 6, 3, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(13, 'user', 6, 3, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(14, 'user', 6, 3, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(15, 'user', 6, 3, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(16, 'channel', 1, 4, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(17, 'channel', 1, 4, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(18, 'channel', 1, 4, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(19, 'channel', 1, 4, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(20, 'channel', 1, 4, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(21, 'channel', 2, 5, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(22, 'channel', 2, 5, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(23, 'channel', 2, 5, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(24, 'channel', 2, 5, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(25, 'channel', 2, 5, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(26, 'subscribtion_user', 1, 6, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(27, 'subscribtion_user', 1, 6, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(28, 'subscribtion_user', 1, 6, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(29, 'subscribtion_user', 1, 6, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(30, 'subscribtion_user', 1, 6, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(31, 'subscribtion_user', 2, 7, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:51', '2023-09-11 03:29:51'),
(32, 'subscribtion_user', 2, 7, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(33, 'subscribtion_user', 2, 7, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(34, 'subscribtion_user', 2, 7, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(35, 'subscribtion_user', 2, 7, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(36, 'subscribtion_user', 3, 8, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(37, 'subscribtion_user', 3, 8, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(38, 'subscribtion_user', 3, 8, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(39, 'subscribtion_user', 3, 8, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(40, 'subscribtion_user', 3, 8, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(41, 'subscribtion_user', 4, 9, 'DEALS_ORDER_PRICE', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(42, 'subscribtion_user', 4, 9, 'DEALS_ORDER_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(43, 'subscribtion_user', 4, 9, 'ACTIVITY_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(44, 'subscribtion_user', 4, 9, 'LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52'),
(45, 'subscribtion_user', 4, 9, 'NEW_LEAD_COUNT', 0, 0, 0, NULL, '2023-09-11 03:29:52', '2023-09-11 03:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `target_type_priorities`
--

CREATE TABLE `target_type_priorities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `target_type` bigint(20) UNSIGNED NOT NULL,
  `priority` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telescope_entries`
--

CREATE TABLE `telescope_entries` (
  `sequence` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `telescope_entries`
--

INSERT INTO `telescope_entries` (`sequence`, `uuid`, `batch_id`, `family_hash`, `should_display_on_index`, `type`, `content`, `created_at`) VALUES
(1, '9a1ae4d7-dbbd-4d98-b68e-ff13060eed7a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `users` where `type` = 2 and `users`.`deleted_at` is null\",\"time\":\"13.59\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":55,\"hash\":\"3ec00ad7be4690383e531476387dfc2f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(2, '9a1ae4d7-deeb-48e0-96ab-54aca6bf843c', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"retrieved\",\"model\":\"App\\\\Models\\\\User\",\"count\":6,\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(3, '9a1ae4d7-e7aa-44a0-bec6-0c19a3d0e0b0', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `users` where `users`.`id` = 7 and `users`.`deleted_at` is null limit 1\",\"time\":\"0.47\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"bafce275a965b8d54647d79c72f8060f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(4, '9a1ae4d7-e861-44c1-b383-14a8206c436e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 2 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.50\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":174,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(5, '9a1ae4d7-e89b-4621-9be1-817938163f5f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"retrieved\",\"model\":\"App\\\\Models\\\\SubscribtionUser\",\"count\":13,\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(6, '9a1ae4d7-e9e5-4d56-a439-a507dbf12e5a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 7, \'user\', 2591999, \'Sales\', \'User Starter - SALES - September 2023\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"2.31\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":59,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(7, '9a1ae4d7-eb32-4778-94bc-76a12f4ad7a2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 1) limit 1\",\"time\":\"0.77\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(8, '9a1ae4d7-ebef-47e6-890a-0b84b7e1ab39', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 1, \'user\', 7, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.09\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(9, '9a1ae4d7-f095-47a2-a666-cae5635935c8', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:1\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(10, '9a1ae4d7-f122-4212-bcbc-f52a2cb72320', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 1) limit 1\",\"time\":\"0.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(11, '9a1ae4d7-f1de-4786-adf7-ff4d1ba2f820', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 1, \'user\', 7, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.04\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(12, '9a1ae4d7-f20a-42a5-930c-88e40c4658de', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:2\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(13, '9a1ae4d7-f2a3-4af8-bd36-84b45f92ced7', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 1) limit 1\",\"time\":\"0.88\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(14, '9a1ae4d7-f360-4285-9aab-6a76bb902e1e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 1, \'user\', 7, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.91\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(15, '9a1ae4d7-f392-44da-a63c-af552f9b7c66', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:3\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(16, '9a1ae4d7-f3f7-47fb-bd5a-8d63ac705bcd', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 1) limit 1\",\"time\":\"0.38\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(17, '9a1ae4d7-f4a9-4762-a288-8b31e2a3512d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 1, \'user\', 7, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.05\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(18, '9a1ae4d7-f4df-479e-8fed-8263ea2794c2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:4\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(19, '9a1ae4d7-f550-4dda-aff2-6acd77991669', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 1) limit 1\",\"time\":\"0.41\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(20, '9a1ae4d7-f61b-4410-8d8a-a34d734fe8ad', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 1, \'user\', 7, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.02\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(21, '9a1ae4d7-f643-4c0a-81c6-f40ce1d3b4bd', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:5\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(22, '9a1ae4d7-f661-4429-b05d-11cfd2da8056', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:1\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(23, '9a1ae4d7-f6be-4d01-ad79-6720e7d26371', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `users` where `type` = 3 and `users`.`deleted_at` is null\",\"time\":\"0.47\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":63,\"hash\":\"3ec00ad7be4690383e531476387dfc2f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(24, '9a1ae4d7-f766-4d7e-a36e-093ed536fcb1', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `users` where `users`.`id` = 5 and `users`.`deleted_at` is null limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"bafce275a965b8d54647d79c72f8060f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(25, '9a1ae4d7-f7de-45a1-b0cb-d402882551e8', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 2 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.38\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":174,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(26, '9a1ae4d7-f8c2-4abd-a2ed-a80020752a7f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 5, \'user\', 2591999, \'BUM\', \'User Starter - BUM - September 2023\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.15\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":67,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(27, '9a1ae4d7-f9b0-48ae-beb2-f7bc03069507', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 2) limit 1\",\"time\":\"1.48\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(28, '9a1ae4d7-fa72-4c6b-9829-320d24df7942', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 2, \'user\', 5, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.07\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(29, '9a1ae4d7-faa7-4dae-a601-5c28e68edc07', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(30, '9a1ae4d7-fb16-4cd4-9884-739cd2762027', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 2) limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(31, '9a1ae4d7-fbd5-459d-add6-4643e10ffa32', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 2, \'user\', 5, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.94\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(32, '9a1ae4d7-fc0a-4596-9fa8-dcd784fd4008', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(33, '9a1ae4d7-fc7b-4b92-b78f-a42176f156ea', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 2) limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(34, '9a1ae4d7-fd3f-434c-89cd-7d5cacf87169', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 2, \'user\', 5, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.99\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(35, '9a1ae4d7-fd75-478b-b601-eaf65bfd49a3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:8\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(36, '9a1ae4d7-fde7-48cf-8753-e1f003e9a2bb', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 2) limit 1\",\"time\":\"0.44\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(37, '9a1ae4d7-fe9c-4769-a2ae-8766ae756334', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 2, \'user\', 5, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.05\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(38, '9a1ae4d7-fecb-4208-8b28-efc86245d528', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:9\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(39, '9a1ae4d7-ff28-4d33-a6fb-cc3d0b1049c2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 2) limit 1\",\"time\":\"0.41\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(40, '9a1ae4d7-ffe1-4c9d-8507-3c4ec5b28450', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 2, \'user\', 5, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.08\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(41, '9a1ae4d8-0016-422d-93e7-3c6d0dae59b7', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:10\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(42, '9a1ae4d8-0040-4769-9b19-1c7c5956536b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:2\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(43, '9a1ae4d8-0136-4e95-9d0f-8fe73b3e4022', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `users` where `users`.`id` = 6 and `users`.`deleted_at` is null limit 1\",\"time\":\"0.59\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"bafce275a965b8d54647d79c72f8060f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(44, '9a1ae4d8-01f0-47ca-a8e9-98055d6cc553', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 2 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":174,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(45, '9a1ae4d8-02d0-4dde-a774-dbcceff48554', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 6, \'user\', 2591999, \'Store Leader\', \'User Starter - STORE LEADER - September 2023\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.03\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":67,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(46, '9a1ae4d8-0352-49a0-acb1-7f12966e5b65', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 3) limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(47, '9a1ae4d8-0417-4fa7-b425-55a3f8c26202', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 3, \'user\', 6, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.01\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(48, '9a1ae4d8-043f-4d3c-85c7-5925f630d799', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:11\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(49, '9a1ae4d8-049b-4335-8280-c13011bb5c08', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 3) limit 1\",\"time\":\"0.41\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(50, '9a1ae4d8-0553-4f47-8f30-17c876d053a3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 3, \'user\', 6, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.07\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(51, '9a1ae4d8-0589-4f2d-8373-76c5a2c80473', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:12\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(52, '9a1ae4d8-0673-4940-a5d8-1eeb534a1903', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 3) limit 1\",\"time\":\"1.62\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(53, '9a1ae4d8-072d-482d-a4bc-1a55631f55f6', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 3, \'user\', 6, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.04\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(54, '9a1ae4d8-0762-44d6-8fd2-55d10efdea63', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:13\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(55, '9a1ae4d8-083d-440d-96d1-3f76c3365699', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 3) limit 1\",\"time\":\"1.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(56, '9a1ae4d8-08f1-4b23-bc4d-08c3d53b3591', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 3, \'user\', 6, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.02\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(57, '9a1ae4d8-0929-478a-a239-271bdf099f7f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:14\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(58, '9a1ae4d8-0a15-4340-816a-e78c3cb2c0fb', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 3) limit 1\",\"time\":\"1.57\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(59, '9a1ae4d8-0adb-4e7d-9b13-4bbba2679c8e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 3, \'user\', 6, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.97\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(60, '9a1ae4d8-0b0f-4f9f-aa1f-ac17d4cd15dc', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:15\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(61, '9a1ae4d8-0b38-4581-8357-0ba61809433a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:3\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(62, '9a1ae4d8-0bdd-4431-a999-c7fd3f6bb1aa', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `channels` where `channels`.`deleted_at` is null\",\"time\":\"0.37\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":71,\"hash\":\"e2e8a211de6f7710a60bf47219d9b3fb\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(63, '9a1ae4d8-0c05-4456-9f92-e444ec7364f5', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"retrieved\",\"model\":\"App\\\\Models\\\\Channel\",\"count\":4,\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(64, '9a1ae4d8-0c9f-4cf4-8020-ac658f61bb6f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `channels` where `channels`.`id` = 1 and `channels`.`deleted_at` is null limit 1\",\"time\":\"0.39\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"d9f2533621b5831c081ce9a0c5f4778e\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(65, '9a1ae4d8-0d24-4584-ab61-7f8cd96b4fa1', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 2 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.45\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":166,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(66, '9a1ae4d8-0dee-4916-885c-725b2e3bbbfa', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 1, \'channel\', 2591999, \'Channel Starter 1\', \'User Starter - Channel Starter 1\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.97\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":75,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(67, '9a1ae4d8-0e6e-4db3-b6fb-dcdaced6be28', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 4) limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(68, '9a1ae4d8-0f44-4eac-b673-daed38a3e6e3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 4, \'channel\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.24\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(69, '9a1ae4d8-0f6d-4586-b770-f5dc451b1af5', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:16\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(70, '9a1ae4d8-0fe5-440b-b7e4-e1ab46070d39', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 4) limit 1\",\"time\":\"0.49\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(71, '9a1ae4d8-109e-4b76-a755-7aaab827a627', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 4, \'channel\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.07\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(72, '9a1ae4d8-10d2-4ef1-a180-e3aacf155ce2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:17\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(73, '9a1ae4d8-1149-473a-b1e8-be142f7b3cb9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 4) limit 1\",\"time\":\"0.47\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(74, '9a1ae4d8-120c-4487-99ff-b8f4f543adee', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 4, \'channel\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.95\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(75, '9a1ae4d8-1240-4833-ad6e-9f02e59c415c', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:18\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(76, '9a1ae4d8-1344-4e9f-88b0-dc0c33ee5c96', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 4) limit 1\",\"time\":\"1.84\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(77, '9a1ae4d8-1412-4515-af33-db5b466b36fa', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 4, \'channel\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.05\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(78, '9a1ae4d8-1446-4421-9b28-d0fa43314675', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:19\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(79, '9a1ae4d8-153b-4ec5-a919-a1e8b6276547', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 4) limit 1\",\"time\":\"1.71\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(80, '9a1ae4d8-1601-4de4-83f8-d85094d85470', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 4, \'channel\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.96\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(81, '9a1ae4d8-162d-497a-af39-6b9a7132ba3f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:20\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(82, '9a1ae4d8-164c-4ab5-b78d-3dec99f199f9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:4\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(83, '9a1ae4d8-16da-446f-9092-95a8f73b814a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `channels` where `channels`.`id` = 2 and `channels`.`deleted_at` is null limit 1\",\"time\":\"0.37\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"d9f2533621b5831c081ce9a0c5f4778e\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(84, '9a1ae4d8-174b-4423-bdb4-04a88ff15136', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 3 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.34\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":166,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(85, '9a1ae4d8-1818-493f-8cc8-7a12f52d851b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 2, \'channel\', 2591999, \'Channel basic 1\', \'User Basic - Channel basic 1\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.04\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":75,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(86, '9a1ae4d8-189b-45ae-9540-29ebbb49048a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 5) limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(87, '9a1ae4d8-196a-45d5-bdf4-2fbbd337b671', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 5, \'channel\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.11\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(88, '9a1ae4d8-199f-4e58-a71e-12daf3564836', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:21\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(89, '9a1ae4d8-1a0c-4581-9eb5-bb49c454209d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 5) limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(90, '9a1ae4d8-1ac1-40b3-9514-e0bcd1a72869', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 5, \'channel\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(91, '9a1ae4d8-1af4-4b46-bce0-0ff468f44ba3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:22\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(92, '9a1ae4d8-1bd0-4be5-a9ad-df4789a808cf', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 5) limit 1\",\"time\":\"1.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(93, '9a1ae4d8-1c86-44f4-b836-8ddb24e84724', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 5, \'channel\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.03\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(94, '9a1ae4d8-1cbe-4f44-86b2-b54266be41cf', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:23\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(95, '9a1ae4d8-1db8-40bb-a108-4fa85eef3a52', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 5) limit 1\",\"time\":\"1.93\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(96, '9a1ae4d8-1e73-4fe6-a58b-2d172b3767cd', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 5, \'channel\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.08\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(97, '9a1ae4d8-1ea9-47a1-a2f5-10362542053d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:24\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(98, '9a1ae4d8-1f85-4d37-a570-48a13fc2df06', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 5) limit 1\",\"time\":\"1.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(99, '9a1ae4d8-2039-4280-8b91-93fea8fb7c10', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 5, \'channel\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.02\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(100, '9a1ae4d8-206e-4dd5-a17d-ef98f270dc2c', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:25\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(101, '9a1ae4d8-2098-4e6f-88fb-b6930460d6a4', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:5\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(102, '9a1ae4d8-20ff-44f2-8295-3c0d61050413', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`deleted_at` is null\",\"time\":\"0.39\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":79,\"hash\":\"f671b872329e5993d3ec989ff241784a\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(103, '9a1ae4d8-21ad-40af-a365-3f5269445f1d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 1 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.36\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(104, '9a1ae4d8-2281-48f0-97e1-ff8833250614', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 1, \'subscribtion_user\', 2591999, \'PT. Alba Digital Technology\', \'PT. Alba Digital Technology September 2023\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"0.99\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":83,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(105, '9a1ae4d8-22ed-4bbe-b828-c1bf05853c7b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 6) limit 1\",\"time\":\"0.44\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(106, '9a1ae4d8-23a5-481d-a1c5-974478170391', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 6, \'subscribtion_user\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.02\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(107, '9a1ae4d8-23da-4b61-abff-a5912c9dd528', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:26\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51');
INSERT INTO `telescope_entries` (`sequence`, `uuid`, `batch_id`, `family_hash`, `should_display_on_index`, `type`, `content`, `created_at`) VALUES
(108, '9a1ae4d8-2445-458f-b66d-c922d0ecab9a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 6) limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(109, '9a1ae4d8-24f8-4d8c-a089-e9cc053a7ae4', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 6, \'subscribtion_user\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.04\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(110, '9a1ae4d8-2520-4a7d-8444-3a1628ad5d11', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:27\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(111, '9a1ae4d8-25f0-4d8c-81b9-ab28bdd76764', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 6) limit 1\",\"time\":\"1.55\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(112, '9a1ae4d8-26a4-4367-8909-a68118478aa9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 6, \'subscribtion_user\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.02\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(113, '9a1ae4d8-26db-4ce2-90f5-7e2dab840d4e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:28\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(114, '9a1ae4d8-27b8-4f45-b34d-0c0e16b24c84', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 6) limit 1\",\"time\":\"1.52\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(115, '9a1ae4d8-286c-43e6-a8bd-6ebf9626e09b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 6, \'subscribtion_user\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.07\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(116, '9a1ae4d8-2895-49e8-91f7-12a5eed3f3e2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:29\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(117, '9a1ae4d8-297e-4328-a030-54fb5f9497b3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 6) limit 1\",\"time\":\"1.78\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(118, '9a1ae4d8-2a3b-4388-8f40-da7e5f56da4c', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 6, \'subscribtion_user\', 1, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.07\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(119, '9a1ae4d8-2a6f-47d1-8c07-43b16c3a1ff0', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:30\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(120, '9a1ae4d8-2a99-4091-bd20-c39891dd8d52', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(121, '9a1ae4d8-2b5d-4378-8468-7e306cb16f8d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 2 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.39\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(122, '9a1ae4d8-2c3e-43d6-aef3-09c7a07b5a96', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 2, \'subscribtion_user\', 2591999, \'User Starter\', \'User Starter September 2023\', \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.05\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":83,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(123, '9a1ae4d8-2cbe-420a-af85-d3e6218c6925', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 7) limit 1\",\"time\":\"0.41\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(124, '9a1ae4d8-2d71-4f74-a944-72be050a4cc2', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 7, \'subscribtion_user\', 2, \'2023-09-11 10:29:51\', \'2023-09-11 10:29:51\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(125, '9a1ae4d8-2d98-4124-a547-7e1ffdc8297f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:31\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:51'),
(126, '9a1ae4d8-2df7-4301-a6f7-249f39ab711e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 7) limit 1\",\"time\":\"0.40\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(127, '9a1ae4d8-2ebd-4760-997e-587c2febb7b1', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 7, \'subscribtion_user\', 2, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"0.99\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(128, '9a1ae4d8-2ef1-4cc0-978c-0d95f1dce9b6', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:32\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(129, '9a1ae4d8-2fe0-49d2-8031-5f8cabdf9eff', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 7) limit 1\",\"time\":\"1.71\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(130, '9a1ae4d8-309e-485f-b619-3c42114e21c1', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 7, \'subscribtion_user\', 2, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.14\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(131, '9a1ae4d8-30c7-40e6-8137-97c0950be820', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:33\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(132, '9a1ae4d8-31ac-4747-9f85-9a8795b91f7b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 7) limit 1\",\"time\":\"1.74\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(133, '9a1ae4d8-325e-4700-8978-99f242fcb557', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 7, \'subscribtion_user\', 2, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"0.97\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(134, '9a1ae4d8-3292-45fe-a8d2-a97fe7355820', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:34\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(135, '9a1ae4d8-3372-42cf-b289-18cc6cde08fc', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 7) limit 1\",\"time\":\"1.51\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(136, '9a1ae4d8-343f-4afc-b258-5290f57b4773', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 7, \'subscribtion_user\', 2, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(137, '9a1ae4d8-346c-4cd6-9338-cee5bc9acd07', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:35\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(138, '9a1ae4d8-348a-4435-a8d2-4cacec43d25f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(139, '9a1ae4d8-351e-4d90-afbe-74cdde63d52b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 3 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(140, '9a1ae4d8-35db-45de-8c75-67e6946204d5', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 3, \'subscribtion_user\', 2591999, \'User Basic\', \'User Basic September 2023\', \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.01\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":83,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(141, '9a1ae4d8-3658-4bae-85db-58fd6de9eb80', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 8) limit 1\",\"time\":\"0.39\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(142, '9a1ae4d8-370b-4eea-9a3f-95be833cdc76', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 8, \'subscribtion_user\', 3, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.00\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(143, '9a1ae4d8-373f-4452-ac92-568c2170965b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:36\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(144, '9a1ae4d8-37af-4410-b3d8-bd1bced12d44', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 8) limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(145, '9a1ae4d8-3879-426c-bdfa-bdbabdeefe58', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 8, \'subscribtion_user\', 3, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.12\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(146, '9a1ae4d8-38ae-41c9-884d-c3af3889505e', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:37\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(147, '9a1ae4d8-3993-4896-89a2-92da2405bd83', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 8) limit 1\",\"time\":\"1.56\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(148, '9a1ae4d8-3a62-42bd-a70a-066554b771d5', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 8, \'subscribtion_user\', 3, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.05\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(149, '9a1ae4d8-3a91-44c0-97e1-8e1380b9ec11', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:38\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(150, '9a1ae4d8-3b74-4a38-a339-223a3eff6e1a', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 8) limit 1\",\"time\":\"1.74\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(151, '9a1ae4d8-3c31-43c8-bed8-3467864c07c0', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 8, \'subscribtion_user\', 3, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.11\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(152, '9a1ae4d8-3c67-4680-9f60-6d4404dcad4c', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:39\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(153, '9a1ae4d8-3d4a-482a-a47f-dd9bf6dffdb9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 8) limit 1\",\"time\":\"1.52\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(154, '9a1ae4d8-3e09-49b5-9fc5-4498c807408f', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 8, \'subscribtion_user\', 3, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(155, '9a1ae4d8-3e3e-4543-aacd-b70443673f0d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:40\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(156, '9a1ae4d8-3e66-43b6-b861-d0e037f085b3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:8\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(157, '9a1ae4d8-3f1b-4078-bbad-60556dae9fc1', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `subscribtion_users` where `subscribtion_users`.`id` = 4 and `subscribtion_users`.`deleted_at` is null limit 1\",\"time\":\"0.37\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Models\\\\Report.php\",\"line\":51,\"hash\":\"17feaa3772c272603fbb2542ad0ecff6\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(158, '9a1ae4d8-3ff4-4adc-9e92-4841b3ba7060', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `reports` (`start_date`, `end_date`, `reportable_id`, `reportable_type`, `time_diff`, `reportable_label`, `name`, `updated_at`, `created_at`) values (\'2023-09-01 00:00:00\', \'2023-09-30 23:59:59\', 4, \'subscribtion_user\', 2591999, \'User Advance\', \'User Advance September 2023\', \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.28\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Console\\\\Commands\\\\GenerateReportsCommand.php\",\"line\":83,\"hash\":\"afee5e9f4ef33ecad229795b4883568f\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(159, '9a1ae4d8-406c-4afb-ad4f-598fb615b18d', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_PRICE\' and `report_id` = 9) limit 1\",\"time\":\"0.47\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(160, '9a1ae4d8-4139-4931-9bfb-68f03fd5524b', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_PRICE\', 9, \'subscribtion_user\', 4, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.03\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(161, '9a1ae4d8-416e-459e-addd-b780025c1ad8', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:41\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(162, '9a1ae4d8-41db-4135-9f5a-90b2ec9af5bb', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'DEALS_ORDER_COUNT\' and `report_id` = 9) limit 1\",\"time\":\"0.42\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(163, '9a1ae4d8-42a2-4bbf-98fc-bc9aee6dc4d4', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'DEALS_ORDER_COUNT\', 9, \'subscribtion_user\', 4, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(164, '9a1ae4d8-42d7-4491-ac78-6d5b797c3d03', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:42\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(165, '9a1ae4d8-43f0-47f7-a7aa-3b50ee222015', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'ACTIVITY_COUNT\' and `report_id` = 9) limit 1\",\"time\":\"2.11\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(166, '9a1ae4d8-44ac-475a-bc46-7736bdff0959', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'ACTIVITY_COUNT\', 9, \'subscribtion_user\', 4, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.08\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(167, '9a1ae4d8-44e0-44af-8980-734e43ba04e9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:43\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(168, '9a1ae4d8-45d4-4c7a-b41d-5a499b726cc3', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'LEAD_COUNT\' and `report_id` = 9) limit 1\",\"time\":\"1.70\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(169, '9a1ae4d8-46a3-48f1-a26b-dc03c9f37434', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'LEAD_COUNT\', 9, \'subscribtion_user\', 4, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.06\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(170, '9a1ae4d8-46dd-428b-9db7-58c371eaf547', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:44\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(171, '9a1ae4d8-47e1-49d8-8803-b31dfb1a4f39', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select * from `targets` where (`type` = \'NEW_LEAD_COUNT\' and `report_id` = 9) limit 1\",\"time\":\"1.86\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"f0ddae525efcc33f625dbb6f838f29e7\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(172, '9a1ae4d8-489e-40f2-9d89-3416d67a31b9', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'query', '{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `targets` (`type`, `report_id`, `model_type`, `model_id`, `updated_at`, `created_at`) values (\'NEW_LEAD_COUNT\', 9, \'subscribtion_user\', 4, \'2023-09-11 10:29:52\', \'2023-09-11 10:29:52\')\",\"time\":\"1.17\",\"slow\":false,\"file\":\"C:\\\\xampp\\\\htdocs\\\\sms-monorepo\\\\packages\\\\sms-backend\\\\app\\\\Services\\\\ReportService.php\",\"line\":37,\"hash\":\"3923f4ef0c8ae21f225e27e30243f72b\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(173, '9a1ae4d8-48c5-4486-93ba-68cac9ac2f18', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Target:45\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(174, '9a1ae4d8-48e3-486d-bd76-114caed52b79', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'model', '{\"action\":\"created\",\"model\":\"App\\\\Models\\\\Report:9\",\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52'),
(175, '9a1ae4d8-493e-4009-8f00-b408d4fb4149', '9a1ae4d8-49ce-4fa9-8a16-5bd81f4ed645', NULL, 1, 'command', '{\"command\":\"reports:generate\",\"exit_code\":0,\"arguments\":{\"command\":\"reports:generate\"},\"options\":{\"month\":null,\"year\":null,\"help\":false,\"quiet\":false,\"verbose\":false,\"version\":false,\"ansi\":null,\"no-interaction\":false,\"env\":null},\"hostname\":\"LAPTOP-FQIM4O5H\"}', '2023-09-11 10:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `telescope_entries_tags`
--

CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `telescope_entries_tags`
--

INSERT INTO `telescope_entries_tags` (`entry_uuid`, `tag`) VALUES
('9a1ae4d7-deeb-48e0-96ab-54aca6bf843c', 'App\\Models\\User'),
('9a1ae4d7-e89b-4621-9be1-817938163f5f', 'App\\Models\\SubscribtionUser'),
('9a1ae4d7-f095-47a2-a666-cae5635935c8', 'App\\Models\\Target:1'),
('9a1ae4d7-f20a-42a5-930c-88e40c4658de', 'App\\Models\\Target:2'),
('9a1ae4d7-f392-44da-a63c-af552f9b7c66', 'App\\Models\\Target:3'),
('9a1ae4d7-f4df-479e-8fed-8263ea2794c2', 'App\\Models\\Target:4'),
('9a1ae4d7-f643-4c0a-81c6-f40ce1d3b4bd', 'App\\Models\\Target:5'),
('9a1ae4d7-f661-4429-b05d-11cfd2da8056', 'App\\Models\\Report:1'),
('9a1ae4d7-faa7-4dae-a601-5c28e68edc07', 'App\\Models\\Target:6'),
('9a1ae4d7-fc0a-4596-9fa8-dcd784fd4008', 'App\\Models\\Target:7'),
('9a1ae4d7-fd75-478b-b601-eaf65bfd49a3', 'App\\Models\\Target:8'),
('9a1ae4d7-fecb-4208-8b28-efc86245d528', 'App\\Models\\Target:9'),
('9a1ae4d8-0016-422d-93e7-3c6d0dae59b7', 'App\\Models\\Target:10'),
('9a1ae4d8-0040-4769-9b19-1c7c5956536b', 'App\\Models\\Report:2'),
('9a1ae4d8-043f-4d3c-85c7-5925f630d799', 'App\\Models\\Target:11'),
('9a1ae4d8-0589-4f2d-8373-76c5a2c80473', 'App\\Models\\Target:12'),
('9a1ae4d8-0762-44d6-8fd2-55d10efdea63', 'App\\Models\\Target:13'),
('9a1ae4d8-0929-478a-a239-271bdf099f7f', 'App\\Models\\Target:14'),
('9a1ae4d8-0b0f-4f9f-aa1f-ac17d4cd15dc', 'App\\Models\\Target:15'),
('9a1ae4d8-0b38-4581-8357-0ba61809433a', 'App\\Models\\Report:3'),
('9a1ae4d8-0c05-4456-9f92-e444ec7364f5', 'App\\Models\\Channel'),
('9a1ae4d8-0f6d-4586-b770-f5dc451b1af5', 'App\\Models\\Target:16'),
('9a1ae4d8-10d2-4ef1-a180-e3aacf155ce2', 'App\\Models\\Target:17'),
('9a1ae4d8-1240-4833-ad6e-9f02e59c415c', 'App\\Models\\Target:18'),
('9a1ae4d8-1446-4421-9b28-d0fa43314675', 'App\\Models\\Target:19'),
('9a1ae4d8-162d-497a-af39-6b9a7132ba3f', 'App\\Models\\Target:20'),
('9a1ae4d8-164c-4ab5-b78d-3dec99f199f9', 'App\\Models\\Report:4'),
('9a1ae4d8-199f-4e58-a71e-12daf3564836', 'App\\Models\\Target:21'),
('9a1ae4d8-1af4-4b46-bce0-0ff468f44ba3', 'App\\Models\\Target:22'),
('9a1ae4d8-1cbe-4f44-86b2-b54266be41cf', 'App\\Models\\Target:23'),
('9a1ae4d8-1ea9-47a1-a2f5-10362542053d', 'App\\Models\\Target:24'),
('9a1ae4d8-206e-4dd5-a17d-ef98f270dc2c', 'App\\Models\\Target:25'),
('9a1ae4d8-2098-4e6f-88fb-b6930460d6a4', 'App\\Models\\Report:5'),
('9a1ae4d8-23da-4b61-abff-a5912c9dd528', 'App\\Models\\Target:26'),
('9a1ae4d8-2520-4a7d-8444-3a1628ad5d11', 'App\\Models\\Target:27'),
('9a1ae4d8-26db-4ce2-90f5-7e2dab840d4e', 'App\\Models\\Target:28'),
('9a1ae4d8-2895-49e8-91f7-12a5eed3f3e2', 'App\\Models\\Target:29'),
('9a1ae4d8-2a6f-47d1-8c07-43b16c3a1ff0', 'App\\Models\\Target:30'),
('9a1ae4d8-2a99-4091-bd20-c39891dd8d52', 'App\\Models\\Report:6'),
('9a1ae4d8-2d98-4124-a547-7e1ffdc8297f', 'App\\Models\\Target:31'),
('9a1ae4d8-2ef1-4cc0-978c-0d95f1dce9b6', 'App\\Models\\Target:32'),
('9a1ae4d8-30c7-40e6-8137-97c0950be820', 'App\\Models\\Target:33'),
('9a1ae4d8-3292-45fe-a8d2-a97fe7355820', 'App\\Models\\Target:34'),
('9a1ae4d8-346c-4cd6-9338-cee5bc9acd07', 'App\\Models\\Target:35'),
('9a1ae4d8-348a-4435-a8d2-4cacec43d25f', 'App\\Models\\Report:7'),
('9a1ae4d8-373f-4452-ac92-568c2170965b', 'App\\Models\\Target:36'),
('9a1ae4d8-38ae-41c9-884d-c3af3889505e', 'App\\Models\\Target:37'),
('9a1ae4d8-3a91-44c0-97e1-8e1380b9ec11', 'App\\Models\\Target:38'),
('9a1ae4d8-3c67-4680-9f60-6d4404dcad4c', 'App\\Models\\Target:39'),
('9a1ae4d8-3e3e-4543-aacd-b70443673f0d', 'App\\Models\\Target:40'),
('9a1ae4d8-3e66-43b6-b861-d0e037f085b3', 'App\\Models\\Report:8'),
('9a1ae4d8-416e-459e-addd-b780025c1ad8', 'App\\Models\\Target:41'),
('9a1ae4d8-42d7-4491-ac78-6d5b797c3d03', 'App\\Models\\Target:42'),
('9a1ae4d8-44e0-44af-8980-734e43ba04e9', 'App\\Models\\Target:43'),
('9a1ae4d8-46dd-428b-9db7-58c371eaf547', 'App\\Models\\Target:44'),
('9a1ae4d8-48c5-4486-93ba-68cac9ac2f18', 'App\\Models\\Target:45'),
('9a1ae4d8-48e3-486d-bd76-114caed52b79', 'App\\Models\\Report:9');

-- --------------------------------------------------------

--
-- Table structure for table `telescope_monitoring`
--

CREATE TABLE `telescope_monitoring` (
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribtion_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1,
  `channel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supervisor_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supervisor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `_lft` int(10) UNSIGNED NOT NULL,
  `_rgt` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `subscribtion_user_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `type`, `channel_id`, `supervisor_type_id`, `supervisor_id`, `_lft`, `_rgt`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Super Admin', 'superadmin@gmail.com', NULL, '$2y$10$fuNYGyoFJon664OPWQD/OehcsTzaFQOk1ng/QxXzYNAbGhZHypUz6', NULL, 1, NULL, NULL, NULL, 1, 2, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(2, 1, 'Admin ALBA', 'admin@gmail.com', NULL, '$2y$10$G5.c9tJds288nzgE9Xcxo.PUU/ftn786w3tV4jzWsxbPJROu6g7a2', NULL, 1, NULL, NULL, NULL, 3, 4, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(3, 2, 'Admin Starter', 'adminstarter@gmail.com', NULL, '$2y$10$FbDBY6rTfNM.zmTkmGyn4OvjIFz4JO8R.z7rAMXH91/MXyZwDNBUK', NULL, 1, NULL, NULL, NULL, 5, 6, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(4, 2, 'Director', 'director@gmail.com', NULL, '$2y$10$FiGIJmcRb062LO/dwuf.bOXI/5DykGu4swVzJeSn1Qci2G5JFJLSu', NULL, 4, 1, NULL, NULL, 7, 8, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(5, 2, 'BUM', 'bum@gmail.com', NULL, '$2y$10$R5PVSkqKmzg/gCw0WRFVKeK/DRaiznDSOAjvbWc5jzhNA9bANQ0c6', NULL, 3, 1, 2, NULL, 9, 14, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(6, 2, 'Store Leader', 'storeleader@gmail.com', NULL, '$2y$10$GcX67DqlHtpGEn4mat8W0.F9DD74Sdgx7sHX7xSGBlj7yFZ.jqKSe', NULL, 3, 1, 1, 5, 10, 13, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(7, 2, 'Sales', 'sales@gmail.com', NULL, '$2y$10$3qpQ6zevf8rY3k4PBM.lr.ht5OusIVBCRH3xoHBnIrg6MJHTCazSm', NULL, 2, 1, NULL, 6, 11, 12, '2023-09-11 03:28:34', '2023-09-11 03:28:34', NULL),
(8, 3, 'Admin basic', 'adminbasic@gmail.com', NULL, '$2y$10$cxqSQT9EElFxSc8pFnyGb.CIolxfIRX/YXOilq0O4STdqiBfnrs8S', NULL, 1, NULL, NULL, NULL, 15, 16, '2023-09-11 03:28:35', '2023-09-11 03:28:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_alerts`
--

CREATE TABLE `user_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alert_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alert_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_user_alert`
--

CREATE TABLE `user_user_alert` (
  `user_alert_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_lead_id_foreign` (`lead_id`),
  ADD KEY `activities_customer_id_foreign` (`customer_id`),
  ADD KEY `activities_channel_id_foreign` (`channel_id`),
  ADD KEY `activities_order_id_foreign` (`order_id`),
  ADD KEY `activities_user_id_foreign` (`user_id`),
  ADD KEY `activities_follow_up_method_index` (`follow_up_method`),
  ADD KEY `activities_status_index` (`status`),
  ADD KEY `activities_latest_activity_comment_id_foreign` (`latest_activity_comment_id`);

--
-- Indexes for table `activity_brand_values`
--
ALTER TABLE `activity_brand_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_brand_values_user_id_foreign` (`user_id`),
  ADD KEY `activity_brand_values_product_brand_id_foreign` (`product_brand_id`),
  ADD KEY `activity_brand_values_lead_id_foreign` (`lead_id`),
  ADD KEY `activity_brand_values_activity_id_order_id_index` (`activity_id`,`order_id`);

--
-- Indexes for table `activity_comments`
--
ALTER TABLE `activity_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_comments_user_id_foreign` (`user_id`),
  ADD KEY `activity_comments_activity_id_foreign` (`activity_id`),
  ADD KEY `activity_comments_activity_comment_id_foreign` (`activity_comment_id`);

--
-- Indexes for table `activity_product`
--
ALTER TABLE `activity_product`
  ADD KEY `activity_id_fk_3286780` (`activity_id`),
  ADD KEY `product_id_fk_3286780` (`product_id`);

--
-- Indexes for table `activity_product_brand`
--
ALTER TABLE `activity_product_brand`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_product_brand_activity_id_foreign` (`activity_id`),
  ADD KEY `activity_product_brand_product_brand_id_foreign` (`product_brand_id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand_categories`
--
ALTER TABLE `brand_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_categories_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`),
  ADD KEY `carts_customer_id_foreign` (`customer_id`),
  ADD KEY `carts_discount_id_foreign` (`discount_id`);

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channels_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `channel_user`
--
ALTER TABLE `channel_user`
  ADD KEY `user_id_fk_3366006` (`user_id`),
  ADD KEY `channel_id_fk_3366006` (`channel_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`),
  ADD KEY `customers_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `customers_first_name_index` (`first_name`),
  ADD KEY `customers_last_name_index` (`last_name`),
  ADD KEY `customers_default_address_id_foreign` (`default_address_id`);

--
-- Indexes for table `customer_discount_uses`
--
ALTER TABLE `customer_discount_uses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_discount_uses_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_discount_uses_discount_id_foreign` (`discount_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `discounts_promo_id_foreign` (`promo_id`),
  ADD KEY `discounts_product_brand_id_foreign` (`product_brand_id`),
  ADD KEY `discounts_name_index` (`name`),
  ADD KEY `discounts_activation_code_index` (`activation_code`);

--
-- Indexes for table `exports`
--
ALTER TABLE `exports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `import_batches`
--
ALTER TABLE `import_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_batches_user_id_foreign` (`user_id`),
  ADD KEY `import_batches_status_index` (`status`),
  ADD KEY `import_batches_type_index` (`type`);

--
-- Indexes for table `import_lines`
--
ALTER TABLE `import_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_lines_import_batch_id_foreign` (`import_batch_id`),
  ADD KEY `import_lines_status_index` (`status`),
  ADD KEY `import_lines_preview_status_index` (`preview_status`),
  ADD KEY `import_lines_row_index` (`row`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_lead_category_id_foreign` (`lead_category_id`),
  ADD KEY `leads_sub_lead_category_id_foreign` (`sub_lead_category_id`),
  ADD KEY `leads_user_id_foreign` (`user_id`),
  ADD KEY `leads_channel_id_foreign` (`channel_id`),
  ADD KEY `leads_customer_id_group_id_index` (`customer_id`,`group_id`),
  ADD KEY `leads_type_index` (`type`),
  ADD KEY `leads_status_index` (`status`),
  ADD KEY `leads_is_unhandled_index` (`is_unhandled`),
  ADD KEY `leads_user_referral_id_index` (`user_referral_id`),
  ADD KEY `leads_has_pending_status_change_index` (`has_pending_status_change`);

--
-- Indexes for table `lead_categories`
--
ALTER TABLE `lead_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_categories_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`subscribtion_user_id`,`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  ADD KEY `model_has_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `model_has_permissions_team_foreign_key_index` (`subscribtion_user_id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`subscribtion_user_id`,`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  ADD KEY `model_has_roles_role_id_foreign` (`role_id`),
  ADD KEY `model_has_roles_team_foreign_key_index` (`subscribtion_user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_devices`
--
ALTER TABLE `notification_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_devices_user_id_code_unique` (`user_id`,`code`),
  ADD KEY `notification_devices_code_index` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_lead_id_foreign` (`lead_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_channel_id_foreign` (`channel_id`),
  ADD KEY `orders_discount_id_foreign` (`discount_id`),
  ADD KEY `orders_approved_by_foreign` (`approved_by`),
  ADD KEY `orders_invoice_number_index` (`invoice_number`),
  ADD KEY `orders_status_index` (`status`),
  ADD KEY `orders_payment_status_index` (`payment_status`),
  ADD KEY `orders_stock_status_index` (`stock_status`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_product_id_foreign` (`product_id`),
  ADD KEY `order_details_order_id_status_index` (`order_id`,`status`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `payments_payment_type_id_foreign` (`payment_type_id`),
  ADD KEY `payments_approved_by_id_foreign` (`approved_by_id`),
  ADD KEY `payments_added_by_id_foreign` (`added_by_id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_reference_index` (`reference`),
  ADD KEY `payments_status_index` (`status`);

--
-- Indexes for table `payment_categories`
--
ALTER TABLE `payment_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_categories_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_types_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `payment_types_payment_category_id_foreign` (`payment_category_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `products_product_category_id_foreign` (`product_category_id`),
  ADD KEY `products_product_brand_id_foreign` (`product_brand_id`),
  ADD KEY `products_name_index` (`name`),
  ADD KEY `products_sku_index` (`sku`);

--
-- Indexes for table `product_brands`
--
ALTER TABLE `product_brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_brands_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `product_brands_name_index` (`name`);

--
-- Indexes for table `product_brand_categories`
--
ALTER TABLE `product_brand_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_brand_categories_product_brand_id_foreign` (`product_brand_id`),
  ADD KEY `product_brand_categories_brand_category_id_foreign` (`brand_category_id`);

--
-- Indexes for table `product_brand_leads`
--
ALTER TABLE `product_brand_leads`
  ADD KEY `product_brand_leads_lead_id_foreign` (`lead_id`),
  ADD KEY `product_brand_leads_product_brand_id_foreign` (`product_brand_id`);

--
-- Indexes for table `product_brand_users`
--
ALTER TABLE `product_brand_users`
  ADD KEY `product_brand_users_user_id_foreign` (`user_id`),
  ADD KEY `product_brand_users_product_brand_id_foreign` (`product_brand_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_categories_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promos_subscribtion_user_id_foreign` (`subscribtion_user_id`);

--
-- Indexes for table `religions`
--
ALTER TABLE `religions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`),
  ADD KEY `reports_start_date_index` (`start_date`),
  ADD KEY `reports_end_date_index` (`end_date`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_subscribtion_user_id_name_guard_name_unique` (`subscribtion_user_id`,`name`,`guard_name`),
  ADD KEY `roles_team_foreign_key_index` (`subscribtion_user_id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `seeders`
--
ALTER TABLE `seeders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stocks_channel_id_product_id_unique` (`channel_id`,`product_id`),
  ADD KEY `stocks_product_id_foreign` (`product_id`);

--
-- Indexes for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_histories_stock_id_foreign` (`stock_id`),
  ADD KEY `stock_histories_order_detail_id_foreign` (`order_detail_id`),
  ADD KEY `stock_histories_stock_transfer_id_foreign` (`stock_transfer_id`),
  ADD KEY `stock_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfers_from_channel_id_foreign` (`from_channel_id`),
  ADD KEY `stock_transfers_to_channel_id_foreign` (`to_channel_id`),
  ADD KEY `stock_transfers_product_id_foreign` (`product_id`);

--
-- Indexes for table `subscribtion_packages`
--
ALTER TABLE `subscribtion_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribtion_users`
--
ALTER TABLE `subscribtion_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscribtion_users_subscribtion_package_id_foreign` (`subscribtion_package_id`);

--
-- Indexes for table `sub_lead_categories`
--
ALTER TABLE `sub_lead_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_lead_categories_lead_category_id_foreign` (`lead_category_id`);

--
-- Indexes for table `supervisor_discount_approval_limits`
--
ALTER TABLE `supervisor_discount_approval_limits`
  ADD KEY `supervisor_discount_approval_limits_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `supervisor_discount_approval_limits_supervisor_type_id_foreign` (`supervisor_type_id`);

--
-- Indexes for table `supervisor_types`
--
ALTER TABLE `supervisor_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supervisor_types_can_assign_lead_index` (`can_assign_lead`);

--
-- Indexes for table `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `targets_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `targets_report_id_foreign` (`report_id`),
  ADD KEY `targets_type_index` (`type`);

--
-- Indexes for table `target_type_priorities`
--
ALTER TABLE `target_type_priorities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `target_type_priorities_target_type_unique` (`target_type`),
  ADD KEY `target_type_priorities_priority_index` (`priority`);

--
-- Indexes for table `telescope_entries`
--
ALTER TABLE `telescope_entries`
  ADD PRIMARY KEY (`sequence`),
  ADD UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  ADD KEY `telescope_entries_batch_id_index` (`batch_id`),
  ADD KEY `telescope_entries_family_hash_index` (`family_hash`),
  ADD KEY `telescope_entries_created_at_index` (`created_at`),
  ADD KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`);

--
-- Indexes for table `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD KEY `telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`),
  ADD KEY `telescope_entries_tags_tag_index` (`tag`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_subscribtion_user_id_foreign` (`subscribtion_user_id`),
  ADD KEY `users_channel_id_foreign` (`channel_id`),
  ADD KEY `users_supervisor_type_id_foreign` (`supervisor_type_id`),
  ADD KEY `users_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `users_type_index` (`type`);

--
-- Indexes for table `user_alerts`
--
ALTER TABLE `user_alerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_user_alert`
--
ALTER TABLE `user_user_alert`
  ADD KEY `user_alert_id_fk_3294269` (`user_alert_id`),
  ADD KEY `user_id_fk_3294269` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_brand_values`
--
ALTER TABLE `activity_brand_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_comments`
--
ALTER TABLE `activity_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_product_brand`
--
ALTER TABLE `activity_product_brand`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand_categories`
--
ALTER TABLE `brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_discount_uses`
--
ALTER TABLE `customer_discount_uses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exports`
--
ALTER TABLE `exports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_batches`
--
ALTER TABLE `import_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_lines`
--
ALTER TABLE `import_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lead_categories`
--
ALTER TABLE `lead_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `notification_devices`
--
ALTER TABLE `notification_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_categories`
--
ALTER TABLE `payment_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_brands`
--
ALTER TABLE `product_brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_brand_categories`
--
ALTER TABLE `product_brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `religions`
--
ALTER TABLE `religions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seeders`
--
ALTER TABLE `seeders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_histories`
--
ALTER TABLE `stock_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribtion_packages`
--
ALTER TABLE `subscribtion_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscribtion_users`
--
ALTER TABLE `subscribtion_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sub_lead_categories`
--
ALTER TABLE `sub_lead_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supervisor_types`
--
ALTER TABLE `supervisor_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `targets`
--
ALTER TABLE `targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `target_type_priorities`
--
ALTER TABLE `target_type_priorities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telescope_entries`
--
ALTER TABLE `telescope_entries`
  MODIFY `sequence` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_alerts`
--
ALTER TABLE `user_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `activities_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `activities_latest_activity_comment_id_foreign` FOREIGN KEY (`latest_activity_comment_id`) REFERENCES `activity_comments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  ADD CONSTRAINT `activities_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `activity_brand_values`
--
ALTER TABLE `activity_brand_values`
  ADD CONSTRAINT `activity_brand_values_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_brand_values_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_brand_values_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `activity_comments`
--
ALTER TABLE `activity_comments`
  ADD CONSTRAINT `activity_comments_activity_comment_id_foreign` FOREIGN KEY (`activity_comment_id`) REFERENCES `activity_comments` (`id`),
  ADD CONSTRAINT `activity_comments_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`),
  ADD CONSTRAINT `activity_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `activity_product`
--
ALTER TABLE `activity_product`
  ADD CONSTRAINT `activity_id_fk_3286780` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_id_fk_3286780` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_product_brand`
--
ALTER TABLE `activity_product_brand`
  ADD CONSTRAINT `activity_product_brand_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_product_brand_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `brand_categories`
--
ALTER TABLE `brand_categories`
  ADD CONSTRAINT `brand_categories_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `carts_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `channels_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `channel_user`
--
ALTER TABLE `channel_user`
  ADD CONSTRAINT `channel_id_fk_3366006` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk_3366006` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_default_address_id_foreign` FOREIGN KEY (`default_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `customer_discount_uses`
--
ALTER TABLE `customer_discount_uses`
  ADD CONSTRAINT `customer_discount_uses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customer_discount_uses_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`);

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`),
  ADD CONSTRAINT `discounts_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `discounts_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `import_batches`
--
ALTER TABLE `import_batches`
  ADD CONSTRAINT `import_batches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `import_lines`
--
ALTER TABLE `import_lines`
  ADD CONSTRAINT `import_lines_import_batch_id_foreign` FOREIGN KEY (`import_batch_id`) REFERENCES `import_batches` (`id`);

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `leads_lead_category_id_foreign` FOREIGN KEY (`lead_category_id`) REFERENCES `lead_categories` (`id`),
  ADD CONSTRAINT `leads_sub_lead_category_id_foreign` FOREIGN KEY (`sub_lead_category_id`) REFERENCES `sub_lead_categories` (`id`),
  ADD CONSTRAINT `leads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lead_categories`
--
ALTER TABLE `lead_categories`
  ADD CONSTRAINT `lead_categories_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

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
-- Constraints for table `notification_devices`
--
ALTER TABLE `notification_devices`
  ADD CONSTRAINT `notification_devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `orders_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  ADD CONSTRAINT `orders_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_added_by_id_foreign` FOREIGN KEY (`added_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_approved_by_id_foreign` FOREIGN KEY (`approved_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `payments_payment_type_id_foreign` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`id`),
  ADD CONSTRAINT `payments_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `payment_categories`
--
ALTER TABLE `payment_categories`
  ADD CONSTRAINT `payment_categories_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD CONSTRAINT `payment_types_payment_category_id_foreign` FOREIGN KEY (`payment_category_id`) REFERENCES `payment_categories` (`id`),
  ADD CONSTRAINT `payment_types_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`),
  ADD CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`),
  ADD CONSTRAINT `products_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `product_brands`
--
ALTER TABLE `product_brands`
  ADD CONSTRAINT `product_brands_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `product_brand_categories`
--
ALTER TABLE `product_brand_categories`
  ADD CONSTRAINT `product_brand_categories_brand_category_id_foreign` FOREIGN KEY (`brand_category_id`) REFERENCES `brand_categories` (`id`),
  ADD CONSTRAINT `product_brand_categories_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`);

--
-- Constraints for table `product_brand_leads`
--
ALTER TABLE `product_brand_leads`
  ADD CONSTRAINT `product_brand_leads_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  ADD CONSTRAINT `product_brand_leads_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`);

--
-- Constraints for table `product_brand_users`
--
ALTER TABLE `product_brand_users`
  ADD CONSTRAINT `product_brand_users_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_brand_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `promos`
--
ALTER TABLE `promos`
  ADD CONSTRAINT `promos_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD CONSTRAINT `stock_histories_order_detail_id_foreign` FOREIGN KEY (`order_detail_id`) REFERENCES `order_details` (`id`),
  ADD CONSTRAINT `stock_histories_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`),
  ADD CONSTRAINT `stock_histories_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`),
  ADD CONSTRAINT `stock_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_from_channel_id_foreign` FOREIGN KEY (`from_channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `stock_transfers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_transfers_to_channel_id_foreign` FOREIGN KEY (`to_channel_id`) REFERENCES `channels` (`id`);

--
-- Constraints for table `subscribtion_users`
--
ALTER TABLE `subscribtion_users`
  ADD CONSTRAINT `subscribtion_users_subscribtion_package_id_foreign` FOREIGN KEY (`subscribtion_package_id`) REFERENCES `subscribtion_packages` (`id`);

--
-- Constraints for table `sub_lead_categories`
--
ALTER TABLE `sub_lead_categories`
  ADD CONSTRAINT `sub_lead_categories_lead_category_id_foreign` FOREIGN KEY (`lead_category_id`) REFERENCES `lead_categories` (`id`);

--
-- Constraints for table `supervisor_discount_approval_limits`
--
ALTER TABLE `supervisor_discount_approval_limits`
  ADD CONSTRAINT `supervisor_discount_approval_limits_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`),
  ADD CONSTRAINT `supervisor_discount_approval_limits_supervisor_type_id_foreign` FOREIGN KEY (`supervisor_type_id`) REFERENCES `supervisor_types` (`id`);

--
-- Constraints for table `targets`
--
ALTER TABLE `targets`
  ADD CONSTRAINT `targets_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `users_subscribtion_user_id_foreign` FOREIGN KEY (`subscribtion_user_id`) REFERENCES `subscribtion_users` (`id`),
  ADD CONSTRAINT `users_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_supervisor_type_id_foreign` FOREIGN KEY (`supervisor_type_id`) REFERENCES `supervisor_types` (`id`);

--
-- Constraints for table `user_user_alert`
--
ALTER TABLE `user_user_alert`
  ADD CONSTRAINT `user_alert_id_fk_3294269` FOREIGN KEY (`user_alert_id`) REFERENCES `user_alerts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk_3294269` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
