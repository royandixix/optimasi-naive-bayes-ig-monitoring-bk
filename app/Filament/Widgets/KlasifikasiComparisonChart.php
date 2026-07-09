<?php

namespace App\Filament\Widgets;

use App\Models\Klasifikasi;
use Filament\Widgets\ChartWidget;

class KlasifikasiComparisonChart extends ChartWidget
{
    protected ?string $heading = 'Perbandingan Naive Bayes dan Naive Bayes + Information Gain';

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
                ],
                [
                    'label' => 'Naive Bayes + Information Gain',
                    'data' => $optimized,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}