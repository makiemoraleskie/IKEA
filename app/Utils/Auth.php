<?php
declare(strict_types=1);

class Auth
{
	public static function user(): ?array
	{
		return $_SESSION['user'] ?? null;
	}

	public static function check(): bool
	{
		return isset($_SESSION['user']);
	}

	public static function id(): ?int
	{
		return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
	}

	public static function role(): ?string
	{
		return $_SESSION['user']['role'] ?? null;
	}

	public static function requireRole(array $roles): void
	{
		if (!self::check() || !in_array(self::role(), $roles, true)) {
			header('Location: /login');
			exit;
		}
	}

	public static function login(array $user): void
	{
		$_SESSION['user'] = [
			'id' => (int)$user['id'],
			'name' => $user['name'],
			'email' => $user['email'],
			'role' => $user['role'],
		];
	}

	public static function logout(): void
	{
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
}


