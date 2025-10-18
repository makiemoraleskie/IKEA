<?php
declare(strict_types=1);

class InventoryController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$this->render('inventory/index.php', [
			'ingredients' => $ingredients,
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
}


