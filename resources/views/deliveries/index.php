<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$deliveredTotals = $deliveredTotals ?? [];
?>
<!-- Page Header -->
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-900 tracking-tight">Delivery Management</h1>
			<p class="text-sm sm:text-base text-gray-600 mt-1 font-medium">Record and track ingredient deliveries</p>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-colors">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
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
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
	<!-- Total Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Deliveries</p>
				<p class="text-3xl sm:text-4xl font-black text-gray-900 mt-1"><?php echo $totalDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="truck" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Complete Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Complete Deliveries</p>
				<p class="text-3xl sm:text-4xl font-black text-[#008000] mt-1"><?php echo $completeDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
				<i data-lucide="check-circle" class="w-6 h-6 sm:w-7 sm:h-7 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Partial Deliveries -->
	<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 p-5 sm:p-6">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Partial Deliveries</p>
				<p class="text-3xl sm:text-4xl font-black text-amber-600 mt-1"><?php echo $partialDeliveries; ?></p>
			</div>
			<div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 rounded-xl flex items-center justify-center border border-amber-200">
				<i data-lucide="clock" class="w-6 h-6 sm:w-7 sm:h-7 text-amber-600"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Record Delivery Form -->
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 mb-6 sm:mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
				<i data-lucide="package-check" class="w-5 h-5 text-[#008000]"></i>
			</div>
			<div>
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Record New Delivery</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Record a delivery for an existing purchase</p>
			</div>
		</div>
	</div>
	
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="p-4 sm:p-6" id="deliveriesForm">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        <input type="hidden" name="items_json" id="deliveriesItemsJson" value="[]">
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Purchase Batch Selection -->
            <div class="space-y-3 md:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Select Purchase Batch</label>
                <div class="grid gap-3 lg:grid-cols-2">
                    <div class="space-y-1">
                        <input id="batchSearchInput" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" placeholder="Search supplier, purchaser, or batch ID..." list="batchSearchOptions">
                        <datalist id="batchSearchOptions">
                            <?php foreach (($purchaseGroups ?? []) as $g): 
                                $label = '#' . htmlspecialchars($g['group_id']) . ' — ' . htmlspecialchars($g['supplier']) . ' — ' . htmlspecialchars($g['purchaser_name']);
                            ?>
                                <option value="<?php echo $label; ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="space-y-1">
                        <select id="batchSelect" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
                            <option value="">Choose a batch</option>
                            <?php foreach (($purchaseGroups ?? []) as $g): ?>
                                <option value="<?php echo htmlspecialchars($g['group_id']); ?>"><?php echo '#'.htmlspecialchars($g['group_id']).' — '.htmlspecialchars($g['supplier']).' — '.htmlspecialchars($g['purchaser_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p class="text-xs text-gray-500" id="batchMeta">After selecting a batch, set per-item received quantities below.</p>
                <div id="batchHighlight" class="hidden rounded-xl border-2 border-[#008000]/20 bg-[#008000]/10 px-4 py-3 text-sm text-[#008000] space-y-1">
                    <div class="font-semibold text-[#008000]" id="batchMetaSupplier"></div>
                    <div class="flex flex-wrap gap-3 text-xs text-[#008000]/80">
                        <span id="batchMetaPurchaser"></span>
                        <span id="batchMetaDate"></span>
                    </div>
                </div>
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
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Delivery Status</label>
				<div class="border-2 border-dashed border-[#008000]/30 bg-[#008000]/5 text-sm text-[#008000] rounded-lg px-4 py-3">
					Status is now auto-calculated when you click <strong class="font-semibold">Record Delivery</strong>. If <em>Receive Now</em> matches the <em>Remaining</em> quantity, the delivery will be marked as <span class="font-semibold">Complete Delivery</span>; otherwise it will be recorded as <span class="font-semibold">Partial Delivery</span>.
				</div>
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
			<button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white px-6 py-3 rounded-xl shadow-md hover:opacity-90 hover:shadow-lg focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold">
				<i data-lucide="package-check" class="w-4 h-4"></i>
				Record Delivery
			</button>
		</div>
	</form>
</div>

<?php if (!empty($awaitingPurchases)): ?>
<div id="awaiting-deliveries" class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 overflow-hidden mb-6 sm:mb-8">
    <div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
                <i data-lucide="truck" class="w-5 h-5 text-[#008000]"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Awaiting Deliveries</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Open purchase batches that still need to be delivered</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold bg-amber-50 text-amber-700 border-2 border-amber-200">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <?php echo count($awaitingPurchases); ?> outstanding
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[720px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Purchase</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Supplier</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Item</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Ordered</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Delivered</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Remaining</th>
                    <th class="text-left px-6 py-3 text-gray-700 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                    <?php foreach ($awaitingPurchases as $pending): 
                    $remaining = max(0, (float)$pending['quantity'] - (float)$pending['delivered_quantity']);
                    $batchTs = substr((string)($pending['date_purchased'] ?? ''),0,19);
                    $batchId = substr(sha1(($pending['purchaser_id']??'').'|'.($pending['supplier']??'').'|'.($pending['payment_status']??'').'|'.($pending['receipt_url']??'').'|'.$batchTs),0,10);
                ?>
                <tr class="hover:bg-[#008000]/5 transition-colors">
                    <td class="px-6 py-4">
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
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-medium">
                            <i data-lucide="factory" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($pending['supplier']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($pending['item_name']); ?></div>
                        <div class="text-xs text-gray-500">Unit: <?php echo htmlspecialchars($pending['unit']); ?></div>
                    </td>
                    <td class="px-6 py-4"><?php echo number_format((float)$pending['quantity'], 2); ?> <?php echo htmlspecialchars($pending['unit']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo number_format((float)$pending['delivered_quantity'], 2); ?> <?php echo htmlspecialchars($pending['unit']); ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border-2 border-amber-200">
                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                            <?php echo number_format($remaining, 2); ?> <?php echo htmlspecialchars($pending['unit']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 px-3 py-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white text-sm rounded-xl shadow-md hover:opacity-90 hover:shadow-lg focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold"
                            data-quick-receive
                            data-purchase-id="<?php echo (int)$pending['id']; ?>"
                            data-batch-label="<?php echo htmlspecialchars('#' . $batchId); ?>"
                            data-supplier="<?php echo htmlspecialchars($pending['supplier']); ?>"
                            data-purchaser="<?php echo htmlspecialchars($pending['purchaser_name']); ?>"
                            data-date="<?php echo htmlspecialchars($pending['date_purchased']); ?>"
                            data-item="<?php echo htmlspecialchars($pending['item_name']); ?>"
                            data-unit="<?php echo htmlspecialchars($pending['unit']); ?>"
                            data-display-unit="<?php echo htmlspecialchars($pending['display_unit'] ?? ''); ?>"
                            data-display-factor="<?php echo htmlspecialchars((string)($pending['display_factor'] ?? '')); ?>"
                            data-ordered="<?php echo htmlspecialchars((string)$pending['quantity']); ?>"
                            data-delivered="<?php echo htmlspecialchars((string)$pending['delivered_quantity']); ?>"
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
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-[#008000] bg-gradient-to-b from-[#00A86B] to-[#008000] text-white focus:ring-2 focus:ring-[#008000]" data-quick-status="complete">
                        Complete delivery
                    </button>
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-700 hover:border-gray-300 focus:ring-2 focus:ring-[#008000]" data-quick-status="partial">
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
            <button type="button" id="quickConfirmBtn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-b from-[#00A86B] to-[#008000] text-white text-sm font-semibold shadow-md hover:opacity-90 hover:shadow-lg focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all">
                <i data-lucide="package-check" class="w-4 h-4"></i>
                Confirm & Record
            </button>
        </div>
    </div>
</div>

<!-- Recent Deliveries Table -->
<div class="bg-white rounded-xl shadow-sm border-2 border-gray-200/80 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
					<i data-lucide="clipboard-list" class="w-5 h-5 text-[#008000]"></i>
				</div>
				<div>
					<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Recent Deliveries</h2>
					<p class="text-xs sm:text-sm text-gray-600 mt-0.5">View and track all delivery records</p>
				</div>
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
					<select id="deliveryStatusFilter" class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008000] focus:border-[#008000]">
						<option value="all">All deliveries</option>
						<option value="complete">Complete only</option>
						<option value="partial">Partial only</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[700px]">
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
				<tr class="hover:bg-gray-50 transition-colors" data-delivery-status="<?php echo strtolower($d['delivery_status']); ?>">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-[#008000]/20">
								<span class="text-xs font-semibold text-[#008000]">#<?php echo (int)$d['id']; ?></span>
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
			<button onclick="document.querySelector('form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white rounded-xl shadow-md hover:opacity-90 hover:shadow-lg transition-all font-semibold">
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
        if ($remaining <= 0) { return null; }
        return [
          'purchase_id' => (int)$p['id'],
          'item_name' => $p['item_name'],
          'unit' => $p['unit'],
          'display_unit' => $p['display_unit'],
          'display_factor' => (float)$p['display_factor'],
          'quantity' => (float)$p['quantity'],
          'delivered' => $del,
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
      btn.classList.toggle('bg-gradient-to-r', active);
      btn.classList.toggle('from-[#008000]', active);
      btn.classList.toggle('via-[#00A86B]', active);
      btn.classList.toggle('to-[#008000]', active);
      btn.classList.toggle('text-white', active);
      btn.classList.toggle('border-[#008000]', active);
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
    if (!input) return;
    const remaining = parseFloat(input.dataset.remaining || '0') || 0;
    let value = parseFloat(input.value || '0');
    if (!isFinite(value) || value < 0) value = 0;
    if (remaining > 0 && value > remaining) value = remaining;
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
    tableBody.innerHTML='';
    if (batchHighlight){
      batchHighlight.classList.add('hidden');
      batchMeta.textContent = 'After selecting a batch, set per-item received quantities below.';
    }
    const g = GROUPS.find(x=>x.group_id===groupId);
    if (!g){ box.classList.add('hidden'); itemsJson.value='[]'; return; }
    if (batchHighlight && g){
      batchMetaSupplier.textContent = `Supplier: ${g.supplier ?? ''}`;
      batchMetaPurchaser.textContent = `Purchaser: ${g.purchaser_name ?? ''}`;
      batchMetaDate.textContent = `Ordered: ${g.date_purchased ?? ''}`;
      batchMeta.textContent = 'Review batch details below and record received quantities.';
      batchHighlight.classList.remove('hidden');
    }
    for (const it of g.items){
      const remaining = Math.max(0, (it.quantity - it.delivered));
      const tr = document.createElement('tr');

      const itemTd = document.createElement('td');
      itemTd.className = 'px-4 py-2';
      itemTd.innerHTML = `${it.item_name}<input type="hidden" name="purchase_id[]" value="${it.purchase_id}">`;
      tr.appendChild(itemTd);

      const remainingTd = document.createElement('td');
      remainingTd.className = 'px-4 py-2 text-gray-600';
      remainingTd.textContent = `${remaining.toFixed(2)} ${it.unit}`;
      tr.appendChild(remainingTd);

      const qtyTd = document.createElement('td');
      qtyTd.className = 'px-4 py-2';
      const qtyInput = document.createElement('input');
      qtyInput.type = 'number';
      qtyInput.step = '0.01';
      qtyInput.min = '0';
      if (remaining > 0){ qtyInput.max = remaining.toFixed(2); }
      qtyInput.name = 'row_qty[]';
      qtyInput.value = remaining.toFixed(2);
      qtyInput.dataset.remaining = remaining.toFixed(6);
      qtyInput.className = 'w-32 border rounded px-3 py-2';
      if (remaining <= 0){
        qtyInput.readOnly = true;
        qtyInput.classList.add('bg-gray-100','text-gray-500','cursor-not-allowed');
      }
      qtyInput.addEventListener('input', ()=>{
        clampInputToRemaining(qtyInput);
        updateStatusPreview(qtyInput);
        sync();
      });
      qtyInput.addEventListener('blur', ()=>{
        clampInputToRemaining(qtyInput);
        updateStatusPreview(qtyInput);
        sync();
      });
      qtyTd.appendChild(qtyInput);
      tr.appendChild(qtyTd);

      const unitTd = document.createElement('td');
      unitTd.className = 'px-4 py-2';
      const unitSel = buildUnitOptions(it.unit, it.display_unit);
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

  sel.addEventListener('change', ()=>{ render(sel.value); });
  if (searchInput){
    searchInput.addEventListener('input', ()=>{
      if (!searchInput.value){
        sel.value = '';
        render('');
        return;
      }
      const match = Array.from(sel.options).find(opt => opt.textContent === searchInput.value);
      if (match){
        sel.value = match.value;
        sel.dispatchEvent(new Event('change'));
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
        awaiting.classList.add('ring-2','ring-[#008000]/30','ring-offset-2');
        awaiting.scrollIntoView({behavior:'smooth'});
        setTimeout(()=> awaiting.classList.remove('ring-2','ring-offset-2','ring-[#008000]/30'), 2000);
      }
    }
  } else {
    applyDeliveryFilter('all');
  }
})();
</script>


