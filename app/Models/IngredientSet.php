<?php
declare(strict_types=1);

class IngredientSet extends BaseModel
{
	private bool $schemaEnsured = false;

	private function ensureSchema(): void
	{
		if ($this->schemaEnsured) {
			return;
		}

		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS ingredient_sets (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(160) NOT NULL,
				description VARCHAR(255) NULL,
				created_by INT UNSIGNED NULL,
				created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY uniq_set_name (name),
				CONSTRAINT fk_set_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
		);

		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS ingredient_set_items (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				set_id INT UNSIGNED NOT NULL,
				ingredient_id INT UNSIGNED NOT NULL,
				quantity DECIMAL(16,4) NOT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY uniq_set_item (set_id, ingredient_id),
				CONSTRAINT fk_set_items_set FOREIGN KEY (set_id) REFERENCES ingredient_sets(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT fk_set_items_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE RESTRICT ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
		);

		$this->schemaEnsured = true;
	}

	public function listWithComponents(): array
	{
		$this->ensureSchema();
		// Optimized: Use INNER JOIN for ingredients to avoid NULL rows, and only fetch what we need
		$sql = 'SELECT
				s.id AS set_id,
				s.name,
				s.description,
				s.created_by,
				s.created_at,
				i.id AS ingredient_id,
				i.name AS ingredient_name,
				i.unit AS ingredient_unit,
				i.display_unit,
				i.display_factor,
				i.quantity AS inventory_quantity,
				i.reorder_level,
				si.quantity AS component_quantity
			FROM ingredient_sets s
			INNER JOIN ingredient_set_items si ON si.set_id = s.id
			INNER JOIN ingredients i ON i.id = si.ingredient_id
			ORDER BY s.name ASC, i.name ASC';
		$stmt = $this->db->query($sql);
		$rows = $stmt->fetchAll();
		$sets = [];
		foreach ($rows as $row) {
			$setId = (int)($row['set_id'] ?? 0);
			if (!$setId) {
				continue;
			}
			if (!isset($sets[$setId])) {
				$sets[$setId] = [
					'id' => $setId,
					'name' => (string)$row['name'],
					'description' => $row['description'] ?? null,
					'created_by' => $row['created_by'] ?? null,
					'created_at' => $row['created_at'] ?? null,
					'components' => [],
				];
			}
			if (!empty($row['ingredient_id'])) {
				$sets[$setId]['components'][] = [
					'ingredient_id' => (int)$row['ingredient_id'],
					'ingredient_name' => (string)$row['ingredient_name'],
					'unit' => (string)$row['ingredient_unit'],
					'display_unit' => $row['display_unit'] ?? null,
					'display_factor' => (float)($row['display_factor'] ?? 1),
					'quantity' => (float)($row['component_quantity'] ?? 0),
					'inventory_quantity' => (float)($row['inventory_quantity'] ?? 0),
					'reorder_level' => (float)($row['reorder_level'] ?? 0),
				];
			}
		}
		return array_values($sets);
	}

	public function create(string $name, ?string $description, array $components, ?int $creatorId): int
	{
		$this->ensureSchema();
		$this->db->beginTransaction();
		try {
			$stmt = $this->db->prepare('INSERT INTO ingredient_sets (name, description, created_by) VALUES (?, ?, ?)');
			$stmt->execute([$name, $description, $creatorId]);
			$setId = (int)$this->db->lastInsertId();

			$itemStmt = $this->db->prepare('INSERT INTO ingredient_set_items (set_id, ingredient_id, quantity) VALUES (?, ?, ?)');
			foreach ($components as $component) {
				$itemStmt->execute([$setId, (int)$component['ingredient_id'], (float)$component['quantity']]);
			}

			$this->db->commit();
			return $setId;
		} catch (Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function delete(int $id): void
	{
		$this->ensureSchema();
		$stmt = $this->db->prepare('DELETE FROM ingredient_sets WHERE id = ?');
		$stmt->execute([$id]);
	}

	public function update(int $id, string $name, ?string $description, array $components): void
	{
		$this->ensureSchema();
		$this->db->beginTransaction();
		try {
			$stmt = $this->db->prepare('UPDATE ingredient_sets SET name = ?, description = ? WHERE id = ?');
			$stmt->execute([$name, $description, $id]);

			$this->db->prepare('DELETE FROM ingredient_set_items WHERE set_id = ?')->execute([$id]);

			$itemStmt = $this->db->prepare('INSERT INTO ingredient_set_items (set_id, ingredient_id, quantity) VALUES (?, ?, ?)');
			foreach ($components as $component) {
				$itemStmt->execute([$id, (int)$component['ingredient_id'], (float)$component['quantity']]);
			}

			$this->db->commit();
		} catch (Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}
}


