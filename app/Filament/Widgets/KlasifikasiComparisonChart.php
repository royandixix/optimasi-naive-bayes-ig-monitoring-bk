<?php

namespace App\Filament\Widgets;

use App\Models\Klasifikasi;
use Filament\Widgets\ChartWidget;

class KlasifikasiComparisonChart extends ChartWidget
{
    protected ?string $heading = 'Perbandingan Hasil Naive Bayes dan NB + Information Gain';

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
        $labels = [
            'Baik',
            'Perlu Pembinaan',
            'Bermasalah',
        ];

        $naiveBayes = collect($labels)
            ->map(fn (string $label): int => Klasifikasi::query()->where('hasil_naive_bayes', $label)->count('id'))
            ->toArray();

        $optimized = collect($labels)
            ->map(fn (string $label): int => Klasifikasi::query()->where('hasil_ig_naive_bayes', $label)->count('id'))
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Naive Bayes',
                    'data' => $naiveBayes,
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
                [
                    'label' => 'Naive Bayes + Information Gain',
                    'data' => $optimized,
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
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
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}