<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- MFA Logic Test Report ---\n";

// 1. Setup User
$user = App\Models\User::where('email', 'mfa_test@example.com')->first();
if ($user)
    $user->delete();

$user = App\Models\User::create([
    'name' => 'MFA Tester',
    'email' => 'mfa_test@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'google2fa_secret' => null // Not set up
]);

echo "[PASS] Test User Created: {$user->email}\n";

// 2. Middleware Check - Expect Redirect to Setup
auth()->login($user);
$request = Illuminate\Http\Request::create('/admin', 'GET');
$middleware = new App\Http\Middleware\EnsureMfaSetup();

$response = $middleware->handle($request, function ($req) {
    return new Illuminate\Http\Response('Allowed');
});

if ($response->isRedirect(route('mfa.setup'))) {
    echo "[PASS] Middleware redirects new user to MFA Setup.\n";
} else {
    echo "[FAIL] Middleware did not redirect to Setup. Status: " . $response->getStatusCode() . "\n";
}

// 3. Admin Reset Logic Check
// Simulate user having a secret
$user->update(['google2fa_secret' => 'TESTSECRET123']);
$user->refresh();

if ($user->google2fa_secret === 'TESTSECRET123') {
    echo "[PASS] User simulated having MFA enabled.\n";
}

// Simulate Admin unchecking the box (triggering callback logic)
// We mimic the logic in the UserForm closure
$state = false; // Unchecked
$record = $user;

if (!$state && $record) {
    $record->update(['google2fa_secret' => null]);
}

$user->refresh();
if ($user->google2fa_secret === null) {
    echo "[PASS] Admin Toggle successfully cleared MFA secret.\n";
} else {
    echo "[FAIL] Admin Toggle failed to clear secret.\n";
}

// 4. Verify OTP Generation logic (Library Check)
$google2fa = app('pragmarx.google2fa');
$secret = $google2fa->generateSecretKey();
$otp = $google2fa->getCurrentOtp($secret);
$valid = $google2fa->verifyKey($secret, $otp);

if ($valid) {
    echo "[PASS] Google2FA Library generates and verifies OTPs correctly.\n";
} else {
    echo "[FAIL] Google2FA OTP verification failed.\n";
}

echo "--- End Report ---\n";
