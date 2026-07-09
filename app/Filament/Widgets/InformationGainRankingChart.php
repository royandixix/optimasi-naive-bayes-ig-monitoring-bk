<?php

namespace App\Filament\Widgets;

use App\Models\InformationGainResult;
use Filament\Widgets\ChartWidget;

class InformationGainRankingChart extends ChartWidget
{
    protected ?string $heading = 'Ranking Information Gain';

    protected ?string $pollingInterval = '10s';

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
                ],
            ],
            'labels' => $results->pluck('fitur')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}