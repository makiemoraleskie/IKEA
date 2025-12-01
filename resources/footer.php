	<?php if ($user): ?>
				</div>
			</main>
		</div>
	</div>
	<?php else: ?>
	</main>
	<?php endif; ?>
	
	<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
	<script src="<?php echo htmlspecialchars($baseUrl); ?>/public/js/app.js"></script>
	<script>
		// Initialize Lucide icons
		lucide.createIcons();
	</script>
</body>
</html>


