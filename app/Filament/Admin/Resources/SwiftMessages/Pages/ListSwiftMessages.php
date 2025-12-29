<?php

namespace App\Filament\Admin\Resources\SwiftMessages\Pages;

use App\Filament\Admin\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSwiftMessages extends ListRecords
{
    protected static string $resource = SwiftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
