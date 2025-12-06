<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$activeDateLabel = 'All activity';
$from = $filters['date_from'] ?? '';
$to = $filters['date_to'] ?? '';
if ($from || $to) {
	$fromLabel = $from ? date('M d, Y', strtotime((string)$from)) : 'start';
	$toLabel = $to ? date('M d, Y', strtotime((string)$to)) : 'today';
	$activeDateLabel = $fromLabel . ' → ' . $toLabel;
}
$currentQuery = http_build_query(array_filter($_GET ?? [], fn($value) => $value !== '' && $value !== null));
?>
<style>
.custom-scroll::-webkit-scrollbar{width:8px;height:8px}
.custom-scroll::-webkit-scrollbar-thumb{background:rgba(100,116,139,0.4);border-radius:9999px}
.custom-scroll::-webkit-scrollbar-track{background:rgba(226,232,240,0.4)}

/* Remove focus ring and border color for all input fields */
input:focus,
select:focus,
textarea:focus {
	outline: none !important;
	box-shadow: none !important;
	border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
	--tw-ring-offset-shadow: 0 0 #0000 !important;
	--tw-ring-shadow: 0 0 #0000 !important;
	--tw-ring-offset-width: 0px !important;
	--tw-ring-width: 0px !important;
}

/* Tablet mode fixes for Clear Logs section */
@media (min-width: 768px) and (max-width: 1023px) {
	#clearLogsForm {
		width: 100%;
	}
	#clearLogsForm select[name="scope"] {
		flex: 1;
		min-width: 0;
	}
	#clearLogsForm button[type="submit"] {
		flex-shrink: 0;
	}
	
	/* Badge spacing on tablet */
	.bg-gray-100 .flex.items-center.gap-2 {
		gap: 0.75rem;
	}
}
</style>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div>
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1">Audit Logs</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Track and monitor system activities across modules</p>
		</div>
	</div>
</div>

<!-- Summary Cards -->
<?php
$limitReached = $limitReached ?? false;
$users = $users ?? [];
$modules = $modules ?? [];
$timeline = array_slice($logs, 0, 10);

