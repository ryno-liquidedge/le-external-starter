<?php

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/vendor/autoload.php';

$input = new ArgvInput();
$output = new ConsoleOutput();
$helper = new QuestionHelper();

$output->writeln("<comment>Project Structure successfully created.</comment>");
$output->writeln("<comment>Please run the following from your command line interface:<info>\033[1;32mphp le-external-starter/setup\033[0m</info></comment>");

//(new \Liquidedge\ExternalStarter\install\Builder())
//	->install_nova_addon();