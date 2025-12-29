<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

// ashraf29122025 : admin page to view and download activity logs
class Logs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.admin.pages.logs';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Activity Logs';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    // ashraf29122025 : define form schema for date separation download
    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('User Activity Logs')
                    ->description('Download logs for all user-side activities.')
                    ->schema([
                        DatePicker::make('user_start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()->subDays(7)),
                        DatePicker::make('user_end_date')
                            ->label('End Date')
                            ->required()
                            ->default(now()),
                        Actions::make([
                            Action::make('downloadUserLogs')
                                ->label('Download CSV')
                                ->action(fn() => $this->downloadLogs('user'))
                                ->color('primary'),
                        ])->fullWidth(),
                    ])->columns(2),

                Section::make('Admin Activity Logs')
                    ->description('Download logs for all admin-side activities.')
                    ->schema([
                        DatePicker::make('admin_start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()->subDays(7)),
                        DatePicker::make('admin_end_date')
                            ->label('End Date')
                            ->required()
                            ->default(now()),
                        Actions::make([
                            Action::make('downloadAdminLogs')
                                ->label('Download CSV')
                                ->action(fn() => $this->downloadLogs('admin'))
                                ->color('primary'),
                        ])->fullWidth(),
                    ])->columns(2),
            ]);
    }

    // ashraf29122025 : handle csv download logic with streaming
    public function downloadLogs(string $scope)
    {
        $data = $this->form->getState();
        $startDate = $scope === 'user' ? $data['user_start_date'] : $data['admin_start_date'];
        $endDate = $scope === 'user' ? $data['user_end_date'] : $data['admin_end_date'];

        $filename = "{$scope}_activity_logs_" . now()->format('Y-m-d_H-i-s') . ".csv";

        return response()->streamDownload(function () use ($scope, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['ID', 'User', 'Action', 'Description', 'IP Address', 'Location', 'Timestamp']);

            ActivityLog::query()
                ->where('scope', $scope)
                ->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->with('user')
                ->chunk(1000, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        // Safe location parsing (handle usage of array or null)
                        $loc = $log->location;
                        // If $loc is array (casted), let's just dump it as json or pick country?
                        // User likely wants readable location. Stevebauman location typically returns object/array.
                        // Let's try to extract City, Country or just JSON encode it.
                        $locationString = is_array($loc)
                            ? implode(', ', array_filter([
                                $loc['cityName'] ?? null,
                                $loc['regionName'] ?? null,
                                $loc['countryName'] ?? null
                            ]))
                            : json_encode($loc);

                        fputcsv($handle, [
                            $log->id,
                            $log->user ? $log->user->name : 'Guest/System',
                            $log->action,
                            $log->description,
                            $log->ip_address,
                            trim($locationString, ', '), // Clean up if empty
                            $log->created_at->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename);
    }
}
