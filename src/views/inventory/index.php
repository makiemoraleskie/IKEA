<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">Inventory</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<div class="bg-white border rounded">
    <div class="p-4 border-b"><h2 class="text-lg font-semibold">Ingredients</h2></div>
    <?php if (!empty($flash)): ?>
    <div class="px-4 py-2 <?php echo $flash['type']==='success'?'bg-emerald-50 text-emerald-700':'bg-red-50 text-red-700'; ?>"><?php echo htmlspecialchars($flash['text']); ?></div>
    <?php endif; ?>
	<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
	<?php if (in_array(Auth::role(), ['Owner','Manager'], true)): ?>
	<div class="p-4 border-b bg-gray-50">
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<div>
				<label class="block text-sm mb-1">Name</label>
				<input name="name" class="w-full border rounded px-3 py-2" required />
			</div>
			<div>
				<label class="block text-sm mb-1">Unit</label>
				<input name="unit" class="w-full border rounded px-3 py-2" placeholder="kg, g, pcs" required />
			</div>
			<div>
				<label class="block text-sm mb-1">Display Unit</label>
				<input name="display_unit" class="w-full border rounded px-3 py-2" placeholder="kg, L (optional)" />
			</div>
			<div>
				<label class="block text-sm mb-1">Display Factor</label>
				<input type="number" step="0.01" min="0.01" name="display_factor" class="w-full border rounded px-3 py-2" value="1" />
			</div>
			<div>
				<label class="block text-sm mb-1">Reorder Level</label>
				<input type="number" step="0.01" min="0" name="reorder_level" class="w-full border rounded px-3 py-2" value="0" />
			</div>
			<div class="md:col-span-2">
				<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Ingredient</button>
			</div>
		</form>
	</div>
	<?php endif; ?>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">Name</th>
					<th class="text-left px-4 py-2">Unit</th>
					<th class="text-left px-4 py-2">Quantity</th>
					<th class="text-left px-4 py-2">Reorder Level</th>
					<th class="text-left px-4 py-2">Status</th>
                    
                </tr>
			</thead>
			<tbody>
				<?php foreach ($ingredients as $ing): $low = (float)$ing['quantity'] <= (float)$ing['reorder_level']; ?>
				<tr class="border-t <?php echo $low ? 'bg-red-50' : ''; ?>">
					<td class="px-4 py-2"><?php echo htmlspecialchars($ing['name']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($ing['unit']); ?></td>
					<td class="px-4 py-2"><?php echo number_format((float)$ing['quantity'], 2); ?></td>
					<td class="px-4 py-2"><?php echo number_format((float)$ing['reorder_level'], 2); ?></td>
					<td class="px-4 py-2">
						<?php if ($low): ?>
							<span class="px-2 py-1 rounded text-xs bg-red-100 text-red-700">Low</span>
						<?php else: ?>
							<span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">OK</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


