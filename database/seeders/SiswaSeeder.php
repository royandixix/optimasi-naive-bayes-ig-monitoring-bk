<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Konfigurasi data
        |--------------------------------------------------------------------------
        */
        $tahunAjaran = '2026/2027';

        $statusAktif = 'aktif';

        $lakiLaki = 'L';
        $perempuan = 'P';

        $siswaTable = (new Siswa())->getTable();

        /*
        |--------------------------------------------------------------------------
        | Pastikan tabel siswa tersedia
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasTable($siswaTable)) {
            throw new RuntimeException(
                "Tabel {$siswaTable} belum tersedia."
            );
        }

        $columns = Schema::getColumnListing($siswaTable);

        $hasColumn = static function (string $column) use ($columns): bool {
            return in_array($column, $columns, true);
        };

        /*
        |--------------------------------------------------------------------------
        | Kolom penting harus tersedia
        |--------------------------------------------------------------------------
        */
        if (! $hasColumn('nis')) {
            throw new RuntimeException(
                "Kolom nis tidak ditemukan pada tabel {$siswaTable}."
            );
        }

        if (! $hasColumn('kelas_id')) {
            throw new RuntimeException(
                "Kolom kelas_id tidak ditemukan pada tabel {$siswaTable}."
            );
        }

        if (! $hasColumn('nama') && ! $hasColumn('nama_lengkap')) {
            throw new RuntimeException(
                "Kolom nama atau nama_lengkap tidak ditemukan pada tabel {$siswaTable}."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil ID kelas berdasarkan nama dan tahun ajaran
        |--------------------------------------------------------------------------
        */
        $kelasIds = Kelas::query()
            ->where('tahun_ajaran', $tahunAjaran)
            ->pluck('id', 'nama_kelas');

        $daftarKelas = [
            'VII A',
            'VII B',
            'VII C',
            'VIII A',
            'VIII B',
            'VIII C',
            'IX A',
            'IX B',
            'IX C',
        ];

        /*
        |--------------------------------------------------------------------------
        | Pastikan seluruh kelas sudah tersedia
        |--------------------------------------------------------------------------
        */
        foreach ($daftarKelas as $namaKelas) {
            if (! $kelasIds->has($namaKelas)) {
                throw new RuntimeException(
                    "Kelas {$namaKelas} tahun ajaran {$tahunAjaran} belum tersedia. Jalankan KelasSeeder terlebih dahulu."
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar 45 siswa
        |--------------------------------------------------------------------------
        | Setiap kelas mendapatkan lima siswa.
        */
        $namaSiswa = [
            'Andi Saputra',
            'Aurelia Putri',
            'Muhammad Fajar',
            'Cecilia Natalia',
            'Rizky Pratama',

            'Gabriel Alexander',
            'Nur Aisyah',
            'Jonathan Wijaya',
            'Maria Theresia',
            'Dimas Setiawan',

            'Yoseph Adrian',
            'Siti Rahma',
            'Ferdinand Gunawan',
            'Clara Angelina',
            'Arif Hidayat',

            'Rafael Christian',
            'Nabila Ramadhani',
            'Kevin Santoso',
            'Monica Patricia',
            'Reza Maulana',

            'Stefanus Michael',
            'Putri Maharani',
            'Aldi Kurniawan',
            'Felicia Amanda',
            'Ilham Akbar',

            'Fransiskus Xavier',
            'Dewi Lestari',
            'Bagas Prakoso',
            'Veronica Melinda',
            'Fadli Rahman',

            'Vincentius Daniel',
            'Intan Permata',
            'Agus Salim',
            'Theresia Gracia',
            'Rian Firmansyah',

            'Dominikus Andre',
            'Ayu Wulandari',
            'Bima Aditya',
            'Christina Olivia',
            'Akmal Fauzan',

            'Markus Sebastian',
            'Anisa Safitri',
            'Yoga Pranata',
            'Angela Clarissa',
            'Farhan Nugraha',
        ];

        $namaAyah = [
            'Ahmad Saputra',
            'Budi Santoso',
            'Yohanes Wijaya',
            'Andi Pratama',
            'Muhammad Hidayat',
            'Rudi Hartono',
            'Anton Gunawan',
            'Joko Setiawan',
            'Samuel Alexander',
            'Hasan Maulana',
        ];

        $namaIbu = [
            'Siti Aminah',
            'Maria Natalia',
            'Dewi Lestari',
            'Nurhayati',
            'Linda Wulandari',
            'Rosalina',
            'Fatimah',
            'Theresia',
            'Yuliana',
            'Kartini',
        ];

        $tempatLahir = [
            'Makassar',
            'Maros',
            'Gowa',
            'Takalar',
            'Parepare',
        ];

        /*
        |--------------------------------------------------------------------------
        | Nomor urut NIS setiap tingkat
        |--------------------------------------------------------------------------
        */
        $nomorUrutPerTingkat = [
            'VII' => 1,
            'VIII' => 1,
            'IX' => 1,
        ];

        $indexSiswa = 0;

        DB::transaction(function () use (
            $daftarKelas,
            $kelasIds,
            $namaSiswa,
            $namaAyah,
            $namaIbu,
            $tempatLahir,
            $tahunAjaran,
            $statusAktif,
            $lakiLaki,
            $perempuan,
            $siswaTable,
            $hasColumn,
            &$nomorUrutPerTingkat,
            &$indexSiswa
        ): void {
            foreach ($daftarKelas as $namaKelas) {
                /*
                |--------------------------------------------------------------------------
                | Tentukan tingkat kelas
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($namaKelas, 'VIII')) {
                    $tingkat = 'VIII';
                } elseif (str_starts_with($namaKelas, 'VII')) {
                    $tingkat = 'VII';
                } else {
                    $tingkat = 'IX';
                }

                /*
                |--------------------------------------------------------------------------
                | Tahun masuk berdasarkan tingkat
                |--------------------------------------------------------------------------
                */
                $tahunMasuk = match ($tingkat) {
                    'VII' => 2026,
                    'VIII' => 2025,
                    'IX' => 2024,
                };

                /*
                |--------------------------------------------------------------------------
                | Tahun lahir berdasarkan tingkat
                |--------------------------------------------------------------------------
                */
                $tahunLahir = match ($tingkat) {
                    'VII' => 2013,
                    'VIII' => 2012,
                    'IX' => 2011,
                };

                /*
                |--------------------------------------------------------------------------
                | Buat lima siswa untuk setiap kelas
                |--------------------------------------------------------------------------
                */
                for ($i = 0; $i < 5; $i++) {
                    $nomorTingkat = $nomorUrutPerTingkat[$tingkat];

                    /*
                    |--------------------------------------------------------------------------
                    | Contoh NIS:
                    | VII  = 2026001
                    | VIII = 2025001
                    | IX   = 2024001
                    |--------------------------------------------------------------------------
                    */
                    $nis = $tahunMasuk . str_pad(
                        (string) $nomorTingkat,
                        3,
                        '0',
                        STR_PAD_LEFT
                    );

                    $nisn = '00' . $nis;

                    $jenisKelamin = $indexSiswa % 2 === 0
                        ? $lakiLaki
                        : $perempuan;

                    $bulanLahir = (($indexSiswa * 2) % 12) + 1;
                    $tanggalLahirAngka = (($indexSiswa * 3) % 27) + 1;

                    $tanggalLahir = sprintf(
                        '%04d-%02d-%02d',
                        $tahunLahir,
                        $bulanLahir,
                        $tanggalLahirAngka
                    );

                    $nomorHpOrtu = '0812' . str_pad(
                        (string) ($indexSiswa + 1),
                        8,
                        '0',
                        STR_PAD_LEFT
                    );

                    $data = [];

                    /*
                    |--------------------------------------------------------------------------
                    | Identitas utama siswa
                    |--------------------------------------------------------------------------
                    */
                    $data['nis'] = $nis;

                    if ($hasColumn('nisn')) {
                        $data['nisn'] = $nisn;
                    }

                    if ($hasColumn('nama')) {
                        $data['nama'] = $namaSiswa[$indexSiswa];
                    }

                    if ($hasColumn('nama_lengkap')) {
                        $data['nama_lengkap'] = $namaSiswa[$indexSiswa];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Jenis kelamin
                    |--------------------------------------------------------------------------
                    | Model Anda menggunakan kolom jk.
                    */
                    if ($hasColumn('jk')) {
                        $data['jk'] = $jenisKelamin;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Dukungan jika database masih memiliki kolom lama
                    |--------------------------------------------------------------------------
                    */
                    if ($hasColumn('jenis_kelamin')) {
                        $data['jenis_kelamin'] = $jenisKelamin;
                    }

                    $data['kelas_id'] = $kelasIds[$namaKelas];

                    if ($hasColumn('tempat_lahir')) {
                        $data['tempat_lahir'] =
                            $tempatLahir[
                                $indexSiswa % count($tempatLahir)
                            ];
                    }

                    if ($hasColumn('tanggal_lahir')) {
                        $data['tanggal_lahir'] = $tanggalLahir;
                    }

                    if ($hasColumn('status')) {
                        $data['status'] = $statusAktif;
                    }

                    if ($hasColumn('is_active')) {
                        $data['is_active'] = true;
                    }

                    if ($hasColumn('tahun_ajaran')) {
                        $data['tahun_ajaran'] = $tahunAjaran;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Data orang tua
                    |--------------------------------------------------------------------------
                    */
                    if ($hasColumn('nama_ayah')) {
                        $data['nama_ayah'] =
                            $namaAyah[
                                $indexSiswa % count($namaAyah)
                            ];
                    }

                    if ($hasColumn('nama_ibu')) {
                        $data['nama_ibu'] =
                            $namaIbu[
                                $indexSiswa % count($namaIbu)
                            ];
                    }

                    if ($hasColumn('no_hp_ortu')) {
                        $data['no_hp_ortu'] = $nomorHpOrtu;
                    }

                    if ($hasColumn('nomor_hp_ortu')) {
                        $data['nomor_hp_ortu'] = $nomorHpOrtu;
                    }

                    if ($hasColumn('nomor_hp_orangtua')) {
                        $data['nomor_hp_orangtua'] = $nomorHpOrtu;
                    }

                    if ($hasColumn('alamat')) {
                        $data['alamat'] =
                            'Jl. Pendidikan No. ' .
                            ($indexSiswa + 1) .
                            ', Makassar';
                    }

                    if ($hasColumn('agama')) {
                        $data['agama'] = 'Katolik';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Timestamp
                    |--------------------------------------------------------------------------
                    */
                    if ($hasColumn('created_at')) {
                        $data['created_at'] = now();
                    }

                    if ($hasColumn('updated_at')) {
                        $data['updated_at'] = now();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Update jika NIS sudah ada
                    |--------------------------------------------------------------------------
                    | Seeder aman dijalankan berulang kali.
                    */
                    DB::table($siswaTable)->updateOrInsert(
                        [
                            'nis' => $nis,
                        ],
                        $data
                    );

                    $nomorUrutPerTingkat[$tingkat]++;
                    $indexSiswa++;
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Ringkasan hasil
        |--------------------------------------------------------------------------
        */
        $this->command?->newLine();

        $this->command?->info(
            "{$indexSiswa} data siswa berhasil ditambahkan atau diperbarui."
        );

        $this->command?->info(
            "Tahun ajaran: {$tahunAjaran}"
        );

        foreach ($daftarKelas as $namaKelas) {
            $jumlah = DB::table($siswaTable)
                ->where('kelas_id', $kelasIds[$namaKelas])
                ->count();

            $this->command?->line(
                "- {$namaKelas}: {$jumlah} siswa"
            );
        }
    }
}