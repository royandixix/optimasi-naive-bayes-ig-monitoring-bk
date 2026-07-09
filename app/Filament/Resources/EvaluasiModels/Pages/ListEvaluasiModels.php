<?php

namespace App\Filament\Resources\EvaluasiModels\Pages;

use App\Filament\Resources\EvaluasiModels\EvaluasiModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluasiModels extends ListRecords
{
    protected static string $resource = EvaluasiModelResource::class;

    protected static ?string $title = 'Evaluasi Model';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Evaluasi')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}