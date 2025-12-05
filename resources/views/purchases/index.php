<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Purchase Transactions</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Record and manage ingredient purchases</p>
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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
	<!-- Total Purchases -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-purple-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL PURCHASES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo count($purchases); ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">All purchase transactions</p>
		</div>
	</div>
	
	<!-- Pending Payments -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="clock" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-yellow-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PENDING PAYMENTS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-yellow-600 mb-1 md:mb-1.5"><?php echo $pendingCount; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Awaiting payment</p>
		</div>
	</div>
	
	<!-- Paid Purchases -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="check-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PAID PURCHASES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-green-600 mb-1 md:mb-1.5"><?php echo count($purchases) - $pendingCount; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Completed payments</p>
		</div>
	</div>
	
	<!-- Total Cost -->
	<div class="bg-white rounded-lg shadow-md border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<span class="text-lg md:text-xl font-bold text-blue-600">₱</span>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">TOTAL COST</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-blue-600 mb-1 md:mb-1.5">₱<?php echo number_format(array_sum(array_column($purchases, 'cost')), 2); ?></div>
			<p class="text-[10px] md:text-xs text-gray-600">Total expenses</p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- New Batch Purchase Form -->
<?php 
$paymentFilter = strtolower((string)($_GET['payment'] ?? 'all'));
?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden w-full">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600 flex-shrink-0"></i>
                    <span class="truncate">Record New Purchase (Batch)</span>
                </h2>
                <p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Search ingredient, set qty/unit/cost, add to list, then save</p>
            </div>
        </div>
    </div>
    
    <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" class="p-4 md:p-5 lg:p-6 w-full overflow-x-hidden" id="purchaseForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        
        <!-- Add item panel -->
        <section class="border rounded-lg p-4 md:p-5 lg:p-6 w-full overflow-x-hidden">
            <div class="mb-3 md:mb-4"><span class="text-[10px] md:text-xs uppercase tracking-wide text-gray-500">Step 1</span><div class="font-medium text-xs md:text-sm">Add purchase items</div></div>
            <div class="space-y-4 md:space-y-5">
                <!-- Ingredient - Primary field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="package" class="w-4 h-4 inline-block mr-1"></i>
                        Ingredient Name
                    </label>
                    <input id="ingInput" type="text" class="w-full border-2 border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors" placeholder="Type or search ingredient name..." required />
                    <input type="hidden" id="ingIdHidden" />
                </div>
                
                <!-- Quantity & Unit - Grouped together -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 lg:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="hash" class="w-4 h-4 inline-block mr-1"></i>
                            Quantity
                        </label>
                        <input id="qtyInput" type="number" step="0.01" min="0.01" class="w-full border-2 border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors" placeholder="0.00" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="ruler" class="w-4 h-4 inline-block mr-1"></i>
                            Unit
                        </label>
                        <select id="unitSelect" class="w-full border-2 border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors bg-white">
                            <option value="">-- Select unit --</option>
                            <option value="g">g (grams)</option>
                            <option value="kg">kg (kilograms)</option>
                            <option value="ml">ml (milliliters)</option>
                            <option value="L">L (liters)</option>
                            <option value="pcs">pcs (pieces)</option>
                            <option value="sack">sack</option>
                            <option value="box">box</option>
                        </select>
                    </div>
                </div>
                
                <!-- Cost - Prominent but separate -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="text-green-600 font-semibold mr-1">₱</span>
                        <span class="text-green-600">Cost</span>
                        <span class="text-xs text-gray-500 font-normal ml-1">(Philippine Peso)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₱</span>
                        <input id="costInput" type="number" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-lg pl-7 md:pl-8 pr-3 md:pr-4 py-2 md:py-3 text-sm bg-gray-50/30 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 font-semibold text-gray-700 transition-colors" placeholder="0.00" />
                    </div>
                </div>
                
                <!-- Add Button -->
                <div class="flex justify-end pt-2">
                    <button type="button" id="addRowBtn" class="inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-semibold shadow-sm text-xs md:text-sm w-full sm:w-auto">
                        <i data-lucide="plus-circle" class="w-4 h-4 md:w-3.5 md:h-3.5 lg:w-5 lg:h-5"></i>
                        <span class="whitespace-nowrap">Add to Purchase List</span>
                    </button>
                </div>
            </div>
        </section>
        
        <!-- Items in this purchase - Below panels -->
        <div class="mt-6 border rounded-lg overflow-hidden w-full max-w-full">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
                <div class="font-semibold text-sm md:text-base text-gray-900 flex items-center gap-1.5 md:gap-2">
                    <i data-lucide="list" class="w-4 h-4 md:w-5 md:h-5 text-green-600"></i>
                    Items
                </div>
            </div>
            <div class="overflow-x-hidden overflow-y-auto max-h-[400px] w-full">
                <table class="w-full text-[10px] md:text-xs lg:text-sm table-fixed">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-[10px] md:text-xs lg:text-sm font-semibold text-gray-700 border-b w-1/3">Ingredient</th>
                            <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-[10px] md:text-xs lg:text-sm font-semibold text-gray-700 border-b w-1/6">Qty</th>
                            <th class="hidden lg:table-cell text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-[10px] md:text-xs lg:text-sm font-semibold text-gray-700 border-b w-1/6">Unit</th>
                            <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-[10px] md:text-xs lg:text-sm font-semibold text-gray-700 border-b w-1/6">Cost</th>
                            <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 text-[10px] md:text-xs lg:text-sm font-semibold text-gray-700 border-b w-1/6">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseList" class="divide-y divide-gray-100">
                        <tr id="emptyStateRow">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p>No items added yet</p>
                                <p class="text-xs mt-1">Add items from the form above</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 md:px-5 lg:px-6 py-3 md:py-4 border-t flex justify-end">
                <button type="button" id="openBatchModalBtn" class="inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-semibold shadow-sm disabled:opacity-60 disabled:cursor-not-allowed text-xs md:text-sm">
                    <i data-lucide="shopping-cart" class="w-4 h-4 md:w-3.5 md:h-3.5 lg:w-5 lg:h-5"></i>
                    <span class="whitespace-nowrap">Record Purchase Batch</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Batch Information Modal -->
