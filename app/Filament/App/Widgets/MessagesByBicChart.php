<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SwiftMessage;
use Illuminate\Support\Facades\DB;

class MessagesByBicChart extends ChartWidget
{
    protected ?string $heading = 'Messages by Sender BIC';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = SwiftMessage::query()
            ->select('frm_BIC', DB::raw('count(*) as count'))
            ->groupBy('frm_BIC')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('frm_BIC')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
