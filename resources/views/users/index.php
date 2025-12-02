<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white rounded-xl shadow-md border-2 border-gray-200/80 p-3 sm:p-4 mb-4 md:mb-6 relative overflow-hidden">
	<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-900 tracking-tight">User Management</h1>
			<p class="text-sm sm:text-base text-gray-600 mt-1 font-medium">Create and manage commissary accounts</p>
		</div>
		<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#008000] bg-[#008000]/10 rounded-xl hover:bg-[#008000]/20 border border-[#008000]/20 transition-colors">
			<i data-lucide="arrow-left" class="w-4 h-4"></i>
			Back to Dashboard
		</a>
	</div>
</div>

<?php if (!empty($flash)): ?>
	<div class="mb-6 rounded-xl px-4 py-3 border flex items-center gap-3 <?php echo ($flash['type'] ?? '') === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-800'; ?>">
		<i data-lucide="<?php echo ($flash['type'] ?? '') === 'error' ? 'alert-octagon' : 'check-circle'; ?>" class="w-5 h-5"></i>
		<p class="text-sm font-medium"><?php echo htmlspecialchars($flash['text'] ?? ''); ?></p>
	</div>
<?php endif; ?>

<div class="bg-white border-2 border-gray-200/80 rounded-xl shadow-sm p-4 sm:p-6 mb-6 sm:mb-8 overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 -mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-6 border-b border-gray-200/60">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
				<i data-lucide="user-plus" class="w-5 h-5 text-[#008000]"></i>
			</div>
			<div>
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Create User</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Invite a teammate with appropriate access level</p>
			</div>
		</div>
	</div>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
			<input name="name" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" required />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
			<input type="email" name="email" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" required />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
			<select name="role" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
				<?php foreach ($roles as $r): ?>
					<option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars($r); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1.5">Password (min 8 chars)</label>
			<input type="password" name="password" minlength="8" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" required />
		</div>
		<div class="md:col-span-4">
			<button class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white px-6 py-3 rounded-xl shadow-md hover:opacity-90 hover:shadow-lg focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold">
				<i data-lucide="user-plus" class="w-4 h-4"></i>
				Create user
			</button>
		</div>
	</form>
</div>

