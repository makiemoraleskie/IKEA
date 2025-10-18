<div class="mt-6">
	<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>
	<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
		<div class="bg-white border rounded p-4">
			<div class="text-sm text-gray-500">Low Stock Items</div>
			<div class="text-3xl font-bold"><?php echo (int)($stats['lowStockCount'] ?? 0); ?></div>
		</div>
		<div class="bg-white border rounded p-4">
			<div class="text-sm text-gray-500">Pending Requests</div>
			<div class="text-3xl font-bold"><?php echo (int)($stats['pendingRequests'] ?? 0); ?></div>
		</div>
		<div class="bg-white border rounded p-4">
			<div class="text-sm text-gray-500">Today's Purchases</div>
			<div class="text-3xl font-bold"><?php echo (int)($stats['todayPurchases'] ?? 0); ?></div>
		</div>
	</div>

	<div class="mt-8 bg-white border rounded p-4">
		<h2 class="text-lg font-semibold mb-2">Overview</h2>
		<div class="relative" style="height:280px;">
			<canvas id="overviewChart"></canvas>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('overviewChart');
if (ctx) {
	new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
			datasets: [{
				label: 'Purchases',
				data: [0,0,0,0,0,0,0],
				backgroundColor: 'rgba(59,130,246,0.6)'
			}]
		},
		options: {responsive: true, maintainAspectRatio: false}
	});
}
</script>


