<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use App\Models\SwiftMessage;

class SwiftDashboard extends Page
{
    protected string $view = 'filament.app.pages.swift-dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Swift Messages';

    protected static ?string $title = 'Swift Messages';

    public function getViewData(): array
    {
        $messages = \App\Models\SwiftMessage::orderBy('system_datime', 'desc')->get();

        $groupedMessages = $messages->groupBy(function ($item) {
            return $item->system_datime->format('Y-m-d');
        })->map(function ($dateGroup) {
            return $dateGroup->groupBy('type');
        });

        return [
            'groupedMessages' => $groupedMessages,
        ];
    }
}