<div class="bg-white border-2 border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
	<div class="bg-gradient-to-r from-[#008000]/10 via-[#00A86B]/5 to-[#008000]/10 px-4 sm:px-6 py-4 border-b border-gray-200/60 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#008000]/20 rounded-xl flex items-center justify-center border border-[#008000]/30">
				<i data-lucide="users" class="w-5 h-5 text-[#008000]"></i>
			</div>
			<div>
				<h2 class="text-xl sm:text-2xl font-bold text-gray-900">Users</h2>
				<p class="text-xs sm:text-sm text-gray-600 mt-0.5">Existing accounts and quick resets</p>
			</div>
		</div>
		<span class="text-xs sm:text-sm text-gray-600 font-semibold"><?php echo count($users); ?> total</span>
	</div>
	<div class="overflow-x-auto">
		<table class="w-full text-sm min-w-[700px]">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Name</th>
					<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Email</th>
					<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Role</th>
					<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Created</th>
					<th class="text-left px-4 sm:px-6 py-3 font-medium text-gray-700">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200">
				<?php foreach ($users as $u): ?>
				<tr class="hover:bg-gray-50 transition-colors">
					<td class="px-4 sm:px-6 py-4">
						<span class="font-medium text-gray-900"><?php echo htmlspecialchars($u['name']); ?></span>
					</td>
					<td class="px-4 sm:px-6 py-4">
						<span class="text-gray-600"><?php echo htmlspecialchars($u['email']); ?></span>
					</td>
					<td class="px-4 sm:px-6 py-4">
						<span class="text-gray-900 text-xs font-semibold">
							<?php echo htmlspecialchars($u['role']); ?>
						</span>
					</td>
					<td class="px-4 sm:px-6 py-4">
						<div class="flex items-center gap-2">
							<i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
							<span class="text-gray-600 text-xs"><?php echo htmlspecialchars($u['created_at']); ?></span>
						</div>
					</td>
					<td class="px-4 sm:px-6 py-4">
						<div class="flex flex-col gap-2">
							<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/reset-password" class="flex gap-2 items-center">
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
								<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
								<input type="password" name="password" minlength="8" placeholder="New Password" class="border-2 border-gray-300 rounded-lg px-3 py-2 flex-1 text-sm focus:ring-2 focus:ring-[#008000] focus:border-[#008000]" required />
								<button class="px-3 py-2 rounded-lg bg-gray-700 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">Reset</button>
							</form>
							<div class="flex flex-wrap gap-2">
								<button type="button" class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg border-2 border-[#008000] text-[#008000] px-3 py-1.5 text-xs font-semibold hover:bg-[#008000]/10 editToggle" data-target="edit-<?php echo (int)$u['id']; ?>">
									<i data-lucide="pencil" class="w-3 h-3"></i>
									Edit
								</button>
								<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/delete" class="flex-1 deleteUserForm">
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
									<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
									<button class="w-full inline-flex items-center justify-center gap-1 rounded-lg border-2 border-red-300 text-red-700 px-3 py-1.5 text-xs font-semibold hover:bg-red-50 transition-colors">
										<i data-lucide="trash-2" class="w-3 h-3"></i>
										Delete
									</button>
								</form>
							</div>
						</div>
					</td>
				</tr>
				<tr id="edit-<?php echo (int)$u['id']; ?>" class="hidden bg-gray-50/50 border-t">
					<td colspan="5" class="px-4 sm:px-6 py-4">
						<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/update" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
							<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
							<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
							<div>
								<label class="block text-xs font-semibold text-gray-700 mb-1.5">Name</label>
								<input name="name" value="<?php echo htmlspecialchars($u['name']); ?>" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" required>
							</div>
							<div>
								<label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
								<input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors" required>
							</div>
							<div>
								<label class="block text-xs font-semibold text-gray-700 mb-1.5">Role</label>
								<select name="role" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#008000] focus:border-[#008000] transition-colors">
									<?php foreach ($roles as $r): ?>
										<option value="<?php echo htmlspecialchars($r); ?>" <?php echo $u['role'] === $r ? 'selected' : ''; ?>><?php echo htmlspecialchars($r); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="flex gap-2">
								<button class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#00A86B] to-[#008000] text-white px-4 py-2 rounded-xl shadow-md hover:opacity-90 hover:shadow-lg focus:ring-2 focus:ring-[#008000] focus:ring-offset-2 transition-all font-semibold">
									<i data-lucide="check" class="w-4 h-4"></i>
									Save
								</button>
								<button type="button" class="flex-1 inline-flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-50 closeEdit font-medium" data-target="edit-<?php echo (int)$u['id']; ?>">
									<i data-lucide="x" class="w-4 h-4"></i>
									Cancel
								</button>
							</div>
						</form>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
(function(){
	document.querySelectorAll('.editToggle').forEach(btn => {
		btn.addEventListener('click', () => {
			const targetId = btn.getAttribute('data-target');
			const row = document.getElementById(targetId);
			if (row) {
				row.classList.toggle('hidden');
			}
		});
	});
	document.querySelectorAll('.closeEdit').forEach(btn => {
		btn.addEventListener('click', () => {
			const targetId = btn.getAttribute('data-target');
			const row = document.getElementById(targetId);
			if (row) {
				row.classList.add('hidden');
			}
		});
	});
	document.querySelectorAll('.deleteUserForm').forEach(form => {
		form.addEventListener('submit', (event) => {
			if (!confirm('Delete this user account? This action cannot be undone.')) {
				event.preventDefault();
			}
		});
	});
})();
</script>


