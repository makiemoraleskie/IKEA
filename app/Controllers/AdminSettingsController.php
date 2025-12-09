<?php
declare(strict_types=1);

class AdminSettingsController extends BaseController
{
	private array $roles = ['Owner','Manager','Purchaser','Stock Handler','Kitchen Staff'];
	private array $permissionKeys = ['view_costs','view_receipts','manage_reports','access_backups'];
	private array $dashboardWidgets = ['low_stock','pending_requests','pending_payments','partial_deliveries','pending_deliveries','inventory_value'];

	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$userModel = new User();
		$users = $userModel->all();
		$security = new UserSecurity();
		$statusMap = $security->getMany(array_column($users, 'id'));
		foreach ($users as &$user) {
			$userId = (int)$user['id'];
			$user['status'] = $statusMap[$userId]['status'] ?? 'active';
		}
		unset($user);

		$flash = $_SESSION['flash_admin_settings'] ?? null;
		unset($_SESSION['flash_admin_settings']);

		$this->render('admin/settings/index.php', [
			'users' => $users,
			'roles' => $this->roles,
			'permissionKeys' => $this->permissionKeys,
			'dashboardWidgets' => $this->dashboardWidgets,
			'settings' => [
				'costHiddenRoles' => Settings::getJson('security.cost_hidden_roles', []),
				'permissions' => Settings::getJson('security.permissions', []),
				'archiveDays' => Settings::archiveDays(),
				'enabledReports' => Settings::reportSectionsEnabled(),
				'companyName' => Settings::companyName(),
				'companyTagline' => Settings::companyTagline(),
				'logoPath' => Settings::logoPath(),
				'themeDefault' => Settings::themeDefault(),
				'dashboardWidgets' => Settings::getJson('display.dashboard_widgets', []),
				'ingredientSetsEnabled' => Settings::get('features.ingredient_sets_enabled', '1') === '1',
				'inventoryActionsVisible' => Settings::get('inventory.actions_visible', '0') === '1',
			],
			'flash' => $flash,
		]);
	}

	public function save(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$section = $_POST['section'] ?? '';
		$userId = Auth::id();
		switch ($section) {
			case 'security':
				$costHidden = array_values(array_filter(array_map('trim', $_POST['cost_hidden_roles'] ?? [])));
				Settings::setJson('security.cost_hidden_roles', $costHidden, $userId);

				$rawPerms = $_POST['permissions'] ?? [];
				$normalized = [];
				foreach ($this->roles as $role) {
					$rolePerms = $rawPerms[$role] ?? [];
					$normalized[$role] = [];
					foreach ($this->permissionKeys as $perm) {
						$normalized[$role][$perm] = isset($rolePerms[$perm]) ? true : false;
					}
				}
				Settings::setJson('security.permissions', $normalized, $userId);
				$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'Security preferences updated.'];
				break;
			case 'reporting':
				$archive = max(0, (int)($_POST['archive_days'] ?? 0));
				Settings::set('reporting.archive_days', (string)$archive, $userId);
				$reports = $_POST['reports'] ?? ['purchase','consumption'];
				$validSections = array_values(array_intersect(['purchase','consumption'], $reports));
				if (empty($validSections)) {
					$validSections = ['purchase','consumption'];
				}
				Settings::setJson('reporting.enabled_sections', $validSections, $userId);
				$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'Reporting configuration saved.'];
				break;
			case 'display':
				$name = trim((string)($_POST['company_name'] ?? ''));
				$tagline = trim((string)($_POST['company_tagline'] ?? ''));
				$theme = strtolower(trim((string)($_POST['theme_default'] ?? 'system')));
				if ($name === '') {
					$name = Settings::companyName();
				}
				if (!in_array($theme, ['light','dark','system'], true)) {
					$theme = 'system';
				}

				$widgetInput = $_POST['widgets'] ?? [];
				$widgetSettings = [];
				foreach ($this->roles as $role) {
					$selected = array_values(array_intersect($this->dashboardWidgets, $widgetInput[$role] ?? []));
					if (!empty($selected)) {
						$widgetSettings[$role] = $selected;
					}
				}
				if (!isset($widgetSettings['default'])) {
					$widgetSettings['default'] = $this->dashboardWidgets;
				}

				// Handle ingredient sets toggle
				// Hidden input sends '0', checkbox sends '1' when checked
				// When checkbox is checked, PHP may send array ['0', '1'] or just '1'
				// When checkbox is unchecked, POST will have '0' (from hidden input)
				$ingredientSetsValue = $_POST['ingredient_sets_enabled'] ?? '0';
				
				// Handle array case (when both hidden and checkbox are present)
				if (is_array($ingredientSetsValue)) {
					// If '1' is in the array, checkbox was checked
					$ingredientSetsEnabled = in_array('1', $ingredientSetsValue, true) ? '1' : '0';
				} else {
					// Single value - use it directly
					$ingredientSetsEnabled = ($ingredientSetsValue === '1') ? '1' : '0';
				}
				
				// Always save explicitly (either '1' or '0')
				Settings::set('features.ingredient_sets_enabled', $ingredientSetsEnabled, $userId);

				Settings::set('display.company_name', $name, $userId);
				Settings::set('display.company_tagline', $tagline, $userId);
				Settings::set('display.theme_default', $theme, $userId);
				Settings::setJson('display.dashboard_widgets', $widgetSettings, $userId);
				$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'Display preferences saved.'];
				break;
			case 'inventory_actions':
				$visible = $_POST['inventory_actions_visible'] ?? '0';
				$visible = $visible === '1' ? '1' : '0';
				Settings::set('inventory.actions_visible', $visible, $userId);
				$state = $visible === '1' ? 'shown' : 'hidden';
				$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => "Inventory action buttons are now {$state}."];
				break;
			default:
				$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Unknown settings section.'];
		}

		$this->redirect('/admin/settings');
	}

	public function updateBranding(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$file = $_FILES['logo'] ?? null;
		if (!$file || (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Select an image to upload.'];
			$this->redirect('/admin/settings');
		}
		if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Failed to upload logo.'];
			$this->redirect('/admin/settings');
		}
		$allowed = [
			'image/png' => '.png',
			'image/jpeg' => '.jpg',
			'image/webp' => '.webp',
			'image/svg+xml' => '.svg',
		];
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($file['tmp_name']);
		$ext = $allowed[$mime] ?? null;
		if (!$ext) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Unsupported logo format.'];
			$this->redirect('/admin/settings');
		}
		$uploadDir = BASE_PATH . '/public/uploads/branding/';
		if (!is_dir($uploadDir)) {
			@mkdir($uploadDir, 0755, true);
		}
		$filename = 'logo_' . date('Ymd_His') . $ext;
		$target = $uploadDir . $filename;
		if (!move_uploaded_file($file['tmp_name'], $target)) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Could not store the uploaded logo.'];
			$this->redirect('/admin/settings');
		}
		Settings::set('display.logo_path', '/public/uploads/branding/' . $filename, Auth::id());
		$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'Brand logo updated.'];
		$this->redirect('/admin/settings');
	}

	public function exportBackup(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$format = strtolower((string)($_POST['format'] ?? 'sql'));
		$format = in_array($format, ['sql','csv'], true) ? $format : 'sql';
		$now = date('Y-m-d_His');
		if ($format === 'sql') {
			$this->streamSqlBackup($now);
			return;
		}
		$this->streamCsvBackup($now);
	}

	public function importInventory(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$file = $_FILES['inventory_csv'] ?? null;
		if (!$file || (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Select a CSV file to import.'];
			$this->redirect('/admin/settings');
		}
		$handle = fopen($file['tmp_name'], 'r');
		if (!$handle) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Unable to read the uploaded file.'];
			$this->redirect('/admin/settings');
		}

		$header = fgetcsv($handle);
		if (!$header) {
			fclose($handle);
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'CSV file is empty.'];
			$this->redirect('/admin/settings');
		}
		$header = array_map(function ($value) {
			return strtolower(trim((string)$value));
		}, $header);

		$ingredientModel = new Ingredient();
		$imported = 0;
		while (($row = fgetcsv($handle)) !== false) {
			$data = array_combine($header, $row);
			if (!$data) {
				continue;
			}
			$name = trim((string)($data['name'] ?? ''));
			if ($name === '') {
				continue;
			}
			$unit = trim((string)($data['unit'] ?? ''));
			if ($unit === '') {
				$unit = 'unit';
			}
			$quantity = (float)($data['quantity'] ?? 0);
			$reorder = (float)($data['reorder_level'] ?? 0);
			$category = trim((string)($data['category'] ?? ''));
			$displayUnit = trim((string)($data['display_unit'] ?? ''));
			$displayFactor = (float)($data['display_factor'] ?? 1);

			$existing = $this->findIngredientByName($ingredientModel, $name);
			if ($existing) {
				$newQty = max(0.0, (float)$existing['quantity'] + $quantity);
				$this->updateIngredient($existing['id'], $newQty, $reorder, $category, $displayUnit, $displayFactor);
			} else {
				// Mark new ingredients as in inventory even if starting quantity is zero
				$id = $ingredientModel->create(
					$name,
					$unit,
					$reorder,
					$displayUnit ?: null,
					$displayFactor > 0 ? $displayFactor : 1,
					null,
					0,
					$category ?: null,
					true // in_inventory
				);
				if ($quantity > 0) {
					$ingredientModel->updateQuantity($id, $quantity);
				}
			}
			$imported++;
		}
		fclose($handle);

		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'import', 'inventory', ['rows' => $imported]);
		$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => "Imported {$imported} inventory rows."];
		$this->redirect('/admin/settings');
	}

	public function toggleUserStatus(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$userId = (int)($_POST['user_id'] ?? 0);
		$status = $_POST['status'] ?? 'active';
		if ($userId <= 0 || !in_array($status, ['active','inactive'], true)) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Invalid user selection.'];
			$this->redirect('/admin/settings');
		}
		if ($userId === (Auth::id() ?? 0) && $status === 'inactive') {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'You cannot deactivate the account you are signed in with.'];
			$this->redirect('/admin/settings');
		}
		$security = new UserSecurity();
		$security->setStatus($userId, $status);
		$security->bumpSession($userId);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'update_status', 'users', ['user_id' => $userId, 'status' => $status]);
		$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'User status updated.'];
		$this->redirect('/admin/settings');
	}

	public function forceLogoutUser(): void
	{
		Auth::requireRole(['Owner','Manager']);
		if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
			http_response_code(400);
			echo 'Invalid CSRF token';
			return;
		}
		$userId = (int)($_POST['user_id'] ?? 0);
		if ($userId <= 0) {
			$_SESSION['flash_admin_settings'] = ['type' => 'error', 'text' => 'Invalid target account.'];
			$this->redirect('/admin/settings');
		}
		$security = new UserSecurity();
		$security->bumpSession($userId);
		$logger = new AuditLog();
		$logger->log(Auth::id() ?? 0, 'force_logout', 'users', ['user_id' => $userId]);
		$_SESSION['flash_admin_settings'] = ['type' => 'success', 'text' => 'User will be required to sign in again.'];
		$this->redirect('/admin/settings');
	}

	private function streamSqlBackup(string $timestamp): void
	{
		$pdo = Database::getConnection();
		$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
		$output = "-- IKEA Commissary backup generated on {$timestamp}\n";
		$output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
		foreach ($tables as $table) {
			$create = $pdo->query('SHOW CREATE TABLE ' . $table)->fetch(PDO::FETCH_ASSOC);
			$output .= "DROP TABLE IF EXISTS `{$table}`;\n";
			$output .= $create['Create Table'] . ";\n\n";
			$rows = $pdo->query('SELECT * FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);
			if (empty($rows)) {
				continue;
			}
			foreach ($rows as $row) {
				$values = array_map(function ($value) use ($pdo) {
					if ($value === null) {
						return 'NULL';
					}
					return $pdo->quote((string)$value);
				}, array_values($row));
				$output .= 'INSERT INTO `' . $table . '` VALUES (' . implode(',', $values) . ");\n";
			}
			$output .= "\n";
		}
		$output .= "SET FOREIGN_KEY_CHECKS=1;\n";

		header('Content-Type: application/sql; charset=utf-8');
		header('Content-Disposition: attachment; filename="backup_' . $timestamp . '.sql"');
		header('Content-Length: ' . strlen($output));
		echo $output;
		exit;
	}

	private function streamCsvBackup(string $timestamp): void
	{
		$pdo = Database::getConnection();
		$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="backup_' . $timestamp . '.csv"');
		$output = fopen('php://output', 'w');
		foreach ($tables as $table) {
			fputcsv($output, ["### {$table} ###"]);
			$rows = $pdo->query('SELECT * FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);
			if (empty($rows)) {
				fputcsv($output, ['(no records)']);
				fputcsv($output, []); // spacer
				continue;
			}
			fputcsv($output, array_keys($rows[0]));
			foreach ($rows as $row) {
				fputcsv($output, array_values($row));
			}
			fputcsv($output, []); // spacer
		}
		fclose($output);
		exit;
	}

	private function findIngredientByName(Ingredient $model, string $name): ?array
	{
		$sql = 'SELECT * FROM ingredients WHERE name = ? LIMIT 1';
		$stmt = Database::getConnection()->prepare($sql);
		$stmt->execute([$name]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	private function updateIngredient(int $id, float $quantity, float $reorder, string $category, string $displayUnit, float $displayFactor): void
	{
		$sql = 'UPDATE ingredients SET quantity = ?, reorder_level = ?, category = ?, display_unit = ?, display_factor = ? WHERE id = ?';
		$stmt = Database::getConnection()->prepare($sql);
		$stmt->execute([$quantity, $reorder, $category, $displayUnit, $displayFactor > 0 ? $displayFactor : 1, $id]);
	}
}


