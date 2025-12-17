<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$ingredientSets = $ingredientSets ?? [];
$lowStockGroups = $lowStockGroups ?? [];
$canManageSets = in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true);
$canManageInventory = in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true);
$supplierFilterOptions = [];
if (!empty($lowStockGroups)) {
	foreach ($lowStockGroups as $group) {
		$label = trim((string)($group['label'] ?? 'Unassigned Supplier'));
		$key = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);
		if (!isset($supplierFilterOptions[$key])) {
			$supplierFilterOptions[$key] = $label;
		}
	}
}
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Inventory Management</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Track and manage ingredient stock levels</p>
		</div>
	</div>
</div>

<!-- Import CSV Modal -->
<div id="importCsvModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
	<div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" data-import-dismiss></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-4xl bg-white rounded-2xl shadow-none border border-gray-200 overflow-hidden max-h-[90vh] flex flex-col">
			<div class="flex items-start justify-between gap-3 px-5 py-4 border-b bg-gray-100">
				<div>
					<p class="text-md uppercase tracking-[0.25em] text-blue-500 font-semibold">Inventory Import</p>
					<p class="text-sm text-gray-600 mt-1">Select a CSV file containing inventory data</p>
				</div>
			</div>
			
			<div class="p-5 overflow-y-auto">
				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/import" enctype="multipart/form-data" class="space-y-4">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

					<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
						<div class="flex items-start gap-3">
							<i data-lucide="info" class="w-5 h-5 text-amber-600 mt-0.5"></i>
							<div class="flex-1">
								<h3 class="text-sm font-semibold text-amber-900 mb-2">CSV Format Requirements</h3>
								<ul class="text-xs text-amber-800 space-y-1 list-disc list-inside">
									<li>Row 1: Date headers (will be skipped)</li>
									<li>Row 2: Column headers (NEW STOCK, DEDUCTION, REMAIN) - will be skipped</li>
									<li>Row 3+: Item name, Unit, then date columns (NEW STOCK, DEDUCTION, REMAIN)</li>
									<li>Item name must be in the first column</li>
									<li>Unit must be in the second column</li>
									<li>The system will use the latest REMAIN value (rightmost non-empty REMAIN column)</li>
								</ul>
							</div>
						</div>
					</div>
					
					<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
						<div class="flex items-start gap-3">
							<i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 mt-0.5"></i>
							<div class="flex-1">
								<h3 class="text-sm font-semibold text-blue-900 mb-2">Import Behavior</h3>
								<ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
									<li>If an ingredient with the same name exists, its quantity will be updated</li>
									<li>If an ingredient doesn't exist, it will be created with the imported quantity</li>
									<li>Units will be normalized automatically (e.g., "Pack" → "pack", "Kg" → "kg")</li>
									<li>Empty rows and rows with missing data will be skipped</li>
								</ul>
							</div>
						</div>
					</div>
					
					<div class="space-y-3">
						<label class="block text-sm font-medium text-gray-700">CSV File</label>
						<div class="flex items-center gap-2">
							<input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
						</div>
						<p class="text-xs text-gray-500">Only CSV files are allowed. Maximum file size: 10MB</p>
					</div>
					
					<div class="flex justify-end gap-3">
						<button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors" data-import-dismiss>
							Cancel
						</button>
						<button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="upload" class="w-4 h-4"></i>
							Import Inventory
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Summary Cards -->
<?php 
$lowStockCount = 0;
foreach ($ingredients as $ing) {
	if ((float)$ing['quantity'] <= (float)$ing['reorder_level']) {
		$lowStockCount++;
	}
}
?>
<?php if (!empty($ingredients)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
	<!-- Total Ingredients -->
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="layers" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-blue-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL INGREDIENTS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo count($ingredients); ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">All ingredients in inventory</p>
		</div>
	</div>
	
	<!-- Low Stock Items -->
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="alert-triangle" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-red-500"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">LOW STOCK ITEMS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-red-600 mb-1 md:mb-1.5"><?php echo $lowStockCount; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">At or below reorder level</p>
		</div>
	</div>
	
	<!-- In Stock Items -->
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="check-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">IN STOCK ITEMS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo count($ingredients) - $lowStockCount; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Above reorder level</p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="mb-4 md:mb-8">
	<div class="flex flex-wrap items-center gap-2 md:gap-3">
		<?php if (in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true)): ?>
		<button 
			type="button" 
			id="openAddIngredientModal"
			class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm"
		>
			<i data-lucide="plus" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
			Add Ingredient
		</button>
		<?php endif; ?>
		<?php if ($inventoryActionsVisible): ?>
			<?php if (Auth::role() === 'Owner'): ?>
			<button 
				type="button" 
				id="openImportCsvModal"
				class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm"
			>
				<i data-lucide="upload" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Import CSV
			</button>
			<a 
				href="<?php echo htmlspecialchars($baseUrl); ?>/inventory/export"
				class="inline-flex items-center gap-1 md:gap-1.5 bg-blue-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-xs md:text-sm"
			>
				<i data-lucide="download" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Export CSV
			</a>
			<?php endif; ?>
			<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/migrate-kg-to-g" class="inline-block" data-confirm="This will convert all ingredients with base unit 'kg' to 'g'. Quantities will be multiplied by 1000. This action cannot be undone. Continue?">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<button type="submit" class="inline-flex items-center gap-1 md:gap-1.5 bg-purple-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="refresh-cw" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					<span class="hidden sm:inline">Convert kg → g</span>
					<span class="sm:hidden">Convert</span>
				</button>
			</form>
			<?php endif; ?>
			<?php if (Auth::role() === 'Owner'): ?>
			<button 
				type="button" 
				id="openImportCsvModal2"
				class="inline-flex items-center gap-1 md:gap-1.5 bg-emerald-700 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-emerald-800 focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-colors text-xs md:text-sm"
			>
				<i data-lucide="upload" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
				Import CSV 2
			</button>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<!-- Inventory Table -->
<div id="inventory-low-stock" class="bg-white rounded-lg shadow-none border border-gray-200 overflow-hidden max-w-full w-full">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
		<div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between">
			<div>
				<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1.5 md:gap-2">
					<i data-lucide="package" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-600"></i>
					Current Inventory
				</h2>
				<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">View and monitor all ingredient stock levels</p>
			</div>
			<div class="flex flex-wrap items-center gap-2 md:gap-3">
				<div class="text-[10px] md:text-xs text-gray-600 whitespace-nowrap">
					<span class="font-medium" id="totalIngredientsCount"><?php echo count($ingredients); ?></span> total
				</div>
				<?php 
				$lowStockCount = 0;
				foreach ($ingredients as $ing) {
					if ((float)$ing['quantity'] <= (float)$ing['reorder_level']) {
						$lowStockCount++;
					}
				}
				?>
				<?php if ($lowStockCount > 0): ?>
					<div class="flex items-center gap-1 md:gap-1.5 px-2 md:px-2.5 py-0.5 md:py-1 bg-red-100 text-red-700 rounded-full text-[10px] md:text-xs font-medium whitespace-nowrap">
						<i data-lucide="alert-triangle" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
						<span id="lowStockCount"><?php echo $lowStockCount; ?></span> low stock
					</div>
				<?php endif; ?>
				<button
					type="button"
					id="inventoryPurchaseListBtn"
					class="inline-flex items-center gap-1 md:gap-1.5 px-2.5 md:px-3 py-1 md:py-1.5 text-[10px] md:text-xs font-semibold rounded-lg shadow-sm border border-rose-200 bg-rose-600 text-white hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed whitespace-nowrap"
					<?php echo empty($lowStockGroups) ? 'disabled title="No low stock items to print"' : ''; ?>
				>
					<i data-lucide="printer" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
					<span class="hidden md:inline">Print Purchase List</span>
					<span class="md:hidden">Print</span>
				</button>
			</div>
		</div>
	</div>
	
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
		<div class="flex flex-col lg:flex-row gap-3">
			<div class="flex-1 relative">
				<i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
				<input 
					type="text" 
					id="inventorySearch" 
					placeholder="Search ingredients..." 
					class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-xs md:text-sm"
				>
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-2 md:gap-3 lg:w-auto">
				<?php 
				$categories = [];
				foreach ($ingredients as $ing) {
					$cat = trim($ing['category'] ?? '');
					if ($cat !== '' && !in_array($cat, $categories, true)) {
						$categories[] = $cat;
					}
				}
				sort($categories);
				?>
				<select id="inventoryCategoryFilter" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-[10px] md:text-xs">
					<option value="">All Categories</option>
					<?php foreach ($categories as $cat): ?>
						<option value="<?php echo htmlspecialchars(strtolower($cat)); ?>"><?php echo htmlspecialchars($cat); ?></option>
					<?php endforeach; ?>
				</select>
				<select id="inventoryStatusFilter" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-[10px] md:text-xs">
					<option value="all">All Status</option>
					<option value="low">Low Stock</option>
					<option value="in_stock">In Stock</option>
					<option value="out_of_stock">Out of Stock</option>
				</select>
				<select id="inventorySortBy" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-[10px] md:text-xs">
					<option value="name_asc">Sort: Name (A-Z)</option>
					<option value="name_desc">Sort: Name (Z-A)</option>
					<option value="stock_asc">Sort: Stock (Low to High)</option>
					<option value="stock_desc">Sort: Stock (High to Low)</option>
					<option value="reorder_asc">Sort: Reorder Level (Low to High)</option>
					<option value="reorder_desc">Sort: Reorder Level (High to Low)</option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="px-4 md:px-6 py-2 md:py-3 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-[10px] md:text-xs lg:text-sm text-gray-600">
		<div class="whitespace-nowrap">
			Showing <span id="inventoryResultsStart" class="font-medium">1</span> - 
			<span id="inventoryResultsEnd" class="font-medium"><?php echo min(25, count($ingredients)); ?></span> of 
			<span id="inventoryResultsTotal" class="font-medium"><?php echo count($ingredients); ?></span> ingredients
		</div>
		<div id="inventoryPagination" class="flex items-center gap-1 md:gap-1.5 lg:gap-2 flex-wrap justify-center">
			<!-- Pagination controls will be inserted here by JavaScript -->
		</div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
		<table class="w-full text-[10px] md:text-xs lg:text-sm table-fixed" style="min-width: 100%; table-layout: fixed;">
			<colgroup>
				<?php if ($canManageInventory): ?>
				<col class="col-ingredient">
				<col class="col-stock">
				<col class="col-reorder">
				<col class="col-status">
				<col class="col-action">
				<?php else: ?>
				<col class="col-ingredient">
				<col class="col-stock">
				<col class="col-reorder">
				<col class="col-status">
				<?php endif; ?>
			</colgroup>
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors sortable bg-white text-[10px] md:text-xs lg:text-sm" data-sort="name">
						<div class="flex items-center gap-1 md:gap-1.5 lg:gap-2">
							<span>Ingredient</span>
							<i data-lucide="arrow-up-down" class="w-2.5 h-2.5 md:w-3 md:h-3 text-gray-400"></i>
						</div>
					</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors sortable bg-white text-[10px] md:text-xs lg:text-sm" data-sort="stock">
						<div class="flex items-center gap-1 md:gap-1.5 lg:gap-2">
							<span>Current Stock</span>
							<i data-lucide="arrow-up-down" class="w-2.5 h-2.5 md:w-3 md:h-3 text-gray-400"></i>
						</div>
					</th>
					<th class="hidden lg:table-cell text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors sortable bg-white text-[10px] md:text-xs lg:text-sm" data-sort="reorder">
						<div class="flex items-center gap-1 md:gap-1.5 lg:gap-2">
							<span>Reorder Level</span>
							<i data-lucide="arrow-up-down" class="w-2.5 h-2.5 md:w-3 md:h-3 text-gray-400"></i>
						</div>
					</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Status</th>
					<?php if ($canManageInventory): ?>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Action</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody id="inventoryTableBody" class="divide-y divide-gray-200">
				<!-- Rows will be rendered dynamically by JavaScript -->
			</tbody>
		</table>
		
		<div id="inventoryEmptyState" class="hidden flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="search-x" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Ingredients Found</h3>
			<p class="text-sm text-gray-600 mb-4">Try adjusting your search or filter criteria</p>
			<button type="button" id="inventoryClearFilters" class="text-sm text-blue-600 hover:text-blue-700 underline">Clear all filters</button>
		</div>
		
		<?php if (empty($ingredients)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="package-x" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Ingredients Found</h3>
			<p class="text-sm text-gray-600 mb-4">Start by adding your first ingredient to the inventory</p>
			<?php if (in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true)): ?>
				<button type="button" id="openAddIngredientModalEmpty" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
					<i data-lucide="plus" class="w-4 h-4"></i>
					Add First Ingredient
				</button>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	</div>
