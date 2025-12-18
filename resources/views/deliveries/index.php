<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$deliveredTotals = $deliveredTotals ?? [];

// Helper function to format date for batch display
function formatBatchDate($dateString) {
	if (empty($dateString)) return '';
	$date = substr($dateString, 0, 10);
	if (strlen($date) !== 10) return $dateString;
	try {
		$timestamp = strtotime($date);
		if ($timestamp === false) return $dateString;
		return date('M j, Y', $timestamp);
	} catch (Exception $e) {
		return $dateString;
	}
}
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div>
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1">Delivery Management</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Record and track ingredient deliveries</p>
		</div>
	</div>
    <?php if (!empty($flash)): ?>
    <div class="mt-3 px-4 py-3 rounded-lg border <?php echo $flash['type']==='success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-700'; ?>">
        <?php echo htmlspecialchars($flash['text'] ?? ''); ?>
    </div>
    <?php endif; ?>
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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8">
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden">
	<div class="bg-gray-100 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
			<i data-lucide="package-check" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
			Record New Delivery
		</h2>
		<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Record a delivery for an existing purchase</p>
	</div>
	
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="p-4 sm:p-6" id="deliveriesForm">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        <input type="hidden" name="items_json" id="deliveriesItemsJson" value="[]">
		
		<!-- Purchase Batch Selection -->
        <div class="space-y-4 mb-6">
            <div class="space-y-2">
                <label class="block text-sm md:text-base font-semibold text-gray-900">Select Purchase Batch</label>
                <p class="text-xs md:text-sm text-gray-600">Search or select a purchase batch to record deliveries for its items</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-xs md:text-sm font-medium text-gray-700">Search Batch</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        <input id="batchSearchInput" class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors" placeholder="Search by supplier, purchaser, batch ID, or date..." list="batchSearchOptions">
                        <datalist id="batchSearchOptions">
                            <?php 
                            foreach (($purchaseGroups ?? []) as $g): 
                                $dateFormatted = formatBatchDate($g['date_purchased'] ?? '');
                                $itemCount = isset($g['items']) && is_array($g['items']) ? count($g['items']) : 0;
                                $itemText = $itemCount === 1 ? 'item' : 'items';
                                $supplier = htmlspecialchars($g['supplier'] ?? '');
                                $purchaser = htmlspecialchars($g['purchaser_name'] ?? '');
                                
                                // Create display text matching select dropdown format
                                $displayText = '';
                                if ($dateFormatted) {
                                    $displayText = $dateFormatted . ' • ';
                                }
                                $displayText .= $supplier;
                                if ($supplier && $purchaser) {
                                    $displayText .= ' • ';
                                }
                                $displayText .= $purchaser;
                                if ($itemCount > 0) {
                                    $displayText .= ' (' . $itemCount . ' ' . $itemText . ')';
                                }
                                if (!$displayText) {
                                    $displayText = 'Batch #' . htmlspecialchars($g['group_id']);
                                }
                            ?>
                                <option value="<?php echo $displayText; ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs md:text-sm font-medium text-gray-700">Or Select from List</label>
                    <select id="batchSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors bg-white">
                        <option value="">Choose a batch from the list</option>
                        <?php 
                        foreach (($purchaseGroups ?? []) as $g): 
                            $dateFormatted = formatBatchDate($g['date_purchased'] ?? '');
                            $itemCount = isset($g['items']) && is_array($g['items']) ? count($g['items']) : 0;
                            $itemText = $itemCount === 1 ? 'item' : 'items';
                            $supplier = htmlspecialchars($g['supplier'] ?? '');
                            $purchaser = htmlspecialchars($g['purchaser_name'] ?? '');
                            $batchId = htmlspecialchars($g['group_id']);
                            
                            // Create a more readable format: Date - Supplier - Purchaser (X items)
                            $displayText = '';
                            if ($dateFormatted) {
                                $displayText = $dateFormatted . ' • ';
                            }
                            $displayText .= $supplier;
                            if ($supplier && $purchaser) {
                                $displayText .= ' • ';
                            }
                            $displayText .= $purchaser;
                            if ($itemCount > 0) {
                                $displayText .= ' (' . $itemCount . ' ' . $itemText . ')';
                            }
                            if (!$displayText) {
                                $displayText = 'Batch #' . $batchId;
                            }
                        ?>
                            <option value="<?php echo $batchId; ?>"><?php echo $displayText; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div id="batchHighlight" class="hidden rounded-xl border-2 border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100 px-4 py-3 space-y-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-orange-600"></i>
                    <div class="font-semibold text-orange-900 text-sm md:text-base" id="batchMetaSupplier"></div>
                </div>
                <div class="flex flex-wrap gap-4 text-xs md:text-sm text-orange-800">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span id="batchMetaPurchaser"></span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span id="batchMetaDate"></span>
                    </span>
                </div>
            </div>
            
            <p class="text-xs md:text-sm text-gray-500" id="batchMeta">Select a batch above to view and set delivery quantities for each item.</p>
        </div>
		
        <div id="batchItemsBox" class="mt-6 hidden">
            <div class="mb-3">
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Delivery Items</h3>
                <p class="text-xs md:text-sm text-gray-600 mt-1">Set the quantities received for each item in this batch</p>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full text-sm min-w-[560px]" id="batchItemsTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 text-xs md:text-sm">Item</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 text-xs md:text-sm">Remaining</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 text-xs md:text-sm">Receive Now</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 text-xs md:text-sm">Unit</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 text-xs md:text-sm">Auto Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
			<button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="package-check" class="w-4 h-4"></i>
				Record Delivery
			</button>
		</div>
        <div id="deliveryInlineError" class="hidden mt-3 px-4 py-3 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700"></div>
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
										<th class="text-left px-3 py-2 font-medium text-gray-700">Purchase Qty</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Unit</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700 w-24">Action</th>
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
				
				<!-- Receive Item Section (shown when Add button is clicked) -->
				<div id="receiveItemSection" class="hidden rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="bg-gray-50 px-4 py-2 rounded-lg">
						<h3 class="text-sm font-semibold text-gray-900">Receive Item</h3>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Item</label>
							<input type="text" id="receiveItemName" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Item name" readonly>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Purchase Qty</label>
							<input type="text" id="receivePurchaseQty" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="0.00" readonly>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Receive Qty</label>
							<input type="number" step="0.01" min="0" id="receiveQuantity" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<input type="text" id="receiveUnit" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Unit" readonly>
						</div>
					</div>
					<div class="flex items-center gap-2">
						<button type="button" id="receiveUndoBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500">
							<i data-lucide="x" class="w-4 h-4"></i>
							Cancel
						</button>
					</div>
				</div>
				
				<!-- Final Inventory Decision Section -->
				<div id="finalInventorySection" class="hidden rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="bg-gray-50 px-4 py-2 rounded-lg">
						<h3 class="text-sm font-semibold text-gray-900">Final Inventory Decision</h3>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1 relative">
							<label class="text-sm font-medium text-gray-700">Select Item</label>
							<div class="relative">
								<input type="text" id="deliveryItemSearch" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Search ingredient..." autocomplete="off">
								<i data-lucide="search" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
								<select id="deliveryItemSelect" class="hidden">
									<option value="">Choose ingredient</option>
									<!-- Options will be populated dynamically -->
								</select>
								<div id="deliveryItemDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
									<!-- Filtered options will be populated here -->
								</div>
							</div>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Quantity</label>
							<input type="number" step="0.01" min="0" id="deliveryQuantityInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<input type="text" id="deliveryUnitInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Unit">
							<select id="deliveryUnitSelect" class="hidden w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
								<option value="">Select unit</option>
							</select>
							<p id="deliveryUnitHelp" class="hidden text-xs text-gray-500 mt-1"></p>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Supplier</label>
							<input type="text" id="deliverySupplierInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Supplier" readonly>
						</div>
					</div>
					<div class="flex items-end">
						<button type="button" id="deliveryAddItemBtn" class="w-full inline-flex items-center justify-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
							<i data-lucide="plus" class="w-4 h-4"></i>
							Add to Delivery
						</button>
					</div>
				</div>
				
				<!-- Error message (always visible in modal) -->
				<div id="deliveryBuilderError" class="hidden mt-3 px-4 py-3 rounded-lg border border-red-300 bg-red-50 text-sm text-red-700 flex items-start gap-2">
					<i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
					<span class="flex-1 font-medium"></span>
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Items</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Status</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Date Received</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Actions</th>
                </tr>
			</thead>
			<tbody class="divide-y divide-gray-200" id="recentDeliveriesBody">
				<?php foreach ($deliveries as $batch): ?>
				<tr class="hover:bg-gray-50 transition-colors" data-delivery-status="<?php echo strtolower($batch['delivery_status']); ?>" data-batch-id="<?php echo htmlspecialchars($batch['batch_id']); ?>" data-purchase-id="<?php echo (int)$batch['purchase_id']; ?>">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-orange-600">#<?php echo (int)$batch['first_delivery_id']; ?></span>
							</div>
						</div>
					</td>
					
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-purple-600">#<?php echo htmlspecialchars($batch['batch_id']); ?></span>
                            </div>
                        </div>
                    </td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<span class="font-medium text-gray-900"><?php echo (int)$batch['items_count']; ?> item<?php echo (int)$batch['items_count'] !== 1 ? 's' : ''; ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<?php 
						$statusClass = $batch['delivery_status'] === 'Complete' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
						$statusIcon = $batch['delivery_status'] === 'Complete' ? 'check-circle' : 'clock';
						?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
							<i data-lucide="<?php echo $statusIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($batch['delivery_status']); ?>
						</span>
					</td>
					
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<span class="text-gray-600"><?php echo htmlspecialchars($batch['date_received']); ?></span>
						</div>
					</td>
					
					<td class="px-6 py-4">
						<button type="button" class="viewBatchDetailsBtn inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors" data-batch-id="<?php echo htmlspecialchars($batch['batch_id']); ?>" data-purchase-id="<?php echo (int)$batch['purchase_id']; ?>">
							<i data-lucide="eye" class="w-3 h-3"></i>
							View Details
						</button>
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
		
