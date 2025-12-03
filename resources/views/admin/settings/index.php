<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$settings = $settings ?? [];
$costHidden = $settings['costHiddenRoles'] ?? [];
$permissionMatrix = $settings['permissions'] ?? [];
$archiveDays = $settings['archiveDays'] ?? 0;
$enabledReports = $settings['enabledReports'] ?? ['purchase','consumption'];
$companyName = $settings['companyName'] ?? 'IKEA Commissary System';
$companyTagline = $settings['companyTagline'] ?? '';
$logoPath = $settings['logoPath'] ?? null;
$themeDefault = $settings['themeDefault'] ?? 'system';
$widgetSettings = $settings['dashboardWidgets'] ?? [];
$ingredientSetsEnabled = $settings['ingredientSetsEnabled'] ?? true;
$widgetsByRole = function (string $role) use ($widgetSettings, $dashboardWidgets) {
	 if (isset($widgetSettings[$role])) {
		 return $widgetSettings[$role];
	 }
	 return $widgetSettings['default'] ?? $dashboardWidgets;
};
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<div>
			<h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">System Settings</h1>
			<p class="text-xs md:text-sm text-gray-600">Security, reporting, display and data retention controls. Only Owners and Managers can update these configurations.</p>
		</div>
	</div>
</div>