</div>

<!-- Purchase List Modal -->
<div id="purchaseListModal" class="fixed inset-0 z-50 hidden">
	<div class="absolute inset-0 bg-gray-900/70 backdrop-blur-md" data-purchase-list-dismiss style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-4xl bg-white rounded-2xl shadow-none border border-gray-200 flex flex-col max-h-[85vh] relative">
			<button type="button" class="absolute top-4 right-4 z-10 inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" data-purchase-list-dismiss aria-label="Close">
				<i data-lucide="x" class="w-5 h-5"></i>
			</button>
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 md:gap-5 px-6 py-6 pr-16 border-b">
				<div class="space-y-1 w-full">
					<p class="text-xs uppercase tracking-[0.35em] text-rose-400 font-semibold">Low Stock</p>
					<h2 class="text-lg font-semibold text-gray-900">Purchase Preparation List</h2>
					<p class="text-xs text-gray-600">Ingredients at or below their reorder level, grouped by supplier.</p>
				</div>
				<div class="flex flex-wrap items-center gap-3 md:gap-4 w-full lg:w-auto">
					<?php if (!empty($supplierFilterOptions)): ?>
					<div class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-700 w-full sm:w-auto md:w-64">
						<i data-lucide="filter" class="w-4 h-4 text-gray-500"></i>
						<select id="purchaseListSupplierFilter" class="bg-transparent border-none focus:border-none focus:ring-0 text-sm text-gray-900 flex-1">
							<option value="">All suppliers</option>
							<?php foreach ($supplierFilterOptions as $key => $label): ?>
								<option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; ?>
					<button type="button" id="purchaseListCustomize" class="inline-flex items-center justify-center gap-2 px-4 md:px-5 py-2.5 rounded-lg border border-rose-200 text-rose-700 bg-rose-50 text-[12px] font-semibold shadow-sm hover:bg-rose-100 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 w-full sm:w-auto">
						<i data-lucide="list-checks" class="w-4 h-4"></i>
						Customize &amp; Print
					</button>
					<button type="button" id="purchaseListPrint" class="inline-flex items-center justify-center gap-2 px-4 md:px-5 py-2.5 rounded-lg bg-rose-600 text-white text-[12px] font-semibold shadow-sm hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 w-full sm:w-auto">
						<i data-lucide="printer" class="w-4 h-4"></i>
						Print List
					</button>
				</div>
			</div>
			<div id="purchaseListContent" class="px-6 py-5 overflow-y-auto space-y-6">
				<?php if (!empty($lowStockGroups)): ?>
					<?php foreach ($lowStockGroups as $group): 
						$supplier = $group['label'] ?? 'Unassigned Supplier';
						$items = $group['items'] ?? [];
						$supplierKey = function_exists('mb_strtolower') ? mb_strtolower($supplier) : strtolower($supplier);
					?>
					<section class="border rounded-2xl p-4 sm:p-6 shadow-none bg-white" data-supplier="<?php echo htmlspecialchars($supplierKey); ?>">
						<div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
							<div>
								<p class="text-xs uppercase tracking-wide text-gray-500">Supplier</p>
								<h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($supplier); ?></h3>
							</div>
							<div class="flex items-center gap-2 flex-wrap">
								<span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium"><?php echo count($items); ?> item<?php echo count($items) === 1 ? '' : 's'; ?></span>
								<button type="button" class="purchase-customize-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-rose-200 text-rose-700 bg-rose-50 text-xs font-semibold hover:bg-rose-100 transition-colors" data-customize-supplier="<?php echo htmlspecialchars($supplierKey); ?>">
									<i data-lucide="sliders" class="w-3.5 h-3.5"></i>
									Customize
								</button>
							</div>
						</div>
						<div class="mt-4 overflow-x-auto">
							<table class="w-full text-xs md:text-sm min-w-[520px]">
								<thead class="bg-gray-50">
									<tr>
										<th class="text-left px-3 md:px-4 py-1.5 md:py-2 font-medium text-gray-700 text-[11px] md:text-xs lg:text-sm">Ingredient</th>
										<th class="text-left px-3 md:px-4 py-1.5 md:py-2 font-medium text-gray-700 text-[11px] md:text-xs lg:text-sm">Status</th>
										<th class="text-left px-3 md:px-4 py-1.5 md:py-2 font-medium text-gray-700 text-[11px] md:text-xs lg:text-sm">On Hand</th>
										<th class="text-left px-3 md:px-4 py-1.5 md:py-2 font-medium text-gray-700 text-[11px] md:text-xs lg:text-sm">Reorder Level</th>
										<th class="text-left px-3 md:px-4 py-1.5 md:py-2 font-medium text-gray-700 text-[11px] md:text-xs lg:text-sm">Recommended Qty</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									<?php foreach ($items as $item): ?>
									<tr>
										<td class="px-3 md:px-4 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm lg:text-base"><?php echo htmlspecialchars($item['name']); ?></td>
										<td class="px-3 md:px-4 py-1.5 md:py-2">
											<span class="text-[10px] md:text-xs font-medium <?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'text-red-800' : 'text-amber-700'; ?>">
												<?php echo htmlspecialchars($item['stock_status'] ?? 'Low Stock'); ?>
											</span>
										</td>
										<td class="px-3 md:px-4 py-1.5 md:py-2 text-gray-700 text-xs md:text-sm lg:text-base"><?php echo number_format((float)$item['quantity'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
										<td class="px-3 md:px-4 py-1.5 md:py-2 text-gray-700 text-xs md:text-sm lg:text-base"><?php echo number_format((float)$item['reorder_level'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
										<td class="px-3 md:px-4 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm lg:text-base"><?php echo number_format((float)$item['recommended_qty'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</section>
					<?php endforeach; ?>
					<div id="purchaseListFilterEmpty" class="hidden py-8 text-center text-sm text-gray-500 border border-dashed border-gray-200 rounded-2xl">
						No suppliers match the selected filter.
					</div>
				<?php else: ?>
					<div class="py-12 text-center text-gray-500">
						<i data-lucide="sparkles" class="w-10 h-10 mx-auto mb-3 text-green-500"></i>
						<p class="text-sm font-medium">All inventory items are above their reorder levels. Nothing to purchase right now.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Purchase List Customize Modal -->
<div id="purchaseCustomizeModal" class="fixed inset-0 z-50 hidden">
	<div class="absolute inset-0 bg-gray-900/70 backdrop-blur-md" data-purchase-customize-dismiss style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-gray-200 flex flex-col max-h-[85vh]">
			<div class="flex items-start justify-between gap-3 px-5 py-4 border-b">
				<div>
					<p class="text-[11px] uppercase tracking-[0.22em] text-rose-500 font-semibold">Customize Print</p>
					<h3 class="text-lg font-semibold text-gray-900" id="purchaseCustomizeSupplier">Supplier</h3>
					<p class="text-xs text-gray-600">Uncheck ingredients you don't want to include.</p>
				</div>
				<button type="button" class="text-gray-400 hover:text-gray-600" data-purchase-customize-dismiss aria-label="Close">
					<i data-lucide="x" class="w-5 h-5"></i>
				</button>
			</div>
			<div class="p-4 md:p-5 overflow-y-auto">
				<div id="purchaseCustomizeList" class="space-y-2 text-sm text-gray-700">
					<p class="text-gray-500 text-sm">Select a supplier to load items.</p>
				</div>
			</div>
			<div class="px-5 py-4 border-t flex justify-end gap-2">
				<button type="button" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50" data-purchase-customize-dismiss>Cancel</button>
				<button type="button" id="purchaseCustomizePrintBtn" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
					<i data-lucide="printer" class="w-4 h-4"></i>
					Print Selected
				</button>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	// Import CSV modal
	(function(){
		const importOpenBtns = Array.from(document.querySelectorAll('#openImportCsvModal, #openImportCsvModal2'));
		const modal = document.getElementById('importCsvModal');
		const dismissEls = modal ? modal.querySelectorAll('[data-import-dismiss]') : [];
		
		const clearOpenImportParam = () => {
			const params = new URLSearchParams(window.location.search);
			if (params.has('openImport')) {
				params.delete('openImport');
				const query = params.toString();
				const newUrl = window.location.pathname + (query ? `?${query}` : '') + window.location.hash;
				window.history.replaceState({}, '', newUrl);
			}
		};
		
		const toggleModal = (show) => {
			if (!modal) return;
			modal.classList.toggle('hidden', !show);
			document.body.classList.toggle('overflow-hidden', show);
			if (show && window.lucide) {
				window.lucide.createIcons({ elements: modal.querySelectorAll('i[data-lucide]') });
			}
			if (!show) {
				// Reset file input when closing
				const fileInput = modal.querySelector('input[type="file"]');
				if (fileInput) fileInput.value = '';
				clearOpenImportParam();
			}
		};
		
		const params = new URLSearchParams(window.location.search);
		
		// Open via button(s)
		importOpenBtns.forEach(btn => btn.addEventListener('click', () => toggleModal(true)));
		
		// Auto-open when ?openImport=1
		if (params.get('openImport') === '1') {
			toggleModal(true);
			clearOpenImportParam();
		}
		
		dismissEls.forEach(el => el.addEventListener('click', () => toggleModal(false)));
		modal?.addEventListener('click', (e) => {
			if (e.target === modal) toggleModal(false);
		});
	})();
	
	const modal = document.getElementById('purchaseListModal');
	const openBtn = document.getElementById('inventoryPurchaseListBtn');
	const printBtn = document.getElementById('purchaseListPrint');
	const modalContent = document.getElementById('purchaseListContent');
	const dismissButtons = modal ? modal.querySelectorAll('[data-purchase-list-dismiss]') : [];
	const supplierFilter = document.getElementById('purchaseListSupplierFilter');
	const supplierSections = modalContent ? Array.from(modalContent.querySelectorAll('[data-supplier]')) : [];
	const filterEmptyState = document.getElementById('purchaseListFilterEmpty');
	const customizeBtn = document.getElementById('purchaseListCustomize');
	const customizeModal = document.getElementById('purchaseCustomizeModal');
	const customizeList = document.getElementById('purchaseCustomizeList');
	const customizeSupplierLabel = document.getElementById('purchaseCustomizeSupplier');
	const customizePrintBtn = document.getElementById('purchaseCustomizePrintBtn');
	const customizeDismissButtons = customizeModal ? customizeModal.querySelectorAll('[data-purchase-customize-dismiss]') : [];
	let customizeItems = [];

	const toggleModal = (show) => {
		if (!modal) { return; }
		modal.classList.toggle('hidden', !show);
		document.body.classList.toggle('overflow-hidden', show);
	};

	const escapeHtml = (value) => {
		const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
		return String(value ?? '').replace(/[&<>"']/g, (char) => map[char]);
	};

	const applySupplierFilter = () => {
		const selected = (supplierFilter?.value || '').toLowerCase();
		let visibleCount = 0;
		supplierSections.forEach(section => {
			const matches = !selected || (section.dataset.supplier || '').toLowerCase() === selected;
			section.classList.toggle('hidden', !matches);
			if (matches) { visibleCount++; }
		});
		if (filterEmptyState) {
			filterEmptyState.classList.toggle('hidden', visibleCount > 0);
		}
	};

	supplierFilter?.addEventListener('change', applySupplierFilter);
	if (supplierFilter) {
		applySupplierFilter();
	}

	const buildItemsFromSection = (section) => {
		if (!section) { return []; }
		const rows = Array.from(section.querySelectorAll('tbody tr'));
		return rows.map(row => {
			const cells = row.querySelectorAll('td');
			return {
				name: cells[0]?.textContent.trim() || '',
				status: cells[1]?.textContent.trim() || '',
				onHand: cells[2]?.textContent.trim() || '',
				reorder: cells[3]?.textContent.trim() || '',
				recommended: cells[4]?.textContent.trim() || '',
				selected: true,
			};
		});
	};

	const renderCustomizeList = () => {
		if (!customizeList) { return; }
		customizeList.innerHTML = '';
		if (!customizeItems.length) {
			customizeList.innerHTML = '<p class="text-sm text-gray-500">No ingredients available for this supplier.</p>';
			return;
		}
		customizeItems.forEach((item, index) => {
			const row = document.createElement('label');
			row.className = 'flex items-start gap-3 p-2 border border-gray-200 rounded-lg hover:bg-gray-50';
			row.innerHTML = `
				<input type="checkbox" class="mt-1 h-4 w-4 text-rose-600 border-gray-300 rounded customize-item-toggle" data-index="${index}" ${item.selected ? 'checked' : ''}>
				<div class="flex-1">
					<p class="font-semibold text-gray-900 text-sm">${escapeHtml(item.name)}</p>
					<div class="text-[12px] text-gray-600 flex flex-wrap gap-2">
						<span>Status: ${escapeHtml(item.status)}</span>
						<span>On hand: ${escapeHtml(item.onHand)}</span>
						<span>Reorder: ${escapeHtml(item.reorder)}</span>
						<span>Recommended: ${escapeHtml(item.recommended)}</span>
					</div>
				</div>
			`;
			customizeList.appendChild(row);
		});
	};

	const openCustomizeModalForSection = (section) => {
		if (!customizeModal || !section) { return; }
		customizeItems = buildItemsFromSection(section);
		const supplierName = section.querySelector('h3')?.textContent?.trim() || 'Supplier';
		if (customizeSupplierLabel) {
			customizeSupplierLabel.textContent = supplierName;
		}
		renderCustomizeList();
		customizeModal.classList.remove('hidden');
		document.body.classList.add('overflow-hidden');
		if (window.lucide?.createIcons) {
			window.lucide.createIcons({ elements: customizeModal.querySelectorAll('i[data-lucide]') });
		}
	};

	const closeCustomizeModal = () => {
		if (!customizeModal) { return; }
		customizeModal.classList.add('hidden');
		const purchaseListOpen = modal && !modal.classList.contains('hidden');
		document.body.classList.toggle('overflow-hidden', purchaseListOpen);
	};

	customizeList?.addEventListener('change', (event) => {
		const toggle = event.target.closest('.customize-item-toggle');
		if (!toggle) { return; }
		const index = parseInt(toggle.getAttribute('data-index') || '-1', 10);
		if (Number.isInteger(index) && index >= 0 && customizeItems[index]) {
			customizeItems[index].selected = toggle.checked;
		}
	});

	customizeDismissButtons.forEach(btn => btn.addEventListener('click', closeCustomizeModal));
	customizeModal?.addEventListener('click', (event) => {
		if (event.target === customizeModal) {
			closeCustomizeModal();
		}
	});

	customizeBtn?.addEventListener('click', () => {
		if (modal && modal.classList.contains('hidden')) {
			toggleModal(true);
		}
		if (!supplierSections.length) {
			return;
		}
		const selectedKey = (supplierFilter?.value || '').toLowerCase();
		let targetSection = null;
		if (selectedKey) {
			targetSection = supplierSections.find(section => (section.dataset.supplier || '').toLowerCase() === selectedKey && !section.classList.contains('hidden'));
		} else {
			const visibleSections = supplierSections.filter(section => !section.classList.contains('hidden'));
			if (visibleSections.length > 0) {
				targetSection = visibleSections[0];
			}
		}
		if (!targetSection) {
			alert('No supplier items available to customize.');
			return;
		}
		openCustomizeModalForSection(targetSection);
	});

	document.querySelectorAll('.purchase-customize-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			if (modal && modal.classList.contains('hidden')) {
				toggleModal(true);
			}
			const supplierKey = (btn.getAttribute('data-customize-supplier') || '').toLowerCase();
			const targetSection = supplierSections.find(section => (section.dataset.supplier || '').toLowerCase() === supplierKey);
			openCustomizeModalForSection(targetSection);
		});
	});

	customizePrintBtn?.addEventListener('click', () => {
		const selectedItems = customizeItems.filter(item => item.selected);
		if (!selectedItems.length) {
			alert('Select at least one ingredient to print.');
			return;
		}
		const rowsHtml = selectedItems.map(item => `
			<tr>
				<td>${escapeHtml(item.name)}</td>
				<td>${escapeHtml(item.status)}</td>
				<td>${escapeHtml(item.onHand)}</td>
				<td>${escapeHtml(item.reorder)}</td>
				<td>${escapeHtml(item.recommended)}</td>
			</tr>
		`).join('');
		const popup = window.open('', '_blank', 'width=900,height=700');
		if (!popup) { return; }
		popup.document.write(`<html><head><title>Purchase List</title>
			<style>
				body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
				h2 { margin: 0 0 16px; font-size: 20px; }
				table { width: 100%; border-collapse: collapse; margin-top: 12px; }
				th, td { border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 13px; text-align: left; }
				th { background: #f3f4f6; }
			</style>
		</head><body>
			<h2>${escapeHtml(customizeSupplierLabel?.textContent || 'Supplier')}</h2>
			<table>
				<thead>
					<tr>
						<th>Ingredient</th>
						<th>Status</th>
						<th>On Hand</th>
						<th>Reorder Level</th>
						<th>Recommended Qty</th>
					</tr>
				</thead>
				<tbody>${rowsHtml}</tbody>
			</table>
		</body></html>`);
		popup.document.close();
		popup.focus();
		popup.print();
	});

	openBtn?.addEventListener('click', () => {
		if (openBtn.disabled) { return; }
		toggleModal(true);
	});

	modal?.addEventListener('click', (event) => {
		if (event.target === modal) {
			toggleModal(false);
		}
	});

	dismissButtons.forEach((btn) => btn.addEventListener('click', () => toggleModal(false)));

	if (printBtn && modalContent) {
		printBtn.addEventListener('click', () => {
			const popup = window.open('', '_blank', 'width=900,height=700');
			if (!popup) { return; }
			popup.document.write(`<html><head><title>Low Stock Purchase List</title>
				<style>
					body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
					h2, h3 { margin: 0 0 12px; }
					table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
					th, td { border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 13px; }
					th { background: #f3f4f6; text-align: left; }
					section { margin-bottom: 24px; }
					.hidden { display: none !important; }
				</style>
			</head><body>${modalContent.innerHTML}</body></html>`);
			popup.document.close();
			popup.focus();
			popup.print();
		});
	}
});
</script>

<!-- Delete Ingredient Confirmation Modal -->
<?php if ($canManageInventory): ?>
<div id="deleteIngredientModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
    <div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
    <div class="relative z-10 flex items-center justify-center p-4 overflow-x-hidden" style="min-height: 100vh;">
        <div class="bg-white rounded-xl shadow-none max-w-sm w-full mx-auto">
            <div class="p-4 md:p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm md:text-base font-semibold text-gray-900">Delete Ingredient</h3>
                        <p class="text-[10px] md:text-xs text-gray-600 mt-0.5">Are you sure you want to delete this ingredient from the inventory?</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2.5 md:p-3 mb-3">
                    <p class="text-xs md:text-sm font-medium text-gray-900" id="deleteIngredientName"></p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelDeleteIngredientBtn" class="inline-flex items-center justify-center px-3 md:px-4 py-1.5 md:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium text-xs md:text-sm">
                        Cancel
                    </button>
                    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/delete" id="deleteIngredientForm" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
                        <input type="hidden" name="id" id="deleteIngredientId" value="">
                        <button type="submit" id="confirmDeleteIngredientBtn" class="inline-flex items-center justify-center px-3 md:px-4 py-1.5 md:py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-xs md:text-sm">
                            Delete Ingredient
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
	(function(){
		const params = new URLSearchParams(window.location.search);
		if (params.get('focus') !== 'low-stock') { return; }
		const target = document.getElementById('inventory-low-stock');
		if (!target) { return; }
		target.classList.add('ring-2','ring-red-200','ring-offset-2');
		const lowRows = target.querySelectorAll('tr.bg-red-50');
		lowRows.forEach(row => row.classList.add('animate-pulse'));
		setTimeout(() => {
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}, 150);
	})();
	<?php if ($canManageSets): ?>
	(function(){
	const INGREDIENTS_FOR_SETS = <?php echo json_encode(array_map(static function ($ing) {
		return [
			'id' => (int)$ing['id'],
			'name' => $ing['name'],
			'unit' => $ing['unit'],
		];
	}, $ingredients), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
	const select = document.getElementById('setIngredientSelect');
	const qtyInput = document.getElementById('setIngredientQty');
	const unitBadge = document.getElementById('setIngredientUnitBadge');
	const addBtn = document.getElementById('setAddIngredientBtn');
	const tableBody = document.getElementById('setComponentsBody');
	const emptyState = document.getElementById('setComponentsEmpty');
	const countLabel = document.getElementById('setComponentCount');
	const componentsInput = document.getElementById('setComponentsJson');
	const errorBox = document.getElementById('setBuilderError');
	const setIdField = document.getElementById('setIdField');
	const setNameInput = document.getElementById('setNameInput');
	const setDescriptionInput = document.getElementById('setDescriptionInput');
	const submitBtn = document.getElementById('setBuilderSubmit');
	const submitLabel = document.getElementById('setBuilderSubmitLabel');
	const cancelEditBtn = document.getElementById('setEditCancelBtn');
	const editButtons = document.querySelectorAll('[data-set-edit]');
	if (!select || !qtyInput || !addBtn || !tableBody || !emptyState || !componentsInput || !setIdField || !setNameInput || !setDescriptionInput || !submitBtn || !submitLabel || !cancelEditBtn) { return; }

	let components = [];
	let editingSetId = 0;
	const ESCAPE_MAP = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
	const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ESCAPE_MAP[char]);

	function showError(message){
		if (!errorBox) { return; }
		errorBox.textContent = message;
		errorBox.classList.remove('hidden');
	}

	function clearError(){
		if (!errorBox) { return; }
		errorBox.textContent = '';
		errorBox.classList.add('hidden');
	}

	function render(){
		if (components.length === 0){
			emptyState.classList.remove('hidden');
		} else {
			emptyState.classList.add('hidden');
		}
		countLabel.textContent = `${components.length} ingredient${components.length === 1 ? '' : 's'}`;
		tableBody.innerHTML = components.map((component, index) => `
			<tr>
				<td class="px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm lg:text-base">${escapeHtml(component.ingredient_name)}</td>
				<td class="px-3 md:px-4 py-1.5 md:py-2 font-semibold text-xs md:text-sm lg:text-base">${Number(component.quantity).toFixed(2)} <span class="text-[10px] md:text-xs text-gray-500">${escapeHtml(component.unit)}</span></td>
				<td class="px-3 md:px-4 py-1.5 md:py-2 text-right">
					<button type="button" class="text-[10px] md:text-xs text-red-600 hover:text-red-700 whitespace-nowrap" data-remove-component data-index="${index}">Remove</button>
				</td>
			</tr>
		`).join('');
		componentsInput.value = JSON.stringify(components);
	}

	function updateUnitBadge(){
		const unit = select.selectedOptions[0]?.getAttribute('data-unit') || 'unit';
		unitBadge.textContent = unit;
	}

	function setSubmitMode(mode){
		if (mode === 'edit'){
			submitLabel.textContent = 'Update set';
			cancelEditBtn.classList.remove('hidden');
		} else {
			submitLabel.textContent = 'Save set';
			cancelEditBtn.classList.add('hidden');
		}
	}

	function enterEditMode(payload){
		if (!payload) { return; }
		editingSetId = parseInt(payload.id || '0', 10) || 0;
		setIdField.value = String(editingSetId);
		setNameInput.value = payload.name || '';
		setDescriptionInput.value = payload.description || '';
		components = Array.isArray(payload.components)
			? payload.components.map(component => ({
				ingredient_id: component.ingredient_id,
				ingredient_name: component.ingredient_name,
				unit: component.unit || 'unit',
				quantity: parseFloat(component.quantity || 0) || 0,
			}))
			: [];
		render();
		setSubmitMode('edit');
		clearError();
		setTimeout(() => setNameInput.focus(), 0);
	}

	function exitEditMode(){
		editingSetId = 0;
		setIdField.value = '0';
		setNameInput.value = '';
		setDescriptionInput.value = '';
		components = [];
		render();
		setSubmitMode('create');
		clearError();
	}

	cancelEditBtn.addEventListener('click', exitEditMode);

	editButtons.forEach(button => {
		button.addEventListener('click', ()=>{
			const payloadRaw = button.getAttribute('data-set-edit');
			if (!payloadRaw) { return; }
			try {
				const payload = JSON.parse(payloadRaw);
				enterEditMode(payload);
			} catch (err) {
				console.error('Failed to parse set payload', err);
			}
		});
	});

	addBtn.addEventListener('click', ()=>{
		const ingredientId = parseInt(select.value || '0', 10);
		const quantity = parseFloat(qtyInput.value || '0');
		if (!ingredientId || !quantity || quantity <= 0){
			showError('Select an ingredient and enter a quantity greater than zero.');
			return;
		}
		if (components.some(component => component.ingredient_id === ingredientId)){
			showError('Each ingredient can only be added once per set.');
			return;
		}
		const ingredient = INGREDIENTS_FOR_SETS.find(item => item.id === ingredientId);
		if (!ingredient){
			showError('Selected ingredient no longer exists. Refresh the page.');
			return;
		}
		components.push({
			ingredient_id: ingredientId,
			ingredient_name: ingredient.name,
			unit: ingredient.unit,
			quantity,
		});
		select.value = '';
		qtyInput.value = '';
		updateUnitBadge();
		render();
		clearError();
	});

	tableBody.addEventListener('click', (event)=>{
		const target = event.target.closest('[data-remove-component]');
		if (!target) { return; }
		const index = parseInt(target.getAttribute('data-index') || '-1', 10);
		if (index >= 0){
			components.splice(index, 1);
			render();
			clearError();
		}
	});

	select.addEventListener('change', updateUnitBadge);
	updateUnitBadge();
	render();
	setSubmitMode('create');
	})();
	<?php endif; ?>
});

