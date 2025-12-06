<?php
declare(strict_types=1);

class InventoryController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$ingredientModel = new Ingredient();
		
		// Fetch ingredients and low stock items in parallel (they're separate queries)
		$ingredients = $ingredientModel->all();
		$lowStockItems = $ingredientModel->getLowStockItems();
		
		// Group low stock items by supplier (optimized array operations)
		$lowStockGroups = [];
		foreach ($lowStockItems as $item) {
			$rawSupplier = trim((string)($item['preferred_supplier'] ?? ''));
			$displaySupplier = $rawSupplier !== '' ? $rawSupplier : 'Unassigned Supplier';
			$key = strtolower($displaySupplier); // mb_strtolower not needed for ASCII supplier names
			if (!isset($lowStockGroups[$key])) {
				$lowStockGroups[$key] = [
					'label' => $displaySupplier,
					'items' => [],
				];
			}
			$lowStockGroups[$key]['items'][] = $item;
		}
		$lowStockGroups = array_values($lowStockGroups);
		
		// Check if ingredient sets feature is enabled (cache this check)
		$ingredientSetsSetting = Settings::get('features.ingredient_sets_enabled');
		$ingredientSetsEnabled = ($ingredientSetsSetting !== '0');
		$ingredientSets = [];
		if ($ingredientSetsEnabled) {
			$setModel = new IngredientSet();
			$ingredientSets = $setModel->listWithComponents();
		}
		
		$this->render('inventory/index.php', [
			'ingredients' => $ingredients,
			'ingredientSets' => $ingredientSets,
			'ingredientSetsEnabled' => $ingredientSetsEnabled,
			'lowStockGroups' => $lowStockGroups,
            'flash' => $_SESSION['flash_inventory'] ?? null,
		]);
        unset($_SESSION['flash_inventory']);
	}

	public function store(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$name = trim((string)($_POST['name'] ?? ''));
		$category = trim((string)($_POST['category'] ?? ''));
		$unit = trim((string)($_POST['unit'] ?? ''));
		$reorder = (float)($_POST['reorder_level'] ?? 0);
		$displayUnit = trim((string)($_POST['display_unit'] ?? '')) ?: null;
		$displayFactor = (float)($_POST['display_factor'] ?? 1);
		$supplier = trim((string)($_POST['preferred_supplier'] ?? ''));
		$restockQuantity = (float)($_POST['restock_quantity'] ?? 0);
		if ($name === '' || $unit === '') { $this->redirect('/inventory'); }
		$model = new Ingredient();
		// When creating ingredient manually in inventory, mark it as in_inventory = true
		$id = $model->create($name, $unit, $reorder, $displayUnit, $displayFactor > 0 ? $displayFactor : 1, $supplier, $restockQuantity, $category, true);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'ingredients', ['ingredient_id' => $id, 'name' => $name]);
		$this->redirect('/inventory');
	}

	public function updateMeta(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
		$supplier = trim((string)($_POST['preferred_supplier'] ?? ''));
		$restockQuantity = (float)($_POST['restock_quantity'] ?? 0);
		if ($id <= 0) { $this->redirect('/inventory'); }
		$model = new Ingredient();
		$model->updateMeta($id, $supplier, $restockQuantity);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'update', 'ingredients', ['ingredient_id' => $id, 'preferred_supplier' => $supplier, 'restock_quantity' => $restockQuantity]);
		$this->redirect('/inventory');
	}

    public function deleteIngredient(): void
    {
        Auth::requireRole(['Owner','Manager','Stock Handler']);
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) { http_response_code(400); echo 'Invalid CSRF token'; return; }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { $this->redirect('/inventory'); }

        $force = false;
        $db = Database::getConnection();
        // Fetch ingredient name for feedback
        $ingName = null;
        try {
            $stmt = $db->prepare('SELECT name FROM ingredients WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            $ingName = $row ? (string)$row['name'] : null;
        } catch (Throwable $e) {
            // ignore
        }
        try {
            $db->beginTransaction();
            // Do not alter FK checks in normal mode
            // Remove dependent rows referencing this ingredient
            // Requests
            $stmt = $db->prepare('DELETE r FROM requests r WHERE r.item_id = ?');
            $stmt->execute([$id]);
            $deletedRequests = $stmt->rowCount();
            // Deliveries (explicit, in case cascade is not present in live DB)
            $stmt = $db->prepare('DELETE d FROM deliveries d JOIN purchases p ON d.purchase_id = p.id WHERE p.item_id = ?');
            $stmt->execute([$id]);
            $deletedDeliveries = $stmt->rowCount();
            // Purchases
            $stmt = $db->prepare('DELETE FROM purchases WHERE item_id = ?');
            $stmt->execute([$id]);
            $deletedPurchases = $stmt->rowCount();
            // Finally ingredient
            $stmt = $db->prepare('DELETE FROM ingredients WHERE id = ?');
            $stmt->execute([$id]);
            $deletedIngredients = $stmt->rowCount();
            $db->commit();
            $_SESSION['flash_inventory'] = [
                'type' => $deletedIngredients > 0 ? 'success' : 'error',
                'text' => ($deletedIngredients > 0
                    ? ('Deleted "' . ($ingName ?? 'ingredient') . '" with ' . ($deletedRequests ?? 0) . ' request(s), ' . ($deletedPurchases ?? 0) . ' purchase(s), ' . ($deletedDeliveries ?? 0) . ' delivery(ies).')
                    : 'Delete did not affect any ingredient row. Please refresh and try again.')
            ];
        } catch (Throwable $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            $_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Delete failed. If this ingredient is referenced, enable Force Delete.'];
        } finally { }

        $logger = new AuditLog();
        $logger->log(Auth::id() ?? 0, 'delete', 'ingredients', ['ingredient_id' => $id, 'force' => (bool)$force]);
        $this->redirect('/inventory');
    }

	public function storeSet(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}

		$setId = (int)($_POST['set_id'] ?? 0);
		$name = trim((string)($_POST['set_name'] ?? ''));
		$description = trim((string)($_POST['set_description'] ?? '')) ?: null;
		$componentsRaw = (string)($_POST['components_json'] ?? '[]');
		$components = json_decode($componentsRaw, true);

		if ($name === '') {
			$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Set name is required.'];
			$this->redirect('/inventory');
		}

		if (!is_array($components) || empty($components)) {
			$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Add at least one ingredient to the set.'];
			$this->redirect('/inventory');
		}

		$ingredientModel = new Ingredient();
		$setModel = new IngredientSet();
		$normalized = [];
		$seen = [];

		foreach ($components as $component) {
			$ingredientId = (int)($component['ingredient_id'] ?? 0);
			$quantity = (float)($component['quantity'] ?? 0);
			if ($ingredientId <= 0 || $quantity <= 0) {
				continue;
			}
			if (isset($seen[$ingredientId])) {
				$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Each ingredient can only appear once in a set.'];
				$this->redirect('/inventory');
			}
			$ingredient = $ingredientModel->find($ingredientId);
			if (!$ingredient) {
				$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'One of the selected ingredients no longer exists. Refresh and try again.'];
				$this->redirect('/inventory');
			}
			$seen[$ingredientId] = true;
			$normalized[] = [
				'ingredient_id' => $ingredientId,
				'quantity' => $quantity,
			];
		}

		if (empty($normalized)) {
			$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Unable to save set without valid components.'];
			$this->redirect('/inventory');
		}

		try {
			$logger = new AuditLog();
			if ($setId > 0) {
				$setModel->update($setId, $name, $description, $normalized);
				$logger->log(Auth::id() ?? 0, 'update', 'ingredient_sets', ['set_id' => $setId, 'name' => $name]);
				$_SESSION['flash_inventory'] = ['type' => 'success', 'text' => 'Set updated successfully.'];
			} else {
				$setId = $setModel->create($name, $description, $normalized, Auth::id());
				$logger->log(Auth::id() ?? 0, 'create', 'ingredient_sets', ['set_id' => $setId, 'name' => $name]);
				$_SESSION['flash_inventory'] = ['type' => 'success', 'text' => 'Set created successfully.'];
			}
		} catch (Throwable $e) {
			$_SESSION['flash_inventory'] = ['type' => 'error', 'text' => 'Failed to save set. Ensure the name is unique.'];
		}

		$this->redirect('/inventory');
	}

	public function deleteSet(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$setId = (int)($_POST['set_id'] ?? 0);
		if ($setId <= 0) {
			$this->redirect('/inventory');
		}
		$setModel = new IngredientSet();
		$setModel->delete($setId);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'delete', 'ingredient_sets', ['set_id' => $setId]);
		$_SESSION['flash_inventory'] = ['type' => 'success', 'text' => 'Set deleted.'];
		$this->redirect('/inventory');
	}

	public function import(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		
		// Handle GET request - show import form
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->render('inventory/import.php', [
				'flash' => $_SESSION['flash_inventory_import'] ?? null,
			]);
			unset($_SESSION['flash_inventory_import']);
			return;
		}
		
		// Handle POST request - process CSV file
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		
		if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
			$_SESSION['flash_inventory_import'] = ['type' => 'error', 'messages' => ['Please select a valid CSV file to upload.']];
			$this->redirect('/inventory/import');
			return;
		}
		
		$file = $_FILES['csv_file'];
		$tmpPath = $file['tmp_name'];
		$fileName = $file['name'];
		
		// Validate file extension
		$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		if ($extension !== 'csv') {
			$_SESSION['flash_inventory_import'] = ['type' => 'error', 'messages' => ['Only CSV files are allowed.']];
			$this->redirect('/inventory/import');
			return;
		}
		
		// Read and parse CSV
		$handle = fopen($tmpPath, 'r');
		if ($handle === false) {
			$_SESSION['flash_inventory_import'] = ['type' => 'error', 'messages' => ['Failed to read the CSV file.']];
			$this->redirect('/inventory/import');
			return;
		}
		
		// Skip first two rows (date row and header row)
		fgetcsv($handle, 0, ',', '"', '\\'); // Row 1: dates
		fgetcsv($handle, 0, ',', '"', '\\'); // Row 2: headers
		
		$ingredientModel = new Ingredient();
		$logger = new AuditLog();
		$userId = Auth::id() ?? 0;
		
		$stats = [
			'created' => 0,
			'updated' => 0,
			'skipped' => 0,
			'errors' => [],
		];
		
		// Normalize unit names (case-insensitive mapping)
		$normalizeUnit = function(string $unit): string {
			$unit = trim($unit);
			$lower = strtolower($unit);
			
			// Unit normalization map
			$unitMap = [
				'pack' => 'pack',
				'packs' => 'pack',
				'kg' => 'kg',
				'kilogram' => 'kg',
				'kilograms' => 'kg',
				'kl' => 'kg',
				'g' => 'g',
				'gram' => 'g',
				'grams' => 'g',
				'can' => 'can',
				'cans' => 'can',
				'bot' => 'bot',
				'bot.' => 'bot',
				'bottle' => 'bot',
				'bottles' => 'bot',
				'pc' => 'pcs',
				'pcs' => 'pcs',
				'piece' => 'pcs',
				'pieces' => 'pcs',
				'gallon' => 'gallon',
				'gallons' => 'gallon',
				'box' => 'box',
				'boxes' => 'box',
				'bag' => 'bag',
				'bags' => 'bag',
				'sack' => 'sack',
				'sacks' => 'sack',
				'l' => 'L',
				'liter' => 'L',
				'liters' => 'L',
				'litre' => 'L',
				'litres' => 'L',
			];
			
			return $unitMap[$lower] ?? $unit;
		};
		
		$rowNum = 2; // Track row number for error reporting
		while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
			$rowNum++;
			
			// Skip empty rows
			if (empty($row) || (count($row) < 2) || empty(trim($row[0] ?? ''))) {
				$stats['skipped']++;
				continue;
			}
			
			$itemName = trim($row[0] ?? '');
			$unit = trim($row[1] ?? '');
			
			if (empty($itemName) || empty($unit)) {
				$stats['skipped']++;
				continue;
			}
			
			// Normalize unit
			$unit = $normalizeUnit($unit);
			
			// Find the last REMAIN value (rightmost non-empty REMAIN column)
			// Pattern: Item (0), Unit (1), [Date1: NEW STOCK (2), DEDUCTION (3), REMAIN (4)], [Date2: NEW STOCK (5), DEDUCTION (6), REMAIN (7)], ...
			// REMAIN columns are at positions: 4, 7, 10, 13, ... (every 3rd column starting from position 4)
			// Formula: position = 4 + (n * 3) where n >= 0
			$lastRemain = null;
			
			// Start from the end and work backwards
			// Check if position matches REMAIN pattern: (position - 4) % 3 == 0 and position >= 4
			for ($i = count($row) - 1; $i >= 4; $i--) {
				$offset = $i - 4;
				// Check if this position is a REMAIN column (every 3rd column starting from 4)
				if ($offset >= 0 && ($offset % 3) === 0) {
					$remainValue = trim($row[$i] ?? '');
					if ($remainValue !== '' && is_numeric($remainValue)) {
						$lastRemain = (float)$remainValue;
						break;
					}
				}
			}
			
			// Default to 0 if no remain value found
			$quantity = $lastRemain !== null ? $lastRemain : 0.0;
			
			// Convert kg to g: If unit is kg, convert to g as base unit
			$baseUnit = $unit;
			$displayUnit = null;
			$displayFactor = 1.0;
			$baseQuantity = $quantity;
			
			if (strtolower($unit) === 'kg') {
				// Convert kg to g: multiply quantity by 1000
				$baseUnit = 'g';
				$displayUnit = 'kg';
				$displayFactor = 1000.0;
				$baseQuantity = $quantity * 1000.0;
			}
			
			// Convert sack to g: Just change unit from sack to g, keep quantity as-is
			if (strtolower($unit) === 'sack') {
				$baseUnit = 'g';
				$displayUnit = null; // No display unit needed
				$displayFactor = 1.0;
				$baseQuantity = $quantity; // Keep quantity unchanged
			}
			
			// Check if ingredient already exists (case-insensitive)
			$existing = $ingredientModel->findByName($itemName);
			
			try {
				$db = Database::getConnection();
				if ($existing) {
					// Update existing ingredient
					$existingId = (int)$existing['id'];
					$existingUnit = strtolower($existing['unit'] ?? '');
					
					// If existing ingredient has kg base unit, convert it too
					if ($existingUnit === 'kg') {
						// Convert existing quantity from kg to g
						$existingQuantity = (float)($existing['quantity'] ?? 0);
						$existingQuantityG = $existingQuantity * 1000.0;
						
						// Update to g base unit with kg display unit
						$updateStmt = $db->prepare('UPDATE ingredients SET unit = ?, quantity = ?, display_unit = ?, display_factor = ? WHERE id = ?');
						$updateStmt->execute(['g', $existingQuantityG, 'kg', 1000.0, $existingId]);
						
						// Now update with new quantity (in g)
						$ingredientModel->updateQuantity($existingId, $baseQuantity);
					} else if ($existingUnit === 'sack') {
						// Convert existing ingredient from sack to g - just change unit, keep quantity
						$updateStmt = $db->prepare('UPDATE ingredients SET unit = ?, display_unit = ?, display_factor = ? WHERE id = ?');
						$updateStmt->execute(['g', null, 1.0, $existingId]);
						
						// Update with new quantity (unchanged, just unit changed)
						$ingredientModel->updateQuantity($existingId, $baseQuantity);
					} else {
						// Existing ingredient is not kg or sack, just update quantity
						$ingredientModel->updateQuantity($existingId, $baseQuantity);
						
						// Update unit, display_unit, and display_factor if they changed
						if (strtolower($existing['unit'] ?? '') !== strtolower($baseUnit)) {
							$updateStmt = $db->prepare('UPDATE ingredients SET unit = ?, display_unit = ?, display_factor = ? WHERE id = ?');
							$updateStmt->execute([$baseUnit, $displayUnit, $displayFactor, $existingId]);
						}
					}
					
					$stats['updated']++;
					$logger->log($userId, 'update', 'ingredients', [
						'ingredient_id' => $existingId,
						'name' => $itemName,
						'quantity' => $baseQuantity,
						'unit' => $baseUnit,
						'display_unit' => $displayUnit,
						'source' => 'csv_import',
					]);
				} else {
					// Create new ingredient with g base unit and kg display unit (if applicable)
					$id = $ingredientModel->create($itemName, $baseUnit, 0.0, $displayUnit, $displayFactor, null, 0.0, null, true);
					$ingredientModel->updateQuantity($id, $baseQuantity);
					$stats['created']++;
					$logger->log($userId, 'create', 'ingredients', [
						'ingredient_id' => $id,
						'name' => $itemName,
						'quantity' => $baseQuantity,
						'unit' => $baseUnit,
						'display_unit' => $displayUnit,
						'source' => 'csv_import',
					]);
				}
			} catch (Throwable $e) {
				$stats['errors'][] = "Row $rowNum ({$itemName}): " . $e->getMessage();
			}
		}
		
		fclose($handle);
		
		// Prepare success message
		$messages = [];
		if ($stats['created'] > 0) {
			$messages[] = "Created {$stats['created']} new ingredient(s).";
		}
		if ($stats['updated'] > 0) {
			$messages[] = "Updated {$stats['updated']} existing ingredient(s).";
		}
		if ($stats['skipped'] > 0) {
			$messages[] = "Skipped {$stats['skipped']} row(s).";
		}
		if (!empty($stats['errors'])) {
			$messages = array_merge($messages, $stats['errors']);
		}
		
		if (empty($messages)) {
			$messages[] = 'No items were imported. Please check your CSV file format.';
		}
		
		$_SESSION['flash_inventory_import'] = [
			'type' => !empty($stats['errors']) ? 'error' : 'success',
			'messages' => $messages,
		];
		
		$this->redirect('/inventory/import');
	}

	/**
	 * Migration: Convert all ingredients with base unit "kg" to "g"
	 * This ensures all weight-based ingredients use grams as the base unit for precise tracking
	 */
	public function migrateKgToG(): void
	{
		Auth::requireRole(['Owner','Manager']);
		
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		
		$db = Database::getConnection();
		$logger = new AuditLog();
		$userId = Auth::id() ?? 0;
		
		try {
			$db->beginTransaction();
			
			// Find all ingredients with unit = 'kg'
			$stmt = $db->query("SELECT id, name, unit, display_unit, display_factor, quantity, reorder_level FROM ingredients WHERE LOWER(unit) = 'kg'");
			$ingredients = $stmt->fetchAll();
			
			$converted = 0;
			$errors = [];
			
			foreach ($ingredients as $ing) {
				$id = (int)$ing['id'];
				$name = $ing['name'];
				$currentQuantity = (float)$ing['quantity'];
				$currentReorderLevel = (float)$ing['reorder_level'];
				$currentDisplayUnit = trim((string)($ing['display_unit'] ?? ''));
				$currentDisplayFactor = (float)($ing['display_factor'] ?? 1);
				
				// Convert quantities: 1 kg = 1000 g
				$newQuantity = $currentQuantity * 1000.0;
				$newReorderLevel = $currentReorderLevel * 1000.0;
				
				// Update display_unit and display_factor
				// If display_unit is not set, set it to 'kg' with factor 1000
				// If display_unit is already 'kg', ensure factor is 1000
				// If display_unit is something else, keep it but adjust factor if needed
				$newDisplayUnit = 'kg';
				$newDisplayFactor = 1000.0;
				
				if ($currentDisplayUnit !== '' && strtolower($currentDisplayUnit) !== 'kg') {
					// Keep existing display_unit but adjust factor
					// If current factor was 1 (meaning 1 kg = 1 kg), now it should be 1000 (1 kg = 1000 g)
					if ($currentDisplayFactor == 1.0) {
						$newDisplayFactor = 1000.0;
					} else {
						// If there was already a factor, multiply by 1000
						$newDisplayFactor = $currentDisplayFactor * 1000.0;
					}
					$newDisplayUnit = $currentDisplayUnit;
				}
				
				// Update the ingredient
				$updateStmt = $db->prepare("UPDATE ingredients SET unit = 'g', quantity = ?, reorder_level = ?, display_unit = ?, display_factor = ? WHERE id = ?");
				$updateStmt->execute([$newQuantity, $newReorderLevel, $newDisplayUnit, $newDisplayFactor, $id]);
				
				$converted++;
				$logger->log($userId, 'update', 'ingredients', [
					'ingredient_id' => $id,
					'name' => $name,
					'migration' => 'kg_to_g',
					'old_unit' => 'kg',
					'new_unit' => 'g',
					'old_quantity' => $currentQuantity,
					'new_quantity' => $newQuantity,
				]);
			}
			
			$db->commit();
			
			$_SESSION['flash_inventory'] = [
				'type' => 'success',
				'text' => "Migration completed: Converted {$converted} ingredient(s) from kg to g base unit.",
			];
		} catch (Throwable $e) {
			if ($db->inTransaction()) {
				$db->rollBack();
			}
			$_SESSION['flash_inventory'] = [
				'type' => 'error',
				'text' => 'Migration failed: ' . $e->getMessage(),
			];
		}
		
		$this->redirect('/inventory');
	}
}


