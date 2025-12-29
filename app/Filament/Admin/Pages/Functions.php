<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Models\User;

class Functions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected string $view = 'filament.admin.pages.functions'; // Verify this view exists or removed if not needed? 
    // Wait, simple Value pages don't need a view if using a Resource-like structure, but this is a custom page.
    // I need to ensure the view renders the table.
    // Or simpler: use a Resource page style logic. 
    // Actually, `make:filament-page` creates a blade view.
    // I need to add `{{ $this->table }}` to the blade view.

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                ToggleColumn::make('is_admin')
                    ->label('Admin Access')
                    ->state(fn(User $record): bool => $record->role === 'admin')
                    ->updateStateUsing(fn(User $record, $state) => $record->update(['role' => $state ? 'admin' : 'user'])),
                ToggleColumn::make('can_view_swift_messages')
                    ->label('View Swift Messages'),
                ToggleColumn::make('can_edit_swift_messages')
                    ->label('Edit/Create Swift Messages'),
            ]);
    }
}
