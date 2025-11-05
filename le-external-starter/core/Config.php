<?php

namespace Liquidedge\ExternalStarter;

use Symfony\Component\Yaml\Yaml;

class Config {

	private static array $config = [];

	//---------------------------------------------------------------------------
	public static function load(): void {
		$file = Core::INSTALLER_CONFIG_FILE;
		if (!file_exists($file)) {
			throw new \RuntimeException('Config file not found. Run composer create-project first.');
		}

		self::$config = Yaml::parseFile($file);
	}

	//---------------------------------------------------------------------------
	public static function get(string $key, $default = null) {
		$keys = explode('.', $key);
		$value = self::$config;
		foreach ($keys as $k) {
			if (!isset($value[$k])) {
				return $default;
			}
			$value = $value[$k];
		}
		return $value;
	}

	//---------------------------------------------------------------------------
	public static function all(): array {
		return self::$config;
	}
	//---------------------------------------------------------------------------
}
