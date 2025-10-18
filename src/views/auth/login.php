<div class="max-w-md mx-auto mt-16 bg-white border rounded-lg shadow-sm p-6">
	<h1 class="text-xl font-semibold mb-4">Login</h1>
	<?php if (!empty($error)): ?>
		<div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></div>
	<?php endif; ?>
	<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/login" class="space-y-4" novalidate>
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div>
			<label class="block text-sm mb-1" for="email">Email</label>
			<input class="w-full border rounded px-3 py-2" id="email" name="email" type="email" required />
		</div>
		<div>
			<label class="block text-sm mb-1" for="password">Password</label>
			<input class="w-full border rounded px-3 py-2" id="password" name="password" type="password" required />
		</div>
		<button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Sign in</button>
	</form>
</div>


