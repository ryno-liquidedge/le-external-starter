<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Core;

class MakeActionInstallvInstall {
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
class vinstall implements \com\\router\int\action {
	//--------------------------------------------------------------------------------
	use \com\\router\\tra\action;
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
		\$installer = \com\coder\installer\all::make();
		\$installer->install();


		\$composerPath = \core::\$folders->get_app()."/inc/composer";
		// Save current working directory
        \$cwd = getcwd();

        // Change to the composer subdirectory
        chdir(\$composerPath);
        \$return_var = 0;
        passthru("composer update 2>&1", \$return_var); // streams output live
		chdir(\$cwd);

		return "stream";
	}
	//--------------------------------------------------------------------------------
}

PHP;

		\Liquidedge\ExternalStarter\com\Os::mkdir(dirname(Core::DIR_NOVA."/app/action/install/a.install.vinstall.php"));
		file_put_contents(Core::DIR_NOVA."/app/action/install/a.install.vinstall.php", $code);

	}
	//---------------------------------------------------------------------------

}