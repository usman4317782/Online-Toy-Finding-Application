-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2024 at 10:32 AM
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
-- Database: `toy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$Ijv8Epd.96GlbRRfGiT4MuuglJD24yJC6CJ0oAJ51jifBn3mXoR/O', '2024-04-13 15:28:19', '2024-04-13 15:28:19');

-- --------------------------------------------------------

--
-- Table structure for table `cart_data`
--

CREATE TABLE `cart_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `toy_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,0) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_address` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_data`
--

INSERT INTO `cart_data` (`id`, `user_id`, `toy_id`, `city_id`, `quantity`, `price`, `total_amount`, `created_at`, `shipping_address`, `status`) VALUES
(27, 2, 6, 6, 4, 1234.00, 4937, '2024-04-23 05:16:17', 'Karachi', 1),
(28, 2, 7, 6, 2, 1988.00, 3976, '2024-04-23 05:16:17', 'Karachi', 1),
(29, 2, 0, 6, 0, 0.00, 8925, '2024-04-23 05:16:17', 'Karachi', 0),
(30, 2, 6, 8, 4, 1234.00, 4937, '2024-04-23 05:17:01', 'Cantt Lahore', 0),
(31, 2, 7, 8, 2, 1988.00, 3976, '2024-04-23 05:17:01', 'Cantt Lahore', 0),
(32, 2, 8, 8, 2, 256.00, 512, '2024-04-23 05:17:01', 'Cantt Lahore', 0),
(33, 2, 0, 8, 0, 0.00, 9510, '2024-04-23 05:17:01', 'Cantt Lahore', 0),
(34, 3, 6, 6, 6, 1234.00, 7406, '2024-04-23 06:33:56', 'North Karachi', 0),
(35, 3, 7, 6, 2, 1988.00, 3976, '2024-04-23 06:33:56', 'North Karachi', 0),
(36, 3, 8, 6, 12, 256.00, 3072, '2024-04-23 06:33:56', 'North Karachi', 0),
(37, 3, 0, 6, 0, 0.00, 14466, '2024-04-23 06:33:56', 'North Karachi', 0);

-- --------------------------------------------------------

--
-- Table structure for table `new_toy_request`
--

CREATE TABLE `new_toy_request` (
  `toy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `toy_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `feedback_by_admin` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_toy_request`
--

INSERT INTO `new_toy_request` (`toy_id`, `user_id`, `toy_name`, `description`, `picture`, `feedback_by_admin`, `created_at`) VALUES
(1, 3, 'Robot Toy', 'A futuristic robot toy with LED lights and remote control functionality.', NULL, 'Request accepted. We\\&#039;ll contact soon.', '2024-04-16 13:37:59'),
(2, 3, 'Robot Toy', 'A futuristic robot toy with LED lights and remote control functionality.', 'uploads/bd81c3afcc.jpg', '', '2024-04-16 13:38:38'),
(3, 3, 'Magic Castle Playset', 'A colorful playset featuring a magical castle, princesses, and dragons for imaginative play.', 'uploads/5c63e02792.jpg', 'Thanks for contacting us. We will contact soon. Your request is under process now.', '2024-04-16 13:39:38'),
(4, 2, 'Dummy Toy', ' Description', NULL, 'Thanks hahsim for contact us. we\\&#039;ll get back soon.', '2024-04-16 13:49:56'),
(5, 2, 'Robot', 'Need a new Toy Robot', 'uploads/7ad1e2a3fa.jpg', '', '2024-04-16 13:50:37');

-- --------------------------------------------------------

--
-- Table structure for table `online_payment`
--

CREATE TABLE `online_payment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `trans_id` varchar(1000) NOT NULL,
  `payment_amount` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `online_payment`
--

INSERT INTO `online_payment` (`id`, `user_id`, `username`, `email`, `created_at`, `trans_id`, `payment_amount`) VALUES
(1, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 04:59:04', 'ch_3P9KOhGCDpOhv9Km03YRcwrr', 184),
(2, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:00:17', 'ch_3P9KPsGCDpOhv9Km05AI9vwb', 184),
(3, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:00:55', 'ch_3P9KQVGCDpOhv9Km08zRiKOp', 184),
(4, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:01:49', 'ch_3P9KRNGCDpOhv9Km1D9yOvWQ', 1000000),
(5, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:02:31', 'ch_3P9KS3GCDpOhv9Km1JQtbCnW', 1843500),
(6, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:03:29', 'ch_3P9KSyGCDpOhv9Km1L4HMT1u', 1843500),
(7, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:04:03', 'ch_3P9KTXGCDpOhv9Km08PTQaUk', 1843500),
(8, 2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '2024-04-25 05:04:38', 'ch_3P9KU5GCDpOhv9Km1IBJJOpw', 18435);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(2, 'qasim javed hashmi qureshi', 'qasim_hahshmi@gmail.com', '$2y$10$JNL3zCPebHACTAnM7S4iBOpnUNK8VhRoOfGU1SKsvH96G/xFXCcA6', '2024-04-13 15:07:54', '2024-04-24 18:26:31'),
(3, 'shahid imran', 'shahid@gmail.com', '$2y$10$JNL3zCPebHACTAnM7S4iBOpnUNK8VhRoOfGU1SKsvH96G/xFXCcA6', '2024-04-13 15:08:27', '2024-04-16 13:19:40');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_charges`
--