$formatDetailSentence = static function ($rawDetails): ?string {
	if ($rawDetails === null || $rawDetails === '') {
		return null;
	}
	$array = json_decode((string)$rawDetails, true);
	if (json_last_error() !== JSON_ERROR_NONE || !is_array($array)) {
		return trim((string)$rawDetails);
	}
	$parts = [];
	foreach ($array as $key => $value) {
		$label = ucwords(str_replace('_', ' ', (string)$key));
		if (is_bool($value)) {
			$prettyValue = $value ? 'yes' : 'no';
		} elseif (is_scalar($value)) {
			$prettyValue = (string)$value;
		} else {
			$prettyValue = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		$parts[] = "{$label} is {$prettyValue}";
	}
	return $parts ? implode(', ', $parts) . '.' : null;
};

// Helper function to extract key summary from details
$extractActivitySummary = static function ($rawDetails, $module, $action): string {
	if ($rawDetails === null || $rawDetails === '') {
		return '';
	}
	$array = json_decode((string)$rawDetails, true);
	if (json_last_error() !== JSON_ERROR_NONE || !is_array($array)) {
		return '';
	}
	
	// Priority order for extracting summary based on module and common fields
	$summaryKeys = [];
	
	switch (strtolower($module)) {
		case 'ingredients':
		case 'inventory':
			$summaryKeys = ['name', 'ingredient_name', 'item_name', 'quantity', 'stock'];
			break;
		case 'purchases':
			$summaryKeys = ['batch_id', 'purchase_id', 'supplier', 'total_cost', 'cost'];
			break;
		case 'requests':
			$summaryKeys = ['batch_id', 'request_id', 'requester_name', 'request_name', 'status'];
			break;
		case 'deliveries':
			$summaryKeys = ['delivery_id', 'batch_id', 'supplier', 'status'];
			break;
		case 'users':
			$summaryKeys = ['username', 'name', 'email', 'role'];
			break;
		default:
			$summaryKeys = ['name', 'title', 'id', 'batch_id', 'request_id', 'purchase_id'];
	}
	
	// Try to find a meaningful summary
	foreach ($summaryKeys as $key) {
		if (isset($array[$key]) && is_scalar($array[$key]) && trim((string)$array[$key]) !== '') {
			$value = trim((string)$array[$key]);
			// Format based on key type
			if (in_array($key, ['batch_id', 'purchase_id', 'request_id', 'delivery_id'])) {
				return '#' . $value;
			}
			if (in_array($key, ['total_cost', 'cost', 'price', 'amount'])) {
				return '₱' . number_format((float)$value, 2);
			}
			// For names/titles, limit length
			if (strlen($value) > 30) {
				return '"' . substr($value, 0, 27) . '..."';
			}
			return '"' . $value . '"';
		}
	}
	
	// Fallback: get first meaningful scalar value
	foreach ($array as $key => $value) {
		if (is_scalar($value) && trim((string)$value) !== '' && !in_array(strtolower($key), ['id', 'user_id', 'created_at', 'updated_at', 'timestamp'])) {
			$val = trim((string)$value);
			if (strlen($val) > 30) {
				return '"' . substr($val, 0, 27) . '..."';
			}
			return '"' . $val . '"';
		}
	}
	
	return '';
};
?>

<?php if (!empty($logs)): ?>
<?php 
// Calculate audit statistics
$totalLogs = count($logs);
$uniqueUsers = count(array_unique(array_column($logs, 'user_id')));
$uniqueModules = count(array_unique(array_column($logs, 'module')));
$todayLogs = 0;
foreach ($logs as $log) {
	if (date('Y-m-d', strtotime($log['timestamp'])) === date('Y-m-d')) {
		$todayLogs++;
	}
}
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8">
	<!-- Total Logs -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="file-text" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL LOGS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo $totalLogs; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">All audit entries</p>
		</div>
	</div>
	
	<!-- Today's Logs -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="calendar" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TODAY'S ACTIVITY</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo $todayLogs; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Today's actions</p>
		</div>
	</div>
	
	<!-- Active Users -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="users" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">ACTIVE USERS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo $uniqueUsers; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Unique users</p>
		</div>
	</div>
	
	<!-- Modules Tracked -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="layers" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">MODULES TRACKED</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo $uniqueModules; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Tracked modules</p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden">
	<div class="bg-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
		<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
			<i data-lucide="filter" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
			Audit Filters
		</h2>
		<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Filter audit logs by user, module, and date range</p>
	</div>
	
	<form method="get" class="p-4 md:p-5 lg:p-6 space-y-5">
		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
			<!-- User -->
			<div class="space-y-2 xl:col-span-2">
				<label class="block text-sm font-medium text-gray-700">User</label>
				<select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
					<option value="">All users</option>
					<?php foreach ($users as $user): ?>
						<option value="<?php echo (int)$user['id']; ?>" <?php echo ((int)($filters['user_id'] ?? 0) === (int)$user['id']) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($user['name'] . ' — ' . $user['role']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<!-- Module -->
			<div class="space-y-2 xl:col-span-2">
				<label class="block text-sm font-medium text-gray-700">Module</label>
				<select name="module" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
					<option value="">All modules</option>
					<?php foreach ($modules as $moduleName): ?>
						<option value="<?php echo htmlspecialchars($moduleName); ?>" <?php echo ($filters['module'] ?? '') === $moduleName ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars(ucwords(str_replace('_',' ', $moduleName))); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<!-- Date From -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">From Date</label>
				<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" />
			</div>
			
			<!-- Date To -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">To Date</label>
				<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" />
			</div>
			
			<!-- Keyword -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Keyword</label>
				<input type="text" name="q" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Search action or details" />
			</div>
			
			<!-- Limit -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Show entries</label>
				<select name="limit" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
					<?php foreach ([50,100,200,500] as $lim): ?>
						<option value="<?php echo $lim; ?>" <?php echo ((int)($filters['limit'] ?? 200) === $lim) ? 'selected' : ''; ?>><?php echo $lim; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="flex flex-wrap items-center gap-3">
			<button type="submit" class="inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
				<i data-lucide="search" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Apply Filters
			</button>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/audit" class="inline-flex items-center gap-1 md:gap-1.5 px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs md:text-sm">
				<i data-lucide="rotate-ccw" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Reset
			</a>
		</div>
	</form>
	<div class="border-t border-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/60">
		<div class="text-xs md:text-sm text-gray-600 flex-shrink-0">
			Clear logs <span class="font-semibold">by date range or entire history</span>. Only Owners and Managers can perform this action.
		</div>
		<form id="clearLogsForm" method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/audit/clear" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 flex-shrink-0">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="return_query" value="<?php echo htmlspecialchars($currentQuery); ?>">
			<?php foreach (['user_id','module','date_from','date_to','search','limit'] as $key): ?>
				<input type="hidden" name="current[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($filters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<select name="scope" class="border border-gray-300 rounded-lg px-3 md:px-4 py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full md:w-auto">
				<option value="filtered">Only logs matching current filters</option>
				<option value="all">All audit logs</option>
			</select>
			<button type="submit" class="inline-flex items-center justify-center gap-1 md:gap-1.5 bg-red-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-xs md:text-sm transition-colors whitespace-nowrap">
				<i data-lucide="trash-2" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Clear Logs
			</button>
		</form>
	</div>
</div>	

<?php if (!empty($timeline)): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden">
	<div class="bg-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
		<div>
			<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
				<i data-lucide="activity" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
				Recent Activity Timeline
			</h2>
			<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Latest <?php echo count($timeline); ?> actions across all modules.</p>
		</div>
		<div class="flex items-center gap-2 flex-wrap">
			<span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-semibold px-2.5 md:px-3 py-1.5 rounded-full bg-green-100 text-green-700 border border-green-200">
				<i data-lucide="clock-3" class="w-3 h-3 md:w-3.5 md:h-3.5 flex-shrink-0"></i>
				<span class="flex flex-col leading-tight">
					<span>Updated <?php echo htmlspecialchars(date('M j, Y')); ?></span>
					<span class="text-[9px] md:text-[10px]"><?php echo htmlspecialchars(date('g:i A')); ?></span>
				</span>
			</span>
			<span class="text-[10px] md:text-xs text-gray-500 bg-white/70 border border-gray-200 rounded-full px-2.5 md:px-3 py-1.5 inline-flex items-center gap-1.5">
				<i data-lucide="calendar" class="w-3 h-3 md:w-3.5 md:h-3.5 flex-shrink-0"></i>
				<?php if ($activeDateLabel === 'All activity'): ?>
					<span class="flex flex-col leading-tight">
						<span class="text-[9px] md:text-[10px]">All Activity</span>
					</span>
				<?php else: ?>
					<span class="leading-tight"><?php echo htmlspecialchars($activeDateLabel); ?></span>
				<?php endif; ?>
			</span>
		</div>
	</div>
	<div class="overflow-x-auto overflow-y-auto max-h-[26rem] custom-scroll">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Activity</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">User</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Time</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($timeline as $index => $entry): 
					$action = strtolower((string)$entry['action']);
					$sentence = $formatDetailSentence($entry['details'] ?? '');
					$detailsRaw = $entry['details'] ?? '';
					$detailArray = null;
					$detailJsonPretty = null;
					if ($detailsRaw !== '' && $detailsRaw !== null) {
						$decoded = json_decode((string)$detailsRaw, true);
						if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
							$detailArray = $decoded;
							$detailJsonPretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
						} else {
							$detailJsonPretty = trim((string)$detailsRaw);
						}
					}
					$summary = $extractActivitySummary($detailsRaw, $entry['module'], $entry['action']);
					$activityText = ucfirst($entry['action']);
					$moduleText = strtolower($entry['module']);
					if ($summary) {
						$activityText .= ' ' . $moduleText . ' ' . $summary;
					} else {
						$activityText .= ' in ' . ucwords(str_replace('_', ' ', $moduleText));
					}
					$userName = $entry['user_name'] ?? (string)($entry['user_id'] ?? 'System');
					$userInitials = strtoupper(substr($userName, 0, 2));
					if ($userInitials === '') {
						$userInitials = 'SY';
					}
					$timestamp = strtotime((string)$entry['timestamp']);
					$fullDate = date('M j, Y g:i A', $timestamp);
				?>
				<tr class="hover:bg-gray-50 transition-colors cursor-pointer timeline-row" 
					data-action="<?php echo htmlspecialchars($entry['action']); ?>"
					data-module="<?php echo htmlspecialchars($entry['module']); ?>"
					data-user="<?php echo htmlspecialchars($userName); ?>"
					data-sentence="<?php echo htmlspecialchars($sentence ?? '', ENT_QUOTES, 'UTF-8'); ?>"
					data-timestamp="<?php echo $timestamp; ?>">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-2">
							<?php if (!empty($sentence) || !empty($detailJsonPretty)): ?>
								<button type="button" class="openTimelineDetailModal text-left flex-1 hover:text-green-600 transition-colors"
									data-action="<?php echo htmlspecialchars($entry['action']); ?>"
									data-module="<?php echo htmlspecialchars($entry['module']); ?>"
									data-user="<?php echo htmlspecialchars($userName); ?>"
									data-sentence="<?php echo htmlspecialchars($sentence ?? '', ENT_QUOTES, 'UTF-8'); ?>"
									data-timestamp="<?php echo $timestamp; ?>">
									<span class="font-medium text-gray-900"><?php echo htmlspecialchars($activityText); ?></span>
								</button>
							<?php else: ?>
								<span class="font-medium text-gray-900"><?php echo htmlspecialchars($activityText); ?></span>
							<?php endif; ?>
						</div>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<span class="text-gray-700 font-medium text-[10px] md:text-xs"><?php echo htmlspecialchars($userName); ?></span>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<span class="text-gray-600 text-[10px] md:text-xs timeline-time" data-full-date="<?php echo htmlspecialchars($fullDate); ?>" data-timestamp="<?php echo $timestamp; ?>">
							<?php echo htmlspecialchars($fullDate); ?>
						</span>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

<!-- Timeline Detail Modal -->
<div id="timelineDetailModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
	<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
	<div class="relative z-10 flex min-h-full items-center justify-center p-4 overflow-x-hidden">
		<div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-auto">
			<!-- Header -->
			<div class="px-4 md:px-5 lg:px-6 py-4 md:py-5 border-b border-gray-200">
				<div class="flex items-center justify-between">
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
							<i data-lucide="activity" class="w-5 h-5 text-white"></i>
						</div>
						<div>
							<h3 class="text-base md:text-lg font-bold text-gray-900">Activity Details</h3>
							<p class="text-xs md:text-sm text-gray-600 mt-0.5" id="timelineModalActivity"></p>
						</div>
					</div>
					<button type="button" id="closeTimelineModal" class="text-gray-400 hover:text-gray-600 transition-colors">
						<i data-lucide="x" class="w-5 h-5 md:w-6 md:h-6"></i>
					</button>
				</div>
			</div>
			
			<!-- Content -->
			<div class="p-4 md:p-5 lg:px-6 max-h-[70vh] overflow-y-auto custom-scroll">
				<div class="space-y-5">
					<!-- Priority 1: Timestamp -->
					<div class="bg-gray-100 rounded-lg p-3 md:p-4 border border-gray-200">
						<div class="flex items-center gap-2 mb-1">
							<i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
							<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Timestamp</h4>
						</div>
						<div class="mt-2">
							<p class="text-sm font-semibold text-gray-900" id="timelineModalFullDate"></p>
							<p class="text-xs text-gray-600 mt-0.5" id="timelineModalRelativeTime"></p>
						</div>
					</div>
					
					<!-- Priority 2: User Information -->
					<div class="bg-gray-100 rounded-lg p-3 md:p-4 border border-gray-200">
						<div class="flex items-center gap-2 mb-1">
							<i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
							<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Performed By</h4>
						</div>
						<div class="mt-2">
							<p class="text-sm font-semibold text-gray-900" id="timelineModalUser"></p>
							<p class="text-xs text-gray-600 mt-0.5" id="timelineModalUserRole"></p>
						</div>
					</div>
					
					<!-- Priority 3: Action Context -->
					<div class="bg-gray-100 rounded-lg p-3 md:p-4 border border-gray-200">
						<div class="flex items-center gap-2 mb-1">
							<i data-lucide="zap" class="w-4 h-4 text-gray-500"></i>
							<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</h4>
						</div>
						<div class="mt-2 space-y-2">
							<div class="flex items-center justify-between">
								<span class="text-xs text-gray-600">Type:</span>
								<span class="text-sm font-semibold text-gray-900" id="timelineModalAction"></span>
							</div>
							<div class="flex items-center justify-between">
								<span class="text-xs text-gray-600">Module:</span>
								<span class="text-sm font-semibold text-gray-900" id="timelineModalModule"></span>
							</div>
						</div>
					</div>
					
					<!-- Priority 4: Changes Made (Most Important) -->
					<div id="timelineModalChangesSection" class="hidden">
						<div class="bg-gray-100 rounded-lg p-3 md:p-4 border border-blue-200">
							<div class="flex items-center gap-2 mb-3">
								<i data-lucide="git-branch" class="w-4 h-4 text-blue-600"></i>
								<h4 class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Changes Made</h4>
							</div>
							<div class="space-y-2.5" id="timelineModalChangesContainer"></div>
						</div>
					</div>
					
					<!-- Priority 5: Key Information -->
					<div id="timelineModalKeyInfoSection" class="hidden">
						<div class="bg-green-50 rounded-lg p-3 md:p-4 border border-green-200">
							<div class="flex items-center gap-2 mb-3">
								<i data-lucide="info" class="w-4 h-4 text-green-600"></i>
								<h4 class="text-xs font-semibold text-green-700 uppercase tracking-wide">Key Information</h4>
							</div>
							<div class="space-y-2" id="timelineModalKeyInfoContainer"></div>
						</div>
					</div>
					
					<!-- Priority 6: Related Information -->
					<div id="timelineModalRelatedSection" class="hidden">
						<div class="bg-gray-100 rounded-lg p-3 md:p-4 border border-gray-200">
							<div class="flex items-center gap-2 mb-3">
								<i data-lucide="link" class="w-4 h-4 text-gray-500"></i>
								<h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Related Information</h4>
							</div>
							<div class="space-y-2" id="timelineModalRelatedContainer"></div>
						</div>
					</div>
					
					<!-- No Details Message -->
					<div id="timelineModalNoDetails" class="hidden">
						<div class="bg-gray-50 rounded-lg p-6 text-center border border-gray-200">
							<i data-lucide="info" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
							<p class="text-xs md:text-sm text-gray-500">No additional details available for this activity.</p>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Footer -->
			<div class="px-4 md:px-5 lg:px-6 py-4 border-t border-gray-200 flex justify-end gap-3 bg-gray-100">
				<button type="button" id="closeTimelineModalBtn" class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg border border-gray-300 bg-green-600 text-white hover:bg-green-700 transition-colors text-sm font-medium">
					Close
				</button>
			</div>
		</div>
	</div>
</div>



<script>
(function(){
	const toggleButtons = document.querySelectorAll('.detailToggle');
	toggleButtons.forEach(btn => {
		btn.addEventListener('click', ()=>{
			const target = btn.getAttribute('data-target');
			if (!target) return;
			const el = document.getElementById(target);
			if (!el) return;
			const isHidden = el.classList.toggle('hidden');
			// Update icon and text
			const icon = btn.querySelector('i[data-lucide]');
			if (icon) {
				// For "More" toggle
				icon.setAttribute('data-lucide', isHidden ? 'chevron-down' : 'chevron-up');
				// Update button text
				const textSpan = btn.querySelector('.detailToggleText');
				if (textSpan) {
					textSpan.textContent = isHidden ? 'More' : 'Less';
				}
				if (window.lucide) {
					window.lucide.createIcons({ elements: [icon] });
				}
			}
		});
	});

	// Relative time formatter
	function formatRelativeTime(timestamp) {
		const now = Math.floor(Date.now() / 1000);
		const diff = now - timestamp;
		
		if (diff < 60) {
			return 'Just now';
		} else if (diff < 3600) {
			const minutes = Math.floor(diff / 60);
			return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
		} else if (diff < 86400) {
			const hours = Math.floor(diff / 3600);
			return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
		} else if (diff < 604800) {
			const days = Math.floor(diff / 86400);
			return `${days} ${days === 1 ? 'day' : 'days'} ago`;
		} else if (diff < 2592000) {
			const weeks = Math.floor(diff / 604800);
			return `${weeks} ${weeks === 1 ? 'week' : 'weeks'} ago`;
		} else if (diff < 31536000) {
			const months = Math.floor(diff / 2592000);
			return `${months} ${months === 1 ? 'month' : 'months'} ago`;
		} else {
			const years = Math.floor(diff / 31536000);
			return `${years} ${years === 1 ? 'year' : 'years'} ago`;
		}
	}
	
	// Update relative times
	function updateRelativeTimes() {
		const timeElements = document.querySelectorAll('.timeline-time[data-timestamp]');
		timeElements.forEach(el => {
			const timestamp = parseInt(el.getAttribute('data-timestamp'), 10);
			const fullDate = el.getAttribute('data-full-date');
			const relative = formatRelativeTime(timestamp);
			el.textContent = relative;
			el.title = fullDate; // Show full date on hover
		});
	}
	
	// Update relative times on load and periodically
	updateRelativeTimes();
	setInterval(updateRelativeTimes, 60000); // Update every minute
	
	// Timeline detail modal
	const timelineModal = document.getElementById('timelineDetailModal');
	const openTimelineButtons = document.querySelectorAll('.openTimelineDetailModal');
	const timelineRows = document.querySelectorAll('.timeline-row');
	const closeTimelineModal = document.getElementById('closeTimelineModal');
	const closeTimelineModalBtn = document.getElementById('closeTimelineModalBtn');
	
	function openTimelineModal(element) {
		// Support both button and row elements
		const action = element.getAttribute('data-action') || '';
		const module = element.getAttribute('data-module') || '';
		const user = element.getAttribute('data-user') || 'System';
		const sentence = element.getAttribute('data-sentence') || '';
		const timestamp = parseInt(element.getAttribute('data-timestamp'), 10) || 0;
		
		// Set header activity info
		document.getElementById('timelineModalActivity').textContent = `${action.charAt(0).toUpperCase() + action.slice(1)} on ${module}`;
		
		// Priority 1: Timestamp
		if (timestamp > 0) {
			const date = new Date(timestamp * 1000);
			const fullDate = date.toLocaleString('en-US', { 
				month: 'short', 
				day: 'numeric', 
				year: 'numeric', 
				hour: 'numeric', 
				minute: '2-digit',
				hour12: true 
			});
			document.getElementById('timelineModalFullDate').textContent = fullDate;
			document.getElementById('timelineModalRelativeTime').textContent = formatRelativeTime(timestamp);
		}
		
		// Priority 2: User Information
		document.getElementById('timelineModalUser').textContent = user;
		document.getElementById('timelineModalUserRole').textContent = 'User Account'; // Could be enhanced with role data
		
		// Priority 3: Action Context
		document.getElementById('timelineModalAction').textContent = action.charAt(0).toUpperCase() + action.slice(1);
		document.getElementById('timelineModalModule').textContent = module.charAt(0).toUpperCase() + module.slice(1);
		
		// Priority 4-6: Parse and organize details
		const changesContainer = document.getElementById('timelineModalChangesContainer');
		const keyInfoContainer = document.getElementById('timelineModalKeyInfoContainer');
		const relatedContainer = document.getElementById('timelineModalRelatedContainer');
		
		// Clear containers
		changesContainer.innerHTML = '';
		keyInfoContainer.innerHTML = '';
		relatedContainer.innerHTML = '';
		
		// Show/hide sections
		const changesSection = document.getElementById('timelineModalChangesSection');
		const keyInfoSection = document.getElementById('timelineModalKeyInfoSection');
		const relatedSection = document.getElementById('timelineModalRelatedSection');
		const noDetails = document.getElementById('timelineModalNoDetails');
		
		// Categorize keys
		const changeKeys = ['status', 'cost', 'price', 'amount', 'quantity', 'stock', 'name', 'title', 'description'];
		const relatedKeys = ['batch_id', 'purchase_id', 'request_id', 'delivery_id', 'batch id', 'purchase id', 'request id', 'delivery id'];
		const excludedKeys = ['purchase id', 'item id', 'ingredients id', 'ingredient id', 'purchase_id', 'item_id', 'ingredients_id', 'ingredient_id', 'user_id', 'created_at', 'updated_at', 'timestamp'];
		
		if (sentence) {
			// Parse sentence format: "Key1 is Value1, Key2 is Value2."
			const parts = sentence.split(',').map(part => part.trim()).filter(part => part);
			let hasChanges = false;
			let hasKeyInfo = false;
			let hasRelated = false;
			
			parts.forEach(part => {
				// Remove trailing period if present
				part = part.replace(/\.$/, '');
				// Split by " is " to get key and value
				const match = part.match(/^(.+?)\s+is\s+(.+)$/);
				if (match) {
					const key = match[1].trim();
					const keyLower = key.toLowerCase();
					const value = match[2].trim();
					
					// Skip excluded keys
					if (excludedKeys.includes(keyLower)) {
						return;
					}
					
					// Check if this is an update (has "→" or "to" indicating change)
					const isUpdate = value.includes('→') || value.includes(' to ') || value.toLowerCase().includes('changed');
					
					// Categorize by priority
					if (isUpdate || changeKeys.some(ck => keyLower.includes(ck))) {
						// Priority 4: Changes Made
						hasChanges = true;
						const changeDiv = document.createElement('div');
						changeDiv.className = 'flex items-start justify-between gap-3 py-1.5';
						
						if (isUpdate) {
							// Show before/after
							const parts = value.split(/→| to /).map(p => p.trim());
							if (parts.length >= 2) {
								changeDiv.innerHTML = `
									<span class="text-xs font-medium text-gray-700 flex-1">${escapeHtml(key)}:</span>
									<div class="flex items-center gap-2 text-right">
										<span class="text-xs text-gray-500 line-through">${escapeHtml(parts[0])}</span>
										<i data-lucide="arrow-right" class="w-3 h-3 text-blue-600"></i>
										<span class="text-xs font-semibold text-blue-700">${escapeHtml(parts[1])}</span>
									</div>
								`;
							} else {
								changeDiv.innerHTML = `
									<span class="text-xs font-medium text-gray-700">${escapeHtml(key)}:</span>
									<span class="text-xs font-semibold text-blue-700">${escapeHtml(value)}</span>
								`;
							}
						} else {
							changeDiv.innerHTML = `
								<span class="text-xs font-medium text-gray-700 flex-1">${escapeHtml(key)}:</span>
								<span class="text-xs font-semibold text-blue-700">${escapeHtml(value)}</span>
							`;
						}
						changesContainer.appendChild(changeDiv);
					} else if (relatedKeys.some(rk => keyLower.includes(rk))) {
						// Priority 6: Related Information
						hasRelated = true;
						const relatedDiv = document.createElement('div');
						relatedDiv.className = 'flex items-center justify-between py-1';
						relatedDiv.innerHTML = `
							<span class="text-xs font-medium text-gray-600">${escapeHtml(key)}:</span>
							<span class="text-xs font-semibold text-gray-900">${escapeHtml(value)}</span>
						`;
						relatedContainer.appendChild(relatedDiv);
					} else {
						// Priority 5: Key Information
						hasKeyInfo = true;
						const keyDiv = document.createElement('div');
						keyDiv.className = 'flex items-center justify-between py-1';
						keyDiv.innerHTML = `
							<span class="text-xs font-medium text-gray-600">${escapeHtml(key)}:</span>
							<span class="text-xs font-semibold text-gray-900">${escapeHtml(value)}</span>
						`;
						keyInfoContainer.appendChild(keyDiv);
					}
				} else {
					// Fallback: if parsing fails, show as plain text in key info
					hasKeyInfo = true;
					const keyDiv = document.createElement('div');
					keyDiv.className = 'py-1';
					keyDiv.innerHTML = `<p class="text-xs text-gray-900">${escapeHtml(part)}</p>`;
					keyInfoContainer.appendChild(keyDiv);
				}
			});
			
			// Show/hide sections
			changesSection.classList.toggle('hidden', !hasChanges);
			keyInfoSection.classList.toggle('hidden', !hasKeyInfo);
			relatedSection.classList.toggle('hidden', !hasRelated);
			noDetails.classList.add('hidden');
		} else {
			// No details available
			changesSection.classList.add('hidden');
			keyInfoSection.classList.add('hidden');
			relatedSection.classList.add('hidden');
			noDetails.classList.remove('hidden');
		}
		
		// Show modal
		if (timelineModal) {
			timelineModal.classList.remove('hidden');
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
		}
	}
	
	function closeTimelineModalFunc() {
		if (timelineModal) {
			timelineModal.classList.add('hidden');
		}
	}
	
	// Open modal handlers
	openTimelineButtons.forEach(btn => {
		btn.addEventListener('click', (e) => {
			e.stopPropagation(); // Prevent row click
			openTimelineModal(btn);
		});
	});
	
	// Row click handlers
	timelineRows.forEach(row => {
		row.addEventListener('click', (e) => {
			// Don't open if clicking on a button
			if (e.target.closest('button')) {
				return;
			}
			const sentence = row.getAttribute('data-sentence');
			if (sentence && sentence.trim() !== '') {
				openTimelineModal(row);
			}
		});
	});
	
	// Close modal handlers
	if (closeTimelineModal) {
		closeTimelineModal.addEventListener('click', closeTimelineModalFunc);
	}
	if (closeTimelineModalBtn) {
		closeTimelineModalBtn.addEventListener('click', closeTimelineModalFunc);
	}
	
	// Close on backdrop click
	if (timelineModal) {
		timelineModal.addEventListener('click', (e) => {
			if (e.target.classList.contains('bg-black')) {
				closeTimelineModalFunc();
			}
		});
	}
	
	// Escape HTML helper
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
})();

document.getElementById('clearLogsForm')?.addEventListener('submit', async function(e){
	e.preventDefault();
	const scope = this.querySelector('select[name="scope"]')?.value || 'filtered';
	const message = scope === 'all'
		? 'This will delete every audit log in the system. This action cannot be undone.'
		: 'Delete the logs that match your current filters? This action cannot be undone.';
	const confirmed = await Confirm.show(message, 'Clear Audit Logs', 'Clear Logs', 'Cancel', 'danger');
	if (confirmed) {
		this.submit();
	}
});
</script>


