<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
		<p class="text-gray-600 mt-1">Track and monitor system activities</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
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
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
	<!-- Total Logs -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Logs</p>
				<p class="text-2xl font-bold text-gray-900"><?php echo $totalLogs; ?></p>
			</div>
			<div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
				<i data-lucide="file-text" class="w-6 h-6 text-gray-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Today's Logs -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Today's Activity</p>
				<p class="text-2xl font-bold text-blue-600"><?php echo $todayLogs; ?></p>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
				<i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Active Users -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Active Users</p>
				<p class="text-2xl font-bold text-green-600"><?php echo $uniqueUsers; ?></p>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
				<i data-lucide="users" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Modules Tracked -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Modules Tracked</p>
				<p class="text-2xl font-bold text-purple-600"><?php echo $uniqueModules; ?></p>
			</div>
			<div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
				<i data-lucide="layers" class="w-6 h-6 text-purple-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-indigo-50 to-slate-50 px-4 sm:px-6 py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="activity" class="w-5 h-5 text-indigo-600"></i>
				Recent Activity Timeline
			</h2>
			<p class="text-sm text-gray-600 mt-1">Latest <?php echo count($timeline); ?> actions across all modules.</p>
		</div>
		<span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200">
			<i data-lucide="clock-3" class="w-3 h-3"></i>
			Updated <?php echo htmlspecialchars(date('M j, Y g:i A')); ?>
		</span>
	</div>
	<ol class="relative border-l border-gray-200 p-6 space-y-6">
		<?php foreach ($timeline as $entry): 
			$action = strtolower((string)$entry['action']);
			$timelineColor = match(true) {
				in_array($action, ['delete','remove']) => 'bg-red-500',
				in_array($action, ['update','edit','modify']) => 'bg-amber-500',
				in_array($action, ['create','add','insert']) => 'bg-green-500',
				default => 'bg-blue-500',
			};
			$sentence = $formatDetailSentence($entry['details'] ?? '');
		?>
		<li class="pl-6">
			<span class="absolute -left-1.5 mt-1 w-3 h-3 rounded-full <?php echo $timelineColor; ?>"></span>
			<div class="flex flex-col gap-1">
				<div class="flex flex-wrap items-center justify-between gap-2">
					<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($entry['action']); ?> on <?php echo htmlspecialchars($entry['module']); ?></p>
					<span class="text-xs text-gray-500 font-mono"><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime((string)$entry['timestamp']))); ?></span>
				</div>
				<p class="text-sm text-gray-600">By <?php echo htmlspecialchars($entry['user_name'] ?? (string)($entry['user_id'] ?? 'System')); ?></p>
				<?php if (!empty($sentence)): ?>
				<p class="text-xs text-gray-500">Details: <?php echo htmlspecialchars($sentence); ?></p>
				<?php endif; ?>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
</div>
<?php endif; ?>

<!-- Filters Section -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-slate-50 to-gray-50 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="filter" class="w-5 h-5 text-slate-600"></i>
			Audit Filters
		</h2>
		<p class="text-sm text-gray-600 mt-1">Filter audit logs by user, module, and date range</p>
	</div>
	
	<form method="get" class="p-4 sm:p-6 space-y-6">
		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-6">
			<!-- User -->
			<div class="space-y-2 xl:col-span-2">
				<label class="block text-sm font-medium text-gray-700">User</label>
				<select name="user_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
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
				<select name="module" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
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
				<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors" />
			</div>
			
			<!-- Date To -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">To Date</label>
				<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors" />
			</div>
			
			<!-- Keyword -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Keyword</label>
				<input type="text" name="q" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors" placeholder="Search action or details" />
			</div>
			
			<!-- Limit -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Show entries</label>
				<select name="limit" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
					<?php foreach ([50,100,200,500] as $lim): ?>
						<option value="<?php echo $lim; ?>" <?php echo ((int)($filters['limit'] ?? 200) === $lim) ? 'selected' : ''; ?>><?php echo $lim; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="flex flex-wrap items-center gap-3">
			<button type="submit" class="inline-flex items-center justify-center gap-2 bg-slate-600 text-white px-5 py-3 rounded-lg hover:bg-slate-700 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="search" class="w-4 h-4"></i>
				Apply Filters
			</button>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/audit" class="inline-flex items-center gap-2 px-4 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
				<i data-lucide="rotate-ccw" class="w-4 h-4"></i>
				Reset
			</a>
		</div>
	</form>
</div>

<!-- Audit Logs Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="shield-check" class="w-5 h-5 text-gray-600"></i>
					Audit Logs
				</h2>
				<p class="text-sm text-gray-600 mt-1">System activity and user action tracking</p>
			</div>
			<div class="flex items-center gap-4">
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($logs); ?></span> total entries
				</div>
				<?php if (isset($todayLogs) && $todayLogs > 0): ?>
					<div class="flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
						<i data-lucide="activity" class="w-4 h-4"></i>
						<?php echo $todayLogs; ?> today
					</div>
				<?php endif; ?>
				<?php if ($limitReached): ?>
					<div class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-full px-3 py-1">
						Showing first <?php echo count($logs); ?> results. Refine filters for more.
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
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
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
								<span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($log['user_name'] ?? 'U', 0, 2)); ?></span>
							</div>
							<span class="font-medium text-gray-900"><?php echo htmlspecialchars($log['user_name'] ?? (string)($log['user_id'] ?? '')); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
							<i data-lucide="layers" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($log['module']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$actionClass = match(strtolower($log['action'])) {
							'create', 'add', 'insert' => 'bg-green-100 text-green-800',
							'update', 'edit', 'modify' => 'bg-yellow-100 text-yellow-800',
							'delete', 'remove' => 'bg-red-100 text-red-800',
							'login', 'logout' => 'bg-blue-100 text-blue-800',
							default => 'bg-gray-100 text-gray-800'
						};
						$actionIcon = match(strtolower($log['action'])) {
							'create', 'add', 'insert' => 'plus',
							'update', 'edit', 'modify' => 'edit',
							'delete', 'remove' => 'trash-2',
							'login', 'logout' => 'log-in',
							default => 'activity'
						};
						?>
						<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium <?php echo $actionClass; ?>">
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
									<button type="button" class="detailToggle inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800" data-target="<?php echo $detailsBlockId; ?>">
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
</script>


