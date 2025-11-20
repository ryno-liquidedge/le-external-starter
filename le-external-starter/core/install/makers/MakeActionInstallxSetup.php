<?php

namespace Liquidedge\ExternalStarter\install\makers;

class MakeActionInstallxSetup {
//---------------------------------------------------------------------------
	public function run(): void {

		$code = <<<PHP
<?php

namespace action\install;

/**
 * Action Class.
 *
 * @author Liquid Edge Solutions
 * @copyright Copyright Liquid Edge Solutions. All rights reserved.
 */
class xsetup implements \com\router\int\action {
	//--------------------------------------------------------------------------------
	use \com\router\tra\action;
	//--------------------------------------------------------------------------------
	// magic
	//--------------------------------------------------------------------------------
	protected function __construct() {
		\core::\$app->set_section(\acc\core\section\api::make());
	}
	//--------------------------------------------------------------------------------
	public function auth() {
		return file_exists(\core::\$folders->get_root()."/install.php");
	}
	//--------------------------------------------------------------------------------
	public function run() {
		// params
		\$your_first_name = \$this->request->get("your_first_name", \com\data::TYPE_STRING);

		\$db_type = \$this->request->get("db_type", \com\data::TYPE_STRING);
		\$db_host = \$this->request->get("db_host", \com\data::TYPE_STRING);
		\$db_name = \$this->request->get("db_name", \com\data::TYPE_DBIDENT);
		\$db_username = \$this->request->get("db_username", \com\data::TYPE_STRING);
		\$db_password = \$this->request->get("db_password", \com\data::TYPE_STRING);

		\$email_force_to = \$this->request->get("email_force_to", \com\data::TYPE_EMAIL);

		\$company = \$this->request->get("company", \com\data::TYPE_EMAIL);
		\$system = \$this->request->get("system", \com\data::TYPE_EMAIL);
		\$website = \$this->request->get("website", \com\data::TYPE_EMAIL);

		\$sms_force_to = \$this->request->get("sms_force_to", \com\data::TYPE_STRING);

		\$php_exe = \$this->request->get("php_exe", \com\data::TYPE_STRING);
		\$php_ini = \$this->request->get("php_ini", \com\data::TYPE_STRING);

		// install instance
		\$installer = \com\coder\installer\instance::make([
			"your_first_name" => \$your_first_name,

			".db_type" => \$db_type,
			".db_host" => \$db_host,
			".db_name" => \$db_name,
			".db_username" => \$db_username,
			".db_password" => \$db_password,

			".email_force_to" => \$email_force_to,
			".email_support_to" => \$email_force_to,
			".email_from_name" => \$company,

			".company" => \$company,
			".system" => \$system,
			".website" => \$website,

			".sms_force_to" => \$sms_force_to,
			".sms_price" => 0.25,

			".session_ssl" => 0,

			".title" => "{\$this->company} | {\$this->system} | {\$this->environment_name}",

			".php_exe" => \$php_exe,
			".php_ini" => \$php_ini,
		]);
		\$installer->install();

		// done
		\com\http::redirect("?c=install/vinstall");
		return "clean";
	}
	//--------------------------------------------------------------------------------
}

PHP;

		if(!file_exists(Core::DIR_NOVA_ROOT."/index.php"))
			file_put_contents(Core::DIR_NOVA_ROOT."/index.php", $code);

	}
	//---------------------------------------------------------------------------

}