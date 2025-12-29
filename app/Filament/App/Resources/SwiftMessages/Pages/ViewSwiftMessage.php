<?php

namespace App\Filament\App\Resources\SwiftMessages\Pages;

use App\Filament\App\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSwiftMessage extends ViewRecord
{
    protected static string $resource = SwiftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            // ashraf29122025 : added Back button
            \Filament\Actions\Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