<div id="batchModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
    <div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;"></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-4 md:p-6 overflow-y-auto overflow-x-hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-[calc(100vw-2rem)] md:max-w-2xl my-4 md:my-8 overflow-y-auto overflow-x-hidden max-h-[90vh] mx-auto">
            <div class="sticky top-0 bg-white border-b px-4 md:px-5 lg:px-6 py-3 md:py-4 z-10">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
                        <span class="hidden sm:inline">Record Purchase Batch</span>
                        <span class="sm:hidden">Record Batch</span>
                    </h2>
                    <button type="button" id="closeBatchModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
                    </button>
                </div>
                <div class="flex items-center justify-end">
                    <div class="text-xs md:text-sm font-bold text-green-700">Total: ₱<span id="totalCostModal">0.00</span></div>
                </div>
            </div>
        <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" id="batchModalForm" class="p-4 md:p-5 lg:p-6 max-w-full overflow-x-hidden">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
            <input type="hidden" name="items_json" id="itemsJsonModal" value="[]">
            
            <div class="space-y-6">
                <!-- Batch Information Section -->
                <div>
                    <h3 class="text-[10px] md:text-xs font-semibold text-gray-700 mb-2 md:mb-3 flex items-center gap-1 md:gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                        Batch Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 lg:gap-6">
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">
                                <i data-lucide="truck" class="w-3.5 h-3.5 md:w-4 md:h-4 inline-block mr-1"></i>
                                Supplier
                            </label>
                            <input name="supplier" class="w-full border-2 border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Enter supplier name" required />
                        </div>
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">
                                <i data-lucide="shopping-bag" class="w-3.5 h-3.5 md:w-4 md:h-4 inline-block mr-1"></i>
                                Purchase Type
                            </label>
                            <select name="purchase_type" id="purchaseTypeModal" class="w-full border-2 border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                <option value="in_store">In-store purchase</option>
                                <option value="delivery">Delivery</option>
                            </select>
                            <input type="hidden" name="payment_status" id="paymentStatusHiddenModal" value="Paid">
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information Section -->
                <div>
                    <h3 class="text-[10px] md:text-xs font-semibold text-gray-700 mb-2 md:mb-3 flex items-center gap-1 md:gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                        Payment Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 lg:gap-6">
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">
                                <i data-lucide="wallet" class="w-3.5 h-3.5 md:w-4 md:h-4 inline-block mr-1"></i>
                                Payment Type
                            </label>
                            <select name="payment_type" id="paymentTypeModal" class="w-full border-2 border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                <option value="Card">Card</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                    </div>
                    <div id="cashFieldsModal" class="hidden mt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">Base Amount (Cash)</label>
                                <div class="relative">
                                    <span class="absolute left-2.5 md:left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs md:text-sm">₱</span>
                                    <input type="number" step="0.01" min="0" name="base_amount" id="baseAmountModal" class="w-full border-2 border-gray-300 rounded-lg pl-7 md:pl-8 pr-2.5 md:pr-4 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="0.00" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">Total</label>
                                <div class="relative">
                                    <span class="absolute left-2.5 md:left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs md:text-sm">₱</span>
                                    <input type="text" id="totalCostReadonlyModal" class="w-full border-2 border-gray-200 rounded-lg pl-7 md:pl-8 pr-2.5 md:pr-4 py-1.5 md:py-2 text-xs md:text-sm bg-gray-50 font-semibold" value="0.00" readonly />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">Change</label>
                                <div class="relative">
                                    <span class="absolute left-2.5 md:left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs md:text-sm">₱</span>
                                    <input type="text" id="changeReadonlyModal" class="w-full border-2 border-gray-200 rounded-lg pl-7 md:pl-8 pr-2.5 md:pr-4 py-1.5 md:py-2 text-xs md:text-sm bg-gray-50 font-semibold" value="0.00" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Receipt Upload -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5 md:mb-2">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 md:w-4 md:h-4 inline-block mr-1"></i>
                        Receipt Upload
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-2.5 md:p-3 text-center hover:border-green-400 transition-colors bg-gray-50/50" id="receiptDropzoneModal">
                        <input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf" class="hidden" id="receiptUploadModal" required />
                        <label for="receiptUploadModal" class="cursor-pointer flex flex-col items-center gap-1">
                            <i data-lucide="upload" class="w-5 h-5 md:w-6 md:h-6 text-gray-400"></i>
                            <p class="text-[10px] md:text-xs text-gray-600 font-medium">Click to upload receipt</p>
                            <p class="text-[9px] md:text-[10px] text-gray-500">JPG, PNG, WebP, HEIC, PDF (max 10MB)</p>
                        </label>
                        <div id="receiptSelectedModal" class="mt-2 hidden text-left bg-white border border-gray-200 rounded-lg px-3 py-2 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p id="receiptFileNameModal" class="text-xs font-medium text-gray-800 truncate"></p>
                                <p id="receiptFileSizeModal" class="text-[10px] text-gray-500"></p>
                            </div>
                            <button type="button" id="receiptClearBtnModal" class="text-xs text-red-600 hover:text-red-700 hover:underline whitespace-nowrap">Remove</button>
                        </div>
                        <p id="receiptErrorModal" class="mt-2 text-xs text-red-600 hidden"></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 md:mt-6 flex flex-col sm:flex-row justify-end gap-2 md:gap-3 pt-4 border-t">
                <button type="button" id="cancelBatchModalBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium text-xs md:text-sm">
                    Cancel
                </button>
                <button type="submit" id="recordPurchaseBtnModal" class="w-full sm:w-auto inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-semibold shadow-sm disabled:opacity-60 disabled:cursor-not-allowed text-xs md:text-sm">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                    <span class="whitespace-nowrap">Record Purchase Batch</span>
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Remove Item Confirmation Modal -->
<div id="removeItemModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
    <div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-4 overflow-x-hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-w-[calc(100vw-2rem)] mx-auto">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm md:text-base font-semibold text-gray-900">Remove Item</h3>
                    <p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Are you sure you want to remove this item from the purchase list?</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-2.5 md:p-3 mb-3 md:mb-4">
                <p class="text-xs md:text-sm font-medium text-gray-900" id="removeItemName"></p>
            </div>
            <div class="flex justify-end gap-2 md:gap-3">
                <button type="button" id="cancelRemoveBtn" class="inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium text-xs md:text-sm">
                    Cancel
                </button>
                <button type="button" id="confirmRemoveBtn" class="inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-xs md:text-sm">
                    Remove Item
                </button>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Recent Purchases Table (Grouped) -->
