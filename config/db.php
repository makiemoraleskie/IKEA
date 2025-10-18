<?php
declare(strict_types=1);

class Database
{
	private static ?PDO $pdo = null;

	public static function getConnection(): PDO
	{
		if (self::$pdo !== null) {
			return self::$pdo;
		}

		$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
		$dbName = getenv('DB_NAME') ?: 'ikea';
		$dbUser = getenv('DB_USER') ?: 'root';
		$dbPass = getenv('DB_PASS') ?: '';
		$charset = 'utf8mb4';

		$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];

		self::$pdo = new PDO($dsn, $dbUser, $dbPass, $options);
		return self::$pdo;
	}
}


