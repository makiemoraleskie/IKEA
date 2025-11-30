<!-- Welcome Banner -->
<div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

$chip = function (string $state, bool $active): string {
	$map = [
		'alert' => ['bg-red-100 text-red-800', 'bg-slate-200 text-slate-700'],
		'warn' => ['bg-amber-100 text-amber-800', 'bg-slate-200 text-slate-700'],
		'info' => ['bg-blue-100 text-blue-800', 'bg-slate-200 text-slate-700'],
		'success' => ['bg-green-100 text-green-800', 'bg-slate-200 text-slate-700'],
		'violet' => ['bg-purple-100 text-purple-800', 'bg-slate-200 text-slate-700'],
		'rose' => ['bg-rose-100 text-rose-800', 'bg-slate-200 text-slate-700'],
	];
	$classes = $active ? ($map[$state][0] ?? $map['info'][0]) : ($map[$state][1] ?? $map['info'][1]);
	$text = $active ? 'Action needed' : 'On track';
	return "<span class=\"inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full {$classes}\">{$text}</span>";
};
$cardCopy = [
	'lowStock' => $lowStock ? 'ingredients below their reorder point' : 'No ingredients at critical levels',
	'pendingRequests' => $pendingRequests ? 'requests awaiting approval' : 'No outstanding approvals',
	'pendingPayments' => $pendingPayments ? 'purchase batches awaiting settlement' : 'All payments cleared',
	'partialDeliveries' => $partialDeliveries ? 'deliveries that still need follow-up' : 'No partial drops to chase',
	'pendingDeliveries' => $pendingDeliveries ? 'purchase batches not yet received' : 'Nothing waiting for dispatch',
];
?>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
	<!-- Low Stock Items -->
	<?php if (in_array('low_stock', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative <?php echo $lowStock ? 'border-red-300 ring-2 ring-red-200 bg-red-50' : 'border-gray-200'; ?>">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
			<div class="flex-1">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-sm font-medium text-gray-600">Low Stock Items</h3>
					<?php echo $chip('alert', $lowStock > 0); ?>
				</div>
				<div class="text-4xl font-black tracking-tight <?php echo $lowStock ? 'text-red-700 animate-pulse' : 'text-gray-800'; ?>"><?php echo $lowStock; ?></div>
				<p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($cardCopy['lowStock']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory?focus=low-stock#inventory-low-stock" class="text-sm font-semibold <?php echo $lowStock ? 'text-red-700 hover:text-red-800' : 'text-blue-600 hover:text-blue-700'; ?>">Review inventory →</a>
			</div>
			<div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
				<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Requests -->
	<?php if (in_array('pending_requests', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative <?php echo $pendingRequests ? 'border-amber-300 ring-2 ring-amber-200 bg-amber-50' : 'border-gray-200'; ?>">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
			<div class="flex-1">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-sm font-medium text-gray-600">Pending Requests</h3>
					<?php echo $chip('warn', $pendingRequests > 0); ?>
				</div>
				<div class="text-4xl font-black tracking-tight <?php echo $pendingRequests ? 'text-amber-700 animate-pulse' : 'text-gray-800'; ?>"><?php echo $pendingRequests; ?></div>
				<p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($cardCopy['pendingRequests']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/requests?status=pending#requests-history" class="text-sm font-semibold <?php echo $pendingRequests ? 'text-amber-700 hover:text-amber-800' : 'text-blue-600 hover:text-blue-700'; ?>">Go to approval queue →</a>
			</div>
			<div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center">
				<i data-lucide="inbox" class="w-6 h-6 text-amber-600"></i>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Payments -->
	<?php if (in_array('pending_payments', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative <?php echo $pendingPayments ? 'border-rose-300 ring-2 ring-rose-200 bg-rose-50' : 'border-gray-200'; ?>">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
			<div class="flex-1">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-sm font-medium text-gray-600">Pending Payments</h3>
					<?php echo $chip('rose', $pendingPayments > 0); ?>
				</div>
				<div class="text-4xl font-black tracking-tight <?php echo $pendingPayments ? 'text-rose-700 animate-pulse' : 'text-gray-800'; ?>"><?php echo $pendingPayments; ?></div>
				<p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($cardCopy['pendingPayments']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/purchases?payment=Pending#recent-purchases" class="text-sm font-semibold <?php echo $pendingPayments ? 'text-rose-700 hover:text-rose-800' : 'text-blue-600 hover:text-blue-700'; ?>">Review payments →</a>
			</div>
			<div class="w-14 h-14 bg-rose-100 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="credit-card" class="w-6 h-6 text-rose-600"></i>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Partial Deliveries -->
	<?php if (in_array('partial_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative <?php echo $partialDeliveries ? 'border-purple-300 ring-2 ring-purple-200 bg-purple-50' : 'border-gray-200'; ?>">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
			<div class="flex-1">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-sm font-medium text-gray-600">Partial Deliveries</h3>
					<?php echo $chip('violet', $partialDeliveries > 0); ?>
				</div>
				<div class="text-4xl font-black tracking-tight <?php echo $partialDeliveries ? 'text-purple-700 animate-pulse' : 'text-gray-800'; ?>"><?php echo $partialDeliveries; ?></div>
				<p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($cardCopy['partialDeliveries']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=partial#recent-deliveries" class="text-sm font-semibold <?php echo $partialDeliveries ? 'text-purple-700 hover:text-purple-800' : 'text-blue-600 hover:text-blue-700'; ?>">Track deliveries →</a>
			</div>
			<div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="truck" class="w-6 h-6 text-purple-600"></i>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Deliveries -->
	<?php if (in_array('pending_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative <?php echo $pendingDeliveries ? 'border-blue-200 ring-2 ring-blue-100 bg-blue-50' : 'border-gray-200'; ?>">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
			<div class="flex-1">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-sm font-medium text-gray-600">Awaiting Deliveries</h3>
					<?php echo $chip('info', $pendingDeliveries > 0); ?>
				</div>
				<div class="text-4xl font-black tracking-tight <?php echo $pendingDeliveries ? 'text-blue-700 animate-pulse' : 'text-gray-800'; ?>"><?php echo $pendingDeliveries; ?></div>
				<p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($cardCopy['pendingDeliveries']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=awaiting#awaiting-deliveries" class="text-sm font-semibold text-blue-700 hover:text-blue-800">Open delivery board →</a>
			</div>
			<div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Inventory Value -->
	<?php if (in_array('inventory_value', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 relative border-gray-200">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
		<div class="flex-1">
			<div class="flex items-center justify-between mb-2">
				<h3 class="text-sm font-medium text-gray-600">Inventory Value</h3>
				<?php echo $chip('success', false); ?>
			</div>
			<?php if ($canViewCosts): ?>
				<div class="text-3xl font-black tracking-tight text-gray-800">₱<?php echo number_format($inventoryValue, 2); ?></div>
				<p class="text-sm text-gray-600 mb-3">estimated replacement value</p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory#inventory-low-stock" class="text-sm font-semibold text-green-700 hover:text-green-800">See stock ledger →</a>
			<?php else: ?>
				<div class="text-lg font-semibold text-gray-500">Hidden</div>
				<p class="text-sm text-gray-500 mb-3">Your role cannot view valuation data.</p>
			<?php endif; ?>
		</div>
		<div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center shrink-0">
			<i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
		</div>
	</div>
	<?php endif; ?>
</div>
</div>

<!-- Overview Chart -->
<div class="bg-white rounded-lg shadow-sm border p-6">
	<h2 class="text-lg font-semibold text-gray-800 mb-4">Overview</h2>
	<div class="relative min-h-[18rem] sm:min-h-[20rem] lg:min-h-[22rem]">
		<canvas id="overviewChart" class="h-full w-full"></canvas>
		<div id="chartFallback" class="absolute inset-0 flex items-center justify-center text-sm text-gray-500 <?php echo $chartTotal > 0 ? 'hidden' : ''; ?>">
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


