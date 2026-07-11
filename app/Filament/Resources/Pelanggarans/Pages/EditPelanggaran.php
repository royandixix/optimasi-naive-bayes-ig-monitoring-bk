<?php

namespace App\Filament\Resources\Pelanggarans\Pages;

use App\Filament\Resources\Pelanggarans\PelanggaranResource;
use App\Models\Pelanggaran;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPelanggaran extends EditRecord
{
    protected static string $resource =
        PelanggaranResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        $user = auth()->user();

        /*
         * Saat OSIS memperbaiki laporan,
         * status dikembalikan menjadi menunggu.
         */
        if ($user->isOsis()) {
            $data['status_pengajuan'] =
                Pelanggaran::STATUS_MENUNGGU;

            $data['diproses_oleh'] =
                null;

            $data['diproses_pada'] =
                null;

            $data['catatan_verifikasi'] =
                null;
        }

        return $data;
    }

    protected function getSavedNotificationTitle():
        ?string {
        if (
            auth()
                ->user()
                ?->isOsis()
        ) {
            return
                'Laporan diperbarui dan diajukan kembali kepada Guru BK';
        }

        return
            'Data pelanggaran berhasil diperbarui';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        PelanggaranResource::canDelete(
                            $this->getRecord()
                        )
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(
            'index'
        );
    }
}