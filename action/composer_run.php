<?php
/**
 * composer_run.php
 * Safely executes Composer commands (install/update) and logs the output.
 */


ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


	putenv('COMPOSER_HOME=' . __DIR__ . '/../temp/.composer');
	putenv('COMPOSER_CACHE_DIR=' . __DIR__ . '/../temp/cache');

	$username = $_POST['username'] ?? '';
	$token = $_POST['token'] ?? '';

	if (!$username || !$token) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => false,
			'message' => "Username and token are required."
		]);
		exit;
	}

	$base_dir = __DIR__;
	$log_file = $base_dir . '/composer_log.txt';
	$project_dir = realpath($base_dir . '/../../app/inc/composer'); // Adjust if needed
	$composer_path = find_composer_path();

	// --- Command selection ---
	$allowed_commands = ['install', 'update', 'dump-autoload'];
	$command = $_GET['cmd'] ?? 'install';

	if (!in_array($command, $allowed_commands, true)) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => false,
			'message' => "Invalid command. Allowed: install, update, dump-autoload"
		]);
		exit;
	}

	// --- Start log ---
	safe_log($log_file, "=== Composer $command started ===");
	safe_log($log_file, "Detected Composer path: " . $composer_path);

	// --- Authenticate private repository ---
	$auth_cmd = sprintf(
		'"%s" config --global --auth http-basic.repo.packagist.com '.$username.' '.$token.' 2>&1',
		$composer_path
	);

	safe_log($log_file, "Running authentication: $auth_cmd");
	exec($auth_cmd, $auth_output, $auth_code);
	foreach ($auth_output as $line) safe_log($log_file, "[AUTH] $line");
	safe_log($log_file, "[AUTH] Exit code: $auth_code");

	// --- Build main Composer command ---
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$cmd = sprintf('cd /d "%s" && "%s" %s 2>&1', $project_dir, $composer_path, $command);
	} else {
		$cmd = sprintf('cd "%s" && %s %s 2>&1', $project_dir, escapeshellcmd($composer_path), $command);
	}

	safe_log($log_file, "Executing command: " . $cmd);

	// --- Run Composer ---
	$output = [];
	$return_code = 0;
	exec($cmd, $output, $return_code);

	// --- Log output ---
	foreach ($output as $line) {
		safe_log($log_file, $line);
	}

	safe_log($log_file, "Composer exited with code: $return_code");
	safe_log($log_file, "=== Composer $command finished ===\n");

	// --- Return JSON response if called via web ---
	if (php_sapi_name() !== 'cli') {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => $return_code === 0,
			'exit_code' => $return_code,
			'command' => $command,
			'composer_path' => $composer_path,
			'message' => $return_code === 0
				? "Composer $command completed successfully."
				: "Composer $command failed. Check composer_log.txt for details."
		]);
	}

}


//---------------------------------------------------------------------------------------
// --- Detect Composer Path Automatically ---
function find_composer_path(): string {
    $which = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where composer' : 'which composer';
    $output = [];
    @exec($which, $output);

    if (!empty($output) && file_exists($output[0])) {
        return $output[0];
    }

    // Common Windows install paths
    $common_paths = [
        'C:\\ProgramData\\ComposerSetup\\bin\\composer.bat',
        getenv('APPDATA') . '\\Composer\\composer.bat',
        getenv('APPDATA') . '\\Composer\\vendor\\bin\\composer.bat',
        'C:\\Program Files\\Composer\\composer.bat',
        'C:\\Composer\\composer.bat'
    ];

    foreach ($common_paths as $path) {
        if ($path && file_exists($path)) return $path;
    }

    return 'composer';
}
//---------------------------------------------------------------------------------------
// --- Safe log writer ---
function safe_log($file, $message) {
    $max_attempts = 5;
    $attempt = 0;

    while ($attempt < $max_attempts) {
        $fp = @fopen($file, 'a');
        if ($fp && flock($fp, LOCK_EX)) {
            fwrite($fp, "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL);
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }
        if ($fp) fclose($fp);
        $attempt++;
        usleep(200000); // 0.2 sec
    }
    return false;
}
//---------------------------------------------------------------------------------------