<?php
declare(strict_types=1);

class Purchase extends BaseModel
{
    public function listAll(): array
	{
        // Ensure purchase_unit column exists
        $this->ensurePurchaseUnitColumn();
        
        $sql = 'SELECT p.*, u.name AS purchaser_name, i.name AS item_name, i.unit, i.display_unit, i.display_factor,
                COALESCE(p.purchase_unit, "") AS purchase_unit, COALESCE(p.purchase_quantity, 0) AS purchase_quantity,
                COALESCE(p.date_purchased, p.created_at, NOW()) AS date_purchased
			FROM purchases p
			JOIN users u ON p.purchaser_id = u.id
			JOIN ingredients i ON p.item_id = i.id
			ORDER BY COALESCE(p.date_purchased, p.created_at, NOW()) DESC';
		return $this->db->query($sql)->fetchAll();
	}

	private function ensurePurchaseUnitColumn(): void
	{
		static $ensured = false;
		if ($ensured) {
			return;
		}
		try {
			$columns = $this->db->query('SHOW COLUMNS FROM purchases')->fetchAll(PDO::FETCH_COLUMN);
			if (!in_array('purchase_unit', $columns, true)) {
				$this->db->exec("ALTER TABLE purchases ADD COLUMN purchase_unit VARCHAR(20) NOT NULL DEFAULT '' AFTER quantity");
			}
			if (!in_array('purchase_quantity', $columns, true)) {
				$this->db->exec("ALTER TABLE purchases ADD COLUMN purchase_quantity DECIMAL(16,4) NOT NULL DEFAULT 0 AFTER purchase_unit");
			}
		} catch (\Exception $e) {
			// Column might already exist or table doesn't exist yet
		}
		$ensured = true;
	}

