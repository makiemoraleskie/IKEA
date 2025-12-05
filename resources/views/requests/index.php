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
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div>
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1">Ingredient Requests</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Manage ingredient requests and batch approvals</p>
		</div>
	</div>
</div>

<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner'], true)): ?>
<!-- New Request Button -->
<div class="mb-4 md:mb-8">
	<button type="button" id="newRequestBtn" class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-4 lg:px-5 py-1.5 md:py-2 lg:py-2.5 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
		<i data-lucide="plus-circle" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
		New Batch Request
	</button>
</div>

<!-- New Request Modal -->
<div id="newRequestModal" class="fixed inset-0 bg-black/60 backdrop-blur-md z-[99999] hidden items-center justify-center p-4" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important; z-index: 99999 !important;">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
		<div class="flex items-center justify-between px-4 md:px-5 lg:px-6 py-4 border-b">
			<div>
				<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
					<i data-lucide="plus-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-green-600"></i>
					New Batch Request
				</h2>
				<p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">Describe what you need and when it's required.</p>
			</div>
			<button type="button" id="closeNewRequestModal" class="text-gray-500 hover:text-gray-700 text-xl md:text-2xl leading-none" aria-label="Close">&times;</button>
		</div>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" class="p-3 md:p-4 lg:p-5 space-y-3 md:space-y-4 lg:space-y-5">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
				<div class="space-y-1.5 md:space-y-2">
					<label class="block text-xs md:text-sm font-medium text-gray-700">Name</label>
					<input name="requester_name" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500" placeholder="e.g., Juan Dela Cruz" required>
				</div>
				<div class="space-y-1.5 md:space-y-2">
					<label class="block text-xs md:text-sm font-medium text-gray-700">Date Needed</label>
					<input type="date" name="request_date" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500" required>
				</div>
			</div>
			<div class="space-y-1.5 md:space-y-2">
				<label class="block text-xs md:text-sm font-medium text-gray-700">Ingredients / Notes</label>
				<textarea name="ingredients_note" rows="4" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500" placeholder="List ingredients, quantities, or any prep instructions" required></textarea>
				<p class="text-[10px] md:text-xs text-gray-500">Detailed quantities will be captured later during the Prepare step.</p>
			</div>
			<div class="flex justify-end gap-2 md:gap-3">
				<button type="button" id="cancelNewRequestBtn" class="inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors text-xs md:text-sm">
					Cancel
				</button>
					<button type="submit" class="inline-flex items-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="send" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Submit Request
				</button>
			</div>
		</form>
	</div>
</div>
<?php endif; ?>

