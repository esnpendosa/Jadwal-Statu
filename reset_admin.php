<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "=== FIX ASSET LIVEWIRE & FILAMENT ===" . PHP_EOL;

try {
    echo "1. Publish asset fisik..." . PHP_EOL;
    Artisan::call('livewire:publish', ['--assets' => true]);
    Artisan::call('filament:assets');

    echo "2. Salin asset ke public/livewire..." . PHP_EOL;
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
    echo "✓ Livewire JS file: " . (file_exists(public_path('livewire/livewire.js')) ? 'EXISTS ✅' : 'MISSING ❌') . PHP_EOL;

    echo "3. Clear Cache..." . PHP_EOL;
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('filament:optimize-clear');
    
    echo PHP_EOL . "SELESAI! ✅ Silakan refresh browser." . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
