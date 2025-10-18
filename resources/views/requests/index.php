<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Ingredient Requests</h1>
		<p class="text-gray-600 mt-1">Manage ingredient requests and batch approvals</p>
	</div>
	<a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner'], true)): ?>
<!-- New Request Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
			New Batch Request
		</h2>
		<p class="text-sm text-gray-600 mt-1">Create a new ingredient request batch</p>
	</div>
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" id="requestForm" class="p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

		<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
			<!-- Left: Add items panel -->
			<section class="space-y-6">
				<div class="bg-gray-50 rounded-lg p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
							<span class="text-sm font-semibold text-blue-600">1</span>
						</div>
						<div>
							<h3 class="font-semibold text-gray-900">Choose Ingredient</h3>
							<p class="text-sm text-gray-600">Search or select from available ingredients</p>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Search Ingredient</label>
							<div class="relative">
								<input id="ingredientSearch" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Type ingredient name..." autocomplete="off" />
								<i data-lucide="search" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
								<input type="hidden" id="ingredientIdHidden" />
								<div id="ingredientResults" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden"></div>
							</div>
						</div>
						
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Or Select from List</label>
							<select id="ingredientSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
								<option value="">Choose from dropdown</option>
								<?php foreach ($ingredients as $ing): ?>
									<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="bg-gray-50 rounded-lg p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
							<span class="text-sm font-semibold text-green-600">2</span>
						</div>
						<div>
							<h3 class="font-semibold text-gray-900">Set Quantity</h3>
							<p class="text-sm text-gray-600">Specify the amount needed</p>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Quantity</label>
							<input id="quantityInput" type="number" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter amount" />
						</div>
						
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Unit</label>
							<select id="unitSelector" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></select>
						</div>
					</div>
					
					<div class="mt-4 flex items-center justify-between">
						<p class="text-xs text-gray-500 flex items-center gap-1">
							<i data-lucide="info" class="w-3 h-3"></i>
							All quantities are stored in base units (g/ml/pcs)
						</p>
						<button type="button" id="addItemBtn" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="plus" class="w-4 h-4"></i>
							Add to List
						</button>
					</div>
				</div>
			</section>

			<!-- Right: Staged list panel -->
			<section class="bg-gray-50 rounded-lg overflow-hidden flex flex-col">
				<div class="bg-white border-b px-6 py-4">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-3">
							<i data-lucide="list" class="w-5 h-5 text-gray-600"></i>
							<h3 class="font-semibold text-gray-900">Items in this Request</h3>
						</div>
						<div class="flex items-center gap-3">
							<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
								<i data-lucide="hash" class="w-3 h-3"></i>
								<span id="itemCountBadge">0</span> items
							</span>
							<button type="button" id="clearListBtn" class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
								<i data-lucide="trash-2" class="w-3 h-3"></i>
								Clear All
							</button>
						</div>
					</div>
				</div>
				
				<div class="flex-1 overflow-hidden">
					<div class="h-full overflow-y-auto">
						<table class="w-full text-sm">
							<thead class="bg-gray-100 sticky top-0">
								<tr>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Ingredient</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Quantity</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Unit</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
								</tr>
							</thead>
							<tbody id="listBody" class="divide-y divide-gray-200"></tbody>
						</table>
						
						<!-- Empty State -->
						<div id="emptyState" class="flex flex-col items-center justify-center py-12 text-gray-500">
							<i data-lucide="package" class="w-12 h-12 mb-3 text-gray-300"></i>
							<p class="text-sm">No items added yet</p>
							<p class="text-xs text-gray-400">Add ingredients to create your request</p>
						</div>
					</div>
				</div>
				
				<div class="bg-white border-t px-6 py-4">
					<button id="submitBtn" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2" disabled>
						<i data-lucide="send" class="w-4 h-4"></i>
						Submit Batch Request
					</button>
				</div>
			</section>
		</div>
	</form>
</div>
<?php endif; ?>

