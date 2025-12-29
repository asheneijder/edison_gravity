<?php

namespace App\Filament\Admin\Resources\UrlSettings\Pages;

use App\Filament\Admin\Resources\UrlSettings\UrlSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUrlSettings extends ListRecords
{
    protected static string $resource = UrlSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
