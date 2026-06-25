<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@example.com')->first();
if (!$user) {
    echo "no user\n";
    exit(1);
}

echo "user id: " . $user->id . "\n";
echo "stored password: " . $user->password . "\n";
$ok = Hash::check('secret', $user->password);
echo "Hash::check('secret') => ";
var_export($ok);
echo "\n";