-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2025 at 09:41 AM
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
-- Database: `ikea_commissary`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `module`, `timestamp`, `details`) VALUES
(1, 3, 'login', 'auth', '2025-10-18 00:23:45', '{\"email\":\"makiemorales2@gmail.com\"}'),
(2, 3, 'logout', 'auth', '2025-10-18 00:45:52', '[]'),
(3, 7, 'login', 'auth', '2025-10-18 00:47:10', '{\"email\":\"kitchen@demo.local\"}'),
(4, 7, 'logout', 'auth', '2025-10-18 00:48:57', '[]'),
(5, 6, 'login', 'auth', '2025-10-18 00:50:26', '{\"email\":\"purchaser@demo.local\"}'),
(6, 6, 'logout', 'auth', '2025-10-18 00:57:29', '[]'),
(7, 5, 'login', 'auth', '2025-10-18 00:57:34', '{\"email\":\"stock@demo.local\"}'),
(8, 5, 'logout', 'auth', '2025-10-18 01:00:13', '[]'),
(9, 6, 'login', 'auth', '2025-10-18 01:00:19', '{\"email\":\"purchaser@demo.local\"}'),
(10, 6, 'logout', 'auth', '2025-10-18 01:10:25', '[]'),
(11, 3, 'login', 'auth', '2025-10-18 01:10:32', '{\"email\":\"makiemorales2@gmail.com\"}'),
(12, 3, 'create', 'ingredients', '2025-10-18 01:19:16', '{\"ingredient_id\":1,\"name\":\"Dry flours\"}'),
(13, 3, 'create', 'ingredients', '2025-10-18 01:20:22', '{\"ingredient_id\":2,\"name\":\"White/granulated sugar\"}'),
(14, 3, 'create', 'ingredients', '2025-10-18 01:20:28', '{\"ingredient_id\":3,\"name\":\"brown sugar\"}'),
(15, 3, 'create', 'ingredients', '2025-10-18 01:21:04', '{\"ingredient_id\":4,\"name\":\"Unsalted butter\"}'),
(16, 3, 'create', 'purchases', '2025-10-18 01:54:28', '{\"purchase_id\":1,\"item_id\":3,\"quantity\":1,\"cost\":2300}'),
(17, 3, 'mark_paid', 'purchases', '2025-10-18 01:55:03', '{\"purchase_id\":1}'),
(18, 3, 'mark_paid', 'purchases', '2025-10-18 01:55:38', '{\"purchase_id\":1}'),
(19, 3, 'create', 'deliveries', '2025-10-18 01:59:55', '{\"delivery_id\":1,\"purchase_id\":1,\"quantity_received\":1}'),
(20, 3, 'logout', 'auth', '2025-10-18 03:09:30', '[]'),
(21, 7, 'login', 'auth', '2025-10-18 03:13:04', '{\"email\":\"kitchen@demo.local\"}'),
(22, 7, 'logout', 'auth', '2025-10-18 03:13:53', '[]'),
(23, 3, 'login', 'auth', '2025-10-18 03:15:08', '{\"email\":\"makiemorales2@gmail.com\"}'),
(24, 3, 'login', 'auth', '2025-10-18 03:25:37', '{\"email\":\"makiemorales2@gmail.com\"}'),
(25, 3, 'create', 'ingredients', '2025-10-18 03:42:52', '{\"ingredient_id\":5,\"name\":\"rock salt\"}'),
(26, 3, 'create', 'requests', '2025-10-18 03:43:37', '{\"request_id\":1,\"item_id\":5,\"quantity\":1}'),
(27, 3, 'reject', 'requests', '2025-10-18 03:43:49', '{\"reason\":\"Insufficient stock\",\"request_id\":1}'),
(28, 3, 'create', 'purchases', '2025-10-18 04:12:05', '{\"purchase_id\":2,\"item_id\":5,\"quantity\":50000,\"cost\":3200}'),
(29, 3, 'create', 'deliveries', '2025-10-18 04:12:44', '{\"delivery_id\":2,\"purchase_id\":2,\"quantity_received\":50000}'),
(30, 3, 'create', 'requests', '2025-10-18 04:40:56', '{\"request_id\":2,\"item_id\":5,\"quantity\":40000}'),
(31, 3, 'approve', 'requests', '2025-10-18 04:41:05', '{\"request_id\":2}'),
(32, 3, 'create', 'requests', '2025-10-18 04:53:07', '{\"request_id\":3,\"item_id\":5,\"quantity\":1000}'),
(33, 3, 'approve', 'requests', '2025-10-18 04:54:14', '{\"request_id\":3}'),
(34, 3, 'create', 'requests', '2025-10-18 05:12:33', '{\"request_id\":4,\"item_id\":3,\"quantity\":1000}'),
(35, 3, 'create', 'requests', '2025-10-18 05:12:33', '{\"request_id\":5,\"item_id\":5,\"quantity\":1000}'),
(36, 3, 'reject', 'requests', '2025-10-18 05:12:45', '{\"request_id\":4}'),
(37, 3, 'reject', 'requests', '2025-10-18 05:12:46', '{\"request_id\":5}'),
(38, 3, 'create', 'requests', '2025-10-18 09:41:33', '{\"batch_id\":6,\"request_id\":6,\"item_id\":5,\"quantity\":500}'),
(39, 3, 'create', 'requests', '2025-10-18 09:41:33', '{\"batch_id\":6,\"request_id\":7,\"item_id\":3,\"quantity\":500}'),
(40, 3, 'reject', 'requests', '2025-10-18 09:46:45', '{\"batch_id\":6}'),
(41, 3, 'create', 'requests', '2025-10-18 09:58:13', '{\"batch_id\":7,\"request_id\":8,\"item_id\":5,\"quantity\":1000}'),
(42, 3, 'create', 'requests', '2025-10-18 09:58:13', '{\"batch_id\":7,\"request_id\":9,\"item_id\":3,\"quantity\":500}'),
(43, 3, 'reject', 'requests', '2025-10-18 09:58:24', '{\"batch_id\":7}'),
(44, 3, 'create', 'requests', '2025-10-18 10:35:07', '{\"batch_id\":8,\"request_id\":10,\"item_id\":3,\"quantity\":1000}'),
(45, 3, 'create', 'requests', '2025-10-18 10:35:07', '{\"batch_id\":8,\"request_id\":11,\"item_id\":5,\"quantity\":500}'),
(46, 3, 'reject', 'requests', '2025-10-18 10:35:13', '{\"batch_id\":8}'),
(47, 3, 'create', 'requests', '2025-10-18 10:52:51', '{\"batch_id\":9,\"request_id\":12,\"item_id\":3,\"quantity\":1}'),
(48, 3, 'reject', 'requests', '2025-10-18 10:53:56', '{\"batch_id\":9}'),
(49, 3, 'create', 'requests', '2025-10-18 10:54:56', '{\"batch_id\":10,\"request_id\":13,\"item_id\":5,\"quantity\":1}'),
(50, 3, 'create', 'ingredients', '2025-10-18 11:40:53', '{\"ingredient_id\":1,\"name\":\"brown sugar\"}'),
(51, 3, 'create', 'ingredients', '2025-10-18 11:41:26', '{\"ingredient_id\":2,\"name\":\"white sugar\"}'),
(52, 3, 'create', 'ingredients', '2025-10-18 11:49:37', '{\"ingredient_id\":3,\"name\":\"milk\"}'),
(53, 3, 'create', 'requests', '2025-10-18 11:56:29', '{\"batch_id\":11,\"request_id\":14,\"item_id\":1,\"quantity\":1000}'),
(54, 3, 'reject', 'requests', '2025-10-18 11:56:46', '{\"batch_id\":11}'),
(55, 3, 'create', 'purchases', '2025-10-18 12:05:38', '{\"purchase_id\":3,\"item_id\":1,\"quantity\":50000,\"cost\":2900}'),
(56, 3, 'mark_paid', 'purchases', '2025-10-18 12:05:52', '{\"purchase_id\":3}'),
(57, 3, 'create', 'ingredients', '2025-10-18 12:25:22', '{\"ingredient_id\":4,\"name\":\"rock salt\"}'),
(58, 3, 'create', 'purchases', '2025-10-18 12:27:55', '{\"purchase_id\":4,\"item_id\":4,\"quantity\":50000,\"cost\":1400}'),
(59, 3, 'mark_paid', 'purchases', '2025-10-18 12:27:58', '{\"purchase_id\":4}'),
(60, 3, 'mark_paid', 'purchases', '2025-10-18 12:27:59', '{\"purchase_id\":4}'),
(61, 3, 'mark_paid', 'purchases', '2025-10-18 12:27:59', '{\"purchase_id\":4}'),
(62, 3, 'mark_paid', 'purchases', '2025-10-18 12:27:59', '{\"purchase_id\":4}'),
(63, 3, 'create', 'deliveries', '2025-10-18 12:29:45', '{\"delivery_id\":3,\"purchase_id\":4,\"quantity_received\":50000}'),
(64, 3, 'create', 'requests', '2025-10-18 12:31:15', '{\"batch_id\":12,\"request_id\":15,\"item_id\":4,\"quantity\":1000}'),
(65, 3, 'approve', 'requests', '2025-10-18 12:34:45', '{\"batch_id\":12}'),
(66, 3, 'create', 'requests', '2025-10-18 15:24:20', '{\"batch_id\":13,\"request_id\":16,\"item_id\":1,\"quantity\":2000}'),
(67, 3, 'reject', 'requests', '2025-10-18 15:24:34', '{\"batch_id\":13}'),
(68, 3, 'create', 'ingredients', '2025-10-18 15:29:17', '{\"ingredient_id\":5,\"name\":\"nature spring (250ml)\"}'),
(69, 3, 'create', 'purchases', '2025-10-18 16:00:02', '{\"purchase_id\":5,\"item_id\":5,\"quantity\":50,\"cost\":1500}'),
(70, 3, 'create', 'deliveries', '2025-10-18 16:02:26', '{\"delivery_id\":4,\"purchase_id\":5,\"quantity_received\":50}'),
(71, 3, 'create', 'requests', '2025-10-18 16:03:02', '{\"batch_id\":14,\"request_id\":17,\"item_id\":5,\"quantity\":10}'),
(72, 3, 'approve', 'requests', '2025-10-18 16:03:15', '{\"batch_id\":14}'),
(73, 3, 'create', 'ingredients', '2025-10-18 16:34:15', '{\"ingredient_id\":1,\"name\":\"brown sugar\"}'),
(74, 3, 'create', 'ingredients', '2025-10-18 16:34:49', '{\"ingredient_id\":2,\"name\":\"white sugar\"}'),
(75, 3, 'create', 'ingredients', '2025-10-18 16:35:42', '{\"ingredient_id\":3,\"name\":\"nature spring\"}'),
(76, 3, 'create', 'purchases', '2025-10-18 16:39:04', '{\"purchase_id\":6,\"item_id\":1,\"quantity\":50000,\"cost\":2900}'),
(77, 3, 'mark_paid', 'purchases', '2025-10-18 16:39:09', '{\"purchase_id\":6}'),
(78, 3, 'mark_paid', 'purchases', '2025-10-18 16:39:09', '{\"purchase_id\":6}'),
(79, 3, 'create', 'deliveries', '2025-10-18 16:40:32', '{\"delivery_id\":5,\"purchase_id\":6,\"quantity_received\":50}'),
(80, 3, 'create', 'deliveries', '2025-10-18 16:44:15', '{\"delivery_id\":6,\"purchase_id\":6,\"quantity_received\":50000}'),
(81, 3, 'create', 'requests', '2025-10-18 16:48:03', '{\"batch_id\":15,\"request_id\":18,\"item_id\":1,\"quantity\":500}'),
(82, 3, 'approve', 'requests', '2025-10-18 16:48:04', '{\"batch_id\":15}'),
(83, 3, 'create', 'purchases', '2025-10-18 16:56:09', '{\"purchase_id\":7,\"item_id\":3,\"quantity\":50,\"cost\":1500}'),
(84, 3, 'create', 'deliveries', '2025-10-18 16:57:10', '{\"delivery_id\":7,\"purchase_id\":7,\"quantity_received\":50}'),
(85, 3, 'create', 'ingredients', '2025-10-19 07:53:25', '{\"ingredient_id\":1,\"name\":\"brown sugar\"}'),
(86, 3, 'create', 'ingredients', '2025-10-19 07:54:27', '{\"ingredient_id\":2,\"name\":\"nature spring - 250ml\"}'),
(87, 3, 'create', 'purchases', '2025-10-19 08:01:23', '{\"purchase_id\":8,\"item_id\":1,\"quantity\":50000,\"cost\":2900}'),
(88, 3, 'create', 'deliveries', '2025-10-19 08:02:54', '{\"delivery_id\":8,\"purchase_id\":8,\"quantity_received\":50}'),
(89, 3, 'create', 'purchases', '2025-10-19 08:38:41', '{\"purchase_id\":9,\"item_id\":1,\"quantity\":49000,\"cost\":2800}'),
(90, 3, 'create', 'deliveries', '2025-10-19 08:39:52', '{\"delivery_id\":9,\"purchase_id\":9,\"quantity_received\":49000}'),
(91, 3, 'create', 'purchases', '2025-10-19 09:04:06', '{\"purchase_id\":10,\"item_id\":1,\"quantity\":1000,\"cost\":50}'),
(92, 3, 'create', 'deliveries', '2025-10-19 09:04:48', '{\"delivery_id\":10,\"purchase_id\":10,\"quantity_received\":1}'),
(93, 3, 'create', 'ingredients', '2025-10-19 09:45:52', '{\"ingredient_id\":3,\"name\":\"white sugar\"}'),
(94, 3, 'create', 'purchases', '2025-10-19 09:46:27', '{\"purchase_id\":11,\"item_id\":3,\"quantity\":50000,\"cost\":2900}'),
(95, 3, 'create', 'deliveries', '2025-10-19 09:47:43', '{\"delivery_id\":11,\"purchase_id\":11,\"quantity_received\":50000}'),
(96, 3, 'create', 'ingredients', '2025-10-19 22:00:00', '{\"ingredient_id\":1,\"name\":\"brown sugar\"}'),
(97, 3, 'create', 'purchases', '2025-10-19 22:03:39', '{\"purchase_id\":12,\"item_id\":1,\"quantity\":50000,\"cost\":2800}'),
(98, 3, 'create', 'deliveries', '2025-10-19 22:06:02', '{\"delivery_id\":12,\"purchase_id\":12,\"quantity_received\":50000}'),
(99, 3, 'login', 'auth', '2025-10-22 10:08:48', '{\"email\":\"makiemorales2@gmail.com\"}'),
(100, 3, 'create', 'ingredients', '2025-10-22 14:07:38', '{\"ingredient_id\":2,\"name\":\"white sugar\"}'),
(101, 3, 'create', 'ingredients', '2025-10-22 14:08:31', '{\"ingredient_id\":3,\"name\":\"baking soda\"}'),
(102, 3, 'create', 'ingredients', '2025-10-22 14:09:06', '{\"ingredient_id\":4,\"name\":\"cocoa powder\"}'),
(103, 3, 'create', 'ingredients', '2025-10-22 14:13:23', '{\"ingredient_id\":5,\"name\":\"Fresh milk\"}'),
(104, 3, 'create', 'purchases', '2025-10-23 00:23:05', '{\"purchase_id\":13,\"item_id\":3,\"quantity\":10,\"cost\":800,\"payment_type\":\"Cash\",\"base_amount\":5000}'),
(105, 3, 'create', 'purchases', '2025-10-23 00:23:05', '{\"purchase_id\":14,\"item_id\":2,\"quantity\":50000,\"cost\":3200,\"payment_type\":\"Cash\",\"base_amount\":5000}'),
(106, 3, 'login', 'auth', '2025-10-24 22:25:16', '{\"email\":\"makiemorales2@gmail.com\"}'),
(107, 3, 'create', 'purchases', '2025-10-24 22:37:52', '{\"purchase_id\":15,\"item_id\":3,\"quantity\":25,\"cost\":1500,\"payment_type\":\"Cash\",\"base_amount\":2500}'),
(108, 3, 'create', 'purchases', '2025-10-24 22:37:52', '{\"purchase_id\":16,\"item_id\":5,\"quantity\":5000,\"cost\":500,\"payment_type\":\"Cash\",\"base_amount\":2500}'),
(109, 3, 'create', 'purchases', '2025-10-25 00:11:41', '{\"purchase_id\":17,\"item_id\":3,\"quantity\":25,\"cost\":1500,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(110, 3, 'create', 'purchases', '2025-10-25 00:11:41', '{\"purchase_id\":18,\"item_id\":4,\"quantity\":10,\"cost\":1200,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(111, 3, 'create', 'purchases', '2025-10-25 00:49:09', '{\"purchase_id\":19,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":1300}'),
(112, 3, 'create', 'purchases', '2025-10-25 02:04:12', '{\"purchase_id\":20,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":2500}'),
(113, 3, 'create', 'purchases', '2025-10-25 02:04:12', '{\"purchase_id\":21,\"item_id\":4,\"quantity\":5,\"cost\":500,\"payment_type\":\"Cash\",\"base_amount\":2500}'),
(114, 3, 'create', 'purchases', '2025-10-25 02:13:00', '{\"purchase_id\":22,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":2000}'),
(115, 3, 'create', 'purchases', '2025-10-25 02:13:00', '{\"purchase_id\":23,\"item_id\":5,\"quantity\":10000,\"cost\":800,\"payment_type\":\"Cash\",\"base_amount\":2000}'),
(116, 3, 'create', 'purchases', '2025-10-25 02:18:28', '{\"purchase_id\":24,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":1200}'),
(117, 3, 'create', 'purchases', '2025-10-25 02:22:44', '{\"purchase_id\":25,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":1200}'),
(118, 3, 'create', 'purchases', '2025-10-25 02:32:27', '{\"purchase_id\":26,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":2000}'),
(119, 3, 'create', 'purchases', '2025-10-25 02:36:54', '{\"purchase_id\":27,\"item_id\":5,\"quantity\":10000,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":1100}'),
(120, 3, 'create', 'purchases', '2025-10-25 02:44:52', '{\"purchase_id\":28,\"item_id\":3,\"quantity\":10,\"cost\":1000,\"payment_type\":\"Cash\",\"base_amount\":1200}'),
(121, 3, 'create', 'purchases', '2025-10-25 02:50:11', '{\"purchase_id\":29,\"item_id\":4,\"quantity\":5,\"cost\":500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(122, 3, 'create', 'requests', '2025-10-25 02:54:03', '{\"batch_id\":16,\"request_id\":19,\"item_id\":1,\"quantity\":2000}'),
(123, 3, 'approve', 'requests', '2025-10-25 02:54:36', '{\"batch_id\":16}'),
(124, 3, 'create', 'purchases', '2025-10-26 23:27:15', '{\"purchase_id\":30,\"item_id\":2,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(125, 3, 'create', 'purchases', '2025-10-26 23:59:03', '{\"purchase_id\":31,\"item_id\":2,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(126, 3, 'create', 'purchases', '2025-10-26 23:59:03', '{\"purchase_id\":32,\"item_id\":5,\"quantity\":1000,\"cost\":100,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(127, 3, 'login', 'auth', '2025-11-15 07:38:35', '{\"email\":\"makiemorales2@gmail.com\"}'),
(128, 3, 'login', 'auth', '2025-11-26 17:09:11', '{\"email\":\"makiemorales2@gmail.com\"}'),
(129, 3, 'login', 'auth', '2025-11-26 19:30:11', '{\"email\":\"makiemorales2@gmail.com\"}'),
(130, 3, 'create', 'purchases', '2025-11-26 22:04:41', '{\"purchase_id\":33,\"item_id\":2,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Cash\",\"base_amount\":4400}'),
(131, 3, 'create', 'purchases', '2025-11-26 22:04:41', '{\"purchase_id\":34,\"item_id\":3,\"quantity\":10,\"cost\":1500,\"payment_type\":\"Cash\",\"base_amount\":4400}'),
(132, 3, 'create', 'ingredients', '2025-11-26 22:10:33', '{\"ingredient_id\":1,\"name\":\"white sugar\"}'),
(133, 3, 'create', 'purchases', '2025-11-26 22:32:40', '{\"purchase_id\":35,\"item_id\":1,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(134, 3, 'create', 'purchases', '2025-11-26 22:34:04', '{\"purchase_id\":36,\"item_id\":1,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Cash\",\"base_amount\":3000}'),
(135, 3, 'create', 'deliveries', '2025-11-26 22:41:53', '{\"delivery_id\":1,\"purchase_id\":36,\"quantity_received\":5000}'),
(136, 3, 'create', 'deliveries', '2025-11-26 22:43:51', '{\"delivery_id\":2,\"purchase_id\":36,\"quantity_received\":50000}'),
(137, 3, 'create', 'deliveries', '2025-11-26 23:49:27', '{\"delivery_id\":3,\"purchase_id\":36,\"quantity_received\":50000}'),
(138, 3, 'create', 'deliveries', '2025-11-27 00:26:57', '{\"delivery_id\":4,\"purchase_id\":36,\"quantity_received\":50000}'),
(139, 3, 'create', 'requests', '2025-11-27 01:34:20', '{\"batch_id\":17,\"request_id\":20,\"item_id\":1,\"quantity\":5000}'),
(140, 3, 'approve', 'requests', '2025-11-27 02:19:01', '{\"batch_id\":17}'),
(141, 3, 'create', 'requests', '2025-11-27 02:30:21', '{\"batch_id\":18,\"request_id\":21,\"item_id\":1,\"quantity\":50000}'),
(142, 3, 'create', 'ingredients', '2025-11-27 02:34:25', '{\"ingredient_id\":2,\"name\":\"brown sugar\"}'),
(143, 3, 'create', 'requests', '2025-11-27 02:35:41', '{\"batch_id\":19,\"request_id\":22,\"item_id\":1,\"quantity\":10000}'),
(144, 3, 'create', 'requests', '2025-11-27 02:35:41', '{\"batch_id\":19,\"request_id\":23,\"item_id\":2,\"quantity\":10000}'),
(145, 3, 'reject', 'requests', '2025-11-27 02:37:10', '{\"batch_id\":18}'),
(146, 3, 'reject', 'requests', '2025-11-27 02:38:02', '{\"batch_id\":19}'),
(147, 3, 'login', 'auth', '2025-11-27 03:39:26', '{\"email\":\"makiemorales2@gmail.com\"}'),
(148, 3, 'login', 'auth', '2025-11-27 06:01:50', '{\"email\":\"makiemorales2@gmail.com\"}'),
(149, 3, 'login', 'auth', '2025-11-27 06:04:46', '{\"email\":\"makiemorales2@gmail.com\"}'),
(150, 3, 'create', 'requests', '2025-11-27 06:15:14', '{\"batch_id\":20,\"request_id\":24,\"item_id\":1,\"quantity\":5000}'),
(151, 3, 'create', 'ingredients', '2025-11-27 07:40:03', '{\"ingredient_id\":3,\"name\":\"flour\"}'),
(152, 3, 'create', 'ingredients', '2025-11-27 07:41:09', '{\"ingredient_id\":4,\"name\":\"Cocoa powder\"}'),
(153, 3, 'create', 'ingredients', '2025-11-27 07:41:45', '{\"ingredient_id\":5,\"name\":\"Baking soda\"}'),
(154, 3, 'create', 'ingredients', '2025-11-27 07:42:39', '{\"ingredient_id\":6,\"name\":\"Butte\"}'),
(155, 3, 'create', 'purchases', '2025-11-27 07:47:11', '{\"purchase_id\":37,\"item_id\":5,\"quantity\":100000,\"cost\":5600,\"payment_type\":\"Card\",\"base_amount\":0}'),
(156, 3, 'create', 'purchases', '2025-11-27 07:47:11', '{\"purchase_id\":38,\"item_id\":6,\"quantity\":50000,\"cost\":2500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(157, 3, 'create', 'purchases', '2025-11-27 07:47:11', '{\"purchase_id\":39,\"item_id\":4,\"quantity\":50000,\"cost\":2500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(158, 3, 'create', 'purchases', '2025-11-27 07:47:11', '{\"purchase_id\":40,\"item_id\":3,\"quantity\":100000,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(159, 3, 'create', 'deliveries', '2025-11-27 07:48:23', '{\"delivery_id\":5,\"purchase_id\":37,\"quantity_received\":100000}'),
(160, 3, 'create', 'deliveries', '2025-11-27 07:48:23', '{\"delivery_id\":6,\"purchase_id\":38,\"quantity_received\":50000}'),
(161, 3, 'create', 'deliveries', '2025-11-27 07:48:23', '{\"delivery_id\":7,\"purchase_id\":39,\"quantity_received\":50000}'),
(162, 3, 'create', 'deliveries', '2025-11-27 07:48:23', '{\"delivery_id\":8,\"purchase_id\":40,\"quantity_received\":100000}'),
(163, 3, 'create', 'ingredient_sets', '2025-11-27 07:54:09', '{\"set_id\":1,\"name\":\"chocolate cake\"}'),
(164, 3, 'create', 'requests', '2025-11-27 08:01:23', '{\"batch_id\":21,\"request_id\":25,\"item_id\":5,\"quantity\":5}'),
(165, 3, 'create', 'requests', '2025-11-27 08:01:23', '{\"batch_id\":21,\"request_id\":26,\"item_id\":6,\"quantity\":100}'),
(166, 3, 'create', 'requests', '2025-11-27 08:01:23', '{\"batch_id\":21,\"request_id\":27,\"item_id\":4,\"quantity\":50}'),
(167, 3, 'create', 'requests', '2025-11-27 08:01:23', '{\"batch_id\":21,\"request_id\":28,\"item_id\":3,\"quantity\":250}'),
(168, 3, 'create', 'requests', '2025-11-27 08:16:59', '{\"batch_id\":22,\"request_id\":29,\"item_id\":5,\"quantity\":5}'),
(169, 3, 'create', 'requests', '2025-11-27 08:16:59', '{\"batch_id\":22,\"request_id\":30,\"item_id\":6,\"quantity\":100}'),
(170, 3, 'create', 'requests', '2025-11-27 08:16:59', '{\"batch_id\":22,\"request_id\":31,\"item_id\":4,\"quantity\":50}'),
(171, 3, 'create', 'requests', '2025-11-27 08:16:59', '{\"batch_id\":22,\"request_id\":32,\"item_id\":3,\"quantity\":250}'),
(172, 3, 'approve', 'requests', '2025-11-27 08:17:59', '{\"batch_id\":22}'),
(173, 3, 'create', 'requests', '2025-11-27 08:37:17', '{\"batch_id\":23,\"request_id\":33,\"item_id\":5,\"quantity\":5}'),
(174, 3, 'create', 'requests', '2025-11-27 08:37:17', '{\"batch_id\":23,\"request_id\":34,\"item_id\":6,\"quantity\":100}'),
(175, 3, 'create', 'requests', '2025-11-27 08:37:17', '{\"batch_id\":23,\"request_id\":35,\"item_id\":4,\"quantity\":50}'),
(176, 3, 'create', 'requests', '2025-11-27 08:37:17', '{\"batch_id\":23,\"request_id\":36,\"item_id\":3,\"quantity\":250}'),
(177, 3, 'approve', 'requests', '2025-11-27 08:37:25', '{\"batch_id\":23,\"next_stage\":\"To Prepare\"}'),
(178, 3, 'create', 'requests', '2025-11-27 08:43:42', '{\"batch_id\":24,\"request_id\":37,\"item_id\":5,\"quantity\":5}'),
(179, 3, 'create', 'requests', '2025-11-27 08:43:42', '{\"batch_id\":24,\"request_id\":38,\"item_id\":6,\"quantity\":100}'),
(180, 3, 'create', 'requests', '2025-11-27 08:43:42', '{\"batch_id\":24,\"request_id\":39,\"item_id\":4,\"quantity\":50}'),
(181, 3, 'create', 'requests', '2025-11-27 08:43:42', '{\"batch_id\":24,\"request_id\":40,\"item_id\":3,\"quantity\":250}'),
(182, 3, 'approve', 'requests', '2025-11-27 08:43:45', '{\"batch_id\":24,\"next_stage\":\"To Prepare\"}'),
(183, 3, 'create', 'requests', '2025-11-27 08:46:12', '{\"batch_id\":25,\"request_id\":41,\"item_id\":5,\"quantity\":5}'),
(184, 3, 'create', 'requests', '2025-11-27 08:46:12', '{\"batch_id\":25,\"request_id\":42,\"item_id\":6,\"quantity\":100}'),
(185, 3, 'create', 'requests', '2025-11-27 08:46:12', '{\"batch_id\":25,\"request_id\":43,\"item_id\":4,\"quantity\":50}'),
(186, 3, 'create', 'requests', '2025-11-27 08:46:12', '{\"batch_id\":25,\"request_id\":44,\"item_id\":3,\"quantity\":250}'),
(187, 3, 'approve', 'requests', '2025-11-27 08:46:31', '{\"batch_id\":25,\"next_stage\":\"To Prepare\"}'),
(188, 3, 'distribute', 'requests', '2025-11-27 08:49:00', '{\"batch_id\":25}'),
(189, 3, 'create', 'purchases', '2025-11-27 09:48:19', '{\"purchase_id\":41,\"item_id\":2,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(190, 3, 'create', 'purchases', '2025-11-27 09:56:27', '{\"purchase_id\":42,\"item_id\":2,\"quantity\":25000,\"cost\":1500,\"payment_type\":\"Cash\",\"base_amount\":2000}'),
(191, 3, 'create', 'deliveries', '2025-11-27 10:18:38', '{\"delivery_id\":9,\"purchase_id\":41,\"quantity_received\":50000}'),
(192, 3, 'create', 'deliveries', '2025-11-27 10:21:02', '{\"delivery_id\":10,\"purchase_id\":42,\"quantity_received\":25000}'),
(193, 3, 'create', 'purchases', '2025-11-27 10:28:10', '{\"purchase_id\":43,\"item_id\":4,\"quantity\":10000,\"cost\":1500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(194, 3, 'create', 'deliveries', '2025-11-27 10:31:52', '{\"delivery_id\":11,\"purchase_id\":43,\"quantity_received\":10000}'),
(195, 3, 'mark_paid', 'purchases', '2025-11-27 10:50:46', '{\"purchase_id\":41}'),
(196, 3, 'create', 'purchases', '2025-11-27 10:56:49', '{\"purchase_id\":44,\"item_id\":4,\"quantity\":50000,\"cost\":2500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(197, 3, 'create', 'deliveries', '2025-11-27 10:57:28', '{\"delivery_id\":12,\"purchase_id\":44,\"quantity_received\":50000}'),
(198, 3, 'mark_paid', 'purchases', '2025-11-27 10:58:34', '{\"purchase_id\":44,\"receipt\":\"/public/uploads/9b5970ca260fb622.jpg\"}'),
(199, 3, 'create', 'purchases', '2025-11-27 11:06:14', '{\"purchase_id\":45,\"item_id\":4,\"quantity\":50000,\"cost\":2500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(200, 3, 'mark_paid', 'purchases', '2025-11-27 11:19:39', '{\"purchase_id\":45,\"receipt\":\"/public/uploads/6c890f0fda5a4ded.jpg\"}'),
(201, 3, 'create', 'deliveries', '2025-11-27 11:20:26', '{\"delivery_id\":13,\"purchase_id\":45,\"quantity_received\":50000}'),
(202, 3, 'create', 'purchases', '2025-11-27 11:24:21', '{\"purchase_id\":46,\"item_id\":4,\"quantity\":10000,\"cost\":1500,\"payment_type\":\"Card\",\"base_amount\":0}'),
(203, 3, 'create', 'deliveries', '2025-11-27 11:26:04', '{\"delivery_id\":14,\"purchase_id\":46,\"quantity_received\":10000}'),
(204, 3, 'mark_paid', 'purchases', '2025-11-27 11:26:13', '{\"purchase_id\":46,\"receipt\":\"/public/uploads/6d979f4e8d8ac9c3.jpg\"}'),
(205, 3, 'logout', 'auth', '2025-11-27 11:40:13', '[]'),
(206, 7, 'login', 'auth', '2025-11-27 11:40:40', '{\"email\":\"kitchen@demo.local\"}'),
(207, 7, 'create', 'requests', '2025-11-27 11:43:15', '{\"batch_id\":26,\"request_id\":45,\"item_id\":2,\"quantity\":50000}'),
(208, 7, 'create', 'requests', '2025-11-27 11:43:15', '{\"batch_id\":26,\"request_id\":46,\"item_id\":5,\"quantity\":5}'),
(209, 7, 'create', 'requests', '2025-11-27 11:43:15', '{\"batch_id\":26,\"request_id\":47,\"item_id\":6,\"quantity\":100}'),
(210, 7, 'create', 'requests', '2025-11-27 11:43:15', '{\"batch_id\":26,\"request_id\":48,\"item_id\":4,\"quantity\":50}'),
(211, 7, 'create', 'requests', '2025-11-27 11:43:15', '{\"batch_id\":26,\"request_id\":49,\"item_id\":3,\"quantity\":250}'),
(212, 3, 'login', 'auth', '2025-11-27 11:45:51', '{\"email\":\"makiemorales2@gmail.com\"}'),
(213, 3, 'approve', 'requests', '2025-11-27 12:14:36', '{\"batch_id\":26,\"next_stage\":\"To Prepare\"}'),
(214, 3, 'distribute', 'requests', '2025-11-27 12:21:27', '{\"batch_id\":26}'),
(215, 7, 'logout', 'auth', '2025-11-27 12:30:05', '[]'),
(216, 3, 'logout', 'auth', '2025-11-27 12:35:24', '[]'),
(217, 3, 'login', 'auth', '2025-11-27 12:36:19', '{\"email\":\"makiemorales2@gmail.com\"}'),
(218, 3, 'create', 'purchases', '2025-11-27 12:37:11', '{\"purchase_id\":47,\"item_id\":2,\"quantity\":100000,\"cost\":6000,\"payment_type\":\"Card\",\"base_amount\":0}'),
(219, 7, 'login', 'auth', '2025-11-27 12:45:01', '{\"email\":\"kitchen@demo.local\"}'),
(220, 7, 'create', 'requests', '2025-11-27 13:01:13', '{\"batch_id\":27,\"request_id\":50,\"item_id\":3,\"quantity\":5000}'),
(221, 3, 'approve', 'requests', '2025-11-27 13:01:48', '{\"batch_id\":27,\"next_stage\":\"To Prepare\"}'),
(222, 3, 'create', 'deliveries', '2025-11-27 13:09:57', '{\"delivery_id\":15,\"purchase_id\":47,\"quantity_received\":100000}'),
(223, 7, 'create', 'requests', '2025-11-27 13:10:30', '{\"batch_id\":28,\"request_id\":51,\"item_id\":4,\"quantity\":2000}'),
(224, 3, 'approve', 'requests', '2025-11-27 13:15:22', '{\"batch_id\":28,\"next_stage\":\"To Prepare\"}'),
(225, 6, 'login', 'auth', '2025-11-27 15:48:23', '{\"email\":\"purchaser@demo.local\"}'),
(226, 7, 'logout', 'auth', '2025-11-27 15:52:34', '[]'),
(227, 6, 'create', 'purchases', '2025-11-27 15:58:51', '{\"purchase_id\":48,\"item_id\":1,\"quantity\":25000,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(228, 6, 'logout', 'auth', '2025-11-27 16:08:41', '[]'),
(229, 3, 'login', 'auth', '2025-11-27 16:09:39', '{\"email\":\"makiemorales2@gmail.com\"}'),
(230, 3, 'logout', 'auth', '2025-11-27 16:14:41', '[]'),
(231, 3, 'login', 'auth', '2025-11-27 16:25:14', '{\"email\":\"makiemorales2@gmail.com\"}'),
(232, 3, 'logout', 'auth', '2025-11-27 16:25:21', '[]'),
(233, 3, 'login', 'auth', '2025-11-27 16:29:58', '{\"email\":\"makiemorales2@gmail.com\"}'),
(234, 3, 'create', 'requests', '2025-11-27 16:30:10', '{\"batch_id\":29,\"request_id\":52,\"item_id\":2,\"quantity\":2000}'),
(235, 3, 'reject', 'requests', '2025-11-27 16:30:30', '{\"batch_id\":29}'),
(236, 3, 'logout', 'auth', '2025-11-27 16:32:01', '[]'),
(237, 4, 'login', 'auth', '2025-11-27 16:51:58', '{\"email\":\"manager@demo.local\"}'),
(238, 4, 'logout', 'auth', '2025-11-27 16:58:20', '[]'),
(239, 3, 'login', 'auth', '2025-11-27 16:58:33', '{\"email\":\"makiemorales2@gmail.com\"}'),
(240, 3, 'login', 'auth', '2025-11-27 21:46:33', '{\"email\":\"makiemorales2@gmail.com\"}'),
(241, 4, 'login', 'auth', '2025-11-28 09:58:25', '{\"email\":\"manager@demo.local\"}'),
(242, 4, 'logout', 'auth', '2025-11-28 12:17:23', '[]'),
(243, 3, 'login', 'auth', '2025-11-28 12:18:16', '{\"email\":\"makiemorales2@gmail.com\"}'),
(244, 3, 'logout', 'auth', '2025-11-28 12:36:08', '[]'),
(245, 3, 'login', 'auth', '2025-11-28 12:52:31', '{\"email\":\"makiemorales2@gmail.com\"}'),
(246, 3, 'logout', 'auth', '2025-11-28 13:04:15', '[]'),
(247, 3, 'login', 'auth', '2025-11-29 22:47:38', '{\"email\":\"makiemorales2@gmail.com\"}'),
(248, 3, 'create', 'ingredients', '2025-11-29 22:56:48', '{\"ingredient_id\":7,\"name\":\"brown sugar\"}'),
(249, 3, 'create', 'ingredients', '2025-11-29 22:58:03', '{\"ingredient_id\":8,\"name\":\"white sugar\"}'),
(250, 3, 'login', 'auth', '2025-11-30 08:02:43', '{\"email\":\"makiemorales2@gmail.com\"}'),
(251, 3, 'create', 'purchases', '2025-11-30 15:38:10', '{\"purchase_id\":49,\"item_id\":7,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0}'),
(252, 3, 'create', 'purchases', '2025-11-30 15:38:10', '{\"purchase_id\":50,\"item_id\":7,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0}'),
(253, 3, 'create', 'purchases', '2025-11-30 15:39:15', '{\"purchase_id\":51,\"item_id\":8,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(254, 3, 'create', 'purchases', '2025-11-30 15:40:15', '{\"purchase_id\":52,\"item_id\":7,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0}'),
(255, 3, 'create', 'purchases', '2025-11-30 15:40:49', '{\"purchase_id\":53,\"item_id\":8,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(256, 3, 'create', 'deliveries', '2025-11-30 15:41:15', '{\"delivery_id\":16,\"purchase_id\":53,\"quantity_received\":50000}'),
(257, 3, 'create', 'deliveries', '2025-11-30 15:41:19', '{\"delivery_id\":17,\"purchase_id\":52,\"quantity_received\":50000}'),
(258, 3, 'create', 'requests', '2025-11-30 17:37:52', '{\"batch_id\":30,\"summary\":\"balbacua\\r\\nset pack to pack\\r\\n2x rim\"}'),
(259, 3, 'approve', 'requests', '2025-11-30 17:38:35', '{\"batch_id\":30,\"next_stage\":\"To Prepare\"}'),
(260, 3, 'prepare', 'requests', '2025-11-30 17:39:52', '{\"batch_id\":30,\"items\":2}'),
(261, 3, 'distribute', 'requests', '2025-11-30 17:39:52', '{\"batch_id\":30}'),
(262, 3, 'create', 'requests', '2025-11-30 22:08:54', '{\"batch_id\":31,\"summary\":\"talong\\r\\nokra\\r\\nmani\",\"requester_name\":\"loren\"}'),
(263, 3, 'logout', 'auth', '2025-11-30 23:28:02', '[]'),
(264, 3, 'login', 'auth', '2025-11-30 23:28:21', '{\"email\":\"makiemorales2@gmail.com\"}'),
(265, 3, 'logout', 'auth', '2025-11-30 23:28:45', '[]'),
(266, 7, 'login', 'auth', '2025-11-30 23:29:05', '{\"email\":\"kitchen@demo.local\"}'),
(267, 7, 'logout', 'auth', '2025-11-30 23:29:23', '[]'),
(268, 3, 'login', 'auth', '2025-11-30 23:30:40', '{\"email\":\"makiemorales2@gmail.com\"}'),
(269, 3, 'logout', 'auth', '2025-11-30 23:32:45', '[]'),
(270, 3, 'login', 'auth', '2025-12-01 08:41:32', '{\"email\":\"makiemorales2@gmail.com\"}'),
(271, 3, 'logout', 'auth', '2025-12-01 08:41:41', '[]'),
(272, 3, 'login', 'auth', '2025-12-01 08:41:48', '{\"email\":\"makiemorales2@gmail.com\"}'),
(273, 3, 'create', 'requests', '2025-12-01 09:43:22', '{\"batch_id\":32,\"summary\":\"set balba\\r\\n2 pack spag\\r\\n5killo flour\",\"requester_name\":\"loren\"}'),
(274, 3, 'logout', 'auth', '2025-12-01 09:43:52', '[]'),
(275, 5, 'login', 'auth', '2025-12-01 09:44:08', '{\"email\":\"stock@demo.local\"}'),
(276, 5, 'logout', 'auth', '2025-12-01 09:45:01', '[]'),
(277, 3, 'login', 'auth', '2025-12-01 09:45:06', '{\"email\":\"makiemorales2@gmail.com\"}'),
(278, 3, 'logout', 'auth', '2025-12-01 09:48:15', '[]'),
(279, 7, 'login', 'auth', '2025-12-01 09:48:28', '{\"email\":\"kitchen@demo.local\"}'),
(280, 7, 'logout', 'auth', '2025-12-01 09:48:59', '[]'),
(281, 5, 'login', 'auth', '2025-12-01 09:49:09', '{\"email\":\"stock@demo.local\"}'),
(282, 5, 'approve', 'requests', '2025-12-01 09:50:33', '{\"batch_id\":32,\"next_stage\":\"To Prepare\"}'),
(283, 5, 'logout', 'auth', '2025-12-01 09:50:47', '[]'),
(284, 7, 'login', 'auth', '2025-12-01 09:50:59', '{\"email\":\"kitchen@demo.local\"}'),
(285, 7, 'create', 'requests', '2025-12-01 09:51:35', '{\"batch_id\":33,\"summary\":\"2xdono\\r\\n3x\\r\\n10x\",\"requester_name\":\"makie\"}'),
(286, 7, 'logout', 'auth', '2025-12-01 09:54:17', '[]'),
(287, 3, 'login', 'auth', '2025-12-01 09:54:25', '{\"email\":\"makiemorales2@gmail.com\"}'),
(288, 3, 'logout', 'auth', '2025-12-01 09:54:29', '[]'),
(289, 5, 'login', 'auth', '2025-12-01 09:54:44', '{\"email\":\"stock@demo.local\"}'),
(290, 5, 'approve', 'requests', '2025-12-01 09:56:14', '{\"batch_id\":33,\"next_stage\":\"To Prepare\"}'),
(291, 5, 'logout', 'auth', '2025-12-01 09:57:03', '[]'),
(292, 7, 'login', 'auth', '2025-12-01 09:57:14', '{\"email\":\"kitchen@demo.local\"}'),
(293, 7, 'logout', 'auth', '2025-12-01 09:57:31', '[]'),
(294, 5, 'login', 'auth', '2025-12-01 09:59:24', '{\"email\":\"stock@demo.local\"}'),
(295, 5, 'logout', 'auth', '2025-12-01 10:00:34', '[]'),
(296, 3, 'login', 'auth', '2025-12-01 10:00:41', '{\"email\":\"makiemorales2@gmail.com\"}'),
(297, 3, 'logout', 'auth', '2025-12-01 10:21:56', '[]'),
(298, 5, 'login', 'auth', '2025-12-01 10:22:01', '{\"email\":\"stock@demo.local\"}'),
(299, 5, 'prepare', 'requests', '2025-12-01 10:22:37', '{\"batch_id\":33,\"items\":2}'),
(300, 5, 'distribute', 'requests', '2025-12-01 10:22:37', '{\"batch_id\":33}'),
(301, 3, 'login', 'auth', '2025-12-01 10:27:05', '{\"email\":\"makiemorales2@gmail.com\"}'),
(302, 3, 'logout', 'auth', '2025-12-01 10:32:40', '[]'),
(303, 5, 'login', 'auth', '2025-12-01 10:32:50', '{\"email\":\"stock@demo.local\"}'),
(304, 5, 'logout', 'auth', '2025-12-01 10:34:01', '[]'),
(305, 7, 'login', 'auth', '2025-12-01 10:36:12', '{\"email\":\"kitchen@demo.local\"}'),
(306, 5, 'login', 'auth', '2025-12-01 10:40:35', '{\"email\":\"stock@demo.local\"}'),
(307, 7, 'logout', 'auth', '2025-12-01 10:42:18', '[]'),
(308, 5, 'login', 'auth', '2025-12-01 10:42:26', '{\"email\":\"stock@demo.local\"}'),
(309, 7, 'login', 'auth', '2025-12-01 11:17:03', '{\"email\":\"kitchen@demo.local\"}'),
(310, 7, 'create', 'requests', '2025-12-01 11:17:43', '{\"batch_id\":34,\"summary\":\"2 set pack of jetpack\\r\\n2 set balaba\\r\\n5 kg flour\",\"requester_name\":\"loren\"}'),
(311, 5, 'login', 'auth', '2025-12-01 11:18:40', '{\"email\":\"stock@demo.local\"}'),
(312, 5, 'approve', 'requests', '2025-12-01 11:20:32', '{\"batch_id\":34,\"next_stage\":\"To Prepare\"}'),
(313, 5, 'prepare', 'requests', '2025-12-01 11:26:01', '{\"batch_id\":34,\"items\":2}'),
(314, 5, 'logout', 'auth', '2025-12-01 11:45:37', '[]'),
(315, 6, 'login', 'auth', '2025-12-01 11:45:48', '{\"email\":\"purchaser@demo.local\"}'),
(316, 6, 'create', 'purchases', '2025-12-01 11:48:03', '{\"purchase_id\":54,\"item_id\":7,\"quantity\":50000,\"cost\":2800,\"payment_type\":\"Cash\",\"base_amount\":6000}'),
(317, 6, 'create', 'purchases', '2025-12-01 11:48:03', '{\"purchase_id\":55,\"item_id\":8,\"quantity\":50000,\"cost\":2900,\"payment_type\":\"Cash\",\"base_amount\":6000}'),
(318, 7, 'logout', 'auth', '2025-12-01 11:49:57', '[]'),
(319, 3, 'login', 'auth', '2025-12-01 11:50:02', '{\"email\":\"makiemorales2@gmail.com\"}'),
(320, 6, 'logout', 'auth', '2025-12-01 12:01:26', '[]'),
(321, 5, 'login', 'auth', '2025-12-01 12:01:40', '{\"email\":\"stock@demo.local\"}'),
(322, 5, 'create', 'deliveries', '2025-12-01 12:04:49', '{\"delivery_id\":18,\"purchase_id\":54,\"quantity_received\":50000}'),
(323, 5, 'create', 'deliveries', '2025-12-01 12:04:49', '{\"delivery_id\":19,\"purchase_id\":55,\"quantity_received\":50000}'),
(324, 5, 'create', 'ingredients', '2025-12-01 12:21:50', '{\"ingredient_id\":9,\"name\":\"flour\"}'),
(325, 5, 'prepare', 'requests', '2025-12-01 12:25:30', '{\"batch_id\":34,\"items\":2}'),
(326, 5, 'distribute', 'requests', '2025-12-01 12:25:30', '{\"batch_id\":34}'),
(327, 3, 'logout', 'auth', '2025-12-01 12:25:37', '[]'),
(328, 7, 'login', 'auth', '2025-12-01 12:25:51', '{\"email\":\"kitchen@demo.local\"}'),
(329, 7, 'logout', 'auth', '2025-12-01 12:27:10', '[]'),
(330, 5, 'create', 'ingredients', '2025-12-01 12:48:12', '{\"ingredient_id\":10,\"name\":\"nature spring\"}'),
(331, 5, 'create', 'purchases', '2025-12-01 12:48:42', '{\"purchase_id\":56,\"item_id\":10,\"quantity\":24,\"cost\":400,\"payment_type\":\"Card\",\"base_amount\":0}'),
(332, 5, 'create', 'ingredients', '2025-12-01 13:04:54', '{\"ingredient_id\":11,\"name\":\"Nature Spring (L)\"}'),
(333, 5, 'logout', 'auth', '2025-12-01 13:31:00', '[]'),
(334, 5, 'login', 'auth', '2025-12-01 13:32:17', '{\"email\":\"stock@demo.local\"}'),
(335, 5, 'create', 'ingredients', '2025-12-01 13:33:55', '{\"ingredient_id\":12,\"name\":\"sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(336, 5, 'create', 'purchases', '2025-12-01 13:33:55', '{\"purchase_id\":57,\"item_id\":12,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0}'),
(337, 5, 'create', 'ingredients', '2025-12-01 13:33:55', '{\"ingredient_id\":13,\"name\":\"white sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(338, 5, 'create', 'purchases', '2025-12-01 13:33:55', '{\"purchase_id\":58,\"item_id\":13,\"quantity\":1,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0}'),
(339, 5, 'create', 'ingredients', '2025-12-01 13:33:55', '{\"ingredient_id\":14,\"name\":\"flour\",\"unit\":\"kg\",\"auto_created\":true}'),
(340, 5, 'create', 'purchases', '2025-12-01 13:33:55', '{\"purchase_id\":59,\"item_id\":14,\"quantity\":25,\"cost\":1200,\"payment_type\":\"Card\",\"base_amount\":0}'),
(341, 6, 'login', 'auth', '2025-12-01 13:35:12', '{\"email\":\"purchaser@demo.local\"}'),
(342, 5, 'create', 'ingredients', '2025-12-01 13:53:45', '{\"ingredient_id\":15,\"name\":\"brown sugar\"}'),
(343, 5, 'create', 'purchases', '2025-12-01 14:02:54', '{\"purchase_id\":60,\"item_id\":15,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0}'),
(344, 5, 'create', 'purchases', '2025-12-01 14:15:01', '{\"purchase_id\":61,\"item_id\":15,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(345, 5, 'create', 'purchases', '2025-12-01 14:25:39', '{\"purchase_id\":62,\"item_id\":15,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(346, 6, 'create', 'purchases', '2025-12-01 14:32:05', '{\"purchase_id\":63,\"item_id\":15,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(347, 5, 'create', 'deliveries', '2025-12-01 14:40:11', '{\"delivery_id\":20,\"purchase_id\":63,\"quantity_received\":1}'),
(348, 5, 'create', 'deliveries', '2025-12-01 14:48:30', '{\"delivery_id\":21,\"purchase_id\":63,\"quantity_received\":1}'),
(349, 5, 'create', 'deliveries', '2025-12-01 15:05:13', '{\"delivery_id\":22,\"purchase_id\":63,\"quantity_received\":50000}'),
(350, 5, 'create', 'ingredients', '2025-12-01 15:10:38', '{\"ingredient_id\":16,\"name\":\"brown sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(351, 5, 'create', 'purchases', '2025-12-01 15:10:38', '{\"purchase_id\":64,\"item_id\":16,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(352, 6, 'logout', 'auth', '2025-12-01 15:17:32', '[]'),
(353, 7, 'login', 'auth', '2025-12-01 15:17:52', '{\"email\":\"kitchen@demo.local\"}'),
(354, 7, 'create', 'requests', '2025-12-01 15:18:42', '{\"batch_id\":35,\"summary\":\"2 set pack of jetpack\\r\\n2 set balba\\r\\n5 kg flour\",\"requester_name\":\"loren\"}'),
(355, 5, 'approve', 'requests', '2025-12-01 15:19:18', '{\"batch_id\":35,\"next_stage\":\"To Prepare\"}'),
(356, 5, 'create', 'deliveries', '2025-12-01 15:30:10', '{\"delivery_id\":23,\"purchase_id\":64,\"quantity_received\":50000}'),
(357, 5, 'create', 'ingredients', '2025-12-01 16:02:33', '{\"ingredient_id\":17,\"name\":\"magic sarap (L)\"}'),
(358, 5, 'create', 'ingredients', '2025-12-01 16:07:06', '{\"ingredient_id\":18,\"name\":\"sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(359, 5, 'create', 'purchases', '2025-12-01 16:07:06', '{\"purchase_id\":65,\"item_id\":18,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(360, 5, 'create', 'ingredients', '2025-12-01 16:11:32', '{\"ingredient_id\":19,\"name\":\"brown sugar\"}'),
(361, 5, 'create', 'purchases', '2025-12-01 16:15:08', '{\"purchase_id\":66,\"item_id\":19,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(362, 5, 'create', 'deliveries', '2025-12-01 16:16:02', '{\"delivery_id\":24,\"purchase_id\":66,\"quantity_received\":50000}'),
(363, 7, 'logout', 'auth', '2025-12-01 16:30:28', '[]'),
(364, 5, 'login', 'auth', '2025-12-01 16:30:56', '{\"email\":\"stock@demo.local\"}'),
(365, 5, 'create', 'ingredients', '2025-12-01 16:48:18', '{\"ingredient_id\":20,\"name\":\"lebrown sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(366, 5, 'create', 'purchases', '2025-12-01 16:48:18', '{\"purchase_id\":67,\"item_id\":20,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(367, 5, 'create', 'ingredients', '2025-12-01 17:38:20', '{\"ingredient_id\":21,\"name\":\"lobrawn\",\"unit\":\"sack\",\"auto_created\":true}'),
(368, 5, 'create', 'purchases', '2025-12-01 17:38:20', '{\"purchase_id\":68,\"item_id\":21,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(369, 5, 'create', 'ingredients', '2025-12-01 18:07:15', '{\"ingredient_id\":22,\"name\":\"brown sugar\"}'),
(370, 5, 'create', 'ingredients', '2025-12-01 18:07:43', '{\"ingredient_id\":23,\"name\":\"lebrown sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(371, 5, 'create', 'purchases', '2025-12-01 18:07:43', '{\"purchase_id\":69,\"item_id\":23,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(372, 5, 'create', 'purchases', '2025-12-01 19:20:55', '{\"purchase_id\":70,\"item_id\":22,\"quantity\":50000,\"auto_created_for_restock\":true}'),
(373, 5, 'create', 'deliveries', '2025-12-01 19:20:55', '{\"delivery_id\":25,\"purchase_id\":70,\"quantity_received\":50000}'),
(374, 5, 'create', 'ingredients', '2025-12-01 19:23:09', '{\"ingredient_id\":24,\"name\":\"lebrown\",\"unit\":\"sack\",\"auto_created\":true}'),
(375, 5, 'create', 'purchases', '2025-12-01 19:23:09', '{\"purchase_id\":71,\"item_id\":24,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(376, 5, 'create', 'deliveries', '2025-12-01 19:23:57', '{\"delivery_id\":26,\"purchase_id\":71,\"quantity_received\":50}'),
(377, 5, 'create', 'ingredients', '2025-12-01 19:30:49', '{\"ingredient_id\":25,\"name\":\"loray sugar\",\"unit\":\"sack\",\"auto_created\":true}'),
(378, 5, 'create', 'purchases', '2025-12-01 19:30:49', '{\"purchase_id\":72,\"item_id\":25,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(379, 5, 'create', 'ingredients', '2025-12-01 19:38:14', '{\"ingredient_id\":26,\"name\":\"Brown sugar\"}'),
(380, 5, 'create', 'ingredients', '2025-12-01 19:38:54', '{\"ingredient_id\":27,\"name\":\"brawn sogar\",\"unit\":\"sack\",\"auto_created\":true}'),
(381, 5, 'create', 'purchases', '2025-12-01 19:38:54', '{\"purchase_id\":73,\"item_id\":27,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(382, 5, 'create', 'ingredients', '2025-12-01 19:54:27', '{\"ingredient_id\":28,\"name\":\"brawn sogar\",\"unit\":\"sack\",\"auto_created\":true}'),
(383, 5, 'create', 'purchases', '2025-12-01 19:54:27', '{\"purchase_id\":74,\"item_id\":28,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(384, 5, 'create', 'ingredients', '2025-12-01 20:01:23', '{\"ingredient_id\":29,\"name\":\"Brown Sugar\"}'),
(385, 5, 'create', 'ingredients', '2025-12-01 20:04:06', '{\"ingredient_id\":30,\"name\":\"brawn sogar\",\"unit\":\"sack\",\"auto_created\":true}'),
(386, 5, 'create', 'purchases', '2025-12-01 20:04:06', '{\"purchase_id\":75,\"item_id\":30,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(387, 5, 'create', 'ingredients', '2025-12-01 20:12:53', '{\"ingredient_id\":31,\"name\":\"brown sugar\"}'),
(388, 5, 'create', 'ingredients', '2025-12-01 20:13:20', '{\"ingredient_id\":32,\"name\":\"lownhasd\",\"unit\":\"sack\",\"auto_created\":true}'),
(389, 5, 'create', 'purchases', '2025-12-01 20:13:20', '{\"purchase_id\":76,\"item_id\":32,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(390, 5, 'create', 'ingredients', '2025-12-01 20:20:00', '{\"ingredient_id\":33,\"name\":\"asdasdasd\",\"unit\":\"sack\",\"auto_created\":true}'),
(391, 5, 'create', 'purchases', '2025-12-01 20:20:00', '{\"purchase_id\":77,\"item_id\":33,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(392, 5, 'create', 'ingredients', '2025-12-01 21:13:56', '{\"ingredient_id\":34,\"name\":\"Brown Sugar\"}'),
(393, 5, 'create', 'ingredients', '2025-12-01 21:14:20', '{\"ingredient_id\":35,\"name\":\"asdasd\",\"unit\":\"sack\",\"auto_created\":true}'),
(394, 5, 'create', 'purchases', '2025-12-01 21:14:20', '{\"purchase_id\":78,\"item_id\":35,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(395, 5, 'create', 'ingredients', '2025-12-01 22:22:37', '{\"ingredient_id\":36,\"name\":\"asdasd\",\"unit\":\"sack\",\"auto_created\":true,\"in_inventory\":false}'),
(396, 5, 'create', 'purchases', '2025-12-01 22:22:37', '{\"purchase_id\":79,\"item_id\":36,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(397, 5, 'create', 'purchases', '2025-12-01 22:23:08', '{\"purchase_id\":80,\"item_id\":34,\"quantity\":50000,\"auto_created_for_restock\":true}'),
(398, 5, 'create', 'deliveries', '2025-12-01 22:23:08', '{\"delivery_id\":27,\"purchase_id\":80,\"quantity_received\":50000}'),
(399, 5, 'create', 'ingredients', '2025-12-01 22:28:23', '{\"ingredient_id\":37,\"name\":\"balow\",\"unit\":\"sack\",\"auto_created\":true,\"in_inventory\":false}'),
(400, 5, 'create', 'purchases', '2025-12-01 22:28:23', '{\"purchase_id\":81,\"item_id\":37,\"quantity\":1,\"cost\":2900,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"sack\",\"purchase_quantity\":1}'),
(401, 5, 'create', 'purchases', '2025-12-01 22:35:26', '{\"purchase_id\":82,\"item_id\":38,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"asdasd\",\"purchase_quantity\":1}'),
(402, 5, 'create', 'purchases', '2025-12-01 22:55:29', '{\"purchase_id\":83,\"item_id\":38,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"asdasdsadasdsad\",\"purchase_quantity\":1}'),
(403, 5, 'create', 'purchases', '2025-12-01 22:56:47', '{\"purchase_id\":84,\"item_id\":34,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"asdasdasdsa2222\",\"purchase_quantity\":1}'),
(404, 5, 'create', 'purchases', '2025-12-01 23:19:50', '{\"purchase_id\":85,\"item_id\":34,\"quantity\":1,\"cost\":2400,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"asdasd (asdasd (sack))\",\"purchase_quantity\":1}'),
(405, 5, 'create', 'purchases', '2025-12-01 23:25:38', '{\"purchase_id\":86,\"item_id\":34,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"dsfsdf|dsfsdf (sack)\",\"purchase_quantity\":1}'),
(406, 5, 'create', 'purchases', '2025-12-01 23:31:48', '{\"purchase_id\":87,\"item_id\":34,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"asdasd|sack\",\"purchase_quantity\":1}'),
(407, 5, 'create', 'deliveries', '2025-12-01 23:59:04', '{\"delivery_id\":28,\"purchase_id\":87,\"quantity_received\":50000}'),
(408, 5, 'logout', 'auth', '2025-12-02 00:08:56', '[]'),
(409, 3, 'login', 'auth', '2025-12-02 00:09:07', '{\"email\":\"makiemorales2@gmail.com\"}'),
(410, 5, 'login', 'auth', '2025-12-02 08:03:45', '{\"email\":\"stock@demo.local\"}'),
(411, 3, 'login', 'auth', '2025-12-02 08:04:58', '{\"email\":\"makiemorales2@gmail.com\"}'),
(412, 5, 'create', 'purchases', '2025-12-02 08:23:23', '{\"purchase_id\":88,\"item_id\":34,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"B sugar|sack\",\"purchase_quantity\":1}'),
(413, 5, 'create', 'deliveries', '2025-12-02 08:24:36', '{\"delivery_id\":29,\"purchase_id\":88,\"quantity_received\":50000}'),
(414, 3, 'logout', 'auth', '2025-12-02 09:56:02', '[]'),
(415, 7, 'login', 'auth', '2025-12-02 09:56:11', '{\"email\":\"kitchen@demo.local\"}'),
(416, 7, 'create', 'requests', '2025-12-02 09:56:52', '{\"batch_id\":36,\"summary\":\"chats2x\\r\\nshoot2x\",\"requester_name\":\"makie\"}'),
(417, 5, 'reject', 'requests', '2025-12-02 09:57:24', '{\"batch_id\":36}'),
(418, 7, 'create', 'requests', '2025-12-02 10:01:05', '{\"batch_id\":37,\"summary\":\"asdasdasds\",\"requester_name\":\"asdasd\"}'),
(419, 5, 'reject', 'requests', '2025-12-02 10:01:13', '{\"batch_id\":37}'),
(420, 7, 'create', 'requests', '2025-12-02 10:06:14', '{\"batch_id\":38,\"summary\":\"asdasd\",\"requester_name\":\"asdasdasd\"}'),
(421, 7, 'update', 'requests', '2025-12-02 10:06:50', '{\"batch_id\":38,\"summary\":\"asdasdasd\",\"requester_name\":\"2343243\"}'),
(422, 7, 'update', 'requests', '2025-12-02 10:07:18', '{\"batch_id\":38,\"summary\":\"11111\",\"requester_name\":\"2dfgdfgdfgllalpo\"}'),
(423, 5, 'prepare', 'requests', '2025-12-02 10:08:36', '{\"batch_id\":35,\"items\":1}'),
(424, 5, 'distribute', 'requests', '2025-12-02 10:08:36', '{\"batch_id\":35}'),
(425, 5, 'approve', 'requests', '2025-12-02 10:09:23', '{\"batch_id\":38,\"next_stage\":\"To Prepare\"}'),
(426, 5, 'prepare', 'requests', '2025-12-02 10:09:36', '{\"batch_id\":38,\"items\":1}'),
(427, 5, 'distribute', 'requests', '2025-12-02 10:09:36', '{\"batch_id\":38}'),
(428, 7, 'create', 'requests', '2025-12-02 10:21:38', '{\"batch_id\":39,\"summary\":\"5 kg sugar\",\"requester_name\":\"makie\"}'),
(429, 5, 'approve', 'requests', '2025-12-02 10:22:15', '{\"batch_id\":39,\"next_stage\":\"To Prepare\"}'),
(430, 5, 'prepare', 'requests', '2025-12-02 10:22:45', '{\"batch_id\":39,\"items\":1}'),
(431, 5, 'prepare', 'requests', '2025-12-02 10:23:32', '{\"batch_id\":39,\"items\":1}');
INSERT INTO `audit_log` (`id`, `user_id`, `action`, `module`, `timestamp`, `details`) VALUES
(432, 5, 'create', 'purchases', '2025-12-02 10:24:17', '{\"purchase_id\":89,\"item_id\":34,\"quantity\":1,\"cost\":2800,\"payment_type\":\"Card\",\"base_amount\":0,\"purchase_unit\":\"B Sugar|sack\",\"purchase_quantity\":1}'),
(433, 5, 'create', 'deliveries', '2025-12-02 10:25:06', '{\"delivery_id\":30,\"purchase_id\":89,\"quantity_received\":50000}'),
(434, 5, 'prepare', 'requests', '2025-12-02 10:26:32', '{\"batch_id\":39,\"items\":1}'),
(435, 5, 'distribute', 'requests', '2025-12-02 10:26:32', '{\"batch_id\":39}'),
(436, 7, 'create', 'requests', '2025-12-02 10:26:49', '{\"batch_id\":40,\"summary\":\"asdasdsa\",\"requester_name\":\"loren\"}'),
(437, 5, 'approve', 'requests', '2025-12-02 10:27:45', '{\"batch_id\":40,\"next_stage\":\"To Prepare\"}'),
(438, 5, 'prepare', 'requests', '2025-12-02 10:27:58', '{\"batch_id\":40,\"items\":1}'),
(439, 5, 'distribute', 'requests', '2025-12-02 10:27:58', '{\"batch_id\":40}'),
(440, 7, 'create', 'requests', '2025-12-02 10:32:02', '{\"batch_id\":41,\"summary\":\"sadasd\",\"requester_name\":\"sadasd\"}'),
(441, 5, 'approve', 'requests', '2025-12-02 10:32:06', '{\"batch_id\":41,\"next_stage\":\"To Prepare\"}'),
(442, 5, 'prepare', 'requests', '2025-12-02 10:33:40', '{\"batch_id\":41,\"items\":1}'),
(443, 5, 'distribute', 'requests', '2025-12-02 10:33:40', '{\"batch_id\":41}'),
(444, 7, 'create', 'requests', '2025-12-02 10:34:50', '{\"batch_id\":42,\"summary\":\"asdasd\",\"requester_name\":\"asdasd\"}'),
(445, 5, 'approve', 'requests', '2025-12-02 10:34:55', '{\"batch_id\":42,\"next_stage\":\"To Prepare\"}'),
(446, 5, 'prepare', 'requests', '2025-12-02 10:35:08', '{\"batch_id\":42,\"items\":1}'),
(447, 5, 'distribute', 'requests', '2025-12-02 10:35:08', '{\"batch_id\":42}'),
(448, 7, 'create', 'requests', '2025-12-02 10:38:59', '{\"batch_id\":43,\"summary\":\"asdasdsa\",\"requester_name\":\"asdsad\"}'),
(449, 5, 'approve', 'requests', '2025-12-02 10:39:05', '{\"batch_id\":43,\"next_stage\":\"To Prepare\"}'),
(450, 5, 'prepare', 'requests', '2025-12-02 10:39:53', '{\"batch_id\":43,\"items\":1}'),
(451, 5, 'distribute', 'requests', '2025-12-02 10:39:53', '{\"batch_id\":43}'),
(452, 7, 'create', 'requests', '2025-12-02 10:40:09', '{\"batch_id\":44,\"summary\":\"asdsad\",\"requester_name\":\"asdasd\"}'),
(453, 5, 'approve', 'requests', '2025-12-02 10:40:13', '{\"batch_id\":44,\"next_stage\":\"To Prepare\"}'),
(454, 5, 'prepare', 'requests', '2025-12-02 10:40:41', '{\"batch_id\":44,\"items\":1}'),
(455, 5, 'distribute', 'requests', '2025-12-02 10:40:41', '{\"batch_id\":44}'),
(456, 7, 'create', 'requests', '2025-12-02 10:48:18', '{\"batch_id\":45,\"summary\":\"asdasd\",\"requester_name\":\"asdasd\"}'),
(457, 5, 'approve', 'requests', '2025-12-02 10:48:29', '{\"batch_id\":45,\"next_stage\":\"To Prepare\"}'),
(458, 5, 'prepare', 'requests', '2025-12-02 10:48:46', '{\"batch_id\":45,\"items\":1}'),
(459, 5, 'distribute', 'requests', '2025-12-02 10:48:46', '{\"batch_id\":45}'),
(460, 7, 'create', 'requests', '2025-12-02 10:49:00', '{\"batch_id\":46,\"summary\":\"asdasd\",\"requester_name\":\"asdasd\"}'),
(461, 5, 'approve', 'requests', '2025-12-02 10:49:04', '{\"batch_id\":46,\"next_stage\":\"To Prepare\"}'),
(462, 5, 'prepare', 'requests', '2025-12-02 10:49:32', '{\"batch_id\":46,\"items\":1}'),
(463, 5, 'distribute', 'requests', '2025-12-02 10:49:32', '{\"batch_id\":46}'),
(464, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":39,\"name\":\"Mushroom Gravy\",\"quantity\":0,\"source\":\"csv_import\"}'),
(465, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":40,\"name\":\"Mushroom Cream\",\"quantity\":0,\"source\":\"csv_import\"}'),
(466, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":41,\"name\":\"Tomato Paste (S)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(467, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":42,\"name\":\"Tomato Sauce (115g)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(468, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":43,\"name\":\"Tomato Sauce (250g)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(469, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":44,\"name\":\"Ginisa Mix (250g)\",\"quantity\":1,\"source\":\"csv_import\"}'),
(470, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":45,\"name\":\"Adobo Mix (50g)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(471, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":46,\"name\":\"Palabok Mix\",\"quantity\":6,\"source\":\"csv_import\"}'),
(472, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":47,\"name\":\"Condense (Alaska/Cow Bell)\",\"quantity\":301,\"source\":\"csv_import\"}'),
(473, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":48,\"name\":\"Mineral Water\",\"quantity\":21,\"source\":\"csv_import\"}'),
(474, 5, 'update', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":34,\"name\":\"Brown Sugar\",\"quantity\":4,\"source\":\"csv_import\"}'),
(475, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":49,\"name\":\"White Sugar\",\"quantity\":37,\"source\":\"csv_import\"}'),
(476, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":50,\"name\":\"Pilit\",\"quantity\":0,\"source\":\"csv_import\"}'),
(477, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":51,\"name\":\"Asado Mix\",\"quantity\":27,\"source\":\"csv_import\"}'),
(478, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":52,\"name\":\"Nacho Chips\",\"quantity\":0,\"source\":\"csv_import\"}'),
(479, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":53,\"name\":\"Sausage\",\"quantity\":0,\"source\":\"csv_import\"}'),
(480, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":54,\"name\":\"Chiffon Mix\",\"quantity\":3,\"source\":\"csv_import\"}'),
(481, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":55,\"name\":\"Spaghetti Spice Mix\",\"quantity\":2,\"source\":\"csv_import\"}'),
(482, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":56,\"name\":\"Brownies\",\"quantity\":9,\"source\":\"csv_import\"}'),
(483, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":57,\"name\":\"16 oz. Cup\",\"quantity\":0,\"source\":\"csv_import\"}'),
(484, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":58,\"name\":\"Ice Cream\",\"quantity\":0,\"source\":\"csv_import\"}'),
(485, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":59,\"name\":\"Choco Flour\",\"quantity\":0,\"source\":\"csv_import\"}'),
(486, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":60,\"name\":\"Magnolia Gold\",\"quantity\":0,\"source\":\"csv_import\"}'),
(487, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":61,\"name\":\"Vana Blanca\",\"quantity\":0,\"source\":\"csv_import\"}'),
(488, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":62,\"name\":\"All-Purpose Flour\",\"quantity\":9,\"source\":\"csv_import\"}'),
(489, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":63,\"name\":\"Hersheys\",\"quantity\":13,\"source\":\"csv_import\"}'),
(490, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":64,\"name\":\"Cake Flour\",\"quantity\":10,\"source\":\"csv_import\"}'),
(491, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":65,\"name\":\"Iland\",\"quantity\":10,\"source\":\"csv_import\"}'),
(492, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":66,\"name\":\"Bravo Buttercream\",\"quantity\":39,\"source\":\"csv_import\"}'),
(493, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":67,\"name\":\"Cream Cheese\",\"quantity\":9,\"source\":\"csv_import\"}'),
(494, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":68,\"name\":\"Pleasure Gold\",\"quantity\":13,\"source\":\"csv_import\"}'),
(495, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":69,\"name\":\"Bunge\",\"quantity\":6,\"source\":\"csv_import\"}'),
(496, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":70,\"name\":\"Egg\",\"quantity\":468,\"source\":\"csv_import\"}'),
(497, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":71,\"name\":\"Mascobado\",\"quantity\":0,\"source\":\"csv_import\"}'),
(498, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":72,\"name\":\"Dari Crème\",\"quantity\":96,\"source\":\"csv_import\"}'),
(499, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":73,\"name\":\"Ube Flavor\",\"quantity\":0,\"source\":\"csv_import\"}'),
(500, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":74,\"name\":\"Chocolate Flavorade\",\"quantity\":0,\"source\":\"csv_import\"}'),
(501, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":75,\"name\":\"Blue Berry\",\"quantity\":1,\"source\":\"csv_import\"}'),
(502, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":76,\"name\":\"Rufina\",\"quantity\":3,\"source\":\"csv_import\"}'),
(503, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":77,\"name\":\"Cream Corn\",\"quantity\":0,\"source\":\"csv_import\"}'),
(504, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":78,\"name\":\"Mixed Mushroom\",\"quantity\":0,\"source\":\"csv_import\"}'),
(505, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":79,\"name\":\"Cream Mushroom\",\"quantity\":0,\"source\":\"csv_import\"}'),
(506, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":80,\"name\":\"Parmesan Cheese\",\"quantity\":0,\"source\":\"csv_import\"}'),
(507, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":81,\"name\":\"Parsley\",\"quantity\":0,\"source\":\"csv_import\"}'),
(508, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":82,\"name\":\"Basil Leaves\",\"quantity\":0,\"source\":\"csv_import\"}'),
(509, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":83,\"name\":\"Hot Sauce\",\"quantity\":0,\"source\":\"csv_import\"}'),
(510, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":84,\"name\":\"Grbanzos\",\"quantity\":1,\"source\":\"csv_import\"}'),
(511, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":85,\"name\":\"Gulaman Flavor\",\"quantity\":0,\"source\":\"csv_import\"}'),
(512, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":86,\"name\":\"Hoisin\",\"quantity\":1,\"source\":\"csv_import\"}'),
(513, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":87,\"name\":\"Food Color Red\",\"quantity\":1,\"source\":\"csv_import\"}'),
(514, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":88,\"name\":\"Cherries\",\"quantity\":2,\"source\":\"csv_import\"}'),
(515, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":89,\"name\":\"Ginamus\",\"quantity\":7,\"source\":\"csv_import\"}'),
(516, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":90,\"name\":\"Whole Corn\",\"quantity\":0,\"source\":\"csv_import\"}'),
(517, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":91,\"name\":\"Mongo Beans\",\"quantity\":1,\"source\":\"csv_import\"}'),
(518, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":92,\"name\":\"Magnolia Fresh Milk\",\"quantity\":2,\"source\":\"csv_import\"}'),
(519, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":93,\"name\":\"Mango Jam Small\",\"quantity\":0,\"source\":\"csv_import\"}'),
(520, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":94,\"name\":\"Fruita Mango Jam BIG\",\"quantity\":7,\"source\":\"csv_import\"}'),
(521, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":95,\"name\":\"Rice\",\"quantity\":26,\"source\":\"csv_import\"}'),
(522, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":96,\"name\":\"Berylis\",\"quantity\":2,\"source\":\"csv_import\"}'),
(523, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":97,\"name\":\"Bihon\",\"quantity\":11,\"source\":\"csv_import\"}'),
(524, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":98,\"name\":\"Royal\",\"quantity\":0,\"source\":\"csv_import\"}'),
(525, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":99,\"name\":\"Sprite\",\"quantity\":12,\"source\":\"csv_import\"}'),
(526, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":100,\"name\":\"Coke\",\"quantity\":156,\"source\":\"csv_import\"}'),
(527, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":101,\"name\":\"Spag Box\",\"quantity\":6,\"source\":\"csv_import\"}'),
(528, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":102,\"name\":\"Mami Noodles\",\"quantity\":0,\"source\":\"csv_import\"}'),
(529, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":103,\"name\":\"Ketchup\",\"quantity\":1,\"source\":\"csv_import\"}'),
(530, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":104,\"name\":\"Tuyo\",\"quantity\":1,\"source\":\"csv_import\"}'),
(531, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":105,\"name\":\"Knorr Seasoning\",\"quantity\":0,\"source\":\"csv_import\"}'),
(532, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":106,\"name\":\"Large Bag\",\"quantity\":6,\"source\":\"csv_import\"}'),
(533, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":107,\"name\":\"Fork Wood\",\"quantity\":20,\"source\":\"csv_import\"}'),
(534, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":108,\"name\":\"Shortening\",\"quantity\":2,\"source\":\"csv_import\"}'),
(535, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":109,\"name\":\"Whipping Cream\",\"quantity\":4,\"source\":\"csv_import\"}'),
(536, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":110,\"name\":\"Chicken Cubes\",\"quantity\":17,\"source\":\"csv_import\"}'),
(537, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":111,\"name\":\"Beef Cubes\",\"quantity\":24,\"source\":\"csv_import\"}'),
(538, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":112,\"name\":\"Spoon Wood\",\"quantity\":3,\"source\":\"csv_import\"}'),
(539, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":113,\"name\":\"Take out container\",\"quantity\":6,\"source\":\"csv_import\"}'),
(540, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":114,\"name\":\"Gulaman (Yellow)\",\"quantity\":16,\"source\":\"csv_import\"}'),
(541, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":115,\"name\":\"Gulaman (Red)\",\"quantity\":10,\"source\":\"csv_import\"}'),
(542, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":116,\"name\":\"Gulaman (White)\",\"quantity\":1,\"source\":\"csv_import\"}'),
(543, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":117,\"name\":\"Gulaman (Green)\",\"quantity\":20,\"source\":\"csv_import\"}'),
(544, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":118,\"name\":\"Gloves\",\"quantity\":2,\"source\":\"csv_import\"}'),
(545, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":119,\"name\":\"White Chocolate\",\"quantity\":7,\"source\":\"csv_import\"}'),
(546, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":120,\"name\":\"Dark Chocolate\",\"quantity\":5,\"source\":\"csv_import\"}'),
(547, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":121,\"name\":\"Grahams (200g)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(548, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":122,\"name\":\"Premium boxes\",\"quantity\":0,\"source\":\"csv_import\"}'),
(549, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":123,\"name\":\"1/2 Boxes\",\"quantity\":0,\"source\":\"csv_import\"}'),
(550, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":124,\"name\":\"1/4 Boxes\",\"quantity\":0,\"source\":\"csv_import\"}'),
(551, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":125,\"name\":\"Mini Boxes\",\"quantity\":0,\"source\":\"csv_import\"}'),
(552, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":126,\"name\":\"Board 11\",\"quantity\":0,\"source\":\"csv_import\"}'),
(553, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":127,\"name\":\"Cake Board 8\",\"quantity\":0,\"source\":\"csv_import\"}'),
(554, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":128,\"name\":\"menudo mix\",\"quantity\":0,\"source\":\"csv_import\"}'),
(555, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":129,\"name\":\"spag noodles\",\"quantity\":0,\"source\":\"csv_import\"}'),
(556, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":130,\"name\":\"tropicana\",\"quantity\":1,\"source\":\"csv_import\"}'),
(557, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":131,\"name\":\"surf powder\",\"quantity\":7,\"source\":\"csv_import\"}'),
(558, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":132,\"name\":\"cheese slice\",\"quantity\":2,\"source\":\"csv_import\"}'),
(559, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":133,\"name\":\"smart diswashing\",\"quantity\":1,\"source\":\"csv_import\"}'),
(560, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":134,\"name\":\"ice tea\",\"quantity\":1,\"source\":\"csv_import\"}'),
(561, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":135,\"name\":\"cont.sa kakanin\",\"quantity\":0,\"source\":\"csv_import\"}'),
(562, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":136,\"name\":\"beef feet for balbacua\",\"quantity\":30,\"source\":\"csv_import\"}'),
(563, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":137,\"name\":\"brown bag #35\",\"quantity\":1,\"source\":\"csv_import\"}'),
(564, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":138,\"name\":\"brown bag#20\",\"quantity\":3,\"source\":\"csv_import\"}'),
(565, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":139,\"name\":\"burger plastic\",\"quantity\":8,\"source\":\"csv_import\"}'),
(566, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":140,\"name\":\"Glacin\",\"quantity\":3,\"source\":\"csv_import\"}'),
(567, 5, 'create', 'ingredients', '2025-12-02 13:34:25', '{\"ingredient_id\":141,\"name\":\"Fondant big\",\"quantity\":1,\"source\":\"csv_import\"}'),
(568, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":142,\"name\":\"Fondant small\",\"quantity\":0,\"source\":\"csv_import\"}'),
(569, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":143,\"name\":\"Liver spread\",\"quantity\":1,\"source\":\"csv_import\"}'),
(570, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":144,\"name\":\"Lunga\",\"quantity\":1,\"source\":\"csv_import\"}'),
(571, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":145,\"name\":\"crushed graham\",\"quantity\":1,\"source\":\"csv_import\"}'),
(572, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":146,\"name\":\"Brown bag#16\",\"quantity\":3,\"source\":\"csv_import\"}'),
(573, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":147,\"name\":\"Brown bag # 4\",\"quantity\":2,\"source\":\"csv_import\"}'),
(574, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":148,\"name\":\"brown bag # 5\",\"quantity\":0,\"source\":\"csv_import\"}'),
(575, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":149,\"name\":\"Brown bag # 3\",\"quantity\":3,\"source\":\"csv_import\"}'),
(576, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":150,\"name\":\"Brown bag # 2\",\"quantity\":2,\"source\":\"csv_import\"}'),
(577, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":151,\"name\":\"Brown bag # 1\",\"quantity\":3,\"source\":\"csv_import\"}'),
(578, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":152,\"name\":\"bar cheeze\",\"quantity\":39,\"source\":\"csv_import\"}'),
(579, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":153,\"name\":\"Vanilla\",\"quantity\":1,\"source\":\"csv_import\"}'),
(580, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":154,\"name\":\"3x16 plastic\",\"quantity\":3,\"source\":\"csv_import\"}'),
(581, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":155,\"name\":\"Roll bag\",\"quantity\":15,\"source\":\"csv_import\"}'),
(582, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":156,\"name\":\"gelatin\",\"quantity\":0,\"source\":\"csv_import\"}'),
(583, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":157,\"name\":\"EVAP\",\"quantity\":26,\"source\":\"csv_import\"}'),
(584, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":158,\"name\":\"acord\",\"quantity\":0,\"source\":\"csv_import\"}'),
(585, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":159,\"name\":\"cassava starch\",\"quantity\":0,\"source\":\"csv_import\"}'),
(586, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":160,\"name\":\"Glutinous rice 500grms\",\"quantity\":6,\"source\":\"csv_import\"}'),
(587, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":161,\"name\":\"Rice flour 500grms\",\"quantity\":0,\"source\":\"csv_import\"}'),
(588, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":162,\"name\":\"All purpose cream\",\"quantity\":18,\"source\":\"csv_import\"}'),
(589, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":163,\"name\":\"Birch tree 1.4kg\",\"quantity\":1,\"source\":\"csv_import\"}'),
(590, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":164,\"name\":\"Presto biscuit\",\"quantity\":0,\"source\":\"csv_import\"}'),
(591, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":165,\"name\":\"spaghetti sauce 1kg.\",\"quantity\":0,\"source\":\"csv_import\"}'),
(592, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":166,\"name\":\"tomato sauce 1kgn\",\"quantity\":0,\"source\":\"csv_import\"}'),
(593, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":167,\"name\":\"Calumet 1kg.\",\"quantity\":0,\"source\":\"csv_import\"}'),
(594, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":168,\"name\":\"Nescafe 185g.\",\"quantity\":0,\"source\":\"csv_import\"}'),
(595, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":169,\"name\":\"tuna\",\"quantity\":0,\"source\":\"csv_import\"}'),
(596, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":170,\"name\":\"peanut butter s.\",\"quantity\":0,\"source\":\"csv_import\"}'),
(597, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":171,\"name\":\"peanut butter big\",\"quantity\":0,\"source\":\"csv_import\"}'),
(598, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":172,\"name\":\"mantica\",\"quantity\":0,\"source\":\"csv_import\"}'),
(599, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":173,\"name\":\"rice for lugaw\",\"quantity\":9,\"source\":\"csv_import\"}'),
(600, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":174,\"name\":\"bakers Best\",\"quantity\":96,\"source\":\"csv_import\"}'),
(601, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":175,\"name\":\"PATA slice\",\"quantity\":0,\"source\":\"csv_import\"}'),
(602, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":176,\"name\":\"sipao 5kl.Dough\",\"quantity\":18,\"source\":\"csv_import\"}'),
(603, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":177,\"name\":\"burger 5kl Dough\",\"quantity\":9,\"source\":\"csv_import\"}'),
(604, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":178,\"name\":\"chicken back bone\",\"quantity\":6,\"source\":\"csv_import\"}'),
(605, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":179,\"name\":\"shrimp cubes\",\"quantity\":0,\"source\":\"csv_import\"}'),
(606, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":180,\"name\":\"kaong red\",\"quantity\":0,\"source\":\"csv_import\"}'),
(607, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":181,\"name\":\"peaches\",\"quantity\":0,\"source\":\"csv_import\"}'),
(608, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":182,\"name\":\"taco seasoning\",\"quantity\":0,\"source\":\"csv_import\"}'),
(609, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":183,\"name\":\"board #4\",\"quantity\":0,\"source\":\"csv_import\"}'),
(610, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":184,\"name\":\"shrimp powder\",\"quantity\":0,\"source\":\"csv_import\"}'),
(611, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":185,\"name\":\"whippit\",\"quantity\":0,\"source\":\"csv_import\"}'),
(612, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":186,\"name\":\"red velvet\",\"quantity\":30,\"source\":\"csv_import\"}'),
(613, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":187,\"name\":\"pasas\",\"quantity\":1,\"source\":\"csv_import\"}'),
(614, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":188,\"name\":\"casheu\",\"quantity\":0,\"source\":\"csv_import\"}'),
(615, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":189,\"name\":\"cream and tar2x\",\"quantity\":1,\"source\":\"csv_import\"}'),
(616, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":190,\"name\":\"aluminum foil\",\"quantity\":0,\"source\":\"csv_import\"}'),
(617, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":191,\"name\":\"mayonnaise\",\"quantity\":0,\"source\":\"csv_import\"}'),
(618, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":192,\"name\":\"bread crumbs\",\"quantity\":0,\"source\":\"csv_import\"}'),
(619, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":193,\"name\":\"pickles\",\"quantity\":0,\"source\":\"csv_import\"}'),
(620, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":194,\"name\":\"strawberry jam\",\"quantity\":1,\"source\":\"csv_import\"}'),
(621, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":195,\"name\":\"Ube Alaska Condense\",\"quantity\":0,\"source\":\"csv_import\"}'),
(622, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":61,\"name\":\"vana Blanca\",\"quantity\":0,\"source\":\"csv_import\"}'),
(623, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":196,\"name\":\"beef tapa\",\"quantity\":0,\"source\":\"csv_import\"}'),
(624, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":197,\"name\":\"corn starch\",\"quantity\":0,\"source\":\"csv_import\"}'),
(625, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":198,\"name\":\"salad cup\",\"quantity\":0,\"source\":\"csv_import\"}'),
(626, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":199,\"name\":\"cake drum # 14\",\"quantity\":0,\"source\":\"csv_import\"}'),
(627, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":200,\"name\":\"cake drum #12\",\"quantity\":150,\"source\":\"csv_import\"}'),
(628, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":201,\"name\":\"clear pack roll\",\"quantity\":0,\"source\":\"csv_import\"}'),
(629, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":157,\"name\":\"EVAP\",\"quantity\":0,\"source\":\"csv_import\"}'),
(630, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":202,\"name\":\"Condense\",\"quantity\":0,\"source\":\"csv_import\"}'),
(631, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":203,\"name\":\"clear cup 22 oz\",\"quantity\":0,\"source\":\"csv_import\"}'),
(632, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":204,\"name\":\"beryl\'s Choco cocoa\",\"quantity\":0,\"source\":\"csv_import\"}'),
(633, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":205,\"name\":\"chocolate bar white\",\"quantity\":1,\"source\":\"csv_import\"}'),
(634, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":206,\"name\":\"chocolate bar dark\",\"quantity\":8,\"source\":\"csv_import\"}'),
(635, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":207,\"name\":\"chocolate red bar\",\"quantity\":0,\"source\":\"csv_import\"}'),
(636, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":208,\"name\":\"precut tissue\",\"quantity\":0,\"source\":\"csv_import\"}'),
(637, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":209,\"name\":\"whole mushroom big\",\"quantity\":0,\"source\":\"csv_import\"}'),
(638, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":210,\"name\":\"straw for shake\",\"quantity\":10,\"source\":\"csv_import\"}'),
(639, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":211,\"name\":\"bombay\",\"quantity\":0,\"source\":\"csv_import\"}'),
(640, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":212,\"name\":\"ahos\",\"quantity\":0,\"source\":\"csv_import\"}'),
(641, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":213,\"name\":\"patatas\",\"quantity\":0,\"source\":\"csv_import\"}'),
(642, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":214,\"name\":\"sambo container\",\"quantity\":0,\"source\":\"csv_import\"}'),
(643, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":104,\"name\":\"tuyo\",\"quantity\":1,\"source\":\"csv_import\"}'),
(644, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":215,\"name\":\"suka\",\"quantity\":1,\"source\":\"csv_import\"}'),
(645, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":216,\"name\":\"mocha flavor\",\"quantity\":0,\"source\":\"csv_import\"}'),
(646, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":217,\"name\":\"cookies & cream\",\"quantity\":0,\"source\":\"csv_import\"}'),
(647, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":218,\"name\":\"asin\",\"quantity\":2,\"source\":\"csv_import\"}'),
(648, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":219,\"name\":\"paminta  liso\",\"quantity\":0,\"source\":\"csv_import\"}'),
(649, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":220,\"name\":\"coffeemate  tbsp\",\"quantity\":0,\"source\":\"csv_import\"}'),
(650, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":221,\"name\":\"ice tea tbsp\",\"quantity\":0,\"source\":\"csv_import\"}'),
(651, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":222,\"name\":\"candl stick\",\"quantity\":70,\"source\":\"csv_import\"}'),
(652, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":223,\"name\":\"anchor butter\",\"quantity\":0,\"source\":\"csv_import\"}'),
(653, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":224,\"name\":\"kare2x mix\",\"quantity\":0,\"source\":\"csv_import\"}'),
(654, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":225,\"name\":\"rice use taas ( am.)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(655, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":226,\"name\":\"rice use taas (pm.)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(656, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":227,\"name\":\"tomato sauce\",\"quantity\":0,\"source\":\"csv_import\"}'),
(657, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":228,\"name\":\"bravo evap\",\"quantity\":0,\"source\":\"csv_import\"}'),
(658, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":229,\"name\":\"bravo condense\",\"quantity\":0,\"source\":\"csv_import\"}'),
(659, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":153,\"name\":\"vanilla\",\"quantity\":0,\"source\":\"csv_import\"}'),
(660, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":230,\"name\":\"black beans\",\"quantity\":3,\"source\":\"csv_import\"}'),
(661, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":231,\"name\":\"creamyvit\",\"quantity\":0,\"source\":\"csv_import\"}'),
(662, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":232,\"name\":\"betsin\",\"quantity\":0,\"source\":\"csv_import\"}'),
(663, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":233,\"name\":\"cling wrap\",\"quantity\":0,\"source\":\"csv_import\"}'),
(664, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":234,\"name\":\"white paper\",\"quantity\":0,\"source\":\"csv_import\"}'),
(665, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":235,\"name\":\"maling\",\"quantity\":0,\"source\":\"csv_import\"}'),
(666, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":236,\"name\":\"dry peas\",\"quantity\":0,\"source\":\"csv_import\"}'),
(667, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":237,\"name\":\"halo2x take out  cup\",\"quantity\":595,\"source\":\"csv_import\"}'),
(668, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":238,\"name\":\"mango. flavor\",\"quantity\":1,\"source\":\"csv_import\"}'),
(669, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":239,\"name\":\"yeast\",\"quantity\":0,\"source\":\"csv_import\"}'),
(670, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":240,\"name\":\"cling wrap roll\",\"quantity\":0,\"source\":\"csv_import\"}'),
(671, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":234,\"name\":\"white paper\",\"quantity\":0,\"source\":\"csv_import\"}'),
(672, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":235,\"name\":\"maling\",\"quantity\":0,\"source\":\"csv_import\"}'),
(673, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":236,\"name\":\"dry peas\",\"quantity\":0,\"source\":\"csv_import\"}'),
(674, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":237,\"name\":\"halo2x take out  cup\",\"quantity\":0,\"source\":\"csv_import\"}'),
(675, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":238,\"name\":\"mango. flavor\",\"quantity\":0,\"source\":\"csv_import\"}'),
(676, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":239,\"name\":\"yeast\",\"quantity\":0,\"source\":\"csv_import\"}'),
(677, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":241,\"name\":\"Bensdorp\",\"quantity\":0,\"source\":\"csv_import\"}'),
(678, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":242,\"name\":\"crushed oreo\",\"quantity\":1,\"source\":\"csv_import\"}'),
(679, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":243,\"name\":\"16 oz baso\",\"quantity\":750,\"source\":\"csv_import\"}'),
(680, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":244,\"name\":\"12oz baso\",\"quantity\":700,\"source\":\"csv_import\"}'),
(681, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":245,\"name\":\"brisket\",\"quantity\":0,\"source\":\"csv_import\"}'),
(682, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":246,\"name\":\"coffeemate\",\"quantity\":85,\"source\":\"csv_import\"}'),
(683, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":162,\"name\":\"all purpose cream\",\"quantity\":0,\"source\":\"csv_import\"}'),
(684, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":247,\"name\":\"bilbao\",\"quantity\":0,\"source\":\"csv_import\"}'),
(685, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":248,\"name\":\"pork belly humba\",\"quantity\":0,\"source\":\"csv_import\"}'),
(686, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":249,\"name\":\"pork adobo cut\",\"quantity\":0,\"source\":\"csv_import\"}'),
(687, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":250,\"name\":\"chlorine\",\"quantity\":0,\"source\":\"csv_import\"}'),
(688, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":251,\"name\":\"Batchoy powder\",\"quantity\":6,\"source\":\"csv_import\"}'),
(689, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":252,\"name\":\"pamenta liso\",\"quantity\":0,\"source\":\"csv_import\"}'),
(690, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":253,\"name\":\"manggo\",\"quantity\":0,\"source\":\"csv_import\"}'),
(691, 5, 'update', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":105,\"name\":\"knorr seasoning\",\"quantity\":0,\"source\":\"csv_import\"}'),
(692, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":254,\"name\":\"Desiccated coconut\",\"quantity\":0,\"source\":\"csv_import\"}'),
(693, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":255,\"name\":\"Ground beef\",\"quantity\":0,\"source\":\"csv_import\"}'),
(694, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":256,\"name\":\"chix breast\",\"quantity\":0,\"source\":\"csv_import\"}'),
(695, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":257,\"name\":\"brown bag #5\",\"quantity\":0,\"source\":\"csv_import\"}'),
(696, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":258,\"name\":\"slice container  for cake\'s slice\",\"quantity\":1,\"source\":\"csv_import\"}'),
(697, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":259,\"name\":\"Fruit cocktail\",\"quantity\":0,\"source\":\"csv_import\"}'),
(698, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":260,\"name\":\"buko pandan\",\"quantity\":0,\"source\":\"csv_import\"}'),
(699, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":261,\"name\":\"ground pork\",\"quantity\":0,\"source\":\"csv_import\"}'),
(700, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":262,\"name\":\"Tylose\",\"quantity\":0,\"source\":\"csv_import\"}'),
(701, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":263,\"name\":\"chicken Quarter\",\"quantity\":0,\"source\":\"csv_import\"}'),
(702, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":264,\"name\":\"sorbate\",\"quantity\":0,\"source\":\"csv_import\"}'),
(703, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":265,\"name\":\"spoons sa halo2x t.o\",\"quantity\":5,\"source\":\"csv_import\"}'),
(704, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":266,\"name\":\"pork menudo\",\"quantity\":0,\"source\":\"csv_import\"}'),
(705, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":267,\"name\":\"palabok meat  1 1/2\",\"quantity\":0,\"source\":\"csv_import\"}'),
(706, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":268,\"name\":\"fresh lumpia meat (400g )\",\"quantity\":0,\"source\":\"csv_import\"}'),
(707, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":269,\"name\":\"empanada meat\",\"quantity\":0,\"source\":\"csv_import\"}'),
(708, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":270,\"name\":\"assorted meals plastic (6x8\",\"quantity\":0,\"source\":\"csv_import\"}'),
(709, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":271,\"name\":\"balbacua plastic (6x10\",\"quantity\":0,\"source\":\"csv_import\"}'),
(710, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":272,\"name\":\"chicharon tindahan\",\"quantity\":20,\"source\":\"csv_import\"}'),
(711, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":273,\"name\":\"lihia\",\"quantity\":4,\"source\":\"csv_import\"}'),
(712, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":274,\"name\":\"chicharon palabok\",\"quantity\":0,\"source\":\"csv_import\"}'),
(713, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":275,\"name\":\"oxtripe\",\"quantity\":0,\"source\":\"csv_import\"}'),
(714, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":276,\"name\":\"nangka\",\"quantity\":0,\"source\":\"csv_import\"}'),
(715, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":277,\"name\":\"Luncheon meat (Ellen)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(716, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":278,\"name\":\"walnuts\",\"quantity\":0,\"source\":\"csv_import\"}'),
(717, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":279,\"name\":\"siopao meat\",\"quantity\":0,\"source\":\"csv_import\"}'),
(718, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":280,\"name\":\"canto\",\"quantity\":0,\"source\":\"csv_import\"}'),
(719, 5, 'create', 'ingredients', '2025-12-02 13:34:26', '{\"ingredient_id\":281,\"name\":\"paint sa baka\",\"quantity\":0,\"source\":\"csv_import\"}'),
(720, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":282,\"name\":\"1.50  coke\",\"quantity\":0,\"source\":\"csv_import\"}'),
(721, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":283,\"name\":\"chix thigh\",\"quantity\":0,\"source\":\"csv_import\"}'),
(722, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":284,\"name\":\"hamburger mix\",\"quantity\":0,\"source\":\"csv_import\"}'),
(723, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":285,\"name\":\"pork meat for burger\",\"quantity\":0,\"source\":\"csv_import\"}'),
(724, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":286,\"name\":\"Pasta Alfredo\",\"quantity\":0,\"source\":\"csv_import\"}'),
(725, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":287,\"name\":\"Flower icing\",\"quantity\":0,\"source\":\"csv_import\"}'),
(726, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":288,\"name\":\"turmeric\",\"quantity\":0,\"source\":\"csv_import\"}'),
(727, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":289,\"name\":\"Nata de Coco green\",\"quantity\":0,\"source\":\"csv_import\"}'),
(728, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":290,\"name\":\"Nata de coco red\",\"quantity\":0,\"source\":\"csv_import\"}'),
(729, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":291,\"name\":\"Beef powder( 50grms)\",\"quantity\":5,\"source\":\"csv_import\"}'),
(730, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":292,\"name\":\"acord  100grms\",\"quantity\":5,\"source\":\"csv_import\"}'),
(731, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":293,\"name\":\"Burger mix 1kl\",\"quantity\":1,\"source\":\"csv_import\"}'),
(732, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":294,\"name\":\"Ganador\",\"quantity\":0,\"source\":\"csv_import\"}'),
(733, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":295,\"name\":\"plastic sa sauce (8x12\",\"quantity\":0,\"source\":\"csv_import\"}'),
(734, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":296,\"name\":\"calibot cocoa\",\"quantity\":1,\"source\":\"csv_import\"}'),
(735, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":297,\"name\":\"birthday topper\",\"quantity\":0,\"source\":\"csv_import\"}'),
(736, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":298,\"name\":\"taco shell\",\"quantity\":0,\"source\":\"csv_import\"}'),
(737, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":299,\"name\":\"nacho cheese sauce 3.01kg.\",\"quantity\":0,\"source\":\"csv_import\"}'),
(738, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":300,\"name\":\"Mc 500 container\",\"quantity\":0,\"source\":\"csv_import\"}'),
(739, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":301,\"name\":\"t.o plastic double\",\"quantity\":10,\"source\":\"csv_import\"}'),
(740, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":302,\"name\":\"t.o plastic single\",\"quantity\":7,\"source\":\"csv_import\"}'),
(741, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":303,\"name\":\"toot pick\",\"quantity\":0,\"source\":\"csv_import\"}'),
(742, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":304,\"name\":\"board ( 14x14)\",\"quantity\":0,\"source\":\"csv_import\"}'),
(743, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":305,\"name\":\"dawel. wood\",\"quantity\":0,\"source\":\"csv_import\"}'),
(744, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":306,\"name\":\"dragees assorted colors\",\"quantity\":0,\"source\":\"csv_import\"}'),
(745, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":307,\"name\":\"ribbon\",\"quantity\":2,\"source\":\"csv_import\"}'),
(746, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":308,\"name\":\"cup cake wrapper\",\"quantity\":0,\"source\":\"csv_import\"}'),
(747, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":309,\"name\":\"ofsitin container\",\"quantity\":0,\"source\":\"csv_import\"}'),
(748, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":310,\"name\":\"board 12\",\"quantity\":0,\"source\":\"csv_import\"}'),
(749, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":311,\"name\":\"coke in can 24\",\"quantity\":0,\"source\":\"csv_import\"}'),
(750, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":312,\"name\":\"sprite in can 24\",\"quantity\":4,\"source\":\"csv_import\"}'),
(751, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":313,\"name\":\"royal in can 24\",\"quantity\":6,\"source\":\"csv_import\"}'),
(752, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":314,\"name\":\"pineapple juice\",\"quantity\":0,\"source\":\"csv_import\"}'),
(753, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":315,\"name\":\"lady choice\",\"quantity\":0,\"source\":\"csv_import\"}'),
(754, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":316,\"name\":\"gulaman black\",\"quantity\":4,\"source\":\"csv_import\"}'),
(755, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":317,\"name\":\"Hershey\'s\",\"quantity\":0,\"source\":\"csv_import\"}'),
(756, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":318,\"name\":\"bulaklaksaging\",\"quantity\":0,\"source\":\"csv_import\"}'),
(757, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":319,\"name\":\"banana flavor\",\"quantity\":0,\"source\":\"csv_import\"}'),
(758, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":320,\"name\":\"ube paste\",\"quantity\":0,\"source\":\"csv_import\"}'),
(759, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":321,\"name\":\"mc 750\",\"quantity\":0,\"source\":\"csv_import\"}'),
(760, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":322,\"name\":\"Paprika\",\"quantity\":0,\"source\":\"csv_import\"}'),
(761, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":323,\"name\":\"accord\",\"quantity\":1,\"source\":\"csv_import\"}'),
(762, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":324,\"name\":\"butter milk 1/2 klg\",\"quantity\":0,\"source\":\"csv_import\"}'),
(763, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":325,\"name\":\"white chips 1/2\",\"quantity\":0,\"source\":\"csv_import\"}'),
(764, 5, 'create', 'ingredients', '2025-12-02 13:34:27', '{\"ingredient_id\":326,\"name\":\"chocolate chips 1/2\",\"quantity\":0,\"source\":\"csv_import\"}'),
(765, 7, 'login', 'auth', '2025-12-02 14:18:16', '{\"email\":\"kitchen@demo.local\"}');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(10) UNSIGNED NOT NULL,
  `purchase_id` int(10) UNSIGNED NOT NULL,
  `quantity_received` decimal(16,4) NOT NULL,
  `delivery_status` enum('Partial','Complete') NOT NULL,
  `date_received` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `category` varchar(120) NOT NULL DEFAULT '',
  `unit` varchar(32) NOT NULL,
  `display_unit` varchar(32) DEFAULT NULL,
  `display_factor` decimal(16,4) NOT NULL DEFAULT 1.0000,
  `quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `reorder_level` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `preferred_supplier` varchar(160) NOT NULL DEFAULT '',
  `restock_quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `in_inventory` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_sets`
--

CREATE TABLE `ingredient_sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_set_items`
--

CREATE TABLE `ingredient_set_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `set_id` int(10) UNSIGNED NOT NULL,
  `ingredient_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(16,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `level` enum('info','warning','success','danger') NOT NULL DEFAULT 'info',
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `level`, `read_at`, `created_at`) VALUES
(22, 7, 'Your request #41 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:33:40'),
(23, 7, 'Your request #42 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=42', 'info', NULL, '2025-12-02 02:34:55'),
(24, 7, 'Your request #42 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:35:08'),
(25, 7, 'Your request #43 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=43', 'info', NULL, '2025-12-02 02:39:05'),
(26, 7, 'Your request #43 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:39:53'),
(27, 7, 'Your request #44 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=44', 'info', NULL, '2025-12-02 02:40:13'),
(28, 7, 'Your request #44 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:40:41'),
(29, 7, 'Your request #45 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=45', 'info', NULL, '2025-12-02 02:48:29'),
(30, 7, 'Your request #45 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:48:46'),
(31, 7, 'Your request #46 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=46', 'info', NULL, '2025-12-02 02:49:04'),
(32, 7, 'Your request #46 is ready for pickup.', '/requests?status=distributed#requests-history', 'success', NULL, '2025-12-02 02:49:32');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(10) UNSIGNED NOT NULL,
  `purchaser_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `supplier` varchar(160) NOT NULL,
  `quantity` decimal(16,4) NOT NULL,
  `purchase_unit` varchar(20) NOT NULL DEFAULT '',
  `purchase_quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `cost` decimal(16,2) NOT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `payment_status` enum('Paid','Pending') NOT NULL DEFAULT 'Pending',
  `date_purchased` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_type` enum('Card','Cash') NOT NULL DEFAULT 'Card',
  `cash_base_amount` decimal(16,2) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `batch_id` int(10) UNSIGNED NOT NULL,
  `staff_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(16,4) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `date_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `date_approved` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_batches`
--

CREATE TABLE `request_batches` (
  `id` int(10) UNSIGNED NOT NULL,
  `staff_id` int(10) UNSIGNED NOT NULL,
  `status` enum('Pending','To Prepare','Distributed','Rejected') NOT NULL DEFAULT 'Pending',
  `date_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `date_approved` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `custom_requester` varchar(160) NOT NULL DEFAULT '',
  `custom_ingredients` text DEFAULT NULL,
  `custom_request_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_item_sets`
--

CREATE TABLE `request_item_sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED NOT NULL,
  `set_id` int(10) UNSIGNED DEFAULT NULL,
  `set_name` varchar(160) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(160) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'security.cost_hidden_roles', '[\"Kitchen Staff\"]', 3, '2025-11-30 15:26:07', '2025-11-30 15:26:07'),
(2, 'security.permissions', '{\"Owner\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":true},\"Manager\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":true},\"Purchaser\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":false},\"Stock Handler\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":false},\"Kitchen Staff\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":false}}', 3, '2025-11-30 15:26:07', '2025-11-30 15:26:28'),
(5, 'display.company_name', 'IKEA Commissary System', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(6, 'display.company_tagline', 'Operations Console', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(7, 'display.theme_default', 'system', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(8, 'display.dashboard_widgets', '{\"Owner\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Manager\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Purchaser\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"Stock Handler\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Kitchen Staff\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"default\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"]}', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(13, 'features.ingredient_sets_enabled', '0', 3, '2025-12-02 01:53:04', '2025-12-02 01:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `role` enum('Owner','Manager','Stock Handler','Purchaser','Kitchen Staff') NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `role`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
(3, 'Owner Admin', 'Owner', 'makiemorales2@gmail.com', '$2y$10$D0S5CYq5CHy/h0u0zrYORelBP0FRYyc9rKx6MLF4J3IGt.nvnJAa2', '2025-10-17 16:18:26', '2025-10-17 16:23:26'),
(4, 'Manager Demo', 'Manager', 'manager@demo.local', '$2y$10$D0S5CYq5CHy/h0u0zrYORelBP0FRYyc9rKx6MLF4J3IGt.nvnJAa2', '2025-10-17 16:44:12', '2025-10-17 16:46:51'),
(5, 'Stock Handler Demo', 'Stock Handler', 'stock@demo.local', '$2y$10$D0S5CYq5CHy/h0u0zrYORelBP0FRYyc9rKx6MLF4J3IGt.nvnJAa2', '2025-10-17 16:44:12', '2025-10-17 16:46:47'),
(6, 'Purchaser Demo', 'Purchaser', 'purchaser@demo.local', '$2y$10$D0S5CYq5CHy/h0u0zrYORelBP0FRYyc9rKx6MLF4J3IGt.nvnJAa2', '2025-10-17 16:44:12', '2025-10-17 16:46:43'),
(7, 'Kitchen Staff Demo', 'Kitchen Staff', 'kitchen@demo.local', '$2y$10$D0S5CYq5CHy/h0u0zrYORelBP0FRYyc9rKx6MLF4J3IGt.nvnJAa2', '2025-10-17 16:44:12', '2025-10-17 16:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_security`
--

CREATE TABLE `user_security` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `session_token` varchar(64) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `theme` varchar(16) NOT NULL DEFAULT 'system',
  `dashboard_widgets` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_security`
--

INSERT INTO `user_security` (`user_id`, `session_token`, `status`, `theme`, `dashboard_widgets`, `updated_at`, `created_at`) VALUES
(3, NULL, 'active', 'light', NULL, '2025-12-02 01:56:02', '2025-11-30 15:28:02'),
(5, '335c8452cb9ef93d58bbef9444af4cab7d255931fbca1b6d5ea985f7d8eea532', 'active', 'light', NULL, '2025-12-02 08:04:54', '2025-12-01 01:44:08'),
(6, NULL, 'active', 'light', NULL, '2025-12-01 07:17:32', '2025-12-01 03:45:48'),
(7, 'fdc9a17b83510b61ff4924085340d06426064db8754defb15fd02a623fe8dc1b', 'active', 'light', NULL, '2025-12-02 06:18:16', '2025-11-30 15:29:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_module` (`module`),
  ADD KEY `idx_audit_timestamp` (`timestamp`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_deliveries_purchase` (`purchase_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ingredient_name` (`name`);

--
-- Indexes for table `ingredient_sets`
--
ALTER TABLE `ingredient_sets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_set_name` (`name`),
  ADD KEY `fk_set_creator` (`created_by`);

--
-- Indexes for table `ingredient_set_items`
--
ALTER TABLE `ingredient_set_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_set_item` (`set_id`,`ingredient_id`),
  ADD KEY `fk_set_items_ingredient` (`ingredient_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`),
  ADD KEY `idx_notifications_read` (`read_at`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_purchases_purchaser` (`purchaser_id`),
  ADD KEY `idx_purchases_item` (`item_id`),
  ADD KEY `idx_purchases_supplier` (`supplier`),
  ADD KEY `idx_purchases_payment_status` (`payment_status`),
  ADD KEY `idx_purchases_payment_type` (`payment_type`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_requests_staff` (`staff_id`),
  ADD KEY `idx_requests_item` (`item_id`),
  ADD KEY `idx_requests_status` (`status`),
  ADD KEY `idx_requests_batch` (`batch_id`);

--
-- Indexes for table `request_batches`
--
ALTER TABLE `request_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_request_batches_staff` (`staff_id`),
  ADD KEY `idx_request_batches_status` (`status`);

--
-- Indexes for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_request_set` (`request_id`),
  ADD KEY `idx_request_item_sets_name` (`set_name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `fk_settings_user` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- Indexes for table `user_security`
--
ALTER TABLE `user_security`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=766;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- AUTO_INCREMENT for table `ingredient_sets`
--
ALTER TABLE `ingredient_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ingredient_set_items`
--
ALTER TABLE `ingredient_set_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `request_batches`
--
ALTER TABLE `request_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `fk_deliveries_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ingredient_sets`
--
ALTER TABLE `ingredient_sets`
  ADD CONSTRAINT `fk_set_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ingredient_set_items`
--
ALTER TABLE `ingredient_set_items`
  ADD CONSTRAINT `fk_set_items_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_set_items_set` FOREIGN KEY (`set_id`) REFERENCES `ingredient_sets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_purchases_item` FOREIGN KEY (`item_id`) REFERENCES `ingredients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_purchases_purchaser` FOREIGN KEY (`purchaser_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_batch` FOREIGN KEY (`batch_id`) REFERENCES `request_batches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_requests_item` FOREIGN KEY (`item_id`) REFERENCES `ingredients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_requests_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `request_batches`
--
ALTER TABLE `request_batches`
  ADD CONSTRAINT `fk_request_batches_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  ADD CONSTRAINT `fk_request_item_set_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `fk_settings_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_security`
--
ALTER TABLE `user_security`
  ADD CONSTRAINT `fk_user_security_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
