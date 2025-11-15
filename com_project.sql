-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 10:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `com_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'Admin', '123456'),
(2, 'Htet', '1111'),
(3, 'Htet', '1111'),
(4, 'Htet Naing', '$2y$10$x4XDHAVoIWeXZSNKa8wlDeH2JNZHAzeTopZd6///vNgF.aXpk83iG'),
(5, 'Htet Naing', '$2y$10$bmGpacBnH0msrZlrObHnpu3wiC2u6G.scOEElCoqlxP978KEPb4uG'),
(6, 'Naing', '54321'),
(7, 'Naing', '54321');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 2, 'Aye Aye', 'ayeaye@gmail.com', 'mmadsk', 'mmekmfwek', '2025-10-06 09:26:55'),
(2, 4, 'Sai Sai', 'saisai@gmail.com', 'About Guitar', 'Not suitable for beginners.', '2025-10-17 20:30:10'),
(3, 7, 'naingnaing', 'naingnaing@gmail.com', 'What', 'Wjat', '2025-10-18 04:10:39'),
(4, 9, 'Wang', 'wang@gmail.com', 'Nice', 'Nice', '2025-10-28 04:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_status` enum('processing','shipped','in_transit','delivered') DEFAULT 'processing',
  `delivery_date` datetime DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `carrier` varchar(50) DEFAULT NULL,
  `delivery_confirmation` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `level_id` int(11) NOT NULL,
  `level_name` varchar(50) NOT NULL,
  `point_requirement` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer`
--

CREATE TABLE `manufacturer` (
  `mid` int(11) NOT NULL,
  `mname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturer`
--

INSERT INTO `manufacturer` (`mid`, `mname`) VALUES
(2, 'Yamaha'),
(3, 'Fender'),
(4, 'Global'),
(5, 'Red Hill'),
(6, 'Alesis Nitro ');

-- --------------------------------------------------------

--
-- Table structure for table `order_`
--

CREATE TABLE `order_` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `promo_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_`
--

INSERT INTO `order_` (`order_id`, `customer_id`, `order_status`, `order_date`, `total_amount`, `shipping_address`, `promo_code`, `promo_amount`) VALUES
(1, 2, 'delivered', '2025-10-06 14:38:37', 236000.00, 'heihfqef', NULL, NULL),
(2, 2, 'delivered', '2025-10-06 15:56:18', 504000.00, 'lnjinn', NULL, NULL),
(3, 4, 'delivered', '2025-10-17 10:29:19', 236000.00, 'hgfhg', NULL, NULL),
(4, 4, 'delivered', '2025-10-17 10:32:17', 25600.00, 'hklh', NULL, NULL),
(5, 4, 'delivered', '2025-10-17 14:27:38', 236000.00, 'Mdy', NULL, NULL),
(6, 5, 'delivered', '2025-10-18 04:06:38', 3104000.00, '65 street, Mandalay', NULL, NULL),
(7, 5, 'delivered', '2025-10-18 04:17:00', 1513000.00, '2 Street , Mandalay, Pyin Oo Lwin', NULL, NULL),
(8, 5, 'pending', '2025-10-18 08:28:01', 1513000.00, 'MDY', NULL, NULL),
(9, 5, 'pending', '2025-10-18 08:32:43', 850000.00, 'MDY', 'ThiDaKyut', 150000.00),
(10, 5, 'delivered', '2025-10-18 10:30:19', 800000.00, 'MDY', 'Bb', 200000.00),
(11, 7, 'cancelled', '2025-10-18 10:40:11', 3570000.00, 'MDY', 'ThiDaKyut', 630000.00),
(12, 5, 'pending', '2025-10-18 11:59:35', 3000000.00, 'ok', '', 0.00),
(13, 9, 'pending', '2025-10-28 09:35:41', 5355000.00, 'Mdy', 'ThiDaKyut', 945000.00),
(14, 9, 'pending', '2025-10-28 09:46:22', 3520000.00, 'Mandalay', '', 0.00),
(15, 9, 'pending', '2025-10-28 09:46:56', 3200000.00, 'Mandalay', '', 0.00),
(16, 9, 'delivered', '2025-10-28 09:50:28', 3200000.00, 'Mandalay', '', 0.00),
(17, 9, 'pending', '2025-10-28 14:12:06', 9870000.00, 'Pyin Oo Lwin', 'HaYMaN25', 4230000.00),
(18, 9, 'delivered', '2025-10-28 14:31:15', 3200000.00, 'Mandalay', '', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`order_detail_id`, `order_id`, `product_item_id`, `quantity`, `unit_price`, `discount_amount`) VALUES
(1, 1, 16, 1, 236000.00, 0.00),
(2, 2, 15, 1, 32000.00, 0.00),
(3, 2, 16, 2, 236000.00, 0.00),
(4, 3, 16, 1, 236000.00, 0.00),
(5, 4, 15, 1, 32000.00, 0.00),
(6, 5, 16, 1, 236000.00, 0.00),
(7, 6, 31, 1, 2100000.00, 0.00),
(8, 6, 33, 1, 780000.00, 0.00),
(9, 6, 34, 1, 1000000.00, 0.00),
(10, 7, 33, 1, 780000.00, 0.00),
(11, 7, 34, 1, 1000000.00, 0.00),
(12, 8, 33, 1, 780000.00, 0.00),
(13, 8, 34, 1, 1000000.00, 0.00),
(14, 9, 34, 1, 1000000.00, 0.00),
(15, 10, 34, 1, 1000000.00, 0.00),
(16, 11, 34, 1, 1000000.00, 0.00),
(17, 11, 35, 1, 3200000.00, 0.00),
(18, 12, 34, 3, 1000000.00, 0.00),
(19, 13, 31, 1, 2100000.00, 0.00),
(20, 13, 34, 1, 1000000.00, 0.00),
(21, 13, 35, 1, 3200000.00, 0.00),
(22, 14, 32, 1, 320000.00, 0.00),
(23, 14, 35, 1, 3200000.00, 0.00),
(24, 15, 35, 1, 3200000.00, 0.00),
(25, 16, 35, 1, 3200000.00, 0.00),
(26, 17, 30, 1, 12000000.00, 0.00),
(27, 17, 31, 1, 2100000.00, 0.00),
(28, 18, 35, 1, 3200000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `payment_amount`, `payment_method`, `payment_status`, `payment_date`, `transaction_id`) VALUES
(1, 1, 236000.00, 'Cash on Delivery', 'pending', '2025-10-06 14:38:37', NULL),
(2, 2, 504000.00, 'Cash on Delivery', 'pending', '2025-10-06 15:56:18', NULL),
(3, 3, 236000.00, 'Cash on Delivery', 'pending', '2025-10-17 10:29:19', NULL),
(4, 4, 25600.00, 'Mobile Banking', 'completed', '2025-10-17 10:32:17', NULL),
(5, 5, 236000.00, 'Cash on Delivery', 'pending', '2025-10-17 14:27:38', NULL),
(6, 6, 3104000.00, 'Mobile Banking', 'completed', '2025-10-18 04:06:38', NULL),
(7, 7, 1513000.00, 'Cash on Delivery', 'pending', '2025-10-18 04:17:00', NULL),
(8, 8, 1513000.00, 'Cash on Delivery', 'pending', '2025-10-18 08:28:01', NULL),
(9, 9, 850000.00, 'Cash on Delivery', 'pending', '2025-10-18 08:32:43', NULL),
(10, 10, 800000.00, 'Cash on Delivery', 'pending', '2025-10-18 10:30:19', NULL),
(11, 11, 3570000.00, 'Cash on Delivery', 'pending', '2025-10-18 10:40:11', NULL),
(12, 12, 3000000.00, 'Cash on Delivery', 'pending', '2025-10-18 11:59:35', NULL),
(13, 13, 5355000.00, 'Cash on Delivery', 'pending', '2025-10-28 09:35:41', NULL),
(14, 14, 3520000.00, 'Cash on Delivery', 'pending', '2025-10-28 09:46:22', NULL),
(15, 15, 3200000.00, 'Cash on Delivery', 'pending', '2025-10-28 09:46:56', NULL),
(16, 16, 3200000.00, 'Mobile Banking', 'completed', '2025-10-28 09:50:28', NULL),
(17, 17, 9870000.00, 'Cash on Delivery', 'pending', '2025-10-28 14:12:06', NULL),
(18, 18, 3200000.00, 'Mobile Banking', 'completed', '2025-10-28 14:31:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` int(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `stock` int(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_category_id`, `manufacturer_id`, `title`, `price`, `image`, `stock`, `description`) VALUES
(28, 67, 2, 'Nito ', 320000, '1760692979_black.jpg', 3, 'Easy for Binnger.'),
(29, 67, 5, 'Red Hill Double_Cut Delux ', 450000, '1760726416_images (1).jpg', 8, 'The color is Red (cherry).'),
(30, 70, 2, 'Yamaha Stage Custom Birch 5pc Drum Shell Pack With a 22 Kick Drum and 14”', 12000000, '1760726617_710OlCsxlRL.jpg', 1, 'Snare Drum in Raven Black For Students and Working Drummers'),
(31, 68, 6, 'Alesis Nitro Max Kit 10 Piece Electric Drum', 2100000, '1760726761_71ZLsxohBHL._AC_SL1500_.jpg', 2, 'Set with Quiet Mesh Pads, 10\" Dual Zone Snare, Bluetooth, 440+ Sounds, Drumeo, USB MIDI, Kick Pedal.'),
(32, 66, 2, 'Yamaha Student Series CGS103', 320000, '1760726977_574167a61f22e7e4ce6caca98bf25556.png', 9, 'AII Classical Guitar, Natural'),
(33, 69, 2, 'Yamaha P225B', 780000, '1760727065_images (2).jpg', 0, '88-Key Weighted Action Digital Piano with Power Supply and Sustain Pedal, Black'),
(34, 63, 3, 'Fender Squier Debut Series Stratocaster', 1000000, '1760727240_276272.jpg', 0, 'Electric Guitar, Beginner Guitar, with 2-Year Warranty, Includes Free Lessons, Dakota Red with Matte Finish.'),
(35, 67, 2, 'NIto23 b', 3200000, '1760760396_61b6DTHgNWL.jpg', 0, 'Easy.');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`category_id`, `category_name`) VALUES
(63, 'Electric Guitar'),
(65, 'Piano'),
(66, 'Ukelele'),
(67, 'Acoustic Guitar '),
(68, 'Electric Drum'),
(70, 'Drum'),
(73, 'Violin'),
(74, 'Bass Guitar'),
(75, 'Keyboards');

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL,
  `promotion_code` varchar(50) NOT NULL,
  `promotion_amount` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`promotion_id`, `promotion_code`, `promotion_amount`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
(1, 'Bb', 20.00, '2025-10-09', '2025-10-30', 1, '2025-10-17 04:00:13'),
(5, 'ThiDaKyut', 15.00, '2025-10-17', '2025-10-29', 1, '2025-10-17 21:44:44'),
(7, 'HaYMaN25', 30.00, '2025-10-22', '2025-12-01', 1, '2025-10-28 07:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_usage`
--

CREATE TABLE `promotion_usage` (
  `usage_id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion_usage`
--

INSERT INTO `promotion_usage` (`usage_id`, `promotion_id`, `user_id`, `order_id`, `used_at`) VALUES
(1, 5, 5, 9, '2025-10-18 02:02:43'),
(2, 1, 5, 10, '2025-10-18 04:00:19'),
(3, 5, 7, 11, '2025-10-18 04:10:11'),
(4, 5, 9, 13, '2025-10-28 03:05:41'),
(5, 7, 9, 17, '2025-10-28 07:42:06');

-- --------------------------------------------------------

--
-- Table structure for table `review_rating`
--

CREATE TABLE `review_rating` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_rating`
--

INSERT INTO `review_rating` (`review_id`, `product_id`, `user_id`, `review_text`, `rating`, `review_status`, `created_at`, `updated_at`) VALUES
(2, 35, 9, 'Good Sound Quality!', 5, 'pending', '2025-10-28 04:37:29', '2025-10-28 04:37:29'),
(3, 31, 9, 'Excellent tone and finish.', 3, 'pending', '2025-10-28 07:22:17', '2025-10-28 07:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('active','banned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`, `status`) VALUES
(2, 'Aye Aye', 'ayeaye@gmail.com', '$2y$10$itkrS38fyIiCS.HLn2HSxujr8qKsWM4I2iNPfy8.yBZ6OI8xzRm/a', 'active'),
(3, 'Charm', 'charm@gmail.com', '$2y$10$ylX2ScdUfE0PJvkir1yMHuVabPtbAbeCxDwdsTPC5d5sS.sYSMh2W', 'active'),
(4, 'Sai Sai Khem Hlaing', 'saisai@gmail.com', '$2y$10$5zhnpMVfIhfDLsaoJAnSSu0vXw2eOv43XDZ4KFFwjPePW254ZqXyG', 'active'),
(5, 'Myo Myo', 'myomyo@gmail.com', '$2y$10$BfGJZ1anIMM2tK6k/uP9UuriNkj5X6BFQrVDJAXoR3tSqxEZ/qnku', 'active'),
(6, 'Aung Aung', 'aungaung@gmail.com', '$2y$10$IuY7gk7mDcSPQ6K05cWSRONtvanErTpuGoii9eztQjldjRzW89s16', 'active'),
(7, 'naingnaing', 'naingnaing@gmail.com', '$2y$10$nicXxPK3.UHH0peniy1djewkvFrrtKAG8X7pFDMzLpn3uwK7Tz2ze', 'active'),
(8, 'Celicia', 'thetnin9@gmil.com', '$2y$10$/tjrIE8O8YV0yaBZXhzG0.zVC7iCH9IhgU9zJmx7sQmT1C8HeLAXm', 'active'),
(9, 'Wang', 'wang@gmail.com', '$2y$10$JSHDdaIwReOBBfD5s3XASuqgxF.Wv9gNYktY3SQcT3EYY3GxINSgq', 'active'),
(10, 'MinMin', 'minmin@gmil.com', '$2y$10$H6UTyZoam5Y0n6sR/8Fhuuoxy01zOrgqd/.rAJkkHC6ZAYIy2A4IC', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD PRIMARY KEY (`mid`);

--
-- Indexes for table `order_`
--
ALTER TABLE `order_`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_item_id` (`product_item_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_category_id` (`product_category_id`),
  ADD KEY `products_ibfk_2` (`manufacturer_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `promotion_code` (`promotion_code`);

--
-- Indexes for table `promotion_usage`
--
ALTER TABLE `promotion_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD UNIQUE KEY `unique_promotion_user` (`promotion_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `review_rating`
--
ALTER TABLE `review_rating`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_item_id` (`product_id`),
  ADD KEY `customer_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_`
--
ALTER TABLE `order_`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `promotion_usage`
--
ALTER TABLE `promotion_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `review_rating`
--
ALTER TABLE `review_rating`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_` (`order_id`);

--
-- Constraints for table `order_`
--
ALTER TABLE `order_`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_` (`order_id`);

--
-- Constraints for table `promotion_usage`
--
ALTER TABLE `promotion_usage`
  ADD CONSTRAINT `promotion_usage_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`promotion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order_` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `review_rating`
--
ALTER TABLE `review_rating`
  ADD CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
