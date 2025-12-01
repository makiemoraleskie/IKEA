<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6 sm:mb-8">
	<div>
		<h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Purchase Reports</h1>
		<p class="text-sm sm:text-base text-gray-600 mt-1">Analyze and export purchase data</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<!-- Summary Cards -->
<?php if (!empty($purchases)): ?>
<?php 
// Calculate report statistics
$totalPurchases = count($purchases);
$totalCost = array_sum(array_column($purchases, 'cost'));
$uniqueSuppliers = count(array_unique(array_column($purchases, 'supplier')));
$uniqueItems = count(array_unique(array_column($purchases, 'item_name')));
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8">
	<!-- Total Purchases -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Purchases</p>
				<p class="text-3xl sm:text-4xl font-black text-gray-900 mt-1"><?php echo $totalPurchases; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Total Cost -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Cost</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1">₱<?php echo number_format($totalCost, 2); ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<span class="text-lg sm:text-xl font-bold text-[#008000]">₱</span>
			</div>
		</div>
	</div>
	
	<!-- Unique Suppliers -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Suppliers</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $uniqueSuppliers; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="truck" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Unique Items -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Items Purchased</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $uniqueItems; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="package" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
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
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Report Filters</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Customize your report data and export options</p>
			</div>
		</div>
	</div>
	
	<form method="get" class="p-6">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
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
			
			<!-- Supplier -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Supplier</label>
				<input name="supplier" value="<?php echo htmlspecialchars($filters['supplier'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" placeholder="Filter by supplier" />
			</div>
			
			<!-- Item -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Item</label>
				<select name="item_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
					<option value="">All Items</option>
					<?php foreach ($ingredients as $ing): ?>
						<option value="<?php echo (int)$ing['id']; ?>" <?php echo ((int)($filters['item_id'] ?? 0) === (int)$ing['id'])?'selected':''; ?>><?php echo htmlspecialchars($ing['name']); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<!-- Payment Status -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Payment Status</label>
				<select name="payment_status" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
					<option value="">All</option>
					<option value="Paid" <?php echo (($filters['payment_status'] ?? '') === 'Paid') ? 'selected' : ''; ?>>Paid</option>
					<option value="Pending" <?php echo (($filters['payment_status'] ?? '') === 'Pending') ? 'selected' : ''; ?>>Pending</option>
				</select>
			</div>
			
			<!-- Actions -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Actions</label>
				<div class="space-y-2">
					<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#008000] via-[#00A86B] to-[#008000] text-white px-4 py-3 rounded-xl hover:shadow-lg hover:shadow-[#008000]/30 focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold">
						<i data-lucide="search" class="w-4 h-4"></i>
						Apply Filters
					</button>
					<a href="<?php echo htmlspecialchars($baseUrl); ?>/reports/pdf?<?php echo http_build_query($filters); ?>" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-red-600 text-white px-4 py-3 rounded-xl hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors font-semibold">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export PDF
					</a>
					<button type="button" id="printReportBtn" class="w-full inline-flex items-center justify-center gap-2 bg-gray-700 text-white px-4 py-3 rounded-xl hover:bg-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-semibold">
						<i data-lucide="printer" class="w-4 h-4"></i>
						Print Report
					</button>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Reports Content -->
<div class="space-y-6 sm:space-y-8">
	<!-- Purchases Table -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 overflow-hidden">
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
						<i data-lucide="clipboard-list" class="w-5 h-5 text-[#008000]"></i>
					</div>
					<div>
						<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Purchase Details</h2>
						<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Detailed purchase transaction data</p>
					</div>
				</div>
				<div class="text-xs sm:text-sm text-gray-600">
					<span class="font-semibold"><?php echo count($purchases); ?></span> transactions
				</div>
			</div>
		</div>
		
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-gray-50">
					<tr>
						<th class="text-left px-6 py-3 font-medium text-gray-700">Date</th>
						<th class="text-left px-6 py-3 font-medium text-gray-700">Item</th>
						<th class="text-left px-6 py-3 font-medium text-gray-700">Supplier</th>
						<th class="text-left px-6 py-3 font-medium text-gray-700">Quantity</th>
						<th class="text-left px-6 py-3 font-medium text-gray-700">Cost</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200">
					<?php foreach ($purchases as $p): ?>
					<tr class="hover:bg-gray-50 transition-colors">
						<td class="px-6 py-4">
							<div class="flex items-center gap-2">
								<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
								<span class="text-gray-600"><?php echo htmlspecialchars($p['date_purchased']); ?></span>
							</div>
						</td>
						
						<td class="px-6 py-4">
							<div class="flex items-center gap-3">
								<div class="w-8 h-8 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
									<i data-lucide="package" class="w-4 h-4 text-[#008000]"></i>
								</div>
								<span class="font-medium text-gray-900"><?php echo htmlspecialchars($p['item_name']); ?></span>
							</div>
						</td>
						
						<td class="px-6 py-4">
							<span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
								<i data-lucide="truck" class="w-3 h-3"></i>
								<?php echo htmlspecialchars($p['supplier']); ?>
							</span>
						</td>
						
						<td class="px-6 py-4">
							<span class="font-semibold text-gray-900"><?php echo htmlspecialchars($p['quantity']); ?></span>
						</td>
						
						<td class="px-6 py-4">
							<div class="flex items-center gap-1">
								<span class="text-lg font-bold text-gray-900">₱<?php echo number_format((float)$p['cost'], 2); ?></span>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<?php if (empty($purchases)): ?>
			<div class="flex flex-col items-center justify-center py-12 text-gray-500">
				<i data-lucide="clipboard-x" class="w-16 h-16 mb-4 text-gray-300"></i>
				<h3 class="text-lg font-medium text-gray-900 mb-2">No Data Found</h3>
				<p class="text-sm text-gray-600 mb-4">Try adjusting your filters to see purchase data</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	
	<!-- Daily Spend Chart -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 overflow-hidden">
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
						<i data-lucide="trending-up" class="w-5 h-5 text-[#008000]"></i>
					</div>
					<div>
						<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Daily Spending Trend</h2>
						<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Visualize spending patterns over time</p>
					</div>
				</div>
				<div class="text-xs sm:text-sm text-gray-600">
					<span class="font-semibold"><?php echo count($daily); ?></span> days
				</div>
			</div>
		</div>
		
		<div class="p-6">
			<div class="relative min-h-[18rem] sm:min-h-[20rem] lg:min-h-[24rem]">
				<canvas id="dailyChart" class="h-full w-full"></canvas>
			</div>
			
			<?php if (empty($daily)): ?>
			<div class="flex flex-col items-center justify-center py-12 text-gray-500">
				<i data-lucide="bar-chart-3" class="w-16 h-16 mb-4 text-gray-300"></i>
				<h3 class="text-lg font-medium text-gray-900 mb-2">No Chart Data</h3>
				<p class="text-sm text-gray-600 mb-4">No spending data available for the selected period</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('printReportBtn')?.addEventListener('click', () => {
	window.print();
});
const daily = <?php echo json_encode($daily, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
const labels = daily.map(x => x.d);
const data = daily.map(x => Number(x.total));
const ctx2 = document.getElementById('dailyChart');
if (ctx2 && daily.length > 0) {
	new Chart(ctx2, {
		type: 'line',
		data: { 
			labels, 
			datasets: [{ 
				label: 'Daily Spending', 
				data, 
				fill: true,
				backgroundColor: 'rgba(0, 128, 0, 0.1)',
				borderColor: 'rgb(0, 128, 0)', 
				borderWidth: 2,
				tension: 0.4,
				pointBackgroundColor: 'rgb(0, 128, 0)',
				pointBorderColor: '#fff',
				pointBorderWidth: 2,
				pointRadius: 4
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
					beginAtZero: true,
					grid: {
						color: 'rgba(0, 0, 0, 0.1)'
					},
					ticks: {
						callback: function(value) {
							return '₱' + value.toLocaleString();
						}
					}
				},
				x: {
					grid: {
						color: 'rgba(0, 0, 0, 0.1)'
					}
				}
			}
		}
	});
}
</script>


