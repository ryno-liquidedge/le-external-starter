<?php

namespace Liquidedge\ExternalStarter\Com;

class Os {
	//--------------------------------------------------------------------------------
    public static function mkdir($path): void {
    	if (!file_exists($path)) {
    		mkdir($path, 0777, true);
		}
    }
	//--------------------------------------------------------------------------------
}