<?php
declare(strict_types=1);

class BackupController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$db = Database::getConnection();
		
		// Ensure backups table exists
		$this->ensureBackupsTable();
		
		// Fetch all backups
		$stmt = $db->query('SELECT b.*, u.name as created_by_name FROM backups b LEFT JOIN users u ON b.created_by = u.id ORDER BY b.created_at DESC');
		$backups = $stmt->fetchAll();
		
		// Get backups directory info
		$backupsDir = BASE_PATH . '/backups';
		$totalSize = 0;
		$backupFiles = [];
		if (is_dir($backupsDir)) {
			$files = scandir($backupsDir);
			foreach ($files as $file) {
				if ($file === '.' || $file === '..' || !is_file($backupsDir . '/' . $file)) {
					continue;
				}
				if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
					$filePath = $backupsDir . '/' . $file;
					$fileSize = filesize($filePath);
					$totalSize += $fileSize;
					$backupFiles[$file] = [
						'path' => $filePath,
						'size' => $fileSize,
						'modified' => filemtime($filePath)
					];
				}
			}
		}
		
		$flash = $_SESSION['flash_backup'] ?? null;
		unset($_SESSION['flash_backup']);
		
		$this->render('admin/backup/index.php', [
			'backups' => $backups,
			'totalSize' => $totalSize,
			'backupFiles' => $backupFiles,
			'flash' => $flash,
		]);
	}
	
	public function create(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		
		$db = Database::getConnection();
		$this->ensureBackupsTable();
		
		$description = trim((string)($_POST['description'] ?? ''));
		$timestamp = date('Y-m-d_His');
		$filename = "backup_{$timestamp}.sql";
		$backupsDir = BASE_PATH . '/backups';
		
		// Ensure backups directory exists
		if (!is_dir($backupsDir)) {
			@mkdir($backupsDir, 0755, true);
		}
		
		$filePath = $backupsDir . '/' . $filename;
		
		try {
			// Generate backup SQL
			$sql = $this->generateBackupSql($db);
			
			// Save to file
			if (file_put_contents($filePath, $sql) === false) {
				throw new Exception('Failed to write backup file');
			}
			
			$fileSize = filesize($filePath);
			$recordsCount = $this->countRecordsInBackup($db);
			
			// Save metadata to database
			$stmt = $db->prepare('INSERT INTO backups (filename, file_path, file_size, created_by, description, records_count) VALUES (?, ?, ?, ?, ?, ?)');
			$stmt->execute([
				$filename,
				$filePath,
				$fileSize,
				Auth::id(),
				$description ?: null,
				$recordsCount
			]);
			
			// Log action
			$logger = new AuditLog();
			$logger->log(Auth::id() ?? 0, 'create', 'backups', [
				'backup_id' => $db->lastInsertId(),
				'filename' => $filename,
				'file_size' => $fileSize,
				'description' => $description
			]);
			
			$_SESSION['flash_backup'] = ['type' => 'success', 'text' => 'Backup created successfully.'];
		} catch (Throwable $e) {
			// Clean up file if created
			if (file_exists($filePath)) {
				@unlink($filePath);
			}
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Failed to create backup: ' . $e->getMessage()];
		}
		
		$this->redirect('/backup');
	}
	
	public function restore(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		
		$backupId = (int)($_POST['backup_id'] ?? 0);
		if ($backupId <= 0) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Invalid backup selection.'];
			$this->redirect('/backup');
		}
		
		$db = Database::getConnection();
		$this->ensureBackupsTable();
		
		// Get backup info
		$stmt = $db->prepare('SELECT * FROM backups WHERE id = ?');
		$stmt->execute([$backupId]);
		$backup = $stmt->fetch();
		
		if (!$backup || !file_exists($backup['file_path'])) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Backup file not found.'];
			$this->redirect('/backup');
		}
		
		try {
			// Create auto-backup before restore
			$autoBackupTimestamp = date('Y-m-d_His');
			$autoBackupFilename = "auto_backup_before_restore_{$autoBackupTimestamp}.sql";
			$backupsDir = BASE_PATH . '/backups';
			$autoBackupPath = $backupsDir . '/' . $autoBackupFilename;
			
			$autoBackupSql = $this->generateBackupSql($db);
			file_put_contents($autoBackupPath, $autoBackupSql);
			
			// Save auto-backup metadata
			$autoBackupSize = filesize($autoBackupPath);
			$autoBackupRecords = $this->countRecordsInBackup($db);
			$stmt = $db->prepare('INSERT INTO backups (filename, file_path, file_size, created_by, description, records_count) VALUES (?, ?, ?, ?, ?, ?)');
			$stmt->execute([
				$autoBackupFilename,
				$autoBackupPath,
				$autoBackupSize,
				Auth::id(),
				'Auto-backup created before restore',
				$autoBackupRecords
			]);
			$autoBackupId = (int)$db->lastInsertId();
			
			// Read and execute backup SQL
			$sql = file_get_contents($backup['file_path']);
			if ($sql === false) {
				throw new Exception('Failed to read backup file');
			}
			
			// Execute restore in transaction
			$db->beginTransaction();
			try {
				// Set foreign key checks off first
				$db->exec('SET FOREIGN_KEY_CHECKS=0');
				
				// Split SQL by semicolons, but handle multi-line statements properly
				// Remove comments but preserve the SQL structure
				$sql = preg_replace('/--.*$/m', '', $sql);
				$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
				
				// Split by semicolons, but be careful with semicolons inside strings
				$statements = [];
				$currentStatement = '';
				$inString = false;
				$stringChar = '';
				
				for ($i = 0; $i < strlen($sql); $i++) {
					$char = $sql[$i];
					
					if (!$inString && ($char === '"' || $char === "'" || $char === '`')) {
						$inString = true;
						$stringChar = $char;
						$currentStatement .= $char;
					} elseif ($inString && $char === $stringChar) {
						// Check if escaped
						if ($i > 0 && $sql[$i-1] === '\\') {
							$currentStatement .= $char;
						} else {
							$inString = false;
							$stringChar = '';
							$currentStatement .= $char;
						}
					} elseif (!$inString && $char === ';') {
						$statement = trim($currentStatement);
						if (!empty($statement)) {
							$statements[] = $statement;
						}
						$currentStatement = '';
					} else {
						$currentStatement .= $char;
					}
				}
				
				// Add remaining statement if any
				$remaining = trim($currentStatement);
				if (!empty($remaining)) {
					$statements[] = $remaining;
				}
				
				// Execute all statements except SET FOREIGN_KEY_CHECKS (already executed)
				foreach ($statements as $statement) {
					$statement = trim($statement);
					if (empty($statement)) continue;
					
					// Skip SET FOREIGN_KEY_CHECKS statements
					if (preg_match('/^\s*SET\s+FOREIGN_KEY_CHECKS\s*=/i', $statement)) {
						continue;
					}
					
					try {
						$db->exec($statement);
					} catch (PDOException $e) {
						// Log but continue if it's a table that might not exist
						// Some errors are expected during DROP TABLE IF EXISTS
						if (strpos($e->getMessage(), 'Unknown table') === false) {
							throw $e;
						}
					}
				}
				
				// Re-enable foreign key checks
				$db->exec('SET FOREIGN_KEY_CHECKS=1');
				$db->commit();
				
				// Log action
				$logger = new AuditLog();
				$logger->log(Auth::id() ?? 0, 'restore', 'backups', [
					'backup_id' => $backupId,
					'filename' => $backup['filename'],
					'auto_backup_id' => $autoBackupId
				]);
				
				// Force logout all users by invalidating all session tokens
				$security = new UserSecurity();
				$userModel = new User();
				$allUsers = $userModel->all();
				foreach ($allUsers as $user) {
					$security->bumpSession((int)$user['id']);
				}
				
				// Log out the current user and redirect to login
				Auth::logout();
				$baseUrl = defined('BASE_URL') ? BASE_URL : '';
				header('Location: ' . $baseUrl . '/login?status=restored');
				exit;
			} catch (Throwable $e) {
				$db->rollBack();
				throw $e;
			}
		} catch (Throwable $e) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Failed to restore backup: ' . $e->getMessage()];
		}
		
		$this->redirect('/backup');
	}
	
	public function delete(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		
		$backupId = (int)($_POST['backup_id'] ?? 0);
		if ($backupId <= 0) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Invalid backup selection.'];
			$this->redirect('/backup');
		}
		
		$db = Database::getConnection();
		$this->ensureBackupsTable();
		
		// Get backup info
		$stmt = $db->prepare('SELECT * FROM backups WHERE id = ?');
		$stmt->execute([$backupId]);
		$backup = $stmt->fetch();
		
		if (!$backup) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Backup not found.'];
			$this->redirect('/backup');
		}
		
		try {
			// Delete file if exists
			if (file_exists($backup['file_path'])) {
				@unlink($backup['file_path']);
			}
			
			// Delete from database
			$stmt = $db->prepare('DELETE FROM backups WHERE id = ?');
			$stmt->execute([$backupId]);
			
			// Log action
			$logger = new AuditLog();
			$logger->log(Auth::id() ?? 0, 'delete', 'backups', [
				'backup_id' => $backupId,
				'filename' => $backup['filename']
			]);
			
			$_SESSION['flash_backup'] = ['type' => 'success', 'text' => 'Backup deleted successfully.'];
		} catch (Throwable $e) {
			$_SESSION['flash_backup'] = ['type' => 'error', 'text' => 'Failed to delete backup: ' . $e->getMessage()];
		}
		
		$this->redirect('/backup');
	}
	
	public function download(): void
	{
		Auth::requireRole(['Owner','Manager']);
		
		$backupId = (int)($_GET['id'] ?? 0);
		if ($backupId <= 0) {
			http_response_code(404);
			echo 'Backup not found';
			return;
		}
		
		$db = Database::getConnection();
		$this->ensureBackupsTable();
		
		$stmt = $db->prepare('SELECT * FROM backups WHERE id = ?');
		$stmt->execute([$backupId]);
		$backup = $stmt->fetch();
		
		if (!$backup || !file_exists($backup['file_path'])) {
			http_response_code(404);
			echo 'Backup file not found';
			return;
		}
		
		// Log download action
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'download', 'backups', [
			'backup_id' => $backupId,
			'filename' => $backup['filename']
		]);
		
		// Stream file
		header('Content-Type: application/sql; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . htmlspecialchars($backup['filename']) . '"');
		header('Content-Length: ' . $backup['file_size']);
		readfile($backup['file_path']);
		exit;
	}
	
	private function ensureBackupsTable(): void
	{
		$db = Database::getConnection();
		$migrationPath = BASE_PATH . '/database/migrations/create_backups_table.sql';
		if (file_exists($migrationPath)) {
			$sql = file_get_contents($migrationPath);
			try {
				$db->exec($sql);
			} catch (PDOException $e) {
				// Table might already exist, ignore
			}
		}
	}
	
	private function generateBackupSql(PDO $db): string
	{
		$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
		$timestamp = date('Y-m-d H:i:s');
		$output = "-- IKEA Commissary backup generated on {$timestamp}\n";
		$output .= "-- This backup includes all database tables and data\n";
		$output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
		
		foreach ($tables as $table) {
			// Skip backups table to avoid issues
			if ($table === 'backups') {
				continue;
			}
			
			$create = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_ASSOC);
			if (!$create) continue;
			
			$output .= "DROP TABLE IF EXISTS `{$table}`;\n";
			$output .= $create['Create Table'] . ";\n\n";
			
			$rows = $db->query('SELECT * FROM `' . $table . '`')->fetchAll(PDO::FETCH_ASSOC);
			if (empty($rows)) {
				$output .= "\n";
				continue;
			}
			
			// Get column names for INSERT statement
			$columns = array_keys($rows[0]);
			$columnList = '`' . implode('`, `', array_map(function($col) {
				return str_replace('`', '``', $col);
			}, $columns)) . '`';
			
			foreach ($rows as $row) {
				$values = array_map(function ($value) use ($db) {
					if ($value === null) {
						return 'NULL';
					}
					return $db->quote((string)$value);
				}, array_values($row));
				$output .= 'INSERT INTO `' . $table . '` (' . $columnList . ') VALUES (' . implode(',', $values) . ");\n";
			}
			$output .= "\n";
		}
		
		$output .= "SET FOREIGN_KEY_CHECKS=1;\n";
		return $output;
	}
	
	private function countRecordsInBackup(PDO $db): int
	{
		$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
		$total = 0;
		foreach ($tables as $table) {
			if ($table === 'backups') continue; // Don't count backups table itself
			$stmt = $db->query('SELECT COUNT(*) as cnt FROM `' . $table . '`');
			$result = $stmt->fetch();
			$total += (int)($result['cnt'] ?? 0);
		}
		return $total;
	}
}

