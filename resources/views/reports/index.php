<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Purchase Reports</h1>
		<p class="text-gray-600 mt-1">Analyze and export purchase data</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
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
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
	<!-- Total Purchases -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Purchases</p>
				<p class="text-2xl font-bold text-gray-900"><?php echo $totalPurchases; ?></p>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
				<i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Total Cost -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Cost</p>
				<p class="text-2xl font-bold text-green-600">₱<?php echo number_format($totalCost, 2); ?></p>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
				<i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Unique Suppliers -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Suppliers</p>
				<p class="text-2xl font-bold text-purple-600"><?php echo $uniqueSuppliers; ?></p>
			</div>
			<div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
				<i data-lucide="truck" class="w-6 h-6 text-purple-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Unique Items -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Items Purchased</p>
				<p class="text-2xl font-bold text-orange-600"><?php echo $uniqueItems; ?></p>
			</div>
			<div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
				<i data-lucide="package" class="w-6 h-6 text-orange-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="filter" class="w-5 h-5 text-indigo-600"></i>
			Report Filters
		</h2>
		<p class="text-sm text-gray-600 mt-1">Customize your report data and export options</p>
	</div>
	
	<form method="get" class="p-6">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
			<!-- Date From -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">From Date</label>
				<input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
			</div>
			
			<!-- Date To -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">To Date</label>
				<input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
			</div>
			
			<!-- Supplier -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Supplier</label>
				<input name="supplier" value="<?php echo htmlspecialchars($filters['supplier'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Filter by supplier" />
			</div>
			
			<!-- Item -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Item</label>
				<select name="item_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
					<option value="">All Items</option>
					<?php foreach ($ingredients as $ing): ?>
						<option value="<?php echo (int)$ing['id']; ?>" <?php echo ((int)($filters['item_id'] ?? 0) === (int)$ing['id'])?'selected':''; ?>><?php echo htmlspecialchars($ing['name']); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<!-- Payment Status -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Payment Status</label>
				<select name="payment_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
					<option value="">All</option>
					<option value="Paid" <?php echo (($filters['payment_status'] ?? '') === 'Paid') ? 'selected' : ''; ?>>Paid</option>
					<option value="Pending" <?php echo (($filters['payment_status'] ?? '') === 'Pending') ? 'selected' : ''; ?>>Pending</option>
				</select>
			</div>
			
			<!-- Actions -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Actions</label>
				<div class="space-y-2">
					<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
						<i data-lucide="search" class="w-4 h-4"></i>
						Apply Filters
					</button>
					<a href="<?php echo htmlspecialchars($baseUrl); ?>/reports/pdf?<?php echo http_build_query($filters); ?>" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export PDF
					</a>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Reports Content -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
	<!-- Purchases Table -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
			<div class="flex items-center justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
						Purchase Details
					</h2>
					<p class="text-sm text-gray-600 mt-1">Detailed purchase transaction data</p>
				</div>
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($purchases); ?></span> transactions
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
								<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
									<i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
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
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
			<div class="flex items-center justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="trending-up" class="w-5 h-5 text-gray-600"></i>
						Daily Spending Trend
					</h2>
					<p class="text-sm text-gray-600 mt-1">Visualize spending patterns over time</p>
				</div>
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($daily); ?></span> days
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
				backgroundColor: 'rgba(59, 130, 246, 0.1)',
				borderColor: 'rgb(59, 130, 246)', 
				borderWidth: 2,
				tension: 0.4,
				pointBackgroundColor: 'rgb(59, 130, 246)',
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


