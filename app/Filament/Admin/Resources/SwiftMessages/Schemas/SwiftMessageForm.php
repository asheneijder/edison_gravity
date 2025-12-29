<?php

namespace App\Filament\Admin\Resources\SwiftMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SwiftMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('frm_BIC'),
                TextInput::make('to_BIC'),
                TextInput::make('messages'),
                DateTimePicker::make('system_datime')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('source_file'),
            ]);
    }
}
