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
		for ($i = 0; $i < $columnCount; $i++) {
			$positions[$i] = $x;
			$x += $widths[$i] ?? 80;
		}

		$neededHeight = ($fontSize + 6) * (max(count($rows), 1) + 1) + 8;
		$this->ensureSpace($neededHeight);

		$headerY = $this->cursorY;
		foreach ($headers as $index => $label) {
			$this->writeText($label, $positions[$index], $headerY, $fontSize + 1);
		}
		$this->cursorY = $headerY - ($fontSize + 6);

		foreach ($rows as $row) {
			$this->ensureSpace($fontSize + 6);
			$rowY = $this->cursorY;
			foreach ($row as $index => $cell) {
				$truncated = $this->truncate($cell, 60);
				$this->writeText($truncated, $positions[$index] ?? self::MARGIN, $rowY, $fontSize);
			}
			$this->cursorY -= ($fontSize + 6);
		}
		$this->cursorY -= 6;
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


