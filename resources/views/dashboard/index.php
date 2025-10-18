<!-- Welcome Banner -->
<div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
	<div class="flex items-center justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($user['name'] ?? 'User'); ?>!</h1>
			<p class="text-gray-600">
				You are logged in as <span class="text-blue-600 font-medium"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></span>
			</p>
			<p class="text-sm text-gray-500 mt-1">You have full access to all system features and data.</p>
		</div>
		<div class="text-blue-600 font-semibold text-lg">
			<?php echo htmlspecialchars($user['role'] ?? 'User'); ?>
		</div>
	</div>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
	<!-- Low Stock Items -->
	<div class="bg-white rounded-lg shadow-sm border p-6 relative">
		<div class="flex items-center justify-between">
			<div class="flex-1">
				<h3 class="text-sm font-medium text-gray-500 mb-1">Low Stock Items</h3>
				<div class="text-3xl font-bold text-gray-800 mb-1"><?php echo (int)($stats['lowStockCount'] ?? 0); ?></div>
				<p class="text-sm text-gray-500 mb-3">items below minimum level</p>
				<a href="/inventory" class="text-blue-600 text-sm font-medium hover:text-blue-700">Details ></a>
			</div>
			<div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
				<i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
			</div>
		</div>
	</div>

	<!-- Pending Requests -->
	<div class="bg-white rounded-lg shadow-sm border p-6 relative">
		<div class="flex items-center justify-between">
			<div class="flex-1">
				<h3 class="text-sm font-medium text-gray-500 mb-1">Pending Requests</h3>
				<div class="text-3xl font-bold text-gray-800 mb-1"><?php echo (int)($stats['pendingRequests'] ?? 0); ?></div>
				<p class="text-sm text-gray-500 mb-3">requests awaiting approval</p>
				<a href="/requests" class="text-blue-600 text-sm font-medium hover:text-blue-700">Details ></a>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-4">
				<i data-lucide="clock" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>

	<!-- Pending Deliveries -->
	<div class="bg-white rounded-lg shadow-sm border p-6 relative">
		<div class="flex items-center justify-between">
			<div class="flex-1">
				<h3 class="text-sm font-medium text-gray-500 mb-1">Pending Deliveries</h3>
				<div class="text-3xl font-bold text-gray-800 mb-1"><?php echo (int)($stats['pendingDeliveries'] ?? 0); ?></div>
				<p class="text-sm text-gray-500 mb-3">expected arrivals</p>
				<a href="/deliveries" class="text-blue-600 text-sm font-medium hover:text-blue-700">Details ></a>
			</div>
			<div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center ml-4">
				<i data-lucide="package" class="w-6 h-6 text-purple-600"></i>
			</div>
		</div>
	</div>

	<!-- Inventory Value -->
	<div class="bg-white rounded-lg shadow-sm border p-6 relative">
		<div class="flex items-center justify-between">
			<div class="flex-1">
				<h3 class="text-sm font-medium text-gray-500 mb-1">Inventory Value</h3>
				<div class="text-3xl font-bold text-gray-800 mb-1">₱<?php echo number_format($stats['inventoryValue'] ?? 0, 2); ?></div>
				<p class="text-sm text-gray-500 mb-3">total current value</p>
				<a href="/inventory" class="text-blue-600 text-sm font-medium hover:text-blue-700">Details ></a>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4">
				<i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
</div>

<!-- Overview Chart -->
<div class="bg-white rounded-lg shadow-sm border p-6">
	<h2 class="text-lg font-semibold text-gray-800 mb-4">Overview</h2>
	<div class="relative" style="height: 300px;">
		<canvas id="overviewChart"></canvas>
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
				backgroundColor: 'rgba(59,130,246,0.6)',
				borderColor: 'rgba(59,130,246,1)',
				borderWidth: 1
			}]
		},
		options: {
			responsive: true, 
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: false
				}
			},
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
}
</script>


