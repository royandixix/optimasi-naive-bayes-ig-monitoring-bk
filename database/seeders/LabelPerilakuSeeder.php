<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LabelPerilakuSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Periode label perilaku
        |--------------------------------------------------------------------------
        | Harus sama dengan periode yang nanti digunakan untuk pelanggaran
        | dan proses klasifikasi.
        */
        $tahunAjaran = '2026/2027';
        $semester = 'Genap';

        /*
        |--------------------------------------------------------------------------
        | Pastikan tabel tersedia
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasTable('label_perilakus')) {
            throw new RuntimeException(
                'Tabel label_perilakus belum tersedia. Jalankan migration terlebih dahulu.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Cari akun Guru BK
        |--------------------------------------------------------------------------
        | labeled_by harus mengarah ke akun Guru BK.
        */
        $guruBkId = User::query()
            ->where('role', 'super_admin')
            ->orderBy('id')
            ->value('id');

        if (! $guruBkId) {
            throw new RuntimeException(
                'Akun Guru BK dengan role super_admin belum tersedia.'
            );
        }

        $siswaTable = (new Siswa())->getTable();
        $kelasTable = (new Kelas())->getTable();

        /*
        |--------------------------------------------------------------------------
        | Pastikan struktur utama siswa tersedia
        |--------------------------------------------------------------------------
        */
        foreach (['id', 'nis', 'kelas_id'] as $column) {
            if (! Schema::hasColumn($siswaTable, $column)) {
                throw new RuntimeException(
                    "Kolom {$column} tidak ditemukan pada tabel {$siswaTable}."
                );
            }
        }

        if (! Schema::hasColumn($kelasTable, 'tahun_ajaran')) {
            throw new RuntimeException(
                "Kolom tahun_ajaran tidak ditemukan pada tabel {$kelasTable}."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh siswa tahun ajaran 2026/2027
        |--------------------------------------------------------------------------
        */
        $siswas = DB::table($siswaTable)
            ->join(
                $kelasTable,
                "{$kelasTable}.id",
                '=',
                "{$siswaTable}.kelas_id"
            )
            ->where(
                "{$kelasTable}.tahun_ajaran",
                $tahunAjaran
            )
            ->orderBy("{$siswaTable}.nis")
            ->select([
                "{$siswaTable}.id as siswa_id",
                "{$siswaTable}.nis",
            ])
            ->get();

        if ($siswas->isEmpty()) {
            throw new RuntimeException(
                "Tidak ada siswa untuk tahun ajaran {$tahunAjaran}. Jalankan SiswaSeeder terlebih dahulu."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Catatan untuk setiap label
        |--------------------------------------------------------------------------
        */
        $catatan = [
            'Baik' =>
                'Siswa menunjukkan sikap disiplin, bertanggung jawab, dan tidak memiliki catatan pelanggaran yang signifikan.',

            'Perlu Pembinaan' =>
                'Siswa memiliki beberapa catatan perilaku dan membutuhkan pembinaan serta pemantauan dari Guru BK.',

            'Bermasalah' =>
                'Siswa memiliki catatan perilaku yang membutuhkan perhatian, konseling, dan penanganan lebih lanjut.',
        ];

        /*
        |--------------------------------------------------------------------------
        | Simpan label secara seimbang
        |--------------------------------------------------------------------------
        | Pola:
        | index 0 = Baik
        | index 1 = Perlu Pembinaan
        | index 2 = Bermasalah
        |
        | Untuk 45 siswa menghasilkan 15 data setiap kategori.
        */
        DB::transaction(function () use (
            $siswas,
            $tahunAjaran,
            $semester,
            $guruBkId,
            $catatan
        ): void {
            foreach ($siswas as $index => $siswa) {
                $labelAktual = match ($index % 3) {
                    0 => 'Baik',
                    1 => 'Perlu Pembinaan',
                    default => 'Bermasalah',
                };

                DB::table('label_perilakus')->updateOrInsert(
                    [
                        'siswa_id' => $siswa->siswa_id,
                        'tahun_ajaran' => $tahunAjaran,
                        'semester' => $semester,
                    ],
                    [
                        'label_aktual' => $labelAktual,
                        'catatan' => $catatan[$labelAktual],
                        'labeled_by' => $guruBkId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Tampilkan ringkasan hasil
        |--------------------------------------------------------------------------
        */
        $ringkasan = DB::table('label_perilakus')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->select(
                'label_aktual',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('label_aktual')
            ->orderBy('label_aktual')
            ->get();

        $this->command?->newLine();

        $this->command?->info(
            "{$siswas->count()} label perilaku berhasil ditambahkan."
        );

        $this->command?->info(
            "Periode: {$tahunAjaran} - {$semester}"
        );

        foreach ($ringkasan as $item) {
            $this->command?->line(
                "- {$item->label_aktual}: {$item->total} siswa"
            );
        }
    }
}