<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Auth\SetupAccount;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/setup-account/{id}/{hash}', SetupAccount::class)
    ->middleware(['signed'])
    ->name('setup.account');
Route::post('/user/location', [\App\Http\Controllers\UserLocationController::class, 'store'])
    ->middleware('auth:web,admin')
    ->name('user.location.store');

Route::middleware(['auth:web,admin'])->group(function () {
    Route::get('/mfa/setup', [\App\Http\Controllers\MfaController::class, 'setup'])->name('mfa.setup');
    Route::post('/mfa/enable', [\App\Http\Controllers\MfaController::class, 'enable'])->name('mfa.enable')->middleware('throttle:6,1');
    Route::get('/mfa/verify', [\App\Http\Controllers\MfaController::class, 'showVerify'])->name('mfa.verify');
    Route::post('/mfa/verify', [\App\Http\Controllers\MfaController::class, 'verify'])->name('mfa.verify.post')->middleware('throttle:6,1');
    Route::post('/mfa/logout', [\App\Http\Controllers\MfaController::class, 'logout'])->name('mfa.logout');

    // Swift Download
    Route::get('/swift/download/{date}/{type}', [\App\Http\Controllers\SwiftDownloadController::class, 'downloadCsv'])->name('swift.download.csv');
});
