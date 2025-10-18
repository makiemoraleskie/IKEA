<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Purchases</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white border rounded p-4 mb-6">
	<h2 class="text-lg font-semibold mb-3">New Purchase</h2>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div>
			<label class="block text-sm mb-1">Ingredient</label>
			<select name="item_id" class="w-full border rounded px-3 py-2" required>
				<option value="">Select</option>
				<?php foreach ($ingredients as $ing): ?>
					<option value="<?php echo (int)$ing['id']; ?>"><?php echo htmlspecialchars($ing['name'] . ' (' . $ing['unit'] . ')'); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<label class="block text-sm mb-1">Supplier</label>
			<input name="supplier" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Quantity</label>
			<input id="purchaseQty" type="number" step="0.01" min="0.01" name="quantity" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Unit</label>
			<select id="purchaseQtyUnit" name="quantity_unit" class="w-full border rounded px-3 py-2">
				<option value="">auto</option>
			</select>
		</div>
		<div>
			<label class="block text-sm mb-1">Cost</label>
			<input type="number" step="0.01" min="0" name="cost" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Receipt (JPG/PNG/WebP/PDF, max 5MB)</label>
			<input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full border rounded px-3 py-2" />
		</div>
		<div class="md:col-span-2">
			<label class="block text-sm mb-1">Payment Status</label>
			<select name="payment_status" class="w-full border rounded px-3 py-2">
				<option value="Pending">Pending</option>
				<option value="Paid">Paid</option>
			</select>
		</div>
		<div class="md:col-span-3">
			<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Record Purchase</button>
		</div>
	</form>
</div>

<div class="bg-white border rounded">
	<div class="p-4 border-b"><h2 class="text-lg font-semibold">Recent Purchases</h2></div>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">ID</th>
					<th class="text-left px-4 py-2">Purchaser</th>
					<th class="text-left px-4 py-2">Item</th>
					<th class="text-left px-4 py-2">Qty</th>
					<th class="text-left px-4 py-2">Cost</th>
					<th class="text-left px-4 py-2">Supplier</th>
					<th class="text-left px-4 py-2">Payment</th>
					<th class="text-left px-4 py-2">Receipt</th>
					<th class="text-left px-4 py-2">Delivered</th>
					<th class="text-left px-4 py-2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($purchases as $p): $deliv = (float)($deliveredTotals[(int)$p['id']] ?? 0); ?>
				<tr class="border-t">
					<td class="px-4 py-2"><?php echo (int)$p['id']; ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($p['purchaser_name']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($p['item_name']); ?> (<?php echo htmlspecialchars($p['display_unit'] ?: $p['unit']); ?>)</td>
					<td class="px-4 py-2"><?php echo isset($p['display_factor']) && (float)$p['display_factor'] > 0 ? number_format((float)$p['quantity'] / (float)$p['display_factor'], 2) : htmlspecialchars($p['quantity']); ?></td>
					<td class="px-4 py-2">₱<?php echo number_format((float)$p['cost'], 2); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($p['supplier']); ?></td>
					<td class="px-4 py-2">
						<span class="px-2 py-1 rounded text-xs <?php echo $p['payment_status']==='Paid'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-800'; ?>"><?php echo htmlspecialchars($p['payment_status']); ?></span>
					</td>
					<td class="px-4 py-2">
						<?php if (!empty($p['receipt_url'])): ?>
							<a class="text-blue-600" href="<?php echo htmlspecialchars($p['receipt_url']); ?>" target="_blank">View</a>
						<?php else: ?>
							<span class="text-gray-400">—</span>
						<?php endif; ?>
					</td>
					<td class="px-4 py-2"><?php echo number_format($deliv, 2) . ' / ' . number_format((float)$p['quantity'], 2); ?></td>
					<td class="px-4 py-2">
						<div class="flex gap-2">
							<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases/mark-paid">
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
								<input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
								<button class="px-3 py-1.5 rounded bg-emerald-600 text-white">Mark Paid</button>
							</form>
								<a class="px-3 py-1.5 rounded bg-gray-800 text-white" href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries">Deliveries</a>
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
  const select = document.querySelector('select[name="item_id"]');
  const unitSel = document.getElementById('purchaseQtyUnit');
  function configure(){
    const opt = select.selectedOptions[0];
    const text = opt ? (opt.textContent || '') : '';
    const m = text.match(/\(([^)]+)\)$/);
    const unit = m ? m[1] : '';
    unitSel.innerHTML = '';
    function add(v,t){ const o=document.createElement('option'); o.value=v; o.textContent=t; unitSel.appendChild(o); }
    if (unit === 'g'){ add('g','g'); add('kg','kg'); }
    else if (unit === 'ml'){ add('ml','ml'); add('L','L'); }
    else { add(unit || '', unit || ''); }
  }
  select.addEventListener('change', configure);
  configure();
})();
</script>

