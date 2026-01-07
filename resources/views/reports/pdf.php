<?php
// Variables available: $purchases, $filters, $consumption, $section
$filters = is_array($filters ?? null) ? $filters : [];
$section = $section ?? 'purchase';
$showCosts = isset($showCosts) ? (bool)$showCosts : true;
$filterDateFrom = trim((string)($filters['date_from'] ?? ''));
$filterDateTo = trim((string)($filters['date_to'] ?? ''));
$filterSupplier = trim((string)($filters['supplier'] ?? ''));
$filterItem = trim((string)($filters['item_id'] ?? ''));
$filterPaymentStatus = trim((string)($filters['payment_status'] ?? ''));
$filterCategory = trim((string)($filters['category'] ?? ''));
$filterUsageStatus = trim((string)($filters['usage_status'] ?? ''));
$usageStatusLabels = [
	'used' => 'Used',
	'expired' => 'Expired',
	'transferred' => 'Transferred',
];
$usageStatusLabel = $filterUsageStatus !== '' ? ($usageStatusLabels[$filterUsageStatus] ?? ucfirst($filterUsageStatus)) : 'All';
$purchases = $purchases ?? [];
$consumption = $consumption ?? [];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>IKEA Commissary Report (<?php echo ucfirst($section); ?>)</title>
	<style>
		* {
			box-sizing: border-box;
		}
		body { 
			font-family: DejaVu Sans, Arial, sans-serif; 
			font-size: 9px; 
			color: #000; 
			margin: 0;
			padding: 0;
			width: 100%;
			overflow-x: hidden;
		}
		header {
			margin-bottom: 15px;
		}
		h1 { 
			font-size: 18px; 
			margin: 0 0 8px 0; 
			padding: 0;
			font-weight: bold;
			color: #000;
			line-height: 1.2;
		}
		.meta { 
			font-size: 9px; 
			margin: 0 0 0 0;
			padding: 0;
			color: #000;
			line-height: 1.4;
		}
		.filters { 
			margin: 0 0 15px 0;
			padding: 0;
			font-size: 9px;
			color: #000;
		}
		.filters p {
			margin: 3px 0;
			padding: 0;
			line-height: 1.4;
		}
		.report-title {
			font-size: 14px;
			font-weight: bold;
			margin: 15px 0 8px 0;
			padding: 0;
			color: #000;
			line-height: 1.3;
		}
		table { 
			width: 100%;
			max-width: 100%;
			border-collapse: collapse; 
			margin: 0 0 20px 0;
			padding: 0;
			font-size: 8px;
			table-layout: fixed;
		}
		th, td { 
			border: 1px solid #000; 
			padding: 3px 4px; 
			text-align: left;
			word-wrap: break-word;
			word-break: break-word;
			overflow: hidden;
			line-height: 1.2;
			max-width: 0;
		}
		th { 
			font-weight: bold; 
			font-size: 8px;
			background: #fff;
			color: #000;
		}
		td { 
			font-size: 8px;
			color: #000;
		}
		thead {
			display: table-header-group;
		}
		tfoot {
			display: table-footer-group;
		}
		tbody tr {
			page-break-inside: avoid;
		}
		thead tr {
			page-break-after: avoid;
			page-break-inside: avoid;
		}
		.table-container {
			width: 100%;
			max-width: 100%;
			overflow: hidden;
		}
		@page {
			margin: 18mm 15mm 18mm 15mm;
			size: A4 portrait;
		}
	</style>
