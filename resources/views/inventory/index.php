<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
		<p class="text-gray-600 mt-1">Track and manage ingredient stock levels</p>
	</div>
	<a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<!-- Add Ingredient Form -->
<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="plus-circle" class="w-5 h-5 text-green-600"></i>
			Add New Ingredient
		</h2>
		<p class="text-sm text-gray-600 mt-1">Add a new ingredient to your inventory</p>
	</div>
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory" class="p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Ingredient Name</label>
				<input name="name" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="e.g., Flour, Sugar" required />
			</div>
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Base Unit</label>
				<select name="unit" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" required>
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
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Display Unit</label>
				<input name="display_unit" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="kg, L (optional)" />
				<p class="text-xs text-gray-500">Optional: How it appears in reports</p>
			</div>
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Display Factor</label>
				<input type="number" step="0.01" min="0.01" name="display_factor" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="1" />
				<p class="text-xs text-gray-500">Conversion factor</p>
			</div>
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Reorder Level</label>
				<input type="number" step="0.01" min="0" name="reorder_level" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="0" />
				<p class="text-xs text-gray-500">Minimum stock level</p>
			</div>
		</div>
		
		<div class="mt-6 flex justify-end">
			<button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Add Ingredient
			</button>
		</div>
	</form>
</div>
<?php endif; ?>

<!-- Inventory Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
					Current Inventory
				</h2>
				<p class="text-sm text-gray-600 mt-1">View and monitor all ingredient stock levels</p>
			</div>
			<div class="flex items-center gap-4">
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($ingredients); ?></span> total ingredients
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
					<div class="flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
						<i data-lucide="alert-triangle" class="w-4 h-4"></i>
						<?php echo $lowStockCount; ?> low stock
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Ingredient</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Unit</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Current Stock</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Reorder Level</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Status</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Stock Level</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($ingredients as $ing): 
					$low = (float)$ing['quantity'] <= (float)$ing['reorder_level'];
					$stockPercentage = $ing['reorder_level'] > 0 ? min(100, (float)$ing['quantity'] / (float)$ing['reorder_level'] * 100) : 100;
				?>
				<tr class="hover:bg-gray-50 transition-colors <?php echo $low ? 'bg-red-50' : ''; ?>">
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
								<i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
							</div>
							<div>
								<div class="font-medium text-gray-900"><?php echo htmlspecialchars($ing['name']); ?></div>
								<?php if (!empty($ing['display_unit'])): ?>
									<div class="text-xs text-gray-500">Display: <?php echo htmlspecialchars($ing['display_unit']); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
							<i data-lucide="ruler" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($ing['unit']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<span class="font-semibold text-gray-900"><?php echo number_format((float)$ing['quantity'], 2); ?></span>
							<span class="text-gray-500 text-sm"><?php echo htmlspecialchars($ing['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<span class="text-gray-700"><?php echo number_format((float)$ing['reorder_level'], 2); ?></span>
							<span class="text-gray-500 text-sm"><?php echo htmlspecialchars($ing['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<?php if ($low): ?>
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
								<i data-lucide="alert-triangle" class="w-3 h-3"></i>
								Low Stock
							</span>
						<?php else: ?>
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
								<i data-lucide="check-circle" class="w-3 h-3"></i>
								In Stock
							</span>
						<?php endif; ?>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="flex-1 bg-gray-200 rounded-full h-2">
								<div class="h-2 rounded-full transition-all duration-300 <?php echo $low ? 'bg-red-500' : ($stockPercentage > 200 ? 'bg-green-500' : 'bg-yellow-500'); ?>" 
									 style="width: <?php echo min(100, $stockPercentage); ?>%"></div>
							</div>
							<span class="text-xs text-gray-600 font-medium min-w-[3rem]">
								<?php echo number_format($stockPercentage, 0); ?>%
							</span>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<?php if (empty($ingredients)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="package-x" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Ingredients Found</h3>
			<p class="text-sm text-gray-600 mb-4">Start by adding your first ingredient to the inventory</p>
			<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
				<button onclick="document.querySelector('form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
					<i data-lucide="plus" class="w-4 h-4"></i>
					Add First Ingredient
				</button>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<!-- Summary Cards -->
<?php if (!empty($ingredients)): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
	<!-- Total Ingredients -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Ingredients</p>
				<p class="text-2xl font-bold text-gray-900"><?php echo count($ingredients); ?></p>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
				<i data-lucide="layers" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Low Stock Items -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Low Stock Items</p>
				<p class="text-2xl font-bold text-red-600"><?php echo $lowStockCount; ?></p>
			</div>
			<div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
				<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
			</div>
		</div>
	</div>
	
	<!-- In Stock Items -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">In Stock Items</p>
				<p class="text-2xl font-bold text-green-600"><?php echo count($ingredients) - $lowStockCount; ?></p>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
				<i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>