<div class="space-y-8">
	<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
		<div>
		<div class="flex flex-wrap gap-3">
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/account/security" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100">
				<i data-lucide="key-round" class="w-4 h-4"></i>
				My Account Security
			</a>
			<a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
				<i data-lucide="arrow-left" class="w-4 h-4"></i>
				Return to Dashboard
			</a>
		</div>
	</div>

	<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
		<div class="xl:col-span-2 space-y-8">
			<!-- Security & Accounts -->
			<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
				<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
					<div>
						<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
							<i data-lucide="shield" class="w-5 h-5 text-indigo-600"></i>
							Account & Security
						</h2>
						<p class="text-sm text-gray-600 mt-1">Manage users, enforce permissions, and hide sensitive data.</p>
					</div>
					<span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full">Admins only</span>
				</div>
				<div class="p-6 space-y-8">
					<!-- Manage Users -->
					<div class="space-y-4">
						<div class="flex items-center justify-between">
							<div>
								<h3 class="text-lg font-semibold text-gray-900">Manage Users</h3>
								<p class="text-sm text-gray-500">Add, modify, deactivate or force logout accounts.</p>
							</div>
							<span class="text-xs text-gray-500">Total users: <?php echo count($users ?? []); ?></span>
						</div>
						<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 bg-slate-50/70 rounded-xl border border-slate-100 p-4">
							<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users" class="grid grid-cols-1 gap-3 lg:col-span-3 md:grid-cols-2">
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
								<input class="border border-gray-300 rounded-lg px-3 py-2" name="name" placeholder="Name" required>
								<input class="border border-gray-300 rounded-lg px-3 py-2" type="email" name="email" placeholder="Email" required>
								<select name="role" class="border border-gray-300 rounded-lg px-3 py-2">
									<?php foreach ($roles as $role): ?>
										<option value="<?php echo htmlspecialchars($role); ?>"><?php echo htmlspecialchars($role); ?></option>
									<?php endforeach; ?>
								</select>
								<input class="border border-gray-300 rounded-lg px-3 py-2" type="password" name="password" minlength="8" placeholder="Temporary Password" required>
								<div class="md:col-span-2">
									<button class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
										<i data-lucide="user-plus" class="w-4 h-4"></i>Create User
									</button>
								</div>
							</form>
							<div class="text-xs text-gray-500">
								<p class="font-semibold text-gray-700 mb-1">Password policy</p>
								<ul class="list-disc list-inside space-y-1">
									<li>Minimum 8 characters</li>
									<li>Encourage phrase-based passwords</li>
									<li>Rotate credentials every quarter</li>
								</ul>
							</div>
						</div>
						<div class="overflow-x-auto">
							<table class="min-w-full text-sm">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">User</th>
										<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Role</th>
										<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Status</th>
										<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									<?php foreach ($users as $u): ?>
										<tr>
											<td class="px-3 py-3">
												<p class="font-semibold text-gray-900"><?php echo htmlspecialchars($u['name']); ?></p>
												<p class="text-xs text-gray-500"><?php echo htmlspecialchars($u['email']); ?></p>
											</td>
											<td class="px-3 py-3 text-gray-700"><?php echo htmlspecialchars($u['role']); ?></td>
											<td class="px-3 py-3">
												<?php $status = $u['status'] ?? 'active'; ?>
												<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold <?php echo $status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'; ?>">
													<i data-lucide="<?php echo $status === 'active' ? 'check' : 'slash'; ?>" class="w-3 h-3"></i>
													<?php echo ucfirst($status); ?>
												</span>
											</td>
											<td class="px-3 py-3 space-y-2">
												<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/reset-password" class="flex gap-2 items-center">
													<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
													<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
													<input type="password" name="password" minlength="8" placeholder="New password" class="border border-gray-300 rounded px-2 py-1 flex-1" required>
													<button class="px-3 py-1 text-xs font-semibold bg-slate-800 text-white rounded">Reset</button>
												</form>
												<div class="flex flex-wrap gap-2">
													<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/users/status">
														<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
														<input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
														<input type="hidden" name="status" value="<?php echo $status === 'active' ? 'inactive' : 'active'; ?>">
														<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded border text-xs font-semibold <?php echo $status === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-700 hover:bg-green-50'; ?>">
															<i data-lucide="<?php echo $status === 'active' ? 'user-x' : 'user-check'; ?>" class="w-3 h-3"></i>
															<?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?>
														</button>
													</form>
													<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/users/force-logout">
														<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
														<input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
														<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded border border-amber-200 text-amber-600 text-xs font-semibold hover:bg-amber-50">
															<i data-lucide="log-out" class="w-3 h-3"></i>
															Force logout
														</button>
													</form>
													<button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded border border-indigo-200 text-indigo-700 text-xs font-semibold hover:bg-indigo-50 editToggle" data-target="edit-<?php echo (int)$u['id']; ?>">
														<i data-lucide="pencil" class="w-3 h-3"></i>
														Edit
													</button>
												</div>
												<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/users/update" id="edit-<?php echo (int)$u['id']; ?>" class="hidden mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 border border-gray-100 rounded-lg p-3 bg-slate-50">
													<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
													<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
													<input name="name" value="<?php echo htmlspecialchars($u['name']); ?>" class="border border-gray-300 rounded px-2 py-1" placeholder="Name" required>
													<input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" class="border border-gray-300 rounded px-2 py-1" required>
													<select name="role" class="border border-gray-300 rounded px-2 py-1">
														<?php foreach ($roles as $role): ?>
															<option value="<?php echo htmlspecialchars($role); ?>" <?php echo $u['role'] === $role ? 'selected' : ''; ?>><?php echo htmlspecialchars($role); ?></option>
														<?php endforeach; ?>
													</select>
													<div class="md:col-span-3 flex gap-2">
														<button class="flex-1 inline-flex items-center justify-center gap-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700">
															<i data-lucide="check" class="w-4 h-4"></i>Save
														</button>
														<button type="button" class="flex-1 inline-flex items-center justify-center gap-1 border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 closeEdit" data-target="edit-<?php echo (int)$u['id']; ?>">
															<i data-lucide="x" class="w-4 h-4"></i>Cancel
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

					<hr class="border-gray-100">

					<!-- Permissions -->
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/save" class="space-y-5">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<input type="hidden" name="section" value="security">
						<div class="flex items-center justify-between">
							<div>
								<h3 class="text-lg font-semibold text-gray-900">Role & Permission Matrix</h3>
								<p class="text-sm text-gray-500">Toggle access to financial data, receipts, and critical tooling.</p>
							</div>
						</div>
						<div class="overflow-x-auto border border-gray-200 rounded-xl">
							<table class="min-w-full text-sm">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-3 py-2 text-left font-semibold text-xs text-gray-500">Role</th>
										<?php foreach ($permissionKeys as $perm): ?>
											<th class="px-3 py-2 text-center font-semibold text-xs text-gray-500"><?php echo ucwords(str_replace('_', ' ', $perm)); ?></th>
										<?php endforeach; ?>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									<?php foreach ($roles as $role): ?>
										<tr>
											<td class="px-3 py-3 font-semibold text-gray-800"><?php echo htmlspecialchars($role); ?></td>
											<?php foreach ($permissionKeys as $perm): ?>
												<?php $checked = !empty($permissionMatrix[$role][$perm]); ?>
												<td class="px-3 py-3 text-center">
													<label class="inline-flex items-center justify-center gap-2">
														<input type="checkbox" name="permissions[<?php echo htmlspecialchars($role); ?>][<?php echo htmlspecialchars($perm); ?>]" value="1" class="accent-indigo-600" <?php echo $checked ? 'checked' : ''; ?>>
													</label>
												</td>
											<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<div class="space-y-3">
							<label class="block text-sm font-semibold text-gray-700">Hide cost/financial data from:</label>
							<div class="flex flex-wrap gap-3">
								<?php foreach ($roles as $role): ?>
									<label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
										<input type="checkbox" name="cost_hidden_roles[]" value="<?php echo htmlspecialchars($role); ?>" class="accent-red-600" <?php echo in_array($role, $costHidden, true) ? 'checked' : ''; ?>>
										<?php echo htmlspecialchars($role); ?>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
						<button class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-3 rounded-lg hover:bg-indigo-700">
							<i data-lucide="save" class="w-4 h-4"></i>Save Security Settings
						</button>
					</form>
				</div>
			</div>

			<!-- Reporting -->
			<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
				<div class="px-6 py-4 border-b border-gray-100">
					<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="file-bar-chart" class="w-5 h-5 text-emerald-600"></i>
						Reporting Configuration
					</h2>
					<p class="text-sm text-gray-600 mt-1">Keep reports fast, compliant, and tailored to each department.</p>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/save" class="p-6 space-y-6">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
					<input type="hidden" name="section" value="reporting">
					<div>
						<label class="block text-sm font-semibold text-gray-700 mb-1">Automatically archive purchase history after (days)</label>
						<input type="number" min="0" name="archive_days" value="<?php echo (int)$archiveDays; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0 = keep all records">
						<p class="text-xs text-gray-500 mt-1">Older records will be hidden by default but can be retrieved via advanced filters.</p>
					</div>
					<div>
						<label class="block text-sm font-semibold text-gray-700 mb-2">Enable report sections</label>
						<div class="flex flex-wrap gap-3">
							<label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
								<input type="checkbox" name="reports[]" value="purchase" class="accent-emerald-600" <?php echo in_array('purchase', $enabledReports, true) ? 'checked' : ''; ?>>
								Purchase Reports
							</label>
							<label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
								<input type="checkbox" name="reports[]" value="consumption" class="accent-emerald-600" <?php echo in_array('consumption', $enabledReports, true) ? 'checked' : ''; ?>>
								Ingredient Consumption
							</label>
						</div>
					</div>
					<button class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-lg hover:bg-emerald-700">
						<i data-lucide="save" class="w-4 h-4"></i>Save Reporting Rules
					</button>
				</form>
			</div>

			<!-- Display & Branding -->
			<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
				<div class="px-6 py-4 border-b border-gray-100">
					<h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="color-swatch" class="w-5 h-5 text-purple-600"></i>
						System Display & Branding
					</h2>
					<p class="text-sm text-gray-600 mt-1">Customize the console’s appearance across the organization.</p>
				</div>
				<div class="p-6 space-y-6">
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/save" class="space-y-4">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<input type="hidden" name="section" value="display">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-1">Commissary / Company Name</label>
								<input name="company_name" value="<?php echo htmlspecialchars($companyName); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
							</div>
							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-1">Tagline</label>
								<input name="company_tagline" value="<?php echo htmlspecialchars($companyTagline); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2">
							</div>
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-700 mb-1">Default Theme</label>
							<select name="theme_default" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
								<option value="system" <?php echo $themeDefault === 'system' ? 'selected' : ''; ?>>Match user's device</option>
								<option value="light" <?php echo $themeDefault === 'light' ? 'selected' : ''; ?>>Light</option>
								<option value="dark" <?php echo $themeDefault === 'dark' ? 'selected' : ''; ?>>Dark</option>
							</select>
						</div>
						<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-1">Ingredient Sets Feature</label>
								<p class="text-xs text-gray-600">Enable or disable the ability to create and manage ingredient sets for kitchen requests.</p>
							</div>
							<label class="relative inline-flex items-center cursor-pointer">
								<input type="hidden" name="ingredient_sets_enabled" value="0">
								<input type="checkbox" name="ingredient_sets_enabled" value="1" class="sr-only peer" <?php echo $ingredientSetsEnabled ? 'checked' : ''; ?>>
								<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
							</label>
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-700 mb-2">Dashboard widgets per role</label>
							<div class="space-y-3">
								<?php foreach ($roles as $role): ?>
									<div class="border border-gray-200 rounded-lg p-3">
										<p class="text-sm font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($role); ?></p>
										<div class="flex flex-wrap gap-3">
											<?php foreach ($dashboardWidgets as $widget): ?>
												<label class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
													<input type="checkbox" name="widgets[<?php echo htmlspecialchars($role); ?>][]" value="<?php echo htmlspecialchars($widget); ?>" class="accent-purple-600" <?php echo in_array($widget, $widgetsByRole($role), true) ? 'checked' : ''; ?>>
													<?php echo ucwords(str_replace('_', ' ', $widget)); ?>
												</label>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<button class="inline-flex items-center gap-2 bg-purple-600 text-white px-5 py-3 rounded-lg hover:bg-purple-700">
							<i data-lucide="save" class="w-4 h-4"></i>Save Display Settings
						</button>
					</form>

					<div class="border border-dashed border-gray-300 rounded-xl p-4">
						<p class="text-sm font-semibold text-gray-800 mb-2">Brand Logo</p>
						<?php if ($logoPath): ?>
							<div class="mb-3">
								<img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Current Logo" class="h-16 object-contain">
							</div>
						<?php endif; ?>
						<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/branding" enctype="multipart/form-data" class="flex flex-col gap-3">
							<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
							<input type="file" name="logo" accept="image/*" class="text-sm">
							<button class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black/80">
								<i data-lucide="upload" class="w-4 h-4"></i>
								Upload new logo
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Data & Backup Column -->
		<div class="space-y-6">
			<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
				<div class="px-5 py-4 border-b border-gray-100">
					<h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
						<i data-lucide="database" class="w-5 h-5 text-slate-700"></i>
						Database & Backups
					</h2>
					<p class="text-sm text-gray-600 mt-1">Export snapshots or import initial stock data.</p>
				</div>
				<div class="p-5 space-y-5">
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/backup/export" class="space-y-3">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<label class="text-sm font-semibold text-gray-700">Export system backup</label>
						<select name="format" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-slate-500">
							<option value="sql">SQL (structure + data)</option>
							<option value="csv">CSV (table snapshots)</option>
						</select>
						<button class="w-full inline-flex items-center justify-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-900">
							<i data-lucide="download" class="w-4 h-4"></i>
							Generate backup
						</button>
						<p class="text-[11px] text-gray-500">Files are named automatically (backup_YYYY-MM-DD.ext).</p>
					</form>
					<hr class="border-gray-100">
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/settings/backup/import" enctype="multipart/form-data" class="space-y-3">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
						<label class="text-sm font-semibold text-gray-700">Import initial inventory (CSV)</label>
						<input type="file" name="inventory_csv" accept=".csv" class="text-sm text-gray-700">
						<button class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700">
							<i data-lucide="import" class="w-4 h-4"></i>
							Import inventory
						</button>
						<p class="text-[11px] text-gray-500">Required columns: name, unit, quantity, reorder_level, category.</p>
					</form>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 space-y-4">
				<h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="info" class="w-5 h-5 text-gray-500"></i>
					Helpful reminders
				</h2>
				<ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
					<li>Changing a user’s password or status forces them to sign in again.</li>
					<li>Kitchen Staff never sees cost data when “hide costs” is enabled.</li>
					<li>Branding updates immediately refresh the sidebar and login screens.</li>
					<li>Record every backup in an external drive for disaster recovery.</li>
				</ul>
			</div>
		</div>
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
})();
</script>

