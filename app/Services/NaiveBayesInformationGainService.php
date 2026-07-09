<?php

namespace App\Services;

use App\Models\EvaluasiModel;
use App\Models\InformationGainResult;
use App\Models\Klasifikasi;
use App\Models\Siswa;
use Illuminate\Support\Collection;

class NaiveBayesInformationGainService
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
                'message' => 'Data belum cukup. Minimal dibutuhkan 3 data siswa untuk menjalankan klasifikasi.',
                'total_samples' => $samples->count(),
            ];
        }

        $trainingRatio = min(max($trainingRatio, 0.5), 0.9);
        $split = $this->splitSamples($samples, $trainingRatio);
        $training = $split['training'];
        $testing = $split['testing'];

        $gainResults = $this->calculateInformationGain($training, $this->featureKeys);
        $selectedFeatures = collect($gainResults)
            ->filter(fn (array $item): bool => $item['gain'] > 0)
            ->sortByDesc('gain')
            ->take(5)
            ->pluck('feature')
            ->values()
            ->all();

        if (count($selectedFeatures) === 0) {
            $selectedFeatures = $this->featureKeys;
        }

        $this->saveInformationGainResults($gainResults, $selectedFeatures, $training->count());

        $baselineModel = $this->trainNaiveBayes($training, $this->featureKeys);
        $optimizedModel = $this->trainNaiveBayes($training, $selectedFeatures);

        foreach ($samples as $sample) {
            $baselinePrediction = $this->predict($baselineModel, $sample['features']);
            $optimizedPrediction = $this->predict($optimizedModel, $sample['features']);

            Klasifikasi::updateOrCreate(
                ['siswa_id' => $sample['siswa_id']],
                [
                    'jumlah_pelanggaran' => $sample['jumlah_pelanggaran'],
                    'total_poin' => $sample['total_poin'],
                    'hasil_klasifikasi' => $optimizedPrediction['class'],
                    'label_aktual' => $sample['label'],
                    'hasil_naive_bayes' => $baselinePrediction['class'],
                    'probabilitas_naive_bayes' => $baselinePrediction['probability'],
                    'hasil_ig_naive_bayes' => $optimizedPrediction['class'],
                    'probabilitas_ig_naive_bayes' => $optimizedPrediction['probability'],
                    'probabilitas' => $optimizedPrediction['probability'],
                    'probabilitas_detail' => $optimizedPrediction['probabilities'],
                    'fitur_klasifikasi' => $sample['features'],
                    'information_gain_detail' => [
                        'selected_features' => $selectedFeatures,
                        'ranking' => $gainResults,
                    ],
                    'metode' => 'Naive Bayes + Information Gain',
                ]
            );
        }

        $baselineEvaluation = $this->evaluate($testing, $baselineModel);
        $optimizedEvaluation = $this->evaluate($testing, $optimizedModel);

        $this->saveEvaluation('Naive Bayes', $baselineEvaluation, $training->count(), $testing->count(), $this->featureKeys);
        $this->saveEvaluation('Naive Bayes + Information Gain', $optimizedEvaluation, $training->count(), $testing->count(), $selectedFeatures);

        return [
            'success' => true,
            'message' => 'Klasifikasi berhasil diproses.',
            'total_samples' => $samples->count(),
            'training_count' => $training->count(),
            'testing_count' => $testing->count(),
            'selected_features' => $selectedFeatures,
            'baseline_evaluation' => $baselineEvaluation,
            'optimized_evaluation' => $optimizedEvaluation,
        ];
    }

    public function buildSamples(?string $tahunAjaran = null, ?string $semester = null): Collection
    {
        return Siswa::query()
            ->with(['pelanggarans.jenisPelanggaran', 'klasifikasi'])
            ->where('status', 'Aktif')
            ->orderBy('id')
            ->get()
            ->map(function (Siswa $siswa) use ($tahunAjaran, $semester): array {
                $pelanggarans = $siswa->pelanggarans;

                if ($tahunAjaran) {
                    $pelanggarans = $pelanggarans->where('tahun_ajaran', $tahunAjaran);
                }

                if ($semester) {
                    $pelanggarans = $pelanggarans->where('semester', $semester);
                }

                $jumlahPelanggaran = $pelanggarans->count();
                $totalPoin = $pelanggarans->sum(fn ($pelanggaran): int => (int) ($pelanggaran->jenisPelanggaran?->poin ?? 0));
                $aspekCounts = $this->countAspects($pelanggarans);
                $tingkatCounts = $this->countLevels($pelanggarans);
                $features = $this->makeFeatures($jumlahPelanggaran, $totalPoin, $aspekCounts, $tingkatCounts, $pelanggarans->last()?->semester ?? 'Tidak Ada');
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
        $level = array_key_first($counts);

        return ($counts[$level] ?? 0) > 0 ? $level : 'Tidak Ada';
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

    private function splitSamples(Collection $samples, float $trainingRatio): array
    {
        $grouped = $samples->groupBy('label');
        $training = collect();
        $testing = collect();

        foreach ($grouped as $group) {
            $items = $group->values();
            $trainingCount = max(1, (int) floor($items->count() * $trainingRatio));

            if ($items->count() > 1 && $trainingCount >= $items->count()) {
                $trainingCount = $items->count() - 1;
            }

            $training = $training->merge($items->take($trainingCount));
            $testing = $testing->merge($items->slice($trainingCount));
        }

        if ($testing->isEmpty()) {
            $testing = $training->take(1);
        }

        return [
            'training' => $training->values(),
            'testing' => $testing->values(),
        ];
    }

    private function calculateInformationGain(Collection $samples, array $features): array
    {
        $baseEntropy = $this->entropy($samples->pluck('label')->all());
        $results = [];

        foreach ($features as $feature) {
            $groups = $samples->groupBy(fn (array $sample): string => (string) ($sample['features'][$feature] ?? 'Tidak Ada'));
            $weightedEntropy = 0;

            foreach ($groups as $group) {
                $weightedEntropy += ($group->count() / max($samples->count(), 1)) * $this->entropy($group->pluck('label')->all());
            }

            $results[] = [
                'feature' => $feature,
                'gain' => round($baseEntropy - $weightedEntropy, 10),
                'entropy_before' => round($baseEntropy, 10),
                'entropy_after' => round($weightedEntropy, 10),
                'values' => $groups->map(fn (Collection $group): int => $group->count())->toArray(),
            ];
        }

        return collect($results)
            ->sortByDesc('gain')
            ->values()
            ->map(function (array $item, int $index): array {
                $item['ranking'] = $index + 1;

                return $item;
            })
            ->all();
    }

    private function entropy(array $labels): float
    {
        $total = count($labels);

        if ($total === 0) {
            return 0;
        }

        $counts = array_count_values($labels);
        $entropy = 0;

        foreach ($counts as $count) {
            $probability = $count / $total;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    private function saveInformationGainResults(array $gainResults, array $selectedFeatures, int $jumlahData): void
    {
        InformationGainResult::query()->delete();

        foreach ($gainResults as $result) {
            InformationGainResult::create([
                'fitur' => $result['feature'],
                'gain' => $result['gain'],
                'entropy_before' => $result['entropy_before'],
                'entropy_after' => $result['entropy_after'],
                'selected' => in_array($result['feature'], $selectedFeatures, true),
                'metode' => 'Information Gain',
                'jumlah_data' => $jumlahData,
                'ranking' => $result['ranking'],
                'detail' => $result['values'],
            ]);
        }
    }

    private function trainNaiveBayes(Collection $samples, array $features): array
    {
        $classCounts = [];
        $featureCounts = [];
        $featureValues = [];

        foreach ($this->classes as $class) {
            $classCounts[$class] = 0;
            $featureCounts[$class] = [];

            foreach ($features as $feature) {
                $featureCounts[$class][$feature] = [];
                $featureValues[$feature] = [];
            }
        }

        foreach ($samples as $sample) {
            $label = $sample['label'];
            $classCounts[$label] = ($classCounts[$label] ?? 0) + 1;

            foreach ($features as $feature) {
                $value = (string) ($sample['features'][$feature] ?? 'Tidak Ada');
                $featureValues[$feature][$value] = true;
                $featureCounts[$label][$feature][$value] = ($featureCounts[$label][$feature][$value] ?? 0) + 1;
            }
        }

        return [
            'total' => $samples->count(),
            'classes' => $this->classes,
            'features' => $features,
            'class_counts' => $classCounts,
            'feature_counts' => $featureCounts,
            'feature_values' => array_map(fn (array $values): array => array_keys($values), $featureValues),
        ];
    }

    private function predict(array $model, array $features): array
    {
        $logs = [];
        $classTotal = count($model['classes']);

        foreach ($model['classes'] as $class) {
            $classCount = $model['class_counts'][$class] ?? 0;
            $logProbability = log(($classCount + 1) / ($model['total'] + $classTotal));

            foreach ($model['features'] as $feature) {
                $value = (string) ($features[$feature] ?? 'Tidak Ada');
                $valueCount = $model['feature_counts'][$class][$feature][$value] ?? 0;
                $valueCardinality = max(count($model['feature_values'][$feature] ?? []), 1);
                $logProbability += log(($valueCount + 1) / ($classCount + $valueCardinality));
            }

            $logs[$class] = $logProbability;
        }

        $probabilities = $this->normalizeLogProbabilities($logs);
        arsort($probabilities);
        $class = array_key_first($probabilities);

        return [
            'class' => $class,
            'probability' => round($probabilities[$class], 6),
            'probabilities' => $probabilities,
        ];
    }

    private function normalizeLogProbabilities(array $logs): array
    {
        $max = max($logs);
        $exp = [];
        $sum = 0;

        foreach ($logs as $class => $logValue) {
            $exp[$class] = exp($logValue - $max);
            $sum += $exp[$class];
        }

        $probabilities = [];

        foreach ($exp as $class => $value) {
            $probabilities[$class] = round($value / max($sum, PHP_FLOAT_MIN), 6);
        }

        return $probabilities;
    }

    private function evaluate(Collection $testing, array $model): array
    {
        $matrix = [];

        foreach ($this->classes as $actual) {
            foreach ($this->classes as $predicted) {
                $matrix[$actual][$predicted] = 0;
            }
        }

        foreach ($testing as $sample) {
            $actual = $sample['label'];
            $prediction = $this->predict($model, $sample['features']);
            $matrix[$actual][$prediction['class']]++;
        }

        $total = max($testing->count(), 1);
        $correct = 0;
        $precisionTotal = 0;
        $recallTotal = 0;
        $f1Total = 0;

        foreach ($this->classes as $class) {
            $tp = $matrix[$class][$class] ?? 0;
            $fp = 0;
            $fn = 0;

            foreach ($this->classes as $otherClass) {
                if ($otherClass !== $class) {
                    $fp += $matrix[$otherClass][$class] ?? 0;
                    $fn += $matrix[$class][$otherClass] ?? 0;
                }
            }

            $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0;
            $recall = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0;
            $f1 = ($precision + $recall) > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0;

            $correct += $tp;
            $precisionTotal += $precision;
            $recallTotal += $recall;
            $f1Total += $f1;
        }

        return [
            'akurasi' => round(($correct / $total) * 100, 2),
            'precision' => round(($precisionTotal / count($this->classes)) * 100, 2),
            'recall' => round(($recallTotal / count($this->classes)) * 100, 2),
            'f1_score' => round(($f1Total / count($this->classes)) * 100, 2),
            'confusion_matrix' => $matrix,
            'features' => $model['features'],
        ];
    }

    private function saveEvaluation(string $method, array $evaluation, int $trainingCount, int $testingCount, array $features): void
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
            'keterangan' => 'Fitur: '.implode(', ', $features),
        ]);
    }
}