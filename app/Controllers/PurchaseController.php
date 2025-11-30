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

        // Group potential batch purchases by purchaser+supplier+payment+receipt+timestamp(second)
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
                    'payment_type' => $p['payment_type'] ?? 'Card',
                    'cash_base_amount' => isset($p['cash_base_amount']) ? (float)$p['cash_base_amount'] : null,
                    'paid_at' => $p['paid_at'] ?? null,
                    'items' => [],
                    'quantity_sum' => 0.0,
                    'delivered_sum' => 0.0,
                    'cost_sum' => 0.0,
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
                    if ($iid > 0 && $q > 0 && $c >= 0) { $itemsFromJson[] = [$iid, $q, $c]; }
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
                if (empty($itemIds) || empty($quantities) || $supplier === '') { $this->redirect('/purchases'); }
                for ($z=0; $z < min(count($itemIds), count($quantities), count($rowCosts)); $z++) {
                    $iid = (int)$itemIds[$z];
                    $q = (float)$quantities[$z];
                    $c = (float)$rowCosts[$z];
                    if ($iid > 0 && $q > 0 && $c >= 0) { $itemsFromJson[] = [$iid, $q, $c]; }
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
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($receiptUpload['tmp_name']);
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
        $logger = new AuditLog();
        if ($isBatch) {
            foreach ($itemsFromJson as [$iid, $qtyBase, $costRow]) {
                $id = $purchaseModel->create(Auth::id() ?? 0, $iid, $supplier, $qtyBase, $costRow, $receiptUrl, $paymentStatus, $paymentType, $baseAmount);
                $logger->log(Auth::id() ?? 0, 'create', 'purchases', ['purchase_id' => $id, 'item_id' => $iid, 'quantity' => $qtyBase, 'cost' => $costRow, 'payment_type' => $paymentType, 'base_amount' => $baseAmount]);
            }
        } else {
            // Convert to base units based on ingredient base unit and selected UI unit (single item path)
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
            $id = $purchaseModel->create(Auth::id() ?? 0, $itemId, $supplier, $quantity, $cost, $receiptUrl, $paymentStatus, $paymentType, $baseAmount);
            $logger->log(Auth::id() ?? 0, 'create', 'purchases', ['purchase_id' => $id, 'item_id' => $itemId, 'quantity' => $quantity, 'cost' => $cost, 'payment_type' => $paymentType, 'base_amount' => $baseAmount]);
        }
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
}


