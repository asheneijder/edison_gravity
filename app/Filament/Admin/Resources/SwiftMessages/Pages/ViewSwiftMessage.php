<?php

namespace App\Filament\Admin\Resources\SwiftMessages\Pages;

use App\Filament\Admin\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSwiftMessage extends ViewRecord
{
    protected static string $resource = SwiftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
