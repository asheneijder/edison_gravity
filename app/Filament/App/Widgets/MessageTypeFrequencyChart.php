<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SwiftMessage;
use Illuminate\Support\Facades\DB;

class MessageTypeFrequencyChart extends ChartWidget
{
    protected ?string $heading = 'Message Type Frequency';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = SwiftMessage::query()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                ],
            ],
            'labels' => $data->pluck('type')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
