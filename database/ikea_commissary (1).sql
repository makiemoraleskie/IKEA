-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 07:30 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(10) UNSIGNED NOT NULL,
  `purchase_id` int(10) UNSIGNED NOT NULL,
  `ingredient_id` int(11) NOT NULL DEFAULT 0,
  `quantity_received` decimal(16,4) NOT NULL,
  `receive_quantity` decimal(16,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(32) NOT NULL DEFAULT '',
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

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `purchase_group_id` varchar(50) NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
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
(1, 'security.cost_hidden_roles', '[\"Kitchen Staff\"]', 3, '2025-11-30 15:26:07', '2025-12-16 15:08:41'),
(2, 'security.permissions', '{\"Owner\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":true,\"access_backups\":true},\"Manager\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":true},\"Purchaser\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":false},\"Stock Handler\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":true,\"access_backups\":false},\"Kitchen Staff\":{\"view_costs\":false,\"view_receipts\":false,\"manage_reports\":false,\"access_backups\":false}}', 3, '2025-11-30 15:26:07', '2025-12-16 14:09:13'),
(5, 'display.company_name', 'IKEA Commissary System', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(6, 'display.company_tagline', 'Operations Console', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(7, 'display.theme_default', 'system', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(8, 'display.dashboard_widgets', '{\"Owner\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Manager\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Purchaser\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"Stock Handler\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"],\"Kitchen Staff\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\"],\"default\":[\"low_stock\",\"pending_requests\",\"pending_payments\",\"partial_deliveries\",\"pending_deliveries\",\"inventory_value\"]}', 3, '2025-11-30 15:27:47', '2025-11-30 15:27:47'),
(13, 'features.ingredient_sets_enabled', '0', 3, '2025-12-02 01:53:04', '2025-12-02 01:53:35'),
(23, 'inventory.actions_visible', '1', 3, '2025-12-08 16:01:04', '2025-12-18 05:43:42'),
(38, 'reporting.archive_days', '0', 3, '2025-12-16 14:10:30', '2025-12-16 14:10:30'),
(39, 'reporting.enabled_sections', '[\"purchase\",\"consumption\"]', 3, '2025-12-16 14:10:30', '2025-12-16 14:10:30');

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
(5, 'Stock Handler Demo', 'Stock Handler', 'stock@demo.local', '$2y$10$D6GiQlm.xhH3Ja/RRFHlqu6EtZoK6Rg49hTHThExg7AgxzpX6lXcW', '2025-10-17 16:44:12', '2025-12-16 11:09:01'),
(6, 'Purchaser Demo', 'Purchaser', 'purchaser@demo.local', '$2y$10$ODVJx87mc2V10UxosEvXi.aI3/lBSYexpbTWR3ytsEC7ZOnF0lHra', '2025-10-17 16:44:12', '2025-12-17 01:11:59'),
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
(3, 'bc1d0aaa7b53672ef8920a60501efd06957686af646f59e3b883b8e9379b3dff', 'active', 'light', NULL, '2025-12-18 05:44:39', '2025-11-30 15:28:02'),
(5, NULL, 'active', 'light', NULL, '2025-12-18 06:07:52', '2025-12-01 01:44:08'),
(6, '8c3449c5814fdb84859cbe59c209dfaca7822f5ae5f03fa0b989ee017a9d04f2', 'active', 'light', NULL, '2025-12-18 06:08:02', '2025-12-01 03:45:48'),
(7, NULL, 'active', 'light', NULL, '2025-12-18 05:39:01', '2025-11-30 15:29:05');

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
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_purchase_id` (`purchase_id`),
  ADD KEY `idx_group_id` (`purchase_group_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6961;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5841;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `request_batches`
--
ALTER TABLE `request_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `request_item_sets`
--
ALTER TABLE `request_item_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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
