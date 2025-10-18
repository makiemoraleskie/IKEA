<?php
// Variables available: $purchases, $filters
?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Purchases Report</title>
	<style>
		body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
		h1 { font-size: 18px; margin-bottom: 6px; }
		.small { color: #666; }
		table { width: 100%; border-collapse: collapse; }
		th, td { border: 1px solid #ddd; padding: 6px; }
		th { background: #f5f5f5; text-align: left; }
	</style>
</head>
<body>
	<h1>Purchases Report</h1>
	<div class="small">
		Filters:
		<?php echo htmlspecialchars(json_encode($filters, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); ?>
	</div>
	<table>
		<thead>
			<tr>
				<th>Date</th>
				<th>Item</th>
				<th>Supplier</th>
				<th>Quantity</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($purchases as $p): ?>
			<tr>
				<td><?php echo htmlspecialchars($p['date_purchased']); ?></td>
				<td><?php echo htmlspecialchars($p['item_name']); ?></td>
				<td><?php echo htmlspecialchars($p['supplier']); ?></td>
				<td><?php echo htmlspecialchars($p['quantity']); ?></td>
				<td>₱<?php echo number_format((float)$p['cost'], 2); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</body>
</html>


