<?php

namespace App\Filament\Resources\JenisPelanggarans\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class JenisPelanggaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jenis Pelanggaran')
                    ->description('Masukkan informasi mengenai jenis pelanggaran.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kode_jenis')
                            ->label('Kode Pelanggaran')
                            ->placeholder('Contoh: JP001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->autocomplete(false),

                        TextInput::make('nama_jenis')
                            ->label('Nama Jenis Pelanggaran')
                            ->placeholder('Contoh: Terlambat Masuk Sekolah')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('poin')
                            ->label('Poin Pelanggaran')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('Semakin besar poin, semakin berat tingkat pelanggaran.'),
                    ]),

                Section::make('Keterangan')
                    ->description('Tambahkan informasi pendukung jika diperlukan.')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(4)
                            ->placeholder('Masukkan keterangan atau penjelasan tambahan...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}