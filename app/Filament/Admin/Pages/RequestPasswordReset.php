<?php

namespace App\Filament\Admin\Pages;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    // Override the request method to send custom notification
    public function request(): void
    {
        try {
            $data = $this->form->getState();
            $email = $data['email'];

            $user = \App\Models\User::where('email', $email)->first();

            if ($user) {
                // Send the custom "Welcome/Setup" notification which creates a signed URL
                // This reuses the SetupAccount flow which handles password + MFA
                $user->notify(new \App\Notifications\WelcomeSetPassword());
            }

            // We mimic the standard response so we don't leak user existence
            Notification::make()
                ->title(__("We have emailed your password reset link."))
                ->success()
                ->send();

            $this->form->fill();

        } catch (\Exception $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
