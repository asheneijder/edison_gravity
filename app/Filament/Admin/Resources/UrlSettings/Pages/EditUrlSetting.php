<?php

namespace App\Filament\Admin\Resources\UrlSettings\Pages;

use App\Filament\Admin\Resources\UrlSettings\UrlSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUrlSetting extends EditRecord
{
    protected static string $resource = UrlSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
