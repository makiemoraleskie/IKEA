<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<style>
	@keyframes fade-in {
		from {
			opacity: 0;
		}
		to {
			opacity: 1;
		}
	}
	.animate-fade-in {
		animation: fade-in 0.2s ease-out;
	}
	
	/* Performance optimizations */
	* {
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	/* Optimize scrolling performance with GPU acceleration */
	.overflow-x-auto,
	.overflow-y-auto {
		transform: translateZ(0);
		backface-visibility: hidden;
	}
	
	/* Mobile responsiveness fixes */
	@media (max-width: 640px) {
		/* Ensure tables scroll properly on mobile */
		.overflow-x-auto {
			-webkit-overflow-scrolling: touch;
			scrollbar-width: thin;
		}
		
		/* Prevent text from being too small */
		input, select, textarea {
			font-size: 16px !important; /* Prevents zoom on iOS */
		}
		
		/* Better spacing on mobile */
		body {
			overflow-x: hidden;
		}
	}
</style>
<!-- Page Header - Enhanced -->
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div class="space-y-1 md:space-y-2">
			<h1 class="text-2xl font-bold text-gray-900 tracking-tight">Purchase Transactions</h1>
			<p class="text-sm sm:text-base text-gray-600 font-medium">Record and manage ingredient purchases</p>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2.5 px-4 py-2.5 sm:px-5 sm:py-3 text-xs font-bold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-all duration-200 shadow-sm w-full sm:w-auto justify-center">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
	</div>
</div>

<?php if (!empty($flash)): ?>
	<div class="mb-6 md:mb-8 px-4 sm:px-5 py-3 sm:py-4 rounded-xl sm:rounded-2xl border-2 border-gray-300 shadow-lg <?php echo ($flash['type'] ?? '') === 'success' ? 'bg-gradient-to-r from-green-50/95 to-green-50/60 text-green-800' : 'bg-gradient-to-r from-red-50/95 to-red-50/60 text-red-800'; ?> animate-fade-in">
		<div class="flex items-start gap-2 sm:gap-3">
			<i data-lucide="<?php echo ($flash['type'] ?? '') === 'error' ? 'alert-circle' : 'check-circle'; ?>" class="w-4 h-4 sm:w-5 sm:h-5 mt-0.5 flex-shrink-0"></i>
			<p class="text-xs sm:text-sm font-semibold"><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
		</div>
	</div>
<?php endif; ?>

<?php if (!empty($lowStockGroups)): ?>
<div id="lowStockSection" class="bg-white rounded-2xl sm:rounded-3xl shadow-md border-2 border-gray-200/80 mb-6 md:mb-10 overflow-hidden">
	
	<div class="relative z-10">
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-[#A8E6CF]/10 px-4 sm:px-6 py-4 sm:py-6 border-b-2 border-gray-200/60 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
			<div class="flex items-center gap-3 sm:gap-4">
				<div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
					<i data-lucide="shopping-bag" class="w-6 h-6 sm:w-7 sm:h-7 text-white"></i>
				</div>
				<div class="min-w-0 flex-1">
					<h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Low Stock Purchase List</h2>
					<p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1 font-medium">Ingredients at or below their reorder levels, grouped by supplier.</p>
				</div>
			</div>
			<button type="button" id="printLowStockList" class="inline-flex items-center justify-center gap-2.5 px-4 py-2.5 sm:px-5 sm:py-3 text-sm font-bold bg-gradient-to-b from-[#00A86B] to-[#008000] text-white rounded-xl shadow-md hover:opacity-90 hover:shadow-lg w-full sm:w-auto">
				<i data-lucide="printer" class="w-4 h-4"></i>
				Print Purchase List
			</button>
		</div>
		<div class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
			<?php foreach ($lowStockGroups as $group): 
				$supplier = $group['label'] ?? 'Unassigned Supplier';
				$items = $group['items'] ?? [];
			?>
			<div class="mb-4 sm:mb-6">
			<div class="flex flex-col gap-2 sm:gap-1 sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4">
				<div class="min-w-0 flex-1">
					<p class="text-xs uppercase tracking-wide text-gray-500">Supplier</p>
					<h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($supplier); ?></h3>
				</div>
				<span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium whitespace-nowrap"><?php echo count($items); ?> item<?php echo count($items) === 1 ? '' : 's'; ?></span>
			</div>
			<div class="overflow-x-auto -mx-4 sm:mx-0">
				<table class="w-full text-xs sm:text-sm min-w-[520px]">
					<thead class="bg-gray-50">
						<tr>
							<th class="text-left px-3 sm:px-4 py-2 font-medium text-gray-700">Ingredient</th>
							<th class="text-left px-3 sm:px-4 py-2 font-medium text-gray-700">Status</th>
							<th class="text-left px-3 sm:px-4 py-2 font-medium text-gray-700 hidden sm:table-cell">On Hand</th>
							<th class="text-left px-3 sm:px-4 py-2 font-medium text-gray-700 hidden md:table-cell">Reorder Level</th>
							<th class="text-left px-3 sm:px-4 py-2 font-medium text-gray-700">Recommended Qty</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
						<?php foreach ($items as $item): ?>
						<tr>
							<td class="px-3 sm:px-4 py-2 font-semibold text-gray-900 text-xs sm:text-sm"><?php echo htmlspecialchars($item['name']); ?></td>
							<td class="px-3 sm:px-4 py-2">
								<span class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 rounded-full text-xs font-medium <?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200'; ?>">
									<i data-lucide="<?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'x' : 'alert-triangle'; ?>" class="w-3 h-3"></i>
									<span class="hidden sm:inline"><?php echo htmlspecialchars($item['stock_status'] ?? 'Low Stock'); ?></span>
									<span class="sm:hidden"><?php echo ($item['stock_status'] ?? '') === 'Out of Stock' ? 'Out' : 'Low'; ?></span>
								</span>
							</td>
							<td class="px-3 sm:px-4 py-2 text-gray-700 text-xs sm:text-sm hidden sm:table-cell"><?php echo number_format((float)$item['quantity'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
							<td class="px-3 sm:px-4 py-2 text-gray-700 text-xs sm:text-sm hidden md:table-cell"><?php echo number_format((float)$item['reorder_level'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
							<td class="px-3 sm:px-4 py-2 font-semibold text-gray-900 text-xs sm:text-sm"><?php echo number_format((float)$item['recommended_qty'], 2); ?> <?php echo htmlspecialchars($item['unit']); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>

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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 md:mb-10">
	<!-- Total Purchases -->
	<div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border-2 border-gray-200/80 p-4 sm:p-6">
		<div class="flex items-center justify-between">
			<div class="min-w-0 flex-1 pr-2">
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Total Purchases</p>
				<p class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight break-words"><?php echo count($purchases); ?></p>
			</div>
			<div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-gray-300 flex-shrink-0 ml-2">
				<i data-lucide="shopping-cart" class="w-5 h-5 sm:w-6 sm:h-6 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Pending Payments -->
	<div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border-2 border-gray-200/80 p-4 sm:p-6">
		<div class="flex items-center justify-between">
			<div class="min-w-0 flex-1 pr-2">
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Pending Payments</p>
				<p class="text-2xl sm:text-3xl font-black text-yellow-600 tracking-tight break-words"><?php echo $pendingCount; ?></p>
			</div>
			<div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-50 rounded-xl flex items-center justify-center border border-gray-300 flex-shrink-0 ml-2">
				<i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600"></i>
			</div>
		</div>
	</div>
	
	<!-- Paid Purchases -->
	<div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border-2 border-gray-200/80 p-4 sm:p-6">
		<div class="flex items-center justify-between">
			<div class="min-w-0 flex-1 pr-2">
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Paid Purchases</p>
				<p class="text-2xl sm:text-3xl font-black text-[#008000] tracking-tight break-words"><?php echo count($purchases) - $pendingCount; ?></p>
			</div>
			<div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-gray-300 flex-shrink-0 ml-2">
				<i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-[#008000]"></i>
			</div>
		</div>
	</div>
	
	<!-- Total Cost -->
	<div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border-2 border-gray-200/80 p-4 sm:p-6">
		<div class="flex items-center justify-between">
			<div class="min-w-0 flex-1 pr-2">
				<p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Total Cost</p>
				<p class="text-xl sm:text-2xl md:text-2xl font-black text-[#008000] tracking-tight break-words">₱<?php echo number_format(array_sum(array_column($purchases, 'cost')), 2); ?></p>
			</div>
			<div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#008000]/10 rounded-xl flex items-center justify-center border border-gray-300 flex-shrink-0 ml-2">
				<i data-lucide="dollar-sign" class="w-5 h-5 sm:w-6 sm:h-6 text-[#008000]"></i>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- New Batch Purchase Form -->
<?php 
$paymentFilter = strtolower((string)($_GET['payment'] ?? 'all'));
?>
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-md border-2 border-gray-200/80 mb-6 md:mb-10 overflow-hidden">
	<div>
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-[#A8E6CF]/10 px-4 sm:px-6 md:px-8 py-4 sm:py-6 border-b-2 border-gray-200/60">
			<div class="flex items-center gap-3 sm:gap-4">
				<div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
					<i data-lucide="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7 text-white"></i>
				</div>
				<div class="min-w-0 flex-1">
					<h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Record New Purchase (Batch)</h2>
					<p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1 font-medium">Search ingredient, set qty/unit/cost, add to list, then save</p>
				</div>
			</div>
		</div>
    
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/purchases" enctype="multipart/form-data" class="p-5 sm:p-6 md:p-8 lg:p-10" id="purchaseForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
        
        <div class="space-y-6 sm:space-y-8">
			<!-- Step 1: Add items and purchase details -->
			<section class="border-2 border-gray-200/80 rounded-xl sm:rounded-2xl p-5 sm:p-6 md:p-7 bg-white/80">
				<div class="mb-5 sm:mb-6">
					<span class="text-xs sm:text-sm uppercase tracking-wide text-gray-500 font-bold">Step 1</span>
					<div class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mt-2">Add Purchase Items & Details</div>
					<p class="text-xs sm:text-sm text-gray-600 mt-1.5">Search or select an ingredient, then enter quantity and cost. Fill in purchase details below.</p>
				</div>
				
				<!-- Add Items Section -->
				<div class="mb-6 sm:mb-8 pb-6 sm:pb-8 border-b-2 border-gray-200/60">
					<h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4">Add Items to Purchase</h3>
					<div class="space-y-4 sm:space-y-5">
						<!-- Search -->
						<div class="relative">
							<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Search Ingredient</label>
							<input id="ingSearch" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200" placeholder="Type to search ingredients..." autocomplete="off" />
							<input type="hidden" id="ingIdHidden" />
							<div id="ingResults" class="absolute z-10 mt-1 w-full bg-white border-2 border-gray-300 rounded-xl shadow-lg max-h-56 overflow-auto hidden"></div>
						</div>
						<!-- Or select -->
						<div>
							<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Or Select from List</label>
							<select id="ingSelect" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200">
								<option value="">Choose an ingredient...</option>
								<?php foreach ($ingredients as $ing): ?>
									<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>" data-dispunit="<?php echo htmlspecialchars($ing['display_unit'] ?? ''); ?>" data-dispfactor="<?php echo htmlspecialchars($ing['display_factor'] ?? 1); ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<!-- Quantity and Unit Row -->
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
							<!-- Qty -->
							<div>
								<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Quantity</label>
								<input id="qtyInput" type="number" step="0.01" min="0.01" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200" placeholder="0.00" />
							</div>
							<!-- Unit -->
							<div>
								<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Unit</label>
								<select id="unitSelect" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200"></select>
							</div>
						</div>
						<!-- Cost -->
						<div>
							<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Cost (₱)</label>
							<input id="costInput" type="number" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200" placeholder="0.00" />
						</div>
						<div class="pt-2">
							<button type="button" id="addRowBtn" class="inline-flex items-center gap-2.5 px-5 sm:px-6 md:px-8 py-3 sm:py-3.5 md:py-4 text-sm sm:text-base font-bold bg-gradient-to-b from-[#00A86B] to-[#008000] text-white rounded-xl shadow-md hover:opacity-90 hover:shadow-lg w-full sm:w-auto justify-center">
								<i data-lucide="plus" class="w-5 h-5"></i>
								Add to List
							</button>
						</div>
					</div>
				</div>
				
				<!-- Purchase Details Section -->
				<div>
					<h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4">Purchase Details</h3>
					<input type="hidden" name="items_json" id="itemsJson" value="[]">
					<div class="space-y-4 sm:space-y-5">
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">
							<div>
								<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Supplier</label>
								<input name="supplier" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200" placeholder="Enter supplier name" required />
							</div>
							<div>
								<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Purchase Type</label>
								<select name="purchase_type" id="purchaseType" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200">
									<option value="in_store">In-store purchase</option>
									<option value="delivery">Delivery</option>
								</select>
								<input type="hidden" name="payment_status" id="paymentStatusHidden" value="Paid">
							</div>
							<div>
								<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Payment Type</label>
								<select name="payment_type" id="paymentType" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200">
									<option value="Card">Card</option>
									<option value="Cash">Cash</option>
								</select>
							</div>
						</div>
						<div id="cashFields" class="hidden">
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
								<div>
									<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Base Amount (Cash)</label>
									<input type="number" step="0.01" min="0" name="base_amount" id="baseAmount" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gradient-to-br from-gray-50 to-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200" placeholder="0.00" />
								</div>
								<div>
									<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Total</label>
									<input type="text" id="totalCostReadonly" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gray-100 text-gray-700 font-semibold" value="0.00" readonly />
								</div>
								<div>
									<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Change</label>
									<input type="text" id="changeReadonly" class="w-full border-2 border-gray-300 rounded-xl px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base sm:text-lg bg-gray-100 text-gray-700 font-semibold" value="0.00" readonly />
								</div>
							</div>
						</div>
						<div>
							<label class="block text-sm sm:text-base font-bold text-gray-700 mb-2">Receipt Upload</label>
							<div class="border-2 border-dashed border-gray-300 rounded-xl p-5 sm:p-6 md:p-8 text-center" id="receiptDropzone">
								<input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf" class="hidden" id="receiptUpload" required />
								<label for="receiptUpload" class="cursor-pointer flex flex-col items-center gap-3">
									<i data-lucide="upload" class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 mx-auto"></i>
									<p class="text-sm sm:text-base text-gray-600 font-medium">Click to upload receipt (applies to batch)</p>
									<p class="text-xs sm:text-sm text-gray-500">JPG, PNG, WebP, HEIC or PDF — up to 10MB</p>
								</label>
								<div id="receiptSelected" class="mt-4 hidden text-left bg-gray-50 border-2 border-gray-200 rounded-lg px-4 py-3 flex items-center justify-between gap-3">
									<div class="min-w-0">
										<p id="receiptFileName" class="text-sm sm:text-base font-medium text-gray-800 truncate"></p>
										<p id="receiptFileSize" class="text-xs sm:text-sm text-gray-500"></p>
									</div>
									<button type="button" id="receiptClearBtn" class="text-sm text-red-600 hover:underline whitespace-nowrap font-semibold">Remove</button>
								</div>
								<p id="receiptError" class="mt-3 text-sm sm:text-base text-red-600 hidden font-medium"></p>
							</div>
						</div>
					</div>
				</div>
			</section>
			
			<!-- Step 2: Items in This Purchase -->
			<section class="border-2 border-gray-200/80 rounded-xl sm:rounded-2xl overflow-hidden bg-white/80">
				<div class="bg-gradient-to-r from-[#008000]/5 to-gray-50/50 px-5 sm:px-6 py-4 sm:py-5 border-b-2 border-gray-200/60">
					<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
						<div>
							<span class="text-xs sm:text-sm uppercase tracking-wide text-gray-500 font-bold">Step 2</span>
							<div class="font-bold text-base sm:text-lg text-gray-900 mt-1">Items in This Purchase</div>
						</div>
						<div class="text-base sm:text-lg md:text-xl font-bold text-[#008000]">Total: ₱<span id="totalCost">0.00</span></div>
					</div>
				</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-base sm:text-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-5 sm:px-6 py-4 font-bold text-gray-700">Ingredient</th>
                                <th class="text-left px-5 sm:px-6 py-4 font-bold text-gray-700">Qty</th>
                                <th class="text-left px-5 sm:px-6 py-4 font-bold text-gray-700">Unit</th>
                                <th class="text-left px-5 sm:px-6 py-4 font-bold text-gray-700">Cost</th>
                                <th class="text-left px-5 sm:px-6 py-4 font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseList" class="divide-y divide-gray-200"></tbody>
                    </table>
                    <div id="purchaseListEmpty" class="px-6 py-12 text-center text-gray-500" style="display: block;">
                        <i data-lucide="shopping-cart" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                        <p class="text-base sm:text-lg font-medium">No items added yet</p>
                        <p class="text-sm text-gray-400 mt-1">Add items from above</p>
                    </div>
                </div>
				<div class="p-5 sm:p-6 md:p-7 border-t-2 border-gray-200/60 flex justify-end">
					<button type="submit" id="recordPurchaseBtn" class="inline-flex items-center gap-2.5 px-6 sm:px-8 md:px-10 py-3.5 sm:py-4 md:py-4.5 text-base sm:text-lg font-bold bg-gradient-to-b from-[#00A86B] to-[#008000] text-white rounded-xl shadow-md hover:opacity-90 hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed w-full sm:w-auto justify-center">
						<i data-lucide="shopping-cart" class="w-5 h-5 sm:w-6 sm:h-6"></i>
						<span class="whitespace-nowrap">Record Purchase Batch</span>
					</button>
				</div>
			</section>
		</div>
	</form>
</div>
</div>

<!-- Recent Purchases Table (Grouped) -->
<div id="recent-purchases" class="bg-white rounded-3xl shadow-md border-2 border-gray-200/80 overflow-hidden">
	<div>
		<div class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-gray-50/50 px-4 sm:px-6 md:px-8 py-4 sm:py-6 border-b-2 border-gray-200/60">
			<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
				<div class="flex items-center gap-3 sm:gap-4">
					<div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-[#008000] to-[#00A86B] rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
						<i data-lucide="receipt" class="w-6 h-6 sm:w-7 sm:h-7 text-white"></i>
					</div>
					<div class="min-w-0 flex-1">
						<h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Recent Purchases</h2>
						<p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1 font-medium">View and manage all purchase transactions</p>
					</div>
				</div>
				<div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-4">
					<div class="flex flex-wrap items-center gap-2 sm:gap-4">
						<div class="text-xs sm:text-sm text-gray-600 font-medium">
							<span class="font-bold"><?php echo isset($purchaseGroups) ? count($purchaseGroups) : count($purchases); ?></span> total purchases
						</div>
						<?php if ($pendingCount > 0): ?>
							<div class="flex items-center gap-2 px-3 sm:px-4 py-1 sm:py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-xs sm:text-sm font-bold border-2 border-yellow-200">
								<i data-lucide="clock" class="w-3 h-3 sm:w-4 sm:h-4"></i>
								<?php echo $pendingCount; ?> pending
							</div>
						<?php endif; ?>
					</div>
					<div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 text-sm text-gray-600">
						<label for="paymentStatusFilter" class="whitespace-nowrap font-semibold text-xs sm:text-sm">Filter payment:</label>
						<select id="paymentStatusFilter" data-default="<?php echo htmlspecialchars($paymentFilter); ?>" class="border-2 border-gray-300 rounded-xl px-3 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-[#008000]/20 focus:border-[#008000] w-full sm:w-auto">
							<option value="all">All</option>
							<option value="paid">Paid</option>
							<option value="pending">Pending</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	
	<div class="overflow-x-auto -mx-4 sm:mx-0 rounded-2xl border-2 border-gray-200/60 shadow-sm bg-white">
        <table class="w-full text-xs sm:text-sm min-w-[600px]">
			<thead class="bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-gray-50/50 border-b-2 border-gray-200/60">
				<tr>
                    <th class="text-left px-4 sm:px-5 py-3 font-bold text-gray-800 whitespace-nowrap text-xs uppercase tracking-wider">
						<div class="flex items-center gap-2">
							<i data-lucide="hash" class="w-3.5 h-3.5 text-[#008000]"></i>
							<span>Batch</span>
						</div>
					</th>
                    <th class="text-left px-4 sm:px-5 py-3 font-bold text-gray-800 whitespace-nowrap text-xs uppercase tracking-wider">
						<div class="flex items-center gap-2">
							<i data-lucide="package" class="w-3.5 h-3.5 text-[#008000]"></i>
							<span>Items</span>
						</div>
					</th>
					<th class="text-left px-4 sm:px-5 py-3 font-bold text-gray-800 whitespace-nowrap text-xs uppercase tracking-wider">
						<div class="flex items-center gap-2">
							<i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-[#008000]"></i>
							<span>Cost</span>
						</div>
					</th>
					<th class="text-left px-4 sm:px-5 py-3 font-bold text-gray-800 whitespace-nowrap text-xs uppercase tracking-wider">
						<div class="flex items-center gap-2">
							<i data-lucide="credit-card" class="w-3.5 h-3.5 text-[#008000]"></i>
							<span>Payment</span>
						</div>
					</th>
					<th class="text-left px-4 sm:px-5 py-3 font-bold text-gray-800 whitespace-nowrap text-xs uppercase tracking-wider">
						<div class="flex items-center gap-2">
							<i data-lucide="settings" class="w-3.5 h-3.5 text-[#008000]"></i>
							<span>Actions</span>
						</div>
					</th>
				</tr>
			</thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach (($purchaseGroups ?? []) as $g): ?>
                <tr class="hover:bg-[#008000]/5 group" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>">
					<td class="px-4 sm:px-5 py-4">
						<div class="flex items-center gap-3">
							<span class="text-sm sm:text-base font-bold text-[#008000]">#<?php echo htmlspecialchars($g['group_id']); ?></span>
						</div>
					</td>
                    <td class="px-4 sm:px-5 py-4">
                        <?php $count = count($g['items']); ?>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
								<div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
									<i data-lucide="package" class="w-3.5 h-3.5 text-gray-600"></i>
								</div>
                                <span class="font-semibold text-gray-900 text-sm sm:text-base"><?php echo $count; ?> item<?php echo $count>1?'s':''; ?></span>
                            </div>
							<button type="button" class="inline-flex items-center gap-1.5 text-[#008000] font-semibold text-xs hover:text-[#00A86B] openPurchaseModal w-fit" data-group-id="<?php echo htmlspecialchars($g['group_id']); ?>">
								<i data-lucide="eye" class="w-3 h-3"></i>
								<span class="underline">View Details</span>
							</button>
                        </div>
                    </td>
                    <td class="px-4 sm:px-5 py-4">
                        <div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-[#008000]/10 rounded-lg flex items-center justify-center border border-[#008000]/20">
								<i data-lucide="dollar-sign" class="w-4 h-4 text-[#008000]"></i>
							</div>
							<div class="flex flex-col">
								<span class="text-lg sm:text-xl font-black text-[#008000] whitespace-nowrap leading-tight">₱<?php echo number_format((float)$g['cost_sum'], 2); ?></span>
							</div>
						</div>
                    </td>
                    <td class="px-4 sm:px-5 py-4">
                        <?php 
                        $paymentClass = $g['payment_status'] === 'Paid' ? 'text-green-700' : 'text-yellow-700';
                        $paymentIcon = $g['payment_status'] === 'Paid' ? 'check-circle' : 'clock';
                        ?>
                        <div class="flex flex-col gap-1.5">
                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold whitespace-nowrap <?php echo $paymentClass; ?>">
                                <i data-lucide="<?php echo $paymentIcon; ?>" class="w-3.5 h-3.5"></i>
                                <span><?php echo htmlspecialchars($g['payment_status']); ?></span>
                            </span>
                            <?php if (!empty($g['paid_at'])): ?>
                                <div class="flex items-center gap-1.5 text-xs text-gray-600">
									<i data-lucide="calendar" class="w-3 h-3"></i>
									<span class="whitespace-nowrap"><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($g['paid_at']))); ?></span>
								</div>
                            <?php endif; ?>
                        </div>
                    </td>
					<td class="px-4 sm:px-5 py-4">
						<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white text-xs sm:text-sm font-bold rounded-lg shadow-md hover:opacity-90 hover:shadow-lg whitespace-nowrap">
							<i data-lucide="truck" class="w-3.5 h-3.5"></i>
							<span>Deliveries</span>
						</a>
					</td>
                </tr>
                <!-- Hidden modal content for this group -->
                <tr class="hidden" data-payment-status="<?php echo strtolower($g['payment_status'] ?? ''); ?>" data-detail-row="true"><td colspan="5">
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
                                            $baseUnit = $p['unit'];
                                            $dispUnit = $p['display_unit'] ?: ($baseUnit==='g'?'kg':($baseUnit==='ml'?'L':$baseUnit));
                                            $dispFactor = (float)($p['display_factor'] ?: ($dispUnit!==$baseUnit?1000:1));
                                            $qtyShow = $dispFactor>0 ? (float)$p['quantity']/$dispFactor : (float)$p['quantity'];
                                        ?>
                                        <tr class="border-t">
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($p['item_name']); ?></td>
                                            <td class="px-4 py-2"><?php echo number_format($qtyShow,2); ?></td>
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($dispUnit); ?></td>
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
                                        <a href="<?php echo htmlspecialchars($fullUrl); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-[#008000]/10 text-[#008000] rounded-lg text-xs font-bold border-2 border-[#008000]/20 hover:bg-[#008000]/20">
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
                                <a href="<?php echo htmlspecialchars($fullUrl2); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-[#008000]/10 text-[#008000] rounded-lg text-xs font-bold border-2 border-[#008000]/20 hover:bg-[#008000]/20">
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
                                    class="markPaidBtn inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
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
        <div id="purchasesFilterEmpty" class="hidden px-6 py-12 text-center border-t-2 border-gray-200/60">
			<div class="flex flex-col items-center justify-center">
				<div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl flex items-center justify-center mb-4 border-2 border-gray-200">
					<i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
				</div>
				<p class="text-sm sm:text-base font-semibold text-gray-700 mb-1">No purchases match the selected filter</p>
				<p class="text-xs sm:text-sm text-gray-500">Try selecting a different payment status</p>
			</div>
        </div>
		
		<?php if (empty($purchases)): ?>
		<div class="flex flex-col items-center justify-center py-16 sm:py-20 px-4">
			<div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-[#008000]/10 to-[#008000]/5 rounded-2xl flex items-center justify-center mb-6 border-2 border-[#008000]/20 shadow-sm">
				<i data-lucide="shopping-cart" class="w-10 h-10 sm:w-12 sm:h-12 text-[#008000]"></i>
			</div>
			<h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 text-center">No Purchases Found</h3>
			<p class="text-sm sm:text-base text-gray-600 font-medium text-center max-w-md">Start by recording your first purchase transaction using the form above</p>
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
const lowStockSection = document.getElementById('lowStockSection');
const printLowStockBtn = document.getElementById('printLowStockList');
if (lowStockSection && printLowStockBtn){
	const printContent = () => {
		const popup = window.open('', '_blank', 'width=900,height=700');
		if (!popup) { return; }
		popup.document.write(`<html><head><title>Low Stock Purchase List</title>
			<style>
				body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
				h2, h3 { margin: 0 0 12px; }
				table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
				th, td { border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 13px; }
				th { background: #f3f4f6; text-align: left; }
				section { margin-bottom: 24px; }
			</style>
		</head><body>${lowStockSection.innerHTML}</body></html>`);
		popup.document.close();
		popup.focus();
		popup.print();
	};
	printLowStockBtn.addEventListener('click', printContent);
}

const INGREDIENTS = <?php echo json_encode(array_map(function($i){ return ['id'=>(int)$i['id'],'name'=>$i['name'],'unit'=>$i['unit'],'display_unit'=>$i['display_unit'] ?? '', 'display_factor'=>(float)($i['display_factor'] ?? 1)]; }, $ingredients), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  const search = document.getElementById('ingSearch');
  const results = document.getElementById('ingResults');
  const hiddenId = document.getElementById('ingIdHidden');
  const select = document.getElementById('ingSelect');
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
  const allRows = document.querySelectorAll('tr[data-payment-status]');
  const purchaseRows = [];
  for (let i = 0; i < allRows.length; i++) {
    if (!allRows[i].hasAttribute('data-detail-row')) {
      purchaseRows.push(allRows[i]);
    }
  }
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
      popup.className = 'fixed inset-x-0 top-6 z-[9999] flex justify-center px-4 opacity-0 pointer-events-none';
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
    receiptDropzone?.scrollIntoView({ block: 'center' });
    return false;
  }

  setRecordBtnReady(false);

  function renderResults(items){
    if (!items.length){ 
      results.classList.add('hidden'); 
      results.innerHTML=''; 
      return; 
    }
    const fragment = document.createDocumentFragment();
    items.forEach(i => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.setAttribute('data-id', i.id);
      btn.className = 'w-full text-left px-3 py-2 hover:bg-gray-100';
      btn.innerHTML = `${i.name} <span class="text-xs text-gray-500">(${i.unit})</span>`;
      fragment.appendChild(btn);
    });
    results.innerHTML = '';
    results.appendChild(fragment);
    results.classList.remove('hidden');
  }

  let searchTimeout = null;
  search.addEventListener('input', ()=>{
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const q = search.value.trim().toLowerCase();
      if (!q){ hiddenId.value=''; results.classList.add('hidden'); return; }
      const matches = INGREDIENTS.filter(i => i.name.toLowerCase().includes(q)).slice(0, 20);
      renderResults(matches);
    }, 150);
  });
  results.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-id]');
    if (!btn) return;
    const id = parseInt(btn.getAttribute('data-id'), 10);
    const item = INGREDIENTS.find(x => x.id === id);
    if (!item) return;
    hiddenId.value = String(item.id);
    search.value = item.name;
    configureUnits(item.unit);
    results.classList.add('hidden');
  });
  document.addEventListener('click', (e)=>{ if (!results.contains(e.target) && e.target !== search){ results.classList.add('hidden'); } });

  function configureUnits(baseUnit){
    unitSel.innerHTML = '';
    const add=(v,t)=>{ const o=document.createElement('option'); o.value=v; o.textContent=t; unitSel.appendChild(o); };
    if (baseUnit === 'g'){ add('g','g'); add('kg','kg'); }
    else if (baseUnit === 'ml'){ add('ml','ml'); add('L','L'); }
    else { add(baseUnit || '', baseUnit || ''); }
  }
  function applyPaymentFilter(value){
    if (!purchaseRows.length) { return; }
    const normalized = value && value !== 'all' ? value.toLowerCase() : 'all';
    let visible = 0;
    for (let i = 0; i < purchaseRows.length; i++) {
      const row = purchaseRows[i];
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
    }
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
    receiptDropzone?.classList.remove('border-[#008000]','bg-[#008000]/5','border-red-300','bg-red-50');
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
    receiptDropzone?.classList.add('border-[#008000]','bg-[#008000]/5');
    setRecordBtnReady(true);
  }
  select.addEventListener('change', ()=>{
    hiddenId.value = '';
    const opt = select.selectedOptions[0];
    const baseUnit = opt ? (opt.getAttribute('data-unit') || '') : '';
    search.value = opt ? (opt.textContent || '') : '';
    configureUnits(baseUnit);
  });
  // initial
  configureUnits('');
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
          receiptDropzone.classList.add('border-[#008000]','bg-[#008000]/5');
        } else if (evt === 'dragleave'){
          receiptDropzone.classList.remove('border-[#008000]','bg-[#008000]/5');
        } else if (evt === 'drop'){
          receiptDropzone.classList.remove('border-[#008000]','bg-[#008000]/5');
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
    const rows = listBody.querySelectorAll('tr[data-id]');
    for (let i = 0; i < rows.length; i++) {
      const tr = rows[i];
      const itemId = parseInt(tr.getAttribute('data-id')||'0',10) || 0;
      const qtyInput = tr.querySelector('input[name="quantity[]"]');
      const costInput = tr.querySelector('input[name="row_cost[]"]');
      const qty = parseFloat(qtyInput?.value || '0') || 0;
      const cost = parseFloat(costInput?.value || '0') || 0;
      if (itemId>0 && qty>0) items.push({ item_id:itemId, quantity:qty, cost:cost });
    }
    if (itemsJson) itemsJson.value = JSON.stringify(items);
  }

  let recalcTimeout = null;
  function recalcTotal(){
    clearTimeout(recalcTimeout);
    recalcTimeout = setTimeout(() => {
      let sum = 0;
      const costInputs = listBody.querySelectorAll('input[name="row_cost[]"]');
      for (let i = 0; i < costInputs.length; i++) {
        sum += parseFloat(costInputs[i].value || '0');
      }
      const total = sum.toFixed(2);
      totalCostSpan.textContent = total;
      if (totalCostReadonly) totalCostReadonly.value = total;
      const base = parseFloat(baseAmount?.value || '0');
      if (!isNaN(base) && changeReadonly){ const ch = base - sum; changeReadonly.value = ch.toFixed(2); }
      syncItemsJson();
    }, 50);
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

  function addRow(itemId, name, baseUnit, baseQty, displayUnit, displayFactor, rowCost){
    // Merge by itemId
    const existing = listBody.querySelector(`tr[data-id="${itemId}"]`);
    if (existing){
      const qHidden = existing.querySelector('input[name="quantity[]"]');
      const cHidden = existing.querySelector('input[name="row_cost[]"]');
      const newBase = (parseFloat(qHidden.value||'0') + baseQty);
      qHidden.value = newBase;
      // update display qty using factor (prefer provided)
      const factor = parseFloat(existing.getAttribute('data-factor')||'1');
      existing.querySelector('.qtyDisp').textContent = (newBase / (factor||1)).toFixed(2);
      // sum cost
      const newCost = (parseFloat(cHidden.value||'0') + rowCost);
      cHidden.value = newCost.toFixed(2);
      existing.querySelector('.costDisp').textContent = newCost.toFixed(2);
      recalcTotal();
      // Hide empty state when items exist
      const emptyState = document.getElementById('purchaseListEmpty');
      if (emptyState) emptyState.style.display = 'none';
      return;
    }
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', String(itemId));
    tr.setAttribute('data-factor', String(displayFactor||1));
    tr.innerHTML = `
      <td class="px-5 sm:px-6 py-4 font-semibold text-base text-gray-900">${name}<input type="hidden" name="item_id[]" value="${itemId}"></td>
      <td class="px-5 sm:px-6 py-4 qtyDisp text-base text-gray-700">${(baseQty/(displayFactor||1)).toFixed(2)}<input type="hidden" name="quantity[]" value="${baseQty}"></td>
      <td class="px-5 sm:px-6 py-4 text-base text-gray-600">${displayUnit||baseUnit}</td>
      <td class="px-5 sm:px-6 py-4 font-bold text-base text-gray-900">₱ <span class="costDisp">${rowCost.toFixed(2)}</span><input type="hidden" name="row_cost[]" value="${rowCost.toFixed(2)}"></td>
      <td class="px-5 sm:px-6 py-4"><button type="button" class="removeRow text-red-600 hover:text-red-700 font-bold text-base">Remove</button></td>
    `;
    // Hide empty state when items are added
    const emptyState = document.getElementById('purchaseListEmpty');
    if (emptyState) emptyState.style.display = 'none';
    listBody.appendChild(tr);
    recalcTotal();
  }

  addBtn.addEventListener('click', ()=>{
    let itemId = parseInt(hiddenId.value || '0', 10);
    let name = search.value || '';
    if (!itemId){
      const selId = parseInt(select.value || '0', 10);
      if (selId){ itemId = selId; name = select.selectedOptions[0]?.textContent || name; }
    }
    const quantity = parseFloat(qty.value || '0');
    const rowCost = parseFloat(cost.value || '0');
    if (!itemId || !quantity || quantity <= 0 || isNaN(rowCost)) return;
    const ingr = INGREDIENTS.find(x=>x.id===itemId);
    const baseUnit = ingr?.unit || '';
    const dispUnit = ingr?.display_unit || '';
    const dispFactor = parseFloat(String(ingr?.display_factor || '1')) || 1;
    const selUnit = unitSel.value || baseUnit;
    let factor = 1;
    if (dispUnit && selUnit === dispUnit) factor = dispFactor; else if ((baseUnit==='g' && selUnit==='kg')||(baseUnit==='ml'&& selUnit==='L')) factor=1000;
    const baseQty = quantity * factor;
    const showUnit = selUnit || baseUnit;
    addRow(itemId, name, baseUnit, baseQty, showUnit, factor, rowCost);
    // clear inputs
    qty.value=''; cost.value=''; hiddenId.value=''; search.value=''; select.value=''; unitSel.innerHTML='';
  });

  listBody.addEventListener('click', (e)=>{
    if (e.target.classList.contains('removeRow')){
      e.target.closest('tr').remove();
      recalcTotal();
      // Show empty state if no items left
      const emptyState = document.getElementById('purchaseListEmpty');
      if (emptyState && listBody.children.length === 0) {
        emptyState.style.display = 'block';
      }
    }
  });

  // Modal logic - use event delegation for better performance
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.openPurchaseModal');
    if (!btn) return;
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
})();
</script>
