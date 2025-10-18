<?php
declare(strict_types=1);

// CLI-only script: php scripts/backup_db.php
if (php_sapi_name() !== 'cli') { http_response_code(403); exit('CLI only'); }

require_once __DIR__ . '/../config/db.php';

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'ikea_commissary';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

$backupDir = realpath(__DIR__ . '/../backups');
if ($backupDir === false) {
	$backupDir = __DIR__ . '/../backups';
	@mkdir($backupDir, 0750, true);
}

$filename = $backupDir . '/backup_' . date('Ymd_His') . '.sql';

// Prefer mysqldump if available
$mysqldump = trim((string)@shell_exec('where mysqldump 2>NUL')); // Windows where
if ($mysqldump === '') {
	$mysqldump = trim((string)@shell_exec('which mysqldump 2>/dev/null'));
}

if ($mysqldump !== '') {
	$cmd = '"' . $mysqldump . '" --host=' . escapeshellarg($dbHost) . ' --user=' . escapeshellarg($dbUser) . ' --password=' . escapeshellarg($dbPass) . ' --databases ' . escapeshellarg($dbName) . ' > ' . escapeshellarg($filename);
	$exit = null;
	@system($cmd, $exit);
	if ($exit === 0 && file_exists($filename)) {
		echo "Backup created: {$filename}\n";
		exit(0);
	}
}

// Fallback: PDO export (structure + data minimal)
try {
	$pdo = Database::getConnection();
	$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
	$out = "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n";
	foreach ($tables as $t) {
		$create = $pdo->query('SHOW CREATE TABLE `' . $t . '`')->fetch();
		$out .= 'DROP TABLE IF EXISTS `' . $t . '`;' . "\n" . $create['Create Table'] . ";\n\n";
		$rows = $pdo->query('SELECT * FROM `' . $t . '`');
		while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
			$cols = array_map(fn($c) => '`' . str_replace('`','``',$c) . '`', array_keys($row));
			$vals = array_map(function($v) use ($pdo) {
				if ($v === null) return 'NULL';
				return $pdo->quote((string)$v);
			}, array_values($row));
			$out .= 'INSERT INTO `' . $t . '` (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ');' . "\n";
		}
		$out .= "\n";
	}
	$out .= 'SET FOREIGN_KEY_CHECKS=1;';
	file_put_contents($filename, $out);
	echo "Backup created (PDO): {$filename}\n";
	exit(0);
} catch (Throwable $e) {
	fwrite(STDERR, 'Backup failed: ' . $e->getMessage() . "\n");
	exit(1);
}


