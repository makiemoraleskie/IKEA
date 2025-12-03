<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-md border border-gray-200 p-3 md:p-4 lg:p-6 mb-5 md:mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div>
			<h1 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 mb-0.5 md:mb-1">Import Inventory from CSV</h1>
			<p class="text-[10px] md:text-xs lg:text-sm text-gray-600">Upload a CSV file to import or update inventory items</p>
		</div>
	</div>
</div>

<!-- Import Form -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b">
		<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
			<i data-lucide="upload" class="w-5 h-5 text-blue-600"></i>
			Upload CSV File
		</h2>
		<p class="text-sm text-gray-600 mt-1">Select a CSV file containing inventory data</p>
	</div>
	
	<div class="p-4 sm:p-6">
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/inventory/import" enctype="multipart/form-data" class="space-y-6">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			
			<div class="space-y-2">
				<label class="block text-sm font-medium text-gray-700">CSV File</label>
				<div class="flex items-center gap-4">
					<input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
				</div>
				<p class="text-xs text-gray-500">Only CSV files are allowed. Maximum file size: 10MB</p>
			</div>
			
			<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
				<div class="flex items-start gap-3">
					<i data-lucide="info" class="w-5 h-5 text-amber-600 mt-0.5"></i>
					<div class="flex-1">
						<h3 class="text-sm font-semibold text-amber-900 mb-2">CSV Format Requirements</h3>
						<ul class="text-xs text-amber-800 space-y-1 list-disc list-inside">
							<li>Row 1: Date headers (will be skipped)</li>
							<li>Row 2: Column headers (NEW STOCK, DEDUCTION, REMAIN) - will be skipped</li>
							<li>Row 3+: Item name, Unit, then date columns (NEW STOCK, DEDUCTION, REMAIN)</li>
							<li>Item name must be in the first column</li>
							<li>Unit must be in the second column</li>
							<li>The system will use the latest REMAIN value (rightmost non-empty REMAIN column)</li>
						</ul>
					</div>
				</div>
			</div>
			
			<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
				<div class="flex items-start gap-3">
					<i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 mt-0.5"></i>
					<div class="flex-1">
						<h3 class="text-sm font-semibold text-blue-900 mb-2">Import Behavior</h3>
						<ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
							<li>If an ingredient with the same name exists, its quantity will be updated</li>
							<li>If an ingredient doesn't exist, it will be created with the imported quantity</li>
							<li>Units will be normalized automatically (e.g., "Pack" → "pack", "Kg" → "kg")</li>
							<li>Empty rows and rows with missing data will be skipped</li>
						</ul>
					</div>
				</div>
			</div>
			
			<div class="flex justify-end gap-3">
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory" class="inline-flex items-center gap-2 px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
					Cancel
				</a>
				<button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
					<i data-lucide="upload" class="w-4 h-4"></i>
					Import Inventory
				</button>
			</div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}
});
</script>
