<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Deliveries</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white border rounded p-4 mb-6">
	<h2 class="text-lg font-semibold mb-3">Record Delivery</h2>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div class="md:col-span-2">
			<label class="block text-sm mb-1">Purchase</label>
			<select name="purchase_id" class="w-full border rounded px-3 py-2" required>
				<option value="">Select Purchase</option>
				<?php foreach ($purchases as $p): $deliv = (float)($deliveredTotals[(int)$p['id']] ?? 0); ?>
					<option value="<?php echo (int)$p['id']; ?>">
						#<?php echo (int)$p['id']; ?> — <?php echo htmlspecialchars($p['item_name']); ?> (<?php echo number_format((float)$p['quantity'],2); ?> <?php echo htmlspecialchars($p['unit']); ?>) — Delivered: <?php echo number_format($deliv,2); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<label class="block text-sm mb-1">Quantity Received</label>
			<input id="deliveryQty" type="number" step="0.01" min="0.01" name="quantity_received" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Unit</label>
			<select id="deliveryQtyUnit" name="quantity_unit" class="w-full border rounded px-3 py-2"></select>
		</div>
		<div>
			<label class="block text-sm mb-1">Status</label>
			<select name="delivery_status" class="w-full border rounded px-3 py-2">
				<option value="Partial">Partial</option>
				<option value="Complete">Complete</option>
			</select>
		</div>
		<div class="md:col-span-5">
			<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Record Delivery</button>
		</div>
	</form>
</div>

<script>
(function(){
  const purchaseSel = document.querySelector('select[name="purchase_id"]');
  const unitSel = document.getElementById('deliveryQtyUnit');
  function configure(){
    const opt = purchaseSel.selectedOptions[0];
    if (!opt) { unitSel.innerHTML=''; return; }
    // parse item unit from option text e.g. "ItemName (kg) — Delivered:"
    const m = opt.textContent.match(/\(([^)]+)\)/);
    const unit = m ? m[1] : '';
    unitSel.innerHTML = '';
    const add=(v,t)=>{ const o=document.createElement('option'); o.value=v; o.textContent=t; unitSel.appendChild(o); };
    if (unit === 'g'){ add('g','g'); add('kg','kg'); }
    else if (unit === 'ml'){ add('ml','ml'); add('L','L'); }
    else { add(unit || '', unit || ''); }
  }
  purchaseSel.addEventListener('change', configure);
  configure();
})();
</script>

<div class="bg-white border rounded">
	<div class="p-4 border-b"><h2 class="text-lg font-semibold">Recent Deliveries</h2></div>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">ID</th>
					<th class="text-left px-4 py-2">Purchase</th>
					<th class="text-left px-4 py-2">Item</th>
					<th class="text-left px-4 py-2">Qty Received</th>
					<th class="text-left px-4 py-2">Status</th>
					<th class="text-left px-4 py-2">Received</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($deliveries as $d): ?>
				<tr class="border-t">
					<td class="px-4 py-2"><?php echo (int)$d['id']; ?></td>
					<td class="px-4 py-2">#<?php echo (int)$d['purchase_id']; ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($d['item_name'] . ' (' . $d['unit'] . ')'); ?></td>
					<td class="px-4 py-2"><?php echo number_format((float)$d['quantity_received'], 2); ?></td>
					<td class="px-4 py-2">
						<span class="px-2 py-1 rounded text-xs <?php echo $d['delivery_status']==='Complete'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-800'; ?>"><?php echo htmlspecialchars($d['delivery_status']); ?></span>
					</td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($d['date_received']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


