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
	 * Get ingredient consumption records (individual distributions) within filters.
	 * Returns: date, name, total_quantity, unit, remaining_stock
	 */
	public function getIngredientConsumption(array $filters): array
	{
		$sql = 'SELECT
				COALESCE(b.date_approved, b.date_requested, b.created_at) AS distribution_date,
				i.id,
				i.name,
				i.category,
				i.unit,
				i.display_unit,
				i.display_factor,
				r.quantity AS total_quantity,
				b.id AS batch_id
			FROM requests r
			JOIN request_batches b ON r.batch_id = b.id
			JOIN ingredients i ON r.item_id = i.id';
		$params = [];
		$conditions = [];
		if (!empty($filters['date_from'])) {
			$conditions[] = 'COALESCE(b.date_approved, b.date_requested, b.created_at) >= ?';
			$params[] = $filters['date_from'] . ' 00:00:00';
		}
		if (!empty($filters['date_to'])) {
			$conditions[] = 'COALESCE(b.date_approved, b.date_requested, b.created_at) <= ?';
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
			$conditions[] = 'b.status IN ("Distributed", "Pending Confirmation", "Received")';
		}
		if ($conditions) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql .= ' ORDER BY COALESCE(b.date_approved, b.date_requested, b.created_at) DESC, i.name ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$results = $stmt->fetchAll();
		
		// Calculate remaining stock for each record
		// We need to calculate stock after each distribution chronologically
		// Get current stock for all ingredients
		$currentStock = [];
		$ingredientStmt = $this->db->query('SELECT id, quantity FROM ingredients');
		foreach ($ingredientStmt->fetchAll() as $ing) {
			$currentStock[(int)$ing['id']] = (float)$ing['quantity'];
		}
		
		// Sort results by date ascending to calculate remaining stock correctly
		usort($results, function($a, $b) {
			$aDate = $a['distribution_date'] ?? '';
			$bDate = $b['distribution_date'] ?? '';
			if ($aDate === $bDate) return 0;
			return $aDate < $bDate ? -1 : 1;
		});
		
		// Calculate remaining stock for each distribution
		foreach ($results as &$row) {
			$ingredientId = (int)$row['id'];
			$quantityUsed = (float)$row['total_quantity'];
			
			// Remaining stock = current stock (which is after this distribution)
			$row['remaining_stock'] = $currentStock[$ingredientId] ?? 0;
			
			// Add back the distributed quantity for next iteration (working backwards)
			$currentStock[$ingredientId] = ($currentStock[$ingredientId] ?? 0) + $quantityUsed;
		}
		unset($row);
		
		// Sort back by date descending for display
		usort($results, function($a, $b) {
			$aDate = $a['distribution_date'] ?? '';
			$bDate = $b['distribution_date'] ?? '';
			if ($aDate === $bDate) return 0;
			return $aDate > $bDate ? -1 : 1;
		});
		
		return $results;
	}
}


