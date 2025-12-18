<?php
declare(strict_types=1);

class RequestModel extends BaseModel
{
	private bool $setTableEnsured = false;
	private bool $batchMetaEnsured = false;

	private function ensureBatchMetadata(): void
	{
		if ($this->batchMetaEnsured) {
			return;
		}
		$columns = $this->db->query('SHOW COLUMNS FROM request_batches')->fetchAll(PDO::FETCH_COLUMN);
		if (!in_array('custom_requester', $columns, true)) {
			$this->db->exec("ALTER TABLE request_batches ADD COLUMN custom_requester VARCHAR(160) NOT NULL DEFAULT ''");
		}
		if (!in_array('custom_ingredients', $columns, true)) {
			$this->db->exec("ALTER TABLE request_batches ADD COLUMN custom_ingredients TEXT NULL");
		}
		if (!in_array('custom_request_date', $columns, true)) {
			$this->db->exec("ALTER TABLE request_batches ADD COLUMN custom_request_date DATE NULL");
		}
		
		// Update status ENUM to include new statuses
		try {
			$this->db->exec("ALTER TABLE request_batches MODIFY COLUMN status ENUM('Pending','To Prepare','Pending Confirmation','Distributed','Received','Rejected') NOT NULL DEFAULT 'Pending'");
		} catch (PDOException $e) {
			// If it fails, try alternative approach - change to VARCHAR if ENUM modification fails
			// This handles cases where the column might already be VARCHAR or have different structure
			try {
				$columnInfo = $this->db->query("SHOW COLUMNS FROM request_batches WHERE Field = 'status'")->fetch(PDO::FETCH_ASSOC);
				if ($columnInfo && stripos($columnInfo['Type'], 'enum') !== false) {
					// Column is ENUM, try to modify it
					$this->db->exec("ALTER TABLE request_batches MODIFY COLUMN status ENUM('Pending','To Prepare','Pending Confirmation','Distributed','Received','Rejected') NOT NULL DEFAULT 'Pending'");
				}
			} catch (PDOException $e2) {
				// If still fails, log but don't break - the status might already be VARCHAR
			}
		}
		
		$this->batchMetaEnsured = true;
	}

	private function ensureSetTable(): void
	{
		if ($this->setTableEnsured) {
			return;
		}
		$this->db->exec('CREATE TABLE IF NOT EXISTS request_item_sets (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			request_id INT UNSIGNED NOT NULL,
			set_id INT UNSIGNED NULL,
			set_name VARCHAR(160) NOT NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY uniq_request_set (request_id),
			KEY idx_request_item_sets_name (set_name),
			CONSTRAINT fk_request_item_set_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
		$this->setTableEnsured = true;
	}

	public function createBatch(int $staffId, string $requesterName, string $ingredientsNote, ?string $requestedDate = null): int
	{
		$this->ensureBatchMetadata();
		$sql = 'INSERT INTO request_batches (staff_id, status, date_requested, custom_requester, custom_ingredients, custom_request_date) VALUES (?, "Pending", NOW(), ?, ?, ?)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$staffId, $requesterName, $ingredientsNote, $requestedDate ?: null]);
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
		$this->ensureSetTable();
		$sql = 'SELECT r.*, i.name AS item_name, i.unit,
				rs.set_name, rs.set_id
			FROM requests r
			JOIN ingredients i ON r.item_id=i.id
			LEFT JOIN request_item_sets rs ON rs.request_id = r.id
			WHERE r.batch_id=?
			ORDER BY r.id ASC';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$batchId]);
		return $stmt->fetchAll();
	}

	public function countBatchesByStatus(string $status): int
	{
		$sql = 'SELECT COUNT(*) AS cnt FROM request_batches WHERE status = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status]);
		$row = $stmt->fetch();
		return (int)($row['cnt'] ?? 0);
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

	public function create(int $staffId, int $itemId, float $quantity, int $batchId, ?int $setId = null, ?string $setLabel = null): int
	{
		$sql = 'INSERT INTO requests (batch_id, staff_id, item_id, quantity, status, date_requested) VALUES (?, ?, ?, ?, "Pending", NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$batchId, $staffId, $itemId, $quantity]);
		$requestId = (int)$this->db->lastInsertId();
		if ($setLabel) {
			$this->ensureSetTable();
			$insertSet = $this->db->prepare('INSERT INTO request_item_sets (request_id, set_id, set_name) VALUES (?, ?, ?)');
			$insertSet->execute([$requestId, $setId ?: null, $setLabel]);
		}
		return $requestId;
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
		$sql = 'UPDATE request_batches SET status=?, date_approved = CASE WHEN (?) IN ("To Prepare","Distributed","Pending Confirmation","Received","Rejected") THEN NOW() ELSE date_approved END WHERE id=?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$status, $status, $batchId]);
	}

	public function replaceBatchItems(int $batchId, int $staffId, array $items): void
	{
		$this->ensureSetTable();
		$delete = $this->db->prepare('DELETE FROM requests WHERE batch_id = ?');
		$delete->execute([$batchId]);
		if (empty($items)) {
			return;
		}
		$sql = 'INSERT INTO requests (batch_id, staff_id, item_id, quantity, status, date_requested) VALUES (?, ?, ?, ?, "Pending", NOW())';
		$insert = $this->db->prepare($sql);
		foreach ($items as $item) {
			$insert->execute([$batchId, $staffId, $item['item_id'], $item['quantity']]);
		}
	}

	public function findBatch(int $batchId): ?array
	{
		$sql = 'SELECT b.*, u.name AS staff_name FROM request_batches b JOIN users u ON b.staff_id = u.id WHERE b.id = ? LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$batchId]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public function updateBatch(int $batchId, string $requesterName, string $ingredientsNote, ?string $requestedDate = null): void
	{
		$this->ensureBatchMetadata();
		$sql = 'UPDATE request_batches SET custom_requester = ?, custom_ingredients = ?, custom_request_date = ? WHERE id = ? AND status = "Pending"';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$requesterName, $ingredientsNote, $requestedDate ?: null, $batchId]);
	}
	
	public function ensureStatusEnum(): void
	{
		// Ensure status ENUM includes all required values
		$this->ensureBatchMetadata(); // This will update the ENUM
	}
}


