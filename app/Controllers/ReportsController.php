<?php
declare(strict_types=1);

class ReportsController extends BaseController
{
	public function index(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$ingredients = $this->getIngredients();
		$enabledSections = Settings::reportSectionsEnabled();
		$includePurchase = in_array('purchase', $enabledSections, true);
		$includeConsumption = in_array('consumption', $enabledSections, true);

		$purchaseData = $includePurchase ? $this->buildSectionData('purchase') : [
			'section' => 'purchase',
			'filters' => $this->buildPurchaseFilters(),
			'purchases' => [],
			'daily' => [],
			'show_costs' => Settings::costVisibleForRole(Auth::role()),
		];
		$consumptionData = $includeConsumption ? $this->buildSectionData('consumption') : [
			'section' => 'consumption',
			'filters' => $this->buildConsumptionFilters(),
			'consumption' => [],
			'show_costs' => false,
		];

		$this->render('reports/index.php', [
			'ingredients' => $ingredients,
			'categoriesList' => $this->extractCategories($ingredients),
			'usageStatuses' => $this->usageStatuses(),
			'purchaseFilters' => $purchaseData['filters'],
			'consumptionFilters' => $consumptionData['filters'],
			'purchases' => $purchaseData['purchases'] ?? [],
			'daily' => $purchaseData['daily'] ?? [],
			'consumption' => $consumptionData['consumption'] ?? [],
			'canViewCosts' => Settings::costVisibleForRole(Auth::role()),
			'sectionsEnabled' => [
				'purchase' => $includePurchase,
				'consumption' => $includeConsumption,
			],
		]);
	}

	public function pdf(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$section = $this->resolveSection();
		if (!in_array($section, Settings::reportSectionsEnabled(), true)) {
			http_response_code(403);
			echo 'This report section is disabled.';
			return;
		}
		$data = $this->buildSectionData($section);

		if (class_exists('Dompdf\\Dompdf')) {
			$html = $this->renderPdfView($data);
			$dompdf = new Dompdf\Dompdf();
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->render();
			$filename = $section . '-report-' . date('Ymd_His') . '.pdf';
			$dompdf->stream($filename, ['Attachment' => true]);
			return;
		}

		$this->streamSimplePdf($data);
	}

