<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$categoriesList = $categoriesList ?? [];
$usageStatuses = $usageStatuses ?? ['used' => 'Used', 'expired' => 'Expired', 'transferred' => 'Transferred'];
$sectionsEnabled = $sectionsEnabled ?? ['purchase' => true, 'consumption' => true];
$canViewCosts = $canViewCosts ?? true;
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

// Initialize consumption data
$consumption = $consumption ?? [];

// Calculate statistics for purchase reports
$totalPurchases = !empty($purchases) ? count($purchases) : 0;
$totalCost = !empty($purchases) ? array_sum(array_column($purchases, 'cost')) : 0;
$uniqueSuppliers = !empty($purchases) ? count(array_unique(array_column($purchases, 'supplier'))) : 0;
$uniqueItems = !empty($purchases) ? count(array_unique(array_column($purchases, 'item_name'))) : 0;
$avgDailyCost = !empty($daily) && count($daily) > 0 ? array_sum(array_column($daily, 'total')) / count($daily) : 0;

// Calculate statistics for consumption reports
$totalIngredients = !empty($consumption) ? count($consumption) : 0;
$totalConsumed = !empty($consumption) ? array_sum(array_column($consumption, 'total_quantity')) : 0;
$uniqueCategories = !empty($consumption) ? count(array_filter(array_unique(array_column($consumption, 'category')))) : 0;
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Reports & Analytics</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Analyze purchase transactions and ingredient consumption</p>
		</div>
	</div>
</div>

<!-- Tab Navigation -->
<div class="bg-white rounded-xl border border-gray-200 mb-4 md:mb-6">
	<div class="flex border-b border-gray-200">
		<button type="button" 
			id="reportTabPurchase" 
			class="report-tab flex-1 px-4 md:px-6 py-3 md:py-4 text-sm md:text-base font-semibold text-gray-700 border-b-2 border-blue-600 bg-blue-50 transition-colors"
			data-tab="purchase">
			<div class="flex items-center justify-center gap-2">
				<i data-lucide="shopping-cart" class="w-4 h-4 md:w-5 md:h-5"></i>
				<span>Purchase Reports</span>
			</div>
		</button>
		<?php if (!empty($sectionsEnabled['consumption'])): ?>
		<button type="button" 
			id="reportTabConsumption" 
			class="report-tab flex-1 px-4 md:px-6 py-3 md:py-4 text-sm md:text-base font-semibold text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:bg-gray-50 transition-colors"
			data-tab="consumption">
			<div class="flex items-center justify-center gap-2">
				<i data-lucide="activity" class="w-4 h-4 md:w-5 md:h-5"></i>
				<span>Consumption Reports</span>
			</div>
		</button>
		<?php endif; ?>
	</div>
</div>

