<?php
declare(strict_types=1);

abstract class BaseController
{
	protected function render(string $viewPath, array $data = []): void
	{
		// Extract and clear unauthorized flash message if present
		$unauthorizedFlash = null;
		if (isset($_SESSION['flash_unauthorized'])) {
			$unauthorizedFlash = $_SESSION['flash_unauthorized'];
			unset($_SESSION['flash_unauthorized']);
		}
		
		extract($data);
		$basePath = BASE_PATH;
		$baseUrl = defined('BASE_URL') ? BASE_URL : '';
		
		// Make unauthorized flash available to header
		$GLOBALS['unauthorizedFlash'] = $unauthorizedFlash;
		
		include $basePath . '/resources/header.php';
		include $basePath . '/resources/views/' . ltrim($viewPath, '/');
		include $basePath . '/resources/footer.php';
	}

	protected function renderLogin(string $viewPath, array $data = []): void
	{
		extract($data);
		$basePath = BASE_PATH;
		$baseUrl = defined('BASE_URL') ? BASE_URL : '';
		
		// Login page layout without sidebar
		?><!doctype html>
		<html lang="en" data-theme="light">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>iKEA Inventory Management System - Login</title>
			<link rel="icon" href="<?php echo htmlspecialchars($baseUrl); ?>/public/favicon.ico">
			<script src="https://cdn.tailwindcss.com"></script>
			<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
		</head>
		<body class="bg-gray-50" data-theme="light">
			<?php include $basePath . '/resources/views/' . ltrim($viewPath, '/'); ?>
		</body>
		</html><?php
	}

	protected function redirect(string $path): void
	{
			$url = $path;
			if (isset($path[0]) && $path[0] === '/' && defined('BASE_URL')) {
				$url = BASE_URL . $path;
			}
			header('Location: ' . $url);
		exit;
	}
}


