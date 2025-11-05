<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Core;

class MakeRootInstall {

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
include_once("../app/inc/composer/vendor/liquid-edge/le-core-classic/core.php");

PHP;

		if(!file_exists(Core::DIR_NOVA_ROOT."/install.php"))
			file_put_contents(Core::DIR_NOVA_ROOT."/install.php", $code);

	}
	//---------------------------------------------------------------------------

}