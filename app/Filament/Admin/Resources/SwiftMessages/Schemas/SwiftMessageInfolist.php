<?php

namespace App\Filament\Admin\Resources\SwiftMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SwiftMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Message Attributes')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('type')->label('Message Type'),
                        TextEntry::make('system_datime')->label('Processed At')->dateTime(),
                        TextEntry::make('source_file')->label('Source File'),
                        TextEntry::make('frm_BIC')->label('Sender'),
                        TextEntry::make('to_BIC')->label('Receiver'),
                    ]),
                \Filament\Schemas\Components\Section::make('Message Data')
                    ->description('Full parsed content of the SWIFT message.')
                    ->schema([
                        \Filament\Infolists\Components\KeyValueEntry::make('messages')
                            ->label('')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                    ]),
            ]);
    }
}
