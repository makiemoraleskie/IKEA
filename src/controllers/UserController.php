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
		$this->render('users/index.php', [
			'users' => $users,
			'roles' => $this->roles,
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
			$this->redirect('/users');
		}
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$userModel = new User();
		$id = $userModel->create($name, $role, $email, $passwordHash);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'create', 'users', ['user_id' => $id, 'email' => $email]);
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
		if ($id <= 0 || strlen($password) < 8) { $this->redirect('/users'); }
		$userModel = new User();
		$userModel->resetPassword($id, password_hash($password, PASSWORD_DEFAULT));
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'reset_password', 'users', ['user_id' => $id]);
		$this->redirect('/users');
	}
}