// ============================================
// Add Ingredient Modal (Initialize First)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
	const addIngredientModal = document.getElementById('addIngredientModal');
	const openAddIngredientBtn = document.getElementById('openAddIngredientModal');
	const openAddIngredientBtnEmpty = document.getElementById('openAddIngredientModalEmpty');
	
	if (!addIngredientModal) {
		console.warn('Add Ingredient Modal not found');
		return;
	}
	
	const resetForm = () => {
		const form = document.getElementById('addIngredientForm');
		if (form) {
			form.reset();
			form.action = '<?php echo htmlspecialchars($baseUrl); ?>/inventory';
			document.getElementById('editIngredientId').value = '';
			document.getElementById('addIngredientModalTitle').innerHTML = '<i data-lucide="plus-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>Add New Ingredient';
			document.getElementById('addIngredientModalSubtitle').textContent = 'Add a new ingredient to your inventory';
			document.getElementById('addIngredientSubmitText').textContent = 'Add Ingredient';
			const submitIcon = document.querySelector('#addIngredientSubmitBtn i[data-lucide]');
			if (submitIcon) {
				submitIcon.setAttribute('data-lucide', 'plus');
				if (window.lucide) window.lucide.createIcons();
			}
			
			// Reset display unit dropdown
			const displayUnitSelect = document.getElementById('displayUnitSelect');
			const displayFactorContainer = document.getElementById('displayFactorContainer');
			if (displayUnitSelect) {
				displayUnitSelect.innerHTML = '<option value="">Select base unit first</option>';
				displayUnitSelect.disabled = true;
			}
			if (displayFactorContainer) {
				displayFactorContainer.style.display = 'none';
			}
		}
	};
	
	window.openAddIngredientModal = (e, editData = null) => {
		if (e) {
			e.preventDefault();
			e.stopPropagation();
		}
		resetForm();
		
		if (editData) {
			// Edit mode
			document.getElementById('editIngredientId').value = editData.id;
			document.getElementById('addIngredientForm').action = '<?php echo htmlspecialchars($baseUrl); ?>/inventory/update';
			document.getElementById('addIngredientModalTitle').innerHTML = '<i data-lucide="pencil" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600"></i>Edit Ingredient';
			document.getElementById('addIngredientModalSubtitle').textContent = 'Update ingredient details';
			document.getElementById('addIngredientSubmitText').textContent = 'Update Ingredient';
			const submitIcon = document.querySelector('#addIngredientSubmitBtn i[data-lucide]');
			if (submitIcon) {
				submitIcon.setAttribute('data-lucide', 'save');
				if (window.lucide) window.lucide.createIcons();
			}
			
			// Populate form fields
			document.querySelector('input[name="name"]').value = editData.name || '';
			document.querySelector('input[name="category"]').value = editData.category || '';
			
			// Set base unit and trigger change to populate display units
			const baseUnitSelect = document.getElementById('baseUnitSelect');
			if (baseUnitSelect) {
				baseUnitSelect.value = editData.unit || '';
				// Trigger change event to populate display unit options
				baseUnitSelect.dispatchEvent(new Event('change'));
				
				// Set display unit after options are populated
				setTimeout(() => {
					const displayUnitSelect = document.getElementById('displayUnitSelect');
					if (displayUnitSelect && editData.display_unit) {
						displayUnitSelect.value = editData.display_unit;
						displayUnitSelect.dispatchEvent(new Event('change'));
					}
					const displayFactorInput = document.getElementById('displayFactorInput');
					if (displayFactorInput) {
						displayFactorInput.value = editData.display_factor || '1';
					}
				}, 50);
			}
			
			const reorderField = document.querySelector('input[name="reorder_level"]');
			if (reorderField) {
				const reorderVal = editData.reorder_level ?? editData.reorderLevel ?? '';
				reorderField.value = reorderVal === null ? '' : reorderVal;
			}
		}
		
		addIngredientModal.classList.remove('hidden');
		document.body.classList.add('overflow-hidden');
		if (window.lucide) {
			window.lucide.createIcons({ elements: addIngredientModal.querySelectorAll('i[data-lucide]') });
		}
	};
	
	const closeModal = (e) => {
		if (e) {
			e.preventDefault();
			e.stopPropagation();
		}
		addIngredientModal.classList.add('hidden');
		document.body.classList.remove('overflow-hidden');
		resetForm();
	};
	
	if (openAddIngredientBtn) {
		openAddIngredientBtn.addEventListener('click', (e) => window.openAddIngredientModal(e));
	}
	
	if (openAddIngredientBtnEmpty) {
		openAddIngredientBtnEmpty.addEventListener('click', (e) => window.openAddIngredientModal(e));
	}
	
	const dismissButtons = addIngredientModal.querySelectorAll('[data-add-ingredient-dismiss]');
	dismissButtons.forEach(btn => {
		btn.addEventListener('click', closeModal);
	});
	
	addIngredientModal.addEventListener('click', (e) => {
		if (e.target === addIngredientModal || e.target.classList.contains('backdrop-blur-sm')) {
			closeModal();
		}
	});
	
	// Display Unit Dynamic Population based on Base Unit
	const baseUnitSelect = document.getElementById('baseUnitSelect');
	const displayUnitSelect = document.getElementById('displayUnitSelect');
	const displayFactorContainer = document.getElementById('displayFactorContainer');
	const displayFactorInput = document.getElementById('displayFactorInput');
	const displayFactorHelp = document.getElementById('displayFactorHelp');
	
	if (baseUnitSelect && displayUnitSelect) {
		// Mapping of base units to their possible display units
		const unitMapping = {
			'pcs': ['pack', 'bundle', 'dozen', 'half-dozen', 'set', 'box', 'carton', 'sleeve', 'case', 'roll', 'pair'],
			'g': ['mg', 'cg', 'dg', 'sachet', 'pack', 'bag'],
			'kg': ['g', 'mg', 'sack'],
			'ml': ['drops', 'tsp', 'tbsp', 'fl oz', 'sachet', 'bottle', 'gallon'],
			'L': ['ml', 'cups', 'pints', 'gallons'],
			'cups': ['tbsp', 'tsp', 'ml', 'L'],
			'tbsp': ['tsp', 'ml'],
			'tsp': ['drops', 'ml']
		};
		
		// Display units that require custom factor input
		const unitsRequiringFactor = {
			'pcs': ['pack', 'bundle', 'dozen', 'half-dozen', 'set', 'box', 'carton', 'sleeve', 'case', 'roll', 'pair'],
			'g': ['sachet', 'pack', 'bag'],
			'kg': ['sack'],
			'ml': ['sachet', 'bottle', 'gallon'],
			'L': ['gallons'],
			'cups': [],
			'tbsp': [],
			'tsp': ['drops']
		};
		
		// Standard conversion factors for known unit pairs
		const standardFactors = {
			'g': { 'kg': 1000, 'mg': 0.001, 'cg': 0.01, 'dg': 0.1 },
			'kg': { 'g': 0.001, 'mg': 0.000001 },
			'ml': { 'L': 1000, 'tsp': 4.92892, 'tbsp': 14.7868, 'fl oz': 29.5735 },
			'L': { 'ml': 0.001, 'cups': 0.236588, 'pints': 0.473176, 'gallons': 3.78541 },
			'cups': { 'tbsp': 16, 'tsp': 48, 'ml': 236.588, 'L': 0.236588 },
			'tbsp': { 'tsp': 3, 'ml': 14.7868 },
			'tsp': { 'ml': 4.92892, 'drops': 98.6 },
			'pcs': { 'dozen': 12, 'half-dozen': 6 }
		};
		
		function updateDisplayUnitOptions() {
			const selectedBaseUnit = baseUnitSelect.value;
			displayUnitSelect.innerHTML = '<option value="">None (use base unit)</option>';
			
			if (!selectedBaseUnit) {
				displayUnitSelect.disabled = true;
				if (displayFactorContainer) displayFactorContainer.style.display = 'none';
				return;
			}
			
			displayUnitSelect.disabled = false;
			const availableUnits = unitMapping[selectedBaseUnit] || [];
			
			availableUnits.forEach(unit => {
				const option = document.createElement('option');
				option.value = unit;
				option.textContent = unit.charAt(0).toUpperCase() + unit.slice(1).replace(/-/g, '-');
				displayUnitSelect.appendChild(option);
			});
			
			// Reset display factor when base unit changes
			if (displayFactorContainer) displayFactorContainer.style.display = 'none';
			if (displayFactorInput) displayFactorInput.value = '1';
		}
		
		function updateDisplayFactorVisibility() {
			const selectedBaseUnit = baseUnitSelect.value;
			const selectedDisplayUnit = displayUnitSelect.value;
			
			if (!selectedBaseUnit || !selectedDisplayUnit) {
				if (displayFactorContainer) displayFactorContainer.style.display = 'none';
				return;
			}
			
			// Check if this combination requires a custom factor
			const requiresFactor = unitsRequiringFactor[selectedBaseUnit]?.includes(selectedDisplayUnit) || false;
			
			if (requiresFactor) {
				if (displayFactorContainer) displayFactorContainer.style.display = 'block';
				if (displayFactorHelp) {
					displayFactorHelp.textContent = `Enter how many ${selectedBaseUnit} are in 1 ${selectedDisplayUnit} (e.g., 24 for pack where 1 pack = 24 pcs)`;
				}
				// Set default value if it's a known standard
				if (displayFactorInput && standardFactors[selectedBaseUnit]?.[selectedDisplayUnit]) {
					displayFactorInput.value = standardFactors[selectedBaseUnit][selectedDisplayUnit];
				} else if (displayFactorInput) {
					displayFactorInput.value = '1';
				}
			} else {
				// For standard conversions, set factor automatically if known
				if (standardFactors[selectedBaseUnit]?.[selectedDisplayUnit]) {
					if (displayFactorContainer) displayFactorContainer.style.display = 'block';
					if (displayFactorInput) displayFactorInput.value = standardFactors[selectedBaseUnit][selectedDisplayUnit];
					if (displayFactorHelp) {
						displayFactorHelp.textContent = `Standard conversion: 1 ${selectedDisplayUnit} = ${standardFactors[selectedBaseUnit][selectedDisplayUnit]} ${selectedBaseUnit}`;
					}
				} else {
					// For simple unit conversions or no conversion, hide factor
					if (displayFactorContainer) displayFactorContainer.style.display = 'none';
					if (displayFactorInput) displayFactorInput.value = '1';
				}
			}
		}
		
		baseUnitSelect.addEventListener('change', function() {
			updateDisplayUnitOptions();
			updateDisplayFactorVisibility();
		});
		
		displayUnitSelect.addEventListener('change', function() {
			updateDisplayFactorVisibility();
		});
		
		// Initialize on page load if base unit is already selected (edit mode)
		if (baseUnitSelect.value) {
			updateDisplayUnitOptions();
		}
	}
});

