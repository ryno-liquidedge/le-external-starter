<?php

use Liquidedge\ExternalStarter\com\Os;
use Liquidedge\ExternalStarter\Core;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/vendor/autoload.php';

$input = new ArgvInput();
$output = new ConsoleOutput();
$helper = new QuestionHelper();

$config = [];

$output->writeln("<comment>Welcome to the Liquid Edge <info>New Project</info> Tool</comment>");
$output->writeln("<comment>Before we can continue we need some information first.</comment>");

$prompt = function($id, $question, $default = null, $options = [])use(&$config, &$helper, &$input, &$output){

	$options = array_merge([
	    "hidden" => false
	], $options);

	$question = new Question('<question>'.$question.':</question> ', $default);
	$question->setHidden($options["hidden"]);
	return $config[$id] = $helper->ask($input, $output, $question);
};

//$prompt("per_firstname", "What is your firstname");
//$prompt("per_lastname", "What is your surname");
//$prompt("per_email", "What is your email");
//
//$prompt("company_name", "What is the client's company name");
//$prompt("system_name", "What is the client's system name");
//$prompt("website", "What is the client's website");
//
//$prompt("db_type", "What DB type is the system using (mysql / sqlite)", "mysql");
//$prompt("db_hostname", "What is the DB hostname", "localhost");
//$prompt("db_username", "What is the DB username");
//$prompt("db_password", "What is the DB password");
//
//$prompt("php_exe_path", "What is the path to your php.exe file (Including file name)");
//$prompt("php_ini_path", "What is the path to your php.ini file (Including file name)");

$prompt("packagist_auth_username", "Please enter your LE Packagist Auth Username");
$prompt("packagist_auth_api_token", "Please enter your LE Packagist Auth API Token");

// Save config to YAML
Os::mkdir(Core::DIR_INSTALLER_CONFIG_DIR);

file_put_contents(Core::INSTALLER_CONFIG_FILE, Yaml::dump($config, 4, 2));
$output->writeln("<info>Configuration saved to config/project_settings.yaml</info>");

$builder = new \Liquidedge\ExternalStarter\install\Builder();
$builder->run();