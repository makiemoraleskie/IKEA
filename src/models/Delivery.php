<?php
declare(strict_types=1);

class Delivery extends BaseModel
{
    public function listAll(): array
	{
        $sql = 'SELECT d.*, p.item_id, p.quantity AS purchase_quantity, i.name AS item_name, i.unit, i.display_unit, i.display_factor
			FROM deliveries d
			JOIN purchases p ON d.purchase_id = p.id
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
}