<!-- Purchase Reports Tab Content -->
<div id="purchaseTabContent" class="report-tab-content">
<div id="purchaseReportSection" class="space-y-6 md:space-y-8">
<?php if (!empty($sectionsEnabled['purchase'])): ?>
	
	<!-- Summary Cards -->
	<?php if (!empty($purchases)): ?>
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
		<!-- Total Purchases -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
					<i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Purchases</p>
				<p class="text-2xl md:text-3xl font-black text-gray-900 mb-1"><?php echo $totalPurchases; ?></p>
				<p class="text-xs text-gray-600">Transaction records</p>
			</div>
		</div>
		
		<!-- Total Cost -->
		<?php if ($canViewCosts): ?>
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
					<span class="text-xl font-bold text-green-600">₱</span>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Cost</p>
				<p class="text-2xl md:text-3xl font-black text-green-600 mb-1">₱<?php echo number_format($totalCost, 2); ?></p>
				<p class="text-xs text-gray-600">Total expenses</p>
			</div>
		</div>
		
		<!-- Average Daily Cost -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
					<i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Avg Daily</p>
				<p class="text-2xl md:text-3xl font-black text-purple-600 mb-1">₱<?php echo number_format($avgDailyCost, 2); ?></p>
				<p class="text-xs text-gray-600">Per day average</p>
			</div>
		</div>
		<?php else: ?>
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
					<i data-lucide="shield" class="w-5 h-5 text-gray-500"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Cost</p>
				<p class="text-2xl md:text-3xl font-black text-gray-500 mb-1">Hidden</p>
				<p class="text-xs text-gray-600">Restricted access</p>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Unique Suppliers -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
					<i data-lucide="truck" class="w-5 h-5 text-orange-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Suppliers</p>
				<p class="text-2xl md:text-3xl font-black text-orange-600 mb-1"><?php echo $uniqueSuppliers; ?></p>
				<p class="text-xs text-gray-600">Unique suppliers</p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Filters Section -->
	<div class="bg-white rounded-xl border border-gray-200 mb-6 md:mb-8 overflow-hidden">
		<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<div class="flex items-center justify-between">
				<div>
					<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="filter" class="w-4 h-4 md:w-5 md:h-5 text-blue-600"></i>
						Filters
					</h2>
					<p class="text-[10px] md:text-xs text-gray-600 mt-0.5">Filter purchase transactions by date, supplier, category, and more</p>
				</div>
				<button type="button" id="togglePurchaseFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
					<i data-lucide="chevron-up" class="w-5 h-5 filter-toggle-icon"></i>
				</button>
			</div>
		</div>
		<div id="purchaseFiltersPanel" class="p-4 md:p-5 lg:p-6">
			<form method="get" id="purchaseFiltersForm">
				<?php foreach (['date_from','date_to','category','usage_status'] as $key): ?>
					<input type="hidden" name="c_<?php echo $key; ?>" value="<?php echo htmlspecialchars($consumptionFilters[$key] ?? ''); ?>">
				<?php endforeach; ?>
				
				<!-- Date Range Presets -->
				<div class="mb-6">
					<label class="block text-xs md:text-sm font-medium text-gray-700 mb-3">Quick Date Range</label>
					<div class="flex flex-wrap gap-2">
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="today">Today</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="week">This Week</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="month">This Month</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="lastmonth">Last Month</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="year">This Year</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="last30">Last 30 Days</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="last90">Last 90 Days</button>
					</div>
				</div>
				
				<!-- Custom Date Range -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">From Date</label>
						<input type="date" name="p_date_from" id="purchaseDateFrom" value="<?php echo htmlspecialchars($purchaseFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">To Date</label>
						<input type="date" name="p_date_to" id="purchaseDateTo" value="<?php echo htmlspecialchars($purchaseFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
					</div>
				</div>
				
				<!-- Other Filters -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Supplier</label>
						<input name="p_supplier" value="<?php echo htmlspecialchars($purchaseFilters['supplier']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Filter by supplier" />
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Item</label>
						<select name="p_item_id" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
							<option value="">All Items</option>
							<?php foreach ($ingredients as $ing): ?>
								<option value="<?php echo (int)$ing['id']; ?>" <?php echo ((int)$purchaseFilters['item_id'] === (int)$ing['id']) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($ing['name']); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Category</label>
						<select name="p_category" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
							<option value="">All Categories</option>
							<?php foreach ($categoriesList as $category): ?>
								<option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($purchaseFilters['category'] === $category) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($category); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Payment Status</label>
						<select name="p_payment_status" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
							<option value="">All</option>
							<option value="Paid" <?php echo ($purchaseFilters['payment_status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
							<option value="Pending" <?php echo ($purchaseFilters['payment_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
						</select>
					</div>
				</div>
				
				<!-- Active Filter Chips -->
				<div id="purchaseFilterChips" class="flex flex-wrap gap-2 mb-4"></div>
				
				<!-- Action Buttons -->
				<div class="flex flex-wrap gap-3">
					<button type="submit" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="search" class="w-4 h-4"></i>
						Apply Filters
					</button>
					<button type="button" id="clearPurchaseFilters" class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
						<i data-lucide="x" class="w-4 h-4"></i>
						Clear All
					</button>
					<div class="flex-1"></div>
					<!-- Export Buttons -->
					<button type="button" id="purchaseExportPDF" class="inline-flex items-center gap-2 bg-red-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export PDF
					</button>
					<button type="button" id="purchaseExportExcel" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
						Export Excel
					</button>
					<button type="button" id="purchaseExportCSV" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export CSV
					</button>
					<button type="button" id="purchasePrintBtn" class="inline-flex items-center gap-2 bg-gray-700 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="printer" class="w-4 h-4"></i>
						Print
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Charts Section -->
	<?php if ($canViewCosts && !empty($daily)): ?>
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
			<h3 class="text-sm md:text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
				<i data-lucide="trending-up" class="w-4 h-4 md:w-5 md:h-5 text-blue-600"></i>
				Daily Spending Trend
			</h3>
			<div class="relative min-h-[250px]">
				<canvas id="dailySpendingChart"></canvas>
			</div>
		</div>
		
		<?php
		// Calculate supplier totals for chart display
		$hasSupplierData = false;
		$tempSupplierTotals = [];
		if ($canViewCosts && !empty($purchases)) {
			foreach ($purchases as $p) {
				$supplier = trim($p['supplier'] ?? '') ?: 'Unknown';
				if (!isset($tempSupplierTotals[$supplier])) {
					$tempSupplierTotals[$supplier] = 0;
				}
				$tempSupplierTotals[$supplier] += (float)($p['cost'] ?? 0);
			}
			$hasSupplierData = !empty($tempSupplierTotals);
		}
		?>
		<?php if ($hasSupplierData): ?>
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
			<h3 class="text-sm md:text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
				<i data-lucide="pie-chart" class="w-4 h-4 md:w-5 md:h-5 text-orange-600"></i>
				Spending by Supplier
			</h3>
			<div class="relative min-h-[250px]">
				<canvas id="supplierChart"></canvas>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<!-- Purchase Details Table -->
	<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
				<div>
					<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="clipboard-list" class="w-4 h-4 md:w-5 md:h-5 text-gray-600"></i>
						Purchase Details
					</h2>
					<p class="text-[10px] md:text-xs text-gray-600 mt-0.5">Detailed purchase transaction data</p>
				</div>
				<div class="flex items-center gap-3">
					<div class="text-xs text-gray-600">
						<span class="font-medium"><?php echo $totalPurchases; ?></span> transactions
					</div>
					<div class="relative">
						<input type="text" id="purchaseTableSearch" placeholder="Search..." class="w-48 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<i data-lucide="search" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
					</div>
				</div>
			</div>
		</div>
		
		<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
			<table class="w-full text-[10px] md:text-xs lg:text-sm" id="purchaseTable">
				<thead class="sticky top-0 bg-white z-10">
					<tr>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="date">
							<div class="flex items-center gap-1">
								<span>Date</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="item">
							<div class="flex items-center gap-1">
								<span>Item</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="supplier">
							<div class="flex items-center gap-1">
								<span>Supplier</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="quantity">
							<div class="flex items-center gap-1">
								<span>Quantity</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<?php if ($canViewCosts): ?>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="cost">
							<div class="flex items-center gap-1">
								<span>Cost</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody id="purchaseTableBody" class="divide-y divide-gray-200">
					<?php foreach ($purchases as $p): ?>
					<tr class="purchase-row hover:bg-gray-50 transition-colors" 
						data-date="<?php echo htmlspecialchars($p['date_purchased']); ?>"
						data-item="<?php echo htmlspecialchars(strtolower($p['item_name'])); ?>"
						data-supplier="<?php echo htmlspecialchars(strtolower($p['supplier'])); ?>"
						data-quantity="<?php echo (float)$p['quantity']; ?>"
						data-cost="<?php echo $canViewCosts ? (float)$p['cost'] : 0; ?>">
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm text-gray-600">
							<?php echo htmlspecialchars($p['date_purchased']); ?>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<div class="flex items-center gap-2">
								<div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
									<i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
								</div>
								<span class="font-medium text-gray-900"><?php echo htmlspecialchars($p['item_name']); ?></span>
							</div>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
								<i data-lucide="truck" class="w-3 h-3"></i>
								<?php echo htmlspecialchars($p['supplier']); ?>
							</span>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<span class="font-semibold text-gray-900"><?php echo number_format((float)$p['quantity'], 2); ?></span>
						</td>
						<?php if ($canViewCosts): ?>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<span class="font-bold text-gray-900">₱<?php echo number_format((float)$p['cost'], 2); ?></span>
						</td>
						<?php endif; ?>
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
		
		<div id="purchaseTableEmpty" class="hidden px-6 py-12 text-center text-sm text-gray-500">
			<i data-lucide="search-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
			<p>No transactions match your search</p>
		</div>
	</div>

<?php else: ?>
	<div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-600 text-center">
		Purchase reporting is currently disabled by an administrator.
	</div>
<?php endif; ?>
</div>
</div>

<!-- Consumption Reports Tab Content -->
<?php if (!empty($sectionsEnabled['consumption'])): ?>
<div id="consumptionTabContent" class="report-tab-content hidden">
<div id="consumptionReportSection" class="space-y-6 md:space-y-8">
	
	<!-- Summary Cards -->
	<?php if (!empty($consumption)): ?>
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
		<!-- Total Ingredients -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
					<i data-lucide="chef-hat" class="w-5 h-5 text-emerald-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ingredients Used</p>
				<p class="text-2xl md:text-3xl font-black text-gray-900 mb-1"><?php echo $totalIngredients; ?></p>
				<p class="text-xs text-gray-600">Unique ingredients</p>
			</div>
		</div>
		
		<!-- Total Quantity -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
					<i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Consumed</p>
				<p class="text-2xl md:text-3xl font-black text-emerald-600 mb-1"><?php echo number_format($totalConsumed, 2); ?></p>
				<p class="text-xs text-gray-600">Base units</p>
			</div>
		</div>
		
		<!-- Categories -->
		<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5 relative">
			<div class="absolute top-4 right-4">
				<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
					<i data-lucide="tags" class="w-5 h-5 text-purple-600"></i>
				</div>
			</div>
			<div class="pr-12">
				<p class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Categories</p>
				<p class="text-2xl md:text-3xl font-black text-purple-600 mb-1"><?php echo $uniqueCategories; ?></p>
				<p class="text-xs text-gray-600">Unique categories</p>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	<!-- Consumption Filters Section -->
	<div class="bg-white rounded-xl border border-gray-200 mb-6 md:mb-8 overflow-hidden">
		<div class="bg-gradient-to-r from-emerald-50 to-green-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<div class="flex items-center justify-between">
				<div>
					<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="filter" class="w-4 h-4 md:w-5 md:h-5 text-emerald-600"></i>
						Filters
					</h2>
					<p class="text-[10px] md:text-xs text-gray-600 mt-0.5">Filter ingredient consumption by date range, category, and usage status</p>
				</div>
				<button type="button" id="toggleConsumptionFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
					<i data-lucide="chevron-up" class="w-5 h-5 filter-toggle-icon"></i>
				</button>
			</div>
		</div>
		<div id="consumptionFiltersPanel" class="p-4 md:p-5 lg:p-6">
			<form method="get" id="consumptionFiltersForm">
				<?php foreach (['date_from','date_to','supplier','item_id','category','payment_status'] as $key): ?>
					<input type="hidden" name="p_<?php echo $key; ?>" value="<?php echo htmlspecialchars($purchaseFilters[$key] ?? ''); ?>">
				<?php endforeach; ?>
				
				<!-- Date Range Presets -->
				<div class="mb-6">
					<label class="block text-xs md:text-sm font-medium text-gray-700 mb-3">Quick Date Range</label>
					<div class="flex flex-wrap gap-2">
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="today">Today</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="week">This Week</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="month">This Month</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="lastmonth">Last Month</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="year">This Year</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="last30">Last 30 Days</button>
						<button type="button" class="date-preset-btn px-3 py-1.5 text-xs md:text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700" data-preset="last90">Last 90 Days</button>
					</div>
				</div>
				
				<!-- Custom Date Range -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">From Date</label>
						<input type="date" name="c_date_from" id="consumptionDateFrom" value="<?php echo htmlspecialchars($consumptionFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" />
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">To Date</label>
						<input type="date" name="c_date_to" id="consumptionDateTo" value="<?php echo htmlspecialchars($consumptionFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" />
					</div>
				</div>
				
				<!-- Other Filters -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Category</label>
						<select name="c_category" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
							<option value="">All Categories</option>
							<?php foreach ($categoriesList as $category): ?>
								<option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($consumptionFilters['category'] === $category) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($category); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Usage Status</label>
						<select name="c_usage_status" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
							<option value="">All Statuses</option>
							<?php foreach ($usageStatuses as $value => $label): ?>
								<option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($consumptionFilters['usage_status'] === $value) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				
				<!-- Active Filter Chips -->
				<div id="consumptionFilterChips" class="flex flex-wrap gap-2 mb-4"></div>
				
				<!-- Action Buttons -->
				<div class="flex flex-wrap gap-3">
					<button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="search" class="w-4 h-4"></i>
						Apply Filters
					</button>
					<button type="button" id="clearConsumptionFilters" class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
						<i data-lucide="x" class="w-4 h-4"></i>
						Clear All
					</button>
					<div class="flex-1"></div>
					<!-- Export Buttons -->
					<button type="button" id="consumptionExportPDF" class="inline-flex items-center gap-2 bg-red-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export PDF
					</button>
					<button type="button" id="consumptionExportExcel" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
						Export Excel
					</button>
					<button type="button" id="consumptionExportCSV" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="file-text" class="w-4 h-4"></i>
						Export CSV
					</button>
					<button type="button" id="consumptionPrintBtn" class="inline-flex items-center gap-2 bg-gray-700 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-sm font-medium">
						<i data-lucide="printer" class="w-4 h-4"></i>
						Print
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Consumption Table -->
	<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
		<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
				<div>
					<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="chef-hat" class="w-4 h-4 md:w-5 md:h-5 text-emerald-600"></i>
						Ingredient Consumption
					</h2>
					<p class="text-[10px] md:text-xs text-gray-600 mt-0.5">Totals show ingredients consumed (Distributed batches) within the selected date range</p>
				</div>
				<div class="flex items-center gap-3">
					<div class="text-xs text-gray-600">
						<span class="font-medium"><?php echo count($consumption); ?></span> ingredient<?php echo count($consumption) === 1 ? '' : 's'; ?>
					</div>
					<div class="relative">
						<input type="text" id="consumptionTableSearch" placeholder="Search..." class="w-48 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
						<i data-lucide="search" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
			<table class="w-full text-[10px] md:text-xs lg:text-sm" id="consumptionTable">
				<thead class="sticky top-0 bg-white z-10">
					<tr>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="name">
							<div class="flex items-center gap-1">
								<span>Ingredient</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="category">
							<div class="flex items-center gap-1">
								<span>Category</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white cursor-pointer hover:bg-gray-50 sortable" data-sort="quantity">
							<div class="flex items-center gap-1">
								<span>Total Used</span>
								<i data-lucide="arrow-up-down" class="w-3 h-3 text-gray-400"></i>
							</div>
						</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white">Display Unit</th>
					</tr>
				</thead>
				<tbody id="consumptionTableBody" class="divide-y divide-gray-200">
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
					<tr class="consumption-row hover:bg-gray-50 transition-colors"
						data-name="<?php echo htmlspecialchars(strtolower($row['name'])); ?>"
						data-category="<?php echo htmlspecialchars(strtolower($row['category'] ?? '')); ?>"
						data-quantity="<?php echo $baseQty; ?>">
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<div class="flex items-center gap-2">
								<div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
									<i data-lucide="leaf" class="w-4 h-4 text-emerald-600"></i>
								</div>
								<div>
									<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($row['name']); ?></p>
									<p class="text-xs text-gray-500">Base: <?php echo htmlspecialchars($unit ?: 'unit'); ?></p>
								</div>
							</div>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm text-gray-600">
							<?php echo htmlspecialchars($row['category'] ?? '—'); ?>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<span class="font-bold text-gray-900"><?php echo number_format($baseQty, 2); ?></span>
							<span class="text-sm text-gray-500 ml-1"><?php echo htmlspecialchars($unit); ?></span>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
							<?php if ($convertedQty !== null): ?>
								<span class="font-semibold text-gray-900"><?php echo number_format($convertedQty, 2); ?></span>
								<span class="text-sm text-gray-500 ml-1"><?php echo htmlspecialchars($displayUnit); ?></span>
							<?php else: ?>
								<span class="text-sm text-gray-500">—</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<?php if (empty($consumption)): ?>
			<div class="flex flex-col items-center justify-center py-12 text-gray-500">
				<i data-lucide="clipboard-x" class="w-16 h-16 mb-4 text-gray-300"></i>
				<h3 class="text-lg font-medium text-gray-900 mb-2">No Data Found</h3>
				<p class="text-sm text-gray-600 mb-4">Try adjusting your filters to see consumption data</p>
			</div>
			<?php endif; ?>
		</div>
		
		<div id="consumptionTableEmpty" class="hidden px-6 py-12 text-center text-sm text-gray-500">
			<i data-lucide="search-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
			<p>No ingredients match your search</p>
		</div>
	</div>
</div>
</div>
</div>
<?php else: ?>
<div class="report-tab-content hidden">
<div class="space-y-6 md:space-y-8">
	<div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-600 text-center">
		Consumption reporting is currently disabled by an administrator.
	</div>
</div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const appBase = '<?php echo rtrim($baseUrl, '/'); ?>';
const buildUrl = (path) => `${appBase}${path}`;

// Tab Switching (robust, no console noise)
function showReportTab(section) {
	const tabs = document.querySelectorAll('.report-tab');
	const contents = document.querySelectorAll('.report-tab-content');
	const tabEl = document.querySelector(`.report-tab[data-tab="${section}"]`);
	const contentEl = document.getElementById(section + 'TabContent');
	
	if (!tabEl) {
		console.warn('Tab element not found for section:', section);
		return false;
	}
	if (!contentEl) {
		console.warn('Content element not found for section:', section);
		return false;
	}

	// Update tab styles
	tabs.forEach(t => {
		t.classList.remove('border-blue-600', 'bg-blue-50', 'text-gray-700');
		t.classList.add('border-transparent', 'text-gray-500');
	});
	tabEl.classList.remove('border-transparent', 'text-gray-500');
	tabEl.classList.add('border-blue-600', 'bg-blue-50', 'text-gray-700');

	// Hide all content - be very explicit
	contents.forEach(c => {
		c.classList.add('hidden');
		c.setAttribute('hidden', '');
		c.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important;';
	});
	
	// Force show the target content - multiple methods to ensure visibility
	contentEl.classList.remove('hidden');
	contentEl.removeAttribute('hidden');
	
	// Also ensure parent is visible if it's a report-tab-content (fixes nested hidden parent issue)
	let parent = contentEl.parentElement;
	while (parent && parent.classList.contains('report-tab-content')) {
		parent.classList.remove('hidden');
		parent.removeAttribute('hidden');
		parent.style.cssText = 'display: block !important; visibility: visible !important;';
		parent = parent.parentElement;
	}
	
	contentEl.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 10 !important; min-height: 100px !important; width: 100% !important;';

	// Double-check visibility after a brief delay
	setTimeout(() => {
		const computed = window.getComputedStyle(contentEl);
		if (computed.display === 'none' || computed.visibility === 'hidden') {
			// Force it again if still hidden
			contentEl.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 10 !important; width: 100% !important;';
		}
		// Debug: Log if element has dimensions
		if (contentEl.offsetWidth === 0 || contentEl.offsetHeight === 0) {
			console.warn('Element has zero dimensions:', {
				width: contentEl.offsetWidth,
				height: contentEl.offsetHeight,
				parent: contentEl.parentElement?.tagName,
				parentClasses: contentEl.parentElement?.className,
				parentDisplay: contentEl.parentElement ? window.getComputedStyle(contentEl.parentElement).display : 'N/A'
			});
		}
	}, 10);

	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}
	
	return true;
}

function initTabSwitching() {
	const tabs = document.querySelectorAll('.report-tab');
	if (tabs.length === 0) return;

	tabs.forEach(tab => {
		tab.addEventListener('click', () => showReportTab(tab.dataset.tab));
	});

	// Determine initial tab: query param ?section=..., else purchase, else first.
	const params = new URLSearchParams(window.location.search);
	const requested = (params.get('section') || '').toLowerCase();
	const available = Array.from(tabs).map(t => t.dataset.tab);
	let initial = available.find(t => t === requested) || null;
	if (!initial) {
		initial = available.includes('purchase') ? 'purchase' : available[0];
	}
	showReportTab(initial);
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initTabSwitching);
} else {
	initTabSwitching();
}

// Filter Panel Toggle
function setupFilterToggle(toggleId, panelId) {
	const toggle = document.getElementById(toggleId);
	const panel = document.getElementById(panelId);
	if (!toggle || !panel) return;
	
	toggle.addEventListener('click', () => {
		const isHidden = panel.classList.contains('hidden');
		panel.classList.toggle('hidden');
		const icon = toggle.querySelector('.filter-toggle-icon');
		if (icon) {
			icon.setAttribute('data-lucide', isHidden ? 'chevron-up' : 'chevron-down');
			if (typeof lucide !== 'undefined') lucide.createIcons();
		}
	});
}

setupFilterToggle('togglePurchaseFilters', 'purchaseFiltersPanel');
setupFilterToggle('toggleConsumptionFilters', 'consumptionFiltersPanel');

// Date Presets
function setDatePreset(preset, fromField, toField) {
	const today = new Date();
	let fromDate, toDate;
	
	switch(preset) {
		case 'today':
			fromDate = toDate = today.toISOString().split('T')[0];
			break;
		case 'week':
			const weekStart = new Date(today);
			weekStart.setDate(today.getDate() - today.getDay());
			fromDate = weekStart.toISOString().split('T')[0];
			toDate = today.toISOString().split('T')[0];
			break;
		case 'month':
			fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
			toDate = today.toISOString().split('T')[0];
			break;
		case 'lastmonth':
			const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
			const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
			fromDate = lastMonth.toISOString().split('T')[0];
			toDate = lastMonthEnd.toISOString().split('T')[0];
			break;
		case 'year':
			fromDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
			toDate = today.toISOString().split('T')[0];
			break;
		case 'last30':
			fromDate = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
			toDate = today.toISOString().split('T')[0];
			break;
		case 'last90':
			fromDate = new Date(today.getTime() - 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
			toDate = today.toISOString().split('T')[0];
			break;
		default:
			return;
	}
	
	if (fromField) fromField.value = fromDate;
	if (toField) toField.value = toDate;
}

document.querySelectorAll('.date-preset-btn').forEach(btn => {
	btn.addEventListener('click', () => {
		const preset = btn.dataset.preset;
		const form = btn.closest('form');
		if (!form) return;
		
		if (form.id === 'purchaseFiltersForm') {
			setDatePreset(preset, document.getElementById('purchaseDateFrom'), document.getElementById('purchaseDateTo'));
		} else if (form.id === 'consumptionFiltersForm') {
			setDatePreset(preset, document.getElementById('consumptionDateFrom'), document.getElementById('consumptionDateTo'));
		}
	});
});

// Filter Chips Display
function updateFilterChips(formId, chipsContainerId) {
	const form = document.getElementById(formId);
	const chipsContainer = document.getElementById(chipsContainerId);
	if (!form || !chipsContainer) return;
	
	const formData = new FormData(form);
	const chips = [];
	
	formData.forEach((value, key) => {
		if (value && value.trim() !== '' && !key.startsWith('c_') && !key.startsWith('p_')) {
			let label = '';
			if (key.includes('date_from')) label = `From: ${value}`;
			else if (key.includes('date_to')) label = `To: ${value}`;
			else if (key.includes('supplier')) label = `Supplier: ${value}`;
			else if (key.includes('category')) label = `Category: ${value}`;
			else if (key.includes('item_id')) {
				const select = form.querySelector('[name="' + key + '"]');
				if (select) label = `Item: ${select.options[select.selectedIndex]?.text || value}`;
			} else if (key.includes('payment_status')) label = `Status: ${value}`;
			else if (key.includes('usage_status')) label = `Usage: ${value}`;
			
			if (label) {
				chips.push({ key, value, label });
			}
		}
	});
	
	chipsContainer.innerHTML = chips.map(chip => `
		<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
			${chip.label}
			<button type="button" class="clear-chip hover:bg-blue-200 rounded-full p-0.5" data-key="${chip.key}">
				<i data-lucide="x" class="w-3 h-3"></i>
			</button>
		</span>
	`).join('');
	
	if (typeof lucide !== 'undefined') {
		lucide.createIcons({ elements: chipsContainer.querySelectorAll('i[data-lucide]') });
	}
	
	// Clear chip handlers
	chipsContainer.querySelectorAll('.clear-chip').forEach(btn => {
		btn.addEventListener('click', () => {
			const key = btn.dataset.key;
			const input = form.querySelector('[name="' + key + '"]');
			if (input) {
				input.value = '';
				updateFilterChips(formId, chipsContainerId);
			}
		});
	});
}

// Update filter chips on load
updateFilterChips('purchaseFiltersForm', 'purchaseFilterChips');
updateFilterChips('consumptionFiltersForm', 'consumptionFilterChips');

// Clear Filters
document.getElementById('clearPurchaseFilters')?.addEventListener('click', () => {
	const form = document.getElementById('purchaseFiltersForm');
	if (form) {
		form.reset();
		updateFilterChips('purchaseFiltersForm', 'purchaseFilterChips');
	}
});

document.getElementById('clearConsumptionFilters')?.addEventListener('click', () => {
	const form = document.getElementById('consumptionFiltersForm');
	if (form) {
		form.reset();
		updateFilterChips('consumptionFiltersForm', 'consumptionFilterChips');
	}
});

// Build Query from Form
const buildQueryFromForm = (form, section) => {
	const params = new URLSearchParams();
	const formData = new FormData(form);
	formData.forEach((value, key) => {
		if (value instanceof File) return;
		const trimmed = typeof value === 'string' ? value.trim() : value;
		if (trimmed === '' || trimmed === null) return;
		params.append(key, trimmed);
	});
	params.set('section', section);
	return params.toString();
};

// Export Functions
function setupExport(buttonId, section, format) {
	const button = document.getElementById(buttonId);
	if (!button) return;
	
	button.addEventListener('click', () => {
		const form = section === 'purchase' ? document.getElementById('purchaseFiltersForm') : document.getElementById('consumptionFiltersForm');
		if (!form) return;
		
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
	});
}

setupExport('purchaseExportPDF', 'purchase', 'pdf');
setupExport('purchaseExportExcel', 'purchase', 'excel');
setupExport('purchaseExportCSV', 'purchase', 'csv');
setupExport('consumptionExportPDF', 'consumption', 'pdf');
setupExport('consumptionExportExcel', 'consumption', 'excel');
setupExport('consumptionExportCSV', 'consumption', 'csv');

// Table Search
function setupTableSearch(searchId, tableId, emptyId) {
	const searchInput = document.getElementById(searchId);
	const table = document.getElementById(tableId);
	const emptyState = document.getElementById(emptyId);
	if (!searchInput || !table) return;
	
	searchInput.addEventListener('input', (e) => {
		const searchTerm = e.target.value.toLowerCase().trim();
		const rows = table.querySelectorAll('tbody tr');
		let visibleCount = 0;
		
		rows.forEach(row => {
			const text = row.textContent.toLowerCase();
			if (text.includes(searchTerm)) {
				row.classList.remove('hidden');
				visibleCount++;
			} else {
				row.classList.add('hidden');
			}
		});
		
		if (emptyState) {
			emptyState.classList.toggle('hidden', visibleCount > 0 || searchTerm === '');
		}
	});
}

setupTableSearch('purchaseTableSearch', 'purchaseTable', 'purchaseTableEmpty');
setupTableSearch('consumptionTableSearch', 'consumptionTable', 'consumptionTableEmpty');

// Table Sorting
function setupTableSorting(tableId) {
	const table = document.getElementById(tableId);
	if (!table) return;
	
	const headers = table.querySelectorAll('thead th.sortable');
	let currentSort = { column: null, direction: 'asc' };
	
	headers.forEach(header => {
		header.addEventListener('click', () => {
			const column = header.dataset.sort;
			if (!column) return;
			
			const tbody = table.querySelector('tbody');
			const rows = Array.from(tbody.querySelectorAll('tr:not(.hidden)'));
			
			// Determine sort direction
			if (currentSort.column === column) {
				currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
			} else {
				currentSort.column = column;
				currentSort.direction = 'asc';
			}
			
			// Sort rows
			rows.sort((a, b) => {
				const aVal = a.dataset[column] || a.textContent.trim();
				const bVal = b.dataset[column] || b.textContent.trim();
				
				// Try numeric comparison
				const aNum = parseFloat(aVal);
				const bNum = parseFloat(bVal);
				if (!isNaN(aNum) && !isNaN(bNum)) {
					return currentSort.direction === 'asc' ? aNum - bNum : bNum - aNum;
				}
				
				// String comparison
				const comparison = aVal.localeCompare(bVal);
				return currentSort.direction === 'asc' ? comparison : -comparison;
			});
			
			// Re-append sorted rows
			rows.forEach(row => tbody.appendChild(row));
			
			// Update sort icons
			headers.forEach(h => {
				const icon = h.querySelector('i[data-lucide="arrow-up-down"], i[data-lucide="arrow-up"], i[data-lucide="arrow-down"]');
				if (icon) {
					icon.setAttribute('data-lucide', 'arrow-up-down');
				}
			});
			const currentIcon = header.querySelector('i[data-lucide]');
			if (currentIcon) {
				currentIcon.setAttribute('data-lucide', currentSort.direction === 'asc' ? 'arrow-up' : 'arrow-down');
				if (typeof lucide !== 'undefined') lucide.createIcons();
			}
		});
	});
}

setupTableSorting('purchaseTable');
setupTableSorting('consumptionTable');

// Enhanced Print with Chart Images
function setupEnhancedPrint(buttonId, sectionId, title, chartIds = []) {
	const button = document.getElementById(buttonId);
	const section = document.getElementById(sectionId);
	if (!button || !section) return;
	
	button.addEventListener('click', async () => {
		const win = window.open('', '_blank', 'width=900,height=700');
		if (!win) return;
		
		const clone = section.cloneNode(true);
		
		// Remove no-print elements
		clone.querySelectorAll('.no-print').forEach(el => el.remove());
		
		// Remove forms and buttons
		clone.querySelectorAll('form, button').forEach(el => el.remove());
		
		// Convert charts to images
		const chartImages = [];
		for (const chartId of chartIds) {
			const canvas = document.getElementById(chartId);
			if (canvas) {
				const chart = Chart.getChart(canvas);
				if (chart) {
					try {
						const imageData = chart.toBase64Image('image/png', 1.0);
						chartImages.push(`<div style="margin: 20px 0; text-align: center;"><img src="${imageData}" style="max-width: 100%; height: auto;" /></div>`);
					} catch (e) {
						console.warn('Failed to export chart:', e);
					}
				}
			}
		}
		
		// Remove canvas elements
		clone.querySelectorAll('canvas').forEach(canvas => {
			const container = canvas.closest('.bg-white.rounded-xl, .bg-white.rounded-lg');
			if (container) container.remove();
		});
		
		// Remove icons
		clone.querySelectorAll('[data-lucide], i[class*="lucide"]').forEach(icon => icon.remove());
		
		// Clean summary cards
		clone.querySelectorAll('.grid').forEach(grid => {
			if (grid.querySelector('.text-2xl, .text-3xl')) {
				grid.style.display = 'flex';
				grid.style.gap = '12px';
				grid.style.marginBottom = '20px';
				grid.style.flexWrap = 'wrap';
			}
		});
		
		const content = clone.innerHTML;
		const chartsHtml = chartImages.length > 0 ? '<div style="margin-bottom: 30px;"><h2 style="font-size: 18px; margin-bottom: 15px; color: #374151;">Charts</h2>' + chartImages.join('') + '</div>' : '';
		
		win.document.write(`<html><head><title>${title}</title>
			<style>
				@page { margin: 1.5cm; }
				body { font-family: Arial, sans-serif; padding: 20px; color: #1f2937; font-size: 12px; line-height: 1.5; }
				h1 { font-size: 24px; margin: 0 0 8px 0; color: #1d4ed8; font-weight: bold; }
				h2 { font-size: 18px; margin: 20px 0 10px 0; color: #374151; font-weight: 600; }
				.grid { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
				.bg-white { background: white !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; padding: 16px !important; margin-bottom: 12px; }
				table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 11px; }
				th, td { border: 1px solid #e5e7eb; padding: 10px 12px; text-align: left; }
				th { background: #f3f4f6 !important; font-weight: 600; font-size: 11px; }
				.text-2xl { font-size: 20px !important; font-weight: bold; }
				.text-3xl { font-size: 24px !important; font-weight: bold; }
				.text-sm { font-size: 11px !important; }
				.text-lg { font-size: 14px !important; }
				.text-gray-600 { color: #4b5563 !important; }
				.text-gray-900 { color: #111827 !important; }
				.text-green-600 { color: #059669 !important; }
				.text-purple-600 { color: #9333ea !important; }
				.text-orange-600 { color: #ea580c !important; }
				.text-blue-600 { color: #2563eb !important; }
				.rounded-xl { border-radius: 8px !important; }
				@media print {
					body { padding: 10px; }
					.bg-white { page-break-inside: avoid; margin-bottom: 10px; }
					table { page-break-inside: auto; }
					tr { page-break-inside: avoid; page-break-after: auto; }
					thead { display: table-header-group; }
				}
			</style>
		</head><body>
			<h1>${title}</h1>
			<p style="font-size: 11px; color: #6b7280; margin-bottom: 20px;">Generated on ${new Date().toLocaleString()}</p>
			${chartsHtml}
			${content}
		</body></html>`);
		win.document.close();
		win.focus();
		setTimeout(() => win.print(), 250);
	});
}

// Setup print with available charts
const purchaseChartIds = [];
if (document.getElementById('dailySpendingChart')) purchaseChartIds.push('dailySpendingChart');
if (document.getElementById('supplierChart')) purchaseChartIds.push('supplierChart');
setupEnhancedPrint('purchasePrintBtn', 'purchaseReportSection', 'Purchase Report', purchaseChartIds);
setupEnhancedPrint('consumptionPrintBtn', 'consumptionReportSection', 'Ingredient Consumption Report', []);

// Charts
<?php if ($canViewCosts && !empty($daily)): ?>
const dailyData = <?php echo json_encode($daily, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
const dailyLabels = dailyData.map(x => x.d);
const dailyValues = dailyData.map(x => Number(x.total));

// Daily Spending Chart
const dailyCtx = document.getElementById('dailySpendingChart');
if (dailyCtx && dailyData.length > 0) {
	new Chart(dailyCtx, {
		type: 'line',
		data: { 
			labels: dailyLabels, 
			datasets: [{ 
				label: 'Daily Spending', 
				data: dailyValues, 
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
				legend: { display: false },
				tooltip: {
					callbacks: {
						label: (context) => '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
					}
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					grid: { color: 'rgba(0, 0, 0, 0.1)' },
					ticks: {
						callback: function(value) {
							return '₱' + value.toLocaleString();
						}
					}
				},
				x: {
					grid: { color: 'rgba(0, 0, 0, 0.1)' }
				}
			}
		}
	});
}

// Supplier Chart (aggregate spending by supplier)
<?php
$supplierTotals = [];
if ($canViewCosts && !empty($purchases)) {
	foreach ($purchases as $p) {
		$supplier = trim($p['supplier'] ?? '') ?: 'Unknown';
		if (!isset($supplierTotals[$supplier])) {
			$supplierTotals[$supplier] = 0;
		}
		$supplierTotals[$supplier] += (float)($p['cost'] ?? 0);
	}
	if (!empty($supplierTotals)) {
		arsort($supplierTotals);
		$supplierTotals = array_slice($supplierTotals, 0, 10, true); // Top 10
	}
}
if ($canViewCosts && !empty($supplierTotals)): ?>
const supplierData = <?php echo json_encode($supplierTotals, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
const supplierCtx = document.getElementById('supplierChart');
if (supplierCtx && Object.keys(supplierData).length > 0) {
	const supplierLabels = Object.keys(supplierData);
	const supplierValues = Object.values(supplierData);
	
	new Chart(supplierCtx, {
		type: 'doughnut',
		data: {
			labels: supplierLabels,
			datasets: [{
				data: supplierValues,
				backgroundColor: [
					'rgba(59, 130, 246, 0.8)',
					'rgba(16, 185, 129, 0.8)',
					'rgba(245, 158, 11, 0.8)',
					'rgba(239, 68, 68, 0.8)',
					'rgba(139, 92, 246, 0.8)',
					'rgba(236, 72, 153, 0.8)',
					'rgba(14, 165, 233, 0.8)',
					'rgba(34, 197, 94, 0.8)',
					'rgba(251, 146, 60, 0.8)',
					'rgba(168, 85, 247, 0.8)',
				]
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					position: 'right',
					labels: {
						padding: 15,
						font: { size: 11 }
					}
				},
				tooltip: {
					callbacks: {
						label: (context) => {
							const label = context.label || '';
							const value = context.parsed || 0;
							const total = context.dataset.data.reduce((a, b) => a + b, 0);
							const percentage = ((value / total) * 100).toFixed(1);
							return `${label}: ₱${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${percentage}%)`;
						}
					}
				}
			}
		}
	});
}
<?php endif; ?>
<?php endif; ?>

// Initialize icons
if (typeof lucide !== 'undefined') {
	lucide.createIcons();
}
</script>

<style>
/* Tab Styles */
.report-tab {
	position: relative;
}
.report-tab-content {
	animation: fadeIn 0.2s ease-in;
	display: none;
}
.report-tab-content:not(.hidden) {
	display: block !important;
	visibility: visible !important;
	opacity: 1 !important;
}
.report-tab-content.hidden {
	display: none !important;
	visibility: hidden !important;
}
@keyframes fadeIn {
	from { opacity: 0; transform: translateY(-5px); }
	to { opacity: 1; transform: translateY(0); }
}

/* Remove focus ring for tablet mode */
@media (min-width: 768px) and (max-width: 1023px) {
	#purchaseFiltersForm input:focus,
	#purchaseFiltersForm select:focus,
	#consumptionFiltersForm input:focus,
	#consumptionFiltersForm select:focus {
		outline: none !important;
		box-shadow: none !important;
		border-color: rgb(209 213 219) !important;
	}
}

/* Sortable table headers */
.sortable {
	cursor: pointer;
	user-select: none;
}
.sortable:hover {
	background-color: #f9fafb !important;
}

/* Remove shadows */
.shadow-sm,
.shadow-md,
.shadow-lg,
.shadow-xl,
.shadow-2xl {
	box-shadow: none !important;
}
</style>
