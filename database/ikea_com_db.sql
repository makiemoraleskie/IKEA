-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 06:08 AM
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
(246, 3, 'logout', 'auth', '2025-11-28 13:04:15', '[]');

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
  `unit` varchar(32) NOT NULL,
  `display_unit` varchar(32) DEFAULT NULL,
  `display_factor` decimal(16,4) NOT NULL DEFAULT 1.0000,
  `quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `reorder_level` decimal(16,4) NOT NULL DEFAULT 0.0000,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `request_batches`
--
ALTER TABLE `request_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