<!-- Batch Requests Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
			Batch Requests History
		</h2>
		<p class="text-sm text-gray-600 mt-1">View and manage all ingredient requests</p>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Batch ID</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Requested By</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Items</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Status</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Requested</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-blue-600">#<?php echo (int)$b['id']; ?></span>
							</div>
						</div>
					</td>
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
								<span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($b['staff_name'] ?? 'U', 0, 2)); ?></span>
							</div>
							<span class="font-medium text-gray-900"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></span>
						</div>
					</td>
					<td class="px-6 py-4">
						<?php $count=(int)($b['items_count'] ?? 0); ?>
						<?php if ($count === 1 && !empty($items)): ?>
							<?php $it=$items[0]; ?>
							<div class="flex items-center gap-2">
								<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
								<span class="text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></span>
								<span class="text-gray-500">— <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
							</div>
						<?php else: ?>
							<div class="flex items-center gap-2">
								<i data-lucide="layers" class="w-4 h-4 text-gray-400"></i>
								<span class="font-medium text-gray-900"><?php echo $count; ?> items</span>
								<?php if ($count > 1): ?>
									<button type="button" class="text-blue-600 hover:text-blue-700 text-xs underline" onclick="document.getElementById('batch-<?php echo (int)$b['id']; ?>').classList.toggle('hidden')">View Details</button>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</td>
					<td class="px-6 py-4">
						<?php 
						$statusClass = match($b['status']) {
							'Approved' => 'bg-green-100 text-green-800 border-green-200',
							'Rejected' => 'bg-red-100 text-red-800 border-red-200',
							default => 'bg-yellow-100 text-yellow-800 border-yellow-200'
						};
						$statusIcon = match($b['status']) {
							'Approved' => 'check-circle',
							'Rejected' => 'x-circle',
							default => 'clock'
						};
						?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
							<i data-lucide="<?php echo $statusIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($b['status']); ?>
						</span>
					</td>
					<td class="px-6 py-4 text-gray-600">
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<?php echo htmlspecialchars($b['date_requested']); ?>
						</div>
					</td>
					<td class="px-6 py-4">
						<?php if (in_array(Auth::role(), ['Owner','Manager'], true) && $b['status'] === 'Pending'): ?>
							<div class="flex gap-2">
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/approve">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="inline-flex items-center gap-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
										<i data-lucide="check" class="w-3 h-3"></i>
										Approve
									</button>
								</form>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/reject">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="inline-flex items-center gap-1 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
										<i data-lucide="x" class="w-3 h-3"></i>
										Reject
									</button>
								</form>
							</div>
						<?php else: ?>
							<span class="text-gray-400 text-sm">No actions</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden bg-gray-50">
					<td colspan="6" class="px-6 py-4">
						<div class="bg-white rounded-lg border border-gray-200 p-4">
							<h4 class="font-medium text-gray-900 mb-3 flex items-center gap-2">
								<i data-lucide="list" class="w-4 h-4"></i>
								Request Details
							</h4>
							<ul class="space-y-2">
								<?php foreach ($items as $it): ?>
								<li class="flex items-center gap-3 text-sm">
									<i data-lucide="package" class="w-3 h-3 text-gray-400"></i>
									<span class="text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></span>
									<span class="text-gray-500">— <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
