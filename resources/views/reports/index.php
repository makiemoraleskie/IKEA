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
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Purchase Reports</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Analyze and export purchase data</p>
		</div>
	</div>
</div>

<section id="purchaseReportSection" class="space-y-8">
<?php if (!empty($sectionsEnabled['purchase'])): ?>
<!-- Summary Cards -->
<?php if (!empty($purchases)): ?>
<?php 
// Calculate report statistics
$totalPurchases = count($purchases);
$totalCost = array_sum(array_column($purchases, 'cost'));
$uniqueSuppliers = count(array_unique(array_column($purchases, 'supplier')));
$uniqueItems = count(array_unique(array_column($purchases, 'item_name')));
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
	<!-- Total Purchases -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-blue-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL PURCHASES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $totalPurchases; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">All purchase records</p>
		</div>
	</div>
	
	<!-- Total Cost -->
	<?php if ($canViewCosts): ?>
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<span class="text-lg md:text-xl font-bold text-green-600">₱</span>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL COST</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5">₱<?php echo number_format($totalCost, 2); ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Total expenses</p>
		</div>
	</div>
	<?php else: ?>
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="shield" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-gray-500"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL COST</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-500 mb-1 md:mb-1.5">Hidden</div>
			<p class="text-[10px] md:text-xs text-gray-600">Cost visibility restricted</p>
		</div>
	</div>
	<?php endif; ?>
	
	<!-- Unique Suppliers -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="truck" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-purple-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">SUPPLIERS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-purple-600 mb-1.5 md:mb-2"><?php echo $uniqueSuppliers; ?></div>
			<p class="text-xs md:text-sm text-gray-600">Unique suppliers</p>
		</div>
	</div>
	
	<!-- Unique Items -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="package" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-orange-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:mb-3">ITEMS PURCHASED</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-orange-600 mb-1.5 md:mb-2"><?php echo $uniqueItems; ?></div>
			<p class="text-xs md:text-sm text-gray-600">Unique items</p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Filters Section -->
