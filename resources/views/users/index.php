<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>

<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">User Management</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Create and manage commissary accounts</p>
		</div>
	</div>
</div>

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6 mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col gap-1 mb-4">
		<h2 class="text-sm md:text-base font-semibold text-gray-900">Create User</h2>
		<p class="text-[10px] md:text-xs text-gray-600">Invite a teammate with appropriate access level</p>

	</div>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end w-full overflow-x-hidden">
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


<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
	<div class="px-4 sm:px-6 py-4 border-b flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
		<div>
			<h2 class="text-sm md:text-base font-semibold text-gray-900">Users</h2>
			<p class="text-[10px] md:text-xs text-gray-600">Existing accounts and quick resets</p>

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
		form.setAttribute('data-confirm', 'Are you sure you want to delete this user account? This action cannot be undone.');
		form.setAttribute('data-confirm-type', 'danger');
	});
})();
</script>


