<?php
declare(strict_types=1);

class SimplePdfBuilder
{
	private const PAGE_WIDTH = 595.28;  // A4 width in points
	private const PAGE_HEIGHT = 841.89; // A4 height in points
	private const MARGIN = 36.0;

	/** @var string[] */
	private array $pages = [];
	private string $current = '';
	private float $cursorY = 0.0;
	private float $lineHeight = 14.0;

	public function __construct()
	{
		$this->startPage();
	}

	private function startPage(): void
	{
		if ($this->current !== '') {
			$this->pages[] = $this->current;
		}
		$this->current = '';
		$this->cursorY = self::PAGE_HEIGHT - self::MARGIN;
	}

	private function ensureSpace(float $requiredHeight = null): void
	{
		$needed = $requiredHeight ?? $this->lineHeight;
		if ($this->cursorY - $needed < self::MARGIN) {
			$this->startPage();
		}
	}

	private function escape(string $text): string
	{
		return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
	}

	private function writeText(string $text, float $x, float $y, float $size): void
	{
		$escaped = $this->escape($text);
		$this->current .= sprintf("BT /F1 %.2F Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n", $size, $x, $y, $escaped);
	}

	public function addHeading(string $text, float $size = 16.0): void
	{
		$this->ensureSpace($size + 6);
		$this->writeText($text, self::MARGIN, $this->cursorY, $size);
		$this->cursorY -= ($size + 6);
	}

	public function addLine(string $text, float $size = 11.0): void
	{
		$this->ensureSpace($size + 4);
		$this->writeText($text, self::MARGIN, $this->cursorY, $size);
		$this->cursorY -= ($size + 4);
	}

	public function addSpacer(float $height = 8.0): void
	{
		$this->cursorY -= $height;
		if ($this->cursorY < self::MARGIN) {
			$this->startPage();
		}
	}

	/**
	 * @param string[] $headers
	 * @param array<int,array<int,string>> $rows
	 * @param float[] $widths
	 */
	public function addTable(array $headers, array $rows, array $widths, float $fontSize = 10.0): void
	{
		$columnCount = count($headers);
		if ($columnCount === 0) {
			return;
		}
		$positions = [];
		$x = self::MARGIN;
		$totalWidth = 0;
		$availableWidth = self::PAGE_WIDTH - (self::MARGIN * 2); // Available width between margins
		for ($i = 0; $i < $columnCount; $i++) {
			$positions[$i] = $x;
			$colWidth = $widths[$i] ?? 80;
			$totalWidth += $colWidth;
		}
		
		// Scale down widths if table exceeds available width
		if ($totalWidth > $availableWidth) {
			$scale = $availableWidth / $totalWidth;
			// Recalculate positions with scaled widths
			$x = self::MARGIN;
			$totalWidth = 0;
			for ($i = 0; $i < $columnCount; $i++) {
				$positions[$i] = $x;
				$widths[$i] = ($widths[$i] ?? 80) * $scale;
				$x += $widths[$i];
				$totalWidth += $widths[$i];
			}
		} else {
			// Calculate positions normally
			$x = self::MARGIN;
			for ($i = 0; $i < $columnCount; $i++) {
				$positions[$i] = $x;
				$x += $widths[$i] ?? 80;
			}
		}

		$rowHeight = $fontSize + 6;
		$headerHeight = $fontSize + 6;
		$cellPadding = 4; // Padding inside cells
		$neededHeight = $headerHeight + ($rowHeight * count($rows)) + 8;
		$this->ensureSpace($neededHeight);

		// Draw header row - position text lower in the cell
		$headerY = $this->cursorY - 10; // Lower the header text by 8 points
		foreach ($headers as $index => $label) {
			$this->writeText($label, $positions[$index] + $cellPadding, $headerY, $fontSize + 1);
		}
		$this->cursorY = $headerY - $headerHeight;
		$headerBottomY = $this->cursorY;
		$firstRowY = $this->cursorY;

		// Draw data rows - position text lower in each cell
		foreach ($rows as $row) {
			$this->ensureSpace($rowHeight);
			$rowY = $this->cursorY - 8; // Lower each row text by 6 points
			foreach ($row as $index => $cell) {
				// Calculate max characters based on column width (approximate 6 points per character)
				$colWidth = $widths[$index] ?? 80;
				$maxChars = (int)(($colWidth - $cellPadding * 2) / 6);
				$truncated = $this->truncate($cell, $maxChars);
				$this->writeText($truncated, $positions[$index] + $cellPadding, $rowY, $fontSize);
			}
			$this->cursorY -= $rowHeight;
		}
		
		// Calculate table borders to contain the text
		// Top border: above header text (account for font ascenders)
		$headerFontSize = $fontSize + 1;
		$tableTopY = round($headerY + ($headerFontSize * 0.7) + 2, 2);
		// Bottom border: below last row text
		// Last row text is at: cursorY (after all decrements) + rowHeight - 6 (lowered position)
		$lastRowTextY = $this->cursorY + $rowHeight - 6;
		$tableBottomY = round($lastRowTextY - ($fontSize * 0.3) - 2, 2);
		
		// Draw borders - use miter joins and butt caps for perfect grid
		$this->current .= "0.5 w\n"; // Set line width to 0.5 points
		$this->current .= "10 M\n"; // Set miter limit
		$this->current .= "0 J\n"; // Miter join for sharp corners
		$this->current .= "0 j\n"; // Butt cap for clean line ends
		
		$tableLeftX = round(self::MARGIN, 2);
		$tableRightX = round(self::MARGIN + $totalWidth, 2);
		// $tableTopY and $tableBottomY already calculated and rounded above
		
		// Calculate all Y positions for horizontal lines (rounded for precision)
		// Header separator is between header and first data row
		$headerBottomY = round($headerBottomY, 2); // Already calculated above
		$horizontalLines = [
			round($tableTopY, 2),      // Top border (above header)
			round($headerBottomY, 2),   // Header separator (below header row)
		];
		// Row separators (between data rows)
		$currentY = round($firstRowY, 2);
		foreach ($rows as $row) {
			$horizontalLines[] = round($currentY, 2);
			$currentY = round($currentY - $rowHeight, 2);
		}
		$horizontalLines[] = round($tableBottomY, 2); // Bottom border (below last row)
		
		// Calculate all X positions for vertical lines (rounded for precision)
		$verticalLines = [round($tableLeftX, 2)]; // Left border
		$xPos = round(self::MARGIN, 2);
		for ($i = 1; $i < $columnCount; $i++) {
			$xPos = round($xPos + ($widths[$i - 1] ?? 80), 2);
			$verticalLines[] = round($xPos, 2);
		}
		$verticalLines[] = round($tableRightX, 2); // Right border
		
		// Draw all horizontal lines first (ensures they connect with verticals)
		foreach ($horizontalLines as $y) {
			$this->current .= sprintf("%.2F %.2F m\n", $tableLeftX, $y);
			$this->current .= sprintf("%.2F %.2F l\n", $tableRightX, $y);
			$this->current .= "S\n";
		}
		
		// Draw all vertical lines (they will connect with horizontals)
		foreach ($verticalLines as $x) {
			$this->current .= sprintf("%.2F %.2F m\n", $x, $tableTopY);
			$this->current .= sprintf("%.2F %.2F l\n", $x, $tableBottomY);
			$this->current .= "S\n";
		}
		
		// Reset line settings to default
		$this->current .= "1 w\n"; // Reset line width
		$this->current .= "0 J\n"; // Reset line join style
		$this->current .= "0 j\n"; // Reset line cap style
		
		$this->cursorY -= 6;
	}
	
