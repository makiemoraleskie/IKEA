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
		];
		$audit = new AuditLog();
		$logs = $audit->list($filters);
		$this->render('audit/index.php', [
			'logs' => $logs,
			'filters' => $filters,
		]);
	}
}


