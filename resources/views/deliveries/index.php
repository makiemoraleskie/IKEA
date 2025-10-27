<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Delivery Management</h1>
		<p class="text-gray-600 mt-1">Record and track ingredient deliveries</p>
	</div>
	<a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<!-- Summary Cards -->
<?php if (!empty($deliveries)): ?>
<?php 
// Calculate delivery statistics
$totalDeliveries = count($deliveries);
$completeDeliveries = 0;
$partialDeliveries = 0;
foreach ($deliveries as $d) {
	if ($d['delivery_status'] === 'Complete') {
		$completeDeliveries++;
	} else {
		$partialDeliveries++;
	}
}
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
	<!-- Total Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Deliveries</p>
				<p class="text-2xl font-bold text-gray-900"><?php echo $totalDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
				<i data-lucide="truck" class="w-6 h-6 text-blue-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Complete Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Complete Deliveries</p>
				<p class="text-2xl font-bold text-green-600"><?php echo $completeDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
				<i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Partial Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Partial Deliveries</p>
				<p class="text-2xl font-bold text-yellow-600"><?php echo $partialDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
				<i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Record Delivery Form -->
<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="package-check" class="w-5 h-5 text-orange-600"></i>
			Record New Delivery
		</h2>
		<p class="text-sm text-gray-600 mt-1">Record a delivery for an existing purchase</p>
	</div>
	
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="p-6" id="deliveriesForm">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        <input type="hidden" name="items_json" id="deliveriesItemsJson" value="[]">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Purchase Batch Selection -->
            <div class="space-y-2 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Select Purchase Batch</label>
                <select id="batchSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="">Choose a batch</option>
                    <?php foreach (($purchaseGroups ?? []) as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['group_id']); ?>"><?php echo '#'.htmlspecialchars($g['group_id']).' — '.htmlspecialchars($g['supplier']).' — '.htmlspecialchars($g['purchaser_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500">After selecting a batch, set per-item received quantities below.</p>
            </div>
			
            <!-- Quantity fields are handled per-row when a batch is chosen -->
            <div class="space-y-2 hidden">
                <label class="block text-sm font-medium text-gray-700">Quantity Received</label>
                <input id="deliveryQty" type="number" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Enter amount" />
            </div>
            <div class="space-y-2 hidden">
                <label class="block text-sm font-medium text-gray-700">Unit</label>
                <select id="deliveryQtyUnit" class="w-full border border-gray-300 rounded-lg px-4 py-3"><option value="">Auto-detect</option></select>
            </div>
			
			<!-- Delivery Status -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Delivery Status</label>
				<select name="delivery_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
					<option value="Partial">Partial Delivery</option>
					<option value="Complete">Complete Delivery</option>
				</select>
			</div>
		</div>
		
        <div id="batchItemsBox" class="mt-4 hidden">
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full text-sm" id="batchItemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-2">Item</th>
                            <th class="text-left px-4 py-2">Remaining</th>
                            <th class="text-left px-4 py-2">Receive Now</th>
                            <th class="text-left px-4 py-2">Unit</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
			<button type="submit" class="inline-flex items-center gap-2 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="package-check" class="w-4 h-4"></i>
				Record Delivery
			</button>
		</div>
	</form>
</div>

