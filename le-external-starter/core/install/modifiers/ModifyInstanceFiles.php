<?php

namespace Liquidedge\ExternalStarter\install\modifiers;

class ModifyInstanceFiles {
	//---------------------------------------------------------------------------
	public function run() {

		$files = glob(\Liquidedge\ExternalStarter\Core::DIR_NOVA."/app/acc/instance/core.instance");
		dump($files);

	}
	//---------------------------------------------------------------------------
}