<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'PJ Pelaksana');
})->whereDoesntHave('roles', function($q) {
    $q->where('name', 'Pimpinan Lembaga Pengusul');
})->first();

if (!$user) {
    echo "No pure PJ Pelaksana found.\n";
    exit;
}
echo "User: " . $user->name . "\n";
echo "Roles: " . $user->getRoleNames()->join(', ') . "\n";
echo "Can ViewAny Complaint Policy: " . ($user->can('viewAny', \App\Models\Complaint::class) ? 'YES' : 'NO') . "\n";
$userAuth = auth()->login($user);
$resource = \App\Filament\Resources\ComplaintResource::class;
echo "Can Access Resource: " . ($resource::canViewAny() ? 'YES' : 'NO') . "\n";
