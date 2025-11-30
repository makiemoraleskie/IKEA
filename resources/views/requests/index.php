<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : ''; 
$statusFilter = strtolower((string)($_GET['status'] ?? 'all'));
$ingredientSets = $ingredientSets ?? [];
$availableSets = array_values(array_filter($ingredientSets, static fn($set) => !empty($set['is_available'])));
$availableSetsCount = count($availableSets);
$ingredientStockMap = $ingredientStock ?? [];
?>
<!-- Page Header -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">Ingredient Requests</h1>
		<p class="text-gray-600 mt-1">Manage ingredient requests and batch approvals</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>

<?php if (!empty($flash)): ?>
<div class="mb-6 px-4 py-3 rounded-lg border <?php echo ($flash['type'] ?? '') === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800'; ?>">
    <div class="flex items-start gap-2">
        <i data-lucide="<?php echo ($flash['type'] ?? '') === 'error' ? 'alert-circle' : 'check-circle'; ?>" class="w-4 h-4 mt-0.5"></i>
        <div class="text-sm font-medium space-y-1">
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
<?php endif; ?>
<?php if (in_array(Auth::role(), ['Kitchen Staff','Manager','Owner'], true)): ?>
<!-- New Request Form -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
			New Batch Request
		</h2>
		<p class="text-sm text-gray-600 mt-1">Describe what you need and when it’s required.</p>
	</div>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" class="p-4 sm:p-6 space-y-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Name</label>
				<input name="requester_name" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., malupiton" required>
			</div>
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">Date Needed</label>
				<input type="date" name="request_date" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
			</div>
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700">Ingredients / Notes</label>
			<textarea name="ingredients_note" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="List ingredients, quantities, or any prep instructions" required></textarea>
			<p class="text-xs text-gray-500">Detailed quantities will be captured later during the Prepare step.</p>
		</div>
		<div class="flex justify-end">
			<button class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
				<i data-lucide="send" class="w-4 h-4"></i>
				Submit Request
			</button>
		</div>
	</form>
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
	<div class="p-4 sm:p-6 space-y-4">
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
					<?php if (!empty($batch['custom_ingredients'])): ?>
						<p class="text-xs text-gray-500">Notes: <?php echo nl2br(htmlspecialchars($batch['custom_ingredients'])); ?></p>
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
					<th class="text-left px-6 py-3 font-medium text-gray-700">Batch ID</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Requested Name</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Request Notes</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Requested</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Status / Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($batches as $b): $items = $batchItems[(int)$b['id']] ?? []; ?>
				<tr class="hover:bg-gray-50 transition-colors" data-status="<?php echo strtolower($b['status'] ?? ''); ?>" data-detail-id="batch-<?php echo (int)$b['id']; ?>">
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
								<span class="text-xs font-semibold text-blue-600">#<?php echo (int)$b['id']; ?></span>
							</div>
						</div>
					</td>
					<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
								<span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($b['custom_requester'] ?: ($b['staff_name'] ?? 'U'), 0, 2)); ?></span>
							</div>
							<div>
								<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester'] ?: ($b['staff_name'] ?? '')); ?></p>
								<p class="text-xs text-gray-500">Created by <?php echo htmlspecialchars($b['staff_name'] ?? ''); ?></p>
							</div>
						</div>
					</td>
					<td class="px-6 py-4">
						<p class="text-sm text-gray-700 whitespace-pre-line max-w-sm"><?php echo htmlspecialchars($b['custom_ingredients'] ?? '—'); ?></p>
                        <button type="button" class="mt-1 text-blue-600 hover:text-blue-700 text-xs underline viewBatchDetails" data-batch="<?php echo (int)$b['id']; ?>">View details</button>
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
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<?php echo htmlspecialchars($b['date_requested']); ?>
						</div>
					</td>
					<td class="px-6 py-4">
						<?php if (in_array(Auth::role(), ['Owner','Manager'], true) && $b['status'] === 'Pending'): ?>
							<div class="flex gap-2">
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/approve">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="inline-flex items-center gap-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
										<i data-lucide="check" class="w-3 h-3"></i>
										Approve
									</button>
								</form>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/reject">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
									<button class="inline-flex items-center gap-1 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
										<i data-lucide="x" class="w-3 h-3"></i>
										Reject
									</button>
								</form>
							</div>
						<?php else: ?>
							<?php 
							$statusClass = match($b['status']) {
								'Distributed' => 'bg-green-100 text-green-800 border-green-200',
								'Rejected' => 'bg-red-100 text-red-800 border-red-200',
								'To Prepare' => 'bg-amber-100 text-amber-800 border-amber-200',
								default => 'bg-gray-100 text-gray-700 border-gray-200'
							};
							?>
							<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
								<i data-lucide="clock" class="w-3 h-3"></i>
								<?php echo htmlspecialchars($b['status']); ?>
							</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr id="batch-<?php echo (int)$b['id']; ?>" class="hidden bg-gray-50" data-detail-for="<?php echo (int)$b['id']; ?>">
					<td colspan="6" class="px-6 py-4">
						<div class="batch-detail-card bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
							<div class="flex flex-col gap-1">
								<p class="text-xs uppercase tracking-wide text-gray-500">Batch</p>
								<div class="flex items-center gap-2 text-lg font-semibold text-gray-900">
									<i data-lucide="hash" class="w-4 h-4 text-blue-500"></i>
									#<?php echo (int)$b['id']; ?>
								</div>
								<p class="text-sm text-gray-500">Status: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($b['status']); ?></span></p>
								<?php if (!empty($b['custom_requester'])): ?>
									<p class="text-sm text-gray-600">Name: <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($b['custom_requester']); ?></span></p>
								<?php endif; ?>
								<?php if (!empty($b['custom_ingredients'])): ?>
									<p class="text-xs text-gray-500 whitespace-pre-line">Notes: <?php echo htmlspecialchars($b['custom_ingredients']); ?></p>
								<?php endif; ?>
							</div>
							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
									<p class="text-xs uppercase tracking-wide text-blue-700">Requested By</p>
									<p class="text-sm font-semibold text-blue-900 mt-1"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></p>
								</div>
								<div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
									<p class="text-xs uppercase tracking-wide text-emerald-700">Date Requested</p>
									<p class="text-sm font-semibold text-emerald-900 mt-1"><?php echo htmlspecialchars($b['date_requested']); ?></p>
								</div>
							</div>
							<div class="space-y-2">
								<h4 class="font-semibold text-gray-900 flex items-center gap-2">
									<i data-lucide="list-checks" class="w-4 h-4 text-gray-500"></i>
									Items In Batch
								</h4>
								<ul class="space-y-2">
									<?php foreach ($items as $it): ?>
									<li class="flex items-center justify-between text-sm rounded-lg border border-gray-100 px-3 py-2">
										<div class="flex items-center gap-3">
											<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-600 text-xs font-medium"><?php echo strtoupper(substr($it['item_name'],0,2)); ?></span>
											<div>
												<p class="font-medium text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></p>
												<p class="text-xs text-gray-500">Unit: <?php echo htmlspecialchars($it['unit']); ?></p>
												<?php if (!empty($it['set_name'])): ?>
													<p class="text-[11px] text-indigo-600 font-semibold mt-1">Part of <?php echo htmlspecialchars($it['set_name']); ?> set</p>
												<?php endif; ?>
											</div>
										</div>
										<span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
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

