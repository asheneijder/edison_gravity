<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->description('Basic user information and credentials.')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'user' => 'User',
                            ])
                            ->required()
                            ->default('user'),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->rules([
                                fn($get) => $get('password_policy_enforced')
                                ? Password::min(20)->letters()->numbers()->symbols()
                                : null,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Security Settings')
                    ->description('Configure security policies and access controls.')
                    ->schema([
                        Checkbox::make('password_policy_enforced')
                            ->label('Enforce Strong Password Policy')
                            ->default(true)
                            ->reactive(),
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
                    ])
                    ->columns(3),
            ]);
    }
}
