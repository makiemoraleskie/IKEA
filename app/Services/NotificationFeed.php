<?php
declare(strict_types=1);

class NotificationFeed
{
	private Notification $notificationModel;
	private Ingredient $ingredientModel;
	private RequestModel $requestModel;
	private Purchase $purchaseModel;
	private Delivery $deliveryModel;

	public function __construct()
	{
		$this->notificationModel = new Notification();
		$this->ingredientModel = new Ingredient();
		$this->requestModel = new RequestModel();
		$this->purchaseModel = new Purchase();
		$this->deliveryModel = new Delivery();
	}

	public function compose(?array $user, int $userId, string $baseUrl, int $personalLimit = 10, bool $includeSystem = true): array
	{
		$feed = [];

		if ($includeSystem && $user !== null) {
			$feed = array_merge($feed, $this->buildSystemNotifications($baseUrl));
		}

		if ($userId > 0) {
			$feed = array_merge($feed, $this->buildPersonalNotifications($userId, $baseUrl, $personalLimit));
		}

		usort($feed, static function (array $a, array $b): int {
			$timeA = $a['created_at'] ?? '';
			$timeB = $b['created_at'] ?? '';
			return strcmp($timeB, $timeA);
		});

		return $feed;
	}

	private function buildSystemNotifications(string $baseUrl): array
	{
		$items = [];
		$now = new DateTimeImmutable();
		$offset = 0;

		$append = function (array $payload) use (&$items, &$now, &$offset): void {
			$createdAt = $now->modify(sprintf('-%d seconds', $offset));
			$items[] = array_merge($payload, [
				'created_at' => $createdAt->format('Y-m-d H:i:s'),
				'type' => 'system',
			]);
			$offset++;
		};

		$lowStock = $this->ingredientModel->getLowStockItems();
		if (!empty($lowStock)) {
			$names = array_map(static fn($item) => $item['name'] ?? 'Item', array_slice($lowStock, 0, 3));
			$extra = count($lowStock) > 3 ? ' +' . (count($lowStock) - 3) . ' more' : '';
			$append([
				'id' => 'system:low-stock',
				'title' => 'Low stock alert',
				'body' => implode(', ', $names) . $extra . ' need replenishment.',
				'level' => 'warning',
				'icon' => 'alert-triangle',
				'accent' => 'text-red-700 bg-red-50',
				'link' => $this->normalizeLink($baseUrl, '/inventory?focus=low-stock#inventory-low-stock'),
			]);
		}

		$pendingBatches = $this->requestModel->countBatchesByStatus('Pending');
		if ($pendingBatches > 0) {
			$append([
				'id' => 'system:pending-requests',
				'title' => 'Requests awaiting approval',
				'body' => $pendingBatches . ' batch' . ($pendingBatches > 1 ? 'es need review.' : ' needs review.'),
				'level' => 'warning',
				'icon' => 'clipboard-list',
				'accent' => 'text-amber-700 bg-amber-50',
				'link' => $this->normalizeLink($baseUrl, '/requests?status=pending#requests-history'),
			]);
		}

		$pendingPayments = $this->purchaseModel->countByPaymentStatus('Pending');
		if ($pendingPayments > 0) {
			$append([
				'id' => 'system:pending-payments',
				'title' => 'Pending payments',
				'body' => $pendingPayments . ' purchase' . ($pendingPayments > 1 ? 's await settlement.' : ' awaits settlement.'),
				'level' => 'info',
				'icon' => 'credit-card',
				'accent' => 'text-rose-700 bg-rose-50',
				'link' => $this->normalizeLink($baseUrl, '/purchases?payment=Pending#recent-purchases'),
			]);
		}

		$partialDeliveries = $this->deliveryModel->countDeliveriesByStatus('Partial');
		if ($partialDeliveries > 0) {
			$append([
				'id' => 'system:partial-deliveries',
				'title' => 'Partial deliveries',
				'body' => $partialDeliveries . ' delivery' . ($partialDeliveries > 1 ? 'ies still have remaining items.' : ' still has remaining items.'),
				'level' => 'warning',
				'icon' => 'truck',
				'accent' => 'text-purple-700 bg-purple-50',
				'link' => $this->normalizeLink($baseUrl, '/deliveries?status=partial#recent-deliveries'),
			]);
		}

		$awaitingDeliveries = $this->deliveryModel->getPendingCount();
		if ($awaitingDeliveries > 0) {
			$append([
				'id' => 'system:awaiting-deliveries',
				'title' => 'Awaiting deliveries',
				'body' => $awaitingDeliveries . ' batch' . ($awaitingDeliveries > 1 ? 'es have not arrived.' : ' has not arrived.'),
				'level' => 'info',
				'icon' => 'package',
				'accent' => 'text-blue-700 bg-blue-50',
				'link' => $this->normalizeLink($baseUrl, '/deliveries?status=awaiting#awaiting-deliveries'),
			]);
		}

		return $items;
	}

	private function buildPersonalNotifications(int $userId, string $baseUrl, int $limit): array
	{
		$rows = $this->notificationModel->listLatest($userId, $limit);
		$items = [];
		foreach ($rows as $row) {
			$level = strtolower(trim((string)($row['level'] ?? 'info')));
			$message = trim((string)($row['message'] ?? ''));
			$title = $message !== '' ? $message : 'Notification';
			$items[] = [
				'id' => 'personal:' . ($row['id'] ?? uniqid()),
				'title' => $title,
				'body' => '',
				'level' => $level,
				'icon' => $this->iconForLevel($level),
				'accent' => $this->accentForLevel($level),
				'link' => $this->normalizeLink($baseUrl, $row['link'] ?? ''),
				'created_at' => $row['created_at'] ?? null,
				'type' => 'personal',
			];
		}
		return $items;
	}

	private function iconForLevel(string $level): string
	{
		return match ($level) {
			'success' => 'check-circle',
			'danger' => 'alert-octagon',
			'warning' => 'alert-triangle',
			default => 'bell-ring',
		};
	}

	private function accentForLevel(string $level): string
	{
		return match ($level) {
			'success' => 'text-green-700 bg-green-50 border border-green-200',
			'danger' => 'text-red-700 bg-red-50 border border-red-200',
			'warning' => 'text-amber-700 bg-amber-50 border border-amber-200',
			default => 'text-indigo-700 bg-indigo-50 border border-indigo-200',
		};
	}

	private function normalizeLink(string $baseUrl, ?string $link): string
	{
		$link = trim((string)$link);
		if ($link === '') {
			return '';
		}
		if (preg_match('#^https?://#i', $link)) {
			return $link;
		}
		$base = rtrim($baseUrl, '/');
		return $base . $link;
	}
}


