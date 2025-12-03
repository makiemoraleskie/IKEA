<!-- Welcome Banner -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<div>
			<h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
				Welcome back, <span class="text-green-600"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?>!</span>
			</h1>
			<p class="text-xs md:text-sm text-gray-600">Full system access enabled</p>
		</div>
	</div>
</div>

<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$canViewCosts = $canViewCosts ?? true;
$dashboardWidgets = $dashboardWidgets ?? ['low_stock','pending_requests','pending_payments','partial_deliveries','pending_deliveries','inventory_value'];
$lowStock = (int)($stats['lowStockCount'] ?? 0);
$pendingRequests = (int)($stats['pendingRequests'] ?? 0);
$pendingPayments = (int)($stats['pendingPayments'] ?? 0);
$partialDeliveries = (int)($stats['partialDeliveries'] ?? 0);
$pendingDeliveries = (int)($stats['pendingDeliveries'] ?? 0);
$inventoryValue = (float)($stats['inventoryValue'] ?? 0);
$chart = $stats['chart'] ?? ['labels' => [], 'purchases' => [], 'deliveries' => []];
$chartTotal = array_sum($chart['purchases'] ?? []) + array_sum($chart['deliveries'] ?? []);

$cardCopy = [
	'lowStock' => 'No ingredients at critical levels',
	'pendingRequests' => 'No outstanding approvals',
	'pendingPayments' => 'All payments cleared',
	'partialDeliveries' => 'No partial drops to chase',
	'pendingDeliveries' => 'Nothing waiting for dispatch',
];
?>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8">
	<!-- Low Stock Items -->
	<?php if (in_array('low_stock', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<i data-lucide="alert-triangle" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-red-500"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">LOW STOCK ITEMS</h3>
			<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2"><?php echo $lowStock; ?></div>
			<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4"><?php echo htmlspecialchars($cardCopy['lowStock']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory?focus=low-stock#inventory-low-stock" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Review inventory ></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Requests -->
	<?php if (in_array('pending_requests', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<i data-lucide="file-text" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">PENDING REQUESTS</h3>
			<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2"><?php echo $pendingRequests; ?></div>
			<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4"><?php echo htmlspecialchars($cardCopy['pendingRequests']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/requests?status=pending#requests-history" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Go to approval queue ></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Payments -->
	<?php if (in_array('pending_payments', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<i data-lucide="credit-card" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">PENDING PAYMENTS</h3>
			<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2"><?php echo $pendingPayments; ?></div>
			<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4"><?php echo htmlspecialchars($cardCopy['pendingPayments']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/purchases?payment=Pending#recent-purchases" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Review payments ></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Partial Deliveries -->
	<?php if (in_array('partial_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<i data-lucide="truck" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">PARTIAL DELIVERIES</h3>
			<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2"><?php echo $partialDeliveries; ?></div>
			<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4"><?php echo htmlspecialchars($cardCopy['partialDeliveries']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=partial#recent-deliveries" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Track deliveries ></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Deliveries -->
	<?php if (in_array('pending_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<i data-lucide="package" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">AWAITING DELIVERIES</h3>
			<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2"><?php echo $pendingDeliveries; ?></div>
			<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4"><?php echo htmlspecialchars($cardCopy['pendingDeliveries']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=awaiting#awaiting-deliveries" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Open delivery board ></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Inventory Value -->
	<?php if (in_array('inventory_value', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 relative">
		<div class="absolute top-3 md:top-4 right-3 md:right-4">
			<span class="text-xl md:text-2xl font-bold text-green-600">₱</span>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">INVENTORY VALUE</h3>
			<?php if ($canViewCosts): ?>
				<div class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-1.5 md:mb-2">₱<?php echo number_format($inventoryValue, 2); ?></div>
				<p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4">estimated replacement value</p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory#inventory-low-stock" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">See stock ledger ></a>
			<?php else: ?>
				<div class="text-base md:text-lg font-semibold text-gray-500">Hidden</div>
				<p class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3">Your role cannot view valuation data.</p>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>

<!-- Overview Chart -->
<div class="bg-white rounded-lg shadow-sm border p-4 md:p-6">
	<h2 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4">Overview</h2>
	<div class="relative min-h-[8rem] sm:min-h-[10rem] lg:min-h-[12rem]">
		<canvas id="overviewChart" class="h-full w-full"></canvas>
		<div id="chartFallback" class="absolute inset-0 flex items-center justify-center text-xs md:text-sm text-gray-500 <?php echo $chartTotal > 0 ? 'hidden' : ''; ?>">
			Not enough activity to chart yet.
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartCtx = document.getElementById('overviewChart');
const chartFallback = document.getElementById('chartFallback');
const chartData = <?php echo json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
if (chartCtx && Array.isArray(chartData.labels)) {
	const datasets = [
		{
			label: 'Purchases',
			data: chartData.purchases ?? [],
			backgroundColor: 'rgba(59,130,246,0.65)',
			borderColor: 'rgba(59,130,246,1)',
			borderWidth: 1,
			borderRadius: 4,
		},
		{
			label: 'Deliveries',
			data: chartData.deliveries ?? [],
			backgroundColor: 'rgba(16,185,129,0.5)',
			borderColor: 'rgba(16,185,129,0.9)',
			borderWidth: 1,
			borderRadius: 4,
		}
	];
	const hasData = datasets.some(ds => Array.isArray(ds.data) && ds.data.some(v => v > 0));
	if (hasData) {
		if (chartFallback) chartFallback.classList.add('hidden');
		new Chart(chartCtx, {
			type: 'bar',
			data: {
				labels: chartData.labels,
				datasets,
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: true,
						position: 'bottom',
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: { precision:0 }
					}
				}
			}
		});
	} else if (chartFallback) {
		chartFallback.classList.remove('hidden');
	}
}
</script>


