<?php

namespace Liquidedge\ExternalStarter\install;

use Liquidedge\ExternalStarter\com\Os;
use Liquidedge\ExternalStarter\Config;
use Liquidedge\ExternalStarter\Core;
use Liquidedge\ExternalStarter\install\installer\InstallInstance;
use Liquidedge\ExternalStarter\install\makers\MakeActionInstallvInstall;
use Liquidedge\ExternalStarter\install\makers\MakeActionInstallvSetup;
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
	public function create_actions(): void {

		(new MakeActionInstallvSetup())->run();
		(new MakeActionInstallvInstall())->run();
	}
	//---------------------------------------------------------------------------
	public function cleanup(): void {
		$items = [
			Core::DIR_INSTALLER_ROOT.'/../admin',
			Core::DIR_INSTALLER_ROOT.'/../app',
			Core::DIR_INSTALLER_ROOT.'/../core',
			Core::DIR_INSTALLER_ROOT.'/../data',
			Core::DIR_INSTALLER_ROOT.'/../root',
		];

		$errors = $this->move_items($items, Core::DIR_NOVA.'/../');
		if ($errors) {
			print_r($errors);
		} else {
			echo "All items moved successfully!";
		}

	}
	//---------------------------------------------------------------------------
	/**
	 * Move files and folders to a new directory
	 *
	 * @param array $paths Array of file/folder paths to move
	 * @param string $targetDir Destination directory
	 * @return array             List of errors (empty if successful)
	 */
	public function move_items(array $paths, string $targetDir): array {
		$errors = [];

		// Ensure target directory exists
		if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
			return ["Failed to create target directory: $targetDir"];
		}

		foreach ($paths as $path) {
			if (!file_exists($path)) {
				$errors[] = "Path does not exist: $path";
				continue;
			}

			$destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($path);

			// Try simple rename first (fastest)
			if (!@rename($path, $destination)) {
				// Fallback for cross-filesystem moves
				if (is_dir($path)) {
					if (!$this->copy_directory($path, $destination) || !$this->delete_directory($path)) {
						$errors[] = "Failed to move directory: $path";
					}
				} else {
					if (!copy($path, $destination) || !unlink($path)) {
						$errors[] = "Failed to move file: $path";
					}
				}
			}
		}

		return $errors;
	}
	//---------------------------------------------------------------------------
	/**
	 * Recursively copy a directory
	 */
	public function copy_directory(string $source, string $destination): bool {
		if (!mkdir($destination, 0755, true) && !is_dir($destination)) {
			return false;
		}

		foreach (scandir($source) as $item) {
			if ($item === '.' || $item === '..') continue;

			$src = $source . DIRECTORY_SEPARATOR . $item;
			$dst = $destination . DIRECTORY_SEPARATOR . $item;

			if (is_dir($src)) {
				if (!$this->copy_directory($src, $dst)) return false;
			} else {
				if (!copy($src, $dst)) return false;
			}
		}

		return true;
	}
	//---------------------------------------------------------------------------
	/**
	 * Recursively delete a directory
	 */
	public function delete_directory(string $dir): bool {
		foreach (scandir($dir) as $item) {
			if ($item === '.' || $item === '..') continue;

			$path = $dir . DIRECTORY_SEPARATOR . $item;
			is_dir($path) ? $this->delete_directory($path) : unlink($path);
		}

		return rmdir($dir);
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
			],
		];


		if(!file_exists(Core::DIR_NOVA."/app/inc/composer/composer.json")){
			Os::mkdir(dirname(Core::DIR_NOVA."/app/inc/composer/composer.json"));
			file_put_contents(Core::DIR_NOVA."/app/inc/composer/composer.json", json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		}

		return $this;
	}
	//---------------------------------------------------------------------------
}