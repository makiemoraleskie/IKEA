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
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
			<!-- Purchase Selection -->
			<div class="space-y-2 md:col-span-2">
				<label class="block text-sm font-medium text-gray-700">Select Purchase</label>
				<select name="purchase_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors" required>
					<option value="">Choose a purchase to deliver</option>
					<?php foreach ($purchases as $p): $deliv = (float)($deliveredTotals[(int)$p['id']] ?? 0); ?>
						<option value="<?php echo (int)$p['id']; ?>">
							#<?php echo (int)$p['id']; ?> — <?php echo htmlspecialchars($p['item_name']); ?> (<?php echo number_format((float)$p['quantity'],2); ?> <?php echo htmlspecialchars($p['unit']); ?>) — Delivered: <?php echo number_format($deliv,2); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="text-xs text-gray-500">Select the purchase order to record delivery for</p>
			</div>
			
			<!-- Quantity Received -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Quantity Received</label>
				<input id="deliveryQty" type="number" step="0.01" min="0.01" name="quantity_received" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors" placeholder="Enter amount" required />
			</div>
			
			<!-- Unit -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Unit</label>
				<select id="deliveryQtyUnit" name="quantity_unit" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
					<option value="">Auto-detect</option>
				</select>
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
					<th class="text-left px-6 py-3 font-medium text-gray-700">Purchase</th>
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
								<span class="text-xs font-semibold text-purple-600">#<?php echo (int)$d['purchase_id']; ?></span>
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


