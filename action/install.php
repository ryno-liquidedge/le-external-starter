<?php
header('Content-Type: application/json');

$base_dir = __DIR__;
$composer_dir = $base_dir . '/../../app/inc/composer';
$log_file = $base_dir . '/composer_log.txt';

if (!is_dir($composer_dir)) {
    mkdir($composer_dir, 0777, true);
}

file_put_contents($log_file, "Starting Composer update...\n");

$php_path = PHP_BINARY;
$cmd = sprintf(
    'start "" /B "%s" "%s\\composer_run.php" > "%s" 2>&1',
    $php_path,
    $base_dir,
    $log_file
);

pclose(popen($cmd, 'r'));

echo json_encode([
    'status' => 'started',
    'message' => 'Composer update started in background.'
]);
