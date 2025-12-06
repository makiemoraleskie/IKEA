<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$deliveredTotals = $deliveredTotals ?? [];
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Delivery Management</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Record and track ingredient deliveries</p>
		</div>
	</div>
</div>

<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Delivery Status</label>
				<div class="border border-dashed border-green-200 bg-green-50 text-sm text-green-800 rounded-lg px-4 py-3">
					Status is now auto-calculated when you click <strong class="font-semibold">Record Delivery</strong>. If <em>Receive Now</em> matches the <em>Remaining</em> quantity, the delivery will be marked as <span class="font-semibold">Complete Delivery</span>; otherwise it will be recorded as <span class="font-semibold">Partial Delivery</span>.
				</div>
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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
	<!-- Total Deliveries -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="truck" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-blue-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL DELIVERIES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $totalDeliveries; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">All delivery records</p>
		</div>
	</div>
	
	<!-- Complete Deliveries -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="check-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">COMPLETE DELIVERIES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo $completeDeliveries; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Fully delivered</p>
		</div>
	</div>
	
	<!-- Partial Deliveries -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="clock" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-yellow-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PARTIAL DELIVERIES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-yellow-600 mb-1 md:mb-1.5"><?php echo $partialDeliveries; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Incomplete shipments</p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Record Delivery Form -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden max-w-full w-full">
	<div class="bg-gray-100 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
			<i data-lucide="package-check" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
			Record New Delivery
		</h2>
		<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Record a delivery for an existing purchase</p>
	</div>
	
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="p-4 sm:p-6 w-full overflow-x-hidden" id="deliveriesForm">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        <input type="hidden" name="items_json" id="deliveriesItemsJson" value="[]">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Purchase Batch Selection -->
            <div class="space-y-3 md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Purchase Batch</label>
                
                <!-- Improved Batch Selector -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <i data-lucide="package-search" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <select id="batchSelect" class="w-full border-2 border-gray-300 rounded-xl pl-12 pr-10 py-3.5 text-sm md:text-base focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors appearance-none bg-white cursor-pointer hover:border-orange-300 shadow-sm">
                        <option value="">🔍 Search and select a purchase batch...</option>
                        <?php foreach (($purchaseGroups ?? []) as $g): 
                            $itemCount = count($g['items'] ?? []);
                            $remainingQty = ($g['quantity_sum'] ?? 0) - ($g['delivered_sum'] ?? 0);
                            $hasRemaining = $remainingQty > 0.0001;
                            $paymentStatus = strtolower($g['payment_status'] ?? 'pending');
                            $dateFormatted = !empty($g['date_purchased']) ? date('M d, Y', strtotime($g['date_purchased'])) : '—';
                            
                            // Create a more readable option text
                            $optionText = sprintf(
                                "#%s • %s • %s • %s • %d %s",
                                htmlspecialchars($g['group_id']),
                                htmlspecialchars($g['supplier'] ?? '—'),
                                htmlspecialchars($g['purchaser_name'] ?? '—'),
                                $dateFormatted,
                                $itemCount,
                                $itemCount === 1 ? 'item' : 'items'
                            );
                        ?>
                            <option value="<?php echo htmlspecialchars($g['group_id']); ?>" 
                                data-supplier="<?php echo htmlspecialchars($g['supplier'] ?? ''); ?>"
                                data-purchaser="<?php echo htmlspecialchars($g['purchaser_name'] ?? ''); ?>"
                                data-date="<?php echo htmlspecialchars($g['date_purchased'] ?? ''); ?>"
                                data-items="<?php echo $itemCount; ?>"
                                data-remaining="<?php echo $hasRemaining ? 'yes' : 'no'; ?>"
                                data-payment="<?php echo htmlspecialchars($paymentStatus); ?>">
                                <?php echo $optionText; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none z-10">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Selected Batch Details Card -->
                <div id="selectedBatchCard" class="hidden mt-4 rounded-xl border-2 border-orange-200 bg-gradient-to-r from-orange-50 to-amber-50 p-4 md:p-5 shadow-sm transition-all duration-300">
                    <div class="flex items-start gap-3 md:gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                            <i data-lucide="package" class="w-6 h-6 text-orange-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-orange-900 uppercase tracking-wide">Selected Batch</span>
                                <span id="selectedBatchId" class="text-sm font-bold text-orange-700"></span>
                                <span id="selectedBatchStatus" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="truck" class="w-4 h-4 text-orange-600 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-gray-600">Supplier:</span>
                                        <span id="selectedSupplier" class="ml-1 font-semibold text-gray-900"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-orange-600 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-gray-600">Purchaser:</span>
                                        <span id="selectedPurchaser" class="ml-1 font-semibold text-gray-900"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-orange-600 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-gray-600">Date:</span>
                                        <span id="selectedDate" class="ml-1 font-semibold text-gray-900"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="package-2" class="w-4 h-4 text-orange-600 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-gray-600">Items:</span>
                                        <span id="selectedItems" class="ml-1 font-semibold text-gray-900"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-2" id="batchMeta">Select a batch above to record delivery. Click the batch to open the delivery modal.</p>
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
		</div>
		
        <div id="batchItemsBox" class="mt-4 hidden">
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full text-sm min-w-[560px]" id="batchItemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-2">Item</th>
                            <th class="text-left px-4 py-2">Remaining</th>
                            <th class="text-left px-4 py-2">Receive Now</th>
                            <th class="text-left px-4 py-2">Unit</th>
                            <th class="text-left px-4 py-2">Auto Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
			<button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="package-check" class="w-4 h-4"></i>
				Record Delivery
			</button>
		</div>
	</form>
</div>

