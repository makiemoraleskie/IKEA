<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$categoriesList = $categoriesList ?? [];
$usageStatuses = $usageStatuses ?? ['used' => 'Used', 'expired' => 'Expired', 'transferred' => 'Transferred'];
$purchaseFilters = array_merge([
	'date_from' => '',
	'date_to' => '',
	'supplier' => '',
	'item_id' => '',
	'payment_status' => 'Paid',
	'category' => '',
], $purchaseFilters ?? []);
$consumptionFilters = array_merge([
	'date_from' => '',
	'date_to' => '',
	'category' => '',
	'usage_status' => '',
], $consumptionFilters ?? []);
?>
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

<section id="purchaseReportSection" class="space-y-8">
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
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b">
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="filter" class="w-5 h-5 text-indigo-600"></i>
				Purchase Filters
			</h2>
			<p class="text-sm text-gray-600 mt-1">Filter purchase transactions.</p>
		</div>
		<form method="get" id="purchaseFiltersForm" class="p-6 space-y-6">
			<?php foreach (['date_from','date_to','category','usage_status'] as $key): ?>
				<input type="hidden" name="c_<?php echo $key; ?>" value="<?php echo htmlspecialchars($consumptionFilters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">From Date</label>
					<input type="date" name="p_date_from" value="<?php echo htmlspecialchars($purchaseFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">To Date</label>
					<input type="date" name="p_date_to" value="<?php echo htmlspecialchars($purchaseFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Supplier</label>
					<input name="p_supplier" value="<?php echo htmlspecialchars($purchaseFilters['supplier']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Filter by supplier" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Item</label>
					<select name="p_item_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
						<option value="">All Items</option>
						<?php foreach ($ingredients as $ing): ?>
							<option value="<?php echo (int)$ing['id']; ?>" <?php echo ((int)$purchaseFilters['item_id'] === (int)$ing['id']) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($ing['name']); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Category</label>
					<select name="p_category" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
						<option value="">All Categories</option>
						<?php foreach ($categoriesList as $category): ?>
							<option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($purchaseFilters['category'] === $category) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($category); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Payment Status</label>
					<select name="p_payment_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
						<option value="">All</option>
						<option value="Paid" <?php echo ($purchaseFilters['payment_status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
						<option value="Pending" <?php echo ($purchaseFilters['payment_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
					</select>
				</div>
			</div>
			<div class="flex flex-col gap-3 lg:flex-row lg:items-center">
				<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="search" class="w-4 h-4"></i>
					Apply Filters
				</button>
				<div class="w-full">
					<label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Export As</label>
					<div class="relative">
						<select id="purchaseExportSelect" class="w-full appearance-none border border-gray-300 rounded-lg px-4 py-3 pr-10 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium text-gray-700">
							<option value="" selected disabled>Select format</option>
							<option value="pdf">PDF</option>
							<option value="excel">Excel (.xls)</option>
							<option value="csv">CSV</option>
						</select>
						<span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
							<i data-lucide="chevron-down" class="w-4 h-4"></i>
						</span>
					</div>
				</div>
				<button type="button" id="purchasePrintBtn" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-4 py-3 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="printer" class="w-4 h-4"></i>
					Print Purchases
				</button>
			</div>
		</form>
	</div>
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="activity" class="w-5 h-5 text-emerald-600"></i>
				Consumption Filters
			</h2>
			<p class="text-sm text-gray-600 mt-1">Filter ingredient consumption totals.</p>
		</div>
		<form method="get" id="consumptionFiltersForm" class="p-6 space-y-6">
			<?php foreach (['date_from','date_to','supplier','item_id','category','payment_status'] as $key): ?>
				<input type="hidden" name="p_<?php echo $key; ?>" value="<?php echo htmlspecialchars($purchaseFilters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">From Date</label>
					<input type="date" name="c_date_from" value="<?php echo htmlspecialchars($consumptionFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">To Date</label>
					<input type="date" name="c_date_to" value="<?php echo htmlspecialchars($consumptionFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Category</label>
					<select name="c_category" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
						<option value="">All Categories</option>
						<?php foreach ($categoriesList as $category): ?>
							<option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($consumptionFilters['category'] === $category) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($category); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Usage Status</label>
					<select name="c_usage_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
						<option value="">All Statuses</option>
						<?php foreach ($usageStatuses as $value => $label): ?>
							<option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($consumptionFilters['usage_status'] === $value) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($label); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="flex flex-col gap-3 lg:flex-row lg:items-center">
				<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-4 py-3 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="search" class="w-4 h-4"></i>
					Apply Filters
				</button>
				<div class="w-full">
					<label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Export As</label>
					<div class="relative">
						<select id="consumptionExportSelect" class="w-full appearance-none border border-gray-300 rounded-lg px-4 py-3 pr-10 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium text-gray-700">
							<option value="" selected disabled>Select format</option>
							<option value="pdf">PDF</option>
							<option value="excel">Excel (.xls)</option>
							<option value="csv">CSV</option>
						</select>
						<span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
							<i data-lucide="chevron-down" class="w-4 h-4"></i>
						</span>
					</div>
				</div>
				<button type="button" id="consumptionPrintBtn" class="w-full inline-flex items-center justify-center gap-2 bg-slate-700 text-white px-4 py-3 rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-600 focus:ring-offset-2 transition-colors">
					<i data-lucide="printer" class="w-4 h-4"></i>
					Print Consumption
				</button>
			</div>
		</form>
	</div>
</div>

<?php $consumption = $consumption ?? []; ?>
</section>

<!-- Ingredient Consumption Report -->
<div id="consumptionReportSection" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
	<div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="chef-hat" class="w-5 h-5 text-emerald-600"></i>
				Ingredient Consumption
			</h2>
			<p class="text-sm text-gray-600 mt-1">Totals show ingredients consumed (Distributed batches) within the selected date range. All values are consolidated into each ingredient's base unit.</p>
		</div>
		<div class="text-sm text-gray-600">
			<?php echo count($consumption); ?> ingredient<?php echo count($consumption) === 1 ? '' : 's'; ?>
		</div>
	</div>
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[720px]">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Ingredient</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Category</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Total Used (Base Unit)</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Converted Display</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($consumption as $row): 
					$baseQty = (float)($row['total_quantity'] ?? 0);
					$unit = $row['unit'] ?? '';
					$displayUnit = $row['display_unit'] ?? '';
					$displayFactor = (float)($row['display_factor'] ?? 1);
					$convertedQty = null;
					if ($displayUnit !== '' && $displayFactor > 0 && abs($displayFactor - 1) > 0.00001) {
						$convertedQty = $baseQty / $displayFactor;
					}
				?>
				<tr>
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
								<i data-lucide="leaf" class="w-5 h-5 text-emerald-600"></i>
							</div>
							<div>
								<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($row['name']); ?></p>
								<p class="text-xs text-gray-500">Base unit: <?php echo htmlspecialchars($unit ?: 'unit'); ?></p>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 text-sm text-gray-600">
						<?php echo htmlspecialchars($row['category'] ?? '—'); ?>
					</td>
					<td class="px-6 py-4">
						<span class="text-lg font-bold text-gray-900"><?php echo number_format($baseQty, 2); ?></span>
						<span class="text-sm text-gray-500"><?php echo htmlspecialchars($unit); ?></span>
					</td>
					<td class="px-6 py-4">
						<?php if ($convertedQty !== null): ?>
							<span class="font-semibold text-gray-900"><?php echo number_format($convertedQty, 2); ?></span>
							<span class="text-sm text-gray-500"><?php echo htmlspecialchars($displayUnit); ?></span>
						<?php else: ?>
							<span class="text-sm text-gray-500">—</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($consumption)): ?>
				<tr>
					<td colspan="4" class="px-6 py-6 text-center text-gray-500 text-sm">No consumption data for the selected filters.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
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
const appBase = '<?php echo rtrim($baseUrl, '/'); ?>';
const buildUrl = (path) => `${appBase}${path}`;

const buildQueryFromForm = (form, section) => {
	const params = new URLSearchParams();
	const formData = new FormData(form);
	formData.forEach((value, key) => {
		if (value instanceof File) { return; }
		const trimmed = typeof value === 'string' ? value.trim() : value;
		if (trimmed === '' || trimmed === null) { return; }
		params.append(key, trimmed);
	});
	params.set('section', section);
	return params.toString();
};

const setupExport = (selectId, section) => {
	const select = document.getElementById(selectId);
	if (!select) { return; }
	select.addEventListener('change', () => {
		const format = select.value;
		if (!format) { return; }
		const form = select.closest('form');
		if (!form) { return; }
		const query = buildQueryFromForm(form, section);
		let endpoint = '';
		if (format === 'pdf') {
			endpoint = buildUrl('/reports/pdf?' + query);
		} else if (format === 'excel') {
			endpoint = buildUrl('/reports/export/excel?' + query);
		} else if (format === 'csv') {
			endpoint = buildUrl('/reports/export/csv?' + query);
		}
		if (endpoint) {
			window.open(endpoint, '_blank');
		}
		select.value = '';
	});
};

const setupPrint = (buttonId, sectionId, title) => {
	const button = document.getElementById(buttonId);
	const section = document.getElementById(sectionId);
	if (!button || !section) { return; }
	button.addEventListener('click', () => {
		const win = window.open('', '_blank', 'width=900,height=700');
		if (!win) { return; }
		win.document.write(`<html><head><title>${title}</title>
			<style>
				body { font-family: Arial, sans-serif; padding: 24px; color: #1f2937; }
				h1 { font-size: 20px; margin-bottom: 16px; }
				table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
				th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
				th { background: #f3f4f6; }
			</style>
		</head><body><h1>${title}</h1>${section.innerHTML}</body></html>`);
		win.document.close();
		win.focus();
		win.print();
	});
};

setupExport('purchaseExportSelect', 'purchase');
setupExport('consumptionExportSelect', 'consumption');
setupPrint('purchasePrintBtn', 'purchaseReportSection', 'Purchase Report');
setupPrint('consumptionPrintBtn', 'consumptionReportSection', 'Ingredient Consumption');

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


