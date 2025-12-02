<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : ''; 
$statusFilter = strtolower((string)($_GET['status'] ?? 'all'));
$ingredientSets = $ingredientSets ?? [];
$availableSets = array_values(array_filter($ingredientSets, static fn($set) => !empty($set['is_available'])));
$availableSetsCount = count($availableSets);
$ingredientStockMap = $ingredientStock ?? [];
?>
<!-- Page Header -->
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ingredient Requests</h1>
			<p class="text-sm sm:text-base text-gray-600 mt-1 font-medium">Manage ingredient requests and batch approvals</p>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2.5 px-5 py-3 text-xs font-bold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-all duration-200 shadow-sm hover:shadow-md">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
	</div>
</div>

<?php if (!empty($flash)): ?>
<!-- Flash Message Modal -->
<div id="flashModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
	<!-- Overlay/Scrim - covers entire viewport including header and sidebar -->
	<div id="flashModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto" style="z-index: 10000; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"></div>
	<!-- Modal Content -->
	<div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 z-[10001] transform transition-all duration-300 scale-95" style="z-index: 10001;">
		<button type="button" id="closeFlashModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
			<i data-lucide="x" class="w-5 h-5"></i>
		</button>
		<div class="flex items-start gap-4">
			<div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center <?php echo ($flash['type'] ?? '') === 'error' ? 'bg-red-100' : 'bg-green-100'; ?>">
				<i data-lucide="<?php echo ($flash['type'] ?? '') === 'error' ? 'alert-circle' : 'check-circle'; ?>" class="w-6 h-6 <?php echo ($flash['type'] ?? '') === 'error' ? 'text-red-600' : 'text-green-600'; ?>"></i>
			</div>
			<div class="flex-1">
				<h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo ($flash['type'] ?? '') === 'error' ? 'Error' : 'Success'; ?></h3>
				<div class="text-sm text-gray-700 space-y-1">
					<?php if (!empty($flash['messages']) && is_array($flash['messages'])): ?>
						<?php foreach ($flash['messages'] as $msg): ?>
							<p><?php echo htmlspecialchars($msg); ?></p>
						<?php endforeach; ?>
					<?php else: ?>
						<p><?php echo htmlspecialchars($flash['messages'][0] ?? ''); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="mt-6 flex justify-end">
			<button type="button" id="flashModalOk" class="px-6 py-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white rounded-lg hover:opacity-90 transition-all font-semibold">
				OK
			</button>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const flashModal = document.getElementById('flashModal');
	const flashModalOverlay = document.getElementById('flashModalOverlay');
	const closeFlashModal = document.getElementById('closeFlashModal');
	const flashModalOk = document.getElementById('flashModalOk');
	const modalContent = flashModal?.querySelector('.relative');
	
	if (flashModal) {
		// Move modal to body level to ensure it's above everything
		document.body.appendChild(flashModal);
		
		// Show modal
		flashModal.classList.remove('hidden');
		flashModal.classList.add('flex');
		requestAnimationFrame(() => {
			flashModal.classList.remove('opacity-0');
			flashModal.classList.add('pointer-events-auto');
			if (modalContent) {
				modalContent.classList.remove('scale-95');
				modalContent.classList.add('scale-100');
			}
		});
		
		// Initialize icons
		if (typeof lucide !== 'undefined') {
			lucide.createIcons();
		}
		
		// Close handlers
		function closeModal() {
			if (modalContent) {
				modalContent.classList.remove('scale-100');
				modalContent.classList.add('scale-95');
			}
			flashModal.classList.add('opacity-0');
			setTimeout(() => {
				flashModal.classList.add('hidden', 'pointer-events-none');
				flashModal.classList.remove('flex', 'pointer-events-auto');
			}, 300);
		}
		
		closeFlashModal?.addEventListener('click', closeModal);
		flashModalOk?.addEventListener('click', closeModal);
		flashModalOverlay?.addEventListener('click', closeModal);
		
		// Auto-close after 5 seconds
		setTimeout(closeModal, 5000);
	}
});
</script>
<?php endif; ?>
<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner'], true)): ?>
<!-- New Request Button -->
<div class="mb-8 flex justify-end">
	<button type="button" id="openNewRequestModal" class="inline-flex items-center gap-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white px-6 py-3 rounded-xl hover:opacity-90 focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
		<div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
			<i data-lucide="plus" class="w-4 h-4"></i>
		</div>
		<span class="font-semibold">New Batch Request</span>
	</button>