(function(){
	const INGREDIENTS = <?php echo json_encode(array_map(function($i){ return ['id'=>(int)$i['id'],'name'=>$i['name'],'unit'=>$i['unit']]; }, $ingredients), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
	const search = document.getElementById('ingredientSearch');
	const select = document.getElementById('ingredientSelect');
	const hiddenId = document.getElementById('ingredientIdHidden');
	const results = document.getElementById('ingredientResults');
	const qty = document.getElementById('quantityInput');
	const unitSel = document.getElementById('unitSelector');
	const addBtn = document.getElementById('addItemBtn');
	const listBody = document.getElementById('listBody');
	const submitBtn = document.getElementById('submitBtn');
	const clearBtn = document.getElementById('clearListBtn');
	const countBadge = document.getElementById('itemCountBadge');
	const emptyState = document.getElementById('emptyState');

	function renderResults(items){
		if (!items.length){ 
			results.classList.add('hidden'); 
			results.innerHTML=''; 
			return; 
		}
		results.innerHTML = items.map(i => `
			<button type="button" data-id="${i.id}" class="w-full text-left px-4 py-3 hover:bg-blue-50 border-b border-gray-100 last:border-b-0 flex items-center gap-3">
				<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
				<div>
					<div class="font-medium text-gray-900">${i.name}</div>
					<div class="text-xs text-gray-500">Unit: ${i.unit}</div>
				</div>
			</button>
		`).join('');
		results.classList.remove('hidden');
	}

	let currentBaseUnit = '';
	function configureUnitChoices(baseUnit){
		unitSel.innerHTML = '';
		const opt = (v,t)=>{ const o=document.createElement('option'); o.value=v; o.textContent=t; return o; };
		if (baseUnit === 'g'){
			unitSel.appendChild(opt('g','g'));
			unitSel.appendChild(opt('kg','kg'));
		} else if (baseUnit === 'ml'){
			unitSel.appendChild(opt('ml','ml'));
			unitSel.appendChild(opt('L','L'));
		} else {
			unitSel.appendChild(opt(baseUnit || 'pcs', baseUnit || 'pcs'));
		}
		unitSel.value = baseUnit || '';
		currentBaseUnit = baseUnit || '';
	}

	search.addEventListener('input', ()=>{
		const q = search.value.trim().toLowerCase();
		if (!q){ hiddenId.value=''; renderResults([]); return; }
		const matches = INGREDIENTS.filter(i => i.name.toLowerCase().includes(q)).slice(0, 20);
		renderResults(matches);
	});

	results.addEventListener('click', (e)=>{
		const btn = e.target.closest('button[data-id]');
		if (!btn) return;
		const id = parseInt(btn.getAttribute('data-id'), 10);
		const item = INGREDIENTS.find(x => x.id === id);
		if (!item) return;
		hiddenId.value = String(item.id);
		search.value = item.name;
		configureUnitChoices(item.unit || '');
		results.classList.add('hidden');
	});

	select.addEventListener('change', ()=>{
		hiddenId.value = '';
		if (select.selectedIndex > 0){
			const opt = select.selectedOptions[0];
			search.value = opt.textContent || '';
			configureUnitChoices(opt.dataset.unit || '');
		} else {
			search.value = '';
			configureUnitChoices('');
		}
	});

	document.addEventListener('click', (e)=>{
		if (!results.contains(e.target) && e.target !== search){
			results.classList.add('hidden');
		}
	});

	function refreshSubmitState(){
		const itemCount = listBody.children.length;
		submitBtn.disabled = itemCount === 0;
		countBadge.textContent = String(itemCount);
		
		// Show/hide empty state
		if (itemCount === 0) {
			emptyState.classList.remove('hidden');
		} else {
			emptyState.classList.add('hidden');
		}
	}

	function formatNum(n){
		return (Math.round((n + Number.EPSILON) * 100) / 100).toString();
	}

	function addRow(itemId, name, baseUnit, baseQuantity, displayUnit, displayFactor){
		// Check if ingredient already exists
		const existing = listBody.querySelector('tr[data-id="'+itemId+'"]');
		if (existing){
			const hiddenQ = existing.querySelector('input[name="quantity[]"]');
			const currentBase = parseFloat(hiddenQ.value || '0');
			const newBase = currentBase + baseQuantity;
			hiddenQ.value = newBase;
			const rowFactor = parseFloat(existing.getAttribute('data-factor') || '1');
			const rowDisplayUnit = existing.getAttribute('data-display') || baseUnit;
			existing.querySelector('.qval').textContent = formatNum(newBase / rowFactor);
			existing.querySelector('.uval').textContent = rowDisplayUnit;
			
			// Show success animation
			existing.classList.add('bg-green-50');
			setTimeout(() => existing.classList.remove('bg-green-50'), 1000);
			return;
		}
		
		const tr = document.createElement('tr');
		tr.setAttribute('data-id', itemId);
		const factor = displayFactor || 1;
		const shownQty = baseQuantity / factor;
		tr.setAttribute('data-factor', String(factor));
		tr.setAttribute('data-display', displayUnit || baseUnit);
		tr.innerHTML = `
			<td class="px-6 py-4">
				<div class="flex items-center gap-3">
					<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
					<span class="font-medium text-gray-900">${name}</span>
					<input type="hidden" name="item_id[]" value="${itemId}">
				</div>
			</td>
			<td class="px-6 py-4">
				<span class="font-medium text-gray-900 qval">${formatNum(shownQty)}</span>
				<input type="hidden" name="quantity[]" value="${baseQuantity}">
			</td>
			<td class="px-6 py-4">
				<span class="text-gray-600 uval">${displayUnit || baseUnit}</span>
			</td>
			<td class="px-6 py-4">
				<button type="button" class="removeRow inline-flex items-center gap-1 px-3 py-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
					<i data-lucide="trash-2" class="w-3 h-3"></i>
					Remove
				</button>
			</td>
		`;
		listBody.appendChild(tr);
		
		// Show success animation
		tr.classList.add('bg-green-50');
		setTimeout(() => tr.classList.remove('bg-green-50'), 1000);
		
		refreshSubmitState();
	}

	addBtn.addEventListener('click', ()=>{
		let itemId = parseInt(hiddenId.value || '0', 10);
		let name = search.value || '';
		if (!itemId){
			const selId = parseInt(select.value || '0', 10);
			if (selId){
				itemId = selId;
				name = select.selectedOptions[0]?.textContent || name;
			}
		}
		const quantity = parseFloat(qty.value || '0');
		if (!itemId || !quantity || quantity <= 0){ 
			// Show error state
			addBtn.classList.add('bg-red-600');
			addBtn.innerHTML = '<i data-lucide="x" class="w-4 h-4"></i>Invalid Input';
			setTimeout(() => {
				addBtn.classList.remove('bg-red-600');
				addBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Add to List';
			}, 1500);
			return; 
		}
		
		// Convert to base if needed
		let factor = 1;
		if (currentBaseUnit === 'g' && unitSel.value === 'kg') factor = 1000;
		if (currentBaseUnit === 'ml' && unitSel.value === 'L') factor = 1000;
		const baseQty = quantity * factor;
		const displayUnit = unitSel.value || currentBaseUnit;
		
		addRow(itemId, name, currentBaseUnit, baseQty, displayUnit, factor);
		
		// Clear form
		qty.value = '';
		hiddenId.value='';
		search.value='';
		select.value='';
		configureUnitChoices('');
		
		// Show success feedback
		addBtn.classList.add('bg-green-600');
		addBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>Added!';
		setTimeout(() => {
			addBtn.classList.remove('bg-green-600');
			addBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Add to List';
		}, 1000);
	});

	listBody.addEventListener('click', (e)=>{
		if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')){
			const tr = e.target.closest('tr');
			tr.classList.add('bg-red-50');
			setTimeout(() => {
				tr.remove();
				refreshSubmitState();
			}, 300);
		}
	});

	clearBtn.addEventListener('click', ()=>{
		if (listBody.children.length === 0) return;
		
		// Animate removal
		Array.from(listBody.children).forEach((row, index) => {
			setTimeout(() => {
				row.classList.add('bg-red-50');
				setTimeout(() => row.remove(), 200);
			}, index * 50);
		});
		
		setTimeout(() => refreshSubmitState(), listBody.children.length * 50 + 200);
	});

	refreshSubmitState();
})();
</script>


