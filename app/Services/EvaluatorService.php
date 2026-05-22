<?php
namespace App\Services;

class EvaluatorService
{
    // -------------------------------------------------------
    // Stage definitions (used across app)
    // -------------------------------------------------------
    public static function stagesDef(): array
    {
        return [
            1 => ['title' => 'Formulation Stage',              'icon' => '🧾', 'color' => '#7C6FFF', 'editable' => true],
            2 => ['title' => 'Production Day',                 'icon' => '⚗️', 'color' => '#00A8FF', 'editable' => false],
            3 => ['title' => 'Pengamatan Jam ke-4',            'icon' => '🕒', 'color' => '#FF9500', 'editable' => false],
            4 => ['title' => 'Pengamatan Jam ke-8',            'icon' => '🕗', 'color' => '#FF6B6B', 'editable' => false],
            5 => ['title' => 'Pengamatan Final (Jam ke-12)',   'icon' => '⏱️', 'color' => '#00C896', 'editable' => false],
            6 => ['title' => 'Lab Report & Feedback',          'icon' => '📈', 'color' => '#F5C842', 'editable' => false],
        ];
    }

    // -------------------------------------------------------
    // Evaluate from stage 5 (Jam ke-12) data
    // -------------------------------------------------------
    public function evaluate(?array $s5Data): ?array
    {
        if (!$s5Data) return null;

        $ph = isset($s5Data['ph_akhir']) ? (float)$s5Data['ph_akhir'] : null;
        $phOk = $ph !== null && $ph >= 3.8 && $ph <= 4.5;

        // Color heuristics: detect obvious contamination keywords in free-text warna
        $warnaText = strtolower(trim($s5Data['warna'] ?? ''));
        $contaminants = ['hitam', 'hijau', 'abu', 'bercak', 'mold', 'jamur'];
        $warnaSuspect = false;
        foreach ($contaminants as $c) {
            if ($c !== '' && strpos($warnaText, $c) !== false) {
                $warnaSuspect = true;
                break;
            }
        }

        // Determine warna passed: if text indicates contamination, fail regardless of flag.
        $warnaFlag = isset($s5Data['warna_normal']) ? (bool)$s5Data['warna_normal'] : null;
        $warnaPassed = $warnaSuspect ? false : ($warnaFlag === null ? true : $warnaFlag);

        $indicators = [
            [
                'id'     => 1,
                'label'  => 'pH Akhir (3,8–4,5)',
                'desc'   => 'Rentang pH asam laktat yang dihasilkan oleh bakteri Lactobacillus pada fermentasi yogurt yang berhasil',
                'passed' => $phOk,
                'actual' => $ph !== null ? "pH: {$ph}" : 'pH tidak diisi',
            ],
            [
                'id'     => 2,
                'label'  => 'Tekstur Normal (Kental/Sangat Kental/Semi-padat)',
                'desc'   => 'Sesuai SNI yogurt — fermentasi laktat mengubah protein susu sehingga yogurt mengental',
                'passed' => (bool)($s5Data['tekstur_normal'] ?? false),
                'actual' => 'Tekstur: ' . ($s5Data['tekstur'] ?? '-'),
            ],
            [
                'id'     => 3,
                'label'  => 'Aroma Normal (Asam khas yogurt / beraroma buah/sayur)',
                'desc'   => 'Aroma asam laktat yang khas menunjukkan fermentasi aktif berjalan dengan baik',
                'passed' => (bool)($s5Data['aroma_normal'] ?? false),
                'actual' => 'Aroma: ' . ($s5Data['aroma'] ?? '-'),
            ],
            [
                'id'     => 4,
                'label'  => 'Rasa Normal (Asam manis segar / Khas yogurt)',
                'desc'   => 'Rasa asam yang menyegarkan menandakan produksi asam laktat optimal',
                'passed' => (bool)($s5Data['rasa_normal'] ?? false),
                'actual' => 'Rasa: ' . ($s5Data['rasa'] ?? '-'),
            ],
            [
                'id'     => 5,
                'label'  => 'Warna Normal (Sesuai warna ekstrak bahan)',
                'desc'   => 'Warna yogurt seharusnya sesuai warna ekstrak; bercak hitam/hijau/abu-abu menandakan kontaminasi',
                'passed' => $warnaPassed,
                'actual' => 'Warna: ' . ($s5Data['warna'] ?? '-') . ($warnaSuspect ? ' (terdeteksi: kemungkinan kontaminasi)' : ''),
            ],
        ];

        $score = count(array_filter($indicators, fn($i) => $i['passed']));
        $berhasil = $score === 5; // ALL must pass

        return [
            'indicators' => $indicators,
            'score'      => $score,
            'total'      => 5,
            'result'     => $berhasil ? 'berhasil' : 'kurang_berhasil',
            'ph'         => $ph,
        ];
    }