</div>

<!-- New Request Modal -->
<div id="newRequestModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
	<!-- Overlay/Scrim - covers entire viewport including header and sidebar -->
	<div id="newRequestModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto" style="z-index: 10000; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"></div>
	<!-- Modal Content - above overlay -->
	<div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300 pointer-events-auto" style="z-index: 10001;">
		<!-- Modal Header -->
		<div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-6 py-5 border-b border-gray-200">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
						<i data-lucide="plus-circle" class="w-6 h-6 text-white"></i>
					</div>
					<div>
						<h2 class="text-xl font-bold text-gray-900">New Batch Request</h2>
						<p class="text-sm text-gray-600 mt-0.5">Describe what you need and when it's required.</p>
					</div>
				</div>
				<button type="button" id="closeNewRequestModal" class="text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-lg p-2 transition-colors duration-200" aria-label="Close">
					<i data-lucide="x" class="w-5 h-5"></i>
				</button>
			</div>
		</div>
		
		<!-- Modal Body -->
		<div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" id="newRequestForm" class="space-y-6">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="space-y-2">
						<label class="block text-sm font-semibold text-gray-700">
							Request Name / Event
							<span class="text-red-500">*</span>
						</label>
						<input 
							name="requester_name" 
							type="text"
							class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder:text-gray-400" 
							placeholder="e.g., Brunch Buffet Prep" 
							required>
					</div>
					<div class="space-y-2">
						<label class="block text-sm font-semibold text-gray-700">
							Date Needed
							<span class="text-red-500">*</span>
						</label>
						<div class="relative">
							<input 
								type="date" 
								name="request_date" 
								class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
								required>
						</div>
					</div>
				</div>
				
				<div class="space-y-2">
					<label class="block text-sm font-semibold text-gray-700">
						Ingredients / Notes
						<span class="text-red-500">*</span>
					</label>
					<textarea 
						name="ingredients_note" 
						rows="5" 
						class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none placeholder:text-gray-400" 
						placeholder="List ingredients, quantities, or any prep instructions" 
						required></textarea>
					<p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
						<i data-lucide="info" class="w-3 h-3"></i>
						Detailed quantities will be captured later during the Prepare step.
					</p>
				</div>
			</form>
		</div>
		
		<!-- Modal Footer -->
		<div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">
			<button 
				type="button" 
				id="cancelNewRequest" 
				class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
				Cancel
			</button>
			<button 
				type="submit" 
				form="newRequestForm"
				class="inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white px-6 py-2.5 text-sm font-semibold rounded-xl hover:opacity-90 focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
				<i data-lucide="send" class="w-4 h-4"></i>
				Submit Request
			</button>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (Auth::role() !== 'Kitchen Staff'): ?>
