<?php

namespace App\Filament\Resources\Penanganans\Pages;

use App\Filament\Resources\Penanganans\PenangananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenanganan extends EditRecord
{
    protected static string $resource = PenangananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Penanganan')
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
