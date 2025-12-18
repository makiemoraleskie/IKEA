<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : ''; 
$statusFilter = strtolower((string)($_GET['status'] ?? 'all'));
$ingredientSets = $ingredientSets ?? [];
$availableSets = array_values(array_filter($ingredientSets, static fn($set) => !empty($set['is_available'])));
$availableSetsCount = count($availableSets);
$ingredientStockMap = $ingredientStock ?? [];

// Helper function to format date from YYYY-MM-DD to "Month Day, Year"
function formatDate($dateString) {
	if (empty($dateString)) return '—';
	$date = substr($dateString, 0, 10);
	if (strlen($date) !== 10) return $dateString;
	try {
		$timestamp = strtotime($date);
		if ($timestamp === false) return $dateString;
		return date('F j, Y', $timestamp);
	} catch (Exception $e) {
		return $dateString;
	}
}
?>
<style>
/* Slightly larger typography and controls inside Prepare modal for readability */
#prepareModal .prepare-modal-card .text-\[9px\],
#prepareModal .prepare-modal-card .text-\[10px\],
#prepareModal .prepare-modal-card .text-\[12px\],
#prepareModal .prepare-modal-card .text-xs { font-size: 13px !important; }
#prepareModal .prepare-modal-card .text-sm { font-size: 14px !important; }
#prepareModal .prepare-modal-card input,
#prepareModal .prepare-modal-card select,
#prepareModal .prepare-modal-card textarea { font-size: 14px !important; }
#prepareModal .prepare-modal-card button { font-size: 14px !important; padding-top: 0.65rem; padding-bottom: 0.65rem; }

/* Remove focus ring/border highlight on the status filter */
#requestStatusFilter:focus {
	outline: none !important;
	box-shadow: none !important;
	border-color: inherit !important;
}

/* Keep table width stable while vertical scrollbar appears/disappears */
.table-scroll-stable {
	scrollbar-gutter: stable;
}

/* Larger typography and controls inside Edit Request modal */
#editRequestModal .edit-modal-card .text-\[9px\],
#editRequestModal .edit-modal-card .text-\[10px\],
#editRequestModal .edit-modal-card .text-xs { font-size: 13px !important; }
#editRequestModal .edit-modal-card .text-sm,
#editRequestModal .edit-modal-card .text-base { font-size: 14px !important; }
#editRequestModal .edit-modal-card input,
#editRequestModal .edit-modal-card textarea { font-size: 14px !important; padding: 0.75rem 1rem !important; }
#editRequestModal .edit-modal-card button { font-size: 14px !important; padding: 0.75rem 1.1rem !important; }

/* Hide scrollbars for modals */
#newRequestModal,
#editRequestModal,
#prepareModal,
#distributeConfirmModal {
  overflow: hidden !important;
}

#newRequestModal .bg-white,
#editRequestModal .bg-white,
#prepareModal .bg-white,
#distributeConfirmModal .bg-white {
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}

#newRequestModal .bg-white::-webkit-scrollbar,
#editRequestModal .bg-white::-webkit-scrollbar,
#prepareModal .bg-white::-webkit-scrollbar,
#distributeConfirmModal .bg-white::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

/* Ensure modals are centered vertically */
#newRequestModal > div:last-child,
#editRequestModal > div:last-child,
#prepareModal > div:last-child,
#distributeConfirmModal > div:last-child {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: absolute !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
}

#newRequestModal > div:last-child > div,
#editRequestModal > div:last-child > div,
#prepareModal > div:last-child > div,
#distributeConfirmModal > div:last-child > div {
  margin-top: auto !important;
  margin-bottom: auto !important;
}
</style>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Ingredient Requests</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Manage ingredient requests and batch approvals</p>
		</div>
	</div>
</div>

<?php if (!empty($flash) && !empty($flash['messages'])): ?>
	<?php 
		$isSuccess = ($flash['type'] ?? '') === 'success';
		$alertClasses = $isSuccess 
			? 'bg-green-50 border border-green-200 text-green-800' 
			: 'bg-red-50 border border-red-200 text-red-800';
	?>
	<div class="mb-4 md:mb-6 rounded-xl px-4 py-3 text-sm <?php echo $alertClasses; ?>">
		<div class="font-semibold mb-1 flex items-center gap-2">
			<i data-lucide="<?php echo $isSuccess ? 'check-circle' : 'alert-triangle'; ?>" class="w-4 h-4"></i>
			<span><?php echo $isSuccess ? 'Success' : 'Please review'; ?></span>
		</div>
		<ul class="list-disc list-inside space-y-1 text-[13px] md:text-sm">
			<?php foreach ((array)$flash['messages'] as $msg): ?>
				<li><?php echo htmlspecialchars((string)$msg); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner','Stock Handler'], true)): ?>
<!-- New Request Button -->
<div class="mb-4 md:mb-8">
	<button type="button" id="newRequestBtn" class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
		<i data-lucide="plus-circle" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
		New Batch Request
	</button>
</div>

<!-- New Request Modal -->
<div id="newRequestModal" class="fixed inset-0 z-[99999] hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 99999 !important;">
	<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
	<div class="relative z-10 flex items-center justify-center p-4" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;">
		<div class="bg-white rounded-xl shadow-none w-full max-w-4xl mx-auto my-auto" style="max-width: 56rem; margin-top: auto !important; margin-bottom: auto !important;">
		<div class="p-6">
			<div class="flex items-center justify-between mb-4">
				<div>
					<h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="plus-circle" class="w-5 h-5 md:w-5 md:h-5 text-green-600"></i>
						New Batch Request
					</h2>
					<p class="text-sm md:text-base text-gray-600 mt-1">Describe what you need and when it's required.</p>
				</div>
				<button type="button" id="closeNewRequestModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Close">
					<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
				</button>
			</div>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" class="space-y-5 md:space-y-6 w-full overflow-x-hidden">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
				<div class="space-y-1">
					<label class="block text-sm md:text-base font-medium text-gray-700">Requester Name</label>
					<input name="requester_name" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-gray-500 md:focus:ring-0 focus:outline-none" placeholder="e.g., Juan Dela Cruz" required>
				</div>
				<div class="space-y-1">
					<label class="block text-sm md:text-base font-medium text-gray-700">Date Needed</label>
					<input type="date" name="request_date" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-gray-500 md:focus:ring-0 focus:outline-none" required>
				</div>
			</div>
			<div class="space-y-1">
				<label class="block text-sm md:text-base font-medium text-gray-700">Ingredients / Items</label>
				<textarea name="ingredients_note" rows="4" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-gray-500 md:focus:ring-0 focus:outline-none" placeholder="List ingredients, quantities, or any prep instructions" required></textarea>
				<p class="text-xs md:text-sm text-gray-500">Detailed quantities will be captured later during the Prepare step.</p>
			</div>
			<div class="flex justify-end gap-2">
				<button type="button" id="cancelNewRequestBtn" class="inline-flex items-center justify-center px-3.5 md:px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base">
					Cancel
				</button>
					<button type="submit" class="inline-flex items-center gap-1 bg-green-600 text-white px-3.5 md:px-4 py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 md:focus:ring-0 md:focus:ring-offset-0 transition-colors text-sm md:text-base">
					<i data-lucide="send" class="w-5 h-5 md:w-5 md:h-5"></i>
					Submit Request
				</button>
			</div>
		</form>
		</div>
	</div>
	</div>
</div>
<?php endif; ?>

