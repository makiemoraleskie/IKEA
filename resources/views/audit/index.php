<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
		<p class="text-gray-600 mt-1">Track and monitor system activities</p>
	</div>
	<a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<!-- Summary Cards -->
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

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="filter" class="w-5 h-5 text-slate-600"></i>
			Audit Filters
		</h2>
		<p class="text-sm text-gray-600 mt-1">Filter audit logs by user, module, and date range</p>
	</div>
	
	<form method="get" class="p-6">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
			<!-- User ID -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">User ID</label>
				<input type="number" name="user_id" value="<?php echo htmlspecialchars((string)($filters['user_id'] ?? '')); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors" placeholder="Filter by user ID" />
			</div>
			
			<!-- Module -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Module</label>
				<input name="module" value="<?php echo htmlspecialchars($filters['module'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors" placeholder="Filter by module" />
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
			
			<!-- Apply Button -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Actions</label>
				<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-slate-600 text-white px-4 py-3 rounded-lg hover:bg-slate-700 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="search" class="w-4 h-4"></i>
					Apply Filters
				</button>
			</div>
		</div>
	</form>
</div>

<!-- Audit Logs Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
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
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
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
						<?php if (!empty($log['details'])): ?>
							<div class="max-w-xs">
								<button type="button" onclick="toggleDetails('details-<?php echo (int)$log['id']; ?>')" class="text-left text-xs text-gray-600 hover:text-gray-800 transition-colors">
									<span class="inline-flex items-center gap-1">
										<i data-lucide="eye" class="w-3 h-3"></i>
										View Details
									</span>
								</button>
								<div id="details-<?php echo (int)$log['id']; ?>" class="hidden mt-2 p-2 bg-gray-50 rounded text-xs font-mono whitespace-pre-wrap border"><?php echo htmlspecialchars($log['details']); ?></div>
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
function toggleDetails(id) {
	const element = document.getElementById(id);
	if (element) {
		element.classList.toggle('hidden');
	}
}
</script>


