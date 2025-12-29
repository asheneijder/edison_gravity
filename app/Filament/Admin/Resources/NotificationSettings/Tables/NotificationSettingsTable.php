<?php

namespace App\Filament\Admin\Resources\NotificationSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class NotificationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('host')
                    ->label('Host'),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            \App\Models\NotificationSetting::where('id', '!=', $record->id)->update(['is_active' => false]);
                        }
                    }),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
