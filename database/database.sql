-- MySQL 8+ schema for IKEA Commissary System
-- Ensure DB created beforehand: CREATE DATABASE ikea_commissary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE ikea_commissary;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Drop tables in reverse FK order (for idempotent re-import during dev)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS deliveries;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS requests;
DROP TABLE IF EXISTS request_batches;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  role ENUM('Owner','Manager','Stock Handler','Purchaser','Kitchen Staff') NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ingredients (track quantity at base unit; support reorder level)
CREATE TABLE ingredients (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  unit VARCHAR(32) NOT NULL,
  display_unit VARCHAR(32) NULL,
  display_factor DECIMAL(16,4) NOT NULL DEFAULT 1,
  quantity DECIMAL(16,4) NOT NULL DEFAULT 0,
  reorder_level DECIMAL(16,4) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_ingredient_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Requests from Kitchen Staff
CREATE TABLE request_batches (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  staff_id INT UNSIGNED NOT NULL,
  status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  date_requested DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_approved DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_request_batches_staff (staff_id),
  KEY idx_request_batches_status (status),
  CONSTRAINT fk_request_batches_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Request items (detail rows)
CREATE TABLE requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  batch_id INT UNSIGNED NOT NULL,
  staff_id INT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NOT NULL,
  quantity DECIMAL(16,4) NOT NULL,
  status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending', -- kept for backward compatibility; batch status is authoritative
  date_requested DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- kept for backward compatibility
  date_approved DATETIME NULL, -- kept for backward compatibility
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_requests_batch (batch_id),
  KEY idx_requests_staff (staff_id),
  KEY idx_requests_item (item_id),
  KEY idx_requests_status (status),
  CONSTRAINT fk_requests_batch FOREIGN KEY (batch_id) REFERENCES request_batches(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_requests_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_requests_item FOREIGN KEY (item_id) REFERENCES ingredients(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchases by Purchaser
CREATE TABLE purchases (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  purchaser_id INT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NOT NULL,
  supplier VARCHAR(160) NOT NULL,
  quantity DECIMAL(16,4) NOT NULL,
  cost DECIMAL(16,2) NOT NULL,
  receipt_url VARCHAR(255) NULL,
  payment_status ENUM('Paid','Pending') NOT NULL DEFAULT 'Pending',
  payment_type ENUM('Card','Cash') NOT NULL DEFAULT 'Card',
  cash_base_amount DECIMAL(16,2) NULL,
  date_purchased DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_purchases_purchaser (purchaser_id),
  KEY idx_purchases_item (item_id),
  KEY idx_purchases_supplier (supplier),
  KEY idx_purchases_payment_status (payment_status),
  KEY idx_purchases_payment_type (payment_type),
  CONSTRAINT fk_purchases_purchaser FOREIGN KEY (purchaser_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_purchases_item FOREIGN KEY (item_id) REFERENCES ingredients(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Deliveries linked to a purchase; can be partial
CREATE TABLE deliveries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  purchase_id INT UNSIGNED NOT NULL,
  quantity_received DECIMAL(16,4) NOT NULL,
  delivery_status ENUM('Partial','Complete') NOT NULL,
  date_received DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_deliveries_purchase (purchase_id),
  CONSTRAINT fk_deliveries_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log (immutable)
CREATE TABLE audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  module VARCHAR(100) NOT NULL,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  details JSON NULL,
  PRIMARY KEY (id),
  KEY idx_audit_user (user_id),
  KEY idx_audit_module (module),
  KEY idx_audit_timestamp (timestamp),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: seed an admin (change password after first login)
-- INSERT INTO users (name, role, email, password_hash) VALUES (
--   'Owner Admin','Owner','owner@example.com',
--   -- password: Admin@123 (change immediately)
--   '$2y$10$Q5.5w3tHhKcQwS3Jp6qX1e4YcCk0u8qE9qH5kYQh2L4hZfGg0m4ka'
-- );


