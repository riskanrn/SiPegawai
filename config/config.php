<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
define('APP_ROOT', dirname(__DIR__));
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$basePath = rtrim(dirname($scriptName), '/');
define('BASE_URL', $basePath === '/' ? '' : $basePath);
$dbConfig = [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'database' => getenv('DB_NAME') ?: 'db_kepegawaian',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset' => 'utf8mb4',
];
if (is_file(__DIR__ . '/local.php')) {
    $local = require __DIR__ . '/local.php';
    if (is_array($local)) $dbConfig = array_merge($dbConfig, $local);
}
define('DB_CONFIG', $dbConfig);
