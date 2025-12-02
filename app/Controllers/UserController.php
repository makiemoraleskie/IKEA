<?php
declare(strict_types=1);

class UserController extends BaseController
{
	private array $roles = ['Owner','Manager','Stock Handler','Purchaser','Kitchen Staff'];

	public function index(): void
	{
		Auth::requireRole(['Owner']);
		$userModel = new User();
		$users = $userModel->all();
		$flash = $_SESSION['flash_users'] ?? null;
		unset($_SESSION['flash_users']);
		$this->render('users/index.php', [
			'users' => $users,
			'roles' => $this->roles,
			'flash' => $flash,
		]);
	}

	public function store(): void
	{
		Auth::requireRole(['Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$name = trim((string)($_POST['name'] ?? ''));
		$email = trim((string)($_POST['email'] ?? ''));
		$role = (string)($_POST['role'] ?? '');
		$password = (string)($_POST['password'] ?? '');
		if ($name === '' || $email === '' || !in_array($role, $this->roles, true) || strlen($password) < 8) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'Please fill out all fields and use a password with at least 8 characters.'];
			$this->redirect('/users');
		}
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$userModel = new User();
		if ($userModel->emailExists($email)) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'That email is already in use.'];
			$this->redirect('/users');
		}
		$id = $userModel->create($name, $role, $email, $passwordHash);
		$security = new UserSecurity();
		$security->setStatus($id, 'active');
		$security->bumpSession($id);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'users', ['user_id' => $id, 'email' => $email]);
		$_SESSION['flash_users'] = ['type' => 'success', 'text' => 'User created successfully.'];
		$this->redirect('/users');
	}

	public function resetPassword(): void
	{
		Auth::requireRole(['Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
		$password = (string)($_POST['password'] ?? '');
		if ($id <= 0 || strlen($password) < 8) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'Password must be at least 8 characters.'];
			$this->redirect('/users');
		}
		$userModel = new User();
		$userModel->resetPassword($id, password_hash($password, PASSWORD_DEFAULT));
		$security = new UserSecurity();
		$security->bumpSession($id);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'reset_password', 'users', ['user_id' => $id]);
		$_SESSION['flash_users'] = ['type' => 'success', 'text' => 'Password updated.'];
		$this->redirect('/users');
	}

	public function update(): void
	{
		Auth::requireRole(['Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
		$name = trim((string)($_POST['name'] ?? ''));
		$email = trim((string)($_POST['email'] ?? ''));
		$role = (string)($_POST['role'] ?? '');
		if ($id <= 0 || $name === '' || $email === '' || !in_array($role, $this->roles, true)) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'Please provide valid name, email, and role.'];
			$this->redirect('/users');
		}
		$userModel = new User();
		$existing = $userModel->find($id);
		if (!$existing) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'User not found.'];
			$this->redirect('/users');
		}
		if ($userModel->emailExists($email, $id)) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'Another account already uses that email.'];
			$this->redirect('/users');
		}
		$userModel->updateUser($id, $name, $email, $role);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'update', 'users', ['user_id' => $id, 'changes' => ['name' => $name, 'email' => $email, 'role' => $role]]);
		$_SESSION['flash_users'] = ['type' => 'success', 'text' => 'User details updated.'];
		$this->redirect('/users');
	}

	public function delete(): void
	{
		Auth::requireRole(['Owner']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$id = (int)($_POST['id'] ?? 0);
		if ($id <= 0) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'Invalid user selection.'];
			$this->redirect('/users');
		}
		if ($id === (Auth::id() ?? 0)) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'You cannot delete the account you are currently signed in with.'];
			$this->redirect('/users');
		}
		$userModel = new User();
		$existing = $userModel->find($id);
		if (!$existing) {
			$_SESSION['flash_users'] = ['type' => 'error', 'text' => 'User not found.'];
			$this->redirect('/users');
		}
		$userModel->deleteUser($id);
		$security = new UserSecurity();
		$security->clearSession($id);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'delete', 'users', ['user_id' => $id, 'email' => $existing['email']]);
		$_SESSION['flash_users'] = ['type' => 'success', 'text' => 'User deleted.'];
		$this->redirect('/users');
	}
}


