<?php

namespace App\Filament\Admin\Resources\NotificationSettings\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotificationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Server Configuration')
                    ->description('Target mail server connection settings.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('mailer')
                                    ->label('Mailer')
                                    ->default('smtp')
                                    ->required()
                                    ->placeholder('smtp'),
                                TextInput::make('scheme')
                                    ->label('Scheme')
                                    ->placeholder('null'),
                                Select::make('encryption')
                                    ->options([
                                        'tls' => 'TLS',
                                        'ssl' => 'SSL',
                                        'null' => 'None',
                                    ])
                                    ->default('tls'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('host')
                                    ->label('SMTP Host')
                                    ->default('smtp-mail.outlook.com')
                                    ->required(),
                                TextInput::make('port')
                                    ->label('Port')
                                    ->numeric()
                                    ->default(587)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Credentials')
                    ->description('Authentication details for the mail server.')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->email()
                            ->required()
                            ->helperText('Typically your email address (e.g. art_tms@artrustees.com.my)'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn($operation) => $operation === 'create')
                            ->dehydrated(fn($state) => filled($state)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Sender Identity')
                    ->description('How emails will appear to recipients.')
                    ->schema([
                        TextInput::make('from_address')
                            ->label('From Address')
                            ->email()
                            ->required()
                            ->placeholder('art_tms@artrustees.com.my'),
                        TextInput::make('from_name')
                            ->label('From Name')
                            ->placeholder('Default: App Name'),
                        TextInput::make('email')
                            ->label('Reference Email')
                            ->helperText('Internal reference only')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Set as Active Mailer')
                            ->helperText('Activating this will deactivate all other configurations.')
                            ->default(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
