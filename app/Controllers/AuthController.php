<?php
declare(strict_types=1);

class AuthController extends BaseController
{
	public function showLogin(): void
	{
		if (Auth::check()) {
			$this->redirect('/dashboard');
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

		$this->redirect('/dashboard');
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
}


