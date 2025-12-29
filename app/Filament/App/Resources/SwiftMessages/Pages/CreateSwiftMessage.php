<?php

namespace App\Filament\App\Resources\SwiftMessages\Pages;

use App\Filament\App\Resources\SwiftMessages\SwiftMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSwiftMessage extends CreateRecord
{
    protected static string $resource = SwiftMessageResource::class;

    public function mount(): void
    {
        // ashraf29122025 : prevent direct URL access if disabled
        if (!\Illuminate\Support\Facades\Auth::user()->can_edit_swift_messages) {
            // This will be caught by the global handler and show the toast
            abort(403, 'This function has been disable.');
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            // ashraf29122025 : added Back button
            \Filament\Actions\Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
