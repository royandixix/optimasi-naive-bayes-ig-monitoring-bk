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

        return [
            Stat::make('Total Data Klasifikasi', number_format($total))
                ->description('Jumlah siswa yang sudah diklasifikasi')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Baik', number_format($baik))
                ->description('Siswa dengan kategori baik')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Perlu Pembinaan', number_format($pembinaan))
                ->description('Siswa yang perlu pembinaan')
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning'),

            Stat::make('Bermasalah', number_format($bermasalah))
                ->description('Siswa dengan kategori bermasalah')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Akurasi NB + IG', number_format((float) ($evaluasi?->akurasi ?? 0), 4))
                ->description('Akurasi model terbaru')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('Fitur Terpilih', number_format($fiturTerpilih))
                ->description('Fitur hasil seleksi Information Gain')
                ->icon('heroicon-o-funnel')
                ->color('gray'),
        ];
    }
}