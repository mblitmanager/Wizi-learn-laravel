<?php
$logFile = 'storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found\n";
    exit;
}

$lines = file($logFile);
$lastLines = array_slice($lines, -500);
$content = implode('', $lastLines);

// Search for the last occurrence of a 500-related error or local.ERROR
$pos = strrpos($content, 'local.ERROR');
if ($pos !== false) {
    echo substr($content, $pos, 2000);
} else {
    echo "No local.ERROR found in last 500 lines\n";
    // Just show last 100 lines
    echo implode('', array_slice($lastLines, -100));
}
