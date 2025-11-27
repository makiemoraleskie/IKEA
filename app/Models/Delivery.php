<?php
declare(strict_types=1);

class Delivery extends BaseModel
{
    public function listAll(): array
    {
        $sql = 'SELECT 
                d.*,
                p.id AS purchase_id,
                p.purchaser_id,
                p.supplier,
                p.payment_status,
                p.receipt_url,
                p.date_purchased,
                p.item_id,
                p.quantity AS purchase_quantity,
                u.name AS purchaser_name,
                i.name AS item_name,
                i.unit,
                i.display_unit,
                i.display_factor
            FROM deliveries d
            JOIN purchases p ON d.purchase_id = p.id
            JOIN users u ON p.purchaser_id = u.id
            JOIN ingredients i ON p.item_id = i.id
            ORDER BY d.date_received DESC';
        return $this->db->query($sql)->fetchAll();
    }

	public function create(int $purchaseId, float $quantityReceived, string $deliveryStatus): int
	{
		$sql = 'INSERT INTO deliveries (purchase_id, quantity_received, delivery_status, date_received)
			VALUES (?, ?, ?, NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$purchaseId, $quantityReceived, $deliveryStatus]);
		return (int)$this->db->lastInsertId();
	}

	public function getDeliveredTotal(int $purchaseId): float
	{
		$sql = 'SELECT COALESCE(SUM(quantity_received),0) AS total FROM deliveries WHERE purchase_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$purchaseId]);
		$row = $stmt->fetch();
		return (float)($row['total'] ?? 0.0);
	}

	public function getPendingCount(): int
	{
        $sql = 'SELECT COUNT(*) AS pending
                FROM (
                    SELECT p.id,
                           p.quantity,
                           COALESCE(SUM(d.quantity_received), 0) AS delivered
                    FROM purchases p
                    LEFT JOIN deliveries d ON d.purchase_id = p.id
                    WHERE p.payment_status = "Pending"
                    GROUP BY p.id, p.quantity
                    HAVING delivered + 0.0001 < p.quantity
                ) AS outstanding';
        $row = $this->db->query($sql)->fetch();
        return (int)($row['pending'] ?? 0);
	}

    public function countDeliveriesByStatus(string $status): int
    {
        $sql = 'SELECT COUNT(*) AS cnt FROM deliveries WHERE delivery_status = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }

    public function dailyCounts(string $startDate, string $endDate): array
    {
        $sql = 'SELECT DATE(date_received) AS day, COUNT(*) AS total
                FROM deliveries
                WHERE DATE(date_received) BETWEEN ? AND ?
                GROUP BY day';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['day']] = (int)$row['total'];
        }
        return $result;
    }

    public function listOutstandingPurchases(): array
    {
        $sql = 'SELECT 
                    p.id,
                    p.purchaser_id,
                    p.payment_status,
                    p.receipt_url,
                    p.quantity,
                    p.supplier,
                    p.date_purchased,
                    u.name AS purchaser_name,
                    i.name AS item_name,
                    i.unit,
                    i.display_unit,
                    i.display_factor,
                    COALESCE(SUM(d.quantity_received), 0) AS delivered_quantity
                FROM purchases p
                JOIN users u ON p.purchaser_id = u.id
                JOIN ingredients i ON p.item_id = i.id
                LEFT JOIN deliveries d ON d.purchase_id = p.id
                WHERE p.payment_status = "Pending"
                GROUP BY p.id, p.purchaser_id, p.payment_status, p.receipt_url, p.quantity, p.supplier, p.date_purchased, u.name, i.name, i.unit, i.display_unit, i.display_factor
                HAVING delivered_quantity < p.quantity
                ORDER BY p.date_purchased DESC
                LIMIT 10';
        return $this->db->query($sql)->fetchAll();
    }
}


