<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "=== PERBAIKAN FILE STORAGE & PERMISSION ===" . PHP_EOL;

try {
    echo "1. Re-link Storage..." . PHP_EOL;
    Artisan::call('storage:link', ['--force' => true]);
    echo Artisan::output();

    echo "2. Publish asset fisik Livewire..." . PHP_EOL;
    Artisan::call('livewire:publish', ['--assets' => true]);
    Artisan::call('filament:assets');

    $targetDir = public_path('livewire');
    $sourceDir = public_path('vendor/livewire');
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (file_exists($sourceDir)) {
        foreach (scandir($sourceDir) as $file) {
            if ($file !== '.' && $file !== '..') {
                copy($sourceDir . '/' . $file, $targetDir . '/' . $file);
            }
        }
    }

    // Ensure status directory exists
    $statusDir = storage_path('app/public/status');
    if (!file_exists($statusDir)) {
        mkdir($statusDir, 0777, true);
    }

    echo "3. Clear Cache..." . PHP_EOL;
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('filament:optimize-clear');
    
    echo PHP_EOL . "SELESAI! ✅ Silakan jalankan chmod di terminal." . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
