<?php
declare(strict_types=1);

class User extends BaseModel
{
	public function findByEmail(string $email): ?array
	{
		$sql = 'SELECT id, name, role, email, password_hash FROM users WHERE email = ? LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$email]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function all(): array
	{
		$sql = 'SELECT id, name, role, email, created_at FROM users ORDER BY name ASC';
		return $this->db->query($sql)->fetchAll();
	}

	public function create(string $name, string $role, string $email, string $passwordHash): int
	{
		$sql = 'INSERT INTO users (name, role, email, password_hash) VALUES (?, ?, ?, ?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$name, $role, $email, $passwordHash]);
		return (int)$this->db->lastInsertId();
	}

	public function resetPassword(int $id, string $passwordHash): void
	{
		$sql = 'UPDATE users SET password_hash = ? WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$passwordHash, $id]);
	}
}


