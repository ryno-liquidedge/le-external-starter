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

	$config_value = Config::get($id);
	if($config_value) return $config[$id] = $config_value;

	$question = new Question('<question>'.$question.':</question> ', $default);
	$question->setHidden($options["hidden"]);
	return $config[$id] = $helper->ask($input, $output, $question);
};

Config::load();

$prompt("packagist_auth_username", "Please enter your LE Packagist Auth Username");
$prompt("packagist_auth_api_token", "Please enter your LE Packagist Auth API Token");
$prompt("site_url", "Please enter the URL to the root folder of your site: IE: http://localhost/root or http://yourdomain.local");

// Save config to YAML
Os::mkdir(Core::DIR_INSTALLER_CONFIG_DIR);

if($config) file_put_contents(Core::INSTALLER_CONFIG_FILE, Yaml::dump($config, 4, 2));
$output->writeln("<info>Configuration saved to config/project_settings.yaml</info>");

$builder = new \Liquidedge\ExternalStarter\install\Builder();
$builder->run();

$output = [];
$return_var = 0;

exec('php ' . __DIR__ . '/_install.php 2>&1', $output, $return_var);

if ($return_var === 0) {

	$builder->create_actions();
	$builder->cleanup();

    echo "Install complete!";
	echo "\n🎉 Your project is ready! Open in your browser:";
	echo "\n";
	echo \Liquidedge\ExternalStarter\com\Os::pathToUrl(realpath(Core::DIR_NOVA_ROOT."/install.php"));
	echo "\n\n\n";
	echo "🎉 Please review your setup process further here:";
	echo "\n";
	echo "https://github.com/liquid-edge/le-core-ext/";
	echo "\n\n";

} else {
    echo "Install failed!";
    print_r($output);
}