	public function excel(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$section = $this->resolveSection();
		if (!in_array($section, Settings::reportSectionsEnabled(), true)) {
			http_response_code(403);
			echo 'This report section is disabled.';
			return;
		}
		$data = $this->buildSectionData($section);
		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $section . '-report-' . date('Ymd_His') . '.xls"');
		echo $this->renderExcel($data);
		exit;
	}

	public function csv(): void
	{
		Auth::requireRole(['Owner','Manager','Stock Handler']);
		$section = $this->resolveSection();
		if (!in_array($section, Settings::reportSectionsEnabled(), true)) {
			http_response_code(403);
			echo 'This report section is disabled.';
			return;
		}
		$data = $this->buildSectionData($section);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $section . '-report-' . date('Ymd_His') . '.csv"');
		$this->outputCsv($data);
		exit;
	}

	private function buildSectionData(string $section): array
	{
		$model = new Reports();
		$showCosts = Settings::costVisibleForRole(Auth::role());
		if ($section === 'consumption') {
			$filters = $this->buildConsumptionFilters();
			$consumption = $model->getIngredientConsumption($filters);
			return [
				'section' => 'consumption',
				'filters' => $filters,
				'consumption' => $consumption,
				'show_costs' => $showCosts,
			];
		}

		$filters = $this->buildPurchaseFilters();
		return [
			'section' => 'purchase',
			'filters' => $filters,
			'purchases' => $model->getPurchases($filters),
			'daily' => $model->getDailyTotals($filters),
			'show_costs' => $showCosts,
		];
	}

	private function buildPurchaseFilters(): array
	{
		$paymentRaw = $_GET['p_payment_status'] ?? null;
		$paymentStatus = $paymentRaw !== null ? trim((string)$paymentRaw) : 'Paid';
		$itemId = isset($_GET['p_item_id']) && $_GET['p_item_id'] !== '' ? (int)$_GET['p_item_id'] : null;
		$dateFrom = trim((string)($_GET['p_date_from'] ?? ''));
		$archiveDays = Settings::archiveDays();
		if ($dateFrom === '' && $archiveDays > 0) {
			$dateFrom = date('Y-m-d', strtotime('-' . $archiveDays . ' days'));
		}
		return [
			'date_from' => $dateFrom,
			'date_to' => trim((string)($_GET['p_date_to'] ?? '')),
			'supplier' => trim((string)($_GET['p_supplier'] ?? '')),
			'item_id' => $itemId,
			'payment_status' => $paymentStatus,
			'category' => trim((string)($_GET['p_category'] ?? '')),
		];
	}

	private function buildConsumptionFilters(): array
	{
		return [
			'date_from' => trim((string)($_GET['c_date_from'] ?? '')),
			'date_to' => trim((string)($_GET['c_date_to'] ?? '')),
			'category' => trim((string)($_GET['c_category'] ?? '')),
			'usage_status' => strtolower(trim((string)($_GET['c_usage_status'] ?? ''))),
		];
	}

	private function resolveSection(): string
	{
		$section = strtolower(trim((string)($_GET['section'] ?? 'purchase')));
		return in_array($section, ['purchase','consumption'], true) ? $section : 'purchase';
	}

	private function getIngredients(): array
	{
		$ingredientModel = new Ingredient();
		return $ingredientModel->all();
	}

	private function extractCategories(array $ingredients): array
	{
		$categories = array_map(static function ($ingredient) {
			return trim((string)($ingredient['category'] ?? ''));
		}, $ingredients);
		$categories = array_values(array_filter(array_unique($categories)));
		sort($categories, SORT_NATURAL | SORT_FLAG_CASE);
		return $categories;
	}

	private function usageStatuses(): array
	{
		return [
			'used' => 'Used',
			'expired' => 'Expired',
			'transferred' => 'Transferred',
		];
	}

	private function renderExcel(array $data): string
	{
		$section = $data['section'] ?? 'purchase';
		$filters = $data['filters'];
		$purchases = $data['purchases'] ?? [];
		$consumption = $data['consumption'] ?? [];
		$showCosts = !empty($data['show_costs']);
		ob_start();
		if ($section === 'consumption') {
			?>
			<table border="1" cellpadding="4" cellspacing="0">
				<tr><th colspan="5">Ingredient Consumption</th></tr>
				<tr>
					<th>Ingredient</th>
					<th>Category</th>
					<th>Total Used (Base)</th>
					<th>Unit</th>
					<th>Converted Display</th>
				</tr>
				<?php foreach ($consumption as $row): 
					$baseQty = (float)($row['total_quantity'] ?? 0);
					$displayUnit = $row['display_unit'] ?? '';
					$displayFactor = (float)($row['display_factor'] ?? 1);
					$converted = ($displayUnit !== '' && $displayFactor > 0 && abs($displayFactor - 1) > 0.00001)
						? number_format($baseQty / $displayFactor, 2) . ' ' . $displayUnit
						: '';
				?>
					<tr>
						<td><?php echo htmlspecialchars($row['name']); ?></td>
						<td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
						<td><?php echo number_format($baseQty, 2); ?></td>
						<td><?php echo htmlspecialchars($row['unit']); ?></td>
						<td><?php echo htmlspecialchars($converted); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php
		} else {
			?>
			<table border="1" cellpadding="4" cellspacing="0">
				<tr><th colspan="<?php echo $showCosts ? 5 : 4; ?>">Purchases Report</th></tr>
				<tr>
					<th>Date</th>
					<th>Item</th>
					<th>Supplier</th>
					<th>Quantity</th>
					<?php if ($showCosts): ?>
					<th>Cost</th>
					<?php endif; ?>
				</tr>
				<?php foreach ($purchases as $row): ?>
					<tr>
						<td><?php echo htmlspecialchars($row['date_purchased']); ?></td>
						<td><?php echo htmlspecialchars($row['item_name']); ?></td>
						<td><?php echo htmlspecialchars($row['supplier']); ?></td>
						<td><?php echo number_format((float)$row['quantity'], 2); ?></td>
						<?php if ($showCosts): ?>
						<td><?php echo number_format((float)$row['cost'], 2); ?></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php
		}
		return ob_get_clean();
	}

	private function outputCsv(array $data): void
	{
		$section = $data['section'] ?? 'purchase';
		$purchases = $data['purchases'] ?? [];
		$consumption = $data['consumption'] ?? [];
		$showCosts = !empty($data['show_costs']);
		$handle = fopen('php://output', 'w');
		if ($section === 'consumption') {
			fputcsv($handle, ['Ingredient Consumption']);
			fputcsv($handle, ['Ingredient', 'Category', 'Total (Base)', 'Unit', 'Converted Qty', 'Display Unit']);
			foreach ($consumption as $row) {
				$baseQty = (float)($row['total_quantity'] ?? 0);
				$displayUnit = $row['display_unit'] ?? '';
				$displayFactor = (float)($row['display_factor'] ?? 1);
				$convertedQty = ($displayUnit !== '' && $displayFactor > 0 && abs($displayFactor - 1) > 0.00001)
					? number_format($baseQty / $displayFactor, 2)
					: '';
				fputcsv($handle, [
					$row['name'],
					$row['category'] ?? '',
					number_format($baseQty, 2),
					$row['unit'],
					$convertedQty,
					$displayUnit,
				]);
			}
		} else {
			fputcsv($handle, ['Purchases Report']);
			$headers = ['Date', 'Item', 'Supplier', 'Quantity'];
			if ($showCosts) {
				$headers[] = 'Cost';
			}
			fputcsv($handle, $headers);
			foreach ($purchases as $row) {
				$line = [
					$row['date_purchased'],
					$row['item_name'],
					$row['supplier'],
					number_format((float)$row['quantity'], 2),
				];
				if ($showCosts) {
					$line[] = number_format((float)$row['cost'], 2);
				}
				fputcsv($handle, $line);
			}
		}
		fclose($handle);
	}

	private function renderPdfView(array $data): string
	{
		$section = $data['section'] ?? 'purchase';
		$filters = $data['filters'];
		$purchases = $data['purchases'] ?? [];
		$consumption = $data['consumption'] ?? [];
		$showCosts = !empty($data['show_costs']);
		ob_start();
		include BASE_PATH . '/resources/views/reports/pdf.php';
		return ob_get_clean();
	}

	private function streamSimplePdf(array $data): void
	{
		require_once BASE_PATH . '/app/Utils/SimplePdfBuilder.php';
		$builder = new SimplePdfBuilder();
		$section = $data['section'] ?? 'purchase';
		$filters = $data['filters'];
		$purchases = $data['purchases'] ?? [];
		$consumption = $data['consumption'] ?? [];
		$showCosts = !empty($data['show_costs']);

		$builder->addHeading('IKEA Commissary Report (' . ucfirst($section) . ')', 18);
		$builder->addLine('Generated on: ' . date('M j, Y g:i A'));
		$builder->addSpacer(4);
		$builder->addLine('Filters:', 12);
		$builder->addLine(' - Date Range: ' . ($filters['date_from'] ?? 'Any') . ' → ' . ($filters['date_to'] ?? 'Any'));
		if ($section === 'purchase') {
			$builder->addLine(' - Supplier: ' . ($filters['supplier'] ?? 'All'));
			$builder->addLine(' - Category: ' . ($filters['category'] ?? 'All'));
			$builder->addLine(' - Status: ' . ($filters['payment_status'] ?? 'All'));
		} else {
			$builder->addLine(' - Category: ' . ($filters['category'] ?? 'All'));
			$builder->addLine(' - Usage Status: ' . ($filters['usage_status'] ?? 'All'));
		}
		$builder->addSpacer(10);

		if ($section === 'consumption') {
			$builder->addHeading('Ingredient Consumption');
			if (!empty($consumption)) {
				$rows = [];
				foreach ($consumption as $row) {
					$baseQty = (float)($row['total_quantity'] ?? 0);
					$displayUnit = $row['display_unit'] ?? '';
					$displayFactor = (float)($row['display_factor'] ?? 1);
					$converted = ($displayUnit !== '' && $displayFactor > 0 && abs($displayFactor - 1) > 0.00001)
						? number_format($baseQty / $displayFactor, 2) . ' ' . $displayUnit
						: '—';
					$rows[] = [
						$row['name'] ?? '',
						$row['category'] ?? '',
						number_format($baseQty, 2) . ' ' . ($row['unit'] ?? ''),
						$converted,
					];
				}
				$builder->addTable(
					['Ingredient', 'Category', 'Total Used', 'Display'],
					$rows,
					[180, 120, 120, 120]
				);
			} else {
				$builder->addLine('No consumption records for the selected filters.');
			}
		} else {
			$builder->addHeading('Purchases');
			if (!empty($purchases)) {
				$rows = [];
				foreach ($purchases as $row) {
					$currentRow = [
						date('M j', strtotime((string)$row['date_purchased'])),
						$row['item_name'] ?? '',
						$row['supplier'] ?? '',
						number_format((float)$row['quantity'], 2),
					];
					if ($showCosts) {
						$currentRow[] = '₱' . number_format((float)$row['cost'], 2);
					}
					$rows[] = $currentRow;
				}
				$headers = ['Date', 'Item', 'Supplier', 'Qty'];
				$widths = [70, 160, 150, 70];
				if ($showCosts) {
					$headers[] = 'Cost';
					$widths[] = 70;
				}
				$builder->addTable(
					$headers,
					$rows,
					$widths
				);
			} else {
				$builder->addLine('No purchases match the selected filters.');
			}
		}

		$pdf = $builder->toPdfString();
		$filename = $section . '-report-' . date('Ymd_His') . '.pdf';
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . strlen($pdf));
		echo $pdf;
	}
}

