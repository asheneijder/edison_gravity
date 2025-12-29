<?php

namespace App\Filament\Admin\Resources\NotificationSettings\Pages;

use App\Filament\Admin\Resources\NotificationSettings\NotificationSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationSettings extends ListRecords
{
    protected static string $resource = NotificationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
