<?php

namespace Liquidedge\ExternalStarter\com;

class Debug {
	//---------------------------------------------------------------------------
	public static function view($var, $options = []): void {
		// options
		$options = array_merge([
			"show_detail" => false,
			"no_formatting" => false,
		], $options);

		// view method
		if (is_object($var) && method_exists($var, "__view")) {
			$var->__view();
			return;
		}

		// show variable value
		if (!$options["no_formatting"]) echo "<pre>";
		if ($options["show_detail"]) var_dump($var);
		else print_r($var);
		if (!$options["no_formatting"])echo "</pre>";
	}
	//---------------------------------------------------------------------------
}