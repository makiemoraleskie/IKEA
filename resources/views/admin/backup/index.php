<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$backups = $backups ?? [];
$totalSize = $totalSize ?? 0;
$backupFiles = $backupFiles ?? [];

function formatFileSize(int $bytes): string {
	if ($bytes < 1024) return $bytes . ' B';
	if ($bytes < 1048576) return number_format($bytes / 1024, 2) . ' KB';
	if ($bytes < 1073741824) return number_format($bytes / 1048576, 2) . ' MB';
	return number_format($bytes / 1073741824, 2) . ' GB';
}
?>
<!-- Page Header -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-5 mb-4 md:mb-6 max-w-full overflow-x-hidden">
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
		<div class="min-w-0 flex-1">
			<h1 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-0.5 md:mb-1 truncate">Backup and Restore</h1>
			<p class="text-[10px] md:text-xs text-gray-600">Create system backups, restore from saved backups, and manage backup files.</p>
		</div>
	</div>
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
<?php endif; ?>

<div class="space-y-6">
	<!-- Create Backup Section -->
	<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
		<div class="px-5 py-4 border-b border-gray-100">
			<h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
				<i data-lucide="database" class="w-5 h-5 text-slate-700"></i>
				Create Backup
			</h2>
			<p class="text-sm text-gray-600 mt-1">Create a complete snapshot of the current system state.</p>
		</div>
		<div class="p-5">
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/backup/create" class="space-y-4">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				<div>
					<label for="backup_description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
					<input type="text" id="backup_description" name="description" placeholder="e.g., Pre-update backup, Weekly backup" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 text-sm">
				</div>
				<button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-slate-800 text-white px-4 py-3 rounded-lg hover:bg-slate-900 transition-colors">
					<i data-lucide="download" class="w-4 h-4"></i>
					<span>Create Backup</span>
				</button>
				<p class="text-xs text-gray-500">The backup will include all database tables, structure, and data. Files are saved in the /backups directory.</p>
			</form>
		</div>
	</div>

	<!-- Saved Backups Section -->
	<div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
		<div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
			<div>
				<h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
					<i data-lucide="archive" class="w-5 h-5 text-blue-600"></i>
					Saved Backups
				</h2>
				<p class="text-sm text-gray-600 mt-1">View and manage all saved backups.</p>
			</div>
			<?php if ($totalSize > 0): ?>
				<span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
					Total: <?php echo formatFileSize($totalSize); ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="p-5">
			<?php if (empty($backups)): ?>
				<div class="text-center py-12">
					<i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
					<p class="text-sm text-gray-600 font-medium">No backups found</p>
					<p class="text-xs text-gray-500 mt-1">Create your first backup to get started.</p>
				</div>
			<?php else: ?>
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-4 py-3 text-left font-semibold text-gray-700">Date/Time</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700">Created By</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700">Description</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700">Size</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700">Records</th>
								<th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100">
							<?php foreach ($backups as $backup): ?>
								<?php
								$backupDate = $backup['created_at'] ?? '';
								$dateFormatted = $backupDate ? date('M j, Y g:i A', strtotime($backupDate)) : 'Unknown';
								$fileSize = isset($backup['file_size']) ? formatFileSize((int)$backup['file_size']) : '0 B';
								$recordsCount = isset($backup['records_count']) ? number_format((int)$backup['records_count']) : '0';
								$filename = $backup['filename'] ?? '';
								$fileExists = isset($backup['file_path']) && file_exists($backup['file_path']);
								?>
								<tr class="hover:bg-gray-50 <?php echo !$fileExists ? 'opacity-60' : ''; ?>">
									<td class="px-4 py-3 text-gray-900">
										<div class="font-medium"><?php echo htmlspecialchars($dateFormatted); ?></div>
										<div class="text-xs text-gray-500"><?php echo htmlspecialchars($filename); ?></div>
									</td>
									<td class="px-4 py-3 text-gray-700">
										<?php echo htmlspecialchars($backup['created_by_name'] ?? 'Unknown'); ?>
									</td>
									<td class="px-4 py-3 text-gray-600">
										<?php echo $backup['description'] ? htmlspecialchars($backup['description']) : '<span class="text-gray-400 italic">No description</span>'; ?>
									</td>
									<td class="px-4 py-3 text-gray-600"><?php echo $fileSize; ?></td>
									<td class="px-4 py-3 text-gray-600"><?php echo $recordsCount; ?></td>
									<td class="px-4 py-3">
										<div class="flex items-center justify-end gap-2">
											<?php if ($fileExists): ?>
												<a href="<?php echo htmlspecialchars($baseUrl); ?>/backup/download?id=<?php echo (int)$backup['id']; ?>" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
													<i data-lucide="download" class="w-3.5 h-3.5"></i>
													Download
												</a>
												<button type="button" onclick="showRestoreModal(<?php echo (int)$backup['id']; ?>, '<?php echo htmlspecialchars(addslashes($dateFormatted)); ?>', '<?php echo htmlspecialchars(addslashes($backup['description'] ?: 'No description')); ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
													<i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
													Restore
												</button>
											<?php else: ?>
												<span class="text-xs text-gray-400 italic">File missing</span>
											<?php endif; ?>
											<button type="button" onclick="showDeleteModal(<?php echo (int)$backup['id']; ?>, '<?php echo htmlspecialchars(addslashes($filename)); ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
												<i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
												Delete
											</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
	<div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="hideRestoreModal()"></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-md bg-white rounded-2xl shadow-xl border-2 border-yellow-200 overflow-hidden">
			<div class="bg-yellow-50 px-5 py-4 border-b border-yellow-200">
				<div class="flex items-start gap-3">
					<div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
						<i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
					</div>
					<div class="flex-1">
						<h3 class="text-lg font-semibold text-gray-900 mb-1">Confirm Restore</h3>
						<p class="text-sm text-gray-600">This action cannot be undone.</p>
					</div>
				</div>
			</div>
			<div class="p-5">
				<div class="space-y-3 mb-6">
					<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
						<div class="flex items-start gap-2">
							<i data-lucide="info" class="w-4 h-4 text-amber-600 mt-0.5 shrink-0"></i>
							<div class="text-xs text-amber-800">
								<strong>Warning:</strong> This will replace ALL current data with data from the selected backup. An automatic backup will be created before restoring, but this action is irreversible.
							</div>
						</div>
					</div>
					<div class="space-y-2">
						<div class="flex items-start gap-3">
							<span class="text-sm font-medium text-gray-700 w-24 shrink-0">Backup:</span>
							<span id="restoreBackupDate" class="text-sm text-gray-900 flex-1"></span>
						</div>
						<div class="flex items-start gap-3">
							<span class="text-sm font-medium text-gray-700 w-24 shrink-0">Description:</span>
							<span id="restoreBackupDesc" class="text-sm text-gray-900 flex-1"></span>
						</div>
					</div>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/backup/restore" id="restoreForm">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
					<input type="hidden" name="backup_id" id="restoreBackupId" value="">
					<div class="flex justify-end gap-3">
						<button type="button" onclick="hideRestoreModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
							Cancel
						</button>
						<button type="submit" class="inline-flex items-center gap-2 bg-yellow-600 text-white px-5 py-2.5 rounded-lg hover:bg-yellow-700 transition-colors">
							<i data-lucide="refresh-cw" class="w-4 h-4"></i>
							Confirm Restore
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
	<div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="hideDeleteModal()"></div>
	<div class="relative z-10 flex min-h-full items-center justify-center px-4 py-8">
		<div class="w-full max-w-md bg-white rounded-2xl shadow-xl border-2 border-red-200 overflow-hidden">
			<div class="bg-red-50 px-5 py-4 border-b border-red-200">
				<div class="flex items-start gap-3">
					<div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
						<i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
					</div>
					<div class="flex-1">
						<h3 class="text-lg font-semibold text-gray-900 mb-1">Delete Backup</h3>
						<p class="text-sm text-gray-600">This action cannot be undone.</p>
					</div>
				</div>
			</div>
			<div class="p-5">
				<div class="space-y-3 mb-6">
					<p class="text-sm text-gray-700">Are you sure you want to delete this backup?</p>
					<div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
						<p class="text-xs font-mono text-gray-600" id="deleteBackupFilename"></p>
					</div>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/backup/delete" id="deleteForm">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
					<input type="hidden" name="backup_id" id="deleteBackupId" value="">
					<div class="flex justify-end gap-3">
						<button type="button" onclick="hideDeleteModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
							Cancel
						</button>
						<button type="submit" class="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700 transition-colors">
							<i data-lucide="trash-2" class="w-4 h-4"></i>
							Delete Backup
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
(function(){
	// Restore Modal Functions
	const restoreModal = document.getElementById('restoreModal');
	const deleteModal = document.getElementById('deleteModal');
	
	function showRestoreModal(backupId, date, description) {
		if (!restoreModal) return;
		document.getElementById('restoreBackupId').value = backupId;
		document.getElementById('restoreBackupDate').textContent = date;
		document.getElementById('restoreBackupDesc').textContent = description || 'No description';
		restoreModal.classList.remove('hidden');
		document.body.classList.add('overflow-hidden');
		if (typeof lucide !== 'undefined') {
			lucide.createIcons({ elements: restoreModal.querySelectorAll('i[data-lucide]') });
		}
	}
	
	function hideRestoreModal() {
		if (restoreModal) {
			restoreModal.classList.add('hidden');
			document.body.classList.remove('overflow-hidden');
		}
	}
	
	function showDeleteModal(backupId, filename) {
		if (!deleteModal) return;
		document.getElementById('deleteBackupId').value = backupId;
		document.getElementById('deleteBackupFilename').textContent = filename;
		deleteModal.classList.remove('hidden');
		document.body.classList.add('overflow-hidden');
		if (typeof lucide !== 'undefined') {
			lucide.createIcons({ elements: deleteModal.querySelectorAll('i[data-lucide]') });
		}
	}
	
	function hideDeleteModal() {
		if (deleteModal) {
			deleteModal.classList.add('hidden');
			document.body.classList.remove('overflow-hidden');
		}
	}
	
	// Make functions globally available
	window.showRestoreModal = showRestoreModal;
	window.hideRestoreModal = hideRestoreModal;
	window.showDeleteModal = showDeleteModal;
	window.hideDeleteModal = hideDeleteModal;
	
	// Close modals on escape key
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			hideRestoreModal();
			hideDeleteModal();
		}
	});
	
	// Initialize Lucide icons on page load
	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}
})();
</script>

