<?php
declare(strict_types=1);

class RequestController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler','Kitchen Staff']);
		$model = new RequestModel();
		$batches = $model->listBatches();
		$toPrepareBatches = $model->listBatches('To Prepare');
		$userRole = Auth::role();
		$userId = Auth::id() ?? 0;
		if ($userRole === 'Kitchen Staff') {
			$batches = array_values(array_filter($batches, static function ($batch) use ($userId) {
				return (int)($batch['staff_id'] ?? 0) === $userId;
			}));
			$toPrepareBatches = array_values(array_filter($toPrepareBatches, static function ($batch) use ($userId) {
				return (int)($batch['staff_id'] ?? 0) === $userId;
			}));
		}
		$batchItems = [];
		$allBatchesForItems = array_merge($batches, $toPrepareBatches);
		foreach ($allBatchesForItems as $b) {
			$batchItems[(int)$b['id']] = $model->listItemsByBatch((int)$b['id']);
		}
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$ingredientStock = [];
		foreach ($batches as $batch) {
			if (($batch['status'] ?? '') !== 'Distributed') {
				continue;
			}
			$items = $batchItems[(int)$batch['id']] ?? [];
			foreach ($items as $req) {
				$itemId = (int)($req['item_id'] ?? 0);
				if ($itemId <= 0 || isset($ingredientStock[$itemId])) {
					continue;
				}
				$ingredient = $ingredientModel->find($itemId);
				$ingredientStock[$itemId] = (float)($ingredient['quantity'] ?? 0);
			}
		}
		$setModel = new IngredientSet();
		$rawSets = $setModel->listWithComponents();
		$ingredientSets = [];
		foreach ($rawSets as $set) {
			if (empty($set['components'])) {
				continue;
			}
			$hasLowStock = false;
			$reason = '';
			foreach ($set['components'] as $component) {
				$currentQty = (float)($component['inventory_quantity'] ?? 0);
				$reorder = (float)($component['reorder_level'] ?? 0);
				if ($currentQty <= $reorder) {
					$hasLowStock = true;
					$reason = sprintf('"%s" needs restocking before this set can be requested.', $component['ingredient_name']);
					break;
				}
				if ($currentQty <= 0) {
					$hasLowStock = true;
					$reason = sprintf('"%s" is currently unavailable.', $component['ingredient_name']);
					break;
				}
			}
			$ingredientSets[] = [
				'id' => $set['id'],
				'name' => $set['name'],
				'description' => $set['description'],
				'components' => $set['components'],
				'component_summary' => implode(', ', array_map(static function ($component) {
					return $component['ingredient_name'];
				}, $set['components'])),
				'is_available' => !$hasLowStock,
				'unavailable_reason' => $reason ?: 'One or more ingredients in this set are below their reorder level.',
			];
		}
        $flash = $_SESSION['flash_requests'] ?? null;
        unset($_SESSION['flash_requests']);

		$this->render('requests/index.php', [
			'batches' => $batches,
			'toPrepareBatches' => $toPrepareBatches,
			'batchItems' => $batchItems,
			'ingredients' => $ingredients,
			'ingredientSets' => $ingredientSets,
			'ingredientStock' => $ingredientStock,
            'flash' => $flash,
		]);
	}

	public function store(): void
	{
		Auth::requireRole(['Kitchen Staff','Manager','Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$requesterName = trim((string)($_POST['requester_name'] ?? ''));
		$notes = trim((string)($_POST['ingredients_note'] ?? ''));
		$requestDate = trim((string)($_POST['request_date'] ?? ''));
		if ($requesterName === '' || $notes === '') {
			$_SESSION['flash_requests'] = ['type' => 'error', 'messages' => ['Please provide a name and describe the requested ingredients.']];
			$this->redirect('/requests');
		}
		if ($requestDate !== '' && strtotime($requestDate) === false) {
			$_SESSION['flash_requests'] = ['type' => 'error', 'messages' => ['Please provide a valid date.']];
			$this->redirect('/requests');
		}
		$model = new RequestModel();
		$batchId = $model->createBatch(Auth::id() ?? 0, $requesterName, $notes, $requestDate ?: null);
		$logger = new AuditLog();
		$logger->log(
			Auth::id() ?? 0,
			'create',
			'requests',
			[
				'batch_id' => $batchId,
				'summary' => $notes,
				'requester_name' => $requesterName,
			]
		);
        $_SESSION['flash_requests'] = ['type' => 'success', 'messages' => ['Request submitted. Pending approval.']];
		$this->redirect('/requests');
	}

	public function approve(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		// Approve entire batch by id
		$batchId = (int)($_POST['batch_id'] ?? 0);
		if ($batchId <= 0) { $this->redirect('/requests'); }
		$model = new RequestModel();
		$items = $model->listItemsByBatch($batchId);
		if ($items) {
			$ingredientModel = new Ingredient();
			foreach ($items as $req) {
				$item = $ingredientModel->find((int)$req['item_id']);
				if (!$item || ((float)$item['quantity'] - (float)$req['quantity']) < -0.0001) {
					$model->setBatchStatus($batchId, 'Rejected');
					$logger = new AuditLog();
					$logger->log(Auth::id() ?? 0, 'reject', 'requests', ['reason' => 'Insufficient stock', 'batch_id' => $batchId]);
					$this->redirect('/requests');
					return;
				}
			}
		}
		$model->setBatchStatus($batchId, 'To Prepare');
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'approve', 'requests', ['batch_id' => $batchId, 'next_stage' => 'To Prepare']);
		$batch = $model->findBatch($batchId);
		if ($batch) {
			$notification = new Notification();
			$approverName = Auth::user()['name'] ?? 'Admin';
			$message = sprintf('Your request #%d has been approved by %s. Please wait while it is being prepared.', $batchId, $approverName);
			$notification->create((int)$batch['staff_id'], $message, '/requests?batch=' . $batchId, 'info');
		}
		$_SESSION['flash_requests'] = ['type' => 'success', 'messages' => ['Batch moved to To Prepare.']];
		$this->redirect('/requests');
	}

	public function distribute(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$batchId = (int)($_POST['batch_id'] ?? 0);
		if ($batchId <= 0) { $this->redirect('/requests'); }
		$this->executeDistribution($batchId);
	}

	public function prepare(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$batchId = (int)($_POST['batch_id'] ?? 0);
		if ($batchId <= 0) { $this->redirect('/requests'); }
		$action = ($_POST['action'] ?? 'save') === 'distribute' ? 'distribute' : 'save';
		$model = new RequestModel();
		$batch = $model->findBatch($batchId);
		if (!$batch) { $this->redirect('/requests'); }
		$itemIds = $_POST['item_id'] ?? [];
		$quantities = $_POST['quantity'] ?? [];
		$items = [];
		$errors = [];
		$ingredientModel = new Ingredient();
		for ($i = 0; $i < count($itemIds); $i++) {
			$itemId = (int)($itemIds[$i] ?? 0);
			$quantity = (float)($quantities[$i] ?? 0);
			if ($itemId <= 0 || $quantity <= 0) {
				continue;
			}
			$ingredient = $ingredientModel->find($itemId);
			if (!$ingredient) {
				$errors[] = 'One of the selected ingredients no longer exists.';
				continue;
			}
			$items[] = [
				'item_id' => $itemId,
				'quantity' => $quantity,
			];
		}
		if (empty($items)) {
			$errors[] = 'Add at least one ingredient with a valid quantity.';
		}
		if (!empty($errors)) {
			$_SESSION['flash_requests'] = ['type' => 'error', 'messages' => $errors];
			$this->redirect('/requests');
		}
		$model->replaceBatchItems($batchId, (int)$batch['staff_id'], $items);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'prepare', 'requests', ['batch_id' => $batchId, 'items' => count($items)]);
		if ($action === 'distribute') {
			$this->executeDistribution($batchId);
			return;
		}
		$_SESSION['flash_requests'] = ['type' => 'success', 'messages' => ['Preparation saved. You can distribute when ready.']];
		$this->redirect('/requests');
	}

	public function reject(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$batchId = (int)($_POST['batch_id'] ?? 0);
		if ($batchId <= 0) { $this->redirect('/requests'); }
		$model = new RequestModel();
		$model->setBatchStatus($batchId, 'Rejected');
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'reject', 'requests', ['batch_id' => $batchId]);
		$this->redirect('/requests');
	}

	private function executeDistribution(int $batchId): void
	{
		$model = new RequestModel();
		$items = $model->listItemsByBatch($batchId);
		if (!$items) {
			$_SESSION['flash_requests'] = ['type' => 'error', 'messages' => ['No prepared ingredients found for this batch.']];
			$this->redirect('/requests');
			return;
		}
		$ingredientModel = new Ingredient();
		foreach ($items as $req) {
			$item = $ingredientModel->find((int)$req['item_id']);
			if (!$item || ((float)$item['quantity'] - (float)$req['quantity']) < -0.0001) {
				$_SESSION['flash_requests'] = ['type' => 'error', 'messages' => ['Insufficient stock to distribute this batch.']];
				$this->redirect('/requests');
				return;
			}
		}
		foreach ($items as $req) {
			$item = $ingredientModel->find((int)$req['item_id']);
			$ingredientModel->updateQuantity((int)$req['item_id'], max(0.0, (float)$item['quantity'] - (float)$req['quantity']));
			$model->setStatus((int)$req['id'], 'Approved');
		}
		$model->setBatchStatus($batchId, 'Distributed');
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'distribute', 'requests', ['batch_id' => $batchId]);
		$batch = $model->findBatch($batchId);
		if ($batch) {
			$notification = new Notification();
			$message = sprintf('Your request #%d is ready for pickup.', $batchId);
			$notification->create((int)$batch['staff_id'], $message, '/requests?status=distributed#requests-history', 'success');
		}
		$_SESSION['flash_requests'] = ['type' => 'success', 'messages' => ['Batch distributed and inventory updated.']];
		$this->redirect('/requests');
	}
}