    public function create(
        int $purchaserId,
        int $itemId,
        string $supplier,
        float $quantity,
        float $cost,
        ?string $receiptUrl,
        string $paymentStatus,
        string $paymentType = 'Card',
        ?float $cashBaseAmount = null,
        string $purchaseUnit = '',
        float $purchaseQuantity = 0.0
    ): int
	{
        $this->ensurePurchaseUnitColumn();
        $paidAt = $paymentStatus === 'Paid' ? date('Y-m-d H:i:s') : null;
        // If purchase_quantity not provided, use quantity (for backward compatibility)
        if ($purchaseQuantity <= 0) {
            $purchaseQuantity = $quantity;
        }
        $sql = 'INSERT INTO purchases (
                purchaser_id, item_id, supplier, quantity, purchase_unit, purchase_quantity, cost, receipt_url, payment_status, payment_type, cash_base_amount, date_purchased, paid_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
            )';
		$stmt = $this->db->prepare($sql);
        $stmt->execute([
            $purchaserId,
            $itemId,
            $supplier,
            $quantity,
            $purchaseUnit,
            $purchaseQuantity,
            $cost,
            $receiptUrl,
            $paymentStatus,
            $paymentType,
            $cashBaseAmount,
            $paidAt,
        ]);
		return (int)$this->db->lastInsertId();
	}

	public function find(int $id): ?array
	{
		$sql = 'SELECT * FROM purchases WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function setPaymentStatus(int $id, string $status): void
	{
		$sql = 'UPDATE purchases SET payment_status = ? WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status, $id]);
	}

	public function getTodayCount(): int
	{
		$sql = 'SELECT COUNT(*) AS cnt FROM purchases WHERE DATE(date_purchased) = CURDATE()';
		$row = $this->db->query($sql)->fetch();
		return (int)($row['cnt'] ?? 0);
	}

    public function countByPaymentStatus(string $status): int
    {
        // Count unique purchase groups (batches), not individual purchases
        // Get all purchases and group them the same way as PurchaseController
        $purchases = $this->listAll();
        
        // Get payment totals grouped by stable group_id
        $db = $this->db;
        try {
            $stmt = $db->query("SELECT purchase_group_id, SUM(amount) AS total_paid FROM payment_transactions GROUP BY purchase_group_id");
            $paymentTotals = [];
            while ($row = $stmt->fetch()) {
                $paymentTotals[$row['purchase_group_id']] = (float)$row['total_paid'];
            }
        } catch (\Exception $e) {
            $paymentTotals = [];
        }
        
        // Group purchases by stable key (without payment_status)
        $groups = [];
        foreach ($purchases as $p) {
            $ts = substr((string)($p['date_purchased'] ?? $p['created_at'] ?? ''), 0, 19);
            $stableKey = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
            $stableGroupId = substr(sha1($stableKey), 0, 10);
            
            if (!isset($groups[$stableGroupId])) {
                $groups[$stableGroupId] = [
                    'group_id' => $stableGroupId,
                    'cost_sum' => 0.0,
                    'payment_status' => $p['payment_status'] ?? 'Pending',
                ];
            }
            $groups[$stableGroupId]['cost_sum'] += (float)$p['cost'];
        }
        
        // Calculate current balance and determine payment status for each group
        $pendingCount = 0;
        foreach ($groups as $group) {
            $totalPaid = $paymentTotals[$group['group_id']] ?? 0.0;
            $currentBalance = max(0, $group['cost_sum'] - $totalPaid);
            
            // Determine actual payment status
            $actualStatus = ($currentBalance <= 0.01 && $totalPaid > 0) ? 'Paid' : 'Pending';
            
            if ($status === 'Pending' && $actualStatus === 'Pending') {
                $pendingCount++;
            } elseif ($status === 'Paid' && $actualStatus === 'Paid') {
                $pendingCount++;
            }
        }
        
        return $pendingCount;
    }

    public function averageCostPerItem(): array
    {
        $sql = 'SELECT item_id, SUM(cost) AS total_cost, SUM(quantity) AS total_quantity
                FROM purchases
                GROUP BY item_id';
        $rows = $this->db->query($sql)->fetchAll();
        $averages = [];
        foreach ($rows as $row) {
            $qty = (float)($row['total_quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $averages[(int)$row['item_id']] = (float)$row['total_cost'] / $qty;
        }
        return $averages;
    }

    public function dailyCounts(string $startDate, string $endDate): array
    {
        $sql = 'SELECT DATE(date_purchased) AS day, COUNT(*) AS total
                FROM purchases
                WHERE DATE(date_purchased) BETWEEN ? AND ?
                GROUP BY day';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['day']] = (int)$row['total'];
        }
        return $result;
    }

    public function markPaidWithReceipt(int $id, string $receiptUrl): void
    {
        $sql = 'UPDATE purchases SET payment_status = "Paid", receipt_url = ?, paid_at = NOW() WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$receiptUrl, $id]);
    }

    public function findByGroupId(string $groupId): array
    {
        $sql = 'SELECT * FROM purchases WHERE group_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll() ?: [];
    }

    public function deleteByGroupId(string $groupId): int
    {
        $sql = 'DELETE FROM purchases WHERE group_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->rowCount();
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM purchases WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($ids));
        return $stmt->fetchAll() ?: [];
    }

    public function deleteByIds(array $ids): int
    {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM purchases WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($ids));
        return $stmt->rowCount();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    public function getGroupPurchases(string $groupId): array
    {
        // Group ID is a hash of purchaser+supplier+payment+receipt+timestamp
        // We need to find all purchases that match this group by recalculating the hash
        $allPurchases = $this->listAll();
        $groupPurchases = [];
        foreach ($allPurchases as $p) {
            $ts = substr((string)($p['date_purchased'] ?? $p['created_at'] ?? ''), 0, 19);
            $key = ($p['purchaser_id'] ?? '') . '|' . ($p['supplier'] ?? '') . '|' . ($p['payment_status'] ?? '') . '|' . ($p['receipt_url'] ?? '') . '|' . $ts;
            $hash = substr(sha1($key), 0, 10);
            if ($hash === $groupId) {
                $groupPurchases[] = $p;
            }
        }
        return $groupPurchases;
    }
}


