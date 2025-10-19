<?php
declare(strict_types=1);

class DeliveryController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Stock Handler','Manager','Owner']);
		$purchaseModel = new Purchase();
		$purchases = $purchaseModel->listAll();
		$deliveryModel = new Delivery();
		$deliveries = $deliveryModel->listAll();
		$deliveredTotals = [];
		foreach ($purchases as $p) {
			$deliveredTotals[(int)$p['id']] = $deliveryModel->getDeliveredTotal((int)$p['id']);
		}
		$this->render('deliveries/index.php', [
			'purchases' => $purchases,
			'deliveries' => $deliveries,
			'deliveredTotals' => $deliveredTotals,
		]);
	}

	public function store(): void
	{
		Auth::requireRole(['Stock Handler','Manager','Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
        $purchaseId = (int)($_POST['purchase_id'] ?? 0);
        $quantityInput = (float)($_POST['quantity_received'] ?? 0);
        $quantityUnit = (string)($_POST['quantity_unit'] ?? '');
		$deliveryStatus = in_array(($_POST['delivery_status'] ?? ''), ['Partial','Complete'], true) ? (string)$_POST['delivery_status'] : 'Partial';
        if ($purchaseId <= 0 || $quantityInput <= 0) { $this->redirect('/deliveries'); }

		$purchaseModel = new Purchase();
		$purchase = $purchaseModel->find($purchaseId);
		if (!$purchase) { $this->redirect('/deliveries'); }

        $deliveryModel = new Delivery();
        // Convert quantity to base using purchase's item base unit
        $ingredientModel = new Ingredient();
        $item = $ingredientModel->find((int)$purchase['item_id']);
        $baseUnit = $item['unit'] ?? '';
        $displayUnit = $item['display_unit'] ?? '';
        $displayFactor = (float)($item['display_factor'] ?? 1);
        $factor = 1.0;
        if ($displayUnit && $quantityUnit === $displayUnit && $displayFactor > 0) {
            $factor = $displayFactor;
        } elseif (($baseUnit === 'g' && $quantityUnit === 'kg') || ($baseUnit === 'ml' && $quantityUnit === 'L')) {
            $factor = 1000.0;
        }
        $quantityReceived = $quantityInput * $factor;
        $deliveryId = $deliveryModel->create($purchaseId, $quantityReceived, $deliveryStatus);

		// Stock-in: add to ingredient quantity
		$ingredientModel = new Ingredient();
		$item = $ingredientModel->find((int)$purchase['item_id']);
		if ($item) {
			$newQty = (float)$item['quantity'] + $quantityReceived;
			$ingredientModel->updateQuantity((int)$purchase['item_id'], $newQty);
		}

		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'deliveries', ['delivery_id' => $deliveryId, 'purchase_id' => $purchaseId, 'quantity_received' => $quantityReceived]);
		$this->redirect('/deliveries');
	}
}


