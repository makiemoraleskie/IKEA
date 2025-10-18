<?php
declare(strict_types=1);

abstract class BaseController
{
	protected function render(string $viewPath, array $data = []): void
	{
		extract($data);
		$basePath = BASE_PATH;
			$baseUrl = defined('BASE_URL') ? BASE_URL : '';
		include $basePath . '/includes/header.php';
		include $basePath . '/src/views/' . ltrim($viewPath, '/');
		include $basePath . '/includes/footer.php';
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


