-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2025 at 08:59 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meteo`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `description`, `image`, `link`, `start_date`, `end_date`, `position`, `sort_order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sản phẩm mới', NULL, 'banners/6hG1rRZKwBsiBpZ8k6OW66w2z0euBKzU9kyQOxvW.jpg', 'http://127.0.0.1:8000/products/sofa-motion-mars-diem-nhan-sang-trong-cho-khong-gian-song-hien-dai', '2025-11-17 09:51:00', '2025-11-17 10:00:00', 0, 1, 'active', '2025-11-16 19:51:27', '2025-12-03 15:14:08', NULL),
(2, 'logo', NULL, 'banners/GfqcNh3Bv3Ipf8q1giSoHsHApBXUxQvcdy2SZst5.jpg', 'http://127.0.0.1:8000', NULL, NULL, 0, 2, 'active', '2025-11-16 19:53:12', '2025-12-03 14:53:38', NULL),
(3, 'Xem chi tiết', NULL, 'banners/4trtPHmh9rNRdoiosKwb73DkX3AyqbkLMkUOw7Ke.jpg', NULL, NULL, NULL, 0, 3, 'active', '2025-11-16 19:55:42', '2025-12-03 14:53:38', NULL),
(4, 'sản phẩm mới', NULL, 'banners/rmXPvVGAbJG2PIxsqFCj9TMxPlHevL4BTgNQBBPH.webp', NULL, NULL, NULL, 0, 4, 'active', '2025-11-26 08:18:46', '2025-12-03 14:53:38', NULL),
(5, 'sản phẩm 2', NULL, 'banners/s9Iz4HL3exsetGf4dgfBNmYAlCGkRh7iVAXpkH7s.jpg', NULL, NULL, NULL, 0, 5, 'active', '2025-11-26 08:19:37', '2025-12-03 14:53:38', NULL),
(6, 'hello các bạn', 'xinc hào nhé', 'banners/U2zjQ2rCHf800GpvOEGVe9B7QzX6eO27FSMpTNuM.png', 'http://127.0.0.1:8000/users/blog', NULL, NULL, 0, 6, 'active', '2025-12-03 08:26:47', '2025-12-03 15:06:42', NULL),
(7, 'fdfgfh', 'hfgh', 'banners/lbhK64TcRC5WlHe5KnMNWX3mTCnhFhl8OVdgv2T2.png', 'http://127.0.0.1:8000/products/sofa-motion-mars-diem-nhan-sang-trong-cho-khong-gian-song-hien-dai', NULL, NULL, 0, 7, 'active', '2025-12-03 14:46:55', '2025-12-03 14:53:38', NULL),
(8, 'văn luân', 'vbvnvn', 'banners/LYElI3VSNimaa1pS6FeEGljKa208CMaPDXwp4F3U.png', 'http://127.0.0.1:8000/products/sofa-motion-mars-diem-nhan-sang-trong-cho-khong-gian-song-hien-dai', NULL, NULL, 0, 8, 'active', '2025-12-03 14:47:30', '2025-12-03 14:57:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Blog 1', 'blog-1', 'mô tả blog 1', NULL, '1762848343-6912ee57bd971.jpg', 'published', '2025-11-09 22:17:39', '2025-11-11 01:05:55');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('meteor_cache_otp_doviethoa041105@gmail.com', 'i:370288;', 1762181350),
('meteor_cache_otp_sent_doviethoa041105@gmail.com', 'O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-03 14:44:10.721976\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}', 1762181110);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','checked_out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 12, '11815000.00', 'active', '2025-11-29 18:43:24', '2025-11-29 18:43:24');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES
(2, 1, 23, 1, '11815000.00', '11815000.00', '2025-11-29 18:43:39', '2025-11-29 18:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Phòng Khách', 'phong-khach', NULL, NULL, 'active', '2025-11-16 18:39:42', '2025-11-16 18:39:42'),
(13, 'sofa', 'sofa', NULL, 12, 'active', '2025-11-16 18:41:24', '2025-11-16 18:41:24'),
(14, 'Phòng ngủ', 'phong-ngu', NULL, NULL, 'active', '2025-11-17 19:47:17', '2025-11-17 19:47:17'),
(15, 'Phòng làm việc', 'phong-lam-viec', NULL, NULL, 'active', '2025-11-17 19:47:35', '2025-11-17 19:47:35'),
(16, 'Phòng ăn', 'phong-an', NULL, NULL, 'active', '2025-11-17 19:47:45', '2025-11-17 19:47:45'),
(17, 'Giường ngủ', 'giuong-ngu', NULL, 14, 'active', '2025-11-17 19:48:37', '2025-11-17 19:48:37'),
(18, 'Bàn ăn', 'ban-an', NULL, 16, 'active', '2025-11-18 01:20:08', '2025-11-18 01:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: active, 0: inactive',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection_product`
--

CREATE TABLE `collection_product` (
  `id` bigint UNSIGNED NOT NULL,
  `collection_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_12_140716_create_users_table', 1),
(2, '2025_10_12_141001_create_categories_table', 1),
(3, '2025_10_12_141028_create_brands_table', 1),
(4, '2025_10_12_141104_create_products_table', 1),
(5, '2025_10_12_141130_create_promotions_table', 1),
(6, '2025_10_12_141152_create_orders_table', 1),
(7, '2025_10_12_141216_create_order_details_table', 1),
(8, '2025_10_12_141249_create_carts_table', 1),
(9, '2025_10_12_141432_create_cart_items_table', 1),
(10, '2025_10_12_141453_create_wishlists_table', 1),
(11, '2025_10_12_141529_create_banners_table', 1),
(12, '2025_10_12_141603_create_blogs_table', 1),
(13, '2025_10_12_141633_create_system_logs_table', 1),
(14, '2025_10_18_084857_add_deleted_at_to_users_table', 2),
(15, '2025_10_26_142548_create_roles_table', 3),
(16, '2025_10_26_063654_add_deleted_at_to_users_table', 4),
(17, '2025_10_29_084548_add_size_and_color_to_products_table', 5),
(18, '2025_10_29_090824_add_dimensions_and_color_to_products_table', 5),
(19, '2025_10_29_101104_create_product_variants_table', 5),
(20, '2025_10_29_125825_add_indexes_to_product_variants_table', 5),
(21, '2025_11_01_132002_create_cache_table', 6),
(23, '2025_11_10_145253_create_product_images_table', 7),
(24, '2025_11_13_175634_add_fields_to_banners_table', 8),
(25, '2025_11_13_180944_remove_display_location_from_banners_table', 8),
(26, '2025_11_17_130305_create_monthly_targets_table', 9),
(27, '2025_11_17_160000_add_client_fields_to_orders_table', 10),
(28, '2025_11_17_161000_enhance_order_details_table', 10),
(29, '2025_11_18_181159_update_stock_nullable_in_products_table', 11),
(30, '2025_12_20_000001_create_order_payments_table', 11),
(31, '2025_12_20_000002_create_order_shipments_table', 11),
(32, '2025_12_20_000003_create_order_refunds_table', 11),
(33, '2025_12_20_000004_create_order_refund_items_table', 11),
(34, '2025_12_20_000005_create_order_returns_table', 11),
(35, '2025_12_20_000006_create_order_return_items_table', 11),
(36, '2025_12_20_000007_create_order_notes_table', 11),
(37, '2025_12_20_000008_create_order_timelines_table', 11),
(38, '2025_12_20_000009_update_order_status_enum', 11),
(47, '2025_11_26_104729_add_product_version_to_product_variants_table', 12),
(48, '2025_11_26_105713_add_product_version_to_products_table', 12),
(49, '2025_11_21_180153_create_wallets_table', 13),
(50, '2025_11_21_180246_create_transactions_table', 13),
(51, '2025_11_22_100000_create_refunds_table', 13),
(52, '2025_11_22_110000_add_refund_fields_to_transactions_table', 13),
(53, '2025_11_22_120000_create_transaction_logs_table', 13),
(54, '2025_11_23_081500_add_refunded_to_payment_status_enum', 13),
(55, '2025_11_23_083000_create_wallet_withdrawals_table', 13),
(56, '2025_11_24_090000_add_marked_columns_to_transactions_table', 13),
(57, '2025_11_25_100000_create_order_logs_table', 13),
(58, '2025_11_26_054700_update_role_enum_in_order_logs_table', 13),
(59, '2025_11_28_000001_add_analytics_dimensions_to_orders_table', 14),
(60, '2025_11_28_161852_add_returned_at_to_orders_table', 15),
(61, '2025_11_29_194034_add_ecommerce_fields_to_products_table', 15),
(62, '2025_11_29_194045_create_reviews_table', 15),
(63, '2025_11_29_202245_update_reviews_table_for_admin_management', 16),
(64, '2025_11_29_202251_create_review_reports_table', 16),
(65, '2025_11_29_202257_create_review_replies_table', 16),
(66, '2025_11_30_221334_add_missing_fields_to_reviews_table', 17),
(67, '2025_12_02_024035_create_review_helpful_table', 18),
(69, '2025_12_02_041033_create_review_audit_logs_table', 19),
(70, '2025_12_04_131809_create_collections_table', 20),
(71, '2025_12_04_132227_create_collection_product_table', 20);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_targets`
--

CREATE TABLE `monthly_targets` (
  `id` bigint UNSIGNED NOT NULL,
  `year` year NOT NULL,
  `month` tinyint NOT NULL,
  `target_amount` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monthly_targets`
--

INSERT INTO `monthly_targets` (`id`, `year`, `month`, `target_amount`, `created_at`, `updated_at`) VALUES
(1, 2025, 11, 200000000, '2025-11-17 06:09:00', '2025-11-17 06:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promotion_id` bigint UNSIGNED DEFAULT NULL,
  `order_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `voucher_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_total` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','bank','momo','paypal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `order_status` enum('pending','awaiting_payment','paid','processing','confirmed','packed','shipping','delivered','completed','cancelled','return_requested','returned','refunded','partial_refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `shipping_status` enum('pending','preparing','ready_to_ship','shipped','in_transit','out_for_delivery','delivered','failed','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancel_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_status` enum('none','requested','approved','rejected','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `return_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_attachments` json DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `packed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_district` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_ward` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_channel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online_store',
  `sales_channel_detail` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_code` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promotion_tag` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marketing_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost_of_goods` decimal(12,2) DEFAULT NULL,
  `shipping_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `promotion_id`, `order_code`, `total_price`, `discount_amount`, `voucher_code`, `final_total`, `sub_total`, `payment_method`, `payment_status`, `order_status`, `shipping_status`, `cancel_reason`, `notes`, `return_status`, `return_reason`, `return_note`, `return_attachments`, `order_date`, `confirmed_at`, `packed_at`, `shipped_at`, `delivered_at`, `cancelled_at`, `refunded_at`, `returned_at`, `shipping_address`, `shipping_city`, `shipping_district`, `shipping_ward`, `tracking_code`, `tracking_url`, `shipping_provider`, `sales_channel`, `sales_channel_detail`, `branch_code`, `warehouse_code`, `campaign_code`, `promotion_tag`, `marketing_cost`, `cost_of_goods`, `shipping_phone`, `shipping_method`, `shipping_fee`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, NULL, 'VH-01', '20000000.00', '0.00', NULL, '20000000.00', NULL, 'cash', 'pending', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, '1', NULL, NULL, '2025-11-03 11:12:35', '2025-11-03 04:14:58'),
(2, 4, NULL, NULL, NULL, NULL, 'VH-04', '400000.00', '0.00', NULL, '400000.00', NULL, 'bank', 'paid', 'cancelled', 'pending', 'sdf', NULL, 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 07:13:06', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, NULL, NULL, '2025-10-14 07:13:36', '2025-11-24 07:13:06'),
(3, 4, NULL, NULL, NULL, NULL, 'VH-05', '100000.00', '0.00', NULL, '100000.00', NULL, 'cash', 'paid', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, NULL, NULL, '2025-12-25 07:34:03', NULL),
(4, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DH7BKBJLKO20251118', '23555000.00', '0.00', NULL, '23555000.00', '23555000.00', 'cash', 'pending', 'shipping', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-18 03:57:48', '2025-11-26 14:53:03', NULL, '2025-11-26 14:53:07', NULL, NULL, NULL, NULL, 'số 9', 'Hà Nội', 'gfhdz', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-18 03:57:48', '2025-11-26 14:53:07'),
(5, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DHWLTH5PFU20251118', '12900000.00', '0.00', NULL, '12900000.00', '12900000.00', 'cash', 'pending', 'shipping', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-18 03:59:01', '2025-11-26 14:52:07', NULL, '2025-11-26 14:52:53', NULL, NULL, NULL, NULL, 'số 9', 'Hà Nội', 'gfhdz', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'express', '0.00', '2025-11-18 03:59:01', '2025-11-26 14:52:53'),
(6, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DHBYG3BARY20251118', '47110000.00', '0.00', NULL, '47110000.00', '47110000.00', 'cash', 'pending', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-18 09:46:45', '2025-11-18 19:49:37', '2025-11-24 07:05:34', '2025-11-24 07:13:59', '2025-11-24 07:15:01', NULL, NULL, NULL, 'số 9', 'Hà Nội', 'gfhdz', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-18 09:46:45', '2025-11-24 07:15:10'),
(7, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DHJPECISW020251119', '26775000.00', '0.00', NULL, '26775000.00', '26775000.00', 'cash', 'pending', 'cancelled', 'pending', 'Đổi ý, đặt nhầm', 'tôi muốn đổi địa chỉ', 'none', NULL, NULL, NULL, '2025-11-18 19:44:37', NULL, NULL, NULL, NULL, '2025-11-18 19:46:23', NULL, NULL, 'Xóm 8 Thanh Chiểu', 'Hà Nội', 'Ba Vì', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-18 19:44:37', '2025-11-18 19:46:23'),
(8, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DHFDLM35C120251124', '26775000.00', '0.00', NULL, '26775000.00', '26775000.00', 'bank', 'paid', 'completed', 'pending', NULL, NULL, 'requested', 'Giao sai sản phẩm', 'ƯAESRDFGH', '[\"returns/PBSEkXQDXtiZC1G2cfe4ZLd05VhcChoqfFdjjy64.png\"]', '2025-11-24 07:17:51', '2025-11-24 07:18:41', '2025-11-24 07:18:48', '2025-11-24 07:18:52', '2025-11-24 07:18:56', NULL, NULL, NULL, 'Xóm 8 Thanh Chiểu', 'Hà Nội', 'Ba Vì', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'fast', '0.00', '2025-11-24 07:17:51', '2025-11-24 18:41:38'),
(9, 8, 'iPhone 11 Pro Max', '1234', 'user@gmail.com', NULL, 'DHBWHCRYPY20251125', '26775000.00', '0.00', NULL, '26775000.00', '26775000.00', 'cash', 'pending', 'cancelled', 'pending', 'Muốn thay đổi địa chỉ', NULL, 'none', NULL, NULL, NULL, '2025-11-24 18:33:12', NULL, NULL, NULL, NULL, '2025-11-24 18:33:39', NULL, NULL, 'Xóm 8 Thanh Chiểu', 'Hà Nội', 'Ba Vì', 'Cổ Đô', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-24 18:33:12', '2025-11-24 18:33:39'),
(10, 1, 'Việt Hòa', '0397766836', 'doviethoa041105@gmail.com', NULL, 'DHT5BLUF2P20251128', '26775000.00', '0.00', NULL, '26775000.00', '26775000.00', 'cash', 'pending', 'processing', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-28 07:44:55', '2025-11-28 07:45:15', NULL, NULL, NULL, NULL, NULL, NULL, 'số 9', 'Tỉnh Bắc Ninh', 'Thành phố Bắc Ninh', 'Phường Hạp Lĩnh', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-28 07:44:55', '2025-11-28 07:45:15'),
(11, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'DHAHXUWA4C20251129', '11815000.00', '0.00', NULL, '11815000.00', '11815000.00', 'bank', 'pending', 'cancelled', 'pending', 'Đổi ý, đặt nhầm', NULL, 'none', NULL, NULL, NULL, '2025-11-29 12:28:59', NULL, NULL, NULL, NULL, '2025-11-29 18:42:16', NULL, NULL, 'kjlj', 'Thành phố Hà Nội', 'Quận Tây Hồ', 'Phường Xuân La', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-29 12:28:59', '2025-11-29 18:42:16'),
(12, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'DHI8AXGMFM20251130', '11815000.00', '0.00', NULL, '11815000.00', '11815000.00', 'cash', 'pending', 'returned', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-29 18:31:43', '2025-11-29 18:33:44', NULL, '2025-11-29 18:33:47', NULL, NULL, NULL, '2025-11-29 18:33:50', 'ds', 'Tỉnh Bắc Kạn', 'Thành Phố Bắc Kạn', 'Phường Đức Xuân', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-29 18:31:43', '2025-11-29 18:33:50'),
(13, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'DHUFYSAAWL20251130', '11815000.00', '0.00', NULL, '11815000.00', '11815000.00', 'cash', 'pending', 'returned', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-29 18:37:16', '2025-11-29 18:40:28', NULL, '2025-11-29 18:40:29', NULL, NULL, NULL, '2025-11-29 18:40:38', 'c', 'Tỉnh Hà Giang', 'Huyện Xín Mần', 'Xã Chế Là', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-29 18:37:16', '2025-11-29 18:40:38'),
(14, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'DHGD5JWQGK20251130', '11815000.00', '0.00', NULL, '11815000.00', '11815000.00', 'cash', 'pending', 'shipping', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-29 18:45:10', '2025-11-29 18:45:27', NULL, '2025-11-29 18:45:29', NULL, NULL, NULL, NULL, 'đ', 'Tỉnh Hà Giang', 'Huyện Bắc Quang', 'Xã Bằng Hành', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, NULL, 'standard', '0.00', '2025-11-29 18:45:10', '2025-11-29 18:45:29'),
(15, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'ORD-KDSITTM3-1764442727-1', '0.00', '0.00', NULL, '0.00', '0.00', 'bank', 'paid', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-05 18:58:47', '2025-11-05 19:58:47', '2025-11-06 18:58:47', '2025-11-07 18:58:47', '2025-11-09 18:58:47', NULL, NULL, NULL, '456 Đường XYZ, Quận 2, TP.HCM', 'TP.HCM', 'Quận 2', 'Phường An Phú', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, '0923456789', 'standard', '30000.00', '2025-11-29 18:58:47', '2025-11-29 18:58:47'),
(16, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'ORD-BQ22TLMB-1764442745-1', '80000000.00', '0.00', NULL, '80030000.00', '80000000.00', 'bank', 'paid', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-06 18:59:05', '2025-11-06 23:59:05', '2025-11-07 18:59:05', '2025-11-07 18:59:05', '2025-11-09 18:59:05', NULL, NULL, NULL, '456 Đường XYZ, Quận 2, TP.HCM', 'TP.HCM', 'Quận 2', 'Phường An Phú', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, '0923456789', 'standard', '30000.00', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(17, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'ORD-AKAG7HIZ-1764442745-2', '70000000.00', '0.00', NULL, '70030000.00', '70000000.00', 'cash', 'paid', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-11-14 18:59:05', '2025-11-14 19:59:05', '2025-11-15 18:59:05', '2025-11-15 18:59:05', '2025-11-16 18:59:05', NULL, NULL, NULL, '456 Đường XYZ, Quận 2, TP.HCM', 'TP.HCM', 'Quận 2', 'Phường An Phú', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, '0923456789', 'standard', '30000.00', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(18, 12, 'Trần Thị B', '0923456789', 'user2@example.com', NULL, 'ORD-UIPIGSFE-1764442745-3', '32900000.00', '0.00', NULL, '32930000.00', '32900000.00', 'momo', 'paid', 'completed', 'pending', NULL, NULL, 'none', NULL, NULL, NULL, '2025-10-30 18:59:05', '2025-10-31 00:59:05', '2025-10-31 18:59:05', '2025-11-01 18:59:05', '2025-11-02 18:59:05', NULL, NULL, NULL, '456 Đường XYZ, Quận 2, TP.HCM', 'TP.HCM', 'Quận 2', 'Phường An Phú', NULL, NULL, NULL, 'online_store', NULL, NULL, NULL, NULL, NULL, '0.00', NULL, '0923456789', 'standard', '30000.00', '2025-11-29 18:59:05', '2025-11-29 18:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `variant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `variant_id`, `variant_name`, `variant_sku`, `quantity`, `price`, `subtotal`, `total_price`, `image_path`, `created_at`, `updated_at`) VALUES
(4, 4, 22, NULL, NULL, NULL, NULL, 1, '23555000.00', '23555000.00', NULL, NULL, '2025-11-18 03:57:48', '2025-11-18 03:57:48'),
(6, 6, 22, NULL, NULL, NULL, NULL, 2, '23555000.00', '47110000.00', NULL, NULL, '2025-11-18 09:46:45', '2025-11-18 09:46:45'),
(7, 7, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', 13, 'trắng - 200.00x90.00x725.00 cm', NULL, 1, '26775000.00', '26775000.00', '26775000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-18 19:44:37', '2025-11-18 19:44:37'),
(8, 8, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', 13, 'trắng - 200.00x90.00x725.00 cm', NULL, 1, '26775000.00', '26775000.00', '26775000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-24 07:17:51', '2025-11-24 07:17:51'),
(9, 5, 21, 'Sofa Ogami 2 Chỗ 1m4 (Vải VACT 10499)', NULL, NULL, NULL, 2, '6450000.00', '12900000.00', '12900000.00', 'products/pKCwmMv5TyiX62WElkpov4jgBWFGYkG5EarF4En6.jpg', '2025-11-24 07:29:01', '2025-11-24 07:29:01'),
(10, 9, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', 13, 'trắng - 200.00x90.00x725.00 cm', NULL, 1, '26775000.00', '26775000.00', '26775000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-24 18:33:12', '2025-11-24 18:33:12'),
(11, 10, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', 13, 'trắng - 200.00x90.00x725.00 cm', NULL, 1, '26775000.00', '26775000.00', '26775000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-28 07:44:55', '2025-11-28 07:44:55'),
(12, 11, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', 12, 'gỗ - 160.00x80.00x75.50 cm', NULL, 1, '11815000.00', '11815000.00', '11815000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 12:28:59', '2025-11-29 12:28:59'),
(13, 12, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', 12, 'gỗ - 160.00x80.00x75.50 cm', NULL, 1, '11815000.00', '11815000.00', '11815000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:31:43', '2025-11-29 18:31:43'),
(14, 13, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', 12, 'gỗ - 160.00x80.00x75.50 cm', NULL, 1, '11815000.00', '11815000.00', '11815000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:37:16', '2025-11-29 18:37:16'),
(15, 14, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', 12, 'gỗ - 160.00x80.00x75.50 cm', NULL, 1, '11815000.00', '11815000.00', '11815000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:45:10', '2025-11-29 18:45:10'),
(16, 15, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', NULL, NULL, NULL, 3, '11815000.00', '35445000.00', '35445000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:58:47', '2025-11-29 18:58:47'),
(17, 15, 19, 'Sofa Motion Mars Điểm Nhấn Sang Trọng Cho Không Gian Sống Hiện Đại', NULL, NULL, NULL, 1, '54999000.00', '54999000.00', '54999000.00', 'products/PLCPDGYVtNneRdIpi4D9scwI2v5KE5UxnoKmVePD.jpg', '2025-11-29 18:58:47', '2025-11-29 18:58:47'),
(18, 15, 21, 'Sofa Ogami 2 Chỗ 1m4 (Vải VACT 10499)', NULL, NULL, NULL, 2, '6450000.00', '12900000.00', '12900000.00', 'products/pKCwmMv5TyiX62WElkpov4jgBWFGYkG5EarF4En6.jpg', '2025-11-29 18:58:47', '2025-11-29 18:58:47'),
(19, 15, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', NULL, NULL, NULL, 3, '26775000.00', '80325000.00', '80325000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-29 18:58:47', '2025-11-29 18:58:47'),
(20, 16, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(21, 16, 20, 'Giường Bridge 1m6 Da bò R1/DB', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/nfEOJ3iJtYKXSFmuCpseobQdW8EpwZKdvZ9koE83.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(22, 16, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(23, 16, 22, 'Giường Leman 1m8 vải VACT10370', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/wpqgNHXM9cck6o46PP6tbxJFaEkuYCrmU0obxS4K.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(24, 17, 23, 'Bàn ăn Coastal 6 chỗ 1,6m', NULL, NULL, NULL, 1, '10000000.00', '10000000.00', '10000000.00', 'products/PYzXW4lgJxdI7ytMtopbrA7XFaQGAr0Qtb5fqghf.jpg', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(25, 17, 22, 'Giường Leman 1m8 vải VACT10370', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/wpqgNHXM9cck6o46PP6tbxJFaEkuYCrmU0obxS4K.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(26, 17, 20, 'Giường Bridge 1m6 Da bò R1/DB', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/nfEOJ3iJtYKXSFmuCpseobQdW8EpwZKdvZ9koE83.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(27, 17, 19, 'Sofa Motion Mars Điểm Nhấn Sang Trọng Cho Không Gian Sống Hiện Đại', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/PLCPDGYVtNneRdIpi4D9scwI2v5KE5UxnoKmVePD.jpg', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(28, 18, 24, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', NULL, NULL, NULL, 2, '10000000.00', '20000000.00', '20000000.00', 'products/7ZfBMZEMeK4YCXCUmT5xFaAfQJqUU0k7vp3JD8pF.webp', '2025-11-29 18:59:05', '2025-11-29 18:59:05'),
(29, 18, 21, 'Sofa Ogami 2 Chỗ 1m4 (Vải VACT 10499)', NULL, NULL, NULL, 2, '6450000.00', '12900000.00', '12900000.00', 'products/pKCwmMv5TyiX62WElkpov4jgBWFGYkG5EarF4En6.jpg', '2025-11-29 18:59:05', '2025-11-29 18:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `order_logs`
--

CREATE TABLE `order_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `role` enum('admin','staff','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `status`, `updated_by`, `role`, `created_at`) VALUES
(1, 10, 'processing', 1, 'admin', '2025-11-28 07:45:15'),
(2, 12, 'processing', 10, 'admin', '2025-11-29 18:33:44'),
(3, 12, 'shipping', 10, 'admin', '2025-11-29 18:33:47'),
(4, 12, 'returned', 10, 'admin', '2025-11-29 18:33:50'),
(5, 13, 'processing', 10, 'admin', '2025-11-29 18:40:28'),
(6, 13, 'shipping', 10, 'admin', '2025-11-29 18:40:29'),
(7, 13, 'returned', 10, 'admin', '2025-11-29 18:40:38'),
(8, 11, 'cancelled', 12, 'customer', '2025-11-29 18:42:16'),
(9, 14, 'processing', 10, 'admin', '2025-11-29 18:45:27'),
(10, 14, 'shipping', 10, 'admin', '2025-11-29 18:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `order_notes`
--

CREATE TABLE `order_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `type` enum('internal','customer','system') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `attachments` json DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `tagged_user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank','momo','paypal','stripe','zalopay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `status` enum('pending','processing','paid','failed','refunded','partially_refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `payment_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_refunds`
--

CREATE TABLE `order_refunds` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `order_payment_id` bigint UNSIGNED DEFAULT NULL,
  `refund_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('full','partial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `status` enum('pending','processing','completed','failed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `refund_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `refund_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_refund_items`
--

CREATE TABLE `order_refund_items` (
  `id` bigint UNSIGNED NOT NULL,
  `refund_id` bigint UNSIGNED NOT NULL,
  `order_detail_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_returns`
--

CREATE TABLE `order_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `return_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('refund','exchange','repair') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'refund',
  `status` enum('requested','approved','rejected','in_transit','received','processed','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `product_condition` enum('new','like_new','used','damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `resolution` enum('refund','exchange','repair','reject') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_product_id` bigint UNSIGNED DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_return_items`
--

CREATE TABLE `order_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `return_id` bigint UNSIGNED NOT NULL,
  `order_detail_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `condition` enum('new','like_new','used','damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_shipments`
--

CREATE TABLE `order_shipments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `shipment_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `carrier` enum('ghn','ghtk','vnpost','shippo','manual','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `carrier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','label_created','picked_up','in_transit','out_for_delivery','delivered','failed','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carrier_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `in_transit_at` timestamp NULL DEFAULT NULL,
  `out_for_delivery_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_timelines`
--

CREATE TABLE `order_timelines` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `event_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `old_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_timelines`
--

INSERT INTO `order_timelines` (`id`, `order_id`, `event_type`, `title`, `description`, `old_value`, `new_value`, `metadata`, `user_id`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 6, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đã đóng gói\' sang \'Đã đóng gói\'', 'processing', 'packed', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:05:34', '2025-11-24 07:05:34'),
(2, 2, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Yêu cầu trả hàng\' sang \'Yêu cầu trả hàng\'', 'completed', 'return_requested', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:11:40', '2025-11-24 07:11:40'),
(3, 2, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đã hủy\' sang \'Đã hủy\'', 'return_requested', 'cancelled', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:13:06', '2025-11-24 07:13:06'),
(4, 6, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đang giao hàng\' sang \'Đang giao hàng\'', 'packed', 'shipping', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:13:59', '2025-11-24 07:13:59'),
(5, 6, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Giao thành công\' sang \'Giao thành công\'', 'shipping', 'delivered', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:15:01', '2025-11-24 07:15:01'),
(6, 6, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Hoàn thành\' sang \'Hoàn thành\'', 'delivered', 'completed', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:15:10', '2025-11-24 07:15:10'),
(7, 8, 'order_created', 'Tạo đơn hàng', 'Đơn hàng được tạo bởi khách hàng. Phương thức thanh toán: bank', NULL, 'awaiting_payment', NULL, 1, 'admin', '2025-11-24 07:17:51', '2025-11-24 07:17:51'),
(8, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đã thanh toán\' sang \'Đã thanh toán\'', 'awaiting_payment', 'paid', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:18:35', '2025-11-24 07:18:35'),
(9, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đang xử lý\' sang \'Đang xử lý\'', 'paid', 'processing', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:18:41', '2025-11-24 07:18:41'),
(10, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đã đóng gói\' sang \'Đã đóng gói\'', 'processing', 'packed', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:18:48', '2025-11-24 07:18:48'),
(11, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Đang giao hàng\' sang \'Đang giao hàng\'', 'packed', 'shipping', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:18:52', '2025-11-24 07:18:52'),
(12, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Giao thành công\' sang \'Giao thành công\'', 'shipping', 'delivered', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 07:18:56', '2025-11-24 07:18:56'),
(13, 5, 'order_edited', 'Chỉnh sửa đơn hàng', 'Đơn hàng đã được chỉnh sửa bởi Việt Hòa', NULL, NULL, NULL, 1, 'admin', '2025-11-24 07:29:01', '2025-11-24 07:29:01'),
(14, 8, 'status_changed', 'Thay đổi trạng thái đơn hàng', 'Trạng thái đã thay đổi từ \'Hoàn thành\' sang \'Hoàn thành\'', 'delivered', 'completed', '{\"note\": null, \"changed_by\": 1}', 1, 'admin', '2025-11-24 18:40:51', '2025-11-24 18:40:51'),
(15, 8, 'return_requested', 'Yêu cầu trả hàng', 'Lý do: Giao sai sản phẩm', NULL, 'return_requested', NULL, 1, 'admin', '2025-11-24 18:41:38', '2025-11-24 18:41:38');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `product_version` int UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `short_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT '0.00',
  `total_sold` int NOT NULL DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL COMMENT 'Chiều dài (cm)',
  `width` decimal(8,2) DEFAULT NULL COMMENT 'Chiều rộng (cm)',
  `height` decimal(8,2) DEFAULT NULL COMMENT 'Chiều cao (cm)',
  `color_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã màu hex, VD: #FF0000',
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_version`, `name`, `slug`, `sku`, `description`, `short_description`, `price`, `sale_price`, `stock`, `rating_avg`, `total_sold`, `image`, `length`, `width`, `height`, `color_code`, `size`, `color`, `category_id`, `brand_id`, `status`, `created_at`, `updated_at`) VALUES
(19, 1, 'Sofa Motion Mars Điểm Nhấn Sang Trọng Cho Không Gian Sống Hiện Đại', 'sofa-motion-mars-diem-nhan-sang-trong-cho-khong-gian-song-hien-dai', NULL, 'Sofa Motion Mars sở hữu kích thước lý tưởng: D2770 – R1050 – C1000 mm, phù hợp cho phòng khách vừa và lớn. Sản phẩm được bọc bằng da bò tự nhiên Semi-Aniline màu light coffee, xuất xứ từ Mỹ, với độ dày từ 0.9 đến 1.1 mm. Da bò tự nhiên không chỉ mang lại cảm giác mềm mại, thoải mái mà còn giữ được các vân và nếp gấp độc đáo, tạo nên sự khác biệt cho từng chiếc sofa.\r\n\r\nChân sofa được làm từ chất liệu plastic leg chắc chắn nhưng vẫn nhẹ nhàng, giúp tối ưu hóa độ bền và dễ dàng di chuyển. Đường cong nhẹ ở lòng cạnh trước không chỉ mang lại cảm giác thoải mái mà còn truyền tải sự ấm áp và gần gũi.', NULL, '54999000.00', NULL, 5, '3.80', 0, 'products/PLCPDGYVtNneRdIpi4D9scwI2v5KE5UxnoKmVePD.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-11-16 18:45:01', '2025-12-03 10:57:10'),
(20, 1, 'Giường Bridge 1m6 Da bò R1/DB', 'giuong-bridge-1m6-da-bo-r1db', NULL, 'Giường Bridge 1m6 Da bò R1/DB là một loại giường ngủ đôi, có kích thước chiều rộng 1m6 (160cm) và chiều dài 2m. Tên gọi \"Bridge\" là tên mẫu, \"Da bò\" là chất liệu bọc, \"R1/DB\" là mã hiệu hoặc phân loại sản phẩm, thể hiện quy cách và màu sắc. Giường này thuộc kích thước \"Queen size\"', NULL, '81515000.00', NULL, NULL, '4.25', 0, 'products/nfEOJ3iJtYKXSFmuCpseobQdW8EpwZKdvZ9koE83.webp', NULL, NULL, NULL, NULL, NULL, NULL, 17, NULL, 'active', '2025-11-17 20:05:01', '2025-12-03 10:57:10'),
(21, 1, 'Sofa Ogami 2 Chỗ 1m4 (Vải VACT 10499)', 'sofa-ogami', NULL, 'Thiết kế\r\n\r\nLà sofa 2 chỗ (dạng văng đôi), phù hợp cho phòng khách nhỏ hoặc không gian yêu cầu sofa gọn gàng. \r\nTroppicity\r\n\r\nPhong cách hiện đại, nhẹ nhàng và tối giản, tạo cảm giác thanh lịch và tinh tế. \r\nTroppicity\r\n\r\nForm ghế cân đối, phần lưng và cánh tay thiết kế êm ái.\r\n\r\nKích thước\r\n\r\nChiều dài (Dài): 1.440 mm (~1,44 m). \r\nTroppicity\r\n\r\nChiều sâu (Rộng): 720 mm (~0,72 m). \r\nTroppicity\r\n\r\nChiều cao (Cao): 730 mm (~0,73 m). \r\nTroppicity\r\n\r\nKích thước này khiến sofa có diện tích nhỏ gọn, dễ đặt trong nhiều loại phòng.\r\n\r\nChất liệu\r\n\r\nKhung ghế làm bằng gỗ Beech (gỗ keo), đây là loại gỗ tự nhiên, chắc chắn và bền. \r\nTroppicity\r\n+1\r\n\r\nBề mặt bọc vải VACT 10499, là loại vải cao cấp, mang lại cảm giác mềm mịn, thoải mái khi tiếp xúc. \r\nTroppicity\r\n\r\nNệm dày, êm, khi ngồi tạo cảm giác thoải mái, thư giãn. \r\nTroppicity\r\n\r\nMàu sắc & Phong cách\r\n\r\nMô tả từ nhà cung cấp cho biết “nệm dày màu đậu đỏ” (tùy theo lô hàng / vải – có thể khác nếu đặt màu khác) để làm nổi bật không gian phòng khách. \r\nTroppicity\r\n\r\nPhong cách rất thích hợp với nội thất hiện đại, nhẹ nhàng, và những không gian sống đơn giản nhưng tinh tế. \r\nTroppicity\r\n\r\nCông dụng và phù hợp\r\n\r\nRất thích hợp cho phòng khách nhỏ / chung cư, hoặc làm ghế phụ ở phòng đọc sách, phòng làm việc.\r\n\r\nĐặc biệt hợp với người thích sofa không cồng kềnh nhưng vẫn muốn một chỗ ngồi “đủ rộng” để ngồi thoải mái hai người.\r\n\r\nGiá (tham khảo)\r\n\r\nTheo trang nội thất Việt Nam xuất khẩu, mẫu này có giá khoảng 12,900,000 ₫. \r\nTroppicity\r\n\r\nGiá có thể thay đổi tùy theo chi nhánh, chi phí vận chuyển, hoặc các chương trình khuyến mãi.\r\n\r\nBảo hành / Vận chuyển\r\n\r\nNhà Xinh (một trong những nơi bán mẫu này) có thông tin về bảo hành kỹ thuật sản xuất. \r\nNội thất Nhà Xinh\r\n\r\nVận chuyển có thể linh hoạt (tùy theo nơi bán và địa điểm khách).', NULL, '6450000.00', NULL, NULL, '4.80', 0, 'products/pKCwmMv5TyiX62WElkpov4jgBWFGYkG5EarF4En6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-11-18 01:02:44', '2025-11-29 14:28:59'),
(22, 1, 'Giường Leman 1m8 vải VACT10370', 'giuong-leman', NULL, 'Thiết kế & Phong cách\r\n\r\nKiểu giường bọc vải, mang phong cách hiện đại, tinh tế, phù hợp với nhiều không gian phòng ngủ.\r\n\r\nĐầu giường có độ cao vừa phải, ôm phần đầu, tạo cảm giác thoải mái khi tựa lưng.\r\n\r\nChân giường thiết kế chắc chắn, tôn lên nét thanh lịch.\r\n\r\nKích thước\r\n\r\nLà giường cỡ 1m8 (suitable để đặt nệm kích thước ~1,8m), theo Padecor. \r\npadecor.vn\r\n\r\nKích thước này rất phổ biến cho giường đôi lớn, rộng rãi cho hai người ngủ thoải mái.\r\n\r\nChất liệu\r\n\r\nPhần khung giường khả năng làm từ gỗ hoặc khung chắc (thông tin chi tiết khung chưa công khai rõ ràng trên nguồn Treetopix).\r\n\r\nBọc vải sử dụng vải VACT 10370, là loại vải nhập khẩu cao cấp theo mô tả sản phẩm. \r\ntreetopix.com\r\n\r\nVải mang lại cảm giác mềm mại khi chạm, đồng thời giúp giường trông sang trọng hơn.\r\n\r\nMàu sắc\r\n\r\nDo mã vải VACT10370, màu vải có thể là tông trung tính hoặc nhẹ nhàng (thường là màu xám, be hoặc tương tự – tùy theo bản mẫu thực tế).\r\n\r\nMàu này dễ phối với nhiều đồ nội thất khác như tủ, đèn, ga giường.\r\n\r\nChất lượng & Giá\r\n\r\nTrên Padecor, mẫu này có giá là 33.650.000 ₫. \r\npadecor.vn\r\n\r\nGiá cho thấy đây là sản phẩm cao cấp, phù hợp cho các không gian nhà sang hoặc muốn đầu tư giường bền đẹp.\r\n\r\nƯu điểm\r\n\r\nVải bọc cao cấp giúp giảm tiếng ồn khi di chuyển, đồng thời tạo cảm giác êm ái, thoải mái.\r\n\r\nThiết kế bọc vải giúp bảo vệ khung giường, và nếu vệ sinh vải khá dễ (có thể hút bụi, giặt vỏ vải nếu được thiết kế tháo rời).\r\n\r\nKích thước lớn (1m8) phù hợp cho gia đình, hoặc cặp đôi muốn giường rộng rãi.\r\n\r\nNhược điểm / Lưu ý\r\n\r\nDo bọc vải nên giường có thể hút bụi hơn so với giường gỗ hoặc da: cần vệ sinh vải thường xuyên.\r\n\r\nNếu vải không tháo rời để giặt, việc vệ sinh có thể khó hơn — nên kiểm tra khi mua.\r\n\r\nKích thước giường lớn đòi hỏi không gian phòng đủ rộng để đặt.\r\n\r\nPhù hợp với ai / nhu cầu nào\r\n\r\nRất phù hợp cho người thích nội thất hiện đại, sang trọng nhưng không muốn dùng giường da hoặc gỗ trơn.\r\n\r\nThích giường êm, có đầu tựa để đọc sách hoặc ngồi lên.\r\n\r\nThích sử dụng giường lớn (1m8) để có không gian ngủ thoải mái.', NULL, '23555000.00', NULL, NULL, '3.50', 0, 'products/wpqgNHXM9cck6o46PP6tbxJFaEkuYCrmU0obxS4K.webp', NULL, NULL, NULL, NULL, NULL, NULL, 17, NULL, 'active', '2025-11-18 01:13:46', '2025-11-29 16:57:37'),
(23, 2, 'Bàn ăn Coastal 6 chỗ 1,6m', 'ban-an-coastal-6-cho-16m', NULL, NULL, NULL, '11815000.00', NULL, NULL, '3.55', 0, 'products/k7V3Nf7sE5vHokG85plTRe52oru4jlQR6CFVKpZD.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, 'active', '2025-11-18 01:27:54', '2025-12-03 10:57:10'),
(24, 4, 'Bàn ăn Taura 8 chỗ E0202 Eponji 2m', 'ban-an-taura-8-cho-e0202-eponji-2m', NULL, 'Mô tả Bàn ăn Taura 8 chỗ E0202 Eponji 2m\r\n\r\nBàn ăn Taura 8 chỗ E0202 Eponji 2m là mẫu bàn ăn cao cấp mang phong cách hiện đại, sang trọng, phù hợp cho những không gian rộng rãi như phòng ăn gia đình, căn hộ cao cấp, biệt thự hoặc nhà hàng tinh tế. Thiết kế tối giản, đường nét sắc sảo cùng chất liệu bền bỉ giúp sản phẩm trở thành điểm nhấn nổi bật trong không gian nội thất.\r\n\r\nĐặc điểm nổi bật\r\n\r\nKích thước 2m rộng rãi, thoải mái cho 6–8 người ngồi, phù hợp cho những buổi họp mặt gia đình hoặc tiếp khách.\r\n\r\nMặt bàn Eponji cao cấp, có khả năng chống trầy xước, chống thấm và dễ vệ sinh, giữ độ mới lâu dài.\r\n\r\nKhung bàn chắc chắn, được gia công tỉ mỉ, đảm bảo độ ổn định và an toàn trong quá trình sử dụng.\r\n\r\nThiết kế hiện đại – tối giản, dễ dàng kết hợp với nhiều phong cách nội thất khác nhau.\r\n\r\nTone màu trang nhã, mang đến cảm giác sang trọng và hài hoà cho không gian phòng ăn.\r\n\r\nỨng dụng\r\n\r\nBàn ăn gia đình\r\n\r\nBàn tiếp khách trong căn hộ cao cấp\r\n\r\nNội thất quán ăn, nhà hàng sang trọng\r\n\r\nKhông gian làm việc hoặc tiếp khách tại văn phòng\r\n\r\nLợi ích khi sử dụng\r\n\r\nTăng tính thẩm mỹ cho không gian sống\r\n\r\nMang lại sự thoải mái cho gia đình nhiều thành viên\r\n\r\nBền, dễ lau chùi, không lo ố vàng hay bong tróc\r\n\r\nGiá trị sử dụng lâu dài nhờ chất lượng hoàn thiện cao', NULL, '26775000.00', NULL, NULL, '4.25', 0, 'products/ahQut9kPZFQhtPOP3pjiHs1PTEc4FM1uwrwvzrqm.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, 'active', '2025-11-18 11:00:35', '2025-12-03 13:27:54'),
(25, 1, 'Sản phẩm demo 1', 'san-pham-demo-1-nUMMGT', 'DEMO-0001', 'Mô tả ngắn cho Sản phẩm demo 1. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 1', '4754842.00', NULL, 9, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(26, 1, 'Sản phẩm demo 2', 'san-pham-demo-2-vB6THu', 'DEMO-0002', 'Mô tả ngắn cho Sản phẩm demo 2. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 2', '18432377.00', NULL, 44, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(27, 1, 'Sản phẩm demo 3', 'san-pham-demo-3-0FJsgr', 'DEMO-0003', 'Mô tả ngắn cho Sản phẩm demo 3. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 3', '8787540.00', NULL, 10, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(28, 1, 'Sản phẩm demo 4', 'san-pham-demo-4-wreLY1', 'DEMO-0004', 'Mô tả ngắn cho Sản phẩm demo 4. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 4', '8294599.00', NULL, 29, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(29, 1, 'Sản phẩm demo 5', 'san-pham-demo-5-Sp8ZQz', 'DEMO-0005', 'Mô tả ngắn cho Sản phẩm demo 5. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 5', '3662298.00', NULL, 5, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(30, 1, 'Sản phẩm demo 6', 'san-pham-demo-6-FMrvEO', 'DEMO-0006', 'Mô tả ngắn cho Sản phẩm demo 6. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 6', '10051602.00', NULL, 30, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(31, 1, 'Sản phẩm demo 7', 'san-pham-demo-7-8VAezA', 'DEMO-0007', 'Mô tả ngắn cho Sản phẩm demo 7. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 7', '18419746.00', NULL, 21, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(32, 1, 'Sản phẩm demo 8', 'san-pham-demo-8-A2p1Yw', 'DEMO-0008', 'Mô tả ngắn cho Sản phẩm demo 8. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 8', '13884431.00', NULL, 46, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(33, 1, 'Sản phẩm demo 9', 'san-pham-demo-9-mjsLtC', 'DEMO-0009', 'Mô tả ngắn cho Sản phẩm demo 9. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 9', '9103506.00', NULL, 8, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(34, 1, 'Sản phẩm demo 10', 'san-pham-demo-10-HhQXR6', 'DEMO-0010', 'Mô tả ngắn cho Sản phẩm demo 10. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 10', '16093430.00', NULL, 25, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(35, 1, 'Sản phẩm demo 11', 'san-pham-demo-11-lI9w0x', 'DEMO-0011', 'Mô tả ngắn cho Sản phẩm demo 11. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 11', '15653569.00', NULL, 27, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(36, 1, 'Sản phẩm demo 12', 'san-pham-demo-12-2eolbc', 'DEMO-0012', 'Mô tả ngắn cho Sản phẩm demo 12. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 12', '10039663.00', NULL, 3, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(37, 1, 'Sản phẩm demo 13', 'san-pham-demo-13-Wa0OMn', 'DEMO-0013', 'Mô tả ngắn cho Sản phẩm demo 13. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 13', '15315875.00', NULL, 9, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(38, 1, 'Sản phẩm demo 14', 'san-pham-demo-14-Pa3ifk', 'DEMO-0014', 'Mô tả ngắn cho Sản phẩm demo 14. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 14', '8287840.00', NULL, 33, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(39, 1, 'Sản phẩm demo 15', 'san-pham-demo-15-ckhJ1I', 'DEMO-0015', 'Mô tả ngắn cho Sản phẩm demo 15. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 15', '8414448.00', NULL, 45, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(40, 1, 'Sản phẩm demo 16', 'san-pham-demo-16-J51fAS', 'DEMO-0016', 'Mô tả ngắn cho Sản phẩm demo 16. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 16', '11790090.00', NULL, 47, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(41, 1, 'Sản phẩm demo 17', 'san-pham-demo-17-ekBEh3', 'DEMO-0017', 'Mô tả ngắn cho Sản phẩm demo 17. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 17', '8473488.00', NULL, 37, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(42, 1, 'Sản phẩm demo 18', 'san-pham-demo-18-N2mEvj', 'DEMO-0018', 'Mô tả ngắn cho Sản phẩm demo 18. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 18', '5808131.00', NULL, 31, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(43, 1, 'Sản phẩm demo 19', 'san-pham-demo-19-sIB5Qy', 'DEMO-0019', 'Mô tả ngắn cho Sản phẩm demo 19. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 19', '12039343.00', NULL, 44, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(44, 1, 'Sản phẩm demo 20', 'san-pham-demo-20-4r4T7T', 'DEMO-0020', 'Mô tả ngắn cho Sản phẩm demo 20. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 20', '19259968.00', NULL, 9, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(45, 1, 'Sản phẩm demo 21', 'san-pham-demo-21-SMmVcv', 'DEMO-0021', 'Mô tả ngắn cho Sản phẩm demo 21. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 21', '2644269.00', NULL, 18, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(46, 1, 'Sản phẩm demo 22', 'san-pham-demo-22-hiM8Pg', 'DEMO-0022', 'Mô tả ngắn cho Sản phẩm demo 22. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 22', '4696964.00', NULL, 22, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(47, 1, 'Sản phẩm demo 23', 'san-pham-demo-23-PCdd0B', 'DEMO-0023', 'Mô tả ngắn cho Sản phẩm demo 23. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 23', '11191211.00', NULL, 20, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(48, 1, 'Sản phẩm demo 24', 'san-pham-demo-24-SwtopC', 'DEMO-0024', 'Mô tả ngắn cho Sản phẩm demo 24. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 24', '14213138.00', NULL, 48, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(49, 1, 'Sản phẩm demo 25', 'san-pham-demo-25-CK6sW2', 'DEMO-0025', 'Mô tả ngắn cho Sản phẩm demo 25. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 25', '16869666.00', NULL, 41, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(50, 1, 'Sản phẩm demo 26', 'san-pham-demo-26-cIq4S0', 'DEMO-0026', 'Mô tả ngắn cho Sản phẩm demo 26. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 26', '8061433.00', NULL, 6, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(51, 1, 'Sản phẩm demo 27', 'san-pham-demo-27-7Vd012', 'DEMO-0027', 'Mô tả ngắn cho Sản phẩm demo 27. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 27', '13043624.00', NULL, 22, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(52, 1, 'Sản phẩm demo 28', 'san-pham-demo-28-Vyew21', 'DEMO-0028', 'Mô tả ngắn cho Sản phẩm demo 28. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 28', '10762765.00', NULL, 5, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(53, 1, 'Sản phẩm demo 29', 'san-pham-demo-29-gdUJez', 'DEMO-0029', 'Mô tả ngắn cho Sản phẩm demo 29. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 29', '9541737.00', NULL, 50, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47'),
(54, 1, 'Sản phẩm demo 30', 'san-pham-demo-30-8zq27m', 'DEMO-0030', 'Mô tả ngắn cho Sản phẩm demo 30. Đây là dữ liệu demo phục vụ phát triển giao diện.', 'Sản phẩm demo 30', '1419300.00', NULL, 19, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, 'active', '2025-12-04 07:56:47', '2025-12-04 07:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `created_at`, `updated_at`) VALUES
(7, 19, 'products/6O62kQL2TSiJP4J4duZphKQTX24N1t99TEYEC6vQ.jpg', '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(8, 19, 'products/KqB6pQ4AsWIOv8FspY5tkJayH0957yrqeHRyEy6K.jpg', '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(9, 19, 'products/B4GIC0ETSQMljMA1dpHpFTBgRXd0QkyAR5SrorzL.jpg', '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(10, 19, 'products/WDM6TOjtMgDExxeKaYsU7xMFiNZwxWsMg9x9jA9B.jpg', '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(11, 19, 'products/r3Y1a9EwlEiWSlb5B9RUDXuexTvFYSKDIMpskPv0.jpg', '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(12, 19, 'products/9oTpnmBGDeglEf26kknAmwaXhYDjSfrMipic9QIr.jpg', '2025-11-16 18:51:56', '2025-11-16 18:51:56'),
(13, 19, 'products/aO9ZQR5LWNTH88rBuELYXP6jw2ECCgBUj1GblXaq.jpg', '2025-11-16 18:51:56', '2025-11-16 18:51:56'),
(14, 19, 'products/2ZymGOQMhGgWbDoFmLZcF6cbUB1qV66xZUOBSTAt.jpg', '2025-11-16 18:51:56', '2025-11-16 18:51:56'),
(15, 19, 'products/pUUISvfkVRVlcZH68i57B3NBDJyCacxQsgMLWoct.jpg', '2025-11-16 18:51:56', '2025-11-16 18:51:56'),
(16, 19, 'products/0Vi3G8i3sZ6xfRgZXU0uPTHWrYRl5ehU3RTpLax4.jpg', '2025-11-16 18:51:56', '2025-11-16 18:51:56'),
(17, 20, 'products/IEEND9sRm5uVlPJDy7q3OvBqUuuXuWsekGp6cGvd.webp', '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(18, 20, 'products/lckgz4QpBDLZlvlwm3LN1vDN48hVnqyp4M7hcFC3.webp', '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(19, 20, 'products/lowW1b4eZwaa5CouGolq5RMdPtYwcu4Fey258F9W.webp', '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(20, 20, 'products/L9gk7r695ixTvGQ81RYKpmMVo4GkHPCtSLdbyNKj.webp', '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(21, 20, 'products/DDq3R8ZR62a41XGsG5e46UbMbRkhhQtucsN7eUz0.webp', '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(22, 21, 'products/2PsVsgVJjWvPnGfuuk1JVsrNYDwSCs2gKYrPYJTJ.jpg', '2025-11-18 01:02:44', '2025-11-18 01:02:44'),
(23, 21, 'products/B4o6Rok3HEKOMoAzns9vfuUaT2hZvDKSP7nJTiHE.webp', '2025-11-18 01:02:44', '2025-11-18 01:02:44'),
(24, 23, 'products/X3QKifKUb56DMevy2tkSX9MB8VnONcoEozBBrSLZ.jpg', '2025-11-18 01:27:54', '2025-11-18 01:27:54'),
(25, 23, 'products/CZD65kL10LF90QJj1Hk8PILfhv31dLSLcjGm943e.jpg', '2025-11-18 01:27:54', '2025-11-18 01:27:54'),
(26, 24, 'products/g2e2EEFMbg6wLIkAgGyJAfJAgZFnXgev6F6tGKXi.webp', '2025-11-18 11:00:35', '2025-11-18 11:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_version` int UNSIGNED NOT NULL DEFAULT '1',
  `color_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `product_version`, `color_name`, `color_code`, `length`, `width`, `height`, `price`, `stock`, `sku`, `created_at`, `updated_at`) VALUES
(8, 19, 1, 'trắng', '#ffffff', '277.00', '105.00', '100.00', NULL, 100, NULL, '2025-11-16 18:45:01', '2025-11-16 18:45:01'),
(9, 20, 1, 'peach puff', '#ffdab9', '200.00', '160.00', '94.00', NULL, 15, NULL, '2025-11-17 20:05:01', '2025-11-17 20:05:01'),
(10, 21, 1, 'cam', '#ff8800', '144.00', '720.00', '730.00', NULL, 18, NULL, '2025-11-18 01:02:44', '2025-11-18 03:59:01'),
(11, 22, 1, 'xám', '#a1a1a1', '200.00', '180.00', '107.00', NULL, 17, NULL, '2025-11-18 01:13:46', '2025-11-18 09:46:45'),
(12, 23, 2, 'gỗ', '#ddd46e', '160.00', '80.00', '75.50', '11815000.00', 6, NULL, '2025-11-18 01:27:54', '2025-12-03 09:54:41'),
(13, 24, 4, 'trắng', '#ffffff', '200.00', '90.00', '725.00', '26775000.00', 12, NULL, '2025-11-18 11:00:35', '2025-12-03 13:27:54');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percent','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `status` enum('active','expired','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL COMMENT 'ID đơn hàng',
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'ID khách hàng',
  `refund_type` enum('cancel','return') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Loại hoàn tiền: hủy đơn hoặc trả hàng',
  `cancel_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lý do hủy/trả hàng',
  `reason_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả chi tiết lý do',
  `refund_amount` decimal(15,2) NOT NULL COMMENT 'Số tiền hoàn',
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên ngân hàng',
  `bank_account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số tài khoản',
  `account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên chủ tài khoản',
  `status` enum('pending','approved','rejected','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái',
  `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của admin',
  `processed_by` bigint UNSIGNED DEFAULT NULL COMMENT 'ID admin xử lý',
  `processed_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xử lý',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `images` json DEFAULT NULL,
  `video` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','approved','rejected','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reported_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `order_id`, `rating`, `title`, `content`, `comment`, `images`, `video`, `tags`, `is_verified_purchase`, `status`, `reported_count`, `likes_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 19, 12, NULL, 4, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, NULL, NULL, NULL, 0, 'approved', 3, 0, '2025-09-07 14:28:58', '2025-11-29 14:28:58', NULL),
(2, 19, 8, NULL, 4, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, NULL, NULL, NULL, 1, 'hidden', 0, 0, '2025-11-05 14:28:58', '2025-11-29 14:28:58', NULL),
(3, 19, 14, NULL, 5, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 1, 'approved', 1, 0, '2025-09-24 14:28:58', '2025-11-29 14:29:07', NULL),
(4, 19, 13, NULL, 2, NULL, 'Sản phẩm có vấn đề nhỏ, nhưng vẫn dùng được.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-10-17 14:28:58', '2025-12-03 10:57:10', NULL),
(5, 19, 4, NULL, 3, NULL, 'Sản phẩm ổn, không có gì đặc biệt. Giá cả hợp lý.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-11-16 14:28:58', '2025-11-29 14:28:58', NULL),
(6, 19, 8, NULL, 5, NULL, 'Sản phẩm đẹp, đúng như hình ảnh. Giao hàng nhanh chóng.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-11-07 14:28:58', '2025-11-29 14:28:58', NULL),
(7, 20, 11, NULL, 2, NULL, 'Không đúng như mô tả. Hơi thất vọng.', NULL, NULL, NULL, NULL, 1, 'hidden', 3, 0, '2025-10-10 14:28:58', '2025-11-29 14:28:58', NULL),
(8, 20, 15, NULL, 5, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, NULL, NULL, NULL, 0, 'approved', 0, 0, '2025-09-04 14:28:59', '2025-12-03 10:57:10', NULL),
(9, 20, 14, NULL, 4, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, NULL, NULL, NULL, 1, 'rejected', 1, 0, '2025-10-02 14:28:59', '2025-11-29 14:29:07', NULL),
(10, 20, 8, NULL, 5, NULL, 'Sản phẩm tuyệt vời, tôi rất thích. Khuyến nghị mọi người nên mua.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-09-07 14:28:59', '2025-11-29 14:28:59', NULL),
(11, 20, 15, NULL, 2, NULL, 'Chất lượng không như mong đợi. Hơi thất vọng.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 0, 'rejected', 0, 0, '2025-11-02 14:28:59', '2025-11-30 07:39:23', NULL),
(12, 20, 8, NULL, 4, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 0, 'approved', 1, 0, '2025-11-11 14:28:59', '2025-11-29 14:29:07', NULL),
(13, 20, 15, NULL, 3, NULL, 'OK, đúng như mô tả. Giao hàng hơi chậm một chút.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-11-09 14:28:59', '2025-11-30 07:39:23', NULL),
(14, 21, 12, NULL, 5, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-09-24 14:28:59', '2025-11-29 14:28:59', NULL),
(15, 21, 14, NULL, 5, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-09-25 14:28:59', '2025-11-29 14:28:59', NULL),
(16, 21, 12, NULL, 5, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-08-31 14:28:59', '2025-11-29 14:28:59', NULL),
(17, 21, 13, NULL, 4, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-09-17 14:28:59', '2025-11-30 07:39:23', NULL),
(18, 21, 11, NULL, 5, NULL, 'Chất lượng tốt, giá cả hợp lý. Đã mua nhiều lần và rất hài lòng.', NULL, NULL, NULL, NULL, 0, 'rejected', 1, 0, '2025-09-06 14:28:59', '2025-11-29 14:28:59', NULL),
(19, 21, 4, NULL, 5, NULL, 'Sản phẩm tuyệt vời, tôi rất thích. Khuyến nghị mọi người nên mua.', NULL, NULL, NULL, NULL, 0, 'approved', 1, 0, '2025-10-25 14:28:59', '2025-11-29 14:29:07', NULL),
(20, 21, 4, NULL, 4, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, NULL, NULL, NULL, 1, 'hidden', 0, 0, '2025-10-16 14:28:59', '2025-11-29 14:28:59', NULL),
(21, 22, 15, NULL, 5, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 1, 0, '2025-09-18 14:28:59', '2025-11-30 07:39:23', NULL),
(22, 22, 12, NULL, 4, NULL, 'Sản phẩm tuyệt vời, tôi rất thích. Khuyến nghị mọi người nên mua.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 0, 'hidden', 0, 0, '2025-09-14 14:28:59', '2025-11-30 07:39:23', NULL),
(23, 22, 14, NULL, 3, NULL, 'Sản phẩm ổn, không có gì đặc biệt. Giá cả hợp lý.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 0, 'approved', 1, 0, '2025-10-11 14:28:59', '2025-11-30 07:39:23', NULL),
(24, 22, 11, NULL, 5, NULL, 'Chất lượng tốt, giá cả hợp lý. Đã mua nhiều lần và rất hài lòng.', NULL, NULL, NULL, NULL, 0, 'rejected', 0, 0, '2025-09-01 14:28:59', '2025-11-29 14:28:59', NULL),
(25, 22, 14, NULL, 5, NULL, 'Sản phẩm đẹp, đúng như hình ảnh. Giao hàng nhanh chóng.', NULL, NULL, NULL, NULL, 1, 'rejected', 1, 0, '2025-10-20 14:28:59', '2025-11-29 14:29:07', NULL),
(26, 22, 12, NULL, 3, NULL, 'OK, đúng như mô tả. Giao hàng hơi chậm một chút.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 0, 'approved', 0, 0, '2025-11-02 14:28:59', '2025-11-30 07:39:23', NULL),
(27, 22, 11, NULL, 4, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, NULL, NULL, NULL, 1, 'hidden', 0, 0, '2025-10-08 14:28:59', '2025-11-29 14:28:59', NULL),
(28, 22, 8, NULL, 5, NULL, 'Chất lượng tốt, giá cả hợp lý. Đã mua nhiều lần và rất hài lòng.', NULL, NULL, NULL, NULL, 1, 'rejected', 1, 0, '2025-09-22 14:28:59', '2025-11-29 14:29:07', NULL),
(29, 22, 13, NULL, 3, NULL, 'Chất lượng tạm được, đúng với giá tiền.', NULL, NULL, NULL, NULL, 1, 'approved', 2, 0, '2025-11-08 14:28:59', '2025-11-29 14:28:59', NULL),
(30, 22, 13, NULL, 4, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 1, 'hidden', 3, 0, '2025-10-03 14:28:59', '2025-11-29 14:28:59', NULL),
(31, 23, 12, NULL, 5, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-09-18 14:28:59', '2025-11-29 14:28:59', NULL),
(32, 23, 4, NULL, 5, NULL, 'Chất lượng tốt, giá cả hợp lý. Đã mua nhiều lần và rất hài lòng.', NULL, NULL, NULL, NULL, 0, 'approved', 0, 0, '2025-10-17 14:28:59', '2025-11-29 14:28:59', NULL),
(33, 23, 12, NULL, 5, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-10-27 14:28:59', '2025-12-03 10:57:10', NULL),
(34, 23, 8, NULL, 4, NULL, 'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-10-30 14:28:59', '2025-11-30 07:39:23', NULL),
(35, 23, 4, NULL, 2, NULL, 'Sản phẩm có vấn đề nhỏ, nhưng vẫn dùng được.', NULL, NULL, NULL, NULL, 1, 'rejected', 2, 0, '2025-09-21 14:28:59', '2025-11-29 14:28:59', NULL),
(36, 23, 14, NULL, 4, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 0, 'approved', 1, 0, '2025-09-06 14:28:59', '2025-11-29 14:29:07', NULL),
(37, 23, 12, NULL, 4, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, NULL, NULL, NULL, 1, 'approved', 1, 0, '2025-09-28 14:28:59', '2025-11-29 14:28:59', NULL),
(38, 23, 13, NULL, 4, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 1, 'pending', 1, 0, '2025-11-02 14:28:59', '2025-11-29 14:29:07', NULL),
(39, 23, 8, NULL, 5, NULL, 'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.', NULL, NULL, NULL, NULL, 0, 'approved', 0, 0, '2025-09-03 14:28:59', '2025-11-29 14:28:59', NULL),
(40, 23, 13, NULL, 5, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 1, 'hidden', 0, 0, '2025-10-20 14:28:59', '2025-11-29 14:28:59', NULL),
(41, 24, 8, NULL, 5, NULL, 'Sản phẩm đẹp, đúng như hình ảnh. Giao hàng nhanh chóng.', NULL, NULL, NULL, NULL, 1, 'approved', 0, 0, '2025-11-20 14:28:59', '2025-11-29 14:28:59', NULL),
(42, 24, 13, NULL, 4, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 0, 'approved', 0, 0, '2025-10-04 14:28:59', '2025-12-03 10:57:10', NULL),
(43, 24, 12, NULL, 1, NULL, 'Sản phẩm có vấn đề nhỏ, nhưng vẫn dùng được.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'hidden', 3, 0, '2025-10-25 14:28:59', '2025-11-30 07:39:23', NULL),
(44, 24, 12, NULL, 5, NULL, 'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.', NULL, NULL, NULL, NULL, 0, 'approved', 1, 0, '2025-10-25 14:28:59', '2025-11-29 14:28:59', NULL),
(45, 24, 8, NULL, 5, NULL, 'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!', NULL, NULL, NULL, NULL, 1, 'hidden', 0, 0, '2025-09-26 14:28:59', '2025-11-29 14:28:59', NULL),
(46, 24, 11, NULL, 3, NULL, 'OK, đúng như mô tả. Giao hàng hơi chậm một chút.', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\", \"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-09-30 14:28:59', '2025-12-03 10:57:10', NULL),
(47, 23, 12, NULL, 1, NULL, 'tôi thấy rất là ok, rất tuyệt vời', NULL, '[\"reviews/AmMC8inXJWgb8KooPcPNTzQjoDgcTpBrN2NlAhlj.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-11-29 19:01:21', '2025-11-29 19:01:21', NULL),
(48, 23, 12, NULL, 1, NULL, 'rát đẹp và tuyệt vời , tôi sẽ mua lại', NULL, '[\"reviews/egwb3e2Kp45tV84iSbGdzU7YkwcqzPQVGr4owQX0.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-11-30 07:36:00', '2025-11-30 07:36:00', NULL),
(49, 23, 12, NULL, 2, NULL, 'rát đẹp và tuyệt bbghghvời , tôi sẽ mua lại', NULL, '[\"reviews/QHAA0DyW2lhWzlrMr89EnVvcRX6Qd8gPs2MLeCLW.jpg\"]', NULL, NULL, 1, 'approved', 0, 0, '2025-12-01 20:16:31', '2025-12-01 20:16:31', NULL),
(50, 23, 12, NULL, 2, NULL, 'jklklllllllllllllllllllllllllllll', NULL, '[\"reviews/mdCFS09NFa2Q1nnkZPUSkLBCjnC4sWeN3UPOcm6z.jpg\"]', NULL, NULL, 1, 'rejected', 1, 0, '2025-12-01 20:47:06', '2025-12-03 10:55:45', NULL),
(51, 23, 12, NULL, 3, NULL, 'vvbvcbbbbbbbbbbbbbbbbbbbbbbb', NULL, '[\"reviews/rcFQs1fC7kjyiVYCyP8NOnXx1X5MnL7FdluMulKR.jpg\"]', NULL, NULL, 1, 'approved', 1, 0, '2025-12-03 10:00:23', '2025-12-03 10:54:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `review_audit_logs`
--

CREATE TABLE `review_audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_audit_logs`
--

INSERT INTO `review_audit_logs` (`id`, `review_id`, `admin_id`, `action`, `old_status`, `new_status`, `notes`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 50, 10, 'hide', 'hidden', 'hidden', 'Review hidden by admin', '{\"new_status\": \"hidden\", \"old_status\": \"approved\"}', '2025-12-03 06:26:09', '2025-12-03 06:26:09'),
(2, 50, 10, 'show', 'approved', 'approved', 'Review shown by admin', '{\"new_status\": \"approved\", \"old_status\": \"hidden\"}', '2025-12-03 08:52:29', '2025-12-03 08:52:29'),
(3, 51, 10, 'hide', 'hidden', 'hidden', 'Review hidden by admin', '{\"new_status\": \"hidden\", \"old_status\": \"approved\"}', '2025-12-03 10:04:42', '2025-12-03 10:04:42'),
(4, 51, 10, 'reply', 'hidden', 'hidden', 'Admin replied to review', '{\"reply_content\": \"cảm ơn quý khách\"}', '2025-12-03 10:05:49', '2025-12-03 10:05:49'),
(5, 51, 10, 'show', 'approved', 'approved', 'Review shown by admin', '{\"new_status\": \"approved\", \"old_status\": \"hidden\"}', '2025-12-03 10:09:50', '2025-12-03 10:09:50'),
(6, 51, 10, 'reply', 'approved', 'approved', 'Admin replied to review', '{\"reply_content\": \"rất cảm ơn quý khách\"}', '2025-12-03 10:10:23', '2025-12-03 10:10:23'),
(7, 51, 10, 'reply_delete', 'approved', 'approved', 'Admin deleted a reply', '{\"reply_id\": \"14\", \"reply_content\": \"rất cảm ơn quý khách\"}', '2025-12-03 10:23:42', '2025-12-03 10:23:42'),
(8, 51, 10, 'reply', 'approved', 'approved', 'Admin replied to review', '{\"reply_content\": \"rất cảm ơn\"}', '2025-12-03 10:29:49', '2025-12-03 10:29:49'),
(9, 51, 10, 'reply_delete', 'approved', 'approved', 'Admin deleted a reply (bulk)', '{\"bulk\": true, \"reply_id\": 13, \"reply_content\": \"cảm ơn quý khách\"}', '2025-12-03 10:30:21', '2025-12-03 10:30:21'),
(10, 51, 10, 'hide', 'hidden', 'hidden', 'Review hidden by admin', '{\"new_status\": \"hidden\", \"old_status\": \"approved\"}', '2025-12-03 10:30:43', '2025-12-03 10:30:43'),
(11, 51, 10, 'show', 'approved', 'approved', 'Review shown by admin', '{\"new_status\": \"approved\", \"old_status\": \"hidden\"}', '2025-12-03 10:31:02', '2025-12-03 10:31:02'),
(12, 51, 10, 'hide', 'hidden', 'hidden', 'Review hidden by admin', '{\"new_status\": \"hidden\", \"old_status\": \"approved\"}', '2025-12-03 10:53:42', '2025-12-03 10:53:42'),
(13, 51, 10, 'show', 'approved', 'approved', 'Review shown by admin', '{\"new_status\": \"approved\", \"old_status\": \"hidden\"}', '2025-12-03 10:54:10', '2025-12-03 10:54:10'),
(14, 51, 10, 'reply', 'approved', 'approved', 'Admin replied to review', '{\"reply_content\": \"cảm ơn bạn\"}', '2025-12-03 10:54:39', '2025-12-03 10:54:39'),
(15, 50, 10, 'reject', 'rejected', 'rejected', 'Review rejected by admin', '{\"new_status\": \"rejected\", \"old_status\": \"approved\"}', '2025-12-03 10:55:45', '2025-12-03 10:55:45'),
(16, 4, 10, 'approve', 'approved', 'approved', 'Bulk approved by admin', '{\"new_status\": \"approved\", \"old_status\": \"pending\"}', '2025-12-03 10:57:10', '2025-12-03 10:57:10'),
(17, 8, 10, 'approve', 'approved', 'approved', 'Bulk approved by admin', '{\"new_status\": \"approved\", \"old_status\": \"pending\"}', '2025-12-03 10:57:10', '2025-12-03 10:57:10'),
(18, 33, 10, 'approve', 'approved', 'approved', 'Bulk approved by admin', '{\"new_status\": \"approved\", \"old_status\": \"pending\"}', '2025-12-03 10:57:10', '2025-12-03 10:57:10'),
(19, 42, 10, 'approve', 'approved', 'approved', 'Bulk approved by admin', '{\"new_status\": \"approved\", \"old_status\": \"pending\"}', '2025-12-03 10:57:10', '2025-12-03 10:57:10'),
(20, 46, 10, 'approve', 'approved', 'approved', 'Bulk approved by admin', '{\"new_status\": \"approved\", \"old_status\": \"pending\"}', '2025-12-03 10:57:10', '2025-12-03 10:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `review_helpful`
--

CREATE TABLE `review_helpful` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_helpful`
--

INSERT INTO `review_helpful` (`id`, `review_id`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 49, 12, '2025-12-01 20:40:11', '2025-12-01 20:40:11'),
(4, 47, 12, '2025-12-01 20:46:03', '2025-12-01 20:46:03'),
(6, 51, 10, '2025-12-03 10:31:36', '2025-12-03 10:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `review_replies`
--

CREATE TABLE `review_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_replies`
--

INSERT INTO `review_replies` (`id`, `review_id`, `admin_id`, `content`, `created_at`, `updated_at`) VALUES
(1, 13, 1, 'Xin lỗi vì trải nghiệm không tốt. Vui lòng liên hệ với chúng tôi để được hỗ trợ.', '2025-11-11 14:28:59', '2025-11-29 14:28:59'),
(2, 15, 1, 'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.', '2025-09-27 14:28:59', '2025-11-29 14:28:59'),
(3, 16, 1, 'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.', '2025-09-03 14:28:59', '2025-11-29 14:28:59'),
(4, 19, 1, 'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.', '2025-10-27 14:28:59', '2025-11-29 14:28:59'),
(5, 23, 1, 'Cảm ơn bạn đã phản hồi. Chúng tôi sẽ cải thiện chất lượng sản phẩm.', '2025-10-12 14:28:59', '2025-11-29 14:28:59'),
(6, 29, 1, 'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.', '2025-11-10 14:28:59', '2025-11-29 14:28:59'),
(7, 5, 1, 'Cảm ơn bạn đã tin tưởng và ủng hộ shop. Chúc bạn có trải nghiệm tốt!', '2025-11-17 14:28:58', '2025-11-29 14:29:22'),
(8, 6, 3, 'Cảm ơn bạn đã tin tưởng và ủng hộ shop. Chúc bạn có trải nghiệm tốt!', '2025-11-09 14:28:58', '2025-11-29 14:29:22'),
(9, 34, 3, 'Rất vui khi nhận được phản hồi tích cực từ bạn. Chúng tôi sẽ tiếp tục cố gắng!', '2025-10-31 14:28:59', '2025-11-29 14:29:22'),
(10, 39, 10, 'Rất vui khi nhận được phản hồi tích cực từ bạn. Chúng tôi sẽ tiếp tục cố gắng!', '2025-09-07 14:28:59', '2025-11-29 14:29:22'),
(11, 44, 9, 'Cảm ơn bạn đã đánh giá sản phẩm. Chúng tôi rất vui khi bạn hài lòng!', '2025-10-29 14:28:59', '2025-11-29 14:29:22'),
(12, 47, 10, 'sdffgg', '2025-11-29 20:21:14', '2025-11-29 20:21:14'),
(15, 51, 10, 'rất cảm ơn', '2025-12-03 10:29:49', '2025-12-03 10:29:49'),
(16, 51, 10, 'cảm ơn bạn', '2025-12-03 10:54:39', '2025-12-03 10:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `review_reports`
--

CREATE TABLE `review_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `reason` enum('spam','offensive','false_info','inappropriate','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_reports`
--

INSERT INTO `review_reports` (`id`, `review_id`, `user_id`, `reason`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 'false_info', 'Nội dung không phù hợp', '2025-09-12 14:28:58', '2025-11-29 14:28:58'),
(2, 1, 13, 'spam', 'Nội dung không phù hợp', '2025-09-09 14:28:58', '2025-11-29 14:28:58'),
(3, 1, 15, 'inappropriate', NULL, '2025-09-11 14:28:58', '2025-11-29 14:28:58'),
(4, 7, 4, 'inappropriate', 'Nội dung không phù hợp', '2025-10-12 14:28:58', '2025-11-29 14:28:59'),
(5, 7, 13, 'offensive', 'Nội dung không phù hợp', '2025-10-14 14:28:58', '2025-11-29 14:28:59'),
(6, 7, 14, 'false_info', 'Nội dung không phù hợp', '2025-10-15 14:28:58', '2025-11-29 14:28:59'),
(7, 18, 14, 'other', NULL, '2025-09-12 14:28:59', '2025-11-29 14:28:59'),
(8, 29, 12, 'inappropriate', NULL, '2025-11-13 14:28:59', '2025-11-29 14:28:59'),
(9, 29, 15, 'inappropriate', 'Nội dung không phù hợp', '2025-11-10 14:28:59', '2025-11-29 14:28:59'),
(10, 30, 11, 'offensive', NULL, '2025-10-08 14:28:59', '2025-11-29 14:28:59'),
(11, 30, 12, 'false_info', NULL, '2025-10-08 14:28:59', '2025-11-29 14:28:59'),
(12, 30, 14, 'other', NULL, '2025-10-08 14:28:59', '2025-11-29 14:28:59'),
(13, 35, 8, 'inappropriate', NULL, '2025-09-25 14:28:59', '2025-11-29 14:28:59'),
(14, 35, 13, 'inappropriate', NULL, '2025-09-28 14:28:59', '2025-11-29 14:28:59'),
(15, 37, 4, 'other', 'Nội dung không phù hợp', '2025-10-01 14:28:59', '2025-11-29 14:28:59'),
(16, 43, 13, 'spam', NULL, '2025-10-30 14:28:59', '2025-11-29 14:28:59'),
(17, 43, 14, 'inappropriate', NULL, '2025-10-27 14:28:59', '2025-11-29 14:28:59'),
(18, 43, 15, 'spam', 'Nội dung không phù hợp', '2025-10-27 14:28:59', '2025-11-29 14:28:59'),
(19, 44, 11, 'other', 'Nội dung không phù hợp', '2025-10-29 14:28:59', '2025-11-29 14:28:59'),
(20, 3, 13, 'other', 'Ngôn từ xúc phạm', '2025-11-18 14:29:07', '2025-11-29 14:29:07'),
(21, 9, 11, 'spam', NULL, '2025-11-19 14:29:07', '2025-11-29 14:29:07'),
(22, 12, 15, 'other', 'Nội dung không phù hợp', '2025-11-13 14:29:07', '2025-11-29 14:29:07'),
(23, 19, 15, 'other', 'Thông tin sai sự thật', '2025-11-11 14:29:07', '2025-11-29 14:29:07'),
(24, 21, 11, 'inappropriate', 'Quảng cáo không liên quan', '2025-11-01 14:29:07', '2025-11-29 14:29:07'),
(25, 23, 11, 'other', 'Nội dung spam', '2025-11-18 14:29:07', '2025-11-29 14:29:07'),
(26, 25, 4, 'false_info', NULL, '2025-11-22 14:29:07', '2025-11-29 14:29:07'),
(27, 28, 13, 'inappropriate', 'Thông tin sai sự thật', '2025-11-22 14:29:07', '2025-11-29 14:29:07'),
(28, 36, 12, 'other', NULL, '2025-11-07 14:29:07', '2025-11-29 14:29:07'),
(29, 38, 12, 'spam', 'Nội dung không phù hợp', '2025-11-16 14:29:07', '2025-11-29 14:29:07'),
(30, 51, 10, 'false_info', NULL, '2025-12-03 10:50:38', '2025-12-03 10:50:38'),
(31, 50, 10, 'inappropriate', NULL, '2025-12-03 10:55:13', '2025-12-03 10:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL),
(2, 'user', NULL, NULL),
(3, 'staffs', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID đơn hàng',
  `refund_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID yêu cầu hoàn tiền',
  `wallet_id` bigint UNSIGNED NOT NULL COMMENT 'ID ví nhận tiền',
  `amount` decimal(15,2) NOT NULL COMMENT 'Số tiền giao dịch',
  `type` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'income' COMMENT 'Loại giao dịch',
  `status` enum('pending','completed','failed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái',
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Phương thức thanh toán',
  `transaction_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giao dịch',
  `qr_code_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'URL QR code',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả giao dịch',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian hoàn thành',
  `processed_by` bigint UNSIGNED DEFAULT NULL COMMENT 'ID admin xử lý giao dịch',
  `marked_as_received_by` bigint UNSIGNED DEFAULT NULL,
  `marked_as_received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED NOT NULL COMMENT 'ID giao dịch',
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'ID người thực hiện',
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hành động: confirm, cancel, refund, update',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả hành động',
  `old_data` json DEFAULT NULL COMMENT 'Dữ liệu cũ',
  `new_data` json DEFAULT NULL COMMENT 'Dữ liệu mới',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `address`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Việt Hòa', 'doviethoa041105@gmail.com', NULL, '$2y$12$dd0Ehi2/tp/UiPJ1bG2PN.5EG.hU8QfyBfrs/.nCKT/V5A94GFrui', 'admin', NULL, 'active', '2025-10-18 07:39:20', '2025-11-10 19:51:26', NULL),
(3, 'Việt Hòa', 'admin@gmail.com', NULL, '$2y$12$Cre1NWCKmIVLvk1FzMwwee/uH4XF041BQZAAdxzPzJPSa2n9ar/Zu', 'admin', NULL, 'active', '2025-10-26 08:57:42', '2025-11-12 10:30:49', NULL),
(4, 'test', 'test@gmail.com', NULL, '$2y$12$yiSyc6VtRjvQDVCn/089ueruc.b9CtwWh6CXFVJzxveRN/XQbO.YS', 'user', NULL, 'active', '2025-10-28 20:06:35', '2025-10-28 20:06:35', NULL),
(8, 'iPhone 11 Pro Max', 'user@gmail.com', '1234', '$2y$12$sYxAidmm//sfgj8qbS8VVulW7FCyUQCiQI14unQ2vC6GOYRWhPK5e', 'user', '', 'active', '2025-11-12 10:30:07', '2025-11-12 10:30:07', NULL),
(9, 'Admin User', 'admin@example.com', NULL, '$2y$12$vCnn/EZb/kkLScY.7b9HmePyRgylnorYqh3yTsfkxTWnevGoHxxbS', 'admin', NULL, 'active', '2025-11-28 09:26:02', '2025-11-28 09:26:02', NULL),
(10, 'Admin Van Luan', 'vanluan2k2bg@gmail.com', NULL, '$2y$12$I4IQwi6g94Aff1yBovHQu.Kut/2TjA9MWBvL4Kzal3nJcFci8xkgW', 'admin', NULL, 'active', '2025-11-28 09:26:03', '2025-11-28 09:26:03', NULL),
(11, 'Nguyễn Văn A', 'user1@example.com', '0912345678', '$2y$12$uZ.WpFHE4v4nHEVp1lxisuqqz6XkQqLjDn3obFQpOemhhEnNJWm5C', 'user', '123 Đường ABC, Quận 1, TP.HCM', 'active', '2025-11-29 12:25:19', '2025-11-29 12:25:19', NULL),
(12, 'Trần Thị B', 'user2@example.com', '0923456789', '$2y$12$h7Hy5JfiY7CmJgDchrV3heV59AEltlhHN3GBgYJ4os6eoXmo6T5SS', 'user', '456 Đường XYZ, Quận 2, TP.HCM', 'active', '2025-11-29 12:25:19', '2025-11-29 12:25:19', NULL),
(13, 'Lê Văn C', 'user3@example.com', '0934567890', '$2y$12$h6N.s4B74ye9ORDn3dLske3qzcV5Iqxj59BWmUYK1TbqaBvkJWHzG', 'user', '789 Đường DEF, Quận 3, TP.HCM', 'active', '2025-11-29 12:25:19', '2025-11-29 12:25:19', NULL),
(14, 'Phạm Thị D', 'user4@example.com', '0945678901', '$2y$12$eFJpqmGtm66OvcBHYwirsO6AHqnr2zyJsNNWv4wF7KNy65siJhyjC', 'user', '321 Đường GHI, Quận 4, TP.HCM', 'active', '2025-11-29 12:25:19', '2025-11-29 12:25:19', NULL),
(15, 'Hoàng Văn E', 'user5@example.com', '0956789012', '$2y$12$6TD23nAlbHPQm7kz/mFbQ.GYotxUkwEUvF0qnq7AO7gQLrqf/rxKW', 'user', '654 Đường JKL, Quận 5, TP.HCM', 'active', '2025-11-29 12:25:19', '2025-11-29 12:25:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'ID của admin sở hữu ví',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư hiện tại',
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên ngân hàng',
  `bank_account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số tài khoản',
  `account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên chủ tài khoản',
  `qr_code_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Template QR code',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_withdrawals`
--

CREATE TABLE `wallet_withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `requested_by` bigint UNSIGNED NOT NULL,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blogs_slug_unique` (`slug`),
  ADD KEY `blogs_user_id_foreign` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

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
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_cart_id_foreign` (`cart_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collections_slug_unique` (`slug`);

--
-- Indexes for table `collection_product`
--
ALTER TABLE `collection_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collection_product_collection_id_product_id_unique` (`collection_id`,`product_id`),
  ADD KEY `collection_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_targets`
--
ALTER TABLE `monthly_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_targets_year_month_unique` (`year`,`month`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_promotion_id_foreign` (`promotion_id`),
  ADD KEY `orders_sales_channel_index` (`sales_channel`),
  ADD KEY `orders_branch_code_index` (`branch_code`),
  ADD KEY `orders_warehouse_code_index` (`warehouse_code`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_foreign` (`order_id`),
  ADD KEY `order_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_logs_order_id_foreign` (`order_id`),
  ADD KEY `order_logs_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_notes_order_id_index` (`order_id`),
  ADD KEY `order_notes_type_index` (`type`),
  ADD KEY `order_notes_is_pinned_index` (`is_pinned`);

--
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `order_payments_order_id_index` (`order_id`),
  ADD KEY `order_payments_transaction_id_index` (`transaction_id`),
  ADD KEY `order_payments_status_index` (`status`);

--
-- Indexes for table `order_refunds`
--
ALTER TABLE `order_refunds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_refunds_refund_code_unique` (`refund_code`),
  ADD KEY `order_refunds_order_payment_id_foreign` (`order_payment_id`),
  ADD KEY `order_refunds_order_id_index` (`order_id`),
  ADD KEY `order_refunds_refund_code_index` (`refund_code`),
  ADD KEY `order_refunds_status_index` (`status`);

--
-- Indexes for table `order_refund_items`
--
ALTER TABLE `order_refund_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_refund_items_refund_id_index` (`refund_id`),
  ADD KEY `order_refund_items_order_detail_id_index` (`order_detail_id`);

--
-- Indexes for table `order_returns`
--
ALTER TABLE `order_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_returns_return_code_unique` (`return_code`),
  ADD KEY `order_returns_exchange_product_id_foreign` (`exchange_product_id`),
  ADD KEY `order_returns_order_id_index` (`order_id`),
  ADD KEY `order_returns_return_code_index` (`return_code`),
  ADD KEY `order_returns_status_index` (`status`);

--
-- Indexes for table `order_return_items`
--
ALTER TABLE `order_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_return_items_return_id_index` (`return_id`),
  ADD KEY `order_return_items_order_detail_id_index` (`order_detail_id`);

--
-- Indexes for table `order_shipments`
--
ALTER TABLE `order_shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_shipments_shipment_code_unique` (`shipment_code`),
  ADD KEY `order_shipments_order_id_index` (`order_id`),
  ADD KEY `order_shipments_tracking_number_index` (`tracking_number`),
  ADD KEY `order_shipments_status_index` (`status`);

--
-- Indexes for table `order_timelines`
--
ALTER TABLE `order_timelines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_timelines_order_id_index` (`order_id`),
  ADD KEY `order_timelines_event_type_index` (`event_type`),
  ADD KEY `order_timelines_created_at_index` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_product_variant` (`product_id`,`color_code`,`length`,`width`,`height`),
  ADD KEY `product_variants_product_id_index` (`product_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promotions_code_unique` (`code`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refunds_processed_by_foreign` (`processed_by`),
  ADD KEY `refunds_order_id_index` (`order_id`),
  ADD KEY `refunds_user_id_index` (`user_id`),
  ADD KEY `refunds_status_index` (`status`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_product_id_rating_index` (`product_id`,`rating`),
  ADD KEY `reviews_order_id_foreign` (`order_id`),
  ADD KEY `reviews_product_id_status_index` (`product_id`,`status`),
  ADD KEY `reviews_user_id_product_id_index` (`user_id`,`product_id`);

--
-- Indexes for table `review_audit_logs`
--
ALTER TABLE `review_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_audit_logs_review_id_index` (`review_id`),
  ADD KEY `review_audit_logs_admin_id_index` (`admin_id`),
  ADD KEY `review_audit_logs_action_index` (`action`),
  ADD KEY `review_audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_helpful_review_id_user_id_unique` (`review_id`,`user_id`),
  ADD KEY `review_helpful_user_id_foreign` (`user_id`),
  ADD KEY `review_helpful_review_id_index` (`review_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_replies_admin_id_foreign` (`admin_id`),
  ADD KEY `review_replies_review_id_index` (`review_id`);

--
-- Indexes for table `review_reports`
--
ALTER TABLE `review_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_reports_user_id_foreign` (`user_id`),
  ADD KEY `review_reports_review_id_index` (`review_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_transaction_code_unique` (`transaction_code`),
  ADD KEY `transactions_order_id_index` (`order_id`),
  ADD KEY `transactions_wallet_id_index` (`wallet_id`),
  ADD KEY `transactions_status_index` (`status`),
  ADD KEY `transactions_transaction_code_index` (`transaction_code`),
  ADD KEY `transactions_refund_id_foreign` (`refund_id`),
  ADD KEY `transactions_processed_by_foreign` (`processed_by`),
  ADD KEY `transactions_marked_as_received_by_foreign` (`marked_as_received_by`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_logs_transaction_id_index` (`transaction_id`),
  ADD KEY `transaction_logs_user_id_index` (`user_id`),
  ADD KEY `transaction_logs_action_index` (`action`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_user_id_index` (`user_id`);

--
-- Indexes for table `wallet_withdrawals`
--
ALTER TABLE `wallet_withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_withdrawals_wallet_id_foreign` (`wallet_id`),
  ADD KEY `wallet_withdrawals_requested_by_foreign` (`requested_by`),
  ADD KEY `wallet_withdrawals_processed_by_foreign` (`processed_by`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection_product`
--
ALTER TABLE `collection_product`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `monthly_targets`
--
ALTER TABLE `monthly_targets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_notes`
--
ALTER TABLE `order_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_refunds`
--
ALTER TABLE `order_refunds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_refund_items`
--
ALTER TABLE `order_refund_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_returns`
--
ALTER TABLE `order_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_return_items`
--
ALTER TABLE `order_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_shipments`
--
ALTER TABLE `order_shipments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_timelines`
--
ALTER TABLE `order_timelines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `review_audit_logs`
--
ALTER TABLE `review_audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `review_helpful`
--
ALTER TABLE `review_helpful`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `review_reports`
--
ALTER TABLE `review_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_withdrawals`
--
ALTER TABLE `wallet_withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `collection_product`
--
ALTER TABLE `collection_product`
  ADD CONSTRAINT `collection_product_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `order_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_logs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD CONSTRAINT `order_notes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD CONSTRAINT `order_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_refunds`
--
ALTER TABLE `order_refunds`
  ADD CONSTRAINT `order_refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_refunds_order_payment_id_foreign` FOREIGN KEY (`order_payment_id`) REFERENCES `order_payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_refund_items`
--
ALTER TABLE `order_refund_items`
  ADD CONSTRAINT `order_refund_items_order_detail_id_foreign` FOREIGN KEY (`order_detail_id`) REFERENCES `order_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_refund_items_refund_id_foreign` FOREIGN KEY (`refund_id`) REFERENCES `order_refunds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_returns`
--
ALTER TABLE `order_returns`
  ADD CONSTRAINT `order_returns_exchange_product_id_foreign` FOREIGN KEY (`exchange_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_returns_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_return_items`
--
ALTER TABLE `order_return_items`
  ADD CONSTRAINT `order_return_items_order_detail_id_foreign` FOREIGN KEY (`order_detail_id`) REFERENCES `order_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_return_items_return_id_foreign` FOREIGN KEY (`return_id`) REFERENCES `order_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_shipments`
--
ALTER TABLE `order_shipments`
  ADD CONSTRAINT `order_shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_timelines`
--
ALTER TABLE `order_timelines`
  ADD CONSTRAINT `order_timelines_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `refunds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_audit_logs`
--
ALTER TABLE `review_audit_logs`
  ADD CONSTRAINT `review_audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `review_audit_logs_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD CONSTRAINT `review_helpful_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_helpful_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `review_replies_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_replies_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_reports`
--
ALTER TABLE `review_reports`
  ADD CONSTRAINT `review_reports_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_marked_as_received_by_foreign` FOREIGN KEY (`marked_as_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_refund_id_foreign` FOREIGN KEY (`refund_id`) REFERENCES `refunds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `transaction_logs_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_withdrawals`
--
ALTER TABLE `wallet_withdrawals`
  ADD CONSTRAINT `wallet_withdrawals_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wallet_withdrawals_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_withdrawals_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