// ============================================
// Inventory Table Filtering, Sorting & Pagination
// ============================================
document.addEventListener('DOMContentLoaded', function() {
	const ITEMS_PER_PAGE = 25;
	let currentPage = 1;
	let currentSort = 'name_asc';
	let isCompactView = false;
	
	const searchInput = document.getElementById('inventorySearch');
	const categoryFilter = document.getElementById('inventoryCategoryFilter');
	const statusFilter = document.getElementById('inventoryStatusFilter');
	const sortSelect = document.getElementById('inventorySortBy');
	const viewToggle = document.getElementById('inventoryViewToggle');
	const clearFiltersBtn = document.getElementById('inventoryClearFilters');
	const tableBody = document.getElementById('inventoryTableBody');
	const emptyState = document.getElementById('inventoryEmptyState');
	const paginationContainer = document.getElementById('inventoryPagination');
	const resultsStart = document.getElementById('inventoryResultsStart');
	const resultsEnd = document.getElementById('inventoryResultsEnd');
	const resultsTotal = document.getElementById('inventoryResultsTotal');
	const totalCountEl = document.getElementById('totalIngredientsCount');
	
	// Check if required elements exist
	if (!tableBody) {
		console.warn('Inventory table body not found');
		return;
	}
	if (!searchInput) {
		console.warn('Inventory search input not found');
		return;
	}
	if (!statusFilter) {
		console.warn('Inventory status filter not found');
		return;
	}
	if (!sortSelect) {
		console.warn('Inventory sort select not found');
		return;
	}
	
	// Get ingredient data from JSON (embedded in page)
	const ingredientsData = window.INGREDIENTS_DATA || [];
	if (ingredientsData.length === 0) {
		console.warn('No ingredient data found');
		return;
	}
	
	// Convert data to filterable format
	let allIngredients = ingredientsData.map(ing => {
		const quantity = parseFloat(ing.quantity || 0);
		const reorderLevel = parseFloat(ing.reorder_level || 0);
		const low = quantity <= reorderLevel;
		const outOfStock = quantity <= 0;
		const status = outOfStock ? 'out_of_stock' : (low ? 'low' : 'in_stock');
		const category = (ing.category || '').toLowerCase();
		const supplier = (ing.preferred_supplier || '').toLowerCase();
		const nameLower = (ing.name || '').toLowerCase();
		const unitLower = (ing.unit || '').toLowerCase();
		
		// Calculate percentages
		const stockPercentage = reorderLevel > 0 ? (quantity / reorderLevel * 100) : 100;
		const recommendedQty = ing.recommended_qty || (reorderLevel * 2);
		const maxQty = Math.max(recommendedQty, quantity, reorderLevel);
		const percentage = maxQty > 0 ? Math.min(100, (quantity / maxQty) * 100) : 0;
		
		return {
			...ing,
			quantity,
			reorderLevel,
			low,
			outOfStock,
			status,
			category,
			supplier,
			nameLower, // For filtering
			unitLower, // For filtering
			stockPercentage,
			recommendedQty,
			percentage
		};
	});
	
	let filteredIngredients = [...allIngredients];
	
	function filterRows() {
		const searchTerm = searchInput.value.toLowerCase().trim();
		const categoryValue = categoryFilter ? categoryFilter.value.toLowerCase() : '';
		const statusValue = statusFilter.value;
		
		filteredIngredients = allIngredients.filter(ing => {
			// Search filter
			if (searchTerm) {
				if (!ing.nameLower.includes(searchTerm) && !ing.category.includes(searchTerm) && !ing.supplier.includes(searchTerm)) {
					return false;
				}
			}
			
			// Category filter
			if (categoryValue && categoryFilter) {
				if (ing.category !== categoryValue) {
					return false;
				}
			}
			
			// Status filter
			if (statusValue !== 'all') {
				if (statusValue === 'low') {
					// Low stock includes both 'low' and 'out_of_stock' statuses
					if (ing.status !== 'low' && ing.status !== 'out_of_stock') return false;
				} else if (statusValue === 'in_stock' && ing.status !== 'in_stock') {
					return false;
				} else if (statusValue === 'out_of_stock' && ing.status !== 'out_of_stock') {
					return false;
				}
			}
			
			return true;
		});
		
		// Sort filtered ingredients
		sortIngredients();
		
		// Calculate total pages after filtering
		const totalItems = filteredIngredients.length;
		const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
		
		// Only adjust page if absolutely necessary (no results or page beyond available)
		// Otherwise, keep the exact same page number
		if (totalPages === 0) {
			currentPage = 1; // No results, must go to page 1
		} else if (currentPage > totalPages) {
			currentPage = totalPages; // Page beyond available, go to last page
		}
		// If currentPage is valid (1 <= currentPage <= totalPages), keep it unchanged
		
		renderTable();
	}
	
	function sortIngredients() {
		const [field, direction] = currentSort.split('_');
		
		filteredIngredients.sort((a, b) => {
			let aVal, bVal;
			
			switch(field) {
				case 'name':
					aVal = a.nameLower || '';
					bVal = b.nameLower || '';
					break;
				case 'stock':
					aVal = a.quantity || 0;
					bVal = b.quantity || 0;
					break;
				case 'reorder':
					aVal = a.reorderLevel || 0;
					bVal = b.reorderLevel || 0;
					break;
				case 'unit':
					aVal = a.unitLower || '';
					bVal = b.unitLower || '';
					break;
				default:
					return 0;
			}
			
			if (typeof aVal === 'string') {
				return direction === 'asc' 
					? aVal.localeCompare(bVal)
					: bVal.localeCompare(aVal);
			} else {
				return direction === 'asc' ? aVal - bVal : bVal - aVal;
			}
		});
	}
	
	function renderTable() {
		// Calculate pagination
		const totalItems = filteredIngredients.length;
		const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
		const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
		const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, totalItems);
		
		// Get visible ingredients
		const visibleIngredients = filteredIngredients.slice(startIndex, endIndex);
		
		// Render rows
		if (visibleIngredients.length === 0) {
			tableBody.innerHTML = '';
		} else {
			tableBody.innerHTML = visibleIngredients.map(ing => renderRow(ing)).join('');
			// Initialize icons for rendered rows
			if (window.lucide) {
				window.lucide.createIcons();
			}
		}
		
		// Update results counter
		if (resultsStart) resultsStart.textContent = totalItems > 0 ? startIndex + 1 : 0;
		if (resultsEnd) resultsEnd.textContent = endIndex;
		if (resultsTotal) resultsTotal.textContent = totalItems;
		
		// Show/hide empty state
		if (emptyState) {
			emptyState.classList.toggle('hidden', totalItems > 0);
		}
		tableBody.classList.toggle('hidden', totalItems === 0);
		
		// Render pagination
		renderPagination(totalPages);
		
		// Update sort icons
		updateSortIcons();
	}
	
	function renderRow(ing) {
		const rowClass = `inventory-row hover:bg-gray-50 transition-colors ${ing.low ? 'bg-red-50' : ''} ${isCompactView ? 'compact-view' : ''}`;
		
		return `
			<tr class="${rowClass}">
				<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
					<div>
						<div class="text-[10px] md:text-xs lg:text-sm font-semibold text-gray-900">${escapeHtml(ing.name || '')}</div>
						${ing.display_unit ? `<div class="text-[9px] md:text-[10px] lg:text-xs text-gray-500 mt-0.5">Display: ${escapeHtml(ing.display_unit)}</div>` : ''}
						${ing.preferred_supplier ? `<p class="text-[9px] md:text-[10px] lg:text-xs text-gray-500 mt-1">Supplier: ${escapeHtml(ing.preferred_supplier)}</p>` : ''}
					</div>
				</td>
				<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
					<div class="space-y-1">
						<div class="flex flex-col leading-tight gap-0.5">
							<span class="inline-flex items-center gap-1 md:gap-1.5">
								<span class="font-semibold text-green-700 text-[10px] md:text-xs lg:text-sm">${formatNumber(ing.quantity)}</span>
								<span class="text-gray-500 text-[9px] md:text-[10px] lg:text-xs">${escapeHtml(ing.unit || '')}</span>
							</span>
							${(() => {
								// Show converted display unit if available
								if (ing.display_unit && ing.display_factor && ing.display_factor > 0) {
									const displayQty = ing.quantity / ing.display_factor;
									return `<span class="text-gray-400 text-[9px] md:text-[10px] lg:text-xs">(${formatNumber(displayQty)} ${escapeHtml(ing.display_unit)})</span>`;
								}
								return '';
							})()}
						</div>
					</div>
				</td>
				<td class="hidden lg:table-cell px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
					<div class="flex items-center gap-1 md:gap-1.5 lg:gap-2 flex-wrap">
						<span class="text-gray-700 text-[10px] md:text-xs lg:text-sm">${formatNumber(ing.reorderLevel)}</span>
						<span class="text-gray-500 text-[9px] md:text-[10px] lg:text-xs">${escapeHtml(ing.unit || '')}</span>
						${(() => {
							// Show converted display unit for reorder level if available
							if (ing.display_unit && ing.display_factor && ing.display_factor > 0) {
								const displayReorder = ing.reorderLevel / ing.display_factor;
								return `<span class="text-gray-400 text-[9px] md:text-[10px] lg:text-xs">(${formatNumber(displayReorder)} ${escapeHtml(ing.display_unit)})</span>`;
							}
							return '';
						})()}
					</div>
				</td>
				<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
					<span class="text-[10px] md:text-xs lg:text-sm font-medium ${ing.low ? 'text-red-600' : 'text-green-600'} whitespace-nowrap">
						${ing.low ? 'Low Stock' : 'In Stock'}
					</span>
				</td>
				${<?php echo $canManageInventory ? 'true' : 'false'; ?> ? `
				<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
					<div class="flex items-center gap-1 md:gap-2">
						${<?php echo in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true) ? 'true' : 'false'; ?> ? `
						<button type="button" class="edit-ingredient-btn inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" data-ingredient-id="${ing.id}" title="Edit ingredient">
							<i data-lucide="pencil" class="w-4 h-4 md:w-4 md:h-4"></i>
						</button>
						` : ''}
						<button type="button" class="delete-ingredient-btn inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" data-ingredient-id="${ing.id}" data-ingredient-name="${escapeHtml(ing.name || '')}" title="Delete ingredient">
							<i data-lucide="trash-2" class="w-4 h-4 md:w-4 md:h-4"></i>
						</button>
					</div>
				</td>
				` : ''}
			</tr>
		`;
	}
	
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
	
	function formatNumber(num, decimals = 2) {
		return Number(num).toLocaleString('en-US', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals
		});
	}
	
	function renderPagination(totalPages) {
		if (!paginationContainer || totalPages <= 1) {
			paginationContainer.innerHTML = '';
			return;
		}
		
		// Check if in tablet mode (768px to 1023px)
		const isTabletMode = window.matchMedia('(min-width: 768px) and (max-width: 1023px)').matches;
		
		// Hide pagination entirely in tablet mode
		if (isTabletMode) {
			paginationContainer.innerHTML = '';
			return;
		}
		
		let html = '';
		
		// Previous button
		html += `<button 
			type="button" 
			class="px-2 md:px-2.5 lg:px-3 py-1 md:py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-xs md:text-sm ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
			${currentPage === 1 ? 'disabled' : ''}
			data-page="${currentPage - 1}"
		>
			<i data-lucide="chevron-left" class="w-3 h-3 md:w-3.5 md:h-3.5 lg:w-4 lg:h-4"></i>
		</button>`;
		
		// Show 3 page numbers: current page and one on each side
		const pagesToShow = [];
		if (totalPages <= 3) {
			// If 3 or fewer pages, show all
			for (let i = 1; i <= totalPages; i++) {
				pagesToShow.push(i);
			}
		} else {
			// Show current page and adjacent pages
			if (currentPage === 1) {
				pagesToShow.push(1, 2, 3);
			} else if (currentPage === totalPages) {
				pagesToShow.push(totalPages - 2, totalPages - 1, totalPages);
			} else {
				pagesToShow.push(currentPage - 1, currentPage, currentPage + 1);
			}
		}
		
		// Render page numbers
		for (let i = 0; i < pagesToShow.length; i++) {
			const page = pagesToShow[i];
			html += `<button 
				type="button" 
				class="px-2 md:px-2.5 lg:px-3 py-1 md:py-1.5 border rounded-lg transition-colors text-xs md:text-sm ${
					page === currentPage 
						? 'bg-green-600 text-white border-green-600' 
						: 'border-gray-300 hover:bg-gray-50'
				}"
				data-page="${page}"
			>${page}</button>`;
		}
		
		// Next button
		html += `<button 
			type="button" 
			class="px-2 md:px-2.5 lg:px-3 py-1 md:py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-xs md:text-sm ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
			${currentPage === totalPages ? 'disabled' : ''}
			data-page="${currentPage + 1}"
		>
			<i data-lucide="chevron-right" class="w-3 h-3 md:w-3.5 md:h-3.5 lg:w-4 lg:h-4"></i>
		</button>`;
		
		paginationContainer.innerHTML = html;
		
		// Add event listeners to pagination buttons
		paginationContainer.querySelectorAll('button[data-page]').forEach(btn => {
			btn.addEventListener('click', () => {
				// Store current scroll position
				const tableContainer = tableBody.closest('.overflow-x-auto');
				const scrollTop = tableContainer ? tableContainer.scrollTop : 0;
				
				currentPage = parseInt(btn.dataset.page, 10);
				renderTable();
				
				// Restore scroll position to prevent column shifting
				if (tableContainer) {
					tableContainer.scrollTop = scrollTop;
				}
			});
		});
		
		if (window.lucide) {
			window.lucide.createIcons();
		}
	}
	
	function updateSortIcons() {
		const [field, direction] = currentSort.split('_');
		document.querySelectorAll('.sortable').forEach(th => {
			const icon = th.querySelector('i');
			if (!icon) return;
			
			if (th.dataset.sort === field) {
				icon.setAttribute('data-lucide', direction === 'asc' ? 'arrow-up' : 'arrow-down');
				icon.classList.remove('text-gray-400');
				icon.classList.add('text-green-600');
			} else {
				icon.setAttribute('data-lucide', 'arrow-up-down');
				icon.classList.remove('text-green-600');
				icon.classList.add('text-gray-400');
			}
		});
		if (window.lucide) {
			window.lucide.createIcons();
		}
	}
	
	function toggleCompactView() {
		isCompactView = !isCompactView;
		const icon = viewToggle.querySelector('i');
		if (icon) {
			icon.setAttribute('data-lucide', isCompactView ? 'list' : 'layout-grid');
		}
		renderTable();
		if (window.lucide) {
			window.lucide.createIcons();
		}
	}
	
	// Event listeners
	if (searchInput) {
		searchInput.addEventListener('input', () => {
			filterRows();
		});
	}
	
	if (categoryFilter) {
		categoryFilter.addEventListener('change', () => {
			filterRows();
		});
	}
	
	if (statusFilter) {
		statusFilter.addEventListener('change', () => {
			filterRows();
		});
	}
	
	if (sortSelect) {
		sortSelect.addEventListener('change', () => {
			currentSort = sortSelect.value;
			sortIngredients();
			currentPage = 1;
			renderTable();
		});
	}
	
	if (viewToggle) {
		viewToggle.addEventListener('click', toggleCompactView);
	}
	
	if (clearFiltersBtn) {
		clearFiltersBtn.addEventListener('click', () => {
			searchInput.value = '';
			if (categoryFilter) categoryFilter.value = '';
			statusFilter.value = 'all';
			sortSelect.value = 'name_asc';
			currentSort = 'name_asc';
			filterRows();
		});
	}
	
	// Sortable column headers
	document.querySelectorAll('.sortable').forEach(th => {
		th.addEventListener('click', () => {
			const field = th.dataset.sort;
			if (!field) return;
			
			const [currentField, currentDir] = currentSort.split('_');
			
			if (field === currentField) {
				currentSort = currentDir === 'asc' ? `${field}_desc` : `${field}_asc`;
			} else {
				currentSort = `${field}_asc`;
			}
			
			if (sortSelect) {
				sortSelect.value = currentSort;
			}
			sortIngredients();
			currentPage = 1;
			renderTable();
		});
	});
	
	// Edit ingredient handlers
	<?php if (in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true)): ?>
	if (tableBody) {
		tableBody.addEventListener('click', (e) => {
			const editBtn = e.target.closest('.edit-ingredient-btn');
			if (!editBtn) return;
			
			e.preventDefault();
			const ingredientId = parseInt(editBtn.dataset.ingredientId || '0', 10);
			if (!ingredientId) return;
			
			// Find the ingredient data from the current filtered/sorted list
			const ingredient = filteredIngredients.find(ing => ing.id === ingredientId);
			if (!ingredient) return;
			
			// Open modal in edit mode
			if (window.openAddIngredientModal) {
				window.openAddIngredientModal(null, {
					id: ingredient.id,
					name: ingredient.name || '',
					category: ingredient.category || '',
					unit: ingredient.unit || '',
					display_unit: ingredient.display_unit || '',
					display_factor: ingredient.display_factor || 1,
					reorder_level: ingredient.reorderLevel || 0
				});
			}
		});
	}
	<?php endif; ?>
	
	// Delete ingredient modal handlers
	<?php if ($canManageInventory): ?>
	const deleteIngredientModal = document.getElementById('deleteIngredientModal');
	const deleteIngredientName = document.getElementById('deleteIngredientName');
	const deleteIngredientId = document.getElementById('deleteIngredientId');
	const cancelDeleteIngredientBtn = document.getElementById('cancelDeleteIngredientBtn');
	
	function openDeleteModal(ingredientId, ingredientName) {
		if (!deleteIngredientModal || !deleteIngredientName || !deleteIngredientId) return;
		deleteIngredientName.textContent = ingredientName;
		deleteIngredientId.value = ingredientId;
		deleteIngredientModal.classList.remove('hidden');
		if (window.lucide?.createIcons) {
			window.lucide.createIcons({ elements: deleteIngredientModal.querySelectorAll('i[data-lucide]') });
		}
	}
	
	function closeDeleteModal() {
		if (deleteIngredientModal) deleteIngredientModal.classList.add('hidden');
	}
	
	// Handle delete button clicks (delegated event listener on table body)
	if (tableBody) {
		tableBody.addEventListener('click', (e) => {
			const deleteBtn = e.target.closest('.delete-ingredient-btn');
			if (deleteBtn) {
				e.preventDefault();
				const ingredientId = deleteBtn.getAttribute('data-ingredient-id');
				const ingredientName = deleteBtn.getAttribute('data-ingredient-name');
				if (ingredientId && ingredientName) {
					openDeleteModal(ingredientId, ingredientName);
				}
			}
		});
	}
	
	if (cancelDeleteIngredientBtn) {
		cancelDeleteIngredientBtn.addEventListener('click', closeDeleteModal);
	}
	
	if (deleteIngredientModal) {
		deleteIngredientModal.addEventListener('click', (e) => {
			if (e.target === deleteIngredientModal || e.target.classList.contains('bg-black')) {
				closeDeleteModal();
			}
		});
	}
	<?php endif; ?>
	
	// Initialize
	sortIngredients();
	renderTable();
	
	// Re-render pagination on window resize to adapt to screen size
	let resizeTimeout;
	window.addEventListener('resize', () => {
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(() => {
			const totalItems = filteredIngredients.length;
			const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
			renderPagination(totalPages);
		}, 150);
	});
	
	// Add compact view styles
	const style = document.createElement('style');
	style.textContent = `
		.inventory-row.compact-view td {
			padding-top: 0.5rem;
			padding-bottom: 0.5rem;
		}
		.inventory-row.compact-view .w-10 {
			width: 1.5rem;
			height: 1.5rem;
		}
		.inventory-row.compact-view .w-10 i {
			width: 0.875rem;
			height: 0.875rem;
		}
		.inventory-row {
			transition: opacity 0.2s, transform 0.2s;
		}
		.inventory-row[style*="display: none"] {
			display: none !important;
		}
		#inventoryTableBody td {
			word-wrap: break-word;
			overflow-wrap: break-word;
			word-break: break-word;
		}
		#inventoryTableBody th {
			word-wrap: break-word;
			overflow-wrap: break-word;
			word-break: break-word;
		}
		/* Tablet tuning for inventory table */
		@media (min-width: 768px) and (max-width: 1023px) {
			/* Let table breathe and scroll instead of clipping */
			#inventory-low-stock .overflow-x-auto {
				overflow-x: auto !important;
			}
			#inventory-low-stock table {
				table-layout: auto !important;
				width: 100% !important;
				min-width: 100% !important;
			}
			#inventoryTableBody td,
			#inventoryTableBody th {
				padding-left: 0.5rem !important;
				padding-right: 0.5rem !important;
				padding-top: 0.5rem !important;
				padding-bottom: 0.5rem !important;
				font-size: 11px !important;
			}
			#inventoryTableBody .text-xs,
			#inventoryTableBody .text-[10px],
			#inventoryTableBody .text-[9px] {
				font-size: 11px !important;
			}
			#inventoryTableBody .text-sm {
				font-size: 12px !important;
			}
			#inventoryTableBody .w-8.h-8 {
				width: 1.75rem !important;
				height: 1.75rem !important;
			}
			#inventoryTableBody .w-3\.5.h-3\.5,
			#inventoryTableBody .w-3.h-3,
			#inventoryTableBody .w-4.h-4 {
				width: 0.9rem !important;
				height: 0.9rem !important;
			}
		}
		/* Reduce padding on tablet for better fit */
		@media (min-width: 768px) and (max-width: 1023px) {
			#inventoryTableBody td,
			#inventoryTableBody th {
				padding-left: 0.75rem !important;
				padding-right: 0.75rem !important;
			}
		}
		#inventoryPagination {
			flex-wrap: wrap;
			justify-content: center;
		}
		@media (min-width: 768px) and (max-width: 1023px) {
			#inventoryPagination {
				flex-wrap: nowrap;
			}
		}
		@media (min-width: 1024px) {
			#inventoryPagination {
				flex-wrap: nowrap;
			}
		}
		/* Column widths for desktop */
		<?php if ($canManageInventory): ?>
		table colgroup .col-ingredient { width: 28%; }
		table colgroup .col-stock { width: 28%; }
		table colgroup .col-reorder { width: 18%; }
		table colgroup .col-status { width: 18%; }
		table colgroup .col-action { width: 8%; }
		<?php else: ?>
		table colgroup .col-ingredient { width: 30%; }
		table colgroup .col-stock { width: 30%; }
		table colgroup .col-reorder { width: 20%; }
		table colgroup .col-status { width: 20%; }
		<?php endif; ?>
		/* Column widths for tablet (768px - 1023px) */
		@media (min-width: 768px) and (max-width: 1023px) {
			<?php if ($canManageInventory): ?>
			table colgroup .col-ingredient { width: 35%; }
			table colgroup .col-stock { width: 35%; }
			table colgroup .col-reorder { width: 0%; display: none; }
			table colgroup .col-status { width: 22%; }
			table colgroup .col-action { width: 8%; }
			<?php else: ?>
			table colgroup .col-ingredient { width: 40%; }
			table colgroup .col-stock { width: 40%; }
			table colgroup .col-reorder { width: 0%; display: none; }
			table colgroup .col-status { width: 20%; }
			<?php endif; ?>
		}
	`;
	if (!document.getElementById('inventory-compact-styles')) {
		style.id = 'inventory-compact-styles';
		document.head.appendChild(style);
	}
});
</script>

