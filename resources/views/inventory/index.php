<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$ingredientSets = $ingredientSets ?? [];
$lowStockGroups = $lowStockGroups ?? [];
$canManageSets = in_array(Auth::role(), ['Owner','Manager'], true);
$canManageInventory = in_array(Auth::role(), ['Owner','Manager'], true);
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
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
		<p class="text-gray-600 mt-1">Track and manage ingredient stock levels</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<?php if (!empty($flash)): ?>
	<div class="mb-6 px-4 py-3 rounded-xl border <?php echo ($flash['type'] ?? '') === 'success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800'; ?>">
		<div class="flex items-start gap-3">
			<i data-lucide="<?php echo ($flash['type'] ?? '') === 'success' ? 'check-circle' : 'alert-circle'; ?>" class="w-4 h-4 mt-0.5"></i>
			<p class="text-sm font-medium"><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
		</div>
	</div>
<?php endif; ?>

<!-- Purchase List Modal -->
<div id="purchaseListModal" class="fixed inset-0 z-50 hidden">
	<div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" data-purchase-list-dismiss></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col max-h-[85vh]">
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-6 py-5 border-b">
				<div>
					<p class="text-xs uppercase tracking-[0.35em] text-rose-400 font-semibold">Low Stock</p>
					<h2 class="text-2xl font-semibold text-gray-900">Purchase Preparation List</h2>
					<p class="text-sm text-gray-600 mt-1">Ingredients at or below their reorder level, grouped by supplier.</p>
				</div>
				<div class="flex flex-col sm:flex-row gap-2 sm:items-center">
					<?php if (!empty($supplierFilterOptions)): ?>
					<div class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-700">
						<i data-lucide="filter" class="w-4 h-4 text-gray-500"></i>
						<select id="purchaseListSupplierFilter" class="bg-transparent border-none focus:border-none focus:ring-0 text-sm text-gray-900">
							<option value="">All suppliers</option>
							<?php foreach ($supplierFilterOptions as $key => $label): ?>
								<option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; ?>
					<button type="button" id="purchaseListPrint" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold shadow-sm hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
						<i data-lucide="printer" class="w-4 h-4"></i>
						Print List
					</button>
					<button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-purchase-list-dismiss>
						<i data-lucide="x" class="w-4 h-4"></i>
						Close
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
					<section class="border rounded-2xl p-4 sm:p-6 shadow-sm bg-white" data-supplier="<?php echo htmlspecialchars($supplierKey); ?>">
						<div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
							<div>
								<p class="text-xs uppercase tracking-wide text-gray-500">Supplier</p>
								<h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($supplier); ?></h3>
							</div>
							<span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium"><?php echo count($items); ?> item<?php echo count($items) === 1 ? '' : 's'; ?></span>
						</div>
						<div class="mt-4 overflow-x-auto">
							<table class="w-full text-sm min-w-[520px]">
								<thead class="bg-gray-50">
									<tr>
										<th class="text-left px-4 py-2 font-medium text-gray-700">Ingredient</th>
										<th class="text-left px-4 py-2 font-medium text-gray-700">Status</th>
										<th class="text-left px-4 py-2 font-medium text-gray-700">On Hand</th>
										<th class="text-left px-4 py-2 font-medium text-gray-700">Reorder Level</th>
										<th class="text-left px-4 py-2 font-medium text-gray-700">Recommended Qty</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									<?php foreach ($items as $item): ?>
									<tr>
										<td class="px-4 py-2 font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></td>
										<td class="px-4 py-2">
											<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium <?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200'; ?>">
												<i data-lucide="<?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'x' : 'alert-triangle'; ?>" class="w-3 h-3"></i>
												<?php echo htmlspecialchars($item['stock_status'] ?? 'Low Stock'); ?>
											</span>
										</td>
										<td class="px-4 py-2 text-gray-700"><?php echo number_format((float)$item['quantity'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
										<td class="px-4 py-2 text-gray-700"><?php echo number_format((float)$item['reorder_level'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
										<td class="px-4 py-2 font-semibold text-gray-900"><?php echo number_format((float)$item['recommended_qty'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
	const modal = document.getElementById('purchaseListModal');
	const openBtn = document.getElementById('inventoryPurchaseListBtn');
	const printBtn = document.getElementById('purchaseListPrint');
	const modalContent = document.getElementById('purchaseListContent');
	const dismissButtons = modal ? modal.querySelectorAll('[data-purchase-list-dismiss]') : [];
	const supplierFilter = document.getElementById('purchaseListSupplierFilter');
	const supplierSections = modalContent ? Array.from(modalContent.querySelectorAll('[data-supplier]')) : [];
	const filterEmptyState = document.getElementById('purchaseListFilterEmpty');

	const toggleModal = (show) => {
		if (!modal) { return; }
		modal.classList.toggle('hidden', !show);
		document.body.classList.toggle('overflow-hidden', show);
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
				<td class="px-4 py-2">${escapeHtml(component.ingredient_name)}</td>
				<td class="px-4 py-2 font-semibold">${Number(component.quantity).toFixed(2)} <span class="text-xs text-gray-500">${escapeHtml(component.unit)}</span></td>
				<td class="px-4 py-2 text-right">
					<button type="button" class="text-xs text-red-600 hover:text-red-700" data-remove-component data-index="${index}">Remove</button>
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
</script>

<!-- Add Ingredient Form -->
<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="plus-circle" class="w-5 h-5 text-green-600"></i>
			Add New Ingredient
		</h2>
		<p class="text-sm text-gray-600 mt-1">Add a new ingredient to your inventory</p>
	</div>
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory" class="p-4 sm:p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-6">
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

			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Preferred Supplier</label>
				<input name="preferred_supplier" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="e.g., ABC Foods">
				<p class="text-xs text-gray-500">Appears on auto-generated purchase list</p>
			</div>

			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Restock Quantity</label>
				<input type="number" step="0.01" min="0" name="restock_quantity" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="0" />
				<p class="text-xs text-gray-500">Recommended amount to purchase</p>
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

<!-- Ingredient Sets -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-4 sm:px-6 py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="layers" class="w-5 h-5 text-indigo-600"></i>
				Ingredient Sets
			</h2>
			<p class="text-sm text-gray-600 mt-1">Combine multiple ingredients into a reusable set for kitchen requests.</p>
		</div>
		<span class="text-sm text-gray-500"><?php echo count($ingredientSets); ?> defined</span>
	</div>
	<div class="p-4 sm:p-6 <?php echo $canManageSets ? 'grid gap-6 lg:grid-cols-2' : ''; ?>">
		<?php if ($canManageSets): ?>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set" id="setBuilderForm" class="space-y-5">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="set_id" id="setIdField" value="0">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Set name</label>
				<input id="setNameInput" name="set_name" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="e.g., Chocolate Cake Kit" required>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-xs text-gray-400">(optional)</span></label>
				<textarea id="setDescriptionInput" name="set_description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Short notes for the team"></textarea>
			</div>
			<div class="space-y-4">
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<div class="md:col-span-2">
						<label class="block text-sm font-medium text-gray-700 mb-1">Ingredient</label>
						<select id="setIngredientSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">Select ingredient</option>
							<?php foreach ($ingredients as $ing): ?>
								<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>">
									<?php echo htmlspecialchars($ing['name'] . ' (' . $ing['unit'] . ')'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
						<div class="relative">
							<input type="number" id="setIngredientQty" min="0.01" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
							<span id="setIngredientUnitBadge" class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-gray-500">unit</span>
						</div>
					</div>
				</div>
				<div class="flex items-center justify-between gap-3 flex-wrap">
					<p class="text-xs text-gray-500">Quantities are stored using each ingredient's base unit.</p>
					<button type="button" id="setAddIngredientBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors">
						<i data-lucide="plus" class="w-4 h-4"></i>
						Add to set
					</button>
				</div>
				<div id="setBuilderError" class="hidden px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg"></div>
			</div>
			<div class="border rounded-xl">
				<div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
					<h3 class="text-sm font-semibold text-gray-700">Ingredients in this set</h3>
					<span id="setComponentCount" class="text-xs text-gray-500">0 ingredients</span>
				</div>
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="bg-white">
							<tr>
								<th class="px-4 py-2 text-left font-medium text-gray-600">Ingredient</th>
								<th class="px-4 py-2 text-left font-medium text-gray-600">Quantity</th>
								<th class="px-4 py-2"></th>
							</tr>
						</thead>
						<tbody id="setComponentsBody"></tbody>
					</table>
					<div id="setComponentsEmpty" class="px-4 py-6 text-center text-sm text-gray-500">No ingredients added yet.</div>
				</div>
			</div>
			<input type="hidden" name="components_json" id="setComponentsJson" value="[]">
			<div class="flex justify-end gap-3">
				<button type="button" id="setEditCancelBtn" class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors hidden">
					Cancel edit
				</button>
				<button type="submit" id="setBuilderSubmit" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="check" class="w-4 h-4"></i>
					<span id="setBuilderSubmitLabel">Save set</span>
				</button>
			</div>
		</form>
		<?php endif; ?>
		<div class="space-y-4">
			<?php if (empty($ingredientSets)): ?>
				<div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-500">
					<i data-lucide="archive" class="w-8 h-8 mx-auto mb-3 text-gray-400"></i>
					<p class="text-sm">No sets defined yet. <?php echo $canManageSets ? 'Use the form to create your first set.' : 'Ask a manager to define sets.'; ?></p>
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
			<div class="border rounded-2xl p-5 space-y-4 bg-white shadow-sm">
				<div class="flex items-start justify-between gap-4">
					<div>
						<div class="flex items-center gap-3 flex-wrap">
							<h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($set['name']); ?></h3>
							<span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $lowComponent ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-700'; ?>">
								<i data-lucide="<?php echo $lowComponent ? 'alert-triangle' : 'check-circle'; ?>" class="w-3 h-3"></i>
								<?php echo $lowComponent ? 'Needs restock' : 'Kitchen-ready'; ?>
							</span>
							<span class="text-xs text-gray-500"><?php echo $componentCount; ?> ingredient<?php echo $componentCount === 1 ? '' : 's'; ?></span>
						</div>
						<?php if (!empty($set['description'])): ?>
							<p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($set['description']); ?></p>
						<?php endif; ?>
					</div>
					<?php if ($canManageSets): ?>
					<div class="flex items-center gap-2 shrink-0">
						<button type="button" data-set-edit="<?php echo htmlspecialchars(json_encode($setPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>" class="text-xs text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 px-3 py-1.5 border border-indigo-200 rounded-lg bg-indigo-50">
							<i data-lucide="edit-3" class="w-3 h-3"></i>
							Edit
						</button>
						<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set/delete" class="shrink-0">
							<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
							<input type="hidden" name="set_id" value="<?php echo (int)$set['id']; ?>">
							<button type="submit" class="text-xs text-red-600 hover:text-red-700 inline-flex items-center gap-1 px-3 py-1.5 border border-red-200 rounded-lg">
								<i data-lucide="trash-2" class="w-3 h-3"></i>
								Delete
							</button>
						</form>
					</div>
					<?php endif; ?>
				</div>
				<ul class="space-y-2 text-sm">
					<?php foreach ($set['components'] as $component): ?>
						<li class="flex items-center justify-between text-gray-700">
							<span class="flex items-center gap-2">
								<i data-lucide="dot" class="w-4 h-4 text-gray-400"></i>
								<?php echo htmlspecialchars($component['ingredient_name']); ?>
							</span>
							<span class="font-medium">
								<?php echo number_format((float)$component['quantity'], 2); ?>
								<span class="text-xs text-gray-500"><?php echo htmlspecialchars($component['unit']); ?></span>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ($lowComponent): ?>
					<div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
						<?php echo htmlspecialchars($lowComponent['ingredient_name']); ?> is currently at or below its reorder level.
					</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<!-- Inventory Table -->
<div id="inventory-low-stock" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
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
				<button
					type="button"
					id="inventoryPurchaseListBtn"
					class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg shadow-sm border border-rose-200 bg-rose-600 text-white hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
					<?php echo empty($lowStockGroups) ? 'disabled title="No low stock items to print"' : ''; ?>
				>
					<i data-lucide="printer" class="w-4 h-4"></i>
					Print Purchase List
				</button>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[640px]">
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
					$stockPercentage = $ing['reorder_level'] > 0 ? ((float)$ing['quantity'] / (float)$ing['reorder_level'] * 100) : 100;
					$normalizedWidth = max(0, min(100, $stockPercentage));
					$progressValue = rtrim(rtrim(number_format($normalizedWidth, 2, '.', ''), '0'), '.');
					$progressValue = $progressValue === '' ? '0' : $progressValue;
					$progressWidthClass = '[width:' . $progressValue . '%]';
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
								<?php if (!empty($ing['preferred_supplier'])): ?>
									<p class="text-xs text-gray-500 mt-1">Supplier: <?php echo htmlspecialchars($ing['preferred_supplier']); ?></p>
								<?php endif; ?>
								<?php if (!empty($ing['restock_quantity'])): ?>
									<p class="text-xs text-gray-500">Restock qty: <?php echo number_format((float)$ing['restock_quantity'], 2); ?> <?php echo htmlspecialchars($ing['unit']); ?></p>
								<?php endif; ?>
								<?php if ($canManageInventory): ?>
									<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/meta" class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
										<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
										<input type="hidden" name="id" value="<?php echo (int)$ing['id']; ?>">
										<input name="preferred_supplier" value="<?php echo htmlspecialchars($ing['preferred_supplier'] ?? ''); ?>" placeholder="Supplier" class="border border-gray-200 rounded-lg px-2 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
										<input type="number" step="0.01" min="0" name="restock_quantity" value="<?php echo htmlspecialchars($ing['restock_quantity'] ?? ''); ?>" placeholder="Restock" class="border border-gray-200 rounded-lg px-2 py-1 w-24 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
										<button class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors" type="submit">
											<i data-lucide="save" class="w-3 h-3"></i>
											Save
										</button>
									</form>
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
							<div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
								<div class="h-2 rounded-full transition-all duration-300 <?php echo $low ? 'bg-red-500' : ($stockPercentage > 200 ? 'bg-green-500' : 'bg-yellow-500'); ?> <?php echo $progressWidthClass; ?>"></div>
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
	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
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
	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
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
	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
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


