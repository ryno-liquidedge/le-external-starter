<?php

namespace Liquidedge\ExternalStarter\com;

class Os {
	//--------------------------------------------------------------------------------
    public static function mkdir($path): void {
    	if (!file_exists($path)) {
    		mkdir($path, 0777, true);
		}
    }
	//--------------------------------------------------------------------------------
	public static function pathToUrl($filePath): string {
		// Normalize slashes
		$filePath = str_replace('\\', '/', $filePath);

		// Look for 'wwwroot' in the path
		$pos = stripos($filePath, 'wwwroot');
		if ($pos === false) {
			throw new \Exception('wwwroot not found in path');
		}

		// Get path relative to wwwroot
		$relativePath = substr($filePath, $pos + strlen('wwwroot/'));

		// Convert to URL
		return 'http://localhost/' . $relativePath;
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