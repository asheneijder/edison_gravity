<?php

namespace App\Filament\Admin\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LoginLog;
use Filament\Tables;

class RecentLogsWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => LoginLog::query())
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
                Tables\Columns\TextColumn::make('location.cityName')
                    ->label('Location')
                    ->placeholder('Unknown'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Login Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
