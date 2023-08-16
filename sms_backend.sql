-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2023 at 08:24 AM
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
(1, 'Tangerang Raya', NULL, NULL, '15710', 'Tangerang', 'Indonesia', 'Banten', 1, '0987654321', 1, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 2, 'Brand Category 1', 'BC1', 'brand-category-1', '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 2, 'Channel Starter 1', '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(2, 3, 'Channel basic 1', '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL);

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
(1, 2, 1, 'Customer satu', 'OKE', 'customer@gmail.com', '0987654321', '2023-08-16', 'description customer', NULL, 0, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 2, 'Lead Category 1', 'description Lead Category 1', NULL, '2023-08-16 06:24:18', '2023-08-16 06:24:18');

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
(28, '2021_03_07_000023_create_targets_table', 1),
(29, '2021_03_07_000025_create_target_schedules_table', 1),
(30, '2021_03_07_000031_create_orders_table', 1),
(31, '2021_03_07_000032_create_activities_table', 1),
(32, '2021_03_07_000034_create_order_trackings_table', 1),
(33, '2021_03_07_000035_create_order_details_table', 1),
(34, '2021_03_07_000037_create_media_table', 1),
(35, '2021_03_07_000038_create_audit_logs_table', 1),
(36, '2021_03_07_000040_create_channel_user_pivot_table', 1),
(37, '2021_03_07_000041_create_company_user_pivot_table', 1),
(38, '2021_03_07_000042_create_order_target_pivot_table', 1),
(39, '2021_03_07_000043_create_product_product_category_pivot_table', 1),
(40, '2021_03_07_000044_create_order_detail_target_pivot_table', 1),
(41, '2021_03_07_000045_create_product_product_tag_pivot_table', 1),
(42, '2021_03_07_000048_create_user_user_alert_pivot_table', 1),
(43, '2021_03_07_000052_create_stocks_table', 1),
(44, '2021_03_07_000053_create_stock_transfers_table', 1),
(45, '2021_03_07_000056_create_shipments_table', 1),
(46, '2021_03_07_000057_create_activity_product_pivot_table', 1),
(47, '2021_03_07_000058_create_payments_table', 1),
(48, '2021_03_07_000079_create_qa_table', 1),
(49, '2021_03_19_165256_create_notifications_table', 1),
(50, '2021_04_07_002131_create_carts_table', 1),
(51, '2021_04_10_234713_create_company_data_table', 1),
(52, '2021_04_14_215644_create_qa_topic_users_table', 1),
(53, '2021_04_14_221305_create_qa_message_users_table', 1),
(54, '2021_04_20_003044_customer_discount_use', 1),
(55, '2021_05_06_222516_create_import_batches_table', 1),
(56, '2021_05_06_223702_create_import_lines_table', 1),
(57, '2021_05_07_140028_create_seeders_table', 1),
(58, '2021_05_23_141546_create_stock_histories_table', 1),
(59, '2021_05_24_152823_create_company_accounts_table', 1),
(60, '2021_06_08_224714_add_shipment_order_details', 1),
(61, '2021_06_14_235703_create_notification_devices_table', 1),
(62, '2021_06_23_004512_recreate_report_target_table', 1),
(63, '2021_07_15_021437_create_target_type_priorities_table', 1),
(64, '2021_10_05_180000_create_activity_brand_table', 1),
(65, '2021_10_05_183045_add_estimated_value_to_activities_table', 1),
(66, '2021_10_21_034456_create_brand_categories_table', 1),
(67, '2021_10_21_044339_create_product_brand_categories_table', 1),
(68, '2021_12_13_093940_update_stock_transfers_table', 1),
(69, '2021_12_14_024416_recreate_stock_transfers_table', 1),
(70, '2021_12_14_041829_add_indent_to_stocks_table', 1),
(71, '2021_12_14_041830_create_religions_table', 1),
(72, '2021_12_23_030212_new_update_stock_transfers_table', 1),
(73, '2021_12_23_094505_add_order_id_to_stock_transfers_table', 1),
(74, '2021_12_27_035014_add_total_stock_to_stocks_table', 1),
(75, '2022_01_20_042257_create_order_detail_demands_table', 1),
(76, '2022_01_20_044502_create_cart_demands_table', 1),
(77, '2022_04_18_032112_create_exports_table', 1),
(78, '2022_04_22_095017_create_supervisor_discount_approval_limits_table', 1),
(79, '2022_04_27_071934_create_currencies_table', 1),
(80, '2022_05_11_094937_create_activity_brand_values_table', 1),
(81, '2022_06_22_230356_create_user_companies_table', 1),
(82, '2022_06_30_111625_create_order_discounts_table', 1),
(83, '2022_09_30_140816_create_new_targets_table', 1),
(84, '2022_10_04_153824_create_product_brand_users_table', 1),
(85, '2022_10_07_132636_create_product_brand_leads_table', 1),
(86, '2023_06_22_144407_create_permission_tables', 1);

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
-- Table structure for table `new_targets`
--

CREATE TABLE `new_targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` smallint(6) DEFAULT NULL,
  `target_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` smallint(5) UNSIGNED NOT NULL,
  `target` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `channel_id` bigint(20) UNSIGNED NOT NULL,
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
(1, 2, 'Transfer', '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 2, 'BCA Prioritas', 0, 1, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 'dashboard_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(2, 'user_management_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(3, 'user_access', 'web', 2, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(4, 'user_create', 'web', 3, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(5, 'user_edit', 'web', 3, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(6, 'user_delete', 'web', 3, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(7, 'role_access', 'web', 2, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(8, 'role_create', 'web', 7, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(9, 'role_edit', 'web', 7, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(10, 'role_delete', 'web', 7, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(11, 'permission_access', 'web', 2, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(12, 'permission_create', 'web', 11, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(13, 'permission_edit', 'web', 11, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(14, 'permission_delete', 'web', 11, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(15, 'corporate_management_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(16, 'channel_access', 'web', 15, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(17, 'channel_create', 'web', 16, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(18, 'channel_edit', 'web', 16, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(19, 'channel_delete', 'web', 16, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(20, 'crm_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(21, 'lead_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(22, 'lead_create', 'web', 21, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(23, 'lead_edit', 'web', 21, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(24, 'lead_delete', 'web', 21, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(25, 'lead_category_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(26, 'lead_category_create', 'web', 25, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(27, 'lead_category_edit', 'web', 25, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(28, 'lead_category_delete', 'web', 25, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(29, 'sub_lead_category_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(30, 'sub_lead_category_create', 'web', 29, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(31, 'sub_lead_category_edit', 'web', 29, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(32, 'sub_lead_category_delete', 'web', 29, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(33, 'activity_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(34, 'activity_create', 'web', 33, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(35, 'activity_edit', 'web', 33, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(36, 'activity_delete', 'web', 33, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(37, 'customer_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(38, 'customer_create', 'web', 37, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(39, 'customer_edit', 'web', 37, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(40, 'customer_delete', 'web', 37, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(41, 'address_access', 'web', 20, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(42, 'address_create', 'web', 41, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(43, 'address_edit', 'web', 41, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(44, 'address_delete', 'web', 41, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(45, 'product_management_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(46, 'brand_category_access', 'web', 45, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(47, 'brand_category_create', 'web', 46, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(48, 'brand_category_edit', 'web', 46, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(49, 'brand_category_delete', 'web', 46, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(50, 'product_brand_access', 'web', 45, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(51, 'product_brand_create', 'web', 50, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(52, 'product_brand_edit', 'web', 50, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(53, 'product_brand_delete', 'web', 50, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(54, 'product_category_access', 'web', 45, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(55, 'product_category_create', 'web', 54, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(56, 'product_category_edit', 'web', 54, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(57, 'product_category_delete', 'web', 54, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(58, 'product_access', 'web', 45, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(59, 'product_create', 'web', 58, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(60, 'product_edit', 'web', 58, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(61, 'product_delete', 'web', 58, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(62, 'marketing_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(63, 'promo_access', 'web', 62, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(64, 'promo_create', 'web', 63, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(65, 'promo_edit', 'web', 63, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(66, 'promo_delete', 'web', 63, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(67, 'discount_access', 'web', 62, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(68, 'discount_create', 'web', 67, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(69, 'discount_edit', 'web', 67, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(70, 'discount_delete', 'web', 67, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(71, 'finance_access', 'web', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(72, 'order_access', 'web', 71, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(73, 'order_create', 'web', 72, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(74, 'order_edit', 'web', 72, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(75, 'order_delete', 'web', 72, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(76, 'payment_access', 'web', 71, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(77, 'payment_create', 'web', 76, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(78, 'payment_edit', 'web', 76, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(79, 'payment_delete', 'web', 76, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(80, 'payment_category_access', 'web', 71, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(81, 'payment_category_create', 'web', 80, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(82, 'payment_category_edit', 'web', 80, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(83, 'payment_category_delete', 'web', 80, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(84, 'payment_type_access', 'web', 71, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(85, 'payment_type_create', 'web', 84, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(86, 'payment_type_edit', 'web', 84, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(87, 'payment_type_delete', 'web', 84, '2023-08-16 06:24:16', '2023-08-16 06:24:16');

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
(1, 2, 1, 1, 1, 'Product 1', 'Description Product 1', '001', 1000000, 1, 1, 500000, NULL, NULL, NULL, NULL, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 2, 1, 'Product Brand 1', 0, NULL, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 2, 'Product Category 1', NULL, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
(1, 1, 'super-admin', 'web', '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(2, NULL, 'admin', 'web', '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(3, NULL, 'user', 'web', '2023-08-16 06:24:17', '2023-08-16 06:24:17');

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
(1, 'Starter', 10, 500, 500, 500, 1000, 1, 10, 50, 0, 0, 0, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(2, 'Basic', 50, 3000, 2000, 3000, 10000, 10, 50, 500, 1, 1, 0, '2023-08-16 06:24:16', '2023-08-16 06:24:16'),
(3, 'Advance', 200, NULL, NULL, NULL, NULL, NULL, NULL, 5000, 1, 1, 1, '2023-08-16 06:24:16', '2023-08-16 06:24:16');

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
(1, NULL, 'PT. Alba Digital Technology', 'alba@gmail.com', '080808080808', NULL, '2023-08-16 06:24:16', '2023-08-16 06:24:16', NULL),
(2, 1, 'User Starter', 'user.starter@gmail.com', '09876543211', '2023-09-16', '2023-08-16 06:24:16', '2023-08-16 06:24:16', NULL),
(3, 2, 'User Basic', 'user.basic@gmail.com', '09876789009', '2023-09-16', '2023-08-16 06:24:16', '2023-08-16 06:24:16', NULL),
(4, 3, 'User Advance', 'user.advance@gmail.com', '098123456734', '2023-09-16', '2023-08-16 06:24:16', '2023-08-16 06:24:16', NULL);

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
(1, 1, 'Lead Category 1', 'description Lead Category 1', '2023-08-16 06:24:18', '2023-08-16 06:24:18');

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_discount_approval_limits`
--

CREATE TABLE `supervisor_discount_approval_limits` (
  `supervisor_type_id` bigint(20) UNSIGNED NOT NULL,
  `product_brand_id` bigint(20) UNSIGNED NOT NULL,
  `limit` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Store Leader', 'store-leader', 1, 0, 0, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(2, 'Manager Area', 'manager-area', 2, 0, 0, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(3, 'Head Sales', 'head-sales', 3, 0, 0, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(4, 'Director Sales Marketing', 'director-sales-marketing', 4, 0, 0, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `targets`
--

CREATE TABLE `targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `type` smallint(5) UNSIGNED NOT NULL,
  `target` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `target_lines`
--

CREATE TABLE `target_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `target_id` bigint(20) UNSIGNED NOT NULL,
  `target` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `target_maps`
--

CREATE TABLE `target_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED NOT NULL,
  `value` bigint(20) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `telescope_entries_tags`
--

CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'Super Admin', 'superadmin@gmail.com', NULL, '$2y$10$jgB91UlMIlgZA4dle/f2yOvYkz3H9IksKo8GCn9RG.rpUyXXoWSpO', NULL, 1, NULL, NULL, NULL, 1, 2, '2023-08-16 06:24:16', '2023-08-16 06:24:16', NULL),
(2, 1, 'Admin ALBA', 'admin@gmail.com', NULL, '$2y$10$ZfgTDoNh3yVvt9O8XlogBOZBWoVOtWy3O15yzplVzpsM9D/BRKyVS', NULL, 1, NULL, NULL, NULL, 3, 4, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(3, 2, 'Admin Starter', 'adminstarter@gmail.com', NULL, '$2y$10$6bez2W1218b2TSnwpxliNOZb/sn7F4siuSJkiv5vcBJ0tInSkBtQe', NULL, 1, NULL, NULL, NULL, 5, 6, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(4, 2, 'Director', 'director@gmail.com', NULL, '$2y$10$3sQAX7waONYx.Rcg7MOaNeMPNVFNtcBwB0Nf9fxE4rIlht9Bhv9bG', NULL, 4, 1, NULL, NULL, 7, 8, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(5, 2, 'BUM', 'bum@gmail.com', NULL, '$2y$10$5aTmJGfveGtd9QFxn/Bgz.5Cf8bY0BaO8cV0UrfYFwu74Hg/eyo2G', NULL, 3, 1, 2, NULL, 9, 14, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(6, 2, 'Store Leader', 'storeleader@gmail.com', NULL, '$2y$10$s0SopNH/6obpevaBMQBBwuxtWoHaDcshc9u3OIRX3PdVAFqr4p13G', NULL, 3, 1, 1, 5, 10, 13, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(7, 2, 'Sales', 'sales@gmail.com', NULL, '$2y$10$bSy753hbnAVv39cAP6BttOo7Sf25Xp/lZLPWgNr/M3XGVMKgVLBsK', NULL, 2, 1, NULL, 6, 11, 12, '2023-08-16 06:24:17', '2023-08-16 06:24:17', NULL),
(8, 3, 'Admin basic', 'adminbasic@gmail.com', NULL, '$2y$10$KS4KhyT5B1Ukt67NiY.IBewEMKnaRFD241oyoH01EzM9wLlz0hsua', NULL, 1, NULL, NULL, NULL, 15, 16, '2023-08-16 06:24:18', '2023-08-16 06:24:18', NULL);

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
-- Indexes for table `new_targets`
--
ALTER TABLE `new_targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `new_targets_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `new_targets_start_date_index` (`start_date`),
  ADD KEY `new_targets_end_date_index` (`end_date`);

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
  ADD KEY `payments_channel_id_foreign` (`channel_id`),
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
  ADD KEY `supervisor_discount_approval_limits_supervisor_type_id_foreign` (`supervisor_type_id`),
  ADD KEY `supervisor_discount_approval_limits_product_brand_id_foreign` (`product_brand_id`);

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
-- Indexes for table `target_lines`
--
ALTER TABLE `target_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `target_lines_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `target_lines_target_id_foreign` (`target_id`);

--
-- Indexes for table `target_maps`
--
ALTER TABLE `target_maps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `target_maps_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `target_maps_target_id_foreign` (`target_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand_categories`
--
ALTER TABLE `brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_categories`
--
ALTER TABLE `lead_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `new_targets`
--
ALTER TABLE `new_targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_brands`
--
ALTER TABLE `product_brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_brand_categories`
--
ALTER TABLE `product_brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `target_lines`
--
ALTER TABLE `target_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `target_maps`
--
ALTER TABLE `target_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `target_type_priorities`
--
ALTER TABLE `target_type_priorities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telescope_entries`
--
ALTER TABLE `telescope_entries`
  MODIFY `sequence` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `payments_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
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
  ADD CONSTRAINT `supervisor_discount_approval_limits_product_brand_id_foreign` FOREIGN KEY (`product_brand_id`) REFERENCES `product_brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supervisor_discount_approval_limits_supervisor_type_id_foreign` FOREIGN KEY (`supervisor_type_id`) REFERENCES `supervisor_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `targets`
--
ALTER TABLE `targets`
  ADD CONSTRAINT `targets_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `target_lines`
--
ALTER TABLE `target_lines`
  ADD CONSTRAINT `target_lines_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `targets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `target_maps`
--
ALTER TABLE `target_maps`
  ADD CONSTRAINT `target_maps_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `targets` (`id`) ON DELETE CASCADE;

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
