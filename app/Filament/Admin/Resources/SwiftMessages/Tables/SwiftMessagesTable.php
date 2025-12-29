<?php

namespace App\Filament\Admin\Resources\SwiftMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SwiftMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('system_datime')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('frm_BIC')
                    ->label('Sender (BIC)')
                    ->searchable(),
                TextColumn::make('to_BIC')
                    ->label('Receiver (BIC)')
                    ->searchable(),
                TextColumn::make('source_file')
                    ->label('Source File')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location')
                    ->label('Location')
                    ->state(function (\App\Models\SwiftMessage $record) {
                        $filename = $record->file_directory;
                        if (!$filename) {
                            $directory = "outbound/{$record->type}";
                            $files = \Illuminate\Support\Facades\Storage::disk('swift')->files($directory);
                            $pattern = "/^{$record->type}_.*_" . preg_quote($record->frm_BIC) . "-" . preg_quote($record->to_BIC) . "_.*\.csv$/i";
                            foreach ($files as $file) {
                                if (preg_match($pattern, basename($file))) {
                                    $filename = basename($file);
                                    break;
                                }
                            }
                        }
                        $filename = $filename ?? $record->source_file;
                        return \Illuminate\Support\Facades\Storage::disk('swift')->path('outbound/' . $record->type . '/' . $filename);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->default(request()->input('tableFilters.type.value'))
                    ->options(function () {
                        try {
                            return \App\Models\SwiftMessage::distinct('type')
                                ->pluck('type', 'type')
                                ->map(fn($type) => $type . ' - ' . \App\Services\SwiftCodeTranslator::translateMessageType($type))
                                ->toArray();
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->searchable(),
            ])
            // modifyQueryUsing removed to allow SelectFilter to handle state persistence correctly across actions
            ->recordActions([
                ViewAction::make(),
                \Filament\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (\App\Models\SwiftMessage $record) {
                        $filename = $record->file_directory;
                        if (!$filename) {
                            $directory = "outbound/{$record->type}";
                            $files = \Illuminate\Support\Facades\Storage::disk('swift')->files($directory);
                            $pattern = "/^{$record->type}_.*_" . preg_quote($record->frm_BIC) . "-" . preg_quote($record->to_BIC) . "_.*\.csv$/i";
                            foreach ($files as $file) {
                                if (preg_match($pattern, basename($file))) {
                                    $filename = basename($file);
                                    break;
                                }
                            }
                        }
                        $filename = $filename ?? $record->source_file;
                        return response()->download(\Illuminate\Support\Facades\Storage::disk('swift')->path('outbound/' . $record->type . '/' . $filename), $filename);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
