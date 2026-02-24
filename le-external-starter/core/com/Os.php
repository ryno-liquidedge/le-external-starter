<?php

namespace Liquidedge\ExternalStarter\com;

use Liquidedge\ExternalStarter\Config;

class Os {
	//--------------------------------------------------------------------------------
    public static function mkdir($path): void {
    	if (!file_exists($path)) {
    		mkdir($path, 0777, true);
		}
    }
	//--------------------------------------------------------------------------------
	public static function pathToUrl($filePath): string {

		Config::load();
		$site_url = Config::get('site_url');
		if(!$site_url) throw new \Exception("Site URL not configured correctly");
		if(!str_ends_with($site_url, "/")) $site_url .= "/";

		// Normalize slashes
		$filePath = str_replace('\\', '/', $filePath);
		$parts = explode("/", $filePath);
		$file = end($parts);

		// Convert to URL
		return $site_url . $file;
	}
	//--------------------------------------------------------------------------------
	public static function copy_folder($source, $destination): bool {
		// Ensure source exists
		if (!is_dir($source)) {
			return false;
		}

		// Create destination if not exists
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		// Open the source directory
		$dir = opendir($source);

		while (($file = readdir($dir)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			$src = $source . '/' . $file;
			$dst = $destination . '/' . $file;

			// If it's a directory → recurse
			if (is_dir($src)) {
				self::copy_folder($src, $dst);
			} else {
				copy($src, $dst);
			}
		}

		closedir($dir);

		return true;
	}

	//--------------------------------------------------------------------------------
}