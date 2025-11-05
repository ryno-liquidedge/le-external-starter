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
}