<?php if (Auth::role() !== 'Kitchen Staff'): ?>
<!-- Batch Requests Table -->
<div id="requests-history" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden max-w-full w-full">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
        <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-600"></i>
                    Batch Requests History
                </h2>
                <p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">View and manage all Ingredient/Items requests</p>
            </div>
            <div class="flex flex-col md:flex-col lg:flex-row lg:items-center lg:flex-wrap gap-2 md:gap-3 text-xs md:text-sm text-gray-600 w-full">
                <div class="flex flex-col md:flex-col lg:flex-row lg:items-center gap-1.5 md:gap-2 w-full lg:w-auto">
                    <label for="requestStatusFilter" class="whitespace-nowrap">Status:</label>
                    <select id="requestStatusFilter" data-default="<?php echo htmlspecialchars($statusFilter); ?>" class="w-full lg:w-48 border border-gray-300 rounded-lg px-2 md:px-2.5 py-1 md:py-1.5 text-[11px] md:text-xs">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
						<option value="to prepare">To Prepare</option>
						<option value="pending confirmation">Pending Confirmation</option>
						<option value="distributed">Distributed</option>
						<option value="received">Received</option>
                    </select>
                </div>
                <div class="flex flex-col md:flex-col lg:flex-row lg:items-center gap-1.5 md:gap-2 w-full lg:w-auto">
                    <label class="whitespace-nowrap">Date:</label>
                    <div class="flex flex-col md:flex-col lg:flex-row lg:items-center gap-1.5 md:gap-2 w-full">
                        <input type="date" id="requestDateFrom" class="border border-gray-300 rounded-lg px-2 md:px-2.5 py-1 md:py-1.5 text-[11px] md:text-xs w-full lg:w-44">
                        <span class="text-gray-400 text-[11px] md:text-xs lg:inline hidden">–</span>
                        <input type="date" id="requestDateTo" class="border border-gray-300 rounded-lg px-2 md:px-2.5 py-1 md:py-1.5 text-[11px] md:text-xs w-full lg:w-44">
                    </div>
                </div>
            </div>
        </div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px] table-scroll-stable">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-green-100 text-[10px] md:text-xs lg:text-sm">Requester</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-green-100 text-[10px] md:text-xs lg:text-sm">Details</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-green-100 text-[10px] md:text-xs lg:text-sm">Date Needed</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-green-100 text-[10px] md:text-xs lg:text-sm">Status</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<?php 
				$dateNeededRaw = !empty($b['custom_request_date'])
					? substr((string)$b['custom_request_date'], 0, 10)
					: substr((string)($b['date_requested'] ?? ''), 0, 10);
				?>
				<tr class="transition-colors" data-status="<?php echo strtolower($b['status'] ?? ''); ?>" data-detail-id="batch-<?php echo (int)$b['id']; ?>" data-date-needed="<?php echo htmlspecialchars($dateNeededRaw); ?>">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<div>
							<p class="text-[10px] md:text-xs lg:text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></p>
							<p class="text-[9px] md:text-[10px] lg:text-xs text-gray-500">Created by <?php echo htmlspecialchars($b['staff_name'] ?? ''); ?></p>
						</div>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
                        <button type="button" class="text-green-600 hover:text-green-700 text-[10px] md:text-xs lg:text-sm font-semibold viewBatchDetails" data-batch="<?php echo (int)$b['id']; ?>" data-status="<?php echo htmlspecialchars($b['status'] ?? ''); ?>">View</button>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm">
						<?php 
						echo htmlspecialchars(formatDate($dateNeededRaw)); 
						?>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<?php 
						// Get raw status and normalize it
						$rawStatus = $b['status'] ?? '';
						$rawStatus = trim((string)$rawStatus);
						$status = strtolower($rawStatus);
						
						// For stock handlers/owners: show "Distributed" instead of "Received"
						$currentUserRole = Auth::role();
						$isStockHandler = in_array($currentUserRole, ['Owner', 'Manager', 'Stock Handler'], true);
						
						// Map status to display text - handle all possible status values
						$statusText = match($status) {
							'distributed' => 'Distributed',
							'pending confirmation' => 'Pending Confirmation',
							'received' => $isStockHandler ? 'Distributed' : 'Received', // Stock handlers see "Distributed", requester sees "Received"
							'to prepare', 'preparing' => 'Preparing',
							'pending' => 'Pending',
							'rejected' => 'Rejected',
							'approved' => 'Approved',
							'' => 'No Status',
							default => $rawStatus ? htmlspecialchars($rawStatus) : 'Unknown'
						};
						
						// Map status to CSS classes
						$statusClass = match($status) {
							'distributed' => 'bg-green-100 text-green-800 border-green-200',
							'pending confirmation' => 'bg-blue-100 text-blue-800 border-blue-200',
							'received' => 'bg-green-100 text-green-800 border-green-200', // Same styling for both "Received" and "Distributed"
							'to prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
							'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
							'rejected' => 'bg-red-100 text-red-800 border-red-200',
							'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
							default => 'bg-gray-100 text-gray-700 border-gray-200'
						};
						?>
						<span class="inline-flex items-center px-2 md:px-2.5 lg:px-3 py-1 md:py-1.5 rounded-lg text-[9px] md:text-[10px] lg:text-xs font-semibold border whitespace-nowrap <?php echo $statusClass; ?>">
							<?php echo htmlspecialchars($statusText); ?>
						</span>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden" data-detail-for="<?php echo (int)$b['id']; ?>">
					<td colspan="4" class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6">
						<div class="batch-detail-card bg-white rounded-2xl border border-gray-200 p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5 lg:space-y-6">
							<!-- Batch Header -->
							<div class="flex flex-col sm:flex-row items-start sm:items-start justify-between gap-3 md:gap-4 pb-4 border-b border-gray-200">
								<div class="flex-1">
									<?php if (!empty($b['custom_requester'])): ?>
									<p class="text-sm text-gray-600 mb-2">Request Name: <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester']); ?></span></p>
									<?php endif; ?>
									<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-4 request-info-section">
										<div>
											<p class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">REQUESTED BY</p>
											<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></p>
										</div>
										<div>
											<p class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">DATE REQUESTED</p>
											<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars(formatDate($b['date_requested'])); ?></p>
										</div>
									</div>
								</div>
								<?php 
								// For stock handlers/owners: show "Distributed" instead of "Received"
								$currentUserRole = Auth::role();
								$isStockHandler = in_array($currentUserRole, ['Owner', 'Manager', 'Stock Handler'], true);
								$displayStatus = ($b['status'] === 'Received' && $isStockHandler) ? 'Distributed' : $b['status'];
								
								$statusColor = match($displayStatus) {
									'Distributed' => 'bg-green-100 text-green-800 border-green-200',
									'Pending Confirmation' => 'bg-blue-100 text-blue-800 border-blue-200',
									'Received' => 'bg-green-100 text-green-800 border-green-200',
									'Rejected' => 'bg-red-100 text-red-800 border-red-200',
									'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
									'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
									default => 'bg-gray-100 text-gray-700 border-gray-200'
								};
								?>
								<span class="inline-flex items-center gap-1.5 px-2.5 md:px-3 py-1.5 rounded-lg text-xs font-semibold border <?php echo $statusColor; ?> shrink-0 whitespace-nowrap">
									<i data-lucide="circle" class="w-2 h-2 fill-current"></i>
									<?php echo htmlspecialchars($displayStatus); ?>
								</span>
							</div>

							<!-- Requested Items Section -->
							<?php if (!empty($b['custom_ingredients'])): ?>
							<p class="text-xs font-semibold tracking-wide text-gray-500 mb-2 mt-3 md:mb-3">Requested Ingredients/Items</p>
							<div class="p-3 md:p-4 border border-gray-200 rounded-lg">
								<ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
									<?php 
									$ingredientLines = explode("\n", trim($b['custom_ingredients']));
									foreach ($ingredientLines as $line): 
										$line = trim($line);
										if (empty($line)) continue;
									?>
									<li class="text-sm text-gray-700 flex items-start gap-2">
										<span class="text-gray-500 mt-0.5">•</span>
										<span class="flex-1"><?php echo htmlspecialchars($line); ?></span>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- Items In Batch Section -->
							<?php 
							if (!empty($items) && Auth::role() !== 'Kitchen Staff'): 
								$statusLower = strtolower($b['status'] ?? '');
								$isDistributed = in_array($statusLower, ['distributed', 'pending confirmation', 'received'], true);
								$batchId = (int)$b['id'];
							?>
							<div class="space-y-3 md:space-y-4 pt-2 border-t border-gray-100">
								<div class="flex items-center gap-1.5 md:gap-2">
									<h3 class="text-semibold md:text-sm text-gray-900 tracking-wide mt-3">Items In Batch</h3>
								</div>
								<div class="overflow-x-auto overflow-y-auto max-h-[320px] md:max-h-[380px] rounded-lg border border-gray-200">
									<table class="w-full text-xs md:text-sm">
										<thead class="bg-green-100">
											<tr>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Ingredient/Item</th>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Quantity</th>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm"><?php echo $isDistributed ? 'Remaining Stock' : 'Actions'; ?></th>
											</tr>
										</thead>
										<tbody class="divide-y divide-gray-200">
											<?php foreach ($items as $it): 
												$itemId = (int)($it['item_id'] ?? 0);
												$ingredient = null;
												foreach ($ingredients as $ing) {
													if ((int)$ing['id'] === $itemId) {
														$ingredient = $ing;
														break;
													}
												}
											?>
											<tr class="hover:bg-gray-50 transition-colors">
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<div>
														<p class="font-semibold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($it['item_name']); ?></p>
														<p class="text-[10px] md:text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($it['unit']); ?></p>
														<?php if (!empty($it['set_name'])): ?>
															<p class="text-[9px] md:text-[10px] text-indigo-600 font-semibold mt-0.5 md:mt-1">Part of <?php echo htmlspecialchars($it['set_name']); ?> set</p>
														<?php endif; ?>
													</div>
												</td>
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<span class="text-red-600 text-xs md:text-sm">- <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
												</td>
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<?php if ($isDistributed): 
														$remainingStock = $batchRemainingStock[$batchId][$itemId] ?? 0;
														$baseUnit = $ingredient['unit'] ?? $it['unit'] ?? '';
														$displayUnit = $ingredient['display_unit'] ?? null;
														$displayFactor = (float)($ingredient['display_factor'] ?? 1.0);
														
														// Format remaining stock with display unit if available
														$stockValue = (float)$remainingStock;
														$stockDisplay = '';
														
														if ($displayUnit && $displayFactor > 0) {
															// Convert to display unit
															if ($baseUnit === 'g' && $displayUnit === 'kg') {
																$stockDisplay = number_format($stockValue / 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'kg' && $displayUnit === 'g') {
																$stockDisplay = number_format($stockValue * 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'ml' && $displayUnit === 'L') {
																$stockDisplay = number_format($stockValue / 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'L' && $displayUnit === 'ml') {
																$stockDisplay = number_format($stockValue * 1000.0, 2) . ' ' . $displayUnit;
															} else {
																// Use display_factor for conversion
																$stockDisplay = number_format($stockValue / $displayFactor, 2) . ' ' . $displayUnit;
															}
														} else {
															$stockDisplay = number_format($stockValue, 2) . ' ' . $baseUnit;
														}
													?>
														<span class="text-green-600 text-xs md:text-sm"><?php echo htmlspecialchars($stockDisplay); ?></span>
													<?php else: ?>
														<button type="button" class="text-red-600 hover:text-red-700 text-[10px] md:text-xs font-medium">Remove</button>
													<?php endif; ?>
												</td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
        <div id="requestsFilterEmpty" class="hidden px-4 md:px-5 lg:px-6 py-4 text-center text-sm text-gray-500 border-t">
            No request batches match the selected filter.
        </div>
	</div>
</div>
<?php endif; ?>


<?php if (Auth::role() !== 'Kitchen Staff'): ?>
<!-- To Prepare -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden max-w-full w-full">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-1.5 md:gap-2">
				<i data-lucide="chef-hat" class="w-4 h-4 md:w-5 md:h-5 text-orange-600"></i>
				To Prepare
			</h2>
			<p class="text-xs md:text-sm text-gray-600 mt-0.5 md:mt-1">Approved batches waiting to be prepared and distributed.</p>
		</div>
		<span class="text-xs md:text-sm text-gray-500"><?php echo count($toPrepareBatches ?? []); ?> batch<?php echo (count($toPrepareBatches ?? []) === 1) ? '' : 'es'; ?></span>
	</div>
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px] table-scroll-stable">
		<?php if (!empty($toPrepareBatches)): ?>
			<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
				<thead class="sticky top-0 bg-white z-10">
					<tr>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Name</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Items</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Date Requested</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Status</th>
						<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Action</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200">
					<?php foreach ($toPrepareBatches as $batch):
						$prepItems = $batchItems[(int)$batch['id']] ?? [];
						$itemCount = (int)($batch['items_count'] ?? count($prepItems));
						$metaItems = htmlspecialchars(json_encode($prepItems, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
						$ingredientCount = 0;
						if (!empty($batch['custom_ingredients'])) {
							$ingredientLines = array_filter(array_map('trim', explode("\n", $batch['custom_ingredients'])));
							$ingredientCount = count($ingredientLines);
						}
						$dateNeededRaw = !empty($batch['custom_request_date'])
							? substr((string)$batch['custom_request_date'], 0, 10)
							: substr((string)($batch['date_requested'] ?? ''), 0, 10);
						$dateNeededFormatted = ($dateNeededRaw && strtotime($dateNeededRaw)) 
							? date('F j', strtotime($dateNeededRaw)) 
							: ($dateNeededRaw ?: '—');
					?>
					<tr class="hover:bg-gray-50 transition-colors">
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
							<div>
								<p class="text-[10px] md:text-xs lg:text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($batch['custom_requester'] ?: ($batch['staff_name'] ?? '')); ?></p>
								<p class="text-[9px] md:text-[10px] lg:text-xs text-gray-500">Created by <?php echo htmlspecialchars($batch['staff_name'] ?? ''); ?></p>
							</div>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm">
							<?php echo $itemCount; ?> item<?php echo $itemCount === 1 ? '' : 's'; ?>
							<?php if ($ingredientCount > 0): ?>
								<span class="text-gray-500">(<?php echo $ingredientCount; ?> ingredient<?php echo $ingredientCount === 1 ? '' : 's'; ?> requested)</span>
							<?php endif; ?>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm">
							<?php echo htmlspecialchars($dateNeededFormatted); ?>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
							<span class="inline-flex items-center gap-0.5 md:gap-1 px-2 md:px-2.5 lg:px-3 py-0.5 md:py-1 text-[9px] md:text-[10px] lg:text-xs font-semibold rounded-full text-orange-600 whitespace-nowrap">To Prepare</span>
						</td>
						<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
							<?php if (in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true)): ?>
								<button type="button"
									class="prepareBatchBtn inline-flex items-center gap-1 md:gap-1.5 lg:gap-2 px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 lg:py-2.5 text-[9px] md:text-xs lg:text-sm font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
									data-batch="<?php echo (int)$batch['id']; ?>"
									data-items="<?php echo $metaItems; ?>"
									data-requester="<?php echo htmlspecialchars($batch['custom_requester'] ?: ($batch['staff_name'] ?? '')); ?>"
									data-notes="<?php echo htmlspecialchars($batch['custom_ingredients'] ?? ''); ?>"
									data-date="<?php echo htmlspecialchars($dateNeededFormatted); ?>"
									data-staff="<?php echo htmlspecialchars($batch['staff_name'] ?? ''); ?>"
									data-staff-id="<?php echo (int)($batch['staff_id'] ?? 0); ?>">
									Prepare
								</button>
							<?php else: ?>
								<span class="text-[9px] md:text-xs text-gray-500">Awaiting prep</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-gray-500">
				<i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
				<p class="text-sm">No batches are waiting for distribution.</p>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php if (Auth::role() === 'Kitchen Staff'): ?>
<!-- Kitchen Staff history -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden max-w-full w-full">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
		<h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-1.5 md:gap-2">
			<i data-lucide="clock" class="w-4 h-4 md:w-5 md:h-5 text-gray-600"></i>
			Request History
		</h2>
		<p class="text-xs md:text-sm text-gray-600 mt-0.5 md:mt-1">Track the status of your submitted requests</p>
	</div>
	<div class="overflow-x-auto">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="bg-green-100">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Batch ID</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Requester</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Ingredient/Items</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Date Requested</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Status</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Action</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): 
					$userId = Auth::id() ?? 0;
					$isRequester = ((int)($b['staff_id'] ?? 0) === $userId);
					// Use case-insensitive comparison for status
					$currentStatus = trim($b['status'] ?? '');
					$isPendingConfirmation = (strcasecmp($currentStatus, 'Pending Confirmation') === 0);
				?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<div class="flex items-center gap-1.5 md:gap-2">
							<div class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<span class="text-[9px] md:text-[10px] lg:text-xs font-semibold text-blue-600">#<?php echo (int)$b['id']; ?></span>
							</div>
						</div>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 font-semibold text-gray-900 text-[10px] md:text-xs lg:text-sm"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<?php 
						$isPending = ($b['status'] ?? '') === 'Pending';
						if ($isRequester && $isPending): 
						?>
							<button type="button" 
								class="editRequestBtn text-blue-600 hover:text-blue-700 text-[10px] md:text-xs lg:text-sm font-medium"
								data-batch-id="<?php echo (int)$b['id']; ?>"
								data-requester-name="<?php echo htmlspecialchars($b['custom_requester'] ?? ''); ?>"
								data-ingredients-note="<?php echo htmlspecialchars($b['custom_ingredients'] ?? ''); ?>"
								data-request-date="<?php echo htmlspecialchars($b['custom_request_date'] ?: substr((string)($b['date_requested'] ?? ''), 0, 10)); ?>">
								View
							</button>
						<?php else: ?>
							<button type="button" 
								class="viewBatchDetails text-blue-600 hover:text-blue-700 text-[10px] md:text-xs lg:text-sm font-medium"
								data-batch="<?php echo (int)$b['id']; ?>"
								data-status="<?php echo htmlspecialchars($b['status'] ?? ''); ?>">
								View
							</button>
						<?php endif; ?>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm"><?php echo htmlspecialchars(formatDate($b['date_requested'])); ?></td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<?php 
						$statusLower = strtolower($b['status'] ?? '');
						
						// For stock handlers/owners: show "Distributed" instead of "Received"
						// For requester (kitchen staff): show "Received" when status is "Received"
						$currentUserRole = Auth::role();
						$isStockHandler = in_array($currentUserRole, ['Owner', 'Manager', 'Stock Handler'], true);
						
						$statusText = match($statusLower) {
							'distributed' => 'Distributed',
							'pending confirmation' => 'Pending Confirmation',
							'received' => $isStockHandler ? 'Distributed' : 'Received', // Stock handlers see "Distributed", requester sees "Received"
							'to prepare' => 'Preparing',
							'pending' => 'Pending',
							'rejected' => 'Rejected',
							'approved' => 'Approved',
							default => htmlspecialchars($b['status'] ?? '')
						};
						$statusClass = match($statusLower) {
							'distributed' => 'bg-green-100 text-green-800 border-green-200',
							'pending confirmation' => 'bg-blue-100 text-blue-800 border-blue-200',
							'received' => 'bg-green-100 text-green-800 border-green-200', // Same styling for both "Received" and "Distributed"
							'to prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
							'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
							'rejected' => 'bg-red-100 text-red-800 border-red-200',
							'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
							default => 'bg-gray-100 text-gray-700 border-gray-200'
						};
						?>
						<span class="inline-flex items-center px-2 md:px-2.5 lg:px-3 py-0.5 md:py-1 rounded-full text-[9px] md:text-[10px] lg:text-xs font-medium border whitespace-nowrap <?php echo $statusClass; ?>">
							<?php echo htmlspecialchars($statusText); ?>
						</span>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<?php if ($isRequester && $isPendingConfirmation): ?>
							<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/confirm-delivery" class="inline confirm-delivery-form" data-confirm="Are you sure you want to confirm delivery for request batch #<?php echo (int)$b['id']; ?>?" data-confirm-type="info">
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
								<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
								<button type="submit" class="inline-flex items-center justify-center gap-1 bg-green-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-[10px] md:text-xs lg:text-sm font-medium transition-colors">
									Confirm Delivery
								</button>
							</form>
						<?php else: ?>
							<span class="text-gray-400 text-[10px] md:text-xs">—</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden" data-detail-for="<?php echo (int)$b['id']; ?>">
					<td colspan="6" class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6">
						<div class="batch-detail-card bg-white rounded-2xl border border-gray-200 p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5 lg:space-y-6">
							<!-- Batch Header -->
							<div class="flex flex-col sm:flex-row items-start sm:items-start justify-between gap-3 md:gap-4 pb-4 border-b border-gray-200">
								<div class="flex-1">
									<?php if (!empty($b['custom_requester'])): ?>
									<p class="text-sm font-semibold tracking-wide text-gray-500 mb-2">Request Name: <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester']); ?></span></p>
									<?php endif; ?>
									<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-4 request-info-section">
										<div>
											<p class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">REQUESTED BY</p>
											<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></p>
										</div>
										<div>
											<p class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">DATE REQUESTED</p>
											<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars(formatDate($b['date_requested'])); ?></p>
										</div>
									</div>
								</div>
								<?php 
								// For stock handlers/owners: show "Distributed" instead of "Received"
								$currentUserRole = Auth::role();
								$isStockHandler = in_array($currentUserRole, ['Owner', 'Manager', 'Stock Handler'], true);
								$displayStatus = ($b['status'] === 'Received' && $isStockHandler) ? 'Distributed' : $b['status'];
								
								$statusColor = match($displayStatus) {
									'Distributed' => 'bg-green-100 text-green-800 border-green-200',
									'Pending Confirmation' => 'bg-blue-100 text-blue-800 border-blue-200',
									'Received' => 'bg-green-100 text-green-800 border-green-200',
									'Rejected' => 'bg-red-100 text-red-800 border-red-200',
									'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
									'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
									default => 'bg-gray-100 text-gray-700 border-gray-200'
								};
								?>
								<span class="inline-flex items-center gap-1.5 px-2.5 md:px-3 py-1.5 rounded-lg text-xs font-semibold border <?php echo $statusColor; ?> shrink-0 whitespace-nowrap">
									<i data-lucide="circle" class="w-2 h-2 fill-current"></i>
									<?php echo htmlspecialchars($displayStatus); ?>
								</span>
							</div>

							<!-- Requested Items Section -->
							<?php if (!empty($b['custom_ingredients'])): ?>
							<div class="p-3 md:p-4 border border-gray-200 rounded-lg">
								<p class="text-xs tracking-wide text-gray-500 mb-2 md:mb-3">Requested Ingredient/Items</p>
								<ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
									<?php 
									$ingredientLines = explode("\n", trim($b['custom_ingredients']));
									foreach ($ingredientLines as $line): 
										$line = trim($line);
										if (empty($line)) continue;
									?>
									<li class="text-sm text-gray-700 flex items-start gap-2">
										<span class="text-gray-500 mt-0.5">•</span>
										<span class="flex-1"><?php echo htmlspecialchars($line); ?></span>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- Items In Batch Section -->
							<?php 
							$kitchenStaffItems = $batchItems[(int)$b['id']] ?? [];
							if (!empty($kitchenStaffItems) && Auth::role() !== 'Kitchen Staff'): 
								$statusLower = strtolower($b['status'] ?? '');
								$isDistributed = in_array($statusLower, ['distributed', 'pending confirmation', 'received'], true);
								$batchId = (int)$b['id'];
							?>
							<div class="space-y-3 md:space-y-4 pt-2 border-t border-gray-100">
								<div class="flex items-center gap-1.5 md:gap-2">
									<i data-lucide="list-checks" class="w-4 h-4 md:w-5 md:h-5 text-green-600"></i>
									<h3 class="text-xs md:text-xs font-semibold text-gray-900 tracking-wide">Items In Batch</h3>
								</div>
								<div class="overflow-x-auto overflow-y-auto max-h-[320px] md:max-h-[380px] rounded-lg border border-gray-200">
									<table class="w-full text-xs md:text-sm">
										<thead class="bg-gray-100">
											<tr>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Ingredient</th>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Quantity</th>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm"><?php echo $isDistributed ? 'Remaining Stock' : 'Actions'; ?></th>
											</tr>
										</thead>
										<tbody class="divide-y divide-gray-200">
											<?php foreach ($kitchenStaffItems as $it): 
												$itemId = (int)($it['item_id'] ?? 0);
												$ingredient = null;
												foreach ($ingredients as $ing) {
													if ((int)$ing['id'] === $itemId) {
														$ingredient = $ing;
														break;
													}
												}
											?>
											<tr class="hover:bg-gray-50 transition-colors">
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<div>
														<p class="font-semibold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($it['item_name']); ?></p>
														<p class="text-[10px] md:text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($it['unit']); ?></p>
														<?php if (!empty($it['set_name'])): ?>
															<p class="text-[9px] md:text-[10px] text-indigo-600 font-semibold mt-0.5 md:mt-1">Part of <?php echo htmlspecialchars($it['set_name']); ?> set</p>
														<?php endif; ?>
													</div>
												</td>
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<span class="text-gray-900 text-xs md:text-sm">- <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
												</td>
												<td class="px-2.5 md:px-3 py-2 md:py-2.5">
													<?php if ($isDistributed): 
														$remainingStock = $batchRemainingStock[$batchId][$itemId] ?? 0;
														$baseUnit = $ingredient['unit'] ?? $it['unit'] ?? '';
														$displayUnit = $ingredient['display_unit'] ?? null;
														$displayFactor = (float)($ingredient['display_factor'] ?? 1.0);
														
														// Format remaining stock with display unit if available
														$stockValue = (float)$remainingStock;
														$stockDisplay = '';
														
														if ($displayUnit && $displayFactor > 0) {
															// Convert to display unit
															if ($baseUnit === 'g' && $displayUnit === 'kg') {
																$stockDisplay = number_format($stockValue / 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'kg' && $displayUnit === 'g') {
																$stockDisplay = number_format($stockValue * 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'ml' && $displayUnit === 'L') {
																$stockDisplay = number_format($stockValue / 1000.0, 2) . ' ' . $displayUnit;
															} else if ($baseUnit === 'L' && $displayUnit === 'ml') {
																$stockDisplay = number_format($stockValue * 1000.0, 2) . ' ' . $displayUnit;
															} else {
																// Use display_factor for conversion
																$stockDisplay = number_format($stockValue / $displayFactor, 2) . ' ' . $displayUnit;
															}
														} else {
															$stockDisplay = number_format($stockValue, 2) . ' ' . $baseUnit;
														}
													?>
														<span class="text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($stockDisplay); ?></span>
													<?php else: ?>
														<button type="button" class="text-red-600 hover:text-red-700 text-[10px] md:text-xs font-medium">Remove</button>
													<?php endif; ?>
												</td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($batches)): ?>
				<tr>
					<td colspan="5" class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6 text-center text-sm text-gray-500">No requests submitted yet.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>


<!-- Edit Request Modal -->
<div id="editRequestModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
	<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
	<div class="relative z-10 flex items-center justify-center p-4" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;">
		<div class="bg-white rounded-xl shadow-none w-full max-w-4xl mx-auto my-auto edit-modal-card" style="max-width: 56rem; margin-top: auto !important; margin-bottom: auto !important;">
		<div class="p-6">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1.5">
					<i data-lucide="edit" class="w-4 h-4 md:w-5 md:h-5 text-blue-600"></i>
					Edit Request
				</h2>
				<button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
					<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
				</button>
			</div>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/update" class="space-y-4 md:space-y-5">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="batch_id" id="editBatchId" value="">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
				<div class="space-y-1">
					<label class="block text-[10px] md:text-xs font-medium text-gray-700">Name</label>
					<input name="requester_name" id="editRequesterName" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 md:focus:ring-0 focus:outline-none" placeholder="e.g., malupiton" required>
				</div>
				<div class="space-y-1">
					<label class="block text-[10px] md:text-xs font-medium text-gray-700">Date Needed</label>
					<input type="date" name="request_date" id="editRequestDate" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 md:focus:ring-0 focus:outline-none" required>
				</div>
			</div>
			<div class="space-y-1">
				<label class="block text-[10px] md:text-xs font-medium text-gray-700">Ingredients / Notes</label>
				<textarea name="ingredients_note" id="editIngredientsNote" rows="3" class="w-full border border-gray-300 rounded-lg px-3.5 md:px-4 py-2.5 md:py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 md:focus:ring-0 focus:outline-none" placeholder="List ingredients, quantities, or any prep instructions" required></textarea>
				<p class="text-[9px] md:text-[10px] text-gray-500">Detailed quantities will be captured later during the Prepare step.</p>
			</div>
			<div class="flex justify-end gap-2">
				<button type="button" id="cancelEditModal" class="inline-flex items-center gap-1 px-3.5 md:px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 md:focus:ring-0 md:focus:ring-offset-0 transition-colors text-sm md:text-base">
					Cancel
				</button>
				<button type="submit" class="inline-flex items-center gap-1 bg-blue-600 text-white px-3.5 md:px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 md:focus:ring-0 md:focus:ring-offset-0 transition-colors text-sm md:text-base">
					<i data-lucide="save" class="w-4 h-4 md:w-5 md:h-5"></i>
					Save Changes
				</button>
			</div>
		</form>
		</div>
	</div>
	</div>
</div>

<div id="prepareModal" class="fixed inset-0 z-50 hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 50 !important;">
	<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
	<div class="relative z-10 flex items-center justify-center p-4" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;">
		<div class="bg-white rounded-xl shadow-none w-full max-w-4xl mx-auto my-auto prepare-modal-card" style="max-width: 56rem; margin-top: auto !important; margin-bottom: auto !important;">
		<div class="p-6">
			<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 md:gap-3 mb-4">
				<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-4">
					<div>
						<p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Batch</p>
						<p class="text-sm font-semibold text-gray-900" id="prepareModalBatchLabel">#0</p>
					</div>
					<div>
						<p class="text-[10px] uppercase tracking-wide text-gray-500">Requested By</p>
						<p class="text-[12px] font-semibold text-gray-900 mt-0.5" id="prepareModalStaff">—</p>
					</div>
					<div class="flex flex-wrap items-center gap-2 md:gap-3">
						<div>
							<p class="text-[10px] uppercase tracking-wide text-gray-500">Requester</p>
							<p class="text-[12px] font-semibold text-gray-900 mt-0.5" id="prepareModalRequestName">—</p>
						</div>
						<div>
							<p class="text-[10px] uppercase tracking-wide text-gray-500">Date Needed</p>
							<p class="text-[12px] font-semibold text-gray-900 mt-0.5" id="prepareModalRequestDate">—</p>
						</div>
					</div>
				</div>
				<button type="button" class="prepareModalClose text-gray-400 hover:text-gray-600 transition-colors p-1 shrink-0" aria-label="Close">
					<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
				</button>
			</div>
		<div class="space-y-3">
			<p class="text-[12px] font-semibold tracking-wide text-gray-500 mb-2">Requested Ingredients/Items</p>
			<div class="p-2.5 md:p-3 border border-gray-200 rounded-lg">
				<ul class="grid grid-cols-1 md:grid-cols-2 gap-1.5" id="prepareModalNotes">
					<li class="text-[10px] md:text-xs text-gray-700">—</li>
				</ul>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/prepare" id="prepareModalForm" class="space-y-3">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="batch_id" id="prepareModalBatchId">
				<input type="hidden" name="action" id="prepareModalAction" value="save">
				<div class="rounded-xl border border-gray-200 p-2.5 md:p-3 space-y-2.5">
					<div class="space-y-2.5">
						<div class="space-y-1">
							<label class="text-[10px] md:text-xs font-medium text-gray-700">Ingredients/Items</label>
							<div class="relative">
								<div class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10">
									<i data-lucide="search" class="w-3 h-3 text-gray-400"></i>
								</div>
								<input 
									type="text" 
									id="prepareIngredientSearch" 
									placeholder="Search ingredients..." 
									class="w-full border border-gray-300 rounded-lg pl-8 pr-2 py-2 text-[10px] md:text-xs focus:outline-none"
									autocomplete="off"
								>
								<select id="prepareIngredientSelect" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-[10px] md:text-xs focus:ring-2 focus:ring-gray-500 focus:outline-none" style="display: none;">
									<option value="">Choose ingredient</option>
									<?php foreach ($ingredients as $ing): ?>
										<option 
											value="<?php echo (int)$ing['id']; ?>" 
											data-unit="<?php echo htmlspecialchars($ing['unit']); ?>" 
											data-name="<?php echo htmlspecialchars(strtolower($ing['name'])); ?>"
											data-quantity="<?php echo (float)($ing['quantity'] ?? 0); ?>"
											data-display-unit="<?php echo htmlspecialchars($ing['display_unit'] ?? ''); ?>"
											data-display-factor="<?php echo (float)($ing['display_factor'] ?? 1); ?>"
										><?php echo htmlspecialchars($ing['name']); ?></option>
									<?php endforeach; ?>
								</select>
								<div id="prepareIngredientDropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
									<!-- Options will be populated here -->
								</div>
							</div>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
							<div class="space-y-1">
								<label class="text-[10px] md:text-xs font-medium text-gray-700">Quantity</label>
								<input type="number" step="0.01" min="0.01" id="prepareQuantityInput" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-[10px] md:text-xs focus:outline-none" placeholder="0.00">
							</div>
							<div class="space-y-1">
								<label class="text-[10px] md:text-xs font-medium text-gray-700">Unit</label>
								<select id="prepareUnitSelect" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-[10px] md:text-xs focus:outline-none">
									<option value="">Base unit</option>
								</select>
							</div>
							<div class="flex items-end">
								<button type="button" id="prepareAddItemBtn" class="w-full inline-flex items-center justify-center gap-1 bg-green-600 text-white px-2 md:px-2.5 py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-[10px] md:text-xs">
									<i data-lucide="plus" class="w-3 h-3"></i>
									Add
								</button>
							</div>
						</div>
					</div>
					<div id="prepareBuilderError" class="hidden px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-[10px] md:text-xs text-red-700"></div>
				</div>
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<table class="w-full text-[10px] md:text-xs">
						<thead class="bg-gray-100">
							<tr>
								<th class="text-left px-2 md:px-2.5 py-1.5 font-semibold text-gray-900 text-[10px] md:text-xs">Ingredients/Items</th>
								<th class="text-left px-2 md:px-2.5 py-1.5 font-semibold text-gray-900 text-[10px] md:text-xs">Quantity</th>
								<th class="text-left px-2 md:px-2.5 py-1.5 font-semibold text-gray-900 text-[10px] md:text-xs">Actions</th>
							</tr>
						</thead>
						<tbody id="prepareItemsBody" class="divide-y divide-gray-200"></tbody>
					</table>
					<div id="prepareEmptyState" class="px-3 py-4 text-center text-[10px] md:text-xs text-gray-500">No ingredients added yet.</div>
				</div>
				<div id="prepareDynamicInputs"></div>
				<div class="flex flex-col sm:flex-row sm:justify-end gap-2">
					<button type="button" data-action="save" class="prepareSubmitBtn inline-flex items-center justify-center gap-1 border border-gray-300 text-gray-700 px-2.5 md:px-3 py-1.5 rounded-lg hover:bg-gray-50 text-[10px] md:text-xs">
						<i data-lucide="save" class="w-3 h-3"></i>
						Save Prep
					</button>
					<button type="button" data-action="distribute" class="prepareSubmitBtn inline-flex items-center justify-center gap-1 bg-green-600 text-white px-2.5 md:px-3 py-1.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-[10px] md:text-xs">
						<i data-lucide="send" class="w-3 h-3"></i>
						Distribute
					</button>
				</div>
			</form>
		</div>
		</div>
	</div>
</div>

<!-- Distribute Confirmation Modal -->
<div id="distributeConfirmModal" class="fixed inset-0 z-[100000] hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 100000 !important;">
	<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
	<div class="relative z-10 flex items-center justify-center p-4" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;">
		<div class="bg-white rounded-xl shadow-none w-full max-w-md mx-auto my-auto" style="max-width: 28rem; margin-top: auto !important; margin-bottom: auto !important;">
		<div class="p-6">
			<div class="flex items-center gap-3 mb-4">
				<div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
					<i data-lucide="send" class="w-5 h-5 text-green-600"></i>
				</div>
				<div>
					<h3 class="text-lg md:text-xl font-semibold text-gray-900">Confirm Distribution</h3>
					<p class="text-sm md:text-base text-gray-600 mt-1">Are you sure you want to distribute the selected items?</p>
				</div>
			</div>
			<div class="flex flex-col sm:flex-row sm:justify-end gap-2.5 md:gap-3">
				<button type="button" id="distributeConfirmCancel" class="inline-flex items-center justify-center gap-1.5 border border-gray-300 text-gray-700 px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm md:text-base transition-colors">
					<i data-lucide="x" class="w-4 h-4"></i>
					No, Cancel
				</button>
				<button type="button" id="distributeConfirmYes" class="inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm md:text-base transition-colors">
					<i data-lucide="check" class="w-4 h-4"></i>
					Yes, Distribute
				</button>
			</div>
		</div>
		</div>
	</div>
</div>

<script>
(function(){
	const INGREDIENTS = <?php echo json_encode(array_map(function($i){
		return [
			'id' => (int)$i['id'],
			'name' => $i['name'],
			'unit' => $i['unit'],
			'quantity' => (float)($i['quantity'] ?? 0),
			'reorder_level' => (float)($i['reorder_level'] ?? 0),
			'display_unit' => $i['display_unit'] ?? null,
			'display_factor' => (float)($i['display_factor'] ?? 1),
		];
	}, $ingredients), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
	const INGREDIENT_LOOKUP = INGREDIENTS.reduce((map, item) => {
		map[item.id] = item;
		return map;
	}, {});

	const statusFilterSelect = document.getElementById('requestStatusFilter');
	const dateFromInput = document.getElementById('requestDateFrom');
	const dateToInput = document.getElementById('requestDateTo');
	const requestRows = Array.from(document.querySelectorAll('tr[data-status]'));
	const requestsFilterEmpty = document.getElementById('requestsFilterEmpty');

	function withinDateRange(rowDateStr, fromStr, toStr){
		if (!rowDateStr) return true;
		const rowDate = new Date(rowDateStr + 'T00:00:00');
		if (Number.isNaN(rowDate.getTime())) return true;
		if (fromStr){
			const fromDate = new Date(fromStr + 'T00:00:00');
			if (!Number.isNaN(fromDate.getTime()) && rowDate < fromDate) return false;
		}
		if (toStr){
			const toDate = new Date(toStr + 'T23:59:59');
			if (!Number.isNaN(toDate.getTime()) && rowDate > toDate) return false;
		}
		return true;
	}

	function applyRequestFilter(){
		const statusValue = (statusFilterSelect?.value || 'all').toLowerCase();
		const normalized = statusValue && statusValue !== 'all' ? statusValue : 'all';
		const fromStr = dateFromInput?.value || '';
		const toStr = dateToInput?.value || '';
		let visible = 0;
		requestRows.forEach(row => {
			const status = (row.getAttribute('data-status') || '').toLowerCase();
			const rowDateStr = row.getAttribute('data-date-needed') || '';
			const statusMatches = normalized === 'all' || status === normalized;
			const dateMatches = withinDateRange(rowDateStr, fromStr, toStr);
			const matches = statusMatches && dateMatches;
			row.classList.toggle('hidden', !matches);
			const detailId = row.getAttribute('data-detail-id');
			if (detailId){
				const detail = document.getElementById(detailId);
				if (detail && !matches){
					detail.classList.add('hidden');
				}
			}
			if (matches){ visible++; }
		});
		if (requestsFilterEmpty){
			requestsFilterEmpty.classList.toggle('hidden', visible !== 0);
		}
	}

	if (statusFilterSelect){
		const initial = (statusFilterSelect.dataset.default || 'all').toLowerCase();
		statusFilterSelect.value = initial;
		applyRequestFilter();
		statusFilterSelect.addEventListener('change', ()=>{
			const value = (statusFilterSelect.value || 'all').toLowerCase();
			const params = new URLSearchParams(window.location.search);
			if (value === 'all'){
				params.delete('status');
			} else {
				params.set('status', value);
			}
			const query = params.toString();
			const newUrl = window.location.pathname + (query ? `?${query}` : '') + window.location.hash;
			window.history.replaceState({}, '', newUrl);
			applyRequestFilter();
		});
	} else {
		applyRequestFilter();
	}

	if (dateFromInput) {
		dateFromInput.addEventListener('change', applyRequestFilter);
	}
	if (dateToInput) {
		dateToInput.addEventListener('change', applyRequestFilter);
	}

	const detailButtons = document.querySelectorAll('.viewBatchDetails');
	detailButtons.forEach(btn => {
		btn.addEventListener('click', ()=>{
			const id = btn.getAttribute('data-batch');
			const status = (btn.getAttribute('data-status') || '').trim();
			const template = document.getElementById('batch-' + id);
			if (!template) return;
			const card = template.querySelector('.batch-detail-card');
			if (!card) return;
			
			// Clone the card to avoid modifying the original DOM
			const cardClone = card.cloneNode(true);
			
			// Extract Requested By and Date Requested from the cloned card
			let requestedBy = '—';
			let dateRequested = '—';
			
			// Find the request info section in the clone
			const requestInfoSection = cardClone.querySelector('.request-info-section');
			if (requestInfoSection) {
				const allDivs = requestInfoSection.querySelectorAll('div');
				allDivs.forEach(div => {
					const text = div.textContent || '';
					if (text.includes('REQUESTED BY')) {
						const valueP = div.querySelector('p.text-sm.font-semibold');
						if (valueP) requestedBy = valueP.textContent.trim();
					}
					if (text.includes('DATE REQUESTED')) {
						const valueP = div.querySelector('p.text-sm.font-semibold');
						if (valueP) {
							const fullDate = valueP.textContent.trim();
							// Extract only the date part (first 10 characters: YYYY-MM-DD) and format it
							const dateStr = fullDate.substring(0, 10);
							if (dateStr.length === 10) {
								const date = new Date(dateStr + 'T00:00:00');
								if (!isNaN(date.getTime())) {
									const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
									dateRequested = months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
								} else {
									dateRequested = dateStr;
								}
							} else {
								dateRequested = dateStr;
							}
						}
					}
				});
				
				// Remove the Requested By and Date Requested section from cloned card content
				requestInfoSection.remove();
			}
			
			// Check if user can approve/reject (Owner, Manager, Stock Handler) and status is Pending
			const canApproveReject = <?php echo in_array(Auth::role(), ['Owner','Manager','Stock Handler'], true) ? 'true' : 'false'; ?>;
			const showActions = canApproveReject && status === 'Pending';
			const statusLower = (status || '').toLowerCase().trim();
			const isRejected = statusLower === 'rejected';
			
			// Get CSRF token
			const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
			const baseUrl = '<?php echo htmlspecialchars($baseUrl); ?>';
			
			// Remove any existing overlays to prevent stacking
			const existingOverlays = document.querySelectorAll('.batch-detail-overlay');
			existingOverlays.forEach(ov => ov.remove());
			
			const overlay = document.createElement('div');
			overlay.className = 'batch-detail-overlay fixed inset-0 z-[99999] hidden overflow-hidden';
			overlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 99999 !important;';
			const backdrop = document.createElement('div');
			backdrop.className = 'fixed inset-0 bg-black/50';
			backdrop.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;';
			overlay.appendChild(backdrop);
			const container = document.createElement('div');
			container.className = 'relative z-10 flex items-center justify-center p-4';
			container.style.cssText = 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;';
			const modal = document.createElement('div');
			// Match sizing with "Add Batch Request" modal (max-w-4xl) and avoid whole-modal scroll
			// Make the modal flex with a scrollable body so actions stay visible on small screens
			modal.className = 'bg-white rounded-xl shadow-none w-full max-w-4xl mx-auto my-auto flex flex-col overflow-hidden';
			modal.style.cssText = 'max-width: 56rem; margin-top: auto !important; margin-bottom: auto !important;';
			const modalContent = document.createElement('div');
			modalContent.className = 'p-6';
			modalContent.innerHTML = `
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 md:gap-3 mb-4">
					<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-4">
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-0.5">BATCH ID</p>
							<p class="text-sm font-bold text-gray-900">#${id}</p>
						</div>
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-0.5">REQUESTED BY</p>
							<p class="text-[10px] md:text-xs font-semibold text-gray-900">${requestedBy}</p>
						</div>
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-0.5">DATE REQUESTED</p>
							<p class="text-[10px] md:text-xs font-semibold text-gray-900">${dateRequested}</p>
						</div>
					</div>
					<button type="button" class="closeBatchModal text-gray-400 hover:text-gray-600 transition-colors p-1 shrink-0" aria-label="Close">
						<i data-lucide="x" class="w-4 h-4"></i>
					</button>
				</div>
				<div class="flex-1 overflow-y-auto">${cardClone.innerHTML}</div>
				${showActions ? `
				<div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
					<form method="post" action="${baseUrl}/requests/approve" class="inline">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-green-600 text-white text-[10px] md:text-xs rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="check" class="w-3 h-3"></i>
							Approve
						</button>
					</form>
					<form method="post" action="${baseUrl}/requests/reject" class="inline" data-confirm="Are you sure you want to reject request batch #${id}? The requester will be notified." data-confirm-type="warning">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-red-600 text-white text-[10px] md:text-xs rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="x" class="w-3 h-3"></i>
							Reject
						</button>
					</form>
				</div>
				` : ''}
				${isRejected ? `
				<div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
					<form method="post" action="${baseUrl}/requests/delete" class="inline" id="deleteBatchForm${id}">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="button" class="deleteBatchBtn inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-red-600 text-white text-[10px] md:text-xs rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="trash-2" class="w-3 h-3"></i>
							Delete
						</button>
					</form>
				</div>
				` : ''}
			`;
			modal.appendChild(modalContent);
			container.appendChild(modal);
			overlay.appendChild(container);
			overlay.classList.remove('hidden');
			document.body.appendChild(overlay);
			document.body.classList.add('overflow-hidden');
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
			
			// Prevent Enter key from submitting forms unless it's an input field
			modal.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
					e.preventDefault();
				}
			});
			
			// Handle form confirmations
			const rejectForm = modal.querySelector('form[action*="/requests/reject"]');
			if (rejectForm) {
				rejectForm.addEventListener('submit', function(e) {
					if (!confirm('Are you sure you want to reject request batch #' + id + '? The requester will be notified.')) {
						e.preventDefault();
					}
				});
			}
			
			const approveForm = modal.querySelector('form[action*="/requests/approve"]');
			if (approveForm) {
				approveForm.addEventListener('keydown', function(e) {
					if (e.key === 'Enter') {
						e.preventDefault();
					}
				});
			}
			
			// Handle delete button for rejected requests
			const deleteBtn = modal.querySelector('.deleteBatchBtn');
			if (deleteBtn) {
				deleteBtn.addEventListener('click', function() {
					// Remove any existing confirmation overlays
					const existingConfirmOverlays = document.querySelectorAll('.delete-confirm-overlay');
					existingConfirmOverlays.forEach(ov => ov.remove());
					
					// Create confirmation modal
					const confirmOverlay = document.createElement('div');
					confirmOverlay.className = 'delete-confirm-overlay fixed inset-0 z-[999999] hidden overflow-hidden';
					confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 999999 !important; pointer-events: none !important;';
					const confirmBackdrop = document.createElement('div');
					confirmBackdrop.className = 'fixed inset-0 bg-black/50';
					confirmBackdrop.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;';
					confirmOverlay.appendChild(confirmBackdrop);
					const confirmContainer = document.createElement('div');
					confirmContainer.className = 'relative z-10 flex items-center justify-center p-4';
					confirmContainer.style.cssText = 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;';
					// Make the modal itself clickable
					const confirmModal = document.createElement('div');
					confirmModal.className = 'bg-white rounded-xl shadow-none max-w-sm w-full mx-auto my-auto';
					confirmModal.style.cssText = 'max-width: 24rem; margin-top: auto !important; margin-bottom: auto !important;';
					confirmModal.style.pointerEvents = 'auto';
					const confirmModalContent = document.createElement('div');
					confirmModalContent.className = 'p-6';
					confirmModalContent.innerHTML = `
						<div class="flex items-center gap-3 mb-4">
							<div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
								<i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
							</div>
							<div>
								<h3 class="text-xs md:text-sm font-semibold text-gray-900">Delete Request Batch</h3>
								<p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5">This action cannot be undone</p>
							</div>
						</div>
						<p class="text-[10px] md:text-xs text-gray-700 mb-3 md:mb-4">
							Are you sure you want to delete request batch <strong>#${id}</strong>? This will permanently remove the batch and all associated data.
						</p>
						<div class="flex items-center justify-end gap-2">
							<button type="button" class="cancelDeleteBtn inline-flex items-center justify-center px-2.5 md:px-3 py-1.5 text-[10px] md:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
								Cancel
							</button>
							<button type="button" class="confirmDeleteBtn inline-flex items-center justify-center px-2.5 md:px-3 py-1.5 text-[10px] md:text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
								Delete
							</button>
						</div>
					`;
					confirmModal.appendChild(confirmModalContent);
					confirmContainer.appendChild(confirmModal);
					confirmOverlay.appendChild(confirmContainer);
					confirmOverlay.classList.remove('hidden');
					document.body.appendChild(confirmOverlay);
					
					if (typeof lucide !== 'undefined') {
						lucide.createIcons();
					}
					
					const closeConfirmModal = () => {
						confirmOverlay.remove();
						document.body.classList.remove('overflow-hidden');
					};
					
					confirmModal.querySelector('.cancelDeleteBtn').addEventListener('click', closeConfirmModal);
					
					confirmModal.querySelector('.confirmDeleteBtn').addEventListener('click', function() {
						const deleteForm = document.getElementById('deleteBatchForm' + id);
						if (deleteForm) {
							deleteForm.submit();
						}
					});
				});
			}
			
			const closeModal = ()=> {
				// Remove all overlays related to this modal
				const allOverlays = document.querySelectorAll('.batch-detail-overlay, .delete-confirm-overlay');
				allOverlays.forEach(ov => ov.remove());
				document.body.classList.remove('overflow-hidden');
			};
			overlay.addEventListener('click', (event)=>{ if (event.target === overlay) closeModal(); });
			modal.querySelector('.closeBatchModal').addEventListener('click', closeModal);
		});
	});

	function initPrepareModal(){
		const modal = document.getElementById('prepareModal');
		if (!modal) return;
		const closeBtn = modal.querySelector('.prepareModalClose');
		const form = document.getElementById('prepareModalForm');
		const ingredientSelect = document.getElementById('prepareIngredientSelect');
		const ingredientSearch = document.getElementById('prepareIngredientSearch');
		const ingredientDropdown = document.getElementById('prepareIngredientDropdown');
		const quantityInput = document.getElementById('prepareQuantityInput');
		const unitSelect = document.getElementById('prepareUnitSelect');
		const addBtn = document.getElementById('prepareAddItemBtn');
		const itemsBody = document.getElementById('prepareItemsBody');
		const emptyState = document.getElementById('prepareEmptyState');
		const dynamicInputs = document.getElementById('prepareDynamicInputs');
		const actionInput = document.getElementById('prepareModalAction');
		const batchIdInput = document.getElementById('prepareModalBatchId');
		const errorBox = document.getElementById('prepareBuilderError');
		let items = [];
		let requestedItemIds = [];
		let requestedNameLines = [];
		const updatePrepareControlsState = () => {
			const hasSelection = Boolean(ingredientSelect && ingredientSelect.value);
			if (quantityInput) quantityInput.disabled = !hasSelection;
			if (unitSelect) unitSelect.disabled = !hasSelection;
		};
		
		// Escape HTML function
		const escapeHtml = (text) => {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		};
		
		// Build ingredient options array from select and INGREDIENT_LOOKUP
		const ingredientOptions = [];
		if (ingredientSelect) {
			Array.from(ingredientSelect.options).forEach(option => {
				if (option.value) {
					const id = parseInt(option.value, 10);
					const ingredient = INGREDIENT_LOOKUP[id] || {};
					ingredientOptions.push({
						id: option.value,
						name: option.textContent,
						unit: option.getAttribute('data-unit') || ingredient.unit || '',
						quantity: ingredient.quantity || parseFloat(option.getAttribute('data-quantity') || '0'),
						nameLower: (option.getAttribute('data-name') || option.textContent.toLowerCase())
					});
				}
			});
		}
		updatePrepareControlsState();
		
		// Filter and render dropdown options
		function filterIngredients(searchTerm) {
			if (!ingredientDropdown) return;
			const term = (searchTerm || '').toLowerCase().trim();
			const filtered = term === '' 
				? ingredientOptions 
				: ingredientOptions.filter(opt => opt.nameLower.includes(term));
			
			if (filtered.length === 0) {
				ingredientDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No ingredients found</div>';
				ingredientDropdown.classList.remove('hidden');
				return;
			}
			
			ingredientDropdown.innerHTML = filtered.map(opt => {
				const stockQty = Number(opt.quantity || 0);
				const stockText = stockQty > 0 
					? `${Number(stockQty).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${opt.unit}`
					: 'Out of Stock';
				const stockColor = stockQty > 0 ? 'text-green-600' : 'text-red-600';
				const stockBg = stockQty > 0 ? 'bg-green-50' : 'bg-red-50';
				
				return `
				<button 
					type="button" 
					class="w-full text-left px-3 py-1.5 hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none transition-colors border-b border-gray-100 last:border-b-0"
					data-id="${opt.id}"
					data-unit="${opt.unit}"
				>
					<div class="flex items-center justify-between">
						<div class="flex-1">
							<div class="font-medium text-[10px] md:text-xs text-gray-900">${opt.name}</div>
							<div class="text-[9px] md:text-[10px] text-gray-500">${opt.unit}</div>
						</div>
						<div class="ml-2 text-right">
							<div class="text-[9px] md:text-[10px] font-semibold ${stockColor}">Stock</div>
							<div class="text-[10px] md:text-xs font-medium ${stockColor}">${stockText}</div>
						</div>
					</div>
				</button>
			`;
			}).join('');
			
			// Add click handlers
			ingredientDropdown.querySelectorAll('button').forEach(btn => {
				btn.addEventListener('click', () => {
					const id = btn.getAttribute('data-id');
					const unit = btn.getAttribute('data-unit');
					const name = btn.querySelector('.font-medium').textContent;
					const ingredient = INGREDIENT_LOOKUP[parseInt(id, 10)];
					
					// Update hidden select
					if (ingredientSelect) {
						ingredientSelect.value = id;
						updatePrepareControlsState();
					}
					
					// Update search input to show selected name
					if (ingredientSearch) {
						ingredientSearch.value = name;
					}
					
					// Hide dropdown
					ingredientDropdown.classList.add('hidden');
					
					// Trigger change event on select to update units
					if (ingredientSelect && ingredient) {
						configurePrepareUnits(ingredient.unit || '', ingredient.display_unit || null, ingredient.display_factor || 1);
					} else if (ingredientSelect) {
						ingredientSelect.dispatchEvent(new Event('change'));
					}
				});
			});
			
			ingredientDropdown.classList.remove('hidden');
		}
		
		// Search input handlers
		if (ingredientSearch && ingredientDropdown) {
			ingredientSearch.addEventListener('input', (e) => {
				filterIngredients(e.target.value);
			});
			
			ingredientSearch.addEventListener('focus', () => {
				if (ingredientSearch.value.trim() === '') {
					filterIngredients('');
				} else {
					filterIngredients(ingredientSearch.value);
				}
			});
			
			ingredientSearch.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					ingredientDropdown.classList.add('hidden');
					ingredientSearch.blur();
				} else if (e.key === 'ArrowDown') {
					e.preventDefault();
					const firstBtn = ingredientDropdown.querySelector('button');
					if (firstBtn) firstBtn.focus();
				}
			});
			
			// Close dropdown when clicking outside
			document.addEventListener('click', (e) => {
				if (!ingredientSearch.contains(e.target) && !ingredientDropdown.contains(e.target)) {
					ingredientDropdown.classList.add('hidden');
				}
			});
		}

		function openModal(button){
			// Remove any existing overlays
			const existingOverlays = document.querySelectorAll('.batch-detail-overlay, .delete-confirm-overlay');
			existingOverlays.forEach(ov => ov.remove());
			
			items = [];
			const json = button.getAttribute('data-items') || '[]';
			try {
				const parsed = JSON.parse(json);
				// Track the originally requested item ids for validation on distribute
				requestedItemIds = parsed
					.map(item => Number(item.item_id || item.id || 0))
					.filter(id => id > 0);
				items = parsed.map(item => ({
					id: Number(item.item_id || item.id),
					name: item.item_name || INGREDIENT_LOOKUP[item.item_id || item.id]?.name || 'Ingredient',
					unit: item.unit || INGREDIENT_LOOKUP[item.item_id || item.id]?.unit || '',
					quantity: Number(item.quantity || 0),
				})).filter(item => item.id && item.quantity > 0);
			} catch (err) {
				items = [];
				requestedItemIds = [];
			}
			document.getElementById('prepareModalBatchLabel').textContent = '#' + (button.getAttribute('data-batch') || '0');
			document.getElementById('prepareModalRequestName').textContent = button.getAttribute('data-requester') || '—';
			document.getElementById('prepareModalRequestDate').textContent = button.getAttribute('data-date') || '—';
			document.getElementById('prepareModalStaff').textContent = button.getAttribute('data-staff') || '—';
			
			// Populate ingredients as bullet list in 2-column grid
			const notesContainer = document.getElementById('prepareModalNotes');
			const notes = button.getAttribute('data-notes') || '';
			notesContainer.innerHTML = '';
			requestedNameLines = [];
			if (notes && notes !== '—') {
				const ingredientLines = notes.split('\n').filter(line => line.trim() !== '');
				if (ingredientLines.length > 0) {
					ingredientLines.forEach(line => {
						const li = document.createElement('li');
						li.className = 'text-[10px] md:text-xs text-gray-700 flex items-start gap-1.5';
						const bullet = document.createElement('span');
						bullet.className = 'text-gray-500 mt-0.5';
						bullet.textContent = '•';
						const text = document.createElement('span');
						text.textContent = line.trim();
						li.appendChild(bullet);
						li.appendChild(text);
						notesContainer.appendChild(li);
						requestedNameLines.push(line.trim().toLowerCase());
					});
				} else {
					const li = document.createElement('li');
					li.className = 'text-[10px] md:text-xs text-gray-700';
					li.textContent = '—';
					notesContainer.appendChild(li);
				}
			} else {
				const li = document.createElement('li');
				li.className = 'text-[10px] md:text-xs text-gray-700';
				li.textContent = '—';
				notesContainer.appendChild(li);
			}
			batchIdInput.value = button.getAttribute('data-batch') || '';
			actionInput.value = 'save';
			updatePrepareControlsState();
			renderItems();
			modal.classList.remove('hidden');
			modal.classList.add('flex');
			document.body.classList.add('overflow-hidden');
			
			// Prevent Enter key from submitting form unless in input field
			if (form) {
				form.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT') {
						e.preventDefault();
					}
				});
			}
			
			configurePrepareUnits('');
			// Clear search when opening modal
			if (ingredientSearch) ingredientSearch.value = '';
			if (ingredientDropdown) ingredientDropdown.classList.add('hidden');
		}

		function closeModal(){
			modal.classList.add('hidden');
			modal.classList.remove('flex');
			document.body.classList.remove('overflow-hidden');
			items = [];
			renderItems();
			quantityInput.value = '';
			if (ingredientSelect) ingredientSelect.value = '';
			updatePrepareControlsState();
			if (ingredientSearch) ingredientSearch.value = '';
			if (ingredientDropdown) ingredientDropdown.classList.add('hidden');
			errorBox.classList.add('hidden');
			requestedItemIds = [];
			requestedNameLines = [];
		}

		function showBuilderError(message){
			errorBox.textContent = message;
			errorBox.classList.remove('hidden');
		}

		function clearBuilderError(){
			errorBox.textContent = '';
			errorBox.classList.add('hidden');
		}

		// Custom confirmation modal function
		function showDistributeConfirmation() {
			return new Promise((resolve) => {
				const confirmModal = document.getElementById('distributeConfirmModal');
				if (!confirmModal) {
					resolve(false);
					return;
				}

				const yesBtn = document.getElementById('distributeConfirmYes');
				const cancelBtn = document.getElementById('distributeConfirmCancel');

				// Show modal
				confirmModal.classList.remove('hidden');
				confirmModal.classList.add('flex');
				document.body.classList.add('overflow-hidden');

				// Initialize icons if lucide is available
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}

				let resolved = false;

				// Handle Escape key
				const handleEscape = (e) => {
					if (e.key === 'Escape' && !resolved) {
						closeModal();
						resolved = true;
						resolve(false);
					}
				};
				document.addEventListener('keydown', handleEscape);

				// Close modal function
				const closeModal = () => {
					if (resolved) return;
					confirmModal.classList.add('hidden');
					confirmModal.classList.remove('flex');
					document.body.classList.remove('overflow-hidden');
					document.removeEventListener('keydown', handleEscape);
					confirmModal.removeEventListener('click', overlayClick);
					yesBtn.removeEventListener('click', yesClick);
					cancelBtn.removeEventListener('click', cancelClick);
				};

				// Close on overlay click
				const overlayClick = (e) => {
					if (e.target === confirmModal && !resolved) {
						closeModal();
						resolved = true;
						resolve(false);
					}
				};
				confirmModal.addEventListener('click', overlayClick);

				// Yes button handler
				const yesClick = () => {
					if (resolved) return;
					closeModal();
					resolved = true;
					resolve(true);
				};
				yesBtn.addEventListener('click', yesClick);

				// Cancel button handler
				const cancelClick = () => {
					if (resolved) return;
					closeModal();
					resolved = true;
					resolve(false);
				};
				cancelBtn.addEventListener('click', cancelClick);
			});
		}

		function renderItems(){
			itemsBody.innerHTML = '';
			dynamicInputs.innerHTML = '';
			if (!items.length){
				emptyState.classList.remove('hidden');
				return;
			}
			emptyState.classList.add('hidden');
			items.forEach((item, index) => {
				const tr = document.createElement('tr');
				tr.innerHTML = `
					<td class="px-2 md:px-2.5 py-1.5 md:py-2">
						<div>
							<p class="font-semibold text-gray-900 text-[10px] md:text-xs">${escapeHtml(item.name || '')}</p>
							<p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5">${escapeHtml(item.display_unit || item.unit || '')}</p>
						</div>
					</td>
					<td class="px-2 md:px-2.5 py-1.5 md:py-2">
						<div class="flex flex-col leading-tight">
							<span class="font-bold text-gray-900 text-[10px] md:text-xs">
								${Number(item.quantity || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${escapeHtml(item.unit || '')}
							</span>
							${(item.display_unit && item.display_unit !== item.unit) ? `<span class="text-[9px] md:text-[10px] text-gray-500">Selected unit: ${escapeHtml(item.display_unit)}</span>` : ''}
						</div>
					</td>
					<td class="px-2 md:px-2.5 py-1.5 md:py-2">
						<button type="button" class="text-red-600 hover:text-red-700 text-[9px] md:text-[10px] font-medium removePrepItem" data-index="${index}">
							Remove
						</button>
					</td>
				`;
				itemsBody.appendChild(tr);
				const idInput = document.createElement('input');
				idInput.type = 'hidden';
				idInput.name = 'item_id[]';
				idInput.value = item.id;
				const qtyInput = document.createElement('input');
				qtyInput.type = 'hidden';
				qtyInput.name = 'quantity[]';
				qtyInput.value = item.quantity;
				const unitInput = document.createElement('input');
				unitInput.type = 'hidden';
				unitInput.name = 'unit_display[]';
				unitInput.value = item.display_unit || item.unit;
				dynamicInputs.appendChild(idInput);
				dynamicInputs.appendChild(qtyInput);
				dynamicInputs.appendChild(unitInput);
			});
		}

		function configurePrepareUnits(baseUnit, displayUnit = null, displayFactor = 1){
			if (!unitSelect) return;
			unitSelect.innerHTML = '';
			const opt = (value, label)=>{ const o=document.createElement('option'); o.value=value; o.textContent=label; return o; };
			
			// Only show base unit and display unit (if different) when ingredient is selected
			if (baseUnit) {
				// Always show base unit first
				unitSelect.appendChild(opt(baseUnit, baseUnit));
				
				// Add display unit if it exists and is different from base unit
				if (displayUnit && displayUnit.toLowerCase() !== baseUnit.toLowerCase()) {
					const displayLabel = displayUnit.charAt(0).toUpperCase() + displayUnit.slice(1);
					unitSelect.appendChild(opt(displayUnit, displayLabel));
				}
				
				// Set default to base unit
				unitSelect.value = baseUnit;
			} else {
				// No ingredient selected, show default
				unitSelect.appendChild(opt('pcs', 'pcs'));
				unitSelect.value = 'pcs';
			}
		}

		ingredientSelect.addEventListener('change', ()=>{
			updatePrepareControlsState();
			const ing = INGREDIENT_LOOKUP[parseInt(ingredientSelect.value || '0', 10)];
			if (ing) {
				configurePrepareUnits(ing.unit || '', ing.display_unit || null, ing.display_factor || 1);
			} else {
				configurePrepareUnits('');
			}
		});

		addBtn.addEventListener('click', ()=>{
			const id = parseInt(ingredientSelect.value || '0', 10);
			const quantity = parseFloat(quantityInput.value || '0');
			if (!id || !quantity || quantity <= 0){
				showBuilderError('Select an ingredient and provide a valid quantity.');
				return;
			}
			const ingredient = INGREDIENT_LOOKUP[id];
			if (!ingredient){
				showBuilderError('Selected ingredient does not exist.');
				return;
			}
			let baseQuantity = quantity;
			let chosenUnit = unitSelect.value || ingredient.unit;
			const baseUnit = ingredient.unit;
			
			// Convert to base unit
			if (chosenUnit === 'g' && baseUnit === 'kg') {
				// User selected g, base is kg: convert g to kg
				baseQuantity = quantity / 1000;
			} else if (chosenUnit === 'kg' && baseUnit === 'g') {
				// User selected kg, base is g: convert kg to g
				baseQuantity = quantity * 1000;
			} else if (chosenUnit === 'ml' && baseUnit === 'L') {
				// User selected ml, base is L: convert ml to L
				baseQuantity = quantity / 1000;
			} else if (chosenUnit === 'L' && baseUnit === 'ml') {
				// User selected L, base is ml: convert L to ml
				baseQuantity = quantity * 1000;
			} else if ((chosenUnit === 'g' || chosenUnit === 'kg') && baseUnit !== 'g' && baseUnit !== 'kg') {
				// User selected g/kg but base unit is something else (like 'sack')
				// Use display_factor if available to convert
				// display_factor means: 1 base unit = display_factor * display_unit
				// Example: 1 sack = 25 kg (display_unit='kg', display_factor=25)
				// So: 1 kg = 1/25 sacks
				if (ingredient.display_factor && ingredient.display_factor > 0 && ingredient.display_unit) {
					const displayUnit = ingredient.display_unit.toLowerCase();
					if (displayUnit === 'kg' || displayUnit === 'g') {
						// Convert to kg first if needed
						const qtyInKg = chosenUnit === 'g' ? quantity / 1000 : quantity;
						// Convert to base unit: kg / factor = base units
						baseQuantity = qtyInKg / ingredient.display_factor;
					} else {
						// Display unit doesn't match, can't convert accurately
						// Store as-is (user should use base unit or set display_unit/factor)
						baseQuantity = quantity;
					}
				} else {
					// No conversion factor available - show warning but allow it
					// The quantity will be stored directly in base unit
					// This means if user enters 1kg and base is "sack", it stores as 1 sack
					// User should set display_unit and display_factor in inventory for accurate conversion
					baseQuantity = quantity;
				}
			} else if ((chosenUnit === 'ml' || chosenUnit === 'L') && baseUnit !== 'ml' && baseUnit !== 'L') {
				// Similar logic for volume units
				if (ingredient.display_factor && ingredient.display_factor > 0 && ingredient.display_unit) {
					const displayUnit = ingredient.display_unit.toLowerCase();
					if (displayUnit === 'l' || displayUnit === 'ml') {
						const qtyInL = chosenUnit === 'ml' ? quantity / 1000 : quantity;
						baseQuantity = qtyInL / ingredient.display_factor;
					} else {
						baseQuantity = quantity;
					}
				} else {
					baseQuantity = quantity;
				}
			} else if (chosenUnit.toLowerCase() === (ingredient.display_unit || '').toLowerCase() && baseUnit !== chosenUnit) {
				// User selected display_unit (e.g., "pack" when base is "pcs")
				// display_factor means: 1 display_unit = display_factor * base_unit
				// Example: 1 pack = 24 pcs (display_factor = 24)
				// So: quantity in display_unit * factor = base quantity
				if (ingredient.display_factor && ingredient.display_factor > 0) {
					baseQuantity = quantity * ingredient.display_factor;
				} else {
					// No factor set, assume 1:1
					baseQuantity = quantity;
				}
			}
			
			// Check if ingredient already exists in the list
			const existing = items.find(entry => entry.id === id);
			const totalRequestedQty = (existing ? existing.quantity + baseQuantity : baseQuantity);
			
			// Stock validation before adding
			const currentStock = ingredient.quantity || 0;
			const reorderLevel = ingredient.reorder_level || 0;
			
			// Check if out of stock
			if (currentStock <= 0) {
				showBuilderError('This ingredient is out of stock and cannot be added to the request.');
				return;
			}
			
			// Check if low stock - show warning if ingredient is low stock (regardless of quantity)
			const isLowStock = currentStock <= reorderLevel && currentStock > 0;
			if (isLowStock) {
				// Show confirmation modal for low stock
				const confirmOverlay = document.createElement('div');
				confirmOverlay.className = 'low-stock-confirm-overlay fixed inset-0 z-[999999] hidden overflow-hidden';
				confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 999999 !important; pointer-events: none !important;';
				const confirmBackdrop = document.createElement('div');
				confirmBackdrop.className = 'fixed inset-0 bg-black/50';
				confirmBackdrop.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;';
				confirmOverlay.appendChild(confirmBackdrop);
				const confirmContainer = document.createElement('div');
				confirmContainer.className = 'relative z-10 flex min-h-full items-center justify-center p-4 overflow-y-auto overflow-x-hidden';
				
				const confirmModal = document.createElement('div');
				confirmModal.className = 'bg-white rounded-xl shadow-none max-w-md w-full mx-auto my-auto';
				confirmModal.style.cssText = 'max-width: 28rem; margin-top: auto !important; margin-bottom: auto !important;';
				confirmModal.style.pointerEvents = 'auto';
				const confirmModalContent = document.createElement('div');
				confirmModalContent.className = 'p-6';
				
				const stockDisplay = currentStock.toFixed(2) + ' ' + (ingredient.display_unit || ingredient.unit || '');
				
				confirmModalContent.innerHTML = `
					<div class="flex items-center gap-4 mb-4">
						<div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
							<i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
						</div>
						<div>
							<h3 class="text-sm md:text-base font-semibold text-gray-900">Low Stock Warning</h3>
							<p class="text-[10px] md:text-xs text-gray-500 mt-0.5 md:mt-1">Insufficient inventory</p>
						</div>
					</div>
					<p class="text-xs md:text-sm text-gray-700 mb-4 md:mb-6">
						Ingredient "<strong>${escapeHtml(ingredient.name)}</strong>" is low stock with remaining <strong>${stockDisplay}</strong>. Do you still wish to proceed?
					</p>
					<div class="flex items-center justify-end gap-2 md:gap-3">
						<button type="button" class="cancelLowStockBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
							No
						</button>
						<button type="button" class="confirmLowStockBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
							Yes
						</button>
					</div>
				`;
				
				confirmModal.appendChild(confirmModalContent);
				confirmContainer.appendChild(confirmModal);
				confirmOverlay.appendChild(confirmContainer);
				confirmOverlay.classList.remove('hidden');
				document.body.appendChild(confirmOverlay);
				document.body.classList.add('overflow-hidden');
				
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
				
				// Helper function to escape HTML
				function escapeHtml(text) {
					const div = document.createElement('div');
					div.textContent = text;
					return div.innerHTML;
				}
				
				// Handle confirmation
				const proceedWithAdd = () => {
					confirmOverlay.remove();
					document.body.classList.remove('overflow-hidden');
					
					// Proceed with adding the ingredient
					if (existing){
						existing.quantity += baseQuantity;
						existing.display_unit = chosenUnit;
					} else {
						items.push({
							id,
							name: ingredient.name,
							unit: ingredient.unit,
							display_unit: chosenUnit,
							quantity: baseQuantity,
						});
					}
					if (ingredientSelect) ingredientSelect.value = '';
					updatePrepareControlsState();
					if (ingredientSearch) ingredientSearch.value = '';
					if (ingredientDropdown) ingredientDropdown.classList.add('hidden');
					quantityInput.value = '';
					configurePrepareUnits('');
					clearBuilderError();
					renderItems();
				};
				
				confirmModal.querySelector('.cancelLowStockBtn').addEventListener('click', () => {
					confirmOverlay.remove();
					document.body.classList.remove('overflow-hidden');
				});
				
				confirmModal.querySelector('.confirmLowStockBtn').addEventListener('click', proceedWithAdd);
				
				// Close on overlay click
				confirmOverlay.addEventListener('click', (e) => {
					if (e.target === confirmOverlay) {
						confirmOverlay.remove();
						document.body.classList.remove('overflow-hidden');
					}
				});
				
				return; // Stop here, wait for user confirmation
			}
			
			// Stock is sufficient, proceed with adding
			if (existing){
				existing.quantity += baseQuantity;
				existing.display_unit = chosenUnit;
			} else {
				items.push({
					id,
					name: ingredient.name,
					unit: ingredient.unit,
					display_unit: chosenUnit,
					quantity: baseQuantity,
				});
			}
			if (ingredientSelect) ingredientSelect.value = '';
			updatePrepareControlsState();
			if (ingredientSearch) ingredientSearch.value = '';
			if (ingredientDropdown) ingredientDropdown.classList.add('hidden');
			quantityInput.value = '';
			configurePrepareUnits('');
			clearBuilderError();
			renderItems();
		});

		itemsBody.addEventListener('click', (event)=>{
			const btn = event.target.closest('.removePrepItem');
			if (!btn) return;
			const index = parseInt(btn.getAttribute('data-index') || '-1', 10);
			if (index < 0 || !items[index]) return;
			
			const item = items[index];
			const ingredientName = item.name || 'this ingredient';
			const quantity = item.quantity || 0;
			const unit = item.display_unit || item.unit || '';
			
			// Remove any existing confirmation overlays
			const existingConfirmOverlays = document.querySelectorAll('.remove-ingredient-confirm-overlay');
			existingConfirmOverlays.forEach(ov => ov.remove());
			
			// Create confirmation modal
			const confirmOverlay = document.createElement('div');
			confirmOverlay.className = 'remove-ingredient-confirm-overlay fixed inset-0 z-[999999] hidden overflow-hidden';
			confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 999999 !important; pointer-events: none !important;';
			const confirmBackdrop = document.createElement('div');
			confirmBackdrop.className = 'fixed inset-0 bg-black/50';
			confirmBackdrop.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;';
			confirmOverlay.appendChild(confirmBackdrop);
			const confirmContainer = document.createElement('div');
			confirmContainer.className = 'relative z-10 flex min-h-full items-center justify-center p-4 overflow-y-auto overflow-x-hidden';
			
			// Make the modal itself clickable
			const confirmModal = document.createElement('div');
			confirmModal.className = 'bg-white rounded-xl shadow-none max-w-sm w-full mx-auto';
			confirmModal.style.maxWidth = '24rem';
			confirmModal.style.pointerEvents = 'auto';
			const confirmModalContent = document.createElement('div');
			confirmModalContent.className = 'p-6';
			confirmModalContent.innerHTML = `
					<div class="flex items-center gap-3 mb-3">
						<div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
							<i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
						</div>
						<div>
							<h3 class="text-xs md:text-sm font-semibold text-gray-900">Remove Ingredient</h3>
							<p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5">This action cannot be undone</p>
						</div>
					</div>
					<p class="text-[10px] md:text-xs text-gray-700 mb-3 md:mb-4">
						Are you sure you want to remove <strong>${ingredientName}</strong> (${quantity} ${unit}) from this request?
					</p>
					<div class="flex items-center justify-end gap-2">
						<button type="button" class="cancelRemoveIngredientBtn inline-flex items-center justify-center px-2.5 md:px-3 py-1.5 text-[10px] md:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
							Cancel
						</button>
						<button type="button" class="confirmRemoveIngredientBtn inline-flex items-center justify-center px-2.5 md:px-3 py-1.5 text-[10px] md:text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							Remove
						</button>
					</div>
			`;
			
			confirmModal.appendChild(confirmModalContent);
			confirmContainer.appendChild(confirmModal);
			confirmOverlay.appendChild(confirmContainer);
			confirmOverlay.classList.remove('hidden');
			document.body.appendChild(confirmOverlay);
			document.body.classList.add('overflow-hidden');
			
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
			
			// Close on Escape key handler
			const handleEscape = (e) => {
				if (e.key === 'Escape') {
					closeConfirmModal();
				}
			};
			
			const closeConfirmModal = () => {
				document.removeEventListener('keydown', handleEscape);
				confirmOverlay.remove();
				document.body.classList.remove('overflow-hidden');
			};
			
			// Close on overlay click
			confirmOverlay.addEventListener('click', (e) => {
				if (e.target === confirmOverlay) {
					closeConfirmModal();
				}
			});
			
			// Add Escape key listener
			document.addEventListener('keydown', handleEscape);
			
			confirmModal.querySelector('.cancelRemoveIngredientBtn').addEventListener('click', closeConfirmModal);
			
			confirmModal.querySelector('.confirmRemoveIngredientBtn').addEventListener('click', function() {
				if (index >= 0 && items[index]) {
					items.splice(index, 1);
					renderItems();
				}
				closeConfirmModal();
			});
		});

		form.addEventListener('submit', (event)=>{
			if (!items.length){
				event.preventDefault();
				showBuilderError('Add at least one ingredient before submitting.');
			}
		});

		modal.querySelectorAll('.prepareSubmitBtn').forEach(btn => {
			btn.addEventListener('click', async ()=>{
				const action = btn.getAttribute('data-action') || 'save';
				if (!items.length){
					showBuilderError('Add at least one ingredient before submitting.');
					return;
				}

				// For distribute action, show custom confirmation modal
				if (action === 'distribute') {
					const confirmed = await showDistributeConfirmation();
					if (!confirmed) {
						return;
					}
				}

				clearBuilderError();
				actionInput.value = action;
				form.submit();
			});
		});

		document.querySelectorAll('.prepareBatchBtn').forEach(btn => {
			btn.addEventListener('click', ()=> openModal(btn));
		});

		closeBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (event)=>{
			if (event.target === modal){
				closeModal();
			}
		});
	}

	initPrepareModal();
})();

