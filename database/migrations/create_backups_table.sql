-- Migration: Add backups table for tracking system backups
-- Tracks all backup files created by users for restore functionality

CREATE TABLE IF NOT EXISTS backups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  filename VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  file_size BIGINT UNSIGNED NOT NULL,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  description TEXT NULL,
  records_count INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_backups_created_at (created_at),
  KEY idx_backups_created_by (created_by),
  CONSTRAINT fk_backups_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

