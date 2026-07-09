<?php

namespace App\Filament\Resources\Klasifikasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KlasifikasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

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
                    ->label('Jumlah')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_poin')
                    ->label('Poin')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('label_aktual')
                    ->label('Aktual')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Perlu Pembinaan' => 'warning',
                        'Bermasalah' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('hasil_naive_bayes')
                    ->label('Naive Bayes')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Perlu Pembinaan' => 'warning',
                        'Bermasalah' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('hasil_ig_naive_bayes')
                    ->label('NB + IG')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Perlu Pembinaan' => 'warning',
                        'Bermasalah' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('probabilitas')
                    ->label('Probabilitas')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),

                TextColumn::make('metode')
                    ->label('Metode')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hasil_klasifikasi')
                    ->label('Hasil Klasifikasi')
                    ->options([
                        'Baik' => 'Baik',
                        'Perlu Pembinaan' => 'Perlu Pembinaan',
                        'Bermasalah' => 'Bermasalah',
                    ]),

                SelectFilter::make('label_aktual')
                    ->label('Label Aktual')
                    ->options([
                        'Baik' => 'Baik',
                        'Perlu Pembinaan' => 'Perlu Pembinaan',
                        'Bermasalah' => 'Bermasalah',
                    ]),

                SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Detail'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Belum ada hasil klasifikasi')
            ->emptyStateDescription('Klik tombol proses Python Naive Bayes + Information Gain untuk menghasilkan klasifikasi otomatis.');
    }
}
