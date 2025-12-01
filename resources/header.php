<?php
declare(strict_types=1);
$appTitle = 'IKEA Commissary System';
$user = Auth::user();
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$notifications = [];
$notificationCount = 0;

if ($user) {
	$ingredientModel = new Ingredient();
	$ingredients = $ingredientModel->all();
	$lowStockItems = array_values(array_filter($ingredients, function ($ing) {
		return (float)($ing['quantity'] ?? 0) <= (float)($ing['reorder_level'] ?? 0);
	}));
	if (!empty($lowStockItems)) {
		$names = array_map(fn($i) => $i['name'], array_slice($lowStockItems, 0, 3));
		$notifications[] = [
			'icon' => 'alert-triangle',
			'title' => 'Low stock alert',
			'description' => implode(', ', $names) . (count($lowStockItems) > 3 ? ' +' . (count($lowStockItems) - 3) . ' more' : '') . ' need replenishment.',
			'link' => $baseUrl . '/inventory?focus=low-stock#inventory-low-stock',
			'accent' => 'text-red-700 bg-red-50',
		];
	}

	$requestModel = new RequestModel();
	$pendingBatchCount = $requestModel->countBatchesByStatus('Pending');
	if ($pendingBatchCount > 0) {
		$notifications[] = [
			'icon' => 'clipboard-list',
			'title' => 'Requests awaiting approval',
			'description' => $pendingBatchCount . ' batch' . ($pendingBatchCount > 1 ? 'es' : '') . ' need review.',
			'link' => $baseUrl . '/requests?status=pending#requests-history',
			'accent' => 'text-amber-700 bg-amber-50',
		];
	}

	$purchaseModel = new Purchase();
	$pendingPayments = $purchaseModel->countByPaymentStatus('Pending');
	if ($pendingPayments > 0) {
		$notifications[] = [
			'icon' => 'credit-card',
			'title' => 'Pending payments',
			'description' => $pendingPayments . ' purchase' . ($pendingPayments > 1 ? 's' : '') . ' await settlement.',
			'link' => $baseUrl . '/purchases?payment=Pending#recent-purchases',
			'accent' => 'text-rose-700 bg-rose-50',
		];
	}

	$deliveryModel = new Delivery();
	$partialDeliveries = $deliveryModel->countDeliveriesByStatus('Partial');
	if ($partialDeliveries > 0) {
		$notifications[] = [
			'icon' => 'truck',
			'title' => 'Partial deliveries',
			'description' => $partialDeliveries . ' delivery' . ($partialDeliveries > 1 ? 'ies' : 'y') . ' still have remaining items.',
			'link' => $baseUrl . '/deliveries?status=partial#recent-deliveries',
			'accent' => 'text-purple-700 bg-purple-50',
		];
	}

	$awaitingDeliveries = $deliveryModel->getPendingCount();
	if ($awaitingDeliveries > 0) {
		$notifications[] = [
			'icon' => 'package',
			'title' => 'Awaiting deliveries',
			'description' => $awaitingDeliveries . ' batch' . ($awaitingDeliveries > 1 ? 'es' : '') . ' have not arrived.',
			'link' => $baseUrl . '/deliveries?status=awaiting#awaiting-deliveries',
			'accent' => 'text-blue-700 bg-blue-50',
		];
	}

	// Personal notifications
	$notificationModel = new Notification();
	$userNotifications = $notificationModel->listLatest((int)($user['id'] ?? 0), 8);
	foreach ($userNotifications as $note) {
		$accentMap = [
			'success' => 'text-green-700 bg-green-50 border border-green-200',
			'warning' => 'text-amber-700 bg-amber-50 border border-amber-200',
			'danger' => 'text-red-700 bg-red-50 border border-red-200',
			'info' => 'text-indigo-700 bg-indigo-50 border border-indigo-200',
		];
		$iconMap = [
			'success' => 'check-circle',
			'warning' => 'alert-triangle',
			'danger' => 'alert-octagon',
			'info' => 'bell-ring',
		];
		$level = $note['level'] ?? 'info';
		$notifications[] = [
			'icon' => $iconMap[$level] ?? 'bell-ring',
			'title' => ucfirst($level),
			'description' => trim((string)($note['message'] ?? '')),
			'link' => !empty($note['link']) ? (str_starts_with($note['link'], 'http') ? $note['link'] : $baseUrl . $note['link']) : '',
			'accent' => $accentMap[$level] ?? $accentMap['info'],
			'created_at' => $note['created_at'] ?? null,
		];
	}

	$notificationCount = count($notifications);
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($appTitle); ?></title>
	<link rel="icon" href="<?php echo htmlspecialchars($baseUrl); ?>/public/favicon.ico">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/public/css/theme.css">
	<script>
		// Tailwind config placeholder if needed
	</script>
</head>
<body class="min-h-screen theme-body text-gray-800 antialiased overflow-x-hidden">
	<?php if ($user): ?>
	<div class="min-h-screen md:flex theme-shell md:h-screen md:overflow-hidden">
		<!-- Sidebar Backdrop (Mobile Only) -->
		<div id="sidebarBackdrop" class="fixed inset-0 bg-black/60 z-[35] md:hidden hidden opacity-0 transition-opacity duration-300 pointer-events-none"></div>
		
		<!-- Sidebar - Enhanced -->
		<div
			id="sidebar"
			class="theme-sidebar fixed top-0 left-0 bottom-0 z-50 flex w-64 lg:w-72 flex-col bg-gradient-to-b from-white via-gray-50/30 to-white border-r-2 border-gray-200/60 shadow-xl transition-transform duration-300 ease-in-out transform -translate-x-full md:relative md:flex-shrink-0 md:translate-x-0 md:shadow-none md:z-auto md:transform-none overflow-hidden md:h-full">
			<!-- Decorative background elements -->
			<div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-[#FCBBE9]/5 to-transparent rounded-full blur-3xl -mr-24 -mt-24"></div>
			<div class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-[#A8E6CF]/5 to-transparent rounded-full blur-2xl -ml-20 -mb-20"></div>
			
			<div class="relative z-10 flex flex-col h-full">
				<!-- Logo Section - Enhanced -->
				<div class="p-6 sm:p-8 border-b border-gray-200">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-3">
							<div class="relative">
								<img src="<?php echo htmlspecialchars(BASE_URL . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg'); ?>" alt="IKEA logo" class="w-12 h-12 object-cover rounded-2xl border-2 border-white/80 shadow-lg transition-transform duration-300 hover:scale-110">
							</div>
							<div class="hidden lg:block">
								<p class="text-xs font-normal text-gray-700 uppercase tracking-wider" style="font-family: 'Dancing Script', cursive;">ikea</p>
								<p class="text-xs text-gray-400 font-medium">Commissary</p>
							</div>
						</div>
						<button type="button" id="sidebarClose" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200" aria-label="Close navigation">
							<i data-lucide="x" class="w-5 h-5"></i>
						</button>
					</div>
				</div>
				
				<!-- Navigation - Enhanced -->
				<nav class="px-3 py-4 flex-1 overflow-y-auto">
					<?php 
					$currentPage = $_SERVER['REQUEST_URI'] ?? '';
					$role = $user['role'] ?? '';
					if ($role === 'Kitchen Staff') {
						$navItems = [
							['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'bar-chart-3'],
							['url' => '/requests', 'label' => 'Requests', 'icon' => 'clipboard-list'],
						];
					} elseif ($role === 'Purchaser') {
						$navItems = [
							['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'bar-chart-3'],
							['url' => '/purchases', 'label' => 'Purchases', 'icon' => 'receipt'],
						];
					} else {
						$navItems = [
							['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'bar-chart-3'],
							['url' => '/requests', 'label' => 'Requests', 'icon' => 'clipboard-list'],
							['url' => '/inventory', 'label' => 'Inventory', 'icon' => 'package'],
							['url' => '/purchases', 'label' => 'Purchases', 'icon' => 'receipt'],
							['url' => '/deliveries', 'label' => 'Deliveries', 'icon' => 'truck'],
						];
						if (in_array($role, ['Owner','Manager'], true)) {
							$navItems[] = ['url' => '/reports', 'label' => 'Reports', 'icon' => 'trending-up'];
							$navItems[] = ['url' => '/audit', 'label' => 'Audit Logs', 'icon' => 'clock'];
						}
						if ($role === 'Owner') {
							$navItems[] = ['url' => '/users', 'label' => 'Users', 'icon' => 'users'];
						}
					}
					
					foreach ($navItems as $item): 
						$isActive = strpos($currentPage, $item['url']) !== false;
						$activeClasses = $isActive 
							? 'bg-gradient-to-r from-[#008000]/10 via-[#008000]/5 to-transparent text-[#008000] font-semibold border-l-4 border-[#008000] shadow-sm' 
							: 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-100/50 hover:via-gray-50/30 hover:to-transparent hover:text-[#008000]';
					?>
						<a href="<?php echo htmlspecialchars($baseUrl . $item['url']); ?>" class="sidebar-link flex items-center gap-3 px-4 py-3.5 mx-2 rounded-xl transition-all duration-200 mb-1 <?php echo $activeClasses; ?>">
							<i data-lucide="<?php echo $item['icon']; ?>" class="w-5 h-5 flex-shrink-0"></i>
							<span class="text-sm font-medium"><?php echo $item['label']; ?></span>
							<?php if ($isActive): ?>
								<div class="ml-auto w-2 h-2 rounded-full bg-[#008000] animate-pulse"></div>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</nav>
				
				<!-- User Info Section - Enhanced -->
				<div class="mt-auto p-5 sm:p-6 border-t border-gray-200">
					<div class="mb-4 flex items-center gap-3">
						<i data-lucide="user" class="w-5 h-5 text-gray-600 flex-shrink-0"></i>
						<div class="flex-1 min-w-0">
							<div class="text-sm font-bold text-gray-800 truncate"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
							<div class="text-xs text-gray-500 font-medium truncate"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
						</div>
					</div>
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/logout">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all duration-200 border border-transparent hover:border-red-200">
							<i data-lucide="log-out" class="w-4 h-4"></i>
							<span>Logout</span>
						</button>
					</form>
				</div>
			</div>
		</div>
		
		<!-- Main Content -->
		<div class="flex-1 flex flex-col md:h-full md:overflow-hidden w-full md:w-auto min-w-0 relative z-0">
			<!-- Top Header -->
			<header class="bg-white border-b theme-header md:sticky md:top-0 z-20">
				<div class="mx-auto flex w-full max-w-7xl flex-col gap-3 sm:gap-4 px-4 py-2 sm:py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 xl:px-10">
					<h1 class="text-2xl font-bold text-gray-800 truncate"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
					<div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
						<div class="flex items-center justify-between gap-3 w-full sm:w-auto">
							<button id="sidebarToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" aria-label="Toggle navigation">
								<i data-lucide="menu" class="w-5 h-5"></i>
							</button>
							<div class="relative flex-1">
								<input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
								<i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
							</div>
						</div>
						<div class="relative" id="notificationWrapper">
							<button type="button" id="notificationButton" aria-haspopup="true" aria-expanded="false" class="relative focus:outline-none rounded-full p-2 border <?php echo $notificationCount ? 'border-red-200 text-red-700 bg-red-50 animate-bounce' : 'border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
								<i data-lucide="bell" class="w-5 h-5"></i>
								<?php if ($notificationCount > 0): ?>
									<span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?php echo $notificationCount; ?></span>
									<span class="absolute -top-1 -right-1 inline-flex h-5 w-5 rounded-full bg-red-500 opacity-40 animate-ping"></span>
								<?php else: ?>
									<span class="absolute -top-1 -right-1 w-4 h-4 bg-gray-300 text-gray-700 text-[10px] rounded-full flex items-center justify-center">0</span>
								<?php endif; ?>
							</button>
							<div id="notificationPanel" class="hidden absolute right-0 mt-3 w-screen max-w-xs sm:max-w-sm max-h-[26rem] overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-xl z-50">
								<div class="px-4 py-3 border-b flex items-center justify-between gap-4">
									<div>
										<p class="text-sm font-semibold text-gray-900">Notifications</p>
										<p class="text-xs text-gray-500"><?php echo $notificationCount > 0 ? 'Stay on top of critical updates' : 'All systems look good'; ?></p>
									</div>
									<button type="button" class="text-xs text-gray-500 hover:text-gray-700" onclick="document.getElementById('notificationPanel').classList.add('hidden'); document.getElementById('notificationButton').setAttribute('aria-expanded','false');">Close</button>
								</div>
								<?php if ($notificationCount > 0): ?>
									<ul class="divide-y divide-gray-100">
										<?php foreach ($notifications as $note): ?>
											<li>
												<a href="<?php echo htmlspecialchars($note['link'] ?: '#'); ?>" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
													<span class="inline-flex items-center justify-center w-10 h-10 rounded-full <?php echo htmlspecialchars($note['accent']); ?>">
														<i data-lucide="<?php echo htmlspecialchars($note['icon']); ?>" class="w-4 h-4"></i>
													</span>
													<div class="flex-1">
														<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($note['title']); ?></p>
														<p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars($note['description'] ?: 'No description provided.'); ?></p>
														<?php if (!empty($note['created_at'])): ?>
															<p class="text-[11px] text-gray-400 mt-1"><?php echo htmlspecialchars(date('M j, g:i A', strtotime($note['created_at']))); ?></p>
														<?php endif; ?>
														<?php if (!empty($note['link'])): ?>
															<span class="text-xs text-blue-600 font-medium mt-2 inline-flex items-center gap-1">Review <i data-lucide="arrow-up-right" class="w-3 h-3"></i></span>
														<?php endif; ?>
													</div>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
									<div class="border-t px-4 py-3 flex items-center justify-between gap-3">
										<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/notifications" class="flex items-center gap-2">
											<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
											<button type="submit" name="action" value="mark" class="text-xs text-gray-600 hover:text-gray-800">Mark all read</button>
											<button type="submit" name="action" value="clear" class="text-xs text-red-600 hover:text-red-700">Clear all</button>
										</form>
										<a href="<?php echo htmlspecialchars($baseUrl); ?>/notifications" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
											View all
											<i data-lucide="arrow-right" class="w-4 h-4"></i>
										</a>
									</div>
								<?php else: ?>
									<div class="px-4 py-6 text-center text-sm text-gray-500">
										<i data-lucide="sparkles" class="w-6 h-6 mx-auto mb-2 text-green-500"></i>
										<p>No new notifications.</p>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="text-sm text-gray-500 text-center sm:text-left">
							Last updated: <?php echo date('m/d/Y, g:i:s A'); ?>
						</div>
					</div>
				</div>
			</header>
			<script>
			(function(){
				const btn = document.getElementById('notificationButton');
				const panel = document.getElementById('notificationPanel');
				const sidebar = document.getElementById('sidebar');
				const sidebarToggle = document.getElementById('sidebarToggle');
				const sidebarClose = document.getElementById('sidebarClose');
				const body = document.body;
				if (!btn || !panel) { return; }
				function closePanel() {
					panel.classList.add('hidden');
					btn.setAttribute('aria-expanded', 'false');
				}
				btn.addEventListener('click', (event) => {
					event.stopPropagation();
					const isOpen = btn.getAttribute('aria-expanded') === 'true';
					if (isOpen) {
						closePanel();
					} else {
						panel.classList.remove('hidden');
						btn.setAttribute('aria-expanded', 'true');
					}
				});
				document.addEventListener('click', (event) => {
					if (!panel.contains(event.target) && !btn.contains(event.target)) {
						closePanel();
					}
				});
			})();
			(function(){
				const sidebar = document.getElementById('sidebar');
				const toggle = document.getElementById('sidebarToggle');
				const closeBtn = document.getElementById('sidebarClose');
				if (!sidebar || !toggle) return;
				const backdrop = document.getElementById('sidebarBackdrop');
				const body = document.body;
				const html = document.documentElement;
				
				const openSidebar = ()=>{
					sidebar.classList.remove('-translate-x-full');
					if (backdrop) {
						backdrop.classList.remove('hidden');
						backdrop.classList.remove('pointer-events-none');
						requestAnimationFrame(() => {
							backdrop.classList.remove('opacity-0');
						});
					}
					// Prevent body scroll when sidebar is open on mobile
					if (window.innerWidth < 768) {
						body.style.overflow = 'hidden';
						html.style.overflow = 'hidden';
					}
				};
				
				const closeSidebar = ()=>{
					sidebar.classList.add('-translate-x-full');
					if (backdrop) {
						backdrop.classList.add('opacity-0');
						backdrop.classList.add('pointer-events-none');
						setTimeout(() => {
							backdrop.classList.add('hidden');
						}, 300);
					}
					// Restore body scroll when sidebar is closed
					body.style.overflow = '';
					html.style.overflow = '';
				};
				
				toggle.addEventListener('click', (e)=>{
					e.stopPropagation();
					const isOpen = !sidebar.classList.contains('-translate-x-full');
					if (isOpen){ closeSidebar(); } else { openSidebar(); }
				});
				
				closeBtn?.addEventListener('click', (e)=>{
					e.stopPropagation();
					closeSidebar();
				});
				
				if (backdrop) {
					backdrop.addEventListener('click', (e)=>{
						e.stopPropagation();
						closeSidebar();
					});
				}
				
				document.addEventListener('click', (e)=>{
					if (!sidebar.contains(e.target) && !toggle.contains(e.target) && !sidebar.classList.contains('-translate-x-full')){
						closeSidebar();
					}
				});
				
				// Close sidebar on window resize if transitioning from mobile to desktop
				window.addEventListener('resize', ()=>{
					if (window.innerWidth >= 768) {
						closeSidebar();
					}
				});
			})();
			</script>
			
			<!-- Main Content Area -->
			<main class="flex-1 overflow-y-auto relative bg-[#E8F5E8]">
				<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 pt-2 sm:pt-4 md:pt-6 pb-6 space-y-4 sm:space-y-6 md:space-y-8 relative z-10">
	<?php else: ?>
	<main class="max-w-7xl mx-auto p-4">
	<?php endif; ?>


