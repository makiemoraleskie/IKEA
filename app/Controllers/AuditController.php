<?php
declare(strict_types=1);

class AuditController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$dateFromRaw = trim((string)($_GET['date_from'] ?? ''));
		$dateToRaw = trim((string)($_GET['date_to'] ?? ''));
		$dateFrom = $this->normalizeDate($dateFromRaw);
		$dateTo = $this->normalizeDate($dateToRaw);

		// If both dates are provided but out of order, swap to ensure a valid range
		if ($dateFrom && $dateTo) {
			$fromTs = strtotime($dateFrom);
			$toTs = strtotime($dateTo);
			if ($fromTs !== false && $toTs !== false && $fromTs > $toTs) {
				[$dateFrom, $dateTo] = [$dateTo, $dateFrom];
			}
		}

		$filters = [
			'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null,
			'module' => trim((string)($_GET['module'] ?? '')),
			'date_from' => $dateFrom ?: '',
			'date_to' => $dateTo ?: '',
			'search' => trim((string)($_GET['q'] ?? '')),
		];
		$rawLimit = $_GET['limit'] ?? null;
		$hasDateRange = ($dateFrom !== '' && $dateFrom !== null) || ($dateTo !== '' && $dateTo !== null);
		if ($rawLimit === 'all') {
			$limit = 0; // no limit
		} elseif ($rawLimit !== null) {
			$limit = max(20, min((int)$rawLimit, 500));
		} elseif ($hasDateRange) {
			// When filtering by date range but no limit is chosen, show the full range
			$limit = 0;
		} else {
			$limit = 200;
		}
		$filters['limit'] = ($rawLimit === 'all' || $limit === 0) ? 'all' : $limit;
		$filters['_limit_effective'] = $limit; // keep the numeric limit used for querying
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
			'limitReached' => $limit > 0 && count($logs) >= $limit,
			'flash' => $flash,
		]);
	}

	private function normalizeDate(?string $input): ?string
	{
		$input = trim((string)$input);
		if ($input === '') {
			return null;
		}
		$dt = \DateTime::createFromFormat('Y-m-d', $input);
		if ($dt instanceof \DateTime) {
			return $dt->format('Y-m-d');
		}
		$ts = strtotime($input);
		return $ts !== false ? date('Y-m-d', $ts) : null;
	}

	public function clear(): void
	{
		Auth::requireRole(['Owner','Manager']);
		Csrf::verify($_POST['csrf_token'] ?? '');
		$scope = $_POST['scope'] ?? 'filtered';
		$current = $_POST['current'] ?? [];
		$filters = [
			'user_id' => isset($current['user_id']) && $current['user_id'] !== '' ? (int)$current['user_id'] : null,
			'module' => trim((string)($current['module'] ?? '')),
			'date_from' => trim((string)($current['date_from'] ?? '')),
			'date_to' => trim((string)($current['date_to'] ?? '')),
			'search' => trim((string)($current['search'] ?? '')),
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


