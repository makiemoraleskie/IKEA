<div class="flex items-center justify-between mt-4 mb-6">
	<h1 class="text-2xl font-semibold">User Management</h1>
	<a href="/dashboard" class="text-sm text-blue-600">Back to Dashboard</a>
</div>

<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="bg-white border rounded p-4 mb-6">
	<h2 class="text-lg font-semibold mb-3">Create User</h2>
	<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
		<div>
			<label class="block text-sm mb-1">Name</label>
			<input name="name" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Email</label>
			<input type="email" name="email" class="w-full border rounded px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm mb-1">Role</label>
			<select name="role" class="w-full border rounded px-3 py-2">
				<?php foreach ($roles as $r): ?>
					<option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars($r); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<label class="block text-sm mb-1">Password (min 8 chars)</label>
			<input type="password" name="password" minlength="8" class="w-full border rounded px-3 py-2" required />
		</div>
		<div class="md:col-span-4">
			<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Create</button>
		</div>
	</form>
</div>

<div class="bg-white border rounded">
	<div class="p-4 border-b"><h2 class="text-lg font-semibold">Users</h2></div>
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead class="bg-gray-50">
				<tr>
					<th class="text-left px-4 py-2">ID</th>
					<th class="text-left px-4 py-2">Name</th>
					<th class="text-left px-4 py-2">Email</th>
					<th class="text-left px-4 py-2">Role</th>
					<th class="text-left px-4 py-2">Created</th>
					<th class="text-left px-4 py-2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $u): ?>
				<tr class="border-t">
					<td class="px-4 py-2"><?php echo (int)$u['id']; ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($u['name']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($u['email']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($u['role']); ?></td>
					<td class="px-4 py-2"><?php echo htmlspecialchars($u['created_at']); ?></td>
					<td class="px-4 py-2">
						<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/reset-password" class="flex gap-2 items-center">
							<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
							<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
							<input type="password" name="password" minlength="8" placeholder="New Password" class="border rounded px-2 py-1" required />
							<button class="px-3 py-1.5 rounded bg-gray-800 text-white">Reset</button>
						</form>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


