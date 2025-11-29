<?php
declare(strict_types=1);

class Ingredient extends BaseModel
{
	private bool $supplierFieldsEnsured = false;

	private function ensureSupplierFields(): void
	{
		if ($this->supplierFieldsEnsured) {
			return;
		}
		$columns = $this->db->query('SHOW COLUMNS FROM ingredients')->fetchAll(PDO::FETCH_COLUMN);
		if (!in_array('preferred_supplier', $columns, true)) {
			$this->db->exec("ALTER TABLE ingredients ADD COLUMN preferred_supplier VARCHAR(160) NOT NULL DEFAULT '' AFTER reorder_level");
		}
		if (!in_array('restock_quantity', $columns, true)) {
			$this->db->exec("ALTER TABLE ingredients ADD COLUMN restock_quantity DECIMAL(16,4) NOT NULL DEFAULT 0 AFTER preferred_supplier");
		}
		$this->supplierFieldsEnsured = true;
	}

	public function all(): array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity FROM ingredients ORDER BY name ASC';
		return $this->db->query($sql)->fetchAll();
	}

	public function find(int $id): ?array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity FROM ingredients WHERE id = ?';
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

	public function create(string $name, string $unit, float $reorderLevel, ?string $displayUnit = null, float $displayFactor = 1.0, ?string $preferredSupplier = null, float $restockQuantity = 0.0): int
	{
		$this->ensureSupplierFields();
		$sql = 'INSERT INTO ingredients (name, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity) VALUES (?, ?, ?, ?, 0, ?, ?, ?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$name, $unit, $displayUnit, $displayFactor, $reorderLevel, $preferredSupplier ?? '', $restockQuantity]);
		return (int)$this->db->lastInsertId();
	}

	public function updateMeta(int $id, string $supplier, float $restockQuantity): void
	{
		$this->ensureSupplierFields();
		$sql = 'UPDATE ingredients SET preferred_supplier = ?, restock_quantity = ? WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$supplier, $restockQuantity, $id]);
	}

	public function getLowStockItems(): array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, unit, quantity, reorder_level, preferred_supplier, restock_quantity
			FROM ingredients
			WHERE quantity <= reorder_level OR quantity <= 0
			ORDER BY preferred_supplier, name';
		$stmt = $this->db->query($sql);
		$items = $stmt->fetchAll();
		foreach ($items as &$item) {
			$available = (float)($item['quantity'] ?? 0);
			$reorder = (float)($item['reorder_level'] ?? 0);
			$recommended = (float)($item['restock_quantity'] ?? 0);
			if ($recommended <= 0) {
				$gap = $reorder - $available;
				if ($gap <= 0) {
					$gap = $reorder ?: 1;
				}
				$recommended = max($gap, 1);
			}
			$item['recommended_qty'] = $recommended;
			$item['stock_status'] = $available <= 0 ? 'Out of Stock' : 'Low Stock';
		}
		return $items;
	}
}