<div id="recent-purchases" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden max-w-full">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-5 lg:px-6 py-3 md:py-4 border-b">
		<div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between">
			<div>
                <h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
                    <span class="text-green-600 font-semibold text-sm md:text-base">₱</span>
                    Recent Purchases
                </h2>
				<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">View and manage all purchase transactions</p>
			</div>
			<div class="flex flex-col sm:flex-row gap-3 md:gap-4 items-start sm:items-center">
				<div class="flex items-center gap-2 md:gap-4">
					<div class="text-xs md:text-sm text-gray-600">
						<span class="font-medium"><?php echo isset($purchaseGroups) ? count($purchaseGroups) : count($purchases); ?></span> total purchases
					</div>
					<?php if ($pendingCount > 0): ?>
						<div class="flex items-center gap-1.5 md:gap-2 px-2.5 md:px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs md:text-sm font-medium">
							<i data-lucide="clock" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
							<?php echo $pendingCount; ?> pending
						</div>
					<?php endif; ?>
				</div>
				<div class="flex flex-col sm:flex-row sm:items-center gap-2 text-xs md:text-sm text-gray-600">
					<label for="paymentStatusFilter" class="whitespace-nowrap">Filter payment:</label>
					<select id="paymentStatusFilter" data-default="<?php echo htmlspecialchars($paymentFilter); ?>" class="w-full sm:w-auto border border-gray-300 rounded-lg px-3 md:px-4 py-2 text-xs md:text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
						<option value="all">All</option>
						<option value="paid">Paid</option>
						<option value="pending">Pending</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px] w-full">
        <table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="hidden lg:table-cell text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Purchaser</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Items</th>
                    <th class="hidden lg:table-cell text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Total Qty</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Cost</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Supplier</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Payment</th>
                    <th class="hidden lg:table-cell text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Receipt</th>
                    <th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Delivery Status</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Actions</th>
				</tr>
			</thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach (($purchaseGroups ?? []) as $g): ?>
                <tr class="hover:bg-gray-50 transition-colors" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>">
                    <td class="hidden lg:table-cell px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($g['purchaser_name'], 0, 2)); ?></span>
                            </div>
                            <span class="font-medium text-gray-900"><?php echo htmlspecialchars($g['purchaser_name']); ?></span>
                        </div>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <?php $count = count($g['items']); ?>
                        <div class="flex items-center gap-1 md:gap-1.5 lg:gap-2">
                            <span class="font-medium text-gray-900"><?php echo $count; ?> item<?php echo $count>1?'s':''; ?></span>
                            <?php if ($count>1): ?>
                                <button type="button" class="text-blue-600 underline text-xs openPurchaseModal" data-group-id="<?php echo htmlspecialchars($g['group_id']); ?>">View</button>
                            <?php else: ?>
                                <button type="button" class="text-blue-600 underline text-xs openPurchaseModal" data-group-id="<?php echo htmlspecialchars($g['group_id']); ?>">View</button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
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
                        <div class="flex items-center gap-1 md:gap-1.5 lg:gap-2">
                            <span class="font-semibold text-gray-900"><?php echo number_format($qtyShow,2); ?></span>
                            <span class="text-gray-500 text-[9px] md:text-[10px] lg:text-xs"><?php echo htmlspecialchars($unitShow); ?></span>
                        </div>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <span class="text-lg font-bold text-gray-900">₱<?php echo number_format((float)$g['cost_sum'], 2); ?></span>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-[10px] md:text-xs lg:text-sm font-medium">
                            <i data-lucide="truck" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                            <?php echo htmlspecialchars($g['supplier']); ?>
                        </span>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <?php 
                        $paymentClass = $g['payment_status'] === 'Paid' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $paymentIcon = $g['payment_status'] === 'Paid' ? 'check-circle' : 'clock';
                        ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] md:text-xs lg:text-sm font-medium border <?php echo $paymentClass; ?>">
                            <i data-lucide="<?php echo $paymentIcon; ?>" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                            <?php echo htmlspecialchars($g['payment_status']); ?>
                        </span>
                        <?php if (!empty($g['paid_at'])): ?>
                            <p class="text-[9px] md:text-[10px] lg:text-xs text-gray-500 mt-1">Paid on <?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($g['paid_at']))); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="hidden lg:table-cell px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <?php if (!empty($g['receipt_url'])): ?>
                            <?php $fullUrl = (preg_match('#^https?://#', $g['receipt_url'])) ? $g['receipt_url'] : (rtrim($baseUrl, '/').'/'.ltrim($g['receipt_url'], '/')); ?>
                            <a href="<?php echo htmlspecialchars($fullUrl); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-[10px] md:text-xs lg:text-sm font-medium">
                                <i data-lucide="file-text" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                                View Receipt
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-[10px] md:text-xs lg:text-sm">No receipt</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <?php 
                        $deliveryPercentage = (float)$g['quantity_sum'] > 0 ? ($g['delivered_sum'] / (float)$g['quantity_sum']) * 100 : 0;
                        $deliveryStatus = $deliveryPercentage >= 100 ? 'Complete' : ($deliveryPercentage > 0 ? 'Partial' : 'Pending');
                        $deliveryClass = $deliveryStatus === 'Complete' ? 'bg-green-100 text-green-800' : ($deliveryStatus === 'Partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
                        $deliveryIcon = $deliveryStatus === 'Complete' ? 'check-circle' : ($deliveryStatus === 'Partial' ? 'clock' : 'package');
                        ?>
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] md:text-xs lg:text-sm font-medium <?php echo $deliveryClass; ?>">
                                <i data-lucide="<?php echo $deliveryIcon; ?>" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                                <?php echo $deliveryStatus; ?>
                            </span>
                            <div class="text-[9px] md:text-[10px] lg:text-xs text-gray-500">
                                <?php echo number_format($g['delivered_sum'], 2); ?> / <?php echo number_format((float)$g['quantity_sum'], 2); ?> (base)
                            </div>
                        </div>
                    </td>
                    <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-[10px] md:text-xs lg:text-sm">
                        <?php if (Auth::role() !== 'Purchaser'): ?>
                            <a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-600 text-white text-[10px] md:text-xs lg:text-sm rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                <i data-lucide="truck" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                                Deliveries
                            </a>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-3 py-2 bg-gray-300 text-gray-500 text-[10px] md:text-xs lg:text-sm rounded-lg cursor-not-allowed" title="Not available for Purchaser role">
                                <i data-lucide="truck" class="w-3 h-3 md:w-3.5 md:h-3.5"></i>
                                Deliveries
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <!-- Hidden modal content for this group -->
                <tr class="hidden" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>" data-detail-row="true"><td colspan="9">
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
                        <div class="overflow-x-auto border rounded-lg w-full">
                            <table class="w-full text-sm">
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
		</div>
		<?php endif; ?>
	</div>
