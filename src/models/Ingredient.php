<?php
declare(strict_types=1);

class Ingredient extends BaseModel
{
	public function all(): array
	{
		$sql = 'SELECT id, name, unit, display_unit, display_factor, quantity, reorder_level FROM ingredients ORDER BY name ASC';
		return $this->db->query($sql)->fetchAll();
	}

	public function find(int $id): ?array
	{
		$sql = 'SELECT id, name, unit, display_unit, display_factor, quantity, reorder_level FROM ingredients WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function updateQuantity(int $id, float $newQuantity): void
	{
		$sql = 'UPDATE ingredients SET quantity = ? WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$newQuantity, $id]);
	}

	public function create(string $name, string $unit, float $reorderLevel, ?string $displayUnit = null, float $displayFactor = 1.0): int
	{
		$sql = 'INSERT INTO ingredients (name, unit, display_unit, display_factor, quantity, reorder_level) VALUES (?, ?, ?, ?, 0, ?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$name, $unit, $displayUnit, $displayFactor, $reorderLevel]);
		return (int)$this->db->lastInsertId();
	}
}


