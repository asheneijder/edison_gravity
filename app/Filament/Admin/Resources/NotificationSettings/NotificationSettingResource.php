<?php

namespace App\Filament\Admin\Resources\NotificationSettings;

use App\Filament\Admin\Resources\NotificationSettings\Pages\CreateNotificationSetting;
use App\Filament\Admin\Resources\NotificationSettings\Pages\EditNotificationSetting;
use App\Filament\Admin\Resources\NotificationSettings\Pages\ListNotificationSettings;
use App\Filament\Admin\Resources\NotificationSettings\Schemas\NotificationSettingForm;
use App\Filament\Admin\Resources\NotificationSettings\Tables\NotificationSettingsTable;
use App\Models\NotificationSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationSettingResource extends Resource
{
    protected static ?string $model = NotificationSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Notification Setup';

    protected static ?string $modelLabel = 'Notification Setup';

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Schema $schema): Schema
    {
        return NotificationSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationSettings::route('/'),
            'create' => CreateNotificationSetting::route('/create'),
            'edit' => EditNotificationSetting::route('/{record}/edit'),
        ];
    }
}
