<?php
declare(strict_types=1);

class Auth
{
	private static bool $sessionValidated = false;

	public static function user(): ?array
	{
		self::validateSession();
		return $_SESSION['user'] ?? null;
	}

	public static function check(): bool
	{
		self::validateSession();
		return isset($_SESSION['user']);
	}

	public static function id(): ?int
	{
		self::validateSession();
		return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
	}

	public static function role(): ?string
	{
		self::validateSession();
		return $_SESSION['user']['role'] ?? null;
	}

	public static function requireRole(array $roles): void
	{
		self::validateSession();
		if (!isset($_SESSION['user'])) {
			// User not logged in - redirect to login
			self::redirectToLogin();
		}
		if (!in_array($_SESSION['user']['role'] ?? null, $roles, true)) {
			// User is logged in but doesn't have the required role
			// Set flash message and redirect back
			self::handleUnauthorizedAccess();
		}
	}

	private static function handleUnauthorizedAccess(): void
	{
		// Set flash message
		$_SESSION['flash_unauthorized'] = [
			'type' => 'error',
			'messages' => ['You do not have permission to access this page.']
		];
		
		// Get referrer or determine safe default redirect
		$referrer = $_SERVER['HTTP_REFERER'] ?? null;
		$baseUrl = defined('BASE_URL') ? BASE_URL : '';
		$defaultPath = '/dashboard';
		
		// Determine default path based on user role
		$userRole = $_SESSION['user']['role'] ?? null;
		if ($userRole === 'Kitchen Staff') {
			$defaultPath = '/requests';
		} elseif ($userRole === 'Purchaser') {
			$defaultPath = '/purchases';
		}
		
		$redirectPath = $defaultPath;
		
		// Validate referrer - only allow redirects within our application
		if ($referrer) {
			$referrerPath = parse_url($referrer, PHP_URL_PATH);
			$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
			$referrerRelative = '/' . ltrim(substr($referrerPath, strlen($scriptDir)), '/');
			
			// Only redirect back if it's a valid internal path (not login, not external)
			if ($referrerRelative !== '/login' && strpos($referrer, $baseUrl) !== false && $referrerRelative !== '/') {
				$redirectPath = $referrerRelative;
			}
		}
		
		$url = $redirectPath;
		if (isset($redirectPath[0]) && $redirectPath[0] === '/' && defined('BASE_URL')) {
			$url = BASE_URL . $redirectPath;
		}
		
		header('Location: ' . $url);
		exit;
	}

	public static function requireLogin(): void
	{
		self::validateSession();
		if (!isset($_SESSION['user'])) {
			self::redirectToLogin();
		}
	}

	public static function login(array $user): void
	{
		$security = new UserSecurity();
		$token = bin2hex(random_bytes(32));
		$security->setSessionToken((int)$user['id'], $token);
		$theme = $security->getTheme((int)$user['id']);

		$_SESSION['user'] = [
			'id' => (int)$user['id'],
			'name' => $user['name'],
			'email' => $user['email'],
			'role' => $user['role'],
		];
		$_SESSION['session_token'] = $token;
		$_SESSION['user_theme'] = $theme;
		self::$sessionValidated = true;
	}

	public static function logout(): void
	{
		$userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
		if ($userId) {
			$security = new UserSecurity();
			$security->clearSession($userId);
		}
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
		self::$sessionValidated = false;
	}

	private static function validateSession(): void
	{
		if (self::$sessionValidated) {
			return;
		}
		if (!isset($_SESSION['user']['id']) || empty($_SESSION['session_token'])) {
			return;
		}
		$userId = (int)$_SESSION['user']['id'];
		$sessionToken = (string)$_SESSION['session_token'];
		$security = new UserSecurity();
		$record = $security->get($userId);
		$status = $record['status'] ?? 'active';
		if ($status !== 'active') {
			self::forceLogout('disabled');
		}

		$dbToken = $record['session_token'] ?? '';
		if ($dbToken === '' || !hash_equals($dbToken, $sessionToken)) {
			self::forceLogout('expired');
		}

		$_SESSION['user_theme'] = $record['theme'] ?? ($_SESSION['user_theme'] ?? 'system');
		self::$sessionValidated = true;
	}

	private static function forceLogout(string $reason): void
	{
		self::logout();
		header('Location: /login?status=' . urlencode($reason));
		exit;
	}

	private static function redirectToLogin(): void
	{
		header('Location: /login');
		exit;
	}
}