<!-- Delivery Details Modal -->
<div id="deliveryDetailsModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
		<div class="flex items-center justify-between px-6 py-4 border-b">
			<div>
				<p class="text-xs uppercase tracking-wide text-gray-500">Delivery Details</p>
				<p class="text-lg font-semibold text-gray-900" id="deliveryDetailsBatchLabel">Batch #0</p>
			</div>
		</div>
		<div class="px-6 py-4 space-y-4">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
					<p class="text-xs uppercase tracking-wide text-blue-700">Supplier</p>
					<p class="font-semibold text-blue-900 mt-1" id="deliveryDetailsSupplier">—</p>
				</div>
				<div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
					<p class="text-xs uppercase tracking-wide text-emerald-700">Purchaser</p>
					<p class="font-semibold text-emerald-900 mt-1" id="deliveryDetailsPurchaser">—</p>
				</div>
				<div class="rounded-xl bg-purple-50 border border-purple-100 p-4">
					<p class="text-xs uppercase tracking-wide text-purple-700">Purchased Date</p>
					<p class="font-semibold text-purple-900 mt-1" id="deliveryDetailsDate">—</p>
				</div>
			</div>
			
			<div class="rounded-xl border border-gray-200 overflow-hidden">
				<div class="bg-gray-50 px-4 py-3 border-b">
					<h3 class="text-sm font-semibold text-gray-900">Item Details</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th class="text-left px-4 py-3 font-medium text-gray-700">Item Name</th>
								<th class="text-left px-4 py-3 font-medium text-gray-700">Ordered Qty</th>
								<th class="text-left px-4 py-3 font-medium text-gray-700">Received Qty</th>
								<th class="text-left px-4 py-3 font-medium text-gray-700">Remaining Qty</th>
							</tr>
						</thead>
						<tbody id="deliveryDetailsItemsBody" class="divide-y divide-gray-200">
							<tr>
								<td colspan="4" class="px-4 py-6 text-center text-gray-500">Loading...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- Complete Delivery Section (only for Partial deliveries) -->
			<div id="continueDeliverySection" class="hidden space-y-4">
				<div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
					<h3 class="text-sm font-semibold text-orange-900 mb-2">Complete Delivery</h3>
					<p class="text-xs text-orange-700">Complete the remaining items for this partial delivery.</p>
				</div>
				
				<!-- Purchase Items List (only partial items have Add button) -->
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
										<th class="text-left px-3 py-2 font-medium text-gray-700">Purchase Qty</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Remaining Qty</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Unit</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700 w-24">Action</th>
									</tr>
								</thead>
								<tbody id="continueDeliveryPurchaseItemsList" class="divide-y divide-gray-200">
									<!-- Purchase items will be displayed here -->
								</tbody>
							</table>
						</div>
						<div id="continueDeliveryPurchaseItemsEmpty" class="text-sm text-gray-500 text-center py-4">No items available.</div>
					</div>
				</div>
				
				<!-- Receive Item Section (shown when Add button is clicked) -->
				<div id="continueReceiveItemSection" class="hidden rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="bg-gray-50 px-4 py-2 rounded-lg">
						<h3 class="text-sm font-semibold text-gray-900">Receive Item</h3>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Item</label>
							<input type="text" id="continueReceiveItemName" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Item name" readonly>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Remaining Qty</label>
							<input type="text" id="continueReceivePurchaseQty" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="0.00" readonly>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Receive Qty</label>
							<input type="number" step="0.01" min="0" id="continueReceiveQuantity" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<input type="text" id="continueReceiveUnit" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Unit" readonly>
						</div>
					</div>
					<div class="flex items-center gap-2">
						<button type="button" id="continueReceiveUndoBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500">
							<i data-lucide="x" class="w-4 h-4"></i>
							Cancel
						</button>
					</div>
				</div>
				
				<!-- Final Inventory Decision Section -->
				<div id="continueFinalInventorySection" class="hidden rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="bg-gray-50 px-4 py-2 rounded-lg">
						<h3 class="text-sm font-semibold text-gray-900">Final Inventory Decision</h3>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1 relative">
							<label class="text-sm font-medium text-gray-700">Select Item</label>
							<div class="relative">
								<input type="text" id="continueDeliveryItemSearch" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Search ingredient..." autocomplete="off">
								<i data-lucide="search" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
								<div id="continueDeliveryItemDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
									<!-- Filtered options will be populated here -->
								</div>
							</div>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Quantity</label>
							<input type="number" step="0.01" min="0" id="continueDeliveryQuantityInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<input type="text" id="continueDeliveryUnitInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Unit">
							<select id="continueDeliveryUnitSelect" class="hidden w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
								<option value="">Select unit</option>
							</select>
							<p id="continueDeliveryUnitHelp" class="hidden text-xs text-gray-500 mt-1"></p>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Supplier</label>
							<input type="text" id="continueDeliverySupplierInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700" placeholder="Supplier" readonly>
						</div>
					</div>
					<div class="flex items-end">
						<button type="button" id="continueDeliveryAddItemBtn" class="w-full inline-flex items-center justify-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
							<i data-lucide="plus" class="w-4 h-4"></i>
							Add to Delivery
						</button>
					</div>
				</div>
				
				<!-- Delivery Items List -->
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<div class="bg-gray-50 px-4 py-3 border-b">
						<h3 class="text-sm font-semibold text-gray-900">Delivery Items</h3>
					</div>
					<div class="p-4">
						<div class="overflow-x-auto">
							<table class="w-full text-sm">
								<thead class="bg-gray-50">
									<tr>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Item</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Quantity</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Unit</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Supplier</th>
										<th class="text-left px-3 py-2 font-medium text-gray-700">Actions</th>
									</tr>
								</thead>
								<tbody id="continueDeliveryItemsBody" class="divide-y divide-gray-200">
									<!-- Delivery items will be displayed here -->
								</tbody>
							</table>
						</div>
						<div id="continueDeliveryItemsEmpty" class="text-sm text-gray-500 text-center py-4">No items added yet. Select items from the purchase list above.</div>
					</div>
				</div>
				
				<!-- Record Delivery Button -->
				<div class="flex justify-end mt-4">
					<button type="button" id="continueRecordDeliveryBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
						<i data-lucide="truck" class="w-4 h-4"></i>
						Record Delivery
					</button>
				</div>
			</div>
			
			<div class="flex justify-between items-center">
				<button type="button" id="continueDeliveryBtn" class="hidden inline-flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
					<i data-lucide="truck" class="w-4 h-4"></i>
					Complete Delivery
				</button>
				<button type="button" class="deliveryDetailsModalClose inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

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
  const searchInput = document.getElementById('batchSearchInput');
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
  const recentDeliveriesBody = document.getElementById('recentDeliveriesBody');
  const recentDeliveriesEmpty = document.getElementById('deliveryFilterEmpty');
  const deliveryInlineError = document.getElementById('deliveryInlineError');
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

  function render(groupId){
    if (!tableBody) {
      return;
    }
    tableBody.innerHTML='';
    if (batchHighlight){
      batchHighlight.classList.add('hidden');
      if (batchMeta) batchMeta.textContent = 'After selecting a batch, set per-item received quantities below.';
    }
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
      if (batchHighlight) {
        batchHighlight.classList.remove('hidden');
        if (batchMeta) batchMeta.textContent = 'No items available for delivery in this batch.';
      }
      return; 
    }
    if (batchHighlight && g){
      if (batchMetaSupplier) batchMetaSupplier.textContent = `Supplier: ${g.supplier ?? ''}`;
      if (batchMetaPurchaser) batchMetaPurchaser.textContent = `Purchaser: ${g.purchaser_name ?? ''}`;
      if (batchMetaDate) batchMetaDate.textContent = `Ordered: ${g.date_purchased ?? ''}`;
      if (batchMeta) batchMeta.textContent = 'Review batch details below and record received quantities.';
      batchHighlight.classList.remove('hidden');
      if (typeof lucide !== 'undefined') {
        lucide.createIcons({ elements: batchHighlight.querySelectorAll('i[data-lucide]') });
      }
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
    updateBatchSelectFilter();
  }

  function getRecordedPurchaseIds(){
    const recordedIds = new Set();
    
    // Get purchase IDs from main form (deliveriesItemsJson)
    try {
      const mainFormItems = JSON.parse(itemsJson?.value || '[]');
      if (Array.isArray(mainFormItems)) {
        mainFormItems.forEach(item => {
          if (item.purchase_id) {
            recordedIds.add(parseInt(item.purchase_id, 10));
          }
        });
      }
    } catch (e) {
      console.error('Error parsing deliveriesItemsJson:', e);
    }
    
    // Get purchase IDs from modal form (deliveryItems array)
    if (Array.isArray(deliveryItems)) {
      deliveryItems.forEach(item => {
        if (item.purchaseId) {
          recordedIds.add(parseInt(item.purchaseId, 10));
        }
      });
    }
    
    // Also check deliveryModalItemsJson if it exists
    try {
      const modalFormItems = JSON.parse(deliveryModalItemsJson?.value || '[]');
      if (Array.isArray(modalFormItems)) {
        modalFormItems.forEach(item => {
          if (item.purchase_id) {
            recordedIds.add(parseInt(item.purchase_id, 10));
          }
        });
      }
    } catch (e) {
      // Ignore errors for modal form
    }
    
    return Array.from(recordedIds);
  }

  function updateBatchSelectFilter(){
    if (!sel) return;
    
    const recordedPurchaseIds = getRecordedPurchaseIds();
    if (recordedPurchaseIds.length === 0) {
      // No items recorded, show all batches
      Array.from(sel.options).forEach(opt => {
        if (opt.value !== '') {
          opt.style.display = '';
          opt.disabled = false;
        }
      });
      return;
    }
    
    // Find which group IDs contain the recorded purchase IDs
    const recordedGroupIds = new Set();
    GROUPS.forEach(group => {
      if (group.items && Array.isArray(group.items)) {
        const hasRecordedPurchase = group.items.some(item => 
          recordedPurchaseIds.includes(item.purchase_id)
        );
        if (hasRecordedPurchase) {
          recordedGroupIds.add(group.group_id);
        }
      }
    });
    
    // Hide/disable options for recorded batches
    Array.from(sel.options).forEach(opt => {
      if (opt.value === '') {
        // Keep the placeholder option visible
        return;
      }
      
      if (recordedGroupIds.has(opt.value)) {
        opt.style.display = 'none';
        opt.disabled = true;
        
        // If the currently selected batch is now recorded, reset selection
        if (sel.value === opt.value) {
          sel.value = '';
          if (box) box.classList.add('hidden');
          if (itemsJson) itemsJson.value = '[]';
        }
      } else {
        opt.style.display = '';
        opt.disabled = false;
      }
    });
  }

  function showInlineError(message){
    const alertBox = document.getElementById('deliveryInlineError');
    if (alertBox){
      alertBox.textContent = message;
      alertBox.classList.remove('hidden');
    } else {
      alert(message);
    }
  }

  function clearInlineError(){
    const alertBox = document.getElementById('deliveryInlineError');
    if (alertBox){
      alertBox.textContent = '';
      alertBox.classList.add('hidden');
    }
  }

  function collectInlinePayload(){
    if (!itemsJson) return [];
    try {
      const parsed = JSON.parse(itemsJson.value || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function buildRecentEntriesFromInline(){
    if (!sel || !sel.value) return [];
    const groupId = sel.value;
    const g = GROUPS.find(x=>x.group_id === groupId);
    if (!g || !tableBody) return [];
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const entries = [];
    rows.forEach(row=>{
      const pidInput = row.querySelector('input[name="purchase_id[]"]');
      const qtyInput = row.querySelector('input[name="row_qty[]"]');
      const unitSelect = row.querySelector('select[name="row_unit[]"]');
      if (!pidInput || !qtyInput || !unitSelect) return;
      const purchaseId = parseInt(pidInput.value || '0', 10);
      const qty = parseFloat(qtyInput.value || '0');
      if (!purchaseId || qty <= 0) return;
      const unit = unitSelect.value || unitSelect.options[unitSelect.selectedIndex]?.value || '';
      const item = (g.items || []).find(it => it.purchase_id === purchaseId);
      const name = item?.item_name || 'Item';
      const remainingDisplay = item?.remaining_display ?? item?.quantity ?? 0;
      const status = qty >= (remainingDisplay - 0.0001) ? 'complete' : 'partial';
      entries.push({
        batchId: groupId,
        itemName: name,
        quantity: qty,
        unit: unit || item?.purchase_unit || item?.unit || '',
        status,
        date: g.date_purchased || new Date().toISOString()
      });
    });
    return entries;
  }

  function buildRecentEntriesFromModal(){
    if (!currentDeliveryGroup || !Array.isArray(deliveryItems) || !deliveryItems.length) return [];
    const now = new Date().toISOString();
    return deliveryItems.map((di, index)=>{
      // Use the status that was already calculated when adding the item
      const status = di.status || 'partial';
      return {
        batchId: currentDeliveryGroup.group_id,
        deliveryId: '—', // Will be assigned by backend
        purchaseId: di.purchaseId || 0,
        itemName: di.itemName || 'Item',
        quantity: di.receiveQuantity || di.quantity || 0, // Use receive quantity for display
        unit: di.receiveUnit || di.unit || '',
        status,
        date: now // Same date for all items in same modal submission
      };
    });
  }

  function removeSelectedBatchOption(){
    if (!sel) return;
    const currentValue = sel.value;
    if (!currentValue) return;
    const opt = sel.querySelector(`option[value="${currentValue}"]`);
    if (opt){
      opt.remove();
    }
    sel.value = '';
    if (box) box.classList.add('hidden');
    if (itemsJson) itemsJson.value = '[]';
  }

  function addRecentDeliveryRows(entries){
    if (!recentDeliveriesBody || !Array.isArray(entries) || entries.length === 0) return;
    
    // Group entries by batch_id and date (same delivery modal submission)
    const batchGroups = {};
    entries.forEach(entry => {
      const batchKey = `${entry.batchId || ''}|${entry.date || ''}`;
      if (!batchGroups[batchKey]) {
        batchGroups[batchKey] = {
          batchId: entry.batchId || '',
          date: entry.date || new Date().toLocaleString(),
          status: entry.status || 'partial',
          itemsCount: 0,
          firstDeliveryId: entry.deliveryId || '—',
          purchaseId: entry.purchaseId || 0
        };
      }
      batchGroups[batchKey].itemsCount++;
      // If any item is partial, batch is partial
      if (entry.status === 'partial') {
        batchGroups[batchKey].status = 'partial';
      }
    });
    
    // Create or update rows for each batch
    Object.values(batchGroups).forEach(batch => {
      // Check if a row with this batch_id already exists
      const existingRow = recentDeliveriesBody.querySelector(`tr[data-batch-id="${batch.batchId}"]`);
      
      if (existingRow) {
        // Update existing row instead of creating duplicate
        existingRow.setAttribute('data-delivery-status', batch.status);
        existingRow.setAttribute('data-purchase-id', batch.purchaseId.toString());
        
        const statusClass = (batch.status === 'complete') ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
        const statusIcon = (batch.status === 'complete') ? 'check-circle' : 'clock';
        const statusText = (batch.status === 'complete') ? 'Complete' : 'Partial';
        const batchLabel = batch.batchId ? `#${batch.batchId}` : 'Batch';
        const itemsText = `${batch.itemsCount} item${batch.itemsCount !== 1 ? 's' : ''}`;
        
        // Update the row content
        existingRow.innerHTML = `
          <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-semibold text-orange-600">${batch.firstDeliveryId}</span>
              </div>
            </div>
          </td>
          <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-semibold text-purple-600">${batchLabel}</span>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2">
              <span class="font-medium text-gray-900">${itemsText}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border ${statusClass}">
              <i data-lucide="${statusIcon}" class="w-3 h-3"></i>
              ${statusText}
            </span>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2">
              <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
              <span class="text-gray-600">${batch.date}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <button type="button" class="viewBatchDetailsBtn inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors" data-batch-id="${batch.batchId}" data-purchase-id="${batch.purchaseId}">
              <i data-lucide="eye" class="w-3 h-3"></i>
              View Details
            </button>
          </td>
        `;
        
        // Re-initialize icons for the updated row
        if (typeof lucide !== 'undefined') {
          lucide.createIcons({ elements: existingRow.querySelectorAll('i[data-lucide]') });
        }
      } else {
        // Create new row if it doesn't exist
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors';
        tr.setAttribute('data-delivery-status', batch.status);
        tr.setAttribute('data-batch-id', batch.batchId);
        tr.setAttribute('data-purchase-id', batch.purchaseId.toString());
        const statusClass = (batch.status === 'complete') ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
        const statusIcon = (batch.status === 'complete') ? 'check-circle' : 'clock';
        const statusText = (batch.status === 'complete') ? 'Complete' : 'Partial';
        const batchLabel = batch.batchId ? `#${batch.batchId}` : 'Batch';
        const itemsText = `${batch.itemsCount} item${batch.itemsCount !== 1 ? 's' : ''}`;
        tr.innerHTML = `
          <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-semibold text-orange-600">${batch.firstDeliveryId}</span>
              </div>
            </div>
          </td>
          <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-semibold text-purple-600">${batchLabel}</span>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2">
              <span class="font-medium text-gray-900">${itemsText}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border ${statusClass}">
              <i data-lucide="${statusIcon}" class="w-3 h-3"></i>
              ${statusText}
            </span>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2">
              <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
              <span class="text-gray-600">${batch.date}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <button type="button" class="viewBatchDetailsBtn inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors" data-batch-id="${batch.batchId}" data-purchase-id="${batch.purchaseId}">
              <i data-lucide="eye" class="w-3 h-3"></i>
              View Details
            </button>
          </td>
        `;
        recentDeliveriesBody.prepend(tr);
      }
    });
    if (recentDeliveriesEmpty){
      recentDeliveriesEmpty.classList.add('hidden');
    }
    if (typeof lucide !== 'undefined') {
      lucide.createIcons({ elements: recentDeliveriesBody.querySelectorAll('i[data-lucide]') });
    }
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
    updateBatchSelectFilter();
    closeQuickModal();
    deliveriesForm.submit();
  });

  // Initialize delivery modal
  const deliveryModal = document.getElementById('deliveryModal');
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
  const receiveItemSection = document.getElementById('receiveItemSection');
  const finalInventorySection = document.getElementById('finalInventorySection');
  const receiveItemName = document.getElementById('receiveItemName');
  const receivePurchaseQty = document.getElementById('receivePurchaseQty');
  const receiveQuantity = document.getElementById('receiveQuantity');
  const receiveUnit = document.getElementById('receiveUnit');
  const receiveUndoBtn = document.getElementById('receiveUndoBtn');
  let deliveryItems = [];
  let currentDeliveryGroup = null;
  let lastSelectedUnit = ''; // Track last selected unit for conversion calculations
  let deliveryIngredientOptions = []; // Store all ingredient options for search
  let currentPurchaseItemData = null; // Store current purchase item for receive section

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

  function openDeliveryModal(groupId){
    if (!deliveryModal) {
      alert('Error: Delivery modal not found. Please refresh the page.');
      return;
    }
    
    // Clear any previous errors
    clearDeliveryError();
    
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
        
        g.items.forEach((item, itemIndex) => {
          const orderedQty = parseFloat(item.purchase_quantity || item.quantity || 0);
          const remainingBase = Math.max(0, (item.quantity - (item.delivered || 0)));
          
          // Skip fully delivered items
          if (remainingBase <= 0.0001) return;
          
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
          
          const remainingDisplay = item.remaining_display !== undefined ? item.remaining_display : remainingBase;
          
          const tr = document.createElement('tr');
          tr.className = 'hover:bg-gray-50';
          tr.dataset.purchaseItemIndex = itemIndex;
          tr.dataset.purchaseId = item.purchase_id || '';
          tr.dataset.itemName = itemName;
          tr.dataset.quantity = remainingDisplay;
          tr.dataset.quantityBase = remainingBase;
          tr.dataset.unit = displayUnit;
          tr.dataset.baseUnit = item.unit || '';
          tr.dataset.itemId = item.item_id || '';
          tr.dataset.orderedQty = orderedQty;
          tr.dataset.removed = 'false';
          tr.innerHTML = `
            <td class="px-3 py-2 font-medium text-gray-900">${escapeHtml(itemName)}</td>
            <td class="px-3 py-2 text-gray-700">${orderedQty.toFixed(2)}</td>
            <td class="px-3 py-2 text-gray-700">${escapeHtml(displayUnit)}</td>
            <td class="px-3 py-2">
              <button type="button" class="addPurchaseItemBtn inline-flex items-center gap-1 px-2 py-1 text-xs bg-orange-600 text-white rounded hover:bg-orange-700 focus:ring-2 focus:ring-orange-500" data-item-index="${itemIndex}">
                <i data-lucide="plus" class="w-3 h-3"></i>Add
              </button>
            </td>
          `;
          purchaseItemsList.appendChild(tr);
        });
      } else {
        purchaseItemsEmpty.classList.remove('hidden');
      }
    }
    
    // Populate item select dropdown with ALL ingredients from inventory
    if (!deliveryItemSelect || !deliveryItemSearch) {
      alert('Error: Delivery form elements not found. Please refresh the page.');
      return;
    }
    deliveryItemSelect.innerHTML = '<option value="">Choose ingredient</option>';
    deliveryIngredientOptions = [];
    
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
    
    // Add ALL ingredients from inventory (not just those matching purchase items)
    if (typeof INGREDIENTS !== 'undefined' && Array.isArray(INGREDIENTS)) {
      INGREDIENTS.forEach(ing => {
      const opt = document.createElement('option');
      opt.value = ing.id; // Use ingredient ID as value
      
      // Display ingredient name with its unit
      const unitLabel = ing.display_unit || ing.unit || 'unit';
      opt.textContent = `${ing.name} (${unitLabel})`;
      
      opt.dataset.ingredientId = ing.id;
      opt.dataset.itemName = ing.name;
      opt.dataset.baseUnit = ing.unit || '';
      opt.dataset.displayUnit = ing.display_unit || '';
      opt.dataset.displayFactor = ing.display_factor || 1;
      opt.dataset.supplier = g.supplier || '';
      
      // Store matching purchase items for this ingredient (if any)
      const matchingPurchases = purchaseItemsByIngredient[ing.id] || [];
      if (matchingPurchases.length > 0) {
        // Use the first matching purchase item
        const purchaseItem = matchingPurchases[0];
        opt.dataset.purchaseId = purchaseItem.purchaseId;
        opt.dataset.purchaseIndex = purchaseItem.index;
        opt.dataset.quantity = purchaseItem.quantity;
        opt.dataset.quantityBase = purchaseItem.quantityBase;
      } else {
        // No matching purchase, but still allow selection
        opt.dataset.purchaseId = '';
        opt.dataset.purchaseIndex = '';
      }
      
      deliveryItemSelect.appendChild(opt);
      
      // Also add to searchable options array
      deliveryIngredientOptions.push({
        id: ing.id,
        name: ing.name,
        baseUnit: ing.unit || '',
        displayUnit: ing.display_unit || '',
        displayFactor: ing.display_factor || 1,
        displayText: `${ing.name} (${unitLabel})`,
        supplier: g.supplier || '',
        purchaseId: opt.dataset.purchaseId || '',
        purchaseIndex: opt.dataset.purchaseIndex || '',
        quantity: opt.dataset.quantity || undefined,
        quantityBase: opt.dataset.quantityBase || undefined
      });
      });
    }
    
    // Reset search input
    if (deliveryItemSearch) {
      deliveryItemSearch.value = '';
    }
    if (deliveryItemDropdown) {
      deliveryItemDropdown.classList.add('hidden');
    }
    
    renderDeliveryItems();
    
    // Show the modal - ensure it's visible
    deliveryModal.classList.remove('hidden');
    deliveryModal.classList.add('flex');
    deliveryModal.style.display = 'flex'; // Force display
    deliveryModal.style.zIndex = '50'; // Ensure z-index
    document.body.classList.add('overflow-hidden');
    
    // Initialize lucide icons
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
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
    currentPurchaseItemData = null;
    renderDeliveryItems();
    
    // Clear the modal items JSON to prevent batch from being hidden
    if (deliveryModalItemsJson) {
      deliveryModalItemsJson.value = '[]';
    }
    
    // Hide receive sections
    if (receiveItemSection) {
      receiveItemSection.classList.add('hidden');
    }
    if (finalInventorySection) {
      finalInventorySection.classList.add('hidden');
    }
    
    // Clear receive item fields
    if (receiveItemName) receiveItemName.value = '';
    if (receivePurchaseQty) receivePurchaseQty.value = '';
    if (receiveQuantity) receiveQuantity.value = '';
    if (receiveUnit) receiveUnit.value = '';
    
    deliveryItemSelect.value = '';
    if (deliveryItemSearch) {
      deliveryItemSearch.value = '';
    }
    if (deliveryItemDropdown) {
      deliveryItemDropdown.classList.add('hidden');
    }
    deliveryQuantityInput.value = '';
    deliveryUnitInput.value = '';
    if (deliveryUnitSelect) {
      deliveryUnitSelect.value = '';
      deliveryUnitSelect.classList.add('hidden');
    }
    deliveryUnitInput.classList.remove('hidden');
    deliverySupplierInput.value = '';
    
    // Restore all purchase item rows
    const purchaseRows = purchaseItemsList?.querySelectorAll('tr[data-removed="true"]');
    purchaseRows?.forEach(row => {
      row.style.display = '';
      row.dataset.removed = 'false';
    });
    
    deliveryQuantityInput.readOnly = true;
    deliveryUnitInput.readOnly = true;
    if (deliveryUnitSelect) deliveryUnitSelect.disabled = true;
    deliverySupplierInput.readOnly = true;
    if (deliveryBuilderError) deliveryBuilderError.classList.add('hidden');
    updateBatchSelectFilter();
  }

  function showDeliveryError(message){
    console.log('showDeliveryError called with message:', message);
    // Re-fetch element in case DOM changed
    const errorElement = document.getElementById('deliveryBuilderError');
    if (!errorElement) {
      console.error('deliveryBuilderError element not found');
      alert(message); // Fallback to alert if element not found
      return;
    }
    
    console.log('Error element found:', errorElement);
    
    // Find or create the span element for the error message
    let errorText = errorElement.querySelector('span.flex-1');
    if (!errorText) {
      console.log('Creating new span element for error message');
      // If span doesn't exist, create it
      errorText = document.createElement('span');
      errorText.className = 'flex-1 font-medium';
      // Insert after icon if it exists, otherwise append
      const icon = errorElement.querySelector('i[data-lucide]');
      // Simply append the error text - it will appear after the icon
      errorElement.appendChild(errorText);
    }
    
    // Set the error message
    errorText.textContent = message;
    console.log('Error message set:', errorText.textContent);
    
    // Show the error element
    errorElement.classList.remove('hidden');
    errorElement.style.display = 'flex'; // Ensure flex display
    errorElement.style.visibility = 'visible'; // Ensure visibility
    errorElement.style.opacity = '1'; // Ensure opacity
    
    // Check parent visibility
    let parent = errorElement.parentElement;
    while (parent && parent !== document.body) {
      if (parent.classList.contains('hidden') || parent.style.display === 'none') {
        console.warn('Parent element is hidden:', parent);
        parent.classList.remove('hidden');
        parent.style.display = '';
      }
      parent = parent.parentElement;
    }
    
    console.log('Error element classes:', errorElement.className);
    console.log('Error element display:', errorElement.style.display);
    console.log('Error element is visible:', errorElement.offsetParent !== null);
    
    // Scroll error into view
    if (errorElement) {
      errorElement.scrollIntoView({ behavior: 'auto', block: 'nearest' });
    }
    
    // Re-initialize icons if lucide is available
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }

  function clearDeliveryError(){
    if (!deliveryBuilderError) return;
    const errorText = deliveryBuilderError.querySelector('span');
    if (errorText) {
      errorText.textContent = '';
    }
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
      updateBatchSelectFilter();
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
      supplier: item.supplier,
      receive_quantity: item.receiveQuantity, // Actual received quantity
      receive_unit: item.receiveUnit, // Unit of received quantity
      status: item.status || 'partial' // Status: 'partial' or 'complete'
    }));
    if (deliveryModalItemsJson) deliveryModalItemsJson.value = JSON.stringify(jsonData);
    updateBatchSelectFilter();
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
        if (deliveryItemSearch) {
          deliveryItemSearch.value = '';
        }
        if (deliveryItemDropdown) {
          deliveryItemDropdown.classList.add('hidden');
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
      
      // Check if receive item section is active (purchase item was selected)
      if (!currentPurchaseItemData) {
        showDeliveryError('Please click "Add" on a purchase item first.');
        return;
      }
      
      // Validate receive quantity
      const receiveQty = parseFloat(receiveQuantity?.value || '0');
      if (!receiveQty || receiveQty <= 0) {
        showDeliveryError('Please enter a valid received quantity.');
        return;
      }
      if (receiveQty > currentPurchaseItemData.quantity) {
        showDeliveryError(`Received quantity cannot exceed remaining quantity (${currentPurchaseItemData.quantity.toFixed(2)}).`);
        return;
      }
      
      // Check if this ingredient already added (by ingredient ID)
      const ingredientId = parseInt(selectedOpt.value, 10);
      const existing = deliveryItems.find(entry => entry.ingredientId === ingredientId);
      if (existing) {
        showDeliveryError('This ingredient has already been added. Remove it first to modify.');
        return;
      }
      
      // Use purchase item data
      const purchaseIdInt = parseInt(currentPurchaseItemData.purchaseId || '0', 10);
      if (!purchaseIdInt) {
        showDeliveryError('Invalid purchase item data.');
        return;
      }
      
      // Determine status: "partial" if Receive Qty < Purchase Qty, otherwise "complete"
      const purchaseQty = currentPurchaseItemData.orderedQty || 0;
      const deliveryStatus = (receiveQty < purchaseQty) ? 'partial' : 'complete';
      
      deliveryItems.push({
        purchaseId: purchaseIdInt,
        ingredientId: ingredientId,
        itemName: ingredient.name,
        quantity: quantity, // Inventory quantity
        unit: unit, // Inventory unit
        supplier: supplier || currentDeliveryGroup?.supplier || '',
        // Store receive item data for reference
        receiveQuantity: receiveQty,
        receiveUnit: (receiveUnit?.value || '').trim() || currentPurchaseItemData.unit,
        purchaseItemName: currentPurchaseItemData.itemName,
        purchaseItemId: currentPurchaseItemData.itemId,
        status: deliveryStatus // Auto-set status based on receive qty vs purchase qty
      });
      
      // Clear and hide receive section
      if (receiveItemSection) {
        receiveItemSection.classList.add('hidden');
      }
      if (finalInventorySection) {
        finalInventorySection.classList.add('hidden');
      }
      
      // Clear receive item fields
      if (receiveItemName) receiveItemName.value = '';
      if (receivePurchaseQty) receivePurchaseQty.value = '';
      if (receiveQuantity) receiveQuantity.value = '';
      if (receiveUnit) receiveUnit.value = '';
      
      // Clear inventory inputs
      deliveryItemSelect.value = '';
      if (deliveryItemSearch) {
        deliveryItemSearch.value = '';
      }
      if (deliveryItemDropdown) {
        deliveryItemDropdown.classList.add('hidden');
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
      lastSelectedUnit = '';
      
      // Clear stored purchase item data
      currentPurchaseItemData = null;
      
      renderDeliveryItems();
      clearDeliveryError();
      updateBatchSelectFilter();
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Handle Add button on purchase items
  const purchaseItemsList = document.getElementById('deliveryPurchaseItemsList');
  if (purchaseItemsList) {
    purchaseItemsList.addEventListener('click', (e) => {
      const btn = e.target.closest('.addPurchaseItemBtn');
      if (!btn) return;
      
      // Check if another item is already selected
      if (currentPurchaseItemData) {
        showDeliveryError('Cannot add multiple items. Please complete or cancel the current item first.');
        return;
      }
      
      const itemIndex = parseInt(btn.dataset.itemIndex || '-1', 10);
      const row = btn.closest('tr');
      if (!row || itemIndex < 0 || row.dataset.removed === 'true') return;
      
      // Get purchase item data
      const purchaseItem = {
        purchaseId: row.dataset.purchaseId || '',
        itemId: row.dataset.itemId || '',
        itemName: row.dataset.itemName || '',
        orderedQty: parseFloat(row.dataset.orderedQty || '0'),
        quantity: parseFloat(row.dataset.quantity || '0'),
        quantityBase: parseFloat(row.dataset.quantityBase || '0'),
        unit: row.dataset.unit || '',
        baseUnit: row.dataset.baseUnit || '',
        itemIndex: itemIndex,
        row: row
      };
      
      // Store for later use
      currentPurchaseItemData = purchaseItem;
      
      // Show receive item section
      if (receiveItemSection) {
        receiveItemSection.classList.remove('hidden');
      }
      if (finalInventorySection) {
        finalInventorySection.classList.remove('hidden');
      }
      
      // Auto-fill receive item fields
      if (receiveItemName) {
        receiveItemName.value = purchaseItem.itemName;
      }
      if (receivePurchaseQty) {
        receivePurchaseQty.value = purchaseItem.orderedQty.toFixed(2);
      }
      if (receiveQuantity) {
        receiveQuantity.value = '';
        receiveQuantity.max = purchaseItem.quantity;
      }
      if (receiveUnit) {
        receiveUnit.value = purchaseItem.unit;
      }
      
      // Enable inventory fields
      if (deliveryItemSearch) deliveryItemSearch.readOnly = false;
      if (deliveryQuantityInput) deliveryQuantityInput.readOnly = false;
      if (deliveryUnitInput) deliveryUnitInput.readOnly = false;
      if (deliveryUnitSelect) deliveryUnitSelect.disabled = false;
      
      // Auto-fill supplier from current delivery group
      if (deliverySupplierInput && currentDeliveryGroup?.supplier) {
        deliverySupplierInput.value = currentDeliveryGroup.supplier;
      }
      
      // Hide the purchase item row
      row.style.display = 'none';
      row.dataset.removed = 'true';
      
      // Focus on receive quantity
      if (receiveQuantity) {
        receiveQuantity.focus();
      }
      
      clearDeliveryError();
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Handle Cancel/Undo button in receive section
  if (receiveUndoBtn) {
    receiveUndoBtn.addEventListener('click', () => {
      // Restore purchase item row
      if (currentPurchaseItemData && currentPurchaseItemData.row) {
        currentPurchaseItemData.row.style.display = '';
        currentPurchaseItemData.row.dataset.removed = 'false';
      }
      
      // Hide sections
      if (receiveItemSection) {
        receiveItemSection.classList.add('hidden');
      }
      if (finalInventorySection) {
        finalInventorySection.classList.add('hidden');
      }
      
      // Clear receive item fields
      if (receiveItemName) receiveItemName.value = '';
      if (receivePurchaseQty) receivePurchaseQty.value = '';
      if (receiveQuantity) receiveQuantity.value = '';
      if (receiveUnit) receiveUnit.value = '';
      
      // Clear inventory fields
      if (deliveryItemSearch) deliveryItemSearch.value = '';
      if (deliveryQuantityInput) deliveryQuantityInput.value = '';
      if (deliveryUnitInput) deliveryUnitInput.value = '';
      if (deliverySupplierInput) deliverySupplierInput.value = '';
      
      // Clear stored data
      currentPurchaseItemData = null;
    });
  }
  
  // Handle remove item
  if (deliveryItemsBody) {
    deliveryItemsBody.addEventListener('click', (e)=>{
      const btn = e.target.closest('.removeDeliveryItem');
      if (!btn) return;
      const index = parseInt(btn.dataset.index || '-1', 10);
      if (index >= 0 && index < deliveryItems.length) {
        const removedItem = deliveryItems[index];
        
        // Restore purchase item row if it was removed
        // Only restore ONE row that matches the removed item
        if (removedItem.purchaseItemId && purchaseItemsList) {
          const purchaseRows = purchaseItemsList.querySelectorAll('tr');
          const removedItemId = removedItem.purchaseItemId?.toString() || '';
          let restored = false;
          
          // First try to match by itemId (most specific)
          for (const row of purchaseRows) {
            if (restored) break; // Only restore one row
            if (row.dataset.removed !== 'true') continue;
            
            const rowItemId = row.dataset.itemId || '';
            if (rowItemId && removedItemId && rowItemId === removedItemId) {
              row.style.display = '';
              row.dataset.removed = 'false';
              restored = true;
              break;
            }
          }
          
          // If not found by itemId, try by name (fallback, but still only restore one)
          if (!restored && removedItem.purchaseItemName) {
            for (const row of purchaseRows) {
              if (restored) break; // Only restore one row
              if (row.dataset.removed !== 'true') continue;
              
              if (row.dataset.itemName === removedItem.purchaseItemName) {
                row.style.display = '';
                row.dataset.removed = 'false';
                restored = true;
                break;
              }
            }
          }
        }
        
        deliveryItems.splice(index, 1);
        renderDeliveryItems();
        updateBatchSelectFilter();
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      }
    });
  }

  // Handle modal close - get all close buttons (Cancel button)
  const deliveryModalCloseButtons = deliveryModal?.querySelectorAll('.deliveryModalClose');
  if (deliveryModalCloseButtons && deliveryModalCloseButtons.length > 0) {
    deliveryModalCloseButtons.forEach(btn => {
      btn.addEventListener('click', closeDeliveryModal);
    });
  }
  // Also close when clicking outside the modal
  deliveryModal?.addEventListener('click', (e)=>{
    if (e.target === deliveryModal) closeDeliveryModal();
  });

  // Handle modal form submission
  if (deliveryModalForm) {
    deliveryModalForm.addEventListener('submit', (e)=>{
      if (deliveryItems.length === 0) {
        e.preventDefault();
        showDeliveryError('Please add at least one item to record delivery.');
        // Ensure button is enabled so user can fix the issue
        if (deliverySubmitBtn) {
          deliverySubmitBtn.removeAttribute('disabled');
          deliverySubmitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
        return;
      }
      
      // Check if all purchase items have been added
      const purchaseItemsList = document.getElementById('deliveryPurchaseItemsList');
      if (purchaseItemsList) {
        const allPurchaseRows = purchaseItemsList.querySelectorAll('tr');
        const unprocessedItems = [];
        allPurchaseRows.forEach(row => {
          // Check if row is visible (not removed/hidden)
          if (row.style.display !== 'none' && row.dataset.removed !== 'true') {
            const itemName = row.dataset.itemName || 'Unknown item';
            unprocessedItems.push(itemName);
          }
        });
        
        if (unprocessedItems.length > 0) {
          e.preventDefault();
          const itemList = unprocessedItems.join(', ');
          showDeliveryError(`Please add all purchase items to the delivery. Remaining items: ${itemList}`);
          // Ensure button is enabled so user can fix the issue
          if (deliverySubmitBtn) {
            deliverySubmitBtn.removeAttribute('disabled');
            deliverySubmitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            // Reset button text if it was changed to "Processing..."
            const btnContent = deliverySubmitBtn.textContent || '';
            if (btnContent.includes('Processing')) {
              const icon = deliverySubmitBtn.querySelector('i[data-lucide]');
              if (icon) {
                deliverySubmitBtn.innerHTML = '<i data-lucide="package-check" class="w-4 h-4"></i>Record Delivery';
                if (typeof lucide !== 'undefined') {
                  lucide.createIcons();
                }
              }
            }
          }
          return;
        }
      }
      
      // Only disable button if validation passes
      deliverySubmitBtn?.setAttribute('disabled','disabled');
      deliverySubmitBtn?.classList.add('opacity-60','cursor-not-allowed');
      addRecentDeliveryRows(buildRecentEntriesFromModal());
      removeSelectedBatchOption();
      // Form will submit normally with items_json
    });
  }

  if (deliveriesForm){
    deliveriesForm.addEventListener('submit', (e)=>{
      clearInlineError();
      sync();
      const payload = collectInlinePayload();
      if (!payload.length){
        e.preventDefault();
        showInlineError('Add at least one outstanding item with a quantity before recording the delivery.');
        return;
      }
      deliverySubmitBtn?.setAttribute('disabled','disabled');
      deliverySubmitBtn?.classList.add('opacity-60','cursor-not-allowed');
      addRecentDeliveryRows(buildRecentEntriesFromInline());
      removeSelectedBatchOption();
    });
  }


  console.log("sel", sel);
  // Update batch selection to open modal instead of rendering table
  if (sel) {
    sel.addEventListener('change', ()=>{ 
      const groupId = sel.value;
      if (groupId && groupId !== '') {
        openDeliveryModal(groupId);
      } else {
        if (box) box.classList.add('hidden');
        if (itemsJson) itemsJson.value='[]';
      }
      updateBatchSelectFilter();
    });
  }
  if (searchInput){
    searchInput.addEventListener('input', ()=>{
      if (!searchInput.value){
        sel.value = '';
        render('');
        return;
      }
      const searchValue = searchInput.value.toLowerCase().trim();
      
      // Try to find by batch ID first (if search starts with # or contains batch ID)
      if (searchValue.startsWith('#')) {
        const batchIdMatch = searchValue.substring(1).trim();
        const match = Array.from(sel.options).find(opt => opt.value.toLowerCase() === batchIdMatch);
        if (match && match.value){
          sel.value = match.value;
          sel.dispatchEvent(new Event('change'));
          searchInput.value = match.textContent;
          return;
        }
      }
      
      // Try exact match by batch ID (without #)
      const directBatchMatch = Array.from(sel.options).find(opt => opt.value.toLowerCase() === searchValue);
      if (directBatchMatch && directBatchMatch.value){
        sel.value = directBatchMatch.value;
        sel.dispatchEvent(new Event('change'));
        searchInput.value = directBatchMatch.textContent;
        return;
      }
      
      // Otherwise, find by text content (case-insensitive partial match)
      const match = Array.from(sel.options).find(opt => {
        if (!opt.value) return false;
        const optText = opt.textContent.toLowerCase();
        return optText.includes(searchValue);
      });
      if (match && match.value){
        sel.value = match.value;
        sel.dispatchEvent(new Event('change'));
        // Update search input to show the full formatted text for better UX
        searchInput.value = match.textContent;
      }
    });
  }

  // Initialize batch select filter on page load
  updateBatchSelectFilter();
  
  // Initialize lucide icons on page load
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // Delivery Details Modal
  const deliveryDetailsModal = document.getElementById('deliveryDetailsModal');
  const deliveryDetailsBatchLabel = document.getElementById('deliveryDetailsBatchLabel');
  const deliveryDetailsSupplier = document.getElementById('deliveryDetailsSupplier');
  const deliveryDetailsPurchaser = document.getElementById('deliveryDetailsPurchaser');
  const deliveryDetailsDate = document.getElementById('deliveryDetailsDate');
  const deliveryDetailsItemsBody = document.getElementById('deliveryDetailsItemsBody');

  function openDeliveryDetailsModal(batchId, purchaseId) {
    if (!deliveryDetailsModal) return;
    
    // Show loading state
    if (deliveryDetailsItemsBody) {
      deliveryDetailsItemsBody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Loading...</td></tr>';
    }
    
    // Show modal
    deliveryDetailsModal.classList.remove('hidden');
    deliveryDetailsModal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    
    // Fetch batch details
    const url = `<?php echo htmlspecialchars($baseUrl); ?>/deliveries/batch-details?batch_id=${encodeURIComponent(batchId)}&purchase_id=${purchaseId}`;
    fetch(url)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          if (deliveryDetailsItemsBody) {
            deliveryDetailsItemsBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-red-500">${escapeHtml(data.error)}</td></tr>`;
          }
          return;
        }
        
        // Populate header info
        if (deliveryDetailsBatchLabel) {
          deliveryDetailsBatchLabel.textContent = `Batch #${data.batch_id}`;
        }
        if (deliveryDetailsSupplier) {
          deliveryDetailsSupplier.textContent = data.supplier || '—';
        }
        if (deliveryDetailsPurchaser) {
          deliveryDetailsPurchaser.textContent = data.purchaser_name || '—';
        }
        if (deliveryDetailsDate) {
          deliveryDetailsDate.textContent = data.date_purchased || '—';
        }
        
        // Check if batch has partial items (status is Partial)
        const hasPartialItems = data.items && data.items.some(item => {
          const orderedQty = Number(item.ordered_qty || 0);
          const receivedQty = Number(item.received_qty || 0);
          return receivedQty < orderedQty - 0.0001;
        });
        
        // Show Complete Delivery button only for partial deliveries
        const continueDeliveryBtn = document.getElementById('continueDeliveryBtn');
        const continueDeliverySection = document.getElementById('continueDeliverySection');
        if (continueDeliveryBtn && continueDeliverySection) {
          if (hasPartialItems) {
            continueDeliveryBtn.classList.remove('hidden');
          } else {
            continueDeliveryBtn.classList.add('hidden');
            continueDeliverySection.classList.add('hidden');
          }
        }
        
        // Store batch data for continue delivery feature
        window.currentBatchData = data;
        
        // Populate items table
        if (deliveryDetailsItemsBody && data.items && data.items.length > 0) {
          deliveryDetailsItemsBody.innerHTML = '';
          data.items.forEach(item => {
            const orderedQty = Number(item.ordered_qty || 0);
            const receivedQty = Number(item.received_qty || 0);
            const isPartial = receivedQty < orderedQty - 0.0001; // Account for floating point precision
            
            // Apply different styling for partial items
            const rowClass = isPartial 
              ? 'bg-yellow-50 hover:bg-yellow-100 border-l-4 border-yellow-400' 
              : 'hover:bg-gray-50';
            const textClass = isPartial ? 'text-yellow-900' : 'text-gray-900';
            const cellTextClass = isPartial ? 'text-yellow-800' : 'text-gray-700';
            
            const tr = document.createElement('tr');
            tr.className = rowClass;
            tr.innerHTML = `
              <td class="px-4 py-3 font-medium ${textClass}">${escapeHtml(item.item_name)}</td>
              <td class="px-4 py-3 ${cellTextClass}">${orderedQty.toFixed(2)} ${escapeHtml(item.unit)}</td>
              <td class="px-4 py-3 ${cellTextClass}">${receivedQty.toFixed(2)} ${escapeHtml(item.unit)}</td>
              <td class="px-4 py-3 ${cellTextClass}">${Number(item.remaining_qty).toFixed(2)} ${escapeHtml(item.unit)}</td>
            `;
            deliveryDetailsItemsBody.appendChild(tr);
          });
        } else if (deliveryDetailsItemsBody) {
          deliveryDetailsItemsBody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No items found</td></tr>';
        }
      })
      .catch(error => {
        console.error('Error fetching batch details:', error);
        if (deliveryDetailsItemsBody) {
          deliveryDetailsItemsBody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-red-500">Error loading batch details</td></tr>';
        }
      });
  }

  function closeDeliveryDetailsModal() {
    if (!deliveryDetailsModal) return;
    deliveryDetailsModal.classList.add('hidden');
    deliveryDetailsModal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
    
    // Clear continue delivery state
    continueDeliveryItems = [];
    continueCurrentPurchaseItemData = null;
    window.currentBatchData = null;
    
    // Hide continue delivery section
    const continueDeliverySection = document.getElementById('continueDeliverySection');
    if (continueDeliverySection) {
      continueDeliverySection.classList.add('hidden');
    }
    
    // Restore all hidden purchase items in continue delivery section
    const continuePurchaseItemsList = document.getElementById('continueDeliveryPurchaseItemsList');
    if (continuePurchaseItemsList) {
      const hiddenRows = continuePurchaseItemsList.querySelectorAll('tr[data-removed="true"]');
      hiddenRows.forEach(row => {
        row.style.display = '';
        row.dataset.removed = 'false';
      });
    }
    
    // Clear continue delivery fields
    const continueReceiveItemSection = document.getElementById('continueReceiveItemSection');
    const continueFinalInventorySection = document.getElementById('continueFinalInventorySection');
    if (continueReceiveItemSection) continueReceiveItemSection.classList.add('hidden');
    if (continueFinalInventorySection) continueFinalInventorySection.classList.add('hidden');
    
    // Clear continue delivery form fields
    const continueReceiveItemName = document.getElementById('continueReceiveItemName');
    const continueReceivePurchaseQty = document.getElementById('continueReceivePurchaseQty');
    const continueReceiveQuantity = document.getElementById('continueReceiveQuantity');
    const continueReceiveUnit = document.getElementById('continueReceiveUnit');
    const continueDeliveryItemSearch = document.getElementById('continueDeliveryItemSearch');
    const continueDeliveryQuantityInput = document.getElementById('continueDeliveryQuantityInput');
    const continueDeliveryUnitInput = document.getElementById('continueDeliveryUnitInput');
    const continueDeliveryUnitSelect = document.getElementById('continueDeliveryUnitSelect');
    const continueDeliverySupplierInput = document.getElementById('continueDeliverySupplierInput');
    
    if (continueReceiveItemName) continueReceiveItemName.value = '';
    if (continueReceivePurchaseQty) continueReceivePurchaseQty.value = '';
    if (continueReceiveQuantity) continueReceiveQuantity.value = '';
    if (continueReceiveUnit) continueReceiveUnit.value = '';
    if (continueDeliveryItemSearch) continueDeliveryItemSearch.value = '';
    if (continueDeliveryQuantityInput) continueDeliveryQuantityInput.value = '';
    if (continueDeliveryUnitInput) {
      continueDeliveryUnitInput.value = '';
      continueDeliveryUnitInput.classList.remove('hidden');
    }
    if (continueDeliveryUnitSelect) {
      continueDeliveryUnitSelect.value = '';
      continueDeliveryUnitSelect.classList.add('hidden');
      continueDeliveryUnitSelect.disabled = true;
    }
    if (continueDeliverySupplierInput) continueDeliverySupplierInput.value = '';
    
    // Clear continue delivery items display
    const continueDeliveryItemsBody = document.getElementById('continueDeliveryItemsBody');
    const continueDeliveryItemsEmpty = document.getElementById('continueDeliveryItemsEmpty');
    if (continueDeliveryItemsBody) continueDeliveryItemsBody.innerHTML = '';
    if (continueDeliveryItemsEmpty) continueDeliveryItemsEmpty.classList.remove('hidden');
  }

  // Handle modal close - get all close buttons
  const closeButtons = deliveryDetailsModal?.querySelectorAll('.deliveryDetailsModalClose');
  if (closeButtons && closeButtons.length > 0) {
    closeButtons.forEach(btn => {
      btn.addEventListener('click', closeDeliveryDetailsModal);
    });
  }
  // Also close when clicking outside the modal
  deliveryDetailsModal?.addEventListener('click', (e) => {
    if (e.target === deliveryDetailsModal) closeDeliveryDetailsModal();
  });

  // Handle View Details button clicks
  if (recentDeliveriesBody) {
    recentDeliveriesBody.addEventListener('click', (e) => {
      const btn = e.target.closest('.viewBatchDetailsBtn');
      if (!btn) return;
      
      const batchId = btn.dataset.batchId || '';
      const purchaseId = parseInt(btn.dataset.purchaseId || '0', 10);
      
      if (batchId || purchaseId > 0) {
        openDeliveryDetailsModal(batchId, purchaseId);
      }
    });
  }

  // Complete Delivery Feature (only for Partial deliveries)
  const continueDeliveryBtn = document.getElementById('continueDeliveryBtn');
  const continueDeliverySection = document.getElementById('continueDeliverySection');
  const continueDeliveryPurchaseItemsList = document.getElementById('continueDeliveryPurchaseItemsList');
  const continueDeliveryPurchaseItemsEmpty = document.getElementById('continueDeliveryPurchaseItemsEmpty');
  const continueReceiveItemSection = document.getElementById('continueReceiveItemSection');
  const continueFinalInventorySection = document.getElementById('continueFinalInventorySection');
  const continueReceiveItemName = document.getElementById('continueReceiveItemName');
  const continueReceivePurchaseQty = document.getElementById('continueReceivePurchaseQty');
  const continueReceiveQuantity = document.getElementById('continueReceiveQuantity');
  const continueReceiveUnit = document.getElementById('continueReceiveUnit');
  const continueReceiveUndoBtn = document.getElementById('continueReceiveUndoBtn');
  const continueDeliveryItemSearch = document.getElementById('continueDeliveryItemSearch');
  const continueDeliveryItemDropdown = document.getElementById('continueDeliveryItemDropdown');
  const continueDeliveryQuantityInput = document.getElementById('continueDeliveryQuantityInput');
  const continueDeliveryUnitInput = document.getElementById('continueDeliveryUnitInput');
  const continueDeliveryUnitSelect = document.getElementById('continueDeliveryUnitSelect');
  const continueDeliveryUnitHelp = document.getElementById('continueDeliveryUnitHelp');
  const continueDeliverySupplierInput = document.getElementById('continueDeliverySupplierInput');
  const continueDeliveryAddItemBtn = document.getElementById('continueDeliveryAddItemBtn');
  const continueDeliveryItemsBody = document.getElementById('continueDeliveryItemsBody');
  const continueDeliveryItemsEmpty = document.getElementById('continueDeliveryItemsEmpty');
  
  let continueCurrentPurchaseItemData = null;
  let continueDeliveryItems = [];
  
  // Show complete delivery section when button is clicked
  if (continueDeliveryBtn && continueDeliverySection) {
    continueDeliveryBtn.addEventListener('click', () => {
      if (continueDeliverySection.classList.contains('hidden')) {
        continueDeliverySection.classList.remove('hidden');
        populateContinueDeliveryPurchaseItems();
        populateContinueDeliveryIngredientOptions();
      } else {
        continueDeliverySection.classList.add('hidden');
      }
    });
  }
  
  // Populate ingredient options for continue delivery (from INGREDIENTS array)
  function populateContinueDeliveryIngredientOptions() {
    if (typeof INGREDIENTS === 'undefined' || !Array.isArray(INGREDIENTS)) {
      console.error('INGREDIENTS array not found');
      return;
    }
    
    // Populate deliveryIngredientOptions for the continue delivery search
    deliveryIngredientOptions = [];
    INGREDIENTS.forEach(ing => {
      const unitLabel = ing.display_unit || ing.unit || 'unit';
      deliveryIngredientOptions.push({
        id: ing.id,
        name: ing.name,
        baseUnit: ing.unit || '',
        displayUnit: ing.display_unit || '',
        displayFactor: ing.display_factor || 1,
        displayText: `${ing.name} (${unitLabel})`
      });
    });
  }
  
  // Populate purchase items (only partial items get Add button)
  function populateContinueDeliveryPurchaseItems() {
    if (!continueDeliveryPurchaseItemsList || !window.currentBatchData || !window.currentBatchData.items) return;
    
    const items = window.currentBatchData.items;
    continueDeliveryPurchaseItemsList.innerHTML = '';
    
    let hasItems = false;
    items.forEach((item, index) => {
      const orderedQty = Number(item.ordered_qty || 0);
      const receivedQty = Number(item.received_qty || 0);
      const remainingQty = Number(item.remaining_qty || 0);
      const isPartial = remainingQty > 0.0001; // Only show items with remaining quantity
      
      if (!isPartial) return; // Skip complete items
      
      hasItems = true;
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-gray-50';
      tr.dataset.purchaseId = item.purchase_id || '';
      tr.dataset.itemName = item.item_name || '';
      tr.dataset.orderedQty = orderedQty;
      tr.dataset.remainingQty = remainingQty;
      tr.dataset.unit = item.unit || '';
      tr.dataset.itemIndex = index;
      
      tr.innerHTML = `
        <td class="px-3 py-2 text-gray-900">${escapeHtml(item.item_name)}</td>
        <td class="px-3 py-2 text-gray-700">${orderedQty.toFixed(2)}</td>
        <td class="px-3 py-2 text-gray-700">${remainingQty.toFixed(2)}</td>
        <td class="px-3 py-2 text-gray-700">${escapeHtml(item.unit)}</td>
        <td class="px-3 py-2">
          <button type="button" class="addContinuePurchaseItemBtn inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 transition-colors" data-item-index="${index}">
            <i data-lucide="plus" class="w-3 h-3"></i>
            Add
          </button>
        </td>
      `;
      continueDeliveryPurchaseItemsList.appendChild(tr);
    });
    
    if (continueDeliveryPurchaseItemsEmpty) {
      continueDeliveryPurchaseItemsEmpty.classList.toggle('hidden', hasItems);
    }
    if (continueDeliveryPurchaseItemsList) {
      continueDeliveryPurchaseItemsList.classList.toggle('hidden', !hasItems);
    }
    
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }
  
  // Handle Add button on continue delivery purchase items
  if (continueDeliveryPurchaseItemsList) {
    continueDeliveryPurchaseItemsList.addEventListener('click', (e) => {
      const btn = e.target.closest('.addContinuePurchaseItemBtn');
      if (!btn) return;
      
      // Check if another item is already selected
      if (continueCurrentPurchaseItemData) {
        alert('Cannot add multiple items. Please complete or cancel the current item first.');
        return;
      }
      
      const itemIndex = parseInt(btn.dataset.itemIndex || '-1', 10);
      const row = btn.closest('tr');
      if (!row || itemIndex < 0 || row.dataset.removed === 'true') return;
      
      // Get purchase item data
      const purchaseItem = {
        purchaseId: row.dataset.purchaseId || '',
        itemName: row.dataset.itemName || '',
        orderedQty: parseFloat(row.dataset.orderedQty || '0'),
        remainingQty: parseFloat(row.dataset.remainingQty || '0'),
        unit: row.dataset.unit || '',
        itemIndex: itemIndex,
        row: row
      };
      
      // Store for later use
      continueCurrentPurchaseItemData = purchaseItem;
      
      // Show receive item section
      if (continueReceiveItemSection) {
        continueReceiveItemSection.classList.remove('hidden');
      }
      if (continueFinalInventorySection) {
        continueFinalInventorySection.classList.remove('hidden');
      }
      
      // Auto-fill receive item fields
      if (continueReceiveItemName) {
        continueReceiveItemName.value = purchaseItem.itemName;
      }
      if (continueReceivePurchaseQty) {
        continueReceivePurchaseQty.value = purchaseItem.remainingQty.toFixed(2);
      }
      if (continueReceiveQuantity) {
        continueReceiveQuantity.value = '';
        continueReceiveQuantity.max = purchaseItem.remainingQty; // Max is remaining qty
      }
      if (continueReceiveUnit) {
        continueReceiveUnit.value = purchaseItem.unit;
      }
      
      // Enable inventory fields
      if (continueDeliveryItemSearch) continueDeliveryItemSearch.readOnly = false;
      if (continueDeliveryQuantityInput) continueDeliveryQuantityInput.readOnly = false;
      if (continueDeliveryUnitInput) continueDeliveryUnitInput.readOnly = false;
      if (continueDeliveryUnitSelect) continueDeliveryUnitSelect.disabled = false;
      
      // Auto-fill supplier from batch data
      if (continueDeliverySupplierInput && window.currentBatchData?.supplier) {
        continueDeliverySupplierInput.value = window.currentBatchData.supplier;
      }
      
      // Hide the purchase item row
      row.style.display = 'none';
      row.dataset.removed = 'true';
      
      // Focus on receive quantity
      if (continueReceiveQuantity) {
        continueReceiveQuantity.focus();
      }
      
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Handle Cancel/Undo button in continue receive section
  if (continueReceiveUndoBtn) {
    continueReceiveUndoBtn.addEventListener('click', () => {
      // Restore purchase item row
      if (continueCurrentPurchaseItemData && continueCurrentPurchaseItemData.row) {
        continueCurrentPurchaseItemData.row.style.display = '';
        continueCurrentPurchaseItemData.row.dataset.removed = 'false';
      }
      
      // Hide sections
      if (continueReceiveItemSection) {
        continueReceiveItemSection.classList.add('hidden');
      }
      if (continueFinalInventorySection) {
        continueFinalInventorySection.classList.add('hidden');
      }
      
      // Clear fields
      if (continueReceiveItemName) continueReceiveItemName.value = '';
      if (continueReceivePurchaseQty) continueReceivePurchaseQty.value = '';
      if (continueReceiveQuantity) continueReceiveQuantity.value = '';
      if (continueReceiveUnit) continueReceiveUnit.value = '';
      if (continueDeliveryItemSearch) continueDeliveryItemSearch.value = '';
      if (continueDeliveryQuantityInput) continueDeliveryQuantityInput.value = '';
      if (continueDeliveryUnitInput) {
        continueDeliveryUnitInput.value = '';
        continueDeliveryUnitInput.classList.remove('hidden');
      }
      if (continueDeliveryUnitSelect) {
        continueDeliveryUnitSelect.value = '';
        continueDeliveryUnitSelect.classList.add('hidden');
        continueDeliveryUnitSelect.disabled = true;
      }
      if (continueDeliveryUnitHelp) {
        continueDeliveryUnitHelp.classList.add('hidden');
        continueDeliveryUnitHelp.textContent = '';
      }
      if (continueDeliverySupplierInput) continueDeliverySupplierInput.value = '';
      
      // Clear stored data
      continueCurrentPurchaseItemData = null;
      
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Handle Add to Delivery button in continue section
  if (continueDeliveryAddItemBtn) {
    continueDeliveryAddItemBtn.addEventListener('click', () => {
      if (!continueCurrentPurchaseItemData) return;
      
      const receiveQty = parseFloat(continueReceiveQuantity?.value || '0');
      const purchaseQty = continueCurrentPurchaseItemData.orderedQty;
      const remainingQty = continueCurrentPurchaseItemData.remainingQty;
      
      // Validate receive quantity
      if (receiveQty <= 0) {
        alert('Please enter a valid receive quantity.');
        return;
      }
      
      if (receiveQty > remainingQty + 0.0001) {
        alert(`Receive quantity cannot exceed remaining quantity (${remainingQty.toFixed(2)}).`);
        return;
      }
      
      // Get inventory item
      const ingredientName = continueDeliveryItemSearch?.value?.trim() || '';
      if (!ingredientName) {
        alert('Please select an inventory item.');
        return;
      }
      
      const ingredient = INGREDIENTS.find(ing => ing.name.toLowerCase() === ingredientName.toLowerCase());
      if (!ingredient) {
        alert('Selected ingredient not found.');
        return;
      }
      
      const inventoryQty = parseFloat(continueDeliveryQuantityInput?.value || '0');
      if (inventoryQty <= 0) {
        alert('Please enter a valid inventory quantity.');
        return;
      }
      
      // Get unit from dropdown if visible, otherwise from input
      let inventoryUnit = '';
      if (continueDeliveryUnitSelect && !continueDeliveryUnitSelect.classList.contains('hidden')) {
        inventoryUnit = continueDeliveryUnitSelect?.value?.trim() || '';
      } else {
        inventoryUnit = continueDeliveryUnitInput?.value?.trim() || '';
      }
      
      if (!inventoryUnit) {
        alert('Please enter or select a unit.');
        return;
      }
      
      // Calculate delivery status
      const newReceivedQty = receiveQty;
      const deliveryStatus = newReceivedQty < purchaseQty - 0.0001 ? 'partial' : 'complete';
      
      // Add to delivery items
      continueDeliveryItems.push({
        purchase_id: continueCurrentPurchaseItemData.purchaseId,
        ingredient_id: ingredient.id,
        itemName: ingredient.name,
        quantity: inventoryQty,
        unit: inventoryUnit,
        supplier: continueDeliverySupplierInput?.value || window.currentBatchData?.supplier || '',
        receiveQuantity: newReceivedQty,
        receiveUnit: continueCurrentPurchaseItemData.unit,
        purchaseItemName: continueCurrentPurchaseItemData.itemName,
        status: deliveryStatus
      });
      
      // Clear and hide sections
      if (continueReceiveItemSection) {
        continueReceiveItemSection.classList.add('hidden');
      }
      if (continueFinalInventorySection) {
        continueFinalInventorySection.classList.add('hidden');
      }
      
      // Clear fields
      if (continueReceiveItemName) continueReceiveItemName.value = '';
      if (continueReceivePurchaseQty) continueReceivePurchaseQty.value = '';
      if (continueReceiveQuantity) continueReceiveQuantity.value = '';
      if (continueReceiveUnit) continueReceiveUnit.value = '';
      if (continueDeliveryItemSearch) continueDeliveryItemSearch.value = '';
      if (continueDeliveryQuantityInput) continueDeliveryQuantityInput.value = '';
      if (continueDeliveryUnitInput) {
        continueDeliveryUnitInput.value = '';
        continueDeliveryUnitInput.classList.remove('hidden');
      }
      if (continueDeliveryUnitSelect) {
        continueDeliveryUnitSelect.value = '';
        continueDeliveryUnitSelect.classList.add('hidden');
        continueDeliveryUnitSelect.disabled = true;
      }
      if (continueDeliveryUnitHelp) {
        continueDeliveryUnitHelp.classList.add('hidden');
        continueDeliveryUnitHelp.textContent = '';
      }
      if (continueDeliverySupplierInput) continueDeliverySupplierInput.value = '';
      
      // Clear stored data
      continueCurrentPurchaseItemData = null;
      
      // Render delivery items
      renderContinueDeliveryItems();
      
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }
  
  // Render continue delivery items
  function renderContinueDeliveryItems() {
    if (!continueDeliveryItemsBody) return;
    
    if (continueDeliveryItems.length === 0) {
      continueDeliveryItemsBody.innerHTML = '';
      if (continueDeliveryItemsEmpty) {
        continueDeliveryItemsEmpty.classList.remove('hidden');
      }
      return;
    }
    
    if (continueDeliveryItemsEmpty) {
      continueDeliveryItemsEmpty.classList.add('hidden');
    }
    
    continueDeliveryItemsBody.innerHTML = '';
    continueDeliveryItems.forEach((item, index) => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-gray-50';
      tr.innerHTML = `
        <td class="px-3 py-2">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
              <i data-lucide="package" class="w-4 h-4 text-gray-600"></i>
            </div>
            <span class="font-medium text-gray-900">${escapeHtml(item.itemName)}</span>
          </div>
        </td>
        <td class="px-3 py-2 text-gray-700">${Number(item.quantity).toFixed(2)}</td>
        <td class="px-3 py-2 text-gray-700">${escapeHtml(item.unit)}</td>
        <td class="px-3 py-2 text-gray-700">${escapeHtml(item.supplier)}</td>
        <td class="px-3 py-2">
          <button type="button" class="removeContinueDeliveryItem inline-flex items-center gap-1 text-red-600 hover:text-red-700" data-index="${index}">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            Remove
          </button>
        </td>
      `;
      continueDeliveryItemsBody.appendChild(tr);
    });
    
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }
  
  // Handle remove item from continue delivery
  if (continueDeliveryItemsBody) {
    continueDeliveryItemsBody.addEventListener('click', (e) => {
      const btn = e.target.closest('.removeContinueDeliveryItem');
      if (!btn) return;
      const index = parseInt(btn.dataset.index || '-1', 10);
      if (index >= 0 && index < continueDeliveryItems.length) {
        const removedItem = continueDeliveryItems[index];
        
        // Restore purchase item row if it was removed
        if (removedItem.purchaseItemName && continueDeliveryPurchaseItemsList) {
          const purchaseRows = continueDeliveryPurchaseItemsList.querySelectorAll('tr');
          for (const row of purchaseRows) {
            if (row.dataset.removed === 'true' && row.dataset.itemName === removedItem.purchaseItemName) {
              row.style.display = '';
              row.dataset.removed = 'false';
              break;
            }
          }
        }
        
        continueDeliveryItems.splice(index, 1);
        renderContinueDeliveryItems();
        
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      }
    });
  }
  
  // Inventory search for continue delivery
  if (continueDeliveryItemSearch && continueDeliveryItemDropdown) {
    continueDeliveryItemSearch.addEventListener('input', (e) => {
      const searchTerm = e.target.value;
      filterContinueDeliveryIngredients(searchTerm);
    });
    
    continueDeliveryItemSearch.addEventListener('focus', () => {
      if (continueDeliveryItemSearch.value.trim() === '') {
        filterContinueDeliveryIngredients('');
      } else {
        filterContinueDeliveryIngredients(continueDeliveryItemSearch.value);
      }
    });
  }
  
  function filterContinueDeliveryIngredients(searchTerm) {
    if (!continueDeliveryItemDropdown) return;
    
    const term = (searchTerm || '').toLowerCase().trim();
    const filtered = deliveryIngredientOptions.filter(opt => {
      const nameMatch = opt.name.toLowerCase().includes(term);
      const unitMatch = (opt.displayUnit || opt.baseUnit || '').toLowerCase().includes(term);
      return nameMatch || unitMatch;
    });
    
    continueDeliveryItemDropdown.innerHTML = '';
    
    if (filtered.length === 0) {
      continueDeliveryItemDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No ingredients found</div>';
      continueDeliveryItemDropdown.classList.remove('hidden');
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
        selectContinueDeliveryIngredient(opt);
      });
      
      continueDeliveryItemDropdown.appendChild(div);
    });
    
    continueDeliveryItemDropdown.classList.remove('hidden');
  }
  
  function selectContinueDeliveryIngredient(ingredient) {
    if (!continueDeliveryItemSearch || !continueDeliveryItemDropdown) return;
    
    continueDeliveryItemSearch.value = ingredient.name;
    continueDeliveryItemDropdown.classList.add('hidden');
    
    // Get ingredient details from INGREDIENT_LOOKUP
    const ingredientDetails = INGREDIENT_LOOKUP[ingredient.id];
    if (!ingredientDetails) {
      // Fallback to ingredient object passed
      configureContinueDeliveryUnits(ingredient.baseUnit || '', ingredient.displayUnit || '', ingredient.displayFactor || 1);
    } else {
      configureContinueDeliveryUnits(ingredientDetails.unit || '', ingredientDetails.display_unit || '', ingredientDetails.display_factor || 1);
    }
    
    // Focus on quantity
    if (continueDeliveryQuantityInput) {
      continueDeliveryQuantityInput.focus();
    }
  }
  
  function configureContinueDeliveryUnits(baseUnit, displayUnit, displayFactor) {
    if (!continueDeliveryUnitInput || !continueDeliveryUnitSelect) return;
    
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
    }
    // Standard conversions: ml/L
    else if (baseUnit === 'ml') {
      shouldShowDropdown = true;
      dropdownOptions = [
        { value: 'ml', label: 'ml (milliliters)' },
        { value: 'L', label: 'L (liters)' }
      ];
      defaultUnit = 'ml';
    }
    // Custom display_unit with conversion factor
    else if (displayUnit && displayUnit !== baseUnit && displayFactor > 0 && displayFactor !== 1) {
      shouldShowDropdown = true;
      dropdownOptions = [
        { value: baseUnit, label: `${baseUnit} (base unit)` },
        { value: displayUnit, label: `${displayUnit} (${displayFactor}x)` }
      ];
      defaultUnit = displayUnit; // Default to display unit for better UX
    }
    
    if (shouldShowDropdown && dropdownOptions.length > 0) {
      // Show dropdown
      continueDeliveryUnitInput.classList.add('hidden');
      continueDeliveryUnitSelect.classList.remove('hidden');
      continueDeliveryUnitSelect.innerHTML = '<option value="">Select unit</option>' + 
        dropdownOptions.map(opt => `<option value="${opt.value}">${opt.label}</option>`).join('');
      continueDeliveryUnitSelect.disabled = false;
      continueDeliveryUnitSelect.value = defaultUnit;
      
      // Show conversion help text
      if (continueDeliveryUnitHelp) {
        let helpText = '';
        if (baseUnit === 'g') {
          helpText = '1 kg = 1000 g';
        } else if (baseUnit === 'ml') {
          helpText = '1 L = 1000 ml';
        } else if (displayUnit && displayFactor > 1) {
          helpText = `1 ${displayUnit} = ${displayFactor} ${baseUnit}`;
        }
        if (helpText) {
          continueDeliveryUnitHelp.textContent = helpText;
          continueDeliveryUnitHelp.classList.remove('hidden');
        } else {
          continueDeliveryUnitHelp.classList.add('hidden');
        }
      }
    } else {
      // Show text input
      continueDeliveryUnitInput.classList.remove('hidden');
      continueDeliveryUnitSelect.classList.add('hidden');
      const unitToShow = displayUnit || baseUnit || '';
      continueDeliveryUnitInput.value = unitToShow;
      // Hide help text for manual input
      if (continueDeliveryUnitHelp) {
        if (displayUnit && displayUnit !== baseUnit && displayFactor > 1) {
          continueDeliveryUnitHelp.textContent = `1 ${displayUnit} = ${displayFactor} ${baseUnit}`;
          continueDeliveryUnitHelp.classList.remove('hidden');
        } else {
          continueDeliveryUnitHelp.classList.add('hidden');
        }
      }
    }
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (continueDeliveryItemDropdown && continueDeliveryItemSearch && 
        !continueDeliveryItemDropdown.contains(e.target) && 
        !continueDeliveryItemSearch.contains(e.target)) {
      continueDeliveryItemDropdown.classList.add('hidden');
    }
  });
  
  // Form submission for continue delivery
  const continueRecordDeliveryBtn = document.getElementById('continueRecordDeliveryBtn');
  const continueDeliveryForm = document.createElement('form');
  continueDeliveryForm.method = 'post';
  continueDeliveryForm.action = `<?php echo htmlspecialchars($baseUrl); ?>/deliveries`;
  continueDeliveryForm.style.display = 'none';
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = 'csrf_token';
  csrfInput.value = '<?php echo htmlspecialchars(Csrf::token()); ?>';
  continueDeliveryForm.appendChild(csrfInput);
  const actionInput = document.createElement('input');
  actionInput.type = 'hidden';
  actionInput.name = 'action';
  actionInput.value = 'store';
  continueDeliveryForm.appendChild(actionInput);
  const itemsJsonInput = document.createElement('input');
  itemsJsonInput.type = 'hidden';
  itemsJsonInput.name = 'items_json';
  itemsJsonInput.id = 'continueDeliveryItemsJson';
  continueDeliveryForm.appendChild(itemsJsonInput);
  document.body.appendChild(continueDeliveryForm);
  
  // Handle Record Delivery button click
  if (continueRecordDeliveryBtn) {
    continueRecordDeliveryBtn.addEventListener('click', () => {
      if (continueDeliveryItems.length === 0) {
        alert('Please add at least one item to record delivery.');
        return;
      }
      
      // Prepare items JSON
      const itemsJson = continueDeliveryItems.map(item => ({
        purchase_id: item.purchase_id,
        ingredient_id: item.ingredient_id,
        quantity: item.quantity,
        unit: item.unit,
        receive_quantity: item.receiveQuantity
      }));
      
      itemsJsonInput.value = JSON.stringify(itemsJson);
      continueDeliveryForm.submit();
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
  } else {
    applyDeliveryFilter('all');
  }
})();
</script>

<style>
	/* Remove focus ring and border color for all input fields */
	input:focus,
	select:focus,
	textarea:focus {
		outline: none !important;
		box-shadow: none !important;
		border-color: rgb(209 213 219) !important; /* Keep gray-300 border color */
		--tw-ring-offset-shadow: 0 0 #0000 !important;
		--tw-ring-shadow: 0 0 #0000 !important;
		--tw-ring-offset-width: 0px !important;
		--tw-ring-width: 0px !important;
	}

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
		#receiveQuickModal textarea:focus {
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


