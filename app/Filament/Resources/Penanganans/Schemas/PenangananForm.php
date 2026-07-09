<?php

namespace App\Filament\Resources\Penanganans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenangananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data Pelanggaran')
                    ->columns(2)
                    ->schema([
                        Select::make('pelanggaran_id')
                            ->label('Pelanggaran Siswa')
                            ->relationship('pelanggaran', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->siswa?->nama ?? 'Siswa tidak ditemukan').' - '.($record->jenisPelanggaran?->nama_jenis ?? 'Pelanggaran tidak ditemukan')))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        DatePicker::make('tanggal_penanganan')
                            ->label('Tanggal Penanganan')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Detail Penanganan')
                    ->columns(2)
                    ->schema([
                        Select::make('tindakan')
                            ->label('Tindakan')
                            ->options([
                                'Teguran Lisan' => 'Teguran Lisan',
                                'Teguran Tertulis' => 'Teguran Tertulis',
                                'Pemanggilan Orang Tua' => 'Pemanggilan Orang Tua',
                                'Konseling' => 'Konseling',
                                'Skorsing' => 'Skorsing',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('user_id')
                            ->label('Petugas Penanganan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn (): ?int => auth()->id())
                            ->native(false),
                    ]),

                Section::make('Catatan')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Penanganan')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
