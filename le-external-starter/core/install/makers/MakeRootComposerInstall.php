<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Core;

class MakeRootComposerInstall {

	//---------------------------------------------------------------------------
	public function run(): void {

		$code = <<<PHP
<?php
/**
 * Nova Installation.
 *
 * @author Liquid Edge Solutions
 * @copyright Copyright Liquid Edge Solutions. All rights reserved.
 */
if(file_exists(\core::\$folders->get_root()."/install.php")){
	\$composerPath = \core::\$folders->get_app()."/inc/composer";
	\$cwd = getcwd();
	chdir(\$composerPath);
	exec("composer dump-autoload 2>&1");
	chdir(\$cwd);
	
	sleep(3);
}

PHP;

		if(!file_exists(Core::DIR_NOVA_ROOT."/install_composer.php"))
			file_put_contents(Core::DIR_NOVA_ROOT."/install_composer.php", $code);

	}
	//---------------------------------------------------------------------------

}