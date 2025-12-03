	<?php if ($user): ?>
				</div>
			</main>
		</div>
	</div>
	<?php else: ?>
	</main>
	<?php endif; ?>
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


