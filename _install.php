<?php

use Liquidedge\ExternalStarter\com\Os;
use Liquidedge\ExternalStarter\Config;
use Liquidedge\ExternalStarter\Core;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/vendor/autoload.php';

$url = \Liquidedge\ExternalStarter\com\Os::pathToUrl(realpath(Core::DIR_NOVA_ROOT."/install.php"));
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
