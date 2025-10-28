<?php

namespace Liquidedge\ExternalStarter\Install;

use Liquidedge\ExternalStarter\Com\Debug;
use Liquidedge\ExternalStarter\Com\Os;
use Liquidedge\ExternalStarter\Core;

class Builder {
	//---------------------------------------------------------------------------
	public function run() {

		//first create folders
		$this->create_folders()
		->create_composer_json();

	}

	//---------------------------------------------------------------------------
	private function create_install_files(): self {

		return $this;
	}
	//---------------------------------------------------------------------------
	private function create_folders(): self {

		Os::mkdir(Core::NOVA."/app/inc/composer");
		Os::mkdir(Core::NOVA."/data");
		Os::mkdir(Core::NOVA."/root");

		return $this;
	}
	//---------------------------------------------------------------------------
	private function create_composer_json(): self {

		$config = [
			"config" => [
				"optimize-autoloader" => true,
				"platform" => [
					"php" => "8.3"
				]
			],
			"require" => [
				"liquid-edge/le-core-classic" => "12.0.*",
				"liquid-edge/le-core-ext" => "2.0.*",
				"piwik/device-detector" => "3.11.*"
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


		if(!file_exists(Core::NOVA."/app/inc/composer/composer.json")){
			file_put_contents(Core::NOVA."/app/inc/composer/composer.json", json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		}

		return $this;
	}
	//---------------------------------------------------------------------------
}