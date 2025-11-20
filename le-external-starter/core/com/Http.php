<?php

namespace Liquidedge\ExternalStarter\com;

class Http {

	//--------------------------------------------------------------------------------
	public static function get_host(): string {
		$http = (self::is_https() ? "https" : "http");
		return "{$http}://".($_SERVER["HTTP_HOST"] ?? "localhost");
	}
	//--------------------------------------------------------------------------------
	public static function is_https(): bool {
		// check https server parameter ('off' on iis)
		if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") return true;

		// check https port
		if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443) return true;

		// check protocol
		if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") return true;

		// done
		return false;
	}
}