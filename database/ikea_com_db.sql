-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 05:13 PM
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
(409, 3, 'login', 'auth', '2025-12-02 00:09:07', '{\"email\":\"makiemorales2@gmail.com\"}');

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

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `purchase_id`, `quantity_received`, `delivery_status`, `date_received`, `created_at`, `updated_at`) VALUES
(28, 87, 50000.0000, 'Complete', '2025-12-01 23:59:04', '2025-12-01 15:59:04', '2025-12-01 15:59:04');

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

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `category`, `unit`, `display_unit`, `display_factor`, `quantity`, `reorder_level`, `preferred_supplier`, `restock_quantity`, `in_inventory`, `created_at`, `updated_at`) VALUES
(34, 'Brown Sugar', '', 'g', NULL, 1000.0000, 100000.0000, 10000.0000, '', 0.0000, 1, '2025-12-01 13:13:56', '2025-12-01 15:59:04');

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
(12, 7, 'Your request #35 has been approved by Stock Handler Demo. Please wait while it is being prepared.', '/requests?batch=35', 'info', NULL, '2025-12-01 07:19:18');

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

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `purchaser_id`, `item_id`, `supplier`, `quantity`, `purchase_unit`, `purchase_quantity`, `cost`, `receipt_url`, `payment_status`, `date_purchased`, `created_at`, `updated_at`, `payment_type`, `cash_base_amount`, `paid_at`) VALUES
(87, 5, 34, 'simion', 1.0000, 'asdasd|sack', 1.0000, 2800.00, '/public/uploads/8331708be018b2da.jpg', 'Paid', '2025-12-01 23:31:48', '2025-12-01 15:31:48', '2025-12-01 15:31:48', 'Card', 0.00, '2025-12-01 16:31:48');

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

--
-- Dumping data for table `request_batches`
--

INSERT INTO `request_batches` (`id`, `staff_id`, `status`, `date_requested`, `date_approved`, `created_at`, `updated_at`, `custom_requester`, `custom_ingredients`, `custom_request_date`) VALUES
(35, 7, 'To Prepare', '2025-12-01 15:18:42', '2025-12-01 15:19:18', '2025-12-01 07:18:42', '2025-12-01 07:19:18', 'loren', '2 set pack of jetpack\r\n2 set balba\r\n5 kg flour', '2025-12-02');

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
(8, 'display.dashboard_widgets', '{\"Owner\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Manager\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Purchaser\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"Stock Handler\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Kitchen Staff\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"default\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"]}', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47');

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
(3, '0ad1eaa0f56ded7043e647908e15a4a8fdbe4320abd9ffeec1ad6640e2a5231c', 'active', 'light', NULL, '2025-12-01 16:09:07', '2025-11-30 15:28:02'),
(5, NULL, 'active', 'light', NULL, '2025-12-01 16:08:56', '2025-12-01 01:44:08'),
(6, NULL, 'active', 'light', NULL, '2025-12-01 07:17:32', '2025-12-01 03:45:48'),
(7, NULL, 'active', 'light', NULL, '2025-12-01 08:30:28', '2025-11-30 15:29:05');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=410;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `request_batches`
--
ALTER TABLE `request_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
