<?php
declare(strict_types=1);

class Database
{
	private static ?PDO $pdo = null;
	private const DEFAULT_CHARSET = 'utf8mb4';
	private const DEFAULT_COLLATION = 'utf8mb4_unicode_ci';

	private static function cleanIdentifier(string $value, string $fallback): string
	{
		// Allow only alnum and underscore to prevent injection in SET NAMES
		return preg_match('/^[A-Za-z0-9_]+$/', $value) === 1 ? $value : $fallback;
	}

	public static function getConnection(): PDO
	{
		if (self::$pdo !== null) {
			return self::$pdo;
		}

		$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
		$dbName = getenv('DB_NAME') ?: 'ikea_commissary';
		$dbUser = getenv('DB_USER') ?: 'root';
		$dbPass = getenv('DB_PASS') ?: '';
		$charset = self::cleanIdentifier(getenv('DB_CHARSET') ?: self::DEFAULT_CHARSET, self::DEFAULT_CHARSET);
		$collation = self::cleanIdentifier(getenv('DB_COLLATION') ?: self::DEFAULT_COLLATION, self::DEFAULT_COLLATION);

		$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];

		self::$pdo = new PDO($dsn, $dbUser, $dbPass, $options);
		self::$pdo->exec("SET NAMES {$charset} COLLATE {$collation}");
		self::$pdo->exec("SET collation_connection = {$collation}");
		return self::$pdo;
	}
}


