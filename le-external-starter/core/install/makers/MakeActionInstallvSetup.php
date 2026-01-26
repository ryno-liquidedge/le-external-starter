<?php

namespace Liquidedge\ExternalStarter\install\makers;

use Liquidedge\ExternalStarter\Core;

class MakeActionInstallvSetup {
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
class vsetup implements \com\\router\int\action {
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
		// init

		\$this->regenerate_composer_file();

		\$value_arr = [
			"your_first_name" => "",
			"email_force_to" => "",
			"sms_force_to" => "",
			"company" => "",
			"system" => "",
			"website" => "",

			"db_type" => "",
			"db_host" => "",
			"db_name" => "",
			"db_username" => "",
			"db_password" => "",

			"php_exe" => "",
			"php_ini" => "",
		];

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
						<p>Please create a new database with the credentials you will be filling in below and then complete the form.</p>
						<button type="button" class="btn btn-default" onclick="$('#setup_form').submit();">Next</button>
					</div>
				</div>

				<div class="container">
					<div class="row">
						<form action="?c=install/xsetup" method="post" autocomplete="off" id="setup_form">
							<div class="col-md-4">
								<h3>Instance</h3>
								<div class="form-group">
									<label for="your_first_name">Your first name</label>
									<input type="text" class="form-control" id="your_first_name" name="your_first_name" value="<?= \$value_arr["your_first_name"]; ?>" />
								</div>
								<div class="form-group">
									<label for="email_force_to">Your email</label>
									<input type="text" class="form-control" id="email_force_to" name="email_force_to" value="<?= \$value_arr["email_force_to"]; ?>" />
								</div>
								<div class="form-group">
									<label for="sms_force_to">Your cellphone number</label>
									<input type="text" class="form-control" id="sms_force_to" name="sms_force_to" value="<?= \$value_arr["sms_force_to"]; ?>" />
								</div>
								<div class="form-group">
									<label for="company">Client company</label>
									<input type="text" class="form-control" id="company" name="company" value="<?= \$value_arr["company"]; ?>" />
								</div>
								<div class="form-group">
									<label for="system">Client system name</label>
									<input type="text" class="form-control" id="system" name="system" value="<?= \$value_arr["system"]; ?>" />
								</div>
								<div class="form-group">
									<label for="website">Client web site</label>
									<input type="text" class="form-control" id="website" name="website" value="<?= \$value_arr["website"]; ?>" />
								</div>
							</div>

							<div class="col-md-4">
								<h3>Database</h3>
								<div class="form-group">
									<label for="db_type">Type</label>
									<select class="form-control" id="db_type" name="db_type">
										<option value="sqlsrv" selected="selected">Microsoft SQL Server</option>
										<option value="mysql">MySQL</option>
									</select>
								</div>
								<div class="form-group">
									<label for="db_host">Host</label>
									<input type="text" class="form-control" id="db_host" name="db_host" value="<?= \$value_arr["db_host"]; ?>" />
								</div>
								<div class="form-group">
									<label for="db_name">Database name</label>
									<input type="text" class="form-control" id="db_name" name="db_name" value="<?= \$value_arr["db_name"]; ?>" />
								</div>
								<div class="form-group">
									<label for="db_username">Username</label>
									<input type="text" class="form-control" id="db_username" name="db_username" value="<?= \$value_arr["db_username"]; ?>" />
								</div>
								<div class="form-group">
									<label for="db_password">Password</label>
									<input type="text" class="form-control" id="db_password" name="db_password" value="<?= \$value_arr["db_password"]; ?>" />
								</div>
							</div>

							<div class="col-md-4">
								<h3>PHP</h3>
								<div class="form-group">
									<label for="php_exe">Path to php.exe (Including file name)</label>
									<input type="text" class="form-control" id="php_exe" name="php_exe" value="<?= \$value_arr["php_exe"]; ?>" />
								</div>
								<div class="form-group">
									<label for="php_ini">Path to php.ini (Including file name)</label>
									<input type="text" class="form-control" id="php_ini" name="php_ini" value="<?= \$value_arr["php_ini"]; ?>" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</body>
		</html>
		<?php

		// done
		return "clean";
	}
	//--------------------------------------------------------------------------------
	private function regenerate_composer_file(): void {

		\$config = [
			"config" => [
				"optimize-autoloader" => true,
				"platform" => [
					"php" => "8.3"
				]
			],
			"require" => [
				"liquid-edge/le-core-classic" => "12.0.*",
				"liquid-edge/le-core-ext" => "1.1.*",
				"liquid-edge/le-external-starter" => "1.0.*",
			],
			"repositories" => [
				[
					"type" => "composer",
					"url" => "https://repo.packagist.com/liquid-edge/"
				],
				[
					"type" => "composer",
					"url" => "https://www.setasign.com/downloads/"
				],
				[
					"packagist.org" => false
				]
			]
		];


        file_put_contents(\\core::\$folders->get_app()."/inc/composer/composer.json", json_encode(\$config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

	}
	//---------------------------------------------------------------------------
	public function cli_composer_dump_autoload(): void {
		\$composerPath = Core::DIR_NOVA_COMPOSER;
		// Save current working directory
        \$cwd = getcwd();

        // Change to the composer subdirectory
        \$return_var = 0;
        chdir(\\core::\$folders->get_app()."/inc/composer/");
        passthru("composer dump-autoload 2>&1", \$return_var); // streams output live
		chdir(\$cwd);
	}
	//--------------------------------------------------------------------------------
}

PHP;

		\Liquidedge\ExternalStarter\com\Os::mkdir(dirname(Core::DIR_NOVA."/app/action/install/a.install.vsetup.php"));
		file_put_contents(Core::DIR_NOVA."/app/action/install/a.install.vsetup.php", $code);

	}
	//---------------------------------------------------------------------------

}