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
include_once("../core/core.php");

PHP;

		if(!file_exists(Core::NOVA_ROOT."/install.php"))
			file_put_contents(Core::NOVA_ROOT."/install.php", $code);

	}
	//---------------------------------------------------------------------------

}