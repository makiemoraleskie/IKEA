<?php
declare(strict_types=1);

class Ingredient extends BaseModel
{
	private static ?array $columnCache = null;
	private bool $supplierFieldsEnsured = false;

	private function getColumns(): array
	{
		if (self::$columnCache === null) {
			self::$columnCache = $this->db->query('SHOW COLUMNS FROM ingredients')->fetchAll(PDO::FETCH_COLUMN);
		}
		return self::$columnCache;
	}

	private function ensureSupplierFields(): void
	{
		if ($this->supplierFieldsEnsured) {
			return;
		}
		$columns = $this->getColumns();
		if (!in_array('category', $columns, true)) {
			$this->db->exec("ALTER TABLE ingredients ADD COLUMN category VARCHAR(120) NOT NULL DEFAULT '' AFTER name");
			self::$columnCache = null; // Clear cache after schema change
		}
		if (!in_array('preferred_supplier', $columns, true)) {
			$this->db->exec("ALTER TABLE ingredients ADD COLUMN preferred_supplier VARCHAR(160) NOT NULL DEFAULT '' AFTER reorder_level");
			self::$columnCache = null;
		}
		if (!in_array('restock_quantity', $columns, true)) {
			$this->db->exec("ALTER TABLE ingredients ADD COLUMN restock_quantity DECIMAL(16,4) NOT NULL DEFAULT 0 AFTER preferred_supplier");
			self::$columnCache = null;
		}
		$this->supplierFieldsEnsured = true;
	}

	public function all(): array
	{
		$this->ensureSupplierFields();
		// Check if in_inventory column exists (use cached column list)
		$columns = $this->getColumns();
		$hasInInventoryColumn = in_array('in_inventory', $columns, true);
		
		if ($hasInInventoryColumn) {
			// Use in_inventory column if it exists - only show ingredients marked as in inventory
			// This is much faster than the JOIN query
			$sql = 'SELECT id, name, category, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity 
				FROM ingredients 
				WHERE in_inventory = 1 
				ORDER BY name ASC';
		} else {
			// Fallback: Only show ingredients that have been delivered at least once OR have quantity > 0
			// Optimized: Use EXISTS subquery instead of LEFT JOINs for better performance
			$sql = 'SELECT i.id, i.name, i.category, i.unit, i.display_unit, i.display_factor, i.quantity, i.reorder_level, i.preferred_supplier, i.restock_quantity 
				FROM ingredients i
				WHERE i.quantity > 0 
				   OR EXISTS (
					   SELECT 1 FROM purchases p 
					   INNER JOIN deliveries d ON d.purchase_id = p.id 
					   WHERE p.item_id = i.id
				   )
				ORDER BY i.name ASC';
		}
		return $this->db->query($sql)->fetchAll();
	}

	public function find(int $id): ?array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, category, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity FROM ingredients WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function findByName(string $name): ?array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, category, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity FROM ingredients WHERE LOWER(name) = LOWER(?) LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([trim($name)]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function updateQuantity(int $id, float $newQuantity): void
	{
		$this->ensureSupplierFields();
		// Check if in_inventory column exists (use cached column list)
		$columns = $this->getColumns();
		$hasInInventoryColumn = in_array('in_inventory', $columns, true);
		
		if ($hasInInventoryColumn) {
			// When updating quantity (during delivery), also mark ingredient as "in inventory"
			$sql = 'UPDATE ingredients SET quantity = ?, in_inventory = 1 WHERE id = ?';
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$newQuantity, $id]);
		} else {
			$sql = 'UPDATE ingredients SET quantity = ? WHERE id = ?';
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$newQuantity, $id]);
		}
	}

	public function create(string $name, string $unit, float $reorderLevel, ?string $displayUnit = null, float $displayFactor = 1.0, ?string $preferredSupplier = null, float $restockQuantity = 0.0, ?string $category = null, bool $inInventory = false): int
	{
		$this->ensureSupplierFields();
		// Check if in_inventory column exists (use cached column list)
		$columns = $this->getColumns();
		$hasInInventoryColumn = in_array('in_inventory', $columns, true);
		
		if ($hasInInventoryColumn) {
			// in_inventory = 0 means ingredient exists but hasn't been delivered yet (won't show in inventory list)
			// in_inventory = 1 means ingredient has been delivered at least once (will show in inventory list)
			$sql = 'INSERT INTO ingredients (name, category, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity, in_inventory) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?)';
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$name, $category ?? '', $unit, $displayUnit, $displayFactor, $reorderLevel, $preferredSupplier ?? '', $restockQuantity, $inInventory ? 1 : 0]);
		} else {
			$sql = 'INSERT INTO ingredients (name, category, unit, display_unit, display_factor, quantity, reorder_level, preferred_supplier, restock_quantity) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)';
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$name, $category ?? '', $unit, $displayUnit, $displayFactor, $reorderLevel, $preferredSupplier ?? '', $restockQuantity]);
		}
		return (int)$this->db->lastInsertId();
	}

	public function updateMeta(int $id, string $supplier, float $restockQuantity): void
	{
		$this->ensureSupplierFields();
		$sql = 'UPDATE ingredients SET preferred_supplier = ?, restock_quantity = ? WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$supplier, $restockQuantity, $id]);
	}

	public function countLowStockItems(): int
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT COUNT(*) AS cnt FROM ingredients WHERE quantity <= reorder_level OR quantity <= 0';
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		return (int)($row['cnt'] ?? 0);
	}

	public function getLowStockItems(?int $limit = null): array
	{
		$this->ensureSupplierFields();
		$sql = 'SELECT id, name, category, unit, quantity, reorder_level, preferred_supplier, restock_quantity
			FROM ingredients
			WHERE quantity <= reorder_level OR quantity <= 0
			ORDER BY preferred_supplier, name';
		if ($limit !== null && $limit > 0) {
			$sql .= ' LIMIT ' . (int)$limit;
		}
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


