<?php

namespace App\Filament\Widgets;

use App\Models\Klasifikasi;
use Filament\Widgets\ChartWidget;

class KlasifikasiHasilChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Hasil Klasifikasi';

    protected ?string $pollingInterval = '10s';

    public ?string $filter = 'ig';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            'ig' => 'Naive Bayes + Information Gain',
            'nb' => 'Naive Bayes',
            'akhir' => 'Hasil Klasifikasi Akhir',
        ];
    }

    protected function getData(): array
    {
        $column = match ($this->filter) {
            'nb' => 'hasil_naive_bayes',
            'akhir' => 'hasil_klasifikasi',
            default => 'hasil_ig_naive_bayes',
        };

        $labels = [
            'Baik',
            'Perlu Pembinaan',
            'Bermasalah',
        ];

        $data = collect($labels)
            ->map(fn (string $label): int => Klasifikasi::query()->where($column, $label)->count('id'))
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}