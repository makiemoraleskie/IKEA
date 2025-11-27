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
        // Build grouped purchases for selection (same grouping key as purchases page)
        $groups = [];
        foreach ($purchases as $p) {
			$status = strtoupper((string)($p['payment_status'] ?? ''));
			if ($status !== 'PAID') {
				continue;
			}
            $ts = substr((string)($p['date_purchased'] ?? $p['created_at'] ?? ''), 0, 19);
            $key = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['payment_status'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'group_id' => substr(sha1($key), 0, 10),
                    'purchaser_name' => $p['purchaser_name'] ?? '',
                    'supplier' => $p['supplier'] ?? '',
                    'payment_status' => $p['payment_status'] ?? 'Pending',
                    'receipt_url' => $p['receipt_url'] ?? '',
                    'date_purchased' => $p['date_purchased'] ?? '',
                    'items' => [],
                    'quantity_sum' => 0.0,
                    'delivered_sum' => 0.0,
                    'first_id' => (int)$p['id'],
                ];
            }
            $groups[$key]['items'][] = $p;
            $groups[$key]['quantity_sum'] += (float)$p['quantity'];
            $groups[$key]['delivered_sum'] += (float)($deliveredTotals[(int)$p['id']] ?? 0);
        }

        // Keep only groups that still have remaining quantities after recent deliveries
        $groups = array_filter($groups, static function(array $group) use ($deliveredTotals): bool {
            foreach ($group['items'] as $item) {
                $delivered = (float)($deliveredTotals[(int)$item['id']] ?? 0);
                if (((float)$item['quantity'] - $delivered) > 0.0001) {
                    return true;
                }
            }
            return false;
        });

        $awaitingPurchases = $deliveryModel->listOutstandingPurchases();

        $this->render('deliveries/index.php', [
            'purchases' => $purchases,
            'purchaseGroups' => array_values($groups),
            'deliveries' => $deliveries,
            'deliveredTotals' => $deliveredTotals,
            'awaitingPurchases' => $awaitingPurchases,
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
        $itemsJson = trim((string)($_POST['items_json'] ?? ''));
        $rows = json_decode($itemsJson, true);
        if (!is_array($rows) || empty($rows)) { $this->redirect('/deliveries'); }

        $purchaseModel = new Purchase();
        $deliveryModel = new Delivery();
        $ingredientModel = new Ingredient();
        $logger = new AuditLog();
        $deliveredCache = [];

        foreach ($rows as $row) {
            $purchaseId = (int)($row['purchase_id'] ?? 0);
            $quantityInput = (float)($row['quantity'] ?? 0);
            $quantityUnit = (string)($row['unit'] ?? '');
            if ($purchaseId <= 0 || $quantityInput <= 0) { continue; }
            $purchase = $purchaseModel->find($purchaseId);
            if (!$purchase) { continue; }

            // Convert to base unit
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
            $purchaseQuantity = (float)($purchase['quantity'] ?? 0);
            if (!array_key_exists($purchaseId, $deliveredCache)) {
                $deliveredCache[$purchaseId] = $deliveryModel->getDeliveredTotal($purchaseId);
            }
            $deliveredSoFar = $deliveredCache[$purchaseId];
            $remainingBefore = max(0.0, $purchaseQuantity - $deliveredSoFar);
            if ($remainingBefore <= 0.0) { continue; }

            if ($quantityReceived >= $remainingBefore - 0.0001) {
                $deliveryStatus = 'Complete';
                $quantityReceived = $remainingBefore;
            } else {
                $deliveryStatus = 'Partial';
            }
            $deliveryId = $deliveryModel->create($purchaseId, $quantityReceived, $deliveryStatus);
            $deliveredCache[$purchaseId] += $quantityReceived;

            // Stock-in update
            if ($item) {
                $newQty = (float)$item['quantity'] + $quantityReceived;
                $ingredientModel->updateQuantity((int)$purchase['item_id'], $newQty);
            }

            $logger->log(Auth::id() ?? 0, 'create', 'deliveries', ['delivery_id' => $deliveryId, 'purchase_id' => $purchaseId, 'quantity_received' => $quantityReceived]);
        }
        $this->redirect('/deliveries');
	}
}


