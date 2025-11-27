<?php 
$baseUrl = defined('BASE_URL') ? BASE_URL : ''; 
$statusFilter = strtolower((string)($_GET['status'] ?? 'all'));
$ingredientSets = $ingredientSets ?? [];
$availableSets = array_values(array_filter($ingredientSets, static fn($set) => !empty($set['is_available'])));
$availableSetsCount = count($availableSets);
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
		<p class="text-sm text-gray-600 mt-1">Create a new ingredient request batch</p>
	</div>
	
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests" id="requestForm" class="p-4 sm:p-6">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

		<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
			<!-- Left: Add items panel -->
			<section class="space-y-6">
				<div class="bg-gray-50 rounded-lg p-4 sm:p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
							<span class="text-sm font-semibold text-blue-600">1</span>
						</div>
						<div>
							<h3 class="font-semibold text-gray-900">Choose Ingredient</h3>
							<p class="text-sm text-gray-600">Search or select from available ingredients</p>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Search Ingredient</label>
							<div class="relative">
								<input id="ingredientSearch" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Type ingredient name..." autocomplete="off" />
								<i data-lucide="search" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
								<input type="hidden" id="ingredientIdHidden" />
								<div id="ingredientResults" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden"></div>
							</div>
						</div>
						
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Or Select from List</label>
							<select id="ingredientSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
								<option value="">Choose from dropdown</option>
								<?php foreach ($ingredients as $ing): ?>
									<option value="<?php echo (int)$ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit']); ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="bg-gray-50 rounded-lg p-4 sm:p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
							<span class="text-sm font-semibold text-green-600">2</span>
						</div>
						<div>
							<h3 class="font-semibold text-gray-900">Set Quantity</h3>
							<p class="text-sm text-gray-600">Specify the amount needed</p>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Quantity</label>
							<input id="quantityInput" type="number" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter amount" />
						</div>
						
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Unit</label>
							<select id="unitSelector" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></select>
						</div>
					</div>
					
					<div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
						<p class="text-xs text-gray-500 flex items-center gap-1">
							<i data-lucide="info" class="w-3 h-3"></i>
							All quantities are stored in base units (g/ml/pcs)
						</p>
						<button type="button" id="addItemBtn" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="plus" class="w-4 h-4"></i>
							Add to List
						</button>
					</div>
                    <div id="requestError" class="hidden mt-4 px-4 py-3 rounded-lg border border-red-200 bg-red-50 text-sm text-red-800"></div>
				</div>
				<?php if (!empty($availableSets)): ?>
				<div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 space-y-4">
					<div class="flex items-start gap-3">
						<span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
							<i data-lucide="layers" class="w-5 h-5"></i>
						</span>
						<div>
							<h3 class="font-semibold text-gray-900">Quick Sets</h3>
							<p class="text-sm text-gray-600">Search for an available set and add all of its ingredients at once.</p>
						</div>
					</div>
					<div class="space-y-4">
						<div class="grid gap-4 md:grid-cols-2">
							<div class="space-y-2">
								<label class="text-sm font-medium text-gray-700" for="setPickerInput">Choose a set</label>
								<input id="setPickerInput" type="text" list="setPickerOptions" placeholder="Start typing to search..." class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
								<datalist id="setPickerOptions">
									<?php foreach ($availableSets as $set): ?>
										<option value="<?php echo htmlspecialchars($set['name']); ?>"></option>
									<?php endforeach; ?>
								</datalist>
							</div>
							<div class="space-y-2">
								<label class="text-sm font-medium text-gray-700" for="setPickerQty">Number of sets</label>
								<input id="setPickerQty" type="number" min="1" value="1" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
							</div>
						</div>
						<div id="setPickerSummary" class="rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
							Select a set to see its ingredient summary and stock status.
						</div>
						<div class="flex justify-end">
							<button type="button" id="setPickerAddBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-200 text-white cursor-not-allowed" disabled>
								<i data-lucide="plus" class="w-4 h-4"></i>
								Add set to request
							</button>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</section>

			<!-- Right: Staged list panel -->
			<section class="bg-gray-50 rounded-lg overflow-hidden flex flex-col">
				<div class="bg-white border-b px-4 sm:px-6 py-4">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-3">
							<i data-lucide="list" class="w-5 h-5 text-gray-600"></i>
							<h3 class="font-semibold text-gray-900">Items in this Request</h3>
						</div>
						<div class="flex items-center gap-3">
							<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
								<i data-lucide="hash" class="w-3 h-3"></i>
								<span id="itemCountBadge">0</span> items
							</span>
							<button type="button" id="clearListBtn" class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
								<i data-lucide="trash-2" class="w-3 h-3"></i>
								Clear All
							</button>
						</div>
					</div>
				</div>
				
				<div class="flex-1 overflow-hidden">
					<div class="h-full overflow-y-auto overflow-x-auto">
						<table class="w-full text-sm min-w-[480px]">
							<thead class="bg-gray-100 sticky top-0">
								<tr>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Ingredient</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Quantity</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Unit</th>
									<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
								</tr>
							</thead>
							<tbody id="listBody" class="divide-y divide-gray-200"></tbody>
						</table>
						
						<!-- Empty State -->
						<div id="emptyState" class="flex flex-col items-center justify-center py-12 text-gray-500">
							<i data-lucide="package" class="w-12 h-12 mb-3 text-gray-300"></i>
							<p class="text-sm">No items added yet</p>
							<p class="text-xs text-gray-400">Add ingredients to create your request</p>
						</div>
					</div>
				</div>
				
				<div class="bg-white border-t px-4 sm:px-6 py-4">
					<button id="submitBtn" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2" disabled>
						<i data-lucide="send" class="w-4 h-4"></i>
						Submit Batch Request
					</button>
				</div>
			</section>
		</div>
	</form>
