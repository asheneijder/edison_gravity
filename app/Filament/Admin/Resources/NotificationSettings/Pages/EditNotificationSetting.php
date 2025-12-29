<?php

namespace App\Filament\Admin\Resources\NotificationSettings\Pages;

use App\Filament\Admin\Resources\NotificationSettings\NotificationSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNotificationSetting extends EditRecord
{
    protected static string $resource = NotificationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
