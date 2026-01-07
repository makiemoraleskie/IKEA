-- Migration: Add ingredient_losses table for tracking spoiled/damaged ingredients
-- Only Owner role can record losses
-- This table tracks ingredient losses with reason and automatically reduces stock

CREATE TABLE IF NOT EXISTS ingredient_losses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ingredient_id INT UNSIGNED NOT NULL,
  quantity DECIMAL(16,4) NOT NULL,
  reason ENUM('Spoiled', 'Damaged', 'Expired', 'Contaminated', 'Other') NOT NULL DEFAULT 'Other',
  notes TEXT NULL,
  recorded_by INT UNSIGNED NOT NULL,
  recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ingredient_losses_ingredient (ingredient_id),
  KEY idx_ingredient_losses_recorded_by (recorded_by),
  KEY idx_ingredient_losses_recorded_at (recorded_at),
  KEY idx_ingredient_losses_reason (reason),
  CONSTRAINT fk_ingredient_losses_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_ingredient_losses_user FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

