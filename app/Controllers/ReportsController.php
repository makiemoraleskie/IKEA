<?php
declare(strict_types=1);

class ReportsController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$ingredientModel = new Ingredient();
		$ingredients = $ingredientModel->all();
		$filters = [
			'date_from' => trim((string)($_GET['date_from'] ?? '')),
			'date_to' => trim((string)($_GET['date_to'] ?? '')),
			'supplier' => trim((string)($_GET['supplier'] ?? '')),
			'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
		];
		$model = new Reports();
		$purchases = $model->getPurchases($filters);
		$daily = $model->getDailyTotals($filters);
		$this->render('reports/index.php', [
			'ingredients' => $ingredients,
			'purchases' => $purchases,
			'daily' => $daily,
			'filters' => $filters,
		]);
	}

	public function pdf(): void
	{
		Auth::requireRole(['Owner','Manager']);
		$filters = [
			'date_from' => trim((string)($_GET['date_from'] ?? '')),
			'date_to' => trim((string)($_GET['date_to'] ?? '')),
			'supplier' => trim((string)($_GET['supplier'] ?? '')),
			'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
		];
		$model = new Reports();
		$purchases = $model->getPurchases($filters);

		// Basic HTML to PDF via Dompdf if available
		ob_start();
		include BASE_PATH . '/src/views/reports/pdf.php';
		$html = ob_get_clean();

		if (class_exists('Dompdf\\Dompdf')) {
			$dompdf = new Dompdf\Dompdf();
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->render();
			$dompdf->stream('report.pdf', ['Attachment' => false]);
			return;
		}

		// Fallback: render HTML directly
		header('Content-Type: text/html; charset=utf-8');
		echo $html;
	}
}


