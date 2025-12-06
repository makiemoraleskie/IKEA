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
$activeTheme = 'light';

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
	<style>
		@media (min-width: 768px) and (max-width: 1023px) {
			#sidebar.sidebar-tablet-hidden {
				transform: translateX(-100%) !important;
			}
			#sidebar.sidebar-tablet-visible {
				transform: translateX(0) !important;
			}
		}
	</style>
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
			class="theme-sidebar fixed inset-y-0 z-40 flex w-56 md:w-64 lg:w-72 flex-col shadow-lg transition-transform duration-300 -translate-x-full md:relative md:flex-shrink-0 md:translate-x-0 md:shadow-none sidebar-tablet-visible">
			<!-- Logo -->
			<div class="p-4 md:p-5 lg:p-6 flex items-center justify-between">
				<div class="flex items-center gap-3 md:gap-4">
					<img src="<?php echo htmlspecialchars($logoOverride ?: $defaultLogo); ?>" alt="<?php echo htmlspecialchars($companyName); ?> logo" class="w-8 h-8 md:w-10 md:h-10 object-cover rounded-xl shadow-sm bg-white">
					<div class="flex flex-col">
						<span class="text-xs md:text-sm font-semibold text-gray-900">IKEA</span>
						<span class="text-[10px] md:text-xs text-gray-600">Commissary</span>
					</div>
				</div>
				<div class="flex items-center gap-2">
					<button type="button" id="sidebarToggleTablet" class="hidden md:inline-flex lg:hidden items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition-colors" aria-label="Toggle sidebar">
						<i data-lucide="menu" class="w-4 h-4 md:w-5 md:h-5"></i>
					</button>
					<button type="button" id="sidebarClose" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Close navigation">
						<i data-lucide="x" class="w-4 h-4 md:w-5 md:h-5"></i>
					</button>
				</div>
			</div>
			
			<!-- Navigation -->
			<nav class="mt-8 md:mt-12">
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
					$classes = 'sidebar-link flex items-center gap-2 md:gap-3 px-4 md:px-6 py-2 md:py-3 transition-colors duration-200' . ($isActive ? ' active' : '');
					$isPurchases = $item['url'] === '/purchases';
				?>
					<a href="<?php echo htmlspecialchars($baseUrl . $item['url']); ?>" class="<?php echo $classes; ?>">
						<?php if ($isPurchases): ?>
							<span class="text-base md:text-lg font-bold text-current">₱</span>
						<?php else: ?>
							<i data-lucide="<?php echo $item['icon']; ?>" class="w-4 h-4 md:w-5 md:h-5"></i>
						<?php endif; ?>
						<span class="text-xs md:text-sm font-medium"><?php echo $item['label']; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
		
		<!-- Main Content -->
		<div class="flex-1 flex flex-col transition-all duration-300 min-w-0">
			<!-- Top Header -->
			<header class="bg-white border-b theme-header relative z-10" style="z-index: 10;">
				<div class="mx-auto flex w-full max-w-7xl flex-col gap-3 md:gap-4 px-4 py-3 md:py-4 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8 xl:px-10">
					<div class="flex-1 min-w-0">
						<h1 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 truncate"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
					</div>
					<div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center md:w-auto md:ml-auto">
						<div class="flex items-center justify-between gap-3 w-full sm:w-auto">
							<button id="sidebarToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" aria-label="Toggle navigation">
								<i data-lucide="menu" class="w-5 h-5"></i>
							</button>
							<button id="sidebarShowTablet" class="hidden items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none transition-colors" aria-label="Show sidebar">
								<i data-lucide="menu" class="w-5 h-5"></i>
							</button>
						</div>
						<div class="flex items-center gap-3">
							<!-- Notification Bell -->
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
							
							<!-- User Profile Dropdown -->
							<div class="relative" id="userProfileDropdown">
								<button type="button" id="userProfileButton" class="flex items-center gap-3 px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500/20" style="border-color: #e5e7eb !important;" onmouseover="this.style.borderColor='#e5e7eb'" onmousedown="this.style.borderColor='#e5e7eb'" onmouseup="this.style.borderColor='#e5e7eb'">
									<div class="text-left hidden sm:block">
										<div class="text-sm font-semibold text-green-600"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
										<div class="text-xs text-gray-600"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
									</div>
									<svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
									</svg>
								</button>
								<div id="userProfileMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
									<div class="px-4 py-3 border-b border-gray-100">
										<div class="flex items-center gap-3">
											<div>
												<div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
												<div class="text-xs text-gray-600"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
											</div>
										</div>
									</div>
									<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/logout" class="border-t border-gray-100">
										<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
										<button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
											</svg>
											<span>Logout</span>
										</button>
									</form>
								</div>
							</div>
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
				const toggleTablet = document.getElementById('sidebarToggleTablet');
				const showTablet = document.getElementById('sidebarShowTablet');
				const closeBtn = document.getElementById('sidebarClose');
				if (!sidebar || !toggle) return;
				
				const openSidebar = ()=>{
					sidebar.classList.remove('-translate-x-full');
				};
				const closeSidebar = ()=>{
					sidebar.classList.add('-translate-x-full');
				};
				const toggleSidebarTablet = ()=>{
					const isVisible = sidebar.classList.contains('sidebar-tablet-visible');
					if (isVisible) {
						sidebar.classList.remove('sidebar-tablet-visible');
						sidebar.classList.add('sidebar-tablet-hidden');
					} else {
						sidebar.classList.add('sidebar-tablet-visible');
						sidebar.classList.remove('sidebar-tablet-hidden');
					}
					updateShowButtonVisibility();
				};
				
				const updateShowButtonVisibility = ()=>{
					if (!showTablet) return;
					const isHidden = sidebar.classList.contains('sidebar-tablet-hidden');
					// Check if we're on tablet size (768px to 1023px)
					const isTabletSize = window.innerWidth >= 768 && window.innerWidth < 1024;
					if (isHidden && isTabletSize) {
						showTablet.classList.remove('hidden');
						showTablet.classList.add('inline-flex');
						// Ensure Lucide icons are initialized
						if (typeof lucide !== 'undefined') {
							lucide.createIcons();
						}
					} else {
						showTablet.classList.add('hidden');
						showTablet.classList.remove('inline-flex');
					}
				};
				
				toggle.addEventListener('click', (e)=>{
					e.stopPropagation();
					const isOpen = !sidebar.classList.contains('-translate-x-full');
					if (isOpen){ closeSidebar(); } else { openSidebar(); }
				});
				
				toggleTablet?.addEventListener('click', (e)=>{
					e.stopPropagation();
					toggleSidebarTablet();
				});
				
				showTablet?.addEventListener('click', (e)=>{
					e.stopPropagation();
					toggleSidebarTablet();
				});
				
				closeBtn?.addEventListener('click', closeSidebar);
				
				// Update button visibility on load and window resize
				updateShowButtonVisibility();
				window.addEventListener('resize', updateShowButtonVisibility);
				
				document.addEventListener('click', (e)=>{
					if (!sidebar.contains(e.target) && !toggle.contains(e.target) && !sidebar.classList.contains('-translate-x-full')){
						closeSidebar();
					}
				});
			})();
			(function(){
				const userBtn = document.getElementById('userProfileButton');
				const userMenu = document.getElementById('userProfileMenu');
				if (!userBtn || !userMenu) return;
				
				userBtn.addEventListener('click', (e) => {
					e.stopPropagation();
					const isOpen = !userMenu.classList.contains('hidden');
					if (isOpen) {
						userMenu.classList.add('hidden');
						userBtn.setAttribute('aria-expanded', 'false');
					} else {
						userMenu.classList.remove('hidden');
						userBtn.setAttribute('aria-expanded', 'true');
					}
				});
				
				document.addEventListener('click', (e) => {
					if (!userMenu.contains(e.target) && !userBtn.contains(e.target)) {
						userMenu.classList.add('hidden');
						userBtn.setAttribute('aria-expanded', 'false');
					}
				});
			})();
			</script>
			
			<!-- Session Expiration Modal -->
			<div id="sessionExpiredModal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; margin: 0 !important; padding: 1rem !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important; z-index: 99999 !important; background: rgba(0, 0, 0, 0.6) !important;">
				<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
					<div class="flex items-center gap-4">
						<div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
							<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
						</div>
						<div class="flex-1">
							<h3 class="text-lg font-semibold text-gray-900" id="sessionModalTitle">Session Expired</h3>
							<p class="text-sm text-gray-600 mt-1" id="sessionModalMessage">Your session has expired. Please sign in again.</p>
						</div>
					</div>
					<div class="flex justify-end gap-3 pt-4 border-t">
						<a href="<?php echo htmlspecialchars($baseUrl); ?>/login" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
							<i data-lucide="log-in" class="w-4 h-4"></i>
							Go to Login
						</a>
					</div>
				</div>
			</div>
			
			<script>
			// Session Monitoring
			(function() {
				if (!document.getElementById('sessionExpiredModal')) return;
				
				const baseUrl = '<?php echo htmlspecialchars($baseUrl); ?>';
				const modal = document.getElementById('sessionExpiredModal');
				const modalTitle = document.getElementById('sessionModalTitle');
				const modalMessage = document.getElementById('sessionModalMessage');
				let checkInterval = null;
				let isChecking = false;
				let redirectTimer = null;
				
				function showSessionModal(reason, message) {
					if (!modal || modal.classList.contains('flex')) return; // Already showing
					
					// Set modal content
					if (reason === 'disabled') {
						modalTitle.textContent = 'Account Disabled';
						modalMessage.textContent = message || 'Your account has been disabled. Please contact an administrator.';
					} else if (reason === 'expired') {
						modalTitle.textContent = 'Session Expired';
						modalMessage.textContent = message || 'Your session has expired. Please sign in again.';
					} else {
						modalTitle.textContent = 'Session Error';
						modalMessage.textContent = message || 'Your session is no longer valid. Please sign in again.';
					}
					
					// Show modal
					modal.classList.remove('hidden');
					modal.classList.add('flex');
					document.body.classList.add('overflow-hidden');
					
					// Initialize icons
					if (typeof lucide !== 'undefined') {
						lucide.createIcons();
					}
					
					// Auto-redirect after 5 seconds
					redirectTimer = setTimeout(() => {
						window.location.href = baseUrl + '/login?status=' + encodeURIComponent(reason);
					}, 5000);
				}
				
				function checkSession() {
					if (isChecking) return;
					isChecking = true;
					
					fetch(baseUrl + '/auth/check-session', {
						method: 'GET',
						credentials: 'same-origin',
						headers: {
							'X-Requested-With': 'XMLHttpRequest'
						}
					})
					.then(response => {
						if (!response.ok) {
							throw new Error('Network response was not ok');
						}
						return response.json();
					})
					.then(data => {
						if (!data.authenticated) {
							// Stop checking
							if (checkInterval) {
								clearInterval(checkInterval);
								checkInterval = null;
							}
							
							// Show modal
							showSessionModal(data.reason || 'expired', data.message);
						}
					})
					.catch(error => {
						// Network errors or other issues - don't show modal for these
						// Only show if it's a 401/403 which might indicate auth issues
						console.warn('Session check failed:', error);
					})
					.finally(() => {
						isChecking = false;
					});
				}
				
				// Start checking session every 30 seconds
				checkInterval = setInterval(checkSession, 30000);
				
				// Also check on page visibility change (when user comes back to tab)
				document.addEventListener('visibilitychange', () => {
					if (!document.hidden && !isChecking) {
						checkSession();
					}
				});
				
				// Check on focus
				window.addEventListener('focus', () => {
					if (!isChecking) {
						checkSession();
					}
				});
				
				// Initial check after 5 seconds (give page time to load)
				setTimeout(checkSession, 5000);
			})();
			</script>
			
			<!-- Main Content Area -->
			<main class="flex-1 overflow-y-auto min-w-0" style="background-color: #f9fafb;">
				<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-8 min-w-0">
	<?php else: ?>
	<main class="max-w-7xl mx-auto p-4">
	<?php endif; ?>