</div>

<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases/mark-paid" id="markPaidForm" class="hidden" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
    <input type="hidden" name="id" id="markPaidPurchaseId" value="0">
    <input type="file" name="receipt" id="markPaidReceiptInput" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf" class="hidden">
</form>


<style>
#purchaseForm {
  box-sizing: border-box;
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
}
#purchaseForm section,
#purchaseForm > div {
  box-sizing: border-box;
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
}
.mt-6.border.rounded-lg {
  max-width: 100%;
  overflow-x: hidden;
}
.mt-6.border.rounded-lg > div {
  max-width: 100%;
  overflow-x: hidden;
}
#purchaseList {
  width: 100%;
  table-layout: fixed;
  max-width: 100%;
}
#purchaseList td,
#purchaseList th {
  word-wrap: break-word;
  overflow-wrap: break-word;
  overflow: hidden;
}
#purchaseList td:first-child {
  word-break: break-word;
  overflow-wrap: anywhere;
}
</style>
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
  const totalCostModal = document.getElementById('totalCostModal');
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

  // Modal elements
  const batchModal = document.getElementById('batchModal');
  const openBatchModalBtn = document.getElementById('openBatchModalBtn');
  const closeBatchModalBtn = document.getElementById('closeBatchModalBtn');
  const cancelBatchModalBtn = document.getElementById('cancelBatchModalBtn');
  const batchModalForm = document.getElementById('batchModalForm');
  const itemsJsonModal = document.getElementById('itemsJsonModal');
  const purchaseTypeModal = document.getElementById('purchaseTypeModal');
  const paymentTypeModal = document.getElementById('paymentTypeModal');
  const paymentStatusHiddenModal = document.getElementById('paymentStatusHiddenModal');
  const cashFieldsModal = document.getElementById('cashFieldsModal');
  const baseAmountModal = document.getElementById('baseAmountModal');
  const totalCostReadonlyModal = document.getElementById('totalCostReadonlyModal');
  const changeReadonlyModal = document.getElementById('changeReadonlyModal');
  const receiptInputModal = document.getElementById('receiptUploadModal');
  const receiptSelectedModal = document.getElementById('receiptSelectedModal');
  const receiptFileNameModal = document.getElementById('receiptFileNameModal');
  const receiptFileSizeModal = document.getElementById('receiptFileSizeModal');
  const receiptClearBtnModal = document.getElementById('receiptClearBtnModal');
  const receiptErrorModal = document.getElementById('receiptErrorModal');
  const receiptDropzoneModal = document.getElementById('receiptDropzoneModal');
  const recordPurchaseBtnModal = document.getElementById('recordPurchaseBtnModal');

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

  // Modal receipt handlers
  function resetReceiptUiModal(){
    if (receiptSelectedModal) receiptSelectedModal.classList.add('hidden');
    if (receiptErrorModal){
      receiptErrorModal.textContent = '';
      receiptErrorModal.classList.add('hidden');
    }
    if (receiptDropzoneModal){
      receiptDropzoneModal.classList.remove('border-purple-400','bg-purple-50','border-red-300','bg-red-50');
    }
  }
  function showReceiptErrorModal(message){
    if (!receiptErrorModal) return;
    receiptErrorModal.textContent = message;
    receiptErrorModal.classList.remove('hidden');
    if (receiptDropzoneModal) receiptDropzoneModal.classList.add('border-red-300','bg-red-50');
    if (receiptInputModal) receiptInputModal.value = '';
    if (receiptSelectedModal) receiptSelectedModal.classList.add('hidden');
  }
  function handleReceiptSelectionModal(file){
    if (!file){ resetReceiptUiModal(); return; }
    if (!RECEIPT_ALLOWED.includes(file.type)){
      showReceiptErrorModal('Unsupported file type. Use JPG, PNG, WebP, HEIC or PDF.');
      return;
    }
    if (file.size > RECEIPT_MAX_BYTES){
      showReceiptErrorModal('File exceeds 10MB. Please compress or upload a PDF scan.');
      return;
    }
    if (receiptErrorModal) receiptErrorModal.classList.add('hidden');
    if (receiptSelectedModal) receiptSelectedModal.classList.remove('hidden');
    if (receiptFileNameModal) receiptFileNameModal.textContent = file.name;
    if (receiptFileSizeModal) receiptFileSizeModal.textContent = formatBytes(file.size);
    if (receiptDropzoneModal){
      receiptDropzoneModal.classList.remove('border-red-300','bg-red-50');
      receiptDropzoneModal.classList.add('border-purple-400','bg-purple-50');
    }
  }
  if (receiptInputModal){
    receiptInputModal.addEventListener('change', ()=>{
      const file = receiptInputModal.files && receiptInputModal.files[0] ? receiptInputModal.files[0] : null;
      if (!file){ resetReceiptUiModal(); return; }
      handleReceiptSelectionModal(file);
    });
  }
  if (receiptClearBtnModal){
    receiptClearBtnModal.addEventListener('click', (e)=>{
      e.preventDefault();
      if (receiptInputModal) receiptInputModal.value = '';
      resetReceiptUiModal();
    });
  }
  if (receiptDropzoneModal){
    ['dragover','dragleave','drop'].forEach(evt=>{
      receiptDropzoneModal.addEventListener(evt, (event)=>{
        event.preventDefault();
        event.stopPropagation();
        if (evt === 'dragover'){
          receiptDropzoneModal.classList.add('border-purple-400','bg-purple-50');
        } else if (evt === 'dragleave'){
          receiptDropzoneModal.classList.remove('border-purple-400','bg-purple-50');
        } else if (evt === 'drop'){
          receiptDropzoneModal.classList.remove('border-purple-400','bg-purple-50');
          const files = event.dataTransfer?.files;
          if (files && files.length > 0 && receiptInputModal){
            try {
              if (typeof DataTransfer !== 'undefined'){
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                receiptInputModal.files = dt.files;
                receiptInputModal.dispatchEvent(new Event('change', { bubbles: true }));
              } else {
                showReceiptErrorModal('Drag & drop is not supported in this browser. Click to select instead.');
              }
            } catch (err) {
              showReceiptErrorModal('Drag & drop is not supported in this browser. Click to select instead.');
            }
          }
        }
      });
    });
  }

  // Modal form submission
  if (batchModalForm){
    batchModalForm.addEventListener('submit', (event)=>{
      if (!receiptInputModal || !receiptInputModal.files || receiptInputModal.files.length === 0){
        event.preventDefault();
        showReceiptErrorModal('Please upload a receipt before recording the purchase batch.');
        showReceiptPopup('Upload the receipt image before recording this purchase batch.');
        return false;
      }
    });
  }

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
    const itemsJsonStr = JSON.stringify(items);
    if (itemsJson) itemsJson.value = itemsJsonStr;
    if (itemsJsonModal) itemsJsonModal.value = itemsJsonStr;
  }

  function recalcTotal(){
    let sum = 0;
    listBody.querySelectorAll('input[name="row_cost[]"]').forEach(inp=>{ sum += parseFloat(inp.value || '0'); });
    if (totalCostSpan) totalCostSpan.textContent = sum.toFixed(2);
    if (totalCostModal) totalCostModal.textContent = sum.toFixed(2);
    if (totalCostReadonly) totalCostReadonly.value = sum.toFixed(2);
    if (totalCostReadonlyModal) totalCostReadonlyModal.value = sum.toFixed(2);
    const base = parseFloat(baseAmount?.value || '0');
    const baseModal = parseFloat(baseAmountModal?.value || '0');
    const baseVal = baseModal || base;
    if (!isNaN(base) && changeReadonly){ const ch = base - sum; changeReadonly.value = ch.toFixed(2); }
    if (!isNaN(baseModal) && changeReadonlyModal){ const ch = baseModal - sum; changeReadonlyModal.value = ch.toFixed(2); }
    syncItemsJson();
    // Update button state
    const hasItems = listBody.querySelectorAll('tr[data-id]').length > 0;
    if (openBatchModalBtn){
      openBatchModalBtn.disabled = !hasItems;
    }
  }
  if (baseAmount){ baseAmount.addEventListener('input', recalcTotal); }
  if (baseAmountModal){ baseAmountModal.addEventListener('input', recalcTotal); }
  if (payType){
    const toggle = ()=>{ cashFields.classList.toggle('hidden', payType.value !== 'Cash'); recalcTotal(); };
    payType.addEventListener('change', toggle); toggle();
  }
  if (paymentTypeModal){
    const toggleModal = ()=>{ 
      if (cashFieldsModal) cashFieldsModal.classList.toggle('hidden', paymentTypeModal.value !== 'Cash'); 
      recalcTotal(); 
    };
    paymentTypeModal.addEventListener('change', toggleModal);
  }
  const syncPaymentStatus = ()=>{
    if (paymentStatusHidden){
      const typeValue = purchaseTypeSel ? purchaseTypeSel.value : 'in_store';
      paymentStatusHidden.value = typeValue === 'delivery' ? 'Pending' : 'Paid';
    }
    if (paymentStatusHiddenModal){
      const typeValue = purchaseTypeModal ? purchaseTypeModal.value : 'in_store';
      paymentStatusHiddenModal.value = typeValue === 'delivery' ? 'Pending' : 'Paid';
    }
  };
  if (purchaseTypeSel){
    purchaseTypeSel.addEventListener('change', syncPaymentStatus);
  }
  if (purchaseTypeModal){
    purchaseTypeModal.addEventListener('change', syncPaymentStatus);
  }
  syncPaymentStatus();

  // Modal handlers
  function openBatchModal(){
    if (!batchModal) return;
    const hasItems = listBody.querySelectorAll('tr[data-id]').length > 0;
    if (!hasItems){
      showReceiptPopup('Please add at least one item before recording the purchase batch.');
      return;
    }
    syncItemsJson();
    recalcTotal();
    batchModal.classList.remove('hidden');
    if (window.lucide?.createIcons){
      window.lucide.createIcons({ elements: batchModal.querySelectorAll('i[data-lucide]') });
    }
  }

  function closeBatchModal(){
    if (batchModal) batchModal.classList.add('hidden');
    if (receiptInputModal) receiptInputModal.value = '';
    if (receiptSelectedModal) receiptSelectedModal.classList.add('hidden');
    if (receiptErrorModal){
      receiptErrorModal.classList.add('hidden');
      receiptErrorModal.textContent = '';
    }
    if (receiptDropzoneModal){
      receiptDropzoneModal.classList.remove('border-purple-400','bg-purple-50','border-red-300','bg-red-50');
    }
  }

  if (openBatchModalBtn){
    openBatchModalBtn.addEventListener('click', openBatchModal);
  }
  if (closeBatchModalBtn){
    closeBatchModalBtn.addEventListener('click', closeBatchModal);
  }
  if (cancelBatchModalBtn){
    cancelBatchModalBtn.addEventListener('click', closeBatchModal);
  }
  if (batchModal){
    const modalContent = batchModal.querySelector('.bg-white');
    batchModal.addEventListener('click', (e)=>{
      // Close if clicking outside the white modal content
      if (modalContent && !modalContent.contains(e.target)) {
        closeBatchModal();
      }
    });
  }

  function addRow(itemId, name, baseUnit, baseQty, displayUnit, factor, rowCost, originalQty){
    // originalQty is the quantity as entered (before conversion)
    const displayQty = originalQty !== undefined ? originalQty : (baseQty / (factor || 1));
    
    // Remove empty state message if it exists
    const emptyState = listBody.querySelector('tr:not([data-id])');
    if (emptyState) emptyState.remove();
    
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
    tr.className = 'hover:bg-gray-50 transition-colors';
    tr.innerHTML = `
      <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 font-medium text-gray-900 text-[10px] md:text-xs lg:text-sm">${name}<input type="hidden" name="item_id[]" value="${itemId}"></td>
      <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 qtyDisp text-gray-700 text-[10px] md:text-xs lg:text-sm">${displayQty.toFixed(2)}<input type="hidden" name="quantity[]" value="${baseQty}"><input type="hidden" name="original_quantity[]" value="${displayQty}"></td>
      <td class="hidden lg:table-cell px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm">${displayUnit||baseUnit}<input type="hidden" name="unit[]" value="${displayUnit||baseUnit}"></td>
      <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 font-semibold text-gray-900 text-[10px] md:text-xs lg:text-sm">₱<span class="costDisp">${rowCost.toFixed(2)}</span><input type="hidden" name="row_cost[]" value="${rowCost.toFixed(2)}"></td>
      <td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4"><button type="button" class="removeRow text-red-600 hover:text-red-700 hover:underline text-[10px] md:text-xs lg:text-sm font-medium">Remove</button></td>
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

  // Remove item confirmation modal
  const removeItemModal = document.getElementById('removeItemModal');
  const cancelRemoveBtn = document.getElementById('cancelRemoveBtn');
  const confirmRemoveBtn = document.getElementById('confirmRemoveBtn');
  const removeItemName = document.getElementById('removeItemName');
  let pendingRemoveRow = null;

  function openRemoveModal(row){
    if (!removeItemModal || !removeItemName) return;
    const itemName = row.querySelector('td:first-child')?.textContent?.trim() || 'this item';
    removeItemName.textContent = itemName;
    pendingRemoveRow = row;
    removeItemModal.classList.remove('hidden');
    if (window.lucide?.createIcons){
      window.lucide.createIcons({ elements: removeItemModal.querySelectorAll('i[data-lucide]') });
    }
  }

  function closeRemoveModal(){
    if (removeItemModal) removeItemModal.classList.add('hidden');
    pendingRemoveRow = null;
  }

  function updateEmptyStateColspan(){
    const emptyStateRow = document.getElementById('emptyStateRow');
    if (emptyStateRow) {
      const td = emptyStateRow.querySelector('td');
      if (td) {
        // Check if we're on large screen (lg breakpoint = 1024px)
        td.setAttribute('colspan', window.innerWidth >= 1024 ? '5' : '4');
      }
    }
    // Also update any empty state rows created dynamically
    const emptyRows = listBody.querySelectorAll('tr:not([data-id])');
    emptyRows.forEach(row => {
      const td = row.querySelector('td');
      if (td && td.getAttribute('colspan')) {
        td.setAttribute('colspan', window.innerWidth >= 1024 ? '5' : '4');
      }
    });
  }

  function confirmRemoveItem(){
    if (!pendingRemoveRow) return;
    pendingRemoveRow.remove();
    recalcTotal();
    // Show empty state if no items left
    if (listBody.querySelectorAll('tr[data-id]').length === 0){
      listBody.innerHTML = `
        <tr id="emptyStateRow">
          <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">
            <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
            <p>No items added yet</p>
            <p class="text-xs mt-1">Add items from the form above</p>
          </td>
        </tr>
      `;
      updateEmptyStateColspan();
      if (window.lucide?.createIcons){
        window.lucide.createIcons({ elements: listBody.querySelectorAll('i[data-lucide]') });
      }
    }
    closeRemoveModal();
  }

  listBody.addEventListener('click', (e)=>{
    if (e.target.classList.contains('removeRow')){
      e.preventDefault();
      const row = e.target.closest('tr');
      if (row) openRemoveModal(row);
    }
  });

  if (cancelRemoveBtn){
    cancelRemoveBtn.addEventListener('click', closeRemoveModal);
  }
  if (confirmRemoveBtn){
    confirmRemoveBtn.addEventListener('click', confirmRemoveItem);
  }
  if (removeItemModal){
    removeItemModal.addEventListener('click', (e)=>{
      if (e.target === removeItemModal) closeRemoveModal();
    });
  }

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

  // Initialize button state
  recalcTotal();
  
  // Update empty state colspan on load and resize
  updateEmptyStateColspan();
  window.addEventListener('resize', updateEmptyStateColspan);
})();
</script>

