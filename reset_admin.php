<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSTIK LENGKAP HASIL RESET ===" . PHP_EOL;

try {
    echo "DB Connection : " . config('database.default') . PHP_EOL;
    echo "SESSION_DRIVER: " . config('session.driver') . PHP_EOL;
    echo "SESSION_PATH  : " . config('session.files') . PHP_EOL;
    echo "APP_URL       : " . config('app.url') . PHP_EOL;
    echo "APP_TIMEZONE  : " . config('app.timezone') . PHP_EOL;
    echo PHP_EOL;

    // Check sessions table if database driver is used
    if (config('session.driver') === 'database') {
        echo "Cek tabel 'sessions' di Database MySQL: " . (Schema::hasTable('sessions') ? 'ADA ✅' : 'TIDAK ADA! ❌ (Jalankan php artisan migrate)') . PHP_EOL;
    } elseif (config('session.driver') === 'file') {
        $sessDir = config('session.files');
        echo "Cek folder session file: " . (is_dir($sessDir) && is_writable($sessDir) ? 'WRITABLE ✅' : 'TIDAK BISA DITULIS! ❌') . PHP_EOL;
    }

    // Force set user password
    $user = User::where('email', 'admin@statusscheduler.com')->first();
    if (!$user) {
        $user = new User();
        $user->email = 'admin@statusscheduler.com';
        $user->name = 'Admin';
    }
    
    $user->setRawAttributes(array_merge($user->getAttributes(), [
        'password' => Hash::make('admin123')
    ]));
    $user->save();

    echo PHP_EOL;
    echo "✓ User ID       : " . $user->id . PHP_EOL;
    echo "✓ User Email    : " . $user->email . PHP_EOL;
    echo "✓ Test Password : " . (Hash::check('admin123', $user->password) ? 'MATCH (admin123) ✅' : 'FAILED ❌') . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