    // Evaluate from full stages data: consider jam0/jam4/jam8/jam12 coloration
    public function evaluateFromStages(array $stagesData): ?array
    {
        $s5 = $stagesData[5]['data'] ?? null;
        $base = $this->evaluate($s5);
        if (!$base) return null;

        // Search earlier stages for suspicious warna
        $suspectFound = false;
        $suspectPoints = [];
        $contaminants = ['hitam', 'hijau', 'abu', 'bercak', 'mold', 'jamur'];

        // check jam0 (stage 2)
        $j0 = $stagesData[2]['data']['jam0']['warna'] ?? null;
        $checks = [ ['label' => 'Jam ke-0', 'warna' => $j0],
                    ['label' => 'Jam ke-4', 'warna' => $stagesData[3]['data']['warna'] ?? null],
                    ['label' => 'Jam ke-8', 'warna' => $stagesData[4]['data']['warna'] ?? null],
                    ['label' => 'Jam ke-12', 'warna' => $s5['warna'] ?? null],
        ];

        foreach ($checks as $c) {
            $w = strtolower(trim((string)($c['warna'] ?? '')));
            if ($w === '') continue;
            foreach ($contaminants as $token) {
                if ($token !== '' && strpos($w, $token) !== false) {
                    $suspectFound = true;
                    $suspectPoints[] = $c['label'];
                    break 2;
                }
            }
        }

        if ($suspectFound) {
            // Force 'Warna' indicator (id 5) to fail
            foreach ($base['indicators'] as &$ind) {
                if ($ind['id'] === 5) {
                    $ind['passed'] = false;
                    $ind['actual'] .= ' (detected contamination in: ' . implode(', ', $suspectPoints) . ')';
                }
            }
            unset($ind);

            // Recompute score and result
            $base['score'] = count(array_filter($base['indicators'], fn($i) => $i['passed']));
            $base['result'] = $base['score'] === $base['total'] ? 'berhasil' : 'kurang_berhasil';
            $base['note'] = 'Evaluasi override: kontaminasi terdeteksi pada tahap sebelumnya; warna dianggap tidak normal.';
        }

        return $base;
    }