	private function drawLine(float $x1, float $y1, float $x2, float $y2): void
	{
		$this->current .= sprintf("%.2F %.2F m\n", $x1, $y1);
		$this->current .= sprintf("%.2F %.2F l\n", $x2, $y2);
		$this->current .= "S\n";
	}
	
	private function drawRectangle(float $x, float $y, float $width, float $height): void
	{
		// Draw rectangle: bottom-left -> bottom-right -> top-right -> top-left -> close and stroke
		$this->current .= sprintf("%.2F %.2F m\n", $x, $y);
		$this->current .= sprintf("%.2F %.2F l\n", $x + $width, $y);
		$this->current .= sprintf("%.2F %.2F l\n", $x + $width, $y + $height);
		$this->current .= sprintf("%.2F %.2F l\n", $x, $y + $height);
		$this->current .= "s\n"; // s = close path and stroke
	}

	private function truncate(string $text, int $limit): string
	{
		if (mb_strlen($text, 'UTF-8') <= $limit) {
			return $text;
		}
		return rtrim(mb_substr($text, 0, $limit - 1, 'UTF-8')) . '…';
	}

	private function finalizePages(): void
	{
		if ($this->current !== '') {
			$this->pages[] = $this->current;
			$this->current = '';
		}
		if (empty($this->pages)) {
			$this->startPage();
			$this->pages[] = $this->current;
			$this->current = '';
		}
	}

	public function toPdfString(): string
	{
		$this->finalizePages();
		$objects = [];
		$objects[1] = ''; // catalog placeholder
		$objects[2] = ''; // pages placeholder
		$objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
		$objectId = 4;
		$contentIds = [];
		foreach ($this->pages as $content) {
			$contentIds[] = $objectId;
			$objects[$objectId] = sprintf("<< /Length %d >>\nstream\n%s\nendstream", strlen($content), $content);
			$objectId++;
		}
		$pageIds = [];
		foreach ($contentIds as $contentId) {
			$pageIds[] = $objectId;
			$objects[$objectId] = sprintf(
				"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R >> >> /Contents %d 0 R >>",
				self::PAGE_WIDTH,
				self::PAGE_HEIGHT,
				$contentId
			);
			$objectId++;
		}
		$kids = implode(' ', array_map(static fn($id) => $id . ' 0 R', $pageIds));
		$objects[2] = sprintf("<< /Type /Pages /Kids [%s] /Count %d >>", $kids, count($pageIds));
		$objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

		$pdf = "%PDF-1.4\n";
		$offsets = [0 => 0];
		for ($i = 1; $i <= $objectId - 1; $i++) {
			$offsets[$i] = strlen($pdf);
			$pdf .= $i . " 0 obj\n" . ($objects[$i] ?? '<< >>') . "\nendobj\n";
		}
		$startXref = strlen($pdf);
		$pdf .= "xref\n0 $objectId\n";
		$pdf .= "0000000000 65535 f \n";
		for ($i = 1; $i <= $objectId - 1; $i++) {
			$pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
		}
		$pdf .= "trailer << /Size $objectId /Root 1 0 R >>\nstartxref\n$startXref\n%%EOF";
		return $pdf;
	}
}


