<?php
declare(strict_types=1);

class Notification extends BaseModel
{
    private bool $tableEnsured = false;

    private function ensureTable(): void
    {
        if ($this->tableEnsured) {
            return;
        }
        $this->db->exec('CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            message VARCHAR(255) NOT NULL,
            link VARCHAR(255) NULL,
            level ENUM(\'info\',\'warning\',\'success\',\'danger\') NOT NULL DEFAULT \'info\',
            read_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_notifications_user (user_id),
            KEY idx_notifications_read (read_at),
            CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        
        // Ensure level column exists (for existing tables)
        $columns = $this->db->query('SHOW COLUMNS FROM notifications')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('level', $columns, true)) {
            $this->db->exec('ALTER TABLE notifications ADD COLUMN level ENUM(\'info\',\'warning\',\'success\',\'danger\') NOT NULL DEFAULT \'info\' AFTER link');
        }
        
        $this->tableEnsured = true;
    }

    public function create(int $userId, string $message, ?string $link = null, string $level = 'info'): void
    {
        $this->ensureTable();
        
        // Check user role and filter notifications accordingly
        $userModel = new User();
        $user = $userModel->find($userId);
        if (!$user) {
            return; // User not found, skip notification
        }
        
        $userRole = $user['role'] ?? '';
        $messageLower = strtolower($message);
        $linkLower = strtolower($link ?? '');
        
        // Kitchen Staff: only allow request-related notifications
        if ($userRole === 'Kitchen Staff') {
            $isRequestRelated = (
                stripos($message, 'request') !== false ||
                stripos($message, 'distributed') !== false ||
                stripos($message, 'approved') !== false ||
                stripos($message, 'rejected') !== false ||
                stripos($message, 'confirm') !== false ||
                stripos($message, 'preparing') !== false ||
                stripos($link, '/requests') !== false
            );
            
            if (!$isRequestRelated) {
                return; // Skip non-request-related notifications for Kitchen Staff
            }
        }
        
        // Purchaser: only allow payment and delivery-related notifications
        if ($userRole === 'Purchaser') {
            $isPaymentOrDeliveryRelated = (
                stripos($message, 'payment') !== false ||
                stripos($message, 'purchase') !== false ||
                stripos($message, 'delivery') !== false ||
                stripos($message, 'partial') !== false ||
                stripos($link, '/purchases') !== false ||
                stripos($link, '/deliveries') !== false
            );
            
            if (!$isPaymentOrDeliveryRelated) {
                return; // Skip non-payment/delivery-related notifications for Purchaser
            }
        }
        
        $sql = 'INSERT INTO notifications (user_id, message, link, level) VALUES (?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $message, $link, $level]);
    }

    public function listLatest(int $userId, int $limit = 10): array
    {
        $this->ensureTable();
        $limit = max(1, min($limit, 50));
        $sql = 'SELECT id, message, link, level, read_at, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ' . $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markAllRead(int $userId): void
    {
        $this->ensureTable();
        $sql = 'UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }

	public function deleteAll(int $userId): void
	{
		$this->ensureTable();
		$sql = 'DELETE FROM notifications WHERE user_id = ?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$userId]);
	}

	public function deleteNonCritical(int $userId): void
	{
		$this->ensureTable();
		$sql = 'DELETE FROM notifications WHERE user_id = ? AND level <> "danger"';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$userId]);
	}
}


