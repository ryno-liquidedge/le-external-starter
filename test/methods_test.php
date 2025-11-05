<?php


//function pathToUrl($filePath) {
//    // Normalize slashes
//    $filePath = str_replace('\\', '/', $filePath);
//
//    // Look for 'wwwroot' in the path
//    $pos = stripos($filePath, 'wwwroot');
//    if ($pos === false) {
//        throw new Exception('wwwroot not found in path');
//    }
//
//    // Get path relative to wwwroot
//    $relativePath = substr($filePath, $pos + strlen('wwwroot/'));
//
//    // Convert to URL
//    return 'http://localhost/' . $relativePath;
//}
//
//// Example usage
//$path = 'C:\inetpub\wwwroot\nebula_v2\le-external-starter\test\methods_test.php';
//$url = pathToUrl($path);
//
//echo $url;

//require_once __DIR__ . '/../vendor/autoload.php';
//
//$builder = new \Liquidedge\ExternalStarter\install\install\Builder();
//
//$builder->run();

?>

<!--<script>-->
<!--	async function runInstall() {-->
<!--        await fetch('/nebula_v2/le-external-starter/action/install.php');-->
<!--        const logBox = document.getElementById('log');-->
<!--        const interval = setInterval(async () => {-->
<!--            const res = await fetch('/nebula_v2/le-external-starter/action/composer_log.txt');-->
<!--            const text = await res.text();-->
<!--            console.log(text);-->
<!--            console.log(1);-->
<!--            logBox.textContent = text;-->
<!--            if (text.includes('Generating autoload files')) clearInterval(interval);-->
<!--        }, 2000);-->
<!--    }-->
<!---->
<!--    runInstall();-->
<!--</script>-->
