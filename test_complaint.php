<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'PJ Pelaksana');
})->first();

if (!$user) {
    echo "No PJ Pelaksana found.\n";
    exit;
}
echo "User: " . $user->name . "\n";
echo "Roles: " . $user->getRoleNames()->join(', ') . "\n";
echo "Can ViewAny Complaint Policy: " . ($user->can('viewAny', \App\Models\Complaint::class) ? 'YES' : 'NO') . "\n";
// Manually instantiate to check any unexpected failures
$resource = \App\Filament\Resources\ComplaintResource::class;
$userAuth = auth()->login($user);
echo "Can Access Resource: " . ($resource::canViewAny() ? 'YES' : 'NO') . "\n";
