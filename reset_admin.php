<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSTIK PROSES MIGRASI & TABEL ===" . PHP_EOL;

try {
    echo "1. Menjalankan migrasi database..." . PHP_EOL;
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();

    echo "2. Cek ketersediaan tabel:" . PHP_EOL;
    echo "   - users    : " . (Schema::hasTable('users') ? 'ADA ✅' : 'TIDAK ADA ❌') . PHP_EOL;
    echo "   - posts    : " . (Schema::hasTable('posts') ? 'ADA ✅' : 'TIDAK ADA ❌') . PHP_EOL;
    echo "   - settings : " . (Schema::hasTable('settings') ? 'ADA ✅' : 'TIDAK ADA ❌') . PHP_EOL;
    echo PHP_EOL;

    echo "3. Cek jumlah data:" . PHP_EOL;
    echo "   - User count : " . User::count() . PHP_EOL;
    echo "   - Post count : " . Post::count() . PHP_EOL;
    echo "   - Setting    : " . Setting::count() . PHP_EOL;
    echo PHP_EOL;

    echo "4. Membersihkan cache tampilan & filament..." . PHP_EOL;
    Artisan::call('view:clear');
    Artisan::call('filament:optimize-clear');
    Artisan::call('icons:clear');
    echo "SELESAI! ✅" . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
