<?php

namespace App\Filament\App\Resources\SwiftMessages\Pages;

use App\Filament\App\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSwiftMessage extends EditRecord
{
    protected static string $resource = SwiftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
