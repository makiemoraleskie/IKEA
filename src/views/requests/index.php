<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Ingredient Requests</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner'], true)): ?>
<div class="bg-white border rounded p-4 mb-6">
    <h2 class="text-lg font-semibold mb-4">New Batch Request</h2>
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" id="requestForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Add items panel -->
            <section class="border rounded-lg p-4">
                <div class="mb-3"><span class="text-xs uppercase tracking-wide text-gray-500">Step 1</span><div class="font-medium">Choose ingredient</div></div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-3 relative">
                        <label class="block text-sm mb-1">Search</label>
                        <input id="ingredientSearch" class="w-full border rounded px-3 py-2" placeholder="Search ingredient..." autocomplete="off" />
                        <input type="hidden" id="ingredientIdHidden" />
                        <div id="ingredientResults" class="absolute z-10 mt-1 w-full bg-white border rounded shadow-sm max-h-56 overflow-auto hidden"></div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1">Or select</label>
                        <select id="ingredientSelect" class="w-full border rounded px-3 py-2">
                            <option value="">Select from list</option>
                            <?php foreach ($ingredients as $ing): ?>
                                <option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Quantity</label>
                        <input id="quantityInput" type="number" step="0.01" min="0.01" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Unit</label>
                        <select id="unitSelector" class="border rounded px-2 py-2 w-28 md:w-32"></select>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <p class="text-xs text-gray-500">All quantities are in base units (g/ml/pcs).</p>
                    <button type="button" id="addItemBtn" class="bg-gray-800 text-white px-4 py-2 rounded">Add to List</button>
                </div>
            </section>

            <!-- Right: Staged list panel -->
            <section class="border rounded-lg overflow-hidden flex flex-col">
                <div class="flex items-center justify-between bg-gray-50 px-4 py-2">
                    <div class="font-medium">Items in this request</div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Count: <span id="itemCountBadge" class="ml-1">0</span></span>
                        <button type="button" id="clearListBtn" class="text-sm text-red-600">Clear</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2">Ingredient</th>
                                <th class="text-left px-4 py-2">Quantity</th>
                                <th class="text-left px-4 py-2">Unit</th>
                                <th class="text-left px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="listBody" class="divide-y"></tbody>
                    </table>
                </div>
                <div class="p-4 border-t flex justify-end">
                    <button id="submitBtn" class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50" disabled>Submit Batch</button>
                </div>
            </section>
        </div>
    </form>

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

        function renderResults(items){
			if (!items.length){ results.classList.add('hidden'); results.innerHTML=''; return; }
			results.innerHTML = items.map(i => `<button type="button" data-id="${i.id}" class="w-full text-left px-3 py-2 hover:bg-gray-100">${i.name} <span class=\"text-xs text-gray-500\">(${i.unit})</span></button>`).join('');
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
            // reflect via selection change handler
        }

        // no separate reflection needed; unit value is directly from unitSelector

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
			// Selecting from dropdown clears search selection
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

        // nothing to do on change; value is read during add

		document.addEventListener('click', (e)=>{
			if (!results.contains(e.target) && e.target !== search){
				results.classList.add('hidden');
			}
		});

		function refreshSubmitState(){
			submitBtn.disabled = listBody.children.length === 0;
			countBadge.textContent = String(listBody.children.length);
		}

        function formatNum(n){
            return (Math.round((n + Number.EPSILON) * 100) / 100).toString();
        }

        function addRow(itemId, name, baseUnit, baseQuantity, displayUnit, displayFactor){
            // if ingredient already exists, sum base quantity and re-render display using existing factor
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
                return;
            }
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', itemId);
            const factor = displayFactor || 1;
            const shownQty = baseQuantity / factor;
            tr.setAttribute('data-factor', String(factor));
            tr.setAttribute('data-display', displayUnit || baseUnit);
            tr.innerHTML = `
                <td class="px-4 py-2">${name}<input type="hidden" name="item_id[]" value="${itemId}"></td>
                <td class="px-4 py-2 qval">${formatNum(shownQty)}<input type="hidden" name="quantity[]" value="${baseQuantity}"></td>
                <td class="px-4 py-2 uval">${displayUnit || baseUnit}</td>
                <td class="px-4 py-2"><button type="button" class="removeRow text-red-600">Remove</button></td>
            `;
            listBody.appendChild(tr);
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
			if (!itemId || !quantity || quantity <= 0){ return; }
            // Convert to base if needed and set display unit accordingly
            let factor = 1;
            if (currentBaseUnit === 'g' && unitSel.value === 'kg') factor = 1000;
            if (currentBaseUnit === 'ml' && unitSel.value === 'L') factor = 1000; // L to ml
            const baseQty = quantity * factor;
			const displayUnit = unitSel.value || currentBaseUnit;
			addRow(itemId, name, currentBaseUnit, baseQty, displayUnit, factor);
			qty.value = '';
			hiddenId.value='';
			search.value='';
			select.value='';
            configureUnitChoices('');
		});

		listBody.addEventListener('click', (e)=>{
			if (e.target.classList.contains('removeRow')){
				const tr = e.target.closest('tr');
				tr.remove();
				refreshSubmitState();
			}
		});

		clearBtn.addEventListener('click', ()=>{
			listBody.innerHTML = '';
			refreshSubmitState();
		});

		refreshSubmitState();
	})();
	</script>
</div>
<?php endif; ?>

<div class="bg-white border rounded">
	<div class="p-4 border-b"><h2 class="text-lg font-semibold">Batch Requests</h2></div>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">Batch</th>
					<th class="text-left px-4 py-2">Staff</th>
					<th class="text-left px-4 py-2">Items</th>
					<th class="text-left px-4 py-2">Status</th>
					<th class="text-left px-4 py-2">Requested</th>
					<th class="text-left px-4 py-2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<tr class="border-t">
					<td class="px-4 py-2">#<?php echo (int)$b['id']; ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></td>
					<td class="px-4 py-2">
						<?php $count=(int)($b['items_count'] ?? 0); ?>
						<?php if ($count === 1 && !empty($items)): ?>
							<?php $it=$items[0]; ?>
							<?php echo htmlspecialchars($it['item_name']); ?> — <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?>
						<?php else: ?>
							<?php echo $count; ?> items
							<?php if ($count > 1): ?>
								<button type="button" class="ml-2 text-blue-600 underline text-xs" onclick="document.getElementById('batch-<?php echo (int)$b['id']; ?>').classList.toggle('hidden')">View</button>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td class="px-4 py-2">
						<span class="px-2 py-1 rounded text-xs <?php echo $b['status']==='Approved'?'bg-green-100 text-green-700':($b['status']==='Rejected'?'bg-red-100 text-red-700':'bg-yellow-100 text-yellow-800'); ?>"><?php echo htmlspecialchars($b['status']); ?></span>
					</td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($b['date_requested']); ?></td>
					<td class="px-4 py-2">
						<?php if (in_array(Auth::role(), ['Owner','Manager'], true) && $b['status'] === 'Pending'): ?>
							<div class="flex gap-2">
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/approve">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="px-3 py-1.5 rounded bg-green-600 text-white">Approve Batch</button>
								</form>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/reject">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="px-3 py-1.5 rounded bg-red-600 text-white">Reject Batch</button>
								</form>
							</div>
						<?php else: ?>
							<span class="text-gray-400">—</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden bg-gray-50">
					<td colspan="6" class="px-6 py-3">
						<ul class="list-disc pl-5 text-sm">
							<?php foreach ($items as $it): ?>
							<li><?php echo htmlspecialchars($it['item_name']); ?> — <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


