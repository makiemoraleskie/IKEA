<?php
declare(strict_types=1);

return [
	'GET' => [
		'/' => ['DashboardController', 'index'],
		'/login' => ['AuthController', 'showLogin'],
		'/dashboard' => ['DashboardController', 'index'],
		'/requests' => ['RequestController', 'index'],
		'/purchases' => ['PurchaseController', 'index'],
		'/deliveries' => ['DeliveryController', 'index'],
		'/reports' => ['ReportsController', 'index'],
		'/audit' => ['AuditController', 'index'],
		'/reports/pdf' => ['ReportsController', 'pdf'],
		'/inventory' => ['InventoryController', 'index'],
		'/users' => ['UserController', 'index'],
	],
	'POST' => [
		'/login' => ['AuthController', 'login'],
		'/logout' => ['AuthController', 'logout'],
		'/requests' => ['RequestController', 'store'],
		'/requests/approve' => ['RequestController', 'approve'],
		'/requests/reject' => ['RequestController', 'reject'],
		'/purchases' => ['PurchaseController', 'store'],
		'/purchases/mark-paid' => ['PurchaseController', 'markPaid'],
		'/deliveries' => ['DeliveryController', 'store'],
		'/inventory' => ['InventoryController', 'store'],
		'/users' => ['UserController', 'store'],
		'/users/reset-password' => ['UserController', 'resetPassword'],
	],
];

