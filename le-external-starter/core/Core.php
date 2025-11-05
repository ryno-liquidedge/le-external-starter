<?php

namespace Liquidedge\ExternalStarter;

class Core {

	public const string DIR_INSTALLER_ROOT = __DIR__."/..";
	public const string DIR_INSTALLER_CONFIG_DIR = self::DIR_INSTALLER_ROOT."/config/";
	public const string INSTALLER_CONFIG_FILE = self::DIR_INSTALLER_ROOT."/config/project_settings.yaml";

	public const string DIR_NOVA = self::DIR_INSTALLER_ROOT."/..";
	public const string DIR_NOVA_ROOT = self::DIR_NOVA."/root";
	public const string DIR_NOVA_APP = self::DIR_NOVA."/app";
	public const string DIR_NOVA_COMPOSER = self::DIR_NOVA_APP."/inc/composer";
	public const string DIR_NOVA_DATA = self::DIR_NOVA."/data";

}