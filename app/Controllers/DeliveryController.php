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
        // Show ALL purchases (both Paid and Pending) so users can record deliveries immediately
        $groups = [];
        foreach ($purchases as $p) {
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
        
        $ingredientModel = new Ingredient();
        $ingredients = $ingredientModel->all();

        $this->render('deliveries/index.php', [
            'purchases' => $purchases,
            'purchaseGroups' => array_values($groups),
            'deliveries' => $deliveries,
            'deliveredTotals' => $deliveredTotals,
            'awaitingPurchases' => $awaitingPurchases,
            'ingredients' => $ingredients,
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
            $ingredientId = (int)($row['ingredient_id'] ?? 0);
            $quantityInput = (float)($row['quantity'] ?? 0);
            $quantityUnit = (string)($row['unit'] ?? '');
            
            if ($quantityInput <= 0) { continue; }
            
            // If no purchase_id provided, create a purchase automatically for restocking
            if ($purchaseId <= 0) {
                if ($ingredientId <= 0) { continue; }
                
                // Get ingredient to convert quantity to base units
                $item = $ingredientModel->find($ingredientId);
                if (!$item) { continue; }
                
                $baseUnit = $item['unit'] ?? '';
                $displayUnit = $item['display_unit'] ?? '';
                $displayFactor = (float)($item['display_factor'] ?? 1);
                $factor = 1.0;
                
                // Convert to base unit
                if ($displayUnit && $quantityUnit === $displayUnit && $displayFactor > 0) {
                    $factor = $displayFactor;
                } elseif ($baseUnit === 'g' && $quantityUnit === 'kg') {
                    $factor = 1000.0;
                } elseif ($baseUnit === 'kg' && $quantityUnit === 'g') {
                    $factor = 0.001;
                } elseif ($baseUnit === 'ml' && $quantityUnit === 'L') {
                    $factor = 1000.0;
                } elseif ($baseUnit === 'L' && $quantityUnit === 'ml') {
                    $factor = 0.001;
                } elseif ($quantityUnit === $baseUnit) {
                    $factor = 1.0;
                }
                
                $quantityBase = $quantityInput * $factor;
                
                // Create a purchase record for this restocking delivery
                $supplier = trim((string)($row['supplier'] ?? 'Restock'));
                $purchaseId = $purchaseModel->create(
                    Auth::id() ?? 0,
                    $ingredientId,
                    $supplier,
                    $quantityBase, // Store in base units
                    0.0, // No cost for restocking
                    null, // No receipt
                    'Paid', // Mark as paid since it's a direct restock
                    'Card',
                    null,
                    $quantityUnit, // Store the unit user entered
                    $quantityInput // Store the original quantity
                );
                $logger->log(Auth::id() ?? 0, 'create', 'purchases', [
                    'purchase_id' => $purchaseId,
                    'item_id' => $ingredientId,
                    'quantity' => $quantityBase,
                    'auto_created_for_restock' => true
                ]);
            }
            
            $purchase = $purchaseModel->find($purchaseId);
            if (!$purchase) { continue; }

            // Convert to base unit
            $item = $ingredientModel->find((int)$purchase['item_id']);
            $baseUnit = $item['unit'] ?? '';
            $displayUnit = $item['display_unit'] ?? '';
            $displayFactor = (float)($item['display_factor'] ?? 1);
            $factor = 1.0;
            
            // Handle unit conversion to base unit for storage and inventory
            // This ensures the exact amount entered (in any unit) is properly converted and added to inventory
            if ($displayUnit && $quantityUnit === $displayUnit && $displayFactor > 0) {
                $factor = $displayFactor;
            } elseif ($baseUnit === 'g' && $quantityUnit === 'kg') {
                $factor = 1000.0; // 1 kg = 1000 g
            } elseif ($baseUnit === 'kg' && $quantityUnit === 'g') {
                $factor = 0.001; // 1 g = 0.001 kg
            } elseif ($baseUnit === 'ml' && $quantityUnit === 'L') {
                $factor = 1000.0; // 1 L = 1000 ml
            } elseif ($baseUnit === 'L' && $quantityUnit === 'ml') {
                $factor = 0.001; // 1 ml = 0.001 L
            } elseif ($quantityUnit === $baseUnit) {
                $factor = 1.0; // Same unit, no conversion needed
            } else {
                // If unit doesn't match any known conversion, assume it's already in base unit
                // This handles cases where user enters a unit that matches the base unit
                $factor = 1.0;
            }
            // Convert to base unit: if user enters 50 kg and base is g, this becomes 50000 g
            // If user enters 50000 g and base is g, this stays 50000 g
            $quantityReceived = $quantityInput * $factor;
            $purchaseQuantity = (float)($purchase['quantity'] ?? 0);
            if (!array_key_exists($purchaseId, $deliveredCache)) {
                $deliveredCache[$purchaseId] = $deliveryModel->getDeliveredTotal($purchaseId);
            }
            $deliveredSoFar = $deliveredCache[$purchaseId];
            $remainingBefore = max(0.0, $purchaseQuantity - $deliveredSoFar);
            
            // Determine delivery status based on remaining quantity
            // But always add the exact amount entered to inventory
            if ($remainingBefore <= 0.0) {
                // Purchase already fully delivered, but still allow adding to inventory
                $deliveryStatus = 'Complete';
            } elseif ($quantityReceived >= $remainingBefore - 0.0001) {
                $deliveryStatus = 'Complete';
            } else {
                $deliveryStatus = 'Partial';
            }
            
            // Store delivery: use the exact entered amount (converted to base units)
            // Don't clamp to remaining - allow over-delivery
            $deliveryId = $deliveryModel->create($purchaseId, $quantityReceived, $deliveryStatus);
            $deliveredCache[$purchaseId] += $quantityReceived;

            // IMPORTANT: This is the ONLY place where inventory quantities are updated after a purchase.
            // PurchaseController::store() does NOT update inventory - only delivery confirmation does.
            // Stock-in update: Add the exact converted amount to inventory
            // If user enters 50 kg (base unit is g), this adds 50000 g to inventory
            // If user enters 50000 g (base unit is g), this adds 50000 g to inventory
            if ($item) {
                $currentQty = (float)$item['quantity'];
                $newQty = $currentQty + $quantityReceived;
                $ingredientModel->updateQuantity((int)$purchase['item_id'], $newQty);
            }

            $logger->log(Auth::id() ?? 0, 'create', 'deliveries', ['delivery_id' => $deliveryId, 'purchase_id' => $purchaseId, 'quantity_received' => $quantityReceived]);
        }
        $this->redirect('/deliveries');
	}
}


