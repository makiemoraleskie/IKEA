
<!-- Welcome Banner -->
<div class="bg-white rounded-2xl shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">
				Welcome back, <span class="text-green-600"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?>!</span>
			</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Full system access enabled</p>

		</div>
	</div>
	
	<!-- Welcome message -->
	<div class="relative z-10 pr-24 sm:pr-28">
		<h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight leading-tight">Welcome back,<br/><span class="bg-gradient-to-r from-[#008000] to-[#00A86B] bg-clip-text text-transparent"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></span>!</h1>
	</div>
</div>

<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$canViewCosts = $canViewCosts ?? true;
$dashboardWidgets = $dashboardWidgets ?? ['low_stock','pending_requests','pending_payments','partial_deliveries','pending_deliveries','inventory_value'];
$lowStock = (int)($stats['lowStockCount'] ?? 0);
$pendingRequests = (int)($stats['pendingRequests'] ?? 0);
$pendingPayments = (int)($stats['pendingPayments'] ?? 0);
$partialDeliveries = (int)($stats['partialDeliveries'] ?? 0);
$pendingDeliveries = (int)($stats['pendingDeliveries'] ?? 0);
$inventoryValue = (float)($stats['inventoryValue'] ?? 0);
$chart = $stats['chart'] ?? ['labels' => [], 'purchases' => [], 'deliveries' => []];
$chartTotal = array_sum($chart['purchases'] ?? []) + array_sum($chart['deliveries'] ?? []);



$cardCopy = [
	'lowStock' => 'No ingredients at critical levels',
	'pendingRequests' => 'No outstanding approvals',
	'pendingPayments' => 'All payments cleared',
	'partialDeliveries' => 'No partial drops to chase',
	'pendingDeliveries' => 'Nothing waiting for dispatch',
];
?>


<!-- Dashboard Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8 max-w-full overflow-x-hidden">
	<!-- Low Stock Items -->
	<?php if (in_array('low_stock', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="alert-triangle" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-red-500"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">LOW STOCK ITEMS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $lowStock; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3"><?php echo htmlspecialchars($cardCopy['lowStock']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory?focus=low-stock#inventory-low-stock" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">Review inventory ></a>

		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Requests -->

	<?php if (in_array('pending_requests', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="file-text" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PENDING REQUESTS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $pendingRequests; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3"><?php echo htmlspecialchars($cardCopy['pendingRequests']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/requests?status=pending#requests-history" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">Go to approval queue ></a>

		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Payments -->

	<?php if (in_array('pending_payments', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="credit-card" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PENDING PAYMENTS</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $pendingPayments; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3"><?php echo htmlspecialchars($cardCopy['pendingPayments']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/purchases?payment=Pending#recent-purchases" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">Review payments ></a>

		</div>
	</div>
	<?php endif; ?>

	<!-- Partial Deliveries -->

	<?php if (in_array('partial_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="truck" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">PARTIAL DELIVERIES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $partialDeliveries; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3"><?php echo htmlspecialchars($cardCopy['partialDeliveries']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=partial#recent-deliveries" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">Track deliveries ></a>

		</div>
	</div>
	<?php endif; ?>

	<!-- Pending Deliveries -->

	<?php if (in_array('pending_deliveries', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<i data-lucide="package" class="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-600"></i>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">AWAITING DELIVERIES</h3>
			<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5"><?php echo $pendingDeliveries; ?></div>
			<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3"><?php echo htmlspecialchars($cardCopy['pendingDeliveries']); ?></p>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries?status=awaiting#awaiting-deliveries" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">Open delivery board ></a>

		</div>
	</div>
	<?php endif; ?>

	<!-- Inventory Value -->

	<?php if (in_array('inventory_value', $dashboardWidgets, true)): ?>
	<div class="bg-white rounded-lg shadow-none border border-gray-200 p-3 md:p-4 lg:p-5 relative">
		<div class="absolute top-2.5 md:top-3 right-2.5 md:right-3">
			<span class="text-lg md:text-xl font-bold text-green-600">₱</span>
		</div>
		<div class="flex flex-col">
			<h3 class="text-[9px] md:text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 md:mb-2">INVENTORY VALUE</h3>
			<?php if ($canViewCosts): ?>
				<div class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight text-gray-900 mb-1 md:mb-1.5">₱<?php echo number_format($inventoryValue, 2); ?></div>
				<p class="text-[10px] md:text-xs text-gray-600 mb-2 md:mb-3">estimated replacement value</p>
				<a href="<?php echo htmlspecialchars($baseUrl); ?>/inventory#inventory-low-stock" class="text-[10px] md:text-xs font-semibold text-green-600 hover:text-green-700">See stock ledger ></a>
			<?php else: ?>
				<div class="text-sm md:text-base font-semibold text-gray-500">Hidden</div>
				<p class="text-[10px] md:text-xs text-gray-500 mb-1.5 md:mb-2">Your role cannot view valuation data.</p>
			<?php endif; ?>

		</div>
	</div>
	<?php endif; ?>
</div>



