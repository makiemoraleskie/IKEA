<?php
declare(strict_types=1);

abstract class BaseController
{
	protected function render(string $viewPath, array $data = []): void
	{
		extract($data);
		$basePath = BASE_PATH;
			$baseUrl = defined('BASE_URL') ? BASE_URL : '';
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
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>iKEA Inventory Management System - Login</title>
			<link rel="icon" href="<?php echo htmlspecialchars($baseUrl); ?>/public/favicon.ico">
			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
			<script src="https://cdn.tailwindcss.com"></script>
		</head>
		<body class="bg-gray-50">
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