<!-- Delivery Modal -->
<div id="deliveryModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
		<div class="flex items-center justify-between px-6 py-4 border-b">
			<div>
				<p class="text-xs uppercase tracking-wide text-gray-500">Purchase Batch</p>
				<p class="text-lg font-semibold text-gray-900" id="deliveryModalBatchLabel">#0</p>
			</div>
			<button type="button" class="deliveryModalClose text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Close">&times;</button>
		</div>
		<div class="px-6 py-4 space-y-4">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
					<p class="text-xs uppercase tracking-wide text-blue-700">Supplier</p>
					<p class="font-semibold text-blue-900 mt-1" id="deliveryModalSupplier">—</p>
				</div>
				<div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
					<p class="text-xs uppercase tracking-wide text-emerald-700">Purchaser</p>
					<p class="font-semibold text-emerald-900 mt-1" id="deliveryModalPurchaser">—</p>
				</div>
				<div class="rounded-xl bg-purple-50 border border-purple-100 p-4">
					<p class="text-xs uppercase tracking-wide text-purple-700">Date Ordered</p>
					<p class="font-semibold text-purple-900 mt-1" id="deliveryModalDate">—</p>
				</div>
			</div>
			
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" id="deliveryModalForm" class="space-y-4">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="items_json" id="deliveryModalItemsJson" value="[]">
				<input type="hidden" name="action" value="store">
				
				<!-- Purchase Items List (Read-only Note) -->
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<div class="bg-gray-50 px-4 py-3 border-b">
						<h3 class="text-sm font-semibold text-gray-900">Purchase Items</h3>
						<p class="text-xs text-gray-600 mt-1">Items from this purchase batch</p>
					</div>
					<div class="p-4">
						<div class="overflow-x-auto">
							<table class="w-full text-sm">
								<thead class="bg-gray-50">
									<tr>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Name</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Quantity</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Unit</th>
									</tr>
								</thead>
								<tbody id="deliveryPurchaseItemsList" class="divide-y divide-gray-200">
									<!-- Purchase items will be displayed here -->
								</tbody>
							</table>
						</div>
						<div id="deliveryPurchaseItemsEmpty" class="text-sm text-gray-500 text-center py-4">No items in this purchase batch.</div>
					</div>
				</div>
				
					<div class="rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1 relative">
							<label class="text-sm font-medium text-gray-700">Select Item</label>
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
									<i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
								</div>
								<input type="text" id="deliveryItemSearch" class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Search ingredient..." autocomplete="off">
								<select id="deliveryItemSelect" class="hidden">
									<option value="">Choose ingredient</option>
									<!-- Options will be populated dynamically -->
								</select>
								<div id="deliveryItemDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
									<!-- Filtered options will appear here -->
								</div>
							</div>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Quantity</label>
							<input type="number" step="0.01" min="0" id="deliveryQuantityInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0.00" readonly>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<input type="text" id="deliveryUnitInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Unit" readonly>
							<select id="deliveryUnitSelect" class="hidden w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
								<option value="">Select unit</option>
							</select>
							<p id="deliveryUnitHelp" class="hidden text-xs text-gray-500 mt-1"></p>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Supplier</label>
							<input type="text" id="deliverySupplierInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Supplier" readonly>
						</div>
						<div class="flex items-end">
							<button type="button" id="deliveryAddItemBtn" class="w-full inline-flex items-center justify-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
								<i data-lucide="plus" class="w-4 h-4"></i>
								Add to Delivery
							</button>
						</div>
					</div>
					<div id="deliveryBuilderError" class="hidden px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700"></div>
				</div>
				
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<table class="w-full text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th class="text-left px-4 py-2">Item</th>
								<th class="text-left px-4 py-2">Quantity</th>
								<th class="text-left px-4 py-2">Unit</th>
								<th class="text-left px-4 py-2">Supplier</th>
								<th class="text-left px-4 py-2 w-20">Actions</th>
							</tr>
						</thead>
						<tbody id="deliveryItemsBody" class="divide-y divide-gray-200"></tbody>
					</table>
					<div id="deliveryEmptyState" class="px-4 py-6 text-center text-sm text-gray-500">No items added yet. Select ingredients from the inventory above.</div>
				</div>
				
				<div class="flex flex-col sm:flex-row sm:justify-end gap-3">
					<button type="button" class="deliveryModalClose inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
						Cancel
					</button>
					<button type="submit" id="deliverySubmitBtn" class="inline-flex items-center justify-center gap-2 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
						<i data-lucide="package-check" class="w-4 h-4"></i>
						Record Delivery
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if (!empty($awaitingPurchases)): ?>
<div id="awaiting-deliveries" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6 md:mb-8 max-w-full w-full">
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-4 sm:px-6 py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="truck" class="w-5 h-5 text-orange-600"></i>
                Awaiting Deliveries
            </h2>
            <p class="text-sm text-gray-600 mt-1">Open purchase batches that still need to be delivered</p>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 border border-orange-200">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <?php echo count($awaitingPurchases); ?> outstanding
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
            <thead class="sticky top-0 bg-white z-10">
                <tr>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Purchase</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Supplier</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Item</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Ordered</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Delivered</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Remaining</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-gray-700 font-medium bg-white text-[10px] md:text-xs lg:text-sm">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                    <?php foreach ($awaitingPurchases as $pending): 
                    // Calculate remaining in base units (for internal calculations)
                    $remainingBase = max(0, (float)$pending['quantity'] - (float)$pending['delivered_quantity']);
                    
                    // Use purchase_unit and purchase_quantity for display if available
                    $purchaseUnit = trim((string)($pending['purchase_unit'] ?? ''));
                    $purchaseQty = (float)($pending['purchase_quantity'] ?? 0);
                    $purchasedQty = (float)$pending['quantity'];
                    
                    // Calculate remaining in purchase unit
                    if ($purchaseUnit !== '' && $purchaseQty > 0 && $purchasedQty > 0) {
                        // Calculate conversion factor: purchase_quantity (in purchase_unit) = quantity (in base unit)
                        $conversionFactor = $purchasedQty / $purchaseQty;
                        $remainingDisplay = $remainingBase / $conversionFactor;
                        $displayUnit = $purchaseUnit;
                        $orderedDisplay = $purchaseQty;
                        $deliveredDisplay = ($pending['delivered_quantity'] ?? 0) / $conversionFactor;
                    } else {
                        // Fallback to base unit
                        $remainingDisplay = $remainingBase;
                        $displayUnit = $pending['unit'];
                        $orderedDisplay = $purchasedQty;
                        $deliveredDisplay = (float)($pending['delivered_quantity'] ?? 0);
                    }
                    
                    $batchTs = substr((string)($pending['date_purchased'] ?? ''),0,19);
                    $batchId = substr(sha1(($pending['purchaser_id']??'').'|'.($pending['supplier']??'').'|'.($pending['payment_status']??'').'|'.($pending['receipt_url']??'').'|'.$batchTs),0,10);
                ?>
                <tr class="hover:bg-orange-50 transition-colors">
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-purple-700">#<?php echo htmlspecialchars($batchId); ?></span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($pending['purchaser_name']); ?></div>
                                <div class="text-xs text-gray-500">Placed: <?php echo htmlspecialchars($pending['date_purchased']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-medium">
                            <i data-lucide="factory" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($pending['supplier']); ?>
                        </span>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($pending['item_name']); ?></div>
                        <div class="text-xs text-gray-500">Unit: <?php echo htmlspecialchars($displayUnit); ?></div>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm"><?php echo number_format($orderedDisplay, 2); ?> <?php echo htmlspecialchars($displayUnit); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo number_format($deliveredDisplay, 2); ?> <?php echo htmlspecialchars($displayUnit); ?></td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                            <?php echo number_format($remainingDisplay, 2); ?> <?php echo htmlspecialchars($displayUnit); ?>
                        </span>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors"
                            data-quick-receive
                            data-purchase-id="<?php echo (int)$pending['id']; ?>"
                            data-batch-label="<?php echo htmlspecialchars('#' . $batchId); ?>"
                            data-supplier="<?php echo htmlspecialchars($pending['supplier']); ?>"
                            data-purchaser="<?php echo htmlspecialchars($pending['purchaser_name']); ?>"
                            data-date="<?php echo htmlspecialchars($pending['date_purchased']); ?>"
                            data-item="<?php echo htmlspecialchars($pending['item_name']); ?>"
                            data-unit="<?php echo htmlspecialchars($pending['unit']); ?>"
                            data-purchase-unit="<?php echo htmlspecialchars($displayUnit); ?>"
                            data-purchase-quantity="<?php echo htmlspecialchars((string)$purchaseQty); ?>"
                            data-display-unit="<?php echo htmlspecialchars($pending['display_unit'] ?? ''); ?>"
                            data-display-factor="<?php echo htmlspecialchars((string)($pending['display_factor'] ?? '')); ?>"
                            data-ordered="<?php echo htmlspecialchars((string)$pending['quantity']); ?>"
                            data-ordered-display="<?php echo htmlspecialchars((string)$orderedDisplay); ?>"
                            data-delivered="<?php echo htmlspecialchars((string)$pending['delivered_quantity']); ?>"
                            data-delivered-display="<?php echo htmlspecialchars((string)$deliveredDisplay); ?>"
                            data-remaining-base="<?php echo htmlspecialchars((string)$remainingBase); ?>"
                            data-remaining-display="<?php echo htmlspecialchars((string)$remainingDisplay); ?>"
                        >
                            <i data-lucide="clipboard-check" class="w-3 h-3"></i>
                            Receive now
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Quick Receive Modal -->
<div id="receiveQuickModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-8">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" data-quick-cancel></div>
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
        <div class="flex items-start justify-between gap-4 px-6 py-4 border-b bg-gray-50">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Receive delivery</p>
                <h3 class="text-xl font-semibold text-gray-900" id="quickBatchLabel">Batch</h3>
                <p class="text-sm text-gray-600" id="quickMetaText"></p>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600" data-quick-cancel aria-label="Close quick receive panel">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-gray-200 p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Supplier</p>
                    <p class="text-base font-semibold text-gray-900 mt-1" id="quickSupplier">—</p>
                </div>
                <div class="rounded-xl border border-gray-200 p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Requested by</p>
                    <p class="text-base font-semibold text-gray-900 mt-1" id="quickPurchaser">—</p>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase mb-3">Item summary</p>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-base font-semibold text-gray-900" id="quickItemName">—</p>
                        <p class="text-sm text-gray-500">Ordered on <span id="quickDate">—</span></p>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm font-semibold text-gray-900">
                        <span>Ordered: <span id="quickOrdered">0.00</span></span>
                        <span>Delivered: <span id="quickDelivered">0.00</span></span>
                        <span>Remaining: <span id="quickRemaining">0.00</span></span>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Delivery type</p>
                <div class="flex flex-wrap gap-3">
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border border-orange-200 bg-orange-600 text-white focus:ring-2 focus:ring-orange-500" data-quick-status="complete">
                        Complete delivery
                    </button>
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-700 hover:border-gray-300 focus:ring-2 focus:ring-orange-500" data-quick-status="partial">
                        Partial delivery
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2" id="quickStatusNote">Entire remaining quantity will be received now.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity received now</label>
                <div class="flex items-center gap-3">
                    <input id="quickQtyInput" type="number" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100 text-gray-600 cursor-not-allowed" readonly>
                    <span class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 text-sm font-semibold text-gray-700" id="quickQtyUnit">unit</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">You cannot exceed the remaining quantity for this purchase.</p>
                <p class="mt-2 text-sm text-red-600 hidden" id="quickModalError"></p>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between gap-3">
            <button type="button" class="text-sm font-semibold text-gray-600 hover:text-gray-800" data-quick-cancel>Cancel</button>
            <button type="button" id="quickConfirmBtn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 text-white text-sm font-semibold hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <i data-lucide="package-check" class="w-4 h-4"></i>
                Confirm & Record
            </button>
        </div>
    </div>
</div>

<!-- Recent Deliveries Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden max-w-full w-full">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
					Recent Deliveries
				</h2>
				<p class="text-sm text-gray-600 mt-1">View and track all delivery records</p>
			</div>
			<div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-4">
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
				<div class="flex items-center gap-2 text-sm text-gray-600">
					<label for="deliveryStatusFilter" class="whitespace-nowrap">Filter status:</label>
					<select id="deliveryStatusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
						<option value="all">All deliveries</option>
						<option value="complete">Complete only</option>
						<option value="partial">Partial only</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
                <tr>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Delivery ID</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Batch</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Item</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Quantity Received</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Status</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Date Received</th>
                </tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($deliveries as $d): ?>
				<tr class="hover:bg-gray-50 transition-colors" data-delivery-status="<?php echo strtolower($d['delivery_status']); ?>">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-orange-600">#<?php echo (int)$d['id']; ?></span>
							</div>
						</div>
					</td>
					
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
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
						<?php
						// Display quantity_received in the ingredient's base unit
						// quantity_received is stored in base units, so display it as-is
						$qtyReceived = (float)$d['quantity_received'];
						$baseUnit = $d['unit']; // This is the ingredient's base unit
						
						// Show in base unit (the unit that was actually used when recording delivery)
						// If user entered 50000 g, it's stored as 50000 g in base units, so show as 50000.00 g
						$qtyDisplay = $qtyReceived;
						$unitDisplay = $baseUnit;
						?>
						<div class="flex items-center gap-2">
							<span class="font-semibold text-gray-900"><?php echo number_format($qtyDisplay, 2); ?></span>
							<span class="text-gray-500 text-sm"><?php echo htmlspecialchars($unitDisplay); ?></span>
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
        <?php if (!empty($deliveries)): ?>
        <div id="deliveryFilterEmpty" class="hidden px-6 py-4 text-center text-sm text-gray-500 border-t">
            No deliveries match the selected filter.
        </div>
        <?php endif; ?>
		
		<?php if (empty($deliveries)): ?>
		<div class="flex flex-col items-center justify-center py-12 text-gray-500">
			<i data-lucide="truck" class="w-16 h-16 mb-4 text-gray-300"></i>
			<h3 class="text-lg font-medium text-gray-900 mb-2">No Deliveries Found</h3>
			<p class="text-sm text-gray-600 mb-4">Start by recording your first delivery</p>
			<button onclick="document.querySelector('form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
				<i data-lucide="plus" class="w-4 h-4"></i>
				Record First Delivery
			</button>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>

(function(){
  // Ingredients lookup from inventory
  const INGREDIENTS = <?php echo json_encode(array_map(function($ing) {
    return [
      'id' => (int)$ing['id'],
      'name' => $ing['name'],
      'unit' => $ing['unit'] ?? '',
      'display_unit' => $ing['display_unit'] ?? '',
      'display_factor' => (float)($ing['display_factor'] ?? 1),
    ];
  }, ($ingredients ?? [])), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  
  const INGREDIENT_LOOKUP = {};
  INGREDIENTS.forEach(ing => {
    INGREDIENT_LOOKUP[ing.id] = ing;
  });
  
  const GROUPS = <?php echo json_encode(array_map(function($g) use ($deliveredTotals) {
    $deliveredLookup = [];
    foreach (($g['items'] ?? []) as $p) {
      $deliveredLookup[(int)$p['id']] = (float)($deliveredTotals[(int)$p['id']] ?? 0);
    }
    return [
      'group_id' => $g['group_id'],
      'supplier' => $g['supplier'] ?? '',
      'purchaser_name' => $g['purchaser_name'] ?? '',
      'date_purchased' => $g['date_purchased'] ?? '',
      'items' => array_values(array_filter(array_map(function($p) use ($deliveredLookup){
        $del = (float)($deliveredLookup[(int)$p['id']] ?? 0);
        $remaining = (float)$p['quantity'] - $del;
        // Use a small epsilon to account for floating point precision
        if ($remaining <= 0.0001) { return null; }
        // Use purchase_unit and purchase_quantity for display if available
        $purchaseUnit = trim((string)($p['purchase_unit'] ?? ''));
        $purchaseQty = (float)($p['purchase_quantity'] ?? 0);
        $purchasedQty = (float)$p['quantity'];
        
        // Calculate display values
        if ($purchaseUnit !== '' && $purchaseQty > 0 && $purchasedQty > 0) {
          $conversionFactor = $purchasedQty / $purchaseQty;
          if ($conversionFactor > 0 && is_finite($conversionFactor)) {
            $remainingDisplay = $remaining / $conversionFactor;
            $deliveredDisplay = $del / $conversionFactor;
            $displayUnit = $purchaseUnit;
          } else {
            // Fallback if conversion factor is invalid
            $remainingDisplay = $remaining;
            $deliveredDisplay = $del;
            $displayUnit = $p['unit'];
          }
        } else {
          $remainingDisplay = $remaining;
          $deliveredDisplay = $del;
          $displayUnit = $p['unit'];
        }
        
        return [
          'purchase_id' => (int)$p['id'],
          'item_id' => (int)($p['item_id'] ?? 0),
          'item_name' => $p['item_name'],
          'unit' => $p['unit'],
          'purchase_unit' => $displayUnit,
          'purchase_quantity' => $purchaseQty,
          'display_unit' => $p['display_unit'],
          'display_factor' => (float)$p['display_factor'],
          'quantity' => (float)$p['quantity'],
          'quantity_display' => $purchaseQty > 0 ? $purchaseQty : (float)$p['quantity'],
          'delivered' => $del,
          'delivered_display' => $deliveredDisplay,
          'remaining_display' => $remainingDisplay,
        ];
      }, $g['items'] ?? [])))
    ];
  }, ($purchaseGroups ?? [])), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  const sel = document.getElementById('batchSelect');
  const box = document.getElementById('batchItemsBox');
  const tableBody = document.querySelector('#batchItemsTable tbody');
  const itemsJson = document.getElementById('deliveriesItemsJson');
  const filterSelect = document.getElementById('deliveryStatusFilter');
  const deliveryRows = Array.from(document.querySelectorAll('[data-delivery-status]'));
  const filterEmpty = document.getElementById('deliveryFilterEmpty');
  const urlParams = new URLSearchParams(window.location.search);
  const batchMeta = document.getElementById('batchMeta');
  const batchMetaSupplier = document.getElementById('batchMetaSupplier');
  const batchMetaPurchaser = document.getElementById('batchMetaPurchaser');
  const batchMetaDate = document.getElementById('batchMetaDate');
  const batchHighlight = document.getElementById('batchHighlight');
  const deliveriesForm = document.getElementById('deliveriesForm');
  const quickModal = document.getElementById('receiveQuickModal');
  const quickQtyInput = document.getElementById('quickQtyInput');
  const quickQtyUnit = document.getElementById('quickQtyUnit');
  const quickSupplier = document.getElementById('quickSupplier');
  const quickPurchaser = document.getElementById('quickPurchaser');
  const quickItemName = document.getElementById('quickItemName');
  const quickOrdered = document.getElementById('quickOrdered');
  const quickDelivered = document.getElementById('quickDelivered');
  const quickRemaining = document.getElementById('quickRemaining');
  const quickDate = document.getElementById('quickDate');
  const quickBatchLabel = document.getElementById('quickBatchLabel');
  const quickMetaText = document.getElementById('quickMetaText');
  const quickStatusNote = document.getElementById('quickStatusNote');
  const quickError = document.getElementById('quickModalError');
  const quickConfirmBtn = document.getElementById('quickConfirmBtn');
  const quickStatusButtons = Array.from(document.querySelectorAll('[data-quick-status]'));
  const quickCloseTargets = Array.from(document.querySelectorAll('[data-quick-cancel]'));
  const quickReceiveButtons = Array.from(document.querySelectorAll('[data-quick-receive]'));
  let quickSelection = null;
  let quickStatus = 'complete';

  function toggleBodyScroll(disable){
    document.body.classList.toggle('overflow-hidden', !!disable);
  }

  function clampQuickQuantity(){
    if (!quickSelection || !quickQtyInput) return;
    const remaining = quickSelection.remaining;
    let value = parseFloat(quickQtyInput.value || '0');
    if (!isFinite(value) || value < 0){ value = 0; }
    if (remaining >= 0 && value > remaining){ value = remaining; }
    quickQtyInput.value = value > 0 ? value.toFixed(2) : '';
  }

  function setQuickStatus(status){
    if (!quickQtyInput || !quickStatusNote) return;
    quickStatus = status === 'partial' ? 'partial' : 'complete';
    quickStatusButtons.forEach(btn => {
      const active = btn.dataset.quickStatus === quickStatus;
      btn.classList.toggle('bg-orange-600', active);
      btn.classList.toggle('text-white', active);
      btn.classList.toggle('border-orange-200', active);
      btn.classList.toggle('border-gray-200', !active);
      btn.classList.toggle('text-gray-700', !active);
    });
    if (!quickSelection) { return; }
    if (quickStatus === 'complete'){
      quickQtyInput.value = quickSelection.remaining.toFixed(2);
      quickQtyInput.setAttribute('readonly','readonly');
      quickQtyInput.classList.add('bg-gray-100','text-gray-600','cursor-not-allowed');
      quickStatusNote.textContent = 'Entire remaining quantity will be received now.';
    } else {
      quickQtyInput.removeAttribute('readonly');
      quickQtyInput.classList.remove('bg-gray-100','text-gray-600','cursor-not-allowed');
      quickStatusNote.textContent = 'Enter the exact amount you received to record a partial delivery.';
      quickQtyInput.focus();
    }
    quickError?.classList.add('hidden');
  }

  function openQuickModal(data){
    if (!quickModal) return;
    quickSelection = data;
    const unitLabel = data.unit || 'unit';
    quickSupplier.textContent = data.supplier || '—';
    quickPurchaser.textContent = data.purchaser || '—';
    quickItemName.textContent = data.item || '—';
    quickOrdered.textContent = `${data.ordered.toFixed(2)} ${unitLabel}`;
    quickDelivered.textContent = `${data.delivered.toFixed(2)} ${unitLabel}`;
    quickRemaining.textContent = `${data.remaining.toFixed(2)} ${unitLabel}`;
    quickQtyUnit.textContent = unitLabel;
    quickDate.textContent = data.date || '—';
    quickBatchLabel.textContent = data.batchLabel || 'Batch';
    quickMetaText.textContent = `Remaining ${data.remaining.toFixed(2)} ${unitLabel}`;
    quickModal.classList.remove('hidden');
    quickModal.classList.add('flex');
    toggleBodyScroll(true);
    setQuickStatus('complete');
  }

  function closeQuickModal(){
    if (!quickModal) return;
    quickModal.classList.add('hidden');
    quickModal.classList.remove('flex');
    toggleBodyScroll(false);
    quickSelection = null;
    quickError?.classList.add('hidden');
  }


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

  function setBadgeState(badge, status){
    if (!badge) return;
    const baseClasses = ['bg-green-100','text-green-800','border-green-200','bg-yellow-100','text-yellow-800','border-yellow-200'];
    badge.classList.remove(...baseClasses);
    if (status === 'complete'){
      badge.textContent = 'Complete Delivery';
      badge.classList.add('bg-green-100','text-green-800','border-green-200');
    } else {
      badge.textContent = 'Partial Delivery';
      badge.classList.add('bg-yellow-100','text-yellow-800','border-yellow-200');
    }
    badge.dataset.status = status;
  }

  function clampInputToRemaining(input){
    // Function kept for backward compatibility but no longer actively used
    // to allow free typing in the Receive Now field
    if (!input) return;
    const remaining = parseFloat(input.dataset.remaining || '0') || 0;
    let value = parseFloat(input.value || '0');
    if (!isFinite(value) || value < 0) value = 0;
    // Removed the clamp to remaining - allow values greater than remaining
    input.value = value.toFixed(2);
  }

  function updateStatusPreview(input){
    if (!input) return;
    const remaining = parseFloat(input.dataset.remaining || '0') || 0;
    const value = parseFloat(input.value || '0') || 0;
    const status = (remaining <= 0 || value >= remaining - 0.0001) ? 'complete' : 'partial';
    const badge = input.closest('tr')?.querySelector('.statusPreview');
    setBadgeState(badge, status);
  }

  function updateSelectedBatchCard(groupId) {
    if (!groupId || groupId === '') {
      if (selectedBatchCard) selectedBatchCard.classList.add('hidden');
      if (batchMeta) batchMeta.textContent = 'Select a batch above to record delivery. Click the batch to open the delivery modal.';
      return;
    }
    
    const selectedOption = sel?.querySelector(`option[value="${groupId}"]`);
    if (!selectedOption || !selectedBatchCard) return;
    
    const supplier = selectedOption.dataset.supplier || '—';
    const purchaser = selectedOption.dataset.purchaser || '—';
    const date = selectedOption.dataset.date || '—';
    const items = selectedOption.dataset.items || '0';
    const hasRemaining = selectedOption.dataset.remaining === 'yes';
    const paymentStatus = selectedOption.dataset.payment || 'pending';
    
    // Format date
    let formattedDate = '—';
    if (date && date !== '—') {
      try {
        formattedDate = new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      } catch (e) {
        formattedDate = date;
      }
    }
    
    // Update card content
    if (selectedBatchId) selectedBatchId.textContent = '#' + groupId;
    if (selectedSupplier) selectedSupplier.textContent = supplier;
    if (selectedPurchaser) selectedPurchaser.textContent = purchaser;
    if (selectedDate) selectedDate.textContent = formattedDate;
    if (selectedItems) selectedItems.textContent = `${items} ${items === '1' ? 'item' : 'items'}`;
    
    // Update status badge
    if (selectedBatchStatus) {
      selectedBatchStatus.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold';
      if (hasRemaining) {
        selectedBatchStatus.textContent = 'Pending Delivery';
        selectedBatchStatus.classList.add('bg-orange-100', 'text-orange-700', 'border', 'border-orange-200');
      } else {
        selectedBatchStatus.textContent = 'Fully Delivered';
        selectedBatchStatus.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-200');
      }
    }
    
    selectedBatchCard.classList.remove('hidden');
    if (batchMeta) batchMeta.textContent = 'Click the batch selector again or use the button below to open the delivery modal.';
  }

  function render(groupId){
    if (!tableBody) {
      return;
    }
    tableBody.innerHTML='';
    if (!groupId || groupId === ''){
      if (box) box.classList.add('hidden');
      if (itemsJson) itemsJson.value='[]';
      return;
    }
    const g = GROUPS.find(x=>x.group_id===groupId);
    if (!g){ 
      if (box) box.classList.add('hidden'); 
      if (itemsJson) itemsJson.value='[]'; 
      return; 
    }
    if (!g.items || !Array.isArray(g.items) || g.items.length === 0){ 
      if (box) box.classList.add('hidden'); 
      if (itemsJson) itemsJson.value='[]'; 
      return; 
    }
    if (box) {
      box.classList.remove('hidden');
    }
    for (const it of g.items){
      // Use purchase_unit and purchase_quantity for display if available
      const remainingBase = Math.max(0, (it.quantity - it.delivered));
      const remainingDisplay = it.remaining_display !== undefined ? it.remaining_display : remainingBase;
      const displayUnit = it.purchase_unit || it.unit;
      const tr = document.createElement('tr');

      const itemTd = document.createElement('td');
      itemTd.className = 'px-4 py-2';
      itemTd.innerHTML = `${it.item_name}<input type="hidden" name="purchase_id[]" value="${it.purchase_id}">`;
      tr.appendChild(itemTd);

      const remainingTd = document.createElement('td');
      remainingTd.className = 'px-4 py-2 text-gray-600';
      remainingTd.textContent = `${remainingDisplay.toFixed(2)} ${displayUnit}`;
      tr.appendChild(remainingTd);

      const qtyTd = document.createElement('td');
      qtyTd.className = 'px-4 py-2';
      const qtyInput = document.createElement('input');
      qtyInput.type = 'number';
      qtyInput.step = '0.01';
      qtyInput.min = '0';
      // Remove max constraint to allow free typing
      qtyInput.name = 'row_qty[]';
      qtyInput.value = remainingDisplay.toFixed(2);
      qtyInput.dataset.remaining = remainingBase.toFixed(6); // Store base for calculations
      qtyInput.dataset.remainingDisplay = remainingDisplay.toFixed(6); // Store display for UI
      qtyInput.dataset.displayUnit = displayUnit;
      qtyInput.className = 'w-32 border rounded px-3 py-2';
      if (remainingBase <= 0.0001){
        qtyInput.readOnly = true;
        qtyInput.classList.add('bg-gray-100','text-gray-500','cursor-not-allowed');
      }
      qtyInput.addEventListener('input', ()=>{
        // Allow free typing - only update status preview and sync
        updateStatusPreview(qtyInput);
        sync();
      });
      qtyInput.addEventListener('blur', ()=>{
        // On blur, optionally clamp to remaining if desired, but allow free typing
        updateStatusPreview(qtyInput);
        sync();
      });
      qtyTd.appendChild(qtyInput);
      tr.appendChild(qtyTd);

      const unitTd = document.createElement('td');
      unitTd.className = 'px-4 py-2';
      // Use purchase_unit as default, fallback to ingredient unit
      const defaultUnit = displayUnit || it.unit;
      const unitSel = buildUnitOptions(it.unit, it.display_unit);
      // Set the selected unit to purchase_unit if available
      if (displayUnit && unitSel.querySelector(`option[value="${displayUnit}"]`)) {
        unitSel.value = displayUnit;
      } else if (defaultUnit && unitSel.querySelector(`option[value="${defaultUnit}"]`)) {
        unitSel.value = defaultUnit;
      }
      unitSel.addEventListener('change', sync);
      unitTd.appendChild(unitSel);
      tr.appendChild(unitTd);

      const statusTd = document.createElement('td');
      statusTd.className = 'px-4 py-2';
      const badge = document.createElement('span');
      badge.className = 'statusPreview inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border';
      statusTd.appendChild(badge);
      tr.appendChild(statusTd);

      tableBody.appendChild(tr);
      updateStatusPreview(qtyInput);
    }
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

  quickReceiveButtons.forEach(button => {
    button.addEventListener('click', ()=>{
      const purchaseId = parseInt(button.dataset.purchaseId || '0', 10) || 0;
      const ordered = parseFloat(button.dataset.ordered || '0') || 0;
      const delivered = parseFloat(button.dataset.delivered || '0') || 0;
      const remaining = Math.max(0, ordered - delivered);
      if (!purchaseId || remaining <= 0){ return; }
      openQuickModal({
        purchaseId,
        supplier: button.dataset.supplier || '',
        purchaser: button.dataset.purchaser || '',
        item: button.dataset.item || '',
        unit: button.dataset.unit || 'unit',
        ordered,
        delivered,
        remaining,
        date: button.dataset.date || '',
        batchLabel: button.dataset.batchLabel || ''
      });
    });
  });

  quickStatusButtons.forEach(btn => {
    btn.addEventListener('click', ()=>{
      setQuickStatus(btn.dataset.quickStatus);
    });
  });

  quickCloseTargets.forEach(el => {
    el.addEventListener('click', closeQuickModal);
  });

  quickQtyInput?.addEventListener('input', ()=>{
    clampQuickQuantity();
    if (quickStatus !== 'partial'){
      setQuickStatus('partial');
    }
  });

  document.addEventListener('keydown', (event)=>{
    if (event.key === 'Escape' && quickSelection){
      closeQuickModal();
    }
  });

  quickConfirmBtn?.addEventListener('click', ()=>{
    if (!quickSelection || !itemsJson || !deliveriesForm){ return; }
    let qty = parseFloat(quickQtyInput.value || '0');
    if (!isFinite(qty) || qty <= 0){
      if (quickError){
        quickError.textContent = 'Enter the quantity received for this delivery.';
        quickError.classList.remove('hidden');
      }
      return;
    }
    if (qty - quickSelection.remaining > 0.0001){
      if (quickError){
        quickError.textContent = 'Quantity cannot exceed what is still outstanding.';
        quickError.classList.remove('hidden');
      }
      return;
    }
    if (quickError){
      quickError.classList.add('hidden');
    }
    const payload = [{ purchase_id: quickSelection.purchaseId, quantity: qty, unit: quickSelection.unit }];
    itemsJson.value = JSON.stringify(payload);
    closeQuickModal();
    deliveriesForm.submit();
  });

  // Initialize delivery modal
  const deliveryModal = document.getElementById('deliveryModal');
  const deliveryModalClose = deliveryModal?.querySelector('.deliveryModalClose');
  const deliveryModalForm = document.getElementById('deliveryModalForm');
  const deliveryItemSelect = document.getElementById('deliveryItemSelect');
  const deliveryItemSearch = document.getElementById('deliveryItemSearch');
  const deliveryItemDropdown = document.getElementById('deliveryItemDropdown');
  const deliveryQuantityInput = document.getElementById('deliveryQuantityInput');
  const deliveryUnitInput = document.getElementById('deliveryUnitInput');
  const deliveryUnitSelect = document.getElementById('deliveryUnitSelect');
  const deliveryUnitHelp = document.getElementById('deliveryUnitHelp');
  const deliverySupplierInput = document.getElementById('deliverySupplierInput');
  const deliveryAddItemBtn = document.getElementById('deliveryAddItemBtn');
  const deliveryItemsBody = document.getElementById('deliveryItemsBody');
  const deliveryEmptyState = document.getElementById('deliveryEmptyState');
  const deliveryModalItemsJson = document.getElementById('deliveryModalItemsJson');
  const deliverySubmitBtn = document.getElementById('deliverySubmitBtn');
  const deliveryBuilderError = document.getElementById('deliveryBuilderError');
  let deliveryItems = [];
  let currentDeliveryGroup = null;
  let lastSelectedUnit = ''; // Track last selected unit for conversion calculations
  let deliveryIngredientOptions = []; // Store all ingredient options for search

  function openDeliveryModal(groupId){
    if (!deliveryModal) {
      alert('Error: Delivery modal not found. Please refresh the page.');
      return;
    }
    const g = GROUPS.find(x=>x.group_id===groupId);
    if (!g) {
      alert('Error: Purchase batch not found. Please refresh the page.');
      return;
    }
    
    // Allow opening modal even if batch has no items or all items are fully delivered
    // This enables restocking any ingredient from inventory
    if (!g.items) {
      g.items = [];
    }
    
    currentDeliveryGroup = g;
    deliveryItems = [];
    
    // Set modal header info
    const batchLabelEl = document.getElementById('deliveryModalBatchLabel');
    const supplierEl = document.getElementById('deliveryModalSupplier');
    const purchaserEl = document.getElementById('deliveryModalPurchaser');
    const dateEl = document.getElementById('deliveryModalDate');
    
    if (batchLabelEl) batchLabelEl.textContent = '#' + g.group_id;
    if (supplierEl) supplierEl.textContent = g.supplier || '—';
    if (purchaserEl) purchaserEl.textContent = g.purchaser_name || '—';
    if (dateEl) dateEl.textContent = g.date_purchased || '—';
    
    // Display purchase items as a note
    const purchaseItemsList = document.getElementById('deliveryPurchaseItemsList');
    const purchaseItemsEmpty = document.getElementById('deliveryPurchaseItemsEmpty');
    
    if (purchaseItemsList && purchaseItemsEmpty) {
      purchaseItemsList.innerHTML = '';
      
      if (g.items && Array.isArray(g.items) && g.items.length > 0) {
        purchaseItemsEmpty.classList.add('hidden');
        
        g.items.forEach((item) => {
          const orderedQty = parseFloat(item.purchase_quantity || item.quantity || 0);
          
          // Extract item name and unit from purchase_unit
          let itemName = item.item_name || 'Unknown Item';
          let displayUnit = item.purchase_unit || item.unit || 'pcs';
          
          // Check if purchase_unit contains item name in format "itemName|unit"
          if (item.purchase_unit && item.purchase_unit.indexOf('|') !== -1) {
            const parts = item.purchase_unit.split('|');
            if (parts.length >= 2) {
              itemName = parts[0].trim();
              displayUnit = parts[1].trim();
            }
          }
          
          const tr = document.createElement('tr');
          tr.className = 'hover:bg-gray-50';
          tr.innerHTML = `
            <td class="px-3 py-2 font-medium text-gray-900">${escapeHtml(itemName)}</td>
            <td class="px-3 py-2 text-gray-700">${orderedQty.toFixed(2)}</td>
            <td class="px-3 py-2 text-gray-700">${escapeHtml(displayUnit)}</td>
          `;
          purchaseItemsList.appendChild(tr);
        });
      } else {
        purchaseItemsEmpty.classList.remove('hidden');
      }
    }
    
    // Populate item select dropdown with ALL ingredients from inventory
    if (!deliveryItemSelect) {
      alert('Error: Delivery form elements not found. Please refresh the page.');
      return;
    }
    deliveryItemSelect.innerHTML = '<option value="">Choose ingredient</option>';
    
    // Create a map of ingredient IDs to purchase items in this batch for linking
    const purchaseItemsByIngredient = {};
    if (g.items && Array.isArray(g.items)) {
      g.items.forEach((item, idx) => {
      const remainingBase = Math.max(0, (item.quantity - item.delivered));
      if (remainingBase <= 0.0001) return; // Skip fully delivered items
      
      const ingredientId = item.item_id || null;
      if (!ingredientId) return;
      
      if (!purchaseItemsByIngredient[ingredientId]) {
        purchaseItemsByIngredient[ingredientId] = [];
      }
      
      const remainingDisplay = item.remaining_display !== undefined ? item.remaining_display : remainingBase;
      const displayUnit = item.purchase_unit || item.unit;
      
      purchaseItemsByIngredient[ingredientId].push({
        index: idx,
        purchaseId: item.purchase_id,
        itemName: item.item_name,
        quantity: remainingDisplay,
        quantityBase: remainingBase,
        unit: displayUnit,
        baseUnit: item.unit,
        supplier: g.supplier || ''
      });
      });
    }
    
    // Store all ingredient options for search functionality
    deliveryIngredientOptions = [];
    
    // Add ALL ingredients from inventory (not just those matching purchase items)
    if (typeof INGREDIENTS !== 'undefined' && Array.isArray(INGREDIENTS)) {
      INGREDIENTS.forEach(ing => {
      const unitLabel = ing.display_unit || ing.unit || 'unit';
      const displayText = `${ing.name} (${unitLabel})`;
      
      // Store matching purchase items for this ingredient (if any)
      const matchingPurchases = purchaseItemsByIngredient[ing.id] || [];
      let purchaseData = {};
      if (matchingPurchases.length > 0) {
        // Use the first matching purchase item
        const purchaseItem = matchingPurchases[0];
        purchaseData = {
          purchaseId: purchaseItem.purchaseId,
          purchaseIndex: purchaseItem.index,
          quantity: purchaseItem.quantity,
          quantityBase: purchaseItem.quantityBase
        };
      } else {
        purchaseData = {
          purchaseId: '',
          purchaseIndex: ''
        };
      }
      
      // Store option data for search
      deliveryIngredientOptions.push({
        id: ing.id,
        name: ing.name,
        displayText: displayText,
        baseUnit: ing.unit || '',
        displayUnit: ing.display_unit || '',
        displayFactor: ing.display_factor || 1,
        supplier: g.supplier || '',
        ...purchaseData
      });
      
      // Also add to hidden select for backward compatibility
      const opt = document.createElement('option');
      opt.value = ing.id;
      opt.textContent = displayText;
      opt.dataset.ingredientId = ing.id;
      opt.dataset.itemName = ing.name;
      opt.dataset.baseUnit = ing.unit || '';
      opt.dataset.displayUnit = ing.display_unit || '';
      opt.dataset.displayFactor = ing.display_factor || 1;
      opt.dataset.supplier = g.supplier || '';
      opt.dataset.purchaseId = purchaseData.purchaseId;
      opt.dataset.purchaseIndex = purchaseData.purchaseIndex;
      if (purchaseData.quantity !== undefined) {
        opt.dataset.quantity = purchaseData.quantity;
        opt.dataset.quantityBase = purchaseData.quantityBase;
      }
      deliveryItemSelect.appendChild(opt);
      });
    }
    
    // Clear search input and show all options
    if (deliveryItemSearch) {
      deliveryItemSearch.value = '';
    }
    filterDeliveryIngredients('');
    
    renderDeliveryItems();
    
    // Show the modal - ensure it's visible
    deliveryModal.classList.remove('hidden');
    deliveryModal.classList.add('flex');
    deliveryModal.style.display = 'flex'; // Force display
    deliveryModal.style.zIndex = '50'; // Ensure z-index
    document.body.classList.add('overflow-hidden');
    
    // Initialize lucide icons
    if (typeof lucide !== 'undefined') {
      setTimeout(() => lucide.createIcons(), 100);
    }
  }

  function closeDeliveryModal(){
    if (!deliveryModal) return;
    deliveryModal.classList.add('hidden');
    deliveryModal.classList.remove('flex');
    deliveryModal.style.display = 'none'; // Force hide
    document.body.classList.remove('overflow-hidden');
    deliveryItems = [];
    currentDeliveryGroup = null;
    renderDeliveryItems();
    deliveryItemSelect.value = '';
    if (deliveryItemSearch) deliveryItemSearch.value = '';
    if (deliveryItemDropdown) {
      deliveryItemDropdown.classList.add('hidden');
      deliveryItemDropdown.innerHTML = '';
    }
    deliveryQuantityInput.value = '';
    deliveryUnitInput.value = '';
    if (deliveryUnitSelect) {
      deliveryUnitSelect.value = '';
      deliveryUnitSelect.classList.add('hidden');
    }
    deliveryUnitInput.classList.remove('hidden');
    deliverySupplierInput.value = '';
    deliveryQuantityInput.readOnly = true;
    deliveryUnitInput.readOnly = true;
    if (deliveryUnitSelect) deliveryUnitSelect.disabled = true;
    deliverySupplierInput.readOnly = true;
    if (deliveryBuilderError) deliveryBuilderError.classList.add('hidden');
  }

  function filterDeliveryIngredients(searchTerm) {
    if (!deliveryItemDropdown) return;
    
    const term = (searchTerm || '').toLowerCase().trim();
    const filtered = deliveryIngredientOptions.filter(opt => {
      const nameMatch = opt.name.toLowerCase().includes(term);
      const unitMatch = (opt.displayUnit || opt.baseUnit || '').toLowerCase().includes(term);
      return nameMatch || unitMatch;
    });
    
    deliveryItemDropdown.innerHTML = '';
    
    if (filtered.length === 0) {
      deliveryItemDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No ingredients found</div>';
      deliveryItemDropdown.classList.remove('hidden');
      return;
    }
    
    filtered.forEach(opt => {
      const div = document.createElement('div');
      div.className = 'px-4 py-2 hover:bg-orange-50 cursor-pointer border-b border-gray-100 last:border-b-0';
      div.dataset.ingredientId = opt.id;
      div.innerHTML = `
        <div class="font-medium text-gray-900">${escapeHtml(opt.name)}</div>
        <div class="text-xs text-gray-500">${escapeHtml(opt.displayUnit || opt.baseUnit || 'unit')}</div>
      `;
      
      div.addEventListener('click', () => {
        selectDeliveryIngredient(opt);
      });
      
      deliveryItemDropdown.appendChild(div);
    });
    
    deliveryItemDropdown.classList.remove('hidden');
  }

  function selectDeliveryIngredient(option) {
    if (!option || !deliveryItemSelect) return;
    
    // Find the matching option element
    let optElement = deliveryItemSelect.querySelector(`option[value="${option.id}"]`);
    
    // If option doesn't exist, create it with all necessary data attributes
    if (!optElement) {
      optElement = document.createElement('option');
      optElement.value = option.id;
      optElement.textContent = option.displayText;
      optElement.dataset.ingredientId = option.id;
      optElement.dataset.itemName = option.name;
      optElement.dataset.baseUnit = option.baseUnit || '';
      optElement.dataset.displayUnit = option.displayUnit || '';
      optElement.dataset.displayFactor = option.displayFactor || 1;
      optElement.dataset.supplier = option.supplier || '';
      optElement.dataset.purchaseId = option.purchaseId || '';
      optElement.dataset.purchaseIndex = option.purchaseIndex || '';
      if (option.quantity !== undefined) {
        optElement.dataset.quantity = option.quantity;
        optElement.dataset.quantityBase = option.quantityBase;
      }
      deliveryItemSelect.appendChild(optElement);
    }
    
    // Set the hidden select value to trigger existing change handler
    deliveryItemSelect.value = option.id;
    
    // Update search input to show selected name
    if (deliveryItemSearch) {
      deliveryItemSearch.value = option.displayText;
    }
    
    // Hide dropdown
    if (deliveryItemDropdown) {
      deliveryItemDropdown.classList.add('hidden');
    }
    
    // Trigger the change event to use existing logic
    deliveryItemSelect.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function showDeliveryError(message){
    if (!deliveryBuilderError) return;
    deliveryBuilderError.textContent = message;
    deliveryBuilderError.classList.remove('hidden');
  }

  function clearDeliveryError(){
    if (!deliveryBuilderError) return;
    deliveryBuilderError.textContent = '';
    deliveryBuilderError.classList.add('hidden');
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function renderDeliveryItems(){
    if (!deliveryItemsBody || !deliveryEmptyState) return;
    deliveryItemsBody.innerHTML = '';
    if (!deliveryItems.length){
      deliveryEmptyState.classList.remove('hidden');
      if (deliverySubmitBtn) deliverySubmitBtn.disabled = true;
      return;
    }
    deliveryEmptyState.classList.add('hidden');
    if (deliverySubmitBtn) deliverySubmitBtn.disabled = false;
    
    deliveryItems.forEach((item, index) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="px-4 py-2">
          <div class="flex items-center gap-2">
            <i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
            <div>
              <p class="font-medium text-gray-900">${item.itemName}</p>
            </div>
          </div>
        </td>
        <td class="px-4 py-2 font-semibold text-gray-900">${Number(item.quantity).toFixed(2)}</td>
        <td class="px-4 py-2 text-gray-600">${item.unit}</td>
        <td class="px-4 py-2 text-gray-600">${item.supplier}</td>
        <td class="px-4 py-2">
          <button type="button" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 removeDeliveryItem" data-index="${index}">
            <i data-lucide="trash-2" class="w-3 h-3"></i>Remove
          </button>
        </td>
      `;
      deliveryItemsBody.appendChild(tr);
    });
    
    // Initialize lucide icons after rendering
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
    
    // Update hidden JSON input
    const jsonData = deliveryItems.map(item => ({
      purchase_id: item.purchaseId,
      ingredient_id: item.ingredientId,
      quantity: item.quantity,
      unit: item.unit,
      supplier: item.supplier
    }));
    if (deliveryModalItemsJson) deliveryModalItemsJson.value = JSON.stringify(jsonData);
  }

  // Handle search input for ingredient selection
  if (deliveryItemSearch) {
    deliveryItemSearch.addEventListener('input', (e) => {
      const searchTerm = e.target.value;
      filterDeliveryIngredients(searchTerm);
    });
    
    deliveryItemSearch.addEventListener('focus', () => {
      if (deliveryItemSearch.value.trim() === '') {
        filterDeliveryIngredients('');
      } else {
        filterDeliveryIngredients(deliveryItemSearch.value);
      }
    });
    
    deliveryItemSearch.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        // Select first filtered option if available
        const firstOption = deliveryItemDropdown?.querySelector('[data-ingredient-id]');
        if (firstOption) {
          const ingredientId = firstOption.dataset.ingredientId;
          const option = deliveryIngredientOptions.find(opt => opt.id.toString() === ingredientId);
          if (option) {
            selectDeliveryIngredient(option);
          }
        }
      } else if (e.key === 'Escape') {
        if (deliveryItemDropdown) {
          deliveryItemDropdown.classList.add('hidden');
        }
        deliveryItemSearch.blur();
      }
    });
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (deliveryItemSearch && deliveryItemDropdown) {
      if (!deliveryItemSearch.contains(e.target) && !deliveryItemDropdown.contains(e.target)) {
        deliveryItemDropdown.classList.add('hidden');
      }
    }
  });

  // Handle item selection
  if (deliveryItemSelect) {
    deliveryItemSelect.addEventListener('change', ()=>{
      const selectedOpt = deliveryItemSelect.options[deliveryItemSelect.selectedIndex];
      if (!selectedOpt || !selectedOpt.value) {
        deliveryQuantityInput.value = '';
        deliveryUnitInput.value = '';
        if (deliveryUnitSelect) {
          deliveryUnitSelect.value = '';
          deliveryUnitSelect.classList.add('hidden');
        }
        deliveryUnitInput.classList.remove('hidden');
        if (deliveryUnitHelp) {
          deliveryUnitHelp.classList.add('hidden');
          deliveryUnitHelp.textContent = '';
        }
        deliverySupplierInput.value = '';
        deliveryQuantityInput.readOnly = true;
        deliveryUnitInput.readOnly = true;
        if (deliveryUnitSelect) deliveryUnitSelect.disabled = true;
        deliverySupplierInput.readOnly = true;
        lastSelectedUnit = '';
        return;
      }
      
      const ingredientId_ = parseInt(selectedOpt.value, 10);
      const ingredient = INGREDIENT_LOOKUP[ingredientId_];
      
      if (!ingredient) {
        clearDeliveryError();
        return;
      }
      
      // Enhanced unit selection: support g/kg, ml/L, and custom display_unit
      const baseUnit = ingredient.unit || '';
      const displayUnit = ingredient.display_unit || '';
      const displayFactor = parseFloat(ingredient.display_factor || 1);
      
      // Check if we should show a dropdown for standard conversions or custom display_unit
      let shouldShowDropdown = false;
      let dropdownOptions = [];
      let defaultUnit = baseUnit;
      
      // Standard conversions: g/kg
      if (baseUnit === 'g') {
        shouldShowDropdown = true;
        dropdownOptions = [
          { value: 'g', label: 'g (grams)' },
          { value: 'kg', label: 'kg (kilograms)' }
        ];
        defaultUnit = 'g';
        lastSelectedUnit = 'g';
      }
      // Standard conversions: ml/L
      else if (baseUnit === 'ml') {
        shouldShowDropdown = true;
        dropdownOptions = [
          { value: 'ml', label: 'ml (milliliters)' },
          { value: 'L', label: 'L (liters)' }
        ];
        defaultUnit = 'ml';
        lastSelectedUnit = 'ml';
      }
      // Custom display_unit with conversion factor
      else if (displayUnit && displayUnit !== baseUnit && displayFactor > 0 && displayFactor !== 1) {
        shouldShowDropdown = true;
        dropdownOptions = [
          { value: baseUnit, label: `${baseUnit} (base unit)` },
          { value: displayUnit, label: `${displayUnit} (${displayFactor}x)` }
        ];
        defaultUnit = displayUnit; // Default to display unit for better UX
        lastSelectedUnit = displayUnit;
      }
      
      if (shouldShowDropdown && dropdownOptions.length > 0) {
        // Show dropdown
        if (deliveryUnitInput) deliveryUnitInput.classList.add('hidden');
        if (deliveryUnitSelect) {
          deliveryUnitSelect.classList.remove('hidden');
          deliveryUnitSelect.innerHTML = '<option value="">Select unit</option>' + 
            dropdownOptions.map(opt => `<option value="${opt.value}">${opt.label}</option>`).join('');
          deliveryUnitSelect.disabled = false;
          deliveryUnitSelect.value = defaultUnit;
          lastSelectedUnit = defaultUnit;
        }
        // Show conversion help text
        if (deliveryUnitHelp) {
          let helpText = '';
          if (baseUnit === 'g') {
            helpText = '1 kg = 1000 g';
          } else if (baseUnit === 'ml') {
            helpText = '1 L = 1000 ml';
          } else if (displayUnit && displayFactor > 1) {
            helpText = `1 ${displayUnit} = ${displayFactor} ${baseUnit}`;
          }
          if (helpText) {
            deliveryUnitHelp.textContent = helpText;
            deliveryUnitHelp.classList.remove('hidden');
          } else {
            deliveryUnitHelp.classList.add('hidden');
          }
        }
      } else {
        // Show regular input for other units
        if (deliveryUnitInput) deliveryUnitInput.classList.remove('hidden');
        if (deliveryUnitSelect) deliveryUnitSelect.classList.add('hidden');
        const unitToShow = displayUnit || baseUnit || '';
        deliveryUnitInput.value = unitToShow;
        // Hide help text for manual input
        if (deliveryUnitHelp) {
          if (displayUnit && displayUnit !== baseUnit && displayFactor > 1) {
            deliveryUnitHelp.textContent = `Note: Enter quantity in ${unitToShow}. Conversion: 1 ${displayUnit} = ${displayFactor} ${baseUnit}`;
            deliveryUnitHelp.classList.remove('hidden');
          } else {
            deliveryUnitHelp.classList.add('hidden');
          }
        }
      }
      
      // Auto-fill quantity if there's a matching purchase item in this batch
      const purchaseIndex = selectedOpt.dataset.purchaseIndex;
      if (purchaseIndex !== undefined && purchaseIndex !== '' && currentDeliveryGroup) {
        const idx = parseInt(purchaseIndex, 10);
        const item = currentDeliveryGroup.items[idx];
        if (item) {
          const remainingBase = Math.max(0, (item.quantity - item.delivered));
          let remainingDisplay = item.remaining_display !== undefined ? item.remaining_display : remainingBase;
          
          // Convert quantity based on selected unit in dropdown
          if (deliveryUnitSelect && !deliveryUnitSelect.classList.contains('hidden')) {
            const selectedUnit = deliveryUnitSelect.value || defaultUnit;
            
            // g/kg conversion
            if (baseUnit === 'g') {
              if (selectedUnit === 'kg' && remainingDisplay >= 1000) {
                remainingDisplay = remainingDisplay / 1000; // Convert grams to kg
              }
            }
            // ml/L conversion
            else if (baseUnit === 'ml') {
              if (selectedUnit === 'L' && remainingDisplay >= 1000) {
                remainingDisplay = remainingDisplay / 1000; // Convert ml to L
              }
            }
            // Custom display_unit conversion
            else if (displayUnit && displayFactor > 0) {
              if (selectedUnit === displayUnit && remainingDisplay >= displayFactor) {
                remainingDisplay = remainingDisplay / displayFactor; // Convert to display unit
              }
            }
          }
          
          deliveryQuantityInput.value = remainingDisplay.toFixed(2);
        } else {
          deliveryQuantityInput.value = '';
        }
      } else {
        deliveryQuantityInput.value = '';
      }
      
      deliverySupplierInput.value = selectedOpt.dataset.supplier || currentDeliveryGroup?.supplier || '';
      deliveryQuantityInput.readOnly = false;
      if (deliveryUnitInput && !deliveryUnitInput.classList.contains('hidden')) {
        deliveryUnitInput.readOnly = false;
      }
      if (deliveryUnitSelect && !deliveryUnitSelect.classList.contains('hidden')) {
        deliveryUnitSelect.disabled = false;
      }
      deliverySupplierInput.readOnly = false;
      clearDeliveryError();
    });
  }

  // Handle unit selection change for all conversion types (g/kg, ml/L, custom)
  if (deliveryUnitSelect) {
    deliveryUnitSelect.addEventListener('change', ()=>{
      const selectedOpt = deliveryItemSelect?.options[deliveryItemSelect?.selectedIndex];
      if (!selectedOpt || !selectedOpt.value) return;
      
      const ingredientId_ = parseInt(selectedOpt.value, 10);
      const ingredient = INGREDIENT_LOOKUP[ingredientId_];
      if (!ingredient) return;
      
      const baseUnit = ingredient.unit || '';
      const displayUnit = ingredient.display_unit || '';
      const displayFactor = parseFloat(ingredient.display_factor || 1);
      const selectedUnit = deliveryUnitSelect.value;
      const currentQty = parseFloat(deliveryQuantityInput?.value || '0');
      
      if (currentQty > 0 && lastSelectedUnit && lastSelectedUnit !== selectedUnit) {
        let convertedQty = currentQty;
        
        // g/kg conversion
        if (baseUnit === 'g') {
          if (lastSelectedUnit === 'g' && selectedUnit === 'kg') {
            convertedQty = currentQty / 1000; // Convert grams to kg
          } else if (lastSelectedUnit === 'kg' && selectedUnit === 'g') {
            convertedQty = currentQty * 1000; // Convert kg to grams
          }
        }
        // ml/L conversion
        else if (baseUnit === 'ml') {
          if (lastSelectedUnit === 'ml' && selectedUnit === 'L') {
            convertedQty = currentQty / 1000; // Convert ml to L
          } else if (lastSelectedUnit === 'L' && selectedUnit === 'ml') {
            convertedQty = currentQty * 1000; // Convert L to ml
          }
        }
        // Custom display_unit conversion
        else if (displayUnit && displayFactor > 0) {
          if (lastSelectedUnit === baseUnit && selectedUnit === displayUnit) {
            convertedQty = currentQty / displayFactor; // Convert base to display unit
          } else if (lastSelectedUnit === displayUnit && selectedUnit === baseUnit) {
            convertedQty = currentQty * displayFactor; // Convert display to base unit
          }
        }
        
        deliveryQuantityInput.value = convertedQty.toFixed(2);
      }
      
      lastSelectedUnit = selectedUnit;
    });
  }

  // Handle add item button
  if (deliveryAddItemBtn) {
    deliveryAddItemBtn.addEventListener('click', ()=>{
      const selectedOpt = deliveryItemSelect?.options[deliveryItemSelect?.selectedIndex];
      if (!selectedOpt || !selectedOpt.value) {
        showDeliveryError('Please select an ingredient.');
        return;
      }
      
      const ingredientId_ = parseInt(selectedOpt.value, 10);
      const ingredient = INGREDIENT_LOOKUP[ingredientId_];
      
      if (!ingredient) {
        showDeliveryError('Please select a valid ingredient.');
        return;
      }
      
      let quantity = parseFloat(deliveryQuantityInput?.value || '0');
      let unit = '';
      
      // Get unit from either dropdown (for conversions) or input field
      const baseUnit = ingredient.unit || '';
      const displayUnit = ingredient.display_unit || '';
      const displayFactor = parseFloat(ingredient.display_factor || 1);
      
      // Check if dropdown is visible (means we have a conversion scenario)
      if (deliveryUnitSelect && !deliveryUnitSelect.classList.contains('hidden')) {
        unit = (deliveryUnitSelect?.value || '').trim();
        
        // g/kg conversion
        if (baseUnit === 'g') {
          if (unit === 'kg') {
            quantity = quantity * 1000; // Convert kg to grams
            unit = 'g'; // Store as grams in base unit
          } else {
            unit = 'g'; // Already in grams
          }
        }
        // ml/L conversion
        else if (baseUnit === 'ml') {
          if (unit === 'L') {
            quantity = quantity * 1000; // Convert L to ml
            unit = 'ml'; // Store as ml in base unit
          } else {
            unit = 'ml'; // Already in ml
          }
        }
        // Custom display_unit conversion
        else if (displayUnit && displayFactor > 0) {
          if (unit === displayUnit) {
            quantity = quantity * displayFactor; // Convert display unit to base unit
            unit = baseUnit; // Store in base unit
          } else {
            unit = baseUnit; // Already in base unit
          }
        }
      } else {
        // Manual input - use as-is (backend will handle conversion if needed)
        unit = (deliveryUnitInput?.value || '').trim();
        
        // Validate unit format (should not be empty and should be reasonable length)
        if (!unit || unit.length > 32) {
          showDeliveryError('Please enter a valid unit name (max 32 characters).');
          return;
        }
        
        // Warn if unit doesn't match expected display_unit (optional warning, not blocking)
        if (displayUnit && unit !== displayUnit && unit !== baseUnit) {
          // Allow it but show a note - backend will handle conversion
          console.log(`Note: Unit "${unit}" may not match expected unit "${displayUnit}" or base unit "${baseUnit}". Backend will attempt conversion.`);
        }
      }
      
      const supplier = (deliverySupplierInput?.value || '').trim();
      
      if (!quantity || quantity <= 0) {
        showDeliveryError('Please enter a valid quantity.');
        return;
      }
      if (!unit) {
        showDeliveryError('Please select or enter a unit.');
        return;
      }
      
      // Find matching purchase item for this ingredient in the current batch (if any)
      const purchaseId = selectedOpt.dataset.purchaseId;
      const purchaseIndex = selectedOpt.dataset.purchaseIndex;
      
      // Check if this ingredient already added (by ingredient ID)
      const ingredientId = parseInt(selectedOpt.value, 10);
      const existing = deliveryItems.find(entry => entry.ingredientId === ingredientId);
      if (existing) {
        showDeliveryError('This ingredient has already been added. Remove it first to modify.');
        return;
      }
      
      // If there's a matching purchase in the batch, use it; otherwise, we'll create one on the backend
      let purchaseIdInt = null;
      if (purchaseId && purchaseId !== '') {
        purchaseIdInt = parseInt(purchaseId, 10);
      }
      
      deliveryItems.push({
        purchaseId: purchaseIdInt, // May be null if no matching purchase
        ingredientId: ingredientId,
        itemName: ingredient.name,
        quantity: quantity,
        unit: unit,
        supplier: supplier || currentDeliveryGroup?.supplier || ''
      });
      
      // Clear inputs
      deliveryItemSelect.value = '';
      if (deliveryItemSearch) deliveryItemSearch.value = '';
      if (deliveryItemDropdown) {
        deliveryItemDropdown.classList.add('hidden');
        deliveryItemDropdown.innerHTML = '';
      }
      deliveryQuantityInput.value = '';
      deliveryUnitInput.value = '';
      if (deliveryUnitSelect) {
        deliveryUnitSelect.value = '';
        deliveryUnitSelect.classList.add('hidden');
      }
      deliveryUnitInput.classList.remove('hidden');
      if (deliveryUnitHelp) {
        deliveryUnitHelp.classList.add('hidden');
        deliveryUnitHelp.textContent = '';
      }
      deliverySupplierInput.value = '';
      deliveryQuantityInput.readOnly = true;
      deliveryUnitInput.readOnly = true;
      if (deliveryUnitSelect) deliveryUnitSelect.disabled = true;
      deliverySupplierInput.readOnly = true;
      lastSelectedUnit = '';
      
      renderDeliveryItems();
      clearDeliveryError();
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Handle remove item
  if (deliveryItemsBody) {
    deliveryItemsBody.addEventListener('click', (e)=>{
      const btn = e.target.closest('.removeDeliveryItem');
      if (!btn) return;
      const index = parseInt(btn.dataset.index || '-1', 10);
      if (index >= 0 && index < deliveryItems.length) {
        deliveryItems.splice(index, 1);
        renderDeliveryItems();
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      }
    });
  }

  // Handle modal close
  if (deliveryModalClose) {
    deliveryModalClose.addEventListener('click', closeDeliveryModal);
    deliveryModal?.addEventListener('click', (e)=>{
      if (e.target === deliveryModal) closeDeliveryModal();
    });
  }

  // Handle modal form submission
  if (deliveryModalForm) {
    deliveryModalForm.addEventListener('submit', (e)=>{
      if (deliveryItems.length === 0) {
        e.preventDefault();
        showDeliveryError('Please add at least one item to record delivery.');
        return;
      }
      // Form will submit normally with items_json
    });
  }


  // Update batch selection to show details and open modal
  if (sel) {
    sel.addEventListener('change', ()=>{ 
      const groupId = sel.value;
      updateSelectedBatchCard(groupId);
      if (groupId && groupId !== '') {
        // Small delay to show the card before opening modal
        setTimeout(() => {
          openDeliveryModal(groupId);
        }, 100);
      } else {
        if (box) box.classList.add('hidden');
        if (itemsJson) itemsJson.value='[]';
      }
    });
  }

  function applyDeliveryFilter(value){
    if (!filterSelect || deliveryRows.length === 0) return;
    const filter = value || filterSelect.value || 'all';
    let visible = 0;
    deliveryRows.forEach(row => {
      const status = row.getAttribute('data-delivery-status');
      const matches = filter === 'all' || status === filter;
      row.classList.toggle('hidden', !matches);
      if (matches) { visible++; }
    });
    if (filterEmpty){
      filterEmpty.classList.toggle('hidden', visible !== 0);
    }
  }
  if (filterSelect){
    const statusParam = (urlParams.get('status') || 'all').toLowerCase();
    const initialFilter = ['complete','partial'].includes(statusParam) ? statusParam : 'all';
    filterSelect.value = initialFilter;
    applyDeliveryFilter(initialFilter);
    filterSelect.addEventListener('change', ()=>{
      const value = filterSelect.value || 'all';
      const params = new URLSearchParams(window.location.search);
      if (value === 'all'){
        params.delete('status');
      } else {
        params.set('status', value);
      }
      const query = params.toString();
      const newUrl = window.location.pathname + (query ? `?${query}` : '') + window.location.hash;
      window.history.replaceState({}, '', newUrl);
      applyDeliveryFilter(value);
    });
    if (statusParam === 'awaiting'){
      const awaiting = document.getElementById('awaiting-deliveries');
      if (awaiting){
        awaiting.classList.add('ring-2','ring-orange-200','ring-offset-2');
        awaiting.scrollIntoView({behavior:'smooth'});
        setTimeout(()=> awaiting.classList.remove('ring-2','ring-offset-2','ring-orange-200'), 2000);
      }
    }
  } else {
    applyDeliveryFilter('all');
  }
  
  // Initialize lucide icons for new UI elements
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
})();
</script>

<style>
	/* Remove focus ring and border color for input fields in deliveries page on tablet mode */
	@media (min-width: 768px) and (max-width: 1023px) {
		#deliveriesForm input:focus,
		#deliveriesForm select:focus,
		#deliveriesForm textarea:focus,
		#deliveryModal input:focus,
		#deliveryModal select:focus,
		#deliveryModal textarea:focus,
		#receiveQuickModal input:focus,
		#receiveQuickModal select:focus,
		#receiveQuickModal textarea:focus,
		#awaiting-deliveries input:focus,
		#awaiting-deliveries select:focus,
		#awaiting-deliveries textarea:focus {
			outline: none !important;
			box-shadow: none !important;
			border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
			--tw-ring-offset-shadow: 0 0 #0000 !important;
			--tw-ring-shadow: 0 0 #0000 !important;
			--tw-ring-offset-width: 0px !important;
			--tw-ring-width: 0px !important;
		}
	}
</style>