<!-- Batch Requests Table -->
<div id="requests-history" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
                    Batch Requests History
                </h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all ingredient requests</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm text-gray-600">
                <label for="requestStatusFilter" class="whitespace-nowrap">Filter status:</label>
                <select id="requestStatusFilter" data-default="<?php echo htmlspecialchars($statusFilter); ?>" class="w-full sm:w-auto border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
	
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[640px]">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Requested Name</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Request Notes</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Requested</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Needed</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<tr class="hover:bg-gray-50 transition-colors" data-status="<?php echo strtolower($b['status'] ?? ''); ?>" data-detail-id="batch-<?php echo (int)$b['id']; ?>">
					<td class="px-6 py-4">
						<div>
							<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></p>
							<p class="text-xs text-gray-500">Created by <?php echo htmlspecialchars($b['staff_name'] ?? ''); ?></p>
						</div>
					</td>
					<td class="px-6 py-4">
                        <button type="button" class="text-blue-600 hover:text-blue-700 text-xs underline viewBatchDetails" data-batch="<?php echo (int)$b['id']; ?>">View details</button>
						<?php if (($b['status'] ?? '') === 'Distributed' && !empty($items)): ?>
							<div class="mt-2 space-y-1 text-xs text-gray-500">
								<?php foreach ($items as $it): $iid = (int)($it['item_id'] ?? 0); $remain = $ingredientStockMap[$iid] ?? null; ?>
									<div class="flex items-center gap-2">
										<i data-lucide="battery-charging" class="w-3 h-3 text-green-500"></i>
										<span>Remaining <?php echo htmlspecialchars($it['item_name']); ?>:
											<span class="font-semibold text-gray-900"><?php echo $remain !== null ? number_format((float)$remain, 2) . ' ' . htmlspecialchars($it['unit']) : '—'; ?></span>
										</span>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</td>
					<td class="px-6 py-4 text-gray-600">
						<?php 
						if (!empty($b['date_requested'])) {
							$timestamp = strtotime($b['date_requested']);
							echo htmlspecialchars($timestamp ? date('m/d/Y', $timestamp) : substr($b['date_requested'], 0, 10));
						} else {
							echo '—';
						}
						?>
					</td>
					<td class="px-6 py-4 text-gray-600">
						<?php 
						$dateNeeded = !empty($b['custom_request_date']) ? $b['custom_request_date'] : (!empty($b['date_requested']) ? substr($b['date_requested'], 0, 10) : '—');
						if ($dateNeeded !== '—') {
							$timestamp = strtotime($dateNeeded);
							echo htmlspecialchars($timestamp ? date('m/d/Y', $timestamp) : $dateNeeded);
						} else {
							echo '—';
						}
						?>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden bg-gray-50" data-detail-for="<?php echo (int)$b['id']; ?>">
					<td colspan="4" class="px-6 py-4">
						<div class="batch-detail-card space-y-5">
							<?php 
							$statusClass = match($b['status']) {
								'Distributed' => 'bg-green-100 text-green-800 border-green-200',
								'Rejected' => 'bg-red-100 text-red-800 border-red-200',
								'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
								'Pending' => 'bg-blue-100 text-blue-800 border-blue-200',
								default => 'bg-gray-100 text-gray-700 border-gray-200'
							};
							$dateRequested = !empty($b['date_requested']) ? (strtotime($b['date_requested']) ? date('m/d/Y', strtotime($b['date_requested'])) : substr($b['date_requested'], 0, 10)) : '—';
							$dateNeeded = !empty($b['custom_request_date']) ? (strtotime($b['custom_request_date']) ? date('m/d/Y', strtotime($b['custom_request_date'])) : substr($b['custom_request_date'], 0, 10)) : '—';
							?>
							<!-- Header Section -->
							<div class="space-y-3">
								<?php if (!empty($b['custom_requester'])): ?>
									<h3 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($b['custom_requester']); ?></h3>
								<?php endif; ?>
								<div class="flex items-center gap-3 flex-wrap">
									<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border <?php echo $statusClass; ?>">
										<?php echo htmlspecialchars($b['status']); ?>
									</span>
									<div class="flex items-center gap-4 text-sm text-gray-600">
										<span class="flex items-center gap-1.5">
											<i data-lucide="user" class="w-4 h-4"></i>
											<?php echo htmlspecialchars($b['staff_name'] ?? ''); ?>
										</span>
										<span class="flex items-center gap-1.5">
											<i data-lucide="calendar" class="w-4 h-4"></i>
											<?php echo $dateRequested; ?>
										</span>
										<?php if ($dateNeeded !== '—'): ?>
										<span class="flex items-center gap-1.5 text-[#008000] font-medium">
											<i data-lucide="clock" class="w-4 h-4"></i>
											Needed: <?php echo $dateNeeded; ?>
										</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
							
							<!-- Notes Section -->
							<?php if (!empty($b['custom_ingredients'])): ?>
								<div class="rounded-xl p-4 border border-gray-300 mt-6">
									<p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"><?php echo htmlspecialchars($b['custom_ingredients']); ?></p>
								</div>
							<?php endif; ?>
							
							<!-- Items Section -->
							<?php if (!empty($items)): ?>
								<div class="space-y-3">
									<h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
										<i data-lucide="package" class="w-4 h-4"></i>
										Items (<?php echo count($items); ?>)
									</h4>
									<div class="space-y-2">
										<?php foreach ($items as $it): ?>
										<div class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200 hover:border-gray-300 hover:shadow-sm transition-all">
											<div class="flex items-center gap-3">
												<div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center border border-blue-100">
													<i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
												</div>
												<div>
													<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></p>
													<?php if (!empty($it['set_name'])): ?>
														<p class="text-xs text-indigo-600 font-medium"><?php echo htmlspecialchars($it['set_name']); ?> set</p>
													<?php endif; ?>
												</div>
											</div>
											<div class="text-right">
												<p class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($it['quantity']); ?></p>
												<p class="text-xs text-gray-500"><?php echo htmlspecialchars($it['unit']); ?></p>
											</div>
										</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
							
							<!-- Actions Section -->
							<?php if (in_array(Auth::role(), ['Owner','Manager'], true) && $b['status'] === 'Pending'): ?>
							<div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/reject" class="inline">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-red-600 text-sm font-semibold rounded-lg border-2 border-red-200 hover:bg-red-50 hover:border-red-300 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
										<i data-lucide="x" class="w-4 h-4"></i>
										Reject
									</button>
								</form>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/approve" class="inline">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow">
										<i data-lucide="check" class="w-4 h-4"></i>
										Approve
									</button>
								</form>
							</div>
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
        <div id="requestsFilterEmpty" class="hidden px-6 py-4 text-center text-sm text-gray-500 border-t">
            No request batches match the selected filter.
        </div>
	</div>