CREATE TABLE `shipping_charges` (
  `id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `charge` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_charges`
--

INSERT INTO `shipping_charges` (`id`, `city`, `charge`) VALUES
(6, 'Karachi', 12.00),
(8, 'Lahore', 85.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `toy_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stock_id`, `toy_id`, `quantity`) VALUES
(1, 6, 20),
(2, 8, 56),
(3, 7, 58);

-- --------------------------------------------------------

--
-- Table structure for table `stock_reports`
--

CREATE TABLE `stock_reports` (
  `report_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `total_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `toy_categories`
--

CREATE TABLE `toy_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toy_categories`
--

INSERT INTO `toy_categories` (`category_id`, `category_name`) VALUES
(10, 'Arts and Crafts'),
(7, 'Board Games'),
(4, 'Building Block'),
(2, 'Dolls'),
(5, 'Educational Toys'),
(11, 'Musical Toys'),
(9, 'Outdoor Toys'),
(14, 'Plush Toys'),
(8, 'Puzzles'),
(6, 'Remote Control Toys'),
(13, 'Role-Playing Toys'),
(15, 'Sports Equipment'),
(3, 'Stuffed Animals'),
(12, 'Vehicles and Cars');

-- --------------------------------------------------------

--
-- Table structure for table `toy_info`
--

CREATE TABLE `toy_info` (
  `toy_id` int(11) NOT NULL,
  `toy_name` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quality` enum('Low','Medium','High') NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toy_info`
--

INSERT INTO `toy_info` (`toy_id`, `toy_name`, `picture`, `price`, `quality`, `category_id`, `created_at`, `updated_at`) VALUES
(6, 'Barbie Doll', 'toy_pics/38c2284aad.jpg', 1234.25, 'Medium', 2, '2024-04-16 07:28:31', '2024-04-16 08:56:07'),
(7, 'puzzle game', 'toy_pics/38809606b0.jpg', 1988.00, 'High', 8, '2024-04-16 07:29:07', '2024-04-16 08:55:14'),
(8, 'Color Book & Pencils', 'toy_pics/7063394e3c.jpg', 256.00, 'Low', 10, '2024-04-16 07:30:11', '2024-04-16 07:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `toy_id` int(11) NOT NULL,
  `voucher` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id`, `user_id`, `toy_id`, `voucher`, `created_at`, `updated_at`) VALUES
(3, 2, 27, 'uploads/fe93cbd056.pdf', '2024-04-24 18:44:43', '2024-04-24 18:44:43'),
(4, 2, 28, 'uploads/9e8abb5fdf.pdf', '2024-04-24 18:44:50', '2024-04-24 18:44:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_data`
--
ALTER TABLE `cart_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_toy_request`
--
ALTER TABLE `new_toy_request`
  ADD PRIMARY KEY (`toy_id`);

--
-- Indexes for table `online_payment`
--
ALTER TABLE `online_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_charges`
--
ALTER TABLE `shipping_charges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `city` (`city`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `toy_id` (`toy_id`);

--
-- Indexes for table `stock_reports`
--
ALTER TABLE `stock_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `toy_categories`
--
ALTER TABLE `toy_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `toy_info`
--
ALTER TABLE `toy_info`
  ADD PRIMARY KEY (`toy_id`),
  ADD UNIQUE KEY `toy_name` (`toy_name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_data`
--
ALTER TABLE `cart_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `new_toy_request`
--
ALTER TABLE `new_toy_request`
  MODIFY `toy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `online_payment`
--
ALTER TABLE `online_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipping_charges`
--
ALTER TABLE `shipping_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_reports`
--
ALTER TABLE `stock_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `toy_categories`
--
ALTER TABLE `toy_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `toy_info`
--
ALTER TABLE `toy_info`
  MODIFY `toy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`toy_id`) REFERENCES `toy_info` (`toy_id`);

--
-- Constraints for table `toy_info`
--
ALTER TABLE `toy_info`
  ADD CONSTRAINT `toy_info_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `toy_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
