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
	<link rel="icon" href="<?php echo htmlspecialchars($baseUrl); ?>/public/favicon.ico">
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
	<script>
		// Tailwind config placeholder if needed
	</script>
</head>
<body class="h-screen bg-gray-50 text-gray-800 overflow-hidden">
	<?php if ($user): ?>
	<div class="flex h-screen">
		<!-- Sidebar -->
		<div class="w-64 bg-white shadow-lg">
			<!-- Logo -->
			<div class="p-6 border-b">
				<div class="flex items-center gap-3">
					<div class="w-1 h-8 bg-blue-600"></div>
					<span class="text-xl font-bold text-gray-800">iKEA</span>
				</div>
			</div>
			
			<!-- Navigation -->
			<nav class="mt-6">
				<?php 
				$currentPage = $_SERVER['REQUEST_URI'] ?? '';
				$navItems = [
					['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'bar-chart-3'],
					['url' => '/requests', 'label' => 'Requests', 'icon' => 'clipboard-list'],
					['url' => '/inventory', 'label' => 'Inventory', 'icon' => 'package'],
					['url' => '/purchases', 'label' => 'Transactions', 'icon' => 'receipt'],
					['url' => '/deliveries', 'label' => 'Deliveries', 'icon' => 'truck'],
				];
				
				if (in_array($user['role'] ?? '', ['Owner','Manager'], true)) {
					$navItems[] = ['url' => '/reports', 'label' => 'Reports', 'icon' => 'trending-up'];
					$navItems[] = ['url' => '/audit', 'label' => 'Audit Logs', 'icon' => 'clock'];
				}
				
				$navItems[] = ['url' => '/settings', 'label' => 'Settings', 'icon' => 'settings'];
				
				foreach ($navItems as $item): 
					$isActive = strpos($currentPage, $item['url']) !== false;
					$classes = $isActive ? 'flex items-center gap-3 px-6 py-3 text-blue-600 bg-blue-50 border-r-2 border-blue-600' : 'flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50';
				?>
					<a href="<?php echo htmlspecialchars($baseUrl . $item['url']); ?>" class="<?php echo $classes; ?>">
						<i data-lucide="<?php echo $item['icon']; ?>" class="w-5 h-5"></i>
						<span class="text-sm font-medium"><?php echo $item['label']; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
			
			<!-- User Info -->
			<div class="absolute bottom-0 w-64 p-6 border-t bg-white">
				<div class="flex items-center gap-3 mb-3">
					<div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
						<span class="text-sm font-medium text-gray-600"><?php echo strtoupper(substr($user['name'] ?? 'U', 0, 2)); ?></span>
					</div>
					<div>
						<div class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
						<div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
					</div>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/logout">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
					<button class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700">
						<i data-lucide="log-out" class="w-4 h-4"></i>
						<span>Logout</span>
					</button>
				</form>
			</div>
		</div>
		
		<!-- Main Content -->
		<div class="flex-1 flex flex-col">
			<!-- Top Header -->
			<header class="bg-white border-b px-6 py-4">
				<div class="flex items-center justify-between">
					<h1 class="text-2xl font-bold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
					<div class="flex items-center gap-4">
						<div class="relative">
							<input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
							<i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
						</div>
						<div class="relative">
							<i data-lucide="bell" class="w-6 h-6 text-gray-600"></i>
							<span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">0</span>
						</div>
						<div class="text-sm text-gray-500">
							Last updated: <?php echo date('m/d/Y, g:i:s A'); ?>
						</div>
					</div>
				</div>
			</header>
			
			<!-- Main Content Area -->
			<main class="flex-1 overflow-y-auto">
				<div class="p-6">
	<?php else: ?>
	<main class="max-w-7xl mx-auto p-4">
	<?php endif; ?>


