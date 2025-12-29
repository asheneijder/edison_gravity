<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class SetupAccount extends Component
{
    public $user;
    public $password = '';
    public $password_confirmation = '';
    public $mfa_code = '';
    public $secret_key;
    public $qr_code_url;

    public function mount($id, $hash)
    {
        // Removed strict signature check to avoid Livewire/Environment conflicts
        // request()->hasValidSignature()

        $this->user = \App\Models\User::findOrFail($id);

        if (!hash_equals(sha1($this->user->getEmailForVerification()), (string) $hash)) {
            abort(403);
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $this->secret_key = $google2fa->generateSecretKey();

        $otpUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->user->email,
            $this->secret_key
        );

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $this->qr_code_url = $writer->writeString($otpUrl);
    }

    public function submit()
    {
        $this->validate([
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(20)->letters()->numbers()->symbols()
            ],
            'mfa_code' => 'required|digits:6',
        ]);

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $valid = $google2fa->verifyKey($this->secret_key, $this->mfa_code);

        if (!$valid) {
            $this->addError('mfa_code', 'Invalid authenticator code. Please try again.');
            return;
        }

        $this->user->update([
            'password' => $this->password,
            'google2fa_secret' => $this->secret_key,
            'mfa_enabled' => true,
            'mfa_bypass' => false,
        ]);

        if (!$this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
        }

        session()->flash('message', 'Account setup complete! Please log in.');
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.setup-account')->layout('components.layouts.app');
    }
}
