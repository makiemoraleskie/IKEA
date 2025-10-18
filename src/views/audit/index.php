<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Audit Logs</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<div class="bg-white border rounded p-4 mb-6">
	<h2 class="text-lg font-semibold mb-3">Filters</h2>
	<form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
		<div>
			<label class="block text-sm mb-1">User ID</label>
			<input type="number" name="user_id" value="<?php echo htmlspecialchars((string)($filters['user_id'] ?? '')); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">Module</label>
			<input name="module" value="<?php echo htmlspecialchars($filters['module'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">From</label>
			<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">To</label>
			<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Apply</button>
		</div>
	</form>
</div>

<div class="bg-white border rounded">
	<div class="p-4 border-b"><h2 class="text-lg font-semibold">Recent Logs</h2></div>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">Time</th>
					<th class="text-left px-4 py-2">User</th>
					<th class="text-left px-4 py-2">Module</th>
					<th class="text-left px-4 py-2">Action</th>
					<th class="text-left px-4 py-2">Details</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($logs as $log): ?>
				<tr class="border-t">
					<td class="px-4 py-2"><?php echo htmlspecialchars($log['timestamp']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($log['user_name'] ?? (string)($log['user_id'] ?? '')); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($log['module']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($log['action']); ?></td>
					<td class="px-4 py-2"><pre class="whitespace-pre-wrap text-xs"><?php echo htmlspecialchars($log['details'] ?? ''); ?></pre></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


