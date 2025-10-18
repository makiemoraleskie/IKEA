<?php
declare(strict_types=1);

class DashboardController extends BaseController
{
	public function index(): void
	{
		if (!Auth::check()) {
			$this->redirect('/login');
		}

		// Placeholder stats; will be replaced with real queries
		$stats = [
			'user' => Auth::user(),
			'lowStockCount' => 0,
			'pendingRequests' => 0,
			'todayPurchases' => 0,
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

		// Today's purchases
		$purchaseModel = new Purchase();
		$stats['todayPurchases'] = $purchaseModel->getTodayCount();

		$this->render('dashboard/index.php', [
			'user' => Auth::user(),
			'stats' => $stats,
		]);
	}
}