// Edit Request Modal
(function() {
	const editModal = document.getElementById('editRequestModal');
	const closeEditBtn = document.getElementById('closeEditModal');
	const cancelEditBtn = document.getElementById('cancelEditModal');
	const editBatchId = document.getElementById('editBatchId');
	const editRequesterName = document.getElementById('editRequesterName');
	const editIngredientsNote = document.getElementById('editIngredientsNote');
	const editRequestDate = document.getElementById('editRequestDate');

	function openEditModal(batchId, requesterName, ingredientsNote, requestDate) {
		// Remove any existing overlays
		const existingOverlays = document.querySelectorAll('.batch-detail-overlay, .delete-confirm-overlay');
		existingOverlays.forEach(ov => ov.remove());
		
		editBatchId.value = batchId;
		editRequesterName.value = requesterName || '';
		editIngredientsNote.value = ingredientsNote || '';
		editRequestDate.value = requestDate || '';
		editModal.classList.remove('hidden');
		editModal.classList.add('flex');
		document.body.classList.add('overflow-hidden');
		
		// Prevent Enter key from submitting form unless in input field
		const form = editModal.querySelector('form');
		if (form) {
			form.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
					e.preventDefault();
				}
			});
		}
		
		if (typeof lucide !== 'undefined') {
			lucide.createIcons();
		}
	}

	function closeEditModal() {
		editModal.classList.add('hidden');
		editModal.classList.remove('flex');
		document.body.classList.remove('overflow-hidden');
		editBatchId.value = '';
		editRequesterName.value = '';
		editIngredientsNote.value = '';
		editRequestDate.value = '';
	}

	document.querySelectorAll('.editRequestBtn').forEach(btn => {
		btn.addEventListener('click', () => {
			const batchId = btn.getAttribute('data-batch-id');
			const requesterName = btn.getAttribute('data-requester-name') || '';
			const ingredientsNote = btn.getAttribute('data-ingredients-note') || '';
			const requestDate = btn.getAttribute('data-request-date') || '';
			openEditModal(batchId, requesterName, ingredientsNote, requestDate);
		});
	});

	if (closeEditBtn) {
		closeEditBtn.addEventListener('click', closeEditModal);
	}
	if (cancelEditBtn) {
		cancelEditBtn.addEventListener('click', closeEditModal);
	}
	if (editModal) {
		editModal.addEventListener('click', (event) => {
			if (event.target === editModal) {
				closeEditModal();
			}
		});
	}
})();

