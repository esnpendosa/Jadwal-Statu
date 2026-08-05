<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== DIAGNOSTIK DATABASE & USER ===" . PHP_EOL;

try {
    $dbPath = config('database.connections.sqlite.database');
    echo "DB Connection : " . config('database.default') . PHP_EOL;
    echo "DB Path       : " . $dbPath . PHP_EOL;
    echo "DB Exists?    : " . (file_exists($dbPath) ? 'YES' : 'NO!') . PHP_EOL;
    echo "DB Writable?  : " . (is_writable($dbPath) ? 'YES' : 'NO!') . PHP_EOL;

    // Create or update admin user directly with Hash::make
    $user = User::where('email', 'admin@statusscheduler.com')->first();
    if (!$user) {
        $user = new User();
        $user->email = 'admin@statusscheduler.com';
        $user->name = 'Admin';
    }
    
    // Explicitly set raw hashed password to bypass any model casting quirks
    $user->setRawAttributes(array_merge($user->getAttributes(), [
        'password' => Hash::make('admin123')
    ]));
    $user->save();

    echo PHP_EOL;
    echo "✓ User ID       : " . $user->id . PHP_EOL;
    echo "✓ User Email    : " . $user->email . PHP_EOL;
    echo "✓ Test Password : " . (Hash::check('admin123', $user->password) ? 'SUCCESS MATCH (admin123)' : 'FAILED!') . PHP_EOL;
    echo PHP_EOL;
    echo "Total User di DB: " . User::count() . " user(s)." . PHP_EOL;

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
