<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = '2026/2027';

        $kelas = [
            [
                'kode_kelas' => 'VII-A',
                'nama_kelas' => 'VII A',
                'wali_kelas' => 'Budi Santoso, S.Pd.',
            ],
            [
                'kode_kelas' => 'VII-B',
                'nama_kelas' => 'VII B',
                'wali_kelas' => 'Siti Rahmawati, S.Pd.',
            ],
            [
                'kode_kelas' => 'VII-C',
                'nama_kelas' => 'VII C',
                'wali_kelas' => 'Ahmad Hidayat, S.Pd.',
            ],
            [
                'kode_kelas' => 'VIII-A',
                'nama_kelas' => 'VIII A',
                'wali_kelas' => 'Maria Magdalena, S.Pd.',
            ],
            [
                'kode_kelas' => 'VIII-B',
                'nama_kelas' => 'VIII B',
                'wali_kelas' => 'Andi Saputra, S.Pd.',
            ],
            [
                'kode_kelas' => 'VIII-C',
                'nama_kelas' => 'VIII C',
                'wali_kelas' => 'Nurul Aini, S.Pd.',
            ],
            [
                'kode_kelas' => 'IX-A',
                'nama_kelas' => 'IX A',
                'wali_kelas' => 'Yohanes Pratama, S.Pd.',
            ],
            [
                'kode_kelas' => 'IX-B',
                'nama_kelas' => 'IX B',
                'wali_kelas' => 'Dewi Lestari, S.Pd.',
            ],
            [
                'kode_kelas' => 'IX-C',
                'nama_kelas' => 'IX C',
                'wali_kelas' => 'Muhammad Arif, S.Pd.',
            ],
        ];

        foreach ($kelas as $item) {
            Kelas::query()->updateOrCreate(
                [
                    'kode_kelas' => $item['kode_kelas'],
                    'tahun_ajaran' => $tahunAjaran,
                ],
                [
                    'nama_kelas' => $item['nama_kelas'],
                    'wali_kelas' => $item['wali_kelas'],
                ]
            );
        }

        $this->command?->info(
            count($kelas) . ' data kelas berhasil ditambahkan.'
        );
    }
}