<!-- Add Ingredient Modal -->
<?php if (in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true)): ?>
<div id="addIngredientModal" class="fixed inset-0 z-50 hidden">
	<div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" data-add-ingredient-dismiss></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-3xl bg-white rounded-2xl shadow-none border border-gray-200 flex flex-col max-h-[90vh]">
			<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4 px-4 md:px-5 lg:px-6 py-4 md:py-5 border-b">
				<div>
					<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5" id="addIngredientModalTitle">
						<i data-lucide="plus-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
						Add New Ingredient
					</h2>
					<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1" id="addIngredientModalSubtitle">Add a new ingredient to your inventory</p>
				</div>
				<button type="button" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0" data-add-ingredient-dismiss>
					<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
				</button>
			</div>
			
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory" id="addIngredientForm" class="p-4 md:p-5 lg:p-6 overflow-y-auto">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="id" id="editIngredientId" value="">
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 lg:gap-5">
					<div class="space-y-1.5 md:space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Ingredient Name <span class="text-red-500">*</span></label>
						<input name="name" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm" placeholder="e.g., Flour, Sugar" required />
					</div>
					<div class="space-y-1.5 md:space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Category</label>
						<input name="category" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm" placeholder="e.g., Dry Goods, Packaging" />
					</div>
					
					<div class="space-y-1.5 md:space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Base Unit <span class="text-red-500">*</span></label>
						<select name="unit" id="baseUnitSelect" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm" required>
							<option value="">Select unit</option>
							<option value="g">Grams (g)</option>
							<option value="kg">Kilograms (kg)</option>
							<option value="ml">Milliliters (ml)</option>
							<option value="L">Liters (L)</option>
							<option value="pcs">Pieces (pcs)</option>
							<option value="cups">Cups</option>
							<option value="tbsp">Tablespoons</option>
							<option value="tsp">Teaspoons</option>
						</select>
					</div>
					
					<div class="space-y-1.5 md:space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700 flex items-center gap-1">
							Display Unit
							<span class="group relative">
								<i data-lucide="help-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400 cursor-help"></i>
								<span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] md:text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
									How the unit appears in reports (e.g., "kg" instead of "g")
									<span class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></span>
								</span>
							</span>
						</label>
						<select name="display_unit" id="displayUnitSelect" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" disabled>
							<option value="">Select base unit first</option>
						</select>
						<p class="text-[10px] md:text-xs text-gray-500">Optional: How it appears in reports</p>
					</div>
					
					<div class="space-y-1.5 md:space-y-2" id="displayFactorContainer" style="display: none;">
						<label class="block text-xs md:text-sm font-medium text-gray-700 flex items-center gap-1">
							Display Factor
							<span class="group relative">
								<i data-lucide="help-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400 cursor-help"></i>
								<span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 p-2 bg-gray-900 text-white text-[10px] md:text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
									Conversion factor (e.g., 1000 for g→kg, 24 for pcs→pack where 1 pack = 24 pcs)
									<span class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></span>
								</span>
							</span>
						</label>
						<input type="number" step="0.01" min="0.01" name="display_factor" id="displayFactorInput" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="1" />
						<p class="text-[10px] md:text-xs text-gray-500" id="displayFactorHelp">Conversion factor (e.g., 1000 for g→kg)</p>
					</div>
					
					<div class="space-y-1.5 md:space-y-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700">Reorder Level <span class="text-red-500">*</span></label>
						<input type="number" step="0.01" min="0" name="reorder_level" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="" placeholder="Enter reorder level" required />
						<p class="text-[10px] md:text-xs text-gray-500">Minimum stock level</p>
					</div>
				</div>
				
				<div class="mt-4 md:mt-6 flex flex-col sm:flex-row justify-end gap-2 md:gap-3">
					<button type="button" class="inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-xs md:text-sm" data-add-ingredient-dismiss>
						Cancel
					</button>
					<button type="submit" id="addIngredientSubmitBtn" class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
						<i data-lucide="plus" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						<span id="addIngredientSubmitText">Add Ingredient</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>

