<?php
declare(strict_types=1);

class NotificationController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Kitchen Staff','Stock Handler','Manager','Owner','Purchaser']);
		$userId = Auth::id() ?? 0;
		$model = new Notification();
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
				http_response_code(400);
				echo 'Invalid CSRF token';
				return;
			}
			$action = $_POST['action'] ?? '';
			if ($action === 'clear') {
				$model->deleteNonCritical($userId);
				$_SESSION['flash_notifications'] = ['type' => 'success', 'text' => 'All notifications cleared.'];
			} elseif ($action === 'mark') {
				$model->markAllRead($userId);
				$_SESSION['flash_notifications'] = ['type' => 'success', 'text' => 'All notifications marked as read.'];
			}
			$this->redirect('/notifications');
			return;
		}
		$feedBuilder = new NotificationFeed();
		$notifications = $feedBuilder->compose(Auth::user(), $userId, defined('BASE_URL') ? BASE_URL : '', 50, true);
		$this->render('notifications/index.php', [
			'notifications' => $notifications,
			'flash' => $_SESSION['flash_notifications'] ?? null,
		]);
		unset($_SESSION['flash_notifications']);
	}
}


