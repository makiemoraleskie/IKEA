<?php
declare(strict_types=1);

class Reports extends BaseModel
{
	/**
	 * Fetch purchases with optional filters: date_from, date_to, supplier (LIKE), item_id
	 */
	public function getPurchases(array $filters): array
	{
		$sql = 'SELECT p.*, u.name AS purchaser_name, i.name AS item_name, i.unit, i.category
			FROM purchases p
			JOIN users u ON p.purchaser_id = u.id
			JOIN ingredients i ON p.item_id = i.id';
		$where = [];
		$params = [];
		if (!empty($filters['date_from'])) { $where[] = 'p.date_purchased >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
		if (!empty($filters['date_to'])) { $where[] = 'p.date_purchased <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
		if (!empty($filters['supplier'])) { $where[] = 'p.supplier LIKE ?'; $params[] = '%' . $filters['supplier'] . '%'; }
		if (!empty($filters['item_id'])) { $where[] = 'p.item_id = ?'; $params[] = (int)$filters['item_id']; }
		if (!empty($filters['payment_status'])) { $where[] = 'p.payment_status = ?'; $params[] = $filters['payment_status']; }
		if (!empty($filters['category'])) { $where[] = 'i.category = ?'; $params[] = $filters['category']; }
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
		if (!empty($filters['payment_status'])) { $where[] = 'p.payment_status = ?'; $params[] = $filters['payment_status']; }
		if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
		$sql .= ' GROUP BY DATE(p.date_purchased) ORDER BY d ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	/**
	 * Aggregate total ingredient consumption (requests distributed) within filters.
	 */
	public function getIngredientConsumption(array $filters): array
	{
		$sql = 'SELECT
				i.id,
				i.name,
				i.category,
				i.unit,
				i.display_unit,
				i.display_factor,
				SUM(r.quantity) AS total_quantity
			FROM requests r
			JOIN request_batches b ON r.batch_id = b.id
			JOIN ingredients i ON r.item_id = i.id';
		$params = [];
		$conditions = [];
		if (!empty($filters['date_from'])) {
			$conditions[] = 'b.date_requested >= ?';
			$params[] = $filters['date_from'] . ' 00:00:00';
		}
		if (!empty($filters['date_to'])) {
			$conditions[] = 'b.date_requested <= ?';
			$params[] = $filters['date_to'] . ' 23:59:59';
		}
		if (!empty($filters['category'])) {
			$conditions[] = 'i.category = ?';
			$params[] = $filters['category'];
		}
		$statusApplied = false;
		if (!empty($filters['usage_status'])) {
			$statusMap = [
				'used' => 'Distributed',
				'expired' => 'Rejected',
				'transferred' => 'To Prepare',
			];
			$statusKey = strtolower((string)$filters['usage_status']);
			if (isset($statusMap[$statusKey])) {
				$conditions[] = 'b.status = ?';
				$params[] = $statusMap[$statusKey];
				$statusApplied = true;
			}
		}
		if (!$statusApplied) {
			$conditions[] = 'b.status = "Distributed"';
		}
		if ($conditions) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql .= ' GROUP BY i.id, i.name, i.category, i.unit, i.display_unit, i.display_factor
			HAVING total_quantity > 0
			ORDER BY i.name ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}
}


