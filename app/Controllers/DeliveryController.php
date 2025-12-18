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
		$allDeliveries = $deliveryModel->listAll();
		$deliveredTotals = [];
		foreach ($purchases as $p) {
			$deliveredTotals[(int)$p['id']] = $deliveryModel->getDeliveredTotal((int)$p['id']);
		}
		
		// Group deliveries by batch (same batch_id and date_received within same minute = same delivery modal submission)
		// Use stable group ID calculation (without payment_status) to match view
		$deliveryBatches = [];
		$batchPurchaseIds = []; // Track unique purchase_ids per batch_id
		
		foreach ($allDeliveries as $d) {
			$ts = substr((string)($d['date_purchased'] ?? ''), 0, 19);
			// Use stable key (without payment_status) to match view calculation
			$stableKey = ($d['purchaser_id'] ?? '') . '|' . ($d['supplier'] ?? '') . '|' . ($d['receipt_url'] ?? '') . '|' . $ts;
			$batchId = substr(sha1($stableKey), 0, 10);
			
			// Group by batch_id and date_received (rounded to minute for same submission)
			$dateReceived = $d['date_received'] ?? '';
			$dateKey = $dateReceived ? substr($dateReceived, 0, 16) : ''; // YYYY-MM-DD HH:MM
			$batchKey = $batchId . '|' . $dateKey;
			
			if (!isset($deliveryBatches[$batchKey])) {
				$deliveryBatches[$batchKey] = [
					'batch_id' => $batchId,
					'date_received' => $dateReceived,
					'delivery_status' => $d['delivery_status'] ?? 'Partial',
					'items_count' => 0,
					'first_delivery_id' => (int)$d['id'],
					'purchase_id' => (int)$d['purchase_id'],
					'supplier' => $d['supplier'] ?? '',
					'purchaser_name' => $d['purchaser_name'] ?? '',
					'date_purchased' => $d['date_purchased'] ?? '',
					'deliveries' => [],
					'purchase_ids' => [] // Track unique purchase IDs for this batch
				];
				$batchPurchaseIds[$batchId] = [];
			}
			
			// Track unique purchase IDs per batch_id (not per batchKey)
			$purchaseId = (int)$d['purchase_id'];
			if (!in_array($purchaseId, $batchPurchaseIds[$batchId], true)) {
				$batchPurchaseIds[$batchId][] = $purchaseId;
			}
			
			$deliveryBatches[$batchKey]['deliveries'][] = $d;
		}
		
		// Now recalculate items_count and status for each batch based on unique purchase items
		// Group by batch_id only (not batchKey) to get all deliveries for a batch
		$batchesByBatchId = [];
		foreach ($deliveryBatches as $batchKey => $batch) {
			$batchId = $batch['batch_id'];
			if (!isset($batchesByBatchId[$batchId])) {
				$batchesByBatchId[$batchId] = [
					'batch_id' => $batchId,
					'date_received' => $batch['date_received'],
					'first_delivery_id' => $batch['first_delivery_id'],
					'purchase_id' => $batch['purchase_id'],
					'supplier' => $batch['supplier'],
					'purchaser_name' => $batch['purchaser_name'],
					'date_purchased' => $batch['date_purchased'],
					'purchase_ids' => $batchPurchaseIds[$batchId] ?? []
				];
			}
			// Keep the most recent date_received
			if (strcmp($batch['date_received'], $batchesByBatchId[$batchId]['date_received']) > 0) {
				$batchesByBatchId[$batchId]['date_received'] = $batch['date_received'];
				$batchesByBatchId[$batchId]['first_delivery_id'] = $batch['first_delivery_id'];
			}
		}
		
		// Calculate items_count and status for each batch based on remaining quantities
		foreach ($batchesByBatchId as $batchId => &$batch) {
			$purchaseIds = $batch['purchase_ids'];
			if (empty($purchaseIds)) {
				$batch['items_count'] = 0;
				$batch['delivery_status'] = 'Partial';
				continue;
			}
			
			// Count unique purchase items
			$batch['items_count'] = count($purchaseIds);
			
			// Get receive_quantity totals for all purchases in this batch
			$receiveQuantityTotals = $deliveryModel->getReceiveQuantityTotals($purchaseIds);
			
			// Check if all items are complete (remaining_qty <= 0)
			$allComplete = true;
			foreach ($purchaseIds as $purchaseId) {
				$purchase = $purchaseModel->find($purchaseId);
				if (!$purchase) continue;
				
				$orderedQty = (float)($purchase['quantity'] ?? 0);
				$receivedQty = $receiveQuantityTotals[$purchaseId] ?? 0.0;
				$remainingQty = max(0.0, $orderedQty - $receivedQty);
				
				// If any item has remaining quantity > 0, batch is not complete
				if ($remainingQty > 0.0001) {
					$allComplete = false;
					break;
				}
			}
			
			// Set status based on remaining quantities
			$batch['delivery_status'] = $allComplete ? 'Complete' : 'Partial';
		}
		unset($batch);
		
		// Convert to array and sort by date_received descending
		$deliveries = array_values($batchesByBatchId);
		usort($deliveries, function($a, $b) {
			return strcmp($b['date_received'], $a['date_received']);
		});
        $hidePurchaseIds = $_SESSION['deliveries_hide_purchase_ids'] ?? [];
        unset($_SESSION['deliveries_hide_purchase_ids']);
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

        // Additionally hide any groups containing purchase IDs flagged after submission
        if (!empty($hidePurchaseIds)) {
            $hideSet = array_map('intval', $hidePurchaseIds);
            $groups = array_filter($groups, static function(array $group) use ($hideSet): bool {
                foreach ($group['items'] as $item) {
                    if (in_array((int)$item['id'], $hideSet, true)) {
                        return false;
                    }
                }
                return true;
            });
        }

        $awaitingPurchases = $deliveryModel->listOutstandingPurchases();
        
        $ingredientModel = new Ingredient();
        $ingredients = $ingredientModel->all();

        $flash = $_SESSION['flash_deliveries'] ?? null;
        unset($_SESSION['flash_deliveries']);

        $this->render('deliveries/index.php', [
            'purchases' => $purchases,
            'purchaseGroups' => array_values($groups),
            'deliveries' => $deliveries,
            'deliveredTotals' => $deliveredTotals,
            'awaitingPurchases' => $awaitingPurchases,
            'ingredients' => $ingredients,
            'flash' => $flash,
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
        if (!is_array($rows) || empty($rows)) { 
            $_SESSION['flash_deliveries'] = ['type' => 'error', 'text' => 'No delivery items were submitted. Select a batch and enter at least one quantity.'];
            $this->redirect('/deliveries'); 
        }

        $purchaseModel = new Purchase();
        $deliveryModel = new Delivery();
        $ingredientModel = new Ingredient();
        $logger = new AuditLog();
        $deliveredCache = [];
        $processed = 0;
        $processedPurchaseIds = [];

        // First pass: Check all items to determine if batch should be complete
        // Batch is complete only if ALL items have receiveQty >= purchaseQty
        $allItemsComplete = true;
        $purchaseReceiveQuantities = []; // Store purchase_id => receive_quantity mapping
        
        foreach ($rows as $row) {
            $purchaseId = (int)($row['purchase_id'] ?? 0);
            if ($purchaseId <= 0) continue;
            
            $purchase = $purchaseModel->find($purchaseId);
            if (!$purchase) continue;
            
            $purchaseQuantity = (float)($purchase['quantity'] ?? 0);
            $receiveQuantity = isset($row['receive_quantity']) ? (float)$row['receive_quantity'] : (float)($row['quantity'] ?? 0);
            
            $purchaseReceiveQuantities[$purchaseId] = $receiveQuantity;
            
            // If any item has receiveQty < purchaseQty, batch is not complete
            if ($receiveQuantity < $purchaseQuantity - 0.0001) {
                $allItemsComplete = false;
            }
        }
        
        // Determine batch status: Complete only if all items are complete
        $batchStatus = $allItemsComplete ? 'Complete' : 'Partial';

        foreach ($rows as $row) {
            $purchaseId = (int)($row['purchase_id'] ?? 0);
            $ingredientId = (int)($row['ingredient_id'] ?? 0);
            $quantityInput = (float)($row['quantity'] ?? 0);
            $quantityUnit = (string)($row['unit'] ?? '');
            
            if ($quantityInput <= 0) { continue; }
            if ($purchaseId <= 0) {
                $_SESSION['flash_deliveries'] = ['type' => 'error', 'text' => 'Select a purchase batch before recording a delivery.'];
                $this->redirect('/deliveries');
            }
            $purchase = $purchaseModel->find($purchaseId);
            if (!$purchase) {
                $_SESSION['flash_deliveries'] = ['type' => 'error', 'text' => 'Referenced purchase batch was not found. Please refresh and try again.'];
                $this->redirect('/deliveries');
            }

            $item = $ingredientModel->find((int)$ingredientId);
            if (!$item) {
                $_SESSION['flash_deliveries'] = ['type' => 'error', 'text' => 'Selected ingredient was not found in inventory.'];
                $this->redirect('/deliveries');
            }
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
            
            // Use batch status: Complete only if all items in batch are complete
            // Otherwise, use Partial
            $deliveryStatus = $batchStatus;
            
            // Update delivered cache for remaining calculation
            if (!array_key_exists($purchaseId, $deliveredCache)) {
                $deliveredCache[$purchaseId] = $deliveryModel->getDeliveredTotal($purchaseId);
            }
            
            // Get receive_quantity from frontend (the value from Receive Qty field in purchase unit)
            $receiveQuantity = isset($row['receive_quantity']) ? (float)$row['receive_quantity'] : $quantityInput;
            
            // Store delivery: use the exact entered amount (converted to base units)
            // Don't clamp to remaining - allow over-delivery
            $deliveryId = $deliveryModel->create($purchaseId, $ingredientId, $quantityReceived, $deliveryStatus, $quantityUnit, $receiveQuantity);
            $deliveredCache[$purchaseId] += $quantityReceived;

            // Auto-populate preferred_supplier from purchase if ingredient doesn't have one
            // This helps build supplier data over time without manual entry
            if ($item && empty($item['preferred_supplier']) && !empty($purchase['supplier'])) {
                $purchaseSupplier = trim((string)$purchase['supplier']);
                if ($purchaseSupplier !== '') {
                    $ingredientModel->updateMeta((int)$item['id'], $purchaseSupplier, (float)($item['restock_quantity'] ?? 0));
                }
            }

            // IMPORTANT: This is the ONLY place where inventory quantities are updated after a purchase.
            // PurchaseController::store() does NOT update inventory - only delivery confirmation does.
            // Stock-in update: Add the exact converted amount to inventory
            // If user enters 50 kg (base unit is g), this adds 50000 g to inventory
            // If user enters 50000 g (base unit is g), this adds 50000 g to inventory
            if ($item) {
                $currentQty = (float)$item['quantity'];
                $newQty = $currentQty + $quantityReceived;
                $ingredientModel->updateQuantity((int)$item['id'], $newQty);
            }

            $logger->log(Auth::id() ?? 0, 'create', 'deliveries', ['delivery_id' => $deliveryId, 'purchase_id' => $purchaseId, 'ingredient_id' => $ingredientId, 'quantity_received' => $quantityReceived, 'unit' => $quantityUnit]);
            $processed++;
            $processedPurchaseIds[] = $purchaseId;
        }
        if ($processed === 0) {
            $_SESSION['flash_deliveries'] = ['type' => 'error', 'text' => 'No delivery rows were processed. Ensure quantities are greater than zero.'];
        } else {
            $_SESSION['flash_deliveries'] = ['type' => 'success', 'text' => 'Delivery recorded successfully. Inventory has been updated.'];
            $_SESSION['deliveries_hide_purchase_ids'] = array_values(array_unique($processedPurchaseIds));
        }
        $this->redirect('/deliveries');
	}

	public function getBatchDetails(): void
	{
		Auth::requireRole(['Stock Handler','Manager','Owner']);
		
		// Suppress any error output that might interfere with JSON
		error_reporting(0);
		ini_set('display_errors', 0);
		
		header('Content-Type: application/json');
		
		try {
			$batchId = $_GET['batch_id'] ?? '';
			$purchaseId = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;
		
		if (empty($batchId) && $purchaseId <= 0) {
			http_response_code(400);
			echo json_encode(['error' => 'Batch ID or Purchase ID required']);
			return;
		}
		
		$purchaseModel = new Purchase();
		$deliveryModel = new Delivery();
		
		// Get purchases in the batch
		if ($purchaseId > 0) {
			// Get purchase to find batch ID (use stable group ID calculation without payment_status)
			$purchase = $purchaseModel->find($purchaseId);
			if (!$purchase) {
				http_response_code(404);
				echo json_encode(['error' => 'Purchase not found']);
				return;
			}
			$ts = substr((string)($purchase['date_purchased'] ?? $purchase['created_at'] ?? ''), 0, 19);
			// Use stable key (without payment_status) to match view calculation
			$stableKey = ($purchase['purchaser_id'] ?? '') . '|' . ($purchase['supplier'] ?? '') . '|' . ($purchase['receipt_url'] ?? '') . '|' . $ts;
			$batchId = substr(sha1($stableKey), 0, 10);
		}
		
		// Get all purchases and filter by stable group ID
		$allPurchases = $purchaseModel->listAll();
		$groupPurchases = [];
		foreach ($allPurchases as $p) {
			$ts = substr((string)($p['date_purchased'] ?? $p['created_at'] ?? ''), 0, 19);
			$stableKey = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
			$stableGroupId = substr(sha1($stableKey), 0, 10);
			if ($stableGroupId === $batchId) {
				$groupPurchases[] = $p;
			}
		}
		
		if (empty($groupPurchases)) {
			http_response_code(404);
			echo json_encode(['error' => 'Batch not found']);
			return;
		}
		
		// Get receive_quantity totals for each purchase (from receive item section)
		$purchaseIds = array_map(function($p) { return (int)$p['id']; }, $groupPurchases);
		$receiveQuantityTotals = $deliveryModel->getReceiveQuantityTotals($purchaseIds);
		
		// Build item details
		$items = [];
		foreach ($groupPurchases as $p) {
			$purchaseId = (int)$p['id'];
			$orderedQty = (float)$p['quantity'];
			// Use receive_quantity from receive item section (the value entered in Receive Qty field)
			$receivedQty = $receiveQuantityTotals[$purchaseId] ?? 0.0;
			// Remaining Qty = gap between Purchase Qty and Receive Qty
			$remainingQty = max(0.0, $orderedQty - $receivedQty);
			
			// Extract item name and unit
			$itemName = $p['item_name'] ?? 'Unknown Item';
			$displayUnit = $p['purchase_unit'] ?? $p['unit'] ?? 'pcs';
			
			// Check if purchase_unit contains item name in format "itemName|unit"
			$purchaseUnit = $p['purchase_unit'] ?? '';
			if (!empty($purchaseUnit) && strpos($purchaseUnit, '|') !== false) {
				$parts = explode('|', $purchaseUnit);
				if (count($parts) >= 2) {
					$itemName = trim($parts[0]);
					$displayUnit = trim($parts[1]);
				}
			}
			
			$items[] = [
				'purchase_id' => $purchaseId,
				'item_name' => $itemName,
				'ordered_qty' => $orderedQty,
				'received_qty' => $receivedQty, // From Receive Qty field in receive item section
				'remaining_qty' => $remainingQty, // Gap between Purchase Qty and Receive Qty
				'unit' => $displayUnit
			];
		}
		
		// Get batch info from first purchase
		$firstPurchase = $groupPurchases[0];
		// date_purchased is now guaranteed from the query (with fallback to created_at or NOW())
		$datePurchased = $firstPurchase['date_purchased'] ?? '';
		
		// Format date to ensure consistent format
		if (!empty($datePurchased)) {
			$timestamp = strtotime($datePurchased);
			if ($timestamp !== false) {
				$datePurchased = date('Y-m-d H:i:s', $timestamp);
			}
		}
		
		$response = [
			'batch_id' => $batchId,
			'supplier' => $firstPurchase['supplier'] ?? '',
			'purchaser_name' => $firstPurchase['purchaser_name'] ?? '',
			'date_purchased' => $datePurchased,
			'items' => $items
		];
			
			echo json_encode($response);
		} catch (\Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
		}
	}
}


