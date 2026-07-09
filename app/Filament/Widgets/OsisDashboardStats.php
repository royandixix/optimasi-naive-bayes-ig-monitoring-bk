<?php

namespace App\Filament\Widgets;

use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OsisDashboardStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        return auth()->user()?->isOsis() ?? false;
    }

    protected function getStats(): array
    {
        $totalSiswa = Siswa::query()->count('id');

        $totalPelanggaran = Pelanggaran::query()->count('id');

        $pelanggaranHariIni = Pelanggaran::query()
            ->whereDate('tanggal', Carbon::today())
            ->count('id');

        $jenisPelanggaran = JenisPelanggaran::query()->count('id');

        return [
            Stat::make('Total Siswa', number_format($totalSiswa))
                ->description('Data siswa yang dapat dilihat OSIS')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Total Pelanggaran', number_format($totalPelanggaran))
                ->description('Jumlah pelanggaran yang tercatat')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            Stat::make('Pelanggaran Hari Ini', number_format($pelanggaranHariIni))
                ->description('Data pelanggaran yang dicatat hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('danger'),

            Stat::make('Jenis Pelanggaran', number_format($jenisPelanggaran))
                ->description('Referensi jenis pelanggaran siswa')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info'),
        ];
    }
}