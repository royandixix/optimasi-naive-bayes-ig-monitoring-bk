<?php

namespace App\Filament\Resources\Pelanggarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PelanggaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
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

                TextColumn::make('jenisPelanggaran.nama_jenis')
                    ->label('Jenis Pelanggaran')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->jenisPelanggaran?->nama_jenis),

                TextColumn::make('jenisPelanggaran.aspek_pelanggaran')
                    ->label('Aspek')
                    ->badge()
                    ->sortable()
                    ->color('info'),

                TextColumn::make('jenisPelanggaran.tingkat_pelanggaran')
                    ->label('Tingkat')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Berat' => 'danger',
                        'Sedang' => 'warning',
                        'Ringan' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('jenisPelanggaran.poin')
                    ->label('Poin')
                    ->numeric()
                    ->badge()
                    ->alignCenter()
                    ->color(fn (?int $state): string => match (true) {
                        ($state ?? 0) > 15 => 'danger',
                        ($state ?? 0) >= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Ganjil' => 'info',
                        'Genap' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('jenis_pelanggaran_id')
                    ->label('Jenis Pelanggaran')
                    ->relationship('jenisPelanggaran', 'nama_jenis')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ]),

                SelectFilter::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->options(fn (): array => \App\Models\Pelanggaran::query()
                        ->whereNotNull('tahun_ajaran')
                        ->distinct()
                        ->orderBy('tahun_ajaran')
                        ->pluck('tahun_ajaran', 'tahun_ajaran')
                        ->toArray()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data pelanggaran')
            ->emptyStateDescription('Silakan tambahkan data pelanggaran siswa terlebih dahulu.');
    }
}
