<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'u1@u1.com';
$user = App\Models\User::where('email', $email)->first();

if ($user) {
    $user->password = Illuminate\Support\Facades\Hash::make('12345');
    $user->save();
    echo "Password for {$email} reset to '12345'.\n";
    echo "Role: " . $user->role . "\n";
} else {
    // Create if not exists
    $user = App\Models\User::create([
        'name' => 'User 1',
        'email' => $email,
        'password' => Illuminate\Support\Facades\Hash::make('12345'),
        'role' => 'user',
    ]);
    echo "User {$email} created with password '12345'.\n";
}
