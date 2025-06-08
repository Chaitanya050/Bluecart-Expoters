-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 12:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bluecart`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_settings`
--

INSERT INTO `email_settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'smtp_host', 'smtp.gmail.com', '2025-06-08 02:14:14'),
(2, 'smtp_port', '587', '2025-06-08 02:14:14'),
(3, 'smtp_username', '', '2025-06-08 02:14:14'),
(4, 'smtp_password', '', '2025-06-08 02:14:14'),
(5, 'from_email', 'noreply@techhub.com', '2025-06-08 02:14:14'),
(6, 'from_name', 'TechHub Electronics', '2025-06-08 02:14:14'),
(7, 'admin_email', 'khatikanuj914@gmail.com', '2025-06-08 02:14:14'),
(8, 'notifications_enabled', '1', '2025-06-08 02:14:14'),
(9, 'send_order_notifications', '1', '2025-06-08 02:14:14'),
(10, 'send_status_updates', '1', '2025-06-08 02:14:14'),
(11, 'send_low_stock_alerts', '1', '2025-06-08 02:14:14');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `html_content` text NOT NULL,
  `text_content` text DEFAULT NULL,
  `variables` text DEFAULT NULL COMMENT 'JSON array of available variables',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `subject`, `html_content`, `text_content`, `variables`, `created_at`, `updated_at`) VALUES
(1, 'order_notification', 'New Order #{order_id} - Action Required', '', '', '[\"order_id\", \"customer_name\", \"customer_email\", \"total_amount\", \"items\", \"shipping_address\"]', '2025-06-08 02:14:14', '2025-06-08 02:14:14'),
(2, 'order_status_update', 'Order #{order_id} Status Update', '', '', '[\"order_id\", \"customer_name\", \"status\", \"total_amount\"]', '2025-06-08 02:14:14', '2025-06-08 02:14:14'),
(3, 'low_stock_alert', 'Low Stock Alert - {product_count} Products Need Attention', '', '', '[\"products\", \"product_count\"]', '2025-06-08 02:14:14', '2025-06-08 02:14:14');

-- --------------------------------------------------------

--
-- Table structure for table `low_stock_alerts`
--

CREATE TABLE `low_stock_alerts` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `threshold_level` int(11) NOT NULL,
  `alert_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_log`
--

CREATE TABLE `notification_log` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `type` enum('email_admin','email_customer','whatsapp_admin','whatsapp_customer','sms') NOT NULL,
  `status` enum('sent','failed','pending') NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_log`
--

INSERT INTO `notification_log` (`id`, `order_id`, `type`, `status`, `details`, `created_at`) VALUES
(1, 1, 'email_admin', 'sent', '{\"success\":true,\"message\":\"Email sent successfully\"}', '2025-06-08 02:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_number` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT 'COD',
  `order_notes` text DEFAULT NULL,
  `shipping_method_id` int(11) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `estimated_delivery` date DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `delivery_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `order_number`, `customer_name`, `customer_phone`, `shipping_address`, `payment_method`, `order_notes`, `shipping_method_id`, `shipping_cost`, `estimated_delivery`, `tracking_number`, `delivery_instructions`) VALUES
(1, 3, 24900.00, 'pending', '2025-06-08 02:17:36', NULL, NULL, NULL, NULL, 'COD', NULL, NULL, 0.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `item_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `item_total`) VALUES
(1, 1, 7, NULL, 24900.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 5,
  `weight` decimal(8,3) DEFAULT 0.500,
  `dimensions` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage_capacity` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `processor` varchar(100) DEFAULT NULL,
  `display_size` varchar(20) DEFAULT NULL,
  `battery_capacity` varchar(20) DEFAULT NULL,
  `operating_system` varchar(50) DEFAULT NULL,
  `connectivity` text DEFAULT NULL,
  `warranty_period` int(11) DEFAULT 12,
  `warranty_type` enum('manufacturer','seller','extended') DEFAULT 'manufacturer',
  `condition_type` enum('new','refurbished','open_box') DEFAULT 'new',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_bestseller` tinyint(1) DEFAULT 0,
  `is_new_arrival` tinyint(1) DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `image_primary` varchar(255) DEFAULT '/placeholder.svg?height=400&width=400',
  `image_gallery` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `features` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `included_items` text DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_stock_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `model`, `description`, `short_description`, `category`, `subcategory`, `sku`, `barcode`, `price`, `original_price`, `discount_percentage`, `stock_quantity`, `low_stock_threshold`, `weight`, `dimensions`, `color`, `storage_capacity`, `ram`, `processor`, `display_size`, `battery_capacity`, `operating_system`, `connectivity`, `warranty_period`, `warranty_type`, `condition_type`, `is_featured`, `is_bestseller`, `is_new_arrival`, `is_on_sale`, `is_active`, `image_primary`, `image_gallery`, `video_url`, `rating`, `review_count`, `features`, `specifications`, `included_items`, `tags`, `meta_title`, `meta_description`, `created_at`, `updated_at`, `last_stock_update`) VALUES