<div class="mb-8 no-print">
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden max-w-full w-full">
		<div class="bg-gray-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
				<i data-lucide="filter" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
				Purchase Filters
			</h2>
			<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Filter purchase transactions.</p>
		</div>
		<form method="get" id="purchaseFiltersForm" class="p-4 md:p-5 lg:p-6 space-y-5">
			<?php foreach (['date_from','date_to','category','usage_status'] as $key): ?>
				<input type="hidden" name="c_<?php echo $key; ?>" value="<?php echo htmlspecialchars($consumptionFilters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">From Date</label>
					<input type="date" name="p_date_from" value="<?php echo htmlspecialchars($purchaseFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">To Date</label>
					<input type="date" name="p_date_to" value="<?php echo htmlspecialchars($purchaseFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Supplier</label>
					<input name="p_supplier" value="<?php echo htmlspecialchars($purchaseFilters['supplier']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm" placeholder="Filter by supplier" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Item</label>
					<select name="p_item_id" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
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
					<select name="p_category" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
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
					<select name="p_payment_status" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
						<option value="">All</option>
						<option value="Paid" <?php echo ($purchaseFilters['payment_status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
						<option value="Pending" <?php echo ($purchaseFilters['payment_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
					</select>
				</div>
			</div>
			<div class="flex flex-col gap-3 lg:flex-row lg:items-center">
				<button type="submit" class="w-full inline-flex items-center justify-center gap-1 md:gap-1.5 bg-indigo-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="search" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Apply Filters
				</button>
				<div class="w-full">
					<label class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Export As</label>
					<div class="relative">
						<select id="purchaseExportSelect" class="w-full appearance-none border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 pr-9 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs md:text-sm font-medium text-gray-700">
							<option value="" selected disabled>Select format</option>
							<option value="pdf">PDF</option>
							<option value="excel">Excel (.xls)</option>
							<option value="csv">CSV</option>
						</select>
						<span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
							<i data-lucide="chevron-down" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						</span>
					</div>
				</div>
				<button type="button" id="purchasePrintBtn" class="w-full inline-flex items-center justify-center gap-1 md:gap-1.5 bg-emerald-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="printer" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Print Purchases
				</button>
			</div>
		</form>
	</div>
</div>

<?php else: ?>
	<div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-600">
		Purchase reporting is currently disabled by an administrator.
	</div>
<?php endif; ?>

<?php if (!empty($sectionsEnabled['consumption'])): ?>
	<!-- Consumption Filters Section -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden no-print max-w-full w-full">
		<div class="bg-gray-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
			<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
				<i data-lucide="activity" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
				Consumption Filters
			</h2>
			<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Filter ingredient consumption totals.</p>
		</div>
		<form method="get" id="consumptionFiltersForm" class="p-4 md:p-5 lg:p-6 space-y-5">
			<?php foreach (['date_from','date_to','supplier','item_id','category','payment_status'] as $key): ?>
				<input type="hidden" name="p_<?php echo $key; ?>" value="<?php echo htmlspecialchars($purchaseFilters[$key] ?? ''); ?>">
			<?php endforeach; ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">From Date</label>
					<input type="date" name="c_date_from" value="<?php echo htmlspecialchars($consumptionFilters['date_from']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">To Date</label>
					<input type="date" name="c_date_to" value="<?php echo htmlspecialchars($consumptionFilters['date_to']); ?>" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm" />
				</div>
				<div class="space-y-2">
					<label class="block text-sm font-medium text-gray-700">Category</label>
					<select name="c_category" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm">
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
					<select name="c_usage_status" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm">
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
				<button type="submit" class="w-full inline-flex items-center justify-center gap-1 md:gap-1.5 bg-emerald-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="search" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Apply Filters
				</button>
				<div class="w-full">
					<label class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Export As</label>
					<div class="relative">
						<select id="consumptionExportSelect" class="w-full appearance-none border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 pr-9 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-xs md:text-sm font-medium text-gray-700">
							<option value="" selected disabled>Select format</option>
							<option value="pdf">PDF</option>
							<option value="excel">Excel (.xls)</option>
							<option value="csv">CSV</option>
						</select>
						<span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
							<i data-lucide="chevron-down" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						</span>
					</div>
				</div>
				<button type="button" id="consumptionPrintBtn" class="w-full inline-flex items-center justify-center gap-1 md:gap-1.5 bg-slate-700 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-600 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="printer" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Print Consumption
				</button>
			</div>
		</form>
	</div>
<?php else: ?>
	<div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-600">
		Consumption reporting is currently disabled by an administrator.
	</div>
<?php endif; ?>

<!-- Purchase Details Table -->
<?php if (!empty($sectionsEnabled['purchase'])): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
					<i data-lucide="clipboard-list" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-600"></i>
					Purchase Details
				</h2>
				<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Detailed purchase transaction data</p>
			</div>
			<div class="text-[10px] md:text-xs text-gray-600">
				<span class="font-medium"><?php echo count($purchases); ?></span> transactions
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Date</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Item</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Supplier</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Quantity</th>
					<?php if ($canViewCosts): ?>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Cost</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($purchases as $p): ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<span class="text-gray-600"><?php echo htmlspecialchars($p['date_purchased']); ?></span>
						</div>
					</td>
					
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-3">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
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
						<span class="font-semibold text-gray-900"><?php echo htmlspecialchars($p['quantity']); ?></span>
					</td>
					
					<?php if ($canViewCosts): ?>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-1">
							<span class="text-lg font-bold text-gray-900">₱<?php echo number_format((float)$p['cost'], 2); ?></span>
						</div>
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
</div>
<?php endif; ?>
</section>

<?php $consumption = $consumption ?? []; ?>

<?php if (!empty($sectionsEnabled['consumption'])): ?>
<!-- Ingredient Consumption Report -->
<div id="consumptionReportSection" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 max-w-full w-full">
	<div class="bg-gray-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
				<i data-lucide="chef-hat" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
				Ingredient Consumption
			</h2>
			<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Totals show ingredients consumed (Distributed batches) within the selected date range. All values are consolidated into each ingredient's base unit.</p>
		</div>
		<div class="text-[10px] md:text-xs text-gray-600">
			<?php echo count($consumption); ?> ingredient<?php echo count($consumption) === 1 ? '' : 's'; ?>
		</div>
	</div>
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Ingredient</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Category</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Total Used (Base Unit)</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Converted Display</th>
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
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
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
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm text-gray-600">
						<?php echo htmlspecialchars($row['category'] ?? '—'); ?>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<span class="text-lg font-bold text-gray-900"><?php echo number_format($baseQty, 2); ?></span>
						<span class="text-sm text-gray-500"><?php echo htmlspecialchars($unit); ?></span>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
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
<?php endif; ?>

<!-- Reports Content -->
<?php if (!empty($sectionsEnabled['purchase'])): ?>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
	<!-- Daily Spend Chart -->
	<?php if ($canViewCosts): ?>
	<div class="bg-white rounded-lg shadow-md border p-3 md:p-4 lg:p-5 no-print">
		<div class="flex items-center justify-between mb-3 md:mb-4">
			<div>
				<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
					<i data-lucide="trending-up" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-600"></i>
					Daily Spending Trend
				</h2>
				<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Visualize spending patterns over time</p>
			</div>
			<div class="text-[10px] md:text-xs text-gray-600">
				<span class="font-medium"><?php echo count($daily); ?></span> days
			</div>
		</div>
		
		<div class="relative min-h-[12rem] sm:min-h-[14rem] lg:min-h-[16rem]">
			<canvas id="dailyChart" class="h-full w-full"></canvas>
			<?php if (empty($daily)): ?>
			<div class="absolute inset-0 flex items-center justify-center text-[10px] md:text-xs text-gray-500">
				No spending data available for the selected period
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php else: ?>
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex items-center justify-center p-6 text-sm text-gray-600">
		Spending charts are hidden for roles without cost visibility.
	</div>
	<?php endif; ?>
<?php endif; ?>

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
		
		// Clone the section to avoid modifying the original
		const clone = section.cloneNode(true);
		
		// Remove all elements with no-print class
		const noPrintElements = clone.querySelectorAll('.no-print');
		noPrintElements.forEach(el => el.remove());
		
		// Remove forms, buttons, and interactive elements
		const forms = clone.querySelectorAll('form');
		forms.forEach(form => {
			const parent = form.closest('.bg-white.rounded-xl, .grid');
			if (parent) parent.remove();
		});
		
		// Remove charts
		const charts = clone.querySelectorAll('canvas');
		charts.forEach(canvas => {
			const parent = canvas.closest('.bg-white.rounded-xl');
			if (parent) parent.remove();
		});
		
		// Remove icons (lucide icons won't render in print)
		const icons = clone.querySelectorAll('[data-lucide], i[class*="lucide"]');
		icons.forEach(icon => icon.remove());
		
		// Clean up summary cards styling
		const summaryGrids = clone.querySelectorAll('.grid.grid-cols-1');
		summaryGrids.forEach(grid => {
			if (grid.querySelector('.text-2xl')) {
				grid.style.display = 'flex';
				grid.style.gap = '12px';
				grid.style.marginBottom = '20px';
				grid.style.flexWrap = 'wrap';
			}
		});
		
		// Get the cleaned HTML
		const content = clone.innerHTML;
		
		win.document.write(`<html><head><title>${title}</title>
			<style>
				@page { margin: 1.5cm; }
				body { font-family: Arial, sans-serif; padding: 20px; color: #1f2937; font-size: 12px; line-height: 1.5; }
				h1 { font-size: 24px; margin: 0 0 8px 0; color: #1d4ed8; font-weight: bold; }
				h2 { font-size: 18px; margin: 20px 0 10px 0; color: #374151; font-weight: 600; }
				.grid { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
				.bg-white { background: white !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; padding: 16px !important; margin-bottom: 12px; }
				.bg-white .flex { display: flex; align-items: center; }
				table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 11px; }
				th, td { border: 1px solid #e5e7eb; padding: 10px 12px; text-align: left; }
				th { background: #f3f4f6 !important; font-weight: 600; font-size: 11px; }
				.text-2xl { font-size: 20px !important; font-weight: bold; }
				.text-sm { font-size: 11px !important; }
				.text-lg { font-size: 14px !important; }
				.text-gray-600 { color: #4b5563 !important; }
				.text-gray-900 { color: #111827 !important; }
				.text-green-600 { color: #059669 !important; }
				.text-purple-600 { color: #9333ea !important; }
				.text-orange-600 { color: #ea580c !important; }
				.text-blue-600 { color: #2563eb !important; }
				.rounded-xl { border-radius: 8px !important; }
				.shadow-sm { box-shadow: none !important; }
				.overflow-x-auto { overflow: visible !important; }
				.no-print { display: none !important; }
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
			${content}
		</body></html>`);
		win.document.close();
		win.focus();
		setTimeout(() => win.print(), 250);
	});
};

setupExport('purchaseExportSelect', 'purchase');
setupExport('consumptionExportSelect', 'consumption');
setupPrint('purchasePrintBtn', 'purchaseReportSection', 'Purchase Report');
setupPrint('consumptionPrintBtn', 'consumptionReportSection', 'Ingredient Consumption');

<?php if ($canViewCosts): ?>
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
<?php endif; ?>
</script>