</div>
<?php endif; ?>

<?php if (Auth::role() !== 'Kitchen Staff'): ?>
<!-- To Prepare -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-amber-50 to-orange-50 px-4 sm:px-6 py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="chef-hat" class="w-5 h-5 text-orange-600"></i>
				To Prepare
			</h2>
			<p class="text-sm text-gray-600 mt-1">Approved batches waiting to be prepared and distributed.</p>
		</div>
		<span class="text-sm text-gray-500"><?php echo count($toPrepareBatches ?? []); ?> batch<?php echo (count($toPrepareBatches ?? []) === 1) ? '' : 'es'; ?></span>
	</div>
	<div class="p-4 sm:p-6 space-y-4 max-h-[600px] overflow-y-auto scroll-smooth">
		<?php if (!empty($toPrepareBatches)): ?>
			<?php foreach ($toPrepareBatches as $batch):
				$prepItems = $batchItems[(int)$batch['id']] ?? [];
				$itemCount = (int)($batch['items_count'] ?? count($prepItems));
			?>
			<div class="border rounded-2xl p-4 sm:p-6 space-y-4">
				<div class="flex flex-col gap-1">
					<p class="text-xs uppercase tracking-wide text-gray-500">Batch</p>
					<div class="flex items-center gap-3 flex-wrap">
						<span class="text-lg font-semibold text-gray-900">#<?php echo (int)$batch['id']; ?></span>
						<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 border border-orange-200">To Prepare</span>
						<span class="text-xs text-gray-500"><?php echo $itemCount; ?> item<?php echo $itemCount === 1 ? '' : 's'; ?></span>
					</div>
					<p class="text-sm text-gray-600">Requested by <?php echo htmlspecialchars($batch['staff_name'] ?? ''); ?> on <?php echo htmlspecialchars($batch['date_requested'] ?? ''); ?></p>
					<?php if (!empty($batch['custom_requester'])): ?>
						<p class="text-sm text-gray-600">Name: <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($batch['custom_requester']); ?></span></p>
					<?php endif; ?>
				</div>
				<ul class="divide-y divide-gray-100 rounded-xl border border-gray-100">
					<?php foreach ($prepItems as $it): ?>
					<li class="px-4 py-2 flex items-center justify-between text-sm">
						<div class="flex items-center gap-3">
							<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
							<div>
								<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></p>
								<?php if (!empty($it['set_name'])): ?>
									<p class="text-xs text-indigo-600 font-semibold">Part of <?php echo htmlspecialchars($it['set_name']); ?> set</p>
								<?php endif; ?>
							</div>
						</div>
						<span class="font-semibold text-gray-900"><?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<div class="flex flex-wrap items-center gap-3">
					<?php $metaItems = htmlspecialchars(json_encode($prepItems, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
					<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
						<button type="button"
							class="prepareBatchBtn inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50"
							data-batch="<?php echo (int)$batch['id']; ?>"
							data-items="<?php echo $metaItems; ?>"
							data-requester="<?php echo htmlspecialchars($batch['custom_requester'] ?: ($batch['staff_name'] ?? '')); ?>"
							data-notes="<?php echo htmlspecialchars($batch['custom_ingredients'] ?? ''); ?>"
							data-date="<?php echo htmlspecialchars($batch['custom_request_date'] ?: substr((string)($batch['date_requested'] ?? ''), 0, 10)); ?>"
							data-staff="<?php echo htmlspecialchars($batch['staff_name'] ?? ''); ?>"
							data-staff-id="<?php echo (int)($batch['staff_id'] ?? 0); ?>">
							<i data-lucide="chef-hat" class="w-4 h-4"></i>
							Prepare
						</button>
					<?php else: ?>
						<span class="text-xs text-gray-500">Awaiting prep</span>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="clock" class="w-5 h-5 text-gray-600"></i>
			Request History
		</h2>
		<p class="text-sm text-gray-600 mt-1">Track the status of your submitted requests</p>
	</div>
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[540px]">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Batch ID</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Request Name</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Notes</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Requested</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Status</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-blue-600">#<?php echo (int)$b['id']; ?></span>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></td>
					<td class="px-6 py-4 text-sm text-gray-700 whitespace-pre-line max-w-sm"><?php echo htmlspecialchars($b['custom_ingredients'] ?? '—'); ?></td>
					<td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($b['date_requested']); ?></td>
					<td class="px-6 py-4">
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200">
							<i data-lucide="clock" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($b['status']); ?>
						</span>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($batches)): ?>
				<tr>
					<td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No requests submitted yet.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

<div id="requestSetToast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden">
	<div id="requestSetToastInner" class="rounded-xl px-4 py-3 shadow-lg text-sm font-medium"></div>
</div>

<div id="prepareModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
	<!-- Overlay/Scrim - covers entire viewport including header and sidebar -->
	<div id="prepareModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto" style="z-index: 10000; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"></div>
	<!-- Modal Content - above overlay -->
	<div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 pointer-events-auto" style="z-index: 10001;">
		<div class="flex items-center justify-end px-6 py-4 border-b">
			<button type="button" class="prepareModalClose text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Close">&times;</button>
		</div>
		<div class="px-6 py-4 space-y-4">
			<!-- Request Info - Inline -->
			<div class="flex flex-wrap items-center gap-4 pb-4 border-b border-gray-200">
				<div class="flex items-center gap-2">
					<span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Request Name:</span>
					<span class="text-sm font-semibold text-gray-900" id="prepareModalRequestName">—</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date Needed:</span>
					<span class="text-sm font-semibold text-gray-900" id="prepareModalRequestDate">—</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Requested By:</span>
					<span class="text-sm font-semibold text-gray-900" id="prepareModalStaff">—</span>
				</div>
			</div>
			<!-- Requested Items - Bullet Form -->
			<div class="space-y-2">
				<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Requested Items</p>
				<ul class="text-sm text-gray-700 space-y-1 list-disc list-inside" id="prepareModalNotes">—</ul>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/prepare" id="prepareModalForm" class="space-y-4">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<input type="hidden" name="batch_id" id="prepareModalBatchId">
				<input type="hidden" name="action" id="prepareModalAction" value="save">
				<div class="rounded-xl border border-gray-200 p-4 space-y-3">
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Ingredient</label>
							<select id="prepareIngredientSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
								<option value="">Choose ingredient</option>
								<?php foreach ($ingredients as $ing): ?>
									<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Quantity</label>
							<input type="number" step="0.01" min="0.01" id="prepareQuantityInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<select id="prepareUnitSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
								<option value="">Base unit</option>
							</select>
						</div>
						<div class="space-y-1">
							<label class="text-sm font-medium text-gray-700">Unit</label>
							<select id="prepareUnitSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
								<option value="">Base unit</option>
							</select>
						</div>
						<div class="flex items-end">
							<button type="button" id="prepareAddItemBtn" class="w-full inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
								<i data-lucide="plus" class="w-3.5 h-3.5"></i>
								Add
							</button>
						</div>
					</div>
					<div id="prepareBuilderError" class="hidden px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700"></div>
				</div>
				<div class="rounded-xl border border-gray-200 overflow-hidden">
					<table class="w-full text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th class="text-left px-4 py-2">Ingredient</th>
								<th class="text-left px-4 py-2">Quantity</th>
								<th class="text-left px-4 py-2 w-20">Actions</th>
							</tr>
						</thead>
						<tbody id="prepareItemsBody" class="divide-y divide-gray-200"></tbody>
					</table>
					<div id="prepareEmptyState" class="px-4 py-6 text-center text-sm text-gray-500">No ingredients added yet.</div>
				</div>
				<div id="prepareDynamicInputs"></div>
				<div class="flex flex-col sm:flex-row sm:justify-end gap-3">
					<button type="button" data-action="save" class="prepareSubmitBtn inline-flex items-center justify-center gap-1.5 border border-gray-300 text-gray-700 px-3 py-1.5 text-sm rounded-lg hover:bg-gray-50">
						<i data-lucide="save" class="w-3.5 h-3.5"></i>
						Save Prep
					</button>
					<button type="button" data-action="distribute" class="prepareSubmitBtn inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
						<i data-lucide="send" class="w-3.5 h-3.5"></i>
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
			const template = document.getElementById('batch-' + id);
			if (!template) return;
			const card = template.querySelector('.batch-detail-card');
			if (!card) return;
			const overlay = document.createElement('div');
			overlay.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center p-4';
			const modal = document.createElement('div');
			modal.className = 'bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col';
			modal.innerHTML = `
				<div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
					<h2 class="text-xl font-bold text-gray-900">Batch #${id}</h2>
					<button type="button" class="closeBatchModal text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors" aria-label="Close">
						<i data-lucide="x" class="w-5 h-5"></i>
					</button>
				</div>
				<div class="px-6 py-5 overflow-y-auto flex-1">${card.innerHTML}</div>
			`;
			overlay.appendChild(modal);
			document.body.appendChild(overlay);
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
			const closeModal = ()=> overlay.remove();
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

		function openModal(button){
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
			document.getElementById('prepareModalRequestName').textContent = button.getAttribute('data-requester') || '—';
			document.getElementById('prepareModalRequestDate').textContent = button.getAttribute('data-date') || '—';
			document.getElementById('prepareModalStaff').textContent = button.getAttribute('data-staff') || '—';
			
			// Format notes as bullet points
			const notes = button.getAttribute('data-notes') || '—';
			const notesElement = document.getElementById('prepareModalNotes');
			if (notes && notes !== '—') {
				const notesLines = notes.split('\n').filter(line => line.trim());
				notesElement.innerHTML = notesLines.map(line => {
					const escaped = line.trim().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
					return `<li>${escaped}</li>`;
				}).join('');
			} else {
				notesElement.innerHTML = '<li>—</li>';
			}
			batchIdInput.value = button.getAttribute('data-batch') || '';
			actionInput.value = 'save';
			renderItems();
			
			// Move modal to body level to ensure it's above everything
			if (modal.parentElement !== document.body) {
				document.body.appendChild(modal);
			}
			modal.classList.remove('hidden');
			modal.classList.add('flex');
			document.body.classList.add('overflow-hidden');
			// Trigger animations
			setTimeout(() => {
				modal.classList.remove('opacity-0');
				modal.classList.remove('pointer-events-none');
				modal.classList.add('pointer-events-auto');
				const modalContent = modal.querySelector('.relative');
				if (modalContent) {
					modalContent.classList.remove('scale-95');
					modalContent.classList.add('scale-100');
				}
				// Initialize Lucide icons in modal
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
			}, 10);
			configurePrepareUnits('');
		}

		function closeModal(){
			const modalContent = modal.querySelector('.relative');
			if (modalContent) {
				modalContent.classList.remove('scale-100');
				modalContent.classList.add('scale-95');
			}
			modal.classList.add('opacity-0');
			setTimeout(() => {
				modal.classList.add('hidden', 'pointer-events-none');
				modal.classList.remove('flex', 'pointer-events-auto');
				document.body.classList.remove('overflow-hidden');
				items = [];
				renderItems();
				quantityInput.value = '';
				ingredientSelect.value = '';
				errorBox.classList.add('hidden');
			}, 300);
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
					<td class="px-4 py-2">
						<div class="flex items-center gap-2">
							<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
							<div>
								<p class="font-medium text-gray-900">${item.name}</p>
								<p class="text-xs text-gray-500">${item.unit}</p>
							</div>
						</div>
					</td>
					<td class="px-4 py-2 font-semibold text-gray-900">${Number(item.quantity / (item.display_unit === 'kg' && item.unit === 'g' ? 1000 : item.display_unit === 'L' && item.unit === 'ml' ? 1000 : 1)).toFixed(2)} ${item.display_unit || item.unit}</td>
					<td class="px-4 py-2">
						<button type="button" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 removePrepItem" data-index="${index}">
							<i data-lucide="trash-2" class="w-3 h-3"></i>Remove
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

		function configurePrepareUnits(baseUnit){
			if (!unitSelect) return;
			unitSelect.innerHTML = '';
			const opt = (value, label)=>{ const o=document.createElement('option'); o.value=value; o.textContent=label; return o; };
			if (baseUnit === 'g'){
				unitSelect.appendChild(opt('g','g'));
				unitSelect.appendChild(opt('kg','kg'));
			} else if (baseUnit === 'ml'){
				unitSelect.appendChild(opt('ml','ml'));
				unitSelect.appendChild(opt('L','L'));
			} else {
				unitSelect.appendChild(opt(baseUnit || 'pcs', baseUnit || 'pcs'));
			}
			unitSelect.value = baseUnit || '';
		}

		ingredientSelect.addEventListener('change', ()=>{
			const ing = INGREDIENT_LOOKUP[parseInt(ingredientSelect.value || '0', 10)];
			configurePrepareUnits(ing?.unit || '');
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
			if (ingredient.unit === 'g' && chosenUnit === 'kg') { baseQuantity = quantity * 1000; }
			if (ingredient.unit === 'ml' && chosenUnit === 'L') { baseQuantity = quantity * 1000; }
			const existing = items.find(entry => entry.id === id);
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
			ingredientSelect.value = '';
			quantityInput.value = '';
			configurePrepareUnits('');
			clearBuilderError();
			renderItems();
		});

		itemsBody.addEventListener('click', (event)=>{
			const btn = event.target.closest('.removePrepItem');
			if (!btn) return;
			const index = parseInt(btn.getAttribute('data-index') || '-1', 10);
			if (index >= 0){
				items.splice(index, 1);
				renderItems();
			}
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

		const prepareModalOverlay = document.getElementById('prepareModalOverlay');
		closeBtn.addEventListener('click', closeModal);
		if (prepareModalOverlay) {
			prepareModalOverlay.addEventListener('click', closeModal);
		}
	}

	initPrepareModal();

	// New Request Modal Handler
	(function(){
		const modal = document.getElementById('newRequestModal');
		const openBtn = document.getElementById('openNewRequestModal');
		const closeBtn = document.getElementById('closeNewRequestModal');
		const cancelBtn = document.getElementById('cancelNewRequest');
		const modalContent = modal?.querySelector('.bg-white');
		const modalOverlay = document.getElementById('newRequestModalOverlay');

		if (!modal || !openBtn) return;

		function openModal(){
			// Move modal to body level to ensure it's above everything
			if (modal.parentElement !== document.body) {
				document.body.appendChild(modal);
			}
			modal.classList.remove('hidden');
			modal.classList.add('flex');
			document.body.classList.add('overflow-hidden');
			// Trigger animations
			setTimeout(() => {
				modal.classList.remove('opacity-0');
				if (modalContent) {
					modalContent.classList.remove('scale-95');
					modalContent.classList.add('scale-100');
				}
				// Initialize Lucide icons in modal
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
			}, 10);
		}

		function closeModal(){
			modal.classList.add('opacity-0');
			if (modalContent) {
				modalContent.classList.remove('scale-100');
				modalContent.classList.add('scale-95');
			}
			setTimeout(() => {
				modal.classList.add('hidden');
				modal.classList.remove('flex');
				document.body.classList.remove('overflow-hidden');
			}, 300);
		}

		openBtn.addEventListener('click', openModal);
		closeBtn?.addEventListener('click', closeModal);
		cancelBtn?.addEventListener('click', closeModal);

		// Close on backdrop/overlay click
		if (modalOverlay) {
			modalOverlay.addEventListener('click', closeModal);
		}
		// Also allow closing by clicking the modal container (but not the content)
		modal.addEventListener('click', (event) => {
			if (event.target === modal || event.target === modalOverlay) {
				closeModal();
			}
		});

		// Close on Escape key
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
				closeModal();
			}
		});
	})();
})();
</script>


