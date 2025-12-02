<?php
declare(strict_types=1);
// Fallback for Settings class if not defined
if (!class_exists('Settings')) {
	class Settings {
		public static function companyName(): string { return 'IKEA'; }
		public static function companyTagline(): string { return 'Inventory Management'; }
		public static function logoPath(): ?string { return null; }
		public static function themeDefault(): string { return 'light'; }
	}
}
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
	// Fallback for NotificationFeed class if not defined
	if (!class_exists('NotificationFeed')) {
		class NotificationFeed {
			public function compose(array $user, int $userId, string $baseUrl, int $limit, bool $unreadOnly): array {
				return [];
			}
		}
	}
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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
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
<body class="min-h-screen theme-body text-gray-800 antialiased overflow-x-hidden" data-theme="<?php echo htmlspecialchars($activeTheme); ?>">
	<?php if ($user): ?>
	<div class="min-h-screen md:flex theme-shell md:h-screen md:overflow-hidden">
		<!-- Sidebar Backdrop (Mobile Only) -->
		<div id="sidebarBackdrop" class="fixed inset-0 bg-black/60 z-[35] md:hidden hidden opacity-0 transition-opacity duration-300 pointer-events-none"></div>
		
		<!-- Sidebar - Enhanced -->
		<div
			id="sidebar"
			class="theme-sidebar fixed inset-y-0 z-40 flex w-64 lg:w-72 flex-col shadow-lg transition-transform duration-300 -translate-x-full md:relative md:flex-shrink-0 md:translate-x-0 md:shadow-none">
			<!-- Logo -->
			<div class="p-6  flex items-center justify-between">
				<div class="flex items-center gap-3">
					<img src="<?php echo htmlspecialchars(BASE_URL . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg'); ?>" alt="IKEA logo" class="w-10 h-10 object-cover rounded-xl border border-white/60 shadow-sm">
					<div class="flex flex-col">
						<span class="text-lg font-bold text-gray-900 italic">ikea</span>
						<span class="text-xs text-gray-600">commissary</span>
					</div>
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
					$classes = 'sidebar-link flex items-center gap-3 px-6 py-3 transition-colors duration-200' . ($isActive ? ' active' : '');
				?>
					<a href="<?php echo htmlspecialchars($baseUrl . $item['url']); ?>" class="<?php echo $classes; ?>">
						<i data-lucide="<?php echo $item['icon']; ?>" class="w-5 h-5"></i>
						<span class="text-sm font-medium"><?php echo $item['label']; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
		
		<!-- Main Content -->
		<div class="flex-1 flex flex-col md:h-full md:overflow-hidden w-full md:w-auto min-w-0 relative z-0">
			<!-- Top Header -->
			<header class="bg-white border-b theme-header z-20" style="position: relative !important;">
				<div class="mx-auto flex w-full max-w-7xl flex-col gap-3 sm:gap-4 px-4 py-3 sm:py-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 xl:px-10">
					<div class="flex items-center gap-4">
						<button id="sidebarToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" aria-label="Toggle navigation">
							<i data-lucide="menu" class="w-5 h-5"></i>
						</button>
						<div class="flex items-center gap-3">
							<h1 class="text-2xl font-bold text-gray-800 truncate"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
						</div>
					</div>
					<div class="flex items-center gap-3">
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
							<button type="button" id="userProfileButton" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 focus:outline-none transition-colors">
								<div class="flex items-center gap-2">
									<i data-lucide="user" class="w-5 h-5 text-gray-600"></i>
									<div class="text-left hidden sm:block">
										<div class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
										<div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
									</div>
								</div>
								<i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
							</button>
							<div id="userProfileMenu" class="hidden absolute right-0 mt-2 w-48 border border-gray-200 rounded-xl shadow-xl z-50">
								<div class="px-4 py-3 border-b border-gray-200 sm:hidden">
									<div class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
									<div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['role'] ?? 'User'); ?></div>
								</div>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/logout" class="p-2">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:text-red-700 transition-colors">
										Logout
									</button>
								</form>
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
			(function(){
				const userBtn = document.getElementById('userProfileButton');
				const userMenu = document.getElementById('userProfileMenu');
				if (!userBtn || !userMenu) return;
				
				function closeUserMenu() {
					userMenu.classList.add('hidden');
					userBtn.setAttribute('aria-expanded', 'false');
					const chevron = userBtn.querySelector('i[data-lucide="chevron-down"]');
					if (chevron) {
						chevron.setAttribute('data-lucide', 'chevron-down');
						if (typeof lucide !== 'undefined') {
							lucide.createIcons();
						}
					}
				}
				
				userBtn.addEventListener('click', (event) => {
					event.stopPropagation();
					const isOpen = userBtn.getAttribute('aria-expanded') === 'true';
					if (isOpen) {
						closeUserMenu();
					} else {
						userMenu.classList.remove('hidden');
						userBtn.setAttribute('aria-expanded', 'true');
						const chevron = userBtn.querySelector('i[data-lucide="chevron-down"]');
						if (chevron) {
							chevron.setAttribute('data-lucide', 'chevron-up');
							if (typeof lucide !== 'undefined') {
								lucide.createIcons();
							}
						}
					}
				});
				
				document.addEventListener('click', (event) => {
					if (!userMenu.contains(event.target) && !userBtn.contains(event.target)) {
						closeUserMenu();
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
			<main class="flex-1 overflow-y-auto relative bg-gray-100">
				<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 pt-2 sm:pt-4 md:pt-6 pb-6 space-y-4 sm:space-y-6 md:space-y-8 relative z-10">
	<?php else: ?>
	<main class="max-w-7xl mx-auto p-4">
	<?php endif; ?>


