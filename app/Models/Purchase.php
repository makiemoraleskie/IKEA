<?php
declare(strict_types=1);

class Purchase extends BaseModel
{
    public function listAll(): array
	{
        $sql = 'SELECT p.*, u.name AS purchaser_name, i.name AS item_name, i.unit, i.display_unit, i.display_factor
			FROM purchases p
			JOIN users u ON p.purchaser_id = u.id
			JOIN ingredients i ON p.item_id = i.id
			ORDER BY p.date_purchased DESC';
		return $this->db->query($sql)->fetchAll();
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
        ?float $cashBaseAmount = null
    ): int
	{
        $paidAt = $paymentStatus === 'Paid' ? date('Y-m-d H:i:s') : null;
        $sql = 'INSERT INTO purchases (
                purchaser_id, item_id, supplier, quantity, cost, receipt_url, payment_status, payment_type, cash_base_amount, date_purchased, paid_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
            )';
		$stmt = $this->db->prepare($sql);
        $stmt->execute([
            $purchaserId,
            $itemId,
            $supplier,
            $quantity,
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
        $sql = 'SELECT COUNT(*) AS cnt FROM purchases WHERE payment_status = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
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
}