</div>
<?php endif; ?>

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
					<button type="button" class="viewBatchDetails inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50" data-batch="<?php echo (int)$batch['id']; ?>">
						<i data-lucide="eye" class="w-4 h-4"></i>
						View details
					</button>
					<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/requests/distribute" class="inline-flex items-center">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<input type="hidden" name="batch_id" value="<?php echo (int)$batch['id']; ?>">
						<button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
							<i data-lucide="send" class="w-4 h-4"></i>
							Distribute
						</button>
					</form>
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
					<th class="text-left px-6 py-3 font-medium text-gray-700">Requested By</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Items</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Status</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Date Requested</th>
					<th class="text-left px-6 py-3 font-medium text-gray-700">Actions</th>
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
								<span class="text-xs font-medium text-gray-600"><?php echo strtoupper(substr($b['staff_name'] ?? 'U', 0, 2)); ?></span>
							</div>
							<span class="font-medium text-gray-900"><?php echo htmlspecialchars($b['staff_name'] ?? (string)$b['staff_id']); ?></span>
						</div>
					</td>
					<td class="px-6 py-4">
						<?php $count=(int)($b['items_count'] ?? 0); ?>
						<?php if ($count === 1 && !empty($items)): ?>
							<?php $it=$items[0]; ?>
							<div class="flex items-center gap-2">
								<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
								<span class="text-gray-900"><?php echo htmlspecialchars($it['item_name']); ?></span>
								<span class="text-gray-500">— <?php echo htmlspecialchars($it['quantity']); ?> <?php echo htmlspecialchars($it['unit']); ?></span>
							</div>
						<?php else: ?>
							<div class="flex items-center gap-2">
								<i data-lucide="layers" class="w-4 h-4 text-gray-400"></i>
								<span class="font-medium text-gray-900"><?php echo $count; ?> items</span>
							</div>
						<?php endif; ?>
                        <button type="button" class="mt-1 text-blue-600 hover:text-blue-700 text-xs underline viewBatchDetails" data-batch="<?php echo (int)$b['id']; ?>">View details</button>
					</td>
					<td class="px-6 py-4">
						<?php 
						$statusClass = match($b['status']) {
							'Approved' => 'bg-green-100 text-green-800 border-green-200',
							'Rejected' => 'bg-red-100 text-red-800 border-red-200',
							default => 'bg-yellow-100 text-yellow-800 border-yellow-200'
						};
						$statusIcon = match($b['status']) {
							'Approved' => 'check-circle',
							'Rejected' => 'x-circle',
							default => 'clock'
						};
						?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
							<i data-lucide="<?php echo $statusIcon; ?>" class="w-3 h-3"></i>
							<?php echo htmlspecialchars($b['status']); ?>
						</span>
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
							<span class="text-gray-400 text-sm">No actions</span>
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

<div id="requestSetToast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden">
	<div id="requestSetToastInner" class="rounded-xl px-4 py-3 shadow-lg text-sm font-medium"></div>
</div>

