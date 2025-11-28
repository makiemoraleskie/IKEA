<?php
// Variables available: $purchases, $filters
$filters = is_array($filters ?? null) ? $filters : [];
$filterDateFrom = trim((string)($filters['date_from'] ?? ''));
$filterDateTo = trim((string)($filters['date_to'] ?? ''));
$filterSupplier = trim((string)($filters['supplier'] ?? ''));
$filterItem = trim((string)($filters['item_id'] ?? ''));
$filterPaymentStatus = trim((string)($filters['payment_status'] ?? ''));
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Purchases Report</title>
	<style>
		:root {
			--primary: #1d4ed8;
			--accent: #4ade80;
			--muted: #6b7280;
			--border: #e5e7eb;
		}
		body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111827; margin: 24px; }
		h1 { font-size: 24px; margin: 0 0 4px 0; color: var(--primary); }
		section { margin-top: 20px; }
		.card { border: 1px solid var(--border); border-radius: 8px; padding: 16px; margin-bottom: 16px; }
		.meta { font-size: 11px; color: var(--muted); margin-top: 2px; }
		table { width: 100%; border-collapse: collapse; margin-top: 12px; }
		th, td { border: 1px solid var(--border); padding: 8px 10px; }
		th { background: #f8fafc; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.03em; color: #0f172a; }
		td { font-size: 12px; }
		.summary-grid { display: flex; gap: 12px; }
		.summary { flex: 1; background: #eef2ff; border-radius: 8px; padding: 12px; border: 1px solid #c7d2fe; }
		.summary span { display: block; font-size: 11px; text-transform: uppercase; color: var(--muted); letter-spacing: 0.04em; }
		.summary strong { display: block; font-size: 18px; margin-top: 6px; }
		.filter-list { list-style: none; padding: 0; margin: 0; }
		.filter-list li { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
	</style>
</head>
<body>
	<header class="card">
		<h1>IKEA Commissary — Purchases Report</h1>
		<p class="meta">Generated on <?php echo htmlspecialchars(date('M j, Y g:i A')); ?></p>
	</header>

	<section class="card">
		<h2 style="margin:0 0 10px 0; font-size:16px;">Filter Summary</h2>
		<ul class="filter-list">
			<li><strong>Date range:</strong> <?php echo $filterDateFrom ?: 'Any'; ?> → <?php echo $filterDateTo ?: 'Any'; ?></li>
			<li><strong>Supplier:</strong> <?php echo $filterSupplier ?: 'All suppliers'; ?></li>
			<li><strong>Item:</strong> <?php echo $filterItem ? 'ID #' . (int)$filterItem : 'All items'; ?></li>
			<li><strong>Payment Status:</strong> <?php echo $filterPaymentStatus ?: 'All'; ?></li>
		</ul>
	</section>

	<section class="card">
		<h2 style="margin:0 0 10px 0; font-size:16px;">Snapshot</h2>
		<div class="summary-grid">
			<div class="summary">
				<span>Transactions</span>
				<strong><?php echo count($purchases); ?></strong>
			</div>
			<div class="summary">
				<span>Total Cost</span>
				<strong>₱<?php echo number_format(array_sum(array_column($purchases, 'cost')), 2); ?></strong>
			</div>
			<div class="summary">
				<span>Suppliers</span>
				<strong><?php echo count(array_unique(array_column($purchases, 'supplier'))); ?></strong>
			</div>
			<div class="summary">
				<span>Items</span>
				<strong><?php echo count(array_unique(array_column($purchases, 'item_name'))); ?></strong>
			</div>
		</div>
	</section>

	<section class="card">
		<h2 style="margin:0 0 10px 0; font-size:16px;">Detailed Transactions</h2>
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>Item</th>
					<th>Supplier</th>
					<th>Quantity</th>
					<th>Cost</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($purchases as $index => $p): ?>
				<tr>
					<td><?php echo $index + 1; ?></td>
					<td><?php echo htmlspecialchars($p['date_purchased']); ?></td>
					<td><?php echo htmlspecialchars($p['item_name']); ?></td>
					<td><?php echo htmlspecialchars($p['supplier']); ?></td>
					<td><?php echo htmlspecialchars(number_format((float)$p['quantity'], 2)); ?></td>
					<td>₱<?php echo number_format((float)$p['cost'], 2); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($purchases)): ?>
				<tr>
					<td colspan="6" style="text-align:center; color:var(--muted); padding:20px;">No purchases match the selected filters.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</section>
</body>
</html>


