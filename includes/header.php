<?php
declare(strict_types=1);
$appTitle = 'IKEA Commissary System';
$user = Auth::user();
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($appTitle); ?></title>
	<link rel="icon" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.ico">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		// Tailwind config placeholder if needed
	</script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
	<header class="bg-white border-b">
		<div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
			<div class="flex items-center gap-3">
				<img src="<?php echo htmlspecialchars($baseUrl); ?>/assets/logo.png" alt="Logo" class="w-8 h-8" />
				<span class="font-semibold">IKEA Cakes & Snacks Commissary</span>
			</div>
			<nav class="flex items-center gap-4">
				<?php if ($user): ?>
					<span class="text-sm text-gray-600">Hello, <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</span>
					<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/requests">Requests</a>
					<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/purchases">Purchases</a>
					<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/deliveries">Deliveries</a>
					<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/inventory">Inventory</a>
					<?php if (in_array($user['role'] ?? '', ['Owner','Manager'], true)): ?>
						<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/reports">Reports</a>
						<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/audit">Audit Logs</a>
						<?php if (($user['role'] ?? '') === 'Owner'): ?>
							<a class="text-sm text-blue-600" href="<?php echo htmlspecialchars($baseUrl); ?>/users">Users</a>
						<?php endif; ?>
					<?php endif; ?>
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/logout">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<button class="px-3 py-1.5 rounded bg-gray-800 text-white text-sm">Logout</button>
					</form>
				<?php else: ?>
					<a class="text-sm text-blue-600" href="/login">Login</a>
				<?php endif; ?>
			</nav>
		</div>
	</header>
	<main class="max-w-7xl mx-auto p-4">


