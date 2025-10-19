<?php
declare(strict_types=1);

class PurchaseController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Purchaser','Manager','Owner']);
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$purchaseModel = new Purchase();
		$purchases = $purchaseModel->listAll();
		$deliveryModel = new Delivery();
		$deliveredTotals = [];
		foreach ($purchases as $p) {
			$deliveredTotals[(int)$p['id']] = $deliveryModel->getDeliveredTotal((int)$p['id']);
		}
		$this->render('purchases/index.php', [
			'ingredients' => $ingredients,
			'purchases' => $purchases,
			'deliveredTotals' => $deliveredTotals,
		]);
	}

	public function store(): void
	{
		Auth::requireRole(['Purchaser','Manager','Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}

        $itemId = (int)($_POST['item_id'] ?? 0);
		$supplier = trim((string)($_POST['supplier'] ?? ''));
        $quantityInput = (float)($_POST['quantity'] ?? 0);
        $unitSelected = (string)($_POST['quantity_unit'] ?? '');
		$cost = (float)($_POST['cost'] ?? 0);
		$paymentStatus = in_array(($_POST['payment_status'] ?? 'Pending'), ['Paid','Pending'], true) ? (string)$_POST['payment_status'] : 'Pending';

        if ($itemId <= 0 || $quantityInput <= 0 || $cost < 0 || $supplier === '') {
			$this->redirect('/purchases');
		}

		$receiptUrl = null;
		if (!empty($_FILES['receipt']['name'])) {
			$upload = $_FILES['receipt'];
			if (is_uploaded_file($upload['tmp_name'])) {
				$allowed = [
					'image/jpeg' => '.jpg',
					'image/png' => '.png',
					'image/webp' => '.webp',
					'application/pdf' => '.pdf',
				];
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime = $finfo->file($upload['tmp_name']);
				$ext = $allowed[$mime] ?? null;

				$maxBytes = 5 * 1024 * 1024; // 5MB
				if ($ext && $upload['size'] <= $maxBytes) {
					$base = bin2hex(random_bytes(8));
					$filename = $base . $ext;
					$targetDir = BASE_PATH . '/public/uploads/';
					if (!is_dir($targetDir)) { @mkdir($targetDir, 0755, true); }
					$target = $targetDir . $filename;
					if (move_uploaded_file($upload['tmp_name'], $target)) {
						$receiptUrl = '/public/uploads/' . $filename;
					}
				}
			}
		}

        // Convert to base units based on ingredient base unit and selected UI unit
        $ingredientModel = new Ingredient();
        $ingredient = $ingredientModel->find($itemId);
        $baseUnit = $ingredient['unit'] ?? '';
        $displayUnit = $ingredient['display_unit'] ?? '';
        $displayFactor = (float)($ingredient['display_factor'] ?? 1);
        $factor = 1.0;
        if ($displayUnit && $unitSelected === $displayUnit && $displayFactor > 0) {
            $factor = $displayFactor;
        } elseif (($baseUnit === 'g' && $unitSelected === 'kg') || ($baseUnit === 'ml' && $unitSelected === 'L')) {
            $factor = 1000.0;
        }
        $quantity = $quantityInput * $factor;

        $purchaseModel = new Purchase();
        $id = $purchaseModel->create(Auth::id() ?? 0, $itemId, $supplier, $quantity, $cost, $receiptUrl, $paymentStatus);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'purchases', ['purchase_id' => $id, 'item_id' => $itemId, 'quantity' => $quantity, 'cost' => $cost]);
		$this->redirect('/purchases');
	}

	public function markPaid(): void
	{
		Auth::requireRole(['Purchaser','Manager','Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$purchaseModel = new Purchase();
			$purchaseModel->setPaymentStatus($id, 'Paid');
			$logger = new AuditLog();
			$logger->log(Auth::id() ?? 0, 'mark_paid', 'purchases', ['purchase_id' => $id]);
		}
		$this->redirect('/purchases');
	}
}


