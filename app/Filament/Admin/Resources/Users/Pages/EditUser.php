<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('resetPassword')
                ->label('Send Password Reset')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Send Password Reset Link')
                ->modalDescription('Are you sure you want to send a password reset link to this user? This will allow them to set a new password and configure MFA.')
                ->action(function () {
                    $this->record->notify(new \App\Notifications\WelcomeSetPassword());
                    \Filament\Notifications\Notification::make()
                        ->title('Password reset link sent successfully.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
