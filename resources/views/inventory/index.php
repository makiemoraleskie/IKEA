<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$ingredientSets = $ingredientSets ?? [];
$canManageSets = in_array(Auth::role(), ['Owner','Manager'], true);
$canManageInventory = in_array(Auth::role(), ['Owner','Manager'], true);
?>
<!-- Page Header - Enhanced -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-10">
	<div class="space-y-2">
		<h1 class="text-4xl font-bold text-gray-900 tracking-tight">Inventory Management</h1>
		<p class="text-base text-gray-600 font-medium">Track and manage ingredient stock levels efficiently</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2.5 px-5 py-3 text-sm font-bold text-[#008000] bg-gradient-to-r from-[#008000]/10 to-[#008000]/5 border-2 border-gray-300 rounded-xl hover:bg-gradient-to-r hover:from-[#008000]/15 hover:to-[#008000]/10 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<?php if (!empty($flash)): ?>
	<div class="mb-8 px-5 py-4 rounded-2xl border-2 border-gray-300 shadow-lg <?php echo ($flash['type'] ?? '') === 'success' ? 'bg-gradient-to-r from-green-50/95 to-green-50/60 text-green-800' : 'bg-gradient-to-r from-red-50/95 to-red-50/60 text-red-800'; ?> animate-fade-in">
		<div class="flex items-start gap-3">
			<i data-lucide="<?php echo ($flash['type'] ?? '') === 'success' ? 'check-circle' : 'alert-circle'; ?>" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
			<p class="text-sm font-semibold"><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
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

<!-- Add Ingredient Form - Enhanced -->
<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
<div class="bg-gradient-to-br from-white to-gray-50/50 rounded-3xl shadow-xl border-2 border-gray-200/80 mb-10 overflow-hidden relative">
	<!-- Decorative background elements -->
	<div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#FCBBE9]/10 to-transparent rounded-full blur-3xl -mr-32 -mt-32"></div>
	<div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-[#A8E6CF]/10 to-transparent rounded-full blur-2xl -ml-24 -mb-24"></div>
	
	<div class="relative z-10">
		<!-- Header Section -->
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-[#FCBBE9]/5 px-6 sm:px-8 py-6 border-b-2 border-gray-200/60">
			<div class="flex items-center gap-4">
				<div class="w-14 h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-2xl flex items-center justify-center shadow-lg">
					<i data-lucide="plus-circle" class="w-7 h-7 text-white"></i>
				</div>
				<div>
					<h2 class="text-2xl font-bold text-gray-900 tracking-tight">Add New Ingredient</h2>
					<p class="text-sm text-gray-600 mt-1 font-medium">Add a new ingredient to your inventory system</p>
				</div>
			</div>
		</div>
		
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory" class="p-6 sm:p-8" id="addIngredientForm">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			
			<!-- Basic Information Section -->
			<div class="mb-8">
				<div class="flex items-center gap-2 mb-6">
					<div class="w-1 h-6 bg-gradient-to-b from-[#008000] to-[#00A86B] rounded-full"></div>
					<h3 class="text-lg font-bold text-gray-900">Basic Information</h3>
				</div>
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<!-- Ingredient Name -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="tag" class="w-4 h-4 text-[#008000]"></i>
								Ingredient Name <span class="text-red-500">*</span>
							</span>
						</label>
						<div class="relative">
							<input 
								name="name" 
								type="text"
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								placeholder="e.g., All-Purpose Flour, Granulated Sugar"
								required 
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">Enter the common name of the ingredient</p>
					</div>
					
					<!-- Base Unit -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="ruler" class="w-4 h-4 text-[#008000]"></i>
								Base Unit <span class="text-red-500">*</span>
							</span>
						</label>
						<div class="relative">
							<select 
								name="unit" 
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 pr-12 text-gray-900 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg appearance-none cursor-pointer" 
								required
							>
								<option value="">Choose a unit...</option>
								<option value="g">Grams (g)</option>
								<option value="kg">Kilograms (kg)</option>
								<option value="ml">Milliliters (ml)</option>
								<option value="L">Liters (L)</option>
								<option value="pcs">Pieces (pcs)</option>
								<option value="cups">Cups</option>
								<option value="tbsp">Tablespoons (tbsp)</option>
								<option value="tsp">Teaspoons (tsp)</option>
							</select>
							<div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
								<i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
							</div>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">The primary unit for measuring this ingredient</p>
					</div>
				</div>
			</div>
			
			<!-- Display Settings Section -->
			<div class="mb-8">
				<div class="flex items-center gap-2 mb-6">
					<div class="w-1 h-6 bg-gradient-to-b from-[#FCBBE9] to-[#FF9DD9] rounded-full"></div>
					<h3 class="text-lg font-bold text-gray-900">Display Settings <span class="text-sm font-normal text-gray-500">(Optional)</span></h3>
				</div>
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<!-- Display Unit -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="eye" class="w-4 h-4 text-[#FCBBE9]"></i>
								Display Unit
							</span>
						</label>
						<div class="relative">
							<input 
								name="display_unit" 
								type="text"
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								placeholder="e.g., kg, L, boxes"
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">How this ingredient appears in reports and displays</p>
					</div>
					
					<!-- Display Factor -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="calculator" class="w-4 h-4 text-[#FCBBE9]"></i>
								Display Factor
							</span>
						</label>
						<div class="relative">
							<input 
								type="number" 
								step="0.01" 
								min="0.01" 
								name="display_factor" 
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								value="1"
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">Conversion factor from base unit to display unit</p>
					</div>
				</div>
			</div>
			
			<!-- Stock Management Section -->
			<div class="mb-8">
				<div class="flex items-center gap-2 mb-6">
					<div class="w-1 h-6 bg-gradient-to-b from-[#008000] to-[#00A86B] rounded-full"></div>
					<h3 class="text-lg font-bold text-gray-900">Stock Management</h3>
				</div>
				
				<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
					<!-- Reorder Level -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
								Reorder Level
							</span>
						</label>
						<div class="relative">
							<input 
								type="number" 
								step="0.01" 
								min="0" 
								name="reorder_level" 
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								value="0"
								placeholder="0.00"
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">Minimum stock level before alert triggers</p>
					</div>
					
					<!-- Preferred Supplier -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="truck" class="w-4 h-4 text-[#008000]"></i>
								Preferred Supplier
							</span>
						</label>
						<div class="relative">
							<input 
								name="preferred_supplier" 
								type="text"
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								placeholder="e.g., ABC Foods, XYZ Suppliers"
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">Default supplier for purchase orders</p>
					</div>
					
					<!-- Restock Quantity -->
					<div class="space-y-2.5">
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
							<span class="flex items-center gap-2">
								<i data-lucide="package-plus" class="w-4 h-4 text-[#008000]"></i>
								Restock Quantity
							</span>
						</label>
						<div class="relative">
							<input 
								type="number" 
								step="0.01" 
								min="0" 
								name="restock_quantity" 
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" 
								value="0"
								placeholder="0.00"
							/>
						</div>
						<p class="text-xs text-gray-500 font-medium mt-1.5">Recommended purchase amount when restocking</p>
					</div>
				</div>
			</div>
			
			<!-- Action Buttons -->
			<div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t-2 border-gray-200/60">
				<button 
					type="reset" 
					class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border-2 border-gray-300 bg-white text-gray-700 font-bold text-base hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-400/20 transition-all duration-200"
					onclick="document.getElementById('addIngredientForm').reset();"
				>
					<i data-lucide="x" class="w-4 h-4"></i>
					Clear Form
				</button>
				<button 
					type="submit" 
					class="group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-xl bg-gradient-to-r from-[#008000] via-[#00A86B] to-[#008000] text-white font-bold text-base shadow-lg shadow-[#008000]/30 hover:shadow-xl hover:shadow-[#008000]/40 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-4 focus:ring-[#008000]/30 focus:ring-offset-2 transition-all duration-300 overflow-hidden relative"
				>
					<span class="relative z-10 flex items-center gap-2.5">
						<i data-lucide="plus" class="w-5 h-5"></i>
						Add Ingredient
					</span>
					<div class="absolute inset-0 bg-gradient-to-r from-[#006a00] via-[#008000] to-[#006a00] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
				</button>
			</div>
		</form>
	</div>
</div>
<?php endif; ?>

<!-- Ingredient Sets - Enhanced -->
<div class="bg-gradient-to-br from-white to-gray-50/50 rounded-3xl shadow-xl border-2 border-gray-200/80 mb-10 overflow-hidden relative">
	<!-- Decorative background elements -->
	<div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#008000]/5 to-transparent rounded-full blur-3xl -mr-32 -mt-32"></div>
	<div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-[#A8E6CF]/10 to-transparent rounded-full blur-2xl -ml-24 -mb-24"></div>
	
	<div class="relative z-10">
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-[#A8E6CF]/10 px-6 sm:px-8 py-6 border-b-2 border-gray-200/60">
			<div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
				<div class="flex items-center gap-4">
					<div class="w-14 h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-2xl flex items-center justify-center shadow-lg">
						<i data-lucide="layers" class="w-7 h-7 text-white"></i>
					</div>
					<div>
						<h2 class="text-2xl font-bold text-gray-900 tracking-tight">Ingredient Sets</h2>
						<p class="text-sm text-gray-600 mt-1 font-medium">Combine multiple ingredients into reusable sets for kitchen requests</p>
					</div>
				</div>
				<div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/60 backdrop-blur-sm border-2 border-gray-200/60 shadow-sm">
					<span class="text-sm font-bold text-gray-700"><?php echo count($ingredientSets); ?></span>
					<span class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sets Defined</span>
				</div>
			</div>
		</div>
		<div class="p-6 sm:p-8 <?php echo $canManageSets ? 'grid gap-8 lg:grid-cols-2' : ''; ?>">
			<?php if ($canManageSets): ?>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set" id="setBuilderForm" class="space-y-6">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="set_id" id="setIdField" value="0">
				
				<div class="space-y-4">
					<div>
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">
							<span class="flex items-center gap-2">
								<i data-lucide="tag" class="w-4 h-4 text-[#008000]"></i>
								Set Name <span class="text-red-500">*</span>
							</span>
						</label>
						<input id="setNameInput" name="set_name" class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" placeholder="e.g., Chocolate Cake Kit" required>
					</div>
					
					<div>
						<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">
							<span class="flex items-center gap-2">
								<i data-lucide="file-text" class="w-4 h-4 text-[#008000]"></i>
								Description <span class="text-xs font-normal text-gray-500">(optional)</span>
							</span>
						</label>
						<textarea id="setDescriptionInput" name="set_description" rows="3" class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" placeholder="Short notes for the team"></textarea>
					</div>
				</div>
				
				<div class="space-y-4">
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">
								<span class="flex items-center gap-2">
									<i data-lucide="package" class="w-4 h-4 text-[#008000]"></i>
									Ingredient
								</span>
							</label>
							<div class="relative">
								<select id="setIngredientSelect" class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 pr-12 text-gray-900 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg appearance-none cursor-pointer">
									<option value="">Select ingredient...</option>
									<?php foreach ($ingredients as $ing): ?>
										<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>">
											<?php echo htmlspecialchars($ing['name'] . ' (' . $ing['unit'] . ')'); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
									<i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
								</div>
							</div>
						</div>
						<div>
							<label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">
								<span class="flex items-center gap-2">
									<i data-lucide="hash" class="w-4 h-4 text-[#008000]"></i>
									Quantity
								</span>
							</label>
							<div class="relative">
								<input type="number" id="setIngredientQty" min="0.01" step="0.01" class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-5 py-4 pr-20 text-gray-900 placeholder-gray-400 text-base font-normal transition-all duration-300 hover:border-gray-300 hover:bg-white hover:shadow-md focus:border-gray-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-gray-400/15 focus:shadow-lg" placeholder="0.00">
								<span id="setIngredientUnitBadge" class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-bold text-gray-500 pointer-events-none">unit</span>
							</div>
						</div>
					</div>
					<div class="flex items-center justify-between gap-3 flex-wrap">
						<p class="text-xs text-gray-500 font-medium">Quantities are stored using each ingredient's base unit</p>
						<button type="button" id="setAddIngredientBtn" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#008000] to-[#00A86B] text-white rounded-xl font-bold text-sm shadow-md shadow-[#008000]/30 hover:shadow-lg hover:shadow-[#008000]/40 hover:-translate-y-0.5 transition-all duration-200">
							<i data-lucide="plus" class="w-4 h-4"></i>
							Add to Set
						</button>
					</div>
					<div id="setBuilderError" class="hidden px-5 py-4 text-sm font-semibold text-red-800 bg-gradient-to-r from-red-50 to-red-50/60 border-2 border-gray-300 rounded-xl shadow-sm"></div>
				</div>
				
				<div class="border-2 border-gray-200/80 rounded-2xl overflow-hidden bg-white/50 backdrop-blur-sm">
					<div class="bg-gradient-to-r from-gray-100/80 to-gray-50/50 px-5 py-4 flex items-center justify-between border-b-2 border-gray-200/60">
						<h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Ingredients in this set</h3>
						<span id="setComponentCount" class="text-xs font-bold text-gray-600 px-3 py-1 bg-white rounded-full border border-gray-200">0 ingredients</span>
					</div>
					<div class="overflow-x-auto">
						<table class="w-full text-sm">
							<thead class="bg-white/60">
								<tr>
									<th class="px-5 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Ingredient</th>
									<th class="px-5 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wide">Quantity</th>
									<th class="px-5 py-3"></th>
								</tr>
							</thead>
							<tbody id="setComponentsBody" class="divide-y divide-gray-200/60"></tbody>
						</table>
						<div id="setComponentsEmpty" class="px-5 py-8 text-center text-sm text-gray-500">
							<i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
							<p class="font-medium">No ingredients added yet</p>
							<p class="text-xs text-gray-400 mt-1">Add ingredients above to build your set</p>
						</div>
					</div>
				</div>
				
				<input type="hidden" name="components_json" id="setComponentsJson" value="[]">
				<div class="flex justify-end gap-4 pt-4 border-t-2 border-gray-200/60">
					<button type="button" id="setEditCancelBtn" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 bg-white text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-400/20 transition-all duration-200 hidden">
						<i data-lucide="x" class="w-4 h-4"></i>
						Cancel Edit
					</button>
					<button type="submit" id="setBuilderSubmit" class="group inline-flex items-center gap-2.5 px-6 py-3 bg-gradient-to-r from-[#008000] via-[#00A86B] to-[#008000] text-white font-bold rounded-xl shadow-lg shadow-[#008000]/30 hover:shadow-xl hover:shadow-[#008000]/40 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden relative">
						<span class="relative z-10 flex items-center gap-2.5">
							<i data-lucide="check" class="w-4 h-4"></i>
							<span id="setBuilderSubmitLabel">Save Set</span>
						</span>
						<div class="absolute inset-0 bg-gradient-to-r from-[#006a00] via-[#008000] to-[#006a00] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
					</button>
				</div>
			</form>
			<?php endif; ?>
			<div class="space-y-4">
				<?php if (empty($ingredientSets)): ?>
					<div class="rounded-2xl border-2 border-dashed border-gray-300/60 bg-gradient-to-br from-gray-50/50 to-white p-8 text-center">
						<div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
							<i data-lucide="archive" class="w-8 h-8 text-gray-400"></i>
						</div>
						<p class="text-sm font-semibold text-gray-700 mb-1">No sets defined yet</p>
						<p class="text-xs text-gray-500"><?php echo $canManageSets ? 'Use the form to create your first set.' : 'Ask a manager to define sets.'; ?></p>
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
				<div class="group bg-gradient-to-br from-white to-gray-50/50 border-2 border-gray-200/80 rounded-2xl p-6 space-y-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
					<div class="flex items-start justify-between gap-4">
						<div class="flex-1">
							<div class="flex items-center gap-3 flex-wrap mb-2">
								<h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($set['name']); ?></h3>
								<span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border-2 border-gray-300 <?php echo $lowComponent ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-700'; ?>">
									<i data-lucide="<?php echo $lowComponent ? 'alert-triangle' : 'check-circle'; ?>" class="w-3.5 h-3.5"></i>
									<?php echo $lowComponent ? 'Needs Restock' : 'Kitchen Ready'; ?>
								</span>
								<span class="text-xs font-semibold text-gray-500 px-2 py-1 bg-gray-100 rounded-lg"><?php echo $componentCount; ?> ingredient<?php echo $componentCount === 1 ? '' : 's'; ?></span>
							</div>
							<?php if (!empty($set['description'])): ?>
								<p class="text-sm text-gray-600 mt-2 leading-relaxed"><?php echo htmlspecialchars($set['description']); ?></p>
							<?php endif; ?>
						</div>
						<?php if ($canManageSets): ?>
						<div class="flex items-center gap-2 shrink-0">
							<button type="button" data-set-edit="<?php echo htmlspecialchars(json_encode($setPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-[#008000] bg-[#008000]/10 border-2 border-gray-300 rounded-xl hover:bg-[#008000]/20 hover:border-gray-400 transition-all duration-200">
								<i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
								Edit
							</button>
							<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/set/delete" class="shrink-0">
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
								<input type="hidden" name="set_id" value="<?php echo (int)$set['id']; ?>">
								<button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-red-600 bg-red-50 border-2 border-gray-300 rounded-xl hover:bg-red-100 hover:border-gray-400 transition-all duration-200">
									<i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
									Delete
								</button>
							</form>
						</div>
						<?php endif; ?>
					</div>
					
					<div class="bg-white/60 rounded-xl p-4 border border-gray-200/60">
						<ul class="space-y-3">
							<?php foreach ($set['components'] as $component): ?>
								<li class="flex items-center justify-between text-sm">
									<span class="flex items-center gap-2.5 text-gray-700 font-medium">
										<div class="w-6 h-6 bg-gradient-to-br from-[#008000]/20 to-[#A8E6CF]/10 rounded-lg flex items-center justify-center">
											<i data-lucide="check" class="w-3 h-3 text-[#008000]"></i>
										</div>
										<?php echo htmlspecialchars($component['ingredient_name']); ?>
									</span>
									<span class="font-bold text-gray-900">
										<?php echo number_format((float)$component['quantity'], 2); ?>
										<span class="text-xs font-medium text-gray-500 ml-1"><?php echo htmlspecialchars($component['unit']); ?></span>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					
					<?php if ($lowComponent): ?>
						<div class="flex items-start gap-3 px-4 py-3 bg-gradient-to-r from-red-50 to-red-50/60 border-2 border-gray-300 rounded-xl">
							<i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
							<p class="text-sm font-semibold text-red-800">
								<strong><?php echo htmlspecialchars($lowComponent['ingredient_name']); ?></strong> is currently at or below its reorder level.
							</p>
						</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<!-- Current Inventory Table - Enhanced -->
<div id="inventory-low-stock" class="bg-gradient-to-br from-white to-gray-50/50 rounded-3xl shadow-xl border-2 border-gray-200/80 overflow-hidden relative">
	<!-- Decorative background elements -->
	<div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#008000]/5 to-transparent rounded-full blur-3xl -mr-32 -mt-32"></div>
	<div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-[#A8E6CF]/5 to-transparent rounded-full blur-2xl -ml-24 -mb-24"></div>
	
	<div class="relative z-10">
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-gray-50/50 px-6 sm:px-8 py-6 border-b-2 border-gray-200/60">
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<div class="flex items-center gap-4">
					<div class="w-14 h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-2xl flex items-center justify-center shadow-lg">
						<i data-lucide="package" class="w-7 h-7 text-white"></i>
					</div>
					<div>
						<h2 class="text-2xl font-bold text-gray-900 tracking-tight">Current Inventory</h2>
						<p class="text-sm text-gray-600 mt-1 font-medium">View and monitor all ingredient stock levels</p>
					</div>
				</div>
				<div class="flex items-center gap-3 flex-wrap">
					<div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/60 backdrop-blur-sm border-2 border-gray-200/60 shadow-sm">
						<span class="text-sm font-bold text-gray-700"><?php echo count($ingredients); ?></span>
						<span class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total</span>
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
						<div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-50 to-red-50/60 border-2 border-gray-300 text-red-700 rounded-xl text-sm font-bold shadow-sm">
							<i data-lucide="alert-triangle" class="w-4 h-4"></i>
							<?php echo $lowStockCount; ?> Low Stock
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="overflow-x-auto">
			<table class="w-full text-sm min-w-[800px]">
				<thead class="bg-gradient-to-r from-gray-100/80 to-gray-50/60 border-b-2 border-gray-200/60">
					<tr>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Ingredient</th>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Unit</th>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Current Stock</th>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Reorder Level</th>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Status</th>
						<th class="text-left px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wide">Stock Level</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200/60">
				<?php foreach ($ingredients as $ing): 
					$low = (float)$ing['quantity'] <= (float)$ing['reorder_level'];
					$stockPercentage = $ing['reorder_level'] > 0 ? ((float)$ing['quantity'] / (float)$ing['reorder_level'] * 100) : 100;
					$normalizedWidth = max(0, min(100, $stockPercentage));
					$progressValue = rtrim(rtrim(number_format($normalizedWidth, 2, '.', ''), '0'), '.');
					$progressValue = $progressValue === '' ? '0' : $progressValue;
					$progressWidthClass = '[width:' . $progressValue . '%]';
				?>
				<tr class="hover:bg-gradient-to-r hover:from-gray-50/80 hover:to-white transition-all duration-200 <?php echo $low ? 'bg-red-50/50' : 'bg-white'; ?>">
					<td class="px-6 py-5">
						<div class="flex items-center gap-4">
							<div class="w-12 h-12 bg-gradient-to-br from-[#008000]/10 to-[#A8E6CF]/10 rounded-xl flex items-center justify-center border-2 border-gray-200">
								<i data-lucide="package" class="w-6 h-6 text-[#008000]"></i>
							</div>
							<div class="flex-1">
								<div class="font-bold text-gray-900 text-base"><?php echo htmlspecialchars($ing['name']); ?></div>
								<div class="flex flex-wrap items-center gap-3 mt-2">
									<?php if (!empty($ing['display_unit'])): ?>
										<span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 px-2 py-1 bg-gray-100 rounded-lg">
											<i data-lucide="eye" class="w-3 h-3"></i>
											Display: <?php echo htmlspecialchars($ing['display_unit']); ?>
										</span>
									<?php endif; ?>
									<?php if (!empty($ing['preferred_supplier'])): ?>
										<span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 px-2 py-1 bg-gray-100 rounded-lg">
											<i data-lucide="truck" class="w-3 h-3"></i>
											<?php echo htmlspecialchars($ing['preferred_supplier']); ?>
										</span>
									<?php endif; ?>
									<?php if (!empty($ing['restock_quantity'])): ?>
										<span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 px-2 py-1 bg-gray-100 rounded-lg">
											<i data-lucide="package-plus" class="w-3 h-3"></i>
											Restock: <?php echo number_format((float)$ing['restock_quantity'], 2); ?> <?php echo htmlspecialchars($ing['unit']); ?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-5">
						<span class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-br from-gray-100 to-gray-50 text-gray-700 rounded-xl text-xs font-bold border border-gray-200">
							<i data-lucide="ruler" class="w-4 h-4 text-[#008000]"></i>
							<?php echo htmlspecialchars($ing['unit']); ?>
						</span>
					</td>
					
					<td class="px-6 py-5">
						<div class="flex flex-col">
							<span class="font-black text-gray-900 text-lg"><?php echo number_format((float)$ing['quantity'], 2); ?></span>
							<span class="text-xs text-gray-500 font-medium"><?php echo htmlspecialchars($ing['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-5">
						<div class="flex flex-col">
							<span class="font-bold text-gray-700"><?php echo number_format((float)$ing['reorder_level'], 2); ?></span>
							<span class="text-xs text-gray-500 font-medium"><?php echo htmlspecialchars($ing['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-5">
						<?php if ($low): ?>
							<span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-red-50 to-red-50/60 text-red-800 border-2 border-gray-300 shadow-sm">
								<i data-lucide="alert-triangle" class="w-4 h-4"></i>
								Low Stock
							</span>
						<?php else: ?>
							<span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-green-50 to-green-50/60 text-green-700 border-2 border-gray-300 shadow-sm">
								<i data-lucide="check-circle" class="w-4 h-4"></i>
								In Stock
							</span>
						<?php endif; ?>
					</td>
					
					<td class="px-6 py-5">
						<div class="flex items-center gap-3 min-w-[150px]">
							<div class="flex-1 bg-gray-200/80 rounded-full h-3 overflow-hidden shadow-inner">
								<div class="h-3 rounded-full transition-all duration-500 <?php echo $low ? 'bg-gradient-to-r from-red-500 to-red-600' : ($stockPercentage > 200 ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-amber-500 to-amber-600'); ?> <?php echo $progressWidthClass; ?>"></div>
							</div>
							<span class="text-xs text-gray-700 font-bold min-w-[3rem] tabular-nums">
								<?php echo number_format($stockPercentage, 0); ?>%
							</span>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
			<?php if (empty($ingredients)): ?>
			<div class="flex flex-col items-center justify-center py-16 text-gray-500">
				<div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-6">
					<i data-lucide="package-x" class="w-10 h-10 text-gray-300"></i>
				</div>
				<h3 class="text-xl font-bold text-gray-900 mb-2">No Ingredients Found</h3>
				<p class="text-sm text-gray-600 font-medium">Start by adding your first ingredient to the inventory</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Summary Cards - Enhanced -->
<?php if (!empty($ingredients)): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
	<!-- Total Ingredients -->
	<div class="group bg-gradient-to-br from-white to-gray-50/50 rounded-2xl shadow-lg border-2 border-gray-200/80 p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden">
		<div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100/30 to-transparent rounded-full blur-2xl -mr-12 -mt-12"></div>
		<div class="relative z-10 flex items-center justify-between">
			<div>
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Total Ingredients</p>
				<p class="text-4xl font-black text-gray-900 tracking-tight"><?php echo count($ingredients); ?></p>
			</div>
			<div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center shadow-lg border-2 border-gray-300">
				<i data-lucide="layers" class="w-8 h-8 text-blue-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Low Stock Items -->
	<div class="group bg-gradient-to-br from-white to-gray-50/50 rounded-2xl shadow-lg border-2 border-gray-200/80 p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden">
		<div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-100/30 to-transparent rounded-full blur-2xl -mr-12 -mt-12"></div>
		<div class="relative z-10 flex items-center justify-between">
			<div>
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Low Stock Items</p>
				<p class="text-4xl font-black text-red-600 tracking-tight"><?php echo $lowStockCount; ?></p>
			</div>
			<div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-50 rounded-2xl flex items-center justify-center shadow-lg border-2 border-gray-300">
				<i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
			</div>
		</div>
	</div>
	
	<!-- In Stock Items -->
	<div class="group bg-gradient-to-br from-white to-gray-50/50 rounded-2xl shadow-lg border-2 border-gray-200/80 p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden">
		<div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-100/30 to-transparent rounded-full blur-2xl -mr-12 -mt-12"></div>
		<div class="relative z-10 flex items-center justify-between">
			<div>
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">In Stock Items</p>
				<p class="text-4xl font-black text-green-600 tracking-tight"><?php echo count($ingredients) - $lowStockCount; ?></p>
			</div>
			<div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-50 rounded-2xl flex items-center justify-center shadow-lg border-2 border-gray-300">
				<i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<style>
	/* Form enhancements */
	@keyframes fade-in {
		from {
			opacity: 0;
			transform: translateY(10px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.animate-fade-in {
		animation: fade-in 0.4s ease-out;
	}

	/* Enhanced form input focus effects */
	#addIngredientForm input:focus,
	#addIngredientForm select:focus {
		transform: translateY(-1px);
	}

	/* Select dropdown styling */
	select {
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
		background-repeat: no-repeat;
		background-position: right 1rem center;
		background-size: 1.5em 1.5em;
		padding-right: 3rem;
	}

	/* Smooth transitions */
	#addIngredientForm input,
	#addIngredientForm select,
	#addIngredientForm button {
		transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter;
		transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
		transition-duration: 200ms;
	}

	/* Form validation styling - use gray for all states, override browser defaults */
	#addIngredientForm input:invalid,
	#addIngredientForm select:invalid,
	#addIngredientForm input:valid,
	#addIngredientForm select:valid {
		border-color: #e5e7eb !important;
	}

	#addIngredientForm input:invalid:not(:placeholder-shown),
	#addIngredientForm select:invalid:not(:placeholder-shown) {
		border-color: #d1d5db !important;
		background-color: #f9fafb;
	}

	#addIngredientForm input:valid:not(:placeholder-shown),
	#addIngredientForm select:valid:not(:placeholder-shown) {
		border-color: #d1d5db !important;
	}

	/* Override browser default validation styling - force gray borders */
	#addIngredientForm input,
	#addIngredientForm select {
		border-color: #e5e7eb !important;
	}

	#addIngredientForm input:focus:invalid,
	#addIngredientForm select:focus:invalid,
	#addIngredientForm input:invalid,
	#addIngredientForm select:invalid {
		border-color: #d1d5db !important;
		outline: none !important;
		box-shadow: 0 0 0 4px rgba(156, 163, 175, 0.15) !important;
	}

	#addIngredientForm input:focus:valid,
	#addIngredientForm select:focus:valid,
	#addIngredientForm input:valid,
	#addIngredientForm select:valid {
		border-color: #d1d5db !important;
	}

	/* Simple scrollbar styling - only horizontal on tables */
	.overflow-x-auto {
		overflow-x: auto;
		overflow-y: hidden;
		scrollbar-width: thin;
		scrollbar-color: rgba(0, 128, 0, 0.3) transparent;
	}

	.overflow-x-auto::-webkit-scrollbar {
		height: 8px;
	}

	.overflow-x-auto::-webkit-scrollbar-track {
		background: rgba(0, 0, 0, 0.05);
		border-radius: 10px;
	}

	.overflow-x-auto::-webkit-scrollbar-thumb {
		background: rgba(0, 128, 0, 0.4);
		border-radius: 10px;
	}

	.overflow-x-auto::-webkit-scrollbar-thumb:hover {
		background: rgba(0, 128, 0, 0.6);
	}

	/* Fix select dropdown icon positioning when appearance-none is used */
	select[class*="appearance-none"] {
		background-image: none;
	}
</style>

<script>
	// Enhanced form interactions
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('addIngredientForm');
		if (!form) return;

		// Remove colored validation feedback - keep borders gray
		const inputs = form.querySelectorAll('input[required], select[required]');
		inputs.forEach(input => {
			input.addEventListener('blur', function() {
				// Remove any colored borders, keep gray
				this.classList.remove('border-red-300', 'bg-red-50/50', 'border-green-300');
			});

			input.addEventListener('input', function() {
				// Remove any colored borders on input
				this.classList.remove('border-red-300', 'bg-red-50/50', 'border-green-300');
			});
		});

		// Form submission enhancement
		form.addEventListener('submit', function(e) {
			const submitBtn = form.querySelector('button[type="submit"]');
			if (submitBtn && !submitBtn.disabled) {
				submitBtn.disabled = true;
				const originalHTML = submitBtn.innerHTML;
				submitBtn.innerHTML = '<span class="relative z-10 flex items-center gap-2.5"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adding...</span>';
				
				// Re-enable after 5 seconds as fallback
				setTimeout(() => {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalHTML;
				}, 5000);
			}
		});
	});
</script>

