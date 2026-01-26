<?php

namespace Liquidedge\ExternalStarter\install;

use Liquidedge\ExternalStarter\com\Os;
use Liquidedge\ExternalStarter\Config;
use Liquidedge\ExternalStarter\Core;
use Liquidedge\ExternalStarter\install\installer\InstallInstance;
use Liquidedge\ExternalStarter\install\makers\MakeRootInstall;
use Liquidedge\ExternalStarter\install\modifiers\ModifyInstanceFiles;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Builder {

	protected ArgvInput $input;
	protected ConsoleOutput $output;
	protected QuestionHelper $helper;

	//---------------------------------------------------------------------------

	public function __construct() {
		$this->input = new ArgvInput();
		$this->output = new ConsoleOutput();
		$this->helper = new QuestionHelper();
	}

	//---------------------------------------------------------------------------
	public function run(): void {

		//first create folders
		$this->create_folders()
		->create_composer_json()
		->create_root_files()
		->cli_composer_update();
	}
	//---------------------------------------------------------------------------
	public function install_nova_addon(): void {

		$this->create_composer_json(["force" => true])
			->cli_composer_dump_autoload()
			->copy_nebula_files()
			->run_installers();
	}
	//---------------------------------------------------------------------------
	public function copy_nebula_files(): self {
		$nebula_install_copy_dir = Core::DIR_NOVA_COMPOSER."/vendor/liquid-edge/le-core-ext/src/install_copy";
		$nova_dir = Core::DIR_NOVA;

		Os::copy_folder($nebula_install_copy_dir, $nova_dir);

		return $this;
	}
	//---------------------------------------------------------------------------
	public function run_installers(): void {

		(new InstallInstance())->install();

	}
	//---------------------------------------------------------------------------
	public function cli_composer_update():self {

		Config::load();
		$packagist_auth_username = Config::get('packagist_auth_username');
		$packagist_auth_api_token = Config::get('packagist_auth_api_token');

		$composerPath = Core::DIR_NOVA_COMPOSER;
        $composerFile = $composerPath . '/composer.json';

        if (!file_exists($composerFile)) {
			throw new \Exception("No composer.json found at $composerFile\n");
        }

		$this->output->writeln("Running composer update in $composerPath...\n");

        // Save current working directory
        $cwd = getcwd();

        // Change to the composer subdirectory
        chdir($composerPath);

		$return_var = $this->run_cli_command("composer config --global --auth http-basic.repo.packagist.com {$packagist_auth_username} {$packagist_auth_api_token}  2>&1");
		if ($return_var === 0) $this->output->writeln("Packagist authentication configured for {$packagist_auth_username}.");
		else throw new ("Packagist authentication failed for {$packagist_auth_username}.");

		$return_var = $this->run_cli_command("composer update 2>&1");
		if ($return_var === 0) $this->output->writeln("Composer update completed successfully.");
		else throw new ("Composer update failed with exit code $return_var.");

        // Restore original directory
        chdir($cwd);

		return $this;
	}
	//---------------------------------------------------------------------------
	public function cli_composer_dump_autoload(): static {
		$composerPath = Core::DIR_NOVA_COMPOSER;
		// Save current working directory
        $cwd = getcwd();

        // Change to the composer subdirectory
        chdir($composerPath);
		$this->run_cli_command("composer dump-autoload 2>&1");
		chdir($cwd);

		return $this;
	}
	//---------------------------------------------------------------------------
	private function run_cli_command($command): int {
		// Run composer update
        $return_var = 0;

        passthru($command, $return_var); // streams output live
		if ($return_var !== 0) {
			$this->output->writeln("Composer update failed with exit code $return_var.\n");
		}
		return $return_var;
	}
	//---------------------------------------------------------------------------
	private function create_root_files(): self {

		(new MakeRootInstall())->run();

		return $this;
	}
	//---------------------------------------------------------------------------
	private function create_folders(): self {

		Os::mkdir(Core::DIR_NOVA."/app/inc/composer");
		Os::mkdir(Core::DIR_NOVA."/data");
		Os::mkdir(Core::DIR_NOVA."/root");

		return $this;
	}
	//---------------------------------------------------------------------------
	public function create_composer_json($options = []): self {

		$options = array_merge([
		    "force" => false
		], $options);

		if($options["force"]){
			if(file_exists(Core::DIR_NOVA."/app/inc/composer/composer.json")){
				@unlink(Core::DIR_NOVA."/app/inc/composer/composer.json");
			}
		}

		$config = [
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


		if(!file_exists(Core::DIR_NOVA."/app/inc/composer/composer.json")){
			Os::mkdir(dirname(Core::DIR_NOVA."/app/inc/composer/composer.json"));
			file_put_contents(Core::DIR_NOVA."/app/inc/composer/composer.json", json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		}

		return $this;
	}
	//---------------------------------------------------------------------------
}