<?php
declare(strict_types=1);

class RequestController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler','Kitchen Staff']);
		$model = new RequestModel();
		$batches = $model->listBatches();
		$batchItems = [];
		foreach ($batches as $b) {
			$batchItems[(int)$b['id']] = $model->listItemsByBatch((int)$b['id']);
		}
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$this->render('requests/index.php', [
			'batches' => $batches,
			'batchItems' => $batchItems,
			'ingredients' => $ingredients,
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
		$items = $_POST['item_id'] ?? [];
		$quantities = $_POST['quantity'] ?? [];
		if (!is_array($items) || !is_array($quantities)) { $this->redirect('/requests'); }

		$model = new RequestModel();
		$logger = new AuditLog();
		$batchId = $model->createBatch(Auth::id() ?? 0);
		for ($i = 0; $i < count($items); $i++) {
			$itemId = (int)($items[$i] ?? 0);
			$quantity = (float)($quantities[$i] ?? 0);
			if ($itemId > 0 && $quantity > 0) {
				$requestId = $model->create(Auth::id() ?? 0, $itemId, $quantity, $batchId);
				$logger->log(Auth::id() ?? 0, 'create', 'requests', ['batch_id' => $batchId, 'request_id' => $requestId, 'item_id' => $itemId, 'quantity' => $quantity]);
			}
		}
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
		if (!$items) { $this->redirect('/requests'); }
		$ingredientModel = new Ingredient();
		// Validate stock for all items first
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
		// Deduct stocks
		foreach ($items as $req) {
			$item = $ingredientModel->find((int)$req['item_id']);
			$ingredientModel->updateQuantity((int)$req['item_id'], max(0.0, (float)$item['quantity'] - (float)$req['quantity']));
			$model->setStatus((int)$req['id'], 'Approved');
		}
		$model->setBatchStatus($batchId, 'Approved');
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'approve', 'requests', ['batch_id' => $batchId]);
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
}


