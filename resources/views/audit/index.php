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
</style>
<!-- Page Header -->
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-900 tracking-tight">Audit Logs</h1>
			<p class="text-sm sm:text-base text-gray-600 mt-1 font-medium">Track and monitor system activities across modules</p>
			<div class="mt-2 inline-flex items-center gap-2 rounded-full bg-[#008000]/10 text-[#008000] text-xs font-semibold px-3 py-1 border border-[#008000]/20">
				<i data-lucide="calendar" class="w-3 h-3"></i>
				<span><?php echo htmlspecialchars($activeDateLabel); ?></span>
			</div>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-colors">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
	</div>
</div>

<?php if (!empty($flash)): ?>
	<div class="mb-6 rounded-xl border px-4 py-3 flex items-center gap-3 <?php echo $flash['type'] === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-blue-50 border-blue-200 text-blue-800'; ?>">
		<i data-lucide="<?php echo $flash['type'] === 'success' ? 'check-circle' : 'info'; ?>" class="w-5 h-5"></i>
		<p class="text-sm font-medium"><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
	</div>
<?php endif; ?>

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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8">
	<!-- Total Logs -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Logs</p>
				<p class="text-3xl sm:text-4xl font-black text-gray-900 mt-1"><?php echo $totalLogs; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Today's Logs -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Today's Activity</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $todayLogs; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="calendar" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Active Users -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Active Users</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $uniqueUsers; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="users" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Modules Tracked -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Modules Tracked</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $uniqueModules; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="layers" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 mb-6 sm:mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
				<i data-lucide="activity" class="w-5 h-5 text-[#008000]"></i>
			</div>
			<div>
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Recent Activity Timeline</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Latest <?php echo count($timeline); ?> actions across all modules.</p>
			</div>
		</div>
		<div class="flex flex-wrap items-center gap-2">
			<span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-[#008000]/10 text-[#008000] border border-[#008000]/20">
				<i data-lucide="clock-3" class="w-3 h-3"></i>
				Updated <?php echo htmlspecialchars(date('M j, Y g:i A')); ?>
			</span>
			<div class="text-xs text-gray-600 bg-white/80 border-2 border-gray-200 rounded-full px-3 py-1 inline-flex items-center gap-2">
				<i data-lucide="calendar" class="w-3 h-3"></i>
				<?php echo htmlspecialchars($activeDateLabel); ?>
			</div>
		</div>
	</div>
	<div class="overflow-x-auto">
		<div class="max-h-[26rem] overflow-y-auto custom-scroll">
			<table class="w-full text-sm min-w-[600px]">
				<thead class="bg-gray-50 sticky top-0">
					<tr>
						<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Action</th>
						<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Module</th>
						<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">User</th>
						<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Timestamp</th>
						<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Details</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200">
					<?php foreach ($timeline as $entry): 
						$action = strtolower((string)$entry['action']);
						$actionClass = match(true) {
							in_array($action, ['delete','remove']) => 'text-red-800',
							in_array($action, ['update','edit','modify']) => 'text-amber-800',
							in_array($action, ['create','add','insert']) => 'text-[#008000]',
							default => 'text-blue-800',
						};
						$actionIcon = match(true) {
							in_array($action, ['create','add','insert']) => 'plus',
							in_array($action, ['update','edit','modify']) => 'edit',
							in_array($action, ['delete','remove']) => 'trash-2',
							default => 'activity',
						};
						$sentence = $formatDetailSentence($entry['details'] ?? '');
					?>
					<tr class="hover:bg-gray-50 transition-colors">
						<td class="px-4 sm:px-6 py-4">
							<span class="inline-flex items-center gap-1 text-xs font-semibold <?php echo $actionClass; ?>">
								<i data-lucide="<?php echo $actionIcon; ?>" class="w-3 h-3"></i>
								<?php echo htmlspecialchars($entry['action']); ?>
							</span>
						</td>
						<td class="px-4 sm:px-6 py-4">
							<span class="text-gray-900 text-xs font-semibold">
								<?php echo htmlspecialchars($entry['module']); ?>
							</span>
						</td>
						<td class="px-4 sm:px-6 py-4">
							<span class="font-medium text-gray-900 text-xs sm:text-sm"><?php echo htmlspecialchars($entry['user_name'] ?? (string)($entry['user_id'] ?? 'System')); ?></span>
						</td>
						<td class="px-4 sm:px-6 py-4">
							<div class="flex items-center gap-2">
								<i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
								<span class="text-gray-600 font-mono text-xs"><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime((string)$entry['timestamp']))); ?></span>
							</div>
						</td>
						<td class="px-4 sm:px-6 py-4">
							<?php if (!empty($sentence)): ?>
								<p class="text-xs text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($sentence); ?>"><?php echo htmlspecialchars($sentence); ?></p>
							<?php else: ?>
								<span class="text-gray-400 text-xs">No details</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 mb-6 sm:mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
				<i data-lucide="filter" class="w-5 h-5 text-[#008000]"></i>
			</div>
			<div>
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Audit Filters</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Filter audit logs by user, module, and date range</p>
			</div>
		</div>
	</div>
	
	<form method="get" class="p-4 sm:p-6 space-y-6">
		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-6">
			<!-- User -->
			<div class="space-y-2 xl:col-span-2">
				<label class="block text-sm font-medium text-gray-700">User</label>
				<select name="user_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
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
				<select name="module" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
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
				<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" />
			</div>
			
			<!-- Date To -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">To Date</label>
				<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" />
			</div>
			
			<!-- Keyword -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Keyword</label>
				<input type="text" name="q" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" placeholder="Search action or details" />
			</div>
			
			<!-- Limit -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Show entries</label>
				<select name="limit" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
					<?php foreach ([50,100,200,500] as $lim): ?>
						<option value="<?php echo $lim; ?>" <?php echo ((int)($filters['limit'] ?? 200) === $lim) ? 'selected' : ''; ?>><?php echo $lim; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="flex flex-wrap items-center gap-3">
			<button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#008000] via-[#00A86B] to-[#008000] text-white px-5 py-3 rounded-xl hover:shadow-lg hover:shadow-[#008000]/30 focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold">
				<i data-lucide="search" class="w-4 h-4"></i>
				Apply Filters
			</button>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/audit" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
				<i data-lucide="rotate-ccw" class="w-4 h-4"></i>
				Reset
			</a>
		</div>
	</form>
	<div class="border-t border-gray-100 px-4 sm:px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-50/60">
		<div class="text-sm text-gray-600">
			Clear logs <span class="font-semibold">by date range or entire history</span>. Only Owners and Managers can perform this action.
		</div>
		<form id="clearLogsForm" method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/audit/clear" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="return_query" value="<?php echo htmlspecialchars($currentQuery); ?>">
			<?php foreach (['user_id','module','date_from','date_to','search','limit'] as $key): ?>
				<input type="hidden" name="current[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($filters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<select name="scope" class="border-2 border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#008000] focus:border-[#008000]">
				<option value="filtered">Only logs matching current filters</option>
				<option value="all">All audit logs</option>
			</select>
			<button type="submit" class="inline-flex items-center justify-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm transition-colors">
				<i data-lucide="trash-2" class="w-4 h-4"></i>
				Clear Logs
			</button>
		</form>
	</div>
