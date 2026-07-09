<?php

namespace App\Services;

use App\Models\EvaluasiModel;
use App\Models\InformationGainResult;
use App\Models\Klasifikasi;
use App\Models\Siswa;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class PythonNaiveBayesInformationGainService
{
    private array $classes = ['Baik', 'Perlu Pembinaan', 'Bermasalah'];

    private array $featureKeys = [
        'jumlah_pelanggaran_kategori',
        'total_poin_kategori',
        'kelakuan_kategori',
        'kerajinan_kategori',
        'kerapian_kategori',
        'kehadiran_kategori',
        'lainnya_kategori',
        'tingkat_dominan',
        'semester_terakhir',
    ];

    public function run(?string $tahunAjaran = null, ?string $semester = null, float $trainingRatio = 0.8): array
    {
        $samples = $this->buildSamples($tahunAjaran, $semester);

        if ($samples->count() < 3) {
            return [
                'success' => false,
                'message' => 'Data belum cukup. Minimal dibutuhkan 3 data siswa.',
            ];
        }

        $result = $this->callPython([
            'samples' => $samples->values()->all(),
            'features' => $this->featureKeys,
            'classes' => $this->classes,
            'training_ratio' => min(max($trainingRatio, 0.5), 0.9),
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $this->saveInformationGainResults(
            $result['gain_results'],
            $result['selected_features'],
            $result['training_count']
        );

        foreach ($result['predictions'] as $prediction) {
            Klasifikasi::updateOrCreate(
                ['siswa_id' => $prediction['siswa_id']],
                [
                    'jumlah_pelanggaran' => $prediction['jumlah_pelanggaran'],
                    'total_poin' => $prediction['total_poin'],
                    'hasil_klasifikasi' => $prediction['optimized']['class'],
                    'label_aktual' => $prediction['label'],
                    'hasil_naive_bayes' => $prediction['baseline']['class'],
                    'probabilitas_naive_bayes' => $prediction['baseline']['probability'],
                    'hasil_ig_naive_bayes' => $prediction['optimized']['class'],
                    'probabilitas_ig_naive_bayes' => $prediction['optimized']['probability'],
                    'probabilitas' => $prediction['optimized']['probability'],
                    'probabilitas_detail' => $prediction['optimized']['probabilities'],
                    'fitur_klasifikasi' => $prediction['features'],
                    'information_gain_detail' => [
                        'selected_features' => $result['selected_features'],
                        'ranking' => $result['gain_results'],
                    ],
                    'metode' => 'Python Naive Bayes + Information Gain',
                ]
            );
        }

        $this->saveEvaluation(
            'Python Naive Bayes',
            $result['baseline_evaluation'],
            $result['training_count'],
            $result['testing_count']
        );

        $this->saveEvaluation(
            'Python Naive Bayes + Information Gain',
            $result['optimized_evaluation'],
            $result['training_count'],
            $result['testing_count']
        );

        return $result;
    }

    private function callPython(array $payload): array
    {
        $python = env('PYTHON_BIN', 'python3');
        $script = base_path('python/naive_bayes_ig.py');

        if (! file_exists($script)) {
            return [
                'success' => false,
                'message' => 'File Python tidak ditemukan.',
            ];
        }

        $process = new Process([$python, $script], base_path());
        $process->setInput(json_encode($payload, JSON_UNESCAPED_UNICODE));
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            return [
                'success' => false,
                'message' => trim($process->getErrorOutput()) ?: 'Python gagal menjalankan proses.',
            ];
        }

        $output = json_decode($process->getOutput(), true);

        if (! is_array($output)) {
            return [
                'success' => false,
                'message' => 'Output Python tidak valid.',
            ];
        }

        return $output;
    }

    private function buildSamples(?string $tahunAjaran = null, ?string $semester = null): Collection
    {
        return Siswa::query()
            ->with(['pelanggarans.jenisPelanggaran', 'klasifikasi'])
            ->where('status', 'Aktif')
            ->orderBy('id')
            ->get()
            ->map(function (Siswa $siswa) use ($tahunAjaran, $semester): array {
                $pelanggarans = $siswa->pelanggarans;

                if ($tahunAjaran) {
                    $pelanggarans = $pelanggarans->filter(fn ($item): bool => (string) $item->tahun_ajaran === (string) $tahunAjaran);
                }

                if ($semester) {
                    $pelanggarans = $pelanggarans->filter(fn ($item): bool => (string) $item->semester === (string) $semester);
                }

                $jumlahPelanggaran = $pelanggarans->count();
                $totalPoin = $pelanggarans->sum(fn ($item): int => (int) ($item->jenisPelanggaran?->poin ?? 0));
                $aspekCounts = $this->countAspects($pelanggarans);
                $tingkatCounts = $this->countLevels($pelanggarans);
                $semesterTerakhir = (string) ($pelanggarans->last()?->semester ?? 'Tidak Ada');
                $features = $this->makeFeatures($jumlahPelanggaran, $totalPoin, $aspekCounts, $tingkatCounts, $semesterTerakhir);
                $label = $siswa->klasifikasi?->label_aktual ?: $this->deriveLabel($totalPoin, $jumlahPelanggaran, $tingkatCounts);

                return [
                    'siswa_id' => $siswa->id,
                    'jumlah_pelanggaran' => $jumlahPelanggaran,
                    'total_poin' => $totalPoin,
                    'features' => $features,
                    'label' => $label,
                ];
            })
            ->values();
    }

    private function countAspects(Collection $pelanggarans): array
    {
        $counts = [
            'Kelakuan' => 0,
            'Kerajinan' => 0,
            'Kerapian' => 0,
            'Kehadiran' => 0,
            'Lainnya' => 0,
        ];

        foreach ($pelanggarans as $pelanggaran) {
            $aspek = $pelanggaran->jenisPelanggaran?->aspek_pelanggaran ?: 'Lainnya';

            if (! array_key_exists($aspek, $counts)) {
                $aspek = 'Lainnya';
            }

            $counts[$aspek]++;
        }

        return $counts;
    }

    private function countLevels(Collection $pelanggarans): array
    {
        $counts = [
            'Ringan' => 0,
            'Sedang' => 0,
            'Berat' => 0,
        ];

        foreach ($pelanggarans as $pelanggaran) {
            $tingkat = $pelanggaran->jenisPelanggaran?->tingkat_pelanggaran ?: $this->levelFromPoint((int) ($pelanggaran->jenisPelanggaran?->poin ?? 0));

            if (! array_key_exists($tingkat, $counts)) {
                $tingkat = 'Ringan';
            }

            $counts[$tingkat]++;
        }

        return $counts;
    }

    private function makeFeatures(int $jumlahPelanggaran, int $totalPoin, array $aspekCounts, array $tingkatCounts, string $semesterTerakhir): array
    {
        return [
            'jumlah_pelanggaran_kategori' => $this->countCategory($jumlahPelanggaran),
            'total_poin_kategori' => $this->pointCategory($totalPoin),
            'kelakuan_kategori' => $this->countCategory($aspekCounts['Kelakuan'] ?? 0),
            'kerajinan_kategori' => $this->countCategory($aspekCounts['Kerajinan'] ?? 0),
            'kerapian_kategori' => $this->countCategory($aspekCounts['Kerapian'] ?? 0),
            'kehadiran_kategori' => $this->countCategory($aspekCounts['Kehadiran'] ?? 0),
            'lainnya_kategori' => $this->countCategory($aspekCounts['Lainnya'] ?? 0),
            'tingkat_dominan' => $this->dominantLevel($tingkatCounts),
            'semester_terakhir' => $semesterTerakhir,
        ];
    }

    private function countCategory(int $value): string
    {
        return match (true) {
            $value <= 0 => 'Tidak Ada',
            $value <= 2 => 'Rendah',
            $value <= 5 => 'Sedang',
            default => 'Tinggi',
        };
    }

    private function pointCategory(int $value): string
    {
        return match (true) {
            $value <= 0 => 'Tidak Ada',
            $value <= 15 => 'Rendah',
            $value <= 40 => 'Sedang',
            default => 'Tinggi',
        };
    }

    private function levelFromPoint(int $point): string
    {
        return match (true) {
            $point <= 4 => 'Ringan',
            $point <= 15 => 'Sedang',
            default => 'Berat',
        };
    }

    private function dominantLevel(array $counts): string
    {
        arsort($counts);
        $key = array_key_first($counts);

        return ($counts[$key] ?? 0) > 0 ? $key : 'Tidak Ada';
    }

    private function deriveLabel(int $totalPoin, int $jumlahPelanggaran, array $tingkatCounts): string
    {
        if (($tingkatCounts['Berat'] ?? 0) >= 2 || $totalPoin > 40 || $jumlahPelanggaran > 8) {
            return 'Bermasalah';
        }

        if (($tingkatCounts['Berat'] ?? 0) >= 1 || $totalPoin > 15 || $jumlahPelanggaran > 3) {
            return 'Perlu Pembinaan';
        }

        return 'Baik';
    }

    private function saveInformationGainResults(array $results, array $selectedFeatures, int $jumlahData): void
    {
        InformationGainResult::query()->delete();

        foreach ($results as $result) {
            InformationGainResult::create([
                'fitur' => $result['feature'],
                'gain' => $result['gain'],
                'entropy_before' => $result['entropy_before'],
                'entropy_after' => $result['entropy_after'],
                'selected' => in_array($result['feature'], $selectedFeatures, true),
                'metode' => 'Python Information Gain',
                'jumlah_data' => $jumlahData,
                'ranking' => $result['ranking'],
                'detail' => $result['values'],
            ]);
        }
    }

    private function saveEvaluation(string $method, array $evaluation, int $trainingCount, int $testingCount): void
    {
        EvaluasiModel::create([
            'metode' => $method,
            'jumlah_data_training' => $trainingCount,
            'jumlah_data_testing' => $testingCount,
            'akurasi' => $evaluation['akurasi'],
            'precision' => $evaluation['precision'],
            'recall' => $evaluation['recall'],
            'f1_score' => $evaluation['f1_score'],
            'confusion_matrix' => json_encode($evaluation['confusion_matrix'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'keterangan' => 'Fitur: '.implode(', ', $evaluation['features']),
        ]);
    }
}