(1, 'iPhone 15 Pro Max', 'Apple', 'A3108', 'The most advanced iPhone ever with titanium design, A17 Pro chip, and revolutionary camera system. Features the most powerful iPhone camera system with 5x telephoto zoom and Action Button for quick access to your favorite features.', 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system', 'Smartphones', 'Premium Smartphones', 'APL-IP15PM-256-NT', '194253777777', 134900.00, 139900.00, 3.58, 15, 10, 0.221, '15.9 x 7.7 x 0.8', 'Natural Titanium', '256GB', '8GB', 'A17 Pro', '6.7\"', '4441mAh', 'iOS 17', '[\"5G\", \"WiFi 6E\", \"Bluetooth 5.3\", \"NFC\", \"Lightning\"]', 12, 'manufacturer', 'new', 1, 1, 1, 1, 1, '/placeholder.svg?height=400&width=400', NULL, NULL, 4.80, 1247, '[\"Titanium Design\", \"A17 Pro Chip\", \"5x Telephoto Camera\", \"Action Button\", \"USB-C\", \"Dynamic Island\"]', '{\"display\": \"6.7-inch Super Retina XDR OLED\", \"camera\": \"48MP Main + 12MP Ultra Wide + 12MP Telephoto\", \"video\": \"4K ProRes\", \"water_resistance\": \"IP68\", \"face_id\": \"Yes\"}', 'iPhone 15 Pro Max, USB-C to USB-C Cable, Documentation', 'iPhone, Apple, smartphone, 5G, camera, titanium, A17 Pro', 'iPhone 15 Pro Max - Buy Latest Apple Smartphone Online', 'Get the iPhone 15 Pro Max with titanium design, A17 Pro chip, and advanced camera system. Free shipping and warranty included.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(2, 'Samsung Galaxy S24 Ultra', 'Samsung', 'SM-S928B', 'The ultimate Galaxy experience with built-in S Pen, 200MP camera with AI zoom, and Galaxy AI features. Titanium frame with Gorilla Glass Armor for enhanced durability.', 'Flagship Galaxy with S Pen, 200MP camera, and Galaxy AI features', 'Smartphones', 'Premium Smartphones', 'SAM-GS24U-512-TG', '194253888888', 124999.00, 129999.00, 3.85, 12, 10, 0.232, '16.2 x 7.9 x 0.9', 'Titanium Gray', '512GB', '12GB', 'Snapdragon 8 Gen 3', '6.8\"', '5000mAh', 'Android 14', '[\"5G\", \"WiFi 7\", \"Bluetooth 5.3\", \"NFC\", \"USB-C\"]', 12, 'manufacturer', 'new', 1, 1, 0, 1, 1, '/placeholder.svg?height=400&width=400', NULL, NULL, 4.70, 892, '[\"Built-in S Pen\", \"200MP Camera\", \"Galaxy AI\", \"Titanium Frame\", \"100x Space Zoom\", \"Gorilla Glass Armor\"]', '{\"display\": \"6.8-inch Dynamic AMOLED 2X\", \"camera\": \"200MP Main + 50MP Periscope + 12MP Ultra Wide + 10MP Telephoto\", \"s_pen\": \"Built-in\", \"water_resistance\": \"IP68\"}', 'Galaxy S24 Ultra, S Pen, USB-C Cable, SIM Tool, Quick Start Guide', 'Samsung, Galaxy, S24 Ultra, S Pen, Android, 200MP camera', 'Samsung Galaxy S24 Ultra with S Pen - Buy Online', 'Experience the Samsung Galaxy S24 Ultra with built-in S Pen, 200MP camera, and Galaxy AI. Premium titanium design.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(3, 'Google Pixel 8 Pro', 'Google', 'GC3VE', 'The most helpful Pixel yet with Google Tensor G3, Magic Eraser, and Best Take features. Pro-level camera with computational photography.', 'Google flagship with Tensor G3 chip and advanced AI photography', 'Smartphones', 'Premium Smartphones', 'GOO-PX8P-256-OB', '194253999999', 84999.00, 89999.00, 5.56, 8, 10, 0.213, '16.3 x 7.7 x 0.9', 'Obsidian', '256GB', '12GB', 'Google Tensor G3', '6.7\"', '5050mAh', 'Android 14', '[\"5G\", \"WiFi 6E\", \"Bluetooth 5.3\", \"NFC\", \"USB-C\"]', 12, 'manufacturer', 'new', 1, 0, 1, 1, 1, '/placeholder.svg?height=400&width=400', NULL, NULL, 4.60, 634, '[\"Google Tensor G3\", \"Magic Eraser\", \"Best Take\", \"Night Sight\", \"Titan M Security\", \"7 Years Updates\"]', '{\"display\": \"6.7-inch LTPO OLED\", \"camera\": \"50MP Main + 48MP Ultra Wide + 48MP Telephoto\", \"ai_features\": \"Magic Eraser, Best Take, Audio Magic Eraser\", \"updates\": \"7 years\"}', 'Pixel 8 Pro, USB-C Cable, Quick Switch Adapter, SIM Tool, Quick Start Guide', 'Google, Pixel, Android, camera, AI, Tensor G3', 'Google Pixel 8 Pro - Advanced AI Photography', 'Get the Google Pixel 8 Pro with Tensor G3 chip, Magic Eraser, and 7 years of updates. Pure Android experience.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(4, 'MacBook Pro 16-inch M3 Max', 'Apple', 'MRW13', 'Supercharged for pros with M3 Max chip, up to 22 hours battery life, and stunning Liquid Retina XDR display. Perfect for video editing, 3D rendering, and professional workflows.', 'Professional laptop with M3 Max chip and Liquid Retina XDR display', 'Laptops', 'Professional Laptops', 'APL-MBP16-M3MAX-1TB', '194253111111', 399900.00, 419900.00, 4.76, 5, 10, 2.140, '35.6 x 24.8 x 1.7', 'Space Black', '1TB SSD', '36GB', 'Apple M3 Max', '16.2\"', '100Wh', 'macOS Sonoma', '[\"WiFi 6E\", \"Bluetooth 5.3\", \"Thunderbolt 4\", \"HDMI\", \"SDXC\"]', 12, 'manufacturer', 'new', 1, 1, 0, 1, 1, '/placeholder.svg?height=300&width=400', NULL, NULL, 4.90, 423, '[\"M3 Max Chip\", \"Liquid Retina XDR\", \"22hr Battery\", \"6 Thunderbolt 4\", \"1080p Camera\", \"Six-speaker System\"]', '{\"display\": \"16.2-inch Liquid Retina XDR\", \"gpu\": \"40-core GPU\", \"memory\": \"36GB Unified Memory\", \"ports\": \"3x Thunderbolt 4, HDMI, SDXC, MagSafe 3\", \"keyboard\": \"Magic Keyboard with Touch ID\"}', 'MacBook Pro, 140W USB-C Power Adapter, USB-C to MagSafe 3 Cable, Documentation', 'MacBook Pro, Apple, M3 Max, laptop, professional, video editing', 'MacBook Pro 16-inch M3 Max - Professional Laptop', 'Experience the MacBook Pro 16-inch with M3 Max chip, perfect for professionals. Liquid Retina XDR display and 22-hour battery.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(5, 'Dell XPS 15 9530', 'Dell', '9530', 'Premium Windows laptop with 13th Gen Intel Core i7, NVIDIA RTX 4070, and stunning 15.6-inch OLED display. Perfect for creators and professionals.', 'Premium Windows laptop with Intel i7 and NVIDIA RTX 4070', 'Laptops', 'Professional Laptops', 'DEL-XPS15-I7-1TB', '194253222222', 249999.00, 269999.00, 7.41, 6, 10, 1.960, '34.4 x 23.0 x 1.8', 'Platinum Silver', '1TB SSD', '32GB', 'Intel Core i7-13700H', '15.6\"', '86Wh', 'Windows 11 Pro', '[\"WiFi 6E\", \"Bluetooth 5.2\", \"Thunderbolt 4\", \"USB-C\", \"SD Card\"]', 12, 'manufacturer', 'new', 1, 0, 0, 1, 1, '/placeholder.svg?height=300&width=400', NULL, NULL, 4.50, 287, '[\"13th Gen Intel i7\", \"NVIDIA RTX 4070\", \"OLED Display\", \"Thunderbolt 4\", \"Premium Build\", \"Windows Hello\"]', '{\"display\": \"15.6-inch 3.5K OLED Touch\", \"gpu\": \"NVIDIA GeForce RTX 4070 8GB\", \"memory\": \"32GB LPDDR5\", \"storage\": \"1TB PCIe SSD\", \"keyboard\": \"Backlit with fingerprint reader\"}', 'XPS 15 Laptop, 130W Power Adapter, Documentation', 'Dell, XPS, laptop, Intel i7, NVIDIA RTX, OLED, Windows', 'Dell XPS 15 9530 - Premium Windows Laptop', 'Get the Dell XPS 15 with 13th Gen Intel i7, NVIDIA RTX 4070, and stunning OLED display. Perfect for professionals.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(6, 'iPad Pro 12.9-inch M2', 'Apple', 'MNXH3', 'The ultimate iPad experience with M2 chip, Liquid Retina XDR display, and support for Apple Pencil 2nd generation. Perfect for creative professionals.', 'Professional tablet with M2 chip and Liquid Retina XDR display', 'Tablets', 'Professional Tablets', 'APL-IPADPRO-M2-512', '194253333333', 109900.00, 114900.00, 4.35, 10, 10, 0.682, '28.1 x 21.5 x 0.6', 'Space Gray', '512GB', '8GB', 'Apple M2', '12.9\"', '10758mAh', 'iPadOS 17', '[\"WiFi 6E\", \"Bluetooth 5.3\", \"USB-C\", \"5G Optional\"]', 12, 'manufacturer', 'new', 1, 1, 1, 1, 1, '/placeholder.svg?height=350&width=400', NULL, NULL, 4.70, 512, '[\"M2 Chip\", \"Liquid Retina XDR\", \"Apple Pencil Support\", \"Magic Keyboard Compatible\", \"12MP Cameras\", \"Face ID\"]', '{\"display\": \"12.9-inch Liquid Retina XDR\", \"camera\": \"12MP Wide + 10MP Ultra Wide\", \"front_camera\": \"12MP TrueDepth\", \"apple_pencil\": \"2nd generation support\", \"keyboard\": \"Magic Keyboard compatible\"}', 'iPad Pro, USB-C Cable, 20W USB-C Power Adapter, Documentation', 'iPad Pro, Apple, tablet, M2 chip, Apple Pencil, creative', 'iPad Pro 12.9-inch M2 - Professional Tablet', 'Experience the iPad Pro with M2 chip, Liquid Retina XDR display, and Apple Pencil support. Perfect for professionals.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(7, 'AirPods Pro 2nd Generation', 'Apple', 'MTJV3', 'Personalized Spatial Audio with dynamic head tracking, Active Noise Cancellation, and up to 6 hours of listening time with ANC enabled.', 'Premium wireless earbuds with Active Noise Cancellation', 'Audio', 'Wireless Earbuds', 'APL-APP2-USB-C', '194253444444', 24900.00, 26900.00, 7.43, 24, 10, 0.050, '3.0 x 2.2 x 2.4', 'White', 'N/A', 'N/A', 'Apple H2', 'N/A', '6hrs + 24hrs case', 'iOS/Android Compatible', '[\"Bluetooth 5.3\", \"Lightning/USB-C\", \"Wireless Charging\"]', 12, 'manufacturer', 'new', 1, 1, 0, 1, 1, '/placeholder.svg?height=300&width=300', NULL, NULL, 4.80, 1834, '[\"Active Noise Cancellation\", \"Spatial Audio\", \"H2 Chip\", \"Touch Control\", \"Find My\", \"Wireless Charging\"]', '{\"anc\": \"Active Noise Cancellation\", \"battery\": \"6hrs + 24hrs with case\", \"water_resistance\": \"IPX4\", \"chip\": \"Apple H2\", \"spatial_audio\": \"Personalized with head tracking\"}', 'AirPods Pro, MagSafe Charging Case, Silicone Ear Tips (4 sizes), USB-C Cable, Documentation', 'AirPods Pro, Apple, wireless earbuds, noise cancellation, spatial audio', 'AirPods Pro 2nd Generation - Premium Wireless Earbuds', 'Get AirPods Pro with Active Noise Cancellation, Spatial Audio, and H2 chip. Premium wireless audio experience.', '2025-06-08 02:11:27', '2025-06-08 02:17:36', '2025-06-08 02:17:36'),
(8, 'Sony WH-1000XM5', 'Sony', 'WH1000XM5', 'Industry-leading noise canceling with Auto NC Optimizer, exceptional sound quality with 30mm drivers, and up to 30 hours battery life.', 'Premium noise-canceling headphones with 30-hour battery', 'Audio', 'Over-Ear Headphones', 'SON-WH1000XM5-B', '194253555555', 29990.00, 32990.00, 9.09, 18, 10, 0.250, '27.0 x 19.0 x 8.0', 'Black', 'N/A', 'N/A', 'V1 Processor', 'N/A', '30hrs ANC On', 'Universal', '[\"Bluetooth 5.2\", \"NFC\", \"USB-C\", \"3.5mm Jack\"]', 12, 'manufacturer', 'new', 1, 1, 0, 1, 1, '/placeholder.svg?height=300&width=300', NULL, NULL, 4.60, 923, '[\"Industry-leading ANC\", \"30hr Battery\", \"Quick Charge\", \"Multipoint Connection\", \"Speak-to-Chat\", \"Touch Controls\"]', '{\"anc\": \"Industry-leading with V1 processor\", \"battery\": \"30hrs with ANC\", \"drivers\": \"30mm\", \"quick_charge\": \"3min = 3hrs\", \"multipoint\": \"Connect 2 devices\"}', 'WH-1000XM5 Headphones, Carrying Case, USB-C Cable, Audio Cable, Documentation', 'Sony, headphones, noise canceling, wireless, premium audio', 'Sony WH-1000XM5 - Premium Noise Canceling Headphones', 'Experience Sony WH-1000XM5 with industry-leading noise canceling and 30-hour battery. Premium audio quality.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(9, 'Apple Watch Series 9 GPS', 'Apple', 'MR933', 'Your essential companion with S9 SiP, Double Tap gesture, and advanced health features. Track workouts, monitor health, and stay connected.', 'Advanced smartwatch with S9 chip and health monitoring', 'Wearables', 'Smartwatches', 'APL-AWS9-45-MN', '194253666666', 42900.00, 45900.00, 6.54, 20, 10, 0.039, '4.5 x 3.8 x 1.1', 'Midnight', 'N/A', '1GB', 'Apple S9 SiP', '1.9\"', '18hrs', 'watchOS 10', '[\"Bluetooth 5.3\", \"WiFi\", \"NFC\", \"GPS\"]', 12, 'manufacturer', 'new', 1, 1, 1, 1, 1, '/placeholder.svg?height=300&width=300', NULL, NULL, 4.70, 756, '[\"S9 SiP Chip\", \"Double Tap\", \"Always-On Display\", \"ECG\", \"Blood Oxygen\", \"Crash Detection\"]', '{\"display\": \"Always-On Retina LTPO OLED\", \"health\": \"ECG, Blood Oxygen, Heart Rate\", \"fitness\": \"Workout Detection\", \"safety\": \"Fall Detection, Crash Detection\", \"siri\": \"On-device processing\"}', 'Apple Watch Series 9, Sport Band, Magnetic Charging Cable, Documentation', 'Apple Watch, smartwatch, fitness, health monitoring, S9 chip', 'Apple Watch Series 9 - Advanced Health & Fitness', 'Get Apple Watch Series 9 with S9 chip, Double Tap gesture, and advanced health features. Your essential companion.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(10, 'PlayStation 5 Console', 'Sony', 'CFI-1200A', 'Experience lightning-fast loading with an ultra-high speed SSD, deeper immersion with haptic feedback, and stunning visuals in 4K.', 'Next-gen gaming console with ultra-high speed SSD', 'Gaming', 'Gaming Consoles', 'SON-PS5-825GB-W', '194253777777', 54990.00, 59990.00, 8.33, 8, 10, 4.500, '39.0 x 26.0 x 10.4', 'White', '825GB SSD', '16GB GDDR6', 'AMD Zen 2', 'N/A', 'N/A', 'PlayStation OS', '[\"WiFi 6\", \"Bluetooth 5.1\", \"Ethernet\", \"USB\", \"HDMI 2.1\"]', 12, 'manufacturer', 'new', 1, 1, 0, 1, 1, '/placeholder.svg?height=300&width=400', NULL, NULL, 4.80, 1245, '[\"Ultra-High Speed SSD\", \"Ray Tracing\", \"4K Gaming\", \"Haptic Feedback\", \"3D Audio\", \"Backward Compatible\"]', '{\"cpu\": \"AMD Zen 2 8-core\", \"gpu\": \"AMD RDNA 2\", \"memory\": \"16GB GDDR6\", \"storage\": \"825GB SSD\", \"optical\": \"4K UHD Blu-ray\", \"resolution\": \"Up to 4K 120fps\"}', 'PlayStation 5 Console, DualSense Controller, HDMI Cable, USB Cable, Power Cord, Documentation', 'PlayStation 5, PS5, gaming console, Sony, 4K gaming', 'PlayStation 5 Console - Next-Gen Gaming Experience', 'Experience next-gen gaming with PlayStation 5. Ultra-high speed SSD, ray tracing, and 4K gaming capabilities.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(11, 'LG UltraGear 27GP950-B', 'LG', '27GP950-B', '27-inch 4K Nano IPS gaming monitor with 144Hz refresh rate, 1ms response time, and NVIDIA G-SYNC compatibility for smooth gaming.', '27-inch 4K gaming monitor with 144Hz and G-SYNC', 'Monitors', 'Gaming Monitors', 'LG-27GP950-4K-144', '194253888888', 64999.00, 69999.00, 7.14, 7, 10, 6.100, '61.3 x 36.6 x 5.6', 'Black', 'N/A', 'N/A', 'N/A', '27\"', 'N/A', 'Universal', '[\"HDMI 2.1\", \"DisplayPort 1.4\", \"USB Hub\", \"USB-C\"]', 24, 'manufacturer', 'new', 1, 0, 0, 1, 1, '/placeholder.svg?height=250&width=400', NULL, NULL, 4.50, 342, '[\"4K UHD Resolution\", \"144Hz Refresh\", \"1ms Response\", \"Nano IPS\", \"G-SYNC Compatible\", \"HDR600\"]', '{\"resolution\": \"3840x2160 4K UHD\", \"refresh_rate\": \"144Hz\", \"response_time\": \"1ms GtG\", \"panel\": \"Nano IPS\", \"hdr\": \"VESA DisplayHDR 600\", \"gsync\": \"Compatible\"}', 'LG UltraGear Monitor, Power Cable, HDMI Cable, DisplayPort Cable, USB Cable, Stand, Documentation', 'LG, monitor, gaming, 4K, 144Hz, G-SYNC, UltraGear', 'LG UltraGear 27GP950-B - 4K Gaming Monitor', 'Experience 4K gaming with LG UltraGear 27-inch monitor. 144Hz refresh rate, 1ms response time, and G-SYNC compatibility.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51'),
(12, 'Samsung T7 Portable SSD 2TB', 'Samsung', 'MU-PC2T0T', 'Portable SSD with read speeds up to 1,050 MB/s, USB 3.2 Gen 2 interface, and compact metal design. Perfect for content creators.', 'High-speed portable SSD with USB 3.2 Gen 2', 'Storage', 'External Storage', 'SAM-T7-2TB-GRAY', '194253999999', 18999.00, 21999.00, 13.64, 15, 10, 0.058, '8.5 x 5.7 x 0.8', 'Titan Gray', '2TB', 'N/A', 'N/A', 'N/A', 'N/A', 'Universal', '[\"USB 3.2 Gen 2\", \"USB-C\", \"USB-A\"]', 36, 'manufacturer', 'new', 0, 1, 0, 1, 1, '/placeholder.svg?height=200&width=300', NULL, NULL, 4.60, 567, '[\"1,050 MB/s Speed\", \"USB 3.2 Gen 2\", \"Compact Design\", \"Password Protection\", \"AES 256-bit Encryption\", \"Shock Resistant\"]', '{\"speed\": \"Up to 1,050 MB/s read\", \"interface\": \"USB 3.2 Gen 2\", \"compatibility\": \"Windows, Mac, Android\", \"security\": \"Password protection + AES 256-bit encryption\", \"durability\": \"Drop resistant up to 2m\"}', 'Samsung T7 Portable SSD, USB-C to USB-C Cable, USB-C to USB-A Cable, Quick Start Guide', 'Samsung, SSD, portable storage, USB 3.2, external drive', 'Samsung T7 Portable SSD 2TB - High-Speed Storage', 'Get Samsung T7 Portable SSD with speeds up to 1,050 MB/s. Compact, secure, and perfect for content creators.', '2025-06-08 02:11:27', '2025-06-08 02:13:51', '2025-06-08 02:13:51');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `attribute_value` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int(11) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_cost` decimal(10,2) NOT NULL,
  `per_kg_cost` decimal(10,2) NOT NULL,
  `delivery_days_min` int(11) DEFAULT 1,
  `delivery_days_max` int(11) DEFAULT 7,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `method_name`, `description`, `base_cost`, `per_kg_cost`, `delivery_days_min`, `delivery_days_max`, `is_active`, `created_at`) VALUES
(1, 'Standard Delivery', 'Regular delivery within 3-7 business days', 0.00, 0.00, 3, 7, 1, '2025-06-08 02:12:55'),
(2, 'Express Delivery', 'Fast delivery within 1-3 business days', 100.00, 20.00, 1, 3, 1, '2025-06-08 02:12:55'),
(3, 'Same Day Delivery', 'Delivery within same day (Metro cities only)', 200.00, 50.00, 0, 1, 1, '2025-06-08 02:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zones`
--

CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `states` text NOT NULL,
  `base_rate` decimal(10,2) NOT NULL,
  `per_kg_rate` decimal(10,2) NOT NULL,
  `free_shipping_threshold` decimal(10,2) DEFAULT 500.00,
  `delivery_days_min` int(11) DEFAULT 1,
  `delivery_days_max` int(11) DEFAULT 3,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_zones`
--

INSERT INTO `shipping_zones` (`id`, `zone_name`, `states`, `base_rate`, `per_kg_rate`, `free_shipping_threshold`, `delivery_days_min`, `delivery_days_max`, `is_active`, `created_at`) VALUES
(1, 'Metro Cities', 'Delhi,Mumbai,Bangalore,Chennai,Kolkata,Hyderabad,Pune', 0.00, 0.00, 500.00, 1, 2, 1, '2025-06-08 02:12:55'),
(2, 'Tier 1 Cities', 'Ahmedabad,Surat,Jaipur,Lucknow,Kanpur,Nagpur,Indore,Bhopal,Visakhapatnam,Patna', 50.00, 10.00, 750.00, 2, 3, 1, '2025-06-08 02:12:55'),
(3, 'Tier 2 Cities', 'Agra,Nashik,Faridabad,Meerut,Rajkot,Kalyan,Vasai,Varanasi,Srinagar,Aurangabad', 75.00, 15.00, 1000.00, 3, 5, 1, '2025-06-08 02:12:55'),
(4, 'Rural Areas', 'Other States and Remote Areas', 100.00, 20.00, 1500.00, 5, 7, 1, '2025-06-08 02:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `movement_type` enum('in','out','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `movement_type`, `quantity`, `previous_stock`, `new_stock`, `reason`, `admin_id`, `created_at`) VALUES
(1, 4, 'in', 5, 0, 5, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(2, 5, 'in', 6, 0, 6, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(3, 11, 'in', 7, 0, 7, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(4, 3, 'in', 8, 0, 8, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(5, 10, 'in', 8, 0, 8, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(6, 6, 'in', 10, 0, 10, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(7, 2, 'in', 12, 0, 12, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(8, 1, 'in', 15, 0, 15, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(9, 12, 'in', 15, 0, 15, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(10, 8, 'in', 18, 0, 18, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(11, 9, 'in', 20, 0, 20, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(12, 7, 'in', 25, 0, 25, 'Initial stock', NULL, '2025-06-08 02:13:51'),
(16, 7, 'out', 1, 25, 24, 'Order #1', NULL, '2025-06-08 02:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expires` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `phone`, `address`, `created_at`, `updated_at`, `last_login`, `is_active`, `reset_token`, `reset_token_expires`) VALUES
(1, 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NULL, NULL, '2025-06-08 02:11:27', '2025-06-08 02:11:27', NULL, 1, NULL, NULL),
(2, 'Anuj Khatik', 'khatikanuj914@gmail.com', '$2y$10$CtScGIUGSL8Tuh8WnvVe4ew7pe8nNctJ0idGl1//6FQ8fCFc/pnta', 'admin', NULL, NULL, '2025-06-08 02:13:37', '2025-06-08 03:33:28', NULL, 1, NULL, NULL),
(3, 'khatik anuj', 'khatikanuj@gmail.com', '$2y$10$fhlVa57hMrHl3EuY7A75cuK6u/ZIsHVBNdfQ6wnxn/3FmoOrRI3JK', 'user', NULL, NULL, '2025-06-08 02:16:42', '2025-06-08 02:16:42', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json','image') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `description`, `updated_at`) VALUES
(1, 'site_name', 'TechHub Electronics', 'text', 'general', 'Website name', '2025-06-08 02:12:55'),
(2, 'site_tagline', 'Your trusted destination for premium electronics', 'text', 'general', 'Website tagline', '2025-06-08 02:12:55'),
(3, 'site_description', 'Premium electronics store offering smartphones, laptops, accessories and more with authentic products and expert support.', 'text', 'seo', 'Site meta description', '2025-06-08 02:12:55'),
(4, 'contact_email', 'info@techhubelectronics.com', 'text', 'contact', 'Primary contact email', '2025-06-08 02:12:55'),
(5, 'contact_phone', '+91 9876543210', 'text', 'contact', 'Primary contact phone', '2025-06-08 02:12:55'),
(6, 'contact_address', '123 Tech Street, Electronics Hub, Mumbai, Maharashtra 400001', 'text', 'contact', 'Business address', '2025-06-08 02:12:55'),
(7, 'business_hours', 'Monday - Saturday: 9:00 AM - 8:00 PM, Sunday: 10:00 AM - 6:00 PM', 'text', 'contact', 'Business operating hours', '2025-06-08 02:12:55'),
(8, 'free_shipping_threshold', '500.00', 'number', 'shipping', 'Minimum order value for free shipping', '2025-06-08 02:12:55'),
(9, 'cod_charges', '0.00', 'number', 'shipping', 'Cash on delivery charges', '2025-06-08 02:12:55'),
(10, 'tax_rate', '18.00', 'number', 'pricing', 'GST/Tax rate percentage', '2025-06-08 02:12:55'),
(11, 'currency_symbol', '₹', 'text', 'pricing', 'Currency symbol', '2025-06-08 02:12:55'),
(12, 'social_facebook', 'https://facebook.com/techhubelectronics', 'text', 'social', 'Facebook page URL', '2025-06-08 02:12:55'),
(13, 'social_twitter', 'https://twitter.com/techhubelectronics', 'text', 'social', 'Twitter profile URL', '2025-06-08 02:12:55'),
(14, 'social_instagram', 'https://instagram.com/techhubelectronics', 'text', 'social', 'Instagram profile URL', '2025-06-08 02:12:55'),
(15, 'social_youtube', 'https://youtube.com/techhubelectronics', 'text', 'social', 'YouTube channel URL', '2025-06-08 02:12:55'),
(16, 'maintenance_mode', 'false', 'boolean', 'system', 'Enable maintenance mode', '2025-06-08 02:12:55'),
(17, 'allow_registration', 'true', 'boolean', 'system', 'Allow new user registration', '2025-06-08 02:12:55'),
(18, 'order_prefix', 'TH', 'text', 'system', 'Order number prefix', '2025-06-08 02:12:55'),
(19, 'low_stock_threshold', '5', 'number', 'inventory', 'Default low stock threshold', '2025-06-08 02:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_settings`
--

CREATE TABLE `whatsapp_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `whatsapp_settings`
--

INSERT INTO `whatsapp_settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'admin_phone', '+919876543210', '2025-06-08 02:14:02'),
(2, 'api_provider', 'twilio', '2025-06-08 02:14:02'),
(3, 'twilio_account_sid', 'YOUR_ACCOUNT_SID', '2025-06-08 02:14:02'),
(4, 'twilio_auth_token', 'YOUR_AUTH_TOKEN', '2025-06-08 02:14:02'),
(5, 'whatsapp_business_token', 'YOUR_WHATSAPP_BUSINESS_TOKEN', '2025-06-08 02:14:02'),
(6, 'notifications_enabled', '1', '2025-06-08 02:14:02'),
(7, 'send_order_notifications', '1', '2025-06-08 02:14:02'),
(8, 'send_status_updates', '1', '2025-06-08 02:14:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_name` (`template_name`);

--
-- Indexes for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `notification_log`
--
ALTER TABLE `notification_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_orders_user_id` (`user_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `fk_shipping_method` (`shipping_method_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order_items_order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_brand` (`brand`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_stock` (`stock_quantity`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_brand_category` (`brand`,`category`),
  ADD KEY `idx_price_range` (`price`,`is_active`),
  ADD KEY `idx_stock_status` (`stock_quantity`,`low_stock_threshold`),
  ADD KEY `idx_featured_products` (`is_featured`,`is_active`,`rating`);
ALTER TABLE `products` ADD FULLTEXT KEY `name` (`name`,`description`,`tags`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `whatsapp_settings`
--
ALTER TABLE `whatsapp_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_log`
--
ALTER TABLE `notification_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `whatsapp_settings`
--
ALTER TABLE `whatsapp_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD CONSTRAINT `low_stock_alerts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_log`
--
ALTER TABLE `notification_log`
  ADD CONSTRAINT `notification_log_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_shipping_method` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
