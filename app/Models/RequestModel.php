<?php
declare(strict_types=1);

class RequestModel extends BaseModel
{
	public function createBatch(int $staffId): int
	{
		$sql = 'INSERT INTO request_batches (staff_id, status, date_requested) VALUES (?, "Pending", NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$staffId]);
		return (int)$this->db->lastInsertId();
	}

	public function listBatches(?string $status = null): array
	{
		$params = [];
		$sql = 'SELECT b.*, u.name AS staff_name,
			(SELECT COUNT(*) FROM requests r WHERE r.batch_id=b.id) AS items_count
			FROM request_batches b JOIN users u ON b.staff_id=u.id';
		if ($status) { $sql .= ' WHERE b.status = ?'; $params[] = $status; }
		$sql .= ' ORDER BY b.date_requested DESC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	public function listItemsByBatch(int $batchId): array
	{
		$sql = 'SELECT r.*, i.name AS item_name, i.unit FROM requests r JOIN ingredients i ON r.item_id=i.id WHERE r.batch_id=? ORDER BY r.id ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$batchId]);
		return $stmt->fetchAll();
	}
	public function listAll(?string $status = null): array
	{
		if ($status) {
			$sql = 'SELECT r.*, u.name AS staff_name, i.name AS item_name, i.unit FROM requests r
				JOIN users u ON r.staff_id = u.id
				JOIN ingredients i ON r.item_id = i.id
				WHERE r.status = ?
				ORDER BY r.date_requested DESC';
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$status]);
			return $stmt->fetchAll();
		}

		$sql = 'SELECT r.*, u.name AS staff_name, i.name AS item_name, i.unit FROM requests r
			JOIN users u ON r.staff_id = u.id
			JOIN ingredients i ON r.item_id = i.id
			ORDER BY r.date_requested DESC';
		return $this->db->query($sql)->fetchAll();
	}

	public function create(int $staffId, int $itemId, float $quantity, int $batchId): int
	{
		$sql = 'INSERT INTO requests (batch_id, staff_id, item_id, quantity, status, date_requested) VALUES (?, ?, ?, ?, "Pending", NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$batchId, $staffId, $itemId, $quantity]);
		return (int)$this->db->lastInsertId();
	}

	public function find(int $id): ?array
	{
		$sql = 'SELECT * FROM requests WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function setStatus(int $id, string $status): void
	{
		$sql = 'UPDATE requests SET status = ?, date_approved = CASE WHEN ? IN ("Approved","Rejected") THEN NOW() ELSE date_approved END WHERE id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status, $status, $id]);
	}

	public function setBatchStatus(int $batchId, string $status): void
	{
		$sql = 'UPDATE request_batches SET status=?, date_approved = CASE WHEN ? IN ("Approved","Rejected") THEN NOW() ELSE date_approved END WHERE id=?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status, $status, $batchId]);
	}
}


