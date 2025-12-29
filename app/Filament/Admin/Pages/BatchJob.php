<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\LoginLog;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class BatchJob extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.batch-job';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    // ashraf29122025 : set page title here
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Batch Job';
    }

    public ?array $data = [];



    // ashraf29122025 : form schema setup, section 1 clean logs, section 2 placebo for now
    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('1. Log Cleaning')
                    ->description('Select a date range to permanently delete login logs.')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->afterOrEqual('start_date'),
                        \Filament\Schemas\Components\Actions::make([
                            \Filament\Actions\Action::make('cleanLogs')
                                ->label('Clean Logs')
                                ->color('danger')
                                ->action('cleanLogs'),
                        ])->fullWidth(),
                    ])
                    ->columns(2),
                Section::make('2. Adhoc Processing')
                    ->description('Process SWIFT MT/MX files from Storage.')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('info')
                            ->label('Input Directory')
                            ->content('Upload files to: storage/app/swift/inbound'),
                        \Filament\Schemas\Components\Actions::make([
                            \Filament\Actions\Action::make('runSwiftEngine')
                                ->label('Run Adhoc Processing')
                                ->color('primary')
                                ->action('runSwiftEngine'),
                        ])->fullWidth(),
                    ]),
            ]);
    }

    // ashraf29122025 : action 2 delete logs based on date range selected
    public function cleanLogs()
    {
        $data = $this->form->getState();

        $count = LoginLog::whereBetween('created_at', [
            $data['start_date'] . ' 00:00:00',
            $data['end_date'] . ' 23:59:59'
        ])->delete();

        Notification::make()
            ->title('Logs Cleaned')
            ->body("Deleted {$count} log entries.")
            ->success()
            ->send();

        // Reset form
        $this->form->fill();
    }

    // ashraf29122025 : trigger swift engine processing
    public function runSwiftEngine(\App\Services\SwiftEngineService $engine)
    {
        // Use 'swift' disk, paths relative to storage/app/swift
        $disk = 'swift';
        $inbound = 'inbound';
        $outbound = 'outbound';

        // Ensure directories exist here too just in case
        \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($inbound);
        \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($outbound);

        $stats = ['processed' => 0, 'duplicates' => 0, 'errors' => 0];

        $engine->processInboundFiles($disk, $inbound, $outbound, function ($type, $msg) use (&$stats) {
            if ($type === 'info' && str_contains($msg, '[ACCEPTED]')) {
                $stats['processed']++;
            }
            if ($type === 'warn' && str_contains($msg, '[DUPLICATE]')) {
                $stats['duplicates']++;
            }
            if ($type === 'error') {
                $stats['errors']++;
            }
        });

        Notification::make()
            ->title('Adhoc Processing Complete')
            ->body("Processed: {$stats['processed']} | Skipped (Check DB): {$stats['duplicates']} | Errors: {$stats['errors']}")
            ->success()
            ->send()
            // ashraf29122025 : notify user via database for persistence
            ->sendToDatabase(\Illuminate\Support\Facades\Auth::user());
    }
}
