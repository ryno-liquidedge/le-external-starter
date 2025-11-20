<?php

namespace Liquidedge\ExternalStarter\install\installer;

use Liquidedge\ExternalStarter\com\Http;
use Liquidedge\ExternalStarter\com\Os;
use Liquidedge\ExternalStarter\Config;
use Liquidedge\ExternalStarter\Core;
use Liquidedge\ExternalStarter\install\makers\MakeInstance;

class InstallInstance {

	//--------------------------------------------------------------------------------
	// properties
	//--------------------------------------------------------------------------------
	protected mixed $your_first_name = null;
	protected mixed $class_name = null;
	protected mixed $options = null;
	//--------------------------------------------------------------------------------
	// magic
	//--------------------------------------------------------------------------------
	public function __construct($options = []) {
		// options
		$options = array_merge([
			"your_first_name" => Config::get("per_firstname"),
		], $options);

		// init
		$this->your_first_name = $options["your_first_name"];
		$this->options = $options;

		// validate
		if (!$this->your_first_name) throw new \Exception("Missing your_first_name option.");;

		// class name
		$this->class_name = strtolower(preg_replace("/[^a-z]/i", "", $this->your_first_name));

	}
	//--------------------------------------------------------------------------------
	// functions
	//--------------------------------------------------------------------------------
	public function is_installed(): bool {
		return file_exists(Core::DIR_NOVA_APP."/acc/core.instance/acc.core.instance.{$this->class_name}.php");
	}
	//--------------------------------------------------------------------------------
	public function get_my_id() {
		$computer_name  = (empty($_SERVER["COMPUTERNAME"]) ? $_SERVER["SERVER_ADDR"] : $_SERVER["COMPUTERNAME"]);
		return strtr("{$computer_name}/".realpath(Core::DIR_NOVA_ROOT), ["\\" => "/"]);
	}
	//--------------------------------------------------------------------------------
	public function install() {
		// folder
		$path = Core::DIR_NOVA_APP."/acc/core.instance";
		Os::mkdir($path);

		$file_path = "{$path}/acc.core.instance.{$this->class_name}.php";

		dump(file_get_contents($file_path));
		$file_contents = file_get_contents($file_path);
		$file_contents = str_replace('extends \com\core\intf\instance', 'extends \acc\core\instance\intf\instance', $file_contents);

		file_put_contents($file_path, $file_contents);

	}
	//--------------------------------------------------------------------------------
}

