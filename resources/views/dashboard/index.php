<!-- Welcome Banner - Enhanced -->
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	
	<!-- Upper right section - absolutely positioned -->
	<div class="absolute top-3 right-3 sm:top-4 sm:right-4 z-20">
		<div class="flex flex-col items-end gap-1.5">
			<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#008000]/10 text-[#008000] font-bold text-xs border-2 border-[#008000]/20">
				<span class="w-1.5 h-1.5 rounded-full bg-[#008000]"></span>
				<?php echo htmlspecialchars($user['role'] ?? 'User'); ?>
			</span>
			<p class="text-xs text-gray-500 font-medium">Full system access enabled</p>
		</div>
	</div>
	
	<!-- Welcome message -->
	<div class="relative z-10 pr-24 sm:pr-28">
		<h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight leading-tight">Welcome back,<br/><span class="bg-gradient-to-r from-[#008000] to-[#00A86B] bg-clip-text text-transparent"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></span>!</h1>
	</div>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php
$lowStock = (int)($stats['lowStockCount'] ?? 0);
$pendingRequests = (int)($stats['pendingRequests'] ?? 0);
$pendingPayments = (int)($stats['pendingPayments'] ?? 0);
$partialDeliveries = (int)($stats['partialDeliveries'] ?? 0);
$pendingDeliveries = (int)($stats['pendingDeliveries'] ?? 0);
$inventoryValue = (float)($stats['inventoryValue'] ?? 0);
$chart = $stats['chart'] ?? ['labels' => [], 'purchases' => [], 'deliveries' => []];
$chartTotal = array_sum($chart['purchases'] ?? []) + array_sum($chart['deliveries'] ?? []);

$chip = function (string $state, bool $active): string {
	if (!$active) return ''; // Don't show chip when inactive
	$map = [
		'alert' => 'bg-red-50 text-red-800 border-red-200',
		'warn' => 'bg-red-50 text-red-800 border-red-200',
		'info' => 'bg-green-50 text-green-800 border-green-200',
		'success' => 'bg-green-50 text-green-800 border-green-200',
		'violet' => 'bg-red-50 text-red-800 border-red-200',
		'rose' => 'bg-red-50 text-red-800 border-red-200',
	];
	$classes = $map[$state] ?? $map['info'];
	return "<span class=\"inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full border {$classes}\">Action needed</span>";
};
$cardCopy = [
	'lowStock' => $lowStock ? 'ingredients below their reorder point' : 'No ingredients at critical levels',
	'pendingRequests' => $pendingRequests ? 'requests awaiting approval' : 'No outstanding approvals',
	'pendingPayments' => $pendingPayments ? 'purchase batches awaiting settlement' : 'All payments cleared',
	'partialDeliveries' => $partialDeliveries ? 'deliveries that still need follow-up' : 'No partial drops to chase',
	'pendingDeliveries' => $pendingDeliveries ? 'purchase batches not yet received' : 'Nothing waiting for dispatch',
];
?>

<!-- Dashboard Cards - Enhanced -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mb-8">
	<!-- Low Stock Items -->
	<div class="group bg-white rounded-xl shadow-md border-2 relative overflow-hidden <?php echo $lowStock ? 'border-red-300/80 ring-2 ring-red-200/50 bg-red-50/50' : 'border-gray-200/80'; ?>">
		<div class="p-4 sm:p-5 relative z-10">
		<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-red-50 rounded-lg flex items-center justify-center border border-red-200 z-20">
			<i data-lucide="alert-triangle" class="w-4 h-4 sm:w-5 sm:h-5 text-red-600"></i>
		</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Low Stock Items</h3>
					<?php echo $chip('alert', $lowStock > 0); ?>
				</div>
				<div class="text-3xl sm:text-4xl font-black tracking-tight mb-2 <?php echo $lowStock ? 'text-red-700' : 'text-gray-800'; ?>"><?php echo $lowStock; ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed"><?php echo htmlspecialchars($cardCopy['lowStock']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory?focus=low-stock#inventory-low-stock" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold <?php echo $lowStock ? 'text-red-700 hover:text-red-800' : 'text-[#008000] hover:text-[#006a00]'; ?>">
					Review inventory
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Pending Requests -->
	<div class="group bg-white rounded-xl shadow-md border-2 relative overflow-hidden <?php echo $pendingRequests ? 'border-red-300/80 ring-2 ring-red-200/50 bg-red-50/50' : 'border-gray-200/80'; ?>">
		<div class="p-4 sm:p-5 relative z-10">
			<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-lg flex items-center justify-center border border-green-200 z-20">
				<i data-lucide="inbox" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i>
			</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Pending Requests</h3>
					<?php echo $chip('warn', $pendingRequests > 0); ?>
				</div>
				<div class="text-3xl sm:text-4xl font-black tracking-tight mb-2 <?php echo $pendingRequests ? 'text-red-700' : 'text-gray-800'; ?>"><?php echo $pendingRequests; ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed"><?php echo htmlspecialchars($cardCopy['pendingRequests']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/requests?status=pending#requests-history" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold <?php echo $pendingRequests ? 'text-red-700 hover:text-red-800' : 'text-[#008000] hover:text-[#006a00]'; ?>">
					Go to approval queue
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Pending Payments -->
	<div class="group bg-white rounded-xl shadow-md border-2 relative overflow-hidden <?php echo $pendingPayments ? 'border-red-300/80 ring-2 ring-red-200/50 bg-red-50/50' : 'border-gray-200/80'; ?>">
		<div class="p-4 sm:p-5 relative z-10">
			<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-lg flex items-center justify-center border border-green-200 z-20">
				<i data-lucide="credit-card" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i>
			</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Pending Payments</h3>
					<?php echo $chip('rose', $pendingPayments > 0); ?>
				</div>
				<div class="text-3xl sm:text-4xl font-black tracking-tight mb-2 <?php echo $pendingPayments ? 'text-red-700' : 'text-gray-800'; ?>"><?php echo $pendingPayments; ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed"><?php echo htmlspecialchars($cardCopy['pendingPayments']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/purchases?payment=Pending#recent-purchases" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold <?php echo $pendingPayments ? 'text-red-700 hover:text-red-800' : 'text-[#008000] hover:text-[#006a00]'; ?>">
					Review payments
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Partial Deliveries -->
	<div class="group bg-white rounded-xl shadow-md border-2 relative overflow-hidden <?php echo $partialDeliveries ? 'border-red-300/80 ring-2 ring-red-200/50 bg-red-50/50' : 'border-gray-200/80'; ?>">
		<div class="p-4 sm:p-5 relative z-10">
			<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-lg flex items-center justify-center border border-green-200 z-20">
				<i data-lucide="truck" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i>
			</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Partial Deliveries</h3>
					<?php echo $chip('violet', $partialDeliveries > 0); ?>
				</div>
				<div class="text-3xl sm:text-4xl font-black tracking-tight mb-2 <?php echo $partialDeliveries ? 'text-red-700' : 'text-gray-800'; ?>"><?php echo $partialDeliveries; ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed"><?php echo htmlspecialchars($cardCopy['partialDeliveries']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=partial#recent-deliveries" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold <?php echo $partialDeliveries ? 'text-red-700 hover:text-red-800' : 'text-[#008000] hover:text-[#006a00]'; ?>">
					Track deliveries
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Pending Deliveries -->
	<div class="group bg-white rounded-xl shadow-md border-2 relative overflow-hidden <?php echo $pendingDeliveries ? 'border-green-300/80 ring-2 ring-green-200/50 bg-green-50/50' : 'border-gray-200/80'; ?>">
		<div class="p-4 sm:p-5 relative z-10">
			<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-lg flex items-center justify-center border border-green-200 z-20">
				<i data-lucide="package" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i>
			</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Awaiting Deliveries</h3>
					<?php echo $chip('info', $pendingDeliveries > 0); ?>
				</div>
				<div class="text-3xl sm:text-4xl font-black tracking-tight mb-2 <?php echo $pendingDeliveries ? 'text-green-700' : 'text-gray-800'; ?>"><?php echo $pendingDeliveries; ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed"><?php echo htmlspecialchars($cardCopy['pendingDeliveries']); ?></p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=awaiting#awaiting-deliveries" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#008000] hover:text-[#006a00]">
					Open delivery board
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Inventory Value -->
	<div class="group bg-white rounded-xl shadow-md border-2 border-gray-200/80 relative overflow-hidden">
		<div class="p-4 sm:p-5 relative z-10">
			<div class="absolute top-3 right-3 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-lg flex items-center justify-center border border-green-200 z-20">
				<span class="text-sm sm:text-base font-bold text-[#008000]">₱</span>
			</div>
			<div class="flex-1 pr-10 sm:pr-12">
				<div class="flex items-center justify-between mb-2">
					<h3 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wide">Inventory Value</h3>
				</div>
				<div class="text-2xl sm:text-3xl font-black tracking-tight mb-2 text-gray-800">₱<?php echo number_format($inventoryValue, 2); ?></div>
				<p class="text-xs text-gray-600 mb-2.5 leading-relaxed">estimated replacement value</p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory#inventory-low-stock" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#008000] hover:text-[#006a00]">
					See stock ledger
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Overview Chart - Enhanced -->
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-6 sm:p-8 relative overflow-hidden">
	<div>
		<div class="flex items-center justify-between mb-8">
			<div class="space-y-1">
				<h2 class="text-3xl font-bold text-gray-900 tracking-tight">Overview</h2>
				<p class="text-sm text-gray-600 font-medium">Recent purchases and deliveries activity</p>
			</div>
			<div class="hidden sm:flex items-center gap-2.5 px-4 py-2 rounded-xl bg-[#008000]/10 border-2 border-[#008000]/20">
				<div class="w-2.5 h-2.5 rounded-full bg-[#008000]"></div>
				<span class="text-xs font-bold text-[#008000] uppercase tracking-wide">Live Data</span>
			</div>
		</div>
		<div class="relative min-h-[18rem] sm:min-h-[20rem] lg:min-h-[24rem] bg-gray-100 rounded-xl p-6 border-2 border-gray-200/60">
			<canvas id="overviewChart" class="h-full w-full"></canvas>
			<div id="chartFallback" class="absolute inset-0 flex flex-col items-center justify-center text-center text-gray-500 <?php echo $chartTotal > 0 ? 'hidden' : ''; ?>">
				<svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
				</svg>
				<p class="text-sm font-medium">Not enough activity to chart yet</p>
				<p class="text-xs text-gray-400 mt-1">Data will appear here as transactions are recorded</p>
			</div>
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
			backgroundColor: 'rgba(0, 128, 0, 0.7)',
			borderColor: 'rgba(0, 128, 0, 1)',
			borderWidth: 2,
			borderRadius: 6,
			barThickness: 'flex',
		},
		{
			label: 'Deliveries',
			data: chartData.deliveries ?? [],
			backgroundColor: 'rgba(229, 231, 235, 0.7)',
			borderColor: 'rgba(229, 231, 235, 1)',
			borderWidth: 2,
			borderRadius: 6,
			barThickness: 'flex',
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
						labels: {
							padding: 20,
							font: {
								size: 13,
								weight: '600',
							},
							usePointStyle: true,
							pointStyle: 'circle',
						}
					},
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						titleFont: {
							size: 13,
							weight: 'bold',
						},
						bodyFont: {
							size: 12,
						},
						borderRadius: 8,
						displayColors: true,
					}
				},
				scales: {
					x: {
						grid: {
							display: false,
						},
						ticks: {
							font: {
								size: 12,
							},
							color: '#6B7280',
						}
					},
					y: {
						beginAtZero: true,
						grid: {
							color: 'rgba(0, 0, 0, 0.05)',
							drawBorder: false,
						},
						ticks: {
							precision: 0,
							font: {
								size: 12,
							},
							color: '#6B7280',
						}
					}
				}
			}
		});
	} else if (chartFallback) {
		chartFallback.classList.remove('hidden');
	}
}
</script>

<style>
	/* Typography - Inter font support */
	* {
		font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}

	/* Performance optimizations */
	* {
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	/* Optimize scrolling performance with GPU acceleration */
	.overflow-x-auto,
	.overflow-y-auto {
		transform: translateZ(0);
		backface-visibility: hidden;
	}

	/* Better focus states for accessibility */
	a:focus-visible,
	button:focus-visible {
		outline: 2px solid #008000;
		outline-offset: 2px;
		border-radius: 0.5rem;
	}


	/* Chart container improvements */
	#overviewChart {
		max-height: 100%;
	}
</style>
