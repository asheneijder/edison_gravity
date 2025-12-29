<?php

namespace App\Filament\Admin\Resources\UrlSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UrlSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Configuration')
                    ->schema([
                        TextInput::make('url')
                            ->label('Application URL')
                            ->placeholder('https://example.com')
                            ->url()
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Set as Active URL')
                            ->helperText('This URL will be used as the root URL for the application.')
                            ->default(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
