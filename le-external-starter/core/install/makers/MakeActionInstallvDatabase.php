<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Config;
use Liquidedge\ExternalStarter\Core;

class MakeActionInstallvDatabase {
//---------------------------------------------------------------------------
	public function run(): void {

		Config::load();
		$packagist_auth_username = Config::get('packagist_auth_username');
		$packagist_auth_api_token = Config::get('packagist_auth_api_token');

		$code = <<<PHP
<?php

namespace action\install;

/**
 * Action Class.
 *
 * @author Liquid Edge Solutions
 * @copyright Copyright Liquid Edge Solutions. All rights reserved.
 */
class vdatabase implements \com\\router\int\action {
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

		if(file_exists(\core::\$folders->get_root()."/install_composer.php")){

			include_once \core::\$folders->get_root()."/install_composer.php";
            @unlink(\core::\$folders->get_root()."/install_composer.php");

            //copy external files
            \$external_dir = \core::\$folders->get_app()."/inc/composer/vendor/liquid-edge/le-core-ext/src/install_copy";
            try{
				\$this->move_items(glob("{\$external_dir}/*"), \core::\$folders->get_root()."/..");
            }catch(\Exception \$ex){}
            echo "
                <script>
                    document.location.reload();
                </script>
            ";
		}

        if(!defined("ADD_NOMINATION_ARR")){
            ?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Nova Installation</title>

                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
                </head>
                <body>
                    <div class="jumbotron">
                        <div class="container">
                            <h1>Nova Installation</h1>
                            <p>Please navigate to your instance file and replace <span style="color: red">extends \com\core\intf\instance</span> with <span style="color: green">extends \acc\core\instance\intf\instance</span></p>
                        </div>
                    </div>
                </body>
            </html>
            <?php

            return;
        }

		// html
		?>
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">

				<title>Nova Installation</title>

				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
			</head>
			<body>
				<div class="jumbotron">
					<div class="container">
						<h1>Nova Installation</h1>
						<p>Please download the database table creation sql script; run it; and then click next.</p>
						<button type="button" class="btn btn-default" onclick="document.location = '?c=install/xsql';">Download SQL</button>
						<button type="button" class="btn btn-default" onclick="document.location = '?c=install/xdatabase';">Next</button>
					</div>
				</div>
			</body>
		</html>
		<?php

		// done
		return "clean";
	}
    //---------------------------------------------------------------------------
	/**
	 * Move files and folders to a new directory
	 *
	 * @param array \$paths Array of file/folder paths to move
	 * @param string \$targetDir Destination directory
	 * @return array             List of errors (empty if successful)
	 */
	public function move_items(array \$paths, string \$targetDir): array {
		\$errors = [];

		// Ensure target directory exists
		if (!is_dir(\$targetDir) && !mkdir(\$targetDir, 0755, true)) {
			return ["Failed to create target directory: \$targetDir"];
		}

		foreach (\$paths as \$path) {
			
			if (!file_exists(\$path)) {
				\$errors[] = "Path does not exist: \$path";
				continue;
			}

			\$destination = rtrim(\$targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename(\$path);

			// Try simple rename first (fastest)
			if (!@rename(\$path, \$destination)) {
				// Fallback for cross-filesystem moves
				if (is_dir(\$path)) {
					if (!\$this->copy_directory(\$path, \$destination)) {
						\$errors[] = "Failed to move directory: \$path";
					}
				} else {
					if (!copy(\$path, \$destination) || !unlink(\$path)) {
						\$errors[] = "Failed to move file: \$path";
					}
				}
			}
		}

		return \$errors;
	}
	//---------------------------------------------------------------------------
	/**
	 * Recursively copy a directory
	 */
	public function copy_directory(string \$source, string \$destination): bool {
		if (!mkdir(\$destination, 0755, true) && !is_dir(\$destination)) {
			return false;
		}

		foreach (scandir(\$source) as \$item) {
			if (\$item === '.' || \$item === '..') continue;

			\$src = \$source . DIRECTORY_SEPARATOR . \$item;
			\$dst = \$destination . DIRECTORY_SEPARATOR . \$item;

			if (is_dir(\$src)) {
				if (!\$this->copy_directory(\$src, \$dst)) return false;
			} else {
				if (!copy(\$src, \$dst)) return false;
			}
		}

		return true;
	}
	//--------------------------------------------------------------------------------
}

PHP;

		\Liquidedge\ExternalStarter\com\Os::mkdir(dirname(Core::DIR_NOVA."/app/action/install/a.install.vdatabase.php"));
		file_put_contents(Core::DIR_NOVA."/app/action/install/a.install.vdatabase.php", $code);

	}
	//---------------------------------------------------------------------------

}