// New Request Modal
(function() {
	const newRequestModal = document.getElementById('newRequestModal');
	const newRequestBtn = document.getElementById('newRequestBtn');
	const closeNewRequestModal = document.getElementById('closeNewRequestModal');
	const cancelNewRequestBtn = document.getElementById('cancelNewRequestBtn');

	function openNewRequestModal() {
		if (newRequestModal) {
			// Remove any existing overlays
			const existingOverlays = document.querySelectorAll('.batch-detail-overlay, .delete-confirm-overlay');
			existingOverlays.forEach(ov => ov.remove());
			
			newRequestModal.classList.remove('hidden');
			newRequestModal.classList.add('flex');
			document.body.classList.add('overflow-hidden');
			
			// Prevent Enter key from submitting form unless in input field
			const form = newRequestModal.querySelector('form');
			if (form) {
				form.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
						e.preventDefault();
					}
				});
			}
			
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
		}
	}

	function closeNewRequestModalFunc() {
		if (newRequestModal) {
			newRequestModal.classList.add('hidden');
			newRequestModal.classList.remove('flex');
			document.body.classList.remove('overflow-hidden');
		}
	}

	if (newRequestBtn) {
		newRequestBtn.addEventListener('click', openNewRequestModal);
	}

	if (closeNewRequestModal) {
		closeNewRequestModal.addEventListener('click', closeNewRequestModalFunc);
	}

	if (cancelNewRequestBtn) {
		cancelNewRequestBtn.addEventListener('click', closeNewRequestModalFunc);
	}

	if (newRequestModal) {
		newRequestModal.addEventListener('click', (event) => {
			if (event.target === newRequestModal) {
				closeNewRequestModalFunc();
			}
		});
	}
})();

