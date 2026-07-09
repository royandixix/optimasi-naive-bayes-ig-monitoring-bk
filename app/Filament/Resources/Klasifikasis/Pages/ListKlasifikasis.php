<?php

namespace App\Filament\Resources\Klasifikasis\Pages;

use App\Filament\Resources\Klasifikasis\KlasifikasiResource;
use App\Services\PythonNaiveBayesInformationGainService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListKlasifikasis extends ListRecords
{
    protected static string $resource = KlasifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('proses_algoritma')
                ->label('Proses Naive Bayes + Information Gain')
                ->icon('heroicon-o-cpu-chip')
                ->color('primary')
                ->visible(fn (): bool => auth()->user()?->isGuruBk() ?? false)
                ->modalHeading('Proses Klasifikasi Naive Bayes + Information Gain')
                ->modalSubmitActionLabel('Proses Sekarang')
                ->schema([
                    TextInput::make('tahun_ajaran')
                        ->label('Tahun Ajaran')
                        ->placeholder('2025/2026')
                        ->maxLength(20),

                    Select::make('semester')
                        ->label('Semester')
                        ->options([
                            'Ganjil' => 'Ganjil',
                            'Genap' => 'Genap',
                        ])
                        ->native(false),

                    TextInput::make('training_ratio')
                        ->label('Rasio Data Training')
                        ->numeric()
                        ->default(0.8)
                        ->minValue(0.1)
                        ->maxValue(0.9)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $result = app(PythonNaiveBayesInformationGainService::class)->run(
                        tahunAjaran: $data['tahun_ajaran'] ?? null,
                        semester: $data['semester'] ?? null,
                        trainingRatio: (float) ($data['training_ratio'] ?? 0.8),
                    );

                    Notification::make()
                        ->title('Klasifikasi berhasil diproses')
                        ->body('Total data: ' . ($result['total_data'] ?? 0) . '. Akurasi Naive Bayes + Information Gain: ' . number_format((float) ($result['optimized_metrics']['accuracy'] ?? 0), 4))
                        ->success()
                        ->send();
                }),
        ];
    }
}