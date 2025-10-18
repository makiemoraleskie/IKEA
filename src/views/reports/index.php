<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Reports</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white border rounded p-4 mb-6">
	<h2 class="text-lg font-semibold mb-3">Filters</h2>
	<form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
		<div>
			<label class="block text-sm mb-1">From</label>
			<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">To</label>
			<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">Supplier</label>
			<input name="supplier" value="<?php echo htmlspecialchars($filters['supplier'] ?? ''); ?>" class="w-full border rounded px-3 py-2" />
		</div>
		<div>
			<label class="block text-sm mb-1">Item</label>
			<select name="item_id" class="w-full border rounded px-3 py-2">
				<option value="">All</option>
				<?php foreach ($ingredients as $ing): ?>
					<option value="<?php echo (int)$ing['id']; ?>" <?php echo ((int)($filters['item_id'] ?? 0) === (int)$ing['id'])?'selected':''; ?>><?php echo htmlspecialchars($ing['name']); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Apply</button>
			<a class="block text-center text-sm text-blue-600 mt-2" href="<?php echo htmlspecialchars($baseUrl); ?>/reports/pdf?<?php echo http_build_query($filters); ?>" target="_blank">Export PDF</a>
		</div>
	</form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
	<div class="bg-white border rounded p-4">
		<h2 class="text-lg font-semibold mb-2">Purchases</h2>
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50">
					<tr>
						<th class="text-left px-4 py-2">Date</th>
						<th class="text-left px-4 py-2">Item</th>
						<th class="text-left px-4 py-2">Supplier</th>
						<th class="text-left px-4 py-2">Qty</th>
						<th class="text-left px-4 py-2">Cost</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($purchases as $p): ?>
					<tr class="border-t">
						<td class="px-4 py-2"><?php echo htmlspecialchars($p['date_purchased']); ?></td>
						<td class="px-4 py-2"><?php echo htmlspecialchars($p['item_name']); ?></td>
						<td class="px-4 py-2"><?php echo htmlspecialchars($p['supplier']); ?></td>
						<td class="px-4 py-2"><?php echo htmlspecialchars($p['quantity']); ?></td>
						<td class="px-4 py-2">₱<?php echo number_format((float)$p['cost'], 2); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="bg-white border rounded p-4">
		<h2 class="text-lg font-semibold mb-2">Daily Spend</h2>
		<canvas id="dailyChart" height="120"></canvas>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const daily = <?php echo json_encode($daily, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
const labels = daily.map(x => x.d);
const data = daily.map(x => Number(x.total));
const ctx2 = document.getElementById('dailyChart');
if (ctx2) {
	new Chart(ctx2, {
		type: 'line',
		data: { labels, datasets: [{ label: 'Total Cost', data, fill: false, borderColor: 'rgb(59,130,246)', tension: 0.2 }] },
		options: { responsive: true, maintainAspectRatio: false }
	});
}
</script>