<?php if (Auth::role() !== 'Kitchen Staff'): ?>
<!-- Batch Requests Table -->
<div id="requests-history" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
        <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-600"></i>
                    Batch Requests History
                </h2>
                <p class="text-[10px] md:text-xs text-gray-600 mt-0.5 md:mt-1">View and manage all ingredient requests</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 md:gap-2 text-xs md:text-sm text-gray-600">
                <label for="requestStatusFilter" class="whitespace-nowrap">Filter status:</label>
                <select id="requestStatusFilter" data-default="<?php echo htmlspecialchars($statusFilter); ?>" class="w-full sm:w-auto border border-gray-300 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
						<option value="to prepare">To Prepare</option>
						<option value="distributed">Distributed</option>
                </select>
            </div>
        </div>
	</div>
	
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="sticky top-0 bg-white z-10">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Requested Name</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Requested Ingredient</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Date Needed</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 bg-white text-[10px] md:text-xs lg:text-sm">Status</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<tr class="transition-colors" data-status="<?php echo strtolower($b['status'] ?? ''); ?>" data-detail-id="batch-<?php echo (int)$b['id']; ?>">
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<div>
							<p class="text-[10px] md:text-xs lg:text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></p>
							<p class="text-[9px] md:text-[10px] lg:text-xs text-gray-500">Created by <?php echo htmlspecialchars($b['staff_name'] ?? ''); ?></p>
						</div>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
                        <button type="button" class="text-green-600 hover:text-green-700 text-[10px] md:text-xs lg:text-sm font-medium viewBatchDetails" data-batch="<?php echo (int)$b['id']; ?>" data-status="<?php echo htmlspecialchars($b['status'] ?? ''); ?>">View details</button>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 text-gray-600 text-[10px] md:text-xs lg:text-sm">
						<?php 
						$dateNeeded = !empty($b['custom_request_date']) ? $b['custom_request_date'] : (substr($b['date_requested'], 0, 10));
						echo htmlspecialchars(formatDate($dateNeeded)); 
						?>
					</td>
					<td class="px-3 md:px-4 lg:px-6 py-2.5 md:py-3 lg:py-4">
						<?php 
						$status = strtolower($b['status'] ?? '');
						$statusText = match($status) {
							'distributed' => 'Done',
							'to prepare' => 'Preparing',
							'pending' => 'Pending',
							'rejected' => 'Rejected',
							'approved' => 'Approved',
							default => htmlspecialchars($b['status'] ?? '')
						};
						$statusColor = match($status) {
							'distributed' => 'text-green-600',
							'to prepare' => 'text-orange-600',
							'pending' => 'text-gray-700',
							'rejected' => 'text-red-600',
							'approved' => 'text-blue-800',
							default => 'text-gray-700'
						};
						?>
						<span class="inline-flex items-center gap-1 px-2 md:px-2.5 lg:px-3 py-1 md:py-1.5 rounded-lg text-[9px] md:text-[10px] lg:text-xs font-semibold <?php echo $statusColor; ?> whitespace-nowrap">
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
								$statusColor = match($b['status']) {
									'Distributed' => 'bg-green-100 text-green-800 border-green-200',
									'Rejected' => 'bg-red-100 text-red-800 border-red-200',
									'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
									default => 'bg-gray-100 text-gray-700 border-gray-200'
								};
								?>
								<span class="inline-flex items-center gap-1.5 px-2.5 md:px-3 py-1.5 rounded-lg text-xs font-semibold border <?php echo $statusColor; ?> shrink-0 whitespace-nowrap">
									<i data-lucide="circle" class="w-2 h-2 fill-current"></i>
									<?php echo htmlspecialchars($b['status']); ?>
								</span>
							</div>

							<!-- Requested Items Section -->
							<?php if (!empty($b['custom_ingredients'])): ?>
							<div class="p-3 md:p-4 border border-gray-200 rounded-lg">
								<p class="text-xs uppercase tracking-wide text-gray-500 mb-2 md:mb-3">Requested Items</p>
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
								$isDistributed = (strtolower($b['status'] ?? '') === 'distributed');
								$batchId = (int)$b['id'];
							?>
							<div class="space-y-3 md:space-y-4 pt-2 border-t border-gray-100">
								<div class="flex items-center gap-1.5 md:gap-2">
									<i data-lucide="list-checks" class="w-4 h-4 md:w-5 md:h-5 text-green-600"></i>
									<h3 class="text-xs md:text-xs font-bold text-gray-900 tracking-wide">Items In Batch</h3>
								</div>
								<div class="overflow-x-auto rounded-lg border border-gray-200">
									<table class="w-full text-xs md:text-sm">
										<thead class="bg-gray-100">
											<tr>
												<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Ingredient</th>
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
													<span class="font-bold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
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
														<span class="font-semibold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($stockDisplay); ?></span>
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
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 md:mb-8 overflow-hidden">
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
	<div class="overflow-x-auto overflow-y-auto max-h-[500px] md:max-h-[600px]">
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
							<?php echo htmlspecialchars(formatDate($batch['date_requested'])); ?>
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
									data-date="<?php echo htmlspecialchars($batch['custom_request_date'] ?: substr((string)($batch['date_requested'] ?? ''), 0, 10)); ?>"
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
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
	<div class="px-4 md:px-6 py-3 md:py-4 border-b">
		<h2 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-1.5 md:gap-2">
			<i data-lucide="clock" class="w-4 h-4 md:w-5 md:h-5 text-gray-600"></i>
			Request History
		</h2>
		<p class="text-xs md:text-sm text-gray-600 mt-0.5 md:mt-1">Track the status of your submitted requests</p>
	</div>
	<div class="overflow-x-auto">
		<table class="w-full text-[10px] md:text-xs lg:text-sm" style="min-width: 100%;">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Batch ID</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Request Name</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Ingredients</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Date Requested</th>
					<th class="text-left px-3 md:px-4 lg:px-6 py-2 md:py-2.5 lg:py-3 font-medium text-gray-700 text-[10px] md:text-xs lg:text-sm">Status</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): ?>
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
						$userId = Auth::id() ?? 0;
						$isRequester = ((int)($b['staff_id'] ?? 0) === $userId);
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
						<span class="inline-flex items-center gap-0.5 md:gap-1 px-2 md:px-2.5 lg:px-3 py-0.5 md:py-1 rounded-full text-[9px] md:text-[10px] lg:text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 whitespace-nowrap">
							<i data-lucide="clock" class="w-2.5 h-2.5 md:w-3 md:h-3"></i>
							<?php echo htmlspecialchars($b['status']); ?>
						</span>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden" data-detail-for="<?php echo (int)$b['id']; ?>">
					<td colspan="5" class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6">
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
								$statusColor = match($b['status']) {
									'Distributed' => 'bg-green-100 text-green-800 border-green-200',
									'Rejected' => 'bg-red-100 text-red-800 border-red-200',
									'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
									default => 'bg-gray-100 text-gray-700 border-gray-200'
								};
								?>
								<span class="inline-flex items-center gap-1.5 px-2.5 md:px-3 py-1.5 rounded-lg text-xs font-semibold border <?php echo $statusColor; ?> shrink-0 whitespace-nowrap">
									<i data-lucide="circle" class="w-2 h-2 fill-current"></i>
									<?php echo htmlspecialchars($b['status']); ?>
								</span>
							</div>

							<!-- Requested Items Section -->
							<?php if (!empty($b['custom_ingredients'])): ?>
							<div class="p-3 md:p-4 border border-gray-200 rounded-lg">
								<p class="text-xs uppercase tracking-wide text-gray-500 mb-2 md:mb-3">Requested Items</p>
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
								$isDistributed = (strtolower($b['status'] ?? '') === 'distributed');
								$batchId = (int)$b['id'];
							?>
							<div class="space-y-3 md:space-y-4 pt-2 border-t border-gray-100">
								<div class="flex items-center gap-1.5 md:gap-2">
									<i data-lucide="list-checks" class="w-4 h-4 md:w-5 md:h-5 text-green-600"></i>
									<h3 class="text-xs md:text-xs font-bold text-gray-900 tracking-wide">Items In Batch</h3>
								</div>
								<div class="overflow-x-auto rounded-lg border border-gray-200">
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
													<span class="font-bold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
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
														<span class="font-semibold text-gray-900 text-xs md:text-sm"><?php echo htmlspecialchars($stockDisplay); ?></span>
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
<div id="editRequestModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
	<div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
		<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 md:px-5 lg:px-6 py-4 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4">
			<h2 class="text-sm md:text-base font-semibold text-gray-900 flex items-center gap-1 md:gap-1.5">
				<i data-lucide="edit" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600"></i>
				Edit Request
			</h2>
			<button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
				<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
			</button>
		</div>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/update" class="p-3 md:p-4 lg:p-5 space-y-3 md:space-y-4 lg:space-y-5">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<input type="hidden" name="batch_id" id="editBatchId" value="">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
				<div class="space-y-1.5 md:space-y-2">
					<label class="block text-xs md:text-sm font-medium text-gray-700">Name</label>
					<input name="requester_name" id="editRequesterName" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., malupiton" required>
				</div>
				<div class="space-y-1.5 md:space-y-2">
					<label class="block text-xs md:text-sm font-medium text-gray-700">Date Needed</label>
					<input type="date" name="request_date" id="editRequestDate" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
				</div>
			</div>
			<div class="space-y-1.5 md:space-y-2">
				<label class="block text-xs md:text-sm font-medium text-gray-700">Ingredients / Notes</label>
				<textarea name="ingredients_note" id="editIngredientsNote" rows="4" class="w-full border border-gray-300 rounded-lg px-2.5 md:px-3 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="List ingredients, quantities, or any prep instructions" required></textarea>
				<p class="text-[10px] md:text-xs text-gray-500">Detailed quantities will be captured later during the Prepare step.</p>
			</div>
			<div class="flex justify-end gap-2 md:gap-3">
				<button type="button" id="cancelEditModal" class="inline-flex items-center gap-1 md:gap-1.5 px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					Cancel
				</button>
				<button type="submit" class="inline-flex items-center gap-1 md:gap-1.5 bg-blue-600 text-white px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-xs md:text-sm">
					<i data-lucide="save" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
					Save Changes
				</button>
			</div>
		</form>
	</div>
