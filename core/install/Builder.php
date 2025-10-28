<?php

namespace Liquidedge\ExternalStarter\Install;

use Liquidedge\ExternalStarter\Core;

class Builder {
	//---------------------------------------------------------------------------
	public function run() {

		//first create folders
		$this->create_folders();

	}

	//---------------------------------------------------------------------------
	private function create_folders(): self {
//		mkdir();
		var_dump(glob(Core::ROOT."/*"));
		return $this;
	}
	//---------------------------------------------------------------------------
}