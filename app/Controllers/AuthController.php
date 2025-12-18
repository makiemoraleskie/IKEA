<?php
declare(strict_types=1);

class AuthController extends BaseController
{
	public function showLogin(): void
	{
		if (Auth::check()) {
			$user = Auth::user();
			$userRole = $user['role'] ?? null;
			// Kitchen Staff should be redirected to requests page, not dashboard
			if ($userRole === 'Kitchen Staff') {
				$this->redirect('/requests');
			} else {
				$this->redirect('/dashboard');
			}
		}
		$this->renderLogin('auth/login.php');
	}

	public function login(): void
	{
		$token = $_POST['csrf_token'] ?? null;
		if (!Csrf::verify($token)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}

		$email = trim((string)($_POST['email'] ?? ''));
		$password = (string)($_POST['password'] ?? '');

		if ($email === '' || $password === '') {
			$this->renderLogin('auth/login.php', ['error' => 'Email and password are required.']);
			return;
		}

		$userModel = new User();
		$user = $userModel->findByEmail($email);
		if (!$user || !password_verify($password, $user['password_hash'])) {
			$this->renderLogin('auth/login.php', ['error' => 'Invalid credentials.']);
			return;
		}

		$security = new UserSecurity();
		$meta = $security->get((int)$user['id']);
		if (($meta['status'] ?? 'active') !== 'active') {
			$this->renderLogin('auth/login.php', ['error' => 'This account is disabled. Please contact an administrator.']);
			return;
		}

		Auth::login($user);
		$logger = new AuditLog();
		$logger->log((int)$user['id'], 'login', 'auth', ['email' => $email]);

		// Kitchen Staff should be redirected to requests page, not dashboard
		$userRole = $user['role'] ?? null;
		if ($userRole === 'Kitchen Staff') {
			$this->redirect('/requests');
		} else {
			$this->redirect('/dashboard');
		}
	}

	public function logout(): void
	{
		if (Auth::check()) {
			$logger = new AuditLog();
			$logger->log(Auth::id() ?? 0, 'logout', 'auth', []);
		}
		Auth::logout();
		$this->redirect('/login');
	}

	public function checkSession(): void
	{
		header('Content-Type: application/json');
		
		if (!Auth::check()) {
			echo json_encode([
				'authenticated' => false,
				'reason' => 'not_logged_in',
				'message' => 'You are not logged in.'
			]);
			return;
		}

		$user = Auth::user();
		$security = new UserSecurity();
		$record = $security->get((int)($user['id'] ?? 0));
		$status = $record['status'] ?? 'active';
		
		if ($status !== 'active') {
			echo json_encode([
				'authenticated' => false,
				'reason' => 'disabled',
				'message' => 'Your account has been disabled. Please contact an administrator.'
			]);
			return;
		}

		// Check session token validity
		$sessionToken = $_SESSION['session_token'] ?? '';
		$dbToken = $record['session_token'] ?? '';
		
		if ($dbToken === '' || !hash_equals($dbToken, $sessionToken)) {
			echo json_encode([
				'authenticated' => false,
				'reason' => 'expired',
				'message' => 'Your session has expired. Please sign in again.'
			]);
			return;
		}

		echo json_encode([
			'authenticated' => true,
			'user' => [
				'id' => $user['id'] ?? null,
				'name' => $user['name'] ?? '',
				'role' => $user['role'] ?? ''
			]
		]);
	}
}


