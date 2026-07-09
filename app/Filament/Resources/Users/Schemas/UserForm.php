<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data Akun')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->autocomplete(false),

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->autocomplete(false),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->autocomplete(false),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Diverifikasi Pada')
                            ->seconds(false),

                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => 'Guru BK',
                                'admin' => 'OSIS',
                                'kepala_sekolah' => 'Kepala Sekolah',
                                'wali_murid' => 'Wali Murid',
                            ])
                            ->default('wali_murid')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Data Profil')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Nomor HP')
                            ->tel()
                            ->maxLength(30)
                            ->autocomplete(false),

                        Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama_kelas')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->native(false),

                        TextInput::make('alamat')
                            ->label('Alamat')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}