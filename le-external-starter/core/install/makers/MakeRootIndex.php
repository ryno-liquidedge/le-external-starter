<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Core;

class MakeRootIndex {

	//---------------------------------------------------------------------------
	public function run(): void {

		$code = <<<PHP
<?php
/**
 * Index page handler
 *
 * @author Liquid Edge Solutions
 * @copyright Copyright Liquid Edge Solutions. All rights reserved.
 */
 
if (!function_exists('getallheaders')) {
    function getallheaders(): array
    {
        return array_combine(
            array_map(
                fn(\$key) => str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(\$key, 5))))),
                array_filter(array_keys(\$_SERVER), fn(\$k) => str_starts_with(\$k, 'HTTP_'))
            ),
            array_filter(\$_SERVER, fn(\$v, \$k) => str_starts_with(\$k, 'HTTP_'), ARRAY_FILTER_USE_BOTH)
        );
    }
}


// include core
include_once("../app/inc/composer/vendor/liquid-edge/le-core-classic/core.php");

// handle url
\$success = \core::handle();
if (!\$success) \com\http::go_home();

PHP;

		if(!file_exists(Core::DIR_NOVA_ROOT."/index.php"))
			file_put_contents(Core::DIR_NOVA_ROOT."/index.php", $code);

	}
	//---------------------------------------------------------------------------

}