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
	protected mixed $system = null;
	protected mixed $options = null;
	//--------------------------------------------------------------------------------
	// magic
	//--------------------------------------------------------------------------------
	public function __construct($options = []) {
		// options
		$options = array_merge([
			"your_first_name" => Config::get("per_firstname"),

			".db_type" => Config::get("db_type"),
			".db_host" => Config::get("db_hostname"),
			".db_name" => Config::get("db_name"),
			".db_username" => Config::get("db_username"),
			".db_password" => Config::get("db_password"),

			".email_force_to" => Config::get("per_email"),
			".email_support_to" => Config::get("per_email"),
			".email_from_name" => Config::get("company_name"),

			".company" => Config::get("company_name"),
			".website" => Config::get("website"),
			".system" => Config::get("system_name"),

			".sms_force_to" => "",
			".sms_price" => 0.25,

			".session_ssl" => 0,

			".title" => "{\$this->company} | {\$this->system} | {\$this->environment_name}",

			".php_exe" => Config::get("php_exe_path"),
			".php_ini" => Config::get("php_ini_path"),

		], $options);

		// init
		$this->your_first_name = $options["your_first_name"];
		$this->system = $options[".system"];
		$this->options = $options;

		// validate
		if (!$this->your_first_name) throw new \Exception("Missing your_first_name option.");;
		if (!$this->system) throw new \Exception("Missing .system option.");

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

		// init
		$name = ucwords($this->your_first_name)." Work PC";
		$code = strtoupper(substr($this->your_first_name, 0, 1));
		$session_name = strtoupper(preg_replace("/[^a-z]/i", "", $this->system))."dev";
		$sms_system_id = strtolower(substr($session_name, 0, 2).$code);

		// instance
		$instance = new MakeInstance();
		$instance->set_classname($this->class_name);
		$instance->set_instance_id($this->get_my_id());
		$instance->set_instance_name($name);
		$instance->set_instance_code($code);
		$instance->set_instance_url(Http::get_host());
		$instance->set_vars([
			"db_type" => $this->options[".db_type"],
			"db_host" => $this->options[".db_host"],
			"db_name" => $this->options[".db_name"],
			"db_username" => $this->options[".db_username"],
			"db_password" => $this->options[".db_password"],

			"db_charset" => "ISO-8859-1",
			"db_enabled" => false,

			"email_smtp" => "",
			"email_host" => "",
			"email_from" => "",
			"email_username" => "",
			"email_password" => "",
			"email_force_to" => $this->options[".email_force_to"],
			"email_support_to" => $this->options[".email_support_to"],
			"email_from_name" => $this->options[".email_from_name"],

			"company" => $this->options[".company"],
			"website" => $this->options[".website"],
			"system" => $this->system,

			"sms_force_to" => $this->options[".sms_force_to"],
			"sms_system_id" => $sms_system_id,
			"sms_price" => 0.25,

			"session_name" => $session_name,

			"title" => "{$this->options[".company"]} | {$this->system} | DEV",

			"php_exe" => $this->options[".php_exe"],
			"php_ini" => $this->options[".php_ini"],
		]);

		// save file
		$file_path = "{$path}/acc.core.instance.{$this->class_name}.php";
		Os::mkdir(dirname($file_path));
		file_put_contents($file_path, $instance->build());
	}
	//--------------------------------------------------------------------------------
}