// Custom Confirmation Modal
(function() {
	// Create confirmation modal HTML
	const confirmationModalHTML = `
		<div id="customConfirmModal" class="fixed inset-0 z-[100001] hidden overflow-hidden" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important; z-index: 100001 !important;">
			<div class="fixed inset-0 bg-black/50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; margin: 0 !important;"></div>
			<div class="relative z-10 flex items-center justify-center p-4" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; overflow-y: auto !important; overflow-x: hidden !important;">
				<div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-auto my-auto p-6" style="max-width: 28rem; margin-top: auto !important; margin-bottom: auto !important;">
					<div class="flex items-start gap-4">
						<div class="flex-shrink-0">
							<div id="confirmModalIcon" class="w-10 h-10 rounded-full flex items-center justify-center">
								<i data-lucide="alert-circle" class="w-6 h-6"></i>
							</div>
						</div>
						<div class="flex-1">
							<h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
							<p id="confirmModalMessage" class="text-sm text-gray-600 mb-4"></p>
							<div class="flex items-center justify-end gap-3">
								<button type="button" id="confirmModalCancel" class="inline-flex items-center justify-center gap-1.5 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm transition-colors">
									Cancel
								</button>
								<button type="button" id="confirmModalConfirm" class="inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm transition-colors">
									Confirm
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	`;
	
	// Insert modal into body
	document.body.insertAdjacentHTML('beforeend', confirmationModalHTML);
	
	const modal = document.getElementById('customConfirmModal');
	const modalTitle = document.getElementById('confirmModalTitle');
	const modalMessage = document.getElementById('confirmModalMessage');
	const modalIcon = document.getElementById('confirmModalIcon');
	const confirmBtn = document.getElementById('confirmModalConfirm');
	const cancelBtn = document.getElementById('confirmModalCancel');
	
	let pendingForm = null;
	let pendingResolve = null;
	
	function showConfirmModal(message, type = 'info') {
		return new Promise((resolve) => {
			pendingResolve = resolve;
			
			// Set message
			modalMessage.textContent = message;
			
			// Set icon and colors based on type
			const iconElement = modalIcon.querySelector('i');
			if (type === 'warning' || type === 'danger') {
				modalIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-red-100';
				iconElement.setAttribute('data-lucide', 'alert-triangle');
				iconElement.className = 'w-6 h-6 text-red-600';
				confirmBtn.className = 'inline-flex items-center justify-center gap-1.5 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm transition-colors';
			} else {
				modalIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-blue-100';
				iconElement.setAttribute('data-lucide', 'info');
				iconElement.className = 'w-6 h-6 text-blue-600';
				confirmBtn.className = 'inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm transition-colors';
			}
			
			// Update lucide icons
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
			
			// Show modal
			modal.classList.remove('hidden');
			document.body.classList.add('overflow-hidden');
		});
	}
	
	function closeConfirmModal() {
		modal.classList.add('hidden');
		document.body.classList.remove('overflow-hidden');
		pendingForm = null;
		pendingResolve = null;
	}
	
	confirmBtn.addEventListener('click', function() {
		if (pendingResolve) {
			pendingResolve(true);
		}
		if (pendingForm) {
			pendingForm.submit();
		}
		closeConfirmModal();
	});
	
	cancelBtn.addEventListener('click', function() {
		if (pendingResolve) {
			pendingResolve(false);
		}
		closeConfirmModal();
	});
	
	// Close on backdrop click
	modal.addEventListener('click', function(e) {
		if (e.target === modal || e.target.classList.contains('bg-black/50')) {
			if (pendingResolve) {
				pendingResolve(false);
			}
			closeConfirmModal();
		}
	});
	
	// Handle forms with data-confirm attribute
	function attachConfirmHandlers() {
		document.querySelectorAll('form[data-confirm]:not([data-handler-attached])').forEach(form => {
			form.setAttribute('data-handler-attached', 'true');
			form.addEventListener('submit', async function(e) {
				e.preventDefault();
				const confirmMessage = form.getAttribute('data-confirm');
				const confirmType = form.getAttribute('data-confirm-type') || 'info';
				
				if (confirmMessage) {
					pendingForm = form;
					const confirmed = await showConfirmModal(confirmMessage, confirmType);
					if (!confirmed) {
						pendingForm = null;
						return false;
					}
				}
				
				// If confirmed, submit the form
				if (pendingForm) {
					const formToSubmit = pendingForm;
					pendingForm = null;
					formToSubmit.submit();
				}
			});
		});
	}
	
	// Attach handlers on page load
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', attachConfirmHandlers);
	} else {
		attachConfirmHandlers();
	}
	
	// Also attach handlers after dynamic content is added
	const observer = new MutationObserver(function() {
		setTimeout(attachConfirmHandlers, 50);
	});
	if (document.body) {
		observer.observe(document.body, { childList: true, subtree: true });
	}
})();

</script>