</div>

<!-- Audit Logs Table -->
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
					<i data-lucide="shield-check" class="w-5 h-5 text-[#008000]"></i>
				</div>
				<div>
					<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Audit Logs</h2>
					<p class="text-xs sm:text-sm text-gray-600 mt-0.5">System activity and user action tracking</p>
				</div>
			</div>
			<div class="flex flex-wrap items-center gap-3">
				<div class="text-xs sm:text-sm text-gray-600">
					<span class="font-semibold"><?php echo count($logs); ?></span> total entries
				</div>
				<?php if (isset($todayLogs) && $todayLogs > 0): ?>
					<div class="flex items-center gap-2 px-3 py-1 bg-[#008000]/10 text-[#008000] rounded-full text-xs sm:text-sm font-semibold border border-[#008000]/20">
						<i data-lucide="activity" class="w-4 h-4"></i>
						<?php echo $todayLogs; ?> today
					</div>
				<?php endif; ?>
				<?php if ($limitReached): ?>
					<div class="text-xs text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-full px-3 py-1">
						Showing first <?php echo count($logs); ?> results. Refine filters for more.
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<div class="max-h-[32rem] overflow-y-auto custom-scroll">
		<table class="w-full text-sm min-w-[720px]">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Timestamp</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">User</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Module</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Action</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Details</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($logs as $log): ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
							<span class="text-gray-600 font-mono text-xs"><?php echo htmlspecialchars($log['timestamp']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<span class="font-medium text-gray-900"><?php echo htmlspecialchars($log['user_name'] ?? (string)($log['user_id'] ?? '')); ?></span>
					</td>
					
					<td class="px-6 py-4">
						<span class="text-gray-900 text-xs font-semibold">
							<?php echo htmlspecialchars($log['module']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$actionClass = match(strtolower($log['action'])) {
							'create', 'add', 'insert' => 'text-green-800',
							'update', 'edit', 'modify' => 'text-yellow-800',
							'delete', 'remove' => 'text-red-800',
							'login', 'logout' => 'text-blue-800',
							default => 'text-gray-800'
						};
						$actionIcon = match(strtolower($log['action'])) {
							'create', 'add', 'insert' => 'plus',
							'update', 'edit', 'modify' => 'edit',
							'delete', 'remove' => 'trash-2',
							'login', 'logout' => 'log-in',
							default => 'activity'
						};
						?>
						<span class="inline-flex items-center gap-1 text-xs font-medium <?php echo $actionClass; ?>">
							<i data-lucide="<?php echo $actionIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($log['action']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<?php
						$detailsBlockId = 'details-' . (int)$log['id'];
						$detailArray = null;
						$detailJsonPretty = null;
						if (!empty($log['details'])) {
							$decoded = json_decode((string)$log['details'], true);
							if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
								$detailArray = $decoded;
								$detailJsonPretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
							} else {
								$detailJsonPretty = trim((string)$log['details']);
							}
						}
						$sentenceDetails = $formatDetailSentence($log['details'] ?? '');
						?>
						<?php if ($sentenceDetails !== null): ?>
							<div class="max-w-sm space-y-2">
								<p class="text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2"><?php echo htmlspecialchars($sentenceDetails); ?></p>
								<?php if ($detailArray): ?>
									<div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-600 space-y-1">
										<?php
										$shown = 0;
										foreach ($detailArray as $key => $value):
											$shown++;
											if ($shown > 4) { break; }
											$displayValue = is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
										?>
										<div class="flex items-start justify-between gap-3">
											<span class="font-semibold text-gray-700"><?php echo htmlspecialchars((string)$key); ?></span>
											<span class="text-gray-600 text-right break-all"><?php echo htmlspecialchars((string)$displayValue); ?></span>
										</div>
										<?php endforeach; ?>
										<?php if (count($detailArray) > 4): ?>
											<p class="text-[11px] text-gray-500 italic">+<?php echo count($detailArray) - 4; ?> more fields</p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<div class="flex flex-wrap items-center gap-2 text-xs">
									<button type="button" class="detailToggle inline-flex items-center gap-1 text-[#008000] hover:text-[#006a00] font-medium" data-target="<?php echo $detailsBlockId; ?>">
										<i data-lucide="eye" class="w-3 h-3"></i>
										View raw
									</button>
									<button type="button" class="copyDetail inline-flex items-center gap-1 text-gray-600 hover:text-gray-800" data-detail="<?php echo htmlspecialchars($detailJsonPretty, ENT_QUOTES, 'UTF-8'); ?>">
										<i data-lucide="copy" class="w-3 h-3"></i>
										Copy JSON
									</button>
								</div>
								<pre id="<?php echo $detailsBlockId; ?>" class="hidden mt-1 p-2 bg-white border border-gray-200 rounded-lg text-[11px] font-mono whitespace-pre-wrap"><?php echo htmlspecialchars($detailJsonPretty); ?></pre>
							</div>
						<?php else: ?>
							<span class="text-gray-400 text-xs">No details</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>
		
		<?php if (empty($logs)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="shield-off" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Audit Logs Found</h3>
			<p class="text-sm text-gray-600 mb-4">Try adjusting your filters to see audit data</p>
		</div>
		<?php endif; ?>
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
			el.classList.toggle('hidden');
		});
	});

	let toastTimer = null;
	function showDetailToast(message){
		let toast = document.getElementById('auditToast');
		if (!toast){
			toast = document.createElement('div');
			toast.id = 'auditToast';
			toast.className = 'fixed top-6 right-6 z-50 px-4 py-3 rounded-xl bg-slate-900 text-white text-sm shadow-lg opacity-0 pointer-events-none transition';
			document.body.appendChild(toast);
		}
		toast.textContent = message;
		toast.classList.remove('opacity-0');
		toast.classList.add('opacity-100');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(()=>{
			toast.classList.add('opacity-0');
			toast.classList.remove('opacity-100');
		}, 2000);
	}

	const copyButtons = document.querySelectorAll('.copyDetail');
	copyButtons.forEach(btn => {
		btn.addEventListener('click', ()=>{
			const content = btn.getAttribute('data-detail');
			if (!content) return;
			navigator.clipboard.writeText(content).then(()=>{
				showDetailToast('Audit detail copied.');
			}).catch(()=>{
				showDetailToast('Unable to copy. Select and copy manually.');
			});
		});
	});
})();

document.getElementById('clearLogsForm')?.addEventListener('submit', function(e){
	const scope = this.querySelector('select[name="scope"]')?.value || 'filtered';
	const message = scope === 'all'
		? 'This will delete every audit log in the system. Continue?'
		: 'Delete the logs that match your current filters?';
	if (!confirm(message)) {
		e.preventDefault();
	}
});
</script>


