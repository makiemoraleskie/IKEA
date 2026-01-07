<?php
declare(strict_types=1);

class IngredientLoss extends BaseModel
{
	public function create(int $ingredientId, float $quantity, string $reason, ?string $notes, int $recordedBy): int
	{
		$sql = 'INSERT INTO ingredient_losses (ingredient_id, quantity, reason, notes, recorded_by) VALUES (?, ?, ?, ?, ?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$ingredientId, $quantity, $reason, $notes, $recordedBy]);
		return (int)$this->db->lastInsertId();
	}

	public function list(array $filters = []): array
	{
		$conditions = [];
		$params = [];
		
		if (!empty($filters['ingredient_id'])) {
			$conditions[] = 'il.ingredient_id = ?';
			$params[] = (int)$filters['ingredient_id'];
		}
		
		if (!empty($filters['reason'])) {
			$conditions[] = 'il.reason = ?';
			$params[] = $filters['reason'];
		}
		
		if (!empty($filters['date_from'])) {
			$conditions[] = 'il.recorded_at >= ?';
			$params[] = $filters['date_from'];
		}
		
		if (!empty($filters['date_to'])) {
			$conditions[] = 'il.recorded_at <= ?';
			$params[] = $filters['date_to'];
		}
		
		$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
		
		$sql = "SELECT il.*, i.name as ingredient_name, i.unit, i.display_unit, i.display_factor,
		               u.name as recorded_by_name
		        FROM ingredient_losses il
		        INNER JOIN ingredients i ON il.ingredient_id = i.id
		        INNER JOIN users u ON il.recorded_by = u.id
		        $where
		        ORDER BY il.recorded_at DESC";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	public function getTotalLossByIngredient(int $ingredientId, ?string $dateFrom = null, ?string $dateTo = null): float
	{
		$conditions = ['il.ingredient_id = ?'];
		$params = [$ingredientId];
		
		if ($dateFrom !== null) {
			$conditions[] = 'il.recorded_at >= ?';
			$params[] = $dateFrom;
		}
		
		if ($dateTo !== null) {
			$conditions[] = 'il.recorded_at <= ?';
			$params[] = $dateTo;
		}
		
		$where = 'WHERE ' . implode(' AND ', $conditions);
		
		$sql = "SELECT COALESCE(SUM(il.quantity), 0) as total_loss
		        FROM ingredient_losses il
		        $where";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		return (float)($row['total_loss'] ?? 0);
	}
}

