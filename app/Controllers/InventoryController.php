<?php
declare(strict_types=1);

class InventoryController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$setModel = new IngredientSet();
		$ingredientSets = $setModel->listWithComponents();
		$this->render('inventory/index.php', [
			'ingredients' => $ingredients,
			'ingredientSets' => $ingredientSets,
            'flash' => $_SESSION['flash_inventory'] ?? null,
		]);
        unset($_SESSION['flash_inventory']);
	}

	public function store(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$name = trim((string)($_POST['name'] ?? ''));
		$unit = trim((string)($_POST['unit'] ?? ''));
		$reorder = (float)($_POST['reorder_level'] ?? 0);
		$displayUnit = trim((string)($_POST['display_unit'] ?? '')) ?: null;
		$displayFactor = (float)($_POST['display_factor'] ?? 1);
		if ($name === '' || $unit === '') { $this->redirect('/inventory'); }
		$model = new Ingredient();
		$id = $model->create($name, $unit, $reorder, $displayUnit, $displayFactor > 0 ? $displayFactor : 1);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'ingredients', ['ingredient_id' => $id, 'name' => $name]);
		$this->redirect('/inventory');
	}

    public function deleteIngredient(): void
    {
        Auth::requireRole(['Owner','Manager']);
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
		Auth::requireRole(['Owner','Manager']);
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
		Auth::requireRole(['Owner','Manager']);
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
}


