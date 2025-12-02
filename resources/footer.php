	<?php if ($user): ?>
				</div>
			</main>
		</div>
	</div>
	<?php else: ?>
	</main>
	<?php endif; ?>
	
	<footer class="border-t bg-white mt-10">
		<div class="max-w-7xl mx-auto px-4 py-6 text-sm text-gray-500">
			&copy; <?php echo date('Y'); ?> IKEA Cakes & Snacks Commissary. All rights reserved.
		</div>
	</footer>
	<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
	<script src="<?php echo htmlspecialchars($baseUrl); ?>/public/js/utils.js"></script>
	<script src="<?php echo htmlspecialchars($baseUrl); ?>/public/js/app.js"></script>
	<script>
		// Initialize Lucide icons
		if (typeof lucide !== 'undefined') {
			lucide.createIcons();
		}
	</script>
</body>
</html>


