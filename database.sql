-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 12:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `land`
--

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `district_name` varchar(255) NOT NULL,
  `region_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_name`, `region_name`) VALUES
('Gairo', 'Morogoro'),
('Kilombero', 'Morogoro'),
('Kilosa', 'Morogoro'),
('Malinyi', 'Morogoro'),
('Morogoro Rural', 'Morogoro'),
('Morogoro Urban', 'Morogoro'),
('Mvomero', 'Morogoro'),
('Ulanga', 'Morogoro');

-- --------------------------------------------------------

--
-- Table structure for table `land_disputes`
--

CREATE TABLE `land_disputes` (
  `dispute_id` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `complainant_id` int(11) NOT NULL,
  `dispute_details` text NOT NULL,
  `dispute_status` enum('Pending','Resolved','Rejected') DEFAULT 'Pending',
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `land_parcels`
--

CREATE TABLE `land_parcels` (
  `land_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `land_title_no` varchar(50) NOT NULL,
  `land_size` decimal(10,2) NOT NULL,
  `land_use` enum('Residential','Commercial','Agricultural','Industrial') NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `registration_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `region_name` varchar(100) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `ward_name` varchar(100) NOT NULL,
  `village_name` varchar(100) NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `owner_approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `gov_approval_status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_parcels`
--

INSERT INTO `land_parcels` (`land_id`, `owner_id`, `land_title_no`, `land_size`, `land_use`, `latitude`, `longitude`, `registration_status`, `registered_at`, `region_name`, `district_name`, `ward_name`, `village_name`, `price`, `status`, `owner_approval_status`, `gov_approval_status`) VALUES
(2, 3, '2345', 23456.00, 'Commercial', 0.00000002, -0.00000003, 'Approved', '2025-03-28 12:25:06', '1', '1', '1', '1', NULL, NULL, 'pending', 'pending'),
(3, 3, '12345678', 2345678.00, 'Industrial', 0.00000028, -0.00000011, 'Approved', '2025-03-28 12:46:09', '1', '1', '1', '1', NULL, NULL, 'pending', 'pending'),
(4, 3, '1234567', 3.00, 'Commercial', 0.00000004, 0.00000002, 'Approved', '2025-03-28 12:48:26', '1', '1', '1', '1', NULL, NULL, 'pending', 'pending'),
(6, 3, '9', 23456.00, 'Agricultural', 0.00000004, 0.00000001, 'Rejected', '2025-03-28 13:19:49', '1', '1', '1', '2', NULL, NULL, 'pending', 'pending'),
(7, 4, '34567', 234567.00, 'Industrial', 0.00000044, 0.00000013, 'Approved', '2025-03-29 14:20:19', '1', '1', '1', '2', NULL, NULL, 'pending', 'pending'),
(8, 4, '2345678', 23.00, 'Agricultural', 0.00000003, -0.00000003, 'Approved', '2025-03-29 15:55:40', 'Morogoro', 'Mvomero', 'Berege', 'Berege Village 2', NULL, NULL, 'pending', 'pending'),
(10, 6, '2346789', 6543.00, 'Industrial', 0.00000023, -0.00000005, 'Approved', '2025-03-29 16:35:58', 'Morogoro', 'Mvomero', 'Mlali', 'Mamboya', NULL, NULL, 'pending', 'pending'),
(11, 6, '9877', 123.00, 'Commercial', 0.00000008, -0.00000005, 'Rejected', '2025-03-29 18:16:32', 'Morogoro', 'Mvomero', 'Berege', 'Berege Village 4', NULL, NULL, 'pending', 'pending'),
(12, 6, '23456781', 23456.00, 'Commercial', 0.00000003, -0.00000004, 'Rejected', '2025-03-29 18:21:44', 'Morogoro', 'Mvomero', 'Mlali', 'Kisaki', NULL, NULL, 'pending', 'pending'),
(13, 12, '876655', 567.00, 'Commercial', 0.00000003, -0.00000001, 'Approved', '2025-03-29 20:33:50', 'Morogoro', 'Mvomero', 'Mlali', 'Mlali', 9887.00, 'Not_sell', 'approved', 'approved'),
(14, 12, '1', 7654.00, 'Commercial', 99.99999999, 999.99999999, 'Approved', '2025-03-31 14:40:32', 'Morogoro', 'Mvomero', 'Mlali', 'Kisaki', 700000.00, 'Sell', 'approved', 'approved'),
(15, 10, '76543245', 234567.00, 'Agricultural', 0.00000008, 0.00000001, 'Approved', '2025-04-04 19:36:22', 'Morogoro', 'Mvomero', 'Mlali', 'Ikwiriri', NULL, NULL, 'pending', 'pending'),
(17, 12, 'LT-67F2CF3A', 567.00, 'Industrial', 0.00000003, 0.00000001, 'Approved', '2025-04-06 22:33:05', 'Morogoro', 'Mvomero', 'Mlali', 'Ndoombo', 8767654.00, 'Sell', 'approved', 'approved'),
(18, 12, 'LT-67F300AC', 76.00, 'Commercial', 0.00000001, 0.00000001, 'Approved', '2025-04-06 23:11:26', 'Morogoro', 'Mvomero', 'Mlali', 'Mlali', 987665.00, 'Sell', 'approved', 'approved'),
(19, 13, 'LT-67F663BB', 5678.00, 'Commercial', 0.00000002, -0.00000001, 'Approved', '2025-04-09 12:11:28', 'Morogoro', 'Mvomero', 'Mlali', 'Kisaki', NULL, NULL, 'pending', 'pending'),
(20, 12, 'LT-67F66399', 45678.00, 'Agricultural', 99.99999999, 876.00000000, 'Approved', '2025-04-09 12:20:01', 'Morogoro', 'Mvomero', 'Mlali', 'Kisaki', 34567.00, 'Sell', 'approved', 'approved'),
(21, 12, 'LT-67FA4103', 33.00, 'Agricultural', 1.00000000, 7.00000000, 'Approved', '2025-04-12 10:34:16', 'Morogoro', 'Mvomero', 'Mlali', 'Mamboya', 67890.00, 'Sell', 'approved', 'approved'),
(22, 12, 'LT-67FC0F83', 67.00, 'Residential', 0.00000001, -0.00000001, 'Approved', '2025-04-13 19:29:01', 'Morogoro', 'Mvomero', 'Mlali', 'Mlali', 453.00, 'Sell', 'approved', 'approved'),
(23, 12, 'LT-67FC290D', 8.00, 'Residential', 0.00000003, 0.00000001, 'Approved', '2025-04-13 21:15:44', 'Morogoro', 'Mvomero', 'Mlali', 'Ndoombo', 7890098.00, 'Sell', 'approved', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `land_title_requests`
--

CREATE TABLE `land_title_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `land_title_no` varchar(50) DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_title_requests`
--

INSERT INTO `land_title_requests` (`request_id`, `user_id`, `request_status`, `land_title_no`, `requested_at`) VALUES
(1, 11, 'Approved', 'LT-67F2CF3A', '2025-04-06 18:50:06'),
(2, 11, 'Approved', 'LT-67F300AC', '2025-04-06 19:03:19'),
(3, 9, 'Approved', 'LT-67F41DB9', '2025-04-07 18:43:01'),
(4, 11, 'Approved', 'LT-67F66399', '2025-04-09 12:09:16'),
(5, 13, 'Approved', 'LT-67F663BB', '2025-04-09 12:10:13'),
(6, 12, 'Pending', NULL, '2025-04-09 17:31:06'),
(7, 10, 'Approved', 'LT-67FA4103', '2025-04-12 09:55:21'),
(8, 11, 'Approved', 'LT-67FC0F83', '2025-04-13 19:24:01'),
(9, 11, 'Approved', 'LT-67FC290D', '2025-04-13 21:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `land_transfers`
--

CREATE TABLE `land_transfers` (
  `transfer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `sale_price` decimal(12,2) NOT NULL,
  `transfer_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `transfer_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_transfers`
--

INSERT INTO `land_transfers` (`transfer_id`, `seller_id`, `buyer_id`, `land_id`, `sale_price`, `transfer_status`, `approved_by`, `transfer_date`) VALUES
(5, 11, 12, 23, 7890098.00, 'Approved', NULL, '2025-04-13 22:50:56'),
(6, 12, 12, 23, 7890098.00, 'Pending', NULL, '2025-04-15 07:14:12'),
(7, 10, 12, 13, 9887.00, 'Approved', NULL, '2025-04-15 20:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `land_verifications`
--

CREATE TABLE `land_verifications` (
  `verification_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `verification_status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `land_title_no` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_verifications`
--

INSERT INTO `land_verifications` (`verification_id`, `requester_id`, `land_id`, `verification_status`, `verified_by`, `verified_at`, `land_title_no`) VALUES
(3, 8, 4, '', 8, '2025-04-02 10:40:17', '1234567'),
(4, 8, 6, 'Rejected', 8, '2025-04-02 10:41:41', '9'),
(5, 7, 15, '', 7, '2025-04-04 19:37:12', '76543245'),
(6, 7, 17, '', 7, '2025-04-06 22:45:23', 'LT-67F2CF3A'),
(7, 7, 18, '', 7, '2025-04-06 23:12:10', 'LT-67F300AC'),
(8, 8, 19, '', 8, '2025-04-09 12:13:26', 'LT-67F663BB'),
(9, 8, 20, '', 8, '2025-04-09 12:21:37', 'LT-67F66399'),
(10, 7, 21, '', 7, '2025-04-12 11:31:37', 'LT-67FA4103'),
(11, 8, 22, '', 8, '2025-04-13 19:32:52', 'LT-67FC0F83'),
(12, 7, 23, '', 7, '2025-04-13 21:16:07', 'LT-67FC290D');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('SMS','Email') NOT NULL,
  `status` enum('Sent','Pending','Failed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_type` enum('Registration','Verification','Transfer') DEFAULT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_approval` varchar(20) DEFAULT NULL,
  `gov_approval` varchar(20) DEFAULT NULL,
  `old_owner_id` int(11) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `payer_id`, `land_id`, `amount`, `payment_type`, `transaction_id`, `payment_status`, `payment_date`, `owner_approval`, `gov_approval`, `old_owner_id`, `transfer_id`) VALUES
(2, 12, 14, 600000.00, '', '4174377200', '', '2025-04-02 13:22:39', NULL, NULL, NULL, NULL),
(4, 12, 14, 600000.00, '', '3244064287', '', '2025-04-02 13:52:07', NULL, NULL, NULL, NULL),
(5, 12, 14, 600000.00, '', '3014197726', '', '2025-04-02 14:10:48', NULL, NULL, NULL, NULL),
(6, 12, 14, 600000.00, '', '5078718634', '', '2025-04-02 14:18:33', NULL, NULL, NULL, NULL),
(7, 12, 14, 600000.00, '', '6804093072', '', '2025-04-02 15:33:48', NULL, NULL, NULL, NULL),
(8, 12, 14, 600000.00, '', '4047411978', '', '2025-04-02 15:41:51', NULL, NULL, NULL, NULL),
(9, 12, 14, 600000.00, '', '9120787141', '', '2025-04-02 16:28:00', NULL, NULL, NULL, NULL),
(11, 12, 14, 600000.00, '', '3540132109', '', '2025-04-02 16:55:59', NULL, NULL, NULL, NULL),
(12, 12, 14, 600000.00, '', '6361974166', '', '2025-04-02 16:57:20', NULL, NULL, NULL, NULL),
(13, 12, 14, 7000000.00, '', '5758155744', '', '2025-04-02 17:17:54', NULL, NULL, NULL, NULL),
(14, 12, 14, 7000000.00, '', '1818744152', '', '2025-04-03 15:11:38', NULL, NULL, NULL, NULL),
(15, 12, 14, 7000000.00, '', '4863759121', '', '2025-04-03 17:19:15', NULL, NULL, NULL, NULL),
(16, 12, 14, 7000000.00, '', '3523061654', '', '2025-04-03 17:20:07', NULL, NULL, NULL, NULL),
(17, 12, 14, 7000000.00, '', '9835522832', '', '2025-04-03 17:31:28', NULL, NULL, NULL, NULL),
(18, 12, 14, 7000000.00, '', '1485032390', '', '2025-04-03 17:31:42', NULL, NULL, NULL, NULL),
(19, 12, 14, 7000000.00, '', '3621820976', '', '2025-04-03 17:40:29', NULL, NULL, NULL, NULL),
(20, 12, 14, 7000000.00, '', '3511080989', '', '2025-04-03 17:41:09', NULL, NULL, NULL, NULL),
(25, 12, 14, 7000000.00, '', '5042648164', 'paid', '2025-04-03 17:50:04', NULL, NULL, NULL, NULL),
(26, 12, 14, 690000.00, '', '8956040706', 'paid', '2025-04-04 19:16:57', NULL, NULL, NULL, NULL),
(28, 12, 14, 4567890.00, '', '7703810800', 'paid', '2025-04-05 19:06:02', NULL, NULL, NULL, NULL),
(29, 12, 14, 700000.00, '', '2854526637', 'paid', '2025-04-05 20:09:24', NULL, NULL, NULL, NULL),
(31, 12, 17, 8767654.00, '', '4677444915', 'paid', '2025-04-06 22:46:10', NULL, NULL, NULL, NULL),
(32, 12, 18, 987665.00, '', '8250946561', 'paid', '2025-04-06 23:23:21', NULL, NULL, NULL, NULL),
(33, 12, 17, 8767654.00, '', '3717108249', 'paid', '2025-04-07 18:57:10', NULL, NULL, NULL, NULL),
(34, 12, 18, 987665.00, '', '1534681587', 'paid', '2025-04-09 12:03:19', NULL, NULL, NULL, NULL),
(35, 12, 20, 34567.00, '', '8254945220', 'paid', '2025-04-09 12:23:14', NULL, NULL, NULL, NULL),
(36, 12, 21, 67890.00, 'Registration', '8044093828', 'paid', '2025-04-12 11:35:25', 'approved', 'approved', 10, NULL),
(45, 12, 22, 453.00, '', '7772900764', 'paid', '2025-04-13 20:41:41', 'approved', 'approved', 12, NULL),
(46, 12, 23, 90900.00, '', '2893112427', 'paid', '2025-04-13 21:18:05', 'approved', 'approved', 11, 5),
(47, 12, 23, 1000000000.00, '', '6856794231', 'paid', '2025-04-13 21:46:20', 'approved', 'approved', 11, 5),
(48, 12, 23, 1000000000.00, '', '8057858820', 'paid', '2025-04-13 21:58:58', 'approved', 'approved', 11, 5),
(49, 12, 23, 1000000000.00, '', '5025277925', 'paid', '2025-04-13 22:05:22', 'approved', 'approved', 11, 5),
(50, 12, 23, 1000000000.00, '', '6149262103', 'paid', '2025-04-13 22:05:52', 'approved', 'approved', 11, 5),
(51, 12, 23, 1000000000.00, '', '1368734948', 'paid', '2025-04-13 22:30:42', 'approved', 'approved', 11, 5),
(52, 12, 23, 7890098.00, '', '6935737400', 'paid', '2025-04-13 22:50:56', 'approved', 'approved', 11, 5),
(54, 12, 23, 7890098.00, '', '9272662184', 'paid', '2025-04-13 23:28:46', 'approved', 'approved', 11, 5),
(55, 12, 23, 7890098.00, '', '6681718189', 'paid', '2025-04-13 23:30:30', 'approved', 'approved', 11, 5),
(56, 12, 23, 7890098.00, '', '3996845311', 'paid', '2025-04-15 07:14:12', NULL, NULL, NULL, 6),
(57, 12, 13, 9887.00, '', '6653843995', 'paid', '2025-04-15 20:04:21', 'approved', 'approved', 10, 7);

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `region_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`region_name`) VALUES
('Arusha'),
('Dar es Salaam'),
('Dodoma'),
('Geita'),
('Iringa'),
('Kagera'),
('Katavi'),
('Kigoma'),
('Kilimanjaro'),
('Lindi'),
('Manyara'),
('Mara'),
('Mbeya'),
('Morogoro'),
('Mtwara'),
('Mwanza'),
('Njombe'),
('Pemba North'),
('Pemba South'),
('Pwani'),
('Rukwa'),
('Ruvuma'),
('Shinyanga'),
('Simiyu'),
('Singida'),
('Songwe'),
('Tabora'),
('Tanga'),
('Zanzibar Central/South'),
('Zanzibar North'),
('Zanzibar Urban/West');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `second_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `national_id` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','landowner','buyer','surveyor','lawyer','government_official') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `second_name`, `last_name`, `sex`, `national_id`, `phone_number`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'Dotto', 'Titto', 'Charles', 'male', '1234', '0764409099', 'titto@gmail.com', '1234', 'landowner', '2025-03-28 08:52:40'),
(4, 'Martina', 'Dotto', 'Charles', 'female', '222222222222', '087676', 'm@gmail.com', '$2y$10$BPYjKfNkwTGYpPUnL2KGoebotDML.G.MefWh8.9hixy1TB1rxfbC2', 'landowner', '2025-03-29 13:40:09'),
(5, 'Teddy', 'Dotto', 'Charles', 'female', '12345678', '09877', 'd@gmail.com', '$2y$10$TjEdFL/ewzzJ4kDcOGmjNOpcYQJV0KNCXAkgfhTp6HMLs3fcIEsKy', 'landowner', '2025-03-29 13:50:09'),
(6, 'Titto', '', 'Charls', 'male', '123456', '0765', 't@gmail.com', '$2y$10$rfb2/82TJYi1DcPLN4qNiuUWxrr8JkRwYqbKqBwWTDroQRbHNVGBa', 'landowner', '2025-03-29 16:32:45'),
(7, 'Dotto', 'Titto', 'Charles', 'male', '88888888888', '7665', 'tttt@gmail.com', '$2y$10$y0xlxL5BMJlIKthLz6MH2Oc4wvHV3o68KFjaEyd1bzPNa6kxZTU1i', 'government_official', '2025-03-29 18:37:35'),
(8, 'Dotto', 't', 'Charles', 'male', '9887765545', '9876', 'r@fgghh', '$2y$10$ImI5fBPuYfPdQK.fCWZw0OWZiXn0B8WmF6AQqLEGJTvesH7PJ7fLS', 'government_official', '2025-03-29 19:44:00'),
(9, 'Dotto', '', 'Charles', 'male', '12349', '9875', 'ggh@hg', '$2y$10$LnWoZ7saHQG0Id.VGIi3UuwsCh6DlsnOYQm4i5tuFD60saTd3cKmq', 'landowner', '2025-03-29 19:46:12'),
(10, 'Dotto', 't', 'charles', 'male', '55555555', '7654', 'tittoc2@gmail.com', '$2y$10$bRDuUHihr0xYbjCIDilme.88SK5kYg7PpIwVALsgHJDjuHjiSyRNa', 'landowner', '2025-03-29 20:31:49'),
(11, 'Mary', '', 'Mwamu', 'female', '777777777', '98766554', 'fgg@gm.com', '$2y$10$91uwoMCfpKei1kfV9Vqd8e/OAShvtT9YkTjrt8BzUKGlPpLLoMBoy', 'landowner', '2025-03-31 14:38:39'),
(12, 'Kisanjara', '', 'Lameck', 'male', '666666', '0977655', 'tfg@gmail.c', '$2y$10$4Txfwo2KUqzuxrKrxuPiD.yry9TlbK.FNzBlekYHcJcm7wC/LBJT6', 'buyer', '2025-04-02 11:13:22'),
(13, 'charles', '', 'mongu', 'male', '333333', '0987', 'ti@g', '$2y$10$caZ0kRtYy6Nc9PObxmfPH.PdDD6FnjF7XGaZGSCWS4GeGSKj98xku', 'surveyor', '2025-04-06 18:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `villages`
--

CREATE TABLE `villages` (
  `village_name` varchar(255) NOT NULL,
  `ward_name` varchar(255) NOT NULL,
  `district_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `villages`
--

INSERT INTO `villages` (`village_name`, `ward_name`, `district_name`) VALUES
('Berege Village 1', 'Berege', 'Mvomero'),
('Berege Village 2', 'Berege', 'Mvomero'),
('Berege Village 3', 'Berege', 'Mvomero'),
('Berege Village 4', 'Berege', 'Mvomero'),
('Ikwiriri', 'Mlali', 'Mvomero'),
('Kisaki', 'Mlali', 'Mvomero'),
('Mamboya', 'Mlali', 'Mvomero'),
('Mlali', 'Mlali', 'Mvomero'),
('Ndoombo', 'Mlali', 'Mvomero');

-- --------------------------------------------------------

--
-- Table structure for table `wards`
--

CREATE TABLE `wards` (
  `ward_name` varchar(255) NOT NULL,
  `district_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wards`
--

INSERT INTO `wards` (`ward_name`, `district_name`) VALUES
('Berege', 'Mvomero'),
('Dindili', 'Mvomero'),
('Kibati', 'Mvomero'),
('Kichangani', 'Mvomero'),
('Mgeta', 'Mvomero'),
('Mlali', 'Mvomero'),
('Mlimani', 'Mvomero'),
('Nguvumali', 'Mvomero'),
('Rudewa', 'Mvomero');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`district_name`),
  ADD KEY `region_name` (`region_name`);

--
-- Indexes for table `land_disputes`
--
ALTER TABLE `land_disputes`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `land_id` (`land_id`),
  ADD KEY `complainant_id` (`complainant_id`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- Indexes for table `land_parcels`
--
ALTER TABLE `land_parcels`
  ADD PRIMARY KEY (`land_id`),
  ADD UNIQUE KEY `land_title_no` (`land_title_no`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `land_title_requests`
--
ALTER TABLE `land_title_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `land_title_no` (`land_title_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `land_transfers`
--
ALTER TABLE `land_transfers`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `land_id` (`land_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `land_verifications`
--
ALTER TABLE `land_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `fk_requester` (`requester_id`),
  ADD KEY `fk_land` (`land_id`),
  ADD KEY `fk_verified_by` (`verified_by`),
  ADD KEY `fk_land_title` (`land_title_no`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `payer_id` (`payer_id`),
  ADD KEY `land_id` (`land_id`),
  ADD KEY `transfer_id` (`transfer_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`region_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `villages`
--
ALTER TABLE `villages`
  ADD PRIMARY KEY (`village_name`,`ward_name`,`district_name`),
  ADD KEY `ward_name` (`ward_name`,`district_name`);

--
-- Indexes for table `wards`
--
ALTER TABLE `wards`
  ADD PRIMARY KEY (`ward_name`,`district_name`),
  ADD KEY `district_name` (`district_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `land_disputes`
--
ALTER TABLE `land_disputes`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `land_parcels`
--
ALTER TABLE `land_parcels`
  MODIFY `land_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `land_title_requests`
--
ALTER TABLE `land_title_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `land_transfers`
--
ALTER TABLE `land_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `land_verifications`
--
ALTER TABLE `land_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`region_name`) REFERENCES `regions` (`region_name`) ON DELETE CASCADE;

--
-- Constraints for table `land_disputes`
--
ALTER TABLE `land_disputes`
  ADD CONSTRAINT `land_disputes_ibfk_1` FOREIGN KEY (`land_id`) REFERENCES `land_parcels` (`land_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `land_disputes_ibfk_2` FOREIGN KEY (`complainant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `land_disputes_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `land_parcels`
--
ALTER TABLE `land_parcels`
  ADD CONSTRAINT `land_parcels_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `land_title_requests`
--
ALTER TABLE `land_title_requests`
  ADD CONSTRAINT `land_title_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `land_transfers`
--
ALTER TABLE `land_transfers`
  ADD CONSTRAINT `land_transfers_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `land_transfers_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `land_transfers_ibfk_3` FOREIGN KEY (`land_id`) REFERENCES `land_parcels` (`land_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `land_transfers_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `land_verifications`
--
ALTER TABLE `land_verifications`
  ADD CONSTRAINT `fk_land` FOREIGN KEY (`land_id`) REFERENCES `land_parcels` (`land_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_land_title` FOREIGN KEY (`land_title_no`) REFERENCES `land_parcels` (`land_title_no`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`payer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`land_id`) REFERENCES `land_parcels` (`land_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`transfer_id`) REFERENCES `land_transfers` (`transfer_id`) ON DELETE SET NULL;

--
-- Constraints for table `villages`
--
ALTER TABLE `villages`
  ADD CONSTRAINT `villages_ibfk_1` FOREIGN KEY (`ward_name`,`district_name`) REFERENCES `wards` (`ward_name`, `district_name`) ON DELETE CASCADE;

--
-- Constraints for table `wards`
--
ALTER TABLE `wards`
  ADD CONSTRAINT `wards_ibfk_1` FOREIGN KEY (`district_name`) REFERENCES `districts` (`district_name`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
