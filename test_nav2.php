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

auth()->login($user);

$panel = \Filament\Facades\Filament::getPanel('sppg');
\Filament\Facades\Filament::setCurrentPanel($panel);

$navGroups = $panel->getNavigation();

echo "Navigation items for " . $user->name . " (PJ Pelaksana) in SPPG Panel:\n";
foreach ($navGroups as $group) {
    echo "Group: " . ($group->getLabel() ?? 'None') . "\n";
    foreach ($group->getItems() as $item) {
        $url = $item->getUrl();
        echo " - " . $item->getLabel() . " (" . $url . ")\n";
    }
}
