<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('telepon', '628324234234')->first();

if (!$user) {
    echo "RESULT: User NOT FOUND\n";
    exit;
}

echo "RESULT: User FOUND\n";
echo "Name: " . $user->name . "\n";
echo "Phone: " . $user->telepon . "\n";
echo "Password Match: " . (Hash::check('password', $user->password) ? 'YES' : 'NO') . "\n";
echo "Roles: " . $user->getRoleNames()->implode(', ') . "\n";

try {
    $panel = Filament::getPanel('sppg');
    echo "Can Access SPPG Panel: " . ($user->canAccessPanel($panel) ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "Panel Check Error: " . $e->getMessage() . "\n";
}
