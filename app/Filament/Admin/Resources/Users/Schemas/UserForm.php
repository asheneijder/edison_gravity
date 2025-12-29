<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'manager' => 'Manager',
                        'user' => 'User',
                    ])
                    ->required()
                    ->default('user'),
                Checkbox::make('mfa_bypass')
                    ->label('Bypass MFA'),
                Checkbox::make('mfa_enabled')
                    ->label('MFA Enabled')
                    ->formatStateUsing(fn($record) => $record && $record->google2fa_secret)
                    ->dehydrated(false)
                    ->afterStateUpdated(function ($state, $record) {
                        if (!$state && $record) {
                            $record->update(['google2fa_secret' => null]);
                        }
                    })
                    ->live(),
            ]);
    }
}
