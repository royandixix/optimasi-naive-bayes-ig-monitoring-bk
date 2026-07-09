<?php

namespace App\Filament\Widgets;

use App\Models\Klasifikasi;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestKlasifikasiTable extends TableWidget
{
    protected static ?string $heading = 'Hasil Klasifikasi Terbaru';

    protected static ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Klasifikasi::query()
                    ->with(['siswa.kelas'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('siswa.kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('jumlah_pelanggaran')
                    ->label('Jumlah Pelanggaran')
                    ->sortable(),

                TextColumn::make('total_poin')
                    ->label('Total Poin')
                    ->sortable(),

                TextColumn::make('hasil_ig_naive_bayes')
                    ->label('Hasil NB + IG')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Perlu Pembinaan' => 'warning',
                        'Bermasalah' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-'),

                TextColumn::make('probabilitas_ig_naive_bayes')
                    ->label('Probabilitas')
                    ->formatStateUsing(fn ($state): string => $state !== null ? number_format((float) $state, 4) : '-')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}