<!-- Recent Deliveries Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
					Recent Deliveries
				</h2>
				<p class="text-sm text-gray-600 mt-1">View and track all delivery records</p>
			</div>
			<div class="flex items-center gap-4">
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($deliveries); ?></span> total deliveries
				</div>
				<?php if (isset($partialDeliveries) && $partialDeliveries > 0): ?>
					<div class="flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
						<i data-lucide="clock" class="w-4 h-4"></i>
						<?php echo $partialDeliveries; ?> partial
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Delivery ID</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Batch</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Item</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Quantity Received</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Status</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Date Received</th>
                </tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($deliveries as $d): ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-orange-600">#<?php echo (int)$d['id']; ?></span>
							</div>
						</div>
					</td>
					
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <?php $ts = substr((string)($d['date_purchased'] ?? ''),0,19); $batchId = substr(sha1(($d['purchaser_id']??'').'|'.($d['supplier']??'').'|'.($d['payment_status']??'').'|'.($d['receipt_url']??'').'|'.$ts),0,10); ?>
                                <span class="text-xs font-semibold text-purple-600">#<?php echo htmlspecialchars($batchId); ?></span>
                            </div>
                        </div>
                    </td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
							</div>
							<div>
								<div class="font-medium text-gray-900"><?php echo htmlspecialchars($d['item_name']); ?></div>
								<div class="text-xs text-gray-500"><?php echo htmlspecialchars($d['unit']); ?></div>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<span class="font-semibold text-gray-900"><?php echo number_format((float)$d['quantity_received'], 2); ?></span>
							<span class="text-gray-500 text-sm"><?php echo htmlspecialchars($d['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$statusClass = $d['delivery_status'] === 'Complete' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
						$statusIcon = $d['delivery_status'] === 'Complete' ? 'check-circle' : 'clock';
						?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
							<i data-lucide="<?php echo $statusIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($d['delivery_status']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<span class="text-gray-600"><?php echo htmlspecialchars($d['date_received']); ?></span>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<?php if (empty($deliveries)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="truck" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Deliveries Found</h3>
			<p class="text-sm text-gray-600 mb-4">Start by recording your first delivery</p>
			<button onclick="document.querySelector('form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Record First Delivery
			</button>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
(function(){
  const GROUPS = <?php echo json_encode(array_map(function($g) use ($deliveredTotals) {
    return [
      'group_id' => $g['group_id'],
      'items' => array_map(function($p) use ($deliveredTotals){
        $del = (float)($deliveredTotals[(int)$p['id']] ?? 0);
        return [
          'purchase_id' => (int)$p['id'],
          'item_name' => $p['item_name'],
          'unit' => $p['unit'],
          'display_unit' => $p['display_unit'],
          'display_factor' => (float)$p['display_factor'],
          'quantity' => (float)$p['quantity'],
          'delivered' => $del,
        ];
      }, $g['items'])
    ];
  }, ($purchaseGroups ?? [])), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

  const sel = document.getElementById('batchSelect');
  const box = document.getElementById('batchItemsBox');
  const tableBody = document.querySelector('#batchItemsTable tbody');
  const itemsJson = document.getElementById('deliveriesItemsJson');
  const form = document.getElementById('deliveriesForm');

  function buildUnitOptions(baseUnit, dispUnit){
    const sel = document.createElement('select'); sel.className='border rounded px-3 py-2'; sel.name='row_unit[]';
    const add=(v,t)=>{ const o=document.createElement('option'); o.value=v; o.textContent=t; sel.appendChild(o); };
    if (baseUnit === 'g'){ add('g','g'); add('kg','kg'); }
    else if (baseUnit === 'ml'){ add('ml','ml'); add('L','L'); }
    else { add(baseUnit, baseUnit); }
    if (dispUnit && ![baseUnit].includes(dispUnit)) {
      let exists=false; for (const o of sel.options) if (o.value===dispUnit) exists=true; if (!exists) add(dispUnit, dispUnit);
    }
    return sel;
  }

  function render(groupId){
    tableBody.innerHTML='';
    const g = GROUPS.find(x=>x.group_id===groupId);
    if (!g){ box.classList.add('hidden'); itemsJson.value='[]'; return; }
    for (const it of g.items){
      const remaining = Math.max(0, (it.quantity - it.delivered));
      const tr = document.createElement('tr');
      tr.innerHTML = `<td class="px-4 py-2">${it.item_name}<input type="hidden" name="purchase_id[]" value="${it.purchase_id}"></td>
                      <td class="px-4 py-2 text-gray-600">${remaining.toFixed(2)} ${it.unit}</td>
                      <td class="px-4 py-2"><input type="number" step="0.01" min="0" name="row_qty[]" value="${remaining.toFixed(2)}" class="w-32 border rounded px-3 py-2" /></td>`;
      const unitTd = document.createElement('td'); unitTd.className='px-4 py-2';
      const unitSel = buildUnitOptions(it.unit, it.display_unit);
      unitTd.appendChild(unitSel);
      tr.appendChild(unitTd);
      tableBody.appendChild(tr);
    }
    box.classList.remove('hidden');
    sync();
  }

  function sync(){
    const rows = [];
    const ids = Array.from(document.querySelectorAll('input[name="purchase_id[]"]')).map(i=>parseInt(i.value,10));
    const qtys = Array.from(document.querySelectorAll('input[name="row_qty[]"]')).map(i=>parseFloat(i.value||'0'));
    const units = Array.from(document.querySelectorAll('select[name="row_unit[]"]')).map(s=>s.value||'');
    for (let i=0;i<ids.length;i++){
      const q = qtys[i]; if (!ids[i] || !q || q<=0) continue;
      rows.push({ purchase_id: ids[i], quantity: q, unit: units[i] });
    }
    itemsJson.value = JSON.stringify(rows);
  }

  sel.addEventListener('change', ()=>{ render(sel.value); });
  document.addEventListener('input', (e)=>{ if (e.target.matches('input[name="row_qty[]"]')) sync(); });
  document.addEventListener('change', (e)=>{ if (e.target.matches('select[name="row_unit[]"]')) sync(); });
})();
</script>


