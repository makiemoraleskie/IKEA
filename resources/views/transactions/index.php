<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
	<div>
		<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">Purchase Transactions</h1>
		<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Record and manage ingredient purchases</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-1 md:gap-1.5 px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
		Back to Dashboard
	</a>
</div>

<!-- New Purchase Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b">
		<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
			<i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4 text-purple-600"></i>
			Record New Purchase
		</h2>
		<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Add a new ingredient purchase to the system</p>
	</div>
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" class="p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<!-- Ingredient Selection -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Ingredient</label>
				<select name="item_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
					<option value="">Select ingredient</option>
					<?php foreach ($ingredients as $ing): ?>
						<option value="<?php echo (int)$ing['id']; ?>"><?php echo htmlspecialchars($ing['name'] . ' (' . $ing['unit'] . ')'); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<!-- Supplier -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Supplier</label>
				<input name="supplier" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="Supplier name" required />
			</div>
			
			<!-- Payment Status -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Payment Status</label>
				<select name="payment_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
					<option value="Pending">Pending</option>
					<option value="Paid">Paid</option>
				</select>
			</div>
			
			<!-- Quantity -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Quantity</label>
				<input id="purchaseQty" type="number" step="0.01" min="0.01" name="quantity" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="Enter quantity" required />
			</div>
			
			<!-- Unit -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Unit</label>
				<select id="purchaseQtyUnit" name="quantity_unit" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
					<option value="">Auto-detect</option>
				</select>
			</div>
			
			<!-- Cost -->
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Total Cost</label>
				<div class="relative">
					<span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
					<input type="number" step="0.01" min="0" name="cost" class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="0.00" required />
				</div>
			</div>
			
			<!-- Receipt Upload -->
			<div class="space-y-2 md:col-span-2 lg:col-span-3">
				<label class="block text-sm font-medium text-gray-700">Receipt Upload</label>
				<div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-400 transition-colors">
					<input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" class="hidden" id="receiptUpload" />
					<label for="receiptUpload" class="cursor-pointer">
						<i data-lucide="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
						<p class="text-sm text-gray-600">Click to upload receipt</p>
						<p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP, PDF (max 5MB)</p>
					</label>
				</div>
			</div>
		</div>
		
		<div class="mt-6 flex justify-end">
			<button type="submit" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="shopping-cart" class="w-4 h-4"></i>
				Record Purchase
			</button>
		</div>
	</form>
</div>

