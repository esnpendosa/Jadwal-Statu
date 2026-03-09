<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| aaPanel / Shared Hosting open_basedir Fix
|--------------------------------------------------------------------------
| Pada aaPanel & shared hosting, open_basedir membatasi akses hanya ke
| folder public/. Script ini mendeteksi root aplikasi secara absolut
| dan menambahkan path yang dibutuhkan ke open_basedir agar Laravel
| dapat mengakses vendor/, storage/, bootstrap/, dll.
*/
$appRoot = dirname(__DIR__);

// Sesuaikan open_basedir agar bisa mengakses seluruh project
if (function_exists('ini_set')) {
    $currentPaths = ini_get('open_basedir');
    if ($currentPaths) {
        $separator = (PHP_OS_FAMILY === 'Windows') ? ';' : ':';
        $newPaths = $currentPaths . $separator . $appRoot . $separator . sys_get_temp_dir();
        ini_set('open_basedir', $newPaths);
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appRoot . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appRoot . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appRoot . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
