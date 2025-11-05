<?php

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/vendor/autoload.php';

$input = new ArgvInput();
$output = new ConsoleOutput();
$helper = new QuestionHelper();

$output->writeln("<comment>Project Structure successfully created.</comment>");
$output->writeln("<comment>Please run <info>[php le-external-starter/setup]</info> from your command line interface</comment>");