</head>
<body>
	<header>
		<h1>IKEA Commissary Report (<?php echo ucfirst($section); ?>)</h1>
		<p class="meta">Generated on: <?php echo htmlspecialchars(date('M j, Y g:i A')); ?></p>
	</header>

	<section class="filters">
		<p><strong>Date Range:</strong> <?php echo $filterDateFrom ? htmlspecialchars($filterDateFrom) : ''; ?><?php echo ($filterDateFrom && $filterDateTo) ? ' - ' : ''; ?><?php echo $filterDateTo ? htmlspecialchars($filterDateTo) : ''; ?></p>
		<?php if ($section === 'consumption'): ?>
			<p><strong>Category:</strong> <?php echo $filterCategory !== '' ? htmlspecialchars($filterCategory) : ''; ?></p>
			<p><strong>Usage Status:</strong> <?php echo htmlspecialchars($usageStatusLabel); ?></p>
		<?php else: ?>
			<p><strong>Supplier:</strong> <?php echo $filterSupplier ? htmlspecialchars($filterSupplier) : ''; ?></p>
			<p><strong>Item:</strong> <?php echo $filterItem ? 'ID #' . (int)$filterItem : ''; ?></p>
			<p><strong>Payment Status:</strong> <?php echo $filterPaymentStatus ? htmlspecialchars($filterPaymentStatus) : ''; ?></p>
			<p><strong>Category:</strong> <?php echo $filterCategory !== '' ? htmlspecialchars($filterCategory) : ''; ?></p>
		<?php endif; ?>
	</section>

	<?php if ($section === 'consumption'): 
		// Helper function to format date and time
		function formatDateTime($dateString) {
			if (empty($dateString)) return '—';
			try {
				$date = new DateTime($dateString);
				return $date->format('M j, Y g:i A');
			} catch (Exception $e) {
				return $dateString;
			}
		}
	?>
		<div class="report-title">Ingredient Consumption</div>
		<div class="table-container">
		<table>
			<thead>
				<tr>
					<th style="width: 22%;">Date</th>
					<th style="width: 23%;">Name</th>
					<th style="width: 13%;">Total Used</th>
					<th style="width: 8%;">Unit</th>
					<th style="width: 34%;">Remaining Stock</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($consumption as $row): 
					$baseQty = (float)($row['total_quantity'] ?? 0);
					$unit = $row['unit'] ?? '';
					$displayUnit = $row['display_unit'] ?? '';
					$displayFactor = (float)($row['display_factor'] ?? 1);
					$remainingStock = (float)($row['remaining_stock'] ?? 0);
					
					// Format remaining stock: base unit (display unit)
					$remainingStockDisplay = number_format($remainingStock, 2) . ' ' . htmlspecialchars($unit);
					if ($displayUnit !== '' && $displayFactor > 0 && abs($displayFactor - 1) > 0.00001) {
						$remainingDisplayQty = $remainingStock / $displayFactor;
						$remainingStockDisplay .= ' (' . number_format($remainingDisplayQty, 2) . ' ' . htmlspecialchars($displayUnit) . ')';
					}
				?>
				<tr>
					<td><?php echo htmlspecialchars(formatDateTime($row['distribution_date'] ?? '')); ?></td>
					<td><?php echo htmlspecialchars($row['name']); ?></td>
					<td><?php echo number_format($baseQty, 2); ?></td>
					<td><?php echo htmlspecialchars($unit ?: 'unit'); ?></td>
					<td><?php echo $remainingStockDisplay; ?></td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($consumption)): ?>
				<tr>
					<td colspan="5" style="text-align:center; padding:20px;">No consumption records match the selected filters.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		</div>
	<?php else: ?>
		<div class="report-title">Purchases</div>
		<div class="table-container">
		<table>
			<thead>
				<tr>
					<th style="width: 4%;">#</th>
					<th style="width: 14%;">Date</th>
					<th style="width: 23%;">Item</th>
					<th style="width: 18%;">Supplier</th>
					<th style="width: 14%;">Quantity</th>
					<?php if ($showCosts): ?>
					<th style="width: 27%;">Cost</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($purchases as $index => $p): ?>
				<tr>
					<td><?php echo $index + 1; ?></td>
					<td><?php echo htmlspecialchars($p['date_purchased']); ?></td>
					<td><?php echo htmlspecialchars($p['item_name']); ?></td>
					<td><?php echo htmlspecialchars($p['supplier']); ?></td>
					<td><?php echo number_format((float)$p['quantity'], 2); ?></td>
					<?php if ($showCosts): ?>
					<td>₱<?php echo number_format((float)$p['cost'], 2); ?></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($purchases)): ?>
				<tr>
					<td colspan="<?php echo $showCosts ? 6 : 5; ?>" style="text-align:center; padding:20px;">No purchases match the selected filters.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		</div>
	<?php endif; ?>
</body>
</html>


