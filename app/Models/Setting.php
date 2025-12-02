<?php
declare(strict_types=1);

class Setting extends BaseModel
{
	private bool $tableEnsured = false;

	private function ensureTable(): void
	{
		if ($this->tableEnsured) {
			return;
		}

		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS settings (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				setting_key VARCHAR(160) NOT NULL UNIQUE,
				setting_value LONGTEXT NULL,
				updated_by INT UNSIGNED NULL,
				created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY idx_setting_key (setting_key),
				CONSTRAINT fk_settings_user FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
		);

		$this->tableEnsured = true;
	}

	public function all(): array
	{
		$this->ensureTable();
		$sql = 'SELECT setting_key, setting_value FROM settings';
		return $this->db->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
	}

	public function get(string $key): ?string
	{
		$this->ensureTable();
		$sql = 'SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$key]);
		$value = $stmt->fetchColumn();
		return $value === false ? null : (string)$value;
	}

	public function set(string $key, ?string $value, ?int $userId = null): void
	{
		$this->ensureTable();
		if ($value === null) {
			$this->delete($key);
			return;
		}
		$sql = 'INSERT INTO settings (setting_key, setting_value, updated_by)
				VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_by = VALUES(updated_by)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$key, $value, $userId]);
	}

	public function delete(string $key): void
	{
		$this->ensureTable();
		$sql = 'DELETE FROM settings WHERE setting_key = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$key]);
	}
}


