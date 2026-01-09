<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$user = $user ?? Auth::user();
?>
<div class="max-w-4xl mx-auto space-y-8">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div>
			<h1 class="text-2xl md:text-3xl font-bold text-gray-900">Account Security</h1>
			<p class="text-sm md:text-base text-gray-600 mt-2">Manage your password and personalization settings.</p>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
	</div>

	<!-- Flash Messages -->
	<?php if (!empty($flash)): ?>
		<div class="mb-4 md:mb-6 rounded-xl px-4 py-3 text-sm <?php echo $flash['type'] === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
			<div class="font-semibold mb-1 flex items-center gap-2">
				<i data-lucide="<?php echo $flash['type'] === 'success' ? 'check-circle' : 'alert-triangle'; ?>" class="w-4 h-4"></i>
				<span><?php echo $flash['type'] === 'success' ? 'Success' : 'Error'; ?></span>
			</div>
			<p><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
		</div>
		<script>
			// Initialize lucide icons for flash message
			if (typeof lucide !== 'undefined') {
				lucide.createIcons();
			}
		</script>
	<?php endif; ?>

	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-8">
		<div>
			<h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
				Change Password
			</h2>
			<p class="text-sm text-gray-600 mt-1">Use a unique passphrase with at least 8 characters.</p>
		</div>
		<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/account/security/password" class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
			<div class="space-y-2">
				<label class="text-sm font-medium text-gray-700">Current Password</label>
				<input type="password" name="current_password" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
			</div>
			<div class="space-y-2">
				<label class="text-sm font-medium text-gray-700">New Password</label>
				<input type="password" name="new_password" minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
			</div>
			<div class="space-y-2 md:col-span-2">
				<label class="text-sm font-medium text-gray-700">Confirm New Password</label>
				<input type="password" name="confirm_password" minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
			</div>
			<div class="md:col-span-2 flex flex-wrap gap-3">
				<button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-3 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
					<i data-lucide="save" class="w-4 h-4"></i>
					Save Password
				</button>
				<p class="text-xs text-gray-500">You’ll be signed out everywhere after updating.</p>
			</div>
		</form>
	</div>
</div>

<script>
	// Initialize Lucide icons when page loads
	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}
</script>

