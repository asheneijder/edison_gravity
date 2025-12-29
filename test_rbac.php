<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'a@a.com')->first();
echo "User: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";

$adminPanel = new Filament\Panel();
$adminPanel->id('admin');

$appPanel = new Filament\Panel();
$appPanel->id('app');

echo "Access Admin Panel: " . ($user->canAccessPanel($adminPanel) ? 'YES' : 'NO') . "\n";
echo "Access App Panel: " . ($user->canAccessPanel($appPanel) ? 'YES' : 'NO') . "\n";