<style>
	/* Remove focus ring and border color for ALL input fields in add ingredient modal on tablet mode */
	@media (min-width: 768px) and (max-width: 1023px) {
		#addIngredientModal input[type="text"]:focus,
		#addIngredientModal input[type="number"]:focus,
		#addIngredientModal input[type="email"]:focus,
		#addIngredientModal input[type="password"]:focus,
		#addIngredientModal input:focus,
		#addIngredientModal select:focus,
		#addIngredientModal textarea:focus {
			outline: none !important;
			box-shadow: none !important;
			border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
			--tw-ring-offset-shadow: 0 0 #0000 !important;
			--tw-ring-shadow: 0 0 #0000 !important;
			--tw-ring-offset-width: 0px !important;
			--tw-ring-width: 0px !important;
		}
		
		/* Remove focus ring and blue border for input fields in current inventory section on tablet mode */
		#inventory-low-stock input:focus,
		#inventory-low-stock select:focus,
		#inventory-low-stock textarea:focus {
			outline: none !important;
			box-shadow: none !important;
			border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
			--tw-ring-offset-shadow: 0 0 #0000 !important;
			--tw-ring-shadow: 0 0 #0000 !important;
			--tw-ring-offset-width: 0px !important;
			--tw-ring-width: 0px !important;
		}
		
		/* Remove focus ring and border color for input fields in purchase list modal on tablet mode */
		#purchaseListModal input:focus,
		#purchaseListModal select:focus,
		#purchaseListModal textarea:focus {
			outline: none !important;
			box-shadow: none !important;
			border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
			--tw-ring-offset-shadow: 0 0 #0000 !important;
			--tw-ring-shadow: 0 0 #0000 !important;
			--tw-ring-offset-width: 0px !important;
			--tw-ring-width: 0px !important;
		}
		
		/* Remove focus ring and indigo border for input fields in ingredient sets form on tablet mode */
		#setBuilderForm input:focus,
		#setBuilderForm select:focus,
		#setBuilderForm textarea:focus {
			outline: none !important;
			box-shadow: none !important;
			border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
			--tw-ring-offset-shadow: 0 0 #0000 !important;
			--tw-ring-shadow: 0 0 #0000 !important;
			--tw-ring-offset-width: 0px !important;
			--tw-ring-width: 0px !important;
		}
	}
