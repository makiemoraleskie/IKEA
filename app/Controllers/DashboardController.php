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
		$canViewCosts = Settings::costVisibleForRole($user['role'] ?? null);

		// Compute low stock count
		$ingredientModel = new Ingredient();
        $purchaseModel = new Purchase();
		$all = $ingredientModel->all();
		$low = 0;
		foreach ($all as $ing) {
			if ((float)$ing['quantity'] <= (float)$ing['reorder_level']) { $low++; }
		}
		$stats['lowStockCount'] = $low;

		// Pending requests (count batches, not individual requests)
		$requestModel = new RequestModel();
		$stats['pendingRequests'] = $requestModel->countBatchesByStatus('Pending');

		// Pending deliveries
		$deliveryModel = new Delivery();
		$stats['pendingDeliveries'] = $deliveryModel->getPendingCount();
        $stats['partialDeliveries'] = $deliveryModel->countDeliveriesByStatus('Partial');
        $stats['pendingPayments'] = $purchaseModel->countByPaymentStatus('Pending');

		// Calculate inventory value using weighted average purchase cost
        $averageCosts = $purchaseModel->averageCostPerItem();
		$totalValue = 0;
		foreach ($all as $ingredient) {
            $itemId = (int)($ingredient['id'] ?? 0);
            $avgCost = $averageCosts[$itemId] ?? null;
            if ($avgCost === null) {
                continue;
            }
			$totalValue += (float)$ingredient['quantity'] * $avgCost;
		}
		$stats['inventoryValue'] = $totalValue;

        // Build weekly chart data (last 7 days)
        $endDate = new DateTime('today');
        $startDate = (clone $endDate)->modify('-6 days');
        $purchaseCounts = $purchaseModel->dailyCounts($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $deliveryCounts = $deliveryModel->dailyCounts($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $labels = [];
        $purchaseSeries = [];
        $deliverySeries = [];
        $cursor = clone $startDate;
        while ($cursor <= $endDate) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('M j');
            $purchaseSeries[] = $purchaseCounts[$key] ?? 0;
            $deliverySeries[] = $deliveryCounts[$key] ?? 0;
            $cursor->modify('+1 day');
        }
        $stats['chart'] = [
            'labels' => $labels,
            'purchases' => $purchaseSeries,
            'deliveries' => $deliverySeries,
        ];

		$this->render('dashboard/index.php', [
			'user' => $user,
			'stats' => $stats,
			'pageTitle' => 'Dashboard',
			'canViewCosts' => $canViewCosts,
			'dashboardWidgets' => Settings::dashboardWidgetsForRole($user['role'] ?? ''),
		]);
	}
}


