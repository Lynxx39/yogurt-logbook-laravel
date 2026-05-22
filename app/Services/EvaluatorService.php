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
    public function evaluate(?array $s5Data, ?string $ekstrakText = null): ?array
    {
        if (!$s5Data) return null;

        $ph = isset($s5Data['ph_akhir']) ? (float)$s5Data['ph_akhir'] : null;
        $phOk = $ph !== null && $ph >= 3.8 && $ph <= 4.5;

        $teksturPassed = $this->resolveTeksturNormal(
            $s5Data['tekstur'] ?? null,
            isset($s5Data['tekstur_normal']) ? (bool)$s5Data['tekstur_normal'] : null
        ) ?? false;
        $aromaPassed = $this->resolveAromaNormal(
            $s5Data['aroma'] ?? null,
            isset($s5Data['aroma_normal']) ? (bool)$s5Data['aroma_normal'] : null
        ) ?? false;
        $rasaPassed = $this->resolveRasaNormal(
            $s5Data['rasa'] ?? null,
            isset($s5Data['rasa_normal']) ? (bool)$s5Data['rasa_normal'] : null
        ) ?? false;
        $warnaPassed = $this->resolveWarnaNormal(
            $s5Data['warna'] ?? null,
            isset($s5Data['warna_normal']) ? (bool)$s5Data['warna_normal'] : null,
            $ekstrakText
        ) ?? false;
        $warnaActual = $this->buildWarnaActual($s5Data['warna'] ?? null, $ekstrakText);

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
                'passed' => $teksturPassed,
                'actual' => 'Tekstur: ' . ($s5Data['tekstur'] ?? '-'),
            ],
            [
                'id'     => 3,
                'label'  => 'Aroma Normal (Asam khas yogurt / beraroma buah/sayur)',
                'desc'   => 'Aroma asam laktat yang khas menunjukkan fermentasi aktif berjalan dengan baik',
                'passed' => $aromaPassed,
                'actual' => 'Aroma: ' . ($s5Data['aroma'] ?? '-'),
            ],
            [
                'id'     => 4,
                'label'  => 'Rasa Normal (Asam manis segar / Khas yogurt)',
                'desc'   => 'Rasa asam yang menyegarkan menandakan produksi asam laktat optimal',
                'passed' => $rasaPassed,
                'actual' => 'Rasa: ' . ($s5Data['rasa'] ?? '-'),
            ],
            [
                'id'     => 5,
                'label'  => 'Warna Normal (Sesuai warna ekstrak bahan)',
                'desc'   => 'Warna yogurt seharusnya sesuai warna ekstrak; bercak hitam/hijau/abu-abu menandakan kontaminasi',
                'passed' => $warnaPassed,
                'actual' => $warnaActual,
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
        $ekstrak = strtolower(trim((string)($stagesData[1]['data']['ekstrak'] ?? '')));
        $base = $this->evaluate($s5, $ekstrak);
        if (!$base) return null;

        // Re-resolve stage-12 color using extract context.
        foreach ($base['indicators'] as &$ind) {
            if ($ind['id'] === 5) {
                $resolvedWarna = $this->resolveWarnaNormal(
                    $s5['warna'] ?? null,
                    isset($s5['warna_normal']) ? (bool)$s5['warna_normal'] : null,
                    $ekstrak
                );
                $ind['passed'] = $resolvedWarna ?? false;
                $ind['actual'] = $this->buildWarnaActual($s5['warna'] ?? null, $ekstrak);
            }
        }
        unset($ind);

        // Search earlier stages for suspicious warna (explicit contamination only)
        $suspectFound = false;
        $suspectPoints = [];

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
            if ($this->isWarnaContaminated($w)) {
                $suspectFound = true;
                $suspectPoints[] = $c['label'];
                break;
            }
        }

        if ($suspectFound) {
            // Force 'Warna' indicator (id 5) to fail
            foreach ($base['indicators'] as &$ind) {
                if ($ind['id'] === 5) {
                    $ind['passed'] = false;
                    $ind['actual'] .= ' (terdeteksi kontaminasi pada: ' . implode(', ', $suspectPoints) . ')';
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

        $ekstrak = strtolower(trim((string)($stagesData[1]['data']['ekstrak'] ?? '')));

        // Jam ke-0 (dari stage 2)
        $s2 = $stagesData[2]['data'] ?? null;
        $jam0 = $s2['jam0'] ?? null;
        $rows[] = [
            'label'         => 'Jam ke-0',
            'waktu'         => 0,
            'warna'         => $jam0['warna'] ?? '-',
            'warna_normal'  => $this->resolveWarnaNormal($jam0['warna'] ?? null, null, $ekstrak),
            'aroma'         => $jam0['aroma'] ?? '-',
            'aroma_normal'  => $this->resolveAromaNormal($jam0['aroma'] ?? null, null),
            'rasa'          => $jam0['rasa'] ?? '-',
            'rasa_normal'   => $this->resolveRasaNormal($jam0['rasa'] ?? null, null),
            'tekstur'       => $jam0['tekstur'] ?? 'Cair (awal)',
            // Baseline sebelum fermentasi memang cair, jadi tetap dianggap normal.
            'tekstur_normal'=> true,
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
            'warna_normal'  => $this->resolveWarnaNormal($s3['warna'] ?? null, isset($s3['warna_normal']) ? (bool)$s3['warna_normal'] : null, $ekstrak),
            'aroma'         => $s3['aroma'] ?? '-',
            'aroma_normal'  => $this->resolveAromaNormal($s3['aroma'] ?? null, isset($s3['aroma_normal']) ? (bool)$s3['aroma_normal'] : null),
            'rasa'          => $s3['rasa'] ?? '-',
            'rasa_normal'   => $this->resolveRasaNormal($s3['rasa'] ?? null, isset($s3['rasa_normal']) ? (bool)$s3['rasa_normal'] : null),
            'tekstur'       => $s3['tekstur'] ?? '-',
            'tekstur_normal'=> $this->resolveTeksturNormal($s3['tekstur'] ?? null, isset($s3['tekstur_normal']) ? (bool)$s3['tekstur_normal'] : null),
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
            'warna_normal'  => $this->resolveWarnaNormal($s4['warna'] ?? null, isset($s4['warna_normal']) ? (bool)$s4['warna_normal'] : null, $ekstrak),
            'aroma'         => $s4['aroma'] ?? '-',
            'aroma_normal'  => $this->resolveAromaNormal($s4['aroma'] ?? null, isset($s4['aroma_normal']) ? (bool)$s4['aroma_normal'] : null),
            'rasa'          => $s4['rasa'] ?? '-',
            'rasa_normal'   => $this->resolveRasaNormal($s4['rasa'] ?? null, isset($s4['rasa_normal']) ? (bool)$s4['rasa_normal'] : null),
            'tekstur'       => $s4['tekstur'] ?? '-',
            'tekstur_normal'=> $this->resolveTeksturNormal($s4['tekstur'] ?? null, isset($s4['tekstur_normal']) ? (bool)$s4['tekstur_normal'] : null),
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
            'warna_normal'  => $this->resolveWarnaNormal($s5['warna'] ?? null, isset($s5['warna_normal']) ? (bool)$s5['warna_normal'] : null, $ekstrak),
            'aroma'         => $s5['aroma'] ?? '-',
            'aroma_normal'  => $this->resolveAromaNormal($s5['aroma'] ?? null, isset($s5['aroma_normal']) ? (bool)$s5['aroma_normal'] : null),
            'rasa'          => $s5['rasa'] ?? '-',
            'rasa_normal'   => $this->resolveRasaNormal($s5['rasa'] ?? null, isset($s5['rasa_normal']) ? (bool)$s5['rasa_normal'] : null),
            'tekstur'       => $s5['tekstur'] ?? '-',
            'tekstur_normal'=> $this->resolveTeksturNormal($s5['tekstur'] ?? null, isset($s5['tekstur_normal']) ? (bool)$s5['tekstur_normal'] : null),
            'ph'            => $s5['ph_akhir'] ?? null,
            'catatan'       => $s5['catatan'] ?? '',
            'foto'          => $s5['foto'] ?? null,
        ];

        return $rows;
    }

    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if ($keyword !== '' && strpos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private function resolveTeksturNormal(?string $teksturText, ?bool $storedFlag = null): ?bool
    {
        $value = strtolower(trim((string)$teksturText));
        if ($value === '') return $storedFlag;

        if ($this->containsAny($value, ['cair', 'encer', 'gagal memadat'])) {
            return false;
        }

        if ($this->containsAny($value, ['sangat kental', 'semi-padat', 'semi padat', 'kental'])) {
            return true;
        }

        return $storedFlag;
    }

    private function resolveAromaNormal(?string $aromaText, ?bool $storedFlag = null): ?bool
    {
        $value = strtolower(trim((string)$aromaText));
        if ($value === '') return $storedFlag;

        if ($this->containsAny($value, ['busuk', 'tengik', 'tidak berbau', 'tidak ada bau', 'apek', 'basi', 'bau asing'])) {
            return false;
        }

        // Positive aroma indicators: include 'manis' and common fruit descriptors
        $positive = ['asam khas', 'asam segar', 'aroma buah', 'aroma sayur', 'khas yogurt', 'asam',
                     'manis', 'buah', 'stroberi', 'strawberry', 'blueberry', 'anggur', 'pisang', 'apel', 'jeruk', 'lemon', 'mangga', 'vanila', 'vanilla'];

        if ($this->containsAny($value, $positive)) {
            return true;
        }

        return $storedFlag;
    }

    private function resolveRasaNormal(?string $rasaText, ?bool $storedFlag = null): ?bool
    {
        $value = strtolower(trim((string)$rasaText));
        if ($value === '') return $storedFlag;

        if ($this->containsAny($value, ['pahit', 'hambar', 'basi', 'busuk', 'rasa asing', 'off'])) {
            return false;
        }

        // Positive taste indicators: include 'manis' and fruit names so textual evidence overrides clicks
        $positive = ['asam manis', 'khas yogurt', 'segar', 'asam', 'manis',
                     'stroberi', 'strawberry', 'blueberry', 'anggur', 'pisang', 'apel', 'jeruk', 'lemon', 'mangga', 'vanila', 'vanilla'];

        if ($this->containsAny($value, $positive)) {
            return true;
        }

        return $storedFlag;
    }

    private function isWarnaContaminated(string $value): bool
    {
        if ($this->containsAny($value, ['hitam'])) return true;
        $hasStrongContamination = $this->containsAny($value, ['jamur', 'kapang', 'mold', 'berbulu']);
        if ($hasStrongContamination) return true;

        $hasSpotKeyword = $this->containsAny($value, ['bercak', 'bintik', 'totol', 'spot']);
        $hasBadSpotColor = $this->containsAny($value, ['hitam', 'hijau', 'abu']);

        return $hasSpotKeyword && $hasBadSpotColor;
    }

    private function resolveWarnaNormal(?string $warnaText, ?bool $storedFlag = null, ?string $ekstrakText = null): ?bool
    {
        $value = strtolower(trim((string)$warnaText));
        $ekstrak = strtolower(trim((string)$ekstrakText));
        if ($value === '') return $storedFlag;

        // Only explicit contamination should force abnormal.
        if ($this->isWarnaContaminated($value)) {
            return false;
        }

        // Domain guard: dark tones can still be normal for some extracts (e.g. buah naga / blueberry / blackcurrant).
        if ($this->isDarkColorNormalByExtract($value, $ekstrak)) {
            return true;
        }

        return $storedFlag;
    }

    private function isDarkColorNormalByExtract(string $warnaValue, string $ekstrakValue): bool
    {
        $darkFriendlyExtract = $this->containsAny($ekstrakValue, ['buah naga', 'blueberry', 'blackcurrant', 'anggur', 'ubi ungu']);
        if (!$darkFriendlyExtract) return false;

        return $this->containsAny($warnaValue, ['ungu tua', 'ungu gelap', 'keunguan']);
    }

    private function buildWarnaActual(?string $warnaText, ?string $ekstrakText = null): string
    {
        $warna = trim((string)$warnaText);
        $ekstrak = strtolower(trim((string)$ekstrakText));
        $value = strtolower($warna);

        $actual = 'Warna: ' . ($warna !== '' ? $warna : '-');
        if ($warna === '') return $actual;

        if ($this->isWarnaContaminated($value)) {
            return $actual . ' (indikasi kontaminasi: ada bercak/bintik/jamur)';
        }

        if ($this->isDarkColorNormalByExtract($value, $ekstrak)) {
            return $actual . ' (warna gelap masih sesuai ekstrak, tidak ada tanda kontaminasi)';
        }

        return $actual;
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
