<?php

namespace App\Filament\Widgets;

use App\Models\EvaluasiModel;
use Filament\Widgets\ChartWidget;

class EvaluasiModelChart extends ChartWidget
{
    protected ?string $heading = 'Evaluasi Performa Model';

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
        $naiveBayes = EvaluasiModel::query()
            ->where('metode', 'Naive Bayes')
            ->latest()
            ->first();

        $optimized = EvaluasiModel::query()
            ->where('metode', 'Naive Bayes + Information Gain')
            ->latest()
            ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Naive Bayes',
                    'data' => [
                        (float) ($naiveBayes?->akurasi ?? 0),
                        (float) ($naiveBayes?->precision ?? 0),
                        (float) ($naiveBayes?->recall ?? 0),
                        (float) ($naiveBayes?->f1_score ?? 0),
                    ],
                ],
                [
                    'label' => 'Naive Bayes + Information Gain',
                    'data' => [
                        (float) ($optimized?->akurasi ?? 0),
                        (float) ($optimized?->precision ?? 0),
                        (float) ($optimized?->recall ?? 0),
                        (float) ($optimized?->f1_score ?? 0),
                    ],
                ],
            ],
            'labels' => [
                'Akurasi',
                'Precision',
                'Recall',
                'F1 Score',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}