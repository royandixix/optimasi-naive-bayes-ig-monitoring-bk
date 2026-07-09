<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('kode_kelas')
                    ->label('Kode Kelas')
                    ->placeholder('Contoh: VII-A')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->autocomplete(false)
                    ->columnSpan(1),

                TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->placeholder('Masukkan nama kelas')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(1),

                TextInput::make('wali_kelas')
                    ->label('Wali Kelas')
                    ->placeholder('Masukkan nama wali kelas')
                    ->maxLength(100)
                    ->columnSpan(1),

                TextInput::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->placeholder('2025/2026')
                    ->maxLength(20)
                    ->columnSpan(1),
            ]);
    }
}