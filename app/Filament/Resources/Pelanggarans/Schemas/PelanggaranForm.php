<?php

namespace App\Filament\Resources\Pelanggarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PelanggaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data Pelanggaran Siswa')
                    ->columns(2)
                    ->schema([
                        Select::make('siswa_id')
                            ->label('Siswa')
                            ->relationship('siswa', 'nama')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->nis ?? '-').' - '.($record->nama ?? '-').' - '.($record->kelas?->nama_kelas ?? '-')))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Select::make('jenis_pelanggaran_id')
                            ->label('Jenis Pelanggaran')
                            ->relationship('jenisPelanggaran', 'nama_jenis')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->kode_jenis ?? '-').' - '.($record->nama_jenis ?? '-').' - '.$record->poin.' poin'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Pelanggaran')
                            ->default(now())
                            ->required()
                            ->native(false),

                        Select::make('semester')
                            ->label('Semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('tahun_ajaran')
                            ->label('Tahun Ajaran')
                            ->placeholder('2025/2026')
                            ->required()
                            ->maxLength(20)
                            ->autocomplete(false),
                    ]),

                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan Pelanggaran')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
