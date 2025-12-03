<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<div>
			<h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">Purchase Transactions</h1>
			<p class="text-xs md:text-sm text-gray-600">Record and manage ingredient purchases</p>
		</div>
	</div>
</div>

<!-- Summary Cards -->
<?php 
$pendingCount = 0;
if (!empty($purchases)) {
	foreach ($purchases as $p) {
		if (($p['payment_status'] ?? '') === 'Pending') {
			$pendingCount++;
		}
	}
}
?>
<?php if (!empty($purchases)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 md:mb-8">
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

<!-- New Batch Purchase Form -->
<?php 
$paymentFilter = strtolower((string)($_GET['payment'] ?? 'all'));
?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden">
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 sm:px-6 py-4 border-b">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
            <i data-lucide="shopping-cart" class="w-5 h-5 text-purple-600"></i>
            Record New Purchase (Batch)
        </h2>
        <p class="text-sm text-gray-600 mt-1">Search ingredient, set qty/unit/cost, add to list, then save</p>
    </div>
    
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" class="p-4 sm:p-6" id="purchaseForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Add item panel -->
            <section class="border rounded-lg p-4">
                <div class="mb-3"><span class="text-xs uppercase tracking-wide text-gray-500">Step 1</span><div class="font-medium">Add purchase items</div></div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <!-- Ingredient -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Ingredient</label>
                        <input id="ingInput" type="text" class="w-full border rounded-lg px-4 py-3" placeholder="Enter ingredient name" />
                        <input type="hidden" id="ingIdHidden" />
                    </div>
                    <!-- Qty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Qty</label>
                        <input id="qtyInput" type="number" step="0.01" min="0.01" class="w-full border rounded-lg px-4 py-3" placeholder="0.01" />
                    </div>
                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit</label>
                        <select id="unitSelect" class="w-full border rounded-lg px-4 py-3">
                            <option value="">Select unit</option>
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                            <option value="ml">ml</option>
                            <option value="L">L</option>
                            <option value="pcs">pcs</option>
                            <option value="sack">sack</option>
                            <option value="box">box</option>
                        </select>
                    </div>
                    <!-- Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cost</label>
                        <input id="costInput" type="number" step="0.01" min="0" class="w-full border rounded-lg px-4 py-3" placeholder="0.00" />
                    </div>
                    <div class="md:col-span-5 flex justify-end">
                        <button type="button" id="addRowBtn" class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2 rounded-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add to list
                        </button>
                    </div>
                </div>
            </section>

            <!-- Right: Staged list & payment panel -->
            <section class="border rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 flex items-center justify-between">
                    <div class="font-medium">Items in this purchase</div>
                    <div class="text-sm text-gray-600">Total: ₱<span id="totalCost">0.00</span></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2">Ingredient</th>
                                <th class="text-left px-4 py-2">Qty</th>
                                <th class="text-left px-4 py-2">Unit</th>
                                <th class="text-left px-4 py-2">Cost</th>
                                <th class="text-left px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseList"></tbody>
                    </table>
                </div>
                <div class="p-4 border-t grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="items_json" id="itemsJson" value="[]">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input name="supplier" class="w-full border rounded-lg px-4 py-3" placeholder="Supplier name" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Type</label>
                        <select name="purchase_type" id="purchaseType" class="w-full border rounded-lg px-4 py-3">
                            <option value="in_store">In-store purchase</option>
                            <option value="delivery">Delivery</option>
                        </select>
                        <input type="hidden" name="payment_status" id="paymentStatusHidden" value="Paid">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select name="payment_type" id="paymentType" class="w-full border rounded-lg px-4 py-3">
                            <option value="Card">Card</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                    <div id="cashFields" class="md:col-span-3 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Base Amount (Cash)</label>
                                <input type="number" step="0.01" min="0" name="base_amount" id="baseAmount" class="w-full border rounded-lg px-4 py-3" placeholder="0.00" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total</label>
                                <input type="text" id="totalCostReadonly" class="w-full border rounded-lg px-4 py-3 bg-gray-50" value="0.00" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Change</label>
                                <input type="text" id="changeReadonly" class="w-full border rounded-lg px-4 py-3 bg-gray-50" value="0.00" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Receipt Upload</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-400 transition-colors" id="receiptDropzone">
                            <input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf" class="hidden" id="receiptUpload" required />
                            <label for="receiptUpload" class="cursor-pointer flex flex-col items-center gap-2">
                                <i data-lucide="upload" class="w-8 h-8 text-gray-400 mx-auto"></i>
                                <p class="text-sm text-gray-600">Click to upload receipt (applies to batch)</p>
                                <p class="text-xs text-gray-500">JPG, PNG, WebP, HEIC or PDF — up to 10MB</p>
                            </label>
                            <div id="receiptSelected" class="mt-3 hidden text-left bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p id="receiptFileName" class="text-sm font-medium text-gray-800 truncate"></p>
                                    <p id="receiptFileSize" class="text-xs text-gray-500"></p>
                                </div>
                                <button type="button" id="receiptClearBtn" class="text-xs text-red-600 hover:underline whitespace-nowrap">Remove</button>
                            </div>
                            <p id="receiptError" class="mt-2 text-sm text-red-600 hidden"></p>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t flex justify-end">
                    <button type="submit" id="recordPurchaseBtn" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        Record Purchase Batch
                    </button>
                </div>
            </section>
        </div>
    </form>
</div>

<!-- Recent Purchases Table (Grouped) -->
<div id="recent-purchases" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
		<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
			<div>
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-gray-600"></i>
                    Recent Purchases
                </h2>
				<p class="text-sm text-gray-600 mt-1">View and manage all purchase transactions</p>
			</div>
			<div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-4">
				<div class="flex items-center gap-4">
					<div class="text-sm text-gray-600">
						<span class="font-medium"><?php echo isset($purchaseGroups) ? count($purchaseGroups) : count($purchases); ?></span> total purchases
					</div>
					<?php if ($pendingCount > 0): ?>
						<div class="flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
							<i data-lucide="clock" class="w-4 h-4"></i>
							<?php echo $pendingCount; ?> pending
						</div>
					<?php endif; ?>
				</div>
				<div class="flex items-center gap-2 text-sm text-gray-600">
					<label for="paymentStatusFilter" class="whitespace-nowrap">Filter payment:</label>
					<select id="paymentStatusFilter" data-default="<?php echo htmlspecialchars($paymentFilter); ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
						<option value="all">All</option>
						<option value="paid">Paid</option>
						<option value="pending">Pending</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
			<thead class="bg-gray-50">
				<tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Batch</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Purchaser</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Items</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Total Qty</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Cost</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Supplier</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Payment</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Receipt</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-700">Delivery Status</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
				</tr>
			</thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach (($purchaseGroups ?? []) as $g): ?>
                <tr class="hover:bg-gray-50 transition-colors" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-purple-600">#<?php echo htmlspecialchars($g['group_id']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($g['purchaser_name'], 0, 2)); ?></span>
                            </div>
                            <span class="font-medium text-gray-900"><?php echo htmlspecialchars($g['purchaser_name']); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php $count = count($g['items']); ?>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900"><?php echo $count; ?> item<?php echo $count>1?'s':''; ?></span>
                            <?php if ($count>1): ?>
                                <button type="button" class="text-blue-600 underline text-xs openPurchaseModal" data-group-id="<?php echo htmlspecialchars($g['group_id']); ?>">View</button>
                            <?php else: ?>
                                <button type="button" class="text-blue-600 underline text-xs openPurchaseModal" data-group-id="<?php echo htmlspecialchars($g['group_id']); ?>">View</button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        // Use purchase_unit and purchase_quantity if available (show as entered, no conversion)
                        $first = $g['items'][0];
                        $purchaseUnit = trim((string)($first['purchase_unit'] ?? ''));
                        $purchaseQtySum = 0;
                        $allSameUnit = true;
                        foreach ($g['items'] as $it) {
                            $pu = trim((string)($it['purchase_unit'] ?? ''));
                            $pq = (float)($it['purchase_quantity'] ?? 0);
                            if ($pu !== '' && $pq > 0) {
                                if ($purchaseUnit === '') $purchaseUnit = $pu;
                                if ($pu !== $purchaseUnit) $allSameUnit = false;
                                $purchaseQtySum += $pq;
                            }
                        }
                        if ($purchaseUnit !== '' && $purchaseQtySum > 0 && $allSameUnit) {
                            $qtyShow = $purchaseQtySum;
                            $unitShow = $purchaseUnit;
                        } else {
                            // Fallback for old records without purchase_unit
                            $baseUnit = $first['unit'];
                            $dispUnit = $first['display_unit'] ?: ($baseUnit==='g'?'kg':($baseUnit==='ml'?'L':$baseUnit));
                            $dispFactor = (float)($first['display_factor'] ?: ($dispUnit!==$baseUnit?1000:1));
                            $qtyShow = $dispFactor>0 ? $g['quantity_sum']/$dispFactor : $g['quantity_sum'];
                            $unitShow = $dispUnit;
                        }
                        ?>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900"><?php echo number_format($qtyShow,2); ?></span>
                            <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($unitShow); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-lg font-bold text-gray-900">₱<?php echo number_format((float)$g['cost_sum'], 2); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
                            <i data-lucide="truck" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($g['supplier']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $paymentClass = $g['payment_status'] === 'Paid' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $paymentIcon = $g['payment_status'] === 'Paid' ? 'check-circle' : 'clock';
                        ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $paymentClass; ?>">
                            <i data-lucide="<?php echo $paymentIcon; ?>" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($g['payment_status']); ?>
                        </span>
                        <?php if (!empty($g['paid_at'])): ?>
                            <p class="text-xs text-gray-500 mt-1">Paid on <?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($g['paid_at']))); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (!empty($g['receipt_url'])): ?>
                            <?php $fullUrl = (preg_match('#^https?://#', $g['receipt_url'])) ? $g['receipt_url'] : (rtrim($baseUrl, '/').'/'.ltrim($g['receipt_url'], '/')); ?>
                            <a href="<?php echo htmlspecialchars($fullUrl); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                View Receipt
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">No receipt</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $deliveryPercentage = (float)$g['quantity_sum'] > 0 ? ($g['delivered_sum'] / (float)$g['quantity_sum']) * 100 : 0;
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
                                <?php echo number_format($g['delivered_sum'], 2); ?> / <?php echo number_format((float)$g['quantity_sum'], 2); ?> (base)
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (Auth::role() !== 'Purchaser'): ?>
                            <a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                <i data-lucide="truck" class="w-3 h-3"></i>
                                Deliveries
                            </a>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-3 py-2 bg-gray-300 text-gray-500 text-sm rounded-lg cursor-not-allowed" title="Not available for Purchaser role">
                                <i data-lucide="truck" class="w-3 h-3"></i>
                                Deliveries
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <!-- Hidden modal content for this group -->
                <tr class="hidden" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>" data-detail-row="true"><td colspan="10">
                    <div id="modal-content-<?php echo htmlspecialchars($g['group_id']); ?>" data-pay-type="<?php echo htmlspecialchars($g['payment_type'] ?? ''); ?>" data-cash-base="<?php echo isset($g['cash_base_amount']) ? number_format((float)$g['cash_base_amount'],2,'.','') : ''; ?>">
                        <div class="mb-4">
                            <div class="text-sm text-gray-600">Batch ID</div>
                            <div class="font-medium text-gray-900">#<?php echo htmlspecialchars($g['group_id']); ?></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <div class="text-sm text-gray-600">Purchaser</div>
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($g['purchaser_name']); ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Supplier</div>
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($g['supplier']); ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Date</div>
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($g['date_purchased']); ?></div>
                            </div>
                        </div>
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-4 py-2">Item</th>
                                        <th class="text-left px-4 py-2">Quantity</th>
                                        <th class="text-left px-4 py-2">Unit</th>
                                        <th class="text-left px-4 py-2">Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($g['items'] as $p): ?>
                                        <?php 
                                            // Use purchase_unit and purchase_quantity if available (show as entered, no conversion)
                                            $purchaseUnit = trim((string)($p['purchase_unit'] ?? ''));
                                            $purchaseQty = (float)($p['purchase_quantity'] ?? 0);
                                            
                                            // Extract item name and unit from purchase_unit
                                            // Format: "itemName|unit" when using placeholder, or just "unit" if ingredient exists
                                            $itemNameToShow = $p['item_name'];
                                            $unitToShow = '';
                                            
                                            if ($purchaseUnit !== '' && $purchaseQty > 0) {
                                                $qtyShow = $purchaseQty;
                                                // Check if purchase_unit contains item name in format "itemName|unit"
                                                if (strpos($purchaseUnit, '|') !== false) {
                                                    // purchase_unit contains item name and unit: "itemName|unit"
                                                    list($itemNameToShow, $unitToShow) = explode('|', $purchaseUnit, 2);
                                                    $itemNameToShow = trim($itemNameToShow);
                                                    $unitToShow = trim($unitToShow);
                                                } else {
                                                    // purchase_unit is just the unit (ingredient exists)
                                                    $unitToShow = $purchaseUnit;
                                                }
                                            } else {
                                                // Fallback for old records without purchase_unit
                                                $baseUnit = $p['unit'];
                                                $dispUnit = $p['display_unit'] ?: ($baseUnit==='g'?'kg':($baseUnit==='ml'?'L':$baseUnit));
                                                $dispFactor = (float)($p['display_factor'] ?: ($dispUnit!==$baseUnit?1000:1));
                                                $qtyShow = $dispFactor>0 ? (float)$p['quantity']/$dispFactor : (float)$p['quantity'];
                                                $unitToShow = $dispUnit;
                                            }
                                        ?>
                                        <tr class="border-t">
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($itemNameToShow); ?></td>
                                            <td class="px-4 py-2"><?php echo number_format($qtyShow,2); ?></td>
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($unitToShow); ?></td>
                                            <td class="px-4 py-2">₱<?php echo number_format((float)$p['cost'],2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-2 font-medium" colspan="3">Total</td>
                                        <td class="px-4 py-2 font-semibold">₱<?php echo number_format((float)$g['cost_sum'],2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="text-sm text-gray-600">Receipt</div>
                                <?php if (!empty($g['receipt_url'])): ?>
                                    <?php 
                                        $url = $g['receipt_url'];
                                        $fullUrl = (preg_match('#^https?://#', $url)) ? $url : (rtrim($baseUrl, '/').'/'.ltrim($url, '/'));
                                        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                                    ?>
                                    <?php if (in_array($ext, ['jpg','jpeg','png','webp','gif'])): ?>
                                        <img src="<?php echo htmlspecialchars($fullUrl); ?>" alt="Receipt" class="max-h-56 rounded border" />
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($fullUrl); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">
                                            <i data-lucide="file-text" class="w-3 h-3"></i>
                                            View Receipt
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-xs text-gray-500">No receipt uploaded</div>
                                <?php endif; ?>
                            </div>
                            <div class="space-y-2">
                                <div class="text-sm text-gray-600">Payment</div>
                                <?php if (($g['payment_type'] ?? 'Card') === 'Cash' && isset($g['cash_base_amount'])): ?>
                                    <?php $baseAmt = (float)$g['cash_base_amount']; $change = $baseAmt - (float)$g['cost_sum']; ?>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <div class="block text-xs text-gray-500">Base Amount</div>
                                            <div class="text-sm font-medium">₱<?php echo number_format($baseAmt,2); ?></div>
                                        </div>
                                        <div>
                                            <div class="block text-xs text-gray-500">Total</div>
                                            <div class="text-sm font-medium">₱<?php echo number_format((float)$g['cost_sum'],2); ?></div>
                                        </div>
                                        <div>
                                            <div class="block text-xs text-gray-500">Change</div>
                                            <div class="text-sm font-medium <?php echo $change<0?'text-red-600':'text-gray-900'; ?>">₱<?php echo number_format($change,2); ?></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border bg-gray-100 text-gray-800 border-gray-200">Card</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <?php if (!empty($g['receipt_url'])): ?>
                                <?php $fullUrl2 = (preg_match('#^https?://#', $g['receipt_url'])) ? $g['receipt_url'] : (rtrim($baseUrl, '/').'/'.ltrim($g['receipt_url'], '/')); ?>
                                <a href="<?php echo htmlspecialchars($fullUrl2); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">
                                    <i data-lucide="file-text" class="w-3 h-3"></i>
                                    View Receipt
                                </a>
                            <?php endif; ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo ($g['payment_status']==='Paid')?'bg-green-100 text-green-800 border-green-200':'bg-yellow-100 text-yellow-800 border-yellow-200'; ?>">
                                <i data-lucide="<?php echo ($g['payment_status']==='Paid')?'check-circle':'clock'; ?>" class="w-3 h-3"></i>
                                <?php echo htmlspecialchars($g['payment_status']); ?>
                            </span>
                            <?php if (($g['payment_status'] ?? '') === 'Pending'): ?>
                                <button
                                    type="button"
                                    class="markPaidBtn inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                    data-purchase-id="<?php echo (int)$g['first_id']; ?>"
                                    data-delivery-status="<?php echo strtolower($deliveryStatus); ?>"
                                >
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Set to Paid
                                </button>
                            <?php elseif (!empty($g['paid_at'])): ?>
                                <span class="text-xs text-gray-500">Paid on <?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($g['paid_at']))); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td></tr>
                <?php endforeach; ?>
            </tbody>
		</table>
        <div id="purchasesFilterEmpty" class="hidden px-6 py-4 text-center text-sm text-gray-500 border-t">
            No purchases match the selected payment filter.
        </div>
		
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

