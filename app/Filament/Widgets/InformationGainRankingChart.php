<?php

namespace App\Filament\Widgets;

use App\Models\InformationGainResult;
use Filament\Widgets\ChartWidget;

class InformationGainRankingChart extends ChartWidget
{
    protected ?string $heading = 'Ranking Fitur Berdasarkan Information Gain';

    protected ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    protected function getData(): array
    {
        $results = InformationGainResult::query()
            ->orderBy('ranking')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Gain',
                    'data' => $results->pluck('gain')->map(fn ($value): float => (float) $value)->toArray(),
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $results->pluck('fitur')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}