<?php
declare(strict_types=1);

class AuditController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$ingredientLossesOnly = isset($_GET['ingredient_losses_only']) && $_GET['ingredient_losses_only'] === '1';
		$moduleFilter = trim((string)($_GET['module'] ?? ''));
		
		$filters = [
			'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null,
			'module' => $ingredientLossesOnly ? '' : $moduleFilter, // Don't set module when ingredient_losses_only is active
			'date_from' => trim((string)($_GET['date_from'] ?? '')),
			'date_to' => trim((string)($_GET['date_to'] ?? '')),
			'search' => trim((string)($_GET['q'] ?? '')),
			'ingredient_losses_only' => $ingredientLossesOnly,
		];
		$limit = isset($_GET['limit']) ? max(20, min((int)$_GET['limit'], 500)) : 200;
		$filters['limit'] = $limit;
		$audit = new AuditLog();
		$logs = $audit->list($filters);
		$usersModel = new User();
		$users = $usersModel->all();
		$modules = $audit->listModules();
		$flash = $_SESSION['flash_audit'] ?? null;
		unset($_SESSION['flash_audit']);
		$this->render('audit/index.php', [
			'logs' => $logs,
			'filters' => $filters,
			'users' => $users,
			'modules' => $modules,
			'limitReached' => count($logs) >= $limit,
			'flash' => $flash,
		]);
	}

	public function clear(): void
	{
		Auth::requireRole(['Owner','Manager']);
		Csrf::verify($_POST['csrf_token'] ?? '');
		$scope = $_POST['scope'] ?? 'filtered';
		$current = $_POST['current'] ?? [];
		$ingredientLossesOnly = isset($current['ingredient_losses_only']) && $current['ingredient_losses_only'] === '1';
		$filters = [
			'user_id' => isset($current['user_id']) && $current['user_id'] !== '' ? (int)$current['user_id'] : null,
			'module' => $ingredientLossesOnly ? 'ingredients' : trim((string)($current['module'] ?? '')),
			'date_from' => trim((string)($current['date_from'] ?? '')),
			'date_to' => trim((string)($current['date_to'] ?? '')),
			'search' => trim((string)($current['search'] ?? '')),
			'ingredient_losses_only' => $ingredientLossesOnly,
		];
		if ($scope === 'all') {
			$filters = [];
		}
		$audit = new AuditLog();
		$deleted = $audit->clear($filters);
		if ($deleted > 0) {
			$_SESSION['flash_audit'] = ['type' => 'success', 'text' => number_format($deleted) . ' audit log' . ($deleted === 1 ? '' : 's') . ' removed.'];
		} else {
			$_SESSION['flash_audit'] = ['type' => 'info', 'text' => 'No audit logs matched the selected criteria.'];
		}
		$returnQuery = trim((string)($_POST['return_query'] ?? ''));
		$redirectTo = '/audit' . ($returnQuery ? ('?' . $returnQuery) : '');
		$this->redirect($redirectTo);
	}
}