</div>

<div id="prepareModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important; z-index: 99999 !important;">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
		<div class="px-4 md:px-5 lg:px-6 py-4 border-b">
			<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4">
				<div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 md:gap-6">
					<div>
						<p class="text-[10px] uppercase tracking-wide text-gray-500">Batch</p>
						<p class="text-base font-semibold text-gray-900" id="prepareModalBatchLabel">#0</p>
					</div>
					<div class="flex flex-wrap items-center gap-3 md:gap-4">
						<div>
							<p class="text-[10px] uppercase tracking-wide text-gray-500">Request Name</p>
							<p class="text-xs font-semibold text-gray-900 mt-1" id="prepareModalRequestName">—</p>
						</div>
						<div>
							<p class="text-[10px] uppercase tracking-wide text-gray-500">Date Needed</p>
							<p class="text-xs font-semibold text-gray-900 mt-1" id="prepareModalRequestDate">—</p>
						</div>
						<div>
							<p class="text-[10px] uppercase tracking-wide text-gray-500">Requested By</p>
							<p class="text-xs font-semibold text-gray-900 mt-1" id="prepareModalStaff">—</p>
						</div>
					</div>
				</div>
				<button type="button" class="prepareModalClose text-gray-500 hover:text-gray-700 text-xl md:text-2xl leading-none shrink-0" aria-label="Close">&times;</button>
			</div>
		</div>
		<div class="px-4 md:px-5 lg:px-6 py-4 space-y-4">
			<div class="p-3 md:p-4 border border-gray-200 rounded-lg">
				<p class="text-xs uppercase tracking-wide text-gray-500 mb-2 md:mb-3">Requested Ingredients</p>
				<ul class="grid grid-cols-1 md:grid-cols-2 gap-2" id="prepareModalNotes">
					<li class="text-sm text-gray-700">—</li>
				</ul>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/prepare" id="prepareModalForm" class="space-y-4">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="batch_id" id="prepareModalBatchId">
				<input type="hidden" name="action" id="prepareModalAction" value="save">
				<div class="rounded-xl border border-gray-200 p-3 md:p-4 space-y-3">
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
						<div class="space-y-1 md:col-span-2 lg:col-span-2">
							<label class="text-sm font-medium text-gray-700">Ingredient</label>
							<div class="relative">
								<div class="absolute left-3 top-1/2 transform -translate-y-1/2 z-10">
									<i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
								</div>
								<input 
									type="text" 
									id="prepareIngredientSearch" 
									placeholder="Search ingredients..." 
									class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
									autocomplete="off"
								>
								<select id="prepareIngredientSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500" style="display: none;">
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
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Quantity</label>
							<input type="number" step="0.01" min="0.01" id="prepareQuantityInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-gray-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<select id="prepareUnitSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-gray-500">
								<option value="">Base unit</option>
							</select>
						</div>
						<div class="flex items-end">
					<button type="button" id="prepareAddItemBtn" class="w-full inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-xs md:text-sm">
						<i data-lucide="plus" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						Add
					</button>
						</div>
					</div>
					<div id="prepareBuilderError" class="hidden px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700"></div>
				</div>
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<table class="w-full text-xs md:text-sm">
						<thead class="bg-gray-100">
							<tr>
								<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Ingredient</th>
								<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Quantity</th>
								<th class="text-left px-2.5 md:px-3 py-1.5 md:py-2 font-semibold text-gray-900 text-xs md:text-sm">Actions</th>
							</tr>
						</thead>
						<tbody id="prepareItemsBody" class="divide-y divide-gray-200"></tbody>
					</table>
					<div id="prepareEmptyState" class="px-4 py-6 text-center text-xs md:text-sm text-gray-500">No ingredients added yet.</div>
				</div>
				<div id="prepareDynamicInputs"></div>
				<div class="flex flex-col sm:flex-row sm:justify-end gap-2 md:gap-3">
					<button type="button" data-action="save" class="prepareSubmitBtn inline-flex items-center justify-center gap-1 md:gap-1.5 border border-gray-300 text-gray-700 px-2.5 md:px-3 py-1.5 md:py-2 rounded-lg hover:bg-gray-50 text-xs md:text-sm">
						<i data-lucide="save" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						Save Prep
					</button>
					<button type="button" data-action="distribute" class="prepareSubmitBtn inline-flex items-center justify-center gap-1 md:gap-1.5 bg-green-600 text-white px-2.5 md:px-3 py-1.5 md:py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-xs md:text-sm">
						<i data-lucide="send" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
						Distribute
					</button>
				</div>
			</form>
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
	const requestRows = Array.from(document.querySelectorAll('tr[data-status]'));
	const requestsFilterEmpty = document.getElementById('requestsFilterEmpty');

	function applyRequestFilter(value){
		const normalized = value && value !== 'all' ? value.toLowerCase() : 'all';
		let visible = 0;
		requestRows.forEach(row => {
			const status = (row.getAttribute('data-status') || '').toLowerCase();
			const matches = normalized === 'all' || status === normalized;
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
		applyRequestFilter(initial);
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
			applyRequestFilter(value);
		});
	} else {
		applyRequestFilter('all');
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
			overlay.className = 'batch-detail-overlay fixed inset-0 bg-black/60 backdrop-blur-md z-[99999] flex items-center justify-center p-4';
			overlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important; z-index: 99999 !important;';
			const modal = document.createElement('div');
			modal.className = 'bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto';
			modal.innerHTML = `
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4 px-4 md:px-5 lg:px-6 py-4 border-b border-gray-200">
					<div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 md:gap-6">
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-1">BATCH ID</p>
							<p class="text-base font-bold text-gray-900">#${id}</p>
						</div>
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-1">REQUESTED BY</p>
							<p class="text-xs font-semibold text-gray-900">${requestedBy}</p>
						</div>
						<div>
							<p class="text-[9px] uppercase tracking-wider text-gray-500 font-medium mb-1">DATE REQUESTED</p>
							<p class="text-xs font-semibold text-gray-900">${dateRequested}</p>
						</div>
					</div>
					<button type="button" class="closeBatchModal text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg shrink-0" aria-label="Close">
						<i data-lucide="x" class="w-5 h-5"></i>
					</button>
				</div>
				<div class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6">${cardClone.innerHTML}</div>
				${showActions ? `
				<div class="px-4 md:px-5 lg:px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
					<form method="post" action="${baseUrl}/requests/approve" class="inline">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 md:py-2 bg-green-600 text-white text-xs md:text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="check" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
							Approve
						</button>
					</form>
					<form method="post" action="${baseUrl}/requests/reject" class="inline" data-confirm="Are you sure you want to reject request batch #${id}? The requester will be notified." data-confirm-type="warning">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 md:py-2 bg-red-600 text-white text-xs md:text-sm rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="x" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
							Reject
						</button>
					</form>
				</div>
				` : ''}
				${isRejected ? `
				<div class="px-4 md:px-5 lg:px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-2 md:gap-3">
					<form method="post" action="${baseUrl}/requests/delete" class="inline" id="deleteBatchForm${id}">
						<input type="hidden" name="csrf_token" value="${csrfToken}">
						<input type="hidden" name="batch_id" value="${id}">
						<button type="button" class="deleteBatchBtn inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 md:py-2 bg-red-600 text-white text-xs md:text-sm rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="trash-2" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
							Delete
						</button>
					</form>
				</div>
				` : ''}
			`;
			overlay.appendChild(modal);
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
					confirmOverlay.className = 'delete-confirm-overlay fixed inset-0 z-[999999] flex items-center justify-center p-4';
					confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; z-index: 999999 !important; pointer-events: none !important;';
					
					// Make the modal itself clickable
					const confirmModal = document.createElement('div');
					confirmModal.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full';
					confirmModal.style.pointerEvents = 'auto';
					confirmModal.innerHTML = `
						<div class="px-4 md:px-5 lg:px-6 py-4 md:py-5 lg:py-6">
							<div class="flex items-center gap-4 mb-4">
								<div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
									<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
								</div>
								<div>
									<h3 class="text-sm md:text-base font-semibold text-gray-900">Delete Request Batch</h3>
									<p class="text-[10px] md:text-xs text-gray-500 mt-0.5 md:mt-1">This action cannot be undone</p>
								</div>
							</div>
							<p class="text-xs md:text-sm text-gray-700 mb-4 md:mb-6">
								Are you sure you want to delete request batch <strong>#${id}</strong>? This will permanently remove the batch and all associated data.
							</p>
							<div class="flex items-center justify-end gap-2 md:gap-3">
								<button type="button" class="cancelDeleteBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
									Cancel
								</button>
								<button type="button" class="confirmDeleteBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
									Delete
								</button>
							</div>
						</div>
					`;
					
					confirmOverlay.appendChild(confirmModal);
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
					class="w-full text-left px-4 py-2 hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none transition-colors border-b border-gray-100 last:border-b-0"
					data-id="${opt.id}"
					data-unit="${opt.unit}"
				>
					<div class="flex items-center justify-between">
						<div class="flex-1">
							<div class="font-medium text-gray-900">${opt.name}</div>
							<div class="text-xs text-gray-500">${opt.unit}</div>
						</div>
						<div class="ml-3 text-right">
							<div class="text-xs font-semibold ${stockColor}">Stock</div>
							<div class="text-sm font-medium ${stockColor}">${stockText}</div>
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
				items = parsed.map(item => ({
					id: Number(item.item_id || item.id),
					name: item.item_name || INGREDIENT_LOOKUP[item.item_id || item.id]?.name || 'Ingredient',
					unit: item.unit || INGREDIENT_LOOKUP[item.item_id || item.id]?.unit || '',
					quantity: Number(item.quantity || 0),
				})).filter(item => item.id && item.quantity > 0);
			} catch (err) {
				items = [];
			}
			document.getElementById('prepareModalBatchLabel').textContent = '#' + (button.getAttribute('data-batch') || '0');
			document.getElementById('prepareModalRequestName').textContent = button.getAttribute('data-requester') || '—';
			document.getElementById('prepareModalRequestDate').textContent = button.getAttribute('data-date') || '—';
			document.getElementById('prepareModalStaff').textContent = button.getAttribute('data-staff') || '—';
			
			// Populate ingredients as bullet list in 2-column grid
			const notesContainer = document.getElementById('prepareModalNotes');
			const notes = button.getAttribute('data-notes') || '';
			notesContainer.innerHTML = '';
			if (notes && notes !== '—') {
				const ingredientLines = notes.split('\n').filter(line => line.trim() !== '');
				if (ingredientLines.length > 0) {
					ingredientLines.forEach(line => {
						const li = document.createElement('li');
						li.className = 'text-sm text-gray-700 flex items-start gap-2';
						const bullet = document.createElement('span');
						bullet.className = 'text-gray-500 mt-0.5';
						bullet.textContent = '•';
						const text = document.createElement('span');
						text.textContent = line.trim();
						li.appendChild(bullet);
						li.appendChild(text);
						notesContainer.appendChild(li);
					});
				} else {
					const li = document.createElement('li');
					li.className = 'text-sm text-gray-700';
					li.textContent = '—';
					notesContainer.appendChild(li);
				}
			} else {
				const li = document.createElement('li');
				li.className = 'text-sm text-gray-700';
				li.textContent = '—';
				notesContainer.appendChild(li);
			}
			batchIdInput.value = button.getAttribute('data-batch') || '';
			actionInput.value = 'save';
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
			if (ingredientSearch) ingredientSearch.value = '';
			if (ingredientDropdown) ingredientDropdown.classList.add('hidden');
			errorBox.classList.add('hidden');
		}

		function showBuilderError(message){
			errorBox.textContent = message;
			errorBox.classList.remove('hidden');
		}

		function clearBuilderError(){
			errorBox.textContent = '';
			errorBox.classList.add('hidden');
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
					<td class="px-2.5 md:px-3 py-2 md:py-2.5">
						<div>
							<p class="font-semibold text-gray-900 text-xs md:text-sm">${escapeHtml(item.name || '')}</p>
							<p class="text-[10px] md:text-xs text-gray-500 mt-0.5">${escapeHtml(item.display_unit || item.unit || '')}</p>
						</div>
					</td>
					<td class="px-2.5 md:px-3 py-2 md:py-2.5">
						<span class="font-bold text-gray-900 text-xs md:text-sm">${(() => {
						let displayQty = item.quantity;
						const baseUnit = item.unit;
						const displayUnit = item.display_unit;
						
						// Convert from base unit to display unit
						if (baseUnit === 'g' && displayUnit === 'kg') {
							displayQty = item.quantity / 1000;
						} else if (baseUnit === 'kg' && displayUnit === 'g') {
							displayQty = item.quantity * 1000;
						} else if (baseUnit === 'ml' && displayUnit === 'L') {
							displayQty = item.quantity / 1000;
						} else if (baseUnit === 'L' && displayUnit === 'ml') {
							displayQty = item.quantity * 1000;
						} else if ((displayUnit === 'g' || displayUnit === 'kg') && baseUnit !== 'g' && baseUnit !== 'kg') {
							// Custom base unit (like 'sack') with g/kg display
							// Use display_factor if available, otherwise show base quantity
							const ingredient = INGREDIENT_LOOKUP[item.id];
							if (ingredient && ingredient.display_factor && ingredient.display_factor > 0) {
								if (displayUnit === 'kg') {
									displayQty = item.quantity * ingredient.display_factor;
								} else if (displayUnit === 'g') {
									displayQty = item.quantity * ingredient.display_factor * 1000;
								}
							}
						} else if ((displayUnit === 'ml' || displayUnit === 'L') && baseUnit !== 'ml' && baseUnit !== 'L') {
							// Similar for volume
							const ingredient = INGREDIENT_LOOKUP[item.id];
							if (ingredient && ingredient.display_factor && ingredient.display_factor > 0) {
								if (displayUnit === 'L') {
									displayQty = item.quantity * ingredient.display_factor;
								} else if (displayUnit === 'ml') {
									displayQty = item.quantity * ingredient.display_factor * 1000;
								}
							}
						}
						return Number(displayQty).toFixed(2);
					})()} ${escapeHtml(item.display_unit || item.unit || '')}</span>
					</td>
					<td class="px-2.5 md:px-3 py-2 md:py-2.5">
						<button type="button" class="text-red-600 hover:text-red-700 text-[10px] md:text-xs font-medium removePrepItem" data-index="${index}">
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
			
			// Always show base unit first
			unitSelect.appendChild(opt(baseUnit || 'pcs', baseUnit || 'pcs'));
			
			// Add weight conversions (g/kg) for any ingredient
			if (baseUnit !== 'g' && baseUnit !== 'kg') {
				unitSelect.appendChild(opt('g','g'));
				unitSelect.appendChild(opt('kg','kg'));
			} else if (baseUnit === 'g'){
				unitSelect.appendChild(opt('kg','kg'));
			} else if (baseUnit === 'kg'){
				unitSelect.appendChild(opt('g','g'));
			}
			
			// Add volume conversions (ml/L) for any ingredient
			if (baseUnit !== 'ml' && baseUnit !== 'L') {
				unitSelect.appendChild(opt('ml','ml'));
				unitSelect.appendChild(opt('L','L'));
			} else if (baseUnit === 'ml'){
				unitSelect.appendChild(opt('L','L'));
			} else if (baseUnit === 'L'){
				unitSelect.appendChild(opt('ml','ml'));
			}
			
			// Set default to base unit
			unitSelect.value = baseUnit || '';
		}

		ingredientSelect.addEventListener('change', ()=>{
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
				confirmOverlay.className = 'low-stock-confirm-overlay fixed inset-0 z-[999999] flex items-center justify-center p-4';
				confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; z-index: 999999 !important; background-color: rgba(0, 0, 0, 0.6) !important; pointer-events: none !important;';
				
				const confirmModal = document.createElement('div');
				confirmModal.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full';
				confirmModal.style.pointerEvents = 'auto';
				
				const stockDisplay = currentStock.toFixed(2) + ' ' + (ingredient.display_unit || ingredient.unit || '');
				
				confirmModal.innerHTML = `
					<div class="px-6 py-6">
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
					</div>
				`;
				
				confirmOverlay.appendChild(confirmModal);
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
			confirmOverlay.className = 'remove-ingredient-confirm-overlay fixed inset-0 z-[999999] flex items-center justify-center p-4';
			confirmOverlay.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; z-index: 999999 !important; background-color: rgba(0, 0, 0, 0.5) !important; pointer-events: none !important;';
			
			// Make the modal itself clickable
			const confirmModal = document.createElement('div');
			confirmModal.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full';
			confirmModal.style.pointerEvents = 'auto';
			confirmModal.innerHTML = `
				<div class="px-6 py-6">
					<div class="flex items-center gap-4 mb-4">
						<div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
							<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
						</div>
						<div>
							<h3 class="text-sm md:text-base font-semibold text-gray-900">Remove Ingredient</h3>
							<p class="text-[10px] md:text-xs text-gray-500 mt-0.5 md:mt-1">This action cannot be undone</p>
						</div>
					</div>
					<p class="text-xs md:text-sm text-gray-700 mb-4 md:mb-6">
						Are you sure you want to remove <strong>${ingredientName}</strong> (${quantity} ${unit}) from this request?
					</p>
					<div class="flex items-center justify-end gap-2 md:gap-3">
						<button type="button" class="cancelRemoveIngredientBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
							Cancel
						</button>
						<button type="button" class="confirmRemoveIngredientBtn inline-flex items-center justify-center px-2.5 md:px-3 lg:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							Remove
						</button>
					</div>
				</div>
			`;
			
			confirmOverlay.appendChild(confirmModal);
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
			btn.addEventListener('click', ()=>{
				const action = btn.getAttribute('data-action') || 'save';
				if (!items.length){
					showBuilderError('Add at least one ingredient before submitting.');
					return;
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

</script>


