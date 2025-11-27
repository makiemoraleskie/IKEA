<?php
declare(strict_types=1);

class AuditController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$filters = [
			'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null,
			'module' => trim((string)($_GET['module'] ?? '')),
			'date_from' => trim((string)($_GET['date_from'] ?? '')),
			'date_to' => trim((string)($_GET['date_to'] ?? '')),
			'search' => trim((string)($_GET['q'] ?? '')),
		];
		$limit = isset($_GET['limit']) ? max(20, min((int)$_GET['limit'], 500)) : 200;
		$filters['limit'] = $limit;
		$audit = new AuditLog();
		$logs = $audit->list($filters);
		$usersModel = new User();
		$users = $usersModel->all();
		$modules = $audit->listModules();
		$this->render('audit/index.php', [
			'logs' => $logs,
			'filters' => $filters,
			'users' => $users,
			'modules' => $modules,
			'limitReached' => count($logs) >= $limit,
		]);
	}
}


