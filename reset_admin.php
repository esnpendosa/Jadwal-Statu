<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

echo "=== FIX ASSET LIVEWIRE & FILAMENT ===" . PHP_EOL;

try {
    echo "1. Publish asset fisik..." . PHP_EOL;
    Artisan::call('livewire:publish', ['--assets' => true]);
    Artisan::call('filament:assets');
    echo Artisan::output();

    echo "2. Menyalin asset ke public/livewire..." . PHP_EOL;
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
    echo "✓ Livewire JS tersedia di public/livewire/livewire.js & public/vendor/livewire/livewire.js" . PHP_EOL;

    echo "3. Migrasi & Clear Cache..." . PHP_EOL;
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('filament:optimize-clear');
    Artisan::call('icons:clear');
    
    echo PHP_EOL . "SELESAI! ✅ Silakan refresh browser." . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