<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases/mark-paid" id="markPaidForm" class="hidden" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
    <input type="hidden" name="id" id="markPaidPurchaseId" value="0">
    <input type="file" name="receipt" id="markPaidReceiptInput" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf" class="hidden">
</form>


<script>
(function(){
const INGREDIENTS = <?php echo json_encode(array_map(function($i){ return ['id'=>(int)$i['id'],'name'=>$i['name'],'unit'=>$i['unit'],'display_unit'=>$i['display_unit'] ?? '', 'display_factor'=>(float)($i['display_factor'] ?? 1)]; }, $ingredients), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  const hiddenId = document.getElementById('ingIdHidden');
  const ingInput = document.getElementById('ingInput');
  const qty = document.getElementById('qtyInput');
  const unitSel = document.getElementById('unitSelect');
  const cost = document.getElementById('costInput');
  const addBtn = document.getElementById('addRowBtn');
  const listBody = document.getElementById('purchaseList');
  const itemsJson = document.getElementById('itemsJson');
  const totalCostSpan = document.getElementById('totalCost');
  const totalCostReadonly = document.getElementById('totalCostReadonly');
  const baseAmount = document.getElementById('baseAmount');
  const changeReadonly = document.getElementById('changeReadonly');
  const payType = document.getElementById('paymentType');
  const cashFields = document.getElementById('cashFields');
  const purchaseTypeSel = document.getElementById('purchaseType');
  const paymentStatusHidden = document.getElementById('paymentStatusHidden');
  const purchaseForm = document.getElementById('purchaseForm');
  const receiptInput = document.getElementById('receiptUpload');
  const receiptSelected = document.getElementById('receiptSelected');
  const receiptFileName = document.getElementById('receiptFileName');
  const receiptFileSize = document.getElementById('receiptFileSize');
  const receiptClearBtn = document.getElementById('receiptClearBtn');
  const receiptError = document.getElementById('receiptError');
  const receiptDropzone = document.getElementById('receiptDropzone');
  const recordPurchaseBtn = document.getElementById('recordPurchaseBtn');
  const RECEIPT_ALLOWED = ['image/jpeg','image/png','image/webp','image/heic','image/heif','image/heic-sequence','image/heif-sequence','application/pdf'];
  const RECEIPT_MAX_BYTES = 10 * 1024 * 1024;
  const paymentFilterSelect = document.getElementById('paymentStatusFilter');
  const purchaseRows = Array.from(document.querySelectorAll('tr[data-payment-status]')).filter(row => !row.hasAttribute('data-detail-row'));
  const purchasesFilterEmpty = document.getElementById('purchasesFilterEmpty');
  const markPaidForm = document.getElementById('markPaidForm');
  const markPaidPurchaseId = document.getElementById('markPaidPurchaseId');
  const markPaidReceiptInput = document.getElementById('markPaidReceiptInput');
  let pendingMarkPaidId = null;
  let receiptPopupTimer = null;

  function setRecordBtnReady(isReady){
    if (!recordPurchaseBtn) return;
    recordPurchaseBtn.classList.toggle('opacity-60', !isReady);
    recordPurchaseBtn.classList.toggle('cursor-not-allowed', !isReady);
    recordPurchaseBtn.dataset.ready = isReady ? '1' : '0';
  }

  function hasReceiptFile(){
    return !!(receiptInput && receiptInput.files && receiptInput.files.length > 0);
  }

  function ensureReceiptPopup(){
    let popup = document.getElementById('receiptRequirementPopup');
    if (!popup){
      popup = document.createElement('div');
      popup.id = 'receiptRequirementPopup';
      popup.className = 'fixed inset-x-0 top-6 z-[9999] flex justify-center px-4 transition-opacity duration-300 opacity-0 pointer-events-none';
      popup.innerHTML = `
        <div class="bg-red-600 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2 w-full max-w-md">
          <i data-lucide="alert-octagon" class="w-5 h-5 text-white/90"></i>
          <span class="text-sm font-semibold" data-popup-text></span>
        </div>`;
      document.body.appendChild(popup);
      const icon = popup.querySelector('i[data-lucide]');
      if (window.lucide?.createIcons && icon){
        window.lucide.createIcons({ elements: [icon] });
      }
    }
    return popup;
  }

  function showReceiptPopup(message){
    const popup = ensureReceiptPopup();
    const textEl = popup.querySelector('[data-popup-text]');
    if (textEl){ textEl.textContent = message; }
    popup.classList.remove('opacity-0','pointer-events-none');
    popup.classList.add('opacity-100');
    clearTimeout(receiptPopupTimer);
    receiptPopupTimer = setTimeout(()=>{
      popup.classList.add('opacity-0','pointer-events-none');
      popup.classList.remove('opacity-100');
    }, 2500);
  }

  function requireReceipt(event){
    if (hasReceiptFile()){
      return true;
    }
    event?.preventDefault();
    const message = 'Upload the receipt image before recording this purchase batch.';
    showReceiptError(message);
    showReceiptPopup(message);
    receiptInput?.focus();
    receiptDropzone?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return false;
  }

  setRecordBtnReady(false);
  function applyPaymentFilter(value){
    if (!purchaseRows.length) { return; }
    const normalized = value && value !== 'all' ? value.toLowerCase() : 'all';
    let visible = 0;
    purchaseRows.forEach(row => {
      const status = (row.getAttribute('data-payment-status') || '').toLowerCase();
      const matches = normalized === 'all' || status === normalized;
      row.classList.toggle('hidden', !matches);
      const detailRow = row.nextElementSibling;
      if (detailRow && detailRow.hasAttribute('data-detail-row')){
        if (!matches){
          detailRow.classList.add('hidden');
        }
      }
      if (matches){ visible++; }
    });
    if (purchasesFilterEmpty){
      purchasesFilterEmpty.classList.toggle('hidden', visible !== 0);
    }
  }
  if (paymentFilterSelect){
    const initial = (paymentFilterSelect.dataset.default || 'all').toLowerCase();
    paymentFilterSelect.value = initial;
    applyPaymentFilter(initial);
    paymentFilterSelect.addEventListener('change', ()=>{
      const value = (paymentFilterSelect.value || 'all').toLowerCase();
      const params = new URLSearchParams(window.location.search);
      if (value === 'all'){
        params.delete('payment');
      } else {
        params.set('payment', value);
      }
      const query = params.toString();
      const newUrl = window.location.pathname + (query ? `?${query}` : '') + window.location.hash;
      window.history.replaceState({}, '', newUrl);
      applyPaymentFilter(value);
    });
  } else {
    applyPaymentFilter('all');
  }
  function formatBytes(bytes){
    if (!bytes || bytes <= 0){ return '0 B'; }
    const units = ['B','KB','MB','GB'];
    const exponent = Math.min(Math.floor(Math.log(bytes)/Math.log(1024)), units.length - 1);
    const value = bytes / Math.pow(1024, exponent);
    return `${value.toFixed(exponent === 0 ? 0 : 1)} ${units[exponent]}`;
  }
  function resetReceiptUi(){
    receiptSelected?.classList.add('hidden');
    setRecordBtnReady(false);
    if (receiptError){
      receiptError.textContent = '';
      receiptError.classList.add('hidden');
    }
    receiptDropzone?.classList.remove('border-purple-400','bg-purple-50','border-red-300','bg-red-50');
  }
  function showReceiptError(message){
    if (!receiptError) return;
    receiptError.textContent = message;
    receiptError.classList.remove('hidden');
    receiptDropzone?.classList.add('border-red-300','bg-red-50');
    if (receiptInput){ receiptInput.value = ''; }
    receiptSelected?.classList.add('hidden');
    setRecordBtnReady(false);
  }
  function handleReceiptSelection(file){
    if (!file){ resetReceiptUi(); return; }
    if (!RECEIPT_ALLOWED.includes(file.type)){
      showReceiptError('Unsupported file type. Use JPG, PNG, WebP, HEIC or PDF.');
      return;
    }
    if (file.size > RECEIPT_MAX_BYTES){
      showReceiptError('File exceeds 10MB. Please compress or upload a PDF scan.');
      return;
    }
    if (receiptError){ receiptError.classList.add('hidden'); }
    if (receiptSelected){ receiptSelected.classList.remove('hidden'); }
    if (receiptFileName){ receiptFileName.textContent = file.name; }
    if (receiptFileSize){ receiptFileSize.textContent = formatBytes(file.size); }
    receiptDropzone?.classList.remove('border-red-300','bg-red-50');
    receiptDropzone?.classList.add('border-purple-400','bg-purple-50');
    setRecordBtnReady(true);
  }
  if (receiptInput){
    receiptInput.addEventListener('change', ()=>{
      const file = receiptInput.files && receiptInput.files[0] ? receiptInput.files[0] : null;
      if (!file){ resetReceiptUi(); return; }
      handleReceiptSelection(file);
    });
  }
  receiptClearBtn?.addEventListener('click', (e)=>{
    e.preventDefault();
    if (receiptInput){ receiptInput.value = ''; }
    resetReceiptUi();
  });
  if (receiptDropzone){
    ['dragover','dragleave','drop'].forEach(evt=>{
      receiptDropzone.addEventListener(evt, (event)=>{
        event.preventDefault();
        event.stopPropagation();
        if (evt === 'dragover'){
          receiptDropzone.classList.add('border-purple-400','bg-purple-50');
        } else if (evt === 'dragleave'){
          receiptDropzone.classList.remove('border-purple-400','bg-purple-50');
        } else if (evt === 'drop'){
          receiptDropzone.classList.remove('border-purple-400','bg-purple-50');
          const files = event.dataTransfer?.files;
          if (files && files.length > 0 && receiptInput){
            try {
              if (typeof DataTransfer !== 'undefined'){
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                receiptInput.files = dt.files;
                receiptInput.dispatchEvent(new Event('change', { bubbles: true }));
              } else {
                showReceiptError('Drag & drop is not supported in this browser. Click to select instead.');
              }
            } catch (err) {
              showReceiptError('Drag & drop is not supported in this browser. Click to select instead.');
            }
          }
        }
      });
    });
  }

  recordPurchaseBtn?.addEventListener('click', (event)=>{
    if (!requireReceipt(event)){
      event.stopPropagation();
    }
  });

  purchaseForm?.addEventListener('submit', (event)=>{
    if (!requireReceipt(event)){
      return;
    }
  });

  document.addEventListener('click', (event)=>{
    const btn = event.target.closest('.markPaidBtn');
    if (!btn){ return; }
    const purchaseId = parseInt(btn.dataset.purchaseId || '0', 10);
    if (!purchaseId || !markPaidForm || !markPaidPurchaseId || !markPaidReceiptInput){
      showReceiptPopup('Unable to start payment confirmation. Refresh the page and try again.');
      return;
    }
    const deliveryStatus = (btn.dataset.deliveryStatus || '').toLowerCase();
    if (deliveryStatus !== 'complete'){
      showReceiptPopup('Finish receiving this purchase before marking it as paid.');
      return;
    }
    pendingMarkPaidId = purchaseId;
    markPaidReceiptInput.value = '';
    markPaidReceiptInput.click();
  });

  markPaidReceiptInput?.addEventListener('change', ()=>{
    const file = markPaidReceiptInput.files && markPaidReceiptInput.files[0] ? markPaidReceiptInput.files[0] : null;
    if (!pendingMarkPaidId || !file){
      pendingMarkPaidId = null;
      markPaidReceiptInput.value = '';
      return;
    }
    if (!RECEIPT_ALLOWED.includes(file.type)){
      showReceiptPopup('Upload JPG, PNG, WebP, HEIC or PDF receipts only.');
      markPaidReceiptInput.value = '';
      pendingMarkPaidId = null;
      return;
    }
    if (file.size > RECEIPT_MAX_BYTES){
      showReceiptPopup('Receipt exceeds 10MB. Please compress or upload a PDF scan.');
      markPaidReceiptInput.value = '';
      pendingMarkPaidId = null;
      return;
    }
    markPaidPurchaseId.value = String(pendingMarkPaidId);
    pendingMarkPaidId = null;
    markPaidForm.submit();
  });

  function syncItemsJson(){
    const items = [];
    listBody.querySelectorAll('tr[data-id]').forEach(tr=>{
      const itemId = parseInt(tr.getAttribute('data-id')||'0',10) || 0;
      const qty = parseFloat(tr.querySelector('input[name="quantity[]"]').value || '0') || 0;
      const cost = parseFloat(tr.querySelector('input[name="row_cost[]"]').value || '0') || 0;
      const name = tr.querySelector('td:first-child')?.textContent?.trim() || '';
      const unit = tr.querySelector('td:nth-child(3)')?.textContent?.trim() || '';
      // Get original quantity (displayed quantity before conversion)
      const qtyDisp = tr.querySelector('.qtyDisp');
      const originalQty = qtyDisp ? parseFloat(qtyDisp.textContent || '0') : qty;
      if (qty>0) items.push({ item_id:itemId, quantity:qty, cost:cost, name:name, unit:unit, original_quantity:originalQty });
    });
    if (itemsJson) itemsJson.value = JSON.stringify(items);
  }

  function recalcTotal(){
    let sum = 0;
    listBody.querySelectorAll('input[name="row_cost[]"]').forEach(inp=>{ sum += parseFloat(inp.value || '0'); });
    totalCostSpan.textContent = sum.toFixed(2);
    if (totalCostReadonly) totalCostReadonly.value = sum.toFixed(2);
    const base = parseFloat(baseAmount?.value || '0');
    if (!isNaN(base) && changeReadonly){ const ch = base - sum; changeReadonly.value = ch.toFixed(2); }
    syncItemsJson();
  }
  if (baseAmount){ baseAmount.addEventListener('input', recalcTotal); }
  if (payType){
    const toggle = ()=>{ cashFields.classList.toggle('hidden', payType.value !== 'Cash'); recalcTotal(); };
    payType.addEventListener('change', toggle); toggle();
  }
  const syncPaymentStatus = ()=>{
    if (!paymentStatusHidden) return;
    const typeValue = purchaseTypeSel ? purchaseTypeSel.value : 'in_store';
    paymentStatusHidden.value = typeValue === 'delivery' ? 'Pending' : 'Paid';
  };
  if (purchaseTypeSel){
    purchaseTypeSel.addEventListener('change', syncPaymentStatus);
  }
  syncPaymentStatus();

  function addRow(itemId, name, baseUnit, baseQty, displayUnit, factor, rowCost, originalQty){
    // originalQty is the quantity as entered (before conversion)
    const displayQty = originalQty !== undefined ? originalQty : (baseQty / (factor || 1));
    
    // Merge by itemId and name (only if both match and itemId > 0)
    const existing = itemId > 0 ? listBody.querySelector(`tr[data-id="${itemId}"]`) : null;
    if (existing){
      const qHidden = existing.querySelector('input[name="quantity[]"]');
      const cHidden = existing.querySelector('input[name="row_cost[]"]');
      const origQtyHidden = existing.querySelector('input[name="original_quantity[]"]');
      const newBase = (parseFloat(qHidden.value||'0') + baseQty);
      qHidden.value = newBase;
      // Update original quantity sum
      const newOrigQty = (parseFloat(origQtyHidden?.value||'0') + displayQty);
      if (origQtyHidden) origQtyHidden.value = newOrigQty;
      // update display qty (show original quantity, not converted)
      existing.querySelector('.qtyDisp').textContent = newOrigQty.toFixed(2);
      // sum cost
      const newCost = (parseFloat(cHidden.value||'0') + rowCost);
      cHidden.value = newCost.toFixed(2);
      existing.querySelector('.costDisp').textContent = newCost.toFixed(2);
      recalcTotal();
      return;
    }
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', String(itemId));
    tr.setAttribute('data-name', name);
    tr.setAttribute('data-factor', String(factor||1));
    tr.innerHTML = `
      <td class="px-4 py-2">${name}<input type="hidden" name="item_id[]" value="${itemId}"></td>
      <td class="px-4 py-2 qtyDisp">${displayQty.toFixed(2)}<input type="hidden" name="quantity[]" value="${baseQty}"><input type="hidden" name="original_quantity[]" value="${displayQty}"></td>
      <td class="px-4 py-2">${displayUnit||baseUnit}<input type="hidden" name="unit[]" value="${displayUnit||baseUnit}"></td>
      <td class="px-4 py-2">₱ <span class="costDisp">${rowCost.toFixed(2)}</span><input type="hidden" name="row_cost[]" value="${rowCost.toFixed(2)}"></td>
      <td class="px-4 py-2"><button type="button" class="removeRow text-red-600">Remove</button></td>
    `;
    listBody.appendChild(tr);
    recalcTotal();
  }

  addBtn.addEventListener('click', ()=>{
    const ingredientName = ingInput.value.trim();
    if (!ingredientName) {
      alert('Please enter an ingredient name');
      return;
    }
    
    const quantity = parseFloat(qty.value || '0');
    const selectedUnit = unitSel.value.trim();
    const rowCost = parseFloat(cost.value || '0');
    
    if (!quantity || quantity <= 0) {
      alert('Please enter a valid quantity');
      return;
    }
    if (!selectedUnit) {
      alert('Please enter or select a unit');
      return;
    }
    if (isNaN(rowCost) || rowCost < 0) {
      alert('Please enter a valid cost');
      return;
    }
    
    // Try to find matching ingredient by name (case-insensitive)
    let itemId = parseInt(hiddenId.value || '0', 10);
    let ingr = null;
    let baseUnit = '';
    let dispUnit = '';
    let dispFactor = 1;
    
    // If no ID from hidden field, try to find by name
    if (!itemId) {
      const nameLower = ingredientName.toLowerCase();
      ingr = INGREDIENTS.find(x => x.name.toLowerCase() === nameLower);
      if (ingr) {
        itemId = ingr.id;
      }
    } else {
      ingr = INGREDIENTS.find(x=>x.id===itemId);
    }
    
    // If ingredient found, use its unit info; otherwise use the entered unit as base
    if (ingr) {
      baseUnit = ingr.unit || selectedUnit;
      dispUnit = ingr.display_unit || '';
      dispFactor = parseFloat(String(ingr.display_factor || '1')) || 1;
    } else {
      // New ingredient - use entered unit as base unit
      baseUnit = selectedUnit;
    }
    
    // Calculate conversion factor
    let factor = 1;
    if (ingr) {
      if (dispUnit && selectedUnit === dispUnit) {
        factor = dispFactor;
      } else if ((baseUnit === 'g' && selectedUnit === 'kg') || (baseUnit === 'ml' && selectedUnit === 'L')) {
        factor = 1000;
      } else if ((baseUnit === 'kg' && selectedUnit === 'g') || (baseUnit === 'L' && selectedUnit === 'ml')) {
        factor = 0.001;
      }
    }
    
    const baseQty = quantity * factor;
    // Store ingredient name and ID (0 if not found - backend will create it)
    // Pass original quantity (before conversion) for display
    addRow(itemId || 0, ingredientName, baseUnit, baseQty, selectedUnit, factor, rowCost, quantity);
    
    // clear inputs
    qty.value = '';
    cost.value = '';
    ingInput.value = '';
    unitSel.value = '';
    hiddenId.value = '';
  });

  listBody.addEventListener('click', (e)=>{
    if (e.target.classList.contains('removeRow')){
      e.target.closest('tr').remove();
      recalcTotal();
    }
  });

  // Modal logic
  document.querySelectorAll('.openPurchaseModal').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-group-id');
      const content = document.getElementById('modal-content-' + id);
      if (!content) return;
      // Create modal container
      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
      const modal = document.createElement('div');
      modal.className = 'bg-white rounded-xl shadow-xl max-w-2xl w-full p-6';
      // Build header with optional cash base/change
      const header = document.createElement('div');
      header.className = 'flex items-center justify-between mb-4';
      const title = document.createElement('h3');
      title.className = 'text-lg font-semibold';
      title.textContent = 'Purchase Details';
      const closeBtn = document.createElement('button');
      closeBtn.className = 'closeModal text-gray-500 hover:text-gray-700';
      closeBtn.textContent = '✕';
      header.appendChild(title);
      header.appendChild(closeBtn);
      modal.appendChild(header);

      const inner = document.createElement('div');
      inner.innerHTML = content.innerHTML;
      modal.appendChild(inner);
      overlay.appendChild(modal);
      document.body.appendChild(overlay);
      overlay.addEventListener('click', (e)=>{ if (e.target === overlay || e.target.classList.contains('closeModal')) overlay.remove(); });
    });
  });
})();
</script>
