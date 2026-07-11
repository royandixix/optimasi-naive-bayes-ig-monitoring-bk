<?php

namespace App\Filament\Resources\Klasifikasis\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KlasifikasisTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->defaultSort(
                'updated_at',
                'desc'
            )
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

                TextColumn::make(
                    'siswa.kelas.nama_kelas'
                )
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make(
                    'tahun_ajaran'
                )
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make(
                    'jumlah_pelanggaran'
                )
                    ->label('Jumlah')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_poin')
                    ->label('Poin')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make(
                    'label_aktual'
                )
                    ->label('Aktual')
                    ->badge()
                    ->color(
                        fn (
                            ?string $state
                        ): string => match ($state) {
                            'Baik' =>
                                'success',

                            'Perlu Pembinaan' =>
                                'warning',

                            'Bermasalah' =>
                                'danger',

                            default =>
                                'gray',
                        }
                    )
                    ->placeholder(
                        'Belum dilabeli'
                    ),

                TextColumn::make(
                    'hasil_naive_bayes'
                )
                    ->label('Naive Bayes')
                    ->badge()
                    ->color(
                        fn (
                            ?string $state
                        ): string => match ($state) {
                            'Baik' =>
                                'success',

                            'Perlu Pembinaan' =>
                                'warning',

                            'Bermasalah' =>
                                'danger',

                            default =>
                                'gray',
                        }
                    ),

                TextColumn::make(
                    'probabilitas_naive_bayes'
                )
                    ->label('Prob. NB')
                    ->formatStateUsing(
                        fn ($state): string =>
                            $state !== null
                            ? number_format(
                                (float) $state * 100,
                                2
                            ).'%'
                            : '-'
                    )
                    ->alignCenter()
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make(
                    'hasil_ig_naive_bayes'
                )
                    ->label('NB + IG')
                    ->badge()
                    ->color(
                        fn (
                            ?string $state
                        ): string => match ($state) {
                            'Baik' =>
                                'success',

                            'Perlu Pembinaan' =>
                                'warning',

                            'Bermasalah' =>
                                'danger',

                            default =>
                                'gray',
                        }
                    ),

                TextColumn::make(
                    'probabilitas_ig_naive_bayes'
                )
                    ->label('Prob. NB + IG')
                    ->formatStateUsing(
                        fn ($state): string =>
                            $state !== null
                            ? number_format(
                                (float) $state * 100,
                                2
                            ).'%'
                            : '-'
                    )
                    ->alignCenter(),

                TextColumn::make('metode')
                    ->label('Metode')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make(
                    'updated_at'
                )
                    ->label('Diperbarui')
                    ->dateTime(
                        'd M Y H:i'
                    )
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make(
                    'tahun_ajaran'
                )
                    ->label('Tahun Ajaran')
                    ->options(
                        fn (): array =>
                            \App\Models\Klasifikasi::query()
                                ->whereNotNull(
                                    'tahun_ajaran'
                                )
                                ->distinct()
                                ->orderByDesc(
                                    'tahun_ajaran'
                                )
                                ->pluck(
                                    'tahun_ajaran',
                                    'tahun_ajaran'
                                )
                                ->toArray()
                    ),

                SelectFilter::make(
                    'semester'
                )
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ]),

                SelectFilter::make(
                    'hasil_ig_naive_bayes'
                )
                    ->label('Hasil NB + IG')
                    ->options([
                        'Baik' =>
                            'Baik',

                        'Perlu Pembinaan' =>
                            'Perlu Pembinaan',

                        'Bermasalah' =>
                            'Bermasalah',
                    ]),

                SelectFilter::make(
                    'label_aktual'
                )
                    ->label('Label Aktual')
                    ->options([
                        'Baik' =>
                            'Baik',

                        'Perlu Pembinaan' =>
                            'Perlu Pembinaan',

                        'Bermasalah' =>
                            'Bermasalah',
                    ]),
            ])
            ->emptyStateHeading(
                'Belum ada hasil klasifikasi'
            )
            ->emptyStateDescription(
                'Isi Label Perilaku, lalu klik Proses Naive Bayes + Information Gain.'
            );
    }
}