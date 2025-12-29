<?php

namespace App\Filament\Admin\Resources\UrlSettings;

use App\Filament\Admin\Resources\UrlSettings\Pages\CreateUrlSetting;
use App\Filament\Admin\Resources\UrlSettings\Pages\EditUrlSetting;
use App\Filament\Admin\Resources\UrlSettings\Pages\ListUrlSettings;
use App\Filament\Admin\Resources\UrlSettings\Schemas\UrlSettingForm;
use App\Filament\Admin\Resources\UrlSettings\Tables\UrlSettingsTable;
use App\Models\UrlSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UrlSettingResource extends Resource
{
    protected static ?string $model = UrlSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'URL Setup';

    protected static ?string $modelLabel = 'URL Setup';

    protected static ?string $recordTitleAttribute = 'url';

    public static function form(Schema $schema): Schema
    {
        return UrlSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UrlSettingsTable::configure($table);
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
            'index' => ListUrlSettings::route('/'),
            'create' => CreateUrlSetting::route('/create'),
            'edit' => EditUrlSetting::route('/{record}/edit'),
        ];
    }
}
