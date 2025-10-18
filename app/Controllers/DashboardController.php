<?php
declare(strict_types=1);

class DashboardController extends BaseController
{
	public function index(): void
	{
		if (!Auth::check()) {
			$this->redirect('/login');
		}

		$user = Auth::user();
		if (!$user) {
			// If user is null, redirect to login
			$this->redirect('/login');
		}

		// Placeholder stats; will be replaced with real queries
		$stats = [
			'user' => Auth::user(),
			'lowStockCount' => 0,
			'pendingRequests' => 0,
			'pendingDeliveries' => 0,
			'inventoryValue' => 0,
		];

		// Compute low stock count
		$ingredientModel = new Ingredient();
		$all = $ingredientModel->all();
		$low = 0;
		foreach ($all as $ing) {
			if ((float)$ing['quantity'] <= (float)$ing['reorder_level']) { $low++; }
		}
		$stats['lowStockCount'] = $low;

		// Pending requests
		$requestModel = new RequestModel();
		$pending = $requestModel->listAll('Pending');
		$stats['pendingRequests'] = count($pending);

		// Pending deliveries
		$deliveryModel = new Delivery();
		$stats['pendingDeliveries'] = $deliveryModel->getPendingCount();

		// Calculate inventory value (placeholder - would need cost data)
		$totalValue = 0;
		foreach ($all as $ingredient) {
			// This is a placeholder calculation - in reality you'd need cost per unit
			$totalValue += (float)$ingredient['quantity'] * 10; // Assuming ₱10 per unit average
		}
		$stats['inventoryValue'] = $totalValue;

		$this->render('dashboard/index.php', [
			'user' => $user,
			'stats' => $stats,
			'pageTitle' => 'Dashboard',
		]);
	}
}


