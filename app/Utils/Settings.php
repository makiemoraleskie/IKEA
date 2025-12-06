<?php
declare(strict_types=1);

class Settings
{
	private static ?array $cache = null;

	private static function ensureLoaded(): void
	{
		if (self::$cache !== null) {
			return;
		}
		$model = new Setting();
		self::$cache = $model->all();
	}

	private static function persist(string $key, $value, ?int $userId = null): void
	{
		$model = new Setting();
		if (is_array($value) || is_object($value)) {
			$value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		} elseif ($value === null) {
			$model->delete($key);
			unset(self::$cache[$key]);
			return;
		} else {
			$value = (string)$value;
		}
		$model->set($key, $value, $userId);
		self::$cache[$key] = $value;
	}

	public static function get(string $key, $default = null)
	{
		self::ensureLoaded();
		if (!array_key_exists($key, self::$cache)) {
			return $default;
		}

		return self::$cache[$key];
	}

	public static function getJson(string $key, $default = [])
	{
		$value = self::get($key);
		if ($value === null || $value === '') {
			return $default;
		}
		$decoded = json_decode((string)$value, true);
		return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : $default;
	}

	public static function set(string $key, $value, ?int $userId = null): void
	{
		self::ensureLoaded();
		self::persist($key, $value, $userId);
	}

	public static function setJson(string $key, array $value, ?int $userId = null): void
	{
		self::ensureLoaded();
		self::persist($key, $value, $userId);
	}

	public static function forget(string $key): void
	{
		self::ensureLoaded();
		$model = new Setting();
		$model->delete($key);
		unset(self::$cache[$key]);
	}

	public static function costVisibleForRole(?string $role): bool
	{
		if ($role === null) {
			return false;
		}
		$hidden = self::getJson('security.cost_hidden_roles', []);
		return !in_array($role, $hidden, true);
	}

	public static function dashboardWidgetsForRole(?string $role): array
	{
		$role = $role ?? '';
		$map = self::getJson('display.dashboard_widgets', []);
		if (isset($map[$role]) && is_array($map[$role])) {
			return $map[$role];
		}
		return $map['default'] ?? ['low_stock','pending_requests','pending_payments','partial_deliveries','pending_deliveries','inventory_value'];
	}

	public static function reportSectionsEnabled(): array
	{
		$sections = self::getJson('reporting.enabled_sections', []);
		if (empty($sections)) {
			return ['purchase','consumption'];
		}
		return $sections;
	}

	public static function archiveDays(): int
	{
		return (int)self::get('reporting.archive_days', 0);
	}

	public static function companyName(): string
	{
		return (string)(self::get('display.company_name', 'IKEA Commissary System'));
	}

	public static function companyTagline(): string
	{
		return (string)(self::get('display.company_tagline', 'Operations Console'));
	}

	public static function logoPath(): ?string
	{
		$path = self::get('display.logo_path');
		return $path ? (string)$path : null;
	}

	public static function themeDefault(): string
	{
		$theme = strtolower((string)self::get('display.theme_default', 'system'));
		return in_array($theme, ['light','dark','system'], true) ? $theme : 'system';
	}
}


