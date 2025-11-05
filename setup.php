<?php

require_once __DIR__ . '/vendor/autoload.php';

$builder = new \Liquidedge\ExternalStarter\install\Builder();

$builder->run();

$url = \Liquidedge\ExternalStarter\com\Os::pathToUrl(__DIR__."/action/install.html");
echo "\n🎉 Your project is ready! Open in your browser:\n";
echo $url . "\n\n";