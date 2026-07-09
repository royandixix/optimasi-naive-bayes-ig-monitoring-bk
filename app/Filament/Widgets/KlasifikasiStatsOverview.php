<?php

namespace App\Filament\Widgets;

use App\Models\EvaluasiModel;
use App\Models\InformationGainResult;
use App\Models\Klasifikasi;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KlasifikasiStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    protected function getStats(): array
    {
        $total = Klasifikasi::query()->count('id');

        $baik = Klasifikasi::query()
            ->where('hasil_ig_naive_bayes', 'Baik')
            ->count('id');

        $pembinaan = Klasifikasi::query()
            ->where('hasil_ig_naive_bayes', 'Perlu Pembinaan')
            ->count('id');

        $bermasalah = Klasifikasi::query()
            ->where('hasil_ig_naive_bayes', 'Bermasalah')
            ->count('id');

        $evaluasi = EvaluasiModel::query()
            ->where('metode', 'Naive Bayes + Information Gain')
            ->latest()
            ->first();

        $fiturTerpilih = InformationGainResult::query()
            ->where('selected', true)
            ->count('id');

        $akurasi = (float) ($evaluasi?->akurasi ?? 0);
        $precision = (float) ($evaluasi?->precision ?? 0);
        $recall = (float) ($evaluasi?->recall ?? 0);

        return [
            Stat::make('Total Data Klasifikasi', number_format($total))
                ->description('Jumlah siswa yang sudah masuk proses klasifikasi')
                ->icon('heroicon-o-user-group')
                ->chart([2, 4, 5, 7, 8, 10, $total])
                ->color('primary'),

            Stat::make('Kategori Baik', number_format($baik))
                ->description('Siswa dengan perilaku kategori baik')
                ->icon('heroicon-o-check-circle')
                ->chart([1, 2, 3, 4, 5, 6, $baik])
                ->color('success'),

            Stat::make('Perlu Pembinaan', number_format($pembinaan))
                ->description('Siswa yang membutuhkan pembinaan lanjutan')
                ->icon('heroicon-o-exclamation-circle')
                ->chart([1, 3, 2, 4, 3, 5, $pembinaan])
                ->color('warning'),

            Stat::make('Bermasalah', number_format($bermasalah))
                ->description('Siswa dengan kategori perilaku bermasalah')
                ->icon('heroicon-o-x-circle')
                ->chart([1, 1, 2, 2, 3, 3, $bermasalah])
                ->color('danger'),

            Stat::make('Akurasi NB + IG', number_format($akurasi * 100, 2) . '%')
                ->description('Precision: ' . number_format($precision * 100, 2) . '% | Recall: ' . number_format($recall * 100, 2) . '%')
                ->icon('heroicon-o-chart-bar')
                ->chart([20, 35, 45, 60, 72, 85, $akurasi * 100])
                ->color('info'),

            Stat::make('Fitur Terpilih', number_format($fiturTerpilih))
                ->description('Fitur terbaik berdasarkan Information Gain')
                ->icon('heroicon-o-funnel')
                ->chart([1, 2, 2, 3, 4, 5, $fiturTerpilih])
                ->color('gray'),
        ];
    }
}