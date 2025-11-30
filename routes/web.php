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
		'/reports/export/excel' => ['ReportsController', 'excel'],
		'/reports/export/csv' => ['ReportsController', 'csv'],
		'/audit' => ['AuditController', 'index'],
		'/notifications' => ['NotificationController', 'index'],
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
		'/requests/distribute' => ['RequestController', 'distribute'],
		'/requests/prepare' => ['RequestController', 'prepare'],
		'/purchases' => ['PurchaseController', 'store'],
		'/purchases/mark-paid' => ['PurchaseController', 'markPaid'],
		'/deliveries' => ['DeliveryController', 'store'],
		// removed force-delete feature routes
		'/inventory' => ['InventoryController', 'store'],
		'/inventory/meta' => ['InventoryController', 'updateMeta'],
		'/inventory/set' => ['InventoryController', 'storeSet'],
		'/inventory/set/delete' => ['InventoryController', 'deleteSet'],
		'/users' => ['UserController', 'store'],
		'/users/update' => ['UserController', 'update'],
		'/users/delete' => ['UserController', 'delete'],
		'/users/reset-password' => ['UserController', 'resetPassword'],
		'/notifications' => ['NotificationController', 'index'],
		'/audit/clear' => ['AuditController', 'clear'],
	],
];

