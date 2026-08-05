<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "=== FIX SYMLINK STORAGE & PERMISSION ===" . PHP_EOL;

try {
    $publicStorage = public_path('storage');
    if (is_link($publicStorage) || file_exists($publicStorage)) {
        echo "1. Menghapus symlink storage lama..." . PHP_EOL;
        if (str_starts_with(PHP_OS, 'WIN')) {
            @unlink($publicStorage);
        } else {
            exec("rm -rf " . escapeshellarg($publicStorage));
        }
    }

    echo "2. Membuat symlink storage baru..." . PHP_EOL;
    Artisan::call('storage:link', ['--force' => true]);
    echo Artisan::output();

    $statusDir = storage_path('app/public/status');
    if (!file_exists($statusDir)) {
        mkdir($statusDir, 0777, true);
    }

    echo "3. Cek file media di storage:" . PHP_EOL;
    $files = glob(storage_path('app/public/status/*'));
    echo "   Ditemukan " . count($files) . " file di storage/app/public/status/" . PHP_EOL;
    foreach (array_slice($files, 0, 5) as $f) {
        echo "   - " . basename($f) . " (" . filesize($f) . " bytes)" . PHP_EOL;
    }

    Artisan::call('config:clear');
    Artisan::call('view:clear');

    echo PHP_EOL . "SELESAI! ✅" . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
