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
		]);
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
}


