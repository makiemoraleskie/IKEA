<?php
declare(strict_types=1);

class PurchaseController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Purchaser','Manager','Owner','Stock Handler']);
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
        $purchaseModel = new Purchase();
        $purchases = $purchaseModel->listAll();
        $deliveryModel = new Delivery();
        $deliveredTotals = [];
        foreach ($purchases as $p) {
            $deliveredTotals[(int)$p['id']] = $deliveryModel->getDeliveredTotal((int)$p['id']);
        }

        // Group potential batch purchases by purchaser+supplier+payment+receipt+timestamp(second)
        $groups = [];
        // Get payment transactions to calculate current balance
        $db = $purchaseModel->getDb();
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS payment_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_id INT NOT NULL,
                purchase_group_id VARCHAR(50) NOT NULL,
                amount DECIMAL(16,2) NOT NULL,
                payment_type VARCHAR(50) NOT NULL,
                receipt_url VARCHAR(255) NULL,
                timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_by INT NOT NULL,
                INDEX idx_purchase_id (purchase_id),
                INDEX idx_group_id (purchase_group_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // Table might already exist
        }
        
        // Get all payment transactions grouped by group_id and last receipt
        $stmt = $db->query("SELECT purchase_group_id, SUM(amount) AS total_paid FROM payment_transactions GROUP BY purchase_group_id");
        $paymentTotals = [];
        while ($row = $stmt->fetch()) {
            $paymentTotals[$row['purchase_group_id']] = (float)$row['total_paid'];
        }
        
        // Get last receipt URL for each group (most recent payment transaction with receipt)
        $stmt = $db->query("SELECT purchase_group_id, receipt_url FROM payment_transactions WHERE receipt_url IS NOT NULL AND receipt_url != '' ORDER BY timestamp DESC, id DESC");
        $lastReceipts = [];
        while ($row = $stmt->fetch()) {
            $groupId = $row['purchase_group_id'];
            if (!isset($lastReceipts[$groupId])) {
                $lastReceipts[$groupId] = $row['receipt_url'];
            }
        }
        
        // Map to store stable group_id for each purchase batch
        $stableGroupIdMap = [];
        
        foreach ($purchases as $p) {
            $ts = substr((string)($p['date_purchased'] ?? $p['created_at'] ?? ''), 0, 19);
            // Calculate stable group_id that doesn't change with payment_status
            // This ensures transactions remain accessible even after payment status changes
            $stableKey = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
            $stableGroupId = substr(sha1($stableKey), 0, 10);
            
            // Store stable group_id mapping (use first purchase's receipt_url as reference)
            if (!isset($stableGroupIdMap[$stableKey])) {
                $stableGroupIdMap[$stableKey] = $stableGroupId;
            }
            
            // For display grouping, still use payment_status to show different groups if needed
            $key = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['payment_status'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
            if (!isset($groups[$key])) {
                $groupId = substr(sha1($key), 0, 10);
                $costSum = 0.0; // Will be calculated below
                // Use stable group_id for looking up payment transactions
                $totalPaid = $paymentTotals[$stableGroupId] ?? 0.0;
                $currentBalance = $costSum - $totalPaid;
                
                $groups[$key] = [
                    'group_id' => $stableGroupId, // Use stable group_id that doesn't change with payment status
                    'purchaser_name' => $p['purchaser_name'] ?? '',
                    'supplier' => $p['supplier'] ?? '',
                    'payment_status' => $p['payment_status'] ?? 'Pending',
                    'receipt_url' => $p['receipt_url'] ?? '',
                    'date_purchased' => $p['date_purchased'] ?? '',
                    'payment_type' => $p['payment_type'] ?? 'Card',
                    'cash_base_amount' => isset($p['cash_base_amount']) ? (float)$p['cash_base_amount'] : null,
                    'paid_at' => $p['paid_at'] ?? null,
                    'items' => [],
                    'quantity_sum' => 0.0,
                    'delivered_sum' => 0.0,
                    'cost_sum' => 0.0,
                    'current_balance' => 0.0, // Will be updated after cost_sum is calculated
                    'first_id' => (int)$p['id'],
                ];
            }
            if (empty($groups[$key]['paid_at']) && !empty($p['paid_at'])) {
                $groups[$key]['paid_at'] = $p['paid_at'];
            }
            $groups[$key]['items'][] = $p;
            $groups[$key]['quantity_sum'] += (float)$p['quantity'];
            $groups[$key]['delivered_sum'] += (float)($deliveredTotals[(int)$p['id']] ?? 0);
            $groups[$key]['cost_sum'] += (float)$p['cost'];
            // If any row in this group is Cash, carry over base amount for display
            if (!empty($p['payment_type']) && $p['payment_type'] === 'Cash') {
                $groups[$key]['payment_type'] = 'Cash';
                if (isset($p['cash_base_amount']) && $p['cash_base_amount'] !== null) {
                    $groups[$key]['cash_base_amount'] = (float)$p['cash_base_amount'];
                }
            }
        }
        
        // Update current balance for each group after cost_sum is calculated
        foreach ($groups as $key => $group) {
            $totalPaid = $paymentTotals[$group['group_id']] ?? 0.0;
            $groups[$key]['current_balance'] = max(0, (float)$group['cost_sum'] - $totalPaid);
            // Update payment status if fully paid
            if ($groups[$key]['current_balance'] <= 0.01 && $totalPaid > 0) {
                $groups[$key]['payment_status'] = 'Paid';
                // If fully paid, use the last payment receipt instead of original purchase receipt
                // The receipt URL from payment_transactions is already in the correct format (/public/uploads/...)
                // and the view will handle prepending the base URL
                if (isset($lastReceipts[$group['group_id']]) && !empty($lastReceipts[$group['group_id']])) {
                    $groups[$key]['receipt_url'] = $lastReceipts[$group['group_id']];
                }
            }
        }

        $flash = $_SESSION['flash_purchases'] ?? null;
        unset($_SESSION['flash_purchases']);

        // Keep original purchases for potential other uses; pass grouped view-model
        $this->render('purchases/index.php', [
            'ingredients' => $ingredients,
            'purchases' => $purchases,
            'purchaseGroups' => array_values($groups),
            'flash' => $flash,
            'canViewCosts' => Settings::costVisibleForRole(Auth::role()),
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

        $supplier = trim((string)($_POST['supplier'] ?? ''));
        $purchaseTypeRaw = strtolower(trim((string)($_POST['purchase_type'] ?? '')));
        $purchaseTypeNormalized = str_replace([' ', '-'], '_', $purchaseTypeRaw);
        if ($purchaseTypeNormalized === 'delivery') {
            $paymentStatus = 'Pending';
        } elseif (in_array($purchaseTypeNormalized, ['in_store', 'instore'], true)) {
            $paymentStatus = 'Paid';
        } else {
            $paymentStatus = in_array(($_POST['payment_status'] ?? 'Pending'), ['Paid','Pending'], true) ? (string)$_POST['payment_status'] : 'Pending';
        }
        $paymentType = (string)($_POST['payment_type'] ?? 'Card');
        $baseAmount = isset($_POST['base_amount']) ? (float)$_POST['base_amount'] : null; // UI only
        if ($paymentType !== 'Cash' && $baseAmount !== null && $baseAmount > 0) {
            // If a base cash amount was provided, treat as Cash even if dropdown failed to post
            $paymentType = 'Cash';
        }

        // Support batch or single item submissions
        $postedItemsJson = trim((string)($_POST['items_json'] ?? ''));
        $itemsFromJson = [];
        if ($postedItemsJson !== '') {
            $decoded = json_decode($postedItemsJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $row) {
                    $iid = (int)($row['item_id'] ?? 0);
                    $q = (float)($row['quantity'] ?? 0);
                    $c = (float)($row['cost'] ?? 0);
                    $name = trim((string)($row['name'] ?? ''));
                    $unit = trim((string)($row['unit'] ?? ''));
                    if ($q > 0 && $c >= 0) { 
                        $itemsFromJson[] = [
                            'item_id' => $iid,
                            'quantity' => $q,
                            'cost' => $c,
                            'name' => $name,
                            'unit' => $unit
                        ]; 
                    }
                }
            }
        }
        $isBatch = !empty($itemsFromJson) || is_array($_POST['item_id'] ?? null);
        if (!$isBatch) {
            $itemId = (int)($_POST['item_id'] ?? 0);
            $quantityInput = (float)($_POST['quantity'] ?? 0);
            $unitSelected = (string)($_POST['quantity_unit'] ?? '');
            $cost = (float)($_POST['cost'] ?? 0);
            if ($itemId <= 0 || $quantityInput <= 0 || $cost < 0 || $supplier === '') {
                $this->redirect('/purchases');
            }
        } else {
            if (!empty($itemsFromJson)) {
                // OK
            } else {
                $itemIds = array_map('intval', $_POST['item_id'] ?? []);
                $quantities = array_map('floatval', $_POST['quantity'] ?? []); // already base units from UI
                $rowCosts = array_map('floatval', $_POST['row_cost'] ?? []);
                $originalQuantities = array_map('floatval', $_POST['original_quantity'] ?? []);
                $units = array_map('trim', $_POST['unit'] ?? []);
                if (empty($itemIds) || empty($quantities) || $supplier === '') { $this->redirect('/purchases'); }
                for ($z=0; $z < min(count($itemIds), count($quantities), count($rowCosts)); $z++) {
                    $iid = (int)$itemIds[$z];
                    $q = (float)$quantities[$z];
                    $c = (float)$rowCosts[$z];
                    $origQty = (float)($originalQuantities[$z] ?? $q);
                    $unit = trim((string)($units[$z] ?? ''));
                    if ($iid > 0 && $q > 0 && $c >= 0) { 
                        $itemsFromJson[] = [
                            'item_id' => $iid,
                            'quantity' => $q,
                            'cost' => $c,
                            'original_quantity' => $origQty,
                            'unit' => $unit
                        ]; 
                    }
                }
            }
        }

		$receiptUrl = null;
        $receiptUpload = $_FILES['receipt'] ?? null;
        if ($receiptUpload && (($receiptUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE)) {
            $receiptProblem = null;
            if (($receiptUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $receiptProblem = $this->describeUploadError((int)$receiptUpload['error']);
            } elseif (!is_uploaded_file((string)($receiptUpload['tmp_name'] ?? ''))) {
                $receiptProblem = 'Receipt upload failed before it could be saved. Please try again.';
            } else {
                $allowed = [
                    'image/jpeg' => '.jpg',
                    'image/pjpeg' => '.jpg',
                    'image/png' => '.png',
                    'image/webp' => '.webp',
                    'image/heic' => '.heic',
                    'image/heif' => '.heif',
                    'image/heic-sequence' => '.heic',
                    'image/heif-sequence' => '.heif',
                    'application/pdf' => '.pdf',
                ];
                $maxBytes = 10 * 1024 * 1024; // 10MB
                if (($receiptUpload['size'] ?? 0) > $maxBytes) {
                    $receiptProblem = 'Receipt exceeds the 10MB limit. Please compress the file or upload a PDF scan.';
                } else {
                    // Try to detect MIME type using finfo if available, otherwise use fallback methods
                    $mime = null;
                    if (class_exists('finfo')) {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->file($receiptUpload['tmp_name']);
                    } elseif (function_exists('mime_content_type')) {
                        $mime = mime_content_type($receiptUpload['tmp_name']);
                    } else {
                        // Fallback: use file extension from original filename
                        $originalName = $receiptUpload['name'] ?? '';
                        $extensionMap = [
                            'jpg' => 'image/jpeg',
                            'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            'webp' => 'image/webp',
                            'heic' => 'image/heic',
                            'heif' => 'image/heif',
                            'pdf' => 'application/pdf',
                        ];
                        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $mime = $extensionMap[$ext] ?? null;
                    }
                    
                    $ext = $allowed[$mime] ?? null;
                    if ($ext === null) {
                        $receiptProblem = 'Unsupported receipt file type. Allowed types: JPG, PNG, WebP, HEIC and PDF.';
                    } else {
                        $base = bin2hex(random_bytes(8));
                        $filename = $base . $ext;
                        $targetDir = BASE_PATH . '/public/uploads/';
                        if (!is_dir($targetDir)) { @mkdir($targetDir, 0755, true); }
                        $target = $targetDir . $filename;
                        if (move_uploaded_file($receiptUpload['tmp_name'], $target)) {
                            $receiptUrl = '/public/uploads/' . $filename;
                        } else {
                            $receiptProblem = 'Failed to save the uploaded receipt. Please try again.';
                        }
                    }
                }
            }
            if ($receiptProblem !== null) {
                $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => $receiptProblem];
            }
        }

        $purchaseModel = new Purchase();
        $ingredientModel = new Ingredient();
        $logger = new AuditLog();
        
        // IMPORTANT: Purchase recording does NOT update inventory quantities.
        // Inventory is only updated when deliveries are confirmed in DeliveryController::store().
        // This ensures the correct workflow: Purchase → Delivery → Inventory Update
        
        // Get a single placeholder ingredient that must exist (created manually or in deliveries)
        // This is used ONLY for database foreign key constraint - no new ingredients are created here
        $placeholderIngredient = $ingredientModel->findByName('Purchase Note');
        if (!$placeholderIngredient) {
            // If placeholder doesn't exist, try to find any ingredient to use as placeholder
            $allIngredients = $ingredientModel->all();
            $placeholderIngredient = !empty($allIngredients) ? $allIngredients[0] : null;
        }
        $placeholderId = $placeholderIngredient ? (int)$placeholderIngredient['id'] : 0;
        
        if ($isBatch) {
            $processedCount = 0;
            $skippedCount = 0;
            
            foreach ($itemsFromJson as $item) {
                $iid = (int)($item['item_id'] ?? 0);
                $costRow = (float)($item['cost'] ?? 0);
                $itemName = trim((string)($item['name'] ?? ''));
                $unit = trim((string)($item['unit'] ?? 'pcs'));
                
                // Get quantity exactly as entered (no conversions for batch purchases)
                $qtyBase = (float)($item['quantity'] ?? 0);
                $originalQty = (float)($item['original_quantity'] ?? $item['quantity'] ?? $qtyBase);
                
                // For batch purchases, always use the quantity exactly as entered
                // If item_id is 0, this is a free-form item - use placeholder
                if ($iid <= 0 && $itemName !== '') {
                    // Free-form item - use placeholder if available
                    // This is ONLY for database foreign key constraint
                    if ($placeholderId > 0) {
                        $iid = $placeholderId;
                    } else {
                        // No placeholder available - skip this item
                        $skippedCount++;
                        continue;
                    }
                }
                
                if ($iid > 0 && $originalQty > 0) {
                    // Create purchase record only - do NOT update inventory here
                    // Inventory will be updated only when delivery is confirmed in DeliveryController
                    // Store the actual item name in purchase_unit for display purposes when using placeholder
                    // Format: "itemName|unit" to store both item name and unit separately
                    if ($iid === $placeholderId && $itemName !== '') {
                        // Using placeholder - store item name and unit separated by pipe
                        $displayUnit = $itemName . '|' . $unit;
                    } else {
                        // Ingredient exists - just store the unit
                        $displayUnit = $unit;
                    }
                    // Store the original quantity exactly as entered (no conversions)
                    $id = $purchaseModel->create(Auth::id() ?? 0, $iid, $supplier, $originalQty, $costRow, $receiptUrl, $paymentStatus, $paymentType, $baseAmount, $displayUnit, $originalQty);
                    $logger->log(Auth::id() ?? 0, 'create', 'purchases', ['purchase_id' => $id, 'item_id' => $iid, 'quantity' => $originalQty, 'cost' => $costRow, 'payment_type' => $paymentType, 'base_amount' => $baseAmount, 'purchase_unit' => $displayUnit, 'purchase_quantity' => $originalQty]);
                    $processedCount++;
                }
            }
            
            // Show success or error based on processed count
            if ($processedCount > 0) {
                $msg = "Purchase batch recorded successfully. {$processedCount} item" . ($processedCount > 1 ? 's' : '') . " added.";
                if ($skippedCount > 0) {
                    $msg .= " {$skippedCount} item" . ($skippedCount > 1 ? 's' : '') . " skipped (no matching ingredients).";
                }
                $_SESSION['flash_purchases'] = ['type' => 'success', 'text' => $msg];
            } else {
                $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'No valid items to record. Please add items with valid quantities and costs.'];
            }
        } else {
            // Convert to base units based on ingredient base unit and selected UI unit (single item path)
            $ingredientModel = new Ingredient();
            $ingredient = $ingredientModel->find($itemId);
            if (!$ingredient) {
                $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Selected ingredient does not exist. Please select a valid ingredient from the list.'];
                $this->redirect('/purchases');
                return;
            }
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
            $unitSelected = trim((string)($_POST['unit'] ?? ''));
            // Create purchase record only - do NOT update inventory here
            // Inventory will be updated only when delivery is confirmed in DeliveryController
            $id = $purchaseModel->create(Auth::id() ?? 0, $itemId, $supplier, $quantity, $cost, $receiptUrl, $paymentStatus, $paymentType, $baseAmount, $unitSelected);
            $logger->log(Auth::id() ?? 0, 'create', 'purchases', ['purchase_id' => $id, 'item_id' => $itemId, 'quantity' => $quantity, 'cost' => $cost, 'payment_type' => $paymentType, 'base_amount' => $baseAmount, 'purchase_unit' => $unitSelected]);
            $_SESSION['flash_purchases'] = ['type' => 'success', 'text' => 'Purchase recorded successfully.'];
        }
		$this->redirect('/purchases');
	}

	public function markPaid(): void
	{
		Auth::requireRole(['Purchaser','Manager','Owner','Stock Handler']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
        $receiptUpload = $_FILES['receipt'] ?? null;
        if ($id <= 0) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Invalid purchase reference for marking as paid.'];
            $this->redirect('/purchases');
        }

        $purchaseModel = new Purchase();
        $purchase = $purchaseModel->find($id);
        if (!$purchase) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Purchase record not found.'];
            $this->redirect('/purchases');
        }
        $deliveryModel = new Delivery();
        $delivered = $deliveryModel->getDeliveredTotal($id);
        if (((float)$purchase['quantity'] - $delivered) > 0.0001) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Complete the delivery before marking this purchase as paid.'];
            $this->redirect('/purchases');
        }

        if (!$receiptUpload || (($receiptUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Upload a receipt image before marking this purchase as paid.'];
            $this->redirect('/purchases');
        }
        $receiptUrl = null;
        $allowed = [
            'image/jpeg' => '.jpg',
            'image/pjpeg' => '.jpg',
            'image/png' => '.png',
            'image/webp' => '.webp',
            'image/heic' => '.heic',
            'image/heif' => '.heif',
            'image/heic-sequence' => '.heic',
            'image/heif-sequence' => '.heif',
            'application/pdf' => '.pdf',
        ];
        $maxBytes = 10 * 1024 * 1024;
        if (($receiptUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => $this->describeUploadError((int)$receiptUpload['error'])];
            $this->redirect('/purchases');
        }
        if (($receiptUpload['size'] ?? 0) > $maxBytes) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Receipt exceeds the 10MB limit. Please compress the file or upload a PDF scan.'];
            $this->redirect('/purchases');
        }
        $tmpName = (string)($receiptUpload['tmp_name'] ?? '');
        if (!is_uploaded_file($tmpName)) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Receipt upload failed before it could be saved. Please try again.'];
            $this->redirect('/purchases');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);
        $ext = $allowed[$mime] ?? null;
        if (!$ext) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Unsupported receipt file type. Allowed types: JPG, PNG, WebP, HEIC and PDF.'];
            $this->redirect('/purchases');
        }
        $filename = bin2hex(random_bytes(8)) . $ext;
        $targetDir = BASE_PATH . '/public/uploads/';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }
        $target = $targetDir . $filename;
        if (!move_uploaded_file($tmpName, $target)) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Failed to save the uploaded receipt. Please try again.'];
            $this->redirect('/purchases');
        }
        $receiptUrl = '/public/uploads/' . $filename;

        $purchaseModel->markPaidWithReceipt($id, $receiptUrl);
        $logger = new AuditLog();
        $logger->log(Auth::id() ?? 0, 'mark_paid', 'purchases', ['purchase_id' => $id, 'receipt' => $receiptUrl]);
        $_SESSION['flash_purchases'] = ['type' => 'success', 'text' => 'Purchase marked as paid and receipt saved.'];
		$this->redirect('/purchases');
	}

    public function delete(): void
    {
        Auth::requireRole(['Purchaser','Manager','Owner','Stock Handler']);
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $idsRaw = trim((string)($_POST['ids'] ?? ''));
        $ids = array_values(array_filter(array_map('intval', explode(',', $idsRaw)), fn($v)=>$v>0));
        if (empty($ids)) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Invalid purchase selection.'];
            $this->redirect('/purchases');
        }

        $purchaseModel = new Purchase();
        $purchases = $purchaseModel->findByIds($ids);
        if (empty($purchases)) {
            $_SESSION['flash_purchases'] = ['type' => 'error', 'text' => 'Purchase records not found.'];
            $this->redirect('/purchases');
        }

        // Delete attached receipts (only local uploads under /public/uploads)
        foreach ($purchases as $p) {
            $url = (string)($p['receipt_url'] ?? '');
            if ($url && str_starts_with($url, '/public/uploads/')) {
                $path = BASE_PATH . $url;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }

        $deleted = $purchaseModel->deleteByIds($ids);
        $logger = new AuditLog();
        $logger->log(Auth::id() ?? 0, 'delete', 'purchases', ['ids' => $ids, 'rows' => $deleted]);
        $_SESSION['flash_purchases'] = ['type' => 'success', 'text' => 'Purchase group deleted successfully.'];
        $this->redirect('/purchases');
    }

    private function describeUploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Receipt is larger than the server limit. Please compress the file (max 10MB).',
            UPLOAD_ERR_PARTIAL => 'Receipt upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temporary folder is missing. Please contact the administrator.',
            UPLOAD_ERR_CANT_WRITE => 'Server could not write the receipt to disk. Try again or contact support.',
            UPLOAD_ERR_EXTENSION => 'Receipt upload was blocked by a server extension.',
            default => 'Receipt upload failed. Please try again.',
        };
    }

    public function recordPayment(): void
    {
        Auth::requireRole(['Purchaser','Manager','Owner','Stock Handler']);
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $purchaseId = (int)($_POST['purchase_id'] ?? 0);
        $groupId = trim((string)($_POST['purchase_group_id'] ?? ''));
        $amount = (float)($_POST['amount'] ?? 0);
        $paymentType = trim((string)($_POST['payment_type'] ?? ''));
        $paymentTypeOther = trim((string)($_POST['payment_type_other'] ?? ''));
        
        if ($paymentType === 'Other' && $paymentTypeOther !== '') {
            $paymentType = $paymentTypeOther;
        }
        
        if ($purchaseId <= 0 || $amount <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid purchase ID or amount']);
            return;
        }

        $receiptUpload = $_FILES['receipt'] ?? null;
        $receiptUrl = null;
        
        if ($receiptUpload && $receiptUpload['error'] === UPLOAD_ERR_OK) {
            $allowed = [
                'image/jpeg' => '.jpg',
                'image/png' => '.png',
                'image/webp' => '.webp',
                'image/heic' => '.heic',
                'image/heif' => '.heif',
                'application/pdf' => '.pdf',
            ];
            
            $mime = mime_content_type($receiptUpload['tmp_name']);
            if (!isset($allowed[$mime])) {
                http_response_code(400);
                echo json_encode(['error' => 'Unsupported receipt file type']);
                return;
            }
            
            $base = bin2hex(random_bytes(8));
            $filename = $base . $allowed[$mime];
            $targetDir = BASE_PATH . '/public/uploads/';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $target = $targetDir . $filename;
            if (move_uploaded_file($receiptUpload['tmp_name'], $target)) {
                $receiptUrl = '/public/uploads/' . $filename;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save receipt']);
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Receipt upload is required']);
            return;
        }

        $purchaseModel = new Purchase();
        $purchase = $purchaseModel->find($purchaseId);
        if (!$purchase) {
            http_response_code(404);
            echo json_encode(['error' => 'Purchase not found']);
            return;
        }

        // Ensure payment_transactions table exists
        $db = $purchaseModel->getDb();
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS payment_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_id INT NOT NULL,
                purchase_group_id VARCHAR(50) NOT NULL,
                amount DECIMAL(16,2) NOT NULL,
                payment_type VARCHAR(50) NOT NULL,
                receipt_url VARCHAR(255) NULL,
                timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_by INT NOT NULL,
                INDEX idx_purchase_id (purchase_id),
                INDEX idx_group_id (purchase_group_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // Table might already exist
        }

        // Get all purchases in the group to calculate total
        $groupPurchases = $purchaseModel->getGroupPurchases($groupId);
        $totalCost = array_sum(array_column($groupPurchases, 'cost'));
        
        // Get existing payments for this group
        $stmt = $db->prepare("SELECT SUM(amount) AS total_paid FROM payment_transactions WHERE purchase_group_id = ?");
        $stmt->execute([$groupId]);
        $result = $stmt->fetch();
        $totalPaid = (float)($result['total_paid'] ?? 0);
        $currentBalance = $totalCost - $totalPaid;
        
        // Record the payment transaction
        if ($receiptUrl === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Receipt upload failed or was not provided']);
            return;
        }
        $stmt = $db->prepare("INSERT INTO payment_transactions (purchase_id, purchase_group_id, amount, payment_type, receipt_url, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$purchaseId, $groupId, $amount, $paymentType, $receiptUrl, Auth::id() ?? 0]);
        
        // Update total paid
        $totalPaid += $amount;
        $newBalance = $totalCost - $totalPaid;
        
        // Update payment status if fully paid
        if ($newBalance <= 0.01) { // Allow small floating point differences
            // Update all purchases in the group to Paid
            foreach ($groupPurchases as $p) {
                $purchaseModel->setPaymentStatus((int)$p['id'], 'Paid');
                $stmt = $db->prepare("UPDATE purchases SET paid_at = NOW() WHERE id = ?");
                $stmt->execute([(int)$p['id']]);
            }
        }
        
        $logger = new AuditLog();
        $logger->log(Auth::id() ?? 0, 'create', 'payment_transactions', [
            'purchase_id' => $purchaseId,
            'group_id' => $groupId,
            'amount' => $amount,
            'payment_type' => $paymentType
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'current_balance' => max(0, $newBalance), 'total_paid' => $totalPaid]);
    }

    public function getTransactions(): void
    {
        Auth::requireRole(['Purchaser','Manager','Owner','Stock Handler']);
        header('Content-Type: application/json');
        
        $purchaseId = (int)($_GET['purchase_id'] ?? 0);
        $groupId = trim((string)($_GET['group_id'] ?? ''));
        
        if ($purchaseId <= 0 && $groupId === '') {
            echo json_encode(['error' => 'Invalid purchase ID or group ID']);
            return;
        }

        $purchaseModel = new Purchase();
        $db = $purchaseModel->getDb();
        
        // Ensure payment_transactions table exists
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS payment_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_id INT NOT NULL,
                purchase_group_id VARCHAR(50) NOT NULL,
                amount DECIMAL(16,2) NOT NULL,
                payment_type VARCHAR(50) NOT NULL,
                receipt_url VARCHAR(255) NULL,
                timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_by INT NOT NULL,
                INDEX idx_purchase_id (purchase_id),
                INDEX idx_group_id (purchase_group_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // Table might already exist
        }

        // Get transactions for this group
        $stmt = $db->prepare("SELECT * FROM payment_transactions WHERE purchase_group_id = ? ORDER BY timestamp DESC");
        $stmt->execute([$groupId]);
        $transactions = $stmt->fetchAll();
        
        // Get total cost for the group
        $groupPurchases = $purchaseModel->getGroupPurchases($groupId);
        $totalAmount = array_sum(array_column($groupPurchases, 'cost'));
        
        // Calculate totals
        $totalPaid = array_sum(array_column($transactions, 'amount'));
        $currentBalance = $totalAmount - $totalPaid;
        
        // Format transactions
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $formattedTransactions = [];
        foreach ($transactions as $txn) {
            $receiptUrl = $txn['receipt_url'];
            // Fix receipt URL - prepend base URL if it's a relative path
            if ($receiptUrl && !preg_match('#^https?://#', $receiptUrl)) {
                // It's a relative path, prepend base URL
                $receiptUrl = rtrim($baseUrl, '/') . '/' . ltrim($receiptUrl, '/');
            }
            
            $formattedTransactions[] = [
                'id' => (int)$txn['id'],
                'amount' => (float)$txn['amount'],
                'payment_type' => $txn['payment_type'],
                'receipt_url' => $receiptUrl,
                'timestamp' => date('M j, Y g:i A', strtotime($txn['timestamp'])),
            ];
        }
        
        echo json_encode([
            'transactions' => $formattedTransactions,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'current_balance' => max(0, $currentBalance),
        ]);
    }
}