<script>
(function(){
	const INGREDIENTS = <?php echo json_encode(array_map(function($i){
		return [
			'id' => (int)$i['id'],
			'name' => $i['name'],
			'unit' => $i['unit'],
			'quantity' => (float)($i['quantity'] ?? 0),
			'display_unit' => $i['display_unit'] ?? null,
			'display_factor' => (float)($i['display_factor'] ?? 1),
			'reorder_level' => (float)($i['reorder_level'] ?? 0),
		];
	}, $ingredients), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
	const SETS = <?php echo json_encode(array_map(function($set){
		return [
			'id' => (int)$set['id'],
			'name' => $set['name'],
			'is_available' => !empty($set['is_available']),
			'unavailable_reason' => $set['unavailable_reason'],
		'components' => array_map(static function ($component) {
			return [
				'ingredient_id' => (int)$component['ingredient_id'],
				'ingredient_name' => $component['ingredient_name'],
				'unit' => $component['unit'] ?? ($component['ingredient_unit'] ?? ''),
				'quantity' => (float)$component['quantity'],
			];
		}, $set['components']),
		];
	}, $ingredientSets), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
	const SET_LOOKUP = SETS.reduce((map, set) => {
		map[set.id] = set;
		return map;
	}, {});
	const SET_LOOKUP_BY_NAME = SETS.reduce((map, set) => {
		map[set.name.toLowerCase()] = set;
		return map;
	}, {});
	const AVAILABLE_SET_IDS = SETS.filter(set => set.is_available).map(set => set.id);

	const search = document.getElementById('ingredientSearch');
	const select = document.getElementById('ingredientSelect');
	const hiddenId = document.getElementById('ingredientIdHidden');
	const results = document.getElementById('ingredientResults');
	const qty = document.getElementById('quantityInput');
	const unitSel = document.getElementById('unitSelector');
	const addBtn = document.getElementById('addItemBtn');
	const listBody = document.getElementById('listBody');
	const submitBtn = document.getElementById('submitBtn');
	const clearBtn = document.getElementById('clearListBtn');
	const countBadge = document.getElementById('itemCountBadge');
	const emptyState = document.getElementById('emptyState');
	const statusFilterSelect = document.getElementById('requestStatusFilter');
	const requestRows = Array.from(document.querySelectorAll('tr[data-status]'));
	const requestsFilterEmpty = document.getElementById('requestsFilterEmpty');
	const requestErrorBox = document.getElementById('requestError');
	const toast = document.getElementById('requestSetToast');
	const toastInner = document.getElementById('requestSetToastInner');
	let toastTimer;
	const setPickerInput = document.getElementById('setPickerInput');
	const setPickerQty = document.getElementById('setPickerQty');
	const setPickerSummary = document.getElementById('setPickerSummary');
	const setPickerAddBtn = document.getElementById('setPickerAddBtn');
	let selectedSetId = null;

	function renderResults(items){
		if (!items.length){ 
			results.classList.add('hidden'); 
			results.innerHTML=''; 
			return; 
		}
		results.innerHTML = items.map(i => `
			<button type="button" data-id="${i.id}" class="w-full text-left px-4 py-3 hover:bg-blue-50 border-b border-gray-100 last:border-b-0 flex items-center gap-3">
				<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
				<div>
					<div class="font-medium text-gray-900">${i.name}</div>
					<div class="text-xs text-gray-500">Unit: ${i.unit}</div>
				</div>
			</button>
		`).join('');
		results.classList.remove('hidden');
	}

	let currentBaseUnit = '';
	function configureUnitChoices(baseUnit){
		unitSel.innerHTML = '';
		const opt = (v,t)=>{ const o=document.createElement('option'); o.value=v; o.textContent=t; return o; };
		if (baseUnit === 'g'){
			unitSel.appendChild(opt('g','g'));
			unitSel.appendChild(opt('kg','kg'));
		} else if (baseUnit === 'ml'){
			unitSel.appendChild(opt('ml','ml'));
			unitSel.appendChild(opt('L','L'));
		} else {
			unitSel.appendChild(opt(baseUnit || 'pcs', baseUnit || 'pcs'));
		}
		unitSel.value = baseUnit || '';
		currentBaseUnit = baseUnit || '';
	}

	function showRequestError(message){
		if (!requestErrorBox) return;
		requestErrorBox.textContent = message;
		requestErrorBox.classList.remove('hidden');
	}

	function clearRequestError(){
		if (!requestErrorBox) return;
		requestErrorBox.textContent = '';
		requestErrorBox.classList.add('hidden');
	}

	function getIngredientById(id){
		return INGREDIENTS.find(i => i.id === id);
	}

	function getAvailableQuantity(id){
		const ing = getIngredientById(id);
		return ing ? parseFloat(ing.quantity || 0) : 0;
	}

	function notifyIfUnavailable(itemId){
		const ing = getIngredientById(itemId);
		if (!ing) { return; }
		const available = getAvailableQuantity(itemId);
		if (available <= 0){
			showRequestError(`"${ing.name}" is currently out of stock and cannot be requested.`);
		} else {
			clearRequestError();
		}
	}

	function canAllocateQuantity(itemId, additionalBaseQuantity){
		const available = getAvailableQuantity(itemId);
		const existingBase = getExistingBaseQty(itemId);
		return (existingBase + additionalBaseQuantity) <= available + 0.0001;
	}

	function showSetToast(message, tone = 'error'){
		if (!toast || !toastInner){
			alert(message);
			return;
		}
		toastInner.textContent = message;
		toastInner.className = 'rounded-xl px-4 py-3 shadow-xl text-sm font-medium';
		if (tone === 'success'){
			toastInner.classList.add('bg-green-600','text-white');
		} else {
			toastInner.classList.add('bg-red-600','text-white');
		}
		toast.classList.remove('hidden');
		if (toastTimer){
			clearTimeout(toastTimer);
		}
		toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
	}

	search.addEventListener('input', ()=>{
		const q = search.value.trim().toLowerCase();
		if (!q){ hiddenId.value=''; renderResults([]); return; }
		const matches = INGREDIENTS.filter(i => i.name.toLowerCase().includes(q)).slice(0, 20);
		renderResults(matches);
	});

	results.addEventListener('click', (e)=>{
		const btn = e.target.closest('button[data-id]');
		if (!btn) return;
		const id = parseInt(btn.getAttribute('data-id'), 10);
		const item = INGREDIENTS.find(x => x.id === id);
		if (!item) return;
		hiddenId.value = String(item.id);
		search.value = item.name;
		configureUnitChoices(item.unit || '');
		notifyIfUnavailable(item.id);
		results.classList.add('hidden');
	});

	select.addEventListener('change', ()=>{
		hiddenId.value = '';
		if (select.selectedIndex > 0){
			const opt = select.selectedOptions[0];
			search.value = opt.textContent || '';
			configureUnitChoices(opt.dataset.unit || '');
			notifyIfUnavailable(parseInt(opt.value, 10) || 0);
		} else {
			search.value = '';
			configureUnitChoices('');
			clearRequestError();
		}
	});

	document.addEventListener('click', (e)=>{
		if (!results.contains(e.target) && e.target !== search){
			results.classList.add('hidden');
		}
	});

	function refreshSubmitState(){
		const itemCount = listBody.children.length;
		submitBtn.disabled = itemCount === 0;
		countBadge.textContent = String(itemCount);
		if (itemCount === 0) {
			emptyState.classList.remove('hidden');
		} else {
			emptyState.classList.add('hidden');
		}
	}

	function formatNum(n){
		return (Math.round((n + Number.EPSILON) * 100) / 100).toString();
	}

	function getExistingBaseQty(itemId){
		const row = listBody.querySelector(`tr[data-id="${itemId}"]`);
		if (!row) return 0;
		const hiddenQ = row.querySelector('input[name="quantity[]"]');
		return parseFloat(hiddenQ?.value || '0') || 0;
	}

	function addRow(itemId, name, baseUnit, baseQuantity, displayUnit, displayFactor, meta){
		const setLabel = meta?.name || '';
		const setId = meta?.id || '';
		const selector = `tr[data-id="${itemId}"][data-set-label="${setLabel}"]`;
		const existing = listBody.querySelector(selector);
		if (existing){
			const hiddenQ = existing.querySelector('input[name="quantity[]"]');
			const currentBase = parseFloat(hiddenQ.value || '0');
			const newBase = currentBase + baseQuantity;
			hiddenQ.value = newBase;
			const rowFactor = parseFloat(existing.getAttribute('data-factor') || '1');
			const rowDisplayUnit = existing.getAttribute('data-display') || baseUnit;
			existing.querySelector('.qval').textContent = formatNum(newBase / rowFactor);
			existing.querySelector('.uval').textContent = rowDisplayUnit;
			const badge = existing.querySelector('.set-badge');
			if (badge && setLabel){
				badge.textContent = setLabel;
			}
			existing.classList.add('bg-green-50');
			setTimeout(() => existing.classList.remove('bg-green-50'), 1000);
			return;
		}

		const tr = document.createElement('tr');
		tr.setAttribute('data-id', itemId);
		const factor = displayFactor || 1;
		const shownQty = baseQuantity / factor;
		tr.setAttribute('data-factor', String(factor));
		tr.setAttribute('data-display', displayUnit || baseUnit);
		tr.setAttribute('data-set-label', setLabel);
		tr.innerHTML = `
			<td class="px-6 py-4">
				<div class="flex items-center gap-3">
					<i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
					<span class="font-medium text-gray-900">${name}</span>
					${setLabel ? `<span class="set-badge inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">${setLabel}</span>` : '<span class="set-badge hidden"></span>'}
					<input type="hidden" name="item_id[]" value="${itemId}">
					<input type="hidden" name="source_set_id[]" value="${setId}">
					<input type="hidden" name="source_set_label[]" value="${setLabel}">
				</div>
			</td>
			<td class="px-6 py-4">
				<span class="font-medium text-gray-900 qval">${formatNum(shownQty)}</span>
				<input type="hidden" name="quantity[]" value="${baseQuantity}">
			</td>
			<td class="px-6 py-4">
				<span class="text-gray-600 uval">${displayUnit || baseUnit}</span>
			</td>
			<td class="px-6 py-4">
				<button type="button" class="removeRow inline-flex items-center gap-1 px-3 py-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
					<i data-lucide="trash-2" class="w-3 h-3"></i>
					Remove
				</button>
			</td>
		`;
		listBody.appendChild(tr);
		tr.classList.add('bg-green-50');
		setTimeout(() => tr.classList.remove('bg-green-50'), 1000);
		refreshSubmitState();
	}

	addBtn.addEventListener('click', ()=>{
		let itemId = parseInt(hiddenId.value || '0', 10);
		let name = search.value || '';
		if (!itemId){
			const selId = parseInt(select.value || '0', 10);
			if (selId){
				itemId = selId;
				name = select.selectedOptions[0]?.textContent || name;
			}
		}
		const quantity = parseFloat(qty.value || '0');
		if (!itemId || !quantity || quantity <= 0){ 
			addBtn.classList.add('bg-red-600');
			addBtn.innerHTML = '<i data-lucide="x" class="w-4 h-4"></i>Invalid Input';
			setTimeout(() => {
				addBtn.classList.remove('bg-red-600');
				addBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Add to List';
			}, 1500);
			return; 
		}

		const ingredient = getIngredientById(itemId);
		if (!ingredient){
			showRequestError('Please select a valid ingredient.');
			return;
		}
		const available = getAvailableQuantity(itemId);
		if (available <= 0){
			showRequestError(`"${ingredient.name}" is currently out of stock and cannot be requested.`);
			return;
		}

		let factor = 1;
		if (currentBaseUnit === 'g' && unitSel.value === 'kg') factor = 1000;
		if (currentBaseUnit === 'ml' && unitSel.value === 'L') factor = 1000;
		const baseQty = quantity * factor;
		const displayUnit = unitSel.value || currentBaseUnit;
		const existingBase = getExistingBaseQty(itemId);
		if ((existingBase + baseQty) > available + 0.0001){
			showRequestError(`You can only request up to ${available.toFixed(2)} ${ingredient.unit} of "${ingredient.name}".`);
			return;
		}

		addRow(itemId, name, currentBaseUnit, baseQty, displayUnit, factor);
		clearRequestError();

		qty.value = '';
		hiddenId.value='';
		search.value='';
		select.value='';
		configureUnitChoices('');

		addBtn.classList.add('bg-green-600');
		addBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>Added!';
		setTimeout(() => {
			addBtn.classList.remove('bg-green-600');
			addBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Add to List';
		}, 1000);
	});

	listBody.addEventListener('click', (e)=>{
		if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')){
			const tr = e.target.closest('tr');
			tr.classList.add('bg-red-50');
			setTimeout(() => {
				tr.remove();
				refreshSubmitState();
			}, 300);
		}
	});

	clearBtn.addEventListener('click', ()=>{
		if (listBody.children.length === 0) return;
		Array.from(listBody.children).forEach((row, index) => {
			setTimeout(() => {
				row.classList.add('bg-red-50');
				setTimeout(() => row.remove(), 200);
			}, index * 50);
		});
		setTimeout(() => refreshSubmitState(), listBody.children.length * 50 + 200);
	});

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

	function updateSetPickerSummary(set){
		if (!setPickerSummary) { return; }
		if (!set){
			setPickerSummary.className = 'rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500';
			setPickerSummary.textContent = 'Select a set to see its ingredient summary and stock status.';
			return;
		}
		const componentList = set.components.map(component => `<li class="flex items-center justify-between"><span>${component.ingredient_name}</span><span class="text-xs text-gray-500">${Number(component.quantity).toFixed(2)} ${component.unit}</span></li>`).join('');
		setPickerSummary.className = 'rounded-xl border px-4 py-3 text-sm bg-indigo-50 border-indigo-100 text-indigo-900';
		setPickerSummary.innerHTML = `
			<div class="flex items-center justify-between gap-2">
				<strong>${set.name}</strong>
				<span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Available</span>
			</div>
			<ul class="mt-2 space-y-1">${componentList}</ul>
		`;
	}

	function setPickerStateChanged(){
		if (!setPickerInput || !setPickerAddBtn) { return; }
		const value = (setPickerInput.value || '').trim().toLowerCase();
		const match = SET_LOOKUP_BY_NAME[value];
		if (!match || !match.is_available){
			selectedSetId = null;
			setPickerAddBtn.disabled = true;
			setPickerAddBtn.classList.add('bg-indigo-200','cursor-not-allowed');
			setPickerAddBtn.classList.remove('bg-indigo-600','hover:bg-indigo-700');
			updateSetPickerSummary(null);
			return;
		}
		selectedSetId = match.id;
		setPickerAddBtn.disabled = false;
		setPickerAddBtn.classList.remove('bg-indigo-200','cursor-not-allowed');
		setPickerAddBtn.classList.add('bg-indigo-600','hover:bg-indigo-700');
		updateSetPickerSummary(match);
	}

	function addSetToList(setId, multiplier){
		const set = SET_LOOKUP[setId];
		if (!set){
			showSetToast('This set is no longer available. Refresh the page.', 'error');
			return;
		}
		if (!set.is_available){
			showSetToast(set.unavailable_reason || 'This set cannot be requested right now.', 'error');
			return;
		}
		const scaledComponents = set.components.map(component => ({
			...component,
			baseQuantity: (component.quantity || 0) * multiplier,
		}));
		for (const component of scaledComponents){
			if (!component.baseQuantity) { continue; }
			const ingredient = getIngredientById(component.ingredient_id);
			if (!ingredient){
				showSetToast('One of the ingredients in this set no longer exists.', 'error');
				return;
			}
			if (!canAllocateQuantity(component.ingredient_id, component.baseQuantity)){
				showSetToast(`Not enough stock for "${component.ingredient_name}" to assemble ${multiplier} set${multiplier === 1 ? '' : 's'}.`, 'error');
				return;
			}
		}
		scaledComponents.forEach(component => {
			if (!component.baseQuantity) { return; }
			const ingredient = getIngredientById(component.ingredient_id);
			if (!ingredient) { return; }
			const displayFactor = ingredient.display_factor && ingredient.display_factor > 0 ? ingredient.display_factor : 1;
			const displayUnit = ingredient.display_unit || ingredient.unit;
			addRow(component.ingredient_id, component.ingredient_name, ingredient.unit, component.baseQuantity, displayUnit, displayFactor, { id: set.id, name: set.name });
		});
		refreshSubmitState();
		showSetToast(`${set.name} added to the request list.`, 'success');
	}

	if (setPickerInput){
		setPickerInput.addEventListener('input', setPickerStateChanged);
		setPickerInput.addEventListener('blur', ()=>{
			setTimeout(setPickerStateChanged, 50);
		});
		setPickerStateChanged();
	}

	if (setPickerAddBtn){
		setPickerAddBtn.addEventListener('click', ()=>{
			if (!selectedSetId){ return; }
			let multiplier = parseFloat(setPickerQty?.value || '1');
			if (!Number.isFinite(multiplier) || multiplier <= 0){
				multiplier = 1;
				if (setPickerQty){ setPickerQty.value = '1'; }
			}
			addSetToList(selectedSetId, multiplier);
		});
	}

	refreshSubmitState();
})();
</script>


