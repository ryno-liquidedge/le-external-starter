<?php

namespace Liquidedge\ExternalStarter;

use Liquidedge\ExternalStarter\com\Os;
use Symfony\Component\Yaml\Yaml;

class Config {

	private static mixed $config = [];
	private static bool $is_loaded = false;

	//---------------------------------------------------------------------------
	public static function load() {
		$file = Core::INSTALLER_CONFIG_FILE;
		if (!file_exists($file)) {
			Os::mkdir(dirname($file));
			file_put_contents(Core::INSTALLER_CONFIG_FILE, Yaml::dump([], 4, 2));
		}else{
			self::$config = Yaml::parseFile($file);
		}

		self::$is_loaded = true;
	}

	//---------------------------------------------------------------------------
	public static function get(string $key, $default = null) {

		if(!self::$is_loaded) self::load();

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

		if(!self::$is_loaded) self::load();

		return self::$config;
	}
	//---------------------------------------------------------------------------
}
