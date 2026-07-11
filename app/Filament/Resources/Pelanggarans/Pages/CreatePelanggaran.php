<?php

namespace App\Filament\Resources\Pelanggarans\Pages;

use App\Filament\Resources\Pelanggarans\PelanggaranResource;
use App\Models\Pelanggaran;
use Filament\Resources\Pages\CreateRecord;

class CreatePelanggaran extends CreateRecord
{
    protected static string $resource =
        PelanggaranResource::class;

    protected static bool $canCreateAnother =
        false;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $user = auth()->user();

        $data['diajukan_oleh'] =
            $user->id;

        /*
         * Laporan yang dibuat Guru BK dianggap
         * langsung menjadi laporan resmi.
         */
        if ($user->isGuruBk()) {
            $data['status_pengajuan'] =
                Pelanggaran::STATUS_DISETUJUI;

            $data['diproses_oleh'] =
                $user->id;

            $data['diproses_pada'] =
                now();

            $data['catatan_verifikasi'] =
                'Laporan dibuat langsung oleh Guru BK';
        } else {
            /*
             * Laporan OSIS menunggu persetujuan.
             */
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

    protected function getCreatedNotificationTitle():
        ?string {
        if (
            auth()
                ->user()
                ?->isOsis()
        ) {
            return
                'Laporan berhasil diajukan dan menunggu persetujuan Guru BK';
        }

        return
            'Data pelanggaran berhasil dibuat';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(
            'index'
        );
    }
}