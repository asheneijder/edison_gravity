<?php

namespace App\Filament\App\Resources\SwiftMessages\Pages;

use App\Filament\App\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSwiftMessages extends ListRecords
{
    protected static string $resource = SwiftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(null) // ashraf29122025: disable default link to handle click server-side
                ->action(function ($action) {
                    // ashraf29122025 : intercept click, check permission, and show toast if disabled
                    if (!\Illuminate\Support\Facades\Auth::user()->can_edit_swift_messages) {
                        \Filament\Notifications\Notification::make()
                            ->title('This function has been disable.')
                            ->warning()
                            ->duration(10000)
                            ->send();

                        $action->halt();
                    }

                    // If allowed, proceed to standard create page redirect
                    return redirect(SwiftMessageResource::getUrl('create'));
                }),
        ];
    }
}