<div id="prepareModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
		<div class="flex items-center justify-between px-6 py-4 border-b">
			<div>
				<p class="text-xs uppercase tracking-wide text-gray-500">Batch</p>
				<p class="text-lg font-semibold text-gray-900" id="prepareModalBatchLabel">#0</p>
			</div>
			<button type="button" class="prepareModalClose text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Close">&times;</button>
		</div>
		<div class="px-6 py-4 space-y-4">
					<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
				<div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
					<p class="text-xs uppercase tracking-wide text-blue-700">Request Name</p>
					<p class="font-semibold text-blue-900 mt-1" id="prepareModalRequestName">—</p>
				</div>
				<div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
					<p class="text-xs uppercase tracking-wide text-emerald-700">Date Needed</p>
					<p class="font-semibold text-emerald-900 mt-1" id="prepareModalRequestDate">—</p>
				</div>
				<div class="rounded-xl bg-purple-50 border border-purple-100 p-4">
					<p class="text-xs uppercase tracking-wide text-purple-700">Requested By</p>
					<p class="font-semibold text-purple-900 mt-1" id="prepareModalStaff">—</p>
				</div>
			</div>
			<div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
				<p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Notes</p>
				<p class="text-sm text-gray-700" id="prepareModalNotes">—</p>
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
							<button type="button" id="prepareAddItemBtn" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
								<i data-lucide="plus" class="w-4 h-4"></i>
								Add Ingredient
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
					<button type="button" data-action="save" class="prepareSubmitBtn inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
						<i data-lucide="save" class="w-4 h-4"></i>
						Save Prep
					</button>
					<button type="button" data-action="distribute" class="prepareSubmitBtn inline-flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
						<i data-lucide="send" class="w-4 h-4"></i>
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
			overlay.className = 'fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4';
			const modal = document.createElement('div');
			modal.className = 'bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[85vh] overflow-y-auto';
			modal.innerHTML = `
				<div class="flex items-center justify-between px-6 py-4 border-b">
					<div>
						<p class="text-xs uppercase tracking-wide text-gray-500">Request Batch</p>
						<p class="text-lg font-semibold text-gray-900">#${id}</p>
					</div>
					<button type="button" class="closeBatchModal text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Close">&times;</button>
				</div>
				<div class="px-6 py-4 space-y-4">${card.innerHTML}</div>
			`;
			overlay.appendChild(modal);
			document.body.appendChild(overlay);
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
			document.getElementById('prepareModalBatchLabel').textContent = '#' + (button.getAttribute('data-batch') || '0');
			document.getElementById('prepareModalRequestName').textContent = button.getAttribute('data-requester') || '—';
			document.getElementById('prepareModalRequestDate').textContent = button.getAttribute('data-date') || '—';
			document.getElementById('prepareModalStaff').textContent = button.getAttribute('data-staff') || '—';
			document.getElementById('prepareModalNotes').textContent = button.getAttribute('data-notes') || '—';
			batchIdInput.value = button.getAttribute('data-batch') || '';
			actionInput.value = 'save';
			renderItems();
			modal.classList.remove('hidden');
			modal.classList.add('flex');
			document.body.classList.add('overflow-hidden');
			configurePrepareUnits('');
		}

		function closeModal(){
			modal.classList.add('hidden');
			modal.classList.remove('flex');
			document.body.classList.remove('overflow-hidden');
			items = [];
			renderItems();
			quantityInput.value = '';
			ingredientSelect.value = '';
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

		closeBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (event)=>{
			if (event.target === modal){
				closeModal();
			}
		});
	}

	initPrepareModal();
})();
</script>


