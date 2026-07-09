<?php

namespace App\Filament\Resources\Penanganans\Pages;

use App\Filament\Resources\Penanganans\PenangananResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePenanganan extends CreateRecord
{
    protected static string $resource = PenangananResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