</style>

<!-- Ingredient Sets -->
<?php 
// Check if ingredient sets feature is enabled
// The controller passes $ingredientSetsEnabled as a boolean
// If not set, check setting directly (shouldn't happen, but safety check)
if (!isset($ingredientSetsEnabled)) {
	$ingredientSetsEnabled = (Settings::get('features.ingredient_sets_enabled', '1') === '1');
}
?>
<?php if ($ingredientSetsEnabled === true): ?>
<div class="bg-white rounded-2xl shadow-none border border-gray-200 mb-6 md:mb-8 overflow-hidden max-w-full w-full">
	<div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="layers" class="w-5 h-5 text-indigo-600"></i>
				Ingredient Sets
			</h2>
			<p class="text-xs md:text-sm text-gray-600 mt-1">Combine multiple ingredients into a reusable set for kitchen requests.</p>
		</div>
		<span class="text-sm text-gray-500"><?php echo count($ingredientSets ?? []); ?> defined</span>
	</div>
	<div class="p-4 md:p-5 lg:p-6 <?php echo $canManageSets ? 'grid gap-4 md:gap-5 lg:gap-6 md:grid-cols-2' : ''; ?>">
		<?php if ($canManageSets): ?>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set" id="setBuilderForm" class="space-y-4 md:space-y-5">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="set_id" id="setIdField" value="0">
			<div>
				<label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Set name</label>
				<input id="setNameInput" name="set_name" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm" placeholder="e.g., Chocolate Cake Kit" required>
			</div>
			<div>
				<label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Description <span class="text-xs text-gray-400">(optional)</span></label>
				<textarea id="setDescriptionInput" name="set_description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm" placeholder="Short notes for the team"></textarea>
			</div>
			<div class="space-y-3 md:space-y-4">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
					<div class="md:col-span-2">
						<label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Ingredient</label>
						<select id="setIngredientSelect" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
							<option value="">Select ingredient</option>
							<?php foreach ($ingredients as $ing): ?>
								<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>">
									<?php echo htmlspecialchars($ing['name'] . ' (' . $ing['unit'] . ')'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Quantity</label>
						<div class="relative">
							<input type="number" id="setIngredientQty" min="0.01" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 pr-12 md:pr-16 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="0.00">
							<span id="setIngredientUnitBadge" class="absolute inset-y-0 right-2 md:right-3 flex items-center text-xs font-semibold text-gray-500">unit</span>
						</div>
					</div>
				</div>
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 md:gap-3">
					<p class="text-xs text-gray-500">Quantities are stored using each ingredient's base unit.</p>
					<button type="button" id="setAddIngredientBtn" class="inline-flex items-center gap-1.5 md:gap-2 px-3 md:px-4 py-1.5 md:py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors text-xs md:text-sm whitespace-nowrap">
						<i data-lucide="plus" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						Add to set
					</button>
				</div>
				<div id="setBuilderError" class="hidden px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg"></div>
			</div>
			<div class="border rounded-xl">
				<div class="bg-gray-50 px-3 md:px-4 py-2.5 md:py-3 flex items-center justify-between">
					<h3 class="text-xs md:text-sm font-semibold text-gray-700">Ingredients in this set</h3>
					<span id="setComponentCount" class="text-xs text-gray-500">0 ingredients</span>
				</div>
				<div class="overflow-x-auto">
					<table class="w-full text-xs md:text-sm">
						<thead class="bg-white">
							<tr>
								<th class="px-3 md:px-4 py-1.5 md:py-2 text-left font-medium text-gray-600 text-[11px] md:text-xs lg:text-sm">Ingredient</th>
								<th class="px-3 md:px-4 py-1.5 md:py-2 text-left font-medium text-gray-600 text-[11px] md:text-xs lg:text-sm">Quantity</th>
								<th class="px-3 md:px-4 py-1.5 md:py-2"></th>
							</tr>
						</thead>
						<tbody id="setComponentsBody"></tbody>
					</table>
					<div id="setComponentsEmpty" class="px-3 md:px-4 py-5 md:py-6 text-center text-xs md:text-sm text-gray-500">No ingredients added yet.</div>
				</div>
			</div>
			<input type="hidden" name="components_json" id="setComponentsJson" value="[]">
			<div class="flex flex-col sm:flex-row justify-end gap-2 md:gap-3">
				<button type="button" id="setEditCancelBtn" class="inline-flex items-center gap-1.5 md:gap-2 bg-gray-200 text-gray-700 px-3 md:px-4 py-2 md:py-2.5 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors text-xs md:text-sm hidden">
					Cancel edit
				</button>
				<button type="submit" id="setBuilderSubmit" class="inline-flex items-center gap-1.5 md:gap-2 bg-green-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="check" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					<span id="setBuilderSubmitLabel">Save set</span>
				</button>
			</div>
		</form>
		<?php endif; ?>
		<div class="space-y-3 md:space-y-4">
			<?php if (empty($ingredientSets)): ?>
				<div class="rounded-xl border border-dashed border-gray-300 p-4 md:p-6 text-center text-gray-500">
					<i data-lucide="archive" class="w-8 h-8 mx-auto mb-2 md:mb-3 text-gray-400"></i>
					<p class="text-xs md:text-sm">No sets defined yet. <?php echo $canManageSets ? 'Use the form to create your first set.' : 'Ask a manager to define sets.'; ?></p>
				</div>
			<?php endif; ?>
			<?php foreach ($ingredientSets as $set): 
				$componentCount = count($set['components']);
				$lowComponent = null;
				foreach ($set['components'] as $component) {
					if ((float)($component['inventory_quantity'] ?? 0) <= (float)($component['reorder_level'] ?? 0)) {
						$lowComponent = $component;
						break;
					}
				}
				$setPayload = [
					'id' => (int)$set['id'],
					'name' => $set['name'],
					'description' => $set['description'],
					'components' => array_map(static function ($component) {
						return [
							'ingredient_id' => (int)$component['ingredient_id'],
							'ingredient_name' => $component['ingredient_name'],
							'unit' => $component['unit'] ?? $component['ingredient_unit'] ?? '',
							'quantity' => (float)$component['quantity'],
						];
					}, $set['components']),
				];
			?>
			<div class="border rounded-2xl p-4 md:p-5 space-y-3 md:space-y-4 bg-white shadow-none">
				<div class="flex flex-col sm:flex-row items-start sm:items-start justify-between gap-3 md:gap-4">
					<div class="flex-1 min-w-0">
						<div class="flex flex-col sm:flex-row sm:items-center gap-2 md:gap-3 flex-wrap">
							<h3 class="text-base md:text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($set['name']); ?></h3>
							<span class="inline-flex items-center gap-1 text-xs font-semibold px-2 md:px-2.5 py-0.5 md:py-1 rounded-full <?php echo $lowComponent ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-700'; ?> whitespace-nowrap">
								<i data-lucide="<?php echo $lowComponent ? 'alert-triangle' : 'check-circle'; ?>" class="w-3 h-3"></i>
								<?php echo $lowComponent ? 'Needs restock' : 'Kitchen-ready'; ?>
							</span>
							<span class="text-xs text-gray-500 whitespace-nowrap"><?php echo $componentCount; ?> ingredient<?php echo $componentCount === 1 ? '' : 's'; ?></span>
						</div>
						<?php if (!empty($set['description'])): ?>
							<p class="text-xs md:text-sm text-gray-600 mt-1.5 md:mt-2"><?php echo htmlspecialchars($set['description']); ?></p>
						<?php endif; ?>
					</div>
					<?php if ($canManageSets): ?>
					<div class="flex items-center gap-1.5 md:gap-2 shrink-0">
						<button type="button" data-set-edit="<?php echo htmlspecialchars(json_encode($setPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>" class="text-xs text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 px-2.5 md:px-3 py-1 md:py-1.5 border border-indigo-200 rounded-lg bg-indigo-50 whitespace-nowrap">
							<i data-lucide="edit-3" class="w-3 h-3"></i>
							Edit
						</button>
						<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set/delete" class="shrink-0" data-confirm="Are you sure you want to delete the ingredient set '<?php echo htmlspecialchars($set['name']); ?>'? This action cannot be undone." data-confirm-type="danger">
							<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
							<input type="hidden" name="set_id" value="<?php echo (int)$set['id']; ?>">
							<button type="submit" class="text-xs text-red-600 hover:text-red-700 inline-flex items-center gap-1 px-2.5 md:px-3 py-1 md:py-1.5 border border-red-200 rounded-lg whitespace-nowrap">
								<i data-lucide="trash-2" class="w-3 h-3"></i>
								Delete
							</button>
						</form>
					</div>
					<?php endif; ?>
				</div>
				<ul class="space-y-1.5 md:space-y-2 text-xs md:text-sm">
					<?php foreach ($set['components'] as $component): ?>
						<li class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1 sm:gap-2 text-gray-700">
							<span class="flex items-center gap-1.5 md:gap-2">
								<i data-lucide="dot" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400 shrink-0"></i>
								<span class="break-words"><?php echo htmlspecialchars($component['ingredient_name']); ?></span>
							</span>
							<span class="font-medium whitespace-nowrap ml-5 sm:ml-0">
								<?php echo number_format((float)$component['quantity'], 2); ?>
								<span class="text-xs text-gray-500"><?php echo htmlspecialchars($component['unit']); ?></span>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ($lowComponent): ?>
					<div class="text-xs md:text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
						<?php echo htmlspecialchars($lowComponent['ingredient_name']); ?> is currently at or below its reorder level.
					</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<script>
// Embed ingredient data as JSON for client-side rendering
window.INGREDIENTS_DATA = <?php echo json_encode(array_map(function($ing) {
	$low = (float)$ing['quantity'] <= (float)$ing['reorder_level'];
	$outOfStock = (float)$ing['quantity'] <= 0;
	$stockPercentage = $ing['reorder_level'] > 0 ? ((float)$ing['quantity'] / (float)$ing['reorder_level'] * 100) : 100;
	$currentQty = (float)$ing['quantity'];
	$reorderLevel = (float)$ing['reorder_level'];
	$recommendedQty = $reorderLevel * 2;
	$maxQty = max($recommendedQty, $currentQty, $reorderLevel);
	$percentage = $maxQty > 0 ? min(100, ($currentQty / $maxQty) * 100) : 0;
	
	return [
		'id' => (int)$ing['id'],
		'name' => $ing['name'] ?? '',
		'category' => $ing['category'] ?? '',
		'unit' => $ing['unit'] ?? '',
		'display_unit' => $ing['display_unit'] ?? null,
		'display_factor' => (float)($ing['display_factor'] ?? 1),
		'quantity' => $currentQty,
		'reorder_level' => $reorderLevel,
		'preferred_supplier' => $ing['preferred_supplier'] ?? '',
		'recommended_qty' => $recommendedQty,
		'percentage' => $percentage,
		'stock_percentage' => $stockPercentage
	];
}, $ingredients), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
