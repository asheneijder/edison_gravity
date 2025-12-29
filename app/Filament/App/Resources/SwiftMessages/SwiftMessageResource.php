<?php

namespace App\Filament\App\Resources\SwiftMessages;

use App\Filament\App\Resources\SwiftMessages\Pages\CreateSwiftMessage;
use App\Filament\App\Resources\SwiftMessages\Pages\EditSwiftMessage;
use App\Filament\App\Resources\SwiftMessages\Pages\ListSwiftMessages;
use App\Filament\App\Resources\SwiftMessages\Pages\ViewSwiftMessage;
use App\Filament\App\Resources\SwiftMessages\Schemas\SwiftMessageForm;
use App\Filament\App\Resources\SwiftMessages\Schemas\SwiftMessageInfolist;
use App\Filament\App\Resources\SwiftMessages\Tables\SwiftMessagesTable;
use App\Models\SwiftMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SwiftMessageResource extends Resource
{
    protected static ?string $model = SwiftMessage::class;

    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->can_view_swift_messages;
    }

    public static function canCreate(): bool
    {
        // Allow visibility so we can show "Disabled" notification on click
        return \Illuminate\Support\Facades\Auth::user()->can_view_swift_messages;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->can_edit_swift_messages;
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationItems(): array
    {
        try {
            // Fetch distinct types.
            $types = SwiftMessage::distinct('type')->pluck('type');
        } catch (\Exception $e) {
            return [];
        }

        $items = [];

        // Add "All Messages" item first
        $items[] = \Filament\Navigation\NavigationItem::make('All Messages')
            ->group('Swift Messages')
            ->isActiveWhen(fn() => !request()->has('tableFilters.type.value') && request()->routeIs(static::getRouteBaseName() . '.*'))
            ->url(static::getUrl());

        foreach ($types as $type) {
            $description = \App\Services\SwiftCodeTranslator::translateMessageType($type);
            $label = "{$type} ({$description})";

            if (strlen($label) > 40) {
                $label = substr($label, 0, 37) . '...';
            }

            // Using input() to retrieve nested array values from query string
            $isActive = request()->input('tableFilters.type.value') == $type;

            $items[] = \Filament\Navigation\NavigationItem::make($label)
                ->group('Swift Messages')
                ->isActiveWhen(fn() => $isActive)
                ->url(static::getUrl('index', ['tableFilters' => ['type' => ['value' => $type]]]));
        }

        return $items;
    }

    protected static ?string $recordTitleAttribute = 'source_file';

    public static function form(Schema $schema): Schema
    {
        return SwiftMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SwiftMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SwiftMessagesTable::configure($table);
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
            'index' => ListSwiftMessages::route('/'),
            'create' => CreateSwiftMessage::route('/create'),
            'view' => ViewSwiftMessage::route('/{record}'),
            'edit' => EditSwiftMessage::route('/{record}/edit'),
        ];
    }
}
