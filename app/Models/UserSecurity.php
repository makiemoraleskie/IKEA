<?php
declare(strict_types=1);

class UserSecurity extends BaseModel
{
	private bool $tableEnsured = false;

	private function ensureTable(): void
	{
		if ($this->tableEnsured) {
			return;
		}

		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS user_security (
				user_id INT UNSIGNED NOT NULL,
				session_token VARCHAR(64) NULL,
				status ENUM("active","inactive") NOT NULL DEFAULT "active",
				theme VARCHAR(16) NOT NULL DEFAULT "system",
				dashboard_widgets TEXT NULL,
				updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (user_id),
				CONSTRAINT fk_user_security_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
		);

		$this->tableEnsured = true;
	}

	private function ensureRow(int $userId): void
	{
		$this->ensureTable();
		$sql = 'INSERT IGNORE INTO user_security (user_id) VALUES (?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$userId]);
	}

	public function get(int $userId): array
	{
		$this->ensureRow($userId);
		$sql = 'SELECT * FROM user_security WHERE user_id = ? LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$userId]);
		return $stmt->fetch() ?: [];
	}

	public function setStatus(int $userId, string $status): void
	{
		$status = in_array($status, ['active','inactive'], true) ? $status : 'active';
		$this->ensureRow($userId);
		$sql = 'UPDATE user_security SET status = ? WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status, $userId]);
	}

	public function setSessionToken(int $userId, string $token): void
	{
		$this->ensureRow($userId);
		$sql = 'UPDATE user_security SET session_token = ? WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$token, $userId]);
	}

	public function clearSession(int $userId): void
	{
		$this->ensureRow($userId);
		$sql = 'UPDATE user_security SET session_token = NULL WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$userId]);
	}

	public function bumpSession(int $userId): string
	{
		$token = bin2hex(random_bytes(32));
		$this->setSessionToken($userId, $token);
		return $token;
	}

	public function setTheme(int $userId, string $theme): void
	{
		$theme = in_array($theme, ['light','dark','system'], true) ? $theme : 'system';
		$this->ensureRow($userId);
		$sql = 'UPDATE user_security SET theme = ? WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$theme, $userId]);
	}

	public function getTheme(int $userId): string
	{
		$row = $this->get($userId);
		$theme = strtolower((string)($row['theme'] ?? 'system'));
		return in_array($theme, ['light','dark','system'], true) ? $theme : 'system';
	}

	public function setWidgets(int $userId, array $widgets): void
	{
		$this->ensureRow($userId);
		$sql = 'UPDATE user_security SET dashboard_widgets = ? WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([json_encode(array_values(array_unique($widgets)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $userId]);
	}

	public function getWidgets(int $userId): array
	{
		$row = $this->get($userId);
		if (empty($row['dashboard_widgets'])) {
			return [];
		}
		$data = json_decode((string)$row['dashboard_widgets'], true);
		return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : [];
	}

	public function getMany(array $userIds): array
	{
		if (empty($userIds)) {
			return [];
		}
		$this->ensureTable();
		$placeholders = implode(',', array_fill(0, count($userIds), '?'));
		$sql = "SELECT * FROM user_security WHERE user_id IN ($placeholders)";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array_values($userIds));
		$rows = $stmt->fetchAll();
		$map = [];
		foreach ($rows as $row) {
			$map[(int)$row['user_id']] = $row;
		}
		return $map;
	}
}


