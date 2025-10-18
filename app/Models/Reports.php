<?php
declare(strict_types=1);

class Reports extends BaseModel
{
	/**
	 * Fetch purchases with optional filters: date_from, date_to, supplier (LIKE), item_id
	 */
	public function getPurchases(array $filters): array
	{
		$sql = 'SELECT p.*, u.name AS purchaser_name, i.name AS item_name, i.unit
			FROM purchases p
			JOIN users u ON p.purchaser_id = u.id
			JOIN ingredients i ON p.item_id = i.id';
		$where = [];
		$params = [];
		if (!empty($filters['date_from'])) { $where[] = 'p.date_purchased >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
		if (!empty($filters['date_to'])) { $where[] = 'p.date_purchased <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
		if (!empty($filters['supplier'])) { $where[] = 'p.supplier LIKE ?'; $params[] = '%' . $filters['supplier'] . '%'; }
		if (!empty($filters['item_id'])) { $where[] = 'p.item_id = ?'; $params[] = (int)$filters['item_id']; }
		if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
		$sql .= ' ORDER BY p.date_purchased DESC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	/**
	 * Aggregate total cost per day for Chart.js based on same filters.
	 */
	public function getDailyTotals(array $filters): array
	{
		$sql = 'SELECT DATE(p.date_purchased) AS d, SUM(p.cost) AS total
			FROM purchases p';
		$where = [];
		$params = [];
		if (!empty($filters['date_from'])) { $where[] = 'p.date_purchased >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
		if (!empty($filters['date_to'])) { $where[] = 'p.date_purchased <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
		if (!empty($filters['supplier'])) { $where[] = 'p.supplier LIKE ?'; $params[] = '%' . $filters['supplier'] . '%'; }
		if (!empty($filters['item_id'])) { $where[] = 'p.item_id = ?'; $params[] = (int)$filters['item_id']; }
		if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
		$sql .= ' GROUP BY DATE(p.date_purchased) ORDER BY d ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}
}


