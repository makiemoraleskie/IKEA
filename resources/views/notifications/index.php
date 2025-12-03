<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6 md:mb-8">
	<div>
		<h1 class="text-2xl md:text-3xl font-bold text-gray-900">Notifications</h1>
		<p class="text-sm md:text-base text-gray-600 mt-1">Review all recent updates and alerts</p>
	</div>
	<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
		<i data-lucide="arrow-left" class="w-4 h-4"></i>
		Back to Dashboard
	</a>
</div>


<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
	<div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
		<div>
			<h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="bell" class="w-5 h-5 text-indigo-600"></i>
				Activity Feed
			</h2>
			<p class="text-sm text-gray-600 mt-1">Latest 50 notifications for your account.</p>
		</div>
		<div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
			<span><?php echo count($notifications ?? []); ?> entries</span>
			<form method="post" class="flex items-center gap-2">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<button type="submit" name="action" value="mark" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Mark all read</button>
				<button type="submit" name="action" value="clear" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50">Clear all</button>
			</form>
		</div>
	</div>
	<div class="divide-y divide-gray-100">
		<?php if (!empty($notifications)): ?>
			<?php foreach ($notifications as $note):
				$level = strtolower(trim((string)($note['level'] ?? 'info')));
				$title = trim((string)($note['title'] ?? ''));
				$body = trim((string)($note['body'] ?? ''));
				if ($body === '' && isset($note['message'])) {
					$body = trim((string)$note['message']);
				}
				if ($body === '' && isset($note['description'])) {
					$body = trim((string)$note['description']);
				}
				$createdAt = !empty($note['created_at']) ? (string)$note['created_at'] : null;
				$linkTarget = trim((string)($note['link'] ?? ''));
				$typeLabel = $note['type'] ?? '';
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
				$icon = $note['icon'] ?? ($iconMap[$level] ?? $iconMap['info']);
				$accent = $note['accent'] ?? ($accentMap[$level] ?? $accentMap['info']);
				if ($title === '') {
					$title = ucfirst($level);
				}
			?>
			<div class="flex items-start gap-4 px-6 py-4">
				<span class="inline-flex items-center justify-center w-10 h-10 rounded-full <?php echo htmlspecialchars($accent); ?>">
					<i data-lucide="<?php echo htmlspecialchars($icon); ?>" class="w-4 h-4"></i>
				</span>
				<div class="flex-1">
					<div class="flex items-center gap-3">
						<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($title); ?></p>
						<?php if ($typeLabel !== ''): ?>
							<span class="inline-flex items-center text-[10px] uppercase tracking-wide font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500"><?php echo htmlspecialchars(strtoupper($typeLabel)); ?></span>
						<?php endif; ?>
					</div>
					<p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($body !== '' ? $body : 'No additional details available.'); ?></p>
					<p class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars($createdAt ? date('M j, Y g:i A', strtotime($createdAt)) : date('M j, Y g:i A')); ?></p>
					<?php if ($linkTarget !== ''): ?>
					<a href="<?php echo htmlspecialchars($linkTarget); ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 mt-2">
						Open related page
						<i data-lucide="arrow-up-right" class="w-3 h-3"></i>
					</a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		<?php else: ?>
		<div class="px-6 py-12 text-center text-gray-500 flex flex-col items-center gap-2">
			<i data-lucide="sparkles" class="w-10 h-10 text-green-500"></i>
			<p class="text-sm">No notifications to show.</p>
			<p class="text-xs text-gray-400">Check back later for updates.</p>
		</div>
		<?php endif; ?>
	</div>
</div>

