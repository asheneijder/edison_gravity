<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    // ashraf29122025 : show setup page with qr code 4 user to scan
    public function setup()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        // If already set up, redirect handling will be done by middleware usually, 
        // but valid to check here too.
        if ($user->google2fa_secret) {
            return redirect()->route($this->getDashboardRoute($user));
        }

        $secret = $google2fa->generateSecretKey();

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('mfa.setup', ['QR_Image' => $QR_Image, 'secret' => $secret]);
    }

    // ashraf29122025 : logic to enable mfa, verifies otp & saves secret if correct
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'secret' => 'required',
        ]);

        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($request->secret, $request->otp);

        if ($valid) {
            $user = Auth::user();
            $user->google2fa_secret = $request->secret;
            $user->save();

            // Login logic for Google2FA package or manual session
            session(['mfa_verified' => true]);

            return redirect()->route($this->getDashboardRoute($user));
        }

        return back()->with('error', 'Invalid OTP Code.');
    }

    // ashraf29122025 : show verify page where user key in otp
    public function showVerify()
    {
        return view('mfa.verify');
    }

    // ashraf29122025 : verify otp code, if ok redirect dashboard, if fail error
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if ($valid) {
            session(['mfa_verified' => true]);
            if ($request->wantsJson()) {
                return response()->json(['status' => 'ok']);
            }
            return redirect()->intended(route($this->getDashboardRoute($user)));
        }

        return back()->with('error', 'Invalid OTP Code.');
    }
    // ashraf29122025 : helper to check user role & send to correct dashboard admin/app
    private function getDashboardRoute($user)
    {
        return $user->role === 'admin'
            ? 'filament.admin.pages.dashboard'
            : 'filament.app.pages.dashboard';
    }
    // ashraf29122025 : logout user so they can login again (back button)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
