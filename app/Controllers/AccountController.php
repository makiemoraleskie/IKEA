<?php
declare(strict_types=1);

class AccountController extends BaseController
{
	public function security(): void
	{
		Auth::requireLogin();
		$user = Auth::user();
		$flash = $_SESSION['flash_account'] ?? null;
		unset($_SESSION['flash_account']);

		$theme = $_SESSION['user_theme'] ?? Settings::themeDefault();
		$this->render('account/security.php', [
			'user' => $user,
			'theme' => $theme,
			'flash' => $flash,
		]);
	}

	public function updatePassword(): void
	{
		Auth::requireLogin();
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$current = (string)($_POST['current_password'] ?? '');
		$new = (string)($_POST['new_password'] ?? '');
		$confirm = (string)($_POST['confirm_password'] ?? '');

		if (strlen($new) < 8 || $new !== $confirm) {
			$_SESSION['flash_account'] = ['type' => 'error', 'text' => 'Ensure the new password is at least 8 characters and the confirmation matches.'];
			$this->redirect('/account/security');
		}

		$userId = Auth::id() ?? 0;
		$userModel = new User();
		$record = $userModel->findAuthById($userId);
		if (!$record || !password_verify($current, $record['password_hash'])) {
			$_SESSION['flash_account'] = ['type' => 'error', 'text' => 'Current password is incorrect.'];
			$this->redirect('/account/security');
		}

		$userModel->resetPassword($userId, password_hash($new, PASSWORD_DEFAULT));
		$security = new UserSecurity();
		$security->bumpSession($userId);

		$logger = new AuditLog();
		$logger->log($userId, 'change_password', 'account', []);

		Auth::logout();
		header('Location: /login?status=password-updated');
		exit;
	}

	public function updateTheme(): void
	{
		Auth::requireLogin();
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$theme = strtolower(trim((string)($_POST['theme'] ?? 'system')));
		if (!in_array($theme, ['light','dark','system'], true)) {
			$theme = 'system';
		}
		$userId = Auth::id() ?? 0;
		$security = new UserSecurity();
		$security->setTheme($userId, $theme);
		$_SESSION['user_theme'] = $theme;
		$_SESSION['flash_account'] = ['type' => 'success', 'text' => 'Your theme preference has been updated.'];
		$this->redirect('/account/security');
	}
}


