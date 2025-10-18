<?php
declare(strict_types=1);

class AuditLog extends BaseModel
{
	public function log(int $userId, string $action, string $module, array $details = []): void
	{
		$sql = 'INSERT INTO audit_log (user_id, action, module, timestamp, details) VALUES (?, ?, ?, NOW(), ?)';
		$stmt = $this->db->prepare($sql);
		$detailsJson = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$stmt->execute([$userId, $action, $module, $detailsJson]);
	}

	public function list(array $filters = []): array
	{
		$sql = 'SELECT a.*, u.name AS user_name FROM audit_log a LEFT JOIN users u ON a.user_id = u.id';
		$where = [];
		$params = [];
		if (!empty($filters['user_id'])) { $where[] = 'a.user_id = ?'; $params[] = (int)$filters['user_id']; }
		if (!empty($filters['module'])) { $where[] = 'a.module = ?'; $params[] = $filters['module']; }
		if (!empty($filters['date_from'])) { $where[] = 'a.timestamp >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
		if (!empty($filters['date_to'])) { $where[] = 'a.timestamp <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
		if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
		$sql .= ' ORDER BY a.timestamp DESC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}
}