<!-- Recent Purchases Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="receipt" class="w-5 h-5 text-gray-600"></i>
					Recent Purchases
				</h2>
				<p class="text-sm text-gray-600 mt-1">View and manage all purchase transactions</p>
			</div>
			<div class="flex items-center gap-4">
				<div class="text-sm text-gray-600">
					<span class="font-medium"><?php echo count($purchases); ?></span> total purchases
				</div>
				<?php 
				$pendingCount = 0;
				foreach ($purchases as $p) {
					if ($p['payment_status'] === 'Pending') {
						$pendingCount++;
					}
				}
				?>
				<?php if ($pendingCount > 0): ?>
					<div class="flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
						<i data-lucide="clock" class="w-4 h-4"></i>
						<?php echo $pendingCount; ?> pending
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Purchase ID</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Purchaser</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Item</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Quantity</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Cost</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Supplier</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Payment</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Receipt</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Delivery Status</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($purchases as $p): $deliv = (float)($deliveredTotals[(int)$p['id']] ?? 0); ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-purple-600">#<?php echo (int)$p['id']; ?></span>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
								<span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($p['purchaser_name'], 0, 2)); ?></span>
							</div>
							<span class="font-medium text-gray-900"><?php echo htmlspecialchars($p['purchaser_name']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
							</div>
							<div>
								<div class="font-medium text-gray-900"><?php echo htmlspecialchars($p['item_name']); ?></div>
								<div class="text-xs text-gray-500"><?php echo htmlspecialchars($p['display_unit'] ?: $p['unit']); ?></div>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<span class="font-semibold text-gray-900"><?php echo isset($p['display_factor']) && (float)$p['display_factor'] > 0 ? number_format((float)$p['quantity'] / (float)$p['display_factor'], 2) : htmlspecialchars($p['quantity']); ?></span>
							<span class="text-gray-500 text-sm"><?php echo htmlspecialchars($p['display_unit'] ?: $p['unit']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-1">
							<span class="text-lg font-bold text-gray-900">₱<?php echo number_format((float)$p['cost'], 2); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
							<i data-lucide="truck" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($p['supplier']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$paymentClass = $p['payment_status'] === 'Paid' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
						$paymentIcon = $p['payment_status'] === 'Paid' ? 'check-circle' : 'clock';
						?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $paymentClass; ?>">
							<i data-lucide="<?php echo $paymentIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($p['payment_status']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<?php if (!empty($p['receipt_url'])): ?>
							<a href="<?php echo htmlspecialchars($p['receipt_url']); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
								<i data-lucide="file-text" class="w-3 h-3"></i>
								View Receipt
							</a>
						<?php else: ?>
							<span class="text-gray-400 text-sm">No receipt</span>
						<?php endif; ?>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$deliveryPercentage = (float)$p['quantity'] > 0 ? ($deliv / (float)$p['quantity']) * 100 : 0;
						$deliveryStatus = $deliveryPercentage >= 100 ? 'Complete' : ($deliveryPercentage > 0 ? 'Partial' : 'Pending');
						$deliveryClass = $deliveryStatus === 'Complete' ? 'bg-green-100 text-green-800' : ($deliveryStatus === 'Partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
						$deliveryIcon = $deliveryStatus === 'Complete' ? 'check-circle' : ($deliveryStatus === 'Partial' ? 'clock' : 'package');
						?>
						<div class="space-y-1">
							<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium <?php echo $deliveryClass; ?>">
								<i data-lucide="<?php echo $deliveryIcon; ?>" class="w-3 h-3"></i>
								<?php echo $deliveryStatus; ?>
							</span>
							<div class="text-xs text-gray-500">
								<?php echo number_format($deliv, 2); ?> / <?php echo number_format((float)$p['quantity'], 2); ?>
							</div>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex gap-2">
							<?php if ($p['payment_status'] === 'Pending'): ?>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases/mark-paid">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
									<button class="inline-flex items-center gap-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
										<i data-lucide="check" class="w-3 h-3"></i>
										Mark Paid
									</button>
								</form>
							<?php endif; ?>
							<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
								<i data-lucide="truck" class="w-3 h-3"></i>
								Deliveries
							</a>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<?php if (empty($purchases)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="shopping-cart" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Purchases Found</h3>
			<p class="text-sm text-gray-600 mb-4">Start by recording your first purchase transaction</p>
			<button onclick="document.querySelector('form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Record First Purchase
			</button>
		</div>
		<?php endif; ?>
	</div>
</div>

<!-- Summary Cards -->
<?php if (!empty($purchases)): ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
	<!-- Total Purchases -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Purchases</p>
				<p class="text-2xl font-bold text-gray-900"><?php echo count($purchases); ?></p>
			</div>
			<div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
				<i data-lucide="shopping-cart" class="w-6 h-6 text-purple-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Pending Payments -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Pending Payments</p>
				<p class="text-2xl font-bold text-yellow-600"><?php echo $pendingCount; ?></p>
			</div>
			<div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
				<i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Paid Purchases -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Paid Purchases</p>
				<p class="text-2xl font-bold text-green-600"><?php echo count($purchases) - $pendingCount; ?></p>
			</div>
			<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
				<i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Total Cost -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-sm font-medium text-gray-600">Total Cost</p>
				<p class="text-2xl font-bold text-blue-600">₱<?php echo number_format(array_sum(array_column($purchases, 'cost')), 2); ?></p>
			</div>
			<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
				<span class="text-2xl font-bold text-blue-600">₱</span>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

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