    // -------------------------------------------------------
    // Build rekapitulasi table data
    // -------------------------------------------------------
    public function buildRekapitulasi(array $stagesData): array
    {
        $rows = [];

        // Helper: force a value to fail when free-text clearly indicates abnormality.
        $resolveNormalFromText = function (?string $text, ?bool $storedFlag, array $negativeKeywords, bool $defaultTrue = true): ?bool {
            $value = strtolower(trim((string) $text));
            if ($value === '') {
                return $storedFlag;
            }

            foreach ($negativeKeywords as $token) {
                if ($token !== '' && strpos($value, $token) !== false) {
                    return false;
                }
            }

            return $storedFlag ?? $defaultTrue;
        };

        $resolveWarnaNormal = function (?string $warnaText, ?bool $storedFlag = null) use ($resolveNormalFromText): ?bool {
            return $resolveNormalFromText($warnaText, $storedFlag, ['hitam', 'hijau', 'abu', 'bercak', 'mold', 'jamur'], true);
        };

        $resolveAromaNormal = function (?string $aromaText, ?bool $storedFlag = null) use ($resolveNormalFromText): ?bool {
            return $resolveNormalFromText($aromaText, $storedFlag, ['busuk', 'tengik', 'basi', 'apek', 'berjamur', 'tidak sedap', 'bau menyengat'], true);
        };

        $resolveRasaNormal = function (?string $rasaText, ?bool $storedFlag = null) use ($resolveNormalFromText): ?bool {
            return $resolveNormalFromText($rasaText, $storedFlag, ['busuk', 'tengik', 'basi', 'pahit', 'hambar', 'asam berlebih', 'off'], true);
        };

        $resolveTeksturNormal = function (?string $teksturText, ?bool $storedFlag = null) use ($resolveNormalFromText): ?bool {
            return $resolveNormalFromText($teksturText, $storedFlag, ['encer', 'berair', 'pecah', 'menggumpal', 'menggumpal kasar', 'pisah', 'lendir'], true);
        };

        // Jam ke-0 (dari stage 2)
        $s2 = $stagesData[2]['data'] ?? null;
        $jam0 = $s2['jam0'] ?? null;
        $rows[] = [
            'label'         => 'Jam ke-0',
            'waktu'         => 0,
            'warna'         => $jam0['warna'] ?? '-',
            'warna_normal'  => $resolveWarnaNormal($jam0['warna'] ?? null, null),
            'aroma'         => $jam0['aroma'] ?? '-',
            'aroma_normal'  => $resolveAromaNormal($jam0['aroma'] ?? null, null),
            'rasa'          => $jam0['rasa'] ?? '-',
            'rasa_normal'   => $resolveRasaNormal($jam0['rasa'] ?? null, null),
            'tekstur'       => $jam0['tekstur'] ?? 'Cair (awal)',
            'tekstur_normal'=> $resolveTeksturNormal($jam0['tekstur'] ?? null, null),
            'ph'            => $jam0['ph'] ?? null,
            'catatan'       => $jam0['catatan'] ?? '',
            'foto'          => $jam0['foto'] ?? null,
        ];

        // Jam ke-4 (stage 3)
        $s3 = $stagesData[3]['data'] ?? null;
        $rows[] = [
            'label'         => 'Jam ke-4',
            'waktu'         => 4,
            'warna'         => $s3['warna'] ?? '-',
            'warna_normal'  => $resolveWarnaNormal($s3['warna'] ?? null, isset($s3['warna_normal']) ? (bool)$s3['warna_normal'] : null),
            'aroma'         => $s3['aroma'] ?? '-',
            'aroma_normal'  => $resolveAromaNormal($s3['aroma'] ?? null, isset($s3['aroma_normal']) ? (bool)$s3['aroma_normal'] : null),
            'rasa'          => $s3['rasa'] ?? '-',
            'rasa_normal'   => $resolveRasaNormal($s3['rasa'] ?? null, isset($s3['rasa_normal']) ? (bool)$s3['rasa_normal'] : null),
            'tekstur'       => $s3['tekstur'] ?? '-',
            'tekstur_normal'=> $resolveTeksturNormal($s3['tekstur'] ?? null, isset($s3['tekstur_normal']) ? (bool)$s3['tekstur_normal'] : null),
            'ph'            => null,
            'catatan'       => $s3['catatan'] ?? '',
            'foto'          => $s3['foto'] ?? null,
        ];

        // Jam ke-8 (stage 4)
        $s4 = $stagesData[4]['data'] ?? null;
        $rows[] = [
            'label'         => 'Jam ke-8',
            'waktu'         => 8,
            'warna'         => $s4['warna'] ?? '-',
            'warna_normal'  => $resolveWarnaNormal($s4['warna'] ?? null, isset($s4['warna_normal']) ? (bool)$s4['warna_normal'] : null),
            'aroma'         => $s4['aroma'] ?? '-',
            'aroma_normal'  => $resolveAromaNormal($s4['aroma'] ?? null, isset($s4['aroma_normal']) ? (bool)$s4['aroma_normal'] : null),
            'rasa'          => $s4['rasa'] ?? '-',
            'rasa_normal'   => $resolveRasaNormal($s4['rasa'] ?? null, isset($s4['rasa_normal']) ? (bool)$s4['rasa_normal'] : null),
            'tekstur'       => $s4['tekstur'] ?? '-',
            'tekstur_normal'=> $resolveTeksturNormal($s4['tekstur'] ?? null, isset($s4['tekstur_normal']) ? (bool)$s4['tekstur_normal'] : null),
            'ph'            => null,
            'catatan'       => $s4['catatan'] ?? '',
            'foto'          => $s4['foto'] ?? null,
        ];

        // Jam ke-12 (stage 5)
        $s5 = $stagesData[5]['data'] ?? null;
        $rows[] = [
            'label'         => 'Jam ke-12 (Final)',
            'waktu'         => 12,
            'warna'         => $s5['warna'] ?? '-',
            'warna_normal'  => $resolveWarnaNormal($s5['warna'] ?? null, isset($s5['warna_normal']) ? (bool)$s5['warna_normal'] : null),
            'aroma'         => $s5['aroma'] ?? '-',
            'aroma_normal'  => $resolveAromaNormal($s5['aroma'] ?? null, isset($s5['aroma_normal']) ? (bool)$s5['aroma_normal'] : null),
            'rasa'          => $s5['rasa'] ?? '-',
            'rasa_normal'   => $resolveRasaNormal($s5['rasa'] ?? null, isset($s5['rasa_normal']) ? (bool)$s5['rasa_normal'] : null),
            'tekstur'       => $s5['tekstur'] ?? '-',
            'tekstur_normal'=> $resolveTeksturNormal($s5['tekstur'] ?? null, isset($s5['tekstur_normal']) ? (bool)$s5['tekstur_normal'] : null),
            'ph'            => $s5['ph_akhir'] ?? null,
            'catatan'       => $s5['catatan'] ?? '',
            'foto'          => $s5['foto'] ?? null,
        ];

        return $rows;
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    public static function normalLabel(?bool $normal): string
    {
        if ($normal === null) return '-';
        return $normal ? '✔️ Normal' : '✖️ Tidak Normal';
    }

    public static function normalClass(?bool $normal): string
    {
        if ($normal === null) return '';
        return $normal ? 'status-normal' : 'status-abnormal';
    }
}
