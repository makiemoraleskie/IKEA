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
		// First, get ALL distributions (not filtered) to build complete stock timeline
		// This ensures accurate remaining stock calculation
		$allDistributionsSql = 'SELECT
				COALESCE(b.date_approved, b.date_requested, b.created_at) AS distribution_date,
				i.id AS ingredient_id,
				r.id AS request_id,
				r.quantity AS total_quantity,
				b.id AS batch_id
			FROM requests r
			JOIN request_batches b ON r.batch_id = b.id
			JOIN ingredients i ON r.item_id = i.id
			WHERE b.status IN ("Distributed", "Pending Confirmation", "Received")
			ORDER BY COALESCE(b.date_approved, b.date_requested, b.created_at) DESC, i.id ASC';
		
		$allDistributions = $this->db->query($allDistributionsSql)->fetchAll();
		
		// Get current stock for all ingredients
		$currentStock = [];
		$ingredientStmt = $this->db->query('SELECT id, quantity FROM ingredients');
		foreach ($ingredientStmt->fetchAll() as $ing) {
			$currentStock[(int)$ing['id']] = (float)$ing['quantity'];
		}
		
		// Calculate remaining stock after each distribution by working backwards
		// Start with current stock and add back distributed quantities chronologically (reverse order)
		$stockAfterDistribution = [];
		$runningStock = $currentStock; // Start with current stock
		
		foreach ($allDistributions as $dist) {
			$ingredientId = (int)$dist['ingredient_id'];
			$quantityUsed = (float)$dist['total_quantity'];
			$distDate = $dist['distribution_date'] ?? '';
			$requestId = (int)$dist['request_id'];
			$key = $distDate . '_' . $requestId;
			
			// Initialize if not set
			if (!isset($stockAfterDistribution[$ingredientId])) {
				$stockAfterDistribution[$ingredientId] = [];
			}
			
			// The remaining stock AFTER this distribution is the current running stock
			// (because we're working backwards from the most recent distribution)
			$stockAfterDistribution[$ingredientId][$key] = $runningStock[$ingredientId] ?? 0;
			
			// Add back the distributed quantity to get stock before this distribution
			// This becomes the "remaining stock after" for the previous distribution
			$runningStock[$ingredientId] = ($runningStock[$ingredientId] ?? 0) + $quantityUsed;
		}
		
		// Now get filtered results
		$sql = 'SELECT
				COALESCE(b.date_approved, b.date_requested, b.created_at) AS distribution_date,
				i.id,
				i.name,
				i.category,
				i.unit,
				i.display_unit,
				i.display_factor,
				r.id AS request_id,
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
		
		// Assign remaining stock from pre-calculated values
		foreach ($results as &$row) {
			$ingredientId = (int)$row['id'];
			$distDate = $row['distribution_date'] ?? '';
			$requestId = (int)($row['request_id'] ?? 0);
			$key = $distDate . '_' . $requestId;
			
			// Get pre-calculated remaining stock
			if (isset($stockAfterDistribution[$ingredientId][$key])) {
				$row['remaining_stock'] = $stockAfterDistribution[$ingredientId][$key];
			} else {
				// Fallback: get current stock if calculation failed
				$row['remaining_stock'] = $currentStock[$ingredientId] ?? 0;
			}
		}
		unset($row);
		
		return $results;
	}
}


