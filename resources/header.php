<?php
declare(strict_types=1);
$companyName = Settings::companyName();
$companyTagline = Settings::companyTagline();
$appTitle = $companyName . ' Console';
$user = Auth::user();
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$notifications = [];
$notificationCount = 0;
$logoOverride = Settings::logoPath();
$defaultLogo = BASE_URL . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg';
$activeTheme = $_SESSION['user_theme'] ?? Settings::themeDefault();

if ($user) {
	$feedBuilder = new NotificationFeed();
	$notifications = $feedBuilder->compose($user, (int)($user['id'] ?? 0), $baseUrl, 8, true);
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
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/public/css/theme.css">
	<script>
		// Tailwind config placeholder if needed
	</script>
	<script>
		(function(){
			var storedTheme = '<?php echo $activeTheme; ?>';
			if (storedTheme !== 'system') {
				return;
			}
			var mediaQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
			var apply = function(isDark) {
				if (document.body) {
					document.body.setAttribute('data-theme', isDark ? 'dark' : 'light');
				} else {
					document.documentElement.dataset.pendingTheme = isDark ? 'dark' : 'light';
				}
			};
			var isDark = mediaQuery ? mediaQuery.matches : false;
			apply(isDark);
			if (mediaQuery && mediaQuery.addEventListener) {
				mediaQuery.addEventListener('change', function(e){ apply(e.matches); });
			} else if (mediaQuery && mediaQuery.addListener) {
				mediaQuery.addListener(function(e){ apply(e.matches); });
			}
			window.addEventListener('DOMContentLoaded', function(){
				if (document.documentElement.dataset.pendingTheme) {
					document.body.setAttribute('data-theme', document.documentElement.dataset.pendingTheme);
				}
			});
		})();
	</script>
</head>
<body class="min-h-screen theme-body text-gray-800 antialiased" data-theme="<?php echo htmlspecialchars($activeTheme); ?>">
	<?php if ($user): ?>
	<div class="min-h-screen md:flex theme-shell">
		<!-- Sidebar -->
		<div
			id="sidebar"
			class="theme-sidebar fixed inset-y-0 z-40 flex w-64 lg:w-72 flex-col shadow-lg transition-transform duration-300 -translate-x-full md:relative md:flex-shrink-0 md:translate-x-0 md:shadow-none">
			<!-- Logo -->
			<div class="p-6 border-b flex items-center justify-between">
				<div class="flex items-center gap-3">
					<img src="<?php echo htmlspecialchars($logoOverride ?: $defaultLogo); ?>" alt="<?php echo htmlspecialchars($companyName); ?> logo" class="w-10 h-10 object-cover rounded-xl border border-white/60 shadow-sm bg-white">
				</div>
				<button type="button" id="sidebarClose" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Close navigation">
					<i data-lucide="x" class="w-5 h-5"></i>
				</button>
			</div>
			
			<!-- Navigation -->
			<nav class="mt-6">
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
					if (in_array($role, ['Owner','Manager','Stock Handler'], true)) {
						$navItems[] = ['url' => '/reports', 'label' => 'Reports', 'icon' => 'trending-up'];
					}
					if (in_array($role, ['Owner','Manager'], true)) {
						$navItems[] = ['url' => '/audit', 'label' => 'Audit Logs', 'icon' => 'clock'];
						$navItems[] = ['url' => '/admin/settings', 'label' => 'Admin Settings', 'icon' => 'settings'];
					}
				}
				
				foreach ($navItems as $item): 
					$isActive = strpos($currentPage, $item['url']) !== false;
					$classes = 'sidebar-link flex items-center gap-3 px-6 py-3 transition-colors duration-200' . ($isActive ? ' active' : '');
				?>
					<a href="<?php echo htmlspecialchars($baseUrl . $item['url']); ?>" class="<?php echo $classes; ?>">
						<i data-lucide="<?php echo $item['icon']; ?>" class="w-5 h-5"></i>
						<span class="text-sm font-medium"><?php echo $item['label']; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
			
			<!-- User Info -->
			<div class="mt-auto p-6 border-t sidebar-user">
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
		<div class="flex-1 flex flex-col transition-all duration-300">
			<!-- Top Header -->
			<header class="bg-white border-b theme-header">
				<div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 xl:px-10">
					<div class="flex-1 min-w-0">
						<?php
						$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
						$breadcrumbs = [];
						$breadcrumbs[] = ['label' => 'Dashboard', 'url' => $baseUrl . '/dashboard'];
						
						if (strpos($currentPath, '/inventory') !== false) {
							$breadcrumbs[] = ['label' => 'Inventory', 'url' => $baseUrl . '/inventory'];
							if (strpos($currentPath, '/import') !== false) {
								$breadcrumbs[] = ['label' => 'Import', 'url' => ''];
							}
						} elseif (strpos($currentPath, '/requests') !== false) {
							$breadcrumbs[] = ['label' => 'Requests', 'url' => $baseUrl . '/requests'];
						} elseif (strpos($currentPath, '/purchases') !== false) {
							$breadcrumbs[] = ['label' => 'Purchases', 'url' => $baseUrl . '/purchases'];
						} elseif (strpos($currentPath, '/deliveries') !== false) {
							$breadcrumbs[] = ['label' => 'Deliveries', 'url' => $baseUrl . '/deliveries'];
						} elseif (strpos($currentPath, '/reports') !== false) {
							$breadcrumbs[] = ['label' => 'Reports', 'url' => $baseUrl . '/reports'];
						} elseif (strpos($currentPath, '/audit') !== false) {
							$breadcrumbs[] = ['label' => 'Audit Logs', 'url' => $baseUrl . '/audit'];
						} elseif (strpos($currentPath, '/admin/settings') !== false) {
							$breadcrumbs[] = ['label' => 'Admin Settings', 'url' => $baseUrl . '/admin/settings'];
						} elseif (strpos($currentPath, '/users') !== false) {
							$breadcrumbs[] = ['label' => 'Users', 'url' => $baseUrl . '/users'];
						} elseif (strpos($currentPath, '/notifications') !== false) {
							$breadcrumbs[] = ['label' => 'Notifications', 'url' => $baseUrl . '/notifications'];
						} elseif (strpos($currentPath, '/account') !== false) {
							$breadcrumbs[] = ['label' => 'Account', 'url' => $baseUrl . '/account/security'];
						}
						?>
						<nav class="flex items-center gap-2 text-sm text-gray-600 mb-1" aria-label="Breadcrumb">
							<?php foreach ($breadcrumbs as $index => $crumb): ?>
								<?php if ($index > 0): ?>
									<i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
								<?php endif; ?>
								<?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
									<a href="<?php echo htmlspecialchars($crumb['url']); ?>" class="hover:text-gray-900 transition-colors">
										<?php echo htmlspecialchars($crumb['label']); ?>
									</a>
								<?php else: ?>
									<span class="text-gray-900 font-medium"><?php echo htmlspecialchars($crumb['label']); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</nav>
						<h1 class="text-2xl font-bold text-gray-800 truncate"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
					</div>
					<div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
						<div class="flex items-center justify-between gap-3 w-full sm:w-auto">
							<button id="sidebarToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" aria-label="Toggle navigation">
								<i data-lucide="menu" class="w-5 h-5"></i>
							</button>
							<div class="relative flex-1">
								<input type="text" id="globalSearchInput" placeholder="Search ingredients, requests, purchases..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
								<i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
							</div>
						</div>
						<button type="button" id="themeSwitcher" data-theme="<?php echo htmlspecialchars($activeTheme); ?>" data-csrf="<?php echo htmlspecialchars(Csrf::token()); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" aria-label="Toggle theme">
							<i data-lucide="<?php echo $activeTheme === 'dark' ? 'moon' : 'sun'; ?>" class="w-5 h-5"></i>
						</button>
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
														<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($note['title'] ?? 'Notification'); ?></p>
														<p class="text-xs text-gray-600 mt-1">
															<?php
															$bodyText = trim((string)($note['body'] ?? ''));
															if ($bodyText === '' && isset($note['description'])) {
																$bodyText = trim((string)$note['description']);
															}
															if ($bodyText === '' && isset($note['message'])) {
																$bodyText = trim((string)$note['message']);
															}
															echo htmlspecialchars($bodyText !== '' ? $bodyText : 'No additional details available.');
															?>
														</p>
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
				const openSidebar = ()=>{
					sidebar.classList.remove('-translate-x-full');
				};
				const closeSidebar = ()=>{
					sidebar.classList.add('-translate-x-full');
				};
				toggle.addEventListener('click', (e)=>{
					e.stopPropagation();
					const isOpen = !sidebar.classList.contains('-translate-x-full');
					if (isOpen){ closeSidebar(); } else { openSidebar(); }
				});
				closeBtn?.addEventListener('click', closeSidebar);
				document.addEventListener('click', (e)=>{
					if (!sidebar.contains(e.target) && !toggle.contains(e.target) && !sidebar.classList.contains('-translate-x-full')){
						closeSidebar();
					}
				});
			})();
			(function(){
				const btn = document.getElementById('themeSwitcher');
				if (!btn) return;
				const themes = ['light','dark','system'];
				const updateIcon = (theme) => {
					const icon = btn.querySelector('i');
					if (icon) {
						icon.setAttribute('data-lucide', theme === 'dark' ? 'moon' : 'sun');
						if (window.lucide) {
							window.lucide.createIcons();
						}
					}
				};
				btn.addEventListener('click', ()=>{
					const current = btn.getAttribute('data-theme') || 'system';
					const index = themes.indexOf(current);
					const nextTheme = themes[(index + 1) % themes.length];
					btn.setAttribute('data-theme', nextTheme);
					document.body.setAttribute('data-theme', nextTheme);
					updateIcon(nextTheme);

					const formData = new FormData();
					formData.append('csrf_token', btn.getAttribute('data-csrf') || '');
					formData.append('theme', nextTheme);
					fetch('<?php echo htmlspecialchars($baseUrl); ?>/account/theme', {
						method: 'POST',
						body: formData,
						credentials: 'same-origin'
					}).catch(()=>{ /* ignore network errors for toggle */ });
				});
			})();
			</script>
			
			<!-- Main Content Area -->
			<main class="flex-1 overflow-y-auto bg-transparent">
				<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-8">
	<?php else: ?>
	<main class="max-w-7xl mx-auto p-4">
	<?php endif; ?>


