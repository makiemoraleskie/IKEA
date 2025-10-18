<?php
declare(strict_types=1);

session_start();

// Basic error reporting for development (adjust for production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Define base path
define('BASE_PATH', __DIR__);

// Simple class autoloader (no namespaces)
spl_autoload_register(function (string $className): void {
	$paths = [
		BASE_PATH . '/app/Controllers/' . $className . '.php',
		BASE_PATH . '/app/Models/' . $className . '.php',
		BASE_PATH . '/app/Utils/' . $className . '.php',
	];
	foreach ($paths as $path) {
		if (file_exists($path)) {
			require_once $path;
			return;
		}
	}
});

// Load DB config (defines Database class)
require_once BASE_PATH . '/config/db.php';

// Composer autoload (for Dompdf etc.) if present
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
	require_once BASE_PATH . '/vendor/autoload.php';
}

// Load routes
$routes = require BASE_PATH . '/routes/web.php';

// Helper to get current path relative to app base
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (!defined('BASE_URL')) {
	define('BASE_URL', $scriptDir === '/' ? '' : $scriptDir);
}
$path = '/' . ltrim(substr($requestUri, strlen($scriptDir)), '/');
if ($path === '//') { $path = '/'; }
if ($path === '/index.php' || $path === '') { $path = '/'; }

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Dispatch
if (isset($routes[$method][$path])) {
	[$controllerName, $action] = $routes[$method][$path];
	$controller = new $controllerName();
	if (!method_exists($controller, $action)) {
		http_response_code(500);
		echo 'Controller action not found.';
		exit;
	}
	$controller->$action();
} else {
	// Fallback: route '/' to dashboard or login
	if ($path === '/' && $method === 'GET') {
		$controller = new DashboardController();
		$controller->index();
		exit;
	}

	http_response_code(404);
	echo '404 Not